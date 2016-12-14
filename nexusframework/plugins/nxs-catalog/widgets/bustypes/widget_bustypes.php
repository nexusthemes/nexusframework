<?php

function nxs_widgets_bustypes_geticonid() {
	//$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-eye";
}

// Setting the widget title
function nxs_widgets_bustypes_gettitle() {
	return nxs_l18n__("Johan", "nxs_td");
}

// obsolete
function nxs_widgets_bustypes_getunifiedstylingprefix()
{
	$result = "bustypeswidget";
	return $result;
}

function nxs_widgets_bustypes_getunifiedstylinggroup()
{
	$result = "bustypeswidget";
	return $result;
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_bustypes_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_bustypes_gettitle(),
		"sheeticonid" => nxs_widgets_bustypes_geticonid(),
		
		"unifiedstyling" => array
		(
			// obsolete
			"prefix" => nxs_widgets_bustypes_getunifiedstylingprefix(),
			"group" => nxs_widgets_bustypes_getunifiedstylinggroup(),
		),		
			
		"fields" => $fields,
	);
	
	return $options;
}

/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_bustypes_render_webpart_render_htmlvisualization($args) 
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
	nxs_ob_start();
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));

	global $nxs_global_row_render_statebag;
	global $nxs_global_placeholder_render_statebag;
		
	// Appending custom widget class
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-bustypes ";
	
	// EXPRESSIONS
	// ---------------------------------------------------------------------------------------------------- 
	
	//if ($button_text == "") 
	//{
	//	$alternativemessage = nxs_l18n__("Warning: no button text", "nxs_td");
	//}

	
	// OUTPUT
	// ---------------------------------------------------------------------------------------------------- 
	
	if ($alternativemessage != "" && $alternativemessage != null)
	{
		nxs_renderplaceholderwarning($alternativemessage);
	} 
	else 
	{
		echo "johan johan johan";
	}

	// note, we set the generic widget hover menu AFTER rendering, as the blog widget
	// will also set the generic hover menu; we don't want to see the generic hover
	// menu of the blog, we want to see it of this specific wrapping type
	nxs_widgets_setgenericwidgethovermenu($postid, $placeholderid, $placeholdertemplate);

	// -------------------------------------------------------------------------------------------------
	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	
	return $result;
}

/* INITIATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_bustypes_initplaceholderdata($args)
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	$result = nxs_widgets_initplaceholderdatageneric($args, $widgetname);
	return $result;
}