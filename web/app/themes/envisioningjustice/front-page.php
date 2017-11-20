<?php
/**
 * Template Name: Homepage
 */

$total_events = \Firebelly\PostTypes\Event\get_events(['countposts' => 1]);
$total_news = wp_count_posts('post')->publish;
$header_bg = \Firebelly\Media\get_header_bg($post);
$header_text = get_post_meta($post->ID, '_cmb2_header_text', true);
$header_text = str_replace("\n","<br>",strip_tags($header_text, '<u><br><br/>'));
$secondary_header_text = get_post_meta($post->ID, '_cmb2_secondary_header_text', true);
$secondary_header_text = strip_tags($secondary_header_text, '<u><strong><em><a><br><br/>');
?>

<header class="page-header container with-image">
  <div class="header-background" <?= $header_bg ?>></div>
  <div class="-inner">
    <div class="slashfield" data-rows="5"></div>
    <h2 class="header-text"><?= $header_text ?></h2>
    <?php if ($secondary_header_text) { ?>
      <div class="header-text-secondary">
        <div class="-inner">
          <p><?= $secondary_header_text ?></p>
        </div>
      </div>
    <?php } ?>
  </div>
</header>

<div id="page-content">
  <div class="container">

    <?php 
    // homepage shows all current events on map
    echo \Firebelly\PostTypes\Event\get_events(['num_posts' => -1, 'map-points' => true]);
    ?>

    <section class="event-cal section">
      <h2 class="type-h3">Events</h2>
      <div class="events load-more-container article-list masonry">
        <?php echo \Firebelly\PostTypes\Event\get_events(['num_posts' => 3]); ?>
      </div>
      <div class="events-buttons">
        <div class="load-more" data-post-type="event" data-page-at="1" data-past-events="0" data-per-page="3" data-total-pages="<?= ceil($total_events/3) ?>"><a class="no-ajaxy button" href="#">Load More</a></div>
        <p class="view-all"><a href="/events/" class="button">View All Events</a></p>
      </div>
    </section>

    <section class="news section">
      <h2 class="type-h3">Announcements</h2>
      <div class="load-more-container article-list masonry">
        <?php 
        // Recent Blog & News posts
        $news_posts = get_posts(['numberposts' => 4, 'category__not_in' => [9]]);
        if ($news_posts):
          foreach ($news_posts as $news_post) {
            include(locate_template('templates/article-news.php'));
          }
        endif;
        ?>
      </div>
      <div class="news-buttons">
        <div class="load-more" data-page-at="1" data-per-page="4" data-total-pages="<?= ceil($total_news/4) ?>"><a class="no-ajaxy button" href="#">Load More</a></div>
        <p class="view-all"><a href="/news/" class="button">All Articles</a></p>
      </div>
    </section>

  </div><<!-- .container -->
</div><<!-- #page-content -->