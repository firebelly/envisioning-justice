<?php 
// Update IHC posts with new published_date

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
];

$posts = get_posts($args);
foreach ($posts as $post) {
  $publication_date = get_post_meta($post->ID, '_cmb2_publication_date', true);
  if ($publication_date) {
    $post_date = date('Y-m-d H:i:s', strtotime($publication_date));
    wp_update_post([
      'ID'            => $post->ID,
      'post_date'     => $post_date,
      'post_date_gmt' => $post_date,
    ]);
    echo "    wp_update_post([<br>
      'ID'            => {$post->ID},<br>
      'post_date'     => {$post_date},<br>
      'post_date_gmt' => {$post_date},<br>
    ]);<br><br>";
  } else {
    echo "<strong>Post #".$post->ID.' OK!</strong><br>';
  }
}
