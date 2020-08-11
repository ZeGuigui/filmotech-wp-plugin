<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the admin panel
 *
 * @link       https://www.zeguigui.com
 * @since      1.0.0
 *
 * @package    Filmotech
 * @subpackage Filmotech/public/partials
 */

 if ($databaseType === false) {
   esc_html_e( 'Filmotech settings not set. See Settings to set your filmotech database.', 'filmotech' );
   return;
 }

 if ($db === false) {
   esc_html_e( 'Filmotech settings incorrect or database not reachable. Please check your settings', 'filmotech' );
   return;
 }

?>
<h3><?php _e("Statistics:", 'filmotech'); ?></h3>
<ul>
  <li><span class="dashicons-before dashicons-video-alt3"><?php printf( esc_html( _n('%d movie in the database','%d movies in the database',$public->getMovieCount(),'filmotech')),$public->getMovieCount()); ?></span></li>
  <li><span class="dashicons-before dashicons-visibility"><?php printf( esc_html( __('Last movie added: %s','filmotech') ), $public->getLastAddedMovieTime() ); ?></span></li>
</ul>
<p><a href="<?php echo esc_attr($public->getPageUrl(1)); ?>"><?php _e('View filmotech database','filmotech'); ?></a></p>
