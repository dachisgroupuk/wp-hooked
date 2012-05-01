=== DG - Fix for custom post type pagination ===
Tags: pagination, custom post types
Contributors: Ross Tweedie
Requires at least: 3.2
Tested up to: 3.3.1
Stable Tag: 1.0

Fixes an issue with paginating over custom post types in WordPress

== Description ==

Fixes a bug in WordPress 3 ( possibly in lower versions ) when viewing a category listing page pagination, consisting of custom post types, when using the permalink structure %category%/%postname%. After the first page, the URL will be something like cat-name/page/2 and will return a 404 error page. The problem is that the request is only checking for posts with post type ‘post’, within the specified category. By adding all custom post types to the request, pagination will work as expected.

Adapted from http://www.ballyhooblog.com/add-custom-post-types-wordpress-main-feed/

= Features =

* Ensure that custom post_types can be paginated

== Changelog ==

= 1.0 =
* Initial release.


= Credits =
This plugin is adapted from http://www.ballyhooblog.com/add-custom-post-types-wordpress-main-feed

== Installation ==

1. Upload the plugin to your plugins folder: 'wp-content/plugins/'
3. Activate the 'DG - Fix for custom post type pagination' plugin from the Plugins admin panel.

== License ==
All contents under the wp-minify/min/ directory is licensed under
[New BSD License](http://www.opensource.org/licenses/bsd-license.php) (which is
[GPL](http://www.gnu.org/copyleft/gpl.html) compatible).  All other
contents within this package is licensed under GPLv3.
