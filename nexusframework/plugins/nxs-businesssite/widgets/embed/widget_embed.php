<?php

function nxs_widgets_embed_geticonid() 
{
	//$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-text";
}

// Setting the widget title
function nxs_widgets_embed_gettitle() 
{
	return nxs_l18n__("embed", "nxs_td");
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_embed_home_getfieldstoadd()
{
	$result = array
	(
	);
	return $result;
}

// Define the properties of this widget
function nxs_widgets_embed_home_getoptions($args) 
{
	$fields = array
	(	
	);

	
	$options = array
	(
		"sheettitle" => nxs_widgets_embed_gettitle(),
		"sheeticonid" => nxs_widgets_embed_geticonid(),
		"fields" => $fields,
	);
	
	return $options;
}


/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_embed_render_webpart_render_htmlvisualization($args) 
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
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-embed ";
	
	// EXPRESSIONS
	// ---------------------------------------------------------------------------------------------------- 
	
	global $nxs_global_current_containerpostid_being_rendered;
	$containerpostid = $nxs_global_current_containerpostid_being_rendered;

	// OUTPUT
	// ---------------------------------------------------------------------------------------------------- 
	
	if ($alternativemessage != "" && $alternativemessage != null)
	{
		nxs_renderplaceholderwarning($alternativemessage);
	} 
	else 
	{
		$args = $temp_array;
		//  override the following parameter
		$args["postid"] = $containerpostid;
		$args["placeholderid"] = $placeholderid;
		$args["placeholdertemplate"] = "embed";
		
		$url = "http://webdesign.c1.us-e1.nexusthemes.com/wordpress-themes-in-businesstype/";
		$url = nxs_addqueryparametertourl_v2($url, "frontendframework", "alt", true, true);
		// add query parameters based upon the lookup tables of the widget (options)
		$url = nxs_addqueryparametertourl_v2($url, "businesstype", "electrician", true, true);
		$url = nxs_addqueryparametertourl_v2($url, "devicetype", "laptopf", true, true);
		
		$content = file_get_contents($url);
		// tune the output (should be done by the content platform)
		$content = str_replace("nxs-content-container", "template-content-container", $content);
		$content = str_replace("nxs-article-container", "template-article-container", $content);
		$content = str_replace("nxs-postrows", "template-postrows", $content);
		$content = str_replace("nxs-row", "template-row", $content);
		$content = str_replace("nxs-placeholder-list", "template-placeholder-list", $content);
		$content = str_replace("ABC", "template-ABC", $content);
		$content = str_replace("XYZ", "template-XYZ", $content);
		$content = str_replace("nxs-widget-", "template-widget-", $content);
		$content = str_replace("nxs-widget", "template-widget", $content);
		
		$content = str_replace("nxs-containsimmediatehovermenu", "template-containsimmediatehovermenu", $content);
		$content = str_replace("has-no-sidebar", "template-has-no-sidebar", $content);
		$content = str_replace("nxs-elements-container", "template-XYZ", $content);
		$content = str_replace("nxs-runtime-autocellsize", "template-runtime-autocellsize", $content);
		
		echo $content;
	}

	// note, we set the generic widget hover menu AFTER rendering, as the blog widget
	// will also set the generic hover menu; we don't want to see the generic hover
	// menu of the blog, we want to see it of this specific wrapping type
	nxs_widgets_setgenericwidgethovermenu($postid, $placeholderid, $placeholdertemplate);

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
function nxs_widgets_embed_initplaceholderdata($args)
{
	extract($args);

	// 
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}