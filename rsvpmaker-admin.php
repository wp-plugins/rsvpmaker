<?php

function draw_eventdates() {

global $post;
global $wpdb;
global $rsvp_options;

$defaulthour = ($rsvp_options["defaulthour"]) ? ( (int) $rsvp_options["defaulthour"]) : 19;
$defaultmin = ($rsvp_options["defaultmin"]) ? ( (int) $rsvp_options["defaultmin"]) : 0;

for($i=0; $i < 24; $i++)
	{
	$selected = ($i == $defaulthour) ? ' selected="selected" ' : '';
	$padded = ($i < 10) ? '0'.$i : $i;
	if($i == 0)
		$twelvehour = "12 a.m.";
	elseif($i == 12)
		$twelvehour = "12 p.m.";
	elseif($i > 12)
		$twelvehour = ($i - 12) ." p.m.";
	else		
		$twelvehour = $i." a.m.";

	$houropt .= sprintf('<option  value="%s" %s>%s / %s:</option>',$padded,$selected,$twelvehour,$padded);
	}

for($i=0; $i < 60; $i += 5)
	{
	$selected = ($i == $defaultmin) ? ' selected="selected" ' : '';
	$padded = ($i < 10) ? '0'.$i : $i;
	$minopt .= sprintf('<option  value="%s" %s>%s</option>',$padded,$selected,$padded);
	}

wp_nonce_field(-1,'add_date'.$i);

$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."rsvp_dates WHERE postID=".$post->ID.' ORDER BY datetime',ARRAY_A);
if($results)
{
$start = 2;
foreach($results as $row)
	{
	echo "\n<div class=\"event_dates\"> \n";
	$t = strtotime($row["datetime"]);
	echo date('l F jS, Y',$t);
	$dur = $row["duration"];
	if($dur != 'allday')
		echo date(' g:i A',$t);
	if(is_numeric($dur) )
		echo " to ".date ('g:i A',$dur);
	echo sprintf(' <input type="checkbox" name="delete_date[]" value="%d" /> Delete',$row["id"]);
	echo "</div>\n";
	}
}

echo '<p><em>Enter one or more dates. For an event starting at 12:30 p.m., you would select 12 p.m. and 30 minutes. Specifying the duration is optional.</em> </p>';

if(!$start)
	$start = 1;

for($i=$start; $i < 6; $i++)
{
if($i == 2)
	{
	echo "<p><a onclick=\"document.getElementById('additional_dates').style.display='block'\" >Add More Dates</a> </p>
	<div id=\"additional_dates\" style=\"display: none;\">";
	}

if($i > 1)
	$today = '<option value="">None</option>';
else
	{
	$d = date('j');
	$today = sprintf('<option value="%s">%s</option>',$d,$d);
	}

$m = date('n');
$y = date('Y');
$y2 = $y+1;

?>
<div id="event_date<?=$i?>" style="border-bottom: thin solid #888;">
<table width="100%">
<tr>
            <td width="32">Date:</td> 
            <td width="*"><div id="date_block">Month: 
              <select name="event_month[<?=$i?>]"> 
              <option value="<?=$m?>"><?=$m?></option> 
              <option value="1">1</option> 
              <option value="2">2</option> 
              <option value="3">3</option> 
              <option value="4">4</option> 
              <option value="5">5</option> 
              <option value="6">6</option> 
              <option value="7">7</option> 
              <option value="8">8</option> 
              <option value="9">9</option> 
              <option value="10">10</option> 
              <option value="11">11</option> 
              <option value="12">12</option> 
              </select> 
            Day 
            <select name="event_day[<?=$i?>]"> 
              <?=$today?> 
              <option value="1">1</option> 
              <option value="2">2</option> 
              <option value="3">3</option> 
              <option value="4">4</option> 
              <option value="5">5</option> 
              <option value="6">6</option> 
              <option value="7">7</option> 
              <option value="8">8</option> 
              <option value="9">9</option> 
              <option value="10">10</option> 
              <option value="11">11</option> 
              <option value="12">12</option> 
              <option value="13">13</option> 
              <option value="14">14</option> 
              <option value="15">15</option> 
              <option value="16">16</option> 
              <option value="17">17</option> 
              <option value="18">18</option> 
              <option value="19">19</option> 
              <option value="20">20</option> 
              <option value="21">21</option> 
              <option value="22">22</option> 
              <option value="23">23</option> 
              <option value="24">24</option> 
              <option value="25">25</option> 
              <option value="26">26</option> 
              <option value="27">27</option> 
              <option value="28">28</option> 
              <option value="29">29</option> 
              <option value="30">30</option> 
              <option value="31">31</option> 
            </select> 
            Year
            <select name="event_year[<?=$i?>]"> 
              <option value="<?=$y?>"><?=$y?></option> 
              <option value="<?=$y2?>"><?=$y2?></option> 
            </select> 
</div> 
            </td> 
          </tr> 
<tr><td> Time:</td> 
<td>Hour: <select name="event_hour[<?=$i?>]"> 
<?=$houropt?>
</select> 
 
Minutes: <select name="event_minutes[<?=$i?>]"> 
<?=$minopt?>
</select> -

Duration <select name="event_duration[<?=$i?>]">
<option value="">Not set (optional)</option>
<option value="allday">All day/don't show time in headline</option>
<?php for($h = 1; $h < 24; $h++) { ?>
<option value="<?=$h?>"><?=$h?> hours</option>
<option value="<?=$h?>:15"><?=$h?>:15</option>
<option value="<?=$h?>:30"><?=$h?>:30</option>
<option value="<?=$h?>:45"><?=$h?>:45</option>
<?php } ?>
</select>
<br /> 
</td> 
          </tr> 
</table>
</div>
<?php
} // end for loop
echo "\n</div><!--add dates-->\n";

