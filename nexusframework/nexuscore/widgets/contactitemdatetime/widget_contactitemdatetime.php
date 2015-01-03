<?php

function nxs_widgets_contactitemdatetime_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-calendar"; // . $widget_name;
}

function nxs_widgets_contactitemdatetime_gettitle()
{
	return nxs_l18n__("Datetime input", "nxs_td");
}

function nxs_widgets_contactitemdatetime_getformitemsubmitresult($args)
{
	// $args consists of "metadata"
	// combined with $_REQUEST this should feed us with all information
	// needed to produce the result :)
	
	extract($args);
	
	$elementid = $metadata["elementid"];
	$overriddenelementid = $metadata["overriddenelementid"];
	$formlabel = $metadata["formlabel"];
	$isrequired = $metadata["isrequired"];
		
	$result = array();
	$result["result"] = "OK";
	$result["validationerrors"] = array();
	$result["markclientsideelements"] = array();
	
	nxs_requirewidget("contactbox");
	$prefix = nxs_widgets_contactbox_getclientsideprefix($postid, $placeholderid);
	
	if ($overriddenelementid != "")
	{
		$key = $overriddenelementid;
	}
	else
	{
		$key = $prefix . $elementid;
	}
	
	$value_date = $_REQUEST[$key . "_date"];
	$value_hh = $_REQUEST[$key . "_hh"];
	$value_mm = $_REQUEST[$key . "_mm"];
	
	if ($isrequired != "")
	{
		// date is required field
		
		if (trim($value_date) == '')
		{
			// error
			$result["validationerrors"][] = sprintf(nxs_l18n__("%s is a required field", "nxs_td"), $formlabel);
			$result["markclientsideelements"][] = $key . "_date";
		}
		else
		{
			if (trim($value_hh) == '')
			{
				// error
				$result["validationerrors"][] = sprintf(nxs_l18n__("%s is a required field", "nxs_td"), $formlabel);
				$result["markclientsideelements"][] = $key . "_hh";
			}
			else
			{
				if (trim($value_mm) == '')
				{
					// error
					$result["validationerrors"][] = sprintf(nxs_l18n__("%s is a required field", "nxs_td"), $formlabel);
					$result["markclientsideelements"][] = $key . "_mm";
				}
			}
		}
	}
	
	$result["output"] = "{$formlabel}: {$value_date} {$value_hh}:$value_mm";

	return $result;
}


// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_contactitemdatetime_renderincontactbox($args)
{
	//
	extract($args);
	
	extract($metadata, EXTR_PREFIX_ALL, "metadata");
	
	$result = array();
	$result["result"] = "OK";

	nxs_requirewidget("contactbox");
	$prefix = nxs_widgets_contactbox_getclientsideprefix($postid, $placeholderid);
	
	if ($metadata_overriddenelementid != "")
	{
		$key = $metadata_overriddenelementid;
	}
	else
	{
		$key = $prefix . $metadata_elementid;
	}
	
	// format is datetime@hh@mm, for example 16-10-2013@11@00 for 11:00 AM 16 oct 2013
	$splittedvalue = explode("@", $value);
	if (count($splittedvalue) >= 3)
	{
		$datevalue = $splittedvalue[0];
		$timehhvalue = $splittedvalue[1];
		$timemmvalue = $splittedvalue[2];
	}
	
	$dateformat = nxs_date_getdatepickerformat();
	
	//
	// render actual control / html
	//
	
	ob_start();

	?>
  <label class="field_name"><?php echo $metadata_formlabel;?><?php if ($metadata_isrequired != "") { ?>*<?php } ?></label>
  <input type="text" id="<?php echo $key; ?>_date" name="<?php echo $key; ?>_date" class="field_name" style="width: inherit;">
  <select id="<?php echo $key; ?>_hh" name="<?php echo $key; ?>_hh">
  	<option value=''></option>
  	<?php
  	$timeformat = get_option('time_format');	
  	// bijv. "g:i a", see http://codex.wordpress.org/Formatting_Date_and_Time
  	
  	if (
  		strpos($timeformat, 'H') !== false ||	// 00..23
  		strpos($timeformat, 'G') !== false	// 0..23
  	) 
  	{
	  	?>
	  	<option value='00'>00</option>
	  	<option value='01'>01</option>
	  	<option value='02'>02</option>
	  	<option value='03'>03</option>
	  	<option value='04'>04</option>
	  	<option value='05'>05</option>
	  	<option value='06'>06</option>
	  	<option value='07'>07</option>
	  	<option value='08'>08</option>
	  	<option value='09'>09</option>
	  	<option value='10'>10</option>
	  	<option value='11'>11</option>
	  	<option value='12'>12</option>
	  	<option value='13'>13</option>
	  	<option value='14'>14</option>
	  	<option value='15'>15</option>
	  	<option value='16'>16</option>
	  	<option value='17'>17</option>
	  	<option value='18'>18</option>
	  	<option value='19'>19</option>
	  	<option value='20'>20</option>
	  	<option value='21'>21</option>
	  	<option value='22'>22</option>
	  	<option value='23'>23</option>
	  	<?php
	  }
	  else 
	  {
	  	if (
	  		strpos($timeformat, 'a') !== false
	  	)
	  	{
	  		$am = "am";
	  		$pm = "pm";
	  	}
	  	else
	  	{
	  		$am = "AM";
	  		$pm = "PM";
	  	}
	  	
	  	?>
	  	<option value='01 <?php echo $am; ?>'>01 <?php echo $am; ?></option>
	  	<option value='02 <?php echo $am; ?>'>02 <?php echo $am; ?></option>
	  	<option value='03 <?php echo $am; ?>'>03 <?php echo $am; ?></option>
	  	<option value='04 <?php echo $am; ?>'>04 <?php echo $am; ?></option>
	  	<option value='05 <?php echo $am; ?>'>05 <?php echo $am; ?></option>
	  	<option value='06 <?php echo $am; ?>'>06 <?php echo $am; ?></option>
	  	<option value='07 <?php echo $am; ?>'>07 <?php echo $am; ?></option>
	  	<option value='08 <?php echo $am; ?>'>08 <?php echo $am; ?></option>
	  	<option value='09 <?php echo $am; ?>'>09 <?php echo $am; ?></option>
	  	<option value='10 <?php echo $am; ?>'>10 <?php echo $am; ?></option>
	  	<option value='11 <?php echo $am; ?>'>11 <?php echo $am; ?></option>
	  	<option value='12 <?php echo $am; ?>'>12 <?php echo $am; ?></option>
	  	
	  	<option value='01 <?php echo $pm; ?>'>01 <?php echo $pm; ?></option>
	  	<option value='02 <?php echo $pm; ?>'>02 <?php echo $pm; ?></option>
	  	<option value='03 <?php echo $pm; ?>'>03 <?php echo $pm; ?></option>
	  	<option value='04 <?php echo $pm; ?>'>04 <?php echo $pm; ?></option>
	  	<option value='05 <?php echo $pm; ?>'>05 <?php echo $pm; ?></option>
	  	<option value='06 <?php echo $pm; ?>'>06 <?php echo $pm; ?></option>
	  	<option value='07 <?php echo $pm; ?>'>07 <?php echo $pm; ?></option>
	  	<option value='08 <?php echo $pm; ?>'>08 <?php echo $pm; ?></option>
	  	<option value='09 <?php echo $pm; ?>'>09 <?php echo $pm; ?></option>
	  	<option value='10 <?php echo $pm; ?>'>10 <?php echo $pm; ?></option>
	  	<option value='11 <?php echo $pm; ?>'>11 <?php echo $pm; ?></option>
	  	<option value='12 <?php echo $pm; ?>'>12 <?php echo $pm; ?></option>
	  	<?php
	  }
  	?>
  </select>
  <select id="<?php echo $key; ?>_mm" name="<?php echo $key; ?>_mm">
  	<option value=''></option>
  	<option value='00'>00</option>
  	<option value='05'>05</option>
  	<option value='10'>10</option>
  	<option value='15'>15</option>
  	<option value='20'>20</option>
  	<option value='25'>25</option>
  	<option value='30'>30</option>
  	<option value='35'>35</option>
  	<option value='40'>40</option>
  	<option value='45'>45</option>
  	<option value='50'>50</option>
  	<option value='55'>55</option>  
  </select>
  <div class="nxs-clear nxs-filler"></div>
  <script type='text/javascript'>
		jQuery(document).ready
		(
			function() 
			{
				nxs_js_log('setting date..');
				// activate datepicker			
				jQuery("#<?php echo $key; ?>_date").datepicker({ 
					setDate: new Date(), // now
					
					//showOn: "button",
					buttonImage: "images/calendar.gif",
					buttonImageOnly: false,

					onSelect: function()
					{
						// OK... alert('nice');
					},
					beforeShow: function(input, inst) 
					{
						// nxs_js_log($('.ui-datepicker-prev'));
	          jQuery('.ui-datepicker-prev').removeClass('nxs-frontendbutton2').addClass('nxs-frontendbutton2');
	          jQuery('.ui-datepicker-next').removeClass('nxs-frontendbutton2').addClass('nxs-frontendbutton2');
	          jQuery('#ui-datepicker-div').removeClass('nxs-datepicker').addClass('nxs-datepicker');
		    	},					
					firstDay: 1,
					inline: 1,
					minDate: <?php if ($metadata_datefilter_istodayallowed == "") { echo "1"; } else { echo "0"; } ?>,
					dateFormat: "<?php echo $dateformat; ?>",
					dayNames: ['<?php nxs_l18n_e("Sunday", "nxs_td"); ?>', '<?php nxs_l18n_e("Monday", "nxs_td"); ?>', '<?php nxs_l18n_e("Tuesday", "nxs_td"); ?>', '<?php nxs_l18n_e("Wednesday", "nxs_td"); ?>', '<?php nxs_l18n_e("Thursday", "nxs_td"); ?>', '<?php nxs_l18n_e("Friday", "nxs_td"); ?>', '<?php nxs_l18n_e("Saturday", "nxs_td"); ?>'],
					dayNamesMin: ['<?php nxs_l18n_e("Su", "nxs_td"); ?>', '<?php nxs_l18n_e("Mo", "nxs_td"); ?>', '<?php nxs_l18n_e("Tu", "nxs_td"); ?>', '<?php nxs_l18n_e("We", "nxs_td"); ?>', '<?php nxs_l18n_e("Th", "nxs_td"); ?>', '<?php nxs_l18n_e("Fr", "nxs_td"); ?>', '<?php nxs_l18n_e("Sa", "nxs_td"); ?>'],
					monthNames: ['<?php nxs_l18n_e("January", "nxs_td"); ?>', '<?php nxs_l18n_e("February", "nxs_td"); ?>', '<?php nxs_l18n_e("March", "nxs_td"); ?>', '<?php nxs_l18n_e("April", "nxs_td"); ?>', '<?php nxs_l18n_e("May", "nxs_td"); ?>', '<?php nxs_l18n_e("June", "nxs_td"); ?>', 'July', '<?php nxs_l18n_e("August", "nxs_td"); ?>', '<?php nxs_l18n_e("September", "nxs_td"); ?>', '<?php nxs_l18n_e("October", "nxs_td"); ?>', '<?php nxs_l18n_e("November", "nxs_td"); ?>', '<?php nxs_l18n_e("December", "nxs_td"); ?>'],
					nextText: '<?php nxs_l18n_e("Next", "nxs_td"); ?>',
        	prevText: '<?php nxs_l18n_e("Previous", "nxs_td"); ?>'
				});
				nxs_js_log('done..');
			}
		);
	</script>	
  
	<?php 
	
	// var_dump($args);
	
	$html = ob_get_contents();
	ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

function nxs_widgets_contactitemdatetime_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	$mixedattributes = array_merge($temp_array, $args);
	
	$image_imageid = $mixedattributes['image_imageid'];
	$title = $mixedattributes['title'];
	$text = $mixedattributes['text'];
	$destination_articleid = $mixedattributes['destination_articleid'];
	
	$lookup = wp_get_attachment_image_src($image_imageid, 'full', true);
	
	$width = $lookup[1];
	$height = $lookup[2];		
	
	$lookup = wp_get_attachment_image_src($image_imageid, 'thumbnail', true);
	$url = $lookup[0];
	$url = nxs_img_getimageurlthemeversion($url);

	global $nxs_global_placeholder_render_statebag;
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["enable_decoratewidget"] = false;
	$hovermenuargs["enable_deletewidget"] = false;
	$hovermenuargs["enable_deleterow"] = true;
	$hovermenuargs["metadata"] = $mixedattributes;	
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
	/* ADMIN EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-contactitemdatetime-item";
	
	/* ADMIN OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	echo '
	<div ' . $class . '>
		<div class="content2">
			<div class="box">
	        	<div class="box-title nxs-width20"><h4><span class="nxs-icon-clock" style="font-size: 16px;" /> Date and time</h4></div>
				<div class="box-content nxs-width80">'.$formlabel.'</div>
			</div>
			<div class="nxs-clear"></div>
		</div>
	</div>';
	
	/* ------------------------------------------------------------------------------------------------- */

	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = ob_get_contents();
	ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

// Define the properties of this widget
function nxs_widgets_contactitemdatetime_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_contactitemdatetime_gettitle(),
		"sheeticonid" => nxs_widgets_contactitemdatetime_geticonid(),
	
		"fields" => array
		(
			// GENERAL			
			
			array
			( 
				"id" 				=> "formlabel",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Label", "nxs_td"),
				"placeholder" => nxs_l18n__("Label goes here", "nxs_td"),
			),
			
			array
			( 
				"id" 				=> "elementid",
				"type" 				=> "input",
				"visibility"	=> "hide",
				"label" 			=> nxs_l18n__("Element ID", "nxs_td"),
				"placeholder" => nxs_l18n__("Enter a unique ID for this element", "nxs_td"),
			),
			/*
			can only be set by code
			array
			( 
				"id" 				=> "overriddenelementid",
				"type" 				=> "input",
				"visibility"	=> "text",
				"label" 			=> nxs_l18n__("Override default element ID", "nxs_td"),
				"placeholder" => nxs_l18n__("Leave blank to use default", "nxs_td"),
			),
			*/
			array
			( 
				"id" 				=> "isrequired",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Is required", "nxs_td"),
			),
			array(
				"id" 				=> "datefilter_istodayallowed",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Today allowed", "nxs_td"),
			),
		)
	);
	
	return $options;
}

function nxs_widgets_contactitemdatetime_initplaceholderdata($args)
{
	extract($args);

	$args["elementid"] = nxs_generaterandomstring(6);
	$args["datefilter_istodayallowed"] = "true";

	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
