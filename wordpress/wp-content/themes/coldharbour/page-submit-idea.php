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
 * Template Name: Submit Idea
 *
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<section id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>


			<?php endwhile; // end of the loop. ?>
			
			
			<article id="submit">
			  <?php
            $pfs = new PostFromSite(0, 'write a post', false, '');
            $pfs->form();
        ?>
			</article>

		</section><!-- #content -->
	</div><!-- #primary .site-content -->
  

<?php get_sidebar(); ?>
<?php get_footer(); ?>