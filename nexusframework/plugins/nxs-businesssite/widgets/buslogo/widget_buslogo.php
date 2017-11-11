<?php

function nxs_widgets_buslogo_geticonid() 
{
	//$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-logo";
}

// Setting the widget title
function nxs_widgets_buslogo_gettitle() {
	return nxs_l18n__("Business Logo", "nxs_td");
}

// obsolete
function nxs_widgets_buslogo_getunifiedstylingprefix()
{
	$result = "buslogo";
	return $result;
}

function nxs_widgets_buslogo_getunifiedstylinggroup()
{
	$result = "textwidget";
	return $result;
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_buslogo_home_getfieldstoadd()
{
	$result = array
	(
	);
	return $result;
}

function nxs_widgets_buslogo_getwidgettoextend()
{
	return "logo";
}

function nxs_widgets_buslogo_getoptionsofextendedwidget($p)
{
	// ensure widget is loaded
	nxs_requirewidget(nxs_widgets_buslogo_getwidgettoextend());

	$functionnametoinvoke = 'nxs_widgets_' . nxs_widgets_buslogo_getwidgettoextend() . '_home_getoptions';
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

function nxs_widgets_buslogo_getrenderresultofextendedwidget($p)
{
	// ensure widget is loaded
	nxs_requirewidget(nxs_widgets_buslogo_getwidgettoextend());

	$functionnametoinvoke = 'nxs_widgets_' . nxs_widgets_buslogo_getwidgettoextend() . '_render_webpart_render_htmlvisualization';
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

function nxs_widgets_buslogo_home_getfieldidstoremove()
{
	$result = array();
	
	return $result;
}

// Define the properties of this widget
function nxs_widgets_buslogo_home_getoptions($args) 
{
	// we wrappen naar de fields van de options van de enkelvoudige widget
	nxs_requirewidget(nxs_widgets_buslogo_getwidgettoextend());
	
	$optionsparentwidget = nxs_widgets_buslogo_getoptionsofextendedwidget($args);
	$fieldsparentwidget = $optionsparentwidget["fields"];
	
	global $nxs_g_modelmanager;
	$contentmodel = $nxs_g_modelmanager->getcontentmodel();

	$taxonomy = "nxs_companyname";
	$abstractpostid = $contentmodel[$taxonomy]["taxonomy"]["postid"];	
	$companynameediturl = get_edit_post_link($abstractpostid);
	
	$taxonomy = "nxs_slogan";
	$abstractpostid = $contentmodel[$taxonomy]["taxonomy"]["postid"];	
	$sloganediturl = get_edit_post_link($abstractpostid);
	
	$fields = array
	(
		array
		( 
			"id" 				=> "wrapper_input_begin",
			"type" 				=> "wrapperbegin",
			"label" 			=> nxs_l18n__("buslogo", "nxs_td"),
		),

		//
		array
    (
			"id" 					=> "content_custom",
			"type" 				=> "custom",
			"custom"	=> "<div><a class='nxsbutton' href='{$companynameediturl}'>Edit company name</a></div><div><a class='nxsbutton' href='{$sloganediturl}'>Edit slogan</a></div>",
		),		
		
		array
		( 
			"id" 				=> "wrapper_input_end",
			"type" 				=> "wrapperend"
		),		
	);
	
	// add more fields here, if needed
	$fields = array_merge($fields, $fieldsparentwidget);
	$fields = array_merge($fields, nxs_widgets_buslogo_home_getfieldstoadd());
	
	$fieldids_to_remove = nxs_widgets_buslogo_home_getfieldidstoremove();
	
	// remove fields here, if needed
	$fieldsparentwidget;
	$index = 0;
	$indextoberemoved = array();
	foreach ($fields as $currentfield)
	{
		if ($currentfield == null)
		{
			continue;
		}
		
		//
		if (in_array($currentfield["id"], $fieldids_to_remove))
		{
			$indextoberemoved[] = $index;
		}
	}
	// also get rid of all content-fields
	foreach ($fields as $currentfield)
	{
		if ($currentfield == null)
		{
			continue;
		}
		
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
		"sheettitle" => nxs_widgets_buslogo_gettitle(),
		"sheeticonid" => nxs_widgets_buslogo_geticonid(),
		
		"unifiedstyling" => array
		(
			// obsolete
			"prefix" => nxs_widgets_buslogo_getunifiedstylingprefix(),
			"group" => nxs_widgets_buslogo_getunifiedstylinggroup(),
		),		
			
		"fields" => $fields,
	);
	
	return $options;
}


/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_buslogo_render_webpart_render_htmlvisualization($args) 
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
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-buslogo ";
	
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
		$args["placeholdertemplate"] = nxs_widgets_buslogo_getwidgettoextend();

		// apply unistyle for this specific widget (not the extended /parent/ one)
		$unistyle = $temp_array["unistyle"];
		if (isset($unistyle) && $unistyle != "")
		{
			// blend unistyle properties
			$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_buslogo_getunifiedstylinggroup(), $unistyle);
			$args = array_merge($args, $unistyleproperties);	
		}
		
		// 
		global $nxs_g_modelmanager;
		$contentmodel = $nxs_g_modelmanager->getcontentmodel();
		$taxonomy = "nxs_companyname";
		//$abstractpostid = $contentmodel[$taxonomy]["taxonomy"]["postid"];
		//$name = get_post_meta($abstractpostid, "nxs_entity_name", true);
		$name = $contentmodel[$taxonomy]["taxonomy"]["name"];
		
		if ($name == "")
		{
			$name = "company name";
		}
		
		$taxonomy = "nxs_slogan";
		//$abstractpostid = $contentmodel[$taxonomy]["taxonomy"]["postid"];
		//$slogan = get_post_meta($abstractpostid, "nxs_entity_slogan", true);
		$slogan = $contentmodel[$taxonomy]["taxonomy"]["slogan"];
		if ($slogan == "")
		{
			$slogan = "slogan";
		}
		
		// set properties that should be overriden
		$args["title"] = $name;
		$args["subtitle"] = $slogan;
		
		// delegate rendering of this widget to the extended /parent/ widget
		$renderresult = nxs_widgets_buslogo_getrenderresultofextendedwidget($args);		
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
function nxs_widgets_buslogo_initplaceholderdata($args)
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	$result = nxs_widgets_initplaceholderdatageneric($args, $widgetname);
	return $result;
}
