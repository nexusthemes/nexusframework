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
	
	// evaluates nested referenced 
	function evaluatereferencedmodelsinmodeluris($modeluris)
	{
		$shoulddebug = $_REQUEST["magic"] == "debug";		
		if ($shoulddebug)
		{
			echo "debugging evaluatereferencedmodelsinmodeluris for $modeluris <br />";
		}
		
		$humanid = $this->gethumanid("");
		$modeluris = str_replace("{{humanid}}", $humanid, $modeluris);
		
		$modelurisparts = explode("|", $modeluris);
		$recursivelookup = array();
		$updatedmodeluris = array();
		
		foreach ($modelurisparts as $index=>$modelurispart)
		{
			$orig = $modelurispart;
			
			// sanitize element
			$modelurispart = trim($modelurispart);
			
			if ($shoulddebug)
			{
				echo "index: {$index}<br />";
				echo "modelurispart: {$modelurispart}<br />";
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
			$lookupcurrentpart = $this->getlookups($modelurispart);
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
		
		if ($shoulddebug)
		{
			echo "result: $result";
			die();
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
						if ($_REQUEST["finedump"] == "true")
						{
							echo "hoebaboeba";
							die();
						}
						
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
									$currententryderivedparameters["humanid"] = "{$humanid}";
									$currententryderivedparameters["schema"] = "{$conditionschema}";
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
		if ($parameters != "")
		{
			$humanid = $parameters["humanid"];
			$schema = $parameters["schema"];
			$modeluri = "{$humanid}@{$schema}";
		}
		
		// error_log(json_encode($derivedcontext));
		
		// loop over the contentmodel and verify if the requestedslug matches 
		// any of the elements of the contentmodel
		if ($modeluri != "")
		{
			// apparently there's a match

			// make a mapping for the taxonomy fields
			$contentmodel = $this->getcontentmodel($modeluri);
			if ($contentmodel == false)
			{
				// not found, ignore
				return $result;
			}
			
			$lookup = array();
			foreach ($contentmodel as $taxonomy => $m)
			{
				$keyvalues = $m["taxonomy"];
				foreach ($keyvalues as $key => $val)
				{
					$lookup["{$taxonomy}.{$key}"] = $val;
				}
			}
			
			$schematowpinstancemapping = $this->getschematowpinstancemapping();
			$wpinstancemappingcurrentschema = $schematowpinstancemapping[$schema];

			// get the title_template, for example "{{nxs_searchphrase.searchphrase}}"
			$title_template = $wpinstancemappingcurrentschema["title_template"];
			
			// translate the placeholders
			$translateargs = array
			(
				"lookup" => $lookup,
				"item" => $title_template,
			);
			$title = nxs_filter_translate_v2($translateargs);
			
			if ($_REQUEST["seodebug"] == "true")
			{
				echo "$title_template <br />";
				echo "$title <br />";				
				var_dump($lookup);
				die();
			}
			
			if ($title == "")
			{
				$title = "empty?";
			}
			
			$excerpt = "";	// not practical to fill this
			$content = "";  // lets use the front end instead
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
			// todo; handle in better way
			$newpost->nxs_content_license = json_encode(array("type" => "attribution", "author" => "benin"));
			
			$wp_query->posts[0] = $newpost;
			$wp_query->found_posts = 1;	 
			$wp_query->max_num_pages = 1;
				
			$result[]= $newpost;
			
			if (true)	// $_REQUEST["pimpseo"] == "true")
			{
				add_filter('wpseo_title', array($this, 'wpseo_title'), 99999);
				add_filter('wpseo_metadesc', array($this, 'wpseo_metadesc'), 1999);
				add_filter('wpseo_canonical', array($this, 'wpseo_canonical'), 99999);
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
	
	// convert the modeluri to a runtime modeluri
	function getruntimemodeluri($modeluri = "")
	{
		if ($modeluri == "")
		{
			// use the current URI to determine the model
			if (true)
			{
				$derivedcontext = $this->derivemodelforcurrenturl();
				$parameters = $derivedcontext["parameters"];
				if ($parameters != "")
				{
					$humanid = $parameters["humanid"];
					$schema = $parameters["schema"];
					$modeluri = "{$humanid}@{$schema}";
				}
				//var_dump($derivedcontext);
				//die();
			}
		}
		
		return $modeluri;
	}
	
	function gethumanid($modeluri = "")
	{
		$modeluri = $this->getruntimemodeluri($modeluri);
		$pieces = explode("@", $modeluri);
		$result = $pieces[0];
		return $result;
	}
	
	function getlookups($modeluris = "")
	{
		$result = array();
		
		$orig = $modeluris;
		
		// error_log("invoked; getlookups; $modeluris");
		
		$modeluris = str_replace(" ", "", $modeluris);
		$modeluris = str_replace(";", ",", $modeluris);
		$modeluris = str_replace("|", ",", $modeluris);
		$modeluripieces = explode(",", $modeluris);
		
		// we also include the "empty" modeluri (which maps to the model of the page
		// in the current scope
		$modeluripieces = array_merge(array(""), $modeluripieces);
		
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
			
			$lookup = $this->getlookup($modeluri, "{$prefix}");

			$result = array_merge($result, $lookup);
		}
		
		return $result;
	}
	
	function getlookup($modeluri = "", $prefix = "")
	{
		// error_log("invoked; getlookup; $modeluri ($prefix)");
		
		$modeluri = $this->getruntimemodeluri($modeluri);
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
		
		$lookup["this.humanid"] = $this->gethumanid($modeluri);
		
		return $lookup;
	}
	
	function getmodel($modeluri = "")
	{
		// convert provided modeluri to the runtime one
		// (empty modeluri will mean modeluri will be derived from the url being accessed)
		$modeluri = $this->getruntimemodeluri($modeluri);
		
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
		if ($result == "" || $result == false || $_REQUEST["transients"] == "refresh")
		{
			$result = $this->getmodel_actual($modeluri);
			// update cache
			$cacheduration = 60 * 60 * 24; // 24 hours cache
			set_transient($transientkey, $result, $cacheduration);
		}
		
		return $result;
	}
	
	function getmodel_actual($modeluri)
	{
		$shoulddebug = false;
		
		$isvalid = true;
		if (nxs_stringstartswith($modeluri, "@"))
		{
			$isvalid = false;
		}
		else if (nxs_stringstartswith($modeluri, "{{"))
		{
			$isvalid = false;
		}
		else if (nxs_stringstartswith($modeluri, "}}"))
		{
			$isvalid = false;
		}
		
		if (!$isvalid)
		{
			//$st = json_encode(nxs_getstacktrace());
			do_action("nxs_a_modelnotfound", "$modeluri (invalid)");
			if ($shoulddebug)
			{	
				error_log("getmodel_actual; invalid; $modeluri");
			}
			return false;
		}
		
		// error_log("getmodel_actual for (($modeluri))");
		
		// if modeluri is specified retrieve the model through the modeluri
		$url = "https://turnkeypagesprovider.websitesexamples.com/api/1/prod/model-by-uri/{$modeluri}/?nxs=contentprovider-api&licensekey={$licensekey}&nxs_json_output_format=prettyprint";
		$content = file_get_contents($url);
		$json = json_decode($content, true);
		
		if ($json["found"] === false)
		{
			do_action("nxs_a_modelnotfound", "{$modeluri} (not found)");
			error_log("getmodel_actual; not found");
			return false;
		}
		
		$result = $json;
		
		return $result;
	}
	
	function getwidgets($result, $widgetargs)
	{
		$nxsposttype = $widgetargs["nxsposttype"];
		$pagetemplate = $widgetargs["pagetemplate"];
		
		if ($nxsposttype == "post") 
		{
			$result[] = array("widgetid" => "entities");
			$result[] = array("widgetid" => "phone");
			$result[] = array("widgetid" => "socialaccounts");
			$result[] = array("widgetid" => "commercialmsgs");
		}
		else if ($nxsposttype == "sidebar") 
		{
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
			
			// Lookup atts
			$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title","subtitle", "button_text","destination_url", "image_src"));	
			
			// Translate model data
			$mixedattributes = nxs_filter_translatemodel($mixedattributes, array("title", "metadescription", "canonicalurl"));	
			
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
	
	function instance_init()
	{
		// widgets
		nxs_lazyload_plugin_widget(__FILE__, "entities");
		nxs_lazyload_plugin_widget(__FILE__, "phone");
		nxs_lazyload_plugin_widget(__FILE__, "buslogo");
		nxs_lazyload_plugin_widget(__FILE__, "socialaccounts");
		nxs_lazyload_plugin_widget(__FILE__, "commercialmsgs");

		// page decorators
		nxs_lazyload_plugin_widget(__FILE__, "taxpageslider");
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
	
	function the_content($content) 
	{
  	global $post;
  	$posttype = $post->post_type;
  	$taxonomy = $posttype;

		global $nxs_g_modelmanager;
		$businessmodeltaxonomies = $nxs_g_modelmanager->getcontentschema();
  	
		// only do so when the attribution is a feature of this taxonomy
		$shouldprocess = $businessmodeltaxonomies[$taxonomy]["features"]["contentattribution"]["enabled"] == true;
  	if ($shouldprocess)
  	{
	  	$nxs_content_license = $post->nxs_content_license;
	  	if ($nxs_content_license != "")
	  	{
	  		$data = json_decode($nxs_content_license, true);
	  		if ($data["type"] == "attribution")
	  		{
		  		// for now this is hardcoded
		  		if ($data["author"] == "benin")
		  		{
		    		$content .= "<p style='font-size:small'>Benin Brown is a web copywriter who specializes in providing high quality website content for digital marketing professionals. As a white label content provider he is well-versed in writing for all industries. To learn more you can visit <a target='_blank' href='http://www.brownwebcopy.com'>www.brownwebcopy.com</p>";
		    	}
		    	else
		    	{
		    		$content .= $nxs_content_license;
		    	}
		    }
	    }
	  }
	  return $content;
	}
	
	function __construct()
  {
  	add_filter( 'init', array($this, "instance_init"), 5, 1);
		add_action( 'nxs_getwidgets',array( $this, "getwidgets"), 20, 2);
		add_action('admin_head', array($this, "instance_admin_head"), 30, 1);
		
		add_filter('wp_nav_menu_objects', array($this, 'wp_nav_menu_objects'), 10, 3);
		
		add_filter( 'the_content', array($this, 'the_content'), 10, 1);
		
		add_filter("the_posts", array($this, "businesssite_the_posts"), 1000, 2);
  }
  
	/* ---------- */
}

global $nxs_g_modelmanager;
$nxs_g_modelmanager = new nxs_g_modelmanager();