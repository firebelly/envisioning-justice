<?php
/**
 * Sponsor post type
 */

namespace Firebelly\PostTypes\Sponsor;
use PostTypes\PostType; // see https://github.com/jjgrainger/PostTypes
use PostTypes\Taxonomy;

$options = [
  'supports'   => ['title', 'thumbnail'],
  'rewrite'    => ['with_front' => false],
  'menu_icon'  => 'dashicons-store',
  'exclude_from_search' => true
];
$labels = [
  'featured_image'  => 'Sponsor Logo',
  'set_featured_image'  => 'Set Sponsor Logo',
  'remove_featured_image'  => 'Remove Sponsor Logo',
  'use_featured_image'  => 'Use Sponsor Logo'
];
$sponsors = new PostType('sponsor', $options, $labels);
$sponsors->taxonomy('sponsor-type');
$sponsors->register();

// Taxonomies
$sponsor_types = new Taxonomy('sponsor-type');
$sponsor_types->register();

/**
 * CMB2 custom fields
 */
function metaboxes() {
  $prefix = '_cmb2_';

  $sponsor_info = new_cmb2_box([
    'id'            => $prefix . 'sponsor_info',
    'title'         => __( 'Sponsor Info', 'cmb2' ),
    'object_types'  => ['sponsor'],
    'context'       => 'normal',
    'priority'      => 'high',
  ]);
  $sponsor_info->add_field([
    'name'      => 'Description',
    'id'        => $prefix . 'sponsor_description',
    'desc'      => 'A short description, organization affiliation, or job title',
    'type'      => 'wysiwyg',
    'options' => array(
      'media_buttons' => false,
      'textarea_rows' => get_option('default_post_edit_rows', 3),
      'teeny' => true
    ),
  ]);
  $sponsor_info->add_field([
    'name'      => 'Website URL',
    'id'        => $prefix . 'sponsor_url',
    'type'      => 'text_url',
    'desc'      => 'make sure to include http://',
  ]);
}
add_filter( 'cmb2_admin_init', __NAMESPACE__ . '\metaboxes' );

/**
 * Get Sponsors
 */
function get_sponsors($options=[]) {
  $with_thumb_args = [
    'numberposts' => -1,
    'post_type'   => 'sponsor',
    'meta_key'    => '_thumbnail_id',
    'orderby'     => 'title', 
    'order'       => 'ASC'
  ];
  $without_thumb_args = [
    'numberposts' => -1,
    'post_type'   => 'sponsor',
    'meta_query'  => array(
       array(
         'key' => '_thumbnail_id',
         'value' => '?',
         'compare' => 'NOT EXISTS'
       )
    ),
    'orderby'     => 'title', 
    'order'       => 'ASC'
  ];
  if (!empty($options['type'])) {
    $with_thumb_args['tax_query'] = [
      [
        'taxonomy' => 'sponsor-type',
        'field' => 'slug',
        'terms' => $options['type']
      ]
    ];

    $without_thumb_args['tax_query'] = [
      [
        'taxonomy' => 'sponsor-type',
        'field' => 'slug',
        'terms' => $options['type']
      ]
    ];
  }

  // Display all matching posts using article-{$post_type}.php
  $sponsors_posts_with_thumb = get_posts($with_thumb_args);
  $sponsors_posts_without_thumb = get_posts($without_thumb_args);
  if (!$sponsors_posts_with_thumb && !$sponsors_posts_without_thumb) return false;

  $output = '';

  if ($sponsors_posts_with_thumb) {  
    foreach ($sponsors_posts_with_thumb as $sponsor_post):
      ob_start();
      include(locate_template('templates/article-sponsor.php'));
      $output .= ob_get_clean();
    endforeach;
  }

  if ($sponsors_posts_without_thumb) {
    foreach ($sponsors_posts_without_thumb as $sponsor_post):
      ob_start();
      include(locate_template('templates/article-sponsor.php'));
      $output .= ob_get_clean();
    endforeach;
  }

  return $output;
}
