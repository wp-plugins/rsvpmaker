=== RSVPmaker ===
Contributors: davidfcarr
Donate: http://www.rsvpmaker.com
Tags: event, calendar, rsvp, custom post type, paypal
Requires at least: 3.0
Tested up to: 3.03
Stable tag: 0.7.2

Event scheduling and RSVP tracking.

== Description ==

RSVPmaker is an event scheduling and RSVP tracking plugin for WordPress, using the custom post types feature introduced in WP3.0 to track events as an alternate post type with associated dates.

Site editors and administrators have the option to request RSVPs for any given event and specify an email address for notifications when someone responds. RSVP Reports can also be run from the administrator's dashboard.

If a fee is to be charged for the event, RSVPMaker can collect payments online using PayPal (requires manual setup of a PayPal account and creation of a configuration file with API credentials).

[__RSVPmaker.com__](http://www.rsvpmaker.com/)

Also available at [__RSVPmaker.com__](http://www.rsvpmaker.com/): a prototype of ChimpBlast plugin for sending event invites and other email broadcasts through the MailChimp broadcast email service.

== Installation ==

1. Upload the entire `rsvpmaker` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Visit the RSVPmaker options page to configure default values for RSVP email notifications, etc.
1. Use the shortcodes documented on the options page to create an events listing page, or a list of event headlines for the home page. Use the RSVPMaker widget to an events listing to your WordPress sidebar.

For basic usage, you can also have a look at the [plugin homepage](http://www.rsvpmaker.com/).

== Frequently Asked Questions ==

Do you have questions or issues with RSVPmaker? Use these support channels appropriately.

1. [Docs](http://www.rsvpmaker.com/)

[Support](http://www.rsvpmaker.com/)

== Screenshots ==

1. screenshot-1.png

== Credits ==

    RSVPmaker
    Copyright (C) 2010 David F. Carr

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    See the GNU General Public License at <http://www.gnu.org/licenses/>.
	
	RSVPMaker also includes code derived from the PayPal NVP API software
	development kit for PHP.

== Changelog ==

= 0.7.1 =

Bug fix, RSVP Reports

= 0.7.1 =

Bug fix, tweak to register post type configuration

= 0.7 =

* Custom post type slug changed from 'event' to 'rsvpmaker' in an attempt to avoid name conflicts, permalink issues.
* Widget now lets you set the # of posts to display and date format string

= 0.6.2 =

* Updated to WP 3.03
* Addition of event type taxonomy

= 0.6.1 =

* Fixed errors in database code for recording guests and payments
* Added option to switch between 12-hour and 24-hour time formats
* Added ability to set maximum participants per event.

= 0.6 =

* First public release November 2010.

[Releases](http://www.rsvpmaker.com/release/)