<?php

function nxs_widgets_embed_geticonid() 
{
	//$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-puzzle";
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

// Define the properties of this widget
function nxs_widgets_embed_home_getoptions($args) 
{
	if (nxs_iswebmethodinvocation())
	{
		$clientpopupsessioncontext = $_REQUEST["clientpopupsessioncontext"];
		$clientpopupsessiondata = $_REQUEST["clientpopupsessiondata"];
		//
		$containerpostid = $clientpopupsessioncontext["containerpostid"];
		$placeholderid = $clientpopupsessioncontext["placeholderid"];
		
		// load the widget's data from the persisted db
		$placeholdermetadata = nxs_getwidgetmetadata($containerpostid, $placeholderid);
		$embeddabletypemodeluri = $placeholdermetadata["embeddabletypemodeluri"];
		
		//echo "before:";
		//var_dump($placeholdermetadata);
		//die();
		
		// but allow it to be overriden in the session
		if (isset($clientpopupsessiondata["embeddabletypemodeluri"]))
		{
			$embeddabletypemodeluri = $clientpopupsessiondata["embeddabletypemodeluri"];
		}

		if ($embeddabletypemodeluri == "")
		{
			
			$iterator_datasource = "embeddable";
			$iteratormodeluri = "singleton@listof{$iterator_datasource}";
			
			global $nxs_g_modelmanager;
			
			$contentmodel = $nxs_g_modelmanager->getcontentmodel($iteratormodeluri);
			$instances = $contentmodel[$iterator_datasource]["instances"];
			
			$custompicker = "";
			$custompicker .= "<div>";
			foreach ($instances as $instance)
			{
				$itemhumanmodelid = $instance["content"]["humanmodelid"];
				$itemuri = "{$itemhumanmodelid}@${iterator_datasource}";
				$itemtitle = $nxs_g_modelmanager->getcontentmodelproperty($itemuri, "title");
				$custompicker .= "<a href='#' onclick='nxs_js_popup_setsessiondata(\"embeddabletypemodeluri\", \"{$itemuri}\"); nxs_js_popup_sessiondata_make_dirty(); nxs_js_popup_refresh(); return false;'>{$itemtitle}</a><br />";
			}
			$custompicker .= "</div>";
			
			// 
			$fields = array
			(	
				array
		    (
					"id" 					=> "embeddabletypemodeluri",
					"type" 				=> "input",
					"visibility" => "hidden",
					"label" 			=> nxs_l18n__("Embeddable", "nxs_td"),
				),
				array
				(
					"id" 					=> "embeddabletypemodeluripicker",
					"type" 				=> "custom",
					"label" 			=> nxs_l18n__("Embeddable", "nxs_td"),
					"custom"	=> $custompicker,
				),
				/*
				array
				(
					"id" 					=> "test",
					"type" 				=> "modelpicker",
					"label" 			=> nxs_l18n__("test", "nxs_td"),
					"iterator_datasource" => "businesstype",
					"textproperty" => "nexus_prim_bus_type",
					"valueproperty" => "nexus_prim_bus_type",
				),
				*/
			);
			
			// this should be a read only / hidden field,
			// and there should be another custom field populated with content defined by the content provider
			
			$sheettitle = "What do you want to embed?";
			$sheeticon = "nxs-icon-puzzle";
		}
		else
		{
			// load the selected embeddabletypemodeluri from the contentprovider
			global $nxs_g_modelmanager;
			$sheettitle = $nxs_g_modelmanager->getcontentmodelproperty($embeddabletypemodeluri, "title");
			$sheeticon = $nxs_g_modelmanager->getcontentmodelproperty($embeddabletypemodeluri, "icon");
			$fieldsjsonstring = $nxs_g_modelmanager->getcontentmodelproperty($embeddabletypemodeluri, "fields");
			$fields = json_decode($fieldsjsonstring, true);
			// todo: add an option to switch embeddabletypemodeluri ?
			
			$additionalfields = array
			(
				array
				( 
					"id" 				=> "lookups",
					"type" 				=> "textarea",
					"label" 			=> nxs_l18n__("Lookup values", "nxs_td"),
				),
			);
			
			$fields = array_merge($fields, $additionalfields);
		}
	}
	else
	{
		// 
	}
	
	$options = array
	(
		"sheettitle" => $sheettitle,
		"sheeticonid" => $sheeticon,
		"fields" => $fields,
		"footerfiller" => true,
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
	
	if ($render_behaviour == "code")
	{
		//
		$temp_array = array();
	}
	else
	{
		$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	}
	
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
		
	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-embed ";
	}
	
	// EXPRESSIONS
	// ---------------------------------------------------------------------------------------------------- 
	
	global $nxs_global_current_containerpostid_being_rendered;
	$containerpostid = $nxs_global_current_containerpostid_being_rendered;

	if ($embeddabletypemodeluri == "")
	{
		$alternativemessage = "Configure me please :)";
	}
	else
	{
		//
		global $nxs_g_modelmanager;
		$templateurl = $nxs_g_modelmanager->getcontentmodelproperty($embeddabletypemodeluri, "templateurl");
		$fieldsjsonstring = $nxs_g_modelmanager->getcontentmodelproperty($embeddabletypemodeluri, "fields");
		$fields = json_decode($fieldsjsonstring, true);
	}

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
		
		$url = $templateurl;
		$url = nxs_addqueryparametertourl_v2($url, "frontendframework", "alt", true, true);
		// add query parameters based upon the lookup tables of the widget (options)
		
		foreach ($fields as $field => $fieldmeta)
		{
			$id = $fieldmeta["id"];
			$value = $$id;
			
			// it could be that the value contains a lookup placeholder; replace those
			if (nxs_stringcontains($value, "{"))
			{
				$thelookup = array();
				
				$moreitems = nxs_gettemplateruleslookups();
				$thelookup = array_merge($thelookup, $moreitems);
				
				$moreitems = nxs_parse_keyvalues($lookups);				
				$thelookup = array_merge($thelookup, $moreitems);
				
				//error_log("thelookup:" . json_encode($thelookup));
				//error_log("value before:" . $value);
								
				// interpret the iterator_datasource by applying the lookup tables from the pagetemplate_rules
				$translateargs = array
				(
					"lookup" => $thelookup,
					"item" => $value,
				);
				$value = nxs_filter_translate_v2($translateargs);

				//error_log("value after:" . $value);
			}
				
			$url = nxs_addqueryparametertourl_v2($url, $id, $value, true, true);
		}
		error_log("theurl:" . $url);
		
		$transientkey = md5("embed_tr_{$url}");
		$content = get_transient($transientkey);
		$shouldrefreshdbcache = false;
		if ($shouldrefreshdbcache == false && $content == "")
		{
			$shouldrefreshdbcache = true;
		}
		if ($shouldrefreshdbcache == false && $_REQUEST["embed_transients"] == "refresh")
		{
			$shouldrefreshdbcache = true;
		}
		
		if ($shouldrefreshdbcache)
		{
			$content = file_get_contents($url);
			
			// update cache
			$cacheduration = 60 * 60 * 24 * 30; // 30 days cache
			set_transient($transientkey, $content, $cacheduration);
			
			if ($_REQUEST["debugembed"] == "true")
			{
				error_log("embed; url; $url");
			}
		}
		
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
	if ($render_behaviour == "code")
	{
		//
	}
	else
	{	
		nxs_widgets_setgenericwidgethovermenu($postid, $placeholderid, $placeholdertemplate);
	}
	

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