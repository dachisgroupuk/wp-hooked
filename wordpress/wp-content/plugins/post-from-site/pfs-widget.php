<?php
// register PfsWidget widget
add_action('widgets_init', create_function('', 'return register_widget("PfsWidget");'));

/**
 * Post From Site extends the widget class to create widget.
 */
class PfsWidget extends WP_Widget {
    /** constructor */
    function PfsWidget() {
        parent::WP_Widget(false, $name = 'Post From Site', array('description' => "Place a link on your site to pop up a 'write post' box."));	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $category = $instance['category'];
        $link = apply_filters('widget_title', $instance['link']);
        $popup = $instance['popup'];
        echo $before_widget;
        if ( $title ) echo $before_title . $title . $after_title;
        echo "<ul><li>";
        $pfs = new PostFromSite(0,$link,$popup,$category);
        $pfs->form();
        echo "</li></ul>";
        echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['category'] = strip_tags($new_instance['category']);
        $instance['link'] = strip_tags($new_instance['link']);
        $instance['popup'] = (isset($new_instance['popup'])) ? 'true' : false;
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
	    if ( $instance ) {
			$title = esc_attr($instance['title']);
			$category = esc_attr($instance['category']);
			$link = esc_attr($instance['link']);
			$popup = $instance['popup'];
	    } else {
	    	$title = '';
	    	$category = '';
	    	$link = '';
	    	$popup = 0;
	    }
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>">
            <?php _e('Title:'); ?>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </label></p>
        <p><label for="<?php echo $this->get_field_id('category'); ?>">
            <?php _e('Category:','pfs_domain');?>
            <select  class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>"><?php 
                $categories = wp_dropdown_categories("echo=0&hide_empty=0");
                preg_match_all('/\s*<option class="(\S*)" value="(\S*)">(.*)<\/option>\s*/', $categories, $matches, PREG_SET_ORDER);
                echo "<option class='{$matches[0][1]}' value=''></option>";
                foreach ($matches as $match){
                    if ($category == $match[2])
                        echo "<option class='{$match[1]}' value='{$match[2]}' selected>{$match[3]}</option>";
                    else
                        echo "<option class='{$match[1]}' value='{$match[2]}'>{$match[3]}</option>";
                }
            ?></select>
        </label></p>
		<p><label for="<?php echo $this->get_field_id('link'); ?>">
		    <?php _e('Link Text:','pfs_domain'); ?>
		    <input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo $link; ?>" />
        </label></p>
        <p><label for="<?php echo $this->get_field_id('popup'); ?>">
		    <input class="checkbox" id="<?php echo $this->get_field_id('popup'); ?>" name="<?php echo $this->get_field_name('popup'); ?>" type="checkbox" value="true" <?php if ($popup) echo "checked"; ?> />
		    <?php _e('Hide form until clicked','pfs_domain'); ?>
        </label></p>
    <?php }
} // class PfsWidget
?>