<?php 
/**
 * Events landing page
 */

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = get_option('posts_per_page');
$past_events = get_query_var('past_events', 0);
$exhibitions = get_query_var('exhibitions', 0);
$filter_program = get_query_var('filter_program', '');
$filter_focus_area = get_query_var('filter_focus_area', '');
$prox_miles = get_query_var('prox_miles', '');
$prox_zip = get_query_var('prox_zip', '');
$args = [
  'past_events' => $past_events,
  'program' => $filter_program,
  'focus_area' => $filter_focus_area,
  'prox_miles' => $prox_miles,
  'prox_zip' => $prox_zip,
];

// Get post count for load more
$total_events = \Firebelly\PostTypes\Event\get_events(array_merge(['countposts' => 1], $args));
$total_pages = ($total_events > 0) ? ceil($total_events / $per_page) : 1;

// Actually pull posts
$event_posts_output = \Firebelly\PostTypes\Event\get_events(array_merge(['show_images' => true], $args));

// Get parent page for various content areas
$post = get_page_by_path('/events');
$with_image_class = (has_post_thumbnail($post->ID)) ? 'with-image' : '';
$page_content = apply_filters('the_content', $post->post_content);
?>
<div class="content-wrap <?= $with_image_class ?>">
  <?php get_template_part('templates/page', 'image-header'); ?>
  <div id="map" class="large hide"></div>

  <main>
    <ul class="flag-tabs">
      <li><a class="<?= !$past_events && !$exhibitions ? 'active' : '' ?>" href="/events/">Upcoming Events</a></li> 
      <li><a class="<?= $exhibitions ? 'active' : '' ?>" href="/events/?exhibitions=1">Ongoing Exhibitions</a></li> 
      <li><a class="<?= $past_events ? 'active' : '' ?> tab" href="/events/?past_events=1">Past Events</a></li>  
    </ul>

    <?php include(locate_template('templates/filters.php')); ?>

    <div class="events load-more-container article-list masonry">
      <?php if ($event_posts_output): ?>
        <?= $event_posts_output ?>
      <?php else: ?>
        <div class="notice">
          <p>No posts found.</p>
        </div>
      <?php endif; ?>
    </div>
    
    <?php if ($total_pages>1): ?>
      <div class="load-more" data-exhibitions="<?= $exhibitions ?>" data-post-type="event" data-page-at="<?= $paged ?>" data-past-events="<?= $past_events ?>" data-focus-area="<?= $filter_focus_area ?>" data-program="<?= $filter_program ?>" data-prox-zip="<?= $prox_zip ?>" data-prox-miles="<?= $prox_miles ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>"><a class="no-ajaxy button" href="#">Load More</a></div>
    <?php endif; ?>
  </main>
  <aside class="main">
      <?php include(locate_template('templates/thought-of-the-day.php')); ?>
  </aside>
</div>