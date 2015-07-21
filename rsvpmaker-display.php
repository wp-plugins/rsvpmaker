<?php

//event_content defined in rsvpmaker-pluggable.php to allow for variations

add_filter('the_content','event_content',5);

function event_js($content) {
global $post;
if(!is_single())
	return $content;
if($post->post_type != 'rsvpmaker')
	return $content;
global $rsvp_required_field;
ob_start();
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
$('#add_guests').click(function(){
var guestline = '<div class="guest_blank"><?php _e('First Name','rsvpmaker'); ?>: <input type="text" name="guestfirst[]" style="width:30%" /> <?php _e('Last Name','rsvpmaker'); ?>: <input type="text" name="guestlast[]" style="width:30%"/><input type="hidden" name="guestid[]" value="0" /></div>';
$('.add_one').append(guestline);});
<?php
if(isset($rsvp_required_field) )
	{
?>
    jQuery("#rsvpform").submit(function() {
	var leftblank = '';
<?php
foreach($rsvp_required_field as $field)
	{
	echo "if(jQuery(\"#".$field."\").val() === '') leftblank = leftblank + '<div class=\"rsvp_missing\">".$field."</div>';\n";
	}
?>
	if(leftblank != '')
		{
		jQuery("#jqerror").html('<div class="rsvp_validation_error">' + "<?php _e("Required fields left blank",'rsvpmaker'); ?>:\n" + leftblank + '</div>');
		//alert("Required fields left blank:\n" + leftblank);
		return false;
		}
	else
		return true;

});
<?php
	}
?>
 });
</script>
<?php
return $content . ob_get_clean();
}

add_filter('the_content','event_js',15);

add_shortcode('event_listing', 'event_listing');

function event_listing($atts) {

global $wpdb;
global $rsvp_options;

$date_format = ($atts["date_format"]) ? $atts["date_format"] : $rsvp_options["short_date"];
$time_format = ($atts["time_format"]) ? $atts["time_format"] : $rsvp_options["time_format"];

if($atts["past"])
	$sql = "SELECT *, $wpdb->posts.ID as postID, 1 as current
FROM `".$wpdb->prefix."rsvp_dates`
JOIN $wpdb->posts ON ".$wpdb->prefix."rsvp_dates.postID = $wpdb->posts.ID
WHERE datetime < CURDATE( ) AND $wpdb->posts.post_status = 'publish'
ORDER BY datetime DESC";
elseif($_GET["cy"])
	$sql = "SELECT *, $wpdb->posts.ID as postID, 1 as current
FROM `".$wpdb->prefix."rsvp_dates`
JOIN $wpdb->posts ON ".$wpdb->prefix."rsvp_dates.postID = $wpdb->posts.ID
WHERE datetime >= '".$_GET["cy"] .'-'. $_GET["cm"].'-1'."' AND $wpdb->posts.post_status = 'publish'
ORDER BY datetime";
else
	$sql = "SELECT *, $wpdb->posts.ID as postID, datetime > CURDATE( ) as current
FROM `".$wpdb->prefix."rsvp_dates`
JOIN $wpdb->posts ON ".$wpdb->prefix."rsvp_dates.postID = $wpdb->posts.ID
WHERE datetime >= '".date('Y-m')."-1' AND $wpdb->posts.post_status = 'publish'
ORDER BY datetime";

if($atts["limit"])
	$sql .= " LIMIT 0,".$atts["limit"];

$results = $wpdb->get_results($sql,ARRAY_A);

foreach($results as $row)
	{
	$t = strtotime($row["datetime"]);
	if($dateline[$row["postID"]])
		$dateline[$row["postID"]] .= ", ";
	$dateline[$row["postID"]] .= date($date_format,$t);
	if($row["current"] && !$eventlist[$row["postID"]])
		$eventlist[$row["postID"]] = $row;
	}

if($atts["calendar"] || $atts["format"] == 'calendar')
	$listings .= rsvpmaker_calendar($atts);

//strpos test used to catch either "headline" or "headlines"
if($eventlist && ( $atts["format"] == 'headline' || $atts["format"] == 'headlines') )
{
foreach($eventlist as $event)
	{
	if($atts["permalinkhack"])
		$permalink = site_url() ."?p=".$event["postID"];
	else
		$permalink = get_post_permalink($event["postID"]);
	$listings .= sprintf('<li><a href="%s">%s</a> %s</li>'."\n",$permalink,$event["post_title"],$dateline[$event["postID"]]);
	}	

	if($atts["limit"] && $rsvp_options["eventpage"])
		$listings .= '<li><a href="'.$rsvp_options["eventpage"].'">'.__("Go to Events Page",'rsvpmaker')."</a></li>";

	if($atts["title"])
		$listings = "<p><strong>".$atts["title"]."</strong></p>\n<ul id=\"eventheadlines\">\n$listings</ul>\n";
	else
		$listings = "<ul id=\"eventheadlines\">\n$listings</ul>\n";
}//end if $eventlist

	return $listings;
}

