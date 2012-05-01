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
 * Template Name: Ideas archive
 *
 */

get_header(); ?>

		<div id="primary" class="site-content">
			<section id="content" role="main">
        <article>
        	<header class="entry-header">
        		<h1 class="entry-title"><?php the_title(); ?></h1>
        	</header><!-- .entry-header -->
          <?php while ( have_posts() ) : the_post(); ?>
            
	        <div class="entry-content">
        	    <?php the_content('<p>Read the rest of this page &raquo;</p>'); ?>
        	</div>
            
  				<?php endwhile; // end of the loop. ?>
          <?php wp_reset_query(); ?>
				</article>
				<?php
        /*Enables pagination on pages*/
        if ( get_query_var('paged') ) {
                $paged = get_query_var('paged');
        } elseif ( get_query_var('page') ) {
                $paged = get_query_var('page');
        } else {
                $paged = 1;
        }
        query_posts( array( 
          'post_type' => 'ideas', 
          'status' => 'past',
          'paged' => $paged 
        ) );
        if ( have_posts() ) : $count = 0; while ( have_posts() ) : the_post(); $count++;
        ?>
        			<?php
    						/* Include the Post-Format-specific template for the content.
    						 * If you want to overload this in a child theme then include a file
    						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
    						 */
    						get_template_part( 'content', get_post_format() );
    					?>
				<?php endwhile; endif; // end of the loop. ?>

			</section><!-- #content -->
		</div><!-- #primary .site-content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>