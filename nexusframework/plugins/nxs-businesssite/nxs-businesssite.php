<?php
/*
Plugin Name: Nxs Business Site
Version: 1.0.12
Plugin URI: https://github.com/TODO
Description: TODO
Author: GJ
Author URI: https://github.com/TODO/
*/

class businesssite_instance
{
	function containssyncedcontent($post)
	{
		$result = false;
		$previoussyncedcontenthash = get_post_meta($post->ID, 'nxs_synced_contenthash', $single = true);
		if ($previoussyncedcontenthash != "")
		{
			$result = true;
		}
		return $result;
	}
	
	function iscontentmodifiedsincelastsync($post)
	{
		$result = true;
		$contenthash = $this->getcontenthash($post);
		$previoussyncedcontenthash = get_post_meta($post->ID, 'nxs_synced_contenthash', $single = true);
		if ($contenthash == $previoussyncedcontenthash)
		{
			$result = false;
		}
		return $result;
	}
	
	function getcontenthash($post)
	{
		// title, slug, content, excerpt, image
		$hash = "";
		$hash .= md5($post->post_title);
		$hash .= md5($post->post_name);
		$hash .= md5($post->post_excerpt);
		$hash .= md5($post->post_content);
		$result = md5($hash);
		return $result;
	}
	
	function f_shouldrenderaddnewrowoption($result)
	{
		global $post;
		if (!$this->iscontentmodifiedsincelastsync($post))
		{
			$result = false;
		}
		return $result;
	}
	
	function sc_nxscomment($atts, $content=null)
	{
		if ($atts["condition"] == "authenticatedonly")
		{
			if (!is_user_logged_in())
			{
				// suppress it
				$content = "";
			}
		}
		else if ($atts["condition"] == "backendonly")
		{
			// suppress it
			$content = "";
		}
		
		return $content;
	}
	
	// container order sets functionality
	function getcontainerid($cpt)
	{
		$containerid = $this->getcontainerid_internal($cpt);
		if ($containerid === false) 
		{
			//echo "containerid not yet found, creating...";
			$result = $this->createnewcontainer_internal($cpt);
			if ($result["result"] != "OK") { echo "unexpected;"; var_dump($result); die(); }
			$containerid = $result["postid"];
		}
		return $containerid;
	}
	
	function getcontainerid_internal($cpt)
	{
		// published pagedecorators
		$publishedargs = array();
		$publishedargs["post_status"] = "publish";
		$publishedargs["post_type"] = "nxs_genericlist";
		
		$publishedargs['tax_query'] = array
		(
			array
			(
				'taxonomy' => 'nxs_tax_subposttype',
				'field' => 'slug',
				'terms' => "{$cpt}_set",
			)
		);
		
		$publishedargs["orderby"] = "post_date";//$order_by;
		$publishedargs["order"] = "DESC"; //$order;
		$publishedargs["showposts"] = -1;	// allemaal!
	  $pages = get_posts($publishedargs);
	  $result = false;
	  if (count($pages) >= 1) 
	  {
	  	$result = $pages[0]->ID;
	  }
	  
	  return $result;
	}
	
	function createnewcontainer_internal($cpt)
	{
		$subargs = array();
		$subargs["nxsposttype"] = "genericlist";
		$subargs["nxssubposttype"] = "{$cpt}_set";	// NOTE!
		$subargs["poststatus"] = "publish";
		$subargs["titel"] = nxs_l18n__("Set Order", "nxs_td") . " {$cpt} " . nxs_generaterandomstring(6);
		$subargs["slug"] = $subargs["titel"];
		$subargs["postwizard"] = "defaultgenericlist";
		
		$response = nxs_addnewarticle($subargs);
		if ($response["result"] != "OK")
		{
			echo "failed to create container?!";
			die();
		}
		else
		{
			//
		}
		
		return $response;
	}

	
		// abstract taxonomy instance functionality
		function getabstracttaxonomyinstanceid($taxonomy)
		{
			$taxonomiesmeta = nxs_business_gettaxonomiesmeta();
			$taxonomymeta = $taxonomiesmeta[$taxonomy];
			
			if ($taxonomiesmeta[$taxonomy]["singletontaxonomyinstance"] == true)
			{
				// 
				$result = false;
			}
			else
			{
				$singular = $taxonomymeta["singular"];
				$cpt = "nxs_{$singular}";
	
				$postids = nxs_wp_getpostidsbymetahavingposttype("nxs_abstracttaxinstance", $taxonomy, "nxs_taxonomy");
				
				$found = count($postids) > 0;
				if ($found)
				{
					$result = $postids[0];
				}
				else
				{
					// if its not yet there, create it
					$r = $this->createnewabstracttaxonomyinstance_internal($taxonomy);
					if ($r["result"] != "OK") { echo "unexpected;"; var_dump($r); die(); }
					$result = $r["postid"];
				}
			}
			
			return $result;
		}
		
