<?php
  $resource_url = get_permalink($resource_post);
  $resource_address = get_post_meta($resource_post->ID, '_cmb2_primary_org_address', true);
  $resource_lat = get_post_meta($resource_post->ID, '_cmb2_lat', true);
  $resource_lng = get_post_meta($resource_post->ID, '_cmb2_lng', true);
  $resource_description = get_post_meta($resource_post->ID, '_cmb2_resource_description', true);
  $related_hub = get_post_meta($resource_post->ID, '_cmb2_related_hub', true);
  $first_type = \Firebelly\Utils\get_first_term($resource_post, 'resource-type');
?>
<article class="resource map-point" data-url="<?= $resource_url ?>" data-lat="<?= $resource_lat ?>" data-lng="<?= $resource_lng ?>" data-title="<?= $resource_post->title ?>">
  <h2 class="article-title type-h2"><a href="<?= $resource_url ?>"><?= $resource_post->post_title ?></a></h2>
  <div class="article-details">
    <p><?php if ($related_hub) { echo get_the_title($related_hub); } ?></p>
    <p><?php if ($first_type) { echo $first_type->name; } ?></p>
  </div>
  <p class="resource-description">
    <?= $resource_description ?>
  </p>
  <p class="actions">
    <a href="<?= $resource_url ?>">Find Out More</a>
  </p>
</article>