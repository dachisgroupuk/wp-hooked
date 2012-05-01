<?php 
//class Sharing_Widget
require( get_template_directory() . '/plugins/widgets/sharing_widget.php' );
//class Twitter_Widget
require( get_template_directory() . '/plugins/widgets/twitter_widget.php' );

// register custom widgets
add_action('widgets_init', create_function('', 'return register_widget("Sharing_Widget");'));
add_action('widgets_init', create_function('', 'return register_widget("Twitter_Widget");'));
?>