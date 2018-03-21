<?php 
$event = \Firebelly\PostTypes\Event\get_event_details($event_post);
$article_tags = \Firebelly\Utils\get_article_tags($event_post);
$has_image_class = !empty($show_images) && has_post_thumbnail($event_post->ID) ? 'has-image' : '';
$map_class = empty($exclude_from_map) ? 'map-point' : '';
$event_url = get_permalink($event_post);
?>
<article class="event <?= $map_class ?> <?= $has_image_class ?>" data-url="<?= $event_url ?>" data-lat="<?= $event->lat ?>" data-lng="<?= $event->lng ?>" data-title="<?= $event->title ?>" data-desc="<?= $event->desc ?>" data-id="<?= $event->ID ?>">
  <div class="article-content">
    <?php if (!empty($show_images) && $thumb = \Firebelly\Media\get_post_thumbnail($event_post->ID)): ?>
      <a href="<?= get_the_permalink($event_post) ?>" class="article-thumb" style="background-image:url(<?= $thumb ?>);"></a>
    <?php endif; ?>

    <div class="article-content-wrap"> 
      <h1 class="article-title"><a href="<?= $event_url ?>"><?= $event->title ?></a></h1>
      <div class="event-details article-details">
        <time class="article-date flagged" datetime="<?= date('c', $event->event_start); ?>">
        <?php if (date('d', $event->event_start) != date('d', $event->event_end)) { ?>
          <span class="month event-start"><?= date('M d', $event->event_start) ?></span> - <span class="month event-end"><?= date('M d', $event->event_end) ?></span>
        <?php } else { ?>
          <span class="month"><?= date('M', $event->event_start) ?></span> <span class="day"><?= date('d', $event->event_start) ?></span><?= ($event->year < date('Y') ? ' <span class="year">'.$event->year.'</span>' : '') ?>
        <?php } ?>
        </time>
        <p class="time"><?= $event->time_txt ?></p>
        <?php if (!empty($event->address['city'])): ?>
          <p class="address"><?= $event->address['city'] ?>, <?= $event->address['state'] ?> <?= $event->address['zip'] ?></p>
        <?php endif; ?>
        <?php if ($article_tags): ?><div class="article-tags"><?= $article_tags ?></div><?php endif; ?>
      </div>
      <ul class="actions">
        <li><a class="more" href="<?= $event_url ?>">More Details</a></li>
      </ul>
    </div>
  </div>
</article>