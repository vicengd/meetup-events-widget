<?php
/**
 *  Meetup Events Widget
 *
 * @link              https://vicentegarcia.com
 * @since             1.0.0
 * @package           Meetup_Events_Widget
 *
 * @wordpress-plugin
 * Plugin Name:       Meetup Events Widget
 * Plugin URI:        https://es.wordpress.org/plugins/meetup-events-widget/
 * Description:       A simple widget for displaying open events from meetup.com
 * Version:           1.1.3
 * Author:            Vicente Garcia
 * Author URI:        https://vicentegarcia.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       meetup-events-widget
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Function to register widget
 */
function mew_create_widget(){
    include_once(plugin_dir_path( __FILE__ ).'/includes/mewidget.php');
    register_widget('mewidget');
}
add_action('widgets_init','mew_create_widget');
