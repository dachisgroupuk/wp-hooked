<?php

class Ideas_Widget extends WP_Widget {
    /** constructor */
	  function Ideas_Widget() {
	    parent::WP_Widget(
	      false,
	      $name = 'Coldharbour - Ideas widget',
	      $widget_options = array(
	        'description' => 'Ideas widget',
	      )
	    );
	  }


    // build the client facing element of the form
	  //
	  /** @see WP_Widget::widget */
	  function widget($args, $instance) {
	    extract( $args );
      // $title = apply_filters('widget_title', $instance['title']);
	    /**  begin widget html */

	    echo $before_widget;

				?>
			    <div class="widget-box">
			      <h3>Latest Ideas</h3>
			      <ul>
    					<?php
              /*Enables pagination on pages*/
                 query_posts( array(
                'post_type' => 'ideas',
                'status' => 'future',
              ) );
              // The Loop
              while ( have_posts() ) : the_post();
              ?>
              <li>
                <a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'coldharbour' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
                <span class="date">
                  <?php coldharbour_posted_on(); ?>
                </span>
                <span class="thumbs-up">
                  <i class="icon-thumbs-up"></i>
              	  <?php
              	  if(function_exists('wpv_voting_display_vote'))
              	   wpv_voting_display_vote(get_the_ID());
              	  ?>
              	</span>
              	<?php //global $post; ?>
              	<?php //echo b1_get_post_rank($post) ?>
              </li>
    			  <?php endwhile;
    			  // Reset Query
            wp_reset_query();
    			  ?>
    			  </ul>
    			</div>
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
