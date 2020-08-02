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
		add_rewrite_rule('^filmotech/?$', 'index.php?filmotech=0&page=0', 'top');
		add_rewrite_rule('^filmotech/([0-9]+)/?$', 'index.php?filmotech=0&page=$matches[1]', 'top');
		add_rewrite_rule('^filmotech/movie/([0-9]+)-.*/?$', 'index.php?filmotech=$matches[1]', 'top');
		flush_rewrite_rules();
	}

}
