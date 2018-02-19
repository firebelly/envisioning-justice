<?php 
/**
 * Search results
 */

$post = get_page_by_path('/search');
$page_content = apply_filters('the_content', $post->post_content);

?>
<div class="content-wrap <?= $with_image_class ?>">
  <?php get_template_part('templates/page', 'image-header'); ?>
  <main>
    <div class="search-results-form">
      <h2 class="flag">Search Results For</h2>
      <form role="search" method="get" action="<?= esc_url(home_url('/')); ?>">
        <label class="sr-only"><?php _e('Search for:', 'sage'); ?></label>
        <input type="search" value="<?= get_search_query(); ?>" autocomplete="off" name="s" class="search-field form-control" placeholder="Search" required>
        <button type="submit" class="search-submit icon-search"><span class="sr-only"><?php _e('Search', 'sage'); ?></span></button>
      </form>
    </div>

    <?php if (!have_posts()) : ?>

      <div class="alert alert-warning">
        <?php _e('Sorry, no results were found.', 'sage'); ?>
      </div>

    <?php else: ?>

      <div class="masonry article-list">
      <?php while (have_posts()) : the_post(); ?>

        <?php 
        $show_images = true;

        if ($post->post_type=='post'):

          $news_post = $post;
          include(locate_template('templates/article-news.php'));

        elseif (preg_match('/(event)/',$post->post_type)):

          $event_post = $post;
          include(locate_template('templates/article-event.php'));

        elseif (preg_match('/(program)/',$post->post_type)):

          $program_post = $post;
          include(locate_template('templates/article-program.php'));

        elseif (preg_match('/(page)/',$post->post_type)):

          include(locate_template('templates/article-page.php'));

        endif;
        ?>

      <?php endwhile; ?>
      </div>

      <?php the_posts_navigation(); ?>

    <?php endif; ?>

  </main>

</div>
