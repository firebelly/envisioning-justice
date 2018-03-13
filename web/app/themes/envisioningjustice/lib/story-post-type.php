<?php
/**
 * Story post type
 */

namespace Firebelly\PostTypes\Story;
use PostTypes\PostType; // see https://github.com/jjgrainger/PostTypes

$options = [
  'supports'   => ['editor', 'title'],
  'rewrite'    => ['with_front' => false],
  'menu_icon'  => 'dashicons-microphone',
];
$stories = new PostType(['name'=>'story','plural'=>'Stories'], $options);
$stories->register();

/**
 * CMB2 custom fields
 */
function metaboxes(array $meta_boxes) {
  $prefix = '_cmb2_'; // Start with underscore to hide from custom fields list

  $meta_boxes['story_metabox'] = array(
    'id'            => 'story_metabox',
    'title'         => __( 'Extra Fields', 'cmb2' ),
    'object_types'  => array( 'story', ), // Post type
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true, // Show field names on the left
    'fields'        => array(
      array(
          'name'    => 'Author or Story Title',
          // 'desc'    => 'field description (optional)',
          'id'      => $prefix . 'story_name',
          'type'    => 'text',
      ),
      array(
          'name'    => 'Author email',
          // 'desc'    => 'field description (optional)',
          'id'      => $prefix . 'story_email',
          'type'    => 'text_email',
      ),
      array(
          'name'    => 'Uploaded Images',
          'desc'    => 'Images will be displayed on the individual story page, text documents will be ignored.',
          'id'      => $prefix . 'slideshow-images',
          'type'    => 'file_list',

      ),
    ),
  );

  return $meta_boxes;
}
add_filter('cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes');

/**
 * Outputs a "Submit A Story" submit form
 */
function submit_form() {
?>
  <form action="<?= admin_url('admin-ajax.php') ?>" class="new-story-form" method="post" enctype="multipart/form-data" novalidate>
    <div class="input-wrap">
      <label for="story_name">Name or Story Title</label>
      <input type="text" name="story_name" id="story_name" required>
    </div>
    <div class="input-wrap">
      <label for="story_email">Email</label>
      <input type="email" name="story_email" id="story_email" required>
    </div>
    <div class="input-wrap textarea-wrap">
      <label for="story_content">Your Story</label>
      <textarea name="story_content" required></textarea>
    </div>
    <div class="input-wrap file-input-wrap">
      <label for="story_images" class="attach-files-label">Story Images and/or Files (optional)</label>
      <div class="files-attached"></div>
      <input type="file" id="story_images" name="story_images[]" multiple data-content="Click to add file(s)">
    </div>
    <?php wp_nonce_field( 'story_form', 'story_form_nonce' ); ?>
    <!-- die bots --><div style="position: absolute; left: -5000px;"><input type="text" name="die_bots_5000" tabindex="-1" value=""></div>
    <input type="hidden" name="action" value="story_submission">
    <div class="actions">
      <button type="submit" class="button submit">Submit</button>
    </div>
  </form>
<?php
}

/**
 * Handle a Story submission
 */
function new_story() {
  $errors = [];
  $attachments = $attachments_size = [];
  $notification_email = true;
  $name = $_POST['story_name'];
  $story = $_POST['story_content'];

  $story_post = array(
    'post_title'    => $name,
    'post_type'     => 'story',
    'post_content'  => $story,
    'post_author'   => 1,
    'post_status'   => 'draft',
  );
  $post_id = wp_insert_post($story_post);
  if ($post_id) {

    update_post_meta($post_id, '_cmb2_story_name', $_POST['story_name']);
    update_post_meta($post_id, '_cmb2_story_email', $_POST['story_email']);
    update_post_meta($post_id, '_cmb2_story_content', $_POST['story_content']);

    if (!empty($_FILES['story_images'])) {
      require_once(ABSPATH . 'wp-admin/includes/image.php');
      require_once(ABSPATH . 'wp-admin/includes/file.php');
      require_once(ABSPATH . 'wp-admin/includes/media.php');

      $files = $_FILES['story_images'];
      foreach ($files['name'] as $key => $value) {
        if ($files['name'][$key]) {
          $file = array(
            'name' => $files['name'][$key],
            'type' => $files['type'][$key],
            'tmp_name' => $files['tmp_name'][$key],
            'error' => $files['error'][$key],
            'size' => $files['size'][$key]
          );
          $_FILES = array('story_images' => $file);
          $attachment_id = media_handle_upload('story_images', $post_id);

          if (is_wp_error($attachment_id)) {
            $errors[] = 'There was an error uploading '.$file['name'];
          } else {
            $attachment_url = wp_get_attachment_url($attachment_id);
            $attachments[$attachment_id] = $attachment_url;
            $attachments_size[$attachment_id] = $files['size'][$key];
            // if Enhanced Media Library is installed, set category
            if (function_exists('wpuxss_eml_enqueue_media')) {
              wp_set_object_terms($attachment_id, 'stories', 'media_category');
            }
          }
        }
      }
      if (!empty($attachments)) {
        update_post_meta($post_id, '_cmb2_slideshow-images', $attachments);
      }
    }

    // Pull notification email from site_options
    $notification_email = \Firebelly\SiteOptions\get_option('story_submission_email');
    $subject = 'New story submission from ' . $_POST['story_name'];
    $message = "A new story submission was received:\n\n";

    // Send email if notification_email was set for position or in site_options for internships/portfolio
    if ($notification_email) {
      $headers  = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
      $headers .= ['From: Envisioning Justice <www-data@envisioningjustice.com>'];
      $message = '<html><body>';
      $message .= '<h3>Story Name: ' . $_POST['story_name'] . "</h3>";
      $message .= '<h3>Email: ' . $_POST['story_email'] . "</h3>";
      $message .= '<h3>Story: ' . $_POST['story_content'] . "</h3>";
      $message .= "<p>Edit in WordPress:<br>" . admin_url('post.php?post='.$post_id.'&action=edit') . "</p>";
      $message .= '</body></html>';
      if (!empty($attachments)) {
        $message .= "<p>Files uploaded:</p>";
        foreach ($attachments as $attachment_id => $attachment_url) {
          // Add home_url (if not there) to make these links
          if (strpos($attachment_url, get_home_url())===false) {
            $attachment_url = get_home_url().$attachment_url;
          }
          $message .= "<p>" . $attachment_url . "</p>";
        }
      }
      wp_mail($notification_email, $subject, $message, $headers);
    }

    // Send quick receipt email to applicant
    $user_headers  = 'MIME-Version: 1.0' . "\r\n";
    $user_headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $user_headers .= ['From: Envisioning Justice <www-data@envisioningjustice.com>'];
    $user_message = '<html><body>';
    if (!empty(\Firebelly\SiteOptions\get_option('story_submission_email_message'))) {
      $user_message .= \Firebelly\SiteOptions\get_option('story_submission_email_message');
    } else {
      $user_message .= "<p>Thank you for sharing your story with us.</p>";
      $user_message .= "<p>Best Regards,<br>Illinois Humanities</p>";
    }
    $user_message .= '<br><br>';
    $user_message .= '<h3>Story Name: ' . $_POST['story_name'] . "</h3>";
    $user_message .= '<h3>Story: ' . $_POST['story_content'] . "</h3>";
    if (!empty($attachments)) {
      $user_message .= "<p>Files uploaded:</p>";
      foreach ($attachments as $attachment_id => $attachment_url) {
        // Add home_url (if not there) to make these links
        if (strpos($attachment_url, get_home_url())===false) {
          $attachment_url = get_home_url().$attachment_url;
        }
        $user_message .= "<p>" . $attachment_url . "</p>";
      }
    }
    $user_message .= '</body></html>';

    wp_mail($_POST['story_email'], 'Thank you for sharing your story', $user_message, $user_headers);

  } else {
    $errors[] = 'Error inserting post';
  }

  if (empty($errors)) {
    return true;
  } else {
    return $errors;
  }
}


