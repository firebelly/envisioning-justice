<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/utils.php',                 // Utility functions
  'lib/init.php',                  // Initial theme setup and constants
  'lib/wrapper.php',               // Theme wrapper class
  'lib/conditional-tag-check.php', // ConditionalTagCheck class
  'lib/config.php',                // Configuration
  'lib/assets.php',                // Scripts and stylesheets
  'lib/titles.php',                // Page titles
  'lib/nav.php',                   // Custom nav modifications
  'lib/gallery.php',               // Custom [gallery] modifications
  'lib/extras.php',                // Custom functions
];

$firebelly_includes = [
  'lib/disable-comments.php',      // Disables WP comments in admin and frontend
  'lib/fb_init.php',               // FB theme setups
  'lib/fb_assets.php',             // IE scripts, etc
  'lib/media.php',                 // Image sizes, etc
  'lib/custom-functions.php',      // Rando utility functions and miscellany
  'lib/ajax.php',                  // AJAX functions
  'lib/cmb2-custom-fields.php',    // Add new fields to CMB2
  'lib/page-meta-boxes.php',       // Extra CMB2 Page fields
  'lib/post-meta-boxes.php',       // Extra CMB2 Post fields
  'lib/taxonomy-meta-boxes.php',   // Extra CMB2 Taxonomy fields
  'lib/short-codes.php',           // Custom short codes
  'lib/focus-area-taxonomy.php',   // Focus Areas
  'lib/event-post-type.php',       // Events
  'lib/sponsor-post-type.php',     // Sponsor Post Type
  'lib/hub-post-type.php',         // Hub Post Type
  'lib/program-post-type.php',     // Program Post Type
  'lib/commission-post-type.php',  // Commission Post Type
  'lib/story-post-type.php',       // Story Post Type
  'lib/resource-post-type.php',    // Resource Post Type
  'lib/site-options.php',          // Site Options
];

$sage_includes = array_merge($sage_includes, $firebelly_includes);

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);
