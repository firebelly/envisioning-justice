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
      <div class="slashfield" data-rows="7"></div>
      <h2 class="page-title"><span class="color-black">Your Stories</span><br> <?= get_the_title(); ?></h2>
    </div>
    <div class="page-header-bottom grid">
      <div class="page-header-text no-image sm-full section">
        <div class="-inner type-h2">
          <?= $header_text ?>
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
          </div>
        </article>

      </div>

      <div class="images-section md-one-half color-bg-gray">
        <?= \Firebelly\PostTypes\Posts\get_post_slideshow($post->ID); ?>
      </div>

  </div>
</div>