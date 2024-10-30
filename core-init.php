<?php
if (!defined('WPINC')) {
    die;
} // end if
/*
*  Register CSS 
*/
function cush_cpt_register_css()
{
    wp_enqueue_style('cush-cpt-css', CUSH_CPT_PLUGIN_ASSETS . 'plugin.css', null, time('s'), 'all');
};

add_action('wp_enqueue_scripts', 'cush_cpt_register_css');


