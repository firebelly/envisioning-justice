<?php 
/**
 * Template Name: About
 */

$header_text = get_post_meta($post->ID, '_cmb2_header_text', true);
$header_text = str_replace("\n","<br>",strip_tags($header_text, '<u><br><br/>'));
$header_bg = \Firebelly\Media\get_header_bg($post);
$body_content = apply_filters('the_content', $post->post_content);
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

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">

        <div class="user-content">
          <?= $body_content ?>
        </div>

        <div class="accordion">
          <h3 class="accordion-toggle type-h3"><span>Funders</span></h3>
          <div class="accordion-content">
            <?= Firebelly\PostTypes\Sponsor\get_sponsors(['type'=>'funders']) ?>
          </div>
        </div>

        <div class="accordion">
          <h3 class="accordion-toggle type-h3"><span>Partners</span></h3>
          <div class="accordion-content">
            <?= Firebelly\PostTypes\Sponsor\get_sponsors(['type'=>'partners']) ?>
          </div>
        </div>

        <div class="accordion">
          <h3 class="accordion-toggle type-h3"><span>Supporters</span></h3>
          <div class="accordion-content">
            <?= Firebelly\PostTypes\Sponsor\get_sponsors(['type'=>'supporters']) ?>
          </div>
        </div>

      </div>

      <div class="section md-one-half color-bg-gray">
        
      </div>

  </div>
</div>
