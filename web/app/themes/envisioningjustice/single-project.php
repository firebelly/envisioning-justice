<?php
/**
 * Single Project
 */

$text = apply_filters('the_content',get_post_meta($post->ID, '_cmb2_text', true));
$website = get_post_meta($post->ID, '_cmb2_project_url', true);
$link_text = get_post_meta($post->ID, '_cmb2_project_link_text', true);
?>

<?php get_template_part('templates/page', 'header'); ?>

<div id="page-content">
  <div class="container grid">
    <div class="text-section section md-one-half color-bg-gray-light">
      <div class="user-content">
        <?= $text ?>

        <?php if (!empty($website)) { ?>
          <p class="project-website type-h3"><a href="<?= $website ?>" target="_blank"><?= (!empty($link_text) ? $link_text : 'More about '.$post->post_title) ?></a></p>
        <?php } ?>
      </div>
      <p class="page-link">
        <a href="/grantee-project/" class="button">Back to Grantee Project</a>
      </p>
    </div>

    <div class="images-section section md-one-half color-bg-gray">
      <?= \Firebelly\PostTypes\Posts\get_post_slideshow($post->ID, false, false); ?>
    </div>
  </div>
</div>
