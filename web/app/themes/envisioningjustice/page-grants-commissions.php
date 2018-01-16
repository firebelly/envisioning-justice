<?php
/**
 * Template Name: Grants & Commissions
 */
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = get_option('posts_per_page');
$total_commissions = \Firebelly\PostTypes\Commission\get_commissions(['countposts' => 1]);
$total_pages = ($total_commissions > 0) ? ceil($total_commissions / $per_page) : 1;
$primary_content = get_post_meta($post->ID, '_cmb2_primary_content', true);
$secondary_content = get_post_meta($post->ID, '_cmb2_secondary_content', true);
$commissions = Firebelly\PostTypes\Commission\get_commissions(['num_posts' => $per_page]);
?>

<?php include(locate_template('templates/page-header.php')); ?>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">

        <h2 class="type-h2">Grants</h2>
        <div class="user-content">
          <?= $primary_content ?>
        </div>

      </div>

      <div class="section md-one-half color-bg-gray">
        <h2 class="type-h2">Artist Commissions</h2>
        <?= $secondary_content ?>

        <?php if (!empty($commissions)) { echo $commissions; } ?>

        <?php if ($total_commissions > $per_page) { ?>
          <div class="commissions-buttons">
            <div class="load-more" data-post-type="commission" data-page-at="<?= $paged ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>"><a class="no-ajaxy button -full" href="#">Load More</a></div>
          </div>
        <?php } ?>
      </div>

  </div>
</div>
