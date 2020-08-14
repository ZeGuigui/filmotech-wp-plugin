<?php

/**
 * Provide a public-facing view for the plugin
 *
 * List the movies in a grid-view like fashion
 *
 * @link       https://www.zeguigui.com
 * @since      1.0.0
 *
 * @package    Filmotech
 * @subpackage Filmotech/public/partials
 */
?>
<div id="filmotechGridView">
<?php foreach ($movies as $movie) { ?>
  <div class="filmotech-movie">
    <div class="cover">
      <a href="<?php echo esc_url($movie->permalink); ?>" class="movieLink"><img alt="<?php  echo esc_attr($movie->getTitle()); ?>" src="<?php echo esc_url($movie->coverUrl) ?>" /></a>
    </div>
    <div class="title">
      <?php
        echo esc_html($movie->getTitle()) ;
        if ($movie->Annee > 0) {
          echo esc_html( sprintf(' (%d)', $movie->Annee) );
        }
        if (!empty($movie->Edition)) {
          echo '<span class="edition"> - ' . esc_html($movie->Edition) . '</span>';
        }
      ?>
    </div>
  </div>
<?php } ?>
</div>
<?php
    include plugin_dir_path(__FILE__) . 'filmotech-movie-list-footer.php';
 ?>
