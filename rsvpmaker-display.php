<?php

//event_content defined in rsvpmaker-pluggable.php to allow for variations

add_filter('the_content','event_content');

add_shortcode('event_listing', 'event_listing');

function event_listing($atts) {

global $wpdb;

$sql = "SELECT *, $wpdb->posts.ID as postID
FROM `".$wpdb->prefix."rsvp_dates`
JOIN $wpdb->posts ON ".$wpdb->prefix."rsvp_dates.postID = $wpdb->posts.ID
WHERE datetime > CURDATE( ) AND $wpdb->posts.post_status = 'publish'
ORDER BY datetime";

if($atts["limit"])
	$sql .= " LIMIT 0,".$atts["limit"];

$results = $wpdb->get_results($sql,ARRAY_A);

foreach($results as $row)
	{
	$dateblock[$row["postID"]] .= "\n<div> \n";
	$t = strtotime($row["datetime"]);
	$dateblock[$row["postID"]] .= date('l F jS, Y',$t);
	$dateline[$row["postID"]] .= date('F jS',$t)." ";
	$dur = $row["duration"];
	if($dur != 'allday')
		$dateblock[$row["postID"]] .= date(' g:i A',$t);
	if(is_numeric($dur) )
		$dateblock[$row["postID"]] .= " to ".date ('g:i A',$dur);
	$dateblock[$row["postID"]] .= "</div>\n";
	if(!$eventlist[$row["postID"]])
		$eventlist[$row["postID"]] = $row;
	$cal[date('Y-m-d',$t)] .= '<div><a style="font-size: x-small;  line-height: 1;" href="'.get_permalink($row["postID"]).'">'.$row["post_title"]."</a></div>\n";
	}

if($atts["calendar"] || $atts["format"] == 'calendar')
	$listings .= cp_show_calendar($cal);

//strpos test used to catch either "headline" or "headlines"
if($eventlist && ( strpos($atts["format"],'headline') != 'false') )
{
foreach($eventlist as $event)
	{
	$permalink = get_permalink($event["postID"]);
	if($atts["format"] == 'headline')
		$listings .= sprintf('<li><a href="%s">%s</a> %s</li>'."\n",$permalink,$event["post_title"],$dateline[$event["postID"]]);
	}	

	if($atts["title"])
		$listings = "<p><strong>".$atts["title"]."</strong></p>\n<ul id=\"eventheadlines\">\n$listings</ul>\n";
	else
		$listings = "<ul id=\"eventheadlines\">\n$listings</ul>\n";
}//end if $eventlist

	return $listings;
}


function cp_show_calendar($eventarray) 
{
$cm = $_GET["cm"];
$cy = $_GET["cy"];

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
   $prev_link = '<a href="' . $self . strftime('?cm=%m&amp;cy=%Y">%B, %Y</a>', $lm);

// Link to next month (but do not link to too early dates)
$nm = mktime(0, 0, 1, $cm+1, 1, $cy);
   $next_link = '<a href="' . $self . strftime('?cm=%m&amp;cy=%Y">%B, %Y</a>', $nm);

$monthafter = mktime(0, 0, 1, $cm+2, 1, $cy);
   $next_link .= sprintf('<form action="%s" method="get"> Month/Year <input type="text" name="cm" value="%s" size="4" />/<input type="text" name="cy" value="%s" size="4" /><input type="submit" value="Go" ></form>', $self,date('m',$monthafter),date('Y',$monthafter));


// $Id: cal.php,v 1.47 2003/12/31 13:04:27 goba Exp $

// Print out navigation links for previous and next month
//$content .= '<table id="calnav"  width="100%" border="0" cellspacing="0" cellpadding="3">'.
//   "\n<tr>". '<td align="left" width="33%">'. $prev_link. '</td>'.
//     '<td align="center" width="34">'. strftime('<b>%B, %Y</b></td>', $bom).
//     '<td align="right" width="33%">' . $next_link . "</td></tr>\n</table>\n";

// Begin the calendar table
$content .= '<table id="cpcalendar" width="100%" cellspacing="0" cellpadding="3"><caption>'.strftime('<b>%B, %Y</b></td>', $bom)."</caption>\n".'<tr>'."\n";

$content .= '<thead>
<tr> 
<th width="15%">Sunday</th> 
<th width="14%">Monday</th> 
<th width="14%">Tuesday</th> 
<th width="14%">Wednesday</th> 
<th width="14%">Thursday</th> 
<th width="14%">Friday</th> 
<th width="15%">Saturday</th> 
</tr><tr>
</thead>';

$content .= "\n<tbody>\n";

$content .= "\n<tfoot><tr>". '<td align="left" colspan="3">'. $prev_link. '</td>'.
     '<td colspan="4" align="right">' . $next_link . "</td></tr></tfoot>";
	 
// Generate the requisite number of blank days to get things started
for ($days = $i = date("w",$bom); $i > 0; $i--) {
   $content .= '<td class="notaday">&nbsp;</td>';
}

// Print out all the days in this month
for ($i = 1; $i <= date("t",$bom); $i++) {
  
   // Print out day number and all events for the day
	$thisdate = date("Y-m-",$bom).sprintf("%02d",$i);
   $content .= '<td valign="top">';
   if(!empty($eventarray[$thisdate]) )
   {
   $content .= $i;

   $content .= $eventarray[$thisdate];
   }
   else
   	$content .= "<div class=\"day\">" . $i . "</div><p>&nbsp;</p>";
   $content .= '</td>';
  
   // Break HTML table row if at end of week
   if (++$days % 7 == 0) $content .= "</tr>\n<tr>";
}

// Generate the requisite number of blank days to wrap things up
for (; $days % 7; $days++) {
   $content .= '<td class="notaday">&nbsp;</td>';
}

$content .= "\n<tbody>\n";

// End HTML table of events
$content .= "</tr>\n</table>\n";

return $content;
}



