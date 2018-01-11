<?php
/**
 * Single Commission
 */

$body_content = apply_filters('the_content', $post->post_content);
$website = get_post_meta($post->ID, '_cmb2_commission_url', true);
$link_text = get_post_meta($post->ID, '_cmb2_commission_link_text', true);
?>

<?php get_template_part('templates/page', 'header'); ?>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">

        <div class="user-content">
          <?= $body_content ?>
          
          <?php if (!empty($website)) { ?>
            <p class="commission-website type-h3"><a href="<?= $website ?>"><?= (!empty($link_text) ? $link_text : 'Artist\'s Portfolio') ?></a></p>
          <?php } ?>
        </div>


      </div>

      <div class="section md-one-half color-bg-gray">
        
      </div>

  </div>
</div>
