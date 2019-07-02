<?php
/**
 * Template Name: Grantee Project
 */
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = get_option('posts_per_page');
$total_posts = \Firebelly\PostTypes\Project\get_projects(['countposts' => 1]);
$total_pages = ($total_posts > 0) ? ceil($total_posts / $per_page) : 1;
$primary_content = apply_filters('the_content',get_post_meta($post->ID, '_cmb2_primary_content', true));
$secondary_content = apply_filters('the_content',get_post_meta($post->ID, '_cmb2_secondary_content', true));
$projects = Firebelly\PostTypes\Project\get_projects(['num_posts' => $per_page]);
$no_image_in_header = true;
?>

<?php include(locate_template('templates/page-header.php')); ?>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">

        <h2 class="type-h2">Grants</h2>
        <div class="user-content">
          <?= $primary_content ?>
        </div>

      </div>

      <div class="section md-one-half color-bg-gray">
        <h2 class="type-h2">Funded Projects</h2>
        <?= $secondary_content ?>

        <?php if (!empty($projects)) { echo $projects; } ?>

        <?php if ($total_posts > $per_page) { ?>
          <div class="projects-buttons">
            <div class="load-more" data-post-type="project" data-page-at="<?= $paged ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>"><a class="no-ajaxy button -full" href="#">Load More</a></div>
          </div>
        <?php } ?>
      </div>

  </div>
</div>
