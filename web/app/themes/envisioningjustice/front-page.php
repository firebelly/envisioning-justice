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
$primary_content = get_post_meta($post->ID, '_cmb2_primary_content', true);
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

    <div class="grid -first">
      
      <div class="grid-item md-one-half">
        <div class="map-container">
          <div id="map"></div>
        </div>
        <?php 
        // homepage shows all current events on map
        echo \Firebelly\PostTypes\Event\get_events(['num_posts' => -1, 'map-points' => true]);
        ?>
      </div>

      <section class="events-section section grid-item md-one-half">
        <h2 class="section-title type-h3">Events</h2>
        <div class="events-list load-more-container article-list grid">
          <?php echo \Firebelly\PostTypes\Event\get_events(['num_posts' => 3]); ?>
        </div>
        <div class="events-buttons">
          <div class="load-more" data-post-type="event" data-page-at="1" data-past-events="0" data-per-page="4" data-total-pages="<?= ceil($total_events/4) ?>"><a class="no-ajaxy button" href="#">Load More</a></div>
          <p class="view-all"><a href="/events/" class="button">All Events</a></p>
        </div>
      </section>

    </div>

    <div class="grid -second">

      <div class="slashfield-container">
        <div class="-inner">
          <div class="slashfield" data-rows="3"></div>
        </div>
      </div>

      <section class="hub-section section color-bg-yellow grid-item md-one-half">
        <h2 class="section-title type-h3">Hubs</h2>
        <div class="user-content">
          <?= $primary_content ?>
        </div>
        <div class="hub-list">
          <?= Firebelly\PostTypes\Hub\get_hubs() ?>
          <p class="view-all"><a href="/hubs/" class="button theme-exception -black">All Hubs</a></p>
        </div>
      </section>
      
      <section class="announcements-section section color-bg-gray-light grid-item md-one-half">
        <h2 class="section-title type-h3">Announcements</h2>
        <div class="news-list load-more-container article-list">
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

    </div>

    <section class="sponsor-recognition section">
      <h2 class="type-h2">Sponsors</h2>
      <div class="grid sm-spaced">
        <?= Firebelly\PostTypes\Sponsor\get_sponsors(['type'=>'sponsors']) ?>
      </div>
    </section>

  </div><!-- .container -->
</div><!-- #page-content -->