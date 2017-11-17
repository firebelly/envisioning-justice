<?php
/**
 * Program post type
 */

namespace Firebelly\PostTypes\Program;
use Firebelly\Utils;

// Custom image size for post type?
// add_image_size( 'programs-thumb', 300, 300, true );

// Register Custom Post Type
function post_type() {

  $labels = array(
    'name'                => 'Programs',
    'singular_name'       => 'Program',
    'menu_name'           => 'Programs',
    'parent_item_colon'   => '',
    'all_items'           => 'All Programs',
    'view_item'           => 'View Program',
    'add_new_item'        => 'Add New Program',
    'add_new'             => 'Add New',
    'edit_item'           => 'Edit Program',
    'update_item'         => 'Update Program',
    'search_items'        => 'Search Programs',
    'not_found'           => 'Not found',
    'not_found_in_trash'  => 'Not found in Trash',
  );
  $rewrite = array(
    'slug'                => '',
    'with_front'          => false,
    'pages'               => false,
    'feeds'               => false,
  );
  $args = array(
    'label'               => 'program',
    'description'         => 'Programs',
    'labels'              => $labels,
    'supports'            => array( 'title', 'excerpt', 'thumbnail', ),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 20,
    'menu_icon'           => 'dashicons-admin-post',
    'can_export'          => false,
    'has_archive'         => false,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'rewrite'             => $rewrite,
    'capability_type'     => 'program',
    'map_meta_cap'        => true
  );
  register_post_type( 'program', $args );

}
add_action( 'init', __NAMESPACE__ . '\post_type', 0 );

/**
 * Add capabilities to control permissions of Post Type via roles
 */
function add_capabilities() {
  $role_admin = get_role('administrator');

  $role_admin->add_cap('edit_program');
  $role_admin->add_cap('read_program');
  $role_admin->add_cap('delete_program');
  $role_admin->add_cap('edit_programs');
  $role_admin->add_cap('edit_others_programs');
  $role_admin->add_cap('publish_programs');
  $role_admin->add_cap('read_private_programs');
  $role_admin->add_cap('delete_programs');
  $role_admin->add_cap('delete_private_programs');
  $role_admin->add_cap('delete_published_programs');
  $role_admin->add_cap('delete_others_programs');
  $role_admin->add_cap('edit_private_programs');
  $role_admin->add_cap('edit_published_programs');
  $role_admin->add_cap('create_programs');
}
add_action('switch_theme', __NAMESPACE__ . 'add_capabilities');

// Custom admin columns for post type
function edit_columns($columns){
  $columns = array(
    'cb' => '<input type="checkbox" />',
    'title' => 'Title',
    'taxonomy-focus_area' => 'Focus Area',
    // 'featured_image' => 'Image',
  );
  return $columns;
}
add_filter('manage_program_posts_columns', __NAMESPACE__ . '\edit_columns');

function custom_columns($column){
  global $post;
  if ( $post->post_type == 'program' ) {
    if ( $column == 'featured_image' )
      echo the_post_thumbnail('thumbnail');
    else {
      $custom = get_post_custom();
      if (array_key_exists($column, $custom))
        echo $custom[$column][0];
    }
  };
}
add_action('manage_posts_custom_column',  __NAMESPACE__ . '\custom_columns');

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
  $cmb_group = new_cmb2_box( array(
      'id'           => $prefix . 'metabox',
      'title'        => __( 'Page Blocks', 'cmb2' ),
      'priority'      => 'low',
      'object_types' => array( 'program', 'page', ),
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

/**
 * Get related resources
 */
function get_resources($post) {
  $files = get_post_meta($post->ID, '_cmb2_resources', true);
  $links = get_post_meta($post->ID, '_cmb2_resource_links', true);
  if (!$files && !$links) return false;

  $output = '<ul class="resources">';
  if ($files) {
    foreach ((array)$files as $attachment_id => $attachment_url) {
      $post = get_post($attachment_id);
      if ($post) {
        $output .= '<li><a target="_blank" href="' . $attachment_url . '">' . $post->post_title . '</a></li>';
      } else {
        $output .= '<li>Attachment not found.</li>';
      }
    }
  }
  // Break up the resource_links field into LIs we can shove into the UL
  if ($links) {
    foreach (explode("\n", str_replace("\n\n","\n",strip_tags($links, '<a>'))) as $link) {
      $output .= '<li>' . $link . '</li>';
    }
  }
  $output .= '</ul>';
  return $output;
}

/**
 * Redirect 404s for Programs to Archive page 
 */
add_filter('404_template', __NAMESPACE__ . '\redirect_archived_programs');
function redirect_archived_programs($template) {
  global $wp_query;
  
  if (!is_404() || empty($wp_query->query_vars['program']))
    return $template;

  $permalink = get_option('home') . '/archived-programs/';

  wp_redirect($permalink, 301);
  exit;
}
