<?php

function draw_eventdates() {

global $post;
global $wpdb;
global $rsvp_options;

$defaulthour = (isset($rsvp_options["defaulthour"])) ? ( (int) $rsvp_options["defaulthour"]) : 19;
$defaultmin = (isset($rsvp_options["defaultmin"])) ? ( (int) $rsvp_options["defaultmin"]) : 0;
$houropt = $minopt = '';

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
	if($rsvp_options["long_date"]) echo date($rsvp_options["long_date"],$t);
	$dur = $row["duration"];
	if($dur != 'allday')
		echo date(' '.$rsvp_options["time_format"],$t);
	if(is_numeric($dur) )
		echo " to ".date ($rsvp_options["time_format"],$dur);
	echo sprintf(' <input type="checkbox" name="delete_date[]" value="%d" /> Delete',$row["id"]);
	
	$dateparts = split('[-: ]',$row["datetime"]);

	if(is_numeric($dur) )
		$diff = ( (((int) $dur) - $t) / 3600);
	else
		$diff = $row["duration"];
	
	
	echo sprintf('<br />Year: <input type="text" size="6" name="edit_year[%d]" value="%s" /> Month: <input type="text" size="4" name="edit_month[%d]" value="%s" /> Day: <input type="text" size="4" name="edit_day[%d]" value="%s" /> Hour: <input size="4" type="text" name="edit_hour[%d]" value="%s" /> Minutes: <input type="text" size="4" name="edit_minutes[%d]" value="%s" /> Duration: <input type="text" size="4" name="edit_duration[%d]" value="%s" />',$row["id"],$dateparts[0],$row["id"],$dateparts[1],$row["id"],$dateparts[2],$row["id"],$dateparts[3],$row["id"],$dateparts[4],$row["id"],$diff);
	
	echo "</div>\n";
	}

	echo '<p><em>'.__('You can check &quot;delete&quot; to remove dates or edit date parameters. Time must be specified in military format (13:00 for 1 p.m.). Leave duration blank, or enter it as a number of hours (&quot;allday&quot; in duration field means time of day will not be displayed)','rsvpmaker').'</em> </p>';

}
else
	echo '<p><em>'.__('Enter one or more dates. For an event starting at 1:30 p.m., you would select 1 p.m. (or 13: for 24-hour format) and then 30 minutes. Specifying the duration is optional.','rsvpmaker').'</em> </p>';

if(!isset($start))
	$start = 1;

for($i=$start; $i < 6; $i++)
{
if($i == 2)
	{
	echo "<p><a onclick=\"document.getElementById('additional_dates').style.display='block'\" >".__('Add More Dates','rsvpmaker')."</a> </p>
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

;?>
<div id="event_date<?php echo $i;?>" style="border-bottom: thin solid #888;">
<table width="100%">
<tr>
            <td width="*"><div id="date_block"><?php echo __('Month:','rsvpmaker');?> 
              <select name="event_month[<?php echo $i;?>]"> 
              <option value="<?php echo $m;?>"><?php echo $m;?></option> 
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
            <?php echo __('Day:','rsvpmaker');?> 
            <select name="event_day[<?php echo $i;?>]"> 
              <?php echo $today;?>
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
            <?php echo __('Year','rsvpmaker');?>
            <select name="event_year[<?php echo $i ;?>]"> 
              <option value="<?php echo $y;?>"><?php echo $y;?></option> 
              <option value="<?php echo $y2;?>"><?php echo $y2;?></option> 
            </select> 
</div> 
            </td> 
          </tr> 
<tr> 
<td><?php echo __('Hour:','rsvpmaker');?> <select name="event_hour[<?php echo $i;?>]"> 
<?php echo $houropt;?>
</select> 
 
<?php echo __('Minutes:','rsvpmaker');?> <select name="event_minutes[<?php echo $i;?>]"> 
<?php echo $minopt;?>
</select> -

<?php echo __('Duration','rsvpmaker');?> <select name="event_duration[<?php echo $i;?>]">
<option value=""><?php echo __('Not set (optional)','rsvpmaker');?></option>
<option value="allday"><?php echo __("All day/don't show time in headline",'rsvpmaker');?></option>
<?php for($h = 1; $h < 24; $h++) { ;?>
<option value="<?php echo $h;?>"><?php echo $h;?> hours</option>
<option value="<?php echo $h;?>:15"><?php echo $h;?>:15</option>
<option value="<?php echo $h;?>:30"><?php echo $h;?>:30</option>
<option value="<?php echo $h;?>:45"><?php echo $h;?>:45</option>
<?php } ;?>
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
add_meta_box( 'EventDatesBox', __('Event Dates, RSVP Options','rsvpmaker'), 'draw_eventdates', 'rsvpmaker', 'normal', 'high' );

}

function save_calendar_data($postID) {

global $wpdb;

if($parent_id = wp_is_post_revision($postID))
	{
	$postID = $parent_id;
	}

if(isset($_POST["event_month"]) )
	{

	foreach($_POST["event_year"] as $index => $year)
		{
		if(isset($_POST["event_day"][$index]) )
			{
			$cddate = $year . "-" . $_POST["event_month"][$index]  . "-" . $_POST["event_day"][$index] . " " . $_POST["event_hour"][$index] . ":" . $_POST["event_minutes"][$index] . ":00";
			if( $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."rsvp_dates WHERE postID=$postID AND datetime='$cddate' ") )
				continue;
			
			$dpart = explode(':',$_POST["event_duration"][$index]);			
			
			if( is_numeric($dpart[0]) )
				{
				$hour = $_POST["event_hour"][$index] + $dpart[0];
				$minutes = $_POST["event_minutes"][$index] + $dpart[1];
				$duration = mktime( $hour, $minutes,0,$_POST["event_month"][$index],$_POST["event_day"][$index],$year);
				}
			else
				$duration = $_POST["event_duration"][$index]; // empty or all day
				
			$sql = " SET datetime='$cddate',duration='$duration', postID=". $postID;
			
			if(isset($_POST["custom_post_dates_id"][$index]))
				$sql = "UPDATE ".$wpdb->prefix."rsvp_dates $sql WHERE id=". (int) $_POST["custom_post_dates_id"][$index]; 
			else
				$sql = "INSERT INTO ".$wpdb->prefix."rsvp_dates $sql"; 
			
			$wpdb->query($sql);
			}
		}

	
	if(isset($_POST["delete_date"]))
		{
		foreach($_POST["delete_date"] as $id)
			{
			$dsql = "DELETE FROM ".$wpdb->prefix."rsvp_dates WHERE id=$id";
			$wpdb->query($dsql);
			}
		}
	
	if(isset($_POST["setrsvp"]["on"]))
		save_rsvp_meta($postID);
	else
		delete_post_meta($postID, '_rsvp_on', '1');
	
	
	}


if(isset($_POST["edit_month"]))
	{
//print_r($_POST);
	foreach($_POST["edit_year"] as $index => $year)
		{
			$cddate = $year . "-" . $_POST["edit_month"][$index]  . "-" . $_POST["edit_day"][$index] . " " . $_POST["edit_hour"][$index] . ":" . $_POST["edit_minutes"][$index] . ":00";
			
			if( is_numeric($_POST["edit_duration"][$index]) )
				{
				$minutes = $_POST["edit_minutes"][$index] + (60*$_POST["edit_duration"][$index]);
				$duration = mktime( $_POST["edit_hour"][$index], $minutes,0,$_POST["edit_month"][$index],$_POST["edit_day"][$index],$year);
				}
			else
				$duration = $_POST["edit_duration"][$index]; // empty or all day
				
			$sql = "UPDATE ".$wpdb->prefix."rsvp_dates  SET datetime='$cddate',duration='$duration'  WHERE id=$index"; 
			//echo $sql;
			$wpdb->query($sql);
			}
		}
	
}

