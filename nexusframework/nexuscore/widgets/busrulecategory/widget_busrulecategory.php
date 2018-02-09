<?php

function nxs_widgets_busrulecategory_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-categories";
}

// Setting the widget title
function nxs_widgets_busrulecategory_gettitle() {
	return nxs_l18n__("Specific category", "nxs_td");
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_busrulecategory_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_busrulecategory_gettitle(),
		"sheeticonid" => nxs_widgets_busrulecategory_geticonid(),
		"fields" => array
		(
			array( 
				"id" 				=> "wrapper_condition_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Condition", "nxs_td"),
			),
			
			array(
				"id" 				=> "filter_atleastoneof_catids",
				"type" 				=> "categories",
				"label" 			=> nxs_l18n__("Categories", "nxs_td"),
				"editable" 			=> "false",
			),	
		
			array( 
				"id" 				=> "wrapper_condition_end",
				"type" 				=> "wrapperend"
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

function nxs_widgets_busrulecategory_render_webpart_render_htmlvisualization($args) 
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
		$commaseperated = nxs_convert_stringwithbracketlist_to_stringwithcommas($filter_atleastoneof_catids);	
		if ($commaseperated == "")
		{
		}
		else
		{
			$output = wp_list_categories("hide_empty=0&include=$commaseperated&style=none&echo=0");
			//$output = str_replace("<br />", " ", $output);
			$output = str_replace("<a ", "<a target='_blank' ", $output);
			
			$count = substr_count($output, "<a");
			if ($count == 0)
			{
				$output = "<span style='background-color: red; color: white;'>Error; no categories selected</span>";
			}
		}
		
		$filteritemshtml = $output;
		nxs_widgets_busrule_pagetemplate_renderrow(nxs_widgets_busrulecategory_geticonid(), $filteritemshtml, $mixedattributes);
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

function nxs_widgets_busrulecategory_initplaceholderdata($args)
{
	extract($args);

	$args["flow_stopruleprocessingonmatch"] = "true";
	// add more initialization here if needed ...
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_getrecursivecatids_up($catid, $visited = array())
{
  $chain = '';
  $parent = get_category($catid);
	if (is_wp_error($parent))
	{
		$result = array();
		return $result;
	}
	
	if ($parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited))
	{
		// echo "ja, recursive";
		
		// recursion
		$visited[] = $parent->parent;
	  $chain .= nxs_getrecursivecatids_up($parent->parent, $visited);
	}
	else
	{
		// echo "nee, geen recursive";
	}
	
	$chain .= "[" . $parent->term_id . "]";

	//var_dump($chain);

	return $chain;
}

function nxs_busrule_busrulecategory_process($args, &$statebag)
{
	$result = array();
	$result["result"] = "OK";

	$metadata = $args["metadata"];
	
	if (is_singular())
	{
		global $wp_query;
		$p = $wp_query->posts[0];
		$postid = $p->ID;
		
		$reccatidsforpost = array();
		
		$cats = wp_get_post_categories($postid);
		foreach ($cats as $currentcatid)
		{
			// also get the recursive cat ids
			$reccatidsforcatidinbrackets = nxs_getrecursivecatids_up($currentcatid);
			
			//var_dump($reccatidsforcatidinbrackets);
			
			$reccatidsforcatidincommasep = nxs_convert_stringwithbracketlist_to_stringwithcommas($reccatidsforcatidinbrackets);
			$reccatidsforcatid = explode(',', $reccatidsforcatidincommasep);
			$reccatidsforpost = array_merge($reccatidsforpost, $reccatidsforcatid);
		}
		
		// make distinct
		$reccatidsforpost = array_unique($reccatidsforpost);
		
		if (count($reccatidsforpost) > 0)
		{
			$result["ismatch"] = "false";
			$filter_atleastoneof_catids = $metadata["filter_atleastoneof_catids"];
			
			foreach($reccatidsforpost as $currentcategoryid)
			{
				$key = "[" . $currentcategoryid . "]";
				
	  		if (nxs_stringcontains($filter_atleastoneof_catids, $key))
	  		{
	  			$result["ismatch"] = "true";
	  			
	  			// process configured site wide elements
	  			$sitewideelements = nxs_pagetemplates_getsitewideelements();
	  			foreach($sitewideelements as $currentsitewideelement)
	  			{
	  				$selectedvalue = $metadata[$currentsitewideelement];
	  				if ($selectedvalue == "")
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
					break;
	  		}
			}
		}
		else
		{
			$result["ismatch"] = "false";
		}
	}
	else
	{
		$result["ismatch"] = "false";
	}
	
	return $result;
}

?>
