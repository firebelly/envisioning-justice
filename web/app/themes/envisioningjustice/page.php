<?php
/**
 * Single page
 */
$header_text = get_post_meta($post->ID, '_cmb2_header_text', true);
$header_text = str_replace("\n","<br>",strip_tags($header_text, '<u><br><br/>'));
$header_bg = \Firebelly\Media\get_header_bg($post);
$body_content = apply_filters('the_content', $post->post_content);
?>

<header class="page-header container">
  <!-- <?php get_template_part('templates/page', 'image-header'); ?> -->
  <div class="slashfield" data-rows="5"></div>
  <h2 class="page-title"><?= get_the_title(); ?></h2>
  <div class="grid">
    <div class="one-half -left">
      <h3 class="header-text"><?= $header_text ?></h3>
    </div>
    <div class="header-background one-half -right" <?= $header_bg ?>></div>
  </div>
</header>

<div id="page-content">
  <div class="container">
    
    <div class="user-content">
      <?= $body_content ?>
    </div>
    <?= \Firebelly\Utils\get_page_blocks($post) ?>

  </div>
</div>