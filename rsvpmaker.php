<?php

/*
Plugin Name: RSVPMaker
Plugin URI: http://www.rsvpmaker.com
Description: Schedule events and solicit RSVPs. The editor is built around the custom post types feature introduced in WP 3.0, so you get all your familiar post editing tools with a few extra options for setting dates and RSVP options. PayPal payments can be added with a little extra configuration. <a href="options-general.php?page=rsvpmaker-admin.php">Options</a> / <a href="edit.php?post_type=rsvpmaker&page=rsvpmaker_doc">Shortcode documentation</a>. Note that if you delete RSVPMaker from the control panel, all associated data will be deleted automatically including contact info of RSVP respondents. To delete data more selectively, use the <a href="/wp-content/plugins/rsvpmaker/cleanup.php">cleanup utility</a> in the plugin directory.
Author: David F. Carr
Version: 2.4.1
Author URI: http://www.carrcommunications.com
*/

global $wp_version;

if (version_compare($wp_version,"3.0","<"))
	exit( __("RSVPmaker plugin requires WordPress 3.0 or greater",'rsvpmaker') );

$locale = get_locale();

$mofile = WP_PLUGIN_DIR . '/rsvpmaker/translations/rsvpmaker-' . $locale . '.mo';

load_textdomain('rsvpmaker',$mofile);

$rsvp_options = get_option('RSVPMAKER_Options');

//defaults
if(!$rsvp_options["menu_security"])
	$rsvp_options["menu_security"] = 'manage_options';
if(!$rsvp_options["rsvp_to"])
	$rsvp_options["rsvp_to"] = get_bloginfo('admin_email');
if(!$rsvp_options["rsvp_confirm"])
	$rsvp_options["rsvp_confirm"] = __('Thank you!','rsvpmaker');
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
if(!$rsvp_options["profile_table"])
	$rsvp_options["profile_table"] = '
<table border="0" cellspacing="0" cellpadding="0"> 
<tr> 
<td width="100">'.__('Phone','rsvpmaker').':</td> 
<td>
	<input name="profile[phone]" type="text" id="phone" size="20" value="" />
</td> 
</tr> 
<tr> 
<td>'.__('Phone Type','rsvpmaker').':</td> 
<td> 
  <select name="profile[phonetype]" id="phonetype"> 
	<option> 
	</option> 
	<option>'.__('Work Phone','rsvpmaker').'</option> 
	<option>'.__('Mobile Phone','rsvpmaker').'</option> 
	<option>'.__('Home Phone','rsvpmaker').'</option> 
  </select></td> 
</tr> 
</table>';
if(!$rsvp_options["paypal_currency"])
	$rsvp_options["paypal_currency"] = 'USD';
if(!$rsvp_options["currency_decimal"])
	$rsvp_options["currency_decimal"] = '.';
if(!$rsvp_options["currency_thousands"])
	$rsvp_options["currency_thousands"] = ',';

if(file_exists(WP_PLUGIN_DIR."/rsvpmaker-custom.php") )
	include WP_PLUGIN_DIR."/rsvpmaker-custom.php";

include WP_PLUGIN_DIR."/rsvpmaker/rsvpmaker-admin.php";
include WP_PLUGIN_DIR."/rsvpmaker/rsvpmaker-display.php";
include WP_PLUGIN_DIR."/rsvpmaker/rsvpmaker-plugabble.php";

add_action( 'init', 'rsvpmaker_create_post_type' );

function rsvpmaker_create_post_type() {

  register_post_type( 'rsvpmaker',
    array(
      'labels' => array(
        'name' => __( 'RSVP Events' ),
        'add_new_item' => __( 'Add New Event' ),
        'edit_item' => __( 'Edit Event' ),
        'new_item' => __( 'Events' ),
        'singular_name' => __( 'Event' )
      ),
    'menu_icon' => plugins_url('/calendar.png',__FILE__),
	'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'query_var' => true,
    'rewrite' => array( 'slug' => 'rsvpmaker','with_front' => FALSE), 
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_position' => 5,
    'supports' => array('title','editor','author','excerpt','custom-fields'),
	'taxonomies' => array('rsvpmaker-type','post_tag')
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

//tweak for users who report "page not found" errors - flush rules on every init
global $rsvp_options;
if($rsvp_options["flush"])
	flush_rewrite_rules();

}

//make sure new rules will be generated for custom post type - flush for admin but not for regular site visitors
if(!$rsvp_options["flush"])
	add_action('admin_init','flush_rewrite_rules');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
dbDelta($sql);

$sql = "CREATE TABLE `".$wpdb->prefix."rsvpmaker` (
  `id` int(11) NOT NULL auto_increment,
  `email` varchar(255)   CHARACTER SET utf8 COLLATE utf8_general_ci  default NULL,
  `yesno` tinyint(4) NOT NULL default '0',
  `first` varchar(255)  CHARACTER SET utf8 COLLATE utf8_general_ci  NOT NULL default '',
  `last` varchar(255)  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `details` text  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `event` int(11) NOT NULL default '0',
  `owed` float(6,2) NOT NULL default '0.00',
  `amountpaid` float(6,2) NOT NULL default '0.00',
  `master_rsvp` int(11) NOT NULL default '0',
  `guestof` varchar(255)   CHARACTER SET utf8 COLLATE utf8_general_ci  default NULL,
  `note` text   CHARACTER SET  utf8 COLLATE utf8_general_ci NOT NULL,
  `participants` INT NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
dbDelta($sql);

$sql = "CREATE TABLE `".$wpdb->prefix."rsvp_volunteer_time` (
  `id` int(11) NOT NULL auto_increment,
  `event` int(11) NOT NULL default '0',
  `rsvp` int(11) NOT NULL default '0',
  `time` int(11) default '0',
  `participants` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
dbDelta($sql);

$rsvp_options["dbversion"] = 4;
update_option('RSVPMAKER_Options',$rsvp_options);

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

register_activation_hook( __FILE__, 'cpevent_activate' );

//upgrade database if necessary
if($rsvp_options["dbversion"] < 4)
	{
	cpevent_activate();
	//correct character encoding error in early releases
	global $wpdb;
	$wpdb->query("ALTER TABLE `wp_rsvpmaker` CHANGE `first` `first` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''");
	$wpdb->query("ALTER TABLE `wp_rsvpmaker` CHANGE `last` `last` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''");
	$wpdb->query("ALTER TABLE `wp_rsvpmaker` CHANGE `email` `email` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''");	
	$wpdb->query("ALTER TABLE `wp_rsvpmaker` CHANGE `guestof` `guestof` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''");	
	$wpdb->query("ALTER TABLE `wp_rsvpmaker` CHANGE `details` `details` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ");	
	$wpdb->query("ALTER TABLE `wp_rsvpmaker` CHANGE `note` `note` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ");
	}

?>