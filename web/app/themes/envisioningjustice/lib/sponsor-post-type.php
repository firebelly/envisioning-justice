<?php
/**
 * Sponsor post type
 */

namespace Firebelly\PostTypes\Sponsor;
use PostTypes\PostType; // see https://github.com/jjgrainger/PostTypes

$options = [
  'supports'   => ['title', 'thumbnail'],
  'rewrite'    => ['with_front' => false],
  'menu_icon'  => 'dashicons-store',
];
$labels = [
  'featured_image'  => 'Hub Logo',
  'set_featured_image'  => 'Set Hub Logo',
  'remove_featured_image'  => 'Remove Hub Logo',
  'use_featured_image'  => 'Use Hub Logo'
];
$sponsors = new PostType('sponsor', $options, $labels);
$sponsors->register();

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
}
add_filter( 'cmb2_admin_init', __NAMESPACE__ . '\metaboxes' );

/**
 * Get Sponsors
 */
function get_sponsors($options=[]) {
  if (empty($options['num_posts'])) $options['num_posts'] = -1;
  $args = [
    'numberposts' => $options['num_posts'],
    'post_type'   => 'sponsor',
  ];
  if (!empty($options['category'])) {
    $args['tax_query'] = [
      [
        'taxonomy' => 'sponsor_category',
        'field' => 'slug',
        'terms' => $options['category']
      ]
    ];
  }

  // Display all matching posts using article-{$post_type}.php
  $sponsors_posts = get_posts($args);
  if (!$sponsors_posts) return false;
  $output = '';
  foreach ($sponsors_posts as $sponsor_post):
    ob_start();
    include(locate_template('templates/article-sponsor.php'));
    $output .= ob_get_clean();
  endforeach;
  return $output;
}
