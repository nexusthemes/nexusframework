<?php

function nxs_widgets_contactitemdate_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-calendar"; // . $widget_name;
}

function nxs_widgets_contactitemdate_gettitle()
{
	return nxs_l18n__("Date input", "nxs_td");
}

function nxs_widgets_contactitemdate_getformitemsubmitresult($args)
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
	
	$value = $_REQUEST[$key];
	
	if ($isrequired != "")
	{
		// it is required field
		if (trim($value) == '')
		{
			// error
			$result["validationerrors"][] = sprintf(nxs_l18n__("%s is a required field", "nxs_td"), $formlabel);
			$result["markclientsideelements"][] = $key;
		}
	}
	
	$result["output"] = "$formlabel: $value";
	
	return $result;
}


// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_contactitemdate_renderincontactbox($args)
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
	
	//
	// render actual control / html
	//
	
	$dateformat = nxs_date_getdatepickerformat();
	
	ob_start();

	?>
  <label class="field_name"><?php echo $metadata_formlabel;?><?php if ($metadata_isrequired != "") { ?>*<?php } ?></label>
  <input type="text" id="<?php echo $key; ?>" name="<?php echo $key; ?>" class="field_name">
  
  <script type='text/javascript'>
	
		jQuery(document).ready
		(
			function() 
			{
				nxs_js_log('setting date..');
				// activate datepicker			
				jQuery("#<?php echo $key; ?>").datepicker({ 
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
					monthNames: ['<?php nxs_l18n_e("January", "nxs_td"); ?>', '<?php nxs_l18n_e("February", "nxs_td"); ?>', '<?php nxs_l18n_e("March", "nxs_td"); ?>', '<?php nxs_l18n_e("April", "nxs_td"); ?>', '<?php nxs_l18n_e("May", "nxs_td"); ?>', '<?php nxs_l18n_e("June", "nxs_td"); ?>', '<?php nxs_l18n_e("July", "nxs_td"); ?>', '<?php nxs_l18n_e("August", "nxs_td"); ?>', '<?php nxs_l18n_e("September", "nxs_td"); ?>', '<?php nxs_l18n_e("October", "nxs_td"); ?>', '<?php nxs_l18n_e("November", "nxs_td"); ?>', '<?php nxs_l18n_e("December", "nxs_td"); ?>'],
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

function nxs_widgets_contactitemdate_render_webpart_render_htmlvisualization($args)
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

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-contactitemdate-item";
	
	/* ADMIN OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	echo '
	<div ' . $class . '>
		<div class="content2">
			<div class="box">
	        	<div class="box-title nxs-width20"><h4><span class="nxs-icon-calendar-2" style="font-size: 16px;" /> Date</h4></div>
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
function nxs_widgets_contactitemdate_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_contactitemdate_gettitle(),
		"sheeticonid" => nxs_widgets_contactitemdate_geticonid(),
	
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

function nxs_widgets_contactitemdate_initplaceholderdata($args)
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