GetRSVPAdminForm($post->ID);

}

function my_events_menu() {

add_meta_box( 'EventDatesBox', 'Event Dates, RSVP Options', 'draw_eventdates', 'event', 'normal', 'high' );

}

function save_calendar_data($postID) {
if($_POST["event_month"])
	{
	global $wpdb;
	if($parent_id = wp_is_post_revision($postID))
		{
		$postID = $parent_id;
		}

	foreach($_POST["event_year"] as $index => $year)
		{
		if($_POST["event_day"][$index] )
			{
			$cddate = $year . "-" . $_POST["event_month"][$index]  . "-" . $_POST["event_day"][$index] . " " . $_POST["event_hour"][$index] . ":" . $_POST["event_minutes"][$index] . ":00";
			if( $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."rsvp_dates WHERE postID=$postID AND datetime='$cddate' ") )
				continue;
			
			$dpart = explode(':',$_POST["event_duration"][$index]);			
			
			if( is_numeric($dpart[0]) )
				{
				$dpart = explode(':',$_POST["event_duration"][$index]);			
				$hour = $_POST["event_hour"][$index] + $dpart[0];
				$minutes = $_POST["event_minutes"][$index] + $dpart[1];
				$duration = mktime( $hour, $minutes,0,$_POST["event_month"][$index],$_POST["event_day"][$index],$year);
				}
			else
				$duration = $_POST["event_duration"][$index]; // empty or all day
				
			$sql = " SET datetime='$cddate',duration='$duration', postID=". $postID;
			
			
			if($_POST["custom_post_dates_id"][$index])
				$sql = "UPDATE ".$wpdb->prefix."rsvp_dates $sql WHERE id=". (int) $_POST["custom_post_dates_id"][$index]; 
			else
				$sql = "INSERT INTO ".$wpdb->prefix."rsvp_dates $sql"; 
			
			$wpdb->query($sql);
			}
		}

	
	if($_POST["delete_date"])
		{
		foreach($_POST["delete_date"] as $id)
			{
			$dsql = "DELETE FROM ".$wpdb->prefix."rsvp_dates WHERE id=$id";
			$wpdb->query($dsql);
			}
		}
	
	if($_POST["setrsvp"]["on"])
		{
		$setrsvp = $_POST["setrsvp"];
		
		if($_POST["deadyear"] && $_POST["deadmonth"] && $_POST["deadday"])
			$setrsvp["deadline"] = strtotime($_POST["deadyear"].'-'.$_POST["deadmonth"].'-'.$_POST["deadday"].' 23:59:59');
		foreach($setrsvp as $name => $value)
			{
			$field = '_rsvp_'.$name;
			$single = true;
			$current = get_post_meta($postID, $field, $single);
			 
			if($value && ($current == "") )
				add_post_meta($postID, $field, $value, true);
			
			elseif($value != $current)
				update_post_meta($postID, $field, $value);
			
			elseif($value == "")
				delete_post_meta($postID, $field, $current);
			}
		
		if($_POST["unit"])
			{
			foreach($_POST["unit"] as $index => $value)
				{
				if($value && $_POST["price"][$index])
					{
					$per["unit"][$index] = $value;
					$per["price"][$index] = $_POST["price"][$index];
					}
				}	
			
			$value = $per;
			$field = "_per";
			
			$current = get_post_meta($postID, $field, $single); 
			
			if($value && ($current == "") )
				add_post_meta($postID, $field, $value, true);
			
			elseif($value != $current)
				update_post_meta($postID, $field, $value);
			
			elseif($value == "")
				delete_post_meta($postID, $field, $current);

			
			}
		}
	else
		delete_post_meta($postID, '_rsvp_on', '1');
	
	
	}
}

