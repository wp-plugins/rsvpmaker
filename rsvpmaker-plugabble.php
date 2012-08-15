<?php

// start customizable functions, can be overriden by adding a custom.php file to rsvpmaker directory

if(!function_exists('draw_eventdates')) {
function draw_eventdates() {

global $post;
global $wpdb;
global $rsvp_options;

if(isset($post->ID) )
	$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."rsvp_dates WHERE postID=".$post->ID.' ORDER BY datetime',ARRAY_A);
else
	$results = false;

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
	echo sprintf(' <input type="checkbox" name="delete_date[]" value="%d" /> Delete <br />',$row["id"]);

	rsvpmaker_date_option($row);

	echo "</div>\n";
	}

}
else
	echo '<p><em>'.__('Enter one or more dates. For an event starting at 1:30 p.m., you would select 1 p.m. (or 13: for 24-hour format) and then 30 minutes. Specifying the duration is optional.','rsvpmaker').'</em> </p>';

if(!isset($start))
	{
	$start = 1;
	$date = (isset($_GET["add_date"]) ) ? $_GET["add_date"] : 'today';
	}
for($i=$start; $i < 6; $i++)
{
if($i == 2)
	{
	echo "<p><a onclick=\"document.getElementById('additional_dates').style.display='block'\" >".__('Add More Dates','rsvpmaker')."</a> </p>
	<div id=\"additional_dates\" style=\"display: none;\">";
	$date = NULL;
	}

	rsvpmaker_date_option($date, $i);

} // end for loop
echo "\n</div><!--add dates-->\n";

GetRSVPAdminForm($post->ID);

}
} // end draw event dates

if(!function_exists('rsvpmaker_roles') )
{
function rsvpmaker_roles() {
// by default, capabilities for events are the same as for blog posts
global $wp_roles;

if(!isset($wp_roles) )
	$wp_roles = new WP_Roles();
if(isset($wp_roles->roles))
foreach ($wp_roles->roles as $role => $rolearray)
	{
	foreach($rolearray["capabilities"] as $cap => $flag)
		{
			if(strpos($cap,'post') )
				{
					$fbcap = str_replace('post','rsvpmaker',$cap);
					$wp_roles->add_cap( $role, $fbcap );
					//echo "$role $fbcap<br />";
				}
		}
	}

}
}

if(! function_exists('GetRSVPAdminForm') )
{
function GetRSVPAdminForm($postID)
{
$custom_fields = get_post_custom($postID);

if(isset($custom_fields["_rsvp_on"][0]) ) $rsvp_on = $custom_fields["_rsvp_on"][0];
if(isset($custom_fields["_rsvp_to"][0]) ) $rsvp_to = $custom_fields["_rsvp_to"][0];
if(isset($custom_fields["_rsvp_instructions"][0]) ) $rsvp_instructions = $custom_fields["_rsvp_instructions"][0];
if(isset($custom_fields["_rsvp_confirm"][0]) ) $rsvp_confirm = $custom_fields["_rsvp_confirm"][0];
if(isset($custom_fields["_rsvp_form"][0]) ) $rsvp_form = $custom_fields["_rsvp_form"][0];
if(isset($custom_fields["_rsvp_max"][0]) ) $rsvp_max = $custom_fields["_rsvp_max"][0];
if(isset($custom_fields["_rsvp_show_attendees"][0]) ) $rsvp_show_attendees = $custom_fields["_rsvp_show_attendees"][0];
if(isset($custom_fields["_rsvp_captcha"][0]) ) $rsvp_captcha = $custom_fields["_rsvp_captcha"][0];
if(isset($custom_fields["_rsvp_reminder"][0]) && $custom_fields["_rsvp_reminder"][0])
	{
	$rparts = explode("-",$custom_fields["_rsvp_reminder"][0]);
	$remindyear = $rparts[0];
	$remindmonth = $rparts[1];
	$remindday = $rparts[2];
	}
	
if(isset($custom_fields["_rsvp_deadline"][0]) && $custom_fields["_rsvp_deadline"][0])
	{
	$t = (int) $custom_fields["_rsvp_deadline"][0];
	$deadyear = date('Y',$t);
	$deadmonth = date('m',$t);
	$deadday = date('d',$t);
	}

if(isset($custom_fields["_rsvp_start"][0]) && $custom_fields["_rsvp_start"][0])
	{
	$t = (int) $custom_fields["_rsvp_start"][0];
	$startyear = date('Y',$t);
	$startmonth = date('m',$t);
	$startday = date('d',$t);
	}

global $rsvp_options;

if(!isset($rsvp_on) && !isset($rsvp_to) && !isset($rsvp_instructions) && !isset($rsvp_confirm))
	{
	echo '<p>'.__('Loading default values for RSVPs - check the checkbox to collect RSVPs online','rsvpmaker') .'</p>';
	$rsvp_to = $rsvp_options["rsvp_to"];
	$rsvp_instructions = $rsvp_options["rsvp_instructions"];
	$rsvp_confirm = $rsvp_options["rsvp_confirm"];
	$rsvp_form = $rsvp_options["rsvp_form"];
	$rsvp_on = $rsvp_options["rsvp_on"];
	$rsvp_captcha = $rsvp_options["rsvp_captcha"];
	$rsvp_max = 0;
	$rsvp_show_attendees = $rsvp_options["show_attendees"];
	}
if(!isset($rsvp_show_attendees))
	$rsvp_show_attendees = 0;
if(!isset($rsvp_captcha))
	$rsvp_captcha = 0;	
if(!isset($rsvp_on))
	$rsvp_on = 0;	

//get_post_meta($post->ID, '_rsvp_on', true)
//echo "<br />'"; print_r($rsvp_form); echo "'<br />";?>
<p>
  <input type="checkbox" name="setrsvp[on]" id="setrsvp[on]" value="1" <?php if( $rsvp_on ) echo 'checked="checked" ';?> />
<?php echo __('Collect RSVPs','rsvpmaker');?> <?php if( !$rsvp_on ) echo ' <strong style="color: red;">'.__('Check to activate','rsvpmaker').'</strong> ';?>

<br />  <input type="checkbox" name="setrsvp[show_attendees]" id="setrsvp[show_attendees]" value="1" <?php if( $rsvp_show_attendees ) echo 'checked="checked" ';?> />
<?php echo __(' Display attendee names and content of note field publicly','rsvpmaker');?> <?php if( !$rsvp_show_attendees ) echo ' <strong style="color: red;">'.__('Check to activate','rsvpmaker').'</strong> ';?>

<br />  <input type="checkbox" name="setrsvp[captcha]" id="setrsvp[captcha]" value="1" <?php if( $rsvp_captcha ) echo 'checked="checked" ';?> />
<?php echo __(' Include CAPTCHA challenge','rsvpmaker');?> <?php if( !$rsvp_captcha ) echo ' <strong style="color: red;">'.__('Check to activate','rsvpmaker').'</strong> ';?>

</p>

<div id="rsvpoptions">
<?php echo __('Email Address for Notifications','rsvpmaker');?>: <input id="setrsvp[to]" name="setrsvp[to]" type="text" value="<?php echo $rsvp_to;?>"><br />
<br /><?php echo __('Instructions for User','rsvpmaker');?>:<br />
<textarea id="rsvp[instructions]" name="setrsvp[instructions]" cols="80"><?php if(isset($rsvp_instructions)) echo $rsvp_instructions;?></textarea>
<br /><?php echo __('Confirmation Message','rsvpmaker');?>:<br />
<textarea id="rsvp[confirm]" name="setrsvp[confirm]" cols="80"><?php if(isset($rsvp_confirm)) echo $rsvp_confirm;?></textarea>

<br /><strong>Special Options</strong>

<table><tr><td><?php echo __('Deadline (optional)','rsvpmaker').'</td><td> '.__('Month','rsvpmaker');?>: <input type="text" name="deadmonth" id="deadmonth" value="<?php if(isset($deadmonth)) echo $deadmonth;?>" size="2" /> <?php echo __('Day','rsvpmaker');?>: <input type="text" name="deadday" id="deadday" value="<?php  if(isset($deadday)) echo $deadday;?>" size="2" /> <?php echo __('Year','rsvpmaker');?>: 
<input type="text" name="deadyear" id="deadyear" value="<?php  if(isset($deadyear)) echo $deadyear;?>" size="4" /> (<?php echo __('stop collecting RSVPs at midnight','rsvpmaker');?>)</td></tr>

<tr><td><?php echo __('Start Date (optional)','rsvpmaker').'</td><td>'.__('Month','rsvpmaker');?>: <input type="text" name="startmonth" id="startmonth" value="<?php  if(isset($startmonth)) echo $startmonth;?>" size="2" /> <?php echo __('Day','rsvpmaker');?>: <input type="text" name="startday" id="startday" value="<?php  if(isset($startday)) echo $startday;?>" size="2" /> <?php echo __('Year','rsvpmaker');?>: 
<input type="text" name="startyear" id="startyear" value="<?php  if(isset($startyear)) echo $startyear;?>" size="4" /> (<?php echo __('start collecting RSVPs','rsvpmaker');?>)</td></tr>

<tr><td><?php echo __('Reminder (optional)','rsvpmaker').'</td><td>'.__('Month','rsvpmaker');?>: <input type="text" name="remindmonth" id="remindmonth" value="<?php  if(isset($remindmonth)) echo $remindmonth;?>" size="2" /> <?php echo __('Day','rsvpmaker');?>: <input type="text" name="remindday" id="remindday" value="<?php  if(isset($remindday)) echo $remindday;?>" size="2" /> <?php echo __('Year','rsvpmaker');?>: 
<input type="text" name="remindyear" id="remindyear" value="<?php  if(isset($remindyear)) echo $remindyear;?>" size="4" /> (<?php echo __("Send email reminder to people on RSVP list",'rsvpmaker');?>)</td></tr>

</table>

<br /><?php echo __('Maximum participants','rsvpmaker');?> <input type="text" name="setrsvp[max]" id="setrsvp[max]" value="<?php if(isset($rsvp_max)) echo $rsvp_max;?>" size="4" /> (<?php echo __('0 for none specified','rsvpmaker');?>)
<br /><?php echo __('Time Slots','rsvpmaker');?>:

<select name="setrsvp[timeslots]" id="setrsvp[timeslots]">
<option value="0">None</option>
<option value="0:30" <?php if(isset($custom_fields["_rsvp_timeslots"][0]) && ($custom_fields["_rsvp_timeslots"][0] == '0:30')) echo ' selected = "selected" ';?> >30 minutes</option>
<?php
$tslots = (int) $custom_fields["_rsvp_timeslots"][0];
for($i = 1; $i < 13; $i++)
	{
	$selected = ($i == $tslots) ? ' selected = "selected" ' : '';
	echo '<option value="'.$i.'" '.$selected.">$i-hour slots</option>";
	}
;?>
</select>
<br /><em><?php echo __('Used for volunteer shift signups. Duration must also be set.','rsvpmaker');?></em>

<br /><?php echo __('RSVP Form','rsvpmaker');?>:<br />
<textarea id="rsvp[form]" name="setrsvp[form]" cols="80"><?php if(isset($rsvp_form)) echo htmlentities($rsvp_form);?></textarea>
<br />

<?php
if($rsvp_options["paypal_config"])
{
?>
<p><strong><?php echo __('Pricing for Online Payments','rsvpmaker');?></strong></p>
<p><?php echo __('You can set a different price for members vs. non-members, adults vs. children, etc.','rsvpmaker');?></p>
<?php

if(isset($custom_fields["_per"][0]))
	$per = unserialize($custom_fields["_per"][0]);
else
	$per["unit"][0] = __("Tickets",'rsvpmaker');

for($i=0; $i < 5; $i++)
{
?>
Units: <input name="unit[<?php if(isset($i)) echo $i;?>]" value="<?php  if(isset($per["unit"][$i])) echo $per["unit"][$i];?>" /> @
Price: <input name="price[<?php  if(isset($i)) echo $i;?>]" value="<?php  if(isset($per["price"][$i])) echo $per["price"][$i];?>" /> <?php if(isset($rsvp_options["paypal_currency"])) echo $rsvp_options["paypal_currency"]; ?>
<br />
<?php
}

} // end paypal enabled section
?>

</div>
<?php
} } // end rsvp admin ui


