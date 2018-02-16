<?php
$header_text = get_post_meta($post->ID, '_cmb2_header_text', true);
$header_text = str_replace("\n","<br>",strip_tags($header_text, '<u><br><br/>'));
$post_date_timestamp = strtotime($post->post_date);
$with_image_class = (has_post_thumbnail($post->ID)) ? 'with-image' : '';
$article_tags = \Firebelly\Utils\get_article_tags($post);
$slash_rows = 7;
$no_image_in_header = true;
$secondary_title = 'Your Stories';
?>

<?php include(locate_template('templates/page-header.php')); ?>

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

      <div class="images-section md-one-half color-bg-gray-dark">
        <?= \Firebelly\PostTypes\Posts\get_post_slideshow($post->ID); ?>
      </div>

  </div>
</div>