add_action('admin_menu', 'my_events_menu');

add_action('save_post','save_calendar_data');
  
  // Avoid name collisions.
  if (!class_exists('RSVPMAKER_Options'))
      : class RSVPMAKER_Options
      {
          // this variable will hold url to the plugin  
          var $plugin_url;
          
          // name for our options in the DB
          var $db_option = 'RSVPMAKER_Options';
          
          // Initialize the plugin
          function RSVPMAKER_Options()
          {
              $this->plugin_url = trailingslashit( WP_PLUGIN_URL.'/'. dirname( plugin_basename(__FILE__) ) );

              // add options Page
              add_action('admin_menu', array(&$this, 'admin_menu'));
              
          }
          
          // hook the options page
          function admin_menu()
          {
              add_options_page('RSVPMAKER', 'RSVPMAKER', 5, basename(__FILE__), array(&$this, 'handle_options'));
          }
          
          
          // handle plugin options
          function get_options()
          {
              // default values
              $options = array('rsvp_to' => get_bloginfo('admin_email'), 'rsvp_confirm' => 'Thank you!','default_content' =>'', 'event_headline' => '<li style="list-style: none;"><a style="color: #0033FF;" href="[permalink]">[title]</a> [dates] </li>', 'dates_style' => 'padding-top: 1em; padding-bottom: 1em; font-weight: bold;','rsvplink' => '<p><a style="width: 8em; display: block; border: medium inset #FF0000; text-align: center; padding: 3px; background-color: #0000FF; color: #FFFFFF; font-weight: bolder; text-decoration: none;" class="rsvplink" href="%s?e=*|EMAIL|*">RSVP Now!</a></p>','rsvp_on' => 0, 'paypal_enabled' => 0, time_format => 'g:i A', 'defaulthour' => 19, 'defaultmin' => 0);
              
              // get saved options
              $saved = get_option($this->db_option);
              
              // assign them
              if (!empty($saved)) {
                  foreach ($saved as $key => $option)
                      $options[$key] = $option;
              }
              
              // update the options if necessary
              if ($saved != $options)
                  update_option($this->db_option, $options);
              
              //return the options  
              return $options;
          }
          
          // Set up everything
          function install()
          {
              // set default options
              $this->get_options();
          }
          
          // handle the options page
          function handle_options()
          {
              $options = $this->get_options();
              
              if (isset($_POST['submitted'])) {
              		
              		//check security
              		check_admin_referer('calendar-nonce');
              		
                  $newoptions = stripslashes_deep($_POST["option"]);
                  $newoptions["rsvp_on"] = ($_POST["option"]["rsvp_on"]) ? 1 : 0;
                  $newoptions["paypal_enabled"] = ($_POST["option"]["paypal_enabled"]) ? 1 : 0;
				  $newoptions["dbversion"] = $options["dbversion"]; // gets set by db upgrade routine
				  $options = $newoptions;
				  
                  update_option($this->db_option, $options);
                  
                  echo '<div class="updated fade"><p>Plugin settings saved.</p></div>';
              }
              
              // URL for form submit, equals our current page
              $action_url = $_SERVER['REQUEST_URI'];


$defaulthour = ($options["defaulthour"]) ? ( (int) $options["defaulthour"]) : 19;
$defaultmin = ($options["defaultmin"]) ? ( (int) $options["defaultmin"]) : 0;

for($i=0; $i < 24; $i++)
	{
	$selected = ($i == $defaulthour) ? ' selected="selected" ' : '';
	$padded = ($i < 10) ? '0'.$i : $i;
	if($i == 0)
		$twelvehour = "12 a.m.";
	elseif($i == 12)
		$twelvehour = "12 p.m.";
	elseif($i > 12)
		$twelvehour = ($i - 12) ." p.m.";
	else		
		$twelvehour = $i." a.m.";

	$houropt .= sprintf('<option  value="%s" %s>%s / %s:</option>',$padded,$selected,$twelvehour,$padded);
	}

for($i=0; $i < 60; $i += 5)
	{
	$selected = ($i == $defaultmin) ? ' selected="selected" ' : '';
	$padded = ($i < 10) ? '0'.$i : $i;
	$minopt .= sprintf('<option  value="%s" %s>%s</option>',$padded,$selected,$padded);
	}


?>

<div class="wrap" style="max-width:950px !important;">
	<h2>Calendar Options</h2>
				
	<div id="poststuff" style="margin-top:10px;">

	 <div id="mainblock" style="width:710px">
	 
		<div class="dbx-content">
		 	<form name="caldendar_options" action="<?php echo $action_url ?>" method="post">
					
                    <input type="hidden" name="submitted" value="1" /> 
					<?php wp_nonce_field('calendar-nonce'); ?>

					<h3>Default Content for Events (such as standard meeting location):</h3>
  <textarea name="option[default_content]"  rows="5" cols="80" id="default_content"><?=$options["default_content"]?></textarea>
	<br />
Hour: <select name="option[defaulthour]"> 
<?=$houropt?>
</select> 
 
Minutes: <select name="option[defaultmin]"> 
<?=$minopt?>
</select>
<br />

					
					<h3>RSVP On:</h3>
  <input type="checkbox" name="option[rsvp_on]" value="1" <?php if($options["rsvp_on"]) echo ' checked="checked" '; ?> /> check to turn on by default
	<br />
					<h3>RSVP TO:</h3> 
					  <textarea rows="2" cols="80" name="option[rsvp_to]" id="rsvp_to"><?=$options["rsvp_to"]?></textarea>
					<br />
					<h3>Instructions for Form:</h3>
  <textarea name="option[rsvp_instructions]"  rows="5" cols="80" id="rsvp_instructions"><?=$options["rsvp_instructions"]?></textarea>
	<br />
					<h3>Confirmation Message:</h3>
  <textarea name="option[rsvp_confirm]"  rows="5" cols="80" id="rsvp_confirm"><?=$options["rsvp_confirm"]?></textarea>
	<br />
					<h3>RSVP Link:</h3>
  <textarea name="option[rsvplink]"  rows="5" cols="80" id="rsvplink"><?=$options["rsvplink"]?></textarea>
	<br />
					<h3>Dates Style:</h3>
  <textarea name="option[dates_style]"  rows="2" cols="80" id="dates_style"><?=$options["dates_style"]?></textarea>
	<br />
<h3>Time Format:</h3>
<p>
<input type="radio" name="option[time_format]" value="g:i A" <?php if($options["time_format"] == "g:i A") echo ' checked="checked"'; ?> /> 12 hour AM/PM 
<input type="radio" name="option[time_format]" value="H:i" <?php if($options["time_format"] == "H:i") echo ' checked="checked"'; ?> /> 24 hour 
<br />

					<h3>PayPal Enabled:</h3>
  <input type="checkbox" name="option[paypal_enabled]" value="1" <?php if($options["paypal_enabled"]) echo ' checked="checked" '; ?> /> check to prompt for PayPal pricing
	<br />
					<h3>PayPal Configuration File:</h3>
  <input type="text" name="option[paypal_config]" value="<?=$options["paypal_config"]?>" size="80" />
	
    <br /><em>Sample config file included with distribution. Must be manually configured. For security reasons, we recommend storing the file outside of web root. For example, /home/account/paypal_config.php where web content is stored in /home/account/public_html/</em>
    
    <br />
<h3>PEAR Spredsheet Writer:</h3>
  <input type="checkbox" name="option[pear_spreadsheet]" value="1" <?php if($options["pear_spreadsheet"]) echo ' checked="checked" '; ?> /> Enable spreadsheet downloads of RSVP Reports. You must manually install the <a href="http://pear.php.net/package/Spreadsheet_Excel_Writer">PEAR Spreadsheet Writer</a> 
	<br />
					<div class="submit"><input type="submit" name="Submit" value="Update" /></div>
			</form>
		    <p><strong>Shortcodes and Event Listing / Calendar Views</strong></p>
		    <p>RSVPMaker provides the following shortcodes for listing events, listing event headlines, and displaying a calendar with links to events.</p>
		    <p>[event_listing format=&quot;headlines&quot;] displays a list of headlines</p>
		    <p>[event_listing format=&quot;calendar&quot;] OR [event_listing calendar=&quot;1&quot;] displays the calendar</p>
		    <p>[rsvpmaker_upcoming] displays the index of upcoming events. If an RSVP is requested, the event includes the RSVP button link to the single post view, which will include your RSVP form.</p>
		    <p>[rsvpmaker_upcoming calendar=&quot;1&quot;] displays the calendar, followed by the index of upcoming events.</p>
            <p>[rsvpmaker_upcoming no_event="We're working on it. Check back soon"] specifies a custom message to display if there are no upcoming events in the database.</p>
            <div style="background-color: #FFFFFF; padding: 15px; text-align: center;">
            <img src="<?=plugins_url()?>/rsvpmaker/shortcode.png" width="535" height="412" />
<br /><em>Contents for an events page.</em>
            </div>
            
	    </div>
				
	 </div>

	</div>
	
</div>

<?php              
          }
      }
  
  else
      : exit("Class already declared!");
  endif;
  

  // create new instance of the class
  $RSVPMAKER_Options = new RSVPMAKER_Options();
  //print_r($RSVPMAKER_Options);
  if (isset($RSVPMAKER_Options)) {
      // register the activation function by passing the reference to our instance
      register_activation_hook(__FILE__, array(&$RSVPMAKER_Options, 'install'));
  }

