<?php
/**
 * Template Name: Programming
 */
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = get_option('posts_per_page');
$body_content = apply_filters('the_content', $post->post_content);

$filter_event_type = get_query_var('filter_event_type', '');
$filter_order = get_query_var('filter_order', '');
$filter_program_type = get_query_var('filter_program_type', '');
$filter_related_hub = get_query_var('filter_related_hub', '');
$args = [
  'event-type' => $filter_event_type,
  'program-type' => $filter_program_type,
  'hub' => $filter_related_hub,
  'order' => $filter_order,
];

// Get post count for load more
$total_events = \Firebelly\PostTypes\Event\get_events(array_merge(['countposts' => 1], $args));
$total_pages = ($total_events > 0) ? ceil($total_events / $per_page) : 1;

// Actually pull posts
$events = \Firebelly\PostTypes\Event\get_events($args);

?>

<?php include(locate_template('templates/page-header.php')); ?>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light<?= (empty($events) ? ' -empty' : '') ?>">

        <div class="user-content">
          <?= $body_content ?>
        </div>

        <?php include(locate_template('templates/events-filter.php')); ?>

        <?php
          if (!empty($events)) {
            echo '<div class="events-list load-more-container article-list grid">'.$events.'</div>';
          } else {
            echo '<p class="empty-message">There are currently no upcoming events.</p>';
          }
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
