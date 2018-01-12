<?php
/**
 * Commission post type
 */

namespace Firebelly\PostTypes\Commission;
use PostTypes\PostType; // see https://github.com/jjgrainger/PostTypes

$options = [
  'supports'   => ['editor', 'title', 'thumbnail'],
  'rewrite'    => ['slug' => 'grants-commissions'],
  'menu_icon'  => 'dashicons-art',
];
$labels = [
  'featured_image'  => 'Artist Photo',
  'set_featured_image'  => 'Set Artist Photo',
  'remove_featured_image'  => 'Remove Artist Photo',
  'use_featured_image'  => 'Use Artist Photo'
];
$commissions = new PostType('commission', $options, $labels);
$commissions->register();

/**
 * CMB2 custom fields
 */
function metaboxes() {
  $prefix = '_cmb2_';

  $commission_info = new_cmb2_box([
    'id'            => $prefix . 'commission_info',
    'title'         => __( 'Commission Info', 'cmb2' ),
    'object_types'  => ['commission'],
    'context'       => 'normal',
    'priority'      => 'high',
  ]);
  $commission_info->add_field([
    'name'      => 'Website URL',
    'id'        => $prefix . 'commission_url',
    'type'      => 'text_url',
    'desc'      => 'Link to artist\'s website',
  ]);
  $commission_info->add_field([
    'name'      => 'Link Text',
    'id'        => $prefix . 'commission_link_text',
    'type'      => 'text',
    'desc'      => 'The text that links to the website. If left blank, "artist\'s portfolio" will be used.',
  ]);
}
add_filter( 'cmb2_admin_init', __NAMESPACE__ . '\metaboxes' );

/**
 * Get Commissions
 */
function get_commissions($options=[]) {
  if (empty($options['num_posts'])) $options['num_posts'] = -1;
  $args = [
    'numberposts' => $options['num_posts'],
    'post_type'   => 'commission',
  ];
  if (!empty($options['category'])) {
    $args['tax_query'] = [
      [
        'taxonomy' => 'commission_category',
        'field' => 'slug',
        'terms' => $options['category']
      ]
    ];
  }

  // Display all matching posts using article-{$post_type}.php
  $commissions_posts = get_posts($args);
  if (!$commissions_posts) return false;
  $output = '';
  $output .= '<ul class="commissions-list grid">';
  foreach ($commissions_posts as $commission_post):
    ob_start();
    include(locate_template('templates/article-commission.php'));
    $output .= ob_get_clean();
  endforeach;
  $output .= '</ul>';
  return $output;
}
