=== WP Status Notifier ===
Contributors: iDope
Donate link: http://wordpresssupplies.com/
Tags: post, pending, review, status, publish, notification, email, collaboration, contributors, author
Requires at least: 2.3
Tested up to: 3.1.3
Stable tag: trunk

WP Status Notifier will send a notification to given email address(es) of posts pending review by contributors.

== Description ==

WP Status Notifier will send a notification to given email address(es) of posts pending review by contributors. It can also optionally notify the contributors when their post is accepted or rejected. WP Status Notifier is useful for moderation in blogs with multiple contributors.

The plugin will add an options page in the Wordpress administration area where you can set one or more email addresses to notified of posts pending review.

= Features =

1. No need to manually check posts waiting for approval.
2. Includes a link to edit and/or approve the post in the notification email.
3. Optionally notifies contributors of the moderation status.
4. The plugin requires no database access and adds almost zero overhead.
5. Simple configuration options. Doesn't require editing any .php files.

== Installation ==

1. Upload `status-notifier.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. To configure the plugin go to `Options>Status Notifications` in the Wordpress administration area

== Frequently Asked Questions ==

= Can you add a feature X? =

Depends, if its useful enough and I have time for it.


== Screenshots ==

1. Status notification options

== Changelog ==

= 1.3 =
* Fixed duplicate notifications when posts already submitted for review are saved again (Thanks Keith).
* Tested upto Wordpress 2.8.4