if(!function_exists('capture_email') )
{
function capture_email($rsvp) {
//placeholder function, may be overriden to sing person up for email list
} } // end capture email

if(!function_exists('save_rsvp') )
{
function save_rsvp() {
if(isset($_POST["yesno"]) && wp_verify_nonce($_POST['rsvp_nonce'],'rsvp') )
	{

global $wpdb;
global $rsvp_options;

if ( get_magic_quotes_gpc() )
    $_POST = array_map( 'stripslashes_deep', $_POST );

//sanitize input
foreach($_POST["profile"] as $name => $value)
	$rsvp[$name] = esc_attr($value);
if(isset($_POST["note"]))
	$note = esc_attr($_POST["note"]);
else
	$note = "";

$yesno = (int) $_POST["yesno"];
$answer = ($yesno) ? "YES" : "NO";
$event = (int) $_POST["event"];
// page hasn't loaded yet, so retrieve post variables based on event
$post = get_post($event);
//get rsvp_to
$custom_fields = get_post_custom($post->ID);
$rsvp_to = $custom_fields["_rsvp_to"][0];

//if permalinks are not turned on, we need to append to query string not add our own ?
$req_uri = $_SERVER['REQUEST_URI'];
$req_uri .= (strpos($req_uri,'?') ) ? '&' : '?';

if(isset($custom_fields["_rsvp_captcha"][0]) && $custom_fields["_rsvp_captcha"][0])
	{
	if(!isset($_SESSION["captcha_key"]))
		session_start();
	if($_SESSION["captcha_key"] != md5($_POST['captcha']) )	
		{
		header('Location: '.$req_uri.'err='.urlencode('Error - security code not entered correctly! Please try again.'));
		exit();
		}
	}


if( preg_match_all('/http/',$_POST["note"],$matches) > 2 )
	{
	header('Location: '.$req_uri.'err=Invalid input');
	exit();
	}

if( ereg("//",implode(' ',$rsvp)) )
	{
	header('Location: '.$req_uri.'err=Invalid input');
	exit();
	}

if(isset($rsvp["email"]))
	{
	// assuming the form includes email, test to make sure it's a valid one
	if(!filter_var($rsvp["email"], FILTER_VALIDATE_EMAIL))
		{
		header('Location: '.$_SERVER['REDIRECT_URL'].'?err='.urlencode('Error - Invalid input.') );
		exit();
		}
	
	//see if we have a previous rsvp for this event, associated with this email
	$sql = "SELECT id FROM ".$wpdb->prefix."rsvpmaker WHERE event='$event' AND email='".$rsvp["email"]."' ";
	$rsvp_id = $wpdb->get_var($sql);
	}

// test for artificially random input
$rtxt = implode('',$rsvp);
$uppercount = preg_match_all('/[A-Z]/',$rtxt,$upper);
$lowercount = preg_match_all('/[a-z]/',$rtxt,$lower);
$diff = abs($uppercount - $lowercount);
$diff = ($diff) ? $diff : 1;
$diffratio = $diff / ($lowercount + $uppercount);

if($diffratio < .6)
	{
	header('Location: '.$req_uri.'&err=Invalid input');
	exit();
	}

if(isset($_POST["onfile"]))
	{
	$details = $wpdb->get_var("SELECT details FROM ".$wpdb->prefix."rsvpmaker WHERE email='".$rsvp["email"]."' ORDER BY id DESC");
	if($details)
		$contact = unserialize($details);
	else	
		$contact = rsvpmaker_profile_lookup($rsvp["email"]);
		
	if($contact)
		{
		foreach($contact as $name => $value)
			{
			if(!isset($rsvp[$name]))
				$rsvp[$name] = $value;
			}
		}
	}

if(isset($_POST["payingfor"]) && is_array($_POST["payingfor"]) )
	{
	$rsvp["total"] = 0;
	$participants = 0;
	$rsvp["payingfor"] = "";
	foreach($_POST["payingfor"] as $index => $value)
		{
		$value = (int) $value;
		$unit = esc_attr($_POST["unit"][$index]);
		$price = (float) $_POST["price"][$index];
		$cost = $value * $price;
		if(isset($rsvp["payingfor"]) && $rsvp["payingfor"])
			$rsvp["payingfor"] .= ", ";
		$rsvp["payingfor"] .= "$value $unit @ ".number_format($price,2,$rsvp_options["currency_decimal"],$rsvp_options["currency_thousands"]) . ' '.$rsvp_options["paypal_currency"];
		$rsvp["total"] += $cost;
		$participants += $value;
		}
	}

if( isset($_POST["timeslot"]) && is_array($_POST["timeslot"]) )
	{
	$participants = $rsvp["participants"] = (int) $_POST["participants"];
	$rsvp["timeslots"] = ""; // ignore anything retrieved from prev rsvps
	foreach($_POST["timeslot"] as $slot)
		{
		if($rsvp["timeslots"])
			$rsvp["timeslots"] .=  ", ";
		$rsvp["timeslots"] .= date('g:i A',$slot);
		}
	}

if(!isset($participants) && $yesno)
	{
	// if they didn't specify # of participants (paid tickets or volunteers), count the host plus guests
	$participants = 1;
	if(isset($_POST["guestfirst"]))
	{
	foreach($_POST["guestfirst"] as $first)
		if($first)
			$participants++;
	}
	
	if(isset($_POST["guestdelete"]))
		$participants -= sizeof($_POST["guestdelete"]);
	}
if(!$yesno)
	$participants = 0; // if they said no, they don't count

$rsvp_sql = $wpdb->prepare(" SET first=%s, last=%s, email=%s, yesno=%d, event=%d, note=%s, details=%s, participants=%d ", $rsvp["first"], $rsvp["last"], $rsvp["email"],$yesno,$event, $note, serialize($rsvp), $participants );

capture_email($rsvp);

if($rsvp_id)
	{
	$rsvp_sql = "UPDATE ".$wpdb->prefix."rsvpmaker ".$rsvp_sql." WHERE id=$rsvp_id";
	$wpdb->show_errors();
	$wpdb->query($rsvp_sql);
	}
else
	{
	$rsvp_sql = "INSERT INTO ".$wpdb->prefix."rsvpmaker ".$rsvp_sql;
	$wpdb->show_errors();
	$wpdb->query($rsvp_sql);
	$rsvp_id = $wpdb->insert_id;
	}

if(isset($_POST["timeslot"]))
	{
	// clear previous response, if any
	$wpdb->query("DELETE FROM ".$wpdb->prefix."rsvp_volunteer_time WHERE rsvp=$rsvp_id");
	foreach($_POST["timeslot"] as $slot)
		{
		$slot = (int) $slot;
		$participants = (int) $_POST["participants"];
		$sql = $wpdb->prepare("INSERT INTO ".$wpdb->prefix."rsvp_volunteer_time SET time=%d, event=%d, rsvp=%d, participants=%d",$slot,$post->ID,$rsvp_id,$participants); 
		$wpdb->query($sql);
		}
	}

//get start date
$sql = "SELECT * FROM ".$wpdb->prefix."rsvp_dates WHERE postID=".$post->ID.' ORDER BY datetime';
$row = $wpdb->get_row($sql,ARRAY_A);
$t = strtotime($row["datetime"]);
$date = date('M j',$t);

$cleanmessage = '';
foreach($rsvp as $name => $value)
	$cleanmessage .= $name.": ".$value."\n";

$guestof = $rsvp["first"]." ".$rsvp["last"];

if(isset($_POST["guestfirst"]) )
{
foreach($_POST["guestfirst"] as $index => $first) {
	$first = esc_attr($first);
	$last = esc_attr($_POST["guestlast"][$index]);
	$guestid = (int) $_POST["guestid"][$index];
	if($first || $last)
		{
		if(isset($_POST["guestdelete"][$guestid]))
			$sql = "DELETE FROM ".$wpdb->prefix."rsvpmaker WHERE id=". (int) $guestid;
		elseif($guestid)
			{
			$sql = $wpdb->prepare("UPDATE ".$wpdb->prefix."rsvpmaker SET first=%s, last=%s, yesno=%d WHERE id=%d", $first, $last,$yesno,$guestid);
			$cleanmessage .= sprintf("Guest: %s %s\n",$first,$last );
			}
		else
			{
			$sql = $wpdb->prepare("INSERT ".$wpdb->prefix."rsvpmaker SET first=%s, last=%s, event=%d, master_rsvp=%d, yesno=%d, guestof=%s", $first, $last,$event,$rsvp_id,$yesno,$guestof);
			$cleanmessage .= sprintf("Guest: %s %s\n",$first, $last);
			}
		$wpdb->query($sql);
		}
}

}

$subject = "RSVP $answer for ".$post->post_title." $date";
if($_POST["note"])
	$cleanmessage .= 'Note: '.stripslashes($_POST["note"]);
rsvp_notifications ($rsvp,$rsvp_to,$subject,$cleanmessage);

	header('Location: '.$req_uri.'rsvp='.$rsvp_id.'&e='.$rsvp["email"]);
	exit();
	}
} } // end save rsvp


