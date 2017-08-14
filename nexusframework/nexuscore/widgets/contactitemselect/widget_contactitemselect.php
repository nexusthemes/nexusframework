<?php

function nxs_widgets_contactitemselect_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-arrow-down-light"; // . $widget_name;
}

function nxs_widgets_contactitemselect_gettitle()
{
	return nxs_l18n__("Dropdown (single select)", "nxs_td");
}

function nxs_widgets_contactitemselect_getformitemsubmitresult($args)
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
	
	$value = $_POST[$key];
	
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
function nxs_widgets_contactitemselect_renderincontactbox($args)
{
	extract($args);
	
	//
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
	
	nxs_ob_start();

	?>
	
  <label class="field_name"><?php echo $metadata_formlabel;?><?php if ($metadata_isrequired != "") { ?>*<?php } ?></label>
	<select id="<?php echo $key; ?>" name="<?php echo $key; ?>" class="field_name" value="<?php echo $value;?>">
		
		<?php
		$metadata_selectables = str_replace("@@NXSNEWLINE@@", "\n", $metadata_selectables);
		$metadata_selectables = str_replace("\r\n", "\n", $metadata_selectables);
		
		if (is_string($metadata_selectables))
		{
			$splitted = preg_split('/\n|[\n]/', $metadata_selectables);
			foreach($splitted as $splittedpiece)
			{
				if ($splittedpiece == "")
				{
					// ignore
				}
				else
				{
				?>
				<option value="<?php echo nxs_render_html_escape_doublequote($splittedpiece); ?>"><?php echo nxs_render_html_escape_gtlt($splittedpiece); ?></option>
				<?php
				}
			}
		} 
		else if (is_array($metadata_selectables))
		{
			// echo "its not a string";
			// assumed to be a variable
			foreach ($metadata_selectables as $metadata_key => $metadata_val)
			{
				?>
				<option value="<?php echo nxs_render_html_escape_doublequote($metadata_key); ?>"><?php echo nxs_render_html_escape_gtlt($metadata_val); ?></option>
				<?php
			}
		}
		else
		{
			//
			?>
			<option value="">Not supported</option>
			<?php
		}
		?>
	</select>
	<div class="nxs-clear nxs-filler"></div>
	<?php
	
	// var_dump($args);
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

function nxs_widgets_contactitemselect_render_webpart_render_htmlvisualization($args)
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
	
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-contactitemselect-item";
	
	/* ADMIN OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	echo '
	<div class="nxs-dragrow-handler nxs-padding-menu-item">
		<div class="content2">
			<div class="box">
	        	<div class="box-title nxs-width40"><h4><span class="nxs-icon-arrow-down-light" style="font-size: 16px;" /> Dropdown</h4></div>
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
function nxs_widgets_contactitemselect_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_contactitemselect_gettitle(),
		"sheeticonid" => nxs_widgets_contactitemselect_geticonid(),
	
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
			array
			( 
				"id" 				=> "isrequired",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Is required", "nxs_td"),
			),

			array
			( 
				"id" 				=> "selectables",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Options", "nxs_td"),
				"rows"			=> "6",
			),
		)
	);
	
	return $options;
}

function nxs_widgets_contactitemselect_initplaceholderdata($args)
{
	extract($args);

	$args["elementid"] = nxs_generaterandomstring(6);
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
