<?php
/**
 * Focus Area taxonomy
 */

namespace Firebelly\PostTypes\FocusArea;

/**
 * Add capabilities to control permissions of Taxonomy via roles
 */
function add_capabilities() {
  $role_admin = get_role('administrator');
  $role_admin->add_cap('manage_focus_areas');
  $role_admin->add_cap('edit_focus_areas');
  $role_admin->add_cap('delete_focus_areas');
  $role_admin->add_cap('assign_focus_areas');
}
add_action('switch_theme', __NAMESPACE__ . '\add_capabilities');

// Custom taxonomy Focus Areas
register_taxonomy( 'focus_area', 
  array('program', 'post', 'thought', 'event'),
  array('hierarchical' => true, // if this is true, it acts like categories
    'labels' => array(
      'name' => 'Focus Areas',
      'singular_name' => 'Focus Area',
      'search_items' =>  'Search Focus Areas',
      'all_items' => 'All Focus Areas',
      'parent_item' => 'Parent Focus Area',
      'parent_item_colon' => 'Parent Focus Area:',
      'edit_item' => 'Edit Focus Area',
      'update_item' => 'Update Focus Area',
      'add_new_item' => 'Add New Focus Area',
      'new_item_name' => 'New Focus Area',
    ),
    'show_admin_column' => true, 
    'show_ui' => true,
    'query_var' => true,
    'capabilities' => array(
        'manage_terms' => 'manage_focus_areas',
        'edit_terms' => 'edit_focus_areas',
        'delete_terms' => 'delete_focus_areas',
        'assign_terms' => 'assign_focus_areas'
    ),
    'rewrite' => array( 
      'slug' => 'focus-area',
      'with_front' => false 
    ),
  )
);
