<?php

/*
Plugin Name: RSVPMaker
Plugin URI: http://www.rsvpmaker.com
Description: Schedule events and solicit RSVPs. Editor built around the custom post types feature introduced in WP 3.0, so you get all your familiar post editing tools with a few extra options for setting dates and RSVP options. PayPal payments can be added with a little extra configuration. <a href="options-general.php??page=rsvpmaker-admin.php">Options / Shortcode documentation</a>
Author: David F. Carr
Version: 0.7.1
Author URI: http://www.carrcommunications.com
*/

global $wp_version;

if (version_compare($wp_version,"3.0","<"))
	exit("RSVPmaker plugin requires WordPress 3.0 or greater");

$rsvp_options = get_option('RSVPMAKER_Options');

//defaults
if(!$rsvp_options)
	$rsvp_options = array('rsvp_to' => get_bloginfo('admin_email'), 'rsvp_confirm' => 'Thank you!','default_content' =>'', 'event_headline' => '<li style="list-style: none;"><a style="color: #0033FF;" href="[permalink]">[title]</a> [dates] </li>', 'dates_style' => 'padding-top: 1em; padding-bottom: 1em; font-weight: bold;','rsvplink' => '<p><a style="width: 8em; display: block; border: medium inset #FF0000; text-align: center; padding: 3px; background-color: #0000FF; color: #FFFFFF; font-weight: bolder; text-decoration: none;" class="rsvplink" href="%s?e=*|EMAIL|*#rsvpnow">RSVP Now!</a></p>','rsvp_on' => 0, 'paypal_enabled' => 0, 'time_format' => 'g:i A', 'defaulthour' => 19, 'defaultmin' => 0);

if(file_exists(WP_PLUGIN_DIR."/rsvpmaker/custom.php") )
	include WP_PLUGIN_DIR."/rsvpmaker/custom.php";

include WP_PLUGIN_DIR."/rsvpmaker/rsvpmaker-admin.php";
include WP_PLUGIN_DIR."/rsvpmaker/rsvpmaker-display.php";
include WP_PLUGIN_DIR."/rsvpmaker/rsvpmaker-plugabble.php";

add_action( 'init', 'create_post_type' );

function create_post_type() {

  register_post_type( 'rsvpmaker',
    array(
      'labels' => array(
        'name' => __( 'RSVP Events' ),
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
    'rewrite' => array( 'slug' => 'rsvpmaker', 'with_front' => true ),
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_position' => 5,
    'supports' => array('title','editor','author','excerpt'),
	'taxonomies' => array('rsvpmaker-type')
    )
  );


  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name' => _x( 'Event Types', 'taxonomy general name' ),
    'singular_name' => _x( 'Event Type', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Event Types' ),
    'all_items' => __( 'All Event Types' ),
    'parent_item' => __( 'Parent Event Type' ),
    'parent_item_colon' => __( 'Parent Event Type:' ),
    'edit_item' => __( 'Edit Event Type' ), 
    'update_item' => __( 'Update Event Type' ),
    'add_new_item' => __( 'Add New Event Type' ),
    'new_item_name' => __( 'New Event Type' ),
    'menu_name' => __( 'Event Type' ),
  ); 	

  register_taxonomy('rsvpmaker-type',array('rsvpmaker'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'rsvpmaker-type' ),
  ));


global $wpdb;
$sql = "SELECT slug FROM ".$wpdb->prefix."terms JOIN `".$wpdb->prefix."term_taxonomy` on ".$wpdb->prefix."term_taxonomy.term_id= ".$wpdb->prefix."terms.term_id WHERE taxonomy='rsvpmaker-type' AND slug='featured'";

if(! $wpdb->get_var($sql) )
	{
	wp_insert_term(
  'Featured', // the term 
  'rsvpmaker-type', // the taxonomy
  array(
    'description'=> 'Featured event. Can be used to put selected events in a listing, for example on the home page',
    'slug' => 'featured'
  )
);
	}

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