add_action('init','save_rsvp');


function admin_event_listing() {
global $wpdb;

$sql = "SELECT *, $wpdb->posts.ID as postID
FROM `".$wpdb->prefix."rsvp_dates`
JOIN $wpdb->posts ON ".$wpdb->prefix."rsvp_dates.postID = $wpdb->posts.ID
WHERE datetime > CURDATE( ) AND $wpdb->posts.post_status = 'publish'
ORDER BY datetime";

if(!($_GET["events"] == 'all') )
	$sql .= " LIMIT 0, 20";

$results = $wpdb->get_results($sql,ARRAY_A);

foreach($results as $row)
	{
	$t = strtotime($row["datetime"]);
	$dateline[$row["postID"]] .= date('F jS',$t)." ";
	if(!$eventlist[$row["postID"]])
		$eventlist[$row["postID"]] = $row;
	}

if($eventlist)
foreach($eventlist as $event)
	{
		$listings .= sprintf('<li><a href="'.admin_url().'post.php?post=%d&action=edit">%s</a> %s</li>'."\n",$event["postID"],$event["post_title"],$dateline[$event["postID"]]);
	}	

	$listings = "<p><strong>Events (click to edit)</strong></p>\n<ul id=\"eventheadlines\">\n$listings</ul>\n".'<p><a href="?events=all">Show All</a>';
	return $listings;
}