/**
 * CPEventsWidget Class
 */
class CPEventsWidget extends WP_Widget {
    /** constructor */
    function CPEventsWidget() {
        parent::WP_Widget(false, $name = 'RSVPMaker Events');	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
		$limit = ($instance["limit"]) ? $instance["limit"] : 10;
		$dateformat = ($instance["dateformat"]) ? $instance["dateformat"] : 'M. j';
        global $rsvp_options;
		;?>
              <?php echo $before_widget;?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title;?>
              <?php 
			  
			  global $wpdb;
$sql = "SELECT *, $wpdb->posts.ID as postID
FROM `".$wpdb->prefix."rsvp_dates`
JOIN $wpdb->posts ON ".$wpdb->prefix."rsvp_dates.postID = $wpdb->posts.ID
WHERE datetime > CURDATE( ) AND $wpdb->posts.post_status = 'publish'
ORDER BY datetime LIMIT 0, $limit";
			  
			  $results = $wpdb->get_results($sql,ARRAY_A);
			  if($results)
			  {
			  echo "\n<ul>\n";
			  foreach($results as $row)
			  	{
				if($ev[$row["postID"]])
					$ev[$row["postID"]] .= ", ".date($dateformat,strtotime($row["datetime"]) );
				else
					{
					$ev[$row["postID"]] = date($dateformat,strtotime($row["datetime"]) );
					$plink[$row["postID"]] = get_post_permalink($row["postID"]);
					$evtitle[$row["postID"]] = $row["post_title"];  
					}
				}
			
			//pluggable function widgetlink can be overridden from custom.php
			
			  foreach($ev as $id => $e)
			  	echo "<li>".widgetlink($e,$plink[$id],$evtitle[$id])."</li>";
				
			if($rsvp_options["eventpage"])
			  	echo '<li><a href="'.$rsvp_options["eventpage"].'">'.__("Go to Events Page",'rsvpmaker')."</a></li>";
			
			  echo "\n</ul>\n";
			  }
			  
			  
			  echo $after_widget;?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
	$instance['dateformat'] = strip_tags($new_instance['dateformat']);
	$instance['limit'] = (int) $new_instance['limit'];
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {				
        $title = esc_attr($instance['title']);
		$limit = ($instance["limit"]) ? $instance["limit"] : 10;
		$dateformat = ($instance["dateformat"]) ? $instance["dateformat"] : 'M. j';
        ;?>
            <p><label for="<?php echo $this->get_field_id('title');?>"><?php _e('Title:','rsvpmaker');?> <input class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" type="text" value="<?php echo $title;?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('limit');?>"><?php _e('Number to Show:','rsvpmaker');?> <input class="widefat" id="<?php echo $this->get_field_id('limit');?>" name="<?php echo $this->get_field_name('limit');?>" type="text" value="<?php echo $limit;?>" /></label></p>

            <p><label for="<?php echo $this->get_field_id('dateformat');?>"><?php _e('Date Format:','rsvpmaker');?> <input class="widefat" id="<?php echo $this->get_field_id('dateformat');?>" name="<?php echo $this->get_field_name('dateformat');?>" type="text" value="<?php echo $dateformat;?>" /></label> (PHP <a target="_blank" href="http://us2.php.net/manual/en/function.date.php">date</a> format string)</p>

        <?php 
    }

} // class CPEventsWidget

// register CPEventsWidget widget
add_action('widgets_init', create_function('', 'return register_widget("CPEventsWidget");'));

function get_next_events_link( $label = '', $max_page = 0 ) {
	
	global $paged, $wp_query;
	global $rsvp_options;

	if(isset($rsvp_options["eventpage"]) && !strpos($rsvp_options["eventpage"],'://') )
		{ // hack for entries set as slug rather than full url
		global $wpdb;
		$id = $wpdb->get_var("SELECT ID from $wpdb->posts WHERE post_name='".$rsvp_options["eventpage"]."' AND post_status='publish'");
		if($id)
		$rsvp_options["eventpage"] = get_permalink($id);
		update_option('RSVPMAKER_Options',$rsvp_options);
		}

	if ( !$max_page )
		$max_page = $wp_query->max_num_pages;

	if ( !$paged )
		$paged = 1;

	$nextpage = intval($paged) + 1;
	$link = '';

	if ( $nextpage > 2 ) {
		$link = '<a href="' . $rsvp_options["eventpage"] ."\">&laquo; " . __('Events Home','rsvpmaker') . '</a>';
	}

	if ( !is_single() && ( $nextpage <= $max_page ) ) {
		$attr = apply_filters( 'next_posts_link_attributes', '' );
		if($link)
			$link .= " | ";
		if( strpos($rsvp_options["eventpage"],'?') )
			$link .= '<a href="' . $rsvp_options["eventpage"] .'&paged='.$nextpage."\" $attr>" . $label . ' &raquo;</a>';		
		else 
			$link .= '<a href="' . $rsvp_options["eventpage"] .'page/'.$nextpage."/\" $attr>" . $label . ' &raquo;</a>';
	}
	
	if(isset($link))
		echo "<p>$link</p>";
}

function rsvpmaker_select($select) {
  global $wpdb;

    $select .= ", datetime, duration";
  return $select;
}

function rsvpmaker_join($join) {
  global $wpdb;

    $join .= " JOIN ".$wpdb->prefix."rsvp_dates ON ".$wpdb->prefix."rsvp_dates.postID = $wpdb->posts.ID ";

  return $join;
}

function rsvpmaker_where($where) {

global $startday;

if(isset($_REQUEST["cm"]))
	return $where . " AND datetime > '".$_REQUEST["cy"]."-".$_REQUEST["cm"]."-0'";
elseif(isset($startday) && $startday)
	{
		$t = strtotime($startday);
		$d = date('Y-m-d',$t);
		return $where . " AND datetime > '$d'";
	}
elseif(isset($_GET["startday"]))
	{
		$t = strtotime($_GET["startday"]);
		$d = date('Y-m-d',$t);
		return $where . " AND datetime > '$d'";
	}
else
	return $where . " AND datetime > CURDATE( )";

}

function rsvpmaker_groupby($groupby) {
  global $wpdb;
  return " $wpdb->posts.ID ";

}

function rsvpmaker_orderby($orderby) {
  return " datetime ";
}

function rsvpmaker_distinct($distinct){
  return 'DISTINCT';
}

function rsvpmaker_upcoming ($atts)
{
	
$no_events = (isset($atts["no_events"]) && $atts["no_events"]) ? $atts["no_events"] : 'No events currently listed.';

global $post;
global $wp_query;
global $wpdb;
global $showbutton;
global $startday;
global $rsvp_options;

if(isset($atts["startday"]))
	{
    $startday = $atts["startday"];
	}

$showbutton = true;

$backup = $wp_query;

add_filter('posts_join', 'rsvpmaker_join' );
add_filter('posts_where', 'rsvpmaker_where' );
add_filter('posts_groupby', 'rsvpmaker_groupby' );
add_filter('posts_orderby', 'rsvpmaker_orderby' );
add_filter('posts_distinct', 'rsvpmaker_distinct' );
add_filter('posts_fields', 'rsvpmaker_select' );

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$querystring = "post_type=rsvpmaker&post_status=publish&paged=$paged";

if(isset($atts["one"]))
	{
	$querystring .= "&posts_per_page=1";
	if(is_numeric($atts["one"]))
		$querystring .= '&p='.$atts["one"];
	elseif($atts["one"] != 'next')
		$querystring .= '&name='.$atts["one"];
	}
if(isset($atts["type"]))
	$querystring .= "&rsvpmaker-type=".$atts["type"];
if(isset($atts["limit"]))
	$querystring .= "&posts_per_page=".$atts["limit"];
if(isset($atts["add_to_query"]))
	{
		if(!strpos($atts["add_to_query"],'&'))
			$atts["add_to_query"] = '&'.$atts["add_to_query"];
		$querystring .= $atts["add_to_query"];
	}

$wpdb->show_errors();

$wp_query = new WP_Query($querystring);

// clean up so this doesn't interfere with other operations
remove_filter('posts_join', 'rsvpmaker_join' );
remove_filter('posts_where', 'rsvpmaker_where' );
remove_filter('posts_groupby', 'rsvpmaker_groupby' );
remove_filter('posts_orderby', 'rsvpmaker_orderby' );
remove_filter('posts_distinct', 'rsvpmaker_distinct' );
remove_filter('posts_fields', 'rsvpmaker_select' );

ob_start();

if(isset($atts["demo"]))
	{
		$demo = "<div><strong>Shortcode:</strong></div>\n<code>[rsvpmaker_upcoming";
		foreach($atts as $name => $value)
			{
			if($name == "demo")
				continue;
			$demo .= ' '.$name.'="'.$value.'"';
			}
		$demo .= "]</code>\n";
		$demo .= "<div><strong>Output:</strong></div>\n";
		echo $demo;
	}

echo '<div class="rsvpmaker_upcoming">';
	
if ( have_posts() ) {
while ( have_posts() ) : the_post();

?>

<div id="post-<?php the_ID();?>" <?php post_class();?> itemscope itemtype="http://schema.org/Event" >  
<h1 class="entry-title"><a href="<?php the_permalink(); ?>"  itemprop="url"><span itemprop="name"><?php the_title(); ?></span></a></h1>
<div class="entry-content">

<?php the_content(); ?>

</div><!-- .entry-content -->

<?php

if(!isset($atts["hideauthor"]) || !$atts["hideauthor"])
{
$authorlink = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
	get_author_posts_url( get_the_author_meta( 'ID' ) ),
	sprintf( esc_attr__( 'View all posts by %s', 'rsvpmaker' ), get_the_author() ),
	get_the_author());
?>
<div class="event_author"><?php _e('Posted by','rsvpmaker'); echo " $authorlink on ";?><span class="updated" datetime="<?php the_modified_date('c');?>"><?php the_modified_date(); ?></span></div>
<?php 
}
?>
</div>
<?php
if(is_admin() )
	{
		echo '<p><a href="'.admin_url('post.php?action=edit&post='.$post->ID).'">Edit</a></p>';
	}
endwhile;
?>
<p><?php
if(!isset($atts['one'])) 
	get_next_events_link(__('More Events','rsvpmaker'));
} 
else
	echo "<p>$no_events</p>\n";
echo '</div><!-- end rsvpmaker_upcoming -->';

$wp_query = $backup;
wp_reset_postdata();

if(isset($atts["calendar"]) || (isset($atts["format"]) && ($atts["format"] == "calendar") ) )
	$listings = rsvpmaker_calendar($atts);

$listings .= ob_get_clean();

return $listings;

}

