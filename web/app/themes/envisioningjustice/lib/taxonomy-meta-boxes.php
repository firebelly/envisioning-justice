<?php
/**
 * Extra fields for Taxonomies
 */

function cmb2_taxonomy_meta_initiate() {
  require_once( __DIR__.'/Taxonomy_MetaData/Taxonomy_MetaData_CMB2.php' );

  /**
   * Semi-standard CMB2 metabox/fields array
   */
  $meta_box = array(
    'id'         => 'cat_options',
    // 'key' and 'value' should be exactly as follows
    'show_on'    => array( 'key' => 'options-page', 'value' => array( 'unknown', ), ),
    'show_names' => true,
    'fields'     => array(
      array(
        'name' => 'Featured Image',
        'id'   => 'featured_image',
        'type' => 'file',
      ),
      array(
        'name' => 'Secondary Header Text',
        'id'   => 'secondary_header_text',
        'type' => 'wysiwyg',
        'options' => [ 'teeny' => true ],
      ),
      array(
        'name' => 'Contacts',
        'id'   => 'contacts',
        'type' => 'wysiwyg',
        'options' => [ 'teeny' => true ],
      ),
    )
  );

  $cats = new Taxonomy_MetaData_CMB2( 'focus_area', $meta_box, __( 'Category Settings', 'taxonomy-metadata' ) );
}
cmb2_taxonomy_meta_initiate();
