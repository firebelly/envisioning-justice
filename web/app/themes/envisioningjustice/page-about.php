<?php 
/**
 * Template Name: About
 */

$primary_content = apply_filters('the_content',get_post_meta($post->ID, '_cmb2_primary_content', true));
$secondary_content = apply_filters('the_content',get_post_meta($post->ID, '_cmb2_secondary_content', true));
?>


<?php get_template_part('templates/page', 'header'); ?>

<div id="page-content">
  <div class="container grid">

      <div class="section md-one-half color-bg-gray-light">

        <div class="user-content">
          <?= $primary_content ?>
        </div>

        <div class="sponsor-types">
          
          <?php

            $sponsor_types = get_terms( array(
              'taxonomy' => 'sponsor type'
            ));

            foreach ($sponsor_types as $key => $sponsor_type) {
              // Sponsors are not shown on the about page
              if ($sponsor_type->slug == 'sponsors') {
                continue;
              } else {
                $term = get_term_by('slug', $sponsor_type->slug, 'sponsor type'); 
                $description = term_description($term->term_id, 'sponsor type');
                echo '<div class="accordion'.($key===0?' -open':'').'">
                        <h3 class="accordion-toggle type-h3"><span>'.$sponsor_type->name.'</span></h3>';
                if (!empty($description)) {
                  echo '<p>'.$description.'</p>';
                }
                echo '<div class="accordion-content">';
                echo Firebelly\PostTypes\Sponsor\get_sponsors(['type'=>$sponsor_type->slug]);
                echo '</div>
                  </div>';
              }
            }

          ?>

        </div>

      </div>

      <div class="section md-one-half color-bg-gray">
        <?php if (!empty($secondary_content)) { ?>
        <div class="secondary-content user-content">
          <?= $secondary_content ?>
        </div>
        <?php } ?>
        <div class="events-list article-list grid">
          <?php echo \Firebelly\PostTypes\Event\get_events(['num_posts' => 4]); ?>
        </div>
      </div>

  </div>
</div>
