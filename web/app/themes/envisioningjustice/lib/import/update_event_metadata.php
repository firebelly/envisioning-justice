<?php 
// Update IHC events

// Bootstrap WP
define('BASE_PATH', dirname(__FILE__).'/../../../../../wp/');
define('WP_USE_THEMES', false);
require_once(BASE_PATH . 'wp-load.php');
require_once(BASE_PATH . 'wp-admin/includes/image.php');
require_once('migrate_func.php');
global $wpdb;

if (!is_user_logged_in())
  die('You must be logged in to import.');

$args = [
  'numberposts' => -1,
  'offset' => 0,
  'post_type' => 'event',
];

$event_posts = get_posts($args);
foreach ($event_posts as $post) {
  $start = get_post_meta($post->ID, '_cmb2_event_start', true);
  $end = get_post_meta($post->ID, '_cmb2_event_end', true);
  if (!$end && $start) {
    echo "update_post_meta({$post->ID}, '_cmb2_event_end', $start);<br>";
    update_post_meta($post->ID, '_cmb2_event_end', $start);
  } else {
    echo "Post #".$post->ID.' OK!<br>';
  }
}

