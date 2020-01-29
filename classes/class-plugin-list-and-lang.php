<?php
/**
 * Changes on the Dashboard Plugin List
 *
 * @package Wordpress_Custom_Directory
 */

namespace WpCustomDir;

/**
 * Changes the plugin list adding links under the plugin name.
 */
class Plugin_List_And_Lang {

	/**
	 * Singleton.
	 */
	private function __construct() {

	}

	/**
	 * Factory function
	 *
	 * @return self
	 */
	public static function factory(): self {
		static $obj;
		return isset( $obj ) ? $obj : $obj = new self();
	}

	/**
	 * Executes the add_action() and add_filter() functions form WordPress.
	 *
	 * @return self
	 */
	public function start(): self {
		add_filter( 'plugin_action_links_' . plugin_basename( $this->plugin_file ), array( $this, 'add_links' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		return $this;
	}

	/**
	 * Adds the links.
	 *
	 * @param array $links Array of links passed by WordPress.
	 * @return array
	 */
	public function add_links( $links ): array {
		$links[] = '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', 'wp-custom-dir' ) . '</a>';
		$links[] = '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '-help">' . __( 'Documentation', 'wp-custom-dir' ) . '</a>';

		return $links;
	}

	/**
	 * Loads the language on the plugins_loaded hook.
	 *
	 * @return self
	 */
	public function load_textdomain(): self {
		load_plugin_textdomain( 'wp-custom-dir', false, basename( dirname( $this->plugin_file ) ) . '/languages' );
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

	/**
	 * Slug used in the plugin links.
	 *
	 * @var string
	 */
	protected $plugin_slug = '';

	/**
	 * Setter for $this->plugin_slug.
	 *
	 * @param string $slug Short string with no spaces.
	 * @return self
	 */
	public function set_plugin_slug( $slug ): self {
		$this->plugin_slug = $slug;
		return $this;
	}

}
