<?php
/**
 * Blog landing page
 */

$post = get_page_by_path('/search');
$page_content = apply_filters('the_content', $post->post_content);
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = get_query_var( 'posts_per_page', 1 );
$total_posts = $GLOBALS['wp_query']->found_posts;
$total_pages = ($total_posts > 0) ? ceil($total_posts / $per_page) : 1;
$no_header_bottom = true;
$secondary_title = 'Search Results For';
?>

<?php include(locate_template('templates/page-header.php')); ?>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light<?= (have_posts() ? '' : ' -empty') ?>">

        <?php if (have_posts()): ?>
          <div class="news-list load-more-container article-list grid">
            <?php 
            while (have_posts()) : the_post();
              $news_post = $post;
              include(locate_template('templates/article-news.php'));
            endwhile; 
            ?>
          </div>
          <?php if ($total_pages > 1) { ?>
            <div class="news-buttons">
              <div class="load-more" data-page-at="<?= $paged ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>"><a class="no-ajaxy button" href="#">Load More</a></div>
            </div>
          <?php } ?>
        <?php else: ?>    
          <p class="empty-message">There are currently no blog posts.</p>
        <?php endif; ?>

      </div>

      <div class="section page-search color-bg-orange md-one-half -right">
        <div class="-inner">
          <?php get_search_form(); ?>
        </div>
      </div>

  </div>
</div>
