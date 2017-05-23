<?php
/*
Plugin Name: Nxs Business Site
Version: 1.0.12
Plugin URI: https://github.com/TODO
Description: TODO
Author: GJ
Author URI: https://github.com/TODO/
*/

class nxs_g_modelmanager
{
	function getcontentmodeltaxonomyinstances($args)
	{
		$taxonomy = $args["taxonomy"];
		$contentmodel = $this->getcontentmodel();
		$result = $contentmodel[$taxonomy]["instances"];
		return $result;
	}
	
	// todo: move this information to a model
	function getmodelinterpreter()
	{
		// todo: add in mem caching for each request; the rules cannot and will not change
		// during one invocation
		
		$sitelookup = nxs_lookuptable_getlookup_v2(false);
		$websiteseoruleshumanid = $sitelookup["websiteseoruleshumanid"];
		// todo: apply a filter such that specific plugins can overrule the behaviour
		// instead of requiring a plugin
		if ($websiteseoruleshumanid == "")
		{
			// perhaps use some default here?
			return false;
		}
			
		// todo: derive the humanid of the model through the hostnam of the website
		$websiteseorules = $this->getcontentmodel("{$websiteseoruleshumanid}@websiteseorules");
		
		$result = array();
		
		// todo: derive this from the model too
		$result["schematowpinstancemapping"] = array
		(
			// schema => ...
			"leadservice" => array
			(
				"title_template" => "{{properties.searchphrase}}",
			),
		);
		
		$entries = $websiteseorules["entry"]["instances"];
		foreach ($entries as $entry => $entrymeta)
		{
			$extpropsjson = $entrymeta["content"]["extpropsjson"];
			$props = json_decode($extpropsjson, true);
			$name = $props["name"];
			$conditions = $props["conditions"];
			foreach ($conditions as $condition => $conditionmeta)
			{
				$conditiontype = $conditionmeta["conditiontype"];
				$operatortype = $conditionmeta["operatortype"];
				$value = $conditionmeta["value"];
				$result["entries"]["entry{$entry}"]["conditions"]["condition{$condition}"] = array
				(
					"type" => $conditiontype,
					"operator" => $operatortype,
					"value" => $value,
				);
			}
		}
		
		/*
		if (false)
		{
			// old "hardcoded" implementation
			
			$result = array
			(
				"schematowpinstancemapping" => array
				(
					// schema => ...
					"freewebsiteproduct" => array
					(
						"title_template" => "{{nxs_searchphrase.searchphrase}}",
					),
				),
				"entries" => array
				(
					"entry1freewebsiteproductbybusid" => array
					(
						"conditions" => array
						(
							"condition1" => array
							(
								"type" => "homeurl",
								"operator" => "equals",
								"value" => "http://blablabusiness.testgj.c1.us-e1.nexusthemes.com/",
							),
							"condition2" => array
							(
								"type" => "slugatindex",
								"index" => 0,
								"operator" => "equals",
								"value" => "free-willy",
							),
							"condition3" => array
							(
								"type" => "slugatindex",
								"index" => 1,
								"operator" => "betweenpreandpostfixmatchhumanmodelforschema",
								"value" => "free-|freewebsiteproduct|-website",
							),
						),
					),
					// 
					"entry2" => array
					(
						"conditions" => array
						(
							"condition1" => array
							(
								"type" => "homeurl",
								"operator" => "equals",
								"value" => "http://blablabusiness.testgj.c1.us-e1.nexusthemes.com/",
							),
							"condition2" => array
							(
								"type" => "slugatindex",
								"index" => 0,
								"operator" => "equals",
								"value" => "free-willy",
							),
							"condition3" => array
							(
								"type" => "slugatindex",
								"index" => 1,
								"operator" => "exactmatchhumanmodelforschema",
								"schema" => "freewebsiteproduct",
							),						
						),
					),
				),
			);
		}
		*/
		
		if ($_REQUEST["dumprules"] == "true")
		{
			echo json_encode($result);
			die();
		}
		
		return $result;
	}
	
	function getschematowpinstancemapping()
	{
		$sitemapping = $this->getmodelinterpreter();
		$result = $sitemapping["schematowpinstancemapping"];
		return $result;
	}
	
	function getentries()
	{
		$sitemapping = $this->getmodelinterpreter();
		$result = $sitemapping["entries"];
		return $result;
	}
	
	function evaluatereferencedmodelsinmodeluris($modeluris)
	{
		//error_log("evaluatereferencedmodelsinmodeluris (" . $modeluris . ")");
		
		$args = array
		(
			"modeluris" => $modeluris,
			"shouldapply_templaterules_lookups" => true,
			"shouldapplyurlvariables" => true,
		);
		$result = $this->evaluatereferencedmodelsinmodeluris_v2($args);
		return $result;
	}
	
