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
 * Creates a shortcode for creating a list of ites.
 *
 * Also registers the shortcode in Shortcodes Ultimate.
 */
class Custom_Directory_List {
	/**
	 * Singleton.
	 */
	public function __construct() {

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
	 * Executes the add_shortcode() and add_filter() functions.
	 *
	 * @return self
	 */
	public function start(): self {
		add_shortcode( 'custom-directory-list', array( $this, 'shortcode' ) );
		return $this;
	}

	/**
	 * Creates the shortcode.
	 *
	 * @param array $atts The shortcode arguementes ass passed by WordPress.
	 * @return string
	 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'directory' => '',
				'id'        => uniqid( 'custom-directory-list' ),
			),
			$atts
		);

		// Build the template.
		$options = get_option( 'wp_custom_dir', array() );
		$loader  = new ArrayLoader(
			array(
				'tpl_list.html' => array_key_exists( 'tpl_list', $options ) ? $options['tpl_list'] : '{{title}}',
			)
		);
		$twig    = new Environment(
			$loader,
			array(
				'autoescape' => false,
			)
		);

		// Build the query.
		$query_params = array(
			'post_type' => $this->post_type,
			'tax_query' => array(
				array(
					'taxonomy' => $this->taxonomy,
					'field'    => 'term_id',
					'terms'    => 3,
				),
			),
		);
		$query        = new \WP_Query( $query_params );

		// Build the list.
		$out = '<ul class="custom-directory-list" id="' . $atts['id'] . '">';
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();
			$params  = array(
				'title'   => get_the_title(),
				'excerpt' => get_the_excerpt(),
				'author'  => get_the_author(),
				'link'    => get_the_permalink( $post_id ),
			);
			$out    .= '<li class="directory-item">' . $twig->render( 'tpl_list.html', $params );
		}
		$query->reset_post_data();

		// Return the content.
		$out .= '</ul><!-- /custom-directory-list -->';
		return $out;
	}

	/**
	 * Name of the post type for the directory items.
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Setter for $this->post_type.
	 *
	 * @param string $type The name of the post-type.
	 * @return self
	 */
	public function set_post_type( $type ): self {
		$this->post_type = $type;
		return $this;
	}

	/**
	 * Name of the taxonomy of the directory types.
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Setter for $this->taxonomy.
	 *
	 * @param string $tax The name of the taxonomy.
	 * @return self
	 */
	public function set_taxonomy( $tax ): self {
		$this->taxonomy = $tax;
		return $this;
	}
}
