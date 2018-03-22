<?php
/**
 * Event post type
 */

namespace Firebelly\PostTypes\Event;
use PostTypes\PostType; // see https://github.com/jjgrainger/PostTypes
use PostTypes\Taxonomy;

$options = [
  'supports'   => ['editor', 'title'],
  'rewrite'    => ['slug' => 'programming'],
];
$events = new PostType('event', $options);
$events->taxonomy('program-type');
$events->register();

// Taxonomies
$event_types = new Taxonomy('program-type');
$event_types->register();

/**
 * Custom admin columns for post type
 */
function edit_columns($columns){
  $columns = array(
    'cb' => '<input type="checkbox" />',
    'title' => 'Title',
    'event_dates' => 'Date',
    '_cmb2_venue' => 'Venue',
  );
  return $columns;
}
add_filter('manage_event_posts_columns', __NAMESPACE__ . '\edit_columns');

function custom_columns($column){
  global $post;
  if ( $post->post_type == 'event' ) {
    $custom = get_post_custom();
    if ( $column == 'featured_image' )
      echo the_post_thumbnail( 'event-thumb' );
    elseif ( $column == 'event_dates' ) {
      $timestamp_start = $custom['_cmb2_event_start'][0];
      $timestamp_end = !empty($custom['_cmb2_event_end'][0]) ? $custom['_cmb2_event_end'][0] : $timestamp_start;
      if ($timestamp_end != $timestamp_start) {
        $date_txt = date('m/d/Y g:iA', $timestamp_start) . ' – ' . date('m/d/Y g:iA', $timestamp_end);
      } else {
        $date_txt = date('m/d/Y g:iA', $timestamp_start);
      }
      echo $date_txt . ($timestamp_end < current_time('timestamp') ? ' - <strong class="post-state">Past Event</strong>' : '');
    } else {
      if (array_key_exists($column, $custom))
        echo $custom[$column][0];
    }
  }
}
add_action('manage_posts_custom_column',  __NAMESPACE__ . '\custom_columns');

/**
 * CMB2 custom fields
 */
