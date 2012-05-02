=== WP Voting ===
Contributors: tristanmin
Donate link: http://www.wpclue.com/
Tags: plugin, voting
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 1.7

Site owner to add voting functionality to the blog posts.

== Description ==

A plugin to site owner to add voting functionality to the blog posts.
Using the power of the ajax, voter can have instant voting and update to top voted widget.
It doesn't matter voter is logged in or not. Public voting is done by tracking their IP address.
It isn't end here. Blog owner will have complete control over those voting feature as well as using custom
vote button and vote text.

Features

1. Control voting feature via voting on/off (admin feature)
2. Control allow or disallow post author to vote his own posts (admin feature)
3. Control allow or disallow public voting (admin feature)
4. Customise vote and voted text (admin feature)
5. Customise vote and voted buttons images (admin feature)
6. Customise alert message for non logged in users (admin feature)
7. Voting logs for site administrator (admin feature)
8. Sort voting logs by vote count or voted date (admin feature)
9. Show current vote count and vote button on frontend templates
10. Compitible with almost all of the themes. You just need to call required function from your template
11. Now shortcode supported.
12. Total vote count widget
13. Top voted widget
14. Now public voting supported.

Features in future releases

1. Export to CSV for admin voting logs
2. Registered user voting logs
3. Top voted posts list shortcode

Note-1: Kindly please rate this plugin or vote the compatibility.  So I could review it and improve the quality of this plugin. Appreciate your help!

Resources

http://www.garyc40.com/2010/03/5-tips-for-using-ajax-in-wordpress/

http://www.onextrapixel.com/2009/06/22/how-to-add-pagination-into-list-of-records-or-wordpress-plugin/

http://www.onextrapixel.com/2009/07/01/how-to-design-and-style-your-wordpress-plugin-admin-panel/

http://planetozh.com/blog/2009/09/top-10-most-common-coding-mistakes-in-wordpress-plugins/

http://wordpress.org/extend/plugins/vote-it-up/

http://wordpress.org/extend/plugins/shortcodes-ultimate/

== Installation ==

1. Upload `wpv-voting` folder to `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Voting menu >> Voting Options and turn on the voting feature
4. You may add the custom alert message for non logged in users
5. You can use shortcode `[wpvoting]` directly in your posts content < OR >
6. Add `if(function_exists('wpv_voting_display_vote')) wpv_voting_display_vote(get_the_ID());` between the wordpress loop of your theme templates such as category.php or single.php

Note: This plugin will create two tables in your wordpress database

== Frequently Asked Questions ==

Please check it out my blog http://www.wpclue.com/development/plugins/wordpress-voting-plugin/

== Screenshots ==

1. Sample vote count and voting button (logged in user)
2. After vote or post author view
3. Default alert message for un-logged in user
4. Sample admin voting logs
5. Admin voting options panel

== Changelog ==

= 1.0 =
* The first release of the plugin.

= 1.1 =
* Fixed broken images

= 1.2 =
* Added allow or disallow post author to vote his own posts feature

= 1.2.1 =
* Fixed voting on/off options not getting selected state after initial plugin activate
* Fixed allow post author to vote his own posts options not getting selected state after initial plugin activate
* Fixed allow post author to vote his own posts options not getting deleted after deactivate the plugin

= 1.3 =
* Added custom vote text feature
* Added custom voted text feature
* Added custom vote button feature
* Added custom voted button feature

= 1.4 =
* Added shortcode support
* Tested compability with WordPress 3.2.1

= 1.4.1 =
* Fixed the broken admin css

= 1.4.2 =
* Added reset all voting

= 1.5 =
* Added total vote count widget
* Re-design admin voting options page

= 1.5.1 =
* Added db version to options tbl for next release (public voting)

= 1.6 =
* Added public voting feature (unregistered / non-logged in users voting)

= 1.7 =
* Added top vote widget
* Add safe-guard javascript pop-up confirmation box for "Reset All" button
* Implemented proper un-installation (Now deactivate won't delete db tables and settings)
* Bug fix (two or more users voting from same computer can't vote)