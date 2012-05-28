<?php

function my_deregister_styles() {
	wp_deregister_style( 'pfs-style' );
	wp_deregister_style( 'pfs-min-style' );
}

/*De-registering and re-registering pfs-styles to inform user of submission*/
function my_scripts_method() {
    wp_deregister_script( 'pfs-script' );
    wp_register_script( 'pfs-script', get_template_directory_uri() .'/javascripts/post-from-site-fix.js');
    wp_enqueue_script( 'pfs-script' );
}

add_action('wp_enqueue_scripts', 'my_scripts_method');


/*Let's take away some plugin styles yeah?*/
add_action( 'wp_print_styles', 'my_deregister_styles', 100 );


//add_action( 'after_setup_theme' , 'coldharbour_roles');
add_action( 'admin_init', 'remove_author_rights' );

function remove_author_rights(){
  global $wp_roles;

  $wp_roles->add_cap('administrator', 'publish_ideas');
  $wp_roles->add_cap('administrator', 'edit_published_ideas');
  $wp_roles->add_cap('administrator', 'edit_others_ideas');
  $wp_roles->add_cap('administrator', 'delete_ideas');
  $wp_roles->add_cap('administrator', 'delete_private_ideas');
  $wp_roles->add_cap('administrator', 'delete_published_ideas');
  $wp_roles->add_cap('administrator', 'delete_others_ideas');
  $wp_roles->add_cap('administrator', 'edit_private_ideas');
  $wp_roles->add_cap('administrator', 'edit_published_ideas');
  $wp_roles->add_cap('administrator', 'delete_published_posts');

  $wp_roles->remove_cap('author', 'publish_ideas');
  $wp_roles->remove_cap('author', 'edit_published_ideas');
  $wp_roles->remove_cap('author', 'edit_others_ideas');
  $wp_roles->remove_cap('author', 'delete_ideas');
  $wp_roles->remove_cap('author', 'delete_private_ideas');
  $wp_roles->remove_cap('author', 'delete_published_ideas');
  $wp_roles->remove_cap('author', 'delete_others_ideas');
  $wp_roles->remove_cap('author', 'edit_private_ideas');
  $wp_roles->remove_cap('author', 'edit_published_ideas');
  $wp_roles->remove_cap('author', 'delete_published_posts');

  $wp_roles->remove_cap('author', 'add_posts');
  $wp_roles->remove_cap('author', 'edit_posts');
  $wp_roles->remove_cap('author', 'edit_published_posts');
  $wp_roles->remove_cap('author', 'delete_posts');
  $wp_roles->remove_cap('author', 'delete_published_posts');
  $wp_roles->remove_cap('author', 'edit_published_posts');
  $wp_roles->remove_cap('author', 'publish_posts');

  $wp_roles->remove_cap('author', 'add_media');
  $wp_roles->remove_cap('author', 'edit_media');
  $wp_roles->remove_cap('author', 'delete_media');
  $wp_roles->remove_cap('author', 'published_media');
  $wp_roles->remove_cap('author', 'upload_files');

}

/*Voting rank*/
function b1_get_post_rank($post)
{
	global $wpdb;

	$total_posts = $wpdb->get_var('SELECT COUNT( DISTINCT vote_count)
					   	FROM ' . $wpdb->prefix . 'wpv_voting');

	$total_smaller = $wpdb->get_var('SELECT COUNT( DISTINCT vote_count )
					   	FROM ' . $wpdb->prefix . 'wpv_voting
						WHERE vote_count < (SELECT vote_count
											FROM ' . $wpdb->prefix . 'wpv_voting
											WHERE post_id = ' . $post->ID . ')');

	$rank = $total_posts - $total_smaller;

	return $rank;
}