	// evaluates nested references and evaluate variables as set by the seowebsiterules (url parameters)
	function evaluatereferencedmodelsinmodeluris_v2($args)
	{
		$modeluris = $args["modeluris"];
		
		global $nxs_gl_isevaluatingreferencedmodels;
		$nxs_gl_isevaluatingreferencedmodels[$modeluris]++;
		
		if ($modeluris == "")
		{
			return $modeluris;
		}
		
		$shoulddebug = $_REQUEST["magic"] == "debug";		
		if ($shoulddebug)
		{
			echo "debugging evaluatereferencedmodelsinmodeluris for $modeluris <br />";
		}
		
		// make uniform
		
		// error_log("before; $modeluris");
		
		$modeluris = str_replace(";", "|", $modeluris);
		$modeluris = str_replace("\r\n", "|", $modeluris);
		$modeluris = str_replace("\r", "|", $modeluris);
		$modeluris = str_replace("\n", "|", $modeluris);

		// apply templateuri mappings
		// for example in the modeluris "titlemodel:{{@@templateuri.titlemodel}}"
		// the templated variable "@@templateuri.titlemodel" could map to "{{@@url.id}}@game"
		// meaning modeluris would evaluate to "titlemodel:{{@url.id}}@game
		// whether or not these are applied is determined by a argument,
		// when evaluating the modeluris a recursive call is made, and for this recursive call
		// we should NOT apply them (to avoid endless loops), see #23029458092475
		$shouldapply_templaterules_lookups = $args["shouldapply_templaterules_lookups"];
		if ($shouldapply_templaterules_lookups)
		{
			$templateurimappingslookup = array();
			
			// various options to implement the behaviour of the templateuri mapping;
			// see https://docs.google.com/document/d/1rcRJR8sX8OIdofu7rlR3gd_jqFv0IDW6eoVPcpE4cQA/edit#
			// for now we implement the mapping using the businessrules
			$templateproperties = nxs_gettemplateproperties();
			$templaterules_lookups = $templateproperties["templaterules_lookups"];
			if ($templaterules_lookups != "")
			{
				foreach ($templaterules_lookups as $key => $val)
				{
					if ($key != "" && $val != "")
					{
						$key = trim($key);
						$val = trim($val);
						$templateurimappingslookup["@@templateuri.{$key}"] = $val;
					}
				}
				
				// apply the lookup tables to the parts we've evaluated so far
				$translateargs = array
				(
					"lookup" => $templateurimappingslookup,
					"item" => $modeluris,
				);
				$modeluris = nxs_filter_translate_v2($translateargs);
			}
		}
		
		// START - OLD IMPLEMENTATION - USED BY SOMETHING LIKE 4 WEBSITES SHOULD BE PHASED OUT!
		// replace fragments from the url 
		// for example: request: "http://domain/detail/1234/hello-world
		// could define fragments ("id" => "1234") and ("name" => "hello-world")
		// meaning that the modeluris value of "g:{{@@url.id}}@game" could map to "g:1234@game"
		$shouldapplyurlvariables = $args["shouldapplyurlvariables"];
		if ($shouldapplyurlvariables)
		{
			// step 1; ensure the fragments are parsed for the current url
			$parsed = $this->derivemodelforcurrenturl();
			$fragments = $parsed["parameters"]["fragments"];
			
			$fragmentslookup = array();
			foreach ($fragments as $key => $value)
			{
				$key = "@@url.{$key}";	// for example {@@url.id}} would become 1234
				$value = $value;
				$fragmentslookup[$key] = $value;
			}
			
			// add the hostname
			$key = "@@url.hostname";
			$value = $_SERVER['HTTP_HOST'];
			$fragmentslookup[$key] = $value;			
			
			// apply the lookup tables to the parts we've evaluated so far
			$translateargs = array
			(
				"lookup" => $fragmentslookup,
				"item" => $modeluris,
			);
			$modeluris = nxs_filter_translate_v2($translateargs);
		}
		// END
		
		// applying of url variables v2
		if (true)
		{
			$shouldapplyurlvariables = $args["shouldapplyurlvariables"];
			if ($shouldapplyurlvariables)
			{
				// todo: perhaps use the condition here on whether or not we should do this,
				// preventing possible endless loops... for now we will assume this will always go saf
				
				$templateproperties = nxs_gettemplateproperties();
				$url_fragment_variables = $templateproperties["url_fragment_variables"];
				
				$fragmentslookup = array();
				foreach ($url_fragment_variables as $key => $value)
				{
					// $key = "@@url.{$key}";	// for example {{@@url.id}}} would become 1234
					$key = $key;	// for example {{@@url.id}}} would become 1234
					$value = $value;
					$fragmentslookup[$key] = $value;
				}
				
				// add the hostname
				$key = "@@url.hostname";
				$value = $_SERVER['HTTP_HOST'];
				$fragmentslookup[$key] = $value;			
				
				// apply the lookup tables to the parts we've evaluated so far
				$translateargs = array
				(
					"lookup" => $fragmentslookup,
					"item" => $modeluris,
				);
				$modeluris = nxs_filter_translate_v2($translateargs);
			}
		}
		
		// loop through each parts of the sequence of modeluris and apply the variables from previous steps
		// (one part could evaluate a variable that is defined in previous parts)
		if (true)
		{
			$modelurisparts = explode("|", $modeluris);
			
			$recursivelookup = array();
			$updatedmodeluris = array();
			
			foreach ($modelurisparts as $index=>$modelurispart)
			{
				$orig = $modelurispart;
				
				// sanitize element
				$modelurispart = trim($modelurispart);
				
				// if its blank, ignore it
				if ($modelurispart == "")
				{
					continue;
				}
				
				if ($shoulddebug)
				{
					echo "index: {$index}<br />";
					echo "modelurispart: {$modelurispart}<br />";
					echo "recursivelookup: " . json_encode($recursivelookup) . "<br />";
				}
				
				// apply the lookup tables to the parts we've evaluated so far
				$translateargs = array
				(
					"lookup" => $recursivelookup,
					"item" => $modelurispart,
				);
				$modelurispart = nxs_filter_translate_v2($translateargs);
				
				if ($shoulddebug)
				{
					echo "modelurispart; stage 2; modelurispart: {$modelurispart}<br />";
				}
				
				// now apply lookup values again 
				
				// apply lookup values to the modelurispart "extended" models
				$lookupargs = array
				(
					"modeluris" => $modelurispart,
				);
				$lookupcurrentpart = $this->getlookups_v2($lookupargs);
				$recursivelookup = array_merge($recursivelookup, $lookupcurrentpart);
				
				$translateargs = array
				(
					"lookup" => $recursivelookup,
					"item" => $modelurispart,
				);
				$modelurispart = nxs_filter_translate_v2($translateargs);
				
				$hasvalidreferences = true;
				
				//
				if (nxs_stringcontains($modelurispart, "{{"))
				{
					$hasvalidreferences = false;
				}
				else if (nxs_stringcontains($modelurispart, "}}"))
				{
					$hasvalidreferences = false;
				}
				
				if ($shoulddebug)
				{
					
					echo "modelurispart; hasvalidreferences; " . json_encode($hasvalidreferences) . "<br />";
				}
				
				if ($hasvalidreferences)
				{
					// good :)
					$updatedmodeluris[] = $modelurispart;
				}
				else
				{
					$sofar = implode("|", $updatedmodeluris);
					// bad
					do_action("nxs_a_modelnotfound", "(sofar=>$sofar) unresolved:{$orig}");
					// no need to add the fraction to the list, as it wont resolve
					// $updatedmodeluris[] = $orig;
				}
	
				if ($shoulddebug)
				{
					echo "recursivelookup: ";
					var_dump($recursivelookup);
					echo "<br />";
					echo "modelurispart becomes: {$modelurispart}<br />";
				}
			}
	
			// stitch all elements
			$result = implode("|", $updatedmodeluris);
		}
			
		if ($shoulddebug)
		{
			echo "result: $result";
			//die();
		}
		
		return $result;
	}
	
