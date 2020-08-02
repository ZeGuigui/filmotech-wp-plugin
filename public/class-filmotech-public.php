<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.zeguigui.com
 * @since      1.0.0
 *
 * @package    Filmotech
 * @subpackage Filmotech/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Filmotech
 * @subpackage Filmotech/public
 * @author     Guillaume Lapierre <filmotech-plugin@zeguigui.com>
 */
class Filmotech_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $loader ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$loader->add_filter('query_vars', $this, 'register_filmotech_vars');
		$loader->add_action('parse_request', $this, 'parse_filmotech_requests');
	}

	public function getDbConnection() {
		$BASE_FOLDER = get_option('filmotech_base_folder');
		$DB_TYPE     = get_option('filmotech_database_type');
		$DB_SERVER   = get_option('filmotech_mysql_hostname');
    $DB_USER     = get_option('filmotech_mysql_username');
    $DB_PASSWORD = get_option('filmotech_mysql_password');
    $DB_NAME     = get_option('filmotech_database_name');
    $DB_TABLE    = get_option('filmotech_movies_table_name');

		try {
			if ($DB_TYPE === 'sqlite') {
				$db = new PDO('sqlite:'. $BASE_FOLDER . '/' . $DB_NAME .'.sqlite3');
			} else {
				$db = new PDO('mysql:host=' . $DB_SERVER . ';dbname=' . $DB_NAME, $DB_USER, $DB_PASSWORD);
				$db->query("SET NAMES UTF8");
			}
		} catch (Exception $e) {
			error_log(__('Filmotech database error: ','filmotech') . $e->getMessage());
			die(__('Filmotech database error: ','filmotech') . $e->getMessage());
		}

		return $db;
	}

	public function getMovieList($page) {
		$DB_TABLE    = get_option('filmotech_movies_table_name'); // 'fmt_movies';
		if (empty($DB_TABLE)) {
			$DB_TABLE = 'fmt_movies';
		}

		$db = $this->getDbConnection();

		$query = "SELECT count(*) from " . $DB_TABLE;
		$result = $db->query($query);
		$result_fetch = $result->fetch();
		$total_record = $result_fetch[0];
		$result->closeCursor();

		return sprintf(__('There are %d movies in the database. Requested page %d','filmotech'), $total_record, $page);

	}

	public function getMovie($id) {

	}

	/**
	 * Generate a virtual page using current template
	 * @since 1.0.0
	 */
	public function get_virtual_content($posts) {
		// Virtual post
		$post = new stdClass();
		global $wp_query;

		$filmotechId = intval($wp_query->query_vars['filmotech'], 10);

		if ($filmotechId > 0) {
			$title    = 'Filmotech movie ' . $filmotechId;
			$content  = '<p>Hello World</p><p>Requested ID:' . $filmotechId . '</p>';
			$pageDate = current_time( 'mysql' );
			$gmtDate  = current_time( 'mysql', 1 );
		} else {
			$page     = isset($wp_query->query_vars['page']) ? intval($wp_query->query_vars['page'],10) : 0;
			$title    = __('Filmotech movie list', 'filmotech');
			$content  = $this->getMovieList($page);
			$pageDate = current_time( 'mysql' );
			$gmtDate  = current_time( 'mysql', 1 );
		}

		// fill properties of $post with everything a page in the database would have
		$post->ID = -1;                          // use an illegal value for page ID
		$post->post_author           =  1;   // post author id
		$post->post_date             = $pageDate;
		$post->post_date_gmt         = $gmtDate;
		$post->post_content          = $content; // '<p>Hello World</p><p>Requested ID:' . $filmotechId . '</p>';
		$post->post_title            = $title;   // 'Virtual page Hello World!';
		$post->post_excerpt          = '';
		$post->post_status           = 'publish';
		$post->comment_status        = 'closed';        // mark as closed for comments, since page doesn't exist
		$post->ping_status           = 'closed';           // mark as closed for pings, since page doesn't exist
		$post->post_password         = '';               // no password
		$post->post_name             = 'filmotech';
		$post->to_ping               = '';
		$post->pinged                = '';
		$post->post_modified         = $post->post_date;
		$post->post_modified_gmt     = $post->post_date_gmt;
		$post->post_content_filtered = '';
		$post->post_parent           = 0;
		$post->guid                  = get_home_url( '/filmotech' );
		$post->menu_order            = 0;
		$post->post_type             = 'page';
		$post->post_mime_type        = '';
		$post->comment_count         = 0;
		$post->ancestors             = array();

		// allows for any last minute updates to the $post content
		$post = apply_filters( 'filmotech_virtual_page_content', $post );

		// set filter results
		$posts = array( $post );

		// reset wp_query properties to simulate a found page
		$wp_query->is_page = TRUE;
		$wp_query->is_singular = TRUE;
		$wp_query->is_home = FALSE;
		$wp_query->is_archive = FALSE;
		$wp_query->is_category = FALSE;
		unset( $wp_query->query['error'] );
		$wp_query->query_vars['error'] = '';
		$wp_query->is_404 = FALSE;

		// Simulate results!
		$wp_query->post = $post;
		$wp_query->posts = array($post);
		$wp_query->queried_object = $post;
		$wp_query->queried_object_id = $post->ID;
		$wp_query->current_post = $post->ID;
		$wp_query->post_count = 1;

		return array($post);
	}

	public function template_redir() {
		get_template_part('page');
		exit;
	}

	/**
	 * Check for filmotech requests
	 * @since 1.0.0
	 */
	public function parse_filmotech_requests(&$wp) {
		$queryVars = $wp->query_vars;

		if (isset($wp->query_vars['filmotech'])) {
			$filmotechId = intval($wp->query_vars['filmotech'], 10);
			add_action('template_redirect', array($this, 'template_redir'));
			add_filter('the_posts', array($this, 'get_virtual_content'));
			return;
		}

		return;
	}

	/**
	 * Register plugin vars
	 * @since 1.0.0
	 */
	public function register_filmotech_vars($vars) {
		$vars[] = 'filmotech';
		$vars[] = 'page';
		return $vars;
	}

}
