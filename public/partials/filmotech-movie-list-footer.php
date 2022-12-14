<?php
  if ($category === null) {
?>
<p><?php printf(_n('There is %d movie in the database.','There are %d movies in the database.',$total_record,'filmotech'), $total_record) ?></p>
<?php
  } else {
?>
<p><?php printf(_n('There is %1d movie in the %2s category.','There are %1d movies in the %2s category.',$total_record,'filmotech'), $total_record, mb_strtolower($category)) ?></p>
<?php
  }

 // Add page navigation
 if ($number_of_pages > 1) {
?>
<ul id="filmotech-paginator" class="clearfix">
  <?php if ($page > 1) { ?>
     <li class="previous">
       <a href="<?php echo esc_attr($this->getPageUrl($page - 1, $category)); ?>"><?php _e('← Previous', 'filmotech'); ?></a>
     </li>
  <?php } ?>
  <?php for ($i = 1; $i <= $number_of_pages; $i++) {
          $suppClass = ($i == $page) ? ' currentPage' : '';
    ?>
    <li class="pageNumber<?php echo esc_attr($suppClass); ?>">
      <a href="<?php echo esc_attr($this->getPageUrl($i, $category)); ?>"><?php echo esc_html($i) ?></a>
    </li>
  <?php } ?>
  <?php if ($page < $number_of_pages) { ?>
     <li class="next">
       <a href="<?php echo esc_attr($this->getPageUrl($page + 1, $category)); ?>"><?php _e('Next →', 'filmotech'); ?></a>
     </li>
  <?php } ?>
</ul>
<?php
  }  //end if ($number_of_pages>1)
?>
