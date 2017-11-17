<?php 
/**
 * Template Name: Our Work
 */

$content_banner_text = get_post_meta($post->ID, '_cmb2_content_banner_text', true);
$body_content = apply_filters('the_content', $post->post_content);
$with_image_class = (has_post_thumbnail($post->ID)) ? 'with-image' : '';
?>
<div class="content-wrap <?= $with_image_class ?>">

  <?php get_template_part('templates/page', 'image-header'); ?>

  <main>
    <h2 class="flag">Our Focus Areas</h2>
    <ul class="focus-list-large">
      <?php 
      $focus_areas = get_terms('focus_area');
      foreach ($focus_areas as $focus_area) {
        include(locate_template('templates/article-focus-area.php'));
      }
      ?>
    </ul>
  </main>

  <aside class="main">
    <?php include(locate_template('templates/thought-of-the-day.php')); ?>
    <div class="sidebar-content user-content dark">
      <?= get_the_block('Our Work Sidebar') ?>
    </div>
  </aside>

</div>