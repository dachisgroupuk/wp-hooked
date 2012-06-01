<?php
/**
 * Plugin Name: DG - idea list
 * Description: List ideas and give to ability to vote for them via a widget.
 * Version: 1.0
 * Author: Rich Holman and Ross Tweedie
 * Author URI: http://dachisgroup.com
 *
 */
 if ( version_compare( $GLOBALS['wp_version'], '3.1', '<' ) || !function_exists( 'add_action' ) ) {
    if ( !function_exists( 'add_action' ) ) {
        $exit_msg = 'I\'m just a plugin, please don\'t call me directly';
    } else {
        // Subscribe2 needs WordPress 3.1 or above, exit if not on a compatible version
        $exit_msg = sprintf( __( 'This version of country list required WordPress 3.1 or greater.' ) );
    }
    exit( $exit_msg );
}


add_action( 'widgets_init', create_function( '', 'register_widget( "DG_Ideas_Widget" );' ) );

/**
 * Adds DGI_Widget widget.
 */
class DG_Ideas_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
             'dgi_widget', // Base ID
            'DG Ideas Widget', // Name
            array( 'description' => __( 'A DGI Widget', 'text_domain' ), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        extract( $args );
                
        $title = apply_filters('widget_title', $instance['title']);
        
        echo $before_widget;
        ?>
        <div class="widget-box">
        <?php echo $before_title . $title . $after_title; ?>
            <ul>
                <?php
                wp_reset_query();
                /*Enables pagination on pages*/
                query_posts( array(
                                'post_type' => 'ideas',
                                'status' => 'future',
                                  )
                            );
                // The Loop
                while ( have_posts() ) : the_post();
                ?>
                    <li>
                        <a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'vinur' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
                        <span class="date">
                            <?php vinur_posted_on(); ?>
                        </span>
                        <span class="thumbs-up">
                            <i class="icon-thumbs-up"></i>
                            <?php
                            if(function_exists('wpv_voting_display_vote')){
                                wpv_voting_display_vote( get_the_ID() );
                            }
                            ?>
                        </span>
                        <?php global $post; ?>
                        <?php //echo b1_get_post_rank( $post ) ?>
                    </li>
              <?php
              endwhile;
              // Reset Query
              wp_reset_query();
              ?>
            </ul>
        </div>
        <?php
        echo $after_widget;
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        
        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'text_domain' );
        }
        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php 
    }

} // class Foo_Widget