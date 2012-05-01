<?php
/**
 *
 * Plugin Name: DG - Fix for custom post type pagination
 * Description: This plugin fixes an issue with paginating over a customer post_type. A bug in WordPress performs a count on the post_type of 'post'. WordPress should instead be looking at the actual post_type, therefore getting the correct count. This bug returns a 404 page.
 *
 * Version: 1.0
 * Author: Ross Tweedie and Ballyhoo Blog
 * Author URI: http://dachisgroup.com
 * This code is based on the code examples at: http://www.ballyhooblog.com/add-custom-post-types-wordpress-main-feed/
 */

function dg_fix_custom_post_type_pagination($qs){
	
    if( isset($qs['category_name']) and isset($qs['paged']) ){
		
        $args = array(
                        'public'   => true,
                        '_builtin' => false
                    );
        
        $qs['post_type'] = get_post_types( $args );
		array_push( $qs['post_type'],'post' );
	}
	return $qs;
}

add_filter('request', 'dg_fix_custom_post_type_pagination');