=== RSVPmaker ===
Contributors: davidfcarr
Donate: http://www.rsvpmaker.com
Tags: event, calendar, rsvp, custom post type, paypal
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 0.8

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
1. See the documentation for shortcodes you can use to create an events listing page, or a list of event headlines for the home page. Use the RSVPMaker widget if you would like to add an events listing to your WordPress sidebar.
1. OPTIONAL: Depending on your theme, you may want to create a single-rsvpmaker.php template to prevent confusion between the post date and the event date (move the post date display code to the bottom or just remove it). A sample for the Twentyten theme is included with this distribution.
1. OPTIONAL: To enable online payments for events, obtain a PayPal API signature and password, edit the included paypal-constants.php file, and upload it (ideally to a location outside of web root). Record the file location on the settings screen.
1. OPTIONAL: You can override any of the functions in rsvpmaker-pluggable.php by creating your own rsvpmaker-custom.php file and adding it to the plugins directory (the directory above the rsvpmaker folder). You can, for example, override the function that displays the RSVP form to include more, fewer, or different fields.

For basic usage, you can also have a look at the [plugin homepage](http://www.rsvpmaker.com/).

== Screenshots ==

1. screenshot-1.png
1. screenshot-2.png

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

= 0.8 =

* Added type parameter for shortcode so you can display only events tagged with "featured" or another event type using `[rsvpmaker_upcoming type="featured"]`
* Added ability to set RSVP start date as well as deadline for RSVPs
* If signing up workers or volunteers for specific timeslots, you can now specify the duration of the timeslots in one-hour increments
* Cleaned up Event Dates, RSVP Options box in editor, moving less commonly used parameters to the bottom.
* Added a Tweak Permalinks setting (a hack for a few users who have reported "page not found" errors, possibly because some other plugin is overwriting the RSVPmaker rewrite rules).
* Tested with WP 3.1 release candidate

= 0.7.6 =

Fixed issue with setting default options.

= 0.7.5 =

Improved ability to add a series of recuring events, including ability for software to calculate the dates based on a schedule like "Second Tuesday of the month"

= 0.7.4 =

Bug fix to prevent customizations from being overwritten. Custom functions should be placed in rsvpmaker-custom.php and the file must be installed in the plugins directory above the rsvpmaker folder: wp-content/plugins/ instead of wp-content/plugins/rsvpmaker/

= 0.7.3 =

* Updated code for displaying RSVP Reports. Added functionality for deleting entries.
* Beginning to introduce translation support. See translations directory for rsvp.pot file to be used by translators.

= 0.7.2 =

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