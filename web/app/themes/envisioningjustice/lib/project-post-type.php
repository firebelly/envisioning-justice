<?php
/**
 * Project post type
 */

namespace Firebelly\PostTypes\Project;
use PostTypes\PostType; // see https://github.com/jjgrainger/PostTypes

$options = [
  'supports'   => ['title', 'thumbnail'],
  'rewrite'    => ['slug' => 'grantee-project'],
  'menu_icon'  => 'dashicons-art',
];
$labels = [
  'featured_image'  => 'Project Photo',
  'set_featured_image'  => 'Set Project Photo',
  'remove_featured_image'  => 'Remove Project Photo',
  'use_featured_image'  => 'Use Project Photo'
];
$projects = new PostType('project', $options, $labels);
$projects->register();

/**
 * CMB2 custom fields
 */
function metaboxes() {
  $prefix = '_cmb2_';

  $project_info = new_cmb2_box([
    'id'            => $prefix . 'project_info',
    'title'         => __( 'Project Info', 'cmb2' ),
    'object_types'  => ['project'],
    'context'       => 'normal',
    'priority'      => 'high',
  ]);
  $project_info->add_field([
    'name'      => 'Website URL',
    'id'        => $prefix . 'project_url',
    'type'      => 'text_url',
    'desc'      => 'Optionally link out to external URL',
  ]);
  $project_info->add_field([
    'name'      => 'Link Text',
    'id'        => $prefix . 'project_link_text',
    'type'      => 'text',
    'desc'      => 'The text that links to the website. If left blank, "More about {post_title}" will be used.',
  ]);
  $project_info->add_field([
    'name'  => 'Text',
    'id'    => $prefix . 'text',
    'desc'  => 'Text description that appears on the left.',
    'type'  => 'wysiwyg',
    'sanitization_cb' => false,
  ]);
}
add_filter( 'cmb2_admin_init', __NAMESPACE__ . '\metaboxes' );

/**
 * Get Projects
 */
function get_projects($options=[]) {
  if (empty($options['num_posts'])) $options['num_posts'] = get_option('posts_per_page');
  $args = [
    'numberposts' => $options['num_posts'],
    'post_type'   => 'project',
  ];

  if (!empty($options['countposts'])) {

    // Just count posts (used for load-more buttons)
    $args ['posts_per_page'] = -1;
    $args ['fields'] = 'ids';
    $count_query = new \WP_Query($args);
    return $count_query->found_posts;

  } else {

    // Display all matching posts using article-{$post_type}.php
    $projects_posts = get_posts($args);
    if (!$projects_posts) return false;
    $output = '';
    $output .= '<ul class="projects-list load-more-container grid">';
    foreach ($projects_posts as $project_post):
      ob_start();
      include(locate_template('templates/article-project.php'));
      $output .= ob_get_clean();
    endforeach;
    $output .= '</ul>';
    return $output;

  }
}