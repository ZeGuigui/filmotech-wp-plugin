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
  <?php if (!empty ($movie->TitreVO)) { ?>
  <h2 class="entry-title"><?php echo esc_html($movie->TitreVO); ?></h2>
  <?php } ?>

  <div class="movieEssentials">
    <div class="cover">
        <img
          alt="<?php  echo esc_attr($movie->TitreVF); ?>"
          src="<?php echo esc_url($movie->coverUrl) ?>"
        />
        <?php echo esc_html($movie->Edition); ?>
    </div>
    <div class="info">
      <p class="categories"><?php echo esc_html($movie->Genre); ?></p>
      <?php if ($movie->Duree > 0) { ?>
      <p class="duration"><?php echo esc_html($movie->Duree); ?> min.</p>
      <?php } ?>
      <div id="filmotech-notation" class="stars<?php echo esc_attr($movie->Note); ?>"></div>
      <p class="synopsis">
        <?php echo nl2br(esc_html($movie->Synopsis)); ?>
      </p>
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
          <th scope="row"><?php echo __('Bonus','filmotech'); ?></th>
          <td><?php echo esc_html($movie->Bonus) ?></td>
        </tr>
        <tr>
          <th scope="row"><?php echo __('Reference','filmotech'); ?></th>
          <td><?php echo esc_html($movie->Reference) ?></td>
        </tr>
        <tr>
          <th scope="row"><?php echo __('Support','filmotech'); ?></th>
          <td>
            <?php
              if (!empty($movie->NombreSupport)) {
                echo esc_html($movie->NombreSupport) . ' ';
              }
              echo esc_html($movie->Support)
            ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><?php echo __('Language','filmotech'); ?></th>
          <td><?php echo esc_html($movie->Langues) ?></td>
        </tr>
        <tr>
          <th scope="row"><?php echo __('Subtitles','filmotech'); ?></th>
          <td><?php echo esc_html($movie->SousTitres) ?></td>
        </tr>
        <tr>
          <th scope="row"><?php echo __('Audio','filmotech'); ?></th>
          <td><?php echo esc_html($movie->Audio) ?></td>
        </tr>
      </table>
      <p class="entryDate"><?php echo __('Added to filmotech: ','filmotech'); ?><?php echo esc_html($movie->EntreeDate); ?></p>
    </div>
    <div class="support">
    </div>
  </div>
</div>