function default_event_content($content) {
global $post;
global $rsvp_options;
if(($post->post_type == 'event') && ($content == ''))
{
return $rsvp_options['default_content'];
}
else
return $content;
}

add_filter('the_editor_content','default_event_content');


function multiple() {

global $wpdb;

if($_POST)
{

	$my_post['post_status'] = 'publish';
	$my_post['post_author'] = 1;
	$my_post['post_type'] = 'event';

	foreach($_POST["recur_year"] as $index => $year)
		{
		if($_POST["recur_day"][$index] )
			{
			$my_post['post_title'] = $_POST["title"][$index];
			$my_post['post_content'] = $_POST["body"][$index];
			$cddate = $year . "-" . $_POST["recur_month"][$index]  . "-" . $_POST["recur_day"][$index] . " " . $_POST["recur_hour"][$index] . ":" . $_POST["recur_minutes"][$index] . ":00";
// Insert the post into the database
  			if($postID = wp_insert_post( $my_post ) )
				{
				$sql = "INSERT INTO ".$wpdb->prefix."rsvp_dates SET datetime='$cddate', postID=". $postID;
				echo $sql."<br />";
				$wpdb->show_errors();
				$wpdb->query($sql);
				}
			}		
		}

}

global $rsvp_options;

?>
<p>Multiple Events</p>
<form id="form1" name="form1" method="post" action="<?=$_SERVER['REQUEST_URI']?>">
<?php
$today = '<option value="0">None</option>';
for($i=0; $i < 10; $i++)
{

$m = date('n');
$y = date('Y');
$y2 = $y+1;

wp_nonce_field(-1,'add_date'.$i);
?>
<p>Headline: <input type="text" name="title[<?=$i?>]" /></p>
<p><textarea name="body[<?=$i?>]" rows="5" cols="80"><?=$rsvp_options["default_content"]?></textarea></p>

<div id="recur_date<?=$i?>" style="border-bottom: thin solid #888;">

Month: 
              <select name="recur_month[<?=$i?>]"> 
              <option value="<?=$m?>"><?=$m?></option> 
              <option value="1">1</option> 
              <option value="2">2</option> 
              <option value="3">3</option> 
              <option value="4">4</option> 
              <option value="5">5</option> 
              <option value="6">6</option> 
              <option value="7">7</option> 
              <option value="8">8</option> 
              <option value="9">9</option> 
              <option value="10">10</option> 
              <option value="11">11</option> 
              <option value="12">12</option> 
              </select> 
            Day 
            <select name="recur_day[<?=$i?>]"> 
              <?=$today?> 
              <option value="1">1</option> 
              <option value="2">2</option> 
              <option value="3">3</option> 
              <option value="4">4</option> 
              <option value="5">5</option> 
              <option value="6">6</option> 
              <option value="7">7</option> 
              <option value="8">8</option> 
              <option value="9">9</option> 
              <option value="10">10</option> 
              <option value="11">11</option> 
              <option value="12">12</option> 
              <option value="13">13</option> 
              <option value="14">14</option> 
              <option value="15">15</option> 
              <option value="16">16</option> 
              <option value="17">17</option> 
              <option value="18">18</option> 
              <option value="19">19</option> 
              <option value="20">20</option> 
              <option value="21">21</option> 
              <option value="22">22</option> 
              <option value="23">23</option> 
              <option value="24">24</option> 
              <option value="25">25</option> 
              <option value="26">26</option> 
              <option value="27">27</option> 
              <option value="28">28</option> 
              <option value="29">29</option> 
              <option value="30">30</option> 
              <option value="31">31</option> 
            </select> 
            Year
            <select name="recur_year[<?=$i?>]"> 
              <option value="<?=$y?>"><?=$y?></option> 
              <option value="<?=$y2?>"><?=$y2?></option> 
            </select> 

Hour: <select name="recur_hour[<?=$i?>]"> 
 
<option  value="00">12 a.m.</option> 
<option  value="1">1 a.m.</option> 
<option  value="2">2 a.m.</option> 
<option  value="3">3 a.m.</option> 
<option  value="4">4 a.m.</option> 
<option  value="5">5 a.m.</option> 
<option  value="6">6 a.m.</option> 
<option  value="7">7 a.m.</option> 
<option  value="8">8 a.m.</option> 
<option  value="9">9 a.m.</option> 
<option  value="10">10 a.m.</option> 
<option  value="11">11 a.m.</option> 
<option  value="12">12 p.m.</option> 
<option  value="13">1 p.m.</option> 
<option  value="14">2 p.m.</option> 
<option  value="15">3 p.m.</option> 
<option  value="16">4 p.m.</option> 
<option  value="17">5 p.m.</option> 
<option  value="18">6 p.m.</option> 
<option  selected = "selected"  value="19">7 p.m.</option> 
<option  value="20">8 p.m.</option> 
<option  value="21">9 p.m.</option> 
<option  value="22">10 p.m.</option> 
<option  value="23">11 p.m.</option></select> 
 
Minutes: <select name="recur_minutes[<?=$i?>]"> 
<option value="00">00</option> 
<option value="00">00</option> 
<option value="15">15</option> 
<option value="30">30</option> 
<option value="45">45</option> 
</select>

</div>
<?php
} // end for loop
?>

<input type="submit" name="button" id="button" value="Submit" />
</form>

<?php
}



