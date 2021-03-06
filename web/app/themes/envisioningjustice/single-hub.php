<?php
// Single Hub
$hub_url = get_permalink($post);
$primary_org_name = get_post_meta($post->ID, '_cmb2_primary_org_name', true);
$primary_org_website = get_post_meta($post->ID, '_cmb2_primary_org_website', true);
$primary_org_address = get_post_meta($post->ID, '_cmb2_primary_org_address', true);
$hub_lat = get_post_meta($post->ID, '_cmb2_lat', true);
$hub_lng = get_post_meta($post->ID, '_cmb2_lng', true);
$body_content = apply_filters('the_content', $post->post_content);
// $category = \Firebelly\Utils\get_first_term($post, 'hub-area');
?>

<header class="page-header container map-point" data-url="<?= $hub_url ?>" data-lat="<?= $hub_lat ?>" data-lng="<?= $hub_lng ?>" data-title="<?= $post->title ?>">
  <div class="-inner">
    <div class="page-header-top">
      <div class="slashfield" data-rows="7"></div>
      <h2 class="page-title"><span class="color-black">Hub</span><br> <?= get_the_title(); ?></h2>
    </div>
    <div class="page-header-bottom grid">
      <div class="page-header-text md-one-half -left section">
        <div class="-inner">
          <h3 class="hub-address type-h2">
            <?php if (!empty($primary_org_website)) {
                echo '<a href="'.$primary_org_website.'" target="_blank">'.$primary_org_name.'</a>';
              } else {
                echo $primary_org_name;
              }
            ?>
            <br>
            <?= $primary_org_address['address-1'].'<br> '.get_the_title().', '.$primary_org_address['zip'] ?>
          </h3>
        </div>
      </div>
      <div class="page-header-map md-one-half -right">
        <div class="map-container">
          <div id="map" data-color="green"></div>
        </div>
      </div>
    </div>
  </div>
</header>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">

        <div class="entry-content user-content">
          <?= $body_content ?>
        </div>

        <?= Firebelly\PostTypes\Hub\get_hub_staff($post) ?>

        <?= Firebelly\PostTypes\Hub\get_secondary_orgs($post) ?>

      </div>

      <div class="events-section section md-one-half color-bg-gray">
        <?php
          $related_events = \Firebelly\PostTypes\Event\get_events(['num_posts' => 6, 'hub' => $post->ID, 'exclude_from_map' =>true]);
          $related_events_count = \Firebelly\PostTypes\Event\get_events(['hub' => $post->ID, 'countposts' => true]);
          // Past events?
          $past_related_events_count = \Firebelly\PostTypes\Event\get_events(['hub' => $post->ID, 'countposts' => true, 'past_events' => 1]);

          if ($related_events) { ?>
          <h2 class="type-h2">Upcoming Events at <?= get_the_title(); ?></h2>
          <div class="events-list article-list grid">
            <?= $related_events ?>
          </div>
          <?php if ($related_events_count > 6) { ?>
          <div class="events-buttons section-actions">
            <div class="view-all"><a href="/programming/?filter_related_hub=<?= $post->ID ?>#page-content" class="button -full">All Events</a></div>
          </div>
          <?php } ?>
          <?php if ($past_related_events_count > 0) { ?>
          <div class="events-buttons section-actions">
            <div class="view-all"><a href="/programming/?filter_related_hub=<?= $post->ID ?>&past_events=1#page-content" class="button -full">View Past Events (<?= $past_related_events_count ?>)</a></div>
          </div>
          <?php } ?>
        <?php } ?>
      </div>

  </div>
</div>