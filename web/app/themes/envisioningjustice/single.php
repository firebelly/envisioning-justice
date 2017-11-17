<?php
$post_date_timestamp = strtotime($post->post_date);
$with_image_class = (has_post_thumbnail($post->ID)) ? 'with-image' : '';
$article_tags = \Firebelly\Utils\get_article_tags($post);
?>
<article <?php post_class(); ?>>
  <main>
    <?php if ($category = \Firebelly\Utils\get_first_term($post)): ?>
      <h2 class="flag"><a href="<?= get_term_link($category); ?>"><?php echo $category->name; ?></a></h2>
    <?php endif; ?>
    <time class="article-date" datetime="<?php echo date('c', $post_date_timestamp); ?>"><?php echo date('n/j', $post_date_timestamp); ?><?= (date('Y', $post_date_timestamp) != date('Y') ? '<span class="year">'.date('/Y', $post_date_timestamp).'</span>' : '') ?></time>
    <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($post->ID, 'large')): ?>
      <div class="article-thumb" style="background-image:url(<?= $thumb ?>);"></div>
    <?php endif; ?>
    <div class="post-inner">
      <header class="no-header-text <?= $with_image_class ?>">
        <h1 class="article-title"><?php the_title(); ?></h1>
        <?php if ($byline_area = get_post_meta($post->ID, '_cmb2_post_byline', true)): ?>
          <div class="byline-area user-content">
            <?php 
            $byline_area = str_replace(['<h4>','</h4>'],['<p>','</p>'],$byline_area);
            $byline_area = strip_tags($byline_area, '<a><br><br/><p>');
            echo apply_filters('the_content', $byline_area); 
            ?>
          </div>
        <?php endif; ?>
      </header>

      <div class="entry-content user-content">
        <?php echo apply_filters('the_content', $post->post_content); ?>
      </div>
      <footer>
        <?php if ($article_tags): ?><div class="article-tags"><?= $article_tags ?></div><?php endif; ?>
        <?php get_template_part('templates/share'); ?>
      </footer>
    </div>
    <?php comments_template('/templates/comments.php'); ?>
  </main>

  <aside class="main">
    <div class="article-list">
      <?= \Firebelly\Utils\get_related_event_post($post) ?>
    </div>
  </aside>
</article>
