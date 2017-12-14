<?php
// Single Hub

$hub_address = get_post_meta($post->ID, '_cmb2_hub_address', true);
$body_content = apply_filters('the_content', $post->post_content);
$category = \Firebelly\Utils\get_first_term($post, 'hub area');
?>

<header class="page-header container">
  <div class="-inner">
    <div class="page-header-top">
      <div class="slashfield" data-rows="5"></div>
      <h2 class="page-title"><span class="color-black">Hub</span><br> <?= get_the_title(); ?></h2>
    </div>
    <div class="page-header-bottom grid">
      <div class="page-header-text md-one-half -left section">
        <div class="-inner">
          <h3 class="hub-address type-h2">
            <?= get_the_title(); ?><br>
            <?= $hub_address['address-1'].'<br> '.$category->name.', '.$hub_address['zip'] ?>
          </h3>
        </div>
      </div>
      <div class="page-header-map md-one-half -right">
        <div class="map-container">
          <div id="map"></div>
        </div>
      </div>
    </div>
  </div>
</header>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">

        <div class="entry-content user-content">
          <?php echo apply_filters('the_content', $post->post_content); ?>
        </div>

        <div class="hub-staff accordion">
          <h3 class="accordion-toggle type-h3"><span>Hub Staff/Organizers</span></h3>
          <div class="accordion-content">
            <?= Firebelly\PostTypes\Hub\get_hub_staff($post) ?>
          </div>
        </div>

        <div class="hub-sponsors accordion">
          <h3 class="accordion-toggle type-h3"><span>Sponsors</span></h3>
          <div class="accordion-content">
            <?= Firebelly\PostTypes\Hub\get_hub_sponsors($post) ?>
          </div>
        </div>

      </div>

      <div class="events-section section md-one-half color-bg-gray">
        <h2 class="type-h2">Upcoming Events at <?= get_the_title(); ?></h2>
        <div class="events-list article-list grid">
          <?php echo \Firebelly\PostTypes\Event\get_events(['num_posts' => 4]); ?>
        </div>
      </div>

  </div>
</div>