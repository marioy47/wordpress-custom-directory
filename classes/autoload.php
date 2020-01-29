<?php
/**
 * Create a custom autloader that supports WP conventions.
 *
 * @package Wordpress_Custom_Dir
 */

/**
 * Register custom autloader.
 *
 * @param string $class_name The name of the class passed by PHP.
 * @return void
 */
function wp_custom_dir_class_file_autoloader( $class_name ) {

	$namespace = 'WpCustomDir\\';
	if ( 0 !== strpos( $class_name, $namespace ) ) {
		return;
	}

	$file_name = str_replace(
		array( $namespace, '_' ),
		array( 'class-', '-' ),
		$class_name
	);

	$path = __DIR__ . '/' . strtolower( $file_name ) . '.php';

	if ( file_exists( $path ) ) {
		require $path;
	}
}

spl_autoload_register( 'wp_custom_dir_class_file_autoloader' );
