<?php

/*
Plugin Name: RSVPMaker
Plugin URI: http://www.rsvpmaker.com
Description: Schedule events and solicit RSVPs. Editor built around the custom post types feature introduced in WP 3.0, so you get all your familiar post editing tools with a few extra options for setting dates and RSVP options. PayPal payments can be added with a little extra configuration. <a href="options-general.php?page=rsvpmaker-admin.php">Options</a> / <a href="edit.php?post_type=rsvpmaker&page=rsvpmaker_doc">Shortcode documentation</a>
Author: David F. Carr
Version: 0.7.6
Author URI: http://www.carrcommunications.com
*/

global $wp_version;

if (version_compare($wp_version,"3.0","<"))
	exit( __("RSVPmaker plugin requires WordPress 3.0 or greater",'rsvpmaker') );

global $pagenow;
global $post;
if(($_GET["post_type"] == 'rsvpmaker') || ($post->post_type == 'rsvpmaker') || ($pagenow == 'plugins.php') )
	{
	//if we're on the admin page for adding a new event, or are editing or viewing an event, or are on the plugins activation page
	$locale = get_locale();
	$mofile = WP_PLUGIN_DIR . '/rsvpmaker/translation/rsvpmaker-' . $locale . '.mo';
	load_textdomain('rsvpmaker',$mofile);
	}

$rsvp_options = get_option('RSVPMAKER_Options');

//defaults
if(!$rsvp_options["rsvp_to"])
	$rsvp_options["rsvp_to"] = get_bloginfo('admin_email');
if(!$rsvp_options["rsvp_confirm"])
	$rsvp_options["rsvp_confirm"] = __('Thank you!','rsvpmaker');
if(!$rsvp_options['dates_style'])
	$rsvp_options['dates_style'] = 'padding-top: 1em; padding-bottom: 1em; font-weight: bold;';
if(!$rsvp_options['rsvplink'])
	$rsvp_options['rsvplink'] = '<p><a style="width: 8em; display: block; border: medium inset #FF0000; text-align: center; padding: 3px; background-color: #0000FF; color: #FFFFFF; font-weight: bolder; text-decoration: none;" class="rsvplink" href="%s?e=*|EMAIL|*#rsvpnow">'. __('RSVP Now!','rsvpmaker').'</a></p>';
if(!$rsvp_options['defaulthour'])
	{
	$rsvp_options['defaulthour'] = 19;
	$rsvp_options['defaultmin'] = 0;
	}
if(!$rsvp_options["long_date"])
	$rsvp_options["long_date"] = 'l F jS, Y';
if(!$rsvp_options["short_date"])
	$rsvp_options["short_date"] = 'F jS';
if(!$rsvp_options["time_format"])
	$rsvp_options["time_format"] = 'g:i A';

if(file_exists(WP_PLUGIN_DIR."/rsvpmaker-custom.php") )
	include WP_PLUGIN_DIR."/rsvpmaker-custom.php";

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
    'rewrite' => false, 
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_position' => 5,
    'supports' => array('title','editor','author','excerpt'),
	'taxonomies' => array('rsvpmaker-type')
    )
  );

// explicit rewrite function seems to work better than letting WP do it automatically
global $wp_rewrite;
$rsvpmaker_structure = '/rsvpmaker/%rsvpmaker%';
$wp_rewrite->add_rewrite_tag("%rsvpmaker%", '([^/]+)', "rsvpmaker=");
$wp_rewrite->add_permastruct('rsvpmaker', $rsvpmaker_structure, false);

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

//make sure new rules will be generated for custom post type
if($wp_rewrite->extra_permastructs["rsvpmaker"][0] != '/rsvpmaker/%rsvpmaker%')
	flush_rewrite_rules();

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