if(!function_exists('rsvp_notifications') )
{
function rsvp_notifications ($rsvp,$rsvp_to,$subject,$message) {

  $headers = "Reply-To: ".$rsvp["email"]."\n"; 
  $headers .= "From: ".'"=?UTF-8?B?'.base64_encode($rsvp["first"]." ".$rsvp["last"]).'?=" <'.$rsvp["email"].'>'."\n"; 
  $headers .= "Organization: ".$_SERVER['SERVER_NAME']."\n";
  $headers .= "MIME-Version: 1.0\n";
  $headers .= "Content-type: text/plain; charset=UTF-8\n";
  $headers .= "X-Priority: 3\n";
  $headers .= "X-Mailer: PHP". phpversion() ."\n"; 

mail($rsvp_to,'=?UTF-8?B?'.base64_encode($subject).'?=',$message,$headers);

// now send confirmation

  $headers = "Reply-To: The Sender <$rsvp_to>\n"; 
  $headers .= "From: <$rsvp_to>\n"; 
  $headers .= "Organization: ".$_SERVER['SERVER_NAME']."\n";
  $headers .= "MIME-Version: 1.0\n";
  $headers .= "Content-type: text/plain; charset=UTF-8\n";
  $headers .= "X-Priority: 3\n";
  $headers .= "X-Mailer: PHP ". phpversion() ."\n"; 

mail($rsvp["email"],"Confirming ".$subject,$message,$headers);

} } // end rsvp notifications