		function createnewabstracttaxonomyinstance_internal($taxonomy)
		{
			
			$taxonomiesmeta = nxs_business_gettaxonomiesmeta();
			$taxonomymeta = $taxonomiesmeta[$taxonomy];
			$title = $taxonomymeta["title"];			

			$subargs = array();
			$subargs["nxsposttype"] = "taxonomy";
			$subargs["wpposttype"] = "nxs_taxonomy";
			
			$subargs["poststatus"] = "publish";
			$subargs["titel"] = $title;
			$subargs["slug"] = $title . " " . nxs_generaterandomstring(6);
			$subargs["postwizard"] = "skip";
			$subargs["postmetas"] = array
			(
				"nxs_abstracttaxinstance" => $taxonomy,
			);
			
			$response = nxs_addnewarticle($subargs);
			if ($response["result"] != "OK")
			{
				echo "failed to create container?!";
				die();
			}
			else
			{
				//
			}
			
			return $response;
		}
	
	
	function getcontentmodeltaxonomyinstances($arg)
	{
		$taxonomy = $arg["taxonomy"];
		$contentmodel = $this->getcontentmodel();
		$result = $contentmodel[$taxonomy]["instances"];
		return $result;
	}
	
	function getcontentmodel()
	{
		global $nxs_g_contentmodel;
		if (!isset($nxs_g_contentmodel))
		{
			$nxs_g_contentmodel = $this->getcontentmodel_actual();
		}
		return $nxs_g_contentmodel;
	}
	
	function getcontentmodel_actual()
	{
		$result = array();
		
		$taxonomiesmeta = nxs_business_gettaxonomiesmeta();
		
		// grab (and create if it doesn't yet exist) the singleton
		// instance of each abstract taxonomy entity, for example
		// the abstract singeton "services" entity which has a title,
		// and accompaniment title.
		foreach ($taxonomiesmeta as $taxonomy => $taxonomymeta)
		{
			$ati = $this->getabstracttaxonomyinstanceid($taxonomy);
			if ($ati != "")
			{
	  		$result[$taxonomy]["taxonomy"]["postid"] = $ati;
	  		$result[$taxonomy]["taxonomy"]["url"] = nxs_geturl_for_postid($ati);
	  		$post = get_post($ati);
	  		// grab meta data
	  		$result[$taxonomy]["taxonomy"]["post_title"] = $post->post_title;
				$result[$taxonomy]["taxonomy"]["post_excerpt"] = $post->post_excerpt;
				$result[$taxonomy]["taxonomy"]["post_content"] = $post->post_content;
			}
		}
		
		// grab instances of taxanomies
		foreach ($taxonomiesmeta as $taxonomy => $taxonomymeta)
		{
		 	if ($taxonomymeta["arity"] == "n")
		 	{
		 		if ($taxonomymeta["features"]["orderedinstances"]["enabled"] == true)
		 		{
			 		$singular = $taxonomymeta["singular"];
			 		
			 		// this invocation will create a new container when needed
					$containerid = $this->getcontainerid($singular);
				
		  		$result[$taxonomy]["postid"] = $containerid;
		  		$result[$taxonomy]["url"] = nxs_geturl_for_postid($containerid);
		  
				  // find "items" in the post with id containerid
					$filter = array
					(
						"postid" => $containerid,
					);
					$widgetsmetadata = nxs_getwidgetsmetadatainpost_v2($filter);
					
					$countinstancesenabled = 0;
					$items = nxs_getwidgetsmetadatainpost_v2($filter);
					foreach ($items as $placeholderid => $widgetmeta)
					{
						$widgettype = $widgetmeta["type"];
						if (true) // $widgettype == "entity")
						{
							$postid = $widgetmeta["filter_postid"];
							$post = get_post($postid);
							$url = "";
							if ($taxonomymeta["caninstancesbereferenced"])
							{
								$url = nxs_geturl_for_postid($post->ID);
							}
							
							$result[$taxonomy]["instances"][] = array
							(
								"type" => $widgetmeta["type"],
								"enabled" => $widgetmeta["enabled"],
								"content" => array
								(
									"post_id" => $post->ID,
									"post_title" => $post->post_title,
									"post_excerpt" => $post->post_excerpt,
									"post_content" => $post->post_content,
									"post_thumbnail_id" => get_post_thumbnail_id($post->ID),
									"url" => $url,
									"post_icon" => get_post_meta($post->ID, "nxs_entity_icon", true),
									"post_source" => get_post_meta($post->ID, "nxs_entity_source", true),
									"post_rating_text" => get_post_meta($post->ID, "nxs_entity_rating_text", true),
									"post_quote" => get_post_meta($post->ID, "nxs_entity_quote", true),
									"post_stars" => get_post_meta($post->ID, "nxs_entity_stars", true),
									"post_role" => get_post_meta($post->ID, "nxs_entity_role", true),
									//
									
									// "post_imperative_m" => get_post_meta($post->ID, "nxs_entity_imperative_m", true),
									// "post_imperative_l" => get_post_meta($post->ID, "nxs_entity_imperative_l", true),
									// "post_destination_cta" => get_post_meta($post->ID, "nxs_entity_destination_cta", true),
								),
							);
							
							if ($widgetmeta["enabled"] != "")
							{
								$countinstancesenabled++;
							}
						}
						else 
						{
							// 
							echo "unexpected widgettype; <br />";
							echo "postid: " . $containerid . "<br />";
							echo "huhh?! widgettype: {$widgettype}";
							// die();
						}
					}
					
					$result[$taxonomy]["countenabled"] = $countinstancesenabled;
				}
				else
				{
					// 			
				}
			}
		}
		 
		return $result;
	}
	
