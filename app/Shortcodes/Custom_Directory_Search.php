<?php
/**
 * Create a search form shortcode.
 *
 * @package Wordpress_Custom_Directory
 */

namespace WpCustomDir\Shortcodes;

use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * Setup and create a search form for a directory.
 */
class Custom_Directory_Search {

	/**
	 * Singleton.
	 */
	private function __construct() {

	}

	/**
	 * Factory.
	 *
	 * @return self
	 */
	public static function factory(): self {
		static $obj;
		return isset( $obj ) ? $obj : $obj = new self();
	}

	/**
	 * Executes the add_action() and add_filter() WordPress functions.
	 *
	 * @return self
	 */
	public function start(): self {
		add_shortcode( $this->shortcode_name, array( $this, 'shortcode' ) );
		return $this;
	}

	/**
	 * Creates the shortcode.
	 *
	 * @param array $atts The attributes for the shortcode passed by WordPress.
	 * @return string
	 */
	public function shortcode( $atts ): string {
		$atts = shortcode_atts(
			array(
				'directory' => null,
				'id'        => null,
			),
			$atts
		);

		$options = get_option( 'wp_custom_dir', array() );
		if ( empty( $options['tpl_search'] ) ) {
			return __( 'The search form template is empty', 'wp-custom-dir' );
		}

		$out  = '<form id="">';
		$out .= $options['tpl_search'];
		$out .= '</form>';

		return $out;
	}

	/**
	 * Name of the shortcode since is used in several places.
	 *
	 * @var string
	 */
	protected $shortcode_name = 'custom-directory-search';

	/**
	 * Setter for $this->shortcode_name.
	 *
	 * @param string $name The new name for the shortcode.
	 * @return self
	 */
	public function set_shortcode_name( $name ): self {
		$this->shortcode_name = $name;
		return $this;
	}

}
