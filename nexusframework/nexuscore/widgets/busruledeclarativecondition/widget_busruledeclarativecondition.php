<?php

function nxs_widgets_busruledeclarativecondition_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-signpost";
}

// Setting the widget title
function nxs_widgets_busruledeclarativecondition_gettitle() 
{
	return nxs_l18n__("Declarative Condition", "nxs_td");
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_busruledeclarativecondition_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_busruledeclarativecondition_gettitle(),
		"sheeticonid" => nxs_widgets_busruledeclarativecondition_geticonid(),
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
				"id" 					=> "conditionallookups",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Conditional lookup table (used to evaluate the condition)", "nxs_td"),
			),
			array
			(		
				"id" 				=> "condition",		
				"type" 				=> "input",		
				"label" 			=> nxs_l18n__("Condition", "nxs_td"),		
			),			
			array
			(
        "id" 				=> "wrapper_items_end",
        "type" 				=> "wrapperend",
      ),
		)
	);
	
	$moreoptions = nxs_busrules_getgenericoptions($args);
	// optionally strip items here
	
	$options["fields"] = array_merge($options["fields"], $moreoptions["fields"]);
	
	return $options;
}

/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_busruledeclarativecondition_render_webpart_render_htmlvisualization($args) 
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
		$output = "Declarative Condition";
		$filteritemshtml = $output;
		nxs_widgets_busrule_pagetemplate_renderrow(nxs_widgets_busruledeclarativecondition_geticonid(), $filteritemshtml, $mixedattributes);
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

function nxs_widgets_busruledeclarativecondition_initplaceholderdata($args)
{
	extract($args);

	$args["flow_stopruleprocessingonmatch"] = "true";
	// add more initialization here if needed ...
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_busrule_busruledeclarativecondition_process($args, &$statebag)
{
	$result = array();
	$result["result"] = "OK";

	$mixedattributes = $args["metadata"];
	
	//echo "evaluating;";
	//var_dump($mixedattributes);
	
	// Translate model magical fields
	if (true)
	{
		global $nxs_g_modelmanager;
		
		$combined_lookups = array();
		$combined_lookups = array_merge($combined_lookups, nxs_parse_keyvalues($mixedattributes["conditionallookups"]));
		$combined_lookups = nxs_lookups_evaluate_linebyline($combined_lookups);
		
		/*
		if (!nxs_iswebmethodinvocation())
		{
			var_dump($combined_lookups);
			die();
		}
		*/		
		
		

		/*
		if (!nxs_iswebmethodinvocation())
		{
			var_dump($mixedattributes);
			die();
		}
		*/
		
		
	
		// replace values in mixedattributes with the lookup dictionary
		$magicfields = array("condition");
		$translateargs = array
		(
			"lookup" => $combined_lookups,
			"items" => $mixedattributes,
			"fields" => $magicfields,
		);
		$mixedattributes = nxs_filter_translate_v2($translateargs);
		
		$evaluation = $mixedattributes["condition"];
		
		//var_dump(is_singular());
		//var_dump(do_shortcode("[nxs_bool ops=is_singular]"));
		//var_dump($evaluation);
		
		//global $wp_query;
		//var_dump($wp_query);
		//die();
		
	}
	
	$metadata = $mixedattributes;
	
	//echo "to;<br /><br />";
	//var_dump($mixedattributes);
	//die();
	

	if ($evaluation == "true")
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
	
	if ($result["ismatch"] === "true")
	{
		global $wp_query;
		$wp_query->is_404 = false;
		
		global $nxs_gl_ptr;
		$nxs_gl_ptr["is_404"] = "false";
		
		$nxs_action_rendercontent = $metadata["nxs_action_rendercontent"];
		if ($nxs_action_rendercontent != "")
		{
			add_action('nxs_action_rendercontent', $nxs_action_rendercontent);
		}
		
		$the_content = $metadata["the_content"];
		if ($the_content != "")
		{
			add_filter('the_content', $the_content);
		}
		
		$the_content = $metadata["the_content"];
		if ($the_content != "")
		{
			add_filter('the_content', $the_content);
		}
		
		$addnewrowoption = $metadata["addnewrowoption"];
		if ($addnewrowoption != "")
		{
			add_filter('nxs_f_shouldrenderaddnewrowoption', $addnewrowoption);
		}
		
		$renderdelegatedcontent = $metadata["renderdelegatedcontent"];
		if ($renderdelegatedcontent != "")
		{
			add_filter('nxs_f_renderdelegatedcontent', $renderdelegatedcontent);
		}
		
		// allow the plugin to add or tune specific lookups when needed
		$lookups_filter = $metadata["lookups_filter"];
		if ($lookups_filter != "")
		{
			add_filter('nxs_f_lookups', $lookups_filter);
		}
	}
	
	//var_dump($result);
	//die();
	
	return $result;
}