	function getwidgets($result, $widgetargs)
	{
		$nxsposttype = $widgetargs["nxsposttype"];
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
		
		return $result;
	}
	
	function a_edit_form_after_title() 
	{
		global $post;
		if ($this->containssyncedcontent($post))
		{
			if ($this->iscontentmodifiedsincelastsync($post))
			{
		    ?>
		    <div>
		      <p>This post is no longer synchronized with the content server as you made at least one modification in the title, excerpt, slug or content</p>
		    </div>
		    <?php
		  }
		  else
		  {
		  	?>
		  	<style>
		  		.businesssite-admin 
		  		{
					  position: relative;
					  margin-top:20px;
					}
		  		.businesssite-enabled .businesssite-admin-tabs 
		  		{
				    border: none;
				    margin: 10px 0 0;
					}
					.businesssite-admin-tabs a 
					{
				    border-color: #dfdfdf #dfdfdf #f0f0f0;
				    border-style: solid;
				    border-width: 1px 1px 0;
				    color: #aaa;
				    font-size: 12px;
				    font-weight: bold;
				    line-height: 16px;
				    display: inline-block;
				    padding: 8px 14px;
				    text-decoration: none;
				    margin: 0 4px -1px 0;
				    border-top-left-radius: 3px;
				    border-top-right-radius: 3px;
				    -moz-border-top-left-radius: 3px;
				    -moz-border-top-right-radius: 3px;
				    -webkit-border-top-left-radius: 3px;
				    -webkit-border-top-right-radius: 3px;
					}
					.businesssite-enabled .businesssite-admin-ui 
					{
					  display: block;
					}
					.businesssite-admin-ui h3 
					{
				    font-family: Helvetica, sans-serif !important;
				    font-size: 18px !important;
				    font-weight: 300 !important;
				    margin: 0 0 30px 0 !important;
				    padding: 0 !important;
					}
					.businesssite-enabled .businesssite-admin-ui 
					{
					   display: block;
					}
					.businesssite-admin-ui 
					{
				    border: 1px solid #ccc;
				    border-top-right-radius: 3px;
				    border-bottom-right-radius: 3px;
				    border-bottom-left-radius: 3px;
				    -moz-border-top-right-radius: 3px;
				    -moz-border-bottom-right-radius: 3px;
				    -moz-border-bottom-left-radius: 3px;
				    -webkit-border-top-right-radius: 3px;
				    -webkit-border-bottom-right-radius: 3px;
				    -webkit-border-bottom-left-radius: 3px;
				    margin-bottom: 20px;
				    padding: 45px 0 50px;
				    text-align: center;
					}
					.businesssite-admin-tabs a.active 
					{
				    border-width: 1px;
				    color: #464646;
					}
		  	</style>
		  	
				<div class="businesssite-admin">
					<div class="businesssite-admin-tabs">
						<a href="javascript:void(0);" onclick="return false;" class="active">Copyrighted Article</a>
						<!-- <a href="javascript:void(0);" onclick="return false;" class="active">Page Builder</a> -->
					</div>
					<div class="businesssite-admin-ui">
						<h3>Copyrighted article.</h3>
						<p>
		      		Note; this is a <b>copyrighted</b> guest article provided by XYZ. You can use the article on your site for free as long as you keep the attribution and content in place.<br />
		      		To hide the attribution, or to customize the content you will need to buy a non-exclusive license from the author.<br />
						</p>
						<a href="#" class="button button-primary button-large">Remove Article Attribution</a>
						<a href="#" class="button button-primary button-large">Contact Author</a>
						<a href="#postdivrich" onclick="jQuery(this).hide();jQuery('#postdivrich').show(); $(window).scrollTop($(window).scrollTop()+1); return false;" class="button button-primary button-large">Regular WP Editor</a>
					</div>
					<div class="businesssite-loading"></div>
				</div>
		  	
				<!-- -->		  	
		  	
		    <style>
		    	#postdivrich { display: none; }
		    </style>
		    <?php
		  }
	  }
	}
	
