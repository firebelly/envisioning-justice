<?php 
/**
 * Misc functions for imports
 */

/**
 * Replace dumb drupal img_assist shorttags
 * e.g.
 * [img_assist|nid=22074|title=|desc=|link=none|align=right|width=175|height=236]
 * [img_assist|nid=29261|title=|desc=Photo by Victor G. Jeffreys II|link=none|align=left|width=197|height=250]
 * [img_assist|nid=29349|title=|desc=|link=url|url=http://chiwrimo.org/|align=center|width=100|height=100]   
 */
function replace_dumb_drupal_img_tags($body) {
  $body_with_new_images = preg_replace_callback('/\[img_assist\|nid=(\d+)\|title=([^|]+)?\|desc=([^|]+)?\|link=([^|]+)(\|url=([^|]+))?\|align=([^|]+)\|width=([^|]+)\|height=([^\]]+)\]/', 'replace_dumb_drupal_img_tags_callback', $body);
  return $body_with_new_images;
}
function replace_dumb_drupal_img_tags_callback($matches) {
  $nid = $matches[1];
  // get image info from Drupal & import to wordpress, returns new image URL
  $new_img = get_drupal_image($nid);
  // align image?
  $alignclass = !empty($matches[7]) ? ' align' . $matches[7] : '';
  $img_tag = '<img alt="'.esc_html($matches[3]).'" src="' . $new_img . '" class="size-full' . $alignclass . '" width="' . $matches[8] . '" height="' . $matches[9] . '">';
  // is there a link URL?
  if (!empty($matches[6])) {
    $return = '<a target="_blank" href="' . $matches[6] . '">' . $img_tag . '</a>';
  } else {
    $return = $img_tag;
  }
  echo '<h3>[img_assist] found</h3><pre>'.htmlentities($return).'</pre>';
  return $return;
}

// ihc_files.nid 
// ihc_files.filename = '_original' 
// ihc_files.filepath = 'files/ihc/images/Rahm-Chuy-Debate.jpg'
// http://www.prairie.org/files/ihc/images/Rahm-Chuy-Debate.jpg

/**
 * get old drupal image from ihc_node & ihc_files
 */
function get_drupal_image($nid, $featured='') {
  global $drupal_db, $post_id;

  $node_sql = $drupal_db->prepare("SELECT * FROM ihc_node WHERE nid=?");
  $node_sql->execute([ $nid ]);
  $node_row = $node_sql->fetch();
  $title = $node_row['title'];

  $image_sql = $drupal_db->prepare("SELECT * FROM ihc_files WHERE nid=? AND filename='_original'");
  $image_sql->execute([ $nid ]);
  $image_row = $image_sql->fetch();
  // import external image to wordpress and return new URL
  $new_img = import_image_to_wordpress('http://www.prairie.org/' . $image_row['filepath'], $post_id, $title, $featured);
  return $new_img;
}

/**
 * Import an external image into wordpress media library
 */
function import_image_to_wordpress($file_url, $post_id, $title='', $featured='') {
  global $wpdb;
  $file_url = str_replace(' ', '%20', $file_url);
  $site_url = get_option('home');

  if(!$post_id) {
    return false;
  }

  // Directory to import to 
  $import_dir = '/app/uploads/migrated_media/';

  // If the directory doesn't exist, create it  
  if(!file_exists(dirname(__FILE__) . $import_dir)) {
    mkdir(dirname(__FILE__) . $import_dir);
  }

  // Get filename from url
  $tmp_arr = explode('/', $file_url);
  $new_filename = preg_replace('/\-(jpg|gif|png|JPG|GIF|PNG)$/',".$1",slugify(array_pop($tmp_arr)));
  $new_file_url = $site_url . $import_dir . $new_filename;

  // Check if image is already imported! (ARGH)
  $existing_image = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_value = %s AND meta_key = %s ORDER BY post_id DESC", 'migrated_media/'.$new_filename, '_wp_attached_file'));
  if ($existing_image != '') {

    echo '<h3>Existing image found! ' . $new_file_url . '<h3>';
    return $new_file_url;

  } else {

    if (@fclose(@fopen($file_url, 'r'))) { // Make sure the remote file actually exists
      copy($file_url, dirname(__FILE__) . $import_dir . $new_filename);

      $file_info = getimagesize(dirname(__FILE__) . $import_dir . $new_filename);
      $file_title = (!empty($title)) ? $title : $new_filename;

      // Create an array of attachment data to insert into wp_posts table
      $image_data = [
        'post_author' => 1, 
        'post_date' => current_time('mysql'),
        'post_date_gmt' => current_time('mysql'),
        'post_title' => $file_title, 
        'post_status' => 'inherit',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_name' => sanitize_title_with_dashes(str_replace('_', '-', $new_filename), '', 'save'),
        'post_modified' => current_time('mysql'),
        'post_modified_gmt' => current_time('mysql'),
        'post_parent' => $post_id,
        'post_type' => 'attachment',
        'guid' => $new_file_url,
        'post_mime_type' => $file_info['mime'],
        'post_excerpt' => '',
        'post_content' => ''
      ];

      $uploads = wp_upload_dir();
      $save_path = $uploads['basedir'] . '/migrated_media/' . $new_filename;

      // Insert the database record
      $attach_id = wp_insert_attachment($image_data, $save_path, $post_id);

      // Generate metadata and thumbnails
      if ($attach_data = wp_generate_attachment_metadata($attach_id, $save_path)) {
        wp_update_attachment_metadata($attach_id, $attach_data);
      }

      // Make it the featured image of the post it's attached to?
      if ($featured) {
        update_post_meta($post_id, '_thumbnail_id', $attach_id);
      }

    } else {
      echo '<h3>File '.$file_url.' not found</h3>';
      return false;
    }
  
  }
  return $new_file_url;
}

function slugify($text) {
  // replace non letter or digits by -
  $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

  // trim
  $text = trim($text, '-');

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // lowercase
  $text = strtolower($text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}