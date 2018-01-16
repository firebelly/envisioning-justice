<?php
/**
 * Template Name: Homepage
 */

$total_events = \Firebelly\PostTypes\Event\get_events(['countposts' => 1]);
$total_news = wp_count_posts('post')->publish;
$events_to_show = 4;
$events = \Firebelly\PostTypes\Event\get_events(['num_posts' => $events_to_show]);
$header_bg = \Firebelly\Media\get_header_bg($post);
$header_text = get_post_meta($post->ID, '_cmb2_header_text', true);
$header_text = str_replace("\n","<br>",strip_tags($header_text, '<u><br><br/>'));
$secondary_header_text = get_post_meta($post->ID, '_cmb2_frontpage_secondary_header_text', true);
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

    <div class="grid home-section -first">
      
      <div class="grid-item md-one-half">
        <div class="map-container">
          <div id="map" data-color="yellow"></div>
        </div>
      </div>

      <section class="events-section section grid-item md-one-half">
        <h2 class="section-title type-h3">Events</h2>
        <?php
          if (!empty($events)) {
            echo '<div class="events-list load-more-container article-list grid">'.$events.'</div>';
          } else {
            echo '<p class="empty-message">There are currently no upcoming events.</p>';
          }
        ?>
        <?php if ($total_events > $events_to_show) { ?>
        <div class="events-buttons grid sm-spaced">
          <div class="load-more grid-item sm-one-half" data-post-type="event" data-page-at="1" data-per-page="<?= $events_to_show ?>" data-total-pages="<?= ceil($total_events/$events_to_show) ?>"><a class="no-ajaxy button" href="#">Load More</a></div>
          <div class="view-all grid-item sm-one-half"><a href="/events/" class="button">All Events</a></div>
        </div>
        <?php } ?>
      </section>

    </div>

    <div class="home-section -second">

      <div class="slashfield-container">
        <div class="-inner">
          <div class="slashfield" data-rows="3"></div>
        </div>
      </div>

      <section class="hub-section section color-bg-yellow">
        <h2 class="section-title type-h3">Hubs</h2>
        <div class="user-content">
          <?= $primary_content ?>
        </div>
        <div class="hub-list grid">
          <?= Firebelly\PostTypes\Hub\get_hubs() ?>
        </div>
        <p class="view-all"><a href="/hubs/" class="button theme-exception -black">All Hubs</a></p>
      </section>

    </div>

    <section class="sponsors-section section">
      <h2 class="type-h2">Sponsors</h2>
      <div class="grid sm-spaced">
        <?= Firebelly\PostTypes\Sponsor\get_sponsors(['type'=>'sponsors']) ?>
      </div>
    </section>

  </div><!-- .container -->
</div><!-- #page-content -->