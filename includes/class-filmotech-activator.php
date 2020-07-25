<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.zeguigui.com
 * @since      1.0.0
 *
 * @package    Filmotech
 * @subpackage Filmotech/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Filmotech
 * @subpackage Filmotech/includes
 * @author     Guillaume Lapierre <filmotech-plugin@zeguigui.com>
 */
class Filmotech_Activator {

	/**
	 * Setup plugin
	 *
	 * Register options
	 * Register rewrite rules
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		error_log("Filmotech_Activator::activate");
		flush_rewrite_rules();
	}

}
