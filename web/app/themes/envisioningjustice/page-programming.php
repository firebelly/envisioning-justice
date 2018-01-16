<?php
/**
 * Template Name: Programming
 */
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = get_option('posts_per_page');
// $per_page = 1;
$total_events = \Firebelly\PostTypes\Event\get_events(['countposts' => 1]);
$total_pages = ($total_events > 0) ? ceil($total_events / $per_page) : 1;
$events = \Firebelly\PostTypes\Event\get_events(['num_posts' => $per_page]);
?>

<?php include(locate_template('templates/page-header.php')); ?>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light<?= (empty($events) ? ' -empty' : '') ?>">

        <?php
          if (!empty($events)) {
            echo '<div class="events-list load-more-container article-list grid">'.$events.'</div>';
          } else {
            // echo '<p class="empty-message">There are currently no upcoming events.</p>';
          // }
        ?>

        <?php if ($total_events > $per_page) { ?>
          <div class="events-buttons">
            <div class="load-more" data-post-type="event" data-page-at="<?= $paged ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>"><a class="no-ajaxy button -full" href="#">Load More</a></div>
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
