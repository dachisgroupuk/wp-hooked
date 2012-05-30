<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package wp-hooked
 * @since wp-hooked 1.0
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
<?php
/*
<script type="text/javascript">
    Êvar _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-2962934-13']);
    _gaq.push(['_trackPageview']);

 Ê(function() {
 Ê Êvar ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
 Ê Êga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
 Ê Êvar s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
 Ê})();

</script>
*/
?>
</body>
</html>
