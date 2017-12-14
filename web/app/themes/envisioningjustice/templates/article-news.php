<?php 
use Firebelly\Utils;
$category = Utils\get_category($news_post);
$article_tags = Utils\get_article_tags($news_post);
$post_date_timestamp = strtotime($news_post->post_date);
$has_image_class = !empty($show_images) && has_post_thumbnail($news_post->ID) ? 'has-image' : '';
?>
<article class="news-article article <?= $has_image_class ?>">
  <div class="article-content">
    <div class="article-content-wrap">
      <header class="article-header">
<!--         <?php if ($category): ?><div class="article-category"><a href="<?= get_term_link($category); ?>"><?= $category->name; ?></a></div><?php endif; ?> -->
        <h1 class="article-title"><a href="<?= get_the_permalink($news_post); ?>"><?= wp_trim_words($news_post->post_title, 10); ?></a></h1>
      </header>
      <div class="article-details">
        <time class="article-date" datetime="<?= date('c', $post_date_timestamp); ?>"><?= date('n/j', $post_date_timestamp); ?><?= (date('Y', $post_date_timestamp) != date('Y') ? '<span class="year">'.date('/Y', $post_date_timestamp).'</span>' : '') ?></time>
      </div>
      <div class="article-excerpt">
        <p><?= Utils\get_excerpt($news_post); ?></p>
      </div>
      <?php if ($article_tags): ?><div class="article-tags"><?= $article_tags ?></div><?php endif; ?>
      <p class="actions">
        <a href="<?= get_the_permalink($news_post); ?>">Read More</a>
      </p>
    </div>
  </div>
</article>