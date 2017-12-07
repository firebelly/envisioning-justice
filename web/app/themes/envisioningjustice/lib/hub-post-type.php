<?php
/**
 * Hub post type
 */

namespace Firebelly\PostTypes\Hub;
use PostTypes\PostType; // see https://github.com/jjgrainger/PostTypes

$options = [
  'supports'   => ['editor', 'title', 'thumbnail'],
  'rewrite'    => ['with_front' => false],
  'menu_icon'  => 'dashicons-location-alt',
];
$labels = [
  'featured_image'  => 'Hub Logo',
  'set_featured_image'  => 'Set Hub Logo',
  'remove_featured_image'  => 'Remove Hub Logo',
  'use_featured_image'  => 'Use Hub Logo'
];
$hubs = new PostType('hub', $options, $labels);
$hubs->register();

/**
 * CMB2 custom fields
 */
function metaboxes() {
  $prefix = '_cmb2_';

  $hub_info = new_cmb2_box([
    'id'            => $prefix . 'hub_info',
    'title'         => __( 'Hub Info', 'cmb2' ),
    'object_types'  => ['hub'],
    'context'       => 'normal',
    'priority'      => 'high',
  ]);
  $hub_info->add_field([
    'name'      => 'Website URL',
    'id'        => $prefix . 'hub_url',
    'type'      => 'text_url',
    'desc'      => 'make sure to include http://',
  ]);
}
add_filter( 'cmb2_admin_init', __NAMESPACE__ . '\metaboxes' );

/**
 * Get Hubs
 */
function get_hubs($options=[]) {
  if (empty($options['num_posts'])) $options['num_posts'] = -1;
  $args = [
    'numberposts' => $options['num_posts'],
    'post_type'   => 'hub',
  ];
  if (!empty($options['category'])) {
    $args['tax_query'] = [
      [
        'taxonomy' => 'hub_category',
        'field' => 'slug',
        'terms' => $options['category']
      ]
    ];
  }

  // Display all matching posts using article-{$post_type}.php
  $hubs_posts = get_posts($args);
  if (!$hubs_posts) return false;
  $output = '';
  foreach ($hubs_posts as $hub_post):
    ob_start();
    include(locate_template('templates/article-hub.php'));
    $output .= ob_get_clean();
  endforeach;
  return $output;
}
