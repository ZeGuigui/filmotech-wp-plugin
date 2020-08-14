<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.zeguigui.com
 * @since      1.0.0
 *
 * @package    Filmotech
 * @subpackage Filmotech/public/partials
 */
?>

<table id="filmotechMovieList">
<thead>
  <tr>
    <th scope="col"><?php _e('Title','filmotech'); ?></th>
    <th scope="col"><?php _e('Categories', 'filmotech');  ?></th>
  </tr>
</thead>
<tbody>
<?php
 foreach ($movies as $movie) {
 ?>
  <tr>
    <td><a href="<?php echo esc_url($movie->permalink); ?>" class="movieLink"><?php
      echo esc_html($movie->getTitle()) ;
      if ($movie->Annee > 0) { echo esc_html( sprintf(' (%d)', $movie->Annee) ); }
      ?></a><?php if (!empty($movie->Edition)) { ?>
        <span class="edition"> - <?php echo esc_html($movie->Edition) ; ?></span>
      <?php } ?>
    </td>
    <td>
      <?php
        $firstCateg = true;
        foreach ($movie->Categories as $category) {
          if (!$firstCateg) { echo ', '; }
          echo "<span>" . esc_html($category) . "</span>";
          $firstCateg = false;
        }
      ?>
    </td>
  </tr>
<?php
  }  // End foreach movie loop
?>
</tbody>
</table>
<?php
    include plugin_dir_path(__FILE__) . 'filmotech-movie-list-footer.php';
 ?>
