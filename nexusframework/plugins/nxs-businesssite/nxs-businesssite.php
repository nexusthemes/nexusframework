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
	
	// todo: move this information to a model (or even better; model)
	function getmodelinterpreter()
	{
		$result = array
		(
			"schematowpinstancemapping" => array
			(
				// realm => ...
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
							"operator" => "betweenpreandpostfixmatchhumanmodelforrealm",
							"prefix" => "free-",
							"postfix" => "-website",
							"realm" => "freewebsiteproduct",
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
							"operator" => "exactmatchhumanmodelforrealm",
							"realm" => "freewebsiteproduct",
						),
					),
				),
			),
		);
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
				else if ($conditiontype == "slugatindex")
				{
					$index = $conditionmeta["index"];
					$operator = $conditionmeta["operator"];
					$value = $conditionmeta["value"];
					if ($operator == "equals" && $value == $slugpieces[$index])
					{
						// ok, proceed
					}
					else if ($operator == "exactmatchhumanmodelforrealm")
					{
						$humanid = $slugpieces[$index];
						if ($humanid != "")
						{
							$realm = $conditionmeta["realm"];
							$currententryderivedparameters["humanid"] = "{$humanid}";
							$currententryderivedparameters["realm"] = "{$realm}";
							// ok, proceed
						}
						else
						{
							$currententryvalid = false;
							break;
						}
					}
					else if ($operator == "betweenpreandpostfixmatchhumanmodelforrealm")
					{
						$prefix = $conditionmeta["prefix"];
						$postfix = $conditionmeta["postfix"];
						$slug = $slugpieces[$index];
						if (nxs_stringstartswith($slug, $prefix) && nxs_stringendswith($slug, $postfix))
						{
							// humanid is {X} as in "prefix{X}postfix"
							$sluglength = strlen($slug);				
							$prefixlength = strlen($prefix);		
							$postfixlength = strlen($postfix);	
							$start = $prefixlength;
							$length = $sluglength - $prefixlength - $postfixlength;
							$humanid = substr($slug, $start, $length);
							if ($humanid != "")
							{
								$realm = $conditionmeta["realm"];
								$currententryderivedparameters["humanid"] = "{$humanid}";
								$currententryderivedparameters["realm"] = "{$realm}";
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
					else
					{
						error_log("nope; op:($operator) val:($value) index:($index) sp:(" . $slugpieces[$index] . ")");
						
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
				$result = array
				(
					"entryid" => $entryid,
					"parameters" => $currententryderivedparameters,
				);
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
	
	function deriveurlfrommodel($parameters)
	{
		$result = false;
		
		$realm = $parameters["realm"];
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
				else if ($conditiontype == "slugatindex")
				{
					$index = $conditionmeta["index"];
					$operator = $conditionmeta["operator"];
					$value = $conditionmeta["value"];
					if ($operator == "equals")
					{
						$resultsofar["slugpieces"][$index] = $value;
					}
					else if ($operator == "exactmatchhumanmodelforrealm" && $conditionmeta["realm"] == $realm)
					{
						$resultsofar["slugpieces"][$index] = $humanid;
					}
					else if ($operator == "betweenpreandpostfixmatchhumanmodelforrealm" && $conditionmeta["realm"] == $realm)
					{
						$prefix = $conditionmeta["prefix"];
						$postfix = $conditionmeta["postfix"];
						$resultsofar["slugpieces"][$index] = "{$prefix}{$humanid}{$postfix}";
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
		$realm = false;
		$humanid = false;
		
		$derivedcontext = $this->derivemodelforcurrenturl();
		$parameters = $derivedcontext["parameters"];
		if ($parameters != "")
		{
			$humanid = $parameters["humanid"];
			$realm = $parameters["realm"];
			$modeluri = "{$humanid}@{$realm}";
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
			$wpinstancemappingcurrentschema = $schematowpinstancemapping[$realm];

			// get the title_template, for example "{{nxs_searchphrase.searchphrase}}"
			$title_template = $wpinstancemappingcurrentschema["title_template"];
			
			// translate the placeholders
			$translateargs = array
			(
				"lookup" => $lookup,
				"item" => $title_template,
			);
			$title = nxs_filter_translate_v2($translateargs);
			
			if ($title == "")
			{
				$title = "empty?";
			}
			
			$excerpt = "";	// not practical to fill this
			$content = ""; // "TODO CONTENT :)";	// lets use the front end instead
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
				$wp_query->queried_object->name = $realm;
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
			$newpost->post_type = $realm;
			// todo; handle in better way
			$newpost->nxs_content_license = json_encode(array("type" => "attribution", "author" => "benin"));
			
			$wp_query->posts[0] = $newpost;
			$wp_query->found_posts = 1;	 
			$wp_query->max_num_pages = 1;
				
			$result[]= $newpost;
			
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
					$realm = $parameters["realm"];
					$modeluri = "{$humanid}@{$realm}";
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
				$nxs_g_model[$cachekey] = $this->getmodel_actual($modeluri);
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
	
	function getmodel_actual($modeluri)
	{
		error_log("getmodel_actual for (($modeluri))");
		
		// if modeluri is specified retrieve the model through the modeluri
		$url = "https://turnkeypagesprovider.websitesexamples.com/api/1/prod/model-by-uri/{$modeluri}/?nxs=contentprovider-api&licensekey={$licensekey}&nxs_json_output_format=prettyprint";
		
		// todo: add caching here...
		$content = file_get_contents($url);
		$json = json_decode($content, true);
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