<?php

function wp_hooked_setup(){

    /*custom widgets*/
    require( get_stylesheet_directory() . '/plugins/wphooked_widgets.php' );
    /*custom post types*/
    require( get_stylesheet_directory() . '/plugins/wphooked_post_types.php' );
    /*custom taxonomies*/
    require( get_stylesheet_directory() . '/plugins/wphooked_taxonomies.php' );

	//load_theme_textdomain( 'vinur', get_template_directory() . '/languages' );

}
add_action( 'after_setup_theme', 'wp_hooked_setup', 1);

remove_action( 'wp_enqueue_scripts', 'vinur_scripts' );
/**
* Enqueue scripts and styles
*/
function wphooked_scripts() {
    global $post;

    wp_enqueue_style( 'style', get_stylesheet_uri() );

    wp_enqueue_script( 'jquery' );

    wp_enqueue_script( 'small-menu', get_template_directory_uri() . '/javascripts/small-menu.js', 'jquery', '20120328', true );
    wp_enqueue_script( 'helper', get_template_directory_uri() . '/javascripts/helper.js', 'jquery', '20120328', true );
    wp_enqueue_script( 'twitter', 'http://twitterjs.googlecode.com/svn/trunk/src/twitter.min.js', 'jquery', '20120328', true );
    wp_enqueue_script( 'application', get_template_directory_uri() . '/javascripts/application.js', 'jquery', '20120328', true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }

    if ( is_singular() && wp_attachment_is_image( $post->ID ) ) {
        wp_enqueue_script( 'keyboard-image-navigation', get_template_directory_uri() . '/javascripts/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
    }

    wp_enqueue_style( 'font-awesome', get_stylesheet_directory_uri() . '/stylesheets/font-awesome.css', array('style') );
    wp_enqueue_style( 'application', get_stylesheet_directory_uri() . '/stylesheets/application.css', array('style', 'font-awesome' ) );

    wp_enqueue_style( 'widgets', get_template_directory_uri() . '/stylesheets/widgets.css', array('style', 'font-awesome', 'application' ) );

}
add_action( 'wp_enqueue_scripts', 'wphooked_scripts' );

/**
 * Register widgetized area and update sidebar with default widgets
 *
 * @since wp-hooked 1.0
 */
function wphooked_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Sidebar', 'vinur' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}

remove_action( 'widgets_init', 'vinur_widgets_init' );
//add_action( 'widgets_init', 'wphooked_widgets_init' );




function my_deregister_styles() {
	wp_deregister_style( 'pfs-style' );
	wp_deregister_style( 'pfs-min-style' );
}

/*De-registering and re-registering pfs-styles to inform user of submission*/
function my_scripts_method() {
    wp_deregister_script( 'pfs-script' );
    wp_register_script( 'pfs-script', get_stylesheet_directory_uri() .'/javascripts/post-from-site-fix.js');
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

  //$wp_roles->remove_cap('author', 'publish_ideas');
  //$wp_roles->remove_cap('author', 'edit_published_ideas');
  //$wp_roles->remove_cap('author', 'edit_others_ideas');
  //$wp_roles->remove_cap('author', 'delete_ideas');
  //$wp_roles->remove_cap('author', 'delete_private_ideas');
  //$wp_roles->remove_cap('author', 'delete_published_ideas');
  //$wp_roles->remove_cap('author', 'delete_others_ideas');
  //$wp_roles->remove_cap('author', 'edit_private_ideas');
  //$wp_roles->remove_cap('author', 'edit_published_ideas');
  //$wp_roles->remove_cap('author', 'delete_published_posts');

  //$wp_roles->remove_cap('author', 'add_posts');
  //$wp_roles->remove_cap('author', 'edit_posts');
  //$wp_roles->remove_cap('author', 'edit_published_posts');
  //$wp_roles->remove_cap('author', 'delete_posts');
  //$wp_roles->remove_cap('author', 'delete_published_posts');
  //$wp_roles->remove_cap('author', 'edit_published_posts');
  //$wp_roles->remove_cap('author', 'publish_posts');

  //$wp_roles->remove_cap('author', 'add_media');
  //$wp_roles->remove_cap('author', 'edit_media');
  //$wp_roles->remove_cap('author', 'delete_media');
  //$wp_roles->remove_cap('author', 'published_media');
  //$wp_roles->remove_cap('author', 'upload_files');

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
