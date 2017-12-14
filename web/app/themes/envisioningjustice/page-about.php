<?php 
/**
 * Template Name: About
 */

$body_content = apply_filters('the_content', $post->post_content);
?>


<?php get_template_part('templates/page', 'header'); ?>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">

        <div class="user-content">
          <?= $body_content ?>
        </div>

        <div class="accordion -open">
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
        <div class="events-list article-list grid">
          <?php echo \Firebelly\PostTypes\Event\get_events(['num_posts' => 4]); ?>
        </div>
      </div>

  </div>
</div>
