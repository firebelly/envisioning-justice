<?php 
/**
 * Blog landing page
 */

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = get_option('posts_per_page');
$total_posts = $GLOBALS['wp_query']->found_posts;
$total_pages = ($total_posts > 0) ? ceil($total_posts / $per_page) : 1;
$post = get_page_by_path('/news');
$with_image_class = (has_post_thumbnail($post->ID)) ? 'with-image' : '';
?>

<div class="content-wrap <?= $with_image_class ?>">
  <?php get_template_part('templates/page', 'image-header'); ?>
  <main>
    <h4 class="flag">Filter Posts</h4>
    <?php include(locate_template('templates/filters.php')); ?>

    <div class="load-more-container masonry article-list">
      <?php if (have_posts()): ?>
        <?php 
        while (have_posts()) : the_post();
          $news_post = $post;
          $show_images = true;
          include(locate_template('templates/article-news.php'));
        endwhile; 
        ?>
      </div>
      <?php if ($total_pages>1): ?>
        <div class="load-more" data-page-at="<?= $paged ?>" data-focus-area="<?= $filter_focus_area ?>" data-program="<?= $filter_program ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>"><a class="no-ajaxy button" href="#">Load More</a></div>
      <?php endif ?>
    <?php else: ?>    
      <div class="notice">
        <p>No posts found.</p>
      </div>
    <?php endif; ?>
  </main>

  <aside class="main">
    <?php include(locate_template('templates/thought-of-the-day.php')); ?>
  </aside>
</div>