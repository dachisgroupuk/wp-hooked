<?php


/**
 * function create_custom_taxonomies
 * 
 * Create custom taxonomies for use when registering post types
 *
 * @param taxonomy name
 * @param object_type which post type this new taxonomy refers to
 * @param args array of properties describing the new taxonomy
 *
 * @return void
 * @author Rich Holman
 *
 **/

function create_custom_taxonomies()
{

  // Region taxonomy
  register_taxonomy('status', array(), array(
    'hierarchical' => true,
    'labels' => array(
      'name' => _x( 'Status', 'taxonomy general name' ),
      'singular_name' => _x( 'Status', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Status' ),
      'popular_items' => __( 'Popular Status' ),
      'all_items' => __( 'All Statuses' ),
      'parent_item' => null,
      'parent_item_colon' => null,
      'edit_item' => __( 'Edit Status' ),
      'update_item' => __( 'Update Status' ),
      'add_new_item' => __( 'Add New Status' ),
      'new_item_name' => __( 'New Status Name' ),
      'separate_items_with_commas' => __( 'Separate Status with commas' ),
      'add_or_remove_items' => __( 'Add or remove Status' ),
      'choose_from_most_used' => __( 'Choose from the most used Status'),
    ),
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'status' ),
  ));
  
 }

/**
 * register_existing_posts_types_with_taxonomies
 *
 * Let us apply taxonomies to blog posts
 * Adding these direct
 *
 * @return void
 * @author Rich Holman
 **/

function register_existing_posts_types_with_taxonomies()
  {
    register_taxonomy_for_object_type('status', 'post');
  }


/**
 * register_new_post_types_with_taxonomies
 *
 * Because bfc_taxonomies is loaded later than bfc_post_types,
 * registering these taxonomies in the role definition won't work.
 * Calling them here lets us use the same action hook, and keeps the registration
 * of taxonomies in a single file.
 * 
 * @return true
 * @author Chris Adams
 **/
function register_new_post_types_with_taxonomies()
{
  $post_types = array('post', 'ideas');
  $taxonomies = array('status');

  foreach ($post_types as $content_type) {
    foreach($taxonomies as $tax) {
	if(!register_taxonomy_for_object_type($tax, $content_type)) {
		return new WP_Error('taxonomy registration error', "Trouble registering taxonomy $tax on $content_type", array($taxonomies, $post_types));      
	}
    }

  }

}

/**
 *  Add Wordpress' default taxonomies to custom posts.
 *  For reasons best know to wordpress, we have to use the 'init' hook
 *  for adding them.
 *
 *  http://www.deluxeblogtips.com/2010/07/custom-post-type-with-categories-post.html
 *
 * @return true,
 * @author Chris Adams
 * 
 * added study here (it activitates categories and tags
 * @author Nicholas Alexander
 **/
function register_default_taxonomies_with_new_post_types()
{
  $post_types = array('ideas');
  $taxonomies = array('post_tag', 'category');

  foreach ($post_types as $content_type) {
    foreach($taxonomies as $tax)
    {
      if(!register_taxonomy_for_object_type($tax, $content_type)) {
        return new WP_Error('taxonomy registration error', "Trouble registering taxonomy $tax on $content_type", array($taxonomies, $post_types));
      };
    }
  }

}

// Call them as early as possible
add_action( 'after_setup_theme', 'create_custom_taxonomies', 5);
add_action( 'init', 'register_default_taxonomies_with_new_post_types', 5);

add_action( 'after_setup_theme', 'register_new_post_types_with_taxonomies', 10);
add_action( 'after_setup_theme', 'register_existing_posts_types_with_taxonomies', 10 );

?>