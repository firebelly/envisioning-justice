<?php
/**
 * Template Name: Resources
 */

$no_image_in_header = true;
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = get_option('posts_per_page');

$filter_neighborhood = get_query_var('filter_neighborhood', '');
$filter_resource_type = get_query_var('filter_resource_type', '');
$args = [
  'resource-type' => $filter_resource_type,
  'hub' => $filter_neighborhood,
];
  
// Get post count for load more
$total_resources = \Firebelly\PostTypes\Resource\get_resources(array_merge(['countposts' => 1], $args));
$total_pages = ($total_resources > 0) ? ceil($total_resources / $per_page) : 1;

// Actually pull posts
$resources = \Firebelly\PostTypes\Resource\get_resources($args);

?>

<?php include(locate_template('templates/page-header.php')); ?>

<div id="page-content">
  <div class="container grid">

    <div class="section md-one-half color-bg-gray-light<?= (empty($resources) ? ' -empty' : '') ?>">

      <?php include(locate_template('templates/resources-filter.php')); ?>

      <?php
        if (!empty($resources)) {
          echo '<div class="resources-list load-more-container article-list grid">'.$resources.'</div>';
        } else {
          echo '<p class="empty-message">There are currently no resources.</p>';
        }
      ?>

      <?php if ($total_resources > $per_page) { ?>
        <div class="resources-buttons">
          <div class="load-more" data-post-type="resource" data-page-at="<?= $paged ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>"><a class="no-ajaxy button -full" href="#">Load More</a></div>
        </div>
      <?php } ?>

    </div>

    <div class="md-one-half color-bg-gray">
      <div class="map-container">
        <div id="map" data-color="green"></div>
      </div>
    </div>

  </div>
</div>
