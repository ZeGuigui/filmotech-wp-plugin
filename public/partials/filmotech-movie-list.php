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
<p><?php printf(_n('There is %d movie in the database.','There are %d movies in the database.',$total_record,'filmotech'), $total_record) ?></p>

<?php
 // Add page navigation
 if ($number_of_pages > 1) {
?>
<ul class="default-wp-page clearfix">
  <?php if ($page > 1) { ?>
     <li style="list-style: none;" class="previous">
       <a href="<?php echo esc_attr($this->getPageUrl($page - 1)); ?>"><?php _e('← Previous', 'filmotech'); ?></a>
     </li>
  <?php } ?>
  <?php if ($page < $number_of_pages) { ?>
     <li style="list-style: none;" class="next">
       <a href="<?php echo esc_attr($this->getPageUrl($page + 1)); ?>"><?php _e('Next →', 'filmotech'); ?></a>
     </li>
  <?php } ?>
</ul>
<?php
  }  //end if ($number_of_pages>1)
?>
