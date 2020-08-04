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

<div id="filmotech">
  <div class="movieEssentials">
    <div class="cover">
      <img alt="<?php  echo esc_attr($movie->TitreVF); ?>" width="200" src="<?php echo esc_url($movie->coverUrl) ?>" ?>
    </div>
    <div class="info">
      <p class="movieTitleVF"><?php echo esc_html($movie->TitreVF); ?></p>
      <?php if (!empty ($movie->TitreVO)) { ?>
      <p class="movieTitleVO"><?php echo esc_html($movie->TitreVO); ?></p>
      <?php } ?>
      <p class="categories"><?php echo esc_html($movie->Genre); ?></p>
      <?php if ($movie->Annee > 0) { ?>
      <p class="year"><?php echo esc_html($movie->Annee); ?></p>
      <?php } ?>
      <?php if ($movie->Duree > 0) { ?>
      <p class="duration"><?php echo esc_html($movie->Duree); ?> min.</p>
      <?php } ?>
    </div>
  </div>

  <div class="moviedetails">
    <div class="movie">
      <table>
        <tr>
          <th scope="row"><?php echo __('Realisator','filmotech'); ?></th>
          <td><?php echo nl2br(esc_html($movie->Realisateurs)) ?></td>
        </tr>
        <tr>
          <th scope="row"><?php echo __('Actors','filmotech'); ?></th>
          <td><?php echo nl2br(esc_html($movie->Acteurs)) ?></td>
        </tr>
        <tr>
          <th scope="row"><?php echo __('Synopsis','filmotech'); ?></th>
          <td><?php echo esc_html($movie->Synopsis) ?></td>
        </tr>
        <tr>
          <th scope="row"><?php echo __('Bonus','filmotech'); ?></th>
          <td><?php echo esc_html($movie->Bonus) ?></td>
        </tr>
      </table>
    </div>
    <div class="support">
    </div>
  </div>
</div>

<pre>
<?php
  print_r ($movie);
 ?>
</pre>
