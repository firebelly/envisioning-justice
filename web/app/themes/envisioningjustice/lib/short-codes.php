<?php
/**
 * Custom short codes
 */

namespace Firebelly\Utils\ShortCodes;
use Firebelly\Utils;

/**
 * Shortcode [focus_areas]
 * @return string HTML list of Focus Areas
 */

add_shortcode('focus_areas', __NAMESPACE__ . '\focus_areas_shortcode');
function focus_areas_shortcode($atts) {
  $output = '<h3 class="accent">Our Focus Areas</h3>';
  $output .= '<ul class="focus-list">';
  $focus_areas = get_terms('focus_area');
  foreach ($focus_areas as $focus_area) 
    $output .= '<li><a href="' . get_term_link($focus_area) . '">' . $focus_area->name . '</a></li>';
  $output .= '</ul>';
  return $output;
}

/**
 * Shortcode [media_contact]
 * @return string HTML return from setting media_contact
 */
add_shortcode('media_contact', __NAMESPACE__ . '\media_contact_shortcode');
function media_contact_shortcode($atts) {
  return get_option( 'media_contact', 'Set media_contact in Site Settings.' );
}