	function derivemodelforcurrenturl()
	{
		$uriargs = array
		(
			"rewritewebmethods" => true,
		);
		$uri = nxs_geturicurrentpage($uriargs);
		$result = $this->derivemodelbyuri($uri);
		
		return $result;
	}
	
	function derivemodelbyuri($uri)
	{
		global $nxs_gl_modelbyuri;
		
		if (!isset($nxs_gl_modelbyuri[$uri]))
		{
			$uripieces = explode("?", $uri);
			$requestedslug = $uripieces[0];
			$requestedslug = trim($requestedslug, "/");
			
			$slugcount = count($requestedslug);
			$slugpieces = explode("/", $requestedslug);
			
			// find first condition that matches
			$result = false;
			$entries = $this->getentries();
			foreach ($entries as $entryid => $entrymeta)
			{
				$currententryvalid = "sofar";
				$currententryderivedparameters = array();
				
				$conditions = $entrymeta["conditions"];
				foreach ($conditions as $conditionid => $conditionmeta)
				{
					$currentconditionvalid = false;
					$conditiontype = $conditionmeta["type"];
					if ($conditiontype == "homeurl")
					{
						// 
						$operator = $conditionmeta["operator"];
						$value = $conditionmeta["value"];
						if ($operator == "equals" && $value == nxs_geturl_home())
						{
							// ok, proceed
						}
						else
						{
							//error_log("homeurl mismatch (".$value . ") vs (" . nxs_geturl_home() . ")");
							$currententryvalid = false;
							break;
						}
					}
					else if (nxs_stringstartswith($conditiontype, "slugatindex"))
					{
						$index = str_replace("slugatindex", "", $conditiontype);
						// obsolete/backwards compatibility
						if ($index == "")
						{
							$index = $conditionmeta["index"];
						}
						$operator = $conditionmeta["operator"];
						$value = $conditionmeta["value"];
						if ($operator == "equals" && $value == $slugpieces[$index])
						{
							// ok, proceed
						}
						else if ($operator == "exactmatchhumanmodelforschema")
						{
							$humanid = $slugpieces[$index];
							if ($humanid != "")
							{
								$conditionschema = $conditionmeta["value"];
								$currententryderivedparameters["humanid"] = "{$humanid}";
								$currententryderivedparameters["schema"] = "{$conditionschema}";
								$currententryderivedparameters["fragments"][$conditionschema] = $humanid;
								// ok, proceed
							}
							else
							{
								$currententryvalid = false;
								break;
							}
						}
						else if ($operator == "startswithhumanmodelforschema")
						{
							$currentslugpiece = $slugpieces[$index];
							// for example the following;
							// "{{X}}-grab-before-" 
							// "{{X}}-grab-before-*"
							// would be a match for "p13-grab-before-hello world" (X would then be "p13")
							$value = $conditionmeta["value"];
							
							$seperator = $value;
							$seperator = str_replace("*", "", $seperator);
							$seperator = str_replace("{{", "(", $seperator);
							$seperator = str_replace("}}", ")", $seperator);
							$seperator = str_replace("{", "(", $seperator);
							$seperator = str_replace("}", ")", $seperator);
							$seperator = preg_replace("/\([^)]+\)/","",$seperator);
							// for example "{{X}}-grab-before-" then seperator would be "-grab-before-"
							
							$slugsubpieces = explode($seperator, $currentslugpiece);
							// for example ("p13", "hello world")
							
							$humanid = $slugsubpieces[0];
							if ($humanid != "")
							{
								$schematemp = $value;
								$schematemp = str_replace("{{", "", $schematemp);
								$schematemp = str_replace("{", "", $schematemp);
								$schematemp = str_replace("}}", "|", $schematemp);
								$schematemp = str_replace("}", "|", $schematemp);
								$schematemppieces = explode("|", $schematemp);
								$conditionschema = $schematemppieces[0];
								
								if ($_REQUEST["debugmodel"] == "true")
								{
									echo "value:" . $value . "<br />";
									echo "schematemp:" . $schematemp . "<br />";
									echo "conditionschema:" . $conditionschema . "<br />";
									echo "value:" . $value . "<br />";
								}
								
								// for example "{{X}}-grab-before-" then conditionschema be "X"
								$currententryderivedparameters["fragments"][$conditionschema] = $humanid;
								// ok, proceed
							}
							else
							{
								$currententryvalid = false;
								break;
							}
						}
						else if ($operator == "endswithhumanmodelforschema")
						{
							$currentslugpiece = $slugpieces[$index];
							// for example the following;
							// "-grab-after-{{X}}" 
							// "*-grab-after-{{X}}"
							// would be a match for "hello-world-grab-after-{{X}}" (X would then be "p13")
							$value = $conditionmeta["value"];	
							
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
								$schematemppieces = explode("|", $schematemp);			// "-", "X"
								$conditionschema = $schematemppieces[1];
								
								// if the conditionschema has a "@"
								// we have to use the first part as the variable
								// and the 2nd part indicated the true modelschema
								// we should in that case only accept the URL
								// if the humanid exists in that schema
								if (nxs_stringcontains($conditionschema, "@"))
								{
									$conditionschemapieces = explode("@", $conditionschema);
									$conditionschema = $conditionschemapieces[0];
									$modelschema = $conditionschemapieces[1];
									$toverify = "{$humanid}@{$modelschema}";
									
									
									// check if such model exists
									$verified = $this->getmodel($toverify);
									if ($verified === false)
									{
										error_log("model $toverify doesn't exist, it should result in a 404!");	
										$currententryvalid = false;
										break;
									}
								}
								
								if ($_REQUEST["debugmodel2"] == "true")
								{
									echo "value:" . $value . "<br />";
									echo "schematemp:" . $schematemp . "<br />";
									echo "humanid:" . $humanid . "<br />";
									echo "conditionschema:" . $conditionschema . "<br />";
									
								}
								
								// for example "grab-after-{X}" then conditionschema be "X"

								$currententryderivedparameters["fragments"][$conditionschema] = $humanid;
								// ok, proceed
							}
							else
							{
								$currententryvalid = false;
								break;
							}
						}
						else if ($operator == "betweenpreandpostfixmatchhumanmodelforschema")
						{
							$value = $conditionmeta["value"];
							
							$value = str_replace("{{", "{", $value);
							$value = str_replace("}}", "}", $value);
							$value = str_replace("}", "|", $value);
							$value = str_replace("{", "|", $value);
							
							$valuepieces = explode("|", $value);
							$conditionprefix = $valuepieces[0];
							$conditionschema = $valuepieces[1];
							$conditionpostfix = $valuepieces[2];
							$slug = $slugpieces[$index];
							
							if (nxs_stringstartswith($slug, $conditionprefix) && nxs_stringendswith($slug, $conditionpostfix))
							{
								// humanid is {X} as in "prefix{X}postfix"
								$sluglength = strlen($slug);				
								$prefixlength = strlen($conditionprefix);		
								$postfixlength = strlen($conditionpostfix);	
								$start = $prefixlength;
								$length = $sluglength - $prefixlength - $postfixlength;
								$humanid = substr($slug, $start, $length);
								if ($humanid != "")
								{
									$currententryderivedparameters["fragments"][$conditionschema] = $humanid;
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
								$currententryvalid = false;
								break;
							}
						}
						else if ($operator == "equals" && $value != $slugpieces[$index])
						{
							// mismatch
							$currententryvalid = false;
							break;
						}
						else
						{
							error_log("rule; unsupported operator:($operator) val:($value) index:($index) sp:(" . $slugpieces[$index] . ")");
							
							$currententryvalid = false;
							break;
						}
					}
					else
					{
						echo "unsupported conditiontype?";
						die();
					}
					
					// error_log("processing rule; conditiontype $conditiontype; valid? $currententryvalid");
				}
				
				if ($currententryvalid == "sofar")
				{
					// if we come this far, it means it valid
					$result = array
					(
						"entryid" => $entryid,
						"parameters" => $currententryderivedparameters,
					);
					break;
				}
				else
				{
					$homeurl = nxs_geturl_home();
					if (nxs_stringcontains($homeurl, "websitesexample"))
					{
						// absorb
					}
					else if (nxs_stringcontains($homeurl, "nexusthemes"))
					{
						// absorb
					}
					else
					{
						// $currenturl = nxs_geturlcurrentpage();
						// error_log("rules; condition failed; $conditionid; $conditiontype; $homeurl; $currenturl;");
					}
					
					// perhaps next entry is valid, loop
				}
			}
			
			if ($result != false)
			{
				// error_log("rules; conclusion; " . json_encode($result));
			}
			
			$nxs_gl_modelbyuri[$uri] = $result;
		}
		else
		{	
			$result = $nxs_gl_modelbyuri[$uri];
		}
		
		if ($_REQUEST["debugmodel"] == "true")
		{
			echo "derivemodelbyuri for $uri:<br />";
			var_dump($result);
			echo "<br />";
		}
									
		return $result;
	}
	
