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

define ('BROWSER_CACHE_LIFETIME', 90 * 24 * 60 * 60);	// 90 days

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
	 * Plugin configuration
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string
	 */
	 private $BASE_FOLDER ; // = get_option('filmotech_base_folder');
	 /**
 	 * Plugin configuration
 	 *
 	 * @since  1.0.0
 	 * @access private
 	 * @var    string
 	 */
	 private $DB_TYPE     ; // = get_option('filmotech_database_type');
	 /**
 	 * Plugin configuration
 	 *
 	 * @since  1.0.0
 	 * @access private
 	 * @var    string
 	 */
	 private $DB_SERVER   ; // = get_option('filmotech_mysql_hostname');
	 /**
 	 * Plugin configuration
 	 *
 	 * @since  1.0.0
 	 * @access private
 	 * @var    string
 	 */
	 private $DB_USER     ; // = get_option('filmotech_mysql_username');
	 /**
 	 * Plugin configuration
 	 *
 	 * @since  1.0.0
 	 * @access private
 	 * @var    string
 	 */
	 private $DB_PASSWORD ; // = get_option('filmotech_mysql_password');
	 /**
 	 * Plugin configuration
 	 *
 	 * @since  1.0.0
 	 * @access private
 	 * @var    string
 	 */
	 private $DB_NAME     ; // = get_option('filmotech_database_name');
	 /**
 	 * Plugin configuration
 	 *
 	 * @since  1.0.0
 	 * @access private
 	 * @var    string
 	 */
	 private $DB_TABLE    ; // = get_option('filmotech_movies_table_name');

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $loader = null ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->BASE_FOLDER = get_option('filmotech_base_folder', plugin_dir_path(__FILE__));
		$this->DB_TYPE     = get_option('filmotech_database_type', 'sqlite');
		$this->DB_SERVER   = get_option('filmotech_mysql_hostname');
		$this->DB_USER     = get_option('filmotech_mysql_username');
		$this->DB_PASSWORD = get_option('filmotech_mysql_password');
		$this->DB_NAME     = get_option('filmotech_database_name', 'filmotech');
		$this->DB_TABLE    = get_option('filmotech_movies_table_name', 'fmt_movies');

		if ($loader !== null) {
			$loader->add_action('init', $this, 'add_rewrite_rules');
			$loader->add_filter('query_vars', $this, 'register_filmotech_vars');
			$loader->add_action('parse_request', $this, 'parse_filmotech_requests');
			$loader->add_action('wp_enqueue_scripts', $this, 'enqueue_scripts');
		}
	}

	public function enqueue_scripts() {
		wp_enqueue_style('filmotech_css', plugin_dir_url(__FILE__) . '/css/filmotech-public.css' );
	}

	public function add_rewrite_rules() {
		require_once plugin_dir_path( __FILE__ ) . '../includes/class-filmotech-activator.php';
		Filmotech_Activator::activate();
	}

	public function getDbConnection() {

		try {
			if ($this->DB_TYPE === 'sqlite') {
				$dsn = 'sqlite:'. $this->BASE_FOLDER . '/' . $this->DB_NAME .'.sqlite3';
				$db = new PDO($dsn);
			} else {
				$dsn = 'mysql:host=' . $this->DB_SERVER . ';dbname=' . $this->DB_NAME;
				$db = new PDO($dsn, $this->DB_USER, $this->DB_PASSWORD);
				$db->query("SET NAMES UTF8");
			}
		} catch (Exception $e) {
			$error_msg = sprintf(__('Filmotech database error: %s','filmotech'), $e->getMessage());
			error_log($error_msg);
			die($error_msg);
		}

		return $db;
	}

	public function getLastAddedMovieTime() {
		$db = $this->getDbConnection();
		$query = "SELECT max(EntreeDate) from " . $this->DB_TABLE;
		$result = $db->query($query);
		$result_fetch = $result->fetch();
		$lastMovieTime = $result_fetch[0];
		$result->closeCursor();

		return $lastMovieTime;
	}

	/**
	 * Generate page URL for index list
	 * @since 1.0.0
	 */
	public function getPageUrl($page, $category = null) {
		global $wp_rewrite;
		$link = $wp_rewrite->get_page_permastruct();

		if ($category === null) {
			if (!empty($link)) {
				$link = str_replace('%pagename%', 'filmotech/' . absint($page), $link);
				return home_url('/') . $link;
			}

			// No rewrite
			return home_url( '?filmotech=0&fp=' . absint($page) );
		}

		// Category page mode!
		if (!empty($link)) {
			$link = str_replace('%pagename%', 'filmotech/category/' . absint($page) . '/' . $category, $link);
			return home_url('/') . $link;
		}

		// No rewrite
		return home_url( '?filmotech=category&fp=' . absint($page) . '&fc=' . esc_attr($category) );

	}

	/**
	 * Number of movies in the database
	 * @since 1.0.0
	 */
	public function getMovieCount($category = null) {

		if ($category === null) {
			$db = $this->getDbConnection();
			$query = "SELECT count(*) from " . $this->DB_TABLE;
			$result = $db->prepare($query);
			$result->execute();
			$result_fetch = $result->fetch();
			$total_record = $result_fetch[0];
			$result->closeCursor();
		} else {
			$total_record = count($this->getMovieList(-1,'alpha',$category));
		}

		return $total_record;
	}

	/**
	 * Get all the distinct categories from filmotech database
	 * @since 1.0.0
	 */
	public function getAllCategories() {
		$db = $this->getDbConnection();
		$query = "SELECT DISTINCT Genre FROM " . $this->DB_TABLE;
		$result = $db->query($query);
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$categories = $result->fetchAll();
		$result->closeCursor();

		$allCategories = array();

		// Parse categories and split into single categories
		foreach ($categories as $c) {
			$myCategories = preg_split('/\\s*(,|\\/)\\s*/', $c['Genre']);
			foreach ($myCategories as $categ) {
				if (!empty ($categ)) {
					if (!isset($allCategories[$categ])) {
						$allCategories[$categ] = 0;
					}
					$allCategories[$categ]++;
				}
			}
		}

		$resultCategories = array();
		foreach ($allCategories as $fc => $cnt) {
			$rc = array();
			$rc['name'] = $fc;
			$rc['count'] = $cnt;
			$rc['permalink'] = $this->getPageUrl(1, $fc);
			$resultCategories[] = $rc;
		}

		// Sort categories by name
		usort($resultCategories, function($a,$b) { return strnatcmp($a['name'], $b['name']); });

		return $resultCategories;
	}

	/**
	 * MoveList to HTML code
	 * @since 1.0.0
	 */
	public function getMovieListHtml($page, $forcedSortKey = null, $category = null) {
		$movies = $this->getMovieList($page, $forcedSortKey, $category);

		$recordsPerPage = get_option('filmotech_movies_per_page', 20);
		$total_record    = $this->getMovieCount($category);
		$number_of_pages = ceil($total_record / $recordsPerPage);

		ob_start();
		$displayMode = get_option('filmotech_display_style','list');
		if ($displayMode == 'list') {
			include plugin_dir_path(__FILE__) . 'partials/filmotech-movie-list.php';
		} else {
			// Grid view
			include plugin_dir_path(__FILE__) . 'partials/filmotech-grid-view.php';
		}
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * Generate movie list HTML content
	 */
	public function getMovieList($page, $forcedSortKey = null, $category = null) {
		$db = $this->getDbConnection();

		$recordsPerPage = get_option('filmotech_movies_per_page', 20);
		$offset = $offset = ($page-1) * $recordsPerPage;

		if ($forcedSortKey === null) {
			$sortKey = get_option('filmotech_display_order','alpha');
		} else {
			$sortKey = $forcedSortKey;
		}

		// When displaying by title, sort by prefered title!
		$preferedTitle = get_option('filmotech_title_to_display', 'VF');

		if ($sortKey == 'alpha') {
			// Always add TitreVF as second sort key when sorting by TitreVO
			$sortColumn =  $preferedTitle == 'VF' ? 'TitreVF asc, TitreVO asc, EntreeDate desc' : 'IFNULL(NULLIF(TitreVO,\'\'),TitreVF) asc, TitreVF asc, EntreeDate desc';
		} elseif ($sortKey == 'date') {
			// If same entered date, using a title sorting
			$sortColumn = $preferedTitle == 'VF' ? 'EntreeDate desc, TitreVF asc, TitreVO asc' : 'EntreeDate desc, IFNULL(NULLIF(TitreVO,\'\'),TitreVF) asc, TitreVF asc';
		} else {
			error_log(sprintf(__('Unknown sort key: %s','filmotech'),$sortKey));
			$sortColumn = 'TitreVF';
		}

		$whereClause = '';
		if ($category !== null) {
			// Cannot apply limit on DB side as movies will be filtered once fetched for specified category
			$whereClause = 'WHERE Genre like :category ';
			$limit = '';
		} else {
			$limit = "LIMIT $recordsPerPage OFFSET $offset ";
		}

		$query = "SELECT ID, TitreVF, TitreVO, Genre, Annee, Edition " .
						 "FROM $this->DB_TABLE " .
						 $whereClause .
						 "order by $sortColumn " .
						 $limit;
	  $result = $db->prepare($query);

		if ($result === false) {
			error_log("SQLError. Query: " . $query);
		}

		if ($category !== null) {
			$result->bindValue(':category', '%' . $category . '%', PDO::PARAM_STR);
		}
		$result->execute();
		$result->setFetchMode(PDO::FETCH_CLASS,'FilmotechMovie');
		$movies = $result->fetchAll();
		$result->closeCursor();

		// When category name is given, we need to filter a little bit more
		// as a category name can be a subpart of another category
		// ie. Comédie vs Comédie romantique
		// Pb: less than specified number of items... pagination should be done PHP side vs DB side!
		if ($category !== null) {
			$movies = array_filter($movies, function($m) use($category) { return $m->hasCategory($category); });
			if ($page != -1) {
				// $page == -1 => all movies (counting!)
				$movies = array_slice($movies, $offset, $recordsPerPage);
			}
		}

		return $movies;
	}

	/**
	* Fetch a movie from database
	* @since 1.0.0
	*/
	public function getMovie($id) {
		$db = $this->getDbConnection();

		$query = "SELECT * from " . $this->DB_TABLE . " WHERE ID = :id";
		$statement = $db->prepare($query);
		$statement->execute(array(':id' => $id));
		$statement->setFetchMode(PDO::FETCH_CLASS, 'FilmotechMovie');
		$movie = $statement->fetch();
		$statement->closeCursor();

		return $movie;
	}

	/**
	 * Get movie as HTML code
	 * @since 1.0.0
	 */
	public function getMovieContent($movie) {
		ob_start();
		include plugin_dir_path(__FILE__) . 'partials/filmotech-movie-display.php';
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
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
			$movie    = $this->getMovie($filmotechId);
			$title    = $movie->TitreVF;
			if ($movie->Annee > 0) {
				$title .= ' (' . $movie->Annee . ')';
			}
			$content  = $this->getMovieContent($movie);
			$pageDate = current_time( 'mysql' );
			$gmtDate  = current_time( 'mysql', 1 );
		} else {
			$isCategoryIndex = $wp_query->query_vars['filmotech'] == 'category';
			$page     = isset($wp_query->query_vars['fp']) ? intval($wp_query->query_vars['fp'],10) : 1;
			$category = $isCategoryIndex ? rawurldecode($wp_query->query_vars['fc']) : null;
			$title    = __('Filmotech movie list', 'filmotech');
			$content  = $this->getMovieListHtml($page, $isCategoryIndex ? 'alpha' : null, $category);
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

		// allows for any last minute updates to the $post content (like [video])
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

			if (isset($wp->query_vars['cover'])) {
				$basepath  = get_option('filmotech_base_folder');
				$coverpath = get_option('filmotech_cover_folder_name');
				$coverFile = sprintf("%s/%s/Filmotech_%05d.jpg", $basepath, $coverpath, $filmotechId);
				if (is_file($coverFile)) {
					// Check for HTTP cache
					if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
						$previousETag = $_SERVER['HTTP_IF_NONE_MATCH'];
						$eTag = md5_file($coverFile);
						if ($previousETag == $eTag) {
							header("Expires: " . date("r", time() + BROWSER_CACHE_LIFETIME));
							header("Cache-Control: max-age=" . BROWSER_CACHE_LIFETIME );
							header("ETag: $eTag");
							header('HTTP/1.0 304 Not Modified');
							die;
						}
					}
					// Serve file content
					$cover = file_get_contents($coverFile);
					header("Content-type: image/jpg");
					header("Content-Length: " . filesize($coverFile));
					header("Expires: " . date("r", time() + BROWSER_CACHE_LIFETIME));
					header("Cache-Control: max-age=" . BROWSER_CACHE_LIFETIME );
					header("Pragma:");
					header("ETag: " . md5_file($coverFile));
					echo $cover;	// jpg/png binary file directly served to the user. Do not escape!
				} else {
					//TODO Return a default cover... 404 in the meanwhile!
					header("HTTP/1.0 404 Not Found", false, 404);
					echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . PHP_EOL .
						 '<html>' . PHP_EOL . 
						 '<head>' . PHP_EOL . 
						 '<title>Cover not found</title>' . PHP_EOL . 
						 '</head>' . PHP_EOL . 
						 '<body>' . PHP_EOL . 
						 '<p>Cover not found... so sad!</p>' . PHP_EOL . 
						 '</body>' . PHP_EOL . 
						 '</html>';
				}
				die;
			}

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
		$vars[] = 'fp';
		$vars[] = 'cover';
		$vars[] = 'fc';
		return $vars;
	}

}
