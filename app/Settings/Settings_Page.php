<?php
/**
 * Creates a Settings Page for plugin configuration
 *
 * @package Wordpress_Custom_Directory
 */

namespace WpCustomDir\Settings;

/**
 * Creates the settings page.
 */
class Settings_Page {

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
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'register_fields' ) );

		return $this;
	}

	/**
	 * Creates the admin menu and sets-up the settings page.
	 *
	 * @return self
	 */
	public function add_menu(): self {
		add_options_page(
			__( 'WordPress Custom Directory', 'wp-custom-dir' ),
			__( 'WP Custom Directory', 'wp-custom-dir' ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'create_page' ),
			null
		);
		return $this;
	}

	/**
	 * Creates the Settings Page HTML.
	 *
	 * @return self
	 */
	public function create_page(): self {
		$this->options = get_option( $this->options_name, array() );
		?>
	<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form action="options.php" method="post">
		<?php
		settings_fields( $this->plugin_slug );
		do_settings_sections( $this->plugin_slug );
		submit_button( __( 'Save Directory Settings', 'wp-custom-dir' ) );
		?>
	</form>
	</div>
		<?php
		return $this;
	}

	/**
	 * Registers the options, sections and fields for the plugin.
	 *
	 * @return self
	 */
	public function register_fields(): self {
		register_setting( $this->plugin_slug, $this->options_name );

		// Mis options.
		$current_section = 'misc-options';
		add_settings_section(
			$current_section,
			__( 'Paths and Misc options', 'wp-custom-dir' ),
			array( $this, 'section_misc_options' ),
			$this->plugin_slug
		);
		add_settings_field(
			'slug',
			__( 'Choose an slug or base path for the directory items', 'wp-custom-dir' ),
			array( $this, 'field_slug' ),
			$this->plugin_slug,
			$current_section
		);

		// Templates.
		$current_section = 'templates';
		add_settings_section(
			$current_section,
			__( 'Templates', 'wp-custom-dir' ),
			array( $this, 'section_templates' ),
			$this->plugin_slug
		);
		add_settings_field(
			'tpl-single',
			__( 'Single element template', 'wp-custom-dir' ),
			array( $this, 'field_tpl_single' ),
			$this->plugin_slug,
			$current_section
		);
		add_settings_field(
			'tpl-list',
			__( 'List element template', 'wp-custom-dir' ),
			array( $this, 'field_tpl_list' ),
			$this->plugin_slug,
			$current_section
		);
		add_settings_field(
			'tpl-search',
			__( 'Search form template', 'wp-custom-dir' ),
			array( $this, 'field_tpl_search' ),
			$this->plugin_slug,
			$current_section
		);
		return $this;
	}

	/**
	 * Desc for misc options section.
	 *
	 * @return self
	 */
	public function section_misc_options(): self {
		esc_html_e( 'Options that changes the directory behaviour', 'wp-custom-dir' );
		return $this;
	}

	/**
	 * Creates the slug (path) input field.
	 *
	 * @return self
	 */
	public function field_slug(): self {
		$val = array_key_exists( 'slug', $this->options ) ? $this->options['slug'] : null;
		echo '<input type="text" name="' . esc_attr( $this->options_name ) . '[slug]" value="' . esc_attr( $val ) . '" placeholder="custom-path">';
		return $this;
	}

	/**
	 * Section for templates.
	 *
	 * Adds some description and help for the user.
	 *
	 * @return self
	 */
	public function section_templates(): self {
		esc_html_e( 'Placeholder description on how the templates work...', 'wp-custom-dir' );
		return $this;
	}

	/**
	 * Field for the template creation of a single element.
	 *
	 * @return self
	 */
	public function field_tpl_single(): self {
		$val         = array_key_exists( 'tpl_single', $this->options ) ? $this->options['tpl_single'] : '';
		$placeholder = <<<EOP1
<h1>{{title}}</h1>
<div class="content">{{content}}</div>
EOP1;
		// phpcs:ignore
		echo '<textarea name="' . esc_attr( $this->options_name ) . '[tpl_single]" placeholder="' . esc_attr( $placeholder ) . '">' . $val . '</textarea>';
		return $this;
	}

	/**
	 * Field for the template creation of a list item.
	 *
	 * @return self
	 */
	public function field_tpl_list(): self {
		$val         = array_key_exists( 'tpl_list', $this->options ) ? $this->options['tpl_list'] : '';
		$placeholder = <<<EOP2
<div class="left">{{title}}</div><div class="right">{{summary}}</div>
EOP2;
		// phpcs:ignore
		echo '<textarea name="' . esc_attr( $this->options_name ) . '[tpl_list]" placeholder="' . esc_attr( $placeholder ) . '">' . $val . '</textarea>';
		return $this;
	}

	/**
	 * Field for the search form template.
	 *
	 * @return self
	 */
	public function field_tpl_search(): self {
		$val         = array_key_exists( 'tpl_search', $this->options ) ? $this->options['tpl_search'] : '';
		$placeholder = <<<EOP1
<input name="title" placeholder="Title">
EOP1;
		// phpcs:ignore
		echo '<textarea name="' . esc_attr( $this->options_name ) . '[tpl_search]" placeholder="' . esc_attr( $placeholder ) . '">' . $val . '</textarea>';
		return $this;
	}


	/**
	 * The slug used on all settings.
	 *
	 * @var string
	 */
	protected $plugin_slug = '';

	/**
	 * Setter for $this->plugin_slug.
	 *
	 * @param string $slug The slug passed in the initial file.
	 * @return self
	 */
	public function set_plugin_slug( $slug ) : self {
		$this->plugin_slug = $slug;
		return $this;
	}

	/**
	 * The options will be saved in a single array.
	 *
	 * Here we're setting the name for ALL the options
	 *
	 * @var string
	 */
	protected $options_name = 'wp_custom_dir';

	/**
	 * All the options saved for multiple usages.
	 *
	 * @var array
	 */
	protected $options = array();

}
