<?php

function nxs_widgets_busrulehome_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-home";
}

// Setting the widget title
function nxs_widgets_busrulehome_gettitle() {
	return nxs_l18n__("The homepage", "nxs_td");
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_busrulehome_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_busrulehome_gettitle(),
		"sheeticonid" => nxs_widgets_busrulehome_geticonid(),
		//"sheethelp" => nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1764396204"),
		"fields" => array
		(
			array
			( 
				"id" 					=> "wrapper_condition_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Condition", "nxs_td"),
			),
			array
			(
				"id" 				=> "header_postid",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Header template (local id or <a href='https://docs.google.com/spreadsheets/d/1ve5P0pJL_Ofr8cfNtjZHnRju1RfFe2XXNpwz9aUhOt8/edit#gid=0' target='_blank'>remote ref</a>)", "nxs_td"),
			),
			array
			(
				"id" 				=> "content_postid",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Content template (local id or <a href='https://docs.google.com/spreadsheets/d/1ve5P0pJL_Ofr8cfNtjZHnRju1RfFe2XXNpwz9aUhOt8/edit#gid=0' target='_blank'>remote ref</a>)", "nxs_td"),
			),
			array
			(
				"id" 				=> "templaterules_modeluris",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Model URIs", "nxs_td"),
			),
			array
			(
				"id" 				=> "templaterules_lookups",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Lookup values", "nxs_td"),
			),
			array
			( 
				"id" 					=> "wrapper_condition_end",
				"type" 				=> "wrapperend"
			),			
		)
	);
	
	$moreoptions = nxs_busrules_getgenericoptions($args);
	
	// strip the content_postid from the moreoptions
	$items = $moreoptions["fields"];
	$i = -1;
	foreach ($items as $item)
	{
		$i++;
		if ($item["id"] == "content_postid")
		{
			unset($items[$i]);
		}
		else if ($item["id"] == "header_postid")
		{
			unset($items[$i]);
		}
	}
	
	$options["fields"] = array_merge($options["fields"], $items);
	
	return $options;
}

/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_busrulehome_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);

	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));

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
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["enable_decoratewidget"] = false;
	$hovermenuargs["enable_deletewidget"] = false;
	$hovermenuargs["enable_deleterow"] = true;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);

	
	// Turn on output buffering
	nxs_ob_start();
	
	global $nxs_global_placeholder_render_statebag;
	if ($shouldrenderalternative == true) {
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . "-warning ";
	} else {
		// Appending custom widget class
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " ";
	}
	
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;
	/*
	if (
	$person == "" &&
	nxs_has_adminpermissions()) {
		$shouldrenderalternative = true;
	}
	*/
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) 
	{
		nxs_renderplaceholderwarning(nxs_l18n__("Missing input", "nxs_td")); 
	} 
	else 
	{
		$url = nxs_geturl_home();
		$filteritemshtml = "<a href='{$url}' target='_blank'>Homepage</a>";
		nxs_widgets_busrule_pagetemplate_renderrow(nxs_widgets_busrulehome_geticonid(), $filteritemshtml, $mixedattributes);
	} 
	
	/* ------------------------------------------------------------------------------------------------- */
	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	return $result;
}

function nxs_widgets_busrulehome_initplaceholderdata($args)
{
	extract($args);

	$args["flow_stopruleprocessingonmatch"] = "true";
	// add more initialization here if needed ...
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_busrule_busrulehome_process($args, &$statebag)
{
	$result = array();
	$result["result"] = "OK";

	$metadata = $args["metadata"];
	
	
	
	global $wp_query;
	$p = $wp_query->posts[0];
	$currentpostid = $p->ID;
	$homepageid = nxs_gethomepageid();
	
	$ishomepage = false;
	
	// check through the uri
	if (true)
	{
		$serverrequri = nxs_geturicurrentpage();
		$pieces = explode("?", $serverrequri);
		$path = trim($pieces[0], "/");
		if ($path == "")
		{
			$ishomepage = true;
		}
	}
	
	// check through the configuration
	if (true)
	{
		if ($currentpostid == $homepageid)
		{
			$ishomepage = true;
		}
	}
	
	if ($ishomepage)
	{
		$result["ismatch"] = "true";
		
		// process configured site wide elements
		$sitewideelements = nxs_pagetemplates_getsitewideelements();
		foreach($sitewideelements as $currentsitewideelement)
		{
			$selectedvalue = $metadata[$currentsitewideelement];
			if ($selectedvalue == $filter_authoremail)
			{
				// skip
			} 
			else if ($selectedvalue == "@leaveasis")
			{
				// skip
			}
			else if ($selectedvalue == "@suppressed")
			{
				// reset
				$statebag["out"][$currentsitewideelement] = 0;
			}
			else
			{
				// set the value as selected
				$statebag["out"][$currentsitewideelement] = $metadata[$currentsitewideelement];
			}
		}

		// concatenate the modeluris and modelmapping (do NOT yet evaluate them; this happens in stage 2, see #43856394587)
		$statebag["out"]["templaterules_modeluris"] .= "\r\n" . $metadata["templaterules_modeluris"];
		$statebag["out"]["templaterules_lookups"] .= "\r\n" . $metadata["templaterules_lookups"];

		// instruct rule engine to stop further processing if configured to do so (=default)
		$flow_stopruleprocessingonmatch = $metadata["flow_stopruleprocessingonmatch"];
		if ($flow_stopruleprocessingonmatch != "")
		{
			$result["stopruleprocessingonmatch"] = "true";
		}
	}
	else
	{
		$result["ismatch"] = "false";
	}
	
	return $result;
}