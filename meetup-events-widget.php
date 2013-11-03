<?php
/*
Plugin Name: Meetup Events Widget
Plugin URI: http://vicentegarcia.com
Description: A simple widget for displaying open events from meetup.com
Author: Vicente García
Author URI: http://vicentegarcia.com
Author email: v@vicentegarcia.com
Version: 1.0
License: GPLv2
*/

/**
 * Function to register widget
 */
function mew_create_widget(){
    include_once(plugin_dir_path( __FILE__ ).'/includes/mewidget.php');
    register_widget('mewidget');
}
add_action('widgets_init','mew_create_widget');
