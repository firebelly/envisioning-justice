<?php 
  if (!$post) return; // if we somehow get to this partial w/out a post

  $header_text = get_post_meta($post->ID, '_cmb2_header_text', true);
  $header_text = str_replace("\n","<br>",strip_tags($header_text, '<u><br><br/>'));
  $header_bg = \Firebelly\Media\get_header_bg($post);
  if (empty($slash_rows)) {
    $slash_rows = 5;
  }
?>

<header class="page-header container<?= (!empty($no_image_in_header)?' no-image':'') ?>">
  <div class="-inner">
    <div class="page-header-top">    
      <div class="slashfield" data-rows="<?= $slash_rows ?>"></div>
      <?php if (!empty($secondary_title)) { ?>
      <h2 class="page-title"><div class="color-black"><?= $secondary_title ?></div><?= get_the_title(); ?></h2>
      <?php } else { ?>
      <h2 class="page-title"><?= get_the_title(); ?></h2>
      <?php } ?>
    </div>
    <div class="page-header-bottom grid">
    <?php if (!empty($no_image_in_header)) { ?>
      <div class="page-header-text sm-full section">
        <div class="-inner">
          <h3 class="type-h2"><?= $header_text ?></h3>
        </div>
      </div>
    <?php } else { ?>
      <div class="page-header-background md-one-half -right" <?= $header_bg ?>></div>
      <div class="page-header-text md-one-half -left section">
        <div class="-inner">
          <h3 class="type-h2"><?= $header_text ?></h3>
        </div>
      </div>
    <?php } ?>
    </div>
  </div>
</header>