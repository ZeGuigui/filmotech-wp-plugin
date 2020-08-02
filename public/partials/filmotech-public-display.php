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

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">

    <h1>Filmotech</h1>

    <p>Hello World</p>
    <p>Vous avez demandé l'ID : <?php echo get_query_var('filmotech') ?></p>
  </main>
</div>

<?php get_footer(); ?>
