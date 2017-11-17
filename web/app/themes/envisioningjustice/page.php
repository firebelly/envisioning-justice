<?php
/**
 * Single page
 */

$content_banner_text = get_post_meta($post->ID, '_cmb2_content_banner_text', true);
$body_content = apply_filters('the_content', $post->post_content);
$with_image_class = (has_post_thumbnail($post->ID)) ? ' with-image' : '';
$has_header_text_class = get_post_meta($post->ID, '_cmb2_header_text', true) ? '' : ' no-header-text';
?>
<div class="content-wrap<?= $with_image_class ?><?= $has_header_text_class ?>">

  <?php get_template_part('templates/page', 'image-header'); ?>

  <main>
    <?php if ($content_banner_text): ?>
      <h2 class="banner-text"><?= $content_banner_text ?></h2>
    <?php endif; ?>

    <div class="one-column">
      <div class="user-content">
        <?= $body_content ?>
      </div>
    </div>

    <?= \Firebelly\Utils\get_page_blocks($post) ?>

  </main>

</div>