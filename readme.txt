=== Revive To Sky - Post old content to Bluesky ===
Contributors: rhyswynne
Tags: social media, bluesky, automation, content sharing, traffic generation, syndication
Requires at least: 5.8
Tested up to: 6.8
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically syndicate your old blog posts to Bluesky on a regular basis, increasing traffic and engagement automatically.

== Description ==

Revive To Sky is a WordPress plugin that helps you automatically share your old blog posts to [Bluesky](https://bsky.app/), helping you increase traffic and engagement on your website. The plugin runs on a scheduled basis, ensuring your content reaches new audiences without manual intervention.

= Key Features =

* Automatically shares old blog posts to Bluesky
* Configurable sharing schedule
* Customizable post format
* Image support for shared posts
* Easy setup and configuration

= Stay Updated =

Get notified about future updates and improvements by subscribing to [my newsletter](https://dwinrhys.com/newsletter/).

== Installation ==

You can install this plugin in two ways:

1. **WordPress Plugin Directory**:
   * Go to Plugins > Add New in your WordPress admin
   * Search for "Revive To Sky"
   * Click "Install Now" and then "Activate"

2. **Manual Installation**:
   * Upload the `revive-to-sky` folder to the `/wp-content/plugins/` directory
   * Activate the plugin through the 'Plugins' menu in WordPress

3. **Plugin Setup**
   * Go to the plugin settings page (Settings > Revive to Sky)
   * Enter your Bluesky App Password (get one from [here](https://bsky.app/settings/app-passwords))
   * Configure your sharing preferences:
     * Choose which categories and tags to include or exclude from sharing, giving you full control over which posts are eligible.
     * Customize the format of your shared post using template tags (e.g., `%%POSTTITLE%%` for the post title and `%%POSTURL%%` for the post URL) in the message template field.
   * Save your settings

== External services ==

The plugin connects to the following services and uses the following API's:-

= BlueSky =
This plugin connects to the Bluesky API to post messages to your Bluesky account. It will post the post title, URL to a post and a featured image, as well as any message you write, on your behalf - at intervals requested. It is needed to run the plugin. 

This service is provided by BlueSky Social, [Privacy Policy](https://bsky.social/about/support/privacy-policy), [Terms of Service](https://bsky.social/about/support/tos).

= Mailerlite =
This plugin connects to allow users to sign up for a newsletter to receive updates on the plugin from within the plugin's option page. Should you choose to, it will collect a name and email if you fill in the clearly defined form in the plugin's option page. The email list is a double opt in and you can unsubscribe at any time.

This service is provided by Mailerlite, [Privacy Policy](https://www.mailerlite.com/legal/privacy-policy), [Terms of Service](https://www.mailerlite.com/legal/terms-of-service).

= Gravatar =
This plugin connects to Gravatar to load an image of the plugin developer - Rhys Wynne - to put into the plugin's option page.

This service is provided by Gravatar, [Privacy Policy](https://support.gravatar.com/privacy-and-security/data-privacy/)

== Frequently Asked Questions ==

= How often will my posts be shared? =

By default, the plugin checks for posts to share hourly. You can adjust this in the plugin settings.

= Can I customize the format of shared posts? =

Yes, you can customize the post format in the plugin settings to include or exclude specific elements like title, excerpt, and link.

== Screenshots ==

1. Plugin settings page
2. Configuration options
3. Post sharing schedule

== Changelog ==

= 1.0.0 =
* Initial release
* Basic post sharing functionality
* Configurable sharing schedule
* Image support
* Custom post format options

== Upgrade Notice ==

= 1.0.0 =
Initial release of Revive To Sky

== Support ==

For urgent, paid support, please visit [dwinrhys.com/contact-me](https://dwinrhys.com/contact-me/).

For general support and questions, please use the [WordPress.org forums](https://wordpress.org/support/plugin/revive-to-sky/).
