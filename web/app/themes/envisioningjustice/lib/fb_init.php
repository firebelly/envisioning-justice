<?php

namespace Firebelly\Init;

/**
 * Don't run wpautop before shortcodes are run! wtf Wordpress. from http://stackoverflow.com/a/14685465/1001675
 */
remove_filter('the_content', 'wpautop');
add_filter('the_content', 'wpautop' , 99);
add_filter('the_content', 'shortcode_unautop',100);

/**
 * Add Edit Page link for Events archive page
 */
function events_admin_edit_link() {
  global $wp_admin_bar;

  if (is_post_type_archive('event' ) ) {
    $page = get_page_by_title('Events');
    $wp_admin_bar->add_menu(array(
      'id' => 'edit',
      'class' => 'ab-item',
      'title' => __('Edit Page'),
      'href' => get_edit_post_link($page)
    ));
  }
}
add_action('wp_before_admin_bar_render', __NAMESPACE__ . '\events_admin_edit_link');

/**
 * FB theme inits
 */
function setup() {
  // Register wp_nav_menu() menus
  // http://codex.wordpress.org/Function_Reference/register_nav_menus
  register_nav_menus([
    'footer_links' => __('Footer Links', 'sage')
  ]);

  // Default Image options
  update_option('image_default_align', 'none');
  update_option('image_default_link_type', 'none');
  update_option('image_default_size', 'large');
}
add_action('after_setup_theme', __NAMESPACE__ . '\setup');

// function clean_up_content($content) {
//   // Convert <span class="button"><a></span> to <a class="button">
//   $content = preg_replace('/<span class=\\\"button\\\"><a(.*)<\/a><\/span>/', '<a class=\"button\"$1</a>', $content);
//   return $content;
// }
// add_filter('content_save_pre', __NAMESPACE__ . '\\clean_up_content', 10, 1);

/**
 * Custom nav highlighting for various pages
 */
function custom_nav_highlights($classes, $item) {
  // Focus Area taxonomy page should highlight "Our Work" and nothing else (was highlighting Events for some reason)
  if (is_singular('program') || is_tax('focus_area')) {
    if (in_array('menu-our-work', $classes))
      $classes[] = 'active';
    else
      $classes = array_diff($classes, array('active'));
  } else if (is_search()) {
    // no active nav on search
    $classes = array_diff($classes, array('active'));
  }

  return $classes;
}
if (!is_admin()) { add_filter('nav_menu_css_class', __NAMESPACE__ . '\custom_nav_highlights', 20, 2); }

/**
 * Custom theme classes added to body
 */
// function body_class($classes ) {
//   // Array of background hex values
//   $background_array = ['C2D6D9', 'B8DEBA', 'EDBABA', 'E5D4BE', 'C7C9CC', 'EBC7B0'];

//   // Select random bg & accents for page
//   $background = rand(1,6);
//   $accent = rand(1,5);

//   // Set global var to use when creating treated backgrounds
//   define('IHC_BACKGROUND', $background_array[$background-1]);

//   // Add to body_class()
//   $classes[] = 'background-' . $background;
//   $classes[] = 'accent-' . $accent;
//   return $classes;
// }
// add_filter('body_class', __NAMESPACE__ . '\body_class');


function mce_buttons_2($buttons) {
  array_unshift($buttons, 'styleselect');
  return $buttons;
}
add_filter('mce_buttons_2', __NAMESPACE__ . '\mce_buttons_2');

function simplify_tinymce($settings) {
  // What goes into the 'formatselect' list
  $settings['block_formats'] = 'Header=h3;Paragraph=p';

  $settings['inline_styles'] = 'false';
  if (!empty($settings['formats']))
    $settings['formats'] = substr($settings['formats'],0,-1).",underline: { inline: 'u', exact: true} }";
  else
    $settings['formats'] = "{ underline: { inline: 'u', exact: true} }";

  // What goes into the toolbars. Add 'wp_adv' to get the Toolbar toggle button back
  $settings['toolbar1'] = 'styleselect,bold,italic,underline,strikethrough,formatselect,bullist,numlist,blockquote,link,unlink,hr,wp_more,outdent,indent,AccordionShortcode,AccordionItemShortcode,fullscreen';
  $settings['toolbar2'] = '';
  $settings['toolbar3'] = '';
  $settings['toolbar4'] = '';

  // $settings['autoresize_min_height'] = 250;
  $settings['autoresize_max_height'] = 1000;

  // Clear most formatting when pasting text directly in the editor
  $settings['paste_as_text'] = 'true';

  $style_formats = array(
    array(
      'title' => 'Button',
      'block' => 'span',
      'classes' => 'wysiwyg-button',
    )
 );
  $settings['style_formats'] = json_encode($style_formats);

  return $settings;
}
add_filter('tiny_mce_before_init', __NAMESPACE__ . '\simplify_tinymce');

/**
 * Remove Press Release posts from everything but search & category pages
 */
function exclude_press_release_posts($wp_query) {
  if (!is_search() && !is_archive()) {
    set_query_var('category__not_in', [9]);
  }
}
add_action('pre_get_posts', __NAMESPACE__ . '\exclude_press_release_posts');

/**
 * Override Press Release category title to "Press Releases"
 */
add_filter('get_the_archive_title', function($title){
  if(is_category() && single_cat_title('',false)=='Press Release') {
    $title = 'Press Releases';
  }
  return $title;
});

/**
 * Force SSL on production
 */
add_action('template_redirect', __NAMESPACE__ . '\ssl_template_redirect', 1);
function ssl_template_redirect() {
  if (WP_ENV === 'production' && !is_ssl() ) {
    if (0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
        wp_redirect(preg_replace('|^http://|', 'https://', $_SERVER['REQUEST_URI']), 301);
        exit();
    } else {
        wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 301);
        exit();
    }
  }
}

/**
 * Allow <script> tags for Multiple Page Blocks (mostly for BlackBaud donation form)
 */
function allow_script_tags() {
  global $allowedposttags;
  $allowedposttags['script'] = array(
   'type' => array(),
   'src' => array()
 );
}
add_action('init', __NAMESPACE__.'\allow_script_tags', 10);