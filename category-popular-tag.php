<?php
/* 
Plugin Name: Category Popular Tags
Author: Abidemi Kusimo
Text Domain: category-popular-tags
Version: 1.0
License: GPLv2
Author URI: http://basichow.com
Description: Display popular tags on achieve page of your website using sortcode. For example [popular_category_tags count=10 type="popular-tags" category_id="1"]. You can also call this function from your theme file: cush_category_popular_tag().
*/


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('CUSH_CPT_PLUGIN_DIR', __FILE__);
define('CUSH_CPT_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('CUSH_CPT_PLUGIN_ASSETS', plugins_url('assets/', __FILE__));


// loading step
require_once('class/sortcode.class.php'); // Load shortcode
require_once('class/settings.class.php'); // Load the Plugin Options Panel

require_once('core-init.php');


$cush_category_popular_tag_sc = new Cush_CPT_Shortcode(__FILE__);

//add to achieve page
// check if user option to auto display is enable for category page then 
if(!function_exists('cush_cpt_archive_title')) {
    function cush_cpt_archive_title( $title ) {
        $categories = get_the_category();
        if (!empty($categories)) {
            $current_catid = get_cat_ID($categories[0]->name);
        }
        if ( is_category() ) {
            //$title = single_cat_title( '', false );
            $title = do_shortcode('[popular_category_tags count=10 type="popular-tags" category_id="'.$current_catid.'"]');
        } elseif ( is_tag() ) {
        // $title = single_tag_title( '', false );
            $title = do_shortcode('[popular_category_tags count=10 type="popular-tags" category_id="'.$current_catid.'"]');
        } elseif ( is_author() ) {
            //$title = '<span class="vcard">' . get_the_author() . '</span>';
            $title = do_shortcode('[popular_category_tags count=10 type="popular-tags" category_id="'.$current_catid.'"]');
        } elseif ( is_post_type_archive() ) {
            //$title = post_type_archive_title( '', false );
            $title = do_shortcode('[popular_category_tags count=10 type="popular-tags" category_id="'.$current_catid.'"]');
        } elseif ( is_tax() ) {
            //$title = single_term_title( '', false );
            $title = do_shortcode('[popular_category_tags count=10 type="popular-tags" category_id="'.$current_catid.'"]');
        }
    
    echo $title;
    }
}


//add_action( 'get_the_archive_title', 'cush_cpt_archive_title' ); //to be done

if(!function_exists('cush_category_popular_tag')) {
    function cush_category_popular_tag() {
        $categories = get_the_category();
        if (!empty($categories)) {
            $current_catid = get_cat_ID($categories[0]->name);
        }
        if(!is_tag()) :
        echo do_shortcode('[popular_category_tags count=5 type="popular-tags" category_id="'.$current_catid.'"]');
        endif;
    } 
}

//delete transient on deactivation 
register_deactivation_hook(__FILE__, 'cush_popular_cat_delete_transient');
function cush_popular_cat_delete_transient()
{
    global $wpdb;
    $wpdb->query("DELETE FROM `wp_options` WHERE `option_name` LIKE ('_transient%_cush_cpt_%')");
}
