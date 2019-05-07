<?php
/**
 * Template Name: Exhibition
 */

$primary_content = apply_filters('the_content',get_post_meta($post->ID, '_cmb2_primary_content', true));
$secondary_content = apply_filters('the_content',get_post_meta($post->ID, '_cmb2_secondary_content', true));
?>


<?php get_template_part('templates/page', 'header'); ?>

<div id="page-content">
  <div class="container grid">

    <div class="section md-one-half color-bg-gray-light">
      <div class="primary-content user-content">
        <?= $primary_content ?>
      </div>
    </div>

    <div class="section md-one-half color-bg-gray">
      <?php if (!empty($secondary_content)) { ?>
      <div class="secondary-content user-content">
        <?= $secondary_content ?>
      </div>
      <?php } ?>
      <h3 class="type-h2">Upcoming Events</h3>
      <div class="events-list article-list grid">
        <?php echo \Firebelly\PostTypes\Event\get_events(['num_posts' => 4, 'program-type' => 'exhibitions']); ?>
      </div>
    </div>

  </div>
</div>
