<?php

class Ideas_Widget extends WP_Widget {
    /** constructor */
	  function Ideas_Widget() {
	    parent::WP_Widget(
	      false, 
	      $name = 'Coldharbour - Sharing widget', 
	      $widget_options = array(
	        'description' => 'Sharing and liking content widget',
	      )
	    );	
	  }

		
    // build the client facing element of the form
	  //
	  /** @see WP_Widget::widget */
	  function widget($args, $instance) {		
	    extract( $args );
	    $title = apply_filters('widget_title', $instance['title']);
	    /**  begin widget html */

	    echo $before_widget;

				?>
				<ul id="ideas">					
  					
				</ul>
				<?php
	    
	    echo $after_widget; 

	  }
    
    /** @see WP_Widget::update */
	  function update($new_instance, $old_instance) {				
	    // create a back up we can access it when updating content
	    $instance = $old_instance;
	    $instance['title'] = strip_tags($new_instance['title']);
	    return $instance;
	  }

	  /** @see WP_Widget::form */
	  function form($instance) {				
	    $title = esc_attr($instance['title']);
	    // TODO tidy up this form code, it looks horrific
	    ?>
	         <p>
	          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
	        </p>
	    <?php 
	  }

	} 
	?>
