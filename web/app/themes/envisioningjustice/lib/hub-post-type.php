<?php
/**
 * Hub post type
 */

namespace Firebelly\PostTypes\Hub;
use PostTypes\PostType; // see https://github.com/jjgrainger/PostTypes
use PostTypes\Taxonomy;

$options = [
  'supports'   => ['editor', 'title'],
  'rewrite'    => ['with_front' => false],
  'menu_icon'  => 'dashicons-location-alt',
];
// $labels = [
//   'featured_image'  => 'Hub Image',
//   'set_featured_image'  => 'Set Hub Image',
//   'remove_featured_image'  => 'Remove Hub Image',
//   'use_featured_image'  => 'Use Hub Image'
// ];
$hubs = new PostType('hub', $options, $labels);
$hubs->taxonomy('hub area');
$hubs->register();

// Taxonomies
$hub_area = new Taxonomy('hub area');
$hub_area->register();

/**
 * CMB2 custom fields
 */
function metaboxes( array $meta_boxes) {
  $prefix = '_cmb2_';

  $meta_boxes['hub_metadata'] = array(
    'id'            => 'hub_metadata',
    'title'         => __( 'Hub Information', 'cmb2' ),
    'object_types'  => array( 'hub', ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(
      array(
        'name'      => 'Hub Address',
        'id'        => $prefix . 'hub_address',
        'type'      => 'address',
      ),
      array(
        'name'      => 'Hub Description',
        'id'        => $prefix . 'hub_description',
        'type'      => 'textarea_small',
        'desc'      => 'Displays on hubs landing page'
      ),
      array(
        'name'      => 'Hub Website',
        'id'        => $prefix . 'hub_website',
        'type'      => 'text_url',
      ),
    ),
  );

  /**
   * Repeating blocks
   */
  $hub_staff = new_cmb2_box( array(
      'id'           => $prefix . 'hub_staff_organizers_group',
      'title'        => __( 'Staff/Organizers', 'cmb2' ),
      'priority'      => 'low',
      'object_types' => array( 'hub'),
    ) 
  );

  $group_field_id = $hub_staff->add_field( array(
      'id'          => $prefix . 'hub_staff_organizers',
      'type'        => 'group',
      'description' => __( '' ),
      'options'     => array(
          'group_title'   => __( 'Staff Member/Organizer #{#}', 'cmb' ),
          'add_button'    => __( 'Add Another Staff Member/Organizer', 'cmb' ),
          'remove_button' => __( 'Remove Staff Member/Organizer', 'cmb' ),
          'sortable'      => true, // beta
      ),
  ) );

  $hub_staff->add_group_field( $group_field_id, array(
      'name' => 'Name',
      'id'   => 'name',
      'desc' => 'First and last name',
      'type' => 'text',
  ) );

  $hub_staff->add_group_field( $group_field_id, array(
      'name' => 'Bio',
      'id'   => 'bio',
      'type' => 'textarea',
      'desc' => '200 character max',
      'attributes' => array(
          'maxlength'  => '200'
      )
  ) );

  $hub_sponsors = new_cmb2_box( array(
      'id'           => $prefix . 'hub_sponsors_group',
      'title'        => __( 'Sponsors', 'cmb2' ),
      'priority'      => 'low',
      'object_types' => array( 'hub'),
    ) 
  );

  $group_field_id = $hub_sponsors->add_field( array(
      'id'          => $prefix . 'hub_sponsors',
      'type'        => 'group',
      'description' => __( '' ),
      'options'     => array(
          'group_title'   => __( 'Hub Sponsor #{#}', 'cmb' ),
          'add_button'    => __( 'Add Another Sponsor', 'cmb' ),
          'remove_button' => __( 'Remove Sponsor', 'cmb' ),
          'sortable'      => true, // beta
      ),
  ) );

  $hub_sponsors->add_group_field( $group_field_id, array(
      'name' => 'Name',
      'id'   => 'name',
      'type' => 'text'
  ) );

  $hub_sponsors->add_group_field( $group_field_id, array(
      'name' => 'Website',
      'id'   => 'url',
      'desc' => 'Include http://',
      'type' => 'text_url'
  ) );

  $hub_sponsors->add_group_field( $group_field_id, array(
    'name'    => 'Sponsor Logo',
    'desc'    => 'Upload a sponsor logo, ideally in .svg format',
    'id'      => 'logo',
    'type'    => 'file',
    'options' => array(
      'url' => false, // Hide the text input for the url
    ),
    'text'    => array(
      'add_upload_file_text' => 'Add Logo'
    )
  ) );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

/**
 * Get Hubs
 */
function get_hubs($options=[]) {
  if (empty($options['num_posts'])) $options['num_posts'] = -1;
  $args = [
    'numberposts' => $options['num_posts'],
    'post_type'   => 'hub',
  ];
  if (!empty($options['area'])) {
    $args['tax_query'] = [
      [
        'taxonomy' => 'hub area',
        'field' => 'slug',
        'terms' => $options['area']
      ]
    ];
  }

  if (empty($options['show_description'])) $options['show_description'] = false;

  // Display all matching posts using article-{$post_type}.php
  $hubs_posts = get_posts($args);
  if (!$hubs_posts) return false;
  $output = '';
  foreach ($hubs_posts as $hub_post):
    $show_description = $options['show_description'];
    ob_start();
    include(locate_template('templates/article-hub.php'));
    $output .= ob_get_clean();
  endforeach;
  return $output;
}

/**
 * Get Hub Staff/Organizers
 */
function get_hub_staff($post) {
  $output = '';
  $hub_staff_organizers = get_post_meta($post->ID, '_cmb2_hub_staff_organizers', true);
  if ($hub_staff_organizers) {
    foreach ($hub_staff_organizers as $staff_member_organizer) {
      if (!empty($staff_member_organizer['name']))
        $staff_member_organizer_name = $staff_member_organizer['name'];
      if (!empty($staff_member_organizer['bio'])) {
        $staff_member_organizer_bio = apply_filters('the_content', $staff_member_organizer['bio']);
        $output .= '<div class="staff-member-organizer">';
        $output .= '<h4 class="type-h2">' . $staff_member_organizer_name . '</h4>';
        $output .= '<div class="bio user-content">' . $staff_member_organizer_bio . '</div>';
        $output .= '</div>';
      }
    }
  }
  return $output;
}

/**
 * Get Hub Sponsors
 */
function get_hub_sponsors($post) {
  $output = '';
  $hub_sponsors = get_post_meta($post->ID, '_cmb2_hub_sponsors', true);
  if ($hub_sponsors) {
    foreach ($hub_sponsors as $hub_sponsor) {
      if (!empty($hub_sponsor['name']))
        $hub_sponsor_name = $hub_sponsor['name'];
      if (!empty($hub_sponsor['url'])) {
        $hub_sponsor_url = $hub_sponsor['url'];
        $output .= '<div class="sponsor big-clicky">';
      } else {
        $output .= '<div class="sponsor">';
      }
      if (!empty($hub_sponsor['logo'])) {;
        $output .= '<img src="'.$hub_sponsor['logo'].'" class="sponsor-logo">';
      }
      $output .= '<h4 class="type-h3">';
      if ($hub_sponsor_url) {
        $output .= '<a href="'.$hub_sponsor_url.'">'.$hub_sponsor_name.'</a>';
      } else {
        $output .= $hub_sponsor_name;
      }
      $output .= '</div>';
    }
  }
  return $output;
}