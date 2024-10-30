<?php 
/*
*
*	***** Popular category tags *****
*
*	Plugin Settings Page
*	
*/
// If this file is called directly, abort. //
if ( ! defined( 'WPINC' ) ) {die;} // end if
class Cush_CPT_Settings_Page
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Category Tags', 
            'manage_options', 
            'pct-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'pct_option' );
        ?>
        <div class="wrap">
            <h1>Popular Category Tags</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'pct_option_group' );
                do_settings_sections( 'pct-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'pct_option_group', // Option group
            'pct_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'pct-setting-admin' // Page
        );  

        add_settings_field(
            'title', 
            'Title', 
            array( $this, 'title_callback' ), 
            'pct-setting-admin', 
            'setting_section_id'
        );      

        add_settings_field(
            'total_tag', // Number
            'Total Number of tags', // Title 
            array( $this, 'id_number_callback' ), // Callback
            'pct-setting-admin', // Page
            'setting_section_id' // Section           
        );  
        
        add_settings_field(
            'arrow_enable',
            'Show Arrow', 
            array( $this, 'arrow_enable_callback' ), // Callback
            'pct-setting-admin', // Page
            'setting_section_id' // Section   
        );

        add_settings_field(
            'number_enable',
            'Show Numbers', 
            array( $this, 'number_enable_callback' ), // Callback
            'pct-setting-admin', // Page
            'setting_section_id' // Section   
        );

    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['total_tag'] ) )
            $new_input['total_tag'] = absint( $input['total_tag'] );

        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );

        if( isset( $input['arrow_enable'] ) )
            $new_input['arrow_enable'] = $input['arrow_enable'];

        if( isset( $input['number_enable'] ) )
            $new_input['number_enable'] = $input['number_enable'];

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function id_number_callback()
    {
        printf(
            '<input type="number" id="total_tag" name="pct_option[total_tag]" min="1" max="100" value="%s" />',
            isset( $this->options['total_tag'] ) ? esc_attr( $this->options['total_tag']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function title_callback()
    {
        printf(
            '<input type="text" id="title" name="pct_option[title]" value="%s" />',
            isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
        );
    }

     /** 
     * Get the settings option for checkbox values
     */
    
    public function arrow_enable_callback() 
    {
        printf(
            '<input %s id="arrow_enable" name="pct_option[arrow_enable]" type="checkbox" value="1" >',
            $this->options['arrow_enable'] === '1' ? 'checked' : ''
    
        );
    }

    public function number_enable_callback() 
    {
        printf(
            '<input %s id="number_enable" name="pct_option[number_enable]" type="checkbox" value="1" >',
            $this->options['number_enable'] === '1' ? 'checked' : ''
    
        );
    }

}

if( is_admin() )
$popular_category_tag_settings_page = new Cush_CPT_Settings_Page();