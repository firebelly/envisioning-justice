<?php
/**
 * Template Name: Programming
 */
?>

<?php include(locate_template('templates/page-header.php')); ?>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">
        
        <div class="events-list load-more-container article-list grid">
          <?php echo \Firebelly\PostTypes\Event\get_events(); ?>
        </div>
        <div class="events-buttons">
          <div class="load-more" data-post-type="event" data-page-at="1" data-past-events="0" data-per-page="4" data-total-pages="<?= ceil($total_events/4) ?>"><a class="no-ajaxy button" href="#">Load More</a></div>
          <p class="view-all"><a href="/events/" class="button">All Events</a></p>
        </div>

      </div>

      <div class="md-one-half color-bg-gray">
        <div class="map-container">
          <div id="map" data-color="orange"></div>
        </div>
      </div>

  </div>
</div>
