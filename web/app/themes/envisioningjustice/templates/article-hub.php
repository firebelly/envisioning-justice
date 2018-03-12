<?php
  $hub_url = get_permalink($hub_post);
  $hub_address = get_post_meta($hub_post->ID, '_cmb2_primary_org_address', true);
  $hub_lat = get_post_meta($hub_post->ID, '_cmb2_lat', true);
  $hub_lng = get_post_meta($hub_post->ID, '_cmb2_lng', true);
  $hub_description = get_post_meta($hub_post->ID, '_cmb2_hub_description', true);
?>
<article class="hub<?= ($display_on_map !== false ? ' map-point':'') ?>" data-url="<?= $hub_url ?>" data-lat="<?= $hub_lat ?>" data-lng="<?= $hub_lng ?>" data-title="<?= $hub_post->title ?>">
  <h2 class="article-title type-h2"><a href="<?= $hub_url ?>"><?= $hub_post->post_title ?></a></h2>
  <?php if ($show_description !== false) { ?>
  <p class="hub-description">
    <?= $hub_description ?>
  </p>
  <?php } ?>
  <p class="actions">
    <a href="<?= $hub_url ?>">Find Out More</a>
  </p>
</article>