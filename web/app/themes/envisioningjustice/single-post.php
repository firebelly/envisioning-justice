<?php
$header_text = get_post_meta($post->ID, '_cmb2_header_text', true);
$header_text = str_replace("\n","<br>",strip_tags($header_text, '<u><br><br/>'));
$post_date_timestamp = strtotime($post->post_date);
$article_tags = \Firebelly\Utils\get_article_tags($post);
$slash_rows = 7;
$no_image_in_header = true;
$secondary_title = 'News Post';
$post_slideshow = \Firebelly\PostTypes\Posts\get_post_slideshow($post->ID);
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
            <?php
              $terms = get_the_terms($post, 'category');
              $i = 0;
              if (!empty($terms)) {
                echo '<footer class="post-footer"><ul class="tags">';
                foreach ($terms as $i=>$term) {
                  echo '<li class="type-h3">'.$term->name;
                  if ($i !== count($terms) - 1) {
                    echo ',';
                  }
                  echo '</li>';
                  $i++;
                }
                echo '</ul></footer>';
              }
            ?>
          </div>
        </article>

      </div>

      <div class="images-section md-one-half color-bg-purple">
        <?php if (!empty($post_slideshow)) {
            echo $post_slideshow;
          } else {
            echo '<div class="post-featured-image" '.\Firebelly\Media\get_header_bg($post).'></div>';
          }
        ?>
      </div>

  </div>
</div>