function add_dates() {

global $wpdb;

if($_POST)
{

if($_POST["recur-title"])
	{
	$my_post['post_title'] = $_POST["recur-title"];
	$my_post['post_content'] = $_POST["recur-body"];
	$my_post['post_status'] = 'publish';
	$my_post['post_author'] = 1;
	$my_post['post_type'] = 'event';

	foreach($_POST["recur_year"] as $index => $year)
		{
		if($_POST["recur_day"][$index] )
			{
			$cddate = $year . "-" . $_POST["recur_month"][$index]  . "-" . $_POST["recur_day"][$index] . " " . $_POST["recur_hour"][$index] . ":" . $_POST["recur_minutes"][$index] . ":00";
// Insert the post into the database
  			if($postID = wp_insert_post( $my_post ) )
				{
				$sql = "INSERT INTO ".$wpdb->prefix."rsvp_dates SET datetime='$cddate', postID=". $postID;
				echo $sql."<br />";
				$wpdb->show_errors();
				$wpdb->query($sql);
				}
			}		
		}
	}

}

global $rsvp_options;

?>
<p>Recurring Event (same headline/description, mulitple dates)</p>
<form id="form1" name="form1" method="post" action="<?=$_SERVER['REQUEST_URI']?>">
<p>Headline: <input type="text" name="recur-title" value="<?=stripslashes($_POST["recur-title"])?>" /></p>
<p><textarea name="recur-body" rows="5" cols="80"><?=($_POST["recur-body"]) ? stripslashes($_POST["recur-body"]) : $rsvp_options["default_content"]?></textarea></p>
<?php
$today = '<option value="0">None</option>';
for($i=0; $i < 10; $i++)
{

$m = date('n');
$y = date('Y');
$y2 = $y+1;

wp_nonce_field(-1,'add_date'.$i);
?>
<div id="recur_date<?=$i?>" style="border-bottom: thin solid #888;">

Month: 
              <select name="recur_month[<?=$i?>]"> 
              <option value="<?=$m?>"><?=$m?></option> 
              <option value="1">1</option> 
              <option value="2">2</option> 
              <option value="3">3</option> 
              <option value="4">4</option> 
              <option value="5">5</option> 
              <option value="6">6</option> 
              <option value="7">7</option> 
              <option value="8">8</option> 
              <option value="9">9</option> 
              <option value="10">10</option> 
              <option value="11">11</option> 
              <option value="12">12</option> 
              </select> 
            Day 
            <select name="recur_day[<?=$i?>]"> 
              <?=$today?> 
              <option value="1">1</option> 
              <option value="2">2</option> 
              <option value="3">3</option> 
              <option value="4">4</option> 
              <option value="5">5</option> 
              <option value="6">6</option> 
              <option value="7">7</option> 
              <option value="8">8</option> 
              <option value="9">9</option> 
              <option value="10">10</option> 
              <option value="11">11</option> 
              <option value="12">12</option> 
              <option value="13">13</option> 
              <option value="14">14</option> 
              <option value="15">15</option> 
              <option value="16">16</option> 
              <option value="17">17</option> 
              <option value="18">18</option> 
              <option value="19">19</option> 
              <option value="20">20</option> 
              <option value="21">21</option> 
              <option value="22">22</option> 
              <option value="23">23</option> 
              <option value="24">24</option> 
              <option value="25">25</option> 
              <option value="26">26</option> 
              <option value="27">27</option> 
              <option value="28">28</option> 
              <option value="29">29</option> 
              <option value="30">30</option> 
              <option value="31">31</option> 
            </select> 
            Year
            <select name="recur_year[<?=$i?>]"> 
              <option value="<?=$y?>"><?=$y?></option> 
              <option value="<?=$y2?>"><?=$y2?></option> 
            </select> 

Hour: <select name="recur_hour[<?=$i?>]"> 
 
<option  value="00">12 a.m.</option> 
<option  value="1">1 a.m.</option> 
<option  value="2">2 a.m.</option> 
<option  value="3">3 a.m.</option> 
<option  value="4">4 a.m.</option> 
<option  value="5">5 a.m.</option> 
<option  value="6">6 a.m.</option> 
<option  value="7">7 a.m.</option> 
<option  value="8">8 a.m.</option> 
<option  value="9">9 a.m.</option> 
<option  value="10">10 a.m.</option> 
<option  value="11">11 a.m.</option> 
<option  value="12">12 p.m.</option> 
<option  value="13">1 p.m.</option> 
<option  value="14">2 p.m.</option> 
<option  value="15">3 p.m.</option> 
<option  value="16">4 p.m.</option> 
<option  value="17">5 p.m.</option> 
<option  value="18">6 p.m.</option> 
<option  selected = "selected"  value="19">7 p.m.</option> 
<option  value="20">8 p.m.</option> 
<option  value="21">9 p.m.</option> 
<option  value="22">10 p.m.</option> 
<option  value="23">11 p.m.</option></select> 
 
Minutes: <select name="recur_minutes[<?=$i?>]"> 
<option value="00">00</option> 
<option value="00">00</option> 
<option value="15">15</option> 
<option value="30">30</option> 
<option value="45">45</option> 
</select>

</div>
<?php
} // end for loop
?>

<input type="submit" name="button" id="button" value="Submit" />
</form>

<?php
}

