<?php

function nxs_widgets_phone_geticonid() 
{
	//$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-phone";
}

// Setting the widget title
function nxs_widgets_phone_gettitle() 
{
	return nxs_l18n__("Phone", "nxs_td");
}

// obsolete
function nxs_widgets_phone_getunifiedstylingprefix()
{
	$result = "phone";
	return $result;
}

function nxs_widgets_phone_getunifiedstylinggroup()
{
	$result = "textwidget";
	return $result;
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_phone_home_getfieldstoadd()
{
	$result = array
	(
	);
	return $result;
}

function nxs_widgets_phone_getwidgettoextend()
{
	return "text";
}

function nxs_widgets_phone_getoptionsofextendedwidget($p)
{
	// ensure widget is loaded
	nxs_requirewidget(nxs_widgets_phone_getwidgettoextend());

	$functionnametoinvoke = 'nxs_widgets_' . nxs_widgets_phone_getwidgettoextend() . '_home_getoptions';
	if (function_exists($functionnametoinvoke))
	{
		$args = array();
		$args["args"] = $p;
		$parameters = array($args);
		$result = call_user_func_array($functionnametoinvoke, $parameters);
	}
	else
	{
		nxs_webmethod_return_nack("function not found; $functionnametoinvoke");
	}
	
	return $result;
}

function nxs_widgets_phone_getrenderresultofextendedwidget($p)
{
	// ensure widget is loaded
	nxs_requirewidget(nxs_widgets_phone_getwidgettoextend());

	$functionnametoinvoke = 'nxs_widgets_' . nxs_widgets_phone_getwidgettoextend() . '_render_webpart_render_htmlvisualization';
	if (function_exists($functionnametoinvoke))
	{
		$args = array();
		$args["args"] = $p;
		$parameters = array($args);
		$result = call_user_func_array($functionnametoinvoke, $parameters);
	}
	else
	{
		nxs_webmethod_return_nack("function not found; $functionnametoinvoke");
	}
	
	return $result;
}


function nxs_widgets_phone_home_getfieldidstoremove()
{
	$result = array();

	

	//$result[] = "csv_data";
	
	return $result;
}

// Define the properties of this widget
function nxs_widgets_phone_home_getoptions($args) 
{
	// we wrappen naar de fields van de options van de enkelvoudige widget
	nxs_requirewidget(nxs_widgets_phone_getwidgettoextend());
	
	$optionsparentwidget = nxs_widgets_phone_getoptionsofextendedwidget($args);
	$fieldsparentwidget = $optionsparentwidget["fields"];
	
	global $businesssite_instance;
	$contentmodel = $businesssite_instance->getcontentmodel();
	$taxonomy = "nxs_phone";
	$abstractpostid = $contentmodel[$taxonomy]["taxonomy"]["postid"];
	
	$editurl = get_edit_post_link($abstractpostid);
	
	$fields = array
	(
		array
		( 
			"id" 				=> "wrapper_input_begin",
			"type" 				=> "wrapperbegin",
			"label" 			=> nxs_l18n__("Phone number", "nxs_td"),
		),

		//
		array
    (
			"id" 					=> "content_custom",
			"type" 				=> "custom",
			"custom"	=> "<div><a class='nxsbutton' href='{$editurl}'>Edit</a></div>",
		),		
		
		array
		( 
			"id" 				=> "wrapper_input_end",
			"type" 				=> "wrapperend"
		),		
	);
	
	// add more fields here, if needed
	$fields = array_merge($fields, $fieldsparentwidget);
	$fields = array_merge($fields, nxs_widgets_phone_home_getfieldstoadd());
	
	$fieldids_to_remove = nxs_widgets_phone_home_getfieldidstoremove();
	
	// remove fields here, if needed
	$fieldsparentwidget;
	$index = 0;
	$indextoberemoved = array();
	foreach ($fields as $currentfield)
	{
		//
		if (in_array($currentfield["id"], $fieldids_to_remove))
		{
			$indextoberemoved[] = $index;
		}
	}
	// also get rid of all content-fields
	foreach ($fields as $currentfield)
	{
		if ($currentfield["unicontentablefield"] === true)
		{
			$indextoberemoved[] = $index;
		}
		$index++;
	}
	foreach ($indextoberemoved as $currentindextoberemoved)
	{
		unset($fields[$currentindextoberemoved]);
	}
	
	$options = array
	(
		"sheettitle" => nxs_widgets_phone_gettitle(),
		"sheeticonid" => nxs_widgets_phone_geticonid(),
		
		"unifiedstyling" => array
		(
			// obsolete
			"prefix" => nxs_widgets_phone_getunifiedstylingprefix(),
			"group" => nxs_widgets_phone_getunifiedstylinggroup(),
		),		
			
		"fields" => $fields,
	);
	
	return $options;
}


/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_phone_render_webpart_render_htmlvisualization($args) 
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
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-phone ";
	
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
		// "specsurl"
		// "buyurl"
		// "viewurl"
		
		$args = $temp_array;
		//  override the following parameter
		$args["postid"] = $containerpostid;
		$args["placeholderid"] = $placeholderid;
		$args["placeholdertemplate"] = nxs_widgets_phone_getwidgettoextend();

		// apply unistyle for this specific widget (not the extended /parent/ one)
		$unistyle = $temp_array["unistyle"];
		if (isset($unistyle) && $unistyle != "")
		{
			// blend unistyle properties
			$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_phone_getunifiedstylinggroup(), $unistyle);
			$args = array_merge($args, $unistyleproperties);	
		}

		// set properties that should be overriden
		
		// 
		global $businesssite_instance;
		$contentmodel = $businesssite_instance->getcontentmodel();
		$taxonomy = "nxs_phone";
		$abstractpostid = $contentmodel[$taxonomy]["taxonomy"]["postid"];
		
		$phonenumber = get_post_meta($abstractpostid, "nxs_entity_phonenumber", true);
		
		if ($phonenumber == "")
		{
			$phonenumber = "0800test";
		}
		
		$args["title"] = nxs_gettitle_for_postid($abstractpostid);
		$args["text"] = get_post_field('post_content', $abstractpostid);
		$args["button_text"] = $phonenumber;
		$args["destination_url"] = "tel://{$phonenumber}";
		
		
		
		// delegate rendering of this widget to the extended /parent/ widget
		$renderresult = nxs_widgets_phone_getrenderresultofextendedwidget($args);		
		echo $renderresult["html"];
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
function nxs_widgets_phone_initplaceholderdata($args)
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	$result = nxs_widgets_initplaceholderdatageneric($args, $widgetname);
	return $result;
}
