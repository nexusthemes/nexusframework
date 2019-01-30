<?php

function nxs_widgets_contactitemreplyto_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-reply"; // . $widget_name;
}

function nxs_widgets_contactitemreplyto_gettitle()
{
	return nxs_l18n__("Reply to", "nxs_td");
}

function nxs_widgets_contactitemreplyto_getformitemsubmitresult($args)
{
	// $args consists of "metadata"
	// combined with $_POST this should feed us with all information
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
	
	nxs_requirewidget("formbox");
	$prefix = nxs_widgets_formbox_getclientsideprefix($postid, $placeholderid);
	
	
	
	if ($overriddenelementid != "")
	{
		$key = $overriddenelementid;
	}
	else
	{
		$key = $prefix . $elementid;
	}	
	
	$value = $_POST[$key];

	if (trim($value) != '')
	{				
		if (!nxs_isvalidemailaddress($value))
		{
			// error
			$result["validationerrors"][] = sprintf(nxs_l18n__("%s contains an invalid e-mail address", "nxs_td"), $formlabel);
			$result["markclientsideelements"][] = $key;
		}
	}
	
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
	
	$result["output"] = "<b>$formlabel:</b> $value";
	
	return $result;
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_contactitemreplyto_renderinformbox($args)
{
	extract($args);
	
	extract($metadata, EXTR_PREFIX_ALL, "metadata");
	
	$result = array();
	$result["result"] = "OK";
	
	nxs_requirewidget("formbox");
	$prefix = nxs_widgets_formbox_getclientsideprefix($postid, $placeholderid);
	
	if ($metadata_overriddenelementid != "")
	{
		$key = $metadata_overriddenelementid;
	}
	else
	{
		$key = $prefix . $metadata_elementid;
	}
	
	if (!isset($value) || $value == "")
	{
		// first apply any shortcodes if applicable
		$pimped_initialtext = do_shortcode($metadata_initialtext);
		$value = $pimped_initialtext;
	}
	
	//
	// render actual control / html
	//
	
	nxs_ob_start();

	$colorzencssclass = "";
	if ($form_metadata["items_colorzen"] != "")
	{
		$colorzencssclass = "nxs-colorzen nxs-colorzen-" . $form_metadata["items_colorzen"];
	}

	// todo: add support for "pattern" attribute
	// todo: add support for "required" attribute

	?>
  <label class="field_name"><?php echo $metadata_formlabel;?><?php if ($metadata_isrequired != "") { ?>*<?php } ?></label>
  <input type="email" id="<?php echo $key; ?>" name="<?php echo $key; ?>" class="field_name <?php echo $colorzencssclass; ?>" value="<?php echo $value;?>" />
	<?php 
	
	// var_dump($args);
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

function nxs_widgets_contactitemreplyto_render_webpart_render_htmlvisualization($args)
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
	
	$lookup = nxs_wp_get_attachment_image_src($image_imageid, 'full', true);
	
	$width = $lookup[1];
	$height = $lookup[2];		
	
	$lookup = nxs_wp_get_attachment_image_src($image_imageid, 'thumbnail', true);
	$url = $lookup[0];
	$url = nxs_img_getimageurlthemeversion($url);

	global $nxs_global_placeholder_render_statebag;
	
	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		//	
		$hovermenuargs = array();
		$hovermenuargs["postid"] = $postid;
		$hovermenuargs["placeholderid"] = $placeholderid;
		$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
		$hovermenuargs["enable_decoratewidget"] = false;
		$hovermenuargs["enable_deletewidget"] = false;
		$hovermenuargs["enable_deleterow"] = true;
		$hovermenuargs["metadata"] = $mixedattributes;	
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);	
	}
	
	/* ADMIN EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-contactitemreplyto-item";
	
	/* ADMIN OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	echo '
	<div class="nxs-dragrow-handler nxs-padding-menu-item">
		<div class="content2">
			<div class="box">
	        	<div class="box-title nxs-width40"><h4><span class="nxs-icon-reply" style="font-size: 16px;" /> Reply to</h4></div>
				<div class="box-content nxs-width60">'.$formlabel.'</div>
			</div>
			<div class="nxs-clear"></div>
		</div>
	</div>';
	
	/* ------------------------------------------------------------------------------------------------- */

	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

// Define the properties of this widget
function nxs_widgets_contactitemreplyto_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_contactitemreplyto_gettitle(),
		"sheeticonid" => nxs_widgets_contactitemreplyto_geticonid(),
	
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

			array
			( 
				"id" 				=> "placeholder",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Placeholder", "nxs_td"),
			),

			array
			( 
				"id" 				=> "initialtext",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Initial text", "nxs_td"),
			),

		)
	);
	
	return $options;
}

function nxs_widgets_contactitemreplyto_initplaceholderdata($args)
{
	extract($args);

	$args["elementid"] = nxs_generaterandomstring(6);
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_dataprotection_nexusframework_widget_contactitemreplyto_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("widget-defaultformitem");
}

?>