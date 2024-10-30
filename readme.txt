=== Plugin Name ===
Contributors: Speedboxer
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=991603
Tags: comments, notification, moderation
Requires at least: 2.6
Tested up to: 2.7
Stable tag: 1.1.1

Sends an email to the comment author when their comment gets approved (only if it's held for moderation).

== Description ==

The Comment Approval Notification plugin will send an email to the comment author when you approve their comment. The email sent is fully customizable, with shortcodes, through a settings page.

Please note that this plugin has only been tested with PHP 5.2.x.

A full list of available shortcodes is available [here](http://mattsblog.ca/plugins/comment-approval-notification/#shortcodes "Available Shortcodes").

== Installation ==

To install this plugin, please follow these instructions:

1. Download and extract the ZIP archive.
1. Upload `comment-approval-notification.php` to your `wp-content/plugins/` folder.
1. In your WordPress administrative panel, go to Plugins, scroll down to **Comment Approval Notification** and click *Activate* to the right of it.
1. Customize the email sent under Settings > Comment Approval Notification. A full list of available shortcodes is available [here](http://mattsblog.ca/plugins/comment-approval-notification/#shortcodes "Available Shortcodes").

== Changelog ==

= 1.1.1 =
* Fix WordPress 2.6 compatibility

= 1.1 =
* Stop using shortcode API (shortcodes are still available, don't worry)
* Fix PHP warning
* Add WordPress 2.6 compatibility

= 1.0.1 =
* Remove _[comment\_date\_gmt]_ and _[post\_date\_gmt]_
* Use date\_format option on _[comment\_date]_ and _[post\_date]_ and only show the date
* Add _[comment\_time]_ and _[post\_time]_ formatted with time\_format option

= 1.0 =
* Initial release
