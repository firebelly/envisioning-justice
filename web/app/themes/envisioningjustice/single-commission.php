<?php
/**
 * Single Commission
 */

$text = apply_filters('the_content',get_post_meta($post->ID, '_cmb2_text', true));
$website = get_post_meta($post->ID, '_cmb2_commission_url', true);
$link_text = get_post_meta($post->ID, '_cmb2_commission_link_text', true);
?>

<?php get_template_part('templates/page', 'header'); ?>

<div id="page-content">
  <div class="container grid">
    <div class="text-section section md-one-half color-bg-gray-light">
      <div class="user-content">
        <?= $text ?>

        <?php if (!empty($website)) { ?>
          <p class="commission-website type-h3"><a href="<?= $website ?>"><?= (!empty($link_text) ? $link_text : 'Artist\'s Portfolio') ?></a></p>
        <?php } ?>
      </div>
    </div>

    <div class="images-section section md-one-half color-bg-gray">
      <?= \Firebelly\PostTypes\Posts\get_post_slideshow($post->ID, false); ?>
    </div>
  </div>
</div>
