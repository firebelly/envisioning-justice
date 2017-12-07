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
          'name'    => 'Author',
          // 'desc'    => 'field description (optional)',
          'id'      => $prefix . 'author',
          'type'    => 'text',
      ),
      array(
          'name'    => 'Shown Count',
          'desc'    => 'This is here just for testing, will remove when TOD is rotating.',
          'id'      => $prefix . 'shown_count',
          'type'    => 'text',
      ),
      array(
          'name'    => 'Story of the Day',
          'desc'    => 'When checked will clear out previous Story of the Day',
          'id'      => $prefix . 'story_of_the_day',
          'type'    => 'checkbox',
      ),
    ),
  );

  return $meta_boxes;
}
add_filter('cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes');

/**
 * Get Story of the Day and output HTML
 */
function get_story() {
  $story_post = get_story_post();
  if (!$story_post) return false;
  $body = apply_filters('the_content', $story_post->post_content);
  $author = get_post_meta( $story_post->ID, '_cmb2_author', true );

  // hiding Focus Area, see http://issues.firebelly.co/issues/2067 6/11/15
  // if ($focus = \Firebelly\Utils\get_first_term($post, 'focus_area'))
  //   $author .= '<br><a href="'.get_term_link($focus).'">'.$focus->name.'</a>';
  // else
  //   $author .= '<br>Humanities';

  $output = <<<HTML
   <article>
     <blockquote>{$body}</blockquote>
     <cite>{$author}</cite>
   </article>
HTML;
  return $output;
}

/**
 * Get Story post
 */
function get_story_post() {
  $args = array(
    'numberposts' => 1,
    'post_type' => 'story',
    'meta_query' => [
      [
        'key' => '_cmb2_story_of_the_day',
        'value' => 'on',
        'compare' => '='
      ]
    ],
  );

  $story_posts = get_posts($args);
  if (!$story_posts) return false;
  else return $story_posts[0];
}

/**
 * Outputs a "Submit A Story" submit form
 */
function submit_form() {
?>
  <form class="new-story-form" method="post" action="">
    <textarea name="story" required placeholder="Type your story..."></textarea>
    <input type="text" name="author" required placeholder="Your Name">
    <div class="select-wrapper"><?php wp_dropdown_categories('show_option_none=Humanities&taxonomy=focus_area'); ?></div>
    <?php wp_nonce_field('new_story'); ?>
    <!-- die bots --><div style="position: absolute; left: -5000px;"><input type="text" name="die_bots_5000" tabindex="-1" value=""></div>
    <input type="hidden" name="action" value="story_submission">
    <div class="actions">
      <button type="submit" class="button">Submit Story</button>
    </div>
  </form>
<?php
}

/**
 * Handle a Story submission
 */
function story_submission() {
  $story = filter_var($_REQUEST['story'], FILTER_SANITIZE_STRING);
  $author = filter_var($_REQUEST['author'], FILTER_SANITIZE_STRING);
  $cat = filter_var($_REQUEST['cat'], FILTER_SANITIZE_NUMBER_INT);

  if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'new_story')) {
    wp_send_json_error(['message' => 'Failed security check']);
  } elseif (!empty($_REQUEST['die_bots_5000'])) {
    wp_send_json_error(['message' => 'Failed bot check']);
  } else {

    $is_spam = 'false';

    // Check with Akismet if possible
    if(function_exists('akismet_http_post')) {
      if (akismet_get_key()) {
        global $akismet_api_host, $akismet_api_port;

        $data = array(
          'comment_author'        => $author,
          'comment_content'       => $story,
          'user_ip'               => $_SERVER['REMOTE_ADDR'],
          'blog'                  => site_url(),
        );
        $query_string = http_build_query($data);
        $response = akismet_http_post($query_string, $akismet_api_host, '/1.1/comment-check', $akismet_api_port);
        $is_spam = (is_array( $response) && isset( $response[1])) ? $response[1] : 'false';
      }
    }

    if ($is_spam !== 'false') {
      wp_send_json_error(['message' => 'Akismet has marked this as spam']);
    } else {
      $my_post = [
        'post_title'    => sprintf('Submission from %s', $author),
        'post_content'  => $story,
        'post_type'     => 'story',
        'post_author'   => 1,
        'tax_input'     => ['focus_area' => $cat]
      ];
      $post_id = wp_insert_post($my_post);
      update_post_meta($post_id, '_cmb2_author', $author);

      // Notify admin of submission?
      $story_of_day_email = get_option('story_of_day_email');
      if ($story_of_day_email && is_email($story_of_day_email)) {
        if ($cat>0) {
          $focus_area = get_term($cat, 'focus_area');
          $focus_area_name = $focus_area->name;
        } else {
          $focus_area_name = 'Humanities';
        }
        $email_txt = "You have a new Storysubmission!";
        $email_txt .= "\n\nStory: " . $story;
        $email_txt .= "\n\nAuthor: " . $author;
        $email_txt .= "\n\nFocus Area: " . $focus_area_name;
        $email_txt .= "\n\nEdit/Publish: ".admin_url('post.php?post=' . $post_id . '&action=edit');

        // Email user set in Site Settings
        wp_mail($story_of_day_email, sprintf('New Story of the Day submission from %s', $author), $email_txt);
      }

      // Pull response copy from Site Settings and return via json
      $story_of_day_response = get_option('story_of_day_response', 'Your submission is in review.');
      wp_send_json_success(['message' => sprintf($story_of_day_response, $author)]);
    }

  }
}
add_action('wp_ajax_story_submission', __NAMESPACE__ . '\\story_submission');
add_action('wp_ajax_nopriv_story_submission', __NAMESPACE__ . '\\story_submission');

/**
 * Set initial shown_count of new Story to lowest count of all Story posts
 */
function init_shown_count($post_id, $post, $update) {
  global $wpdb;
  if (wp_is_post_revision($post_id) || $update || $post->post_type != 'story')
    return;
  // Find lowest shown_count
  $lowest_count = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_cmb2_shown_count' ORDER BY meta_value ASC LIMIT 1");
  update_post_meta($post_id, '_cmb2_shown_count', $lowest_count);
}
add_action('wp_insert_post', __NAMESPACE__ . '\init_shown_count', 10, 3);

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