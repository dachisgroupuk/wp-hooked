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
		</section><!-- #secondary .widget-area -->
