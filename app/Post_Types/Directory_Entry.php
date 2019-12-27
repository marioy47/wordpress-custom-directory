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
}