if(!function_exists('paypal_start') )
{
function paypal_start() {

global $rsvp_options;

//sets up session to display errors or initializes paypal transactions prior to page display
if( isset($_REQUEST["paypal"]) && ( $_REQUEST["paypal"] == 'error' ) )
	{
	session_start();
	return;
	}
elseif( ! isset($_REQUEST['paymentAmount']) )
	return;

session_start();

require_once $rsvp_options["paypal_config"];
require_once WP_CONTENT_DIR.'/plugins/rsvpmaker/paypal/CallerService.php';
$token = $_REQUEST['token'];
if(! isset($token)) {

// ignore if it fails security test
if(! wp_verify_nonce($_POST["rsvp-pp-nonce"],'pp-nonce') )
	return;

		/* The servername and serverport tells PayPal where the buyer
		   should be directed back to after authorizing payment.
		   In this case, its the local webserver that is running this script
		   Using the servername and serverport, the return URL is the first
		   portion of the URL that buyers will return to after authorizing payment
		   */
		   $serverName = $_SERVER['SERVER_NAME'];
		   $serverPort = $_SERVER['SERVER_PORT'];
		   $url='http://'.$serverName.':'.$serverPort.$_SERVER['REDIRECT_URL'];
		if($_REQUEST['paymentAmount'])
			$paymentAmount=$_REQUEST['paymentAmount'];
		else
			$paymentAmount = $_POST["price"]*$_POST["unit"];
		   $_SESSION["paymentAmount"] = $paymentAmount;//=$_REQUEST['paymentAmount'];
		   $_SESSION["currencyCodeType"] = $currencyCodeType=$rsvp_options["paypal_currency"];
		   $_SESSION["paymentType"] = $paymentType='Sale'; //$_REQUEST['paymentType'];
		   if(!$invoice)
		   	$invoice=$_REQUEST['invoice'];
		   $_SESSION["invoice"] = $invoice;
		   $desc=$_REQUEST['desc'];
			$email = $_REQUEST['email'];

		 /* The returnURL is the location where buyers return when a
			payment has been succesfully authorized.
			The cancelURL is the location buyers are sent to when they hit the
			cancel button during authorization of payment during the PayPal flow
			*/
		   $returnURL =urlencode($url.'?currencyCodeType='.$currencyCodeType.'&paymentType='.$paymentType.'&paymentAmount='.$paymentAmount);
		   
		   $cancelURL =urlencode("$url");

		 /* Construct the parameter string that describes the PayPal payment
			the varialbes were set in the web form, and the resulting string
			is stored in $nvpstr
			*/
		  
		   $nvpstr="&Amt=".$paymentAmount."&PAYMENTACTION=".$paymentType."&RETURNURL=".$returnURL."&CANCELURL=".$cancelURL ."&CURRENCYCODE=".$currencyCodeType.'&EMAIL='.$email;
		   
		   $nvpstr.="&INVNUM=" . $invoice . "&SOLUTIONTYPE=Sole&LANDING=Billing&DESC=" . urlencode($desc);

		 /* Make the call to PayPal to set the Express Checkout token
			If the API call succeded, then redirect the buyer to PayPal
			to begin to authorize payment.  If an error occured, show the
			resulting errors
			*/

/*		   echo "$nvpstr<br />";
		   print_r($_REQUEST);
		   echo "<br />";
		   print_r($_SESSION);
		   exit();
*/
			
		   $resArray=hash_call("SetExpressCheckout",$nvpstr);


		   $_SESSION['reshash']=$resArray;

		   $ack = strtoupper($resArray["ACK"]);

		   if($ack=="SUCCESS"){
					// Redirect to paypal.com here
					$token = urldecode($resArray["TOKEN"]);
					$payPalURL = PAYPAL_URL.$token;
					header("Location: ".$payPalURL);
					exit();
				  } else  {
					 //Redirecting to APIError.php to display errors. 
						$location = $_SERVER['REDIRECT_URL'] . "?paypal=error&function=firstpass";
						header("Location: $location");
						exit();
					}
} else {
		 /* At this point, the buyer has completed in authorizing payment
			at PayPal.  The script will now call PayPal with the details
			of the authorization, incuding any shipping information of the
			buyer.  Remember, the authorization is not a completed transaction
			at this state - the buyer still needs an additional step to finalize
			the transaction
			*/

		   $token =urlencode( $_REQUEST['token']);

		 /* Build a second API request to PayPal, using the token as the
			ID to get the details on the payment authorization
			*/
		   $nvpstr="&TOKEN=".$token;

		 /* Make the API call and store the results in an array.  If the
			call was a success, show the authorization details, and provide
			an action to complete the payment.  If failed, show the error
			*/
		   $resArray=hash_call("GetExpressCheckoutDetails",$nvpstr);
		   $_SESSION['reshash']=$resArray;
		   $ack = strtoupper($resArray["ACK"]);

		   if($ack == "SUCCESS"){
$paymentAmount =urlencode ($_SESSION['paymentAmount']);
$paymentType = urlencode($_SESSION['paymentType']);
$currencyCodeType = urlencode($_SESSION["currencyCodeType"]);
$payerID = urlencode($_REQUEST['PayerID']);
$serverName = urlencode($_SERVER['SERVER_NAME']);

$nvpstr='&TOKEN='.$token.'&PAYERID='.$payerID.'&PAYMENTACTION='.$paymentType.'&AMT='.$paymentAmount.'&CURRENCYCODE='.$currencyCodeType.'&IPADDRESS='.$serverName ;

 /* Make the call to PayPal to finalize payment
    If an error occured, show the resulting errors
    */
$_SESSION['reshash'] = $resArray = hash_call("DoExpressCheckoutPayment",$nvpstr);

/* Display the API response back to the browser.
   If the response from PayPal was a success, display the response parameters'
   If the response was an error, display the errors received using APIError.php.
   */
$ack = strtoupper($resArray["ACK"]);

if($ack!="SUCCESS"){
// second test fails
	$showerror = true;
			   }
		   
		   }
		   else
		   	{
				//first test fails
				$showerror = true;
			  }

if($showerror)
		   	{
				//Redirecting to display errors. 
				$location = $_SERVER['REDIRECT_URL'] . "?paypal=error";
				header("Location: $location");
				exit();
			  }

// otherwise, processing will pick up with the display of the confirmation page  
			  
	}// end second pass

}
} // end paypal start

add_action("init","paypal_start");

if(!function_exists('paypal_payment') )
{
function paypal_payment() {

$resArray = $_SESSION["reshash"];

	if($id = $_SESSION["invoice"])
	{
	global $wpdb;
	$sql = $wpdb->prepare("update ".$wpdb->prefix."rsvpmaker set amountpaid=%s where id=%d",$resArray['AMT'], $id);
	$wpdb->query($sql);
	}

	return '<div id="paypal_thank_you">
	<h1>Thank you for your payment!</h1>
    <table>
        <tr>
            <td>
                Transaction ID:</td>
            <td>'.$resArray['TRANSACTIONID'].'</td>
        </tr>
        <tr>
            <td>
                Amount:</td>
            <td>'.$resArray['CURRENCYCODE'].' '.$resArray['AMT'] . '</td>
        </tr>
    </table>
	</div>
';

} } // end paypal payment

if(!function_exists('paypal_error'))
{
function paypal_error() {

$resArray=$_SESSION['reshash']; 
;?>

<h1>PayPal Error</h1>
<p>
<?php  //it will print if any URL errors 
	if(isset($_SESSION['curl_error_no'])) { 
			$errorCode= $_SESSION['curl_error_no'] ;
			$errorMessage=$_SESSION['curl_error_msg'] ;	
			session_unset();	
;?>

   
Error Number: <?php echo  $errorCode ;?><br />
Error Message:
		<?php echo  $errorMessage ;?>
	<br />
	
<?php } else {

/* If there is no URL Errors, Construct the HTML page with 
   Response Error parameters.   
   */
;?>

		Ack:
		<?php echo  $resArray['ACK'] ;?>
	<br />
	
		Correlation ID:
		<?php echo  $resArray['CORRELATIONID'] ;?>
	<br />
	
		Version:
		<?php echo  $resArray['VERSION'];?>
	<br />
<?php
	$count=0;
	while (isset($resArray["L_SHORTMESSAGE".$count])) {		
		  $errorCode    = $resArray["L_ERRORCODE".$count];
		  $shortMessage = $resArray["L_SHORTMESSAGE".$count];
		  $longMessage  = $resArray["L_LONGMESSAGE".$count]; 
		  $count=$count+1; 
;?>
	
		Error Number:
		<?php echo  $errorCode ;?>
	<br />
	
		Short Message:
		<?php echo  $shortMessage ;?>
	<br />
	
		Long Message:
		<?php echo  $longMessage ;?>
	<br />
	
<?php }//end while
}// end else


	return;
} } // end paypal error

if(!function_exists('event_scripts'))
{
function event_scripts() {
global $post;
global $rsvp_options;

if( is_object($post) && ( ($post->post_type == 'rsvpmaker') || strstr($post->post_content,'[rsvpmaker_') ) )
	{
	wp_enqueue_script('jquery');
	$myStyleUrl = (isset($rsvp_options["custom_css"]) && $rsvp_options["custom_css"]) ? $rsvp_options["custom_css"] : WP_PLUGIN_URL . '/rsvpmaker/style.css';
	wp_register_style('rsvp_style', $myStyleUrl);
	wp_enqueue_style( 'rsvp_style');
	}
} } // end event scripts

add_action('wp','event_scripts');

if(!function_exists('basic_form') ) {
function basic_form() {
global $rsvp_options;
global $custom_fields;
if(isset($custom_fields["_rsvp_form"][0]))
	echo do_shortcode($custom_fields["_rsvp_form"][0]);
else
	echo do_shortcode($rsvp_options["rsvp_form"]);
}
}