	/*
	function deriveurlfrommodel($parameters)
	{
		$result = false;
		
		$schema = $parameters["schema"];
		$humanid = $parameters["humanid"];
		
		$entries = $this->getentries();
		foreach ($entries as $entryid => $entrymeta)
		{
			$resultsofar = array();
			
			$currententryvalid = "sofar";
			$currententryderivedparameters = array();
			
			$conditions = $entrymeta["conditions"];
			foreach ($conditions as $conditionid => $conditionmeta)
			{
				$currentconditionvalid = false;
				$conditiontype = $conditionmeta["type"];
				if ($conditiontype == "homeurl")
				{
					$resultsofar["homeurl"] = nxs_geturl_home();
				}
				else if (nxs_stringstartswith($conditiontype, "slugatindex"))
				{
					// obsolete/backwards compatibility
					if ($index == "")
					{
						$index = $conditionmeta["index"];
					}
					$operator = $conditionmeta["operator"];
					$value = $conditionmeta["value"];
					if ($operator == "equals")
					{
						$resultsofar["slugpieces"][$index] = $value;
					}
					else if ($operator == "exactmatchhumanmodelforschema" && $conditionmeta["schema"] == $schema)
					{
						$resultsofar["slugpieces"][$index] = $humanid;
					}
					else if ($operator == "betweenpreandpostfixmatchhumanmodelforschema")
					{
						$value = $conditionmeta["value"];
						$valuepieces = explode("|", $value);
						$conditionprefix = $valuepieces[0];
						$conditionschema = $valuepieces[1];
						$conditionpostfix = $valuepieces[2];
						if ($conditionschema == $schema)
						{
							$resultsofar["slugpieces"][$index] = "{$conditionprefix}{$humanid}{$conditionpostfix}";
						}
						else
						{
							$currententryvalid = false;
							break;
						}
					}
					else
					{
						$currententryvalid = false;
						break;
					}
				}
				else
				{
					echo "unsupported conditiontype?";
					die();
				}
			}
			
			if ($currententryvalid == "sofar")
			{
				// if we come this far, it means it valid
				$result = $resultsofar;
				
				$url = $resultsofar["homeurl"];
				$index = -1;
				while (true)
				{
					$index++;
					$part = $resultsofar["slugpieces"][$index];
					// error_log("part: $part");
					if ($part == "" || $index > 10)
					{
						break;
					}
					$url .= $part . "/";
				}
				$result["url"] = $url;
				
				break;
			}
			else
			{
				// error_log("condition failed at; $conditionid");
				// perhaps next entry is valid, loop
			}
		}
		
		return $result;
	}
	*/
	
