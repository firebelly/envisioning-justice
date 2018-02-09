<?php
/**
 * Template Name: Share Your Story
 */

$primary_content = get_post_meta($post->ID, '_cmb2_primary_content', true);
$secondary_content = get_post_meta($post->ID, '_cmb2_secondary_content', true);

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = get_option('posts_per_page');
$total_stories = \Firebelly\PostTypes\Story\get_stories(['countposts' => 1]);
$total_pages = ($total_stories > 0) ? ceil($total_stories / $per_page) : 1;
$stories = \Firebelly\PostTypes\Story\get_stories(['num_posts' => $per_page]);
?>

<?php include(locate_template('templates/page-header.php')); ?>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">

        <div class="user-content">
          <?= $primary_content ?>
          <div class="story-list article-list load-more-container grid">
            <?= $stories ?>
          </div>
          <?php if ($total_stories > $per_page) { ?>
            <div class="commissions-buttons">
              <div class="load-more" data-post-type="story" data-page-at="<?= $paged ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>"><a class="no-ajaxy button -full" href="#">Load More</a></div>
            </div>
          <?php } ?>
        </div>

      </div>

      <div class="section md-one-half color-bg-gray">
        <h2 class="section-title type-h2">Submit Your Story</h2>
        <?= $secondary_content ?>
        <?php include(locate_template('templates/story-submit.php')); ?>
      </div>

  </div>
</div>
