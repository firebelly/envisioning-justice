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
$sponsors->taxonomy('sponsor type');
$sponsors->register();

// Taxonomies
$sponsor_types = new Taxonomy('sponsor type');
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
    'name'      => 'Website URL',
    'id'        => $prefix . 'sponsor_url',
    'type'      => 'text_url',
    'desc'      => 'make sure to include http://',
  ]);

  // Hub Staff/Organizers
  // $cmb_group = new_cmb2_box( array(
  //     'id'           => $prefix . 'metabox',
  //     'title'        => __( 'Page Blocks', 'cmb2' ),
  //     'priority'      => 'low',
  //     'object_types' => array( 'program', 'page', ),
  //   ) 
  // );

  // $group_field_id = $cmb_group->add_field( array(
  //     'id'          => $prefix . 'page_blocks',
  //     'type'        => 'group',
  //     'description' => __( 'Note that you must be in Text mode to reorder the Page Blocks', 'cmb' ),
  //     'options'     => array(
  //         'group_title'   => __( 'Block {#}', 'cmb' ),
  //         'add_button'    => __( 'Add Another Block', 'cmb' ),
  //         'remove_button' => __( 'Remove Block', 'cmb' ),
  //         'sortable'      => true, // beta
  //     ),
  // ) );

  // $cmb_group->add_group_field( $group_field_id, array(
  //     'name' => 'Block Title',
  //     'id'   => 'title',
  //     'type' => 'text',
  // ) );

  // $cmb_group->add_group_field( $group_field_id, array(
  //     'name' => 'Body',
  //     'id'   => 'body',
  //     'type' => 'wysiwyg',
  // ) );

  // $cmb_group->add_group_field( $group_field_id, array(
  //     'name' => 'Hide Block',
  //     // 'desc' => 'Check this to hide Page Block from the front end',
  //     'id'   => 'hide_block',
  //     'type' => 'checkbox',
  // ) );
}
add_filter( 'cmb2_admin_init', __NAMESPACE__ . '\metaboxes' );

/**
 * Get Sponsors
 */
function get_sponsors($options=[]) {
  $with_thumb_args = [
    'numberposts' => -1,
    'post_type'   => 'sponsor',
    'meta_key'    => '_thumbnail_id'
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
  ];
  if (!empty($options['type'])) {
    $with_thumb_args['tax_query'] = [
      [
        'taxonomy' => 'sponsor type',
        'field' => 'slug',
        'terms' => $options['type']
      ]
    ];

    $without_thumb_args['tax_query'] = [
      [
        'taxonomy' => 'sponsor type',
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