	// virtual posts; allow entities from the model to 
	// represent a virtual post/page according to WP
	function businesssite_the_posts($result, $args)
	{
		global $wp,$wp_query;
		global $nxs_g_businesssite_didoverride;
		
		if (!is_main_query()) { return $result; }
		if (is_admin()) { return $result; }
		
		// 20170305; if its a webmethod, dont override it
		if (nxs_iswebmethodinvocation()) { return $result; }
		
		// only override 1x
		if ($nxs_g_businesssite_didoverride === true) { return $result; }
		$nxs_g_businesssite_didoverride = true;
		
		$countmatches = count($result);
		
		$modeluri = false;
		$schema = false;
		$humanid = false;
		
		$derivedcontext = $this->derivemodelforcurrenturl();
		$parameters = $derivedcontext["parameters"];
		$fragments = $parameters["fragments"];
		
		$isvirtual = false;
		
		$schema = "nxs_";
		foreach ($fragments as $key => $val)
		{
			if ($schema != "")
			{
				$schema .= "_";
			}
			$schema .= "{$key}";
			$isvirtual = true;
		}
		
		//
		$templateproperties = nxs_gettemplateproperties();
		if ($templateproperties["lastmatchingrule"] == "busruleurl")
		{
			$schema = "nxs_vtemplate";
			$isvirtual = true;
		}
		
		// error_log(json_encode($derivedcontext));
			
		// loop over the contentmodel and verify if the requestedslug matches 
		// any of the elements of the contentmodel
		if ($isvirtual)
		{
			// apparently there's a match;
			// mimic a post by creating a virtual post
			
			// derived the seo title
			$title = $this->wpseo_title();	
			$excerpt = "";	// intentionally left blank; not practical to fill this
			$content = "";  // intentionally left blank; lets use the front end instead
			$rightnow = current_time('mysql');
			$post_date = $rightnow;
			$post_date_gmt = $rightnow;
			$post_modified = $rightnow;
			$post_modified_gmt = $rightnow;
			
			$result = array();
			
			$foundmatch = true;

			//$wp_query->is_nxs_portfolio = true;
			$wp_query->is_singular = true;
			$wp_query->is_page = true;
			$wp_query->is_404 = false;
			$wp_query->is_attachment = false;
			$wp_query->is_archive = false;
			unset($wp_query->query_vars["error"]);
			if ($wp_query->queried_object != NULL)
			{
				$wp_query->queried_object->term_id = -1;
				$wp_query->queried_object->name = $schema;
			}
			
			$newpost = new stdClass;
			
			$newpost->ID = -999001;
			$newpost->post_author = 1;
			$newpost->post_name = "slug123";	// slug of current uri basically
			$newpost->guid = "test guid";
			$newpost->post_title = $title;
			$newpost->post_excerpt = $excerpt;
			$newpost->to_ping = "";
			$newpost->pinged = "";
			$newpost->post_content = $content;
			$newpost->post_status = "publish";
			$newpost->comment_status = "closed";
			$newpost->ping_status = "closed";
			$newpost->post_password = "";
			$newpost->comment_count = 0;
			$newpost->post_date = $post_date;
			$newpost->filter = "raw";
			$newpost->post_date_gmt = $post_date_gmt;	// current_time('mysql',1);
			$newpost->post_modified = $post_modified;
			$newpost->post_modified_gmt = $post_modified_gmt;
			$newpost->post_parent = 0;
			$newpost->post_type = $schema;
			
			// todo; perhaps handle content licensing
			// $newpost->nxs_content_license = json_encode(array("type" => "attribution", "author" => "benin"));
			
			$wp_query->posts[0] = $newpost;
			$wp_query->found_posts = 1;	 
			$wp_query->max_num_pages = 1;
				
			$result[]= $newpost;
			
			// override the Yoast SEO 
			if (true)
			{
				add_filter('wpseo_title', array($this, 'wpseo_title'), 99999);
				add_filter('wpseo_metadesc', array($this, 'wpseo_metadesc'), 1999);
				add_filter('wpseo_canonical', array($this, 'wpseo_canonical'), 99999);
				add_filter('wpseo_robots', array($this, 'wpseo_robots'), 99999);
			}
			
			// there can/may be only one match
			return $result;
		}
		else
		{
			// no match means keep post from regular WP, or perhaps 404 if its not found
		}
		
		return $result;
	}
	
	function gethumanid($modeluri = "")
	{
		$pieces = explode("@", $modeluri);
		$result = $pieces[0];
		return $result;
	}
	
	//
	//
	//
	function getlookups_v2($args)
	{
		$result = array();
		
		$modeluris = $args["modeluris"];

		$orig = $modeluris;
		
		// error_log("invoked; getlookups; $modeluris");
		
		$modeluris = str_replace(" ", "", $modeluris);
		$modeluris = str_replace(";", ",", $modeluris);
		$modeluris = str_replace("|", ",", $modeluris);
		$modeluripieces = explode(",", $modeluris);
		
		$index = -1;
		foreach ($modeluripieces as $modeluripiece)
		{
			$index++;
			
			$isvalid = true;
			if (nxs_stringcontains($modeluripiece, "{{"))
			{
				$isvalid = false;
			}
			else if (nxs_stringcontains($modeluripiece, "}}"))
			{
				$isvalid = false;
			}
			
			if (!$isvalid)
			{
				error_log("invalid model lookup; $modeluripiece (in orig:'$orig')");
				do_action("nxs_a_modelnotfound", "$modeluripiece (in orig:'$orig')");
				
				// skip!
				continue;
			}
			
			$subpieces = explode(":", $modeluripiece);
			
			if (count($subpieces) == 1)
			{
				$prefix = "";
				$modeluri = $subpieces[0];
			}
			else
			{				
				$prefix = $subpieces[0] . ":";
				$modeluri = $subpieces[1];
			}
			
			// format = prefix:foo@bar
			if ($modeluri != "")
			{
				$lookup = $this->getlookup($modeluri, "{$prefix}");
				$result = array_merge($result, $lookup);
			}
		}
		
		//
		if (true)
		{
			// include parameters as derived by the template engine
			$templateproperties = nxs_gettemplateproperties();
			$modelmapping = $templateproperties["templaterules_lookups_lookup"];
			
			foreach ($modelmapping as $key => $val)
			{
				$lookupkey = "{$key}";
				$result[$lookupkey] = $val;
			}
		}
		
		return $result;
	}
	
	function getlookup($modeluri = "", $prefix = "")
	{
		// error_log("invoked; getlookup; $modeluri ($prefix)");
		
		$schema = $this->getcontentschema($modeluri);
		$contentmodel = $this->getcontentmodel($modeluri);
		
		foreach ($schema as $taxonomyid => $taxonomymeta)
		{
			$taxonomyextendedproperties = $taxonomymeta["taxonomyextendedproperties"];
			foreach ($taxonomyextendedproperties as $fieldid => $fieldmeta)
			{
				$val = $contentmodel[$taxonomyid]["taxonomy"][$fieldid];
				$lookup["{$prefix}{$taxonomyid}.{$fieldid}"] = $val;
			}
		}
		
		return $lookup;
	}
	
