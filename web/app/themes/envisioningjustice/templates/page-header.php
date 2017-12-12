<?php 
  if (!$post) return; // if we somehow get to this partial w/out a post

  $header_text = get_post_meta($post->ID, '_cmb2_header_text', true);
  $header_text = str_replace("\n","<br>",strip_tags($header_text, '<u><br><br/>'));
  $header_bg = \Firebelly\Media\get_header_bg($post);
?>


<header class="page-header container">
  <div class="-inner">
    <div class="page-header-top">    
      <div class="slashfield" data-rows="5"></div>
      <h2 class="page-title"><?= get_the_title(); ?></h2>
    </div>
    <div class="page-header-bottom grid">
      <div class="page-header-background md-one-half -right" <?= $header_bg ?>></div>
      <div class="page-header-text md-one-half -left section">
        <div class="-inner">
          <h3><?= $header_text ?></h3>
        </div>
      </div>
    </div>
  </div>
</header>