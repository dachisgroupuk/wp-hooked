<?php

/** Tell WordPress to run coldharbour_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'coldharbour_post_types', 2 );

/**
 * Setup post types used in the site
 * Remember to update role capabilities too.
 *
 * @return void
 * @author Rich Holman
 * @version 1.0
 *
 **/
function coldharbour_post_types() {

  // Case Studies
  register_post_type( 'ideas',
    array(
        'labels' => array(
        'name' => __( 'Ideas' ),
        'singular_name' => __( 'Idea' ),
        'add_new' => __( 'Add New' ),
        'add_new_item' => __( 'Add New Idea' ),
        'edit' => __( 'Edit' ),
        'edit_item' => __( 'Edit Idea' ),
        'new_item' => __( 'New Idea' ),
        'view' => __( 'View Idea' ),
        'view_item' => __( 'View Idea' ),
        'search_items' => __( 'Search ideas' ),
        'not_found' => __( 'No ideas found' ),
        'not_found_in_trash' => __( 'No ideas found in Trash' ),
        'parent' => __( 'Parent Idea' ),
      ),
      'supports' => array(
          'title' ,
          'editor',
          'thumbnail',
          'custom-fields',
          'trackbacks',
          'comments',
          'excerpt',
          'revisions',
          'author'
      ),
      'map_meta_cap' => true,
      'capability_type' => 'idea',
      'menu_position' => 4,
      'public' => true,
      'rewrite' => array( 'slug' => 'ideas', 'with_front' => true ),
      'register_meta_box_cb' => 'add_property_metaboxes',
    )
  );

}

?>