/**
 * AJAX Story submissions
 */
function story_submission() {
  if($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['story_form_nonce'])) {
    if (wp_verify_nonce($_POST['story_form_nonce'], 'story_form')) {

      // Server side validation of required fields
      $required_fields = ['story_name',
                          'story_email'];
      foreach($required_fields as $required) {
        if (empty($_POST[$required])) {
          $required_txt = ucwords(str_replace('_', ' ', str_replace('story_','',$required)));
          wp_send_json_error(['message' => 'Please enter a value for '.$required_txt]);
        }
      }

      // Check for valid Email
      if (!is_email($_POST['story_email'])) {
        wp_send_json_error(['message' => 'Invalid email']);
      } else {

        // Try to save new Story post
        $return = new_story();
        if (is_array($return)) {
          wp_send_json_error(['message' => 'There was an error: '.implode("\n", $return)]);
        } else {
          wp_send_json_success(['message' => 'Story was saved OK']);
        }

      }
    } else {
      // Bad nonce, man!
      wp_send_json_error(['message' => 'Invalid form submission (bad nonce)']);
    }
  }
  wp_send_json_error(['message' => 'Invalid post']);
}
add_action('wp_ajax_story_submission', __NAMESPACE__ . '\\story_submission');
add_action('wp_ajax_nopriv_story_submission', __NAMESPACE__ . '\\story_submission');

/**
 * Handle AJAX response from CSV import form
 */
add_action('wp_ajax_story_csv_upload', __NAMESPACE__ . '\story_csv_upload');
function story_csv_upload() {
  global $wpdb;
  require_once 'import/story-csv-importer.php';

  $importer = new \StoryCSVImporter;
  $return = $importer->handle_post();

  // Spits out json-encoded $return & die()s
  wp_send_json($return);
}

/**
 * Show link to CSV Import page
 */
add_action('admin_menu', __NAMESPACE__ . '\import_csv_admin_menu');
function import_csv_admin_menu() {
  add_submenu_page('edit.php?post_type=story', 'Import CSV', 'Import CSV', 'manage_options', 'storys-csv-importer', __NAMESPACE__ . '\import_csv_admin_form');
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
      <input type="hidden" name="action" value="story_csv_upload">
      <p class="submit"><input type="submit" class="button" id="csv-submit" name="submit" value="Import"></p>
    </form>

    <div class="import-notes">
      <h3>Format Guide:</h3>
<pre>story,author,focus_area
lorem ipsum,john doe,Media & Journalism
dolor sit amet,jane doe,Business</pre>
    </div>

  </div>
<?php
}

/**
 * Get Stories
 */
function get_stories($options=[]) {
  if (empty($options['num_posts'])) $options['num_posts'] = -1;
  $args = [
    'numberposts' => $options['num_posts'],
    'post_type'   => 'story',
  ];
  if (!empty($options['area'])) {
    $args['tax_query'] = [
      [
        'taxonomy' => 'story area',
        'field' => 'slug',
        'terms' => $options['area']
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
    $stories_posts = get_posts($args);
    $output = '';
    if (!$stories_posts) {
      $output .= '<p class="empty-message">No stories to share yet. Please submit yours now!</p>';
    } else {  
      foreach ($stories_posts as $story_post):
        ob_start();
        include(locate_template('templates/article-story.php'));
        $output .= ob_get_clean();
      endforeach;
    }
    return $output;
  }
}