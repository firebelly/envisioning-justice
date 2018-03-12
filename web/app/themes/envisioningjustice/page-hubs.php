<?php 
/**
 * Template Name: Hubs
 */

$body_content = apply_filters('the_content', $post->post_content);
?>

<?php get_template_part('templates/page', 'header'); ?>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">

        <div class="user-content">
          <?= $body_content ?>
          <div class="hub-list article-list grid map-points-container">
            <?= Firebelly\PostTypes\Hub\get_hubs(['show_description'=>true,'show_primary_org'=>true,'display_on_map'=>true]) ?>
          </div>
        </div>

      </div>

      <div class="md-one-half color-bg-gray">
        <div class="map-container">
          <div id="map" data-color="green"></div>
        </div>
      </div>

  </div>
</div>
