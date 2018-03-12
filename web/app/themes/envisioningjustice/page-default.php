<?php
/**
 * Template Name: Single Page
 */

$primary_content = apply_filters('the_content', $post->post_content);
$secondary_content = apply_filters('the_content',get_post_meta($post->ID, '_cmb2_secondary_content', true));
?>

<?php include(locate_template('templates/page-header.php')); ?>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">

        <div class="user-content">
          <?= $primary_content ?>
        </div>

      </div>

      <div class="section md-one-half color-bg-gray">

        <div class="user-content">
          <?= $secondary_content ?>
        </div>

      </div>

  </div>
</div>