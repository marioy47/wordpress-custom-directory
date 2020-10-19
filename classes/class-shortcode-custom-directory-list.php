<?php
/**
 * Copyright Mario Yepes <marioy47@gmail.com>
 *
 * @package Wordpress_Custom_Directory
 */

namespace WpCustomDir;

use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * Creates a shortcode for creating a list of ites.
 *
 * Also registers the shortcode in Shortcodes Ultimate.
 */
class Shortcode_Custom_Directory_List {
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
		add_shortcode( $this->shortcode_name, array( $this, 'shortcode' ) );

		// Shortcodes Ultimate.
		add_filter( 'su/data/shortcodes', array( $this, 'su_register' ) );
		add_shortcode( get_option( 'su_option_prefix', 'su_' ) . $this->shortcode_name, array( $this, 'shorcode' ) );
		return $this;
	}

	/**
	 * Creates the shortcode.
	 *
	 * @param array  $atts The shortcode arguementes ass passed by WordPress.
	 * @param string $template The template provided by WordPress.
	 * @return string
	 */
	public function shortcode( $atts, $template ) {
		$atts = shortcode_atts(
			array(
				'directory'  => null,
				'id'         => null,
				'class'      => null,
				'order'      => 'menu_order ASC',
				'filter_key' => null,
				'filter_val' => null,
			),
			$atts
		);
		if ( empty( $atts['id'] ) ) {
			$atts['id'] = 'custom-directory-list-' . $atts['directory'];
		}

		// Get the template from options if none provided.
		if ( empty( $template ) ) {
			$options  = get_option( 'wp_custom_dir', array() );
			$template = ! empty( $options['tpl_list'] ) ? $options['tpl_list'] : '{{title}}';
		}

		$loader = new ArrayLoader( array( 'tpl_list.html' => $template ) );
		$twig   = new Environment( $loader, array( 'autoescape' => false ) );

		foreach ( explode( ',', $atts['order'] ) as $line ) {
			list($field, $direction) = array_map( 'trim', explode( ' ', trim( $line ) ) );
			$this->add_query_sort( trim( $field ), trim( $direction ) );
		}

		if ( ! empty( $atts['filter_key'] ) ) {
			$this->add_query_filter( trim( $atts['filter_key'] ), trim( $atts['filter_val'] ) );
		}

		$this->add_query_tax( $atts['directory'] );

		$query = new \WP_Query( $this->get_query_params() );

		// Build the list.
		$out = '<ul class="custom-directory-list" id="' . $atts['id'] . '">';
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();
			$params  = array(
				'title'   => '<span class="search-item" data-field="title">' . get_the_title() . '</span>',
				'excerpt' => '<span class="search-item" data-field="excerpt">' . get_the_excerpt() . '</span>',
				'author'  => '<span class="search-item" data-field="author">' . get_the_author() . '</span>',
				'link'    => get_the_permalink( $post_id ),
				'image'   => get_the_post_thumbnail_url( $post_id ),
			);

			// Si esta instalado ACF.
			if ( function_exists( 'get_fields' ) ) {
				$fields = (array) get_fields( $post_id );
				foreach ( $fields as $name => $value ) {
					$params[ $name ] = '<span class="search-item" data-field="' . $name . '">' . $value . '</span>';
				}
			}

			$out .= '<li class="directory-item">' . $twig->render( 'tpl_list.html', $params ) . '</li>';
		}
		$query->reset_post_data();

		// Return the content.
		$out .= '</ul><!-- /custom-directory-list -->';
		return $out;
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
			'name'     => __( 'Custom Directory List', 'wp-custom-dir' ),
			'type'     => 'wrap',
			'group'    => 'content other',
			'atts'     => array(
				'directory'    => array(
					'type'    => 'text',
					'default' => '',
					'name'    => __( 'Directory ID', 'wp-custom-dir' ),
					'desc'    => __( 'The ID of the directory to list', 'wp-custom-dir' ),
				),
				'id'           => array(
					'type'    => 'text',
					'default' => '',
					'name'    => __( 'Custom CSS id', 'wp-custom-dir' ),
					'desc'    => __( 'Allows you to specify the <ul> id for the list', 'wp-custom-dir' ),
				),
				'class'        => array(
					'type'    => 'text',
					'default' => '',
					'name'    => __( 'HTML Class', 'wp-custom-dir' ),
					'desc'    => __( 'Add custom CSS class for styling', 'wp-custom-dir' ),
				),
				'order'        => array(
					'type'    => 'text',
					'default' => 'menu_order ASC',
					'name'    => __( 'Sort by', 'wp-custom-dir' ),
					'desc'    => __( 'Possible options: <code>title, date, menu_order, content</code> and direction is ASC or DESC', 'wp-custom-dir' ),
				),
				'filter_key'   => array(
					'type'    => 'text',
					'default' => '',
					'name'    => __( 'Filter by this field', 'wp-custom-dir' ),
					'desc'    => __( 'Select a field to filter the items by (use in conjuction to next field)', 'wp-custom-dir' ),
				),
				'filter_value' => array(
					'type'    => 'text',
					'default' => '',
					'name'    => __( 'Filter REGEX', 'wp-custom-dir' ),
					'desc'    => __( 'REGEX to apply the the prev field', 'wp-custom-dir' ),
				),
			),
			'desc'     => __( 'Shows a list of the content for a directory. if you provide "content", it will be used as the template for each directory item.', 'wp-custom-dir' ),
			'icon'     => 'sitemap',
			'function' => array( $this, 'shortcode' ),
		);
		return $shortcodes;
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

	/**
	 * The name of the shortcode.
	 *
	 * Set it here since it will be used in several places.
	 *
	 * @var string
	 */
	protected $shortcode_name = 'custom-directory-list';

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
	 * Array for storing the WP_query
	 *
	 * @var array
	 */
	protected $query_params;

	/**
	 * Adds to $this->getQueryParams the filter options
	 *
	 * @param string $key Name of the field to filter by.
	 * @param string $val Value that neds to have to be filred.
	 * @return self
	 */
	public function add_query_filter( $key, $val ): self {
		if ( empty( $this->query_params ) ) {
			$this->reset_query_params();
		}
		$this->query_params['meta_query'][ $key . '_clause' ] = array(
			'key'     => $key,
			'value'   => '^' . strtolower( $val ) . '$',
			'compare' => 'REGEXP',
			'type'    => 'CHAR',
		);
		return $this;
	}

	/**
	 * Adds to $this->get_query_params the sorting options.
	 *
	 * The filed will alway have the ASC direction for sorting.
	 *
	 * @param string $key Name of the field to sort by.
	 * @param string $direction Can be ASC or DESC.
	 * @return self
	 */
	public function add_query_sort( $key, $direction = 'ASC' ): self {
		if ( empty( $this->query_params ) ) {
			$this->reset_query_params();
		}

		if ( empty( $direction ) ) {
			$direction = 'ASC';
		}

		// "order" no es compatible con meta_query. Entonces si hay meta, lo debemos borrar.
		if ( ! is_array( $this->query_params['orderby'] ) ) {
			$this->query_params['orderby'] = array();
			unset( $this->query_params['order'] );
		}

		// Los campos nativos de WP no necesitan meta_query.
		if ( in_array( $key, array( 'menu_order', 'title', 'content', 'date', 'rand' ), true ) ) {
			$this->query_params['orderby'][ $key ] = $direction;
		} elseif ( ! array_key_exists( $key . '_clause', $this->query_params['meta_query'] ) ) {
			$this->query_params['meta_query'][ $key . '_clause' ] = array(
				'key'     => $key,
				'compare' => 'EXISTS',
			);
			$this->query_params['orderby'][ $key . '_clause' ]    = $direction;
		} else {
			$this->query_params['orderby'][ $key . '_clause' ] = $direction;
		}

		return $this;
	}

	/**
	 * Add a taxonomy query, which is what we use to have multiple directories.
	 *
	 * @param string $taxonomy Name of the directory.
	 * @return self
	 */
	public function add_query_tax( $taxonomy ): self {
		if ( empty( $this->query_params ) ) {
			$this->reset_query_params();
		}
		$this->query_params['tax_query'] = array(
			array(
				'taxonomy' => $this->taxonomy,
				'field'    => 'slug',
				'terms'    => $taxonomy,
			),
		);
		return $this;
	}

	/**
	 * Reset $this->get_query_params to start a new query.
	 *
	 * Useful for testing and when the shortcode doesn't have any filter/sport parammeters.
	 *
	 * @return self
	 */
	public function reset_query_params(): self {
		$this->query_params = array(
			'post_status'    => 'publish',
			'post_type'      => $this->post_type,
			'posts_per_page' => -1,
			'order'          => 'ASC',
			'orderby'        => 'menu_order title',
			'meta_query'     => array(
				'relation' => 'AND',
			),
		);
		return $this;
	}

	/**
	 * Getter for $this->get_query_params
	 *
	 * @return array
	 */
	public function get_query_params(): array {
		if ( empty( $this->query_params ) ) {
			$this->reset_query_params();
		}
		return $this->query_params;
	}

}
