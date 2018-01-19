<?php
$header_text = get_post_meta($post->ID, '_cmb2_header_text', true);
$header_text = str_replace("\n","<br>",strip_tags($header_text, '<u><br><br/>'));
$resource_address = get_post_meta($post->ID, '_cmb2_resource_address', true);
$resource_lat = get_post_meta($post->ID, '_cmb2_lat', true);
$resource_lng = get_post_meta($post->ID, '_cmb2_lng', true);
$body_content = apply_filters('the_content', $post->post_content);
$slash_rows = 7;
$no_image_in_header = true;
$secondary_title = 'Resource';
?>

<?php include(locate_template('templates/page-header.php')); ?>

<div id="page-content">
  <div class="container grid">

    <div class="section md-one-half color-bg-gray-light">

      <article <?php post_class('map-point'); ?> data-url="<?= $resource_url ?>" data-lat="<?= $resource_lat ?>" data-lng="<?= $resource_lng ?>" data-title="<?= $post->title ?>">
        <div class="post-inner">
          <div class="entry-content user-content">
            <?= $body_content ?>
          </div>
        </div>
      </article>

    </div>

    <div class="md-one-half color-bg-gray">
      <div class="resource-meta section color-bg-black color-green">
        <div class="grid sm-spaced">
          <?php
            $terms = get_the_terms($post, 'resource-type');
            $i = 0;
            if (!empty($terms)) {
              echo '<div class="grid-item sm-one-half"><ul>';
              foreach ($terms as $i=>$term) {
                echo '<li class="type-h3">'.$term->name;
                if ($i !== count($terms) - 1) {
                  echo ',';
                }
                echo '</li>';
                $i++;
              }
              echo '</ul></div>';
            }
          ?>
          <div class="grid-item sm-one-half address type-h3">
            <p><?= $resource_address['address-1'] ?></p>
            <p><?= $resource_address['city'].', '.$resource_address['state'].', '.$resource_address['zip'] ?></p>
          </div>
        </div>
      </div>
      <div class="map-container">
        <div id="map" data-color="green"></div>
      </div>
    </div>

  </div>
</div>