function save_rsvp_meta($postID)
{
$setrsvp = $_POST["setrsvp"];

if(!isset($setrsvp["show_attendees"])) $setrsvp["show_attendees"] = 0;
if(!isset($setrsvp["captcha"])) $setrsvp["captcha"] = 0;

if(isset($_POST["deadyear"]) && isset($_POST["deadmonth"]) && isset($_POST["deadday"]))
	$setrsvp["deadline"] = strtotime($_POST["deadyear"].'-'.$_POST["deadmonth"].'-'.$_POST["deadday"].' 23:59:59');

if(isset($_POST["startyear"]) && isset($_POST["startmonth"]) && isset($_POST["startday"]))
	$setrsvp["start"] = strtotime($_POST["startyear"].'-'.$_POST["startmonth"].'-'.$_POST["startday"].' 00:00:00');

if(isset($_POST["remindyear"]) && isset($_POST["remindmonth"]) && isset($_POST["remindday"]))
	$setrsvp["reminder"] = date('Y-m-d',strtotime($_POST["remindyear"].'-'.$_POST["remindmonth"].'-'.$_POST["remindday"].' 00:00:00') );

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

if(isset($_POST["unit"]))
	{
	foreach($_POST["unit"] as $index => $value)
		{
		if($value && isset($_POST["price"][$index]))
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
              $this->plugin_url = plugins_url('',__FILE__).'/';

              // add options Page
              add_action('admin_menu', array(&$this, 'admin_menu'));
              
          }
          
          // hook the options page
          function admin_menu()
          {
              add_options_page('RSVPMaker', 'RSVPMaker', 'manage_options', basename(__FILE__), array(&$this, 'handle_options'));
          }
          
          
          // handle plugin options
          function get_options()
          {
              global $rsvp_options;
              return $rsvp_options;
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
                  $newoptions["rsvp_on"] = (isset($_POST["option"]["rsvp_on"]) && $_POST["option"]["rsvp_on"]) ? 1 : 0;
                  $newoptions["rsvp_captcha"] = (isset($_POST["option"]["rsvp_captcha"]) && $_POST["option"]["rsvp_captcha"]) ? 1 : 0;
                  $newoptions["show_attendees"] = (isset($_POST["option"]["show_attendees"]) && $_POST["option"]["show_attendees"]) ? 1 : 0;
				  $newoptions["dbversion"] = $options["dbversion"]; // gets set by db upgrade routine
				  $newoptions["posttypecheck"] = $options["posttypecheck"];
				if(isset($options["noeventpageok"]) ) $newoptions["noeventpageok"] = $options["noeventpageok"];
				$nfparts = explode('|',$_POST["currency_format"]);
				$newoptions["currency_decimal"] = $nfparts[0];
				$newoptions["currency_thousands"] = $nfparts[1];
				
				  $options = $newoptions;
				  
                  update_option($this->db_option, $options);
                  
                  echo '<div class="updated fade"><p>Plugin settings saved.</p></div>';
              }
              
              // URL for form submit, equals our current page
              $action_url