	function isvalidmodeluri($modeluri = "")
	{
		$isvalid = true;
		if (nxs_stringstartswith($modeluri, "@"))
		{
			$isvalid = false;
		}
		else if (!nxs_stringcontains($modeluri, "@"))
		{
			$isvalid = false;
		}
		else if (nxs_stringcontains($modeluri, "{{"))
		{
			$isvalid = false;
		}
		else if (nxs_stringcontains($modeluri, "}}"))
		{
			$isvalid = false;
		}
		else if (nxs_stringcontains($url, "<"))
		{
			$isvalid = false;
		}
		else if (nxs_stringcontains($url, ">"))
		{
			$isvalid = false;
		}

		//$st = json_encode(nxs_getstacktrace());
		if (!$isvalid)
		{
			do_action("nxs_a_modelnotfound", "$modeluri (invalid)");
		
			$shoulddebug = ($_REQUEST["logrr"] == "true");
			if ($shoulddebug)
			{
				$st = json_encode(debug_backtrace());
				error_log("isvalidmodeluri; invalid; $modeluri; $st");
				die();
			}
		}

		return $isvalid;
	}
	
	function getmodel($modeluri = "")
	{
		$isvalid = $this->isvalidmodeluri($modeluri);
		if (!$isvalid)
		{
			return false;
		}
		
		// error_log("getmodel for $modeluri");
		
		// convert provided modeluri to the runtime one
		// (empty modeluri will mean modeluri will be derived from the url being accessed)
		
		$cachekey = $modeluri;
		if ($cachekey == "")
		{
			$cachekey = "/";
		}
		
		global $nxs_g_model;
		if (!isset($nxs_g_model[$cachekey]))
		{
			if ($modeluri != "")
			{
				$nxs_g_model[$cachekey] = $this->getmodel_dbcache($modeluri);
			}
			else
			{
				// error_log("businessite; no model to be retrieved");
			}
		}
		$result = $nxs_g_model[$cachekey];
		
		return $result;
	}
	
	function getcontentmodel($modeluri = "")
	{
		$model = $this->getmodel($modeluri);
		return $model["contentmodel"];
	}
	
	function getcontentmodelproperty($modeluri, $property)
	{
		$contentmodel = $this->getcontentmodel($modeluri);
		$value = $contentmodel["properties"]["taxonomy"][$property];
		return $value;
	}
	
	function getcontentschema($modeluri = "")
	{
		$model = $this->getmodel($modeluri);
		return $model["meta"]["schema"];
	}
	
	function ismaster()
	{
		$result = true;
		$homeurl = nxs_geturl_home();
		if ($homeurl == "http://theme1.testgj.c1.us-e1.nexusthemes.com/")
		{
			$result = false;	
		}
		else if ($homeurl == "http://theme2.testgj.c1.us-e1.nexusthemes.com/")
		{
			$result = false;	
		}
		else if ($homeurl == "http://theme3.testgj.c1.us-e1.nexusthemes.com/")
		{
			$result = false;	
		}
		else if ($homeurl == "http://blablabusiness.testgj.c1.us-e1.nexusthemes.com/")
		{
			$result = false;	
		}
		return $result;
	}
	
	function getmodel_dbcache($modeluri)
	{
		//
		$transientkey = md5("modeldb_{$modeluri}");
		$result = get_transient($transientkey);
		$shouldrefreshdbcache = false;
		
		if ($shouldrefreshdbcache == false && $result == "")
		{
			$shouldrefreshdbcache = true;
		}
		if ($shouldrefreshdbcache == false && $result == false)
		{
			$shouldrefreshdbcache = true;
		}
		if ($shouldrefreshdbcache == false && $_REQUEST["transients"] == "refresh")
		{
			$shouldrefreshdbcache = true;
		}
		if ($shouldrefreshdbcache == false && $_REQUEST["transients"] == "refresh_modeluricontains")
		{
			$needle = $_REQUEST["modeluricontains"];
			if ($needle != "")
			{
				$ignorecasing = true;
				if (nxs_stringcontains_v2($modeluri, $needle, $ignorecasing))
				{
					$shouldrefreshdbcache = true;
				}
			}
		}
		
		// todo; use a filter instead?
		if ($_REQUEST["nxs_qa_endtoend"] == "true")
		{
			// $shouldrefreshdbcache = true;
		}
		
		if ($shouldrefreshdbcache)
		{
			$result = $this->getmodel_actual($modeluri);
			
			// update cache
			$cacheduration = 60 * 60 * 24 * 30; // 30 days cache

			if (isset($result["cachedurationinsecs"]))
			{
				$cacheduration = $result["cachedurationinsecs"];
				if ($cacheduration == 0)
				{
					$cacheduration = 60 * 60 * 24 * 30; // 30 days cache
				}
			}
			
			set_transient($transientkey, $result, $cacheduration);
		}
		
		return $result;
	}
	
	function getmodel_actual($modeluri)
	{
		error_log("getmodel_actual; attempt; $modeluri");
		
		$isvalid = $this->isvalidmodeluri($modeluri);
		if (!$isvalid)
		{
			return false;
		}
		
		// if modeluri is specified retrieve the model through the modeluri
		$url = "https://turnkeypagesprovider.websitesexamples.com/api/1/prod/model-by-uri/{$modeluri}/?nxs=contentprovider-api&licensekey={$licensekey}&nxs_json_output_format=prettyprint";
		$content = file_get_contents($url);

		error_log("getmodel_actual; returned content");
		
		$json = json_decode($content, true);
		
		if ($json["found"] === false)
		{
			do_action("nxs_a_modelnotfound", "{$modeluri} (not found)");
			error_log("getmodel_actual; not found");
			return false;
		}
		
		if ($json["nxs_queued"] == "true")
		{
			// overrule the cache behaviour; we disable it,
			// since some items were queued; don't cache pages that render information based upon 
			// items that are queued
			nxs_disablecacheforthisrequest();
			
			// error_log("instructing the page to not store cache because of queued content");
			// its throttled/queued
			// for now we return false
			return false;
		}
		
		$result = $json;
		
		return $result;
	}
	
