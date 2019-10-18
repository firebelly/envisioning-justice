<?php
/**
 * Blog landing page
 */

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = get_option('posts_per_page');
$total_posts = $GLOBALS['wp_query']->found_posts;
$total_pages = ($total_posts > 0) ? ceil($total_posts / $per_page) : 1;
$post = get_page_by_path('/news');
$categories = get_categories(['taxonomy' => 'category']);
?>

<?php include(locate_template('templates/page-header.php')); ?>

<div id="page-content">
  <div class="container">
    <div class="news-lists-container color-bg-gray-light">

      <?php foreach ($categories as $category): ?>
        <?php
          $category = get_term($category, 'category');
          $total_cat_posts = new WP_Query(['cat' => $category->term_id]);
          $total_cat_count = $total_cat_posts->found_posts;
          $total_cat_pages = ($total_cat_count > 0) ? ceil($total_cat_count / $per_page) : 1;
          $posts = get_posts(['numberposts' => $per_page, 'category' => $category->term_id]);
        ?>
        <div id="<?= $category->slug ?>" class="post-category section">

          <h3><?= $category->name ?></h3>
          <?php if (!empty($posts)): ?>
            <div class="news-list load-more-container article-list grid masonry">
              <?php foreach ($posts as $news_post): ?>
                <?php include(locate_template('templates/article-news.php')); ?>
              <?php endforeach ?>
            </div>
            <?php if ($total_cat_pages > 1) { ?>
              <div class="news-buttons">
                <div class="load-more" data-page-at="<?= $paged ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_cat_pages ?>" data-category="<?= $category->slug ?>"><a class="no-ajaxy button" href="#">Load More</a></div>
              </div>
            <?php } ?>
          <?php else: ?>
            <p class="empty-message">There are currently no blog posts in this category.</p>
          <?php endif; ?>
        </div>
      <?php endforeach ?>

    </div>
  </div>
</div>