add_shortcode("rsvpmaker_upcoming","rsvpmaker_upcoming");

//get all of the dates for the month
function rsvpmaker_calendar_where($where) {

global $startday;

if(isset($_REQUEST["cm"]))
	$d = "'".$_REQUEST["cy"]."-".$_REQUEST["cm"]."-1'";
elseif(isset($startday) && $startday)
	{
		$t = strtotime($startday);
		$d = "'".date('Y-m-d',$t)."'";
	}
elseif(isset($_GET["startday"]))
	{
		$t = strtotime($_GET["startday"]);
		$d = "'".date('Y-m-d',$t)."'";
	}
else
	$d = ' CURDATE() ';

	return $where . " AND datetime > ".$d.' AND datetime < DATE_ADD('.$d.', INTERVAL 5 WEEK) ';
}

add_shortcode("rsvpmaker_calendar","rsvpmaker_calendar");
function rsvpmaker_calendar($atts) 
{

global $post;
global $wp_query;
global $wpdb;
global $showbutton;
global $startday;
global $rsvp_options;
$date_format = ($atts["date_format"]) ? $atts["date_format"] : $rsvp_options["short_date"];

if(isset($atts["startday"]))
	{
    $startday = $atts["startday"];
	}

$showbutton = true;

$backup = $wp_query;

//removing groupby, which interferes with display of multi-day events
add_filter('posts_join', 'rsvpmaker_join' );
add_filter('posts_where', 'rsvpmaker_calendar_where' );
add_filter('posts_orderby', 'rsvpmaker_orderby' );
add_filter('posts_fields', 'rsvpmaker_select' );

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$querystring = "post_type=rsvpmaker&post_status=publish&posts_per_page=-1&paged=$paged";

if(isset($atts["type"]))
	$querystring .= "&rsvpmaker-type=".$atts["type"];
if(isset($atts["add_to_query"]))
	{
		if(!strpos($atts["add_to_query"],'&'))
			$atts["add_to_query"] = '&'.$atts["add_to_query"];
		$querystring .= $atts["add_to_query"];
	}

$wpdb->show_errors();

$wp_query = new WP_Query($querystring);

// clean up so this doesn't interfere with other operations
remove_filter('posts_join', 'rsvpmaker_join' );
remove_filter('posts_where', 'rsvpmaker_calendar_where' );
remove_filter('posts_orderby', 'rsvpmaker_orderby' );
remove_filter('posts_fields', 'rsvpmaker_select' );

if ( have_posts() ) {
while ( have_posts() ) : the_post();

//calendar entry
	$t = strtotime($post->datetime);
	$time = ($post->duration == 'allday') ? '' : '<br />&nbsp;'.date($rsvp_options["time_format"],$t);
	if(strpos($post->duration,'-'))
		$time .= '-'.date($rsvp_options["time_format"],strtotime($post->duration));
	$eventarray[date('Y-m-d',$t)] .= '<div><a class="calendar_item" href="'.get_post_permalink($post->ID).'" title="'.htmlentities($post->post_title).'">'.$post->post_title.$time."</a></div>\n";
	if(!$atts["noexpand"])
		$caldetail[date('Y-m-d',$t)] .= '<div>'.date($date_format,$t).': <a href="'.get_post_permalink($post->ID).'">'.$post->post_title."</a></div>\n";
endwhile;
}

$wp_query = $backup;
wp_reset_postdata();

// calendar display routine
$nav = isset($atts["nav"]) ? $atts["nav"] : 'bottom';

$cm = $_REQUEST["cm"];
$cy = $_REQUEST["cy"];
$self = $req_uri = get_permalink();
$req_uri .= (strpos($req_uri,'?') ) ? '&' : '?';

if (!isset($cm) || $cm == 0)
	$nowdate = date("Y-m-d");
else
	$nowdate = date("Y-m-d", mktime(0, 0, 1, $cm, 1, $cy) );

// Check if month and year is valid
if ($cm && $cy && !checkdate($cm,1,$cy)) {
   $errors[] = "The specified year and month (".htmlentities("$cy, $cm").") are not valid.";
   unset($cm); unset($cy);
}

// Give defaults for the month and day values if they were invalid
if (!isset($cm) || $cm == 0) { $cm = date("m"); }
if (!isset($cy) || $cy == 0) { $cy = date("Y"); }

// Start of the month date
$date = mktime(0, 0, 1, $cm, 1, $cy);

// Beginning and end of this month
$bom = mktime(0, 0, 1, $cm,  1, $cy);
$eom = mktime(0, 0, 1, $cm+1, 0, $cy);
$eonext = date("Y-m-d",mktime(0, 0, 1, $cm+2, 0, $cy) );

// Link to previous month (but do not link to too early dates)
$lm = mktime(0, 0, 1, $cm, 0, $cy);
   $prev_link = '<a href="' . $req_uri . strftime('cm=%m&cy=%Y">&lt;&lt; %B %Y</a>', $lm);

// Link to next month (but do not link to too early dates)
$nm = mktime(0, 0, 1, $cm+1, 1, $cy);
   $next_link = '<a href="' . $req_uri . strftime('cm=%m&cy=%Y">%B %Y &gt;&gt;</a>', $nm);

$monthafter = mktime(0, 0, 1, $cm+2, 1, $cy);

	$page_id = (isset($_GET["page_id"])) ? '<input type="hidden" name="page_id" value="'. (int) $_GET["page_id"].'" />' : '';

// $Id: cal.php,v 1.47 2003/12/31 13:04:27 goba Exp $

// Print out navigation links for previous and next month
//$content .= '<table id="calnav"  width="100%" border="0" cellspacing="0" cellpadding="3">'.
//   "\n<tr>". '<td align="left" width="33%">'. $prev_link. '</td>'.
//     '<td align="center" width="34">'. strftime('<b>%B, %Y</b></td>', $bom).
//     '<td align="right" width="33%">' . $next_link . "</td></tr>\n</table>\n";

// Begin the calendar table

if(($nav == 'top') || ($nav == 'both')) // either it's top or both
$content .= '<div style="width: 100%; text-align: right;" class="nav"><span class="navprev">'. $prev_link. '</span> / <span class="navnext">'.
     '' . $next_link . "</span></div>";

$content .= '

<table id="cpcalendar" width="100%" cellspacing="0" cellpadding="3"><caption>'.strftime('<b>%B %Y</b>', $bom)."</caption>\n".'<tr>'."\n";

$content .= '<thead>';

$content .= '<tr>
<th>'.__('Sunday','rsvpmaker').'</th> 
<th>'.__('Monday','rsvpmaker').'</th> 
<th>'.__('Tuesday','rsvpmaker').'</th> 
<th>'.__('Wednesday','rsvpmaker').'</th> 
<th>'.__('Thursday','rsvpmaker').'</th> 
<th>'.__('Friday','rsvpmaker').'</th> 
<th>'.__('Saturday','rsvpmaker').'</th> 
</tr>
</thead>
';

$content .= "\n<tbody><tr id=\"rsvprow1\">\n";
$rowcount = 1;
// Generate the requisite number of blank days to get things started
for ($days = $i = date("w",$bom); $i > 0; $i--) {
   $content .= '<td class="notaday">&nbsp;</td>';
}

$todaydate = date('Y-m-d');
// Print out all the days in this month
for ($i = 1; $i <= date("t",$bom); $i++) {
  
   // Print out day number and all events for the day
	$thisdate = date("Y-m-",$bom).sprintf("%02d",$i);
	$class = ($thisdate == $todaydate ) ? 'today day' : 'day';
	if($thisdate < $todaydate)
		$class .= ' past';
   $content .= '<td valign="top" class="'.$class.'">';
   if(!empty($eventarray[$thisdate]) )
   {
   $content .= $i;
   $content .= $eventarray[$thisdate];
   $t = strtotime($thisdate);
   if($caldetail)
   	$detailrow .= $caldetail[$thisdate]; 
   }
   else
   	$content .= '<div class="'.$class.'">' . $i . "</div><p>&nbsp;</p>";
   $content .= '</td>';
  
   // Break HTML table row if at end of week
   if (++$days % 7 == 0)
   	{
		$content .= "</tr>\n";
		if(!empty($detailrow))
			{
			$content .= '<tr id="detailrow'.$rowcount.'"><td colspan="7">'.$detailrow.'</td></tr>';	
			$calj .= ' $("#detailrow'.$rowcount.'" ).hide();

	$("#rsvprow'.$rowcount.'").hover(function(e) {
    $("#detailrow'.$rowcount.'").show();
	}
	,function(e){
		 $("#detailrow'.$rowcount.'" ).hide();
	}
	);

		$("#detailrow'.$rowcount.'").hover(function(e) {
		$("#detailrow'.$rowcount.'").show();
		},
	function(e) {
		$("#detailrow'.$rowcount.'").hide();
		});	

';
			}
		$rowcount++;
		$content .= '<tr id="rsvprow'.$rowcount.'">';
		$detailrow = "";
	}
}

// Generate the requisite number of blank days to wrap things up
for (; $days % 7; $days++) {
   $content .= '<td class="notaday">&nbsp;</td>';
}

$content .= "\n</tr>";

		if(!empty($detailrow))
			{
			$content .= '<tr id="detailrow'.$rowcount.'"><td colspan="7">'.$detailrow.'</td></tr>';	
			$calj .= ' $("#detailrow'.$rowcount.'" ).hide();

	$("#rsvprow'.$rowcount.'").hover(function(e) {
    $("#detailrow'.$rowcount.'").show();
	}
	,function(e){
		 $("#detailrow'.$rowcount.'" ).hide();
	}
	);

		$("#detailrow'.$rowcount.'").hover(function(e) {
		$("#detailrow'.$rowcount.'").show();
		},
	function(e) {
		$("#detailrow'.$rowcount.'").hide();
		});	

';
			}
$content .= "<tbody>\n";

// End HTML table of events
$content .= "\n</table>\n";


if($nav != 'top') // either it's bottom or both
$content .= '<div style="float: right;" class="nav"><span class="navprev">'. $prev_link. '</span> / <span class="navnext">'.
     '' . $next_link . "</span></div>";

//jump form
$content .= sprintf('<form id="jumpform" action="%s" method="post"> Month/Year <input type="text" name="cm" value="%s" size="4" />/<input type="text" name="cy" value="%s" size="4" /><button>Go</button>%s</form>', $self,date('m',$monthafter),date('Y',$monthafter),$page_id);

if(!empty($calj))
	{
$content .= '<script>
jQuery(document).ready(function($) {
'.$calj.'
});
</script>
';
	}

return $content;
}