	function cachebulkmodels($singularschema)
	{
		if (!is_user_logged_in())
		{
			echo "only available for administrators";
			die();
		}
		
		error_log("cachebulkmodels for $singularschema");
		
		// step 1; load the bulk model information
		$url = "https://turnkeypagesprovider.websitesexamples.com/api/1/prod/bulkmodels/{$singularschema}/?nxs=contentprovider-api&licensekey={$licensekey}&nxs_json_output_format=prettyprint";
		$content = file_get_contents($url);
		$json = json_decode($content, true);
		
		$itemcount = 0;
		// step 2; loop over each item in the bulk model
		foreach ($json["items"] as $modeluri => $item)
		{
			$itemcount++;
			
			// step 3; combine /blend/ the content with the schema
			$schema = $item["schema"];
			$item["meta"]["schema"] = $json["schemas"][$schema];
			
			// step 3; store the item in the cache
			$transientkey = md5("modeldb_{$modeluri}");

			//$oldcache = get_transient($transientkey);
			//echo "item; $modeluri <br />";
			//echo "---------<br />cached item;<br />";
			//echo nxs_prettyprint_array($oldcache);
			//echo "---------<br />new item;<br />";
			//echo nxs_prettyprint_array($item);
						
			// update cache
			$cacheduration = 60 * 60 * 24 * 30; // 30 days cache

			if (isset($item["cachedurationinsecs"]))
			{
				$cacheduration = $item["cachedurationinsecs"];
				if ($cacheduration == 0)
				{
					$cacheduration = 60 * 60 * 24 * 30; // 30 days cache
				}
			}
			
			set_transient($transientkey, $item, $cacheduration);
		}
		
		error_log("cachebulkmodels; finished updating $itemcount items for $singularschema");
	}
	
	function getwidgets($result, $widgetargs)
	{
		$nxsposttype = $widgetargs["nxsposttype"];
		$pagetemplate = $widgetargs["pagetemplate"];
		
		if ($nxsposttype == "post") 
		{
			$result[] = array("widgetid" => "list");
			$result[] = array("widgetid" => "entities");
			$result[] = array("widgetid" => "phone");
			$result[] = array("widgetid" => "socialaccounts");
			$result[] = array("widgetid" => "commercialmsgs");
		}
		else if ($nxsposttype == "sidebar") 
		{
			$result[] = array("widgetid" => "list");
			$result[] = array("widgetid" => "entities");
			$result[] = array("widgetid" => "phone");
			$result[] = array("widgetid" => "socialaccounts");
		}
		else if ($nxsposttype == "header") 
		{
			$result[] = array("widgetid" => "phone");
			$result[] = array("widgetid" => "buslogo");
			$result[] = array("widgetid" => "socialaccounts");
			$result[] = array("widgetid" => "commercialmsgs");
		}
		
		if ($pagetemplate == "pagedecorator") 
		{
			$result[] = array("widgetid" => "taxpageslider", "tags" => array("nexus"));		
		}
		
		return $result;
	}
	
	/* YOAST SEO */
	
	function getruntimeseoproperties()
	{
		global $nxs_gl_runtimeseoproperties;
		
		if ($nxs_gl_runtimeseoproperties == "")
		{
			$templateproperties = nxs_gettemplateproperties();
			$content_postid = $templateproperties["content_postid"];
			// locate all "seo" widget(s) in the front-end content "template"
			$filterargs = array
			(
				"postid" => $content_postid,
				"widgettype" => "seo",	// all seo widgets
			);
			$seowidgets = nxs_getwidgetsmetadatainpost_v2($filterargs);
			$mixedattributes = reset($seowidgets);
			
			// Translate templated properties
			// getlookups_v2
			
			
			// Translate model data
			// $mixedattributes = nxs_filter_translatemodel($mixedattributes, array("title", "metadescription", "canonicalurl"));
			$args = array
			(
				"shouldapplyshortcodes" => true,
			);
			$mixedattributes = nxs_filter_translatemodel_v2($mixedattributes, array("title", "metadescription", "canonicalurl"), $args);
			
			// Translate urls
			$mixedattributes["canonicalurl"] = nxs_url_prettyfy($mixedattributes["canonicalurl"]);
			
			$nxs_gl_runtimeseoproperties = $mixedattributes;
		}
		
		if ($_REQUEST["wop"] == "v6")
		{
			var_dump($nxs_gl_runtimeseoproperties);
			die();
		}
		
		return $nxs_gl_runtimeseoproperties;
	}
	
	function wpseo_title($result) 
	{
		// if we reach this point, it means the page has a model as its context
		// the title, description and canonical url can be determined by 
		// a "seo widget" that is stored in the content template defined
		// in the page template rules.
		$runtimeseoproperties = $this->getruntimeseoproperties();
		$title = $runtimeseoproperties["title"];		
		if ($title != "")
		{
			// 
			
			$result = $title;
		}
		return $result;
	}
	
	function wpseo_metadesc($result) 
	{
		// if we reach this point, it means the page has a model as its context
		// the title, description and canonical url can be determined by 
		// a "seo widget" that is stored in the content template defined
		// in the page template rules.
		$runtimeseoproperties = $this->getruntimeseoproperties();
		$title = $runtimeseoproperties["metadescription"];		
		if ($title != "")
		{
			// 
			
			$result = $title;
		}
		return $result;
	}
	
	function wpseo_canonical($result)
	{
		// if we reach this point, it means the page has a model as its context
		// the title, description and canonical url can be determined by 
		// a "seo widget" that is stored in the content template defined
		// in the page template rules.
		$runtimeseoproperties = $this->getruntimeseoproperties();
		$title = $runtimeseoproperties["canonicalurl"];		
		if ($title != "")
		{
			// 
			
			$result = $title;
		}
		return $result;
	}
	
	function wpseo_robots($result = "index,follow")
	{
		// if we reach this point, it means the page has a model as its context
		// the title, description and canonical url can be determined by 
		// a "seo widget" that is stored in the content template defined
		// in the page template rules.
		$runtimeseoproperties = $this->getruntimeseoproperties();
		$canonicalurl = $runtimeseoproperties["canonicalurl"];		
		if ($canonicalurl != "")
		{
			$result = "index,follow";
		}
		return $result;
	}
	
