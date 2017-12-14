<?php
/**
 * Single Commission
 */

$body_content = apply_filters('the_content', $post->post_content);
?>

<?php get_template_part('templates/page', 'header'); ?>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">

        <div class="user-content">
          <?= $body_content ?>
        </div>

      </div>

      <div class="section md-one-half color-bg-gray">
        
      </div>

  </div>
</div>
