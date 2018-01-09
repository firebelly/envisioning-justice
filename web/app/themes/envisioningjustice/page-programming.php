<?php
/**
 * Template Name: Programming
 */
$total_events = \Firebelly\PostTypes\Event\get_events(['countposts' => 1]);
$startNum = 4;
?>

<?php include(locate_template('templates/page-header.php')); ?>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">
        
        <?php $events = \Firebelly\PostTypes\Event\get_events(['num_posts' => $startNum]); ?>

        <?php
          if (!empty($events)) {
            echo '<div class="events-list load-more-container article-list grid">'.$events.'</div>';
          } else {
            echo '<p>There are currently no upcoming events.</p>';
          }
        ?>

        <?php if ($total_events > $startNum) { ?>
          <div class="events-buttons">
            <div class="load-more" data-post-type="event" data-page-at="1" data-past-events="0" data-per-page="<?= $startNum ?>" data-total-pages="<?= ceil($total_events/$startNum) ?>"><a class="no-ajaxy button -full" href="#">Load More</a></div>
          </div>
        <?php } ?>

      </div>

      <div class="md-one-half color-bg-gray">
        <div class="map-container">
          <div id="map" data-color="orange"></div>
        </div>
      </div>

  </div>
</div>
