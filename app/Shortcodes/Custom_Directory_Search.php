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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Shortcodes Ultimate.
		add_filter( 'su/data/shortcodes', array( $this, 'su_register' ) );
		add_shortcode( get_option( 'su_option_prefix', 'su_' ) . $this->shortcode_name, array( $this, 'shorcode' ) );

		return $this;
	}

	/**
	 * Creates the shortcode.
	 *
	 * @param array  $atts The attributes for the shortcode passed by WordPress.
	 * @param string $template If content is passed in the shortcode, it will be assumed to be the template to use.
	 * @return string
	 */
	public function shortcode( $atts, $template ): string {
		$atts = shortcode_atts(
			array(
				'directory' => null,
				'id'        => uniqid( 'custom-directory-search-' ),
			),
			$atts
		);

		$options = get_option( 'wp_custom_dir', array() );
		if ( empty( $options['tpl_search'] ) ) {
			return __( 'The search form template is empty', 'wp-custom-dir' );
		}

		wp_enqueue_script( 'wp-custom-dir' );

		$out  = '<form id="' . $atts['id'] . '" class="custom-directory-search" data-target="custom-directory-list-' . $atts['directory'] . '">';
		$out .= ! empty( $template ) ? $template : $options['tpl_search'];
		$out .= '</form>';

		return $out;
	}

	/**
	 * Adds the scripts required for the dynamic search of a directory.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_register_script( 'wp-custom-dir', plugin_dir_url( $this->plugin_file ) . 'js/frontend.js', array(), WP_CUSTOM_DIRECTORY_VERSION, true );
	}

	/**
	 * Registers the current shortcode in Shortcodes Ultimate.
	 *
	 * @param array $shortcodes The current array of shortcodes passed by SU.
	 * @return array
	 * @link https://docs.getshortcodes.com/article/45-how-to-add-custom-shortcodes
	 */
	public function su_register( $shortcodes ): array {
		$shortcodes[ $this->shortcode_name ] = array(
			'name'     => __( 'Custom Directory Search Form', 'wp-custom-dir' ),
			'type'     => 'wrap',
			'group'    => 'other',
			'atts'     => array(
				'directory' => array(
					'type'    => 'text',
					'default' => '',
					'name'    => __( 'Directory', 'wp-custom-dir' ),
					'desc'    => __( 'Provide the ID or slug for the custom directory you want to search', 'wp-custom-dir' ),
				),
				'id'        => array(
					'type'    => 'text',
					'default' => '',
					'name'    => __( 'Form ID', 'wp-custom-dir' ),
					'desc'    => __( 'You can provide an optional ID for this form', 'wp-custom-dir' ),
				),
			),
			'content'  => '',
			'desc'     => __( 'Creates a form for searching the custom directory. If you provide "content" it will be used as the form content, otherwise the template in "Settings > WP custom dir" will be used as content.', 'wp-custom-dir' ),
			'icon'     => 'dot-circle-o',
			'function' => array( $this, 'shortcode' ),
		);

		return $shortcodes;
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

	/**
	 * The complete path to the main plugin file.
	 *
	 * @var string
	 */
	protected $plugin_file;

	/**
	 * Setter for $this->plugin_file.
	 *
	 * @param string $file The path to the file.
	 * @return self
	 */
	public function set_plugin_file( $file ): self {
		$this->plugin_file = $file;
		return $this;
	}

}