if(!function_exists('event_content') )
{
function event_content($content) {
global $wpdb;
global $post;
global $rsvp_options;
global $profile;
global $guestedit;
$rsvpconfirm = '';

//If the post is not an event, leave it alone
if($post->post_type != 'rsvpmaker' )
	return $content;

//On return from paypal payment process, show confirmation
if(isset($_GET["PayerID"]))
	return paypal_payment();

//Show paypal error for payment gone wrong
if(isset($_GET["paypal"]) && ($_GET["paypal"] == 'error'))
	return paypal_error();

global $custom_fields; // make this globally accessible
$custom_fields = get_post_custom($post->ID);
$permalink = get_permalink($post->ID);

if(isset($custom_fields["_rsvp_on"][0]))
$rsvp_on = $custom_fields["_rsvp_on"][0];
if(isset($custom_fields["_rsvp_to"][0]))
$rsvp_to = $custom_fields["_rsvp_to"][0];
if(isset($custom_fields["_rsvp_max"][0]))
$rsvp_max = $custom_fields["_rsvp_max"][0];
$rsvp_show_attendees = (isset($custom_fields["_rsvp_show_attendees"][0]) && $custom_fields["_rsvp_show_attendees"][0]) ? 1 : 0;
if(isset($custom_fields["_rsvp_deadline"][0]))
	$deadline = (int) $custom_fields["_rsvp_deadline"][0];
if(isset($custom_fields["_rsvp_start"][0]))
	$rsvpstart = (int) $custom_fields["_rsvp_start"][0];
$rsvp_instructions = (isset($custom_fields["_rsvp_instructions"][0])) ? $custom_fields["_rsvp_instructions"][0] : NULL;
$rsvp_confirm = (isset($custom_fields["_rsvp_confirm"][0])) ? $custom_fields["_rsvp_confirm"][0] : NULL;
$e = (isset($_GET["e"]) ) ? $_GET["e"] : NULL;
if ( $e && !filter_var($e, FILTER_VALIDATE_EMAIL) )
	$e = '';

if(isset($_GET["rsvp"]))
	{
	$rsvpconfirm = '<div id="rsvpconfirm" >
<h3>RSVP Recorded</h3>	
<p>'.nl2br($rsvp_confirm).'</p></div>
';
	}

if($e)
	{
	$sql = "SELECT * FROM ".$wpdb->prefix."rsvpmaker WHERE event=".$post->ID." AND email='".$e."'";
	$rsvprow = $wpdb->get_row($sql, ARRAY_A);

	if($rsvprow)
		{
		$answer = ($rsvprow["yesno"]) ? __("Yes",'rsvpmaker') : __("No",'rsvpmaker');
		$rsvpconfirm .= "<div class=\"rsvpdetails\"><p>".__('Your RSVP','rsvpmaker').": $answer</p>\n";
		
		$details = unserialize($rsvprow["details"]);
		if(isset($details["total"]) && $details["total"])
			{
			$nonce= wp_create_nonce('pp-nonce');
			$rsvpconfirm .= "<p><strong>".__('Pay by PayPal for','rsvpmaker')." ".$details["payingfor"].' = '.number_format($details["total"],2,$rsvp_options["currency_decimal"],$rsvp_options["currency_thousands"]).' ' . $rsvp_options["paypal_currency"]."</strong></p>".
			'<form method="post" name="donationform" id="donationform" action="'.$permalink.'">
<input type="hidden" name="paypal" value="payment" /> 
<p>Amount: '.$details["total"].'<input name="paymentAmount" type="hidden" id="paymentAmount" size="10" value="'.$details["total"].'"> '.$rsvp_options["paypal_currency"].'
    </p>
  <p>Email: <input name="email" type="text" id="email" size="40"  value="'.$e.'" >
    </p>
<input name="desc" type="hidden" id="desc" value="'.htmlentities($post->post_title).'" >
<input name="invoice" type="hidden" id="invoice" value="'.$rsvprow["id"].'" >
<input name="rsvp-pp-nonce" type="hidden" id="rsvp-pp-nonce" value="'.$nonce.'" >
<input type="submit" name="Submit" value="Next &gt;&gt;">
</form> 

<p>'.__('Secure payment processing is provided by <strong>PayPal</strong>. After you click &quot;Next,&quot; we will transfer you to the PayPal website, where you can pay by credit card or with a PayPal account.','rsvpmaker').' </p>';
			}
		
		$guestsql = "SELECT * FROM ".$wpdb->prefix."rsvpmaker WHERE master_rsvp=".$rsvprow["id"];
		if($results = $wpdb->get_results($guestsql, ARRAY_A) )
			{
			//print_r($results);
			$rsvpconfirm .=  "<p>Guests:</p>";
			foreach($results as $row)
				{
				$rsvpconfirm .= $row["first"]." ".$row["last"]."<br />";
				$guestedit .= sprintf('<div class="guest_exist">First Name: <input type="text" name="guestfirst[]" value="%s" /> Last Name: <input type="text" name="guestlast[]" value="%s" /><input type="hidden" name="guestid[]" value="%d" /><br /><input type="checkbox" name="guestdelete[%d]" value="1" /> Remove %s %s</div>',$row["first"], $row["last"], $row["id"], $row["id"],$row["first"], $row["last"]);
				}
			}

		$rsvpconfirm .= "</p></div>\n";
		
		}
	
	$sql = "SELECT details FROM ".$wpdb->prefix."rsvpmaker WHERE email='".$e."' ORDER BY id DESC";
	if($details = $wpdb->get_var($sql) )
		$profile = unserialize($details);
	}

	if(!$profile)
		$profile = rsvpmaker_profile_lookup($e);


$sql = "SELECT * FROM ".$wpdb->prefix."rsvp_dates WHERE postID=".$post->ID.' ORDER BY datetime';
$results = $wpdb->get_results($sql,ARRAY_A);
if($results)
{
$start = 2;
$firstrow = NULL;
$dateblock = '';
foreach($results as $row)
	{
	if(!$firstrow)
		$firstrow = $row;
	$last_time = $t = strtotime($row["datetime"]);
	$dateblock .= '<div itemprop="startDate" datetime="'.date('c',$t).'">';
	$dateblock .= date($rsvp_options["long_date"],$t);
	$dur = $row["duration"];
	if($dur != 'allday')
		$dateblock .= date(' '.$rsvp_options["time_format"],$t);
	if(is_numeric($dur) )
		$dateblock .= " to ".date ($rsvp_options["time_format"],$dur);
	$dateblock .= "</div>\n";
	}
}

$content = '<div class="dateblock">'.$dateblock."\n</div>\n".$rsvpconfirm.$content;

if(!isset($rsvp_on) || !$rsvp_on)
	return $content;

//check for responses so far
$sql = "SELECT first,last,note FROM ".$wpdb->prefix."rsvpmaker WHERE event=$post->ID AND yesno=1 ORDER BY id DESC";
$attendees = $wpdb->get_results($sql);
	$total = sizeof($attendees); //(int) $wpdb->get_var($sql);

if(isset($rsvp_max) && $rsvp_max)
	{
	$content .= '<p class="signed_up">'.$total.' '.__('signed up so far. Limit: ','rsvpmaker'). "$rsvp_max.</p>\n";
	if($total >= $rsvp_max)
		$too_many = true;
	}
else
	$content .= '<p class="signed_up">'.$total.' '. __('signed up so far.','rsvpmaker').'</p>';

$now = current_time('timestamp');

if(isset($deadline) && ($now  > $deadline  ) )
	$content .= '<p class="rsvp_status">'.__('RSVP deadline is past','rsvpmaker').'</p>';
elseif( ( $now > $last_time  ) )
	$content .= '<p class="rsvp_status">'.__('Event date is past','rsvpmaker').'</p>';
elseif(isset($rsvpstart) && ( $now < $rsvpstart  ) )
	$content .= '<p class="rsvp_status">'.__('RSVPs accepted starting: ','rsvpmaker').date($rsvp_options["long_date"],$rsvpstart).'</p>';
elseif(isset($too_many))
	$content .= '<p class="rsvp_status">'.__('RSVPs are closed','rsvpmaker').'</p>';
elseif(($rsvp_on && is_admin()) ||  ($rsvp_on && isset($_GET["load"])) ||  ($rsvp_on && !is_single()) ) // when loaded into editor
	$content .= sprintf($rsvp_options["rsvplink"],get_permalink( $post->ID ) );
elseif($rsvp_on && is_single() )
	{
	ob_start();
	echo '<div id="rsvpsection">';

;?>

<form id="rsvpform" action="<?php echo $permalink;?>" method="post">

<h3 id="rsvpnow"><?php echo __('RSVP Now!','rsvpmaker');?></h3> 

  <?php if($rsvp_instructions) echo '<p>'.nl2br($rsvp_instructions).'</p>';?>

  <?php if($rsvp_show_attendees) echo '<p class="rsvp_status">'.__('Names of attendees will be displayed publicly, along with the contents of the notes field.','rsvpmaker').'</p>';?>
   
  <p><?php echo __('Your Answer','rsvpmaker');?>:
            <input name="yesno" type="radio" value="1" <?php echo (!isset($rsvprow) || $rsvprow["yesno"]) ? 'checked="checked"' : '';?> /> 
    <?php echo __('Yes','rsvpmaker');?>
    <input name="yesno" type="radio" value="0" /> 
    <?php echo __('No','rsvpmaker');?></p> 
<?php

wp_nonce_field('rsvp','rsvp_nonce');

if($dur && ( $slotlength = $custom_fields["_rsvp_timeslots"][0] ))
{
;?>

<div><?php echo __('Number of Participants','rsvpmaker');?>: <select name="participants">
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
  </select></div>

<div><?php echo __('Choose timeslots','rsvpmaker');?></div>
<?php
$t = strtotime($firstrow["datetime"]);
$dur = $firstrow["duration"];
$day = date('j',$t);
$month = date('n',$t);
$year = date('Y',$t);
$hour = date('G',$t);
$minutes = date('i',$t);
$slotlength = explode(":",$slotlength);
$min_add = $slotlength[0]*60;
$min_add = $min_add + $slotlength[1];

for($i=0; ($slot = mktime($hour ,$minutes + ($i * $min_add),0,$month,$day,$year)) < $dur; $i++)
	{
	$sql = "SELECT SUM(participants) FROM ".$wpdb->prefix."rsvp_volunteer_time WHERE time=$slot AND event = $post->ID";
	$signups = ($signups = $wpdb->get_var($sql)) ? $signups : 0;
	echo '<div><input type="checkbox" name="timeslot[]" value="'.$slot.'" /> '.date(' '.$rsvp_options["time_format"],$slot)." $signups participants signed up</div>";
	}
}


if(isset($custom_fields["_per"][0]) && $custom_fields["_per"][0])
{
echo "<h3>".__('Paying For','rsvpmaker')."</h3><p>";
$per = unserialize($custom_fields["_per"][0]);
foreach($per["unit"] as $index => $value)
	{
	$price = $per["price"][$index];
	echo '<select name="payingfor['.$index.']">
    <option value="0">0</option>
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
  </select>
<input type="hidden" name="unit['.$index.']" value="'.$value.'" />'.$value.' @ <input type="hidden" name="price['.$index.']" value="'.$price.'" />'.(($rsvp_options["paypal_currency"] == 'USD') ? '$' : $rsvp_options["paypal_currency"]).' '.number_format($price,2,$rsvp_options["currency_decimal"],$rsvp_options["currency_thousands"]).'<br />';
	}
echo "</p>\n";
}

basic_form($profile, $guestedit);

if(isset($custom_fields["_rsvp_captcha"][0]) && $custom_fields["_rsvp_captcha"][0])
{
?>
<p>          <img src="<?php echo plugins_url('/captcha/captcha_ttf.php',__FILE__);  ?>"
                    alt="CAPTCHA image">
<br />
		Type the hidden security message:<br />                    
<input maxlength="10" size="10" name="captcha" type="text" />
</p>
<?php
}
?>
        <p> 
		  <input type="hidden" name="event" value="<?php echo $post->ID;?>" /> 
          <input type="submit" id="rsvpsubmit" name="Submit" value="Submit" /> 
        </p> 

</form>	

</div>
<?php
	$content .= ob_get_clean();
	}

if(isset($_GET["err"]))
	{
	$error = $_GET["err"];
	if(strpos($error,'email') != false)
		$content = '<div id="rsvpconfirm" >
<h3 class="rsvperror">Error: Invalid Email</h3>
<p>Please correct your submission.</p>
</div>
'.$content;
	elseif(strpos($error,'code') != false)
		$content = '<div id="rsvpconfirm" >
<h3 class="rsvperror">Error: Security code not entered correctly</h3>
<p>Please correct your submission.</p>
</div>
'.$content;
	else
		$content = '<div id="rsvpconfirm" >
<h3 class="rsvperror">Error: Invalid Input</h3>
<p>Please correct your submission.</p>
</div>
'.$content;
	}

if($rsvp_show_attendees && $total && !isset($_GET["load"]) )
	{
$content .= '<p><button class="rsvpmaker_show_attendees" onclick="'."jQuery.get('".site_url()."/?ajax_guest_lookup=".$post->ID."', function(data) { jQuery('#attendees-".$post->ID."').html(data); } );". '">'. __('Show Attendees','rsvpmaker') .'</button></p>
<div id="attendees-'.$post->ID.'"></div>';
	}

return $content;
} } // end event content



