<?php

function nxs_widgets_wooproductreference_geticonid() {
	//$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-wooproductreference";
}

// Setting the widget title
function nxs_widgets_wooproductreference_gettitle() {
	return nxs_l18n__("wooproductreference", "nxs_td");
}

function nxs_widgets_wooproductreference_getunifiedstylinggroup()
{
	$result = "wooproductreferencewidget";
	return $result;
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_wooproductreference_home_getoptions($args) 
{
	// we wrappen naar de fields van de options van de enkelvoudige widget
	nxs_requirewidget("text");
	$parentwidget = nxs_widgets_text_home_getoptions($args);
	$fieldsparentwidget = $parentwidget["fields"];
	
	$index = 0;
	$indextoberemoved = array();
	foreach ($fieldsparentwidget as $currentfield)
	{
		if ($currentfield["id"] == "destination_articleid") { $indextoberemoved[] = $index; }
		else if ($currentfield["id"] == "title") { $indextoberemoved[] = $index; }
		else if ($currentfield["id"] == "image_imageid") { $indextoberemoved[] = $index; }
		
		$index++;
	}
	foreach ($indextoberemoved as $currentindextoberemoved)
	{
		unset($fieldsparentwidget[$currentindextoberemoved]);
	}
	
	$fields = array
	(
		array(
				"id" 				=> "destination_articleid",
				"type" 				=> "wooprod_link",
				"label" 			=> nxs_l18n__("Product", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to a product within your site.", "nxs_td"),
		),
	);
	// add more fields here, if needed
		
	$fields = array_merge($fields, $fieldsparentwidget);

	
	$options = array
	(
		"sheettitle" => nxs_widgets_wooproductreference_gettitle(),
		"sheeticonid" => nxs_widgets_wooproductreference_geticonid(),
		
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_wooproductreference_getunifiedstylinggroup(),
		),		
			
		"fields" => $fields,
	);
	
	return $options;
}


/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_wooproductreference_render_webpart_render_htmlvisualization($args) 
{
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
	// Turn on output buffering
	ob_start();
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));

	global $nxs_global_row_render_statebag;
	global $nxs_global_placeholder_render_statebag;
		
	// Appending custom widget class
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-wooproductreference ";
	
	// EXPRESSIONS
	// ---------------------------------------------------------------------------------------------------- 
	
	$args = $temp_array;

	global $nxs_global_current_containerpostid_being_rendered;
	$containerpostid = $nxs_global_current_containerpostid_being_rendered;
	
	// add more args as needed
	
	$args["postid"] = $containerpostid;
	$args["placeholderid"] = $placeholderid;
	$args["placeholdertemplate"] = "text";
	$args["title"] = nxs_gettitle_for_postid($destination_articleid);	// title of product
	$args["image_imageid"] = get_post_thumbnail_id($destination_articleid);	// featured image of product
			
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "")
	{
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_wooproductreference_getunifiedstylinggroup(), $unistyle);
		$args = array_merge($args, $unistyleproperties);	
	}

	//			
	nxs_requirewidget($args["placeholdertemplate"]);
						
	$renderresult = nxs_widgets_text_render_webpart_render_htmlvisualization($args);
	echo $renderresult["html"];

	// note, we set the generic widget hover menu AFTER rendering, as the blog widget
	// will also set the generic hover menu; we don't want to see the generic hover
	// menu of the blog, we want to see it of this specific wrapping type
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $args;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs); 

	// -------------------------------------------------------------------------------------------------
	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = ob_get_contents();
	ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	
	return $result;
}

/* INITIATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_wooproductreference_initplaceholderdata($args)
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));

	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_wooproductreference_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	$result = nxs_widgets_initplaceholderdatageneric($args, $widgetname);
	return $result;
}

?>