	function instance_admin_head()
	{
		if (is_admin())
		{
			add_action( 'edit_form_after_title', array($this, "a_edit_form_after_title"), 30, 1);
		}
	}
	
	function wp_insert_post( $post_id, $post, $update ) 
	{
		// If this is a revision, ignore
		if ( wp_is_post_revision( $post_id ) )
		{
			return;
		}
		if ($update === true)
		{
			// ignore update
			return;
		}
		
		// if the globalid is not yet set, create it
		$ensuredglobalid = nxs_get_globalid($post_id, true);
		
		$post_type = $post->post_type;
		$isbusinessmodeltaxonomy = false;
		$businessmodeltaxonomies = nxs_business_gettaxonomiesmeta();
		foreach ($businessmodeltaxonomies as $taxonomy => $taxmeta)
		{
			$singular = $taxmeta["singular"];
			$cpt = "nxs_{$singular}";
			if ($cpt == $post_type)
			{
				if ($taxonomy == "taxonomies")
				{
					// ignore; taxonies are not ordered
					break;
				}				
				
				$isbusinessmodeltaxonomy = true;
				break;
			}
		}
		
		if (!$isbusinessmodeltaxonomy)
		{
			// ignore entities outside the business taxonomies
			return;
		}
		
		// we automatically add the newly created post to the list
		$contentmodel = $this->getcontentmodel();
		$taxonomyorderedsetpostid = $contentmodel[$taxonomy]["postid"];
		// add an additional row to that post
		// appends a new "one" row, with the specified widget properties to an existing post

		$args = array
		(
			"postid" => $taxonomyorderedsetpostid,
			"widgetmetadata" => array
			(
				"type" => "entity",
				"filter_postid" => $post_id,
				"enabled" => "true",
			),
		);
		$r = nxs_add_widget_to_post($args);
	}
	
	function untrashed_post($post_id)
	{
		// error_log("untrashed_post; $post_id");
		$post = get_post($post_id);
		$post_type = $post->post_type;
		$isbusinessmodeltaxonomy = false;
		$businessmodeltaxonomies = nxs_business_gettaxonomiesmeta();
		foreach ($businessmodeltaxonomies as $taxonomy => $taxmeta)
		{
			$singular = $taxmeta["singular"];
			$cpt = "nxs_{$singular}";
			if ($cpt == $post_type)
			{
				if ($taxonomy == "taxonomies")
				{
					// ignore; taxonies are not ordered
					break;
				}
				
				$isbusinessmodeltaxonomy = true;
				break;
			}
		}
		
		if (!$isbusinessmodeltaxonomy)
		{
			// ignore entities outside the business taxonomies
			return;
		}
		
		// we automatically add the newly created post to the list 
		$contentmodel = $this->getcontentmodel();
		$taxonomyorderedsetpostid = $contentmodel[$taxonomy]["postid"];
		// add an additional row to that post
		// appends a new "one" row, with the specified widget properties to an existing post

		$args = array
		(
			"postid" => $taxonomyorderedsetpostid,
			"widgetmetadata" => array
			(
				"type" => "entity",
				"filter_postid" => $post_id,	// 
				"enabled" => "true",
			),
		);
		$r = nxs_add_widget_to_post($args);
	}
	
