<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package coldharbour
 * @since coldharbour 1.0
 */

get_header(); ?>
    <?php $theme_options = get_option("coldharbour_theme_options") ?>
		<div id="primary" class="site-content">
		  <section id="introduction">
		    <article>
		      		<header class="entry-header">
    						<h1 class="entry-title"><?php _e( 'Welcome to wp-hooked', 'coldharbour' ); ?></h1>
    					</header><!-- .entry-header -->
    					<div class="entry-content"> 
		            <?php echo $theme_options["sample_textarea"]; ?>
		          </div>
		    </article>
		  </section>  
			<section id="content" role="main">
			<?php if ( have_posts() ) : ?>

				<?php // coldharbour_content_nav( 'nav-above' ); ?>

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php
						/* Include the Post-Format-specific template for the content.
						 * If you want to overload this in a child theme then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						get_template_part( 'content', get_post_format() );
					?>

				<?php endwhile; ?>

				<?php coldharbour_content_nav( 'nav-below' ); ?>

			<?php elseif ( current_user_can( 'edit_posts' ) ) : ?>

				<article id="post-0" class="post no-results not-found">
					<header class="entry-header">
						<h1 class="entry-title"><?php _e( 'No posts to display', 'coldharbour' ); ?></h1>
					</header><!-- .entry-header -->

					<div class="entry-content"> 
						<p><?php printf( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'coldharbour' ), admin_url( 'post-new.php' ) ); ?></p>
					</div><!-- .entry-content -->
				</article><!-- #post-0 -->

			<?php endif; ?>

			</section><!-- #content -->
		</div><!-- #primary .site-content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>