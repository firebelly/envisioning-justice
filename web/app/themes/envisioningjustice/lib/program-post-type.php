<?php
/**
 * Program post type
 */

namespace Firebelly\PostTypes\Program;
use PostTypes\PostType; // see https://github.com/jjgrainger/PostTypes

$options = [
  'supports'   => ['editor', 'title'],
  'rewrite'    => ['with_front' => false],
  'menu_icon'  => 'dashicons-nametag',
];
$programs = new PostType('program', $options);
$programs->register();

// Custom CMB2 fields for post type
function metaboxes( array $meta_boxes ) {
  $prefix = '_cmb2_'; // Start with underscore to hide from custom fields list

  $meta_boxes['program_metabox'] = array(
    'id'            => 'program_metabox',
    'title'         => __( 'Program Sidebar Blocks', 'cmb2' ),
    'object_types'  => array( 'program', ),
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
        'desc' => 'Partners, Program Directors, etc',
        'id'   => $prefix . 'addl_info',
        'type' => 'wysiwyg',
      ),
    ),
  );

  /**
   * Repeating blocks
   */
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

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

/**
 * Get Programs matching focus_area
 */
function get_programs($focus_area='') {
  $output = '';
  $args = array(
    'numberposts' => -1,
    'post_type' => 'program',
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
  //       'key' => '_cmb2_program_year',
  //       'value' => $year,
  //       'compare' => '=',
  //     )
  //   );
  // }

  $program_posts = get_posts($args);
  // if (!$program_posts) return false;
  return $program_posts;
}

// Shortcode [programs_filters]
add_shortcode('programs_filters', __NAMESPACE__ . '\shortcode_filters');
function shortcode_filters($atts) {
  global $wpdb;
  $output = '<form class="program-filters" method="get"><label>Sort By</label> ';
  $args = array(
    'numberposts' => -1,
    'post_type' => 'program',
    'orderby' => 'menu_order',
    );

  $years = $wpdb->get_col( "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_cmb2_program_year' GROUP BY meta_value ORDER BY meta_value DESC" );
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