<?php
/**
 * Sponsor post type
 */

namespace Firebelly\PostTypes\Sponsor;
use PostTypes\PostType; // see https://github.com/jjgrainger/PostTypes

$sponsors = new PostType(['name' => 'sponsor', 'plural' => 'Sponsors'], [
  // 'taxonomies' => ['sponsor_category'],
  'supports'   => ['title', 'editor', 'thumbnail'],
  'rewrite'    => ['with_front' => false],
]);
// $sponsors->taxonomy([
//   'name'     => 'sponsor_category',
//   'plural'   => 'Sponsor Categories',
// ]);

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
    'name'      => 'Title',
    'id'        => $prefix . 'sponsor_title',
    'type'      => 'text_medium',
    // 'desc'      => 'e.g. 20xx Freedom Fellow',
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
    $sponsor_post->column_width = $options['column-width'];
    ob_start();
    include(locate_template('templates/article-sponsor.php'));
    $output .= ob_get_clean();
  endforeach;
  return $output;
}
