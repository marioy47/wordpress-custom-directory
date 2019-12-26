<?php
/**
 * WordPress Custom Directory Plugin
 *
 * @package           Wordpress_Custom_Directory
 * @author            Mario Yepes <marioy47@gmail.com>
 * @copyright         2019 Mario Yepes
 * @license
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Custom Directory
 * Plugin URI:        https://marioyepes.com
 * Description:       Creates a searcheable directory of people, elements, or anythig a  custom type can be.
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.0
 * Author:            Mairo Yepes
 * Author URI:        https://marioyepes.com
 * Text Domain:       wp-custom-dir
 * License:
 * License URI:
 */

namespace WpCustomDir;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// For javascript and css file enqueueing.
define( 'WP_CUSTOM_DIRECTORY_VERSION', '1.0.0' );
$plugin_slug = 'wordpress-custom-directory';


require_once __DIR__ . '/vendor/autoload.php';

// Add links to the plugin list.
Settings\Plugin_List::factory()
	->set_plugin_file( __FILE__ )
	->set_plugin_slug( $plugin_slug )
	->start();

Settings\Settings_Page::factory()
	->set_plugin_slug( $plugin_slug )
	->start();
