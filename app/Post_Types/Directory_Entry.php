<?php
/**
 * Creates the directory_entry post type.
 *
 * @package Wordpress_Custom_Directory
 */

namespace WpCustomDir\Post_Types;

/**
 * Creates the post type directory_entry and its metaboxes.
 */
class Directory_Entry {

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
		add_action( 'init', array( $this, 'register_type' ) );
		add_action( 'manage_' . $this->post_type . '_posts_columns', array( $this, 'add_columns' ) );
		add_action( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, 'add_column_content' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( $this, 'filter_posts' ), 10, 2 );
		return $this;
	}

	public function register_type() {

		$labels = array(
			'name'          => __( 'Custom Directory Entries', 'custom-post-type-ui' ),
			'singular_name' => __( 'Custom Directory Entry', 'custom-post-type-ui' ),
		);

		$args = array(
			'label'                 => __( 'Custom Directory Entries', 'custom-post-type-ui' ),
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
				'slug'       => 'directory-entry',
				'with_front' => true,
			),
			'query_var'             => true,
			'menu_icon'             => 'dashicons-tide',
			'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'author' ),
		);

		register_post_type( $this->post_type, $args );
	}

	public function add_columns( $cols ) {
		$cols['directory']  = __( 'Directory', 'wp-custom-dir' );
		$cols['feat_image'] = __( 'Feat Img', 'wp-custom-dir' );
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
				'show_option_all' => __( "Show All {$taxonomy->label}", 'wp-custom-dir' ),
				'taxonomy'        => $this->taxonomy,
				'name'            => $this->taxonomy,
				'orderby'         => 'name',
				'selected'        => isset( $_REQUEST[ $this->taxonomy ] ) ? $_REQUEST[ $this->taxonomy ] : '',
				'show_count'      => true, // Show number of post in parent term.
				'hide_empty'      => false, // Don't show posts w/o terms.
				'value_field'     => 'slug',
				'hierarchical'    => true,
			)
		);
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
	public function set_post_type( $type ): self {
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
}