	function wp_delete_post($post_id)
	{
		//error_log("debug; detect wp_delete_post $post_id");
		
		$post_type = get_post_type($post_id);
		$isbusinessmodeltaxonomy = false;
		
		// only act when this is a taxonomy
		$taxonomiesmeta = nxs_business_gettaxonomiesmeta();
		foreach ($taxonomiesmeta as $taxonomy => $taxonomymeta)
		{
		 	if ($taxonomymeta["arity"] == "n")
		 	{
		 		$singular = $taxonomymeta["singular"];
		 		$taxonomycpt = "nxs_{$singular}";
		 		if ($post_type == $taxonomycpt)
		 		{
		 			if ($taxonomy == "taxonomies")
					{
						// ignore; taxonies are not ordered
						break;
					}
					
		 			$isbusinessmodeltaxonomy = true;
		 			break;
		 		}
			}
		}
		
		if (!$isbusinessmodeltaxonomy)
		{
			// ignore entities outside the business taxonomies
			return;
		}
		
		//error_log("debug; wp_delete_post for taxonomy; cpt $taxonomycpt");
		
		// here we should also delete the item from the ordered list (if its on it)
		$contentmodel = $this->getcontentmodel();
		$taxonomyorderedsetpostid = $contentmodel[$taxonomy]["postid"];

		$filter = array
		(
			"postid" => $taxonomyorderedsetpostid,
			"widgettype" => "entity",
		);
		$entities = nxs_getwidgetsmetadatainpost_v2($filter);
		
		foreach ($entities as $placeholderid => $placeholdermeta)
		{
			// grab the filter_postid value
			$filter_postid = $placeholdermeta["filter_postid"];
			if ($filter_postid == $post_id)
			{
				// get the row identifier that contains the placeholder
				$pagerowid = nxs_getpagerowid_forpostidplaceholderid($taxonomyorderedsetpostid, $placeholderid);

				//error_log("debug; wp_delete_post; deleting pagerowid; $pagerowid in postid $taxonomyorderedsetpostid");
				
				// delete that row!
				nxs_struct_purgerow($taxonomyorderedsetpostid, $pagerowid);
				
				// note; we will not break the loop; in theory the element could be referenced
				// multiple times in the set (not likely, but possible)
			}
		}
	}
	
	function instance_init()
	{
		// 
		nxs_lazyload_plugin_widget(__FILE__, "entities");
		nxs_lazyload_plugin_widget(__FILE__, "phone");
		nxs_lazyload_plugin_widget(__FILE__, "buslogo");
		nxs_lazyload_plugin_widget(__FILE__, "socialaccounts");
		nxs_lazyload_plugin_widget(__FILE__, "commercialmsgs");
	}
	
	function wp_nav_menu_items($result, $args ) 
	{
    if ($_REQUEST["nxs"] == "debugmenu")
    {
    	var_dump($result);
    	var_dump($args);
    	die();
    }

    return $result;
	}
	
	
	function walker_nav_menu_start_el($result, $item, $depth, $args ) 
	{
  	if ($_REQUEST["nxs"] == "debugmenu")
    {
    	//var_dump($result);
    	//var_dump($args);
    	//die();
    }
    
    // $result = "[" . $result . "]";
    
  	return $result;
	}
	
