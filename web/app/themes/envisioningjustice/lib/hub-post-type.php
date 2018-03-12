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
$hubs = new PostType('hub', $options, $labels);
// $hubs->taxonomy('hub-area');
$hubs->register();

// Taxonomies
// $hub_area = new Taxonomy('hub-area');
// $hub_area->register();

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
        'name'      => 'Hub Description',
        'id'        => $prefix . 'hub_description',
        'type'      => 'textarea_small',
        'desc'      => 'Displays on hubs landing page'
      ),
      array(
        'name'      => 'Primary Organization Name',
        'id'        => $prefix . 'primary_org_name',
        'type'      => 'text',
      ),
      array(
        'name'      => 'Primary Organization Address',
        'id'        => $prefix . 'primary_org_address',
        'type'      => 'address',
      ),
      array(
        'name'      => 'Primary Organization Website',
        'id'        => $prefix . 'primary_org_website',
        'type'      => 'text_url',
      ),
    ),
  );

  /**
   * Repeating blocks
   */
  $hub_staff = new_cmb2_box( array(
      'id'           => $prefix . 'hub_staff_organizers_group',
      'title'        => __( 'Primary Organization Staff/Organizers', 'cmb2' ),
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

  $secondary_orgs = new_cmb2_box( array(
      'id'           => $prefix . 'secondary_orgs_group',
      'title'        => __( 'Secondary Organizations', 'cmb2' ),
      'priority'      => 'low',
      'object_types' => array( 'hub'),
    ) 
  );

  $group_field_id = $secondary_orgs->add_field( array(
      'id'          => $prefix . 'secondary_orgs',
      'type'        => 'group',
      'description' => __( '' ),
      'options'     => array(
          'group_title'   => __( 'Supporting Organization #{#}', 'cmb' ),
          'add_button'    => __( 'Add Another Organization', 'cmb' ),
          'remove_button' => __( 'Remove Organization', 'cmb' ),
          'sortable'      => true, // beta
      ),
  ) );

  $secondary_orgs->add_group_field( $group_field_id, array(
      'name' => 'Name',
      'id'   => 'name',
      'type' => 'text'
  ) );

  $secondary_orgs->add_group_field( $group_field_id, array(
      'name' => 'Description',
      'id'   => 'description',
      'type' => 'wysiwyg'
  ) );

  $secondary_orgs->add_group_field( $group_field_id, array(
      'name' => 'Website',
      'id'   => 'website',
      'desc' => 'Include http://',
      'type' => 'text_url'
  ) );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

/**
 * Update lookup table for events geodata, if post_id isn't sent, all posts are updates/inserted into wp_events_lat_lng
 */
function update_hubs_lat_lng($post_id='') {
  global $wpdb;
  $hub_cache = [];
  $post_id_sql = empty($post_id) ? '' : ' AND post_id='.(int)$post_id;
  $hub_posts = $wpdb->get_results("SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta WHERE meta_key IN ('_cmb2_lat','_cmb2_lng') AND meta_value != '' {$post_id_sql} ORDER BY post_id");
  if ($hub_posts) {
    foreach ($hub_posts as $hub) {
      $hub_cache[$hub->post_id][$hub->meta_key] = $hub->meta_value;
    }
    foreach ($hub_cache as $hub_id=>$arr) {
      $cnt = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM wp_hubs_lat_lng WHERE post_id=%d", $hub_id) );
      if ($cnt>0) {
        $wpdb->query( $wpdb->prepare("UPDATE wp_hubs_lat_lng SET lat=%s, lng=%s WHERE post_id=%d", $arr['_cmb2_lat'], $arr['_cmb2_lng'], $hub_id) );
      } else {
        $wpdb->query( $wpdb->prepare("INSERT INTO wp_hubs_lat_lng (post_id,lat,lng) VALUES (%d,%s,%s)", $hub_id, $arr['_cmb2_lat'], $arr['_cmb2_lng']) );
      }
    }
  }
}


/**
 * Geocode address for hub and save in custom fields
 */
function geocode_address($post_id, $post='') {
  $address = !empty($_POST['_cmb2_primary_org_address']) ? $_POST['_cmb2_primary_org_address'] : '';
  $address = wp_parse_args($address, array(
      'address-1' => '',
      'address-2' => '',
      'city'      => '',
      'state'     => '',
      'zip'       => '',
   ));

  if (!empty($address['address-1'])):
    $address_combined = $address['address-1'] . ' ' . $address['address-2'] . ' ' . $address['city'] . ', ' . $address['state'] . ' ' . $address['zip'];
    $request_url = "http://maps.google.com/maps/api/geocode/xml?sensor=false&address=" . urlencode($address_combined);

    $xml = simplexml_load_file($request_url);
    $status = $xml->status;
    if(strcmp($status, 'OK')===0):
        $lat = $xml->result->geometry->location->lat;
        $lng = $xml->result->geometry->location->lng;
        update_post_meta($post_id, '_cmb2_lat', (string)$lat);
        update_post_meta($post_id, '_cmb2_lng', (string)$lng);
        update_hubs_lat_lng($post_id);
    endif;
  endif;
}
add_action('save_post_hub', __NAMESPACE__ . '\\geocode_address', 20, 2);

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
        'taxonomy' => 'hub-area',
        'field' => 'slug',
        'terms' => $options['area']
      ]
    ];
  }

  if (empty($options['show_description'])) $options['show_description'] = false;
  if (empty($options['show_primary_org'])) $options['show_primary_org'] = false;
  if (empty($options['display_on_map'])) $options['display_on_map'] = false;

  // Display all matching posts using article-{$post_type}.php
  $hubs_posts = get_posts($args);
  if (!$hubs_posts) return false;
  $output = '';
  foreach ($hubs_posts as $hub_post):
    $show_primary_org = $options['show_primary_org'];
    $show_description = $options['show_description'];
    $display_on_map = $options['display_on_map'];
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
  if (!$hub_staff_organizers) return false;
  $output .= '<div class="hub-staff accordion">
    <h3 class="accordion-toggle type-h3"><span>Hub Staff/Organizers</span></h3>
    <div class="accordion-content">';
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
  $output .= '</div></div>';
  return $output;
}

/**
 * Get Secondary Organizations
 */
function get_secondary_orgs($post) {
  $output = '';
  $secondary_orgs = get_post_meta($post->ID, '_cmb2_secondary_orgs', true);
  if (!$secondary_orgs) return false;
  $output .= '<div class="secondary-orgs"><h3 class="type-h2">Supporting Organizations</h3>';
  foreach ($secondary_orgs as $secondary_org) {
    $output .= '<div class="secondary-org accordion">';
    if (!empty($secondary_org['name']))
      $secondary_org_name = $secondary_org['name'];
    if (!empty($secondary_org['description'])) {
      $secondary_org_description = apply_filters('the_content', $secondary_org['description']);
      $output .= '<h3 class="accordion-toggle type-h3"><span>'.$secondary_org_name.'</span></h3>';
      $output .= '<div class="accordion-content user-content">';
      $output .= '<div class="description user-content">'.$secondary_org_description.'</div>';
      if (!empty($secondary_org['website']))
        $secondary_org_website = $secondary_org['website'];
        $output .= '<p class="organization-website"><a class="button" href="'.$secondary_org_website.'" target="_blank">Visit Website</a></p>';
      $output .= '</div>';
    }
    $output .= '</div>';
  }
  $output .= '</div>';
  return $output;
}