function my_rsvp_menu() {
add_submenu_page('edit.php?post_type=event', "RSVP Report", "RSVP Report", 2, "rsvp", "rsvp_report", $icon, $position );
add_submenu_page('edit.php?post_type=event', "Recurring Event", "Recurring Event", 8, "add_dates", "add_dates", $icon, $position );
add_submenu_page('edit.php?post_type=event', "Multiple Events", "Multiple Events", 8, "multiple", "multiple", $icon, $position );
}

add_action('admin_menu', 'my_rsvp_menu');

add_filter('manage_posts_columns', 'rsvpmaker_columns');
function rsvpmaker_columns($defaults) {
    $defaults['event_dates'] = __('Event Dates');
    return $defaults;
}

add_action('manage_posts_custom_column', 'rsvpmaker_custom_column', 10, 2);

function rsvpmaker_custom_column($column_name, $post_id) {
    global $wpdb;
    if( $column_name == 'event_dates' ) {
$sql = "SELECT *, $wpdb->posts.ID as postID
FROM `".$wpdb->prefix."rsvp_dates`
JOIN $wpdb->posts ON ".$wpdb->prefix."rsvp_dates.postID = $wpdb->posts.ID
WHERE $wpdb->posts.ID = $post_id ORDER BY datetime";

$results = $wpdb->get_results($sql,ARRAY_A);

foreach($results as $row)
		{
		$t = strtotime($row["datetime"]);
		if($dateline)
			$dateline .= ", ";
		$dateline .= date('F jS, Y',$t);
		}

    }
echo $dateline;
}

function rsvpmaker_admin_notice() {
global $wpdb;
$sql = "SELECT ID from $wpdb->posts WHERE post_status='publish' AND post_content LIKE '%[rsvpmaker_upcoming%' ";
if($id =$wpdb->get_var($sql))
	{
	$permalink = get_permalink($id);
	update_option('rsvpmaker_permalink',$permalink);
	}
else
	echo '<div class="updated" style="background-color:#f66;"><p>RSVPMaker needs you to create a page with the [rsvpmaker_upcoming] shortcode to display event listings.</p></div>';
}

add_action('admin_notices', 'rsvpmaker_admin_notice');

?>