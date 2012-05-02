<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package coldharbour
 * @since coldharbour 1.0
 */
?>
		<section id="sidebar" class="widget-area" role="complementary">
      <?php do_action( 'before_sidebar' ); ?>
			<?php if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>
			<?php endif; // end sidebar widget area ?>
			  <aside id="votes" class="widget">
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
              </li>

    			  <?php endwhile; 
    			  // Reset Query
            wp_reset_query();
    			  ?>
    			  </ul>
    			</div>
			  </aside>
		</section><!-- #secondary .widget-area -->
