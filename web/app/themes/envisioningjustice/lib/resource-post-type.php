<?php
/**
 * Resource post type
 */

namespace Firebelly\PostTypes\Resource;
use PostTypes\PostType; // see https://github.com/jjgrainger/PostTypes
use PostTypes\Taxonomy;

$options = [
  'supports'   => ['editor', 'title'],
  'rewrite'    => ['with_front' => false],
  'menu_icon'  => 'dashicons-sos',
];
$resources = new PostType('resource', $options);
$resources->taxonomy('resource type');
$resources->register();

// Taxonomies
$resource_types = new Taxonomy('resource type');
$resource_types->register();

// Custom CMB2 fields for post type
function metaboxes( array $meta_boxes ) {
  $prefix = '_cmb2_'; // Start with underscore to hide from custom fields list

  $meta_boxes['resource_metabox'] = array(
    'id'            => 'resource_metabox',
    'title'         => __( 'Resource Sidebar Blocks', 'cmb2' ),
    'object_types'  => array( 'resource', ),
    'context'       => 'normal',
    'priority'      => 'low',
    'show_names'    => true,
    'fields'        => array(
      array(
        'name' => 'Resources',
        'desc' => 'Downloadable files e.g. PDFs',
        'id'   => $prefix . 'resources',
        'type' => 'file_list',
      ),
      array(
        'name' => 'Resource Links',
        'desc' => 'Shows below list of Resources',
        'id'   => $prefix . 'resource_links',
        'type' => 'wysiwyg',
        'options' => array(
          'textarea_rows' => 4,
        ),
      ),
      array(
        'name' => 'Additional Info',
        'desc' => 'Partners, Resource Directors, etc',
        'id'   => $prefix . 'addl_info',
        'type' => 'wysiwyg',
      ),
    ),
  );

  /**
   * Repeating blocks
   */
  $cmb_group = new_cmb2_box( array(
      'id'           => $prefix . 'metabox',
      'title'        => __( 'Page Blocks', 'cmb2' ),
      'priority'      => 'low',
      'object_types' => array( 'resource', 'page', ),
    ) 
  );

  $group_field_id = $cmb_group->add_field( array(
      'id'          => $prefix . 'page_blocks',
      'type'        => 'group',
      'description' => __( 'Note that you must be in Text mode to reorder the Page Blocks', 'cmb' ),
      'options'     => array(
          'group_title'   => __( 'Block {#}', 'cmb' ),
          'add_button'    => __( 'Add Another Block', 'cmb' ),
          'remove_button' => __( 'Remove Block', 'cmb' ),
          'sortable'      => true, // beta
      ),
  ) );

  $cmb_group->add_group_field( $group_field_id, array(
      'name' => 'Block Title',
      'id'   => 'title',
      'type' => 'text',
  ) );

  $cmb_group->add_group_field( $group_field_id, array(
      'name' => 'Body',
      'id'   => 'body',
      'type' => 'wysiwyg',
  ) );

  $cmb_group->add_group_field( $group_field_id, array(
      'name' => 'Hide Block',
      // 'desc' => 'Check this to hide Page Block from the front end',
      'id'   => 'hide_block',
      'type' => 'checkbox',
  ) );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

/**
 * Get Resources matching focus_area
 */
function get_resources($focus_area='') {
  $output = '';
  $args = array(
    'numberposts' => -1,
    'post_type' => 'resource',
    'orderby' => ['title' => 'ASC'],
    );
  if ($focus_area != '') {
    $args['tax_query'] = array(
      array(
        'taxonomy' => 'focus_area',
        'field' => 'slug',
        'terms' => $focus_area
      )
    );
  }
  // if ($year != '') {
  //   $args['meta_query'] = array(
  //     array(
  //       'key' => '_cmb2_resource_year',
  //       'value' => $year,
  //       'compare' => '=',
  //     )
  //   );
  // }

  $resource_posts = get_posts($args);
  // if (!$resource_posts) return false;
  return $resource_posts;
}

// Shortcode [resources_filters]
add_shortcode('resources_filters', __NAMESPACE__ . '\shortcode_filters');
function shortcode_filters($atts) {
  global $wpdb;
  $output = '<form class="resource-filters" method="get"><label>Sort By</label> ';
  $args = array(
    'numberposts' => -1,
    'post_type' => 'resource',
    'orderby' => 'menu_order',
    );

  $years = $wpdb->get_col( "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_cmb2_resource_year' GROUP BY meta_value ORDER BY meta_value DESC" );
  $output .= '<div class="select-wrapper"><label>Year:</label><select class="year">';
  $output .= '<option value="">All</option>';
  foreach ($years as $year)
    $output .= '<option value="' . $year . '"' . ($year==$years[0] ? ' selected' : '') . '>' . $year . '</option>';
  $output .= '</select></div> ';

  $sectors = get_terms('focus_area');
  $output .= '<div class="select-wrapper"><label>Sector:</label><select class="sector">';
  $output .= '<option value="">All</option>';
  foreach ($sectors as $sector)
    $output .= '<option value="' . $sector->slug . '">' . $sector->name . '</option>';
  $output .= '</select></div> ';
  $output .= '</form> ';

  return $output;
}

/**
 * Redirect 404s for Resources to Archive page 
 */
add_filter('404_template', __NAMESPACE__ . '\redirect_archived_resources');
function redirect_archived_resources($template) {
  global $wp_query;
  
  if (!is_404() || empty($wp_query->query_vars['resource']))
    return $template;

  $permalink = get_option('home') . '/archived-resources/';

  wp_redirect($permalink, 301);
  exit;
}
