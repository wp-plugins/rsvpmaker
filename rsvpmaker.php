<?php

/*
Plugin Name: RSVPMaker
Plugin URI: http://www.rsvpmaker.com
Description: Schedule events and solicit RSVPs. Editor built around the custom post types feature introduced in WP 3.0, so you get all your familiar post editing tools with a few extra options for setting dates and RSVP options. PayPal payments can be added with a little extra configuration. <a href="options-general.php??page=rsvpmaker-admin.php">Options / Shortcode documentation</a>
Author: David F. Carr
Version: 0.6.1
Author URI: http://www.carrcommunications.com
*/

global $wp_version;

if (version_compare($wp_version,"3.0","<"))
	exit("RSVPmaker plugin requires WordPress 3.0 or greater");

$rsvp_options = get_option('RSVPMAKER_Options');

if(file_exists(WP_PLUGIN_DIR."/rsvpmaker/custom.php") )
	include WP_PLUGIN_DIR."/rsvpmaker/custom.php";

include WP_PLUGIN_DIR."/rsvpmaker/rsvpmaker-admin.php";
include WP_PLUGIN_DIR."/rsvpmaker/rsvpmaker-display.php";
include WP_PLUGIN_DIR."/rsvpmaker/rsvpmaker-plugabble.php";

add_action( 'init', 'create_post_type' );

function create_post_type() {

  register_post_type( 'event',
    array(
      'labels' => array(
        'name' => __( 'Events' ),
        'add_new_item' => __( 'Add New Event' ),
        'edit_item' => __( 'Edit Event' ),
        'new_item' => __( 'Events' ),
        'singular_name' => __( 'Event' )
      ),
    'menu_icon' => WP_PLUGIN_URL.'/rsvpmaker/calendar.png',
	'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_position' => 5,
    'supports' => array('title','editor','author','excerpt')
    )
  );

/*
couldn't get this to work yet
,'taxonomies' => array('category')
*/

}

//make sure new rules will be generated for custom post type
add_action('admin_init', 'flush_rewrite_rules');

function cpevent_activate() {
global $wpdb;
global $rsvp_options;

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

$sql = "CREATE TABLE `".$wpdb->prefix."rsvp_dates` (
  `id` int(11) NOT NULL auto_increment,
  `postID` int(11) default NULL,
  `datetime` datetime default NULL,
  `duration` varchar(255) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
dbDelta($sql);

$sql = "CREATE TABLE `".$wpdb->prefix."rsvpmaker` (
  `id` int(11) NOT NULL auto_increment,
  `email` varchar(255) default NULL,
  `yesno` tinyint(4) NOT NULL default '0',
  `first` varchar(255) NOT NULL default '',
  `last` varchar(255) NOT NULL default '',
  `details` text NOT NULL,
  `event` int(11) NOT NULL default '0',
  `owed` float(6,2) NOT NULL default '0.00',
  `amountpaid` float(6,2) NOT NULL default '0.00',
  `master_rsvp` int(11) NOT NULL default '0',
  `guestof` varchar(255) default NULL,
  `note` text NOT NULL,
  `participants` INT NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
dbDelta($sql);

$sql = "CREATE TABLE `".$wpdb->prefix."rsvp_volunteer_time` (
  `id` int(11) NOT NULL auto_increment,
  `event` int(11) NOT NULL default '0',
  `rsvp` int(11) NOT NULL default '0',
  `time` int(11) default '0',
  `participants` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
dbDelta($sql);

$rsvp_options["dbversion"] = 2;
update_option('RSVPMAKER_Options',$rsvp_options);

}

register_activation_hook( __FILE__, 'cpevent_activate' );

//upgrade database if necessary
if($rsvp_options["dbversion"] < 2)
	cpevent_activate();
?>