<?php

/**
 * The file that defines the movie data structure
 *
 * @link       https://www.zeguigui.com
 * @since      1.0.0
 *
 * @package    Filmotech
 * @subpackage Filmotech/includes
 */

class FilmotechMovie {

  // fmt_movies fields
  public $ID;
  public $DateHeureMAJ;
  public $TitreVF;
  public $TitreVO;
  public $Genre;
  public $Pays;
  public $Annee;
  public $Duree;
  public $Note;
  public $Synopsis;
  public $Acteurs;
  public $Realisateurs;
  public $Commentaires;
  public $Support;
  public $NombreSupport;
  public $Edition;
  public $Zone;
  public $Langues;
  public $SousTitres;
  public $Audio;
  public $Bonus;
  public $EntreeType;
  public $EntreeSource;
  public $EntreeDate;
  public $EntreePrix;
  public $Sortie;
  public $SortieType;
  public $SortieDestinataire;
  public $SortieDate;
  public $SortiePrix;
  public $PretEnCours;
  public $FilmVu;
  public $Reference;
  public $BAChemin;
  public $BAType;
  public $MediaChemin;
  public $MediaType;

  // Computed fields
  public $permalink;
  public $coverUrl;
  public $Categories;

  // Private members
  private $TitleToDisplay;

  public function __construct() {
    // Generate cover URL
		global $wp_rewrite;
		$link = $wp_rewrite->get_page_permastruct();
		$id = absint($this->ID);

    // Which title to display?
    $this->TitleToDisplay = get_option('filmotech_title_to_display','VF');

		// URLs
		if (!empty($link)) {
			$this->permalink = home_url('/') . str_replace('%pagename%', 'filmotech/movie/' . $id . '-' . $this->getTitle(), $link);
			$this->coverUrl  = home_url('/') . str_replace('%pagename%', 'filmotech/cover/' . $id, $link);
		} else {
			$this->permalink = home_url('?filmotech=' . $id);
			$this->coverUrl  = home_url('?filmotech=' . $id . '&cover=1');
		}

		// Split categories
		$this->Categories = preg_split('/,\\s*/', $this->Genre);
  }

  /**
   * Return the prefered title of the movie
   * @since 1.0.0
   */
  public function getTitle() {
    $title = $this->TitreVF;
    if ($this->TitleToDisplay == 'VO') {
      if (!empty($this->TitreVO)) {
        $title = $this->TitreVO;
      }
    }
    return $title;
  }

}
