<?php
/**
 * Template Name: Share Your Story
 */

$primary_content = get_post_meta($post->ID, '_cmb2_primary_content', true);
$secondary_content = get_post_meta($post->ID, '_cmb2_secondary_content', true);
?>

<?php include(locate_template('templates/page-header.php')); ?>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">

        <div class="user-content">
          <?= $primary_content ?>
          <div class="story-list article-list grid">
            <?= Firebelly\PostTypes\Story\get_stories() ?>
          </div>
        </div>

      </div>

      <div class="section md-one-half color-bg-gray">
        <h2 class="type-h2">Submit Your Story</h2>
        <?= $secondary_content ?>
        <?php include(locate_template('templates/story-submit.php')); ?>
      </div>

  </div>
</div>
