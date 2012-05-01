<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package coldharbour
 * @since coldharbour 1.0
 */
?>

	</div><!-- #main -->

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="site-info">
      <p class="copyright">Copyright &copy; <?php echo date('Y'); ?></p>
		</div><!-- .site-info -->
	</footer><!-- .site-footer .site-footer -->
</div><!-- #page .hfeed .site -->

<?php wp_footer(); ?>

<script>
  MBP.scaleFix();
  MBP.hideUrlBar();
</script>

</body>
</html>