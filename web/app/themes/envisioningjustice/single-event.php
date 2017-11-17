<?php
$event = \Firebelly\PostTypes\Event\get_event_details($post);
$banner_text = $event->archived ? 'Past Event' : 'Attend An Event';
$with_image_class = (has_post_thumbnail($post->ID)) ? 'with-image' : '';
$article_tags = \Firebelly\Utils\get_article_tags($post);
?>
<article class="post event map-point" data-lat="<?= $event->lat ?>" data-lng="<?= $event->lng ?>" data-title="<?= $event->title ?>" data-desc="<?= $event->desc ?>" data-id="<?= $event->ID ?>">
  <main>
    <h2 class="flag"><?= $banner_text ?></h2>
    <time class="article-date flagged" datetime="<?= date('c', $event->event_start); ?>">
    <?php if (date('Y-m-d', $event->event_start) != date('Y-m-d', $event->event_end)) { ?>
      <span class="month event-start"><?= date('M d', $event->event_start) ?></span>
      <span class="month event-end"><?= date('M d', $event->event_end) ?></span>
    <?php } else { ?>
      <span class="month"><?= date('M', $event->event_start) ?></span> <span class="day"><?= date('d', $event->event_start) ?></span> <?= ($event->year == date('Y') ? ' <span class="year">'.$event->year.'</span>' : '') ?>
    <?php } ?>
    </time>
    <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($post->ID, 'large')): ?>
      <div class="article-thumb" style="background-image:url(<?= $thumb ?>);"></div>
    <?php endif; ?>
    <div class="post-inner">
      <header class="no-header-text <?= $with_image_class ?>">
        <h1 class="article-title"><span><?= $post->post_title ?></span></h1>
      </header>
      <div class="entry-content user-content">
        <?= $event->body ?>
      </div>
      <footer>
        <?php if ($article_tags): ?><div class="article-tags"><?= $article_tags ?></div><?php endif; ?>
        <?php get_template_part('templates/share'); ?>
      </footer>
    </div>

    <?php if (!($event->archived) && !empty($event->registration_embed)): ?>
      <div class="page-block registration-embed" id="register">
        <h2 class="flag">Register for Event</h2>
        <div class="entry-content user-content">
          <?= $event->registration_embed ?>
        </div>
      </div>
    <?php endif; ?>
  </main>

  <aside class="main">
    <h2 class="flag">Event Details</h2>
    <div id="map"></div>
    <div class="event-details">
      <h3>When:</h3>

      <?php if ($event->multiple_days) { ?>
        <p><?= date('l, F j, Y', $event->event_start) ?>
        <br><em>through</em>
        <br><?= date('l, F j, Y', $event->event_end) ?></p>
        <p><?= $event->time_txt ?> Daily</p>
      <?php } else { ?>
        <p><?= date('l, F j, Y', $event->event_start) ?>
        <br><?= $event->time_txt ?></p>
      <?php } ?>

      <h3>Where:</h3>
      <p><?= $event->venue ?>
      <br><?= $event->address['address-1'] ?>
      <?php if (!empty($event->address['address-2'])): ?>
        <br><?= $event->address['address-2'] ?>
      <?php endif; ?>
      <br><?= $event->address['city'] ?>, <?= $event->address['state'] ?> <?= $event->address['zip'] ?>
      </p>

      <?php if (!($event->archived)): ?>
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
          <li><a class="add-to-calendar" href="<?= $event->add_to_calendar_url ?>">Add To My Calendar</a></li>
        </ul>
      <?php endif; ?>

      <?php if (!empty($event->sponsor)): ?>
        <div class="sponsors">
          <h3>Sponsors:</h3>
          <?= apply_filters('the_content', $event->sponsor); ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($event->partner)): ?>
        <div class="partners">
          <h3>Partners:</h3>
          <?= apply_filters('the_content', $event->partner); ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($event->funder)): ?>
        <div class="funders">
          <h3>Funders:</h3>
          <?= apply_filters('the_content', $event->funder); ?>
        </div>
      <?php endif; ?>

    </div>

    <div class="article-list">
      <?= \Firebelly\Utils\get_related_news_post($post) ?>
    </div>
  </aside>
</article>