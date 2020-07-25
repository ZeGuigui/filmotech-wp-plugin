<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.zeguigui.com
 * @since      1.0.0
 *
 * @package    Filmotech
 * @subpackage Filmotech/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Filmotech
 * @subpackage Filmotech/admin
 * @author     Guillaume Lapierre <filmotech-plugin@zeguigui.com>
 */
class Filmotech_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * List the custom settings
	 * Settings include :
	 * - filmotech base folder
	 * - cover folder name
	 * - database type : SQLite / MySQL (unsupported in 1.0.0)
	 * - database name (eg. filmotech)
	 * - movies table name (eg. fmt_movies)
	 * - mysql hostname (unsupported in 1.0.0)
	 * - mysql username (unsupported in 1.0.0)
	 * - mysql password	(unsupported in 1.0.0)
	 * @since 1.0.0
	 */
	private function get_custom_settings() {
			$settings = array (
						'filmotech_base_folder' => array(
							'type' => 'string',
							'description' => __('Base location of filmotech export files', 'filmotech'),
							'sanitize_callback' => null,
							'show_in_rest' => false
						),
						'filmotech_cover_folder_name' => array(
							'type' => 'string',
							'description' => __('Covers folder name (relative to base location)', 'filmotech'),
							'sanitize_callback' => null,
							'show_in_rest' => false
						),
						'filmotech_database_type' => array(
							'type' => 'string',
							'description' => __('SQlite or MySql database', 'filmotech'),
							'sanitize_callback' => null,
							'show_in_rest' => false,
							'default' => 'sqlite'
						),
						'filmotech_database_name' => array(
							'type' => 'string',
							'description' => __('Name of database as registered in filmotech publish options', 'filmotech'),
							'sanitize_callback' => null,
							'show_in_rest' => false,
						),
						'filmotech_movies_table_name' => array(
							'type' => 'string',
							'description' => __('Movies table name', 'filmotech'),
							'sanitize_callback' => null,
							'show_in_rest' => false,
							'default' => 'fmt_movies'
						),
						'filmotech_mysql_hostname' => array(
							'type' => 'string',
							'description' => __('MySQL filmotech hostname (unsupported)', 'filmotech'),
							'sanitize_callback' => null,
							'show_in_rest' => false
						),
						'filmotech_mysql_username' => array(
							'type' => 'string',
							'description' => __('MySQL filmotech username (unsupported)', 'filmotech'),
							'sanitize_callback' => null,
							'show_in_rest' => false
						),
						'filmotech_mysql_password' => array(
							'type' => 'string',
							'description' => __('MySQL filmotech user password (unsupported)', 'filmotech'),
							'sanitize_callback' => null,
							'show_in_rest' => false
						)
				);
			return $settings;
	}

	/**
	 * Callback to display settings page
	 */
  public function settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
 			return;
 		}

		// Display error messages
		settings_errors( 'filmotech_messages' );
		?>
		<div class="wrap">
		 <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		 <form action="options.php" method="post">
	   <?php
		    settings_fields('filmotech');
		 		do_settings_sections('filmotech-options');
				submit_button('Save settings');
		 ?>
	   </form>
	  </div>
		<?php
	}

	/**
	 * Add an admin menu to change settings
	 * @since 1.0.0
	 */
	public function admin_menu() {
		add_options_page(
			__('Filmotech plugin settings', 'filmotech'),
			__('Filmotech', 'filmotech'),
			'manage_options',
			'filmotech-options',
			array($this, 'settings_page')
		);
	}

	public function setting_input($args) {
		$value = get_option($args['label_for']);
		$name  = $args['label_for'];

		if ($name === 'filmotech_database_type') {
			?>
			<select id="filmotech_database_type" name="filmotech_database_type">
				<option <?php if ($value === 'sqlite') { echo "selected"; } ?> value="sqlite"><?php echo __('SQLite','filmotech'); ?></option>
				<option <?php if ($value === 'mysql')  { echo "selected"; } ?> value="mysql"><?php echo __('MySQL','filmotech'); ?></option>
			</select>
			<?php
		} else {
		?>
			<input type="text" id="<?php echo esc_attr($name) ?>" name="<?php echo esc_attr($name) ?>" value="<?php echo esc_attr($value) ?>" /><br/>
		<?php
		}
		?><p class="description"><?php echo esc_html($args['filmotech_setting']['description'])  ?></p><?php
	}

	/**
	 * Register the settings page under the wp-admin menu
	 * @since		1.0.0
	 */
	public function init_settings() {
		$customSettings = $this->get_custom_settings();

		foreach ($customSettings as $setting => $params) {
			register_setting('filmotech', $setting, $params);
		}

		add_settings_section("filmotech_settings", __('Filmotech settings','filmotech'), null, 'filmotech-options');

		foreach ($customSettings as $setting => $params) {
			add_settings_field(
				$setting,
				__("$setting&nbsp;:", 'filmotech'),
				array($this, 'setting_input'),
				'filmotech-options',
				'filmotech_settings',
				array(
					'label_for' => $setting,
					'filmotech_setting'   => $params
				)
			);
		}

	}

}
