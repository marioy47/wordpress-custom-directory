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
 * Author:            Mario Yepes
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
$plugin_slug         = 'wordpress-custom-directory';
$post_type_name      = 'directory_entry';
$taxonomy_name       = 'directory_tax';
$shotcode_list_name  = 'custom-directory-list';
$shortcode_form_name = 'custom-directory-search';
$shortcode_item_name = 'custom-directory-item';
$query_var_prefix    = 'custom-dir-';

require_once __DIR__ . '/classes/autoload.php';
require_once __DIR__ . '/vendor/autoload.php';

// Add links to the plugin list.
Plugin_List::factory()
	->set_plugin_file( __FILE__ )
	->set_plugin_slug( $plugin_slug )
	->start();

// Create the settings page for custom-type manipulation.
Settings_Page::factory()
	->set_plugin_file( __FILE__ )
	->set_plugin_slug( $plugin_slug )
	->set_post_type( $post_type_name )
	->start();

// Create the help page in the dashboard.
Help_Page::factory()
	->set_plugin_file( __FILE__ )
	->set_plugin_slug( $plugin_slug )
	->start();

// Creates the post_type itself.
Type_Directory_Entry::factory()
	->set_post_type( $post_type_name )
	->set_taxonomy( $taxonomy_name )
	->start();

// Creates the taxonomy asigend to the post-type.
Type_Directory_Tax::factory()
	->set_post_type( $post_type_name )
	->set_taxonomy( $taxonomy_name )
	->start();

// Shortcoce for creating  a list of items.
Shortcode_Custom_Directory_List::factory()
	->set_post_type( $post_type_name )
	->set_taxonomy( $taxonomy_name )
	->set_shortcode_name( $shotcode_list_name )
	->start();

// Shortcode for creating a search form for a directory.
Shortcode_Custom_Directory_Search::factory()
	->set_shortcode_name( $shortcode_form_name )
	->set_plugin_file( __FILE__ )
	->start();

// Shortcode for showing in item embeded in a page.
Shortcode_Custom_Directory_Item::factory()
	->set_shortcode_name( $shortcode_item_name )
	->set_query_vars_prefix( $query_var_prefix )
	->set_post_type( $post_type_name )
	->start();

