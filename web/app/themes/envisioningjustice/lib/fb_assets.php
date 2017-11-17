<?php

/**
 * Scripts and stylesheets
 */

namespace Firebelly\Assets;

function crufty_ie_scripts() {
  // <IE9 js (from http://stackoverflow.com/a/16221114/1001675)
  $conditional_scripts = [
    'svg4everybody'       => \Roots\Sage\Assets\asset_path('scripts/respond.js'),
    'respond'             => \Roots\Sage\Assets\asset_path('scripts/svg4everybody.js')
  ];
  foreach ($conditional_scripts as $handle => $src) {
    wp_enqueue_script($handle, $src, [], null, false);
  }
  add_filter('script_loader_tag', function($tag, $handle) use ($conditional_scripts) {
    return (array_key_exists($handle, $conditional_scripts)) ? "<!--[if lt IE 9]>$tag<![endif]-->\n" : $tag;
  }, 10, 2 );
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\crufty_ie_scripts', 100);

function scripts() {
  wp_enqueue_script('mapbox', \Roots\Sage\Assets\asset_path('scripts/mapbox.js'), [], null, true);
  wp_enqueue_script('addthis', '//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5522ee3d7ab8ddd8', [], null, true);
}
add_action('wp_enqueue_scripts', __NAMESPACE__.'\\scripts', 100);

function admin_scripts() {
  wp_register_style('fb_admin_css', \Roots\Sage\Assets\asset_path('styles/admin.css'), false, '1.0', 'all');
  wp_enqueue_style('fb_admin_css');
  wp_register_script('fb_admin_js', \Roots\Sage\Assets\asset_path('scripts/admin.js'), ['jquery'], null, true);
  wp_enqueue_script('fb_admin_js');

  wp_register_style('select2css', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css', false, '1.0', 'all');
  wp_enqueue_style('select2css');
  wp_register_script('select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js', ['jquery'], null, true);
  wp_enqueue_script('select2');
}
add_action('admin_enqueue_scripts', __NAMESPACE__ . '\admin_scripts');

function admin_scripts_inline() {
  ?>
  <script type='text/javascript'>
  jQuery(document).ready(function($) {
     $('#_cmb2_related_program').select2();
  });
  </script>
  <?php
}
add_action( 'admin_head', __NAMESPACE__ . '\admin_scripts_inline' );