function rsvpmaker_template_fields($select) {
  $select .= ", meta_value as sked";
  return $select;
}

function rsvpmaker_template_join($join) {
  global $wpdb;

    $join .= " JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID ";

  return $join;
}

function rsvpmaker_template_where($where) {

	return $where . " AND meta_key='_sked'";

}

function rsvpmaker_template_orderby($orderby) {
  return " post_title ";
}

function rsvpmaker_template_events_where($where) {
global $rsvptemplate;
	if(isset($_GET["t"]))
		$rsvptemplate = (int) $_GET["t"];
	if(!$rsvptemplate)
		return $where;
	return $where . " AND meta_key='_meet_recur' AND meta_value=$rsvptemplate";
}

//utility function, template tag
function is_rsvpmaker() {
global $post;
if($post->post_type == 'rsvpmaker')
	return true;
else
	return false;
}

add_shortcode('rsvpmaker_looking_ahead','rsvpmaker_looking_ahead');

function rsvpmaker_looking_ahead($atts) {
global $last_time;
global $events_displayed;

if(!$last_time)
	return 'last time not found';
global $wpdb;
$sql = "SELECT *, $wpdb->posts.ID as postID
FROM `".$wpdb->prefix."rsvp_dates`
JOIN $wpdb->posts ON ".$wpdb->prefix."rsvp_dates.postID = $wpdb->posts.ID
WHERE datetime > '".date('Y-m-d',$last_time)."' AND datetime < DATE_ADD('".date('Y-m-d',$last_time)."',INTERVAL 6 WEEK) AND $wpdb->posts.post_status = 'publish'
ORDER BY datetime LIMIT 0,10";

$results = $wpdb->get_results($sql,ARRAY_A);

foreach($results as $row)
	{
	if(in_array($row["postID"], $events_displayed) )
		continue;
		
	$t = strtotime($row["datetime"]);
	if($dateline[$row["postID"]])
		$dateline[$row["postID"]] .= ", ";
	$dateline[$row["postID"]] .= date('M. j',$t);
	$eventlist[$row["postID"]] = $row;
	}

//strpos test used to catch either "headline" or "headlines"
if($eventlist)
{
foreach($eventlist as $event)
	{
	if($atts["permalinkhack"])
		$permalink = site_url() ."?p=".$event["postID"];
	else
		$permalink = get_post_permalink($event["postID"]);
	$listings .= sprintf('<li><a href="%s">%s</a> %s</li>'."\n",$permalink,$event["post_title"],$dateline[$event["postID"]]);
	}

	if($atts["limit"] && $rsvp_options["eventpage"])
		$listings .= '<li><a href="'.$rsvp_options["eventpage"].'">'.__("Go to Events Page",'rsvpmaker')."</a></li>";

	if($atts["title"])
		$listings = "<p><strong>".$atts["title"]."</strong></p>\n<ul id=\"eventheadlines\">\n$listings</ul>\n";
	else
		$listings = "<ul id=\"eventheadlines\">\n$listings</ul>\n";
}//end if $eventlist
return $listings;
}