if(!function_exists('rsvp_report') )
{
function rsvp_report() {

global $wpdb;
global $rsvp_options;
$wpdb->show_errors();
?>
<div class="wrap"> 
	<div id="icon-edit" class="icon32"><br /></div>
<h2>RSVP Report</h2> 
<?php

if(isset($_POST["deletenow"]) && current_user_can('edit_others_posts'))
	{
	
	if(!wp_verify_nonce($_POST["deletenonce"],'rsvpdelete') )
		die("failed security check");
	
	foreach($_POST["deletenow"] as $d)
		$wpdb->query("DELETE FROM ".$wpdb->prefix."rsvpmaker where id=$d");
	}

if(isset($_GET["delete"]) && current_user_can('edit_others_posts'))
	{
	$delete = $_GET["delete"];
	$row = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."rsvpmaker WHERE id=$delete");

	$guests = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."rsvpmaker WHERE master_rsvp=$delete");
	foreach($guests as $guest)
		$guestcheck .= sprintf('<input type="checkbox" name="deletenow[]" value="%s" checked="checked" /> Delete guest: %s %s<br />',$guest->id,$guest->first,$guest->last);

	echo sprintf('<form action="%s" method="post">
<h2 style="color: red;">Confirm Delete for %s %s</h2>
<input type="hidden" name="deletenow[]" value="%s"  />
%s
<input type="hidden" name="deletenonce" value="%s"  />
<input type="submit" style="color: red;" value="Delete Now"  />
</form>
',admin_url().'edit.php?post_type=rsvpmaker&page=rsvp',$row->first,$row->last,$delete,$guestcheck,wp_create_nonce('rsvpdelete') );
	}


if(isset($_GET["event"]))
	{
$eventid = (int) $_GET["event"];	
$sql = "SELECT *
FROM `".$wpdb->prefix."rsvp_dates`
JOIN ".$wpdb->prefix."posts ON ".$wpdb->prefix."rsvp_dates.postID = ".$wpdb->prefix."posts.ID WHERE ".$wpdb->prefix."posts.ID = $eventid";
	$row = $wpdb->get_row($sql);
	$title = $row->post_title;
	$t = strtotime($row->datetime);
	$title .= " ".date('F jS',$t);
	
	echo "<h2>".__("RSVPs for",'rsvpmaker')." ".$title."</h2>\n";
	if(!isset($_GET["rsvp_print"]))
		{
		echo '<div style="float: right; margin-left: 15px; margin-bottom: 15px;"><a href="edit.php?post_type=rsvpmaker&page=rsvp">Show Events List</a>
<a href="edit.php?post_type=rsvpmaker&page=rsvp&event='.$eventid.'&rsvp_order=alpha">Alpha Order</a> <a href="edit.php?post_type=rsvpmaker&page=rsvp&event='.$eventid.'&rsvp_order=timestamp">Most Recent First</a>		
		</div>';
		echo '<p><a href="'.$_SERVER['REQUEST_URI'].'&rsvp_print='.wp_create_nonce('rsvp_print').'" target="_blank" >Format for printing</a></p>';	
		echo '<p><a href="#excel">Download to Excel</a></p>';
		}

	$rsvp_order = (isset($_GET["rsvp_order"]) && ($_GET["rsvp_order"] == 'alpha')) ? ' ORDER BY yesno DESC, last, first' : ' ORDER BY yesno DESC, timestamp DESC';
	$sql = "SELECT * FROM ".$wpdb->prefix."rsvpmaker WHERE event=$eventid $rsvp_order";
	$wpdb->show_errors();
	$results = $wpdb->get_results($sql, ARRAY_A);

	format_rsvp_details($results);

	if(isset($rsvp_options["debug"]))
		{
		echo "<p>DEBUG: $sql</p>";
		echo "<pre>Results:\n";
		print_r($results);
		echo "</pre>";
		}

	}
else
{// show events list

$sql = "SELECT *
FROM `".$wpdb->prefix."rsvp_dates`
JOIN ".$wpdb->prefix."posts ON ".$wpdb->prefix."rsvp_dates.postID = ".$wpdb->prefix."posts.ID ";
$eventlist = "";
if(!isset($_GET["show"]))
	{
	$sql .= " WHERE datetime > CURDATE( ) ";
	$eventlist .= '<p>Showing future events only (<a href="'.$_SERVER['REQUEST_URI'].'&show=all">show all</a>)<p>';
	}
$sql .= " ORDER BY datetime";

$wpdb->show_errors();
$results = $wpdb->get_results($sql);

	if(isset($rsvp_options["debug"]))
		{
		echo "<p>$sql</p>";
		echo "<pre>Results:\n";
		print_r($results);
		echo "</pre>";
		}


if($results)
{

foreach($results as $row)
	{
	if(!isset($events[$row->postID]))
		$events[$row->postID] = $row->post_title;
	$t = strtotime($row->datetime);
	$events[$row->postID] .= " ".date('F jS',$t);
	}
}

if($events)
foreach($events as $postID => $event)
	{
	$eventlist .= "<h3>$event</h3>";
	$sql = "SELECT count(*) FROM ".$wpdb->prefix."rsvpmaker WHERE yesno=1 AND event=".$postID;
	if($rsvpcount = $wpdb->get_var($sql) )
		$eventlist .= '<p><a href="'.admin_url().'edit.php?post_type=rsvpmaker&page=rsvp&event='.$postID.'">'. __('RSVP','rsvpmaker'). ' '.__('Yes','rsvpmaker').': '.$rsvpcount."</a></p>";
	}

if($eventlist && !isset($_GET["rsvp_print"]))
	echo "<h2>Events</h2>\n".$eventlist;
}


} } // end rsvp report