/**
 * CPEventsWidget Class
 */
class CPEventsWidget extends WP_Widget {
    /** constructor */
    function CPEventsWidget() {
        parent::WP_Widget(false, $name = 'CPEventsWidget');	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>
              <?php 
			  
			  global $wpdb;
$sql = "SELECT *, $wpdb->posts.ID as postID
FROM `".$wpdb->prefix."rsvp_dates`
JOIN $wpdb->posts ON ".$wpdb->prefix."rsvp_dates.postID = $wpdb->posts.ID
WHERE datetime > CURDATE( ) AND $wpdb->posts.post_status = 'publish'
ORDER BY datetime LIMIT 0, 10";
			  
			  $results = $wpdb->get_results($sql,ARRAY_A);
			  if($results)
			  {
			  echo "\n<ul>\n";
			  foreach($results as $row)
			  	{
				if($ev[$row["postID"]])
					$ev[$row["postID"]] .= ", ".date('M. j',strtotime($row["datetime"]) );
				else
					$ev[$row["postID"]] = '<a href="'.get_permalink($row["postID"]).'">'.$row["post_title"]."</a> ".date('M. j',strtotime($row["datetime"]) );
				}
			  foreach($ev as $e)
			  	echo "<li>$e</li>";
			  echo "\n</ul>\n";
			  }
			  
			  
			  echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {				
        $title = esc_attr($instance['title']);
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <?php 
    }

} // class CPEventsWidget

// register CPEventsWidget widget
add_action('widgets_init', create_function('', 'return register_widget("CPEventsWidget");'));

function rsvpmaker_join($join) {
  global $wpdb;

    $join .= " JOIN ".$wpdb->prefix."rsvp_dates ON ".$wpdb->prefix."rsvp_dates.postID = $wpdb->posts.ID ";

  return $join;
}

function rsvpmaker_where($where) {

global $wpdb;

return " AND $wpdb->posts.post_type = 'event' AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private') AND datetime > CURDATE( )";

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

$no_events = ($atts["no_events"]) ? $atts["no_events"] : 'No events currently listed.';


global $post;
global $wp_query;
global $wpdb;

add_filter('posts_join', 'rsvpmaker_join' );
add_filter('posts_where', 'rsvpmaker_where' );
add_filter('posts_groupby', 'rsvpmaker_groupby' );
add_filter('posts_orderby', 'rsvpmaker_orderby' );
add_filter('posts_distinct', 'rsvpmaker_distinct' );

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$querystring = "post_type=event&paged=$paged";
$wpdb->show_errors();
query_posts($querystring);

ob_start();

if($atts["calendar"] || ($atts["format"] == "calendar") )
	{
	$atts["format"] = "calendar";
	echo event_listing($atts);
	}
	
if ( have_posts() ) {
while ( have_posts() ) : the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<h1 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
<div class="entry-content">

<?php the_content(); ?>

</div><!-- .entry-content -->
</div>
<?php 
endwhile;
?>
<p><?php posts_nav_link(' &#8212; ', __('&laquo; Previous Page'), __('Next Page &raquo;')); ?></p>
<?php
} 
else
	echo "<p>$no_events</p>\n";

wp_reset_query();

return ob_get_clean();

}

add_shortcode("rsvpmaker_upcoming","rsvpmaker_upcoming");

function date_title( $title, $sep, $seplocation ) {
global $post;
global $wpdb;
if($post->post_type == 'event')
	{
	// get first date associated with event
	$sql = "SELECT datetime FROM ".$wpdb->prefix."rsvp_dates WHERE postID = $post->ID ORDER BY datetime";
	$dt = $wpdb->get_var($sql);
	$title .= date('F jS',strtotime($dt) );
	if($seplocation == "right")
		$title .= " $sep ";
	else
		$title = " $sep $title ";
	}
return $title;
}

add_filter('wp_title','date_title', 1, 3);

?>