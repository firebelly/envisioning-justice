<?php
/**
 * Template Name: Grants & Commissions
 */
$no_image_in_header = true;
$primary_content = get_post_meta($post->ID, '_cmb2_primary_content', true);
$secondary_content = get_post_meta($post->ID, '_cmb2_secondary_content', true);
?>

<?php include(locate_template('templates/page-header.php')); ?>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">

        <h2 class="type-h2">Grants</h2>
        <div class="user-content">
          <?= $primary_content ?>
        </div>

      </div>

      <div class="section md-one-half color-bg-gray">
        <h2 class="type-h2">Artist Commissions</h2>
        <?= $secondary_content ?>
        <?= Firebelly\PostTypes\Commission\get_commissions(); ?>
      </div>

  </div>
</div>
