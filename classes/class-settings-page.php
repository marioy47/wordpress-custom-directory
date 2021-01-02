<?php
/**
 * Creates a Settings Page for plugin configuration
 *
 * @package Wordpress_Custom_Directory
 */

namespace WpCustomDir;

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
		add_action( 'admin_enqueue_scripts', array( $this, 'enable_code_mirror' ) );
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
	<span class="help-links"><a target="_blank" href="edit.php?post_type=directory_entry&page=wordpress-custom-directory-help"><?php esc_html_e( 'Help', 'wp-custom-dir' ); ?></a></span>
	| <span class="help-links"><a target="_blank" href="https://twig.symfony.com/doc/3.x/templates.html"><?php esc_html_e( 'Twig Template System', 'wp-custom-dir' ); ?></a></span>
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

		// Misc options.
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
		add_settings_field(
			'sidebar-name',
			__( 'Change the sidebar link name', 'wp-custom-dir' ),
			array( $this, 'field_sidebar_name' ),
			$this->plugin_slug,
			$current_section
		);
		add_settings_field(
			'remove-title',
			__( 'Remove the title of the single item page ?', 'wp-custom-dir' ),
			array( $this, 'field_remove_title' ),
			$this->plugin_slug,
			$current_section
		);
		add_settings_field(
			'change-layout',
			__( 'Change the single item page layout', 'wp-custom-dir' ),
			array( $this, 'field_change_layout' ),
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
			__( 'Search form code', 'wp-custom-dir' ),
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
		esc_html_e( 'Options that change the directory behaviour', 'wp-custom-dir' );
		return $this;
	}

	/**
	 * Creates the slug (path) input field.
	 *
	 * @return self
	 * phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	 */
	public function field_slug(): self {
		$val = array_key_exists( 'slug', $this->options ) ? $this->options['slug'] : null;
		$url = '<a href="' . admin_url( 'options-permalink.php' ) . '">Permalinks</a>';
		echo '<input type="text" name="' . esc_attr( $this->options_name ) . '[slug]" value="' . esc_attr( $val ) . '" placeholder="directory-entry">';
		// Translators: a[href] for the permalnks page.
		echo '<p class="description">' . sprintf( __( 'If you change this value, you need to got to the %s page and click on "save"', 'wp-custom-dir' ), $url ) . '</p>';
		return $this;
	}

	/**
	 * Creates the "change sidebar name" field
	 *
	 * @return self
	 */
	public function field_sidebar_name(): self {
		$val = array_key_exists( 'sidebar_name', $this->options ) ? $this->options['sidebar_name'] : null;
		echo '<input type="text" name="' . esc_attr( $this->options_name ) . '[sidebar_name]" value="' . esc_attr( $val ) . '" placeholder="Custom Directory Entries"  />';
		echo '<p class="description">' . esc_html__( 'Here you can change the entry name on the admin sidebar', 'wp-custom-dir' ) . '</p>';
		return $this;
	}

	/**
	 * Creates the "remove title" checkbox field.
	 *
	 * @return self
	 */
	public function field_remove_title(): self {
		$val = array_key_exists( 'remove_title', $this->options ) ? $this->options['remove_title'] : null;
		echo '<input type="checkbox" name="' . esc_attr( $this->options_name ) . '[remove_title]" value="1" ' . ( $val ? 'checked' : '' ) . ' />';

		return $this;
	}

	/**
	 * Creates the "changhe_layout" sleect.
	 *
	 * @return self
	 */
	public function field_change_layout(): self {
		$val = array_key_exists( 'change_layout', $this->options ) ? $this->options['change_layout'] : null;
		echo '<select name="' . esc_attr( $this->options_name ) . '[change_layout]">';
		echo '<option value="">' . esc_html__( 'Default site layout', 'wp-custom-dir' ) . '</option>';
		echo '<option value="content_sidebar" ' . selected( 'content_sidebar', $val ) . '>' . esc_html__( 'Use sidebar', 'wp-custom-dir' ) . '</option>';
		echo '<option value="full_width_content" ' . selected( 'full_width_content', $val ) . '>' . esc_html__( 'Full width', 'wp-custom-dir' ) . '</option>';
		echo '</select>';

		return $this;

	}

	/**
	 * Section for templates.
	 *
	 * Adds some description and help for the user.
	 *
	 * @return self
	 * phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	 */
	public function section_templates(): self {
		echo '<p>' . esc_html__( 'Please browse the help (link at the top) on how templates work and how to use them', 'wp-custom-dir' ) . '</p>';

		// Initialize code morror for the template fields.
		echo <<<EOJ
<script type="text/javascript">
	window.onload = function() {
		wp.codeEditor.initialize(document.getElementById("tpl-single"), {});
		wp.codeEditor.initialize(document.getElementById("tpl-list"), {});
		wp.codeEditor.initialize(document.getElementById("tpl-search"), {});
	}
</script>
EOJ;

		$this->template_fields_help = '<p class="description">' . __( 'You can use the following template items:', 'wp-custom-dir' ) . '<br />';
		foreach ( array( 'title', 'excerpt', 'content', 'link', 'image', 'image_url' ) as $element ) {
			$this->template_fields_help .= '<code style="font-size: 80%">{{' . $element . '}}</code> ';
		}
		$this->template_fields_help .= '</p>';

		$this->template_fields_help .= '<p class="description">' . esc_html__( 'You can also use the follwing ACF fields:', 'wp-custom-dir' ) . '<br />';
		if ( function_exists( 'acf_get_field_groups' ) ) {
			$groups = acf_get_field_groups( array( 'post_type' => $this->post_type ) );
			foreach ( $groups as $group ) {
				$fields = acf_get_fields( $group );
				foreach ( $fields as $field ) {
					$this->acf_fields[ $field['ID'] ] = $field;
					$this->template_fields_help      .= '<code style="font-size: 80%">{{' . $field['name'] . '}}</code> ';
				}
			}
		} else {
			$this->template_fields_help .= __( 'ACF plugin is not installed', 'wp-custom-dir' );
		}
		$this->template_fields_help .= '</p>';

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

		echo '<textarea name="' . esc_attr( $this->options_name ) . '[tpl_single]" id="tpl-single" placeholder="' . esc_attr( $placeholder ) . '">' . $val . '</textarea>';
		echo $this->template_fields_help;
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
<div class="left">{{image}}</div>
<div class="right">{{title}}</div>
EOP2;

		echo '<textarea name="' . esc_attr( $this->options_name ) . '[tpl_list]" id="tpl-list" placeholder="' . esc_attr( $placeholder ) . '">' . $val . '</textarea>';
		echo $this->template_fields_help;

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
		echo '<textarea name="' . esc_attr( $this->options_name ) . '[tpl_search]" id="tpl-search" placeholder="' . esc_attr( $placeholder ) . '">' . $val . '</textarea>';
		echo '<p class="description">' . esc_html__( 'Make sure that the input\'s name\'s are the same as the fields used in the list template. And DO NOT include the <form></form> tags', 'wp-custom-dir' ) . '</p>';
		return $this;
	}

	/**
	 * Enqueues the code-mirror library and the script file that nenables it in the custom post type.
	 *
	 * @return void
	 */
	public function enable_code_mirror() {
		wp_enqueue_code_editor( array( 'type' => 'text/html' ) );
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
	 * Save the ACF fields to be reused
	 *
	 * @var array
	 */
	protected $acf_fields = array();

	/**
	 * String with all the template fields help.
	 *
	 * @var string
	 */
	protected $template_fields_help;
}
