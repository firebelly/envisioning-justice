<?php 
/**
 * Single Program
 */
use Firebelly\Utils;
$content_banner_text = get_post_meta($post->ID, '_cmb2_content_banner_text', true);
$body_content = apply_filters('the_content', $post->post_content);
$header_banner_text = str_replace("\n","<br>",get_post_meta($post->ID, '_cmb2_header_banner_text', true));
$addl_info = get_post_meta($post->ID, '_cmb2_addl_info', true);
$with_image_class = (has_post_thumbnail($post->ID)) ? 'with-image' : '';
?>

<article <?php post_class($with_image_class); ?>>

  <?php get_template_part('templates/page', 'image-header'); ?>

  <main>
    <?= Utils\get_page_blocks($post) ?>
  </main>

  <aside class="main">
    <div class="article-list">
      <?= Utils\get_related_event_post($post) ?>
      <?= Utils\get_related_news_post($post) ?>
    </div>
    
    <div class="sidebar-content">
    <?php if ($resources = \Firebelly\PostTypes\Program\get_resources($post)): ?>
      <h3>Resources</h3>
      <?= $resources ?>
    <?php endif; ?>

    <?php if ($addl_info): ?>
      <div class="user-content">
        <?= apply_filters('the_content', $addl_info); ?>
      </div>
    <?php endif; ?>
    </div>
  </aside>
</article>
