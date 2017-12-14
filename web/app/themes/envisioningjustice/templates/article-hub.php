<?php
  $hub_url = get_permalink($hub_post);
  $hub_address = get_post_meta($hub_post->ID, '_cmb2_hub_address', true);
  $hub_description = get_post_meta($hub_post->ID, '_cmb2_hub_description', true);
?>
<article class="hub">
  <h2 class="article-title type-h2"><a href="<?= $hub_url ?>"><?= $hub_post->post_title ?></a></h2>
  <div class="article-details">
    <p class="hub-address"><?= $hub_address['address-1'].', '.$hub_address['city'].', '.$hub_address['state'].', '.$hub_address['zip'] ?></p>
  </div>
  <p class="hub-description">
    <?= $hub_description ?>
  </p>
  <p class="actions">
    <a href="<?= $hub_url ?>">Find Out More</a>
  </p>
</article>