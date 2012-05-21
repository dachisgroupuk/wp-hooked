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

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-2962934-13']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>