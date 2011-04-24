<?php
//Any of the functions in rsvpmaker-pluggable.php can be overriden from this file.
//To activate, you must place a copy of this file with your customization in your main plugins directory (wp-content/plugins/ instead of wp-content/plugins/rsvpmaker/)
//Here are some sample customizations

//I use this in conjunction with a Mailchimp integration plugin (not yet released publicly). You can insert your own function to log email addresses to a database table or the service of your choice
function capture_email($rsvp) {
if(!$_POST["onfile"] && function_exists('ChimpAdd'))
	{
		$mergevars["FNAME"] = stripslashes($rsvp["first"]);
		$mergevars["LNAME"] = stripslashes($rsvp["first"]);
		ChimpAdd($rsvp["email"],$mergevars);
	}
}


// customize the fields to be included with the RSVP form (in addition to name and email)
// this example was used with several political campaigns to collect contact info, plus the occupation/employer data needed for campaign finance reporting related to fundraisers
//custom fields must follow format name="profile[customfield]"
function rsvp_profile($profile) {

if($profile["city"])
	{
;?>
<div id="profiledetails">Profile details on file.<br />To update profile, 

or RSVP for someone else <a href="<?php echo the_permalink();?>">fetch a blank 

form</a></div>
<input type="hidden" name="onfile" value="1" />
<?php
	}
else
	{
;?>
<table border="0" cellspacing="0" cellpadding="0"> 
  <tr> 
	<td width="100">Occupation:</td> 
	<td> 
	  <input name="profile[occupation]" type="text" id="occupation" size="60" value="" /></td> 
  </tr> 
  <tr> 
	<td width="100">Company:</td> 
	<td> 
	  <input name="profile[company]" type="text" id="company" size="60" 

value="" />            </td> 
  </tr> 
  <tr> 
	<td width="100">Address:</td> 
	<td> 
	  <input name="profile[address]" type="text" id="address" size="60" 

/>            </td> 
  </tr> 
  <tr> 
	<td width="100">City:</td> 
	<td> 
	  <input name="profile[city]" type="text" id="city" size="60"  

value=""  />            </td> 
  </tr> 
  <tr> 
	<td width="100">State:</td> 
	<td> 
	  <input name="profile[state]" type="text" id="state" size="5"  

value="" /> Zip: <input name="profile[zip]" type="text" id="zip" size="20"  

value=""  />            </td> 
  </tr> 
  <tr> 
	<td>Phone:</td> 
	<td>
		<input name="profile[phone]" type="text" id="phone" 

size="20" value="" />
	</td> 
  </tr> 
  <tr> 
	<td>Phone Type:</td> 
	<td> 
	  <select name="profile[phonetype]" id="phonetype"> 
		<option> 
						</option> 
		<option>work</option> 
		<option>mobile</option> 
		<option>home</option> 
	  </select></td> 
  </tr> 
</table>
<?php
	}

}

// changes the formatting of rsvp details in the rsvp report
function format_rsvp_details($results) {

	if($results)
	foreach($results as $index => $row)
		{
		$row["yesno"] = ($row["yesno"]) ? "YES" : "NO";
		
		echo '<h3>'.$row["yesno"]." ".$row["first"]." ".$row["last"]." ".$row["email"];
		if($row["guestof"])
			echo " (guest of ".$row["guestof"].")";
		echo "</h3>";
		echo "<p>";
		if($row["details"])
			{
			$details = unserialize($row["details"]);
			// custom formatting for each individual's details
			echo sprintf("<p><strong>%s %s:</strong> %s %s</p>\n",$details["first"],$details["last"],$details["email"], $details["phone"]);
			}
		if($row["note"])
			echo "note: " . nl2br($row["note"]);
		echo "</p>";
		
		echo sprintf('<p><a href="%s&delete=%d">Delete record for: %s %s</a></p>',admin_url().'edit.php?post_type=rsvpmaker&page=rsvp',$row["id"],$row["first"],$row["last"]);
		}

}

//alternate implementation, requires PEAR Mail and Mime modules to be installed on server
function rsvp_notifications ($rsvp,$rsvp_to,$subject,$message) {

include('Mail.php');
include('Mail/mime.php');
$mail =& Mail::factory('mail');

$text = $message;
$html = "<html><body>\n".wpautop($message).'</body></html>';
$crlf = "\n";

$hdrs = array(
              'From'    => '"'.$rsvp["first"]." ".$rsvp["last"].'" <'.$rsvp["email"].'>',
              'Subject' => $subject
              );

$mime = new Mail_mime($crlf);

$mime->setTXTBody($text);
$mime->setHTMLBody($html);

//do not ever try to call these lines in reverse order
$body = $mime->get();
$hdrs = $mime->headers($hdrs);

$mail->send($rsvp_to, $hdrs, $body);

// now send confirmation

$hdrs = array(
              'From'    => $rsvp_options["rsvp_to"],
              'Subject' => "Confirming RSVP $answer for ".$post->post_title." $date"
              );

$mime = new Mail_mime($crlf);

$mime->setTXTBody($text);
$mime->setHTMLBody($html);

//do not ever try to call these lines in reverse order
$body = $mime->get();
$hdrs = $mime->headers($hdrs);

$mail->send($rsvp["email"], $hdrs, $body);

}

// changes the default formatting for event links that appear in the widget
function widgetlink($evdates,$plink,$evtitle) {
	return sprintf('%s <a href="%s">%s</a> ',$evdates,$plink,$evtitle);
}

?>