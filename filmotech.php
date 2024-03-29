<?php

/**
 *
 * @link              https://www.zeguigui.com/
 * @since             1.0.0
 * @package           Filmotech
 *
 * @wordpress-plugin
 * Plugin Name:       Filmotech Database Viewer
 * Plugin URI:        https://github.com/ZeGuigui/filmotech-wp-plugin
 * Description:       Display your filmotech collection inside your website!
 * Version:           1.1.1
 * Author:            Guillaume Lapierre
 * Author URI:        https://www.zeguigui.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       filmotech
 * Domain Path:       /languages
 * Requires at least: 5.4
 * Requires PHP:      5.6
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'PLUGIN_NAME_VERSION', '1.1.1' );

define ('FILMOTECH_PLUGIN_NAME', __('Filmotech Database Viewer','filmotech'));
define ('FILMOTECH_PLUGIN_DESC', __('Display your filmotech collection inside your website!','filmotech'));

/**
 * The code that runs during plugin activation.
 */
function activate_filmotech_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-filmotech-activator.php';
	Filmotech_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_filmotech_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-filmotech-deactivator.php';
	Filmotech_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_filmotech_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_filmotech_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-filmotech.php';

/**
 * Begins execution of the plugin.
 * @since    1.0.0
 */
function run_filmotech() {

	$plugin = new FilmotechPlugin();
	$plugin->run();

}
run_filmotech();
