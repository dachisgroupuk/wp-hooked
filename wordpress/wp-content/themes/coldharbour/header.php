<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package coldharbour
 * @since coldharbour 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'coldharbour' ), max( $paged, $page ) );

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php wp_head(); ?>

<!--[if lt IE 9]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/stylesheets/font-awesome.css" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/stylesheets/application.css" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/stylesheets/widgets.css" />

<script type="text/javascript" src="http://use.typekit.com/tdt1tnx.js"></script>
<script type="text/javascript">try{Typekit.load();}catch(e){}</script>

</head>

<body <?php body_class(); ?>>
<div id="site" class="hfeed site">
	<?php do_action( 'before' ); ?>
	<header id="masthead" class="site-header" role="banner">
		<hgroup>
			<h1 class="site-title"><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
			<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
		</hgroup>

		<nav role="navigation" class="site-navigation main-navigation">
			<h1 class="assistive-text"><?php _e( 'Menu', 'coldharbour' ); ?></h1>
			<div class="assistive-text skip-link"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'coldharbour' ); ?>"><?php _e( 'Skip to content', 'coldharbour' ); ?></a></div>

			<?php wp_nav_menu( array( 'theme_location' => 'primary',  'menu' => 'primary') ); ?>
		</nav>
	</header><!-- #masthead .site-header -->

	<div id="main">