function get_adjacent_rsvp_join($join) {
global $post;
if($post->post_type != 'rsvpmaker')
	return $join;
global $wpdb;
return $join .' JOIN '.$wpdb->prefix.'rsvp_dates ON p.ID='.$wpdb->prefix.'rsvp_dates.postID';
}

add_filter('get_previous_post_join','get_adjacent_rsvp_join');
add_filter('get_next_post_join','get_adjacent_rsvp_join');

function get_adjacent_rsvp_sort($sort) {
global $post;
if($post->post_type != 'rsvpmaker')
	return $sort;
global $wpdb;
$sort = str_replace('p.post_date',$wpdb->prefix.'rsvp_dates.datetime',$sort);

return $sort;
}
add_filter('get_previous_post_sort','get_adjacent_rsvp_sort');
add_filter('get_next_post_sort','get_adjacent_rsvp_sort');


function get_adjacent_rsvp_where($where) {
global $post;
if($post->post_type != 'rsvpmaker')
	return $where;
global $wpdb;
$op = strpos($where, '>') ? '>' : '<';
$current_event_date = $wpdb->get_var("select datetime from ".$wpdb->prefix."rsvp_dates WHERE postID=".$post->ID);
//split and modify
$wparts = explode('p.post_type',$where);//

$where = "WHERE ".$wpdb->prefix."rsvp_dates.datetime $op '$current_event_date' AND p.ID != $post->ID AND p.post_type".$wparts[1];
return $where;
}