	function instance_init()
	{
		// widgets
		nxs_lazyload_plugin_widget(__FILE__, "list");
		nxs_lazyload_plugin_widget(__FILE__, "entities");
		nxs_lazyload_plugin_widget(__FILE__, "phone");
		nxs_lazyload_plugin_widget(__FILE__, "buslogo");
		nxs_lazyload_plugin_widget(__FILE__, "socialaccounts");
		nxs_lazyload_plugin_widget(__FILE__, "commercialmsgs");

		// page decorators
		nxs_lazyload_plugin_widget(__FILE__, "taxpageslider");
		
		// handle bulk model prefetching
		if (is_user_logged_in())
		{
			$dumpmodeluri = $_REQUEST["dumpmodeluri"];
			if ($dumpmodeluri != "")
			{
				echo "output for $dumpmodeluri:<br /><br />";
				$d = $this->getmodel($dumpmodeluri);
				echo json_encode($d);
				die();
			}
			if ($_REQUEST["bulkmodels"] == "true")
			{
				$singularschema = $_REQUEST["singularschema"];
				if ($singularschema == "") { echo "singularschema not specified?"; die(); }
	
				$this->cachebulkmodels($singularschema);
				
				echo "Bulk models updated :)";
				die();
			}
			if ($_REQUEST["awstest"] == "true")
			{
				$leftovers = 9000;
				$d = $this->getmodel("singleton@listofgame");
				$instances = $d["contentmodel"]["game"]["instances"];
				
				echo "num of instances: " . count($instances) . "<br />";
				//die();
				$invaliditems = array();
				
				foreach ($instances as $instancemeta)
				{
					$gameid = $instancemeta["content"]["humanmodelid"];
					$gamemodel = $this->getmodel("{$gameid}@game");
					$upc = $gamemodel["contentmodel"]["properties"]["taxonomy"]["upc"];
					//echo "loading upc {$upc}<br />";
					$result = $this->getmodel("{$upc}@upc@awsproductapi");
					
					if (true)
					{
						if ($result["nxs_error"] == "true")
						{
							$invaliditems[] = $upc;
						}
					}
					
					// var_dump($awsmodel);
					// die();
					$leftovers--;
					if ($leftovers<=1)
					{
						echo "no more left overs... stopping";
						die();
					}
				}
				
				$invalidcnt = count($invaliditems);
				echo "invalidcnt; $invalidcnt<br />";
				echo "finished; left overs; $leftovers<br />";
				
				foreach ($invaliditems as $upc)
				{
					echo "{$upc}<br />";
				}
				
				die();
			}
		}
	}
	
	// kudos to https://teleogistic.net/2013/02/11/dynamically-add-items-to-a-wp_nav_menu-list/
	function wp_nav_menu_objects($result, $menu, $args)
	{
		if (true) //$_REQUEST["nxs"] == "debugmenu2")
    {
    	$newresult = array();
    	
    	$contentmodel = $this->getcontentmodel();
			$taxonomiesmeta = $this->getcontentschema();
			
    	// process taxonomy menu items (adds custom child items,
    	// and etches items that are empty)
    	
    	foreach ($result as $post)
    	{
    		//echo "found menu item;" . $post->object . " <br />";
    		
    		$title = $post->title;
    		$shouldbeprocessed = false;
    		if (nxs_stringcontains($title, "nxs_"))
    		{
    			$shouldbeprocessed = true;
    		}
    		
    		if ($shouldbeprocessed)
    		{
    			$found = false;
    			$posttype = $post->title;
    			
    			foreach ($taxonomiesmeta as $taxonomy => $taxonomymeta)
					{
						$shouldrender = false;
						if ($posttype == $taxonomy)
						{
							$shouldrender = true;
						}
						else if ($contentmodel[$taxonomy]["taxonomy"]["postid"] == $singleton_instanceid)
						{
							$shouldrender = true;
						}
						
						if ($shouldrender)
						{
							// this is the taxonomy we were looking for
							$found = true;
							
							if ($taxonomymeta["caninstancesbereferenced"] == true)
							{
								$instances = $contentmodel[$taxonomy]["instances"];
								if (count($instances) > 0)
								{
									// update this item (the "parent")
									$title = $contentmodel[$taxonomy]["taxonomy"]["title"];
									if ($title == "")
									{
										$title = "(empty)";
									}
									$post->title = $title;
									
									
									// make it not clickable
									$post->url = "#";
									$post->classes[] = "nxs-menuitemempty";
									$newresult[] = $post;
									
									// add instances as child elements to the list
									$childindex = -1;
									foreach ($instances as $instance)
									{
										$childindex++;
	
										$childpost = array
										(
							        'title'            => $instance["content"]["title"],
							        'menu_item_parent' => $post->ID,
							        'ID'               => '',
							        'db_id'            => '',
							        'url'              => '/' . $instance["content"]["slug"] . '/',
							      );
							      $newresult[] = (object) $childpost;
									}
								}
								else
								{
									// if there's "no" instances we remove/etch the item from the 
									// menu by simply not adding it to the newresult list
								}
							}
							else
							{
								// the instances cannot be referenced, thus we skip them
								// (item is 
							}
						}
						else
						{
							// some other taxonomy; ignore
						}
					}
					
					if (!$found)
					{
						// absorb
					}
				}
				else
				{
					// clone as-is
					//$post->title = $post->object . ";" . $singleton_instanceid;
					$newresult[] = $post;		
				}
			}
			
			// swap
			$result = $newresult;
    }
    
		return $result;
	}
	
	function __construct()
  {
  	add_filter('init', array($this, "instance_init"), 5, 1);
		add_action('nxs_getwidgets',array( $this, "getwidgets"), 20, 2);
		add_action('admin_head', array($this, "instance_admin_head"), 30, 1);
		
		add_filter('wp_nav_menu_objects', array($this, 'wp_nav_menu_objects'), 10, 3);
		
		//add_filter( 'the_content', array($this, 'the_content'), 10, 1);
		
		add_filter("the_posts", array($this, "businesssite_the_posts"), 1000, 2);
  }
  
	/* ---------- */
}

global $nxs_g_modelmanager;
$nxs_g_modelmanager = new nxs_g_modelmanager();