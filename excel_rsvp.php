<?php

if(!current_user_can('edit_posts') )
	die("This function is only available to editors and administrators");

if($eventid = (int) $_GET["event"])
{
$config = $_SERVER['DOCUMENT_ROOT'].'/wp-config.php';
if(file_exists($config))
	require_once($config);
else
	{
	$config = str_replace('public_html/','',$config);
	require_once($config); 
	}

include_once 'Spreadsheet/Excel/Writer.php';
	
	$sql = "SELECT post_title FROM ".$wpdb->posts." WHERE ID = $eventid";
	$title = $wpdb->get_var($sql);

$workbook = new Spreadsheet_Excel_Writer();
// sending HTTP headers
$workbook->send("event-$eventid-rsvp.xls");

// set up formats
$format_bold =& $workbook->addFormat();
$format_bold->setBold();
$format_bold->setSize(9);

$format_title =& $workbook->addFormat();
$format_title->setBold();
$format_title->setSize(12);
// let's merge
$format_title->setAlign('merge');

$format_wrap =& $workbook->addFormat();
$format_wrap->setTextWrap();
$format_wrap->setAlign('top');
$format_wrap->setSize(9);
$format_wrap->setBorder(1);

$amt_format =& $workbook->addFormat();
$amt_format->setNumFormat('0.00');
$amt_format->setBold();
$amt_format->setAlign('left');
$amt_format->setAlign('top');
$amt_format->setBorder(1);
$amt_format->setSize(9);

$format_border =& $workbook->addFormat();
$format_border->setBorder(1);
$format_border->setAlign('top');
$format_border->setSize(9);

// Creating a worksheet
$worksheet =& $workbook->addWorksheet('RSVPs');
$worksheet->setHeader('&R RSVPs for  ' . $title . ' Page &P of &N',0.5);

// row headers 1
$worksheet->setLandscape();
$worksheet->setMargins(0.5);
$worksheet->setMarginTop(1.0);
$worksheet->repeatRows(0,0);

$data = get_spreadsheet_data($eventid);
	foreach($data as $index => $srow)
		{
		$column = 0;
	
		foreach($srow as $name => $value)
			{
			if($index == 0)
				{
				$worksheet->write($index, $column, $name, $format_bold);
				if($name == "email")
					$worksheet->setColumn($column,$column, 30);
				elseif($name == "answer")
					$worksheet->setColumn($column,$column, 8);
				else
					$worksheet->setColumn($column,$column, 20);
				}
			$worksheet->write($index+1, $column, $value, $format_wrap);
			$column++;
			}
		}

$worksheet->write($index+3, 2, "RSVPs for ".$title, $format_title);

$workbook->close();

}
else
	die("No event selected");
	
?>