function metaboxes( array $meta_boxes ) {
  $prefix = '_cmb2_'; // Start with underscore to hide from custom fields list

  $meta_boxes['event_when'] = array(
    'id'            => 'event_when',
    'title'         => __( 'Event When', 'cmb2' ),
    'object_types'  => array( 'event', ), // Post type
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true, // Show field names on the left
    'fields'        => array(
      array(
          'name'    => 'Start Date',
          'id'      => $prefix . 'event_start',
          'type'    => 'text_datetime_timestamp',
      ),
      array(
          'name'    => 'End Date',
          'id'      => $prefix . 'event_end',
          'type'    => 'text_datetime_timestamp',
      ),
      // array(
      //     'name'    => 'Event Type',
      //     'id'      => $prefix . 'event_type',
      //     'type'    => 'hidden',
      // ),
      array(
          'name'    => 'Days of the Week',
          'desc'    => 'What days of the week does the program meet? Ex: "Mondays, Wednesdays, Fridays". If not multiple days, or if daily, leave blank.',
          'id'      => $prefix . 'days',
          'type'    => 'text',
      ),
     
    ),
  );

  $meta_boxes['event_where'] = array(
    'id'            => 'event_where',
    'title'         => __( 'Event Where', 'cmb2' ),
    'object_types'  => array( 'event', ), // Post type
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true, // Show field names on the left
    'fields'        => array(
      array(
          'name'    => 'Venue',
          'id'      => $prefix . 'venue',
          'type'    => 'text',
      ),
      array(
          'name'    => 'Address',
          'id'      => $prefix . 'address',
          'type'    => 'address',
      ),
    ),
  );

  $meta_boxes['event_details'] = array(
    'id'            => 'event_details',
    'title'         => __( 'Event Details', 'cmb2' ),
    'object_types'  => array( 'event', ), // Post type
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true, // Show field names on the left
    'fields'        => array(
      array(
        'name'    => 'Cost',
        'desc'    => 'Leave blank or set to 0 to show "Free. Open to the public."',
        'id'      => $prefix . 'cost',
        'type'    => 'text',
      ),
      array(
        'name'    => 'Registration URL',
        'desc'    => 'If set, shows "Register for Event" link, and adds "RSVP is required." to the Cost text.',
        'id'      => $prefix . 'registration_url',
        'type'    => 'text_url',
      ),
      array(
        'name'    => 'Registration Embed Code',
        'desc'    => 'If set, same behavior as Registration URL, but shows embedded registration form on Single Event page.',
        'id'      => $prefix . 'registration_embed',
        'type'    => 'textarea',
      ),
      array(
        'name'    => 'Registration Link Text',
        'desc'    => 'The text displayed in the link to the registration URL or embed code. If left blank it will read "Register Now"',
        'id'      => $prefix . 'registration_link_text',
        'type'    => 'text_small',
      ),
      array(
        'name'    => 'RSVP',
        'id'      => $prefix . 'rsvp_text',
        'type'    => 'radio_inline',
        'show_option_none' => true,
        'options' => array(
          'required' => 'Required',
          'recommended' => 'Recommended',
        )
      ),
    ),
  );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

/**
 * Get Events
 */
function get_events($options=[]) {
  if (empty($options['num_posts'])) $options['num_posts'] = get_option('posts_per_page');
  if (empty($options['order'])) $options['order'] = 'ASC';
  if (!empty($_REQUEST['past_events'])) $options['past_events'] = 1;
  // if (!empty($_REQUEST['exhibitions'])) $options['exhibitions'] = 1;
  $args = [
    'numberposts' => $options['num_posts'],
    'post_type' => 'event',
    'meta_key' => '_cmb2_event_start',
    'orderby' => 'meta_value_num',
    'order' => $options['order']
  ];

  $args['meta_query'] = [
    [
      'key' => '_cmb2_event_end',
      'value' => current_time('timestamp'),
      'compare' => (!empty($options['past_events']) ? '<=' : '>')
    ]
  ];

  if (!empty($options['event-type'])) {
    $args['meta_query'][] = array(
      'key' => '_cmb2_event_type',
      'value' => array( (int)$options['event-type'] ),
      'compare' => 'IN',
    );
  }

  if (!empty($options['program-type'])) {
    $args['tax_query'][] = array(
      array(
        'taxonomy' => 'program-type',
        'field'    => 'slug',
        'terms'    => $options['program-type'],
      ),
    );
  }

  if (!empty($options['hub'])) {
    $args['meta_query'][] = array(
      'key' => '_cmb2_related_hub',
      'value' => array( (int)$options['hub'] ),
      'compare' => 'IN',
    );
  }

  // Geo query?
  if (!empty($options['prox_zip']) && is_numeric($options['prox_zip']) && !empty($options['prox_miles'])) {
    $prox_zip = (int)$options['prox_zip'];
    $prox_miles = (int)$options['prox_miles'];
    $close_events = get_event_ids_in_proximity($prox_zip,$prox_miles);
    if ($close_events) {
      $close_event_ids = [];
      foreach($close_events as $close_event) {
        $close_event_ids[] = $close_event->post_id;
      }
      $args['post__in'] = $close_event_ids;
    } else {
      // No posts match within proximity
      $args['post__in'] = [0];
    }
  }


  if (!empty($options['countposts'])) {

    // Just count posts (used for load-more buttons)
    $args ['posts_per_page'] = -1;
    $args ['fields'] = 'ids';
    $count_query = new \WP_Query($args);
    return $count_query->found_posts;

  } else {

    // Display all matching events using article-event.php
    $event_posts = get_posts($args);
    if (!$event_posts) return false;
    $output = '';
    $show_view_all_button = (!empty($options['show_view_all_button']));
    foreach ($event_posts as $event_post):
      if (!empty($options['map-points'])):
        $event = get_event_details($event_post);
        $url = get_permalink($event_post);
        $output .= '<span class="map-point" data-url="' . $url . '" data-lat="' . $event->lat . '" data-lng="' . $event->lng . '" data-title="' . $event->title . '" data-desc="' . $event->desc . '" data-id="' . $event->ID . '"></span>';
      else:
        ob_start();
        $show_images = !empty($options['show_images']);
        $exclude_from_map = !empty($options['exclude_from_map']);
        include(locate_template('templates/article-event.php'));
        $output .= ob_get_clean();
      endif;
    endforeach;
    return $output;
  }
}

function get_event_ids_in_proximity($prox_zip,$prox_miles) {
  global $wpdb;
  $lat_lng = $wpdb->get_row( $wpdb->prepare("SELECT lat,lng FROM wp_zip_lat_lng WHERE zip=%d", $prox_zip) );
  if (!$lat_lng)
    return false;

  $ids = $wpdb->get_results($wpdb->prepare("
    SELECT
      post_id, (
        3959 * acos (
        cos ( radians(%f) )
        * cos( radians( lat ) )
        * cos( radians( lng ) - radians(%f) )
        + sin ( radians(%f) )
        * sin( radians( lat ) )
      )
    ) AS distance
    FROM wp_events_lat_lng
    HAVING distance < %d
    ORDER BY distance;
    ", $lat_lng->lat, $lat_lng->lng, $lat_lng->lat, $prox_miles
  ));
  return $ids;
}

/**
 * Update lookup table for events geodata, if post_id isn't sent, all posts are updates/inserted into wp_events_lat_lng
 */
function update_events_lat_lng($post_id='') {
  global $wpdb;
  $event_cache = [];
  $post_id_sql = empty($post_id) ? '' : ' AND post_id='.(int)$post_id;
  $event_posts = $wpdb->get_results("SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta WHERE meta_key IN ('_cmb2_lat','_cmb2_lng') AND meta_value != '' {$post_id_sql} ORDER BY post_id");
  if ($event_posts) {
    foreach ($event_posts as $event) {
      $event_cache[$event->post_id][$event->meta_key] = $event->meta_value;
    }
    foreach ($event_cache as $event_id=>$arr) {
      $cnt = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM wp_events_lat_lng WHERE post_id=%d", $event_id) );
      if ($cnt>0) {
        $wpdb->query( $wpdb->prepare("UPDATE wp_events_lat_lng SET lat=%s, lng=%s WHERE post_id=%d", $arr['_cmb2_lat'], $arr['_cmb2_lng'], $event_id) );
      } else {
        $wpdb->query( $wpdb->prepare("INSERT INTO wp_events_lat_lng (post_id,lat,lng) VALUES (%d,%s,%s)", $event_id, $arr['_cmb2_lat'], $arr['_cmb2_lng']) );
      }
    }
  }
}

/**
 * Geocode address for event and save in custom fields
 */
function geocode_address($post_id, $post='') {
  $address = !empty($_POST['_cmb2_address']) ? $_POST['_cmb2_address'] : '';
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
        update_events_lat_lng($post_id);
    endif;
  endif;
}
add_action('save_post_event', __NAMESPACE__ . '\\geocode_address', 20, 2);

/**
 * Set whether event-type is 'one-time' or 'ongoing' (one or multiple days) for event filter
 */
// function set_event_type($post_id) {
//   if ($_POST['_cmb2_event_start']['date'] != $_POST['_cmb2_event_end']['date']) {
//     $event_type = 'ongoing';
//   } else {
//     $event_type = 'one-time';
//   }

//   update_post_meta($post_id, '_cmb2_event_type', (string)$event_type);
// }
// add_action('save_post_event', __NAMESPACE__ . '\\set_event_type', 20, 1);

/**
 * Generate an iCalendar .ics file for event
 */
function event_ics() {
  $event_id = preg_replace('/\D/', '', $_REQUEST['id']);
  if (!$event_id) die('No ID sent');

  $event_post = get_post($event_id);
  if (!$event_post) die('No Event found');

  if (!empty($_REQUEST['plaintext'])) { // for debugging
      header('Content-Type: text/plain; charset=utf-8');
  } else {
      header('Content-Type: text/calendar; charset=utf-8');
      header('Content-Disposition: attachment; filename="ihs-event-' . $event_post->post_name . '.ics"');
  }

  $event_start = get_post_meta($event_post->ID, '_cmb2_event_start', true);
  $event_end = get_post_meta( $event_post->ID, '_cmb2_event_end', true);
  $venue = get_post_meta($event_post->ID, '_cmb2_venue', true);
  $start_time = date('g:iA', $event_start);
  // $gmtOffset = 60 * 60 * get_option('gmt_offset');

  $ics = [
    'BEGIN:VCALENDAR',
    'VERSION:2.0',
    'METHOD:PUBLISH',
    'CALSCALE:GREGORIAN',
    'PRODID:-//IHC Events//1.0//EN',
    'BEGIN:VEVENT',
    "UID:event-{$event_id}@" . parse_url(get_option('home'), PHP_URL_HOST),
    'SUMMARY:' . $event_post->post_title,
    'URL:' . get_permalink($event_post->ID),
    'LOCATION:' . $venue,
    'DTSTART:' . get_ical_date($event_start),
    'DTEND:' . (!empty($event_end) ? get_ical_date($event_end) : ''),
    'DTSTAMP:' . get_ical_date(strtotime($event_post->post_modified)),
    'END:VEVENT',
    'END:VCALENDAR',
  ];

  foreach ($ics as $line) {
    echo wordwrap("{$line}\n", 75, "\n\t", TRUE);
  }

  die();
}
add_action('wp_ajax_event_ics', __NAMESPACE__ . '\\event_ics');
add_action('wp_ajax_nopriv_event_ics', __NAMESPACE__ . '\\event_ics');

function get_ical_date($time, $incl_time=true){
  return $incl_time ? date('Ymd\THis', $time) : date('Ymd', $time);
}

// custom URLs like /events/2014/11/the-event-name
// function event_rewrite_tag() {
//   global $wp_rewrite;
//   $event_structure = '/events/%year%/%monthnum%/%postname%';
//   $wp_rewrite->add_rewrite_tag("%event%", '([^/]+)', "event=");
//   $wp_rewrite->add_permastruct('event', $event_structure, true);
// }
// add_action('init', __NAMESPACE__ . '\\event_rewrite_tag', 10, 0);

// add_filter('post_type_link', __NAMESPACE__ . '\\event_permalink', 10, 3);
// // Adapted from get_permalink function in wp-includes/link-template.php
// function event_permalink($permalink, $post_id, $leavename) {
//     $post = get_post($post_id);
//     $rewritecode = array(
//         '%year%',
//         '%monthnum%',
//         '%day%',
//         $leavename? '' : '%postname%',
//         '%post_id%',
//     );

//     if ( '' != $permalink && !in_array($post->post_status, array('draft', 'pending', 'auto-draft')) ) {
//         $unixtime = strtotime($post->post_date);

//         $date = explode(" ",date('Y m d H i s', $unixtime));
//         $rewritereplace =
//         array(
//             $date[0],
//             $date[1],
//             $date[2],
//             $post->post_name,
//             $post->ID,
//         );
//         $permalink = str_replace($rewritecode, $rewritereplace, $permalink);
//     } else { // if they're not using the fancy permalink option
//     }
//     return $permalink;
// }

/**
 * Add query vars for events
 */
function add_query_vars_filter($vars){
  $vars[] = "filter_event_type";
  $vars[] = "filter_order";
  $vars[] = "filter_program_type";
  $vars[] = "filter_related_hub";
  return $vars;
}
add_filter( 'query_vars', __NAMESPACE__ . '\\add_query_vars_filter' );

/**
 * Helper function to populate event object for listings & single view
 */
function get_event_details($post) {
  $event = [
    'ID' => $post->ID,
    'title' => $post->post_title,
    'body' => apply_filters('the_content', $post->post_content),
    'event_start' => get_post_meta($post->ID, '_cmb2_event_start', true),
    'event_end' => get_post_meta( $post->ID, '_cmb2_event_end', true),
    'venue' => get_post_meta($post->ID, '_cmb2_venue', true),
    'sponsor' => get_post_meta($post->ID, '_cmb2_sponsor', true),
    'funder' => get_post_meta($post->ID, '_cmb2_funder', true),
    'partner' => get_post_meta($post->ID, '_cmb2_partner', true),
    'cost' => get_post_meta($post->ID, '_cmb2_cost', true),
    'registration_url' => get_post_meta($post->ID, '_cmb2_registration_url', true),
    'rsvp_text' => get_post_meta($post->ID, '_cmb2_rsvp_text', true),
    'registration_embed' => get_post_meta($post->ID, '_cmb2_registration_embed', true),
    'registration_link_text' => get_post_meta($post->ID, '_cmb2_registration_link_text', true),
    'lat' => get_post_meta($post->ID, '_cmb2_lat', true),
    'lng' => get_post_meta($post->ID, '_cmb2_lng', true),
    'add_to_calendar_url' => admin_url('admin-ajax.php') . "?action=event_ics&amp;id={$post->ID}&amp;nc=" . current_time('timestamp'),
    'event_type' => get_post_meta($post->ID, '_cmb2_event_type', true),
  ];
  // Is this event multiple days?
  $event['multiple_days'] = (date('Y-m-d', $event['event_start']) != date('Y-m-d', $event['event_end']));
  $event['start_time'] = date('g:iA', $event['event_start']);
  $event['end_time'] = date('g:iA', $event['event_end']);
  if ($event['start_time'] != $event['end_time']) {
    $event['time_txt'] = $event['start_time'] . '–' . $event['end_time'];
  } else {
    $event['time_txt'] = $event['start_time'];
  }
  $event['days'] = get_post_meta($post->ID, '_cmb2_days', true);
  
  $event['archived'] = empty($event['event_end']) ? ($event['event_start'] < current_time('timestamp')) : ($event['event_end'] < current_time('timestamp'));
  $event['desc'] = date('M d, Y @ ', $event['event_start']) . $event['time_txt']; // used in map pins
  $event['year'] = date('Y', $event['event_start']);

  $address = get_post_meta($post->ID, '_cmb2_address', true);
  $event['address'] = wp_parse_args($address, array(
      'address-1' => '',
      'address-2' => '',
      'city'      => '',
      'state'     => '',
      'zip'       => '',
   ));
  return (object)$event;
}

/**
 * Alter WP query for Event archive pages
 * if "past_events" is set, only shows archived events
 */

/*
 * currently site is just using get_events() in event-post-type.php

function event_query($query){
  global $wp_the_query;
  if ($wp_the_query === $query && !is_admin() && is_post_type_archive('event')) {
    $meta_query = array(
      array(
        'key' => '_cmb2_event_end',
        'value' => current_time('timestamp'),
        'compare' => (get_query_var('past_events') ? '<=' : '>')
      )
    );
    $query->set('meta_query', $meta_query);
    $query->set('orderby', 'meta_value_num');
    $query->set('meta_key', '_cmb2_event_end');
    // show events oldest->newest
    $query->set('order', (get_query_var('past_events') ? 'DESC' : 'ASC'));

    // focus area?
    if (get_query_var('event_focus_area')) {
      $tax_query = array(
        array(
          'taxonomy' => 'focus_area',
          'field' => 'id',
          'terms' => get_query_var('event_focus_area')
        )
      );
      $query->set('tax_query', $tax_query);
    }
  }
}
add_action('pre_get_posts', __NAMESPACE__ . '\\event_query');
*/

/**
 * Handle AJAX response from CSV import form
 */
add_action( 'wp_ajax_event_csv_upload', __NAMESPACE__ . '\event_csv_upload' );
function event_csv_upload() {
  global $wpdb;
  require_once 'import/event-csv-importer.php';

  $importer = new \EventCSVImporter;
  $return = $importer->handle_post();

  // Spits out json-encoded $return & die()s
  wp_send_json($return);
}

/**
 * Show link to CSV Import page
 */
add_action('admin_menu', __NAMESPACE__ . '\import_csv_admin_menu');
function import_csv_admin_menu() {
  add_submenu_page('edit.php?post_type=event', 'Import CSV', 'Import CSV', 'manage_options', 'events-csv-importer', __NAMESPACE__ . '\import_csv_admin_form');
}

/**
 * Basic CSV Importer admin page
 */
function import_csv_admin_form() {
?>
  <div class="wrap">
    <h2>Import CSV</h2>
    <form method="post" id="csv-upload-form" enctype="multipart/form-data" action="">
      <fieldset>
        <label for="csv_import">Upload file(s):</label>
        <input name="csv_import[]" id="csv-import" type="file" multiple>
        <div id="filedrag">or drop files here</div>
      </fieldset>
      <div class="progress-bar"><div class="progress-done"></div></div>
      <input type="hidden" name="action" value="event_csv_upload">
      <p class="submit"><input type="submit" class="button" id="csv-submit" name="submit" value="Import"></p>
    </form>

   <div class="import-notes">
      <h3>Uses these fields from Raiser's Edge export:</h3>
<pre>
Ev_Type                         (Focus Area)
Ev_Group                        (Related Program)
Ev_Start_Date                  
Ev_End_Date                    
Ev_Start_Time                  
Ev_End_Time                    
Ev_Event_ID                    
Ev_Import_ID                   
Attendee Cost                   (Cost)
Attendee Cost Details           (Cost details)
RSVP                            (yes/no)
Required/Recommended            (required/recommended)
Web Title                       (Title)
Web Description                 (Body)
RSVP URL                        (Registration URL)
RSVP Embed                      (Reg. embed code)
Location                        
Ev_Prt_1_01_CnAdrPrf_Addrline1 
Ev_Prt_1_01_CnAdrPrf_Addrline2 
Ev_Prt_1_01_CnAdrPrf_City      
Ev_Prt_1_01_CnAdrPrf_State     
Ev_Prt_1_01_CnAdrPrf_ZIP       
Ev_Prt_1_01_CnAdrPrf_County                           
</pre>
  </div>
<?php
}