if(!function_exists('format_rsvp_details') )
{
function format_rsvp_details($results) {
	
	global $rsvp_options;
	
	if($results)
	$fields = array('yesno','first','last','email','guestof','amountpaid');
	foreach($results as $index => $row)
		{
		$row["yesno"] = ($row["yesno"]) ? "YES" : "NO";
		
		echo '<h3>'.$row["yesno"]." ".esc_attr($row["first"])." ".esc_attr($row["last"])." ".$row["email"];
		if($row["guestof"])
			echo " (". __('guest of','rsvpmaker')." ".esc_attr($row["guestof"]).")";
		echo "</h3>";

		if($row["amountpaid"] > 0)
			echo '<div style="color: #006400;font-weight: bold;">Paid: '.$row["amountpaid"]."</div>";		

		echo "<p>";
		if($row["details"])
			{
			$details = unserialize($row["details"]);
			foreach($details as $name => $value)
				if($value) {
					echo $name.': '.esc_attr($value)."<br />";
					if(!in_array($name,$fields) )
						$fields[] = $name;
					}
			}
		if($row["note"])
			echo "note: " . nl2br(esc_attr($row["note"]))."<br />";
		$t = strtotime($row["timestamp"]);
		echo 'posted: '.date($rsvp_options["short_date"],$t);
		echo "</p>";
		
		if(!isset($_GET["rsvp_print"]) && current_user_can('edit_others_posts'))
			echo sprintf('<p><a href="%s&delete=%d">Delete record for: %s %s</a></p>',admin_url().'edit.php?post_type=rsvpmaker&page=rsvp',$row["id"],esc_attr($row["first"]),esc_attr($row["last"]) );
		}

global $phpexcel_enabled; // set if excel extension is active
if($fields && !isset($_GET["rsvp_print"]))
	{
	$fields[]='note'; 
;?>
<div id="excel" name="excel" style="padding: 10px; border: thin dotted #333; width: 300px;margin-top: 30px;">
<?php
if(isset($phpexcel_enabled))
{
?>
<h3>Download to Excel</h3>
<form method="get" action="edit.php">
<?php
foreach($_GET as $name => $value)
	echo sprintf('<input type="hidden" name="%s" value="%s" />',$name,$value);

foreach($fields as $field)
	echo '<input type="checkbox" name="fields[]" value="'.$field.'" checked="checked" /> '.$field . "<br />\n";
wp_nonce_field('rsvpexcel','rsvpexcel');
?>
<button>Get Spreadsheet</button>
</form>
<?php
}
else
	echo "Additional RSVPMaker Excel plugin required for download to Excel function.";
?>
</div>
<?php
	}

echo "</div>\n";
} } // end format_rsvp_details

if(!function_exists('rsvp_print') ) {
function rsvp_print() {
if(!isset($_GET["rsvp_print"]))
	return;

if(!wp_verify_nonce($_GET["rsvp_print"],'rsvp_print') )
	die("Security error");

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>RSVP REPORT</title>
</head>

<body>
';
rsvp_report();
echo "</body></html>";
exit();
} } // end rsvp_print

add_action('admin_init','rsvp_print');

if(!function_exists('get_spreadsheet_data') )
{
function get_spreadsheet_data($eventid) {
global $wpdb;

	$sql = "SELECT yesno,first,last,email, details, note, guestof FROM ".$wpdb->prefix."rsvpmaker WHERE event=$eventid ORDER BY yesno DESC, last, first";
	$results = $wpdb->get_results($sql, ARRAY_A);
	
	foreach($results as $index => $row)
		{
		$srow["answer"] = ($row["yesno"]) ? "YES" : "NO";
		$srow["name"] = $row["first"]." ".$row["last"];
		
		$details = unserialize($srow["details"]);
		
		$srow["address"] = $details["address"]." ".$details["city"]." ".$details["state"]." ".$details["zip"];
		$srow["employment"] = $details["occupation"]." ".$details["company"];
		$srow["email"] = $row["email"];
		$srow["guestof"] = $row["guestof"];
		$srow["note"] = $row["note"];
		$spreadsheet[] = $srow;
		}
return $spreadsheet;
} } // end get spreadsheet data

if(!function_exists('widgetlink') ) {
function widgetlink($evdates,$plink,$evtitle) {
	return sprintf('<a href="%s">%s</a> %s',$plink,$evtitle,$evdates);
} } // end widgetlink

if(!function_exists('rsvpmaker_profile_lookup') ) {
function rsvpmaker_profile_lookup($email = '') {

// placeholder - override to implement alternate profile lookup based on login, membership, email list, etc.
if(!$email)
	{
	// if members are registered and logged in, retrieve basic info for profile
	if(is_user_logged_in() )
		{
		global $current_user;
		$profile["email"] = $current_user->user_email;
		$profile["first"] = $current_user->first_name;
		$profile["last"] = $current_user->last_name;		
		}
	else
		$profile = NULL;
	}
return $profile;
} }

if(!function_exists('ajax_guest_lookup') )
{
function ajax_guest_lookup() {
if(!isset($_GET["ajax_guest_lookup"]))
	return;
$event = $_GET["ajax_guest_lookup"];
global $wpdb;

$sql = "SELECT first,last,note FROM ".$wpdb->prefix."rsvpmaker WHERE event=$event AND yesno=1 ORDER BY id DESC";
$attendees = $wpdb->get_results($sql);
echo '<div class="attendee_list">';
foreach($attendees as $row)
	{
;?>
<h3 class="attendee"><?php echo $row->first;?> <?php echo $row->last;?></h3>
<?php	
if($row->note);
echo wpautop($row->note);
	}
echo '</div>';
exit();
} }

