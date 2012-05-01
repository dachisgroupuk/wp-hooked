<?php

class Sharing_Widget extends WP_Widget {
    /** constructor */
	  function Sharing_Widget() {
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
				<p class="notice">The content on this website is for Investment Professionals only and should be shared responsibly</p>
				<ul id="sharing">					
			    		<li class="linkdin"><a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(get_permalink()); ?>&title=<?php echo urlencode(the_title($echo=false)); ?>&source=<?php echo urlencode("http://www.bondvigilantes.co.uk"); ?>">LinkedIn</a></li>
  						<li class="twitter"><a href="http://twitter.com/home?status=Currently reading <?php the_permalink() ?>">Twitter</a></li>
  						<li class="facebook"><a href="<?php the_permalink() ?>">Facebook</a></li>
  						<li class="delicious"><a href="http://del.icio.us/post?url=<?php the_permalink() ?>&title=<?php the_title(); ?>">Del.icio.us</a></li>				
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
