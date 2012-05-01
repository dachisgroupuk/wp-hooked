<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package coldharbour
 * @since coldharbour 1.0
 *
 * Template Name: Custom page
 *
 */

get_header(); ?>

		<div id="primary" class="site-content">
			<section id="content" role="main">
        
				<?php
        /*Enables pagination on pages*/
        if ( get_query_var('paged') ) {
                $paged = get_query_var('paged');
        } elseif ( get_query_var('page') ) {
                $paged = get_query_var('page');
        } else {
                $paged = 1;
        }
        query_posts( array( 'post_type' => 'work', 'paged' => $paged ) );
        if ( have_posts() ) : $count = 0; while ( have_posts() ) : the_post(); $count++;
        ?>
					<?php get_template_part( 'content-custom', get_post_format() ); ?>

				<?php endwhile; // end of the loop. ?>

			</section><!-- #content -->
		</div><!-- #primary .site-content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>