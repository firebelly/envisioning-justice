<?php
$header_text = get_post_meta($post->ID, '_cmb2_header_text', true);
$header_text = str_replace("\n","<br>",strip_tags($header_text, '<u><br><br/>'));
$post_date_timestamp = strtotime($post->post_date);
$with_image_class = (has_post_thumbnail($post->ID)) ? 'with-image' : '';
$article_tags = \Firebelly\Utils\get_article_tags($post);
?>

<header class="page-header container">
  <div class="-inner">
    <div class="page-header-top">
      <div class="slashfield" data-rows="5"></div>
      <?php if ($category = \Firebelly\Utils\get_first_term($post)): ?>
        <h2 class="type-h2"><a href="<?= get_term_link($category); ?>"><?php echo $category->name; ?></a></h2>
      <?php endif; ?>
      <h2 class="page-title"><?= get_the_title(); ?></h2>
    </div>
    <div class="page-header-bottom grid">
      <div class="page-header-text no-image sm-full section">
        <div class="-inner">

        </div>
      </div>
    </div>
  </div>
</header>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">

        <article <?php post_class(); ?>>
          <time class="article-date" datetime="<?php echo date('c', $post_date_timestamp); ?>"><?php echo date('n/j', $post_date_timestamp); ?><?= (date('Y', $post_date_timestamp) != date('Y') ? '<span class="year">'.date('/Y', $post_date_timestamp).'</span>' : '') ?></time>
          <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($post->ID, 'large')): ?>
            <div class="article-thumb" style="background-image:url(<?= $thumb ?>);"></div>
          <?php endif; ?>
          <div class="post-inner">
            <div class="entry-content user-content">
              <?php echo apply_filters('the_content', $post->post_content); ?>
            </div>
            <footer>
              <?php if ($article_tags): ?><div class="article-tags"><?= $article_tags ?></div><?php endif; ?>
              <!-- <?php get_template_part('templates/share'); ?> -->
            </footer>
          </div>
        </article>

      </div>

      <div class="images-section md-one-half color-bg-gray">
        <?= \Firebelly\PostTypes\Posts\get_post_slideshow($post->ID); ?>
      </div>

  </div>
</div>