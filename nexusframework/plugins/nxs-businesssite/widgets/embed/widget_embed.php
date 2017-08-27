<?php

function nxs_widgets_embed_geticonid() 
{
	//$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-puzzle";
}

// Setting the widget title
function nxs_widgets_embed_gettitle() 
{
	return nxs_l18n__("Embed", "nxs_td");
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
		$postid = $clientpopupsessioncontext["postid"];
		$placeholderid = $clientpopupsessioncontext["placeholderid"];
		
		// load the widget's data from the persisted db
		$placeholdermetadata = nxs_getwidgetmetadata($postid, $placeholderid);
		$embeddabletypemodeluri = $placeholdermetadata["embeddabletypemodeluri"];
		
		// but allow it to be overriden in the session
		if (isset($clientpopupsessiondata["embeddabletypemodeluri"]))
		{
			$embeddabletypemodeluri = $clientpopupsessiondata["embeddabletypemodeluri"];
		}
		
		if ($embeddabletypemodeluri == "")
		{
			$iterator_datasources = array("nxs.embeddables.public");
			
			// allow plugins to extend or tune the datasources
			$filterargs = array();
			$iterator_datasources = apply_filters("nxs_f_embed_datasources", $iterator_datasources, $filterargs);
			
			global $nxs_g_modelmanager;

			$custompicker = "";
			$custompicker .= "<style>";
			$custompicker .= ".widgetgroup { margin: 5px; padding: 5px; background-color: #EEE; border: 1px solid #ccc; }";
			$custompicker .= ".widgetgroupheader { margin: 2px; padding-bottom: 20px; }";
			$custompicker .= "</style>";
			
			$custompicker .= "<div>";
			
			foreach ($iterator_datasources as $iterator_datasource)
			{
				$custompicker .= "<div class='widgetgroup'>";
				
				
				$refresh_url = nxs_geturlcurrentpage();
				$refresh_url = nxs_addqueryparametertourl_v2($refresh_url,"bulkmodels", "true", true, true);
				$refresh_url = nxs_addqueryparametertourl_v2($refresh_url,"singularschema", $iterator_datasource, true, true);
				
				$clearcache_html = "<a target='_blank' href='{$refresh_url}'>Clear cache</a><br />";

				
				$custompicker .= "<div class='widgetgroupheader'>Widget Group - {$iterator_datasource} {$clearcache_html}</div>";
				
				$iteratormodeluri = "singleton@listof{$iterator_datasource}";
				$contentmodel = $nxs_g_modelmanager->getcontentmodel($iteratormodeluri);
				$instances = $contentmodel[$iterator_datasource]["instances"];
				
				$custompicker .= "<div style='display: flex;'>";
				
				foreach ($instances as $instance)
				{
					$itemhumanmodelid = $instance["content"]["humanmodelid"];
					$itemuri = "{$itemhumanmodelid}@${iterator_datasource}";
					$itemtitle = $nxs_g_modelmanager->getcontentmodelproperty($itemuri, "title");
					$itemicon = $nxs_g_modelmanager->getcontentmodelproperty($itemuri, "icon");
					
					$abbreviatedtitle = $itemtitle;
										
					$breakuplength = 12;
					if (strlen($abbreviatedtitle) > $breakuplength)
					{
						if (!nxs_stringcontains($abbreviatedtitle, " "))
						{
							// te lang...
							$abbreviatedtitle = substr($abbreviatedtitle, 0, $breakuplength - 1) . "-" . substr($abbreviatedtitle, $breakuplength - 1);
						}
					}
					
					$maxlength = 14;
					if (strlen($abbreviatedtitle) > $maxlength)
					{
						// chop!
						$abbreviatedtitle = substr($abbreviatedtitle, 0, $maxlength - 1) . "..";
					}
					
					$custompicker .= "<div class='item' style='border-color: #ddd; border-style: solid; border-width: 1px; margin: 3px; padding: 3px;'>";
					$custompicker .= "<a href='#' onclick='nxs_js_popup_setsessiondata(\"embeddabletypemodeluri\", \"{$itemuri}\"); nxs_js_popup_sessiondata_make_dirty(); nxs_js_popup_refresh(); return false;'>";
					$custompicker .= "<div>";
					$custompicker .= "<span title='{$itemtitle}' id='placeholdertemplate_<?php echo $widgetid; ?>' class='nxs-widget-icon {$itemicon}'></span>";
					$custompicker .= "<p title='{$itemtitle}' style='text-align:center'>{$abbreviatedtitle}</p>";
					$custompicker .= "</div>";
					$custompicker .= "</a>";
					$custompicker .= "</div>";
				}
				
				$custompicker .= "</div>";
				
				
				$custompicker .= "</div>";
			}
			
			//
			
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
				
			);
			
			// this should be a read only / hidden field,
			// and there should be another custom field populated with content defined by the content provider
			
			$sheettitle = nxs_l18n__("Select a widget", "nxs_td");
			$sheeticon = "nxs-icon-puzzle";
		}
		else
		{
			// load the selected embeddabletypemodeluri from the contentprovider
			global $nxs_g_modelmanager;
			$sheettitle = $nxs_g_modelmanager->getcontentmodelproperty($embeddabletypemodeluri, "title");
			$sheeticon = $nxs_g_modelmanager->getcontentmodelproperty($embeddabletypemodeluri, "icon");
			
			// 
			$fieldsjsonstring = $nxs_g_modelmanager->getcontentmodelproperty($embeddabletypemodeluri, "fields");
			$specificfields = json_decode($fieldsjsonstring, true);
			// todo: add an option to switch embeddabletypemodeluri ?
			
			$lookupfields = array
			(
				// -------------------------------------------------------			
				
				// LOOKUPS
				
				array
				( 
					"id" 				=> "wrapper_title_begin",
					"type" 				=> "wrapperbegin",
					"label" 			=> nxs_l18n__("Lookups", "nxs_td"),
					"initial_toggle_state" => "closed",
				),
				array
	      (
					"id" 					=> "lookups",
					"type" 				=> "textarea",
					"label" 			=> nxs_l18n__("Lookup table (evaluated one time when the widget renders)", "nxs_td"),
				),
				array( 
					"id" 				=> "wrapper_title_end",
					"type" 				=> "wrapperend"
				),
			);
			
			$propertiesheaderfields = array
			(
				array
				( 
					"id" 				=> "wrapper_title_begin",
					"type" 				=> "wrapperbegin",
					"label" 			=> nxs_l18n__("Properties", "nxs_td"),
				),
			);
			
			$propertiesfooterfields = array
			(
				array( 
					"id" 				=> "wrapper_title_end",
					"type" 				=> "wrapperend"
				),
			);
			$refresh_url = nxs_geturlcurrentpage();
			$refresh_url = nxs_addqueryparametertourl_v2($refresh_url, "embed_transients", "refresh", true, true);
			
			$propertiesrefreshcachefields = array
			(
				array
				( 
					"id" 				=> "wrapper_cache_begin",
					"type" 				=> "wrapperbegin",
					"label" 			=> nxs_l18n__("Cache", "nxs_td"),
				),
				array
				( 
					"id" 				=> "cache_custom",
					"type" 				=> "custom",
					"customcontent" => "<button onclick='location.href=\"{$refresh_url}\"; return false;'>Refresh (clear cache)</button>",
				),
				array( 
					"id" 				=> "wrapper_cache_end",
					"type" 				=> "wrapperend"
				),
			);
			
			$fields = array();	
			$fields = array_merge($fields, $lookupfields);
			$fields = array_merge($fields, $propertiesheaderfields);
			$fields = array_merge($fields, $specificfields);
			$fields = array_merge($fields, $propertiesfooterfields);
			$fields = array_merge($fields, $propertiesrefreshcachefields);
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
	//error_log("render embed");
	
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
	
	unset($mixedattributes["id"]);
	
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
	

	// OUTPUT
	// ---------------------------------------------------------------------------------------------------- 
	
	if ($_REQUEST["customhtml"] == "off" && is_user_logged_in())
	{
		//
		nxs_renderplaceholderwarning("embed widget");
	}
	else if ($alternativemessage != "" && $alternativemessage != null)
	{
		nxs_renderplaceholderwarning($alternativemessage);
	} 
	else 
	{
		//
		global $nxs_g_modelmanager;
		$templateurl = $nxs_g_modelmanager->getcontentmodelproperty($embeddabletypemodeluri, "templateurl");
		$fieldsjsonstring = $nxs_g_modelmanager->getcontentmodelproperty($embeddabletypemodeluri, "fields");
		$fields = json_decode($fieldsjsonstring, true);
	
		$args = $temp_array;
		//  override the following parameter
		$args["postid"] = $containerpostid;
		$args["placeholderid"] = $placeholderid;
		$args["placeholdertemplate"] = "embed";
		
		$url = $templateurl;
		$url = nxs_addqueryparametertourl_v2($url, "frontendframework", "alt", true, true);
		//$url = nxs_addqueryparametertourl_v2($url, "frontendframework", "nxs2", true, true);
		$url = nxs_addqueryparametertourl_v2($url, "nxs_triggeredby", "embedwidget", true, true);
		// add query parameters based upon the lookup tables of the widget (options)
		
		$combined_lookups = nxs_lookups_getcombinedlookups_for_currenturl();
		$combined_lookups = array_merge($combined_lookups, nxs_parse_keyvalues($lookups));
		
		// evaluate the thelookup values line by line
		$sofar = array();
		foreach ($combined_lookups as $key => $val)
		{
			$sofar[$key] = $val;
			//echo "step 1; processing $key=$val sofar=".json_encode($sofar)."<br />";

			//echo "step 2; about to evaluate lookup tables on; $val<br />";
			// apply the lookup values
			$sofar = nxs_lookups_blendlookupstoitselfrecursively($sofar);

			// apply shortcodes
			$val = $sofar[$key];
			//echo "step 3; result is $val<br />";

			//echo "step 4; about to evaluate shortcode on; $val<br />";

			$val = do_shortcode($val);
			$sofar[$key] = $val;

			//echo "step 5; $key evaluates to $val (after applying shortcodes)<br /><br />";

			$combined_lookups[$key] = $val;
		}
		
		foreach ($fields as $field => $fieldmeta)
		{
			$id = $fieldmeta["id"];
			$value = $$id;
			
			// it could be that the value contains a lookup placeholder; replace those
			if (nxs_stringcontains($value, "{"))
			{
				
				
				//error_log("thelookup:" . json_encode($combined_lookups));
				//error_log("value before:" . $value);
								
				// interpret the iterator_datasource by applying the lookup tables from the pagetemplate_rules
				$translateargs = array
				(
					"lookup" => $combined_lookups,
					"item" => $value,
				);
				$value = nxs_filter_translate_v2($translateargs);

				//error_log("value after:" . $value);
			}
				
			$url = nxs_addqueryparametertourl_v2($url, $id, $value, true, true);
		}
		
		$prefix = "embed_tr_";
		// todo:  cacheduration should be specified/determined by the model
		$cacheduration = 0; // 60 * 60 * 24 * 30; // 30 days cache // forever
		
		do_action("nxs_a_usetransients", array("prefix" => $prefix, "title" => "Embed widget", "cacheduration" => $cacheduration));
		
		if ($_REQUEST["embed_transients"] == "refresh")
		{
			$isallowed = false;
			
			if (is_user_logged_in())
			{
				$isallowed = true;
			}
			
			if ($isallowed)
			{
				nxs_cache_cleartransients($prefix);
				//echo "cache wiped";
				//die();
			}
		}
		
		$transientkey = $prefix . md5("{$url}");
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
			error_log("embed fetching content for $url");
			$content = nxs_geturlcontents(array("url" => $url));
			
			
			
			// update cache
			set_transient($transientkey, $content, $cacheduration);
		}
		
		

		if ($_REQUEST["debugembed"] == "true" && is_user_logged_in())
		{
			echo $url;
			echo "<br />";
			echo "<br />";
			var_dump($content);
			die();
		}
		
		// apply shortcodes (used in for example the google maps widget)
		// this has to be done prior to changing the clases (see below)
		$content = do_shortcode($content);
		
		// tune the output (should be done by the content platform)
		
		$content = str_replace("nxs-sitewide-element", "template-sitewide-element", $content);
		$content = str_replace("nxs-content-container", "template-content-container", $content);
		$content = str_replace("nxs-article-container", "template-article-container", $content);
		$content = str_replace("nxs-postrows", "template-postrows", $content);
		$content = str_replace("nxs-row", "template-row", $content);
		$content = str_replace("nxs-placeholder-list", "template-placeholder-list", $content);
		$content = str_replace("ABC", "template-ABC", $content);
		$content = str_replace("XYZ", "template-XYZ", $content);
		$content = str_replace("nxs-widget-", "template-widget-", $content);
		$content = str_replace("nxs-widget", "template-widget", $content);
		
		$content = str_replace("nxs-placeholder", "template-placeholder", $content);
		
		$content = str_replace("nxs-containsimmediatehovermenu", "template-containsimmediatehovermenu", $content);
		$content = str_replace("has-no-sidebar", "template-has-no-sidebar", $content);
		$content = str_replace("nxs-elements-container", "template-XYZ", $content);
		$content = str_replace("nxs-runtime-autocellsize", "template-runtime-autocellsize", $content);
		
		echo "<style>";
		echo "@media (max-width: 400px) {  .template-placeholder-list { display: block; }}";
		echo ".template-placeholder-list { display: flex; justify-content: space-between; }";
		echo ".template-placeholder { display:flex; list-style: none; margin-bottom: 30px; }";
		echo ".template-placeholder.nxs-margin-bottom-0-0 { margin-bottom: 0px; }";
		echo ".template-ABC { width: 100%; }";
		// echo ".template-XYZ { width: 100%; }";
		echo ".template-sitewide-element { margin: 0 !important; padding: 0 !important; }";
		
		
		// echo ".template-ABC.nxs-height100 { height: 100% !important; }";
		echo ".template-placeholder-list .nxs-one-whole { width: 100% !important; border-right: 0px !important; }";
		echo "</style>";
		
		echo $content;
		
		if ($_REQUEST["embed"] == "where")
		{
			echo "right here";
		}
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