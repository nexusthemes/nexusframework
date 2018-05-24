<?php

function nxs_widgets_formitemprivacyconsent_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-handshake";
}

function nxs_widgets_formitemprivacyconsent_gettitle()
{
	return nxs_l18n__("Privacy consent", "nxs_td");
}

function nxs_widgets_formitemprivacyconsent_getformitemsubmitresult($args)
{
	// $args consists of "metadata"
	// combined with $_POST this should feed us with all information
	// needed to produce the result :)
	
	extract($args);
	
	$elementid = $metadata["elementid"];
	$overriddenelementid = $metadata["overriddenelementid"];
	$formlabel = $metadata["formlabel"];
		
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
	
	if (true)
	{
		if (!nxs_dataprotection_isprivacysupported_and_configured())
		{
			// error
			$result["validationerrors"][] = nxs_l18n__("Privacy policy is not setup properly (contact the site admin)", "nxs_td");
			$result["markclientsideelements"][] = $key;
		}
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
function nxs_widgets_formitemprivacyconsent_renderinformbox($args)
{
	extract($args);
	
	extract($metadata, EXTR_PREFIX_ALL, "metadata");
	
	$result = array();
	$result["result"] = "OK";
	
	nxs_requirewidget("formbox");
	$prefix = nxs_widgets_formbox_getclientsideprefix($postid, $placeholderid);
	
	$privacypolicyurl = nxs_dataprotection_getprivacypolicyurl();
	
	if ($metadata_overriddenelementid != "")
	{
		$key = $metadata_overriddenelementid;
	}
	else
	{
		$key = $prefix . $metadata_elementid;
	}

	if ($metadata_popuptextifnoconsent == "")
	{
		$metadata_popuptextifnoconsent = "We cannot process your form without your consent to the privacy policy";
	}
	
	//
	// render actual control / html
	//
	
	nxs_ob_start();

	$checkedattribute = "";
	if ($value != "")
	{
		$checkedattribute = "checked='checked'";
	}
	?>
	<div style='margin-bottom: 10px;'>
  <input type="checkbox" style='margin-bottom: 0px; height: 13px;' id="<?php echo $key; ?>" name="<?php echo $key; ?>" class="field_name nxs-requires-explicitconsent-before-sending" <?php echo $checkedattribute; ?> data-textnoconsent="<?php echo $metadata_popuptextifnoconsent; ?>" />
  <label for="<?php echo $key; ?>" style='display: inline-block;' class="field_name">
  	<a href="<?php echo $privacypolicyurl; ?>" target="_blank"><?php echo $metadata_formlabel;?><?php if (true) { ?>*<?php } ?></a>
  </label>
  </div>
	<?php
	// var_dump($args);
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

function nxs_widgets_formitemprivacyconsent_render_webpart_render_htmlvisualization($args)
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

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-formitemprivacyconsent-item";
	
	/* ADMIN OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	echo '
	<div class="nxs-dragrow-handler nxs-padding-menu-item">
		<div class="content2">
			<div class="box">
	        	<div class="box-title nxs-width40"><h4><span class="nxs-icon-handshake" style="font-size: 16px;" /> Privacy consent</h4></div>
				<div class="box-content  nxs-width60">'.$formlabel.'</div>
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
function nxs_widgets_formitemprivacyconsent_home_getoptions($args) 
{
	if (!nxs_dataprotection_isprivacysupported())
	{
		$fixurl = get_admin_url(null, "update-core.php");
		return nxs_popup_factory_createnotificationoptions("Sorry", "Please first <a href='$fixurl'>upgrade to the WP 4.9.6 or above</a>.");
	}
	
	// this is wp 4.9.6 or above
	if (nxs_dataprotection_getprivacypolicy_postid() == "")
	{
		$fixurl = get_admin_url(null, "privacy.php");
		return nxs_popup_factory_createnotificationoptions("Sorry", "Please first <a href='$fixurl'>configure the privacy policy page in the WP backend and ensure its published</a>.");
	}
	
	$options = array
	(
		"sheettitle" => nxs_widgets_formitemprivacyconsent_gettitle(),
		"sheeticonid" => nxs_widgets_formitemprivacyconsent_geticonid(),
	
		"fields" => array
		(
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
		)
	);
	
	$privacypolicyurl = nxs_dataprotection_getprivacypolicyurl();
	if ($privacypolicyurl != "")
	{
		$custom = nxs_l18n__("<a target='_blank' href='{$privacypolicyurl}'>{$privacypolicyurl}</a>");
	}
	else
	{
		$custom = nxs_l18n__("Error; privacy policy is not yet configured");
	}
	
	$fixurl = get_admin_url(null, "privacy.php");
	$options["fields"][] = array
	( 
		"id"				=> "editlinkref",
		"type" 				=> "custom",
		"label" 			=> nxs_l18n__("Change privacy policy link", "nxs_td"),
		"custom" => "$custom | <a href='$fixurl' class='nxsbutton1'>Edit</a>",
	);
	
	$options["fields"][] = array
	( 
		"id" 				=> "popuptextifnoconsent",
		"type" 				=> "input",
		"label" 			=> nxs_l18n__("No consent error text", "nxs_td"),
	);
	
	return $options;
}

function nxs_widgets_formitemprivacyconsent_initplaceholderdata($args)
{
	extract($args);

	$args["elementid"] = nxs_generaterandomstring(6);
	$args["popuptextifnoconsent"] = "We cannot process your form without your consent to the privacy policy";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_dataprotection_nexusframework_widget_formitemprivacyconsent_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("widget-defaultformitem");
}

?>