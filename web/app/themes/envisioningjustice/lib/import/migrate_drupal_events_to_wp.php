<?php 
// Import IHC events from Drupal 5

// Bootstrap WP
define('BASE_PATH', dirname(__FILE__).'/../../../../../wp/');
define('WP_USE_THEMES', false);
require_once(BASE_PATH . 'wp-load.php');
require_once(BASE_PATH . 'wp-admin/includes/image.php');
require_once('migrate_func.php');
global $wpdb;

if (!is_user_logged_in())
  die('You must be logged in to import.');

// Connect to old Drupal db
$drupal_db = new PDO('mysql:host=127.0.0.1;dbname=ihc_old;charset=utf8', 'root', 'root', array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$prefix = '_cmb2_';
$start = !empty($_GET['start']) ? $_GET['start'] : '0';
$num = !empty($_GET['num']) ? $_GET['num'] : '100';
$next_url = '/migrate_drupal_events_to_wp.php?start='.($start+$num).'&num='.$num;

// timestamp was 6 hours ahead on drupal site
$time_offset = -(6 * 3600);

$stmt = $drupal_db->prepare("SELECT * FROM ihc_node WHERE type=? AND status=? ORDER BY nid DESC LIMIT ?,?");
$stmt->execute(['event', 1, $start, $num]);
$nodes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Import-O-Matic (<?= $start.'-'.($start+$num) ?>)</title>
</head>
<body>

<?php
echo '<h1>Importing Events '.$start.'-'.($start+$num).'</h1>';
echo '<p>Next: <a href="'.$next_url.'">'.$next_url.'</a></p>';
echo '<p>AutoNext: <a href="'.$next_url.'&autostart=1">'.$next_url.'</a></p>';
echo '<hr>';

if (count($nodes)==0) die('No more posts found.');

foreach($nodes as $node) {
  // print_r($node);

  // Check if post already imported
  $imported = $wpdb->get_var( "SELECT COUNT(*) FROM wp_postmeta WHERE meta_key='_nid' AND meta_value=".$node['nid'] );

  if ($imported) {
    echo '<h2>Post already imported!</h2>';
  } else {
    // Get node body
    $body_sql = $drupal_db->prepare("SELECT body FROM ihc_node_revisions WHERE nid=? ORDER BY timestamp DESC LIMIT 1");
    $body_sql->execute([ $node['nid'] ]);
    $body_row = $body_sql->fetch();
    $body = $body_row['body'];

    echo '<p>SELECT * FROM ihc_content_type_event WHERE nid='.$node['nid'].' ORDER BY vid DESC LIMIT 1</p>';

    // Get basic event data
    $event_sql = $drupal_db->prepare("SELECT * FROM ihc_content_type_event WHERE nid=? AND vid=?");
    $event_sql->execute([ $node['nid'], $node['vid'] ]);
    $event_row = $event_sql->fetch();
    // print_r($event_row);
    $sponsor = $event_row['field_event_site_org_value'];
    $cost = $event_row['field_event_fee_value'];
    $county = $event_row['field_event_county_value'];
    $registration_url = $event_row['field_event_reg_url_url'];

    // Append scheduling notes to body field (not sure why this was a separate field)
    $body .= "\n\n" . $event_row['field_event_scheduling_notes_value'];

    // Insert basic data into WP as post_type=event
    $post_id = wp_insert_post([
      'post_status' => 'publish',
      'post_type' => 'event',
      'post_author' => 1,
      'post_content' => $body,
      'post_title' => $node['title'],
      'post_date' => date('Y-m-d H:i:s', $node['created']),
    ]);

    if($post_id) {
      echo '<h2>Post inserted ok: <a href="/wp/wp-admin/post.php?post='.$post_id.'&action=edit">'.$post_id.'</a> <small><a href="http://www.prairie.org/node/'.$node['nid'].'/edit">Old</a></small></h2>';
      // Store nid to avoid duplicate imports
      update_post_meta($post_id, '_nid', $node['nid']);
      
      // Set various fields from ihc_content_type_event (set above) if not blank
      if ($sponsor)
        update_post_meta($post_id, $prefix.'sponsor', $sponsor);
      if ($cost)
        update_post_meta($post_id, $prefix.'cost', $cost);
      if ($county)
        update_post_meta($post_id, $prefix.'county', $county);
      if ($registration_url)
        update_post_meta($post_id, $prefix.'registration_url', $registration_url);

      // Import + replace Drupal shortcode images
      $body_with_new_images = replace_dumb_drupal_img_tags($body);

      // Were there any images? Update post_content if so
      if ($body_with_new_images != $body) {
        wp_update_post([
          'ID'           => $post_id,
          'post_content' => $body_with_new_images,
        ]);
      }

      try {
        // Get event timestamps
        $times_sql = $drupal_db->prepare("SELECT event_start,event_end FROM ihc_event WHERE nid=?");
        $times_sql->execute([ $node['nid'] ]);
        $times_row = $times_sql->fetch();

        update_post_meta($post_id, $prefix.'event_start', $times_row['event_start'] + $time_offset);
        if ($times_row['event_end'] != $times_row['event_start']) {
          update_post_meta($post_id, $prefix.'event_end', $times_row['event_end'] + $time_offset);
        }
        echo '<h3>Timestamps added ok</h3>';
      } catch(PDOException $ex) {
        echo "Unable to get timestamps: " . $ex->getMessage();
      }

      try {
        echo '<p>SELECT * FROM ihc_location WHERE eid='.$node['vid'].'</p>';
        // get event location
        $location_sql = $drupal_db->prepare("SELECT * FROM ihc_location WHERE eid=?");
        $location_sql->execute([ $node['vid'] ]);
        $location_row = $location_sql->fetch();

        update_post_meta($post_id, $prefix.'venue', $location_row['name']);
        $address = [
          'address-1' => $location_row['street'],
          'address-2' => $location_row['additional'],
          'city' => $location_row['city'],
          'state' => $location_row['province'],
          'zip' => $location_row['postal_code'],
        ];
        // print_r($address);
        update_post_meta($post_id, $prefix.'address', $address);
        update_post_meta($post_id, $prefix.'lat', $location_row['latitude']);
        update_post_meta($post_id, $prefix.'lng', $location_row['longitude']);
        echo '<h3>Location added ok</h3>';
      } catch(PDOException $ex) {
        echo "Unable to get location: " . $ex->getMessage();
      }
    } // if post inserted ok
  } // if post already imported
} // foreach nodes

?>

<?php if (!empty($_GET['autostart']) && count($nodes)>0): ?>
  <script>
  setTimeout(function() {
    location.href = "<?= $next_url ?>&autostart=1";
  }, 1000);
  </script>
<?php endif; ?>

</body></html>