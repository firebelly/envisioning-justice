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
$resources->taxonomy('resource-type');
$resources->register();

// Taxonomies
$resource_types = new Taxonomy('resource-type');
$resource_types->register();

// Custom CMB2 fields for post type
function metaboxes( array $meta_boxes ) {
  $prefix = '_cmb2_'; // Start with underscore to hide from custom fields list

  $meta_boxes['resource_metabox'] = array(
    'id'            => 'resource_metabox',
    'title'         => __( 'Resource Info', 'cmb2' ),
    'object_types'  => array( 'resource', ),
    'context'       => 'normal',
    'priority'      => 'low',
    'show_names'    => true,
    'fields'        => array(
      array(
        'name'      => 'Resource Description',
        'id'        => $prefix . 'resource_description',
        'type'      => 'textarea_small',
        'desc'      => 'Displays on resources landing page'
      ),
      array(
        'name'      => 'Resource Address',
        'id'        => $prefix . 'resource_address',
        'type'      => 'address',
      ),
      array(
        'name'      => 'Resource Website',
        'id'        => $prefix . 'resource_website',
        'type'      => 'text_url',
      ),
    ),
  );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

/**
 * Update lookup table for events geodata, if post_id isn't sent, all posts are updates/inserted into wp_events_lat_lng
 */
function update_resource_lat_lng($post_id='') {
  global $wpdb;
  $resource_cache = [];
  $post_id_sql = empty($post_id) ? '' : ' AND post_id='.(int)$post_id;
  $resource_posts = $wpdb->get_results("SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta WHERE meta_key IN ('_cmb2_lat','_cmb2_lng') AND meta_value != '' {$post_id_sql} ORDER BY post_id");
  if ($resource_posts) {
    foreach ($resource_posts as $resource) {
      $resource_cache[$resource->post_id][$resource->meta_key] = $resource->meta_value;
    }
    foreach ($resource_cache as $resource_id=>$arr) {
      $cnt = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM wp_resource_lat_lng WHERE post_id=%d", $resource_id) );
      if ($cnt>0) {
        $wpdb->query( $wpdb->prepare("UPDATE wp_resource_lat_lng SET lat=%s, lng=%s WHERE post_id=%d", $arr['_cmb2_lat'], $arr['_cmb2_lng'], $resource_id) );
      } else {
        $wpdb->query( $wpdb->prepare("INSERT INTO wp_resource_lat_lng (post_id,lat,lng) VALUES (%d,%s,%s)", $resource_id, $arr['_cmb2_lat'], $arr['_cmb2_lng']) );
      }
    }
  }
}

/**
 * Geocode address for resource and save in custom fields
 */
function geocode_address($post_id, $post='') {
  $address = !empty($_POST['_cmb2_resource_address']) ? $_POST['_cmb2_resource_address'] : '';
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
        update_resource_lat_lng($post_id);
    endif;
  endif;
}
add_action('save_post_resource', __NAMESPACE__ . '\\geocode_address', 20, 2);

/**
 * Get Resources
 */
function get_resources($options=[]) {
  if (empty($options['num_posts'])) $options['num_posts'] = -1;
  $args = [
    'numberposts' => $options['num_posts'],
    'post_type'   => 'resource',
  ];
  if (!empty($options['type'])) {
    $args['tax_query'] = [
      [
        'taxonomy' => 'resource type',
        'field' => 'slug',
        'terms' => $options['type']
      ]
    ];
  }

  if (!empty($options['countposts'])) {

    // Just count posts (used for load-more buttons)
    $args ['posts_per_page'] = -1;
    $args ['fields'] = 'ids';
    $count_query = new \WP_Query($args);
    return $count_query->found_posts;

  } else {
    // Display all matching posts using article-{$post_type}.php
    $resource_posts = get_posts($args);
    if (!$resource_posts) return false;
    $output = '';
    foreach ($resource_posts as $resource_post):
      ob_start();
      include(locate_template('templates/article-resource.php'));
      $output .= ob_get_clean();
    endforeach;
    return $output;
  }
}
