<?php

// start customizable functions, can be overriden by adding a custom.php file to rsvpmaker directory

if(! function_exists('GetRSVPAdminForm') )
{
function GetRSVPAdminForm($postID)
{
$custom_fields = get_post_custom($postID);
$rsvp_on = $custom_fields["_rsvp_on"][0];
$rsvp_to = $custom_fields["_rsvp_to"][0];
$rsvp_instructions = $custom_fields["_rsvp_instructions"][0];
$rsvp_confirm = $custom_fields["_rsvp_confirm"][0];
$rsvp_max = $custom_fields["_rsvp_max"][0];
$rsvp_show_attendees = $custom_fields["_rsvp_show_attendees"][0];
$rsvp_captcha = $custom_fields["_rsvp_captcha"][0];

if($custom_fields["_rsvp_deadline"][0])
	{
	$t = (int) $custom_fields["_rsvp_deadline"][0];
	$deadyear = date('Y',$t);
	$deadmonth = date('m',$t);
	$deadday = date('d',$t);
	}

if($custom_fields["_rsvp_start"][0])
	{
	$t = (int) $custom_fields["_rsvp_start"][0];
	$startyear = date('Y',$t);
	$startmonth = date('m',$t);
	$startday = date('d',$t);
	}

global $rsvp_options;

if($rsvp_on.$rsvp_to.$rsvp_instructions.$rsvp_confirm == '')
	{
	echo '<p>'.__('Loading default values for RSVPs - check the checkbox to collect RSVPs online','rsvpmaker') .'</p>';
	$rsvp_to = $rsvp_options["rsvp_to"];
	$rsvp_instructions = $rsvp_options["rsvp_instructions"];
	$rsvp_confirm = $rsvp_options["rsvp_confirm"];
	$rsvp_on = $rsvp_options["rsvp_on"];
	$rsvp_captcha = $rsvp_options["rsvp_captcha"];
	$rsvp_max = 0;
	$rsvp_show_attendees = $rsvp_options["show_attendees"];
	}
//get_post_meta($post->ID, '_rsvp_on', true)
;?>
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
<textarea id="rsvp[instructions]" name="setrsvp[instructions]" cols="80"><?php echo $rsvp_instructions;?></textarea>
<br /><?php echo __('Confirmation Message','rsvpmaker');?>:<br />
<textarea id="rsvp[confirm]" name="setrsvp[confirm]" cols="80"><?php echo $rsvp_confirm;?></textarea>

<br /><strong>Special Options</strong>

<table><tr><td><?php echo __('Deadline (optional)','rsvpmaker').'</td><td> '.__('Month','rsvpmaker');?>: <input type="text" name="deadmonth" id="deadmonth" value="<?php echo $deadmonth;?>" size="2" /> <?php echo __('Day','rsvpmaker');?>: <input type="text" name="deadday" id="deadday" value="<?php echo $deadday;?>" size="2" /> <?php echo __('Year','rsvpmaker');?>: 
<input type="text" name="deadyear" id="deadyear" value="<?php echo $deadyear;?>" size="4" /> (<?php echo __('stop collecting RSVPs at midnight','rsvpmaker');?>)</td></tr>

<tr><td><?php echo __('Start Date (optional)','rsvpmaker').'</td><td>'.__('Month','rsvpmaker');?>: <input type="text" name="startmonth" id="startmonth" value="<?php echo $startmonth;?>" size="2" /> <?php echo __('Day','rsvpmaker');?>: <input type="text" name="startday" id="startday" value="<?php echo $startday;?>" size="2" /> <?php echo __('Year','rsvpmaker');?>: 
<input type="text" name="startyear" id="startyear" value="<?php echo $startyear;?>" size="4" /> (<?php echo __('start collecting RSVPs','rsvpmaker');?>)</td></tr></table>

<br /><?php echo __('Maximum participants','rsvpmaker');?> <input type="text" name="setrsvp[max]" id="setrsvp[max]" value="<?php echo $rsvp_max;?>" size="4" /> (<?php echo __('0 for none specified','rsvpmaker');?>)
<br /><?php echo __('Time Slots','rsvpmaker');?>:

<select name="setrsvp[timeslots]" id="setrsvp[timeslots]">
<option value="0">None</option>
<option value="0:30" <?php if($custom_fields["_rsvp_timeslots"][0] == '0:30') echo ' selected = "selected" ';?> >30 minutes</option>
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
<br />

<?php
if($rsvp_options["paypal_config"])
{
?>
<p><strong><?php echo __('Pricing for Online Payments','rsvpmaker');?></strong></p>
<p><?php echo __('You can set a different price for members vs. non-members, adults vs. children, etc.','rsvpmaker');?></p>
<?php

if($custom_fields["_per"][0])
	$per = unserialize($custom_fields["_per"][0]);
else
	$per["unit"][0] = __("Tickets",'rsvpmaker');

for($i=0; $i < 5; $i++)
{
?>
Units: <input name="unit[<?php echo $i;?>]" value="<?php echo $per["unit"][$i];?>" /> @
Price: <input name="price[<?php echo $i;?>]" value="<?php echo $per["price"][$i];?>" /> <?php echo $rsvp_options["paypal_currency"]; ?>
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

$rsvp = stripslashes_deep($_POST["profile"]);

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

if($custom_fields["_rsvp_captcha"][0])
	{
	if(!$_SESSION["captcha_key"])
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

if($_POST["onfile"])
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
			if(!$rsvp[$name])
				$rsvp[$name] = $value;
			}
		}
	}

if(is_array($_POST["payingfor"]) )
	{
	foreach($_POST["payingfor"] as $index => $value)
		{
		$unit = $_POST["unit"][$index];
		$price = $_POST["price"][$index];
		$cost = $value * $price;
		if($rsvp["payingfor"])
			$rsvp["payingfor"] .= ", ";
		$rsvp["payingfor"] .= "$value $unit @ ".number_format($price,2) . ' '.$rsvp_options["paypal_currency"];
		$rsvp["total"] += $cost;
		$participants += $value;
		}
	}

if( is_array($_POST["timeslot"]) )
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

if(!$participants && $yesno)
	{
	// if they didn't specify # of participants (paid tickets or volunteers), count the host plus guests
	$participants = 1;
	foreach($_POST["guestfirst"] as $first)
		if($first)
			$participants++;
	if($_POST["guestdelete"])
		$participants -= sizeof($_POST["guestdelete"]);
	}
if(!$yesno)
	$participants = 0; // if they said no, they don't count

$rsvp_sql = $wpdb->prepare(" SET first=%s, last=%s, email=%s, yesno=%d, event=%d, note=%s, details=%s, participants=%d ", $rsvp["first"], $rsvp["last"], $rsvp["email"],$yesno,$event, $_POST["note"], serialize($rsvp), $participants );

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

if($_POST["timeslot"])
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

foreach($rsvp as $name => $value)
	$cleanmessage .= $name.": ".$value."\n";

$guestof = $rsvp["first"]." ".$rsvp["last"];

foreach($_POST["guestfirst"] as $index => $first) {
	$last = $_POST["guestlast"][$index];
	$guestid = $_POST["guestid"][$index];
	if($first || $last)
		{
		if($_POST["guestdelete"][$guestid])
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

  $headers = "Reply-To: ".$rsvp["email"]."\r\n"; 
  $headers .= "From: ".'"'.$rsvp["first"]." ".$rsvp["last"].'" <'.$rsvp["email"].'>'."\r\n"; 
  $headers .= "Organization: ".$_SERVER['SERVER_NAME']."\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
  $headers .= "X-Priority: 3\r\n";
  $headers .= "X-Mailer: PHP". phpversion() ."\r\n"; 

mail($rsvp_to,$subject,$message,$headers);

// now send confirmation

  $headers = "Reply-To: The Sender <$rsvpto>\r\n"; 
  $headers .= "From: <".$rsvp["email"].">\r\n"; 
  $headers .= "Organization: ".$_SERVER['SERVER_NAME']."\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
  $headers .= "X-Priority: 3\r\n";
  $headers .= "X-Mailer: PHP ". phpversion() ."\r\n"; 

mail($rsvp["email"],"Confirming ".$subject,$message,$headers);

} } // end rsvp notifications


if(!function_exists('paypal_start') )
{
function paypal_start() {

global $rsvp_options;

//sets up session to display errors or initializes paypal transactions prior to page display
if( ( $_REQUEST["paypal"] == 'error' ) )
	{
	session_start();
	return;
	}
elseif( ! $_REQUEST['paymentAmount'] )
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
$currCodeType = urlencode($_SESSION['currCodeType']);
$payerID = urlencode($_REQUEST['PayerID']);
$serverName = urlencode($_SERVER['SERVER_NAME']);

$nvpstr='&TOKEN='.$token.'&PAYERID='.$payerID.'&PAYMENTACTION='.$paymentType.'&AMT='.$paymentAmount.'&CURRENCYCODE='.$currCodeType.'&IPADDRESS='.$serverName ;

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
            <td>$'. $currCodeType.' '.$resArray['AMT'] . '</td>
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

if( ($post->post_type == 'rsvpmaker') || strstr($post->post_content,'[rsvpmaker_') )
	{
	wp_enqueue_script('jquery');
	$myStyleUrl = ($rsvp_options["custom_css"]) ? $rsvp_options["custom_css"] : WP_PLUGIN_URL . '/rsvpmaker/style.css';
	wp_register_style('rsvp_style', $myStyleUrl);
	wp_enqueue_style( 'rsvp_style');
	}
} } // end event scripts

add_action('wp','event_scripts');

if(!function_exists('basic_form') ) {
function basic_form($profile, $guestedit = '')
{
//be cautious if you override this function. RSVPMaker expects tog get at least name and email address as identifiers for people who respond, and the first and last name fields are also used for alphabetical sorting and searching of RSVP records
;?>
        <table border="0" cellspacing="0" cellpadding="0"> 
          <tr> 
            <td><?php echo __('First Name','rsvpmaker');?>:</td> 
            <td> 
              <input name="profile[first]" type="text" id="first" size="60"  value="<?php echo $profile["first"];?>"  /> 
            </td> 
          </tr> 
          <tr> 
            <td><?php echo __('Last Name','rsvpmaker');?>:</td> 
            <td> 
              <input name="profile[last]" type="text" id="last" size="60"  value="<?php echo $profile["last"];?>"  /> 
            </td> 
          </tr> 
          <tr> 
            <td width="100"><?php echo __('Email','rsvpmaker');?>:</td>
            <td><input name="profile[email]" type="text" id="rsvp[email]" size="60" value="<?php echo $profile["email"];?>" /></td> 
          </tr> 
  </table> 

<?php
//by default, this displays the phone # field or a message saying this is already on file
//can also be customized to request more extensive contact info
rsvp_profile($profile);
?>

<!-- end of profile section-->      

<!-- guest section -->
        <p id="guest_section"><strong><?php echo __('Guests','rsvpmaker');?>:</strong> <?php echo __('If you are bringing guests, please enter their names here','rsvpmaker');?></p>
        <?php echo $guestedit;?>
        <div class="guest_blank"><?php echo __('First Name','rsvpmaker');?>: <input type="text" name="guestfirst[]" /> <?php echo __('Last Name','rsvpmaker');?>: <input type="text" name="guestlast[]" /><input type="hidden" name="guestid[]" value="0" /></div>
        <a href="#guest_section" id="add_guests" name="add_guests">(+) <?php echo __('Add more guests','rsvpmaker');?></a></p>
<script>
jQuery(document).ready(function($) {

$('#add_guests').click(function(){
	$('.guest_blank').append('<div class="guest_blank">First Name: <input type="text" name="guestfirst[]" /> Last Name: <input type="text" name="guestlast[]" /><input type="hidden" name="guestid[]" value="0" /></div>');
	});
});
</script>
<!-- end of guest section-->              
        
<p><?php echo __('Note','rsvpmaker');?>:<br /> 
<textarea name="note" cols="60" rows="2" id="note"></textarea> 
</p> 

<?php

} }

// customize the fields to be included with the RSVP form (in addition to name and email)
if(!function_exists('rsvp_profile') ) {
function rsvp_profile($profile) {

if($profile["phone"])
	{
;?>
<p id="profiledetails"><?php printf( __('Phone # on file.<br />To update profile, 
or RSVP for someone else <a href="%s">fetch a blank 
form</a>','rsvpmaker'),get_permalink() );?></p>
<input type="hidden" name="onfile" value="1" />
<?php
	}
else
	{

global $rsvp_options;
echo $rsvp_options["profile_table"];
/*
;?>
<table border="0" cellspacing="0" cellpadding="0"> 
  <tr> 
	<td width="100"><?php echo __('Phone','rsvpmaker');?>:</td> 
	<td>
		<input name="profile[phone]" type="text" id="phone" 

size="20" value="" />
	</td> 
  </tr> 
  <tr> 
	<td><?php echo __('Phone Type','rsvpmaker');?>:</td> 
	<td> 
	  <select name="profile[phonetype]" id="phonetype"> 
		<option> 
		</option> 
		<option><?php echo __('Work Phone','rsvpmaker');?></option> 
		<option><?php echo __('Mobile Phone','rsvpmaker');?></option> 
		<option><?php echo __('Home Phone','rsvpmaker');?></option> 
	  </select></td> 
  </tr> 
</table>
<?php
*/
	}

} } // end rsvp_profile

if(!function_exists('event_content') )
{
function event_content($content) {
global $wpdb;
global $post;
global $rsvp_options;

//If the post is not an event, leave it alone
if($post->post_type != 'rsvpmaker' )
	return $content;

//On return from paypal payment process, show confirmation
if($_GET["PayerID"])
	return paypal_payment();

//Show paypal error for payment gone wrong
if($_GET["paypal"] == 'error')
	return paypal_error();

$custom_fields = get_post_custom($post->ID);
$permalink = get_permalink($post->ID);
$rsvp_on = $custom_fields["_rsvp_on"][0];
$rsvp_to = $custom_fields["_rsvp_to"][0];
$rsvp_max = $custom_fields["_rsvp_max"][0];
$rsvp_show_attendees = $custom_fields["_rsvp_show_attendees"][0];
if($custom_fields["_rsvp_deadline"][0])
	$deadline = (int) $custom_fields["_rsvp_deadline"][0];
if($custom_fields["_rsvp_start"][0])
	$rsvpstart = (int) $custom_fields["_rsvp_start"][0];
$rsvp_instructions = $custom_fields["_rsvp_instructions"][0];
$rsvp_confirm = $custom_fields["_rsvp_confirm"][0];
$e = $_GET["e"];
if ( !filter_var($e, FILTER_VALIDATE_EMAIL) )
	$e = '';

if($_GET["rsvp"])
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
		if($details["total"])
			{
			$nonce= wp_create_nonce('pp-nonce');
			$rsvpconfirm .= "<p><strong>".__('Pay by PayPal for','rsvpmaker')." ".$details["payingfor"].' = '.number_format($details["total"],2).' ' . $rsvp_options["paypal_currency"]."</strong></p>".
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
foreach($results as $row)
	{
	if(!$firstrow)
		$firstrow = $row;
	$dateblock .= '<div>';
	$last_time = $t = strtotime($row["datetime"]);
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

if(!$rsvp_on)
	return $content;

//check for responses so far
$sql = "SELECT first,last,note FROM ".$wpdb->prefix."rsvpmaker WHERE event=$post->ID AND yesno=1 ORDER BY id DESC";
$attendees = $wpdb->get_results($sql);
	$total = sizeof($attendees); //(int) $wpdb->get_var($sql);

if($rsvp_max)
	{
	$content .= '<p class="signed_up">'.$total.' '.__('signed up so far. Limit: ','rsvpmaker'). "$rsvp_max.</p>\n";
	if($total >= $rsvp_max)
		$too_many = true;
	}
else
	$content .= '<p class="signed_up">'.$total.' '. __('signed up so far.','rsvpmaker').'</p>';

$now = current_time('timestamp');

if($deadline && ($now  > $deadline  ) )
	$content .= '<p class="rsvp_status">'.__('RSVP deadline is past','rsvpmaker').'</p>';
elseif( ( $now > $last_time  ) )
	$content .= '<p class="rsvp_status">'.__('Event date is past','rsvpmaker').'</p>';
elseif($rsvpstart && ( $now < $rsvpstart  ) )
	$content .= '<p class="rsvp_status">'.__('RSVPs accepted starting: ','rsvpmaker').date($rsvp_options["long_date"],$rsvpstart).'</p>';
elseif($too_many)
	$content .= '<p class="rsvp_status">'.__('RSVPs are closed','rsvpmaker').'</p>';
elseif(($rsvp_on && is_admin()) ||  ($rsvp_on && $_GET["load"]) ||  ($rsvp_on && !is_single()) ) // when loaded into editor
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
            <input name="yesno" type="radio" value="1" <?php echo ($rsvprow["yesno"] || !$rsvprow) ? 'checked="checked"' : '';?> /> 
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


if($custom_fields["_per"][0])
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
<input type="hidden" name="unit['.$index.']" value="'.$value.'" />'.$value.' @ <input type="hidden" name="price['.$index.']" value="'.$price.'" />$'.number_format($price,2).'<br />';
	}
echo "</p>\n";
}

basic_form($profile, $guestedit);

if($custom_fields["_rsvp_captcha"][0])
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

if($error = $_GET["err"])
	{
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

if($rsvp_show_attendees && $total && !$_GET["load"] )
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

if($_POST["deletenow"] && current_user_can('edit_others_posts'))
	{
	
	if(!wp_verify_nonce($_POST["deletenonce"],'rsvpdelete') )
		die("failed security check");
	
	foreach($_POST["deletenow"] as $d)
		$wpdb->query("DELETE FROM ".$wpdb->prefix."rsvpmaker where id=$d");
	}

if($_GET["delete"] && current_user_can('edit_others_posts'))
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


if($eventid = (int) $_GET["event"])
	{
	
$sql = "SELECT *
FROM `".$wpdb->prefix."rsvp_dates`
JOIN ".$wpdb->prefix."posts ON ".$wpdb->prefix."rsvp_dates.postID = ".$wpdb->prefix."posts.ID WHERE ".$wpdb->prefix."posts.ID = $eventid";
	$row = $wpdb->get_row($sql);
	$title = $row->post_title;
	$t = strtotime($row->datetime);
	$title .= " ".date('F jS',$t);
	
	echo "<h2>".__("RSVPs for",'rsvpmaker')." ".$title."</h2>\n";
	if(!$_GET["rsvp_print"])
		{
		echo '<div style="float: right; margin-left: 15px; margin-bottom: 15px;"><a href="edit.php?post_type=rsvpmaker&page=rsvp">Show Events List</a>
<a href="edit.php?post_type=rsvpmaker&page=rsvp&event='.$eventid.'&rsvp_order=alpha">Alpha Order</a> <a href="edit.php?post_type=rsvpmaker&page=rsvp&event='.$eventid.'&rsvp_order=timestamp">Most Recent First</a>		
		</div>';
		echo '<p><a href="'.$_SERVER['REQUEST_URI'].'&rsvp_print='.wp_create_nonce('rsvp_print').'" target="_blank" >Format for printing</a></p>';	
		echo '<p><a href="#excel">Download to Excel</a></p>';
		}

	$rsvp_order = ($_GET["rsvp_order"] == 'alpha') ? ' ORDER BY yesno DESC, last, first' : ' ORDER BY yesno DESC, timestamp DESC';
	$sql = "SELECT * FROM ".$wpdb->prefix."rsvpmaker WHERE event=$eventid $rsvp_order";
	$wpdb->show_errors();
	$results = $wpdb->get_results($sql, ARRAY_A);
	
	format_rsvp_details($results);

	if($rsvp_options["debug"])
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
if(!$_GET["show"])
	{
	$sql .= " WHERE datetime > CURDATE( ) ";
	$eventlist .= '<p>Showing future events only (<a href="'.$_SERVER['REQUEST_URI'].'&show=all">show all</a>)<p>';
	}
$sql .= " ORDER BY datetime";

$wpdb->show_errors();
$results = $wpdb->get_results($sql);

	if($rsvp_options["debug"])
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
	if(!$events[$row->postID])
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

if($eventlist && !$_GET["rsvp_print"])
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
		
		echo '<h3>'.$row["yesno"]." ".$row["first"]." ".$row["last"]." ".$row["email"];
		if($row["guestof"])
			echo " (". __('guest of','rsvpmaker')." ".$row["guestof"].")";
		echo "</h3>";
		echo "<p>";
		if($row["details"])
			{
			$details = unserialize($row["details"]);
			foreach($details as $name => $value)
				if($value) {
					echo "$name: $value<br />";
					if(!in_array($name,$fields) )
						$fields[] = $name;
					}
			}
		if($row["note"])
			echo "note: " . nl2br($row["note"])."<br />";
		$t = strtotime($row["timestamp"]);
		echo 'posted: '.date($rsvp_options["short_date"],$t);
		echo "</p>";
		
		if(!$_GET["rsvp_print"] && current_user_can('edit_others_posts'))
			echo sprintf('<p><a href="%s&delete=%d">Delete record for: %s %s</a></p>',admin_url().'edit.php?post_type=rsvpmaker&page=rsvp',$row["id"],$row["first"],$row["last"]);
		}

if($fields && !$_GET["rsvp_print"])
	{
	$fields[]='note'; 
;?>
<div id="excel" name="excel" style="padding: 10px; border: thin dotted #333; width: 300px;margin-top: 30px;">
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
</div>
<?php
	}

echo "</div>\n";
} } // end format_rsvp_details

// helper function for rsvp_excel
function col2chr($a){ 
$a++;
        if($a<27){ 
            return strtoupper(chr($a+96));    
        }else{ 
            while($a > 26){ 
                $b++; 
                $a = $a-26;                
            }                   
            $b = strtoupper(chr($b+96));    
            $a = strtoupper(chr($a+96));                
            return $b.$a; 
        } 
    }

if(!function_exists('rsvp_excel')){

function rsvp_excel() {
if(!$_GET["rsvpexcel"])
	return;
if ( !wp_verify_nonce($_GET['rsvpexcel'],'rsvpexcel') )
{
   print 'Sorry, your nonce did not verify.';
   exit;
}
global $wpdb;
$fields = $_GET["fields"];
$eventid = (int) $_GET["event"];
$columnalpha = array('A',);

include WP_PLUGIN_DIR.'/rsvpmaker/phpexcel/PHPExcel.php'; // include PHP Excel library
	
	$sql = "SELECT post_title FROM ".$wpdb->posts." WHERE ID = $eventid";
	$title = $wpdb->get_var($sql);

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()->setCreator("RSVPMaker")
							 ->setLastModifiedBy("RSVPMaker")
							 ->setTitle($title);

$styleArray = array(
	'font' => array(
		'bold' => true,
	)
);

$index = 1;
foreach($fields as $column => $name )
{

$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($column, $index, $name);

$letter = col2chr($column);
$objPHPExcel->getActiveSheet()->getStyle($letter.'1')->applyFromArray($styleArray);

if($name == "email")
	$objPHPExcel->getActiveSheet()->getColumnDimension( $letter )->setWidth(30);
elseif($name == "answer")
	$objPHPExcel->getActiveSheet()->getColumnDimension( $letter )->setWidth(8);
else
	$objPHPExcel->getActiveSheet()->getColumnDimension( $letter )->setWidth(20);

if($name == 'phone')
	$phonecol = $letter;
}

	$sql = "SELECT * FROM ".$wpdb->prefix."rsvpmaker WHERE event=$eventid ORDER BY yesno DESC, last, first";
	$results = $wpdb->get_results($sql, ARRAY_A);
	$rows = sizeof($results);
	$maxcol = col2chr(sizeof($fields));
	$phonecells = $phonecol.'1:'.$phonecol.($rows+1);
	
$objPHPExcel->getActiveSheet()->getStyle($phonecells)->getNumberFormat()
->setFormatCode('###-###-####');

$bodyStyle = array(
	'borders' => array(
		'bottom' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array('argb' => '88888888'),
		)
	),
	'alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
	)
);
	
	foreach($results as $row)
		{
		$index++;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$index.':'.$maxcol.$index)->applyFromArray($bodyStyle);
		$row["yesno"] = ($row["yesno"]) ? "YES" : "NO";
		if($row["details"])
			{
			$details = unserialize($row["details"]);
			$row = array_merge($row,$details);
			}
		foreach($fields as $column => $name )
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($column, $index, $row[$name]);
			 //$worksheet->write($index, $column, $row[$name], $format_wrap);
		}

$objPHPExcel->getActiveSheet()->getStyle('A1:'.$maxcol.$rows)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(0);
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $index+3, "RSVPs for ".$title);
$objPHPExcel->getActiveSheet(0)->getHeaderFooter()->setOddHeader('&R RSVPs for  ' . $title . ' Page &P of &N');

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="rsvp'.$eventid.'-'.date('Y-m-d-H-i').'.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

exit();
}

} // end rsvp_excel

add_action('admin_init','rsvp_excel');

if(!function_exists('rsvp_print') ) {
function rsvp_print() {
if(!$_GET["rsvp_print"])
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
	}
return $profile;
} }

if(!function_exists('ajax_guest_lookup') )
{
function ajax_guest_lookup() {
if(! ($event = $_GET["ajax_guest_lookup"] ) )
	return;

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

?>