	// kudos to https://teleogistic.net/2013/02/11/dynamically-add-items-to-a-wp_nav_menu-list/
	function wp_nav_menu_objects($result, $menu, $args)
	{
		if (true) //$_REQUEST["nxs"] == "debugmenu2")
    {
    	$newresult = array();
    	
    	$contentmodel = $this->getcontentmodel();
			$taxonomiesmeta = nxs_business_gettaxonomiesmeta();
    	
    	// process taxonomy menu items (adds custom child items,
    	// and etches items that are empty)
    	
    	foreach ($result as $post)
    	{
    		if ($post->object == "nxs_taxonomy")
    		{
    			$found = false;
    			$singleton_instanceid = $post->object_id;
    			foreach ($taxonomiesmeta as $taxonomy => $taxonomymeta)
					{
						if ($contentmodel[$taxonomy]["taxonomy"]["postid"] == $singleton_instanceid)
						{
							// this is the taxonomy we were looking for
							$found = true;		
							
							if ($taxonomymeta["caninstancesbereferenced"] == true)
							{
								$instances = $contentmodel[$taxonomy]["instances"];
								if (count($instances) > 0)
								{
									// update this item (the "parent")
									$post->title = $contentmodel[$taxonomy]["taxonomy"]["post_title"]; //"item voor $taxonomy";
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
							        'title'            => $instance["content"]["post_title"],
							        'menu_item_parent' => $post->ID,
							        'ID'               => '',
							        'db_id'            => '',
							        'url'              => $instance["content"]["url"],
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
						/*
						$post->title = "not found; " . $singleton_instanceid;
						$newresult[] = $post;
						var_dump($contentmodel);
						die();
						*/
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
			
			/*    	
    	$result = array();
    	
    	$new_item = new stdClass;
    	$id_a = "a";
    	$new_item->ID = $id_a;
			$new_item->db_id = $id_a;
			$new_item->menu_item_parent = 0;
			$new_item->url = "http://nexusthemes.com";
			$new_item->title = "a";
			$new_item->menu_order = 1;	// $menu_order;
			$result[] = $new_item;
			
			$new_item = new stdClass;
    	$id_b = "b";
    	$new_item->ID = $id_b;
			$new_item->db_id = $id_b;
			$new_item->menu_item_parent = 0;
			$new_item->url = "http://nexusthemes.com";
			$new_item->title = "b";
			$new_item->menu_order = 1;	// $menu_order;
			$result[] = $new_item;
			
			// https://isabelcastillo.com/dynamically-sub-menu-item-wp_nav_menu
			// http://wordpress.stackexchange.com/questions/100484/how-to-add-a-child-item-to-a-menu-element-using-wp-nav-menu-objects
			$link = array
			(
        'title'            => 'Cats',
        'menu_item_parent' => $id_a,
        'ID'               => '',
        'db_id'            => '',
        'url'              => 'www.google.com'
      );
      $result[] = (object) $link;
			*/
    }
    
		return $result;
	}
	
	function the_content($content) 
	{
  	global $post;
  	$posttype = $post->post_type;
  	$shouldprocess = true;	// for now we will process it "always"
  	// todo: only process the item if the businessmodel has a
  	// metafield telling us to process it
  	// to speed things up
  	if ($shouldprocess)
  	{
	  	$postid = $post->ID;
	  	$nxs_attribution = get_post_meta($postid, 'nxs_content_attribution', $single = true);
	  	if ($nxs_attribution != "")
	  	{
	  		$data = json_decode($nxs_attribution, true);
	  		// for now this is hardcoded
	  		if ($data["author"] == "benin")
	  		{
	    		$content .= "<p style='font-size:small'>Benin Brown is a web copywriter who specializes in providing high quality website content for digital marketing professionals. As a white label content provider he is well-versed in writing for all industries. To learn more you can visit <a target='_blank' href='http://www.brownwebcopy.com'>www.brownwebcopy.com</p>";
	    	}
	    	else
	    	{
	    		$content .= $nxs_attribution;
	    	}
	    }
	  }
	  return $content;
	}
	
	function __construct()
  {
  	add_filter( 'init', array($this, "instance_init"), 5, 1);
		add_action( 'nxs_getwidgets',array( $this, "getwidgets"), 20, 2);
		add_shortcode( 'nxscomment', array($this, "sc_nxscomment"), 20, 2);
		add_filter("nxs_f_shouldrenderaddnewrowoption", array($this, "f_shouldrenderaddnewrowoption"), 1, 1);
		add_action('admin_head', array($this, "instance_admin_head"), 30, 1);
		
		//
		add_action( 'wp_insert_post', array($this, "wp_insert_post"), 10, 3 );
		add_action( 'untrashed_post', array($this, "untrashed_post"), 10, 3 );
		
		//
		add_action( 'delete_post', array($this, "wp_delete_post"), 10, 3 );
		add_action( 'wp_trash_post', array($this, "wp_delete_post"), 10, 3 );
		
		//add_filter('wp_nav_menu_items','wp_nav_menu_items', 10, 2);
		add_filter('walker_nav_menu_start_el', array($this, 'walker_nav_menu_start_el'), 10, 4);
		
		add_filter('wp_nav_menu_objects', array($this, 'wp_nav_menu_objects'), 10, 3);
		
		add_filter( 'the_content', array($this, 'the_content'), 10, 1);
  }
  
	/* ---------- */
}

global $businesssite_instance;
$businesssite_instance = new businesssite_instance();