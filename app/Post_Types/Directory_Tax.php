<?php
/**
 * Copyright Mario Yepes
 *
 * @package Wordpres_Custom_Directory
 */

namespace WpCustomDir\Post_Types;

/**
 * Creates the taxonomy for multiple directories.
 */
class Directory_Tax {
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
	 * Executes the add_action() and add_filter() functions.
	 *
	 * @return self
	 */
	public function start(): self {
		add_action( 'init', array( $this, 'register_tax' ) );
		return $this;
	}

	/**
	 * Register the taxonomy.
	 *
	 * @return void
	 */
	public function register_tax() {
		$labels = array(
			'name'          => __( 'Directories', 'wp-custom-dir' ),
			'singular_name' => __( 'Directory', 'wp-custom-dir' ),
		);

		$args = array(
			'label'                 => __( 'Directories', 'wp-custom-dir' ),
			'labels'                => $labels,
			'public'                => true,
			'publicly_queryable'    => true,
			'hierarchical'          => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => true,
			'query_var'             => true,
			'rewrite'               => array(
				'slug'       => 'directory-tax',
				'with_front' => true,
			),
			'show_admin_column'     => false,
			'show_in_rest'          => true,
			'rest_base'             => 'directory_tax',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'show_in_quick_edit'    => false,
		);
		register_taxonomy( $this->taxonomy, array( $this->post_type ), $args );
	}

	/**
	 * Post type name.
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Setter for the post-type name.
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
