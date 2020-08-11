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
	public function __construct( $plugin_name, $version, $loader = null ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		if ($loader !== null) {
			$loader->add_action( 'admin_init', $this, 'init_settings');
			$loader->add_action( 'admin_menu', $this, 'admin_menu');
			$loader->add_action( 'wp_dashboard_setup', $this, 'setup_dashboard_widget');
			$plugin = plugin_basename(realpath(__DIR__ . '/../filmotech.php'));
			$loader->add_filter( "plugin_action_links_$plugin", $this, 'settings_link');
			$loader->add_action('admin_enqueue_scripts', $this, 'enqueue_scripts');
		}
	}

	/**
	 * Add custom CSS in admin zone
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style('filmotech_admin_css', plugin_dir_url(__FILE__) . '/css/filmotech-admin.css' );
	}

	/**
	 * Add links in the plugins list
	 * @since 1.0.0
	 * @param array $links existing links
	 */
	public function settings_link($links) {
		$settings_link = '<a href="options-general.php?page=filmotech-options">' . __('Settings','filmotech') . '</a>';
	  array_unshift($links, $settings_link);
		return $links;
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
	private static function get_custom_settings() {
			$settings = array (
						'filmotech_base_folder' => array(
							'type' => 'string',
							'description' => __('Base location of filmotech export files', 'filmotech'),
							'sanitize_callback' => function($folder) {
									// Set absolute path
									if (substr($folder, 0, 1) !== '/') {
											$folder = realpath(ABSPATH . $folder);
									}

									// Check if folder exists
									if (!is_readable($folder)) {
										add_settings_error('filmotech_base_folder','filmotech-invalid-base-folder',__('Invalid base folder or folder not readable','filmotech'), 'error');
									}
									return $folder;
								},
							'show_in_rest' => false,
							'name' => __('Base folder','filmotech')
						),
						'filmotech_cover_folder_name' => array(
							'type' => 'string',
							'description' => __('Covers folder name (relative to base location)', 'filmotech'),
							'sanitize_callback' => 'sanitize_text_field',
							'show_in_rest' => false,
							'name' => __('Cover folder','filmotech')
						),
						'filmotech_database_type' => array(
							'type' => 'string',
							'description' => __('SQlite or MySql database', 'filmotech'),
							'sanitize_callback' => function($dbtype) {
									$validOptions = array('sqlite','mysql');
									if (!in_array($dbtype, $validOptions)) {
										add_settings_error('filmotech_database_type', 'filmotech-invalid-dbtype', __('Invalid database type','filmotech'), 'error');
									}
									// Check if pdo-sqlite is installed!
									if ($dbtype == 'sqlite') {
										if (!class_exists('PDO')) {
											add_settings_error('filmotech_database_type', 'filmotech-pdo', __('PDO is needed by filmotech plugin. Please check your PHP installation.','filmotech'), 'warning');
										}
										if (!extension_loaded('pdo_sqlite')) {
											add_settings_error('filmotech_database_type', 'filmotech-pdo-sqlite', __('pdo_sqlite is needed by filmotech plugin. Please check your PHP installation.','filmotech'), 'warning');
										}
									}
									return $dbtype;
								},
							'show_in_rest' => false,
							'default' => 'sqlite',
							'name' => __('Database type','filmotech')
						),
						'filmotech_database_name' => array(
							'type' => 'string',
							'description' => __('Name of database as registered in filmotech publish options', 'filmotech'),
							'sanitize_callback' => 'sanitize_text_field',
							'show_in_rest' => false,
							'name' => __('Database name','filmotech')
						),
						'filmotech_movies_table_name' => array(
							'type' => 'string',
							'description' => __('Movies table name', 'filmotech'),
							'sanitize_callback' => 'sanitize_text_field',
							'show_in_rest' => false,
							'default' => 'fmt_movies',
							'name' => __('Movie table','filmotech')
						),
						'filmotech_mysql_hostname' => array(
							'type' => 'string',
							'description' => __('MySQL filmotech hostname', 'filmotech'),
							'sanitize_callback' => 'sanitize_text_field',
							'show_in_rest' => false,
							'name' => __('MySQL hostname','filmotech')
						),
						'filmotech_mysql_username' => array(
							'type' => 'string',
							'description' => __('MySQL filmotech username', 'filmotech'),
							'sanitize_callback' => 'sanitize_text_field',
							'show_in_rest' => false,
							'name' => __('MySQL username','filmotech')
						),
						'filmotech_mysql_password' => array(
							'type' => 'string',
							'description' => __('MySQL filmotech user password', 'filmotech'),
							'sanitize_callback' => 'sanitize_text_field',
							'show_in_rest' => false,
							'name' => __('MySQL password','filmotech')
						),
						'filmotech_movies_per_page' => array(
							'type' => 'string',
							'description' => __('Number of items per page on list of movies', 'filmotech'),
							'sanitize_callback' => function ($setting) {
									error_log("sanitize_callback for filmotech_movies_per_page");
									return absint($setting);
								},
							'default' => '20',
							'show_in_rest' => false,
							'name' => __('Items per page','filmotech')
						),
						'filmotech_display_style' => array(
							'type' => 'string',
							'description' => __('List of movies should display a table view or grid view', 'filmotech'),
							'sanitize_callback' => 'sanitize_text_field',
							'default' => 'list',
							'show_in_rest' => false,
							'name' => __('Display style','filmotech')
						)
				);
			return $settings;
	}

	/**
	 * Setup dashboard widget
	 */
	public function setup_dashboard_widget() {
		wp_add_dashboard_widget('filmotech_dashboard_widget', __('Filmotech', 'filmotech'), array($this, 'dashboard_widget'));
	}

	/**
	 * Admin dashboard widget content
	 */
	public function dashboard_widget($post, $callback_args) {
		$databaseType = get_option('filmotech_database_type');
		$public = new Filmotech_Public($this->plugin_name, $this->version);
		$db = $public->getDbConnection();

		include plugin_dir_path(__FILE__) . 'partials/filmotech-dashboard-panel.php';
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

	/**
	 * Display appropriate input type for a setting
	 * @since 1.0.0
	 */
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
		} elseif ($name == 'filmotech_movies_per_page') {
			?>
			<select id="filmotech_movies_per_page" name="filmotech_movies_per_page">
				<option <?php if ($value === '10')  { echo "selected"; } ?> value="10">10</option>
				<option <?php if ($value === '20')  { echo "selected"; } ?> value="20">20</option>
				<option <?php if ($value === '30')  { echo "selected"; } ?> value="30">30</option>
				<option <?php if ($value === '40')  { echo "selected"; } ?> value="40">40</option>
				<option <?php if ($value === '50')  { echo "selected"; } ?> value="50">50</option>
				<option <?php if ($value === '60')  { echo "selected"; } ?> value="60">60</option>
				<option <?php if ($value === '70')  { echo "selected"; } ?> value="70">70</option>
				<option <?php if ($value === '80')  { echo "selected"; } ?> value="80">80</option>
				<option <?php if ($value === '90')  { echo "selected"; } ?> value="90">90</option>
				<option <?php if ($value === '100') { echo "selected"; } ?> value="100">100</option>
			</select>
			<?php
		} elseif ($name == 'filmotech_display_style') {
			?>
			<select id="filmotech_display_style" name="filmotech_display_style">
				<option <?php if ($value === 'list')  { echo "selected"; } ?> value="list"><?php _e('List view','filmotech'); ?></option>
				<option <?php if ($value === 'grid')  { echo "selected"; } ?> value="grid"><?php _e('Grid view','filmotech'); ?></option>
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
	 * Uninstall
	 * @since 1.0.0
	 */
	public static function uninstall() {
		$customSettings = self::get_custom_settings();
		foreach ($customSettings as $setting => $params) {
			delete_option($setting);
		}
	}

	/**
	 * Register the settings page under the wp-admin menu
	 * @since		1.0.0
	 */
	public function init_settings() {
		$customSettings = self::get_custom_settings();

		foreach ($customSettings as $setting => $params) {
			register_setting('filmotech', $setting, $params);
		}

		add_settings_section("filmotech_settings", __('Filmotech settings','filmotech'), null, 'filmotech-options');

		foreach ($customSettings as $setting => $params) {
			add_settings_field(
				$setting,
				__($params['name'], 'filmotech'),
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
