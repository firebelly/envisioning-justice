<?php
/**
 * Template Name: Resources
 */

$no_image_in_header = true;
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
// $per_page = get_option('posts_per_page');
$per_page = 1;
$total_resources = \Firebelly\PostTypes\Resource\get_resources(['countposts' => 1]);
$total_pages = ($total_resources > 0) ? ceil($total_resources / $per_page) : 1;
$resources = \Firebelly\PostTypes\Resource\get_resources(['num_posts' => $per_page]);
?>

<?php include(locate_template('templates/page-header.php')); ?>

<div id="page-content">
  <div class="container grid">

    <div class="section md-one-half color-bg-gray-light<?= (empty($resources) ? ' -empty' : '') ?>">

      <?php
        if (!empty($resources)) {
          echo '<div class="resources-list load-more-container article-list grid">'.$resources.'</div>';
        } else {
          echo '<p class="empty-message">There are currently no resources.</p>';
        }
      ?>

      <?php if ($total_resources > $per_page) { ?>
        <div class="resources-buttons">
          <div class="load-more" data-post-type="event" data-page-at="<?= $paged ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>"><a class="no-ajaxy button -full" href="#">Load More</a></div>
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
