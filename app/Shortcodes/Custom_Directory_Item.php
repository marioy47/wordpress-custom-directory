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

		// Shortcodes ultimate.
		add_filter( 'su/data/shortcodes', array( $this, 'su_register' ) );
		add_shortcode( get_option( 'su_option_prefix', 'su_' ) . $this->shortcode_name, array( $this, 'shorcode' ) );

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

		// TODO: Move this to a central lib since its the same as the render post function.
		$params = array(
			'content' => $post->post_content,
			'title'   => $post->post_title,
			'excerpt' => get_the_excerpt( $post->ID ),
			'link'    => get_the_permalink( $post->ID ),
			'image'   => get_the_post_thumbnail_url( $post->ID ),
		);
		if ( function_exists( 'get_fields' ) ) {
			$fields = (array) get_fields( get_the_ID() );
			foreach ( $fields as $name => $value ) {
				$params[ $name ] = $value;
			}
		}

		return $twig->render( 'tpl_single.html', $params );
	}

	/**
	 * Registeres and configures the shortcode with ShortcodesUlitimate.
	 *
	 * @param array $shortcodes The array of shortcodes to be modified.
	 * @return array
	 * @link https://docs.getshortcodes.com/article/45-how-to-add-custom-shortcodes
	 */
	public function su_register( $shortcodes ): array {
		$shortcodes[ $this->shortcode_name ] = array(
			'name'     => __( 'Custom Directory Item', 'wp-custom-dir' ),
			'type'     => 'wrap',
			'group'    => 'other',
			'atts'     => array(
				'item' => array(
					'type'    => 'text',
					'default' => '',
					'name'    => __( 'Item ID', 'wp-custom-dir' ),
					'desc'    => __( 'ID for the item you want to display', 'wp-custom-dir' ),
				),
				'slug' => array(
					'type'    => 'text',
					'default' => '',
					'name'    => __( 'Slug', 'wp-custom-dir' ),
					'desc'    => __( 'URL slug for the item content you want to display', 'wp-custom-dir' ),
				),
			),
			'content'  => '',
			'desc'     => __( 'Adds an item from the directory into the current page. If you provide "content", that will be used as the template. If you dont provide the "slug" or the "item", then they will be extracted from the URL parammeters (mi-site.com/path/?item=34', 'wp-custom-dir' ),
			'icon'     => 'arrow-circle-o-right',
			'function' => array( $this, 'shortcode' ),
		);

		return $shortcodes;
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
