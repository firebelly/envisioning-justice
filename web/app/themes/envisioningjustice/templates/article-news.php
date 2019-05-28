<?php
use Firebelly\Utils;
$category = Utils\get_category($news_post);
$article_tags = Utils\get_article_tags($news_post);
$post_date_timestamp = strtotime($news_post->post_date);
$external_url = get_post_meta($news_post->ID, '_cmb2_external_post_url', true);
?>
<article class="news-article article">
  <div class="article-content">
    <div class="article-content-wrap">
      <header class="article-header">
        <h1 class="article-title">
          <?php if (!empty($external_url)) { ?>
            <a href="<?= $external_url ?>" target="_blank"><?= wp_trim_words($news_post->post_title, 10); ?></a>
          <?php } else { ?>
            <a href="<?= get_the_permalink($news_post); ?>"><?= wp_trim_words($news_post->post_title, 10); ?></a>
          <?php } ?>
        </h1>
      </header>
      <div class="article-details">
        <time class="article-date" datetime="<?= date('c', $post_date_timestamp); ?>"><?= date('n/j', $post_date_timestamp); ?><?= (date('Y', $post_date_timestamp) != date('Y') ? '<span class="year">'.date('/Y', $post_date_timestamp).'</span>' : '') ?></time>
      </div>
      <div class="article-excerpt">
        <p><?= Utils\get_excerpt($news_post); ?></p>
      </div>
      <?php if ($article_tags): ?><div class="article-tags"><?= $article_tags ?></div><?php endif; ?>
      <p class="actions">
        <?php if (!empty($external_url)) { ?>
          <a href="<?= $external_url ?>" target="_blank">Read More</a>
        <?php } else { ?>
          <a href="<?= get_the_permalink($news_post); ?>">Read More</a>
        <?php } ?>
      </p>
    </div>
  </div>
</article>