<?php
$event = \Firebelly\PostTypes\Event\get_event_details($post);
$header_text = get_post_meta($post->ID, '_cmb2_header_text', true);
$header_text = str_replace("\n","<br>",strip_tags($header_text, '<u><br><br/>'));
$related_hub = get_post_meta($post->ID, '_cmb2_related_hub', true);
$post_date_timestamp = strtotime($post->post_date);
$article_tags = \Firebelly\Utils\get_article_tags($post);
?>

<header class="page-header container">
  <div class="map-point" data-url="<?= $event_url ?>" data-lat="<?= $event->lat ?>" data-lng="<?= $event->lng ?>" data-title="<?= $event->title ?>" data-desc="<?= $event->desc ?>" data-id="<?= $event->ID ?>"></div>
  <div class="-inner">
    <div class="page-header-top">
      <div class="slashfield" data-rows="7"></div>
      <h2 class="page-title"><span class="color-black">Event</span><br> <?= get_the_title(); ?></h2>
    </div>
    <div class="page-header-bottom grid">
      <div class="page-header-text md-one-half -left section">
        <div class="-inner">
          <?= $related_hub ?>
          <h3 class="type-h2"><?= $header_text ?></h3>
        </div>
      </div>
      <div class="page-header-details md-one-half -right section color-bg-black color-orange">
        <div class="-inner">
          <div class="grid sm-spaced">
            <div class="event-time grid-item sm-one-half">
              <?php if ($event->multiple_days) { ?>
                <p><?= date('l, F j, Y', $event->event_start) ?>
                <br><em>through</em>
                <br><?= date('l, F j, Y', $event->event_end) ?></p>
                <p><?= $event->time_txt ?> Daily</p>
              <?php } else { ?>
                <p><?= date('l, F j, Y', $event->event_start) ?>
                <br><?= $event->time_txt ?></p>
              <?php } ?>
            </div>
            <div class="event-location grid-item sm-one-half">
              <p><?= $event->venue ?>
              <br><?= $event->address['address-1'] ?>
              <?php if (!empty($event->address['address-2'])): ?>
                <br><?= $event->address['address-2'] ?>
              <?php endif; ?>
              <br><?= $event->address['city'] ?>, <?= $event->address['state'] ?> <?= $event->address['zip'] ?>
              </p>
            </div>
          </div>
          <p class="actions">
            <a class="button add-to-calendar" href="<?= $event->add_to_calendar_url ?>">Add To Calendar</a>
          </p>          
        </div>
      </div>
    </div>
  </div>
</header>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">

        <article <?php post_class(); ?>>
          <div class="post-inner">
            <div class="entry-content user-content">
              <?php echo apply_filters('the_content', $post->post_content); ?>
            </div>
            <footer>
              <h3>Cost:</h3>
              <p class="cost">
                <?php if (!$event->cost): ?>
                  Free, open to the public.
                <?php else: ?>
                  <?= $event->cost ?>
                <?php endif; ?>
                <?php if ($event->rsvp_text): ?>
                  <br>RSVP is <?= $event->rsvp_text ?>.
                <?php endif; ?>
              </p>
              <ul class="actions">
                <?php if (!empty($event->registration_url)): ?>
                  <li><a class="register" target="_blank" href="<?= $event->registration_url ?>">Register For Event</a></li>
                <?php elseif (!empty($event->registration_embed)): ?>
                  <li><a class="register smoothscroll" href="#register">Register For Event</a></li>
                <?php endif; ?>
              </ul>
              <?php if ($article_tags): ?><div class="article-tags"><?= $article_tags ?></div><?php endif; ?>
            </footer>
          </div>
        </article>

      </div>

      <div class="md-one-half color-bg-gray">
        <div class="map-container">
          <div id="map" data-color="orange"></div>
        </div>
      </div>

  </div>
</div>