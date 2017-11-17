<?php 
use Firebelly\Utils;
$category = Utils\get_category($news_post);
$article_tags = Utils\get_article_tags($news_post);
$post_date_timestamp = strtotime($news_post->post_date);
$has_image_class = !empty($show_images) && has_post_thumbnail($news_post->ID) ? 'has-image' : '';
?>
<article class="article <?= $has_image_class ?>">
  <div class="article-content">
    <?php if (!empty($show_images) && $thumb = \Firebelly\Media\get_post_thumbnail($news_post->ID)): ?>
      <a href="<?= get_the_permalink($post) ?>" class="article-thumb" style="background-image:url(<?= $thumb ?>);"></a>
    <?php endif; ?>
    <time class="article-date" datetime="<?= date('c', $post_date_timestamp); ?>"><?= date('n/j', $post_date_timestamp); ?><?= (date('Y', $post_date_timestamp) != date('Y') ? '<span class="year">'.date('/Y', $post_date_timestamp).'</span>' : '') ?></time>
    <div class="article-content-wrap">
      <header class="article-header">
        <?php if ($category): ?><div class="article-category"><a href="<?= get_term_link($category); ?>"><?= $category->name; ?></a></div><?php endif; ?>
        <h1 class="article-title"><a href="<?= get_the_permalink($news_post); ?>"><?= wp_trim_words($news_post->post_title, 10); ?></a></h1>
      </header>
      <div class="article-excerpt">
        <p><?= Utils\get_excerpt($news_post); ?></p>
      </div>
      <?php if ($article_tags): ?><div class="article-tags"><?= $article_tags ?></div><?php endif; ?>
      <?php if (!empty($show_view_all_button)): ?><p class="view-all"><a class="button" href="/news/">View All Articles</a></p><?php endif; ?>
    </div>
  </div>
</article>