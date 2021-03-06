<?php
	
	add_action("init", "nxs_pods_bridge_init");
	function nxs_pods_bridge_init()
	{
		if ($_REQUEST["nxs"] == "dumppods" && $_REQUEST["dumppods"] != "" && is_super_admin())
		{
			$pods = $_REQUEST["dumppods"];
			echo "Dumping {$pods}<br />";
			$debug = true;
			$params = array("name" => $pods);
			$pod = pods_api()->load_pod($params, false);
			echo json_encode($pod);
			//var_dump($pod);
			die();
		}
	}
	
	function nxs_pods_bridge_lookups_recursive_getpost_props($postid, $prefix, $recursionsleft = 3)
	{
		
		
		// die();
	
		$posttype = get_post_type($postid);
		
		$pod_meta = pods_api()->load_pod($posttype, false);
		if ($pod_meta === false)
		{
			$add["{$prefix}postid"] = $postid;
			$add["{$prefix}post_title"] = get_the_title($postid);
			// $metas = get_post_meta($postid);
			
			return $add;
		}
		
		$fields = $pod_meta["fields"];	// alle attributes van de entiteit
		$metas = get_post_meta($postid);
		
		/*
		if ($_REQUEST["peek"] == "true")
		{
			var_dump($metas);
		}
		*/
			
		$add = array();
		
		// todo: this part should probably be moved into the nexus framework as it will apply for
		// all posts
		$add["{$prefix}postid"] = $postid;
		$add["{$prefix}post_title"] = get_the_title($postid);
		$add["{$prefix}thumbnail_id"] = $metas["_thumbnail_id"][0];
		$wpcontent = nxs_getwpcontent_for_postid($postid);
		$wpcontent = apply_filters('the_content', $wpcontent);
		$add["{$prefix}wpcontent"] = $wpcontent;
		
		foreach ($fields as $field)
		{			
			$fieldname = $field["name"];
			$count = count($metas[$fieldname]);
			
			if ($count == 0)
			{
				$add["{$prefix}{$fieldname}"] = "";
			}
			else if ($count == 1)
			{
				$add["{$prefix}{$fieldname}"] = $metas[$fieldname][0];	// first
				
				// if field is recursive reference
				if ($field["type"] == "pick")
				{
					if ($field["pick_object"] == "post_type")
					{
						$related_postid = $metas[$fieldname][0];
						$name = $field["name"];
						if ($prefix == "")
						{
							$relatedprefix = $name . ".";
						}
						else
						{
							$relatedprefix = $prefix . "." . $name . ".";
						}
						$relatedrecursionsleft = $recursionsleft - 1;
						if ($relatedrecursionsleft > 0)
						{
							$related_nxs_lookups_getpost_props = nxs_pods_bridge_lookups_recursive_getpost_props($related_postid, $relatedprefix, $relatedrecursionsleft);
							
							$add = array_merge($add, $related_nxs_lookups_getpost_props);
						}
						else
						{
							// 
						}
						
						
						
						$add["{$prefox}{$fieldname}"] = "{$related_postid}@wp.post";
					}
				}
				else if ($field["type"] == "date")
				{
					$add["{$prefix}{$fieldname}_raw"] = $add["{$prefix}{$fieldname}"];
					$display = pods_field_display($posttype, $postid, $fieldname);
					$add["{$prefix}{$fieldname}"] = $display;
				}
			}
			else 
			{
				// convert the post ids to the modeluri
				foreach ($metas[$fieldname] as $key => $val)
				{
					$metas[$fieldname][$key] = "{$val}@wp.post";
				}
				$add["{$prefix}{$fieldname}"] = implode("|", $metas[$fieldname]);
			}	
		}
		
		return $add;
	}
	
	function nxs_f_pods_bridge_lookups_includepodslookups($result = array())
	{
		global $post;
		
		$add = nxs_pods_bridge_lookups_recursive_getpost_props($post->ID, "", 3);
		
		
		$result = array_merge($result, $add);
		
		//var_dump($result);
		//die();
		
		return $result;
	}
	add_filter("nxs_f_lookups", "nxs_f_pods_bridge_lookups_includepodslookups", 25, 1);
	
	function nxs_f_pods_bridge_lookups_context($result = array(), $context = array())
	{
		$prefix = $context["prefix"];
		$modeluri = $context["modeluri"];
		
		if ($modeluri != "")
		{
			$pieces = explode("@", $modeluri);
			$schema = $pieces[1];
			
		
			if ($schema == "wp.post")
			{
				$postid = $pieces[0];

				$add = nxs_pods_bridge_lookups_recursive_getpost_props($postid, $prefix, 3); 
				$result = array_merge($result, $add);
				
			}
		}

		//echo json_encode($result);
		//die();
		
		return $result;
	}
	add_filter("nxs_f_lookups_context", "nxs_f_pods_bridge_lookups_context", 10, 2);
	
?>