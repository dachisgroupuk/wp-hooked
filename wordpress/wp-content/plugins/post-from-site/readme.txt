=== Plugin Name ===
Contributors: ryelle
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=YB5AWJMBLCCVC&lc=US&item_name=redradar%2enet&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted
Tags: quick post, frontend, insert post, post, Post, custom post type
Requires at least: 3.2
Tested up to: 3.3.1
Stable tag: 3.0.1

Write a post without leaving your site!

== Description ==

Add an interface on your site to write a post (or page, or anything), without having to go into the admin section. Also allows for 'anonymous' posting (not logged in users, still asks for name/email) with a recaptcha. This makes Post From Site a perfect plugin for your user reviews, a suggestion box, or even a very basic forum site. 

After install, you can display a form on your site via a widget, shortcode, or PHP code in your theme. See [this page for further documentation](http://me.redradar.net/category/plugins/post-from-site/).

== Upgrade Notice ==

If you're updating from 2.0.3 or below, you'll need to resave your settings before it will work correctly (a lot of things were changed in 3.0).

== Installation ==

1. Unzip `pfs.zip`
1. Upload all files to the `/wp-content/plugins/post-from-site` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

= Use your choice of include: =
1. Add a widget in the [Widgets section](http://codex.wordpress.org/Appearance_Widgets_Screen)
2. Add a shortcode to a page/post/CPT
 * Post From Site's basic shortcode is `[post-from-site]`. It has three options: `popup` defines whether the form will show on the site, or only after clicking a link (defaults to false, not a popup). `link` defines that link's text (defaults to 'quick post'). `cat` restricts the post to a specific category (defaults to none).
3. Add PHP code to your template files. 
 * `<?php $pfs = new PostFromSite(); $pfs->form(); ?>` will output the form. You can pass the same variables as in the shortcode.

== Changelog ==
= 3.0.1 =
* Added the post_from_site function back for compatability

= 3.0 =
* Rewrote code (again) into a class.
* Added Custom Post Type functionality, along with support for all taxonomies.
* Added widget functionality
* Added actions and filters to allow extension
* Images are uploaded correctly to gallery, and included image size is customizable

= 2.0.3 =
* Fixed the call to the non-existent 'pfs-widget.php'.

= 2.0.2 =
* Fixed an issue with headers
* Changed the `div` tag back to an `a` tag.

= 2.0.1 =
* Compatibility with 3.0

= 2.0.0 =
* scrapped a lot of code, most of it never made a release
* moved over to strictly using jQuery
* multiple file upload support
* submits using ajax, then refreshes page, so you can see you addition immediately
* also gets rid of the double-post if you refresh the page
* default style has been changed

= 1.9.0 =
* fixes double posting; 
* better image support; 
* introduction of '[!--image--]' tag; 
* existing category/tag dropdown with multiple selection;  
* ability to create new categories/tags;  
* other minor adjustments

= 1.7.0 =
* addition of tags 
* bugfixes

= 1.6.x =
* Initial releases

== Frequently Asked Questions ==

= The popup won't show up / I'm redirected to a white page on submit =

Check that you have the javascript and css files in the plugin's folder (`post-from-site`). A problem with the first version of this plugin was that the plugin was looking for the files in the wrong directory. For other people it was also a problem with my code assuming a Linux filestructure, so on Windows servers it broke. 2.0.0+ shouldn't have this problem, as I'm using a different method of calling other files.

[ask a question](http://wordpress.org/tags/post-from-site?forum_id=10)?

== Screenshots ==

1. Post From Site in action (default 'twentyeleven' theme): inserted onto a sticky post using the shortcode.
