<?php
/**
 * Extra fields for Pages
 */

namespace Firebelly\PostTypes\Pages;

// Custom CMB2 fields for post type
function metaboxes( array $meta_boxes ) {
  $prefix = '_cmb2_';

  $meta_boxes['page_metabox'] = array(
    'id'            => 'page_metabox',
    'title'         => __( 'Header Text', 'cmb2' ),
    'object_types'  => array( 'page', 'program', 'commission', 'event', 'story', 'post', 'resource', 'project' ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(

      // Header fields
      array(
        'name' => 'Header Text',
        'desc' => 'Shows at top of page behind featured image',
        'id'   => $prefix . 'header_text',
        'type' => 'wysiwyg',
        'options' => array(
          'textarea_rows' => 6,
          'media_buttons' => false,
          'teeny'         => true,
        ),
      ),
    ),
  );

  $meta_boxes['frontpage_metabox'] = array(
    'id'            => 'frontpage_metabox',
    'title'         => __( 'Secondary Header Text', 'cmb2' ),
    'object_types'  => array( 'page' ),
    'show_on'       => array( 'key' => 'page-template', 'value' => ['front-page.php'] ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(

      // Secondary Header field
      array(
        'name' => 'Secondary Header Text',
        'desc' => 'Shows below Header Text (as image caption on homepage)',
        'id'   => $prefix . 'frontpage_secondary_header_text',
        'type' => 'wysiwyg',
        'options' => array(
          'textarea_rows' => 4,
        ),
      ),
    ),
  );

  $meta_boxes['resources_metabox'] = array(
    'id'            => 'resources_metabox',
    'title'         => __( 'Resource Submission Text', 'cmb2' ),
    'object_types'  => array( 'page' ),
    'show_on'       => array( 'key' => 'page-template', 'value' => ['page-resources.php'] ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => false,
    'fields'        => array(

      array(
        'name' => 'Resource Submission Text',
        'desc' => 'The text to display as instructions for submitting a resource',
        'id'   => $prefix . 'resource_submission_text',
        'type' => 'wysiwyg',
        'options' => array(
          'textarea_rows' => 4,
          'wpautop' => false,
        ),
      ),
    ),
  );

  $meta_boxes['page_content_areas'] = array(
    'id'            => 'page_content_areas',
    'title'         => __( 'Content', 'cmb2' ),
    'object_types'  => array( 'page' ),
    'show_on'       => array( 'key' => 'page-template', 'value' => ['page-grants-commissions.php','page-share-your-story.php', 'page-about.php', 'page-exhibition.php', 'page-grantee-projects.php'] ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(

      array(
        'name' => 'Primary Content Area',
        'id'   => $prefix . 'primary_content',
        'type' => 'wysiwyg',
        'sanitization_cb' => false,
      ),
      array(
        'name' => 'Secondary Content Area',
        'id'   => $prefix . 'secondary_content',
        'type' => 'wysiwyg',
        'sanitization_cb' => false,
      ),
    ),
  );

  $meta_boxes['single_page_content_areas'] = array(
    'id'            => 'single_page_content_areas',
    'title'         => __( 'Content', 'cmb2' ),
    'object_types'  => array( 'page' ),
    'show_on'       => array( 'key' => 'page-template', 'value' => ['page-default.php'] ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(
      array(
        'name' => 'Secondary Content Area',
        'id'   => $prefix . 'single_secondary_content',
        'type' => 'wysiwyg',
      ),
    ),
  );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

/**
 * Hide editor on various pages based on template
 */
function remove_editor() {
  if (isset($_GET['post'])) {
    $id = $_GET['post'];
    $template = get_post_meta($id, '_wp_page_template', true);

    if(in_array($template, ['page-exhibition.php', 'page-grantee-projects.php'])) {
      remove_post_type_support('page', 'editor');
    }
  }
}
add_action('init', __NAMESPACE__ . '\remove_editor');
