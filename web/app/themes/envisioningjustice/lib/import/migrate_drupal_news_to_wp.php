<?php 
// Import IHC news from Drupal 5

// Bootstrap WP
define('BASE_PATH', dirname(__FILE__).'/../../../../../wp/');
define('WP_USE_THEMES', false);
require_once(BASE_PATH . 'wp-load.php');
require_once(BASE_PATH . 'wp-admin/includes/image.php');
require_once('migrate_func.php');
global $wpdb;

if (!is_user_logged_in())
  die('You must be logged in to import.');

// Map drupal categories to new wp
$category_conversions = [
  'IHC in the News' => 'In The News',
  'Press Release'   => 'Press Release'
];

// Connect to old Drupal db
$drupal_db = new PDO('mysql:host=127.0.0.1;dbname=ihc_old;charset=utf8', 'root', 'root', array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$prefix = '_cmb2_';
$start = !empty($_GET['start']) ? $_GET['start'] : '0';
$num = !empty($_GET['num']) ? $_GET['num'] : '10';
$next_url = '/migrate_drupal_news_to_wp.php?start='.($start+$num).'&num='.$num;

// Timestamp was 6 hours ahead on drupal site
$time_offset = -(6 * 3600);

$stmt = $drupal_db->prepare("SELECT * FROM ihc_node WHERE type=? AND status=? ORDER BY nid DESC LIMIT ?,?");
$stmt->execute(['news', 1, $start, $num]);
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
echo '<h1>Importing News '.$start.'-'.($start+$num).'</h1>';
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
    // Get node category
    // field_news_category_value = IHC in the News, Press Release, Awards Received, Awards Given, 
    $category_sql = $drupal_db->prepare("SELECT * FROM ihc_content_field_news_category WHERE nid=?");
    $category_sql->execute([ $node['nid'] ]);
    $category_row = $category_sql->fetch();
    $category = $category_row['field_news_category_value'];

    // Get node body & teaser
    $body_sql = $drupal_db->prepare("SELECT body,teaser FROM ihc_node_revisions WHERE nid=? ORDER BY timestamp DESC LIMIT 1");
    $body_sql->execute([ $node['nid'] ]);
    $body_row = $body_sql->fetch();
    $body = $body_row['body'];
    // $teaser = $body_row['teaser'];

    // Insert basic data into WP
    $post_id = wp_insert_post([
      'post_status' => 'publish',
      'post_type' => 'post',
      'post_author' => 1,
      'post_content' => $body,
      // 'post_excerpt' => $teaser,
      'post_title' => $node['title'],
      'post_date' => date('Y-m-d H:i:s', $node['created']),
    ]);

    if($post_id) {
      echo '<h2>Post inserted ok: <a href="/wp/wp-admin/post.php?post='.$post_id.'&action=edit">'.$post_id.'</a> &nbsp; <small><a href="http://www.prairie.org/node/'.$node['nid'].'/edit">Old</a></small></h2>';

      // Store nid to avoid duplicate imports
      update_post_meta($post_id, '_nid', $node['nid']);

      // Get publication date field from this ridiculous table (oh, Drupal)
      echo '<p>SELECT * FROM ihc_content_type_news WHERE nid='.$node['nid'].' AND vid='.$node['vid'].'</p>';
      $news_sql = $drupal_db->prepare("SELECT * FROM ihc_content_type_news WHERE nid=? AND vid=?");
      $news_sql->execute([ $node['nid'], $node['vid'] ]);
      $news_row = $news_sql->fetch();
      // print_r($news_row);
      $publication_date_value = $news_row['field_news_publication_date_value'];
      if ($publication_date_value) {
        $post_date = date('Y-m-d H:i:s', strtotime($publication_date_value));
        wp_update_post([
          'ID'            => $post_id,
          'post_date'     => $post_date,
          'post_date_gmt' => $post_date,
        ]);
      }

      // Featured image?
      if (!empty($news_row['field_news_photo_fid']) && $news_row['field_news_photo_fid']>0) {
        echo '<h3>featured image: ' . $news_row['field_news_photo_fid'] . '</h3>';
        $image_sql = $drupal_db->prepare("SELECT * FROM ihc_files WHERE fid=?");
        $image_sql->execute([ $news_row['field_news_photo_fid'] ]);
        $image_row = $image_sql->fetch();
        // print_r($image_row);
        if ($image_row) {
          // import external image to wordpress and return new URL
          $new_img = import_image_to_wordpress('http://www.prairie.org/' . $image_row['filepath'], $post_id, $image_row['filename'], 1);
        }
      }

      // Import + replace Drupal shortcode images
      $body_with_new_images = replace_dumb_drupal_img_tags($body);

      // Were there any images? Update post_content if so
      if ($body_with_new_images != $body) { // || $teaser_with_new_images != $teaser
        wp_update_post([
          'ID'           => $post_id,
          // 'post_excerpt' => $teaser_with_new_images,
          'post_content' => $body_with_new_images,
        ]);
      }
      
      // Set category if key exists in conversion table
      if (array_key_exists($category, $category_conversions)) {
        wp_set_object_terms($post_id, $category_conversions[$category], 'category');
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
