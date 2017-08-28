<?php

function nxs_widgets_busruleurl_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-earth";
}

// Setting the widget title
function nxs_widgets_busruleurl_gettitle() 
{
	return nxs_l18n__("URL", "nxs_td");
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_busruleurl_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_busruleurl_gettitle(),
		"sheeticonid" => nxs_widgets_busruleurl_geticonid(),
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
				"id" 				=> "operator",		
				"type" 				=> "select",		
				"label" 			=> nxs_l18n__("Operator", "nxs_td"),		
				"dropdown" 			=> array(		
					"contains"	=>"contains",		
					"template"	=>"template",
					"path"	=>"path",
				),		
			),			
			array
			(		
				"id" 				=> "p1",		
				"type" 				=> "input",		
				"label" 			=> nxs_l18n__("Parameter 1", "nxs_td"),		
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

function nxs_widgets_busruleurl_render_webpart_render_htmlvisualization($args) 
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
		$output = "URL {$operator} {$p1}";
		$filteritemshtml = $output;
		nxs_widgets_busrule_pagetemplate_renderrow(nxs_widgets_busruleurl_geticonid(), $filteritemshtml, $mixedattributes);
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

function nxs_widgets_busruleurl_initplaceholderdata($args)
{
	extract($args);

	$args["flow_stopruleprocessingonmatch"] = "true";
	// add more initialization here if needed ...
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_busrule_busruleurl_process($args, &$statebag)
{
	global $nxs_gl_isevaluatingreferencedmodels;
	if ($nxs_gl_isevaluatingreferencedmodels === true)
	{
		//echo "endless loop?";
		//nxs_dumpstacktrace();		
		//die();
	}
	
	$result = array();
	$result["result"] = "OK";

	$metadata = $args["metadata"];
	
	$currenturl = nxs_geturlcurrentpage();
	
	$operator = $metadata["operator"];
	$p1 = $metadata["p1"];

	if ($operator == "contains" && nxs_stringcontains($currenturl, $p1))
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
	else if ($operator == "template")
	{
		$isconditionvalid = true;
		
		// check condition
		if (true)
		{
			$template = $p1;														// for example "/detail/*-{{name@model}}/"
			$template = trim($template, "/");						// for example "detail/*-{{name@model}}"
			$templatepieces = explode("/", $template);	// for example ["detail", "*-{{name@model}}"]
			$cnttemplatepieces = count($templatepieces);
			
			$uriargs = array
			(
				"rewritewebmethods" => true,
			);
			$uri = nxs_geturicurrentpage($uriargs);			// for example "/detail/very-nice-1/?page=2"
			$uripieces = explode("?", $uri);
			$uri = $uripieces[0];												// for example "/detail/very-nice-1/"
			$uri = trim($uri, "/");											// for example "detail/very-nice-1"
			$uripieces = explode("/", $uri);						// for example ["detail", "very-nice-1"]
			$cnturipieces = count($uripieces);
			
			if ($cnttemplatepieces == $cnturipieces)
			{
				// its valid, until we conclude one piece is not valid
				$isconditionvalid = true;
				
				$derivedurlfragmentkeyvalues = "";
				$url_fragment_variables = array();
				
				// possible match
				for ($fragmentindex = 0; $fragmentindex < $cnturipieces; $fragmentindex++)
				{
					$uripiece = $uripieces[$fragmentindex];
					$templatepiece = $templatepieces[$fragmentindex];
					
					$containsvariable = false;
					if (nxs_stringcontains_v2($templatepiece, "{", false))
					{
						$containsvariable = true;
					}
					
					if ($containsvariable)
					{
						$startswithvariable = nxs_stringstartswith($templatepiece, "{");
						$endswithvariable = nxs_stringendswith($templatepiece, "}");
						if ($startswithvariable && $endswithvariable)
						{
							// wildcard for complete fragment,
							// for example "/detail/{{name@model}}/"
							
							$humanid = $uripiece;
							
							$conditionschema = $templatepiece;													// {{name@model}}
							$conditionschema = str_replace("{", "", $conditionschema);	// name@model}}
							$conditionschema = str_replace("}", "", $conditionschema);	// name@model
								
							// if the conditionschema has a "@"
							// we have to use the first part as the variable
							// and the 2nd part indicated the true modelschema
							// we should in that case only accept the URL
							// if the humanid exists in that schema
							$representsmodellookup = nxs_stringcontains($conditionschema, "@");
							if ($representsmodellookup)
							{
								$conditionschemapieces = explode("@", $conditionschema);
								$conditionschema = $conditionschemapieces[0];
								$modelschema = $conditionschemapieces[1];
								$toverify = "{$humanid}@{$modelschema}";
								
								// check if such model exists
								global $nxs_g_modelmanager;								
								$verified = $nxs_g_modelmanager->getmodel($toverify);
								if ($verified === false)
								{
									// error_log("model $toverify doesn't exist, it should result in a 404!");	
									$currententryvalid = false;
									break;
								}
							}
							else
							{
								// its "just" a variable, not a model lookup
							}
							
							// for example "grab-after-{X}" then conditionschema be "X"
							$derivedurlfragmentkeyvalues .= "{$conditionschema}={$humanid}\r\n";
							$url_fragment_variables[$conditionschema] = $humanid;
							
							// ok, proceed
						}
						else if ($endswithvariable)
						{
							// wildcard / model lookup check, which should/will set a variable,
							// for example "/detail/*-{{name@model}}/"
							
							$currentslugpiece = $uripiece;
							// for example the following;
							// "-grab-after-{{X}}" 
							// "*-grab-after-{{X}}"
							// would be a match for "hello-world-grab-after-{{X}}" (X would then be "p13")
							$value = $templatepiece;
							
							$seperator = $value;
							$seperator = str_replace("*", "", $seperator);
							$seperator = str_replace("{{", "(", $seperator);
							$seperator = str_replace("}}", ")", $seperator);
							$seperator = str_replace("{", "(", $seperator);
							$seperator = str_replace("}", ")", $seperator);
							// for example "-grab-after-(X)"
							$seperator = preg_replace("/\([^)]+\)/","",$seperator);
							// for example "-grab-after-"
							
							$slugsubpieces = explode($seperator, $currentslugpiece);
							// for example ("hello-world", "p13")
							
							$humanid = end($slugsubpieces);
							if ($humanid != "")
							{
								$schematemp = $value;																// -{{X}}
								$schematemp = str_replace("{{", "|", $schematemp);	// -|X}}
								$schematemp = str_replace("{", "|", $schematemp);		// -|X}}
								$schematemp = str_replace("}}", "", $schematemp);		// -|X
								$schematemp = str_replace("}", "", $schematemp);		// -|X
								$schematemppieces = explode("|", $schematemp);			// ["-", "X"]
								$conditionschema = $schematemppieces[1];
								
								// if the conditionschema has a "@"
								// we have to use the first part as the variable
								// and the 2nd part indicated the true modelschema
								// we should in that case only accept the URL
								// if the humanid exists in that schema
								$representsmodellookup = nxs_stringcontains($conditionschema, "@");
								if ($representsmodellookup)
								{
									$conditionschemapieces = explode("@", $conditionschema);
									$conditionschema = $conditionschemapieces[0];
									$modelschema = $conditionschemapieces[1];
									$toverify = "{$humanid}@{$modelschema}";
									
									// check if such model exists
									global $nxs_g_modelmanager;
									$verified = $nxs_g_modelmanager->getmodel($toverify);
									if ($verified === false)
									{
										// error_log("model $toverify doesn't exist, it should result in a 404! (b)");	
										$currententryvalid = false;
										$isconditionvalid = false;
										
										break;
									}
								}
								else
								{
									// its "just" a variable, not a model lookup
								}
								
								// for example "grab-after-{X}" then conditionschema be "X"
								$derivedurlfragmentkeyvalues .= "{$conditionschema}={$humanid}\r\n";
								$url_fragment_variables[$conditionschema] = $humanid;
								
								// ok, proceed
							}
							else
							{
								$currententryvalid = false;
								break;
							}
						}
						else
						{
							// format is not (yet) supported
							$currententryvalid = false;
							break;
						}
					}
					else
					{
						if ($templatepiece === "*")
						{
							// ignore this fraction (a match)
						}
						else if ($templatepiece === $uripiece)
						{
							// yes its identical, continue to the next fragment
						}
						else
						{
							// fragment mismatch; break the loop!
							$isconditionvalid = false;
							break;
						}
					}
				}
			}
			else
			{
				// mismatch
				$isconditionvalid = false;
			}
		}
		
		if ($isconditionvalid && $currententryvalid === false)
		{
			$isconditionvalid = false;
		}
		
		if ($isconditionvalid)
		{
			// yes, unless one of the fragments is a mismatch
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

			// the following is UNIQUE for this specific rule;
			// also add the url fragment keyvalues as derived from the url
			// NOTE; its very important to add the derivedurlfragmentkeyvalues
			// to the templaterules_lookups PRIOR to adding the templaterules_lookups
			// as likely the templaterules_lookups use the variable. If this is done
			// in the wrong order, its likely that modelproperty shortcodes
			// will try to fetch a model with an unreplaced variable, resulting in empty
			// values.
			$statebag["out"]["templaterules_lookups"] .= "\r\n" . trim($derivedurlfragmentkeyvalues);
			$statebag["out"]["url_fragment_variables"] = $url_fragment_variables;


			
			// concatenate the modeluris and modelmapping (do NOT yet evaluate them; this happens in stage 2, see #43856394587)
			$statebag["out"]["templaterules_modeluris"] .= "\r\n" . $metadata["templaterules_modeluris"];
			$statebag["out"]["templaterules_lookups"] .= "\r\n" . trim($metadata["templaterules_lookups"]);
			
			
			// instruct rule engine to stop further processing if configured to do so (=default)
			$flow_stopruleprocessingonmatch = $metadata["flow_stopruleprocessingonmatch"];
			if ($flow_stopruleprocessingonmatch != "")
			{
				$result["stopruleprocessingonmatch"] = "true";
			}
		}
	}
	else if ($operator == "path")
	{
		global $nxs_g_modelmanager;
				
		// for example "{{name@model}}" will match if a name@model exists
		
		$pieces = parse_url($currenturl);
		$path = $pieces["path"];	// "//www.example.com/path/bar/?googleguy=googley" would return "/path/bar/"
		$path = trim($path, "/");	// "/path/bar" would become "path/bar"
		$path = "/" . $path. "/";	// "path/bar" would become "/path/bar/"
		$humanid = $path;
		$humanid = $nxs_g_modelmanager->getnormalizedhumanmodelidentification($humanid);
		
		$p1 = str_replace("{", "", $p1);	// {{key@model}}
		$p1 = str_replace("{", "", $p1);	// key@model}}
		$p1 = str_replace("}", "", $p1);	// key@model
		$pieces = explode("@", $p1);
		$lookupkey = $pieces[0];
		$modelschema = $pieces[1];
		
		$modeluri = "{$humanid}@{$modelschema}";
		
		// check if the modeluri exists
		if ($nxs_g_modelmanager->ismodelfoundincache($modeluri))
		{
			// ok, its there; the condition is valid apparently
			// error_log("busrule; yes, match; $modeluri");
			
			// yes, unless one of the fragments is a mismatch
			$result["ismatch"] = "true";
			
			$derivedurlfragmentkeyvalues = "{$lookupkey}={$humanid}\r\n";
			$url_fragment_variables[$lookupkey] = $humanid;
			
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

			// the following is UNIQUE for this specific rule;
			// also add the url fragment keyvalues as derived from the url
			// NOTE; its very important to add the derivedurlfragmentkeyvalues
			// to the templaterules_lookups PRIOR to adding the templaterules_lookups
			// as likely the templaterules_lookups use the variable. If this is done
			// in the wrong order, its likely that modelproperty shortcodes
			// will try to fetch a model with an unreplaced variable, resulting in empty
			// values.
			$statebag["out"]["templaterules_lookups"] .= "\r\n" . trim($derivedurlfragmentkeyvalues);
			$statebag["out"]["url_fragment_variables"] = $url_fragment_variables;
			
			// concatenate the modeluris and modelmapping (do NOT yet evaluate them; this happens in stage 2, see #43856394587)
			$statebag["out"]["templaterules_modeluris"] .= "\r\n" . $metadata["templaterules_modeluris"];
			$statebag["out"]["templaterules_lookups"] .= "\r\n" . trim($metadata["templaterules_lookups"]);
			
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
	}
	else
	{
		$result["ismatch"] = "false";
	}
	
	if ($result["ismatch"] === "true")
	{
		global $wp_query;
		$wp_query->is_404 = false;
		
		if ($metadata["the_content_invoke"] != "")
		{
			add_action('nxs_action_rendercontent', $metadata["the_content_invoke"]);
		}
	}
	
	return $result;
}