<?php
/**
 * Creates the directory_entry post type.
 *
 * @package Wordpress_Custom_Directory
 */

namespace WpCustomDir;

use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * Creates the post type directory_entry and its metaboxes.
 */
class Type_Directory_Entry {

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

		// Register type.
		add_action( 'init', array( $this, 'register_type' ) );

		// Change the display of the list of post (list of items) in the dashboard.
		add_action( 'manage_' . $this->post_type . '_posts_columns', array( $this, 'add_columns' ) );
		add_action( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, 'add_column_content' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( $this, 'filter_posts' ), 10, 2 );

		// Change the output in the frontend for a single item (not the shortcodes).
		add_filter( 'the_content', array( $this, 'change_post_content' ) );

		// If in settings is set "remove_title" or "change_layout" we enqueue the functions that will do that.
		$this->options = get_option( 'wp_custom_dir', array() );
		if ( array_key_exists( 'remove_title', $this->options ) && $this->options['remove_title'] ) {
			add_action( 'wp_head', array( $this, 'remove_title' ) );
		}
		if ( array_key_exists( 'change_layout', $this->options ) && $this->options['change_layout'] ) {
			add_action( 'wp_head', array( $this, 'change_layout' ) );
		}

		return $this;
	}

	/**
	 * Takes care of actually creating the new post type.
	 *
	 * The code was created with Custom Post Type UI.
	 *
	 * @return void
	 */
	public function register_type() {

		$labels = array(
			'name'          => ! empty( $this->options['sidebar_name'] ) ? $this->options['sidebar_name'] : __( 'Custom Directory Entries', 'wp-custom-dir' ),
			'singular_name' => __( 'Custom Directory Entry', 'wp-custom-dir' ),
		);

		$args = array(
			'label'                 => __( 'Custom Directory Entries', 'wp-custom-dir' ),
			'labels'                => $labels,
			'description'           => '',
			'public'                => true,
			'publicly_queryable'    => true,
			'show_ui'               => true,
			'delete_with_user'      => false,
			'show_in_rest'          => true,
			'rest_base'             => '',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'has_archive'           => false,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => true,
			'delete_with_user'      => false,
			'exclude_from_search'   => false,
			'capability_type'       => 'post',
			'map_meta_cap'          => true,
			'hierarchical'          => false,
			'rewrite'               => array(
				'slug'       => ! empty( $this->options['slug'] ) ? $this->options['slug'] : 'directory-entry',
				'with_front' => true,
			),
			'query_var'             => true,
			'menu_icon'             => 'dashicons-networking',
			'supports'              => array(
				'title',
				'editor',
				'thumbnail',
				'excerpt',
				'revisions',
				'author',
				'genesis-seo',
				' genesis-cpt-archives-settings',
				'genesis-layouts',
			),
		);
			register_post_type( $this->post_type, $args );
	}

	/**
	 * Add a new columns on the post list in the dashboard.
	 *
	 * @param array $cols The original list of columns provided by WordPress.
	 * @return array
	 */
	public function add_columns( $cols ) {
		$cols['directory']  = __( 'Directory', 'wp-custom-dir' );
		$cols['feat_image'] = __( 'Feat Img', 'wp-custom-dir' );
		$cols['menu_order'] = __( 'Order', 'wp-custom-dir' );
		return $cols;
	}

	/**
	 * Adds a new column header on the list of directory-entries.
	 *
	 * @param array $col Array of columns provided by  WordPress.
	 * @param int   $post_id The ID of the current post (for each item of the list).
	 * @return void
	 */
	public function add_column_content( $col, $post_id ) {
		switch ( $col ) {
			case 'directory':
				echo get_the_term_list( $post_id, $this->taxonomy );
				break;
			case 'feat_image':
				echo get_the_post_thumbnail( $post_id, array( '50', '50' ) );
				break;
			case 'menu_order':
				global $post;
				echo esc_html( $post->menu_order );
		}
	}

	/**
	 * Adds the content for each item on the direcotry-entries.
	 *
	 * @param string $post_type The name of the current post type we're listing.
	 * @param string $which Value provided by WordPress.
	 * @return void
	 */
	public function filter_posts( $post_type, $which ) {
		if ( $this->post_type !== $post_type ) {
			return;
		}

		$taxonomy = get_taxonomy( $this->taxonomy );

		wp_dropdown_categories(
			array(
				// translators: Show All {taxonomy label}.
				'show_option_all' => sprintf( __( 'Show All %s', 'wp-custom-dir' ), $taxonomy->label ),
				'taxonomy'        => $this->taxonomy,
				'name'            => $this->taxonomy,
				'orderby'         => 'name',
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				'selected'        => isset( $_REQUEST[ $this->taxonomy ] ) ? $_REQUEST[ $this->taxonomy ] : '',
				'show_count'      => true, // Show number of post in parent term.
				'hide_empty'      => false, // Don't show posts w/o terms.
				'value_field'     => 'slug',
				'hierarchical'    => true,
			)
		);
	}

	/**
	 * Applies the template to the content.
	 *
	 * @param string $content The content passed by WordPress.
	 * @return string
	 */
	public function change_post_content( $content ) {
		if ( get_post_type() !== $this->post_type ) {
			return $content;
		}

		$loader = new ArrayLoader(
			array(
				'tpl_single.html' => array_key_exists( 'tpl_single', $this->options ) ? $this->options['tpl_single'] : '{{content}}',
			)
		);
		$twig   = new Environment( $loader, array( 'autoescape' => false ) );

		// do NOT add 'excerpt' since it hangs WordPress!.
		$params = array(
			'content' => $content,
			'title'   => get_the_title(),
			'author'  => get_the_author(),
			'link'    => get_the_permalink(),
			'image'   => get_the_post_thumbnail_url(),
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
	 * Removes the title if in the settings is checked.
	 *
	 * Works only on genesis templates for now.
	 *
	 * @return self
	 */
	public function remove_title(): self {
		if ( get_post_type() !== $this->post_type ) {
			return $this;
		}
		// remove title.
		remove_action( 'genesis_post_title', 'genesis_do_post_title' );
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

		// Remove header markup.
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );

		// remove entry meta.
		remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );

		return $this;
	}

	/**
	 * Changes the layout if is set.
	 *
	 * @return self
	 */
	public function change_layout(): self {
		$layout = $this->options['change_layout'];
		add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_' . $layout );
		add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_' . $layout );

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
	 * Taxonomy name.
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Seetter for $this->taxonomy.
	 *
	 * @param string $tax new slug for the taxonomy.
	 * @return self
	 */
	public function set_taxonomy( $tax ): self {
		$this->taxonomy = $tax;
		return $this;
	}

	/**
	 * The settings values since we need them in several places.
	 *
	 * @var array
	 */
	protected $options = array();
}


