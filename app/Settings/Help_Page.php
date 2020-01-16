<?php
/**
 * Settings Page.
 *
 * @package Wordpress_Custom_Directory
 */

namespace WpCustomDir\Settings;

use Michelf\MarkdownExtra;

/**
 * Creates a simple page with usage instructions.
 */
class Help_Page {

	/**
	 * Singleton.
	 */
	private function __construct() {

	}

	/**
	 * Factory constructor.
	 *
	 * @return self
	 */
	public static function factory(): self {
		static $obj;
		return isset( $obj ) ? $obj : $obj = new self();
	}

	/**
	 * Executes the add_action() and add_filter() functions.
	 *
	 * @return self
	 */
	public function start(): self {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		return $this;
	}

	/**
	 * Creates the page, but without the menu.
	 *
	 * @return self
	 */
	public function add_page(): self {
		add_options_page(
			__( 'WordPress Custom Directory Help', 'wp-custom-dir' ),
			false,
			'manage_options',
			$this->plugin_slug . '-help',
			array( $this, 'create_page' ),
			null
		);
		return $this;
	}

	/**
	 * Converts the Mardkwon to HTML and outsputti it.
	 *
	 * @return self
	 */
	public function create_page(): self {

		$file = dirname( $this->plugin_file ) . '/PLUGIN_HELP.md';

		if ( ! is_file( $file ) ) {
			esc_html_e( 'Could not find the help file in the plugin directory', 'wp-custom-dir' );
			return $this;
		}

		echo '<style>ul { list-style: inside ;} code{ padding: 0; margin: 3px 5px 2px 5px;} </style>';
		echo '<div class="wrap">';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo MarkdownExtra::defaultTransform( file_get_contents( $file ) );
		echo '</div>';
		return $this;
	}

	/**
	 * Plugin settings slug
	 *
	 * @var string
	 */
	protected $plugin_slug;

	/**
	 * Getter for $this->plugin_slug.
	 *
	 * @param string $slug The slug for the plugin settings (wihtout the "-help" suffix).
	 * @return self
	 */
	public function set_plugin_slug( $slug ): self {
		$this->plugin_slug = $slug;
		return $this;
	}

	/**
	 * Absolute path the to initial plugin file.
	 *
	 * @var string
	 */
	protected $plugin_file = '';

	/**
	 * Setter for $this->plugin_file.
	 *
	 * @param string $file The path.
	 * @return self
	 */
	public function set_plugin_file( $file ): self {
		$this->plugin_file = $file;
		return $this;
	}


}
