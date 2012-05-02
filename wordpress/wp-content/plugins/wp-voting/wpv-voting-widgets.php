<?php
/*
 * Total vote count widget
 * @since 1.5
 */
class Wpv_Total_Vote_Widget extends WP_Widget {
    ### Constructor
    function Wpv_Total_Vote_Widget(){
        parent::WP_Widget('wpv_total_vote_widget', 'WP Voting - Total Vote', array('description' => 'Display total vote count', 'class' => 'wpv-total-vote'));
    }
    
    ### Backend 
    function form($instance){
        $default = array('title' => __('Total Vote Count'));
        $instance = wp_parse_args((array) $instance, $default);
        
        $field_id = $this->get_field_id('title');
        $field_name= $this->get_field_name('title');
        echo "\r\n".'<p><label for="'.$field_id.'">'.__('Title').': <input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.attribute_escape( $instance['title'] ).'" /><label></p>';
    }
    
    ### Update widget settings
    function update($new_instance, $old_instance){
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }
    
    ### Frontend 
    function widget($args, $instance) {
        extract($args, EXTR_SKIP);
        $title = empty($instance['title']) ? '&nbsp;' : apply_filters('widget_title', $instance['title']);
        echo $before_widget;
        echo $before_title . $title . $after_title;
        echo '<span class="wpvtcount">'.  wpv_total_vote_calc() .'</span>';
        echo $after_widget;
    }
}
add_action( 'widgets_init', create_function( '', 'return register_widget("Wpv_Total_Vote_Widget");' ) );


/*
 * Top voted posts widget
 * @since 1.7
 */
class Wpv_Top_Voted_Widget extends WP_Widget {
    ### Constructor
    function Wpv_Top_Voted_Widget(){
        parent::WP_Widget('wpv_top_voted_widget', 'WP Voting - Top Voted', array('description' => 'Display top voted item', 'class' => 'wpv-top-voted'));
    }
    
    ### Backend
    function form($instance){
        $default = array('title' => __('Top Voted'), 'showcount' => __('5'));
        $instance = wp_parse_args((array) $instance, $default);
        
        $title_id = $this->get_field_id('title');
        $title_name= $this->get_field_name('title');
        $showcount_id = $this->get_field_id('showcount');
        $showcount_name= $this->get_field_name('showcount');
        echo "\r\n".'<p><label for="'.$title_id.'">'.__('Title').': <input type="text" class="widefat" id="'.$title_id.'" name="'.$title_name.'" value="'.attribute_escape( $instance['title'] ).'" /><label></p>';
        echo "\r\n".'<p><label for="'.$showcount_id.'">'.__('Number of items to show').': <label><input type="text" size="3" name="'.$showcount_name.'" value="'.attribute_escape( $instance['showcount'] ).'" /></p>';
    }
    
    ### Update widget settings
    function update($new_instance, $old_instance){
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['showcount'] = (int)strip_tags($new_instance['showcount']);
        // Update option to use it in top voted widget ajax update
        update_option('wpv-top-voted-scount', $instance['showcount']); 
        return $instance;
    }
    
    ### Frontend
    function widget($args, $instance) {
        extract($args, EXTR_SKIP);
        $title = empty($instance['title']) ? '&nbsp;' : apply_filters('widget_title', $instance['title']);
        $showcount = empty($instance['showcount']) ? '5' : $instance['showcount'];
        $showcount = (int)$showcount;
        echo $before_widget;
        echo $before_title . $title . $after_title;
        echo '<span class="wpvtopvoted">'.  wpv_top_voted_calc($showcount) .'</span>';
        echo $after_widget;
    }
}
add_action( 'widgets_init', create_function( '', 'return register_widget("Wpv_Top_Voted_Widget");' ) );
?>