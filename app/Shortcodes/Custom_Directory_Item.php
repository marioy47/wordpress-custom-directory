<?php
/**
 * Copyright Mario Yepes <marioy47@gmail.com>
 *
 * @package Wordpress_Custom_Directory
 */

namespace WpCustomDir\Shortcodes;

use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * Creates the shortcode for displaying a single item.
 */
class Custom_Directory_Item {
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
	 * Execute the add_actioN(), add_filter() and add_shortdcode() functions.
	 *
	 * @return self
	 */
	public function start(): self {
		add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
		add_shortcode( $this->shortcode_name, array( $this, 'shortcode' ) );
		return $this;
	}

	/**
	 * Enabling query vars for single item display by shortcode.
	 *
	 * @param array $vars Original array of query vars passed by WordPress.
	 * @return array
	 */
	public function register_query_vars( $vars ): array {
		$vars[] = $this->query_vars_prefix . 'item';
		$vars[] = $this->query_vars_prefix . 'slug';
		return $vars;
	}

	/**
	 * Creates the shortcode itself.
	 *
	 * @param array  $atts The Shortcode parameters.
	 * @param string $template The template to be used if any.
	 * @return string
	 */
	public function shortcode( $atts, $template ): string {
		$atts = shortcode_atts(
			array(
				'slug' => get_query_var( $this->query_vars_prefix . 'slug' ),
				'item' => get_query_var( $this->query_vars_prefix . 'item' ),
			),
			$atts
		);

		if ( empty( $atts['item'] ) && empty( $atts['slug'] ) ) {
			return __( 'You have to provide a slug or an id for the content to be diplayed', 'wp-custom-dir' );
		}

		// Get the post to be displayed.
		$post = null;
		if ( ! empty( $atts['slug'] ) ) {
			$post = get_page_by_path( trim( $atts['slug'] ), OBJECT, $this->post_type );
		} elseif ( ! empty( $atts['item'] ) ) {
			$post = get_post( $atts['item'] );
		}
		if ( empty( $post ) ) {
			return __( 'Could not find the specified item', 'wp-custom-dir' );
		}

		// Get the template to parse the content.
		if ( empty( $template ) ) {
			$options  = get_option( 'wp_custom_dir', array() );
			$template = $options['tpl_single'];
		}
		if ( empty( $template ) ) {
			return __( 'No template for the single item. Create a template on settings or pass it a the shortcode content', 'wp-custom-dir' );
		}

		$loader = new ArrayLoader( array( 'tpl_single.html' => $template ) );
		$twig   = new Environment( $loader, array( 'autoescape' => false ) );

		$params = array(
			'content' => $post->post_content,
			'title'   => $post->post_title,
			'image'   => get_the_post_thumbnail_url( $post->ID ),
		);

		return $twig->render( 'tpl_single.html', $params );
	}

	/**
	 * The name of the shortcode.
	 *
	 * Set it here since it will be used in several places.
	 *
	 * @var string
	 */
	protected $shortcode_name = null;

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
	 * Save the post type in a var for multiple uses.
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Setter for $this->post_type.
	 *
	 * @param string $type New name for the post-type.
	 * @return self
	 */
	public function set_post_type( $type ) : self {
		$this->post_type = $type;
		return $this;
	}

	/**
	 * All the qiuery vars of this plugin have the same prefix, here we store the prefix.
	 *
	 * @var string
	 */
	protected $query_vars_prefix;

	/**
	 * Setter for $this->query_vars_prefix.
	 *
	 * @param string $prefix The prefix. Pe: "custom-dir-".
	 * @return self
	 */
	public function set_query_vars_prefix( $prefix ): self {
		$this->query_vars_prefix = $prefix;
		return $this;
	}

}
