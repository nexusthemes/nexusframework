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
	
	function getentries()
	{
		$entries = array
		(
			"free-willy" => array
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
		);
		return $entries;
	}
	
	function derivemodelfromurl()
	{
		$uri = nxs_geturicurrentpage();
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
					else if ($operator == "exactmatchhumanmodelforrealm" && $slugpieces[$index] != "")
					{
						$humanid = $slugpieces[$index];
						$realm = $conditionmeta["realm"];
						$currententryderivedparameters["humanid"] = "{$humanid}";
						$currententryderivedparameters["realm"] = "{$realm}";
						// ok, proceed
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
		
		//$is404 = ($countmatches == 0);
		//if (!$is404) { return $result; }

		// it would become a 404, unless we intercept the request
		// and inject some virtual values :)
		
		$uri = nxs_geturicurrentpage();
		$uripieces = explode("?", $uri);
		$requestedslug = $uripieces[0];
		$requestedslug = trim($requestedslug, "/");
		
		$slugcount = count($requestedslug);
		$slugpieces = explode("/", $requestedslug);
		
		$derivedcontext = $this->derivemodelfromurl();
		if ($derivedcontext === false)
		{
			// 
			//error_log("found validentry; $validentry");
		}
		else
		{
			// we have a winner
			// error_log("context found for page; :" . json_encode($derivedcontext));
			
			$parameters = $derivedcontext["parameters"];
			$urlresult = $this->deriveurlfrommodel($parameters);
			error_log("urlresult: " . json_encode($urlresult));
		}
		
		// loop over the contentmodel and verify if the requestedslug matches 
		// any of the elements of the contentmodel
		$contentmodel = $this->getcontentmodel();
		
		global $nxs_g_modelmanager;
		$taxonomiesmeta = $nxs_g_modelmanager->getcontentschema();
	
		$foundmatch = false;
		foreach ($taxonomiesmeta as $taxonomy => $taxonomymeta)
		{
			$instances = $contentmodel[$taxonomy]["instances"];
			foreach ($instances as $instance)
			{
				$post_slug = $instance["content"]["post_slug"];
				if ($post_slug == "")
				{
					continue;
				}
				if ($post_slug == $requestedslug)
				{
					// echo "@@@ postslug match ($post_slug) == ($requestedslug)";
					
					// there's a match! inject the content of the model to the post

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
						$wp_query->queried_object->name = $taxonomy;	//$id;
					}
					
					$newpost = new stdClass;
					
					// replicate all fields from the model
					foreach ($instance["content"] as $key => $val)
					{
						$newpost->$key = $val;
					}
					
					// 
					
					$newpost->ID = -999001;	// "hi"; // 0-$instance["content"]["post_id"]; // -1;	//"virtual" . $id;
					$newpost->post_author = 1;
					$newpost->post_name = $instance["content"]["post_slug"];
					$newpost->guid = "test guid";
					$newpost->post_title = $instance["content"]["title"];
					$newpost->post_excerpt = $instance["content"]["excerpt"];
					$newpost->to_ping = "";
					$newpost->pinged = "";
					$newpost->post_content = $instance["content"]["content"];
					$newpost->post_status = "publish";
					$newpost->comment_status = "closed";
					$newpost->ping_status = "closed";
					$newpost->post_password = "";
					$newpost->comment_count = 0;
					$newpost->post_date = current_time('mysql');	
					$newpost->filter = "raw";
					$newpost->post_date_gmt = current_time('mysql',1);
					$newpost->post_modified = current_time('mysql',1);
					$newpost->post_modified_gmt = current_time('mysql',1);
					$newpost->post_parent = 0;
					$newpost->post_type = $taxonomy;
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
					//echo "mismatch: ($post_slug) vs ($requestedslug)<br />";
					//var_dump($instance);
				}
				// echo "<br />";
			}
		}
		
		return $result;
	}
	
	function getmodel($modeluri = "")
	{
		$cachekey = $modeluri;
		if ($cachekey == "")
		{
			$cachekey = "/";
		}
		
		global $nxs_g_model;
		if (!isset($nxs_g_model[$cachekey]))
		{
			$nxs_g_model[$cachekey] = $this->getmodel_actual($modeluri);
		}
		return $nxs_g_model[$cachekey];
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
		$ismaster = $this->ismaster();
		if ($ismaster)
		{
			 $result = false;
		}
		else
		{
			$result = $this->getmodel_actual_slave($modeluri);
		}
		return $result;
	}
	
	function getmodel_actual_slave($modeluri)
	{
		if ($modeluri == "")
		{
			// 
			$homeurl = nxs_geturl_home();
			
			$businesstype = $_REQUEST["businesstype"];
			$businessid = $_REQUEST["businessid"];
			if ($businessid == "")
			{
				if ($_COOKIE["businessid"] != "")
				{
					$businessid = $_COOKIE["businessid"];
				}
			}
			
			if ($businessid == "x")
			{
				$businessid = "";
			}
			
			if ($businessid != "")
			{
				$url = "https://turnkeypagesprovider.websitesexamples.com/api/1/prod/businessmodel/{$businessid}/?nxs=contentprovider-api&licensekey={$licensekey}&nxs_json_output_format=prettyprint";
				// also store the businessid in the cookie
				setcookie("businessid", $businessid);
				
				$content = file_get_contents($url);
				$json = json_decode($content, true);
				$result = $json;
				
				// enrich the model
				// the slug is determined "at runtime"; its derived from the title
				$all_slugs = array();
				$schema = $json["meta"]["schema"];
				$taxonomiesmeta = $schema;
				foreach ($taxonomiesmeta as $taxonomy => $taxonomymeta)
				{
					//echo "tax: $taxonomy <br />";
					
					$titlefield = "";
					$slugfield = "post_slug";
					
					$instanceextendedproperties = $taxonomymeta["instanceextendedproperties"];
					foreach ($instanceextendedproperties as $field => $fieldmeta)
					{
						if ($fieldmeta["persisttype"] == "wp_title")
						{
							$titlefield = $field;
						}
						if ($fieldmeta["persisttype"] == "wp_slug")
						{
							$slugfield = $field;
						}
					}
					
					//echo "titlefield: $titlefield <br />";
					//echo "slugfield: $slugfield <br />";
					
					//
					
					$instances = $result[$taxonomy]["instances"];
					foreach ($instances as $index => $instance)
					{
						$content = $instance["content"];
						$title = $content[$titlefield];
						$slug = $title;
						$slug = nxs_url_prettyfy($slug);
						
						if (in_array($slug, $all_slugs))
						{
							// this slug is already in use; make it unique
							$count = count($all_slugs);
							$slug .= "_{$count}";
						}
						
						$all_slugs[]= $slug;
						
						$result[$taxonomy]["instances"][$index]["content"][$slugfield] = $slug;
						$result[$taxonomy]["instances"][$index]["content"]["post_slug"] = $slug;
					}
				}
			}
			else
			{
				// probably best to do here, is to redirect to a backend page or so,
				// that allows the user to create a new model which is used from that
				// moment on ...
			}
		}
		else
		{
			error_log("getmodel_actual_slave for (($modeluri))");
			
			// if modeluri is specified retrieve the model through the modeluri
			$url = "https://turnkeypagesprovider.websitesexamples.com/api/1/prod/model-by-uri/{$modeluri}/?nxs=contentprovider-api&licensekey={$licensekey}&nxs_json_output_format=prettyprint";
			
			$content = file_get_contents($url);
			$json = json_decode($content, true);
			$result = $json;
		}
		
		// allow plugins to override/extend the behaviour
		$args = array
		(
			"modeluri" => $modeluri
		);
		$result = apply_filters("nxs_f_getmodel_actual_slave", $result, $args);
		
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