add_filter('get_previous_post_where','get_adjacent_rsvp_where');
add_filter('get_next_post_where','get_adjacent_rsvp_where');

function rsvpmaker_to_ical () {
global $post;
global $wpdb;
if(($post->post_type != 'rsvpmaker') )
	return;
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $post->post_name.'.ics');

$sql = "SELECT * FROM ".$wpdb->prefix."rsvp_dates WHERE postID=".$post->ID.' ORDER BY datetime';
$daterow = $wpdb->get_row($sql);
$duration = get_utc_ical ( (strpos($daterow->duration,'-') ) ? $daterow->duration : $daterow->datetime . ' +1 hour' );
$start = get_utc_ical ($daterow->datetime);
printf('BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
CALSCALE:GREGORIAN
BEGIN:VEVENT
DTEND:%s
UID:%s
DTSTAMP:%s
LOCATION:
DESCRIPTION:%s
URL;VALUE=URI:%s
SUMMARY:%s
DTSTART:%s
END:VEVENT
END:VCALENDAR
',$duration,$start.'-'.$post->ID.'@'.$_SERVER['SERVER_NAME'],date('Ymd\THis\Z'), get_bloginfo('name'), get_permalink($post->ID),$post->post_title, $start);
exit;
}

if(isset($_GET["ical"])) 
	add_action('wp','rsvpmaker_to_ical');

function rsvpmaker_to_gcal($post,$datetime,$duration) {
return sprintf('http://www.google.com/calendar/event?action=TEMPLATE&text=%s&dates=%s/%s&details=%s&location=&trp=false&sprop=%s&sprop=name:%s',urlencode($post->post_title),get_utc_ical ($datetime),get_utc_ical ($duration), urlencode(get_bloginfo('name')),get_permalink($post->ID), urlencode(get_bloginfo('name')) );
}

function get_utc_ical ($timestamp) {
return gmdate('Ymd\THis\Z', strtotime($timestamp));
}

?>