add_action('init','ajax_guest_lookup');

add_action('rsvp_daily_reminder_event', 'rsvp_daily_reminder');

function rsvp_reminder_activation() {
	if ( !wp_next_scheduled( 'rsvp_daily_reminder_event' ) ) {
		$hour = 12 - get_option('gmt_offset');
		$t = mktime($hour,0,0);
		wp_schedule_event(time(), 'daily', 'rsvp_daily_reminder_event');
	}
}

function rsvp_reminder_reset($basehour) {
	wp_clear_scheduled_hook('rsvp_daily_reminder_event'); //
	$hour = $basehour - get_option('gmt_offset');
	$t = mktime($hour,0,0);
	wp_schedule_event($t, 'daily', 'rsvp_daily_reminder_event');
}

add_action('wp', 'rsvp_reminder_activation');

if(!function_exists('rsvp_daily_reminder') )
{
function rsvp_daily_reminder() {
global $wpdb;
global $rsvp_options;

$today = date('Y-m-d');
$sql = "SELECT * FROM `wp_postmeta` WHERE `meta_key` LIKE '_rsvp_reminder' AND `meta_value`='$today'";
if( $reminders = $wpdb->get_results($sql) )
	{
	foreach($reminders as $reminder)
		{
		$postID = $reminder->post_id;
		$q = "p=$postID&post_type=rsvpmaker";
		echo "Post $postID is scheduled for a reminder $q<br />";
		global $post;
		query_posts($q);
		global $wp_query;
		// treat as single, display rsvp button, not form
		$wp_query->is_single = false;
		the_post();

		if($post->post_title)
			{
			$event_title = $post->post_title;
			ob_start();
			echo "<h1>";
			the_title();
			echo "</h1>\n<div>\n";	
			the_content();
			echo "\n</div>\n";
			$event = ob_get_clean();
			
			$rsvpto = get_post_meta($postID,'_rsvp_to',true);
			
			$sql = "SELECT * FROM ".$wpdb->prefix."rsvpmaker WHERE event=$postID AND yesno=1";
			$rsvps = $wpdb->get_results($sql,ARRAY_A);
			if($rsvps)
			foreach($rsvps as $row)
				{
				$notify = $row["email"];

				$row["yesno"] = ($row["yesno"]) ? "YES" : "NO";
				
				$notification = "<p>".__("This is an automated reminder that we have you on the RSVP list for the event shown below. If your plans have changed, you can update your response by clicking on the RSVP button again.",'rsvpmaker')."</p>";
				$notification .= '<h3>'.$row["yesno"]." ".$row["first"]." ".$row["last"]." ".$row["email"];
				if($row["guestof"])
					$notification .=  " (". __('guest of','rsvpmaker')." ".$row["guestof"].")";
				$notification .=  "</h3>\n";
				$notification .=   "<p>";
				if($row["details"])
					{
					$details = unserialize($row["details"]);
					foreach($details as $name => $value)
						if($value) {
							$notification .=  "$name: $value<br />";
							}
					}
				if($row["note"])
					$notification .= "note: " . nl2br($row["note"])."<br />";
				$t = strtotime($row["timestamp"]);
				$notification .= 'posted: '.date($rsvp_options["short_date"],$t);
				$notification .=  "</p>";
				$notification .=  "<h3>Event Details</h3>\n".str_replace('*|EMAIL|*',$notify,$event);
				
				echo "Notification for $notify<br />$notification";
				$subject = '=?UTF-8?B?'.base64_encode( __("Event Reminder for",'rsvpmaker').' '.$event_title ).'?=';
				mail($notify,$subject,$notification,"From: $rsvpto\nContent-Type: text/html; charset=UTF-8");
				}
			}
		}
	}
	else
		echo "none found";
}
}// end

if(!function_exists('rsvpguests') )
{
function rsvpguests() {
global $guestedit;

return "
<!-- guest section -->
        <p id=\"guest_section\"><strong>". __('Guests','rsvpmaker').":</strong>". __('If you are bringing guests, please enter their names here','rsvpmaker'). "</p>
".$guestedit."
<div class=\"guest_blank\">". __('First Name','rsvpmaker').": <input type=\"text\" name=\"guestfirst[]\" style=\"width:30%\" /> ". __('Last Name','rsvpmaker').": <input type=\"text\" name=\"guestlast[]\" style=\"width:30%\" /><input type=\"hidden\" name=\"guestid[]\" value=\"0\" /></div><div class=\"add_one\"></div>
        <a href=\"#guest_section\" id=\"add_guests\" name=\"add_guests\">(+) ". __('Add more guests','rsvpmaker')."</a></p>
<script>
jQuery(document).ready(function($) {

$('#add_guests').click(function(){
	$('.add_one').append('<div class=\"guest_blank\">First Name: <input type=\"text\" name=\"guestfirst[]\" style=\"width:30%\" /> Last Name: <input type=\"text\" name=\"guestlast[]\" style=\"width:30%\"/><input type=\"hidden\" name=\"guestid[]\" value=\"0\" /></div>');
	});
});
</script>
<!-- end of guest section-->
";

}
}

add_shortcode('rsvpguests','rsvpguests');

if(!function_exists('rsvpprofiletable') )
{
function rsvpprofiletable( $atts, $content = null ) {
global $profile;
if(!isset($atts["show_if_empty"]) || !(isset($profile[$atts["show_if_empty"]]) && $profile[$atts["show_if_empty"]]) )
	return do_shortcode($content);
else
	{
return '
<p id="profiledetails">'. __('Profile details on file. To update profile, 
or RSVP for someone else','rsvpmaker').' <a href="'.get_permalink().'">'. __('fetch a blank 
form','rsvpmaker').'</a></p>
<input type="hidden" name="onfile" value="1" />';
	}

}
}
add_shortcode('rsvpprofiletable','rsvpprofiletable');

if(!function_exists('rsvpfield') )
{
function rsvpfield($atts) {
global $profile;
if(isset($atts["textfield"])) {
	$field = $atts["textfield"];
	$size = ( isset($atts["size"]) ) ? ' size="'.$atts["size"].'" ' : '';
	$data = ( isset($profile[$field]) ) ? ' value="'.$profile[$field].'" ' : '';
	return '<input type="text" name="profile['.$field.']" id="'.$field.'" '.$size.$data.' />';
	}
elseif(isset($atts["selectfield"])) {
	$field = $atts["selectfield"];
	$output = '<select name="profile['.$field.']" id="'.$field.'" >';
	if(isset($atts["options"]))
		{
			$o = explode(',',$atts["options"]);
			foreach($o as $i)
				{
					$i = trim($i);
					$output .= '<option value="'.$i.'">'.$i.'</option>';
				}
		}
		$output .= '</select>';
	return $output;
	}
}
}

add_shortcode('rsvpfield','rsvpfield');

if(!function_exists('my_rsvp_menu'))
{
function my_rsvp_menu() {
global $rsvp_options;
add_submenu_page('edit.php?post_type=rsvpmaker', "RSVP Report", "RSVP Report", $rsvp_options["menu_security"], "rsvp", "rsvp_report" );
add_submenu_page('edit.php?post_type=rsvpmaker', "Recurring Event", "Recurring Event", 'manage_options', "add_dates", "add_dates" );
add_submenu_page('edit.php?post_type=rsvpmaker', "Multiple Events", "Multiple Events", 'manage_options', "multiple", "multiple" );
add_submenu_page('edit.php?post_type=rsvpmaker', "Documentation", "Documentation", $rsvp_options["menu_security"], "rsvpmaker_doc", "rsvpmaker_doc" );
if(isset($rsvp_options["debug"]) && $rsvp_options["debug"])
	add_submenu_page('edit.php?post_type=rsvpmaker', "Debug", "Debug", 'manage_options', "rsvpmaker_debug", "rsvpmaker_debug");
}
}//end my_rsvp_menu

?>