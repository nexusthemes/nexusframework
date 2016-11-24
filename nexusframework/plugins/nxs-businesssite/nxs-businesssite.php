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
	
	// 
	function sc_nxsarticlecontent($atts)
	{
		// grab output through contentprovider
		
		global $businesssite_instance;
		$nxs_siteid = $businesssite_instance->getsiteid();

		$articleid = $atts["articleid"];
		
		$contentproviderurl = "https://turnkeypagesprovider.websitesexamples.com/api/1/prod/article/?nxs=contentprovider-api&articleid={$articleid}";
		
		//$result .= "$contentproviderurl <br />";
		
		$contentmetajson = file_get_contents($contentproviderurl);
		$contentmeta = json_decode($contentmetajson, true);
		$result .= $contentmeta["content"]["post_content"];
		
		return $result;
	}
	
	function changesiteid($siteid)
	{
		update_option('nxs_siteid', $siteid);
		$this->resynccontentmodel();
		echo "siteid was updated :)";
		die();
	}
	
	function getsiteid()
	{
		$nxs_siteid = esc_attr(get_option('nxs_siteid'));
		
		if ($nxs_siteid == "")
		{
			// TODO: allocate a new site id on the model server using an API,
			// instead of generating one client-side...
			$nxs_siteid = nxs_generaterandomstring(8);
			update_option('nxs_siteid', $nxs_siteid);
		}
		
		return $nxs_siteid;
	}
	
	function resynccontentmodel()
	{
		$siteid = $this->getsiteid();
		
		// re-fetch the information from the content server
		$jsonmodelurl = "https://turnkeypagesprovider.websitesexamples.com/api/1/prod/contentmodel/?nxs=contentprovider-api&nxs_siteid={$siteid}";
		$jsonmodel = file_get_contents($jsonmodelurl);
		$model = json_decode($jsonmodel, true);
		
		// multiple stages
		
		// stage 1; store new and updated existing entities on this (client) site
		if (true)
		{
			$taxonomy = "services";
			$instances = $model[$taxonomy]["instances"];
			$count = count($instances);
			echo "resync {$count} instances for taxonomy taxonomy {$taxonomy} :)<br />";
			foreach ($model[$taxonomy]["instances"] as $serviceinstance)
			{
				$semantic = $serviceinstance["semantic"];
				$semantic_fqn = "{$taxonomy}.{$semantic}";
				$title = $serviceinstance["content"]["post_title"];
				$content = $serviceinstance["content"]["post_content"];
				$excerpt = $serviceinstance["content"]["post_excerpt"];
				$slug = $serviceinstance["content"]["nxs_semantic_slug"];
				
				echo "starting to resync semantic_fqn {$semantic_fqn} <br />";
				$the_query = new WP_Query( array( 'post_type' => array('post', 'page'), 'meta_key' => "nxs_semantic", 'meta_value' => $semantic_fqn ) );
				if ( $the_query->have_posts() )
				{
					$the_query->the_post();
					global $post;
					
					if ($this->iscontentmodifiedsincelastsync($post))
					{
						// 
						echo "<div style='background-color: red; color: white;'>item is modified since last sync, won't update!</div><br />";
						continue;
					}
					
					echo "semantic_fqn $semantic_fqn already exists (post id:" . $post->ID . ")<br />";
					
					$my_post = array
					(
						'ID' => $post->ID
					);
					$isdirty = false;
					
					$title_old = $post->post_title;
					if ($title != $title_old && $title != "")
					{
						echo "semantic_fqn $semantic_fqn old title ({$title_old}) becomes new title ({$title}) <br />";
						// update title, slug and categories
						$my_post['post_title'] = $title;
						$post->post_title = $title;
						$isdirty = true;
					}
					else
					{
						echo "semantic_fqn $semantic_fqn title unchanged ($title)<br />";
					}
					
					// slug
					$slug_old = $post->post_name;
					if ($slug != $slug_old && $slug != "")
					{
						echo "semantic_fqn $semantic_fqn old slug {$slug_old} becomes new title {$slug} <br />";
						// update title, slug and categories
						$my_post['post_name'] = $slug;
						$post->post_name = $slug;
						$isdirty = true;
					}
					else
					{
						//echo "semantic_fqn $semantic_fqn slug unchanged ($slug)<br />";
					}
					
					// excerpt
					$old = $post->post_excerpt;
					if ($excerpt != $old && $excerpt != "")
					{
						echo "semantic_fqn $semantic_fqn old excerpt ({$old}) becomes ({$excerpt}) <br />";
						// update content
						$my_post['post_excerpt'] = $excerpt;
						$post->post_excerpt = $excerpt;
						$isdirty = true;
					}
					else
					{
						//echo "semantic_fqn $semantic_fqn content unchanged ($slug)<br />";
					}
					
					// content
					$content_old = $post->post_content;
					if ($content != $content_old && $content != "")
					{
						echo "semantic_fqn $semantic_fqn old content ({$content_old}) becomes ({$content}) <br />";
						// update content
						$my_post['post_content'] = $content;
						$post->post_content = $content;
						$isdirty = true;
					}
					else
					{
						//echo "semantic_fqn $semantic_fqn content unchanged ($slug)<br />";
					}
					
					if ($isdirty)
					{
						// updates the post in the DB (and creates a new revision)
						wp_update_post($my_post);
					}
						
					//
					wp_reset_postdata();
				}
				else
				{
					echo "semantic_fqn $semantic_fqn is not yet in the site, adding ... <br />";
					
					// 
					$args = array();
					$args["slug"] = $semantic_fqn; // todo: should be determined by the model/translation
					$args["titel"] = $semantic_fqn; // todo: should be determined by the model/translation
					$args["nxsposttype"] = nxs_getnxsposttype_by_wpposttype("page");
					$args["createpage"] = "true";
					$args["postmetas"] = array
					(
						"nxs_semantic" => $semantic_fqn,
						"nxs_semantic_taxonomy" => $taxonomy,
					);
					$args["postwizard"] = "skip";
					$response = nxs_addnewarticle($args);
					if ($response["result"] != "OK") { echo "failed to insert model"; die(); }
					$postid = $response["postid"];
					echo "DONE ($postid)<br />";
				}
			}
		}
		
		// stage 2; enrich the model
		if (true)
		{
			$taxonomy = "services";
			$instances = $model[$taxonomy]["instances"];
			$count = count($instances);
			echo "resync {$count} instances for taxonomy taxonomy {$taxonomy}<br />";
			$index = -1;
			foreach ($model[$taxonomy]["instances"] as $serviceinstance)
			{
				$index++;
				
				$semantic = $serviceinstance["semantic"];
				$semantic_fqn = "{$taxonomy}.{$semantic}";
				$title = $serviceinstance["content"]["post_title"];
				
				echo "starting to enrich semantic_fqn {$semantic_fqn} <br />";
				$the_query = new WP_Query( array( 'post_type' => array('post', 'page'), 'meta_key' => "nxs_semantic", 'meta_value' => $semantic_fqn ) );
				if ( $the_query->have_posts() )
				{
					$the_query->the_post();
					global $post;
					$postid = $post->ID;
					$url = nxs_geturl_for_postid($postid);
					
					// enrich the url
					$model[$taxonomy]["instances"][$index]["content"]["url"] = $url;
					echo "enriched model {$taxonomy} {$semantic} url to {$url} <br />";
					
					// enrich the hash of the content of the post
					// used to determine if the content was modified outside the sync feature
					$contenthash = $this->getcontenthash($post);
					update_post_meta($postid, 'nxs_synced_contenthash', $contenthash);
					
					//
					wp_reset_postdata();
				}
			}
		}
		
		// stage 3;  store the (enriched) model
		$jsonmodel = json_encode($model);

		$semantic_fqn = "contentmodel";
		// echo "locating semantic_fqn {$semantic_fqn} for resyncing<br />";
		$the_query = new WP_Query( array( 'post_type' => array('post', 'page'), 'meta_key' => "nxs_semantic", 'meta_value' => $semantic_fqn ) );
		if ( $the_query->have_posts() )
		{
			echo "semantic_fqn $semantic_fqn already exists ... ";
			$the_query->the_post();
			
			global $post;
			$postid = $post->ID;
			
			//
			
			
			update_post_meta($postid, "nxs_contentmodeljson", $jsonmodel);
			echo "DONE<br />";
			
			//
			wp_reset_postdata();
		}
		else
		{
			echo "semantic_fqn $semantic_fqn is not yet in the site, adding ... <br />";
			// 
			$args = array();
			$args["slug"] = $semantic_fqn;
			$args["titel"] = $semantic_fqn;
			$args["nxsposttype"] = nxs_getnxsposttype_by_wpposttype("page");
			$args["createpage"] = "true";
			$args["postmetas"] = array
			(
				"nxs_semantic" => $semantic_fqn, 
				"nxs_contentmodeljson" => $jsonmodel
			);
			$args["postwizard"] = "skip";
			$response = nxs_addnewarticle($args);
			if ($response["result"] != "OK") { echo "failed to insert model"; die(); }
			$postid = $response["postid"];
			echo "DONE ($postid)<br />";
		}
		
		echo "so far :)<br />";
		die();
	}
	
	function getcontentmodel()
	{
		// new implementation;
		// post with name "model" contains widgets
		$modelid = nxs_getpostidbyslug("model");
		// find "serviceset" widgets on this page
		$filter = array
		(
			"postid" => $modelid,
			"widgettype" => "serviceset",
		);
		$servicesetsmetadata = nxs_getwidgetsmetadatainpost_v2($filter);
		$servicesetmetadata = current($servicesetsmetadata);	// get value of first element

	  $servicespostid = $servicesetmetadata["items_genericlistid"];
	  //echo "servicespostid: $servicespostid <br />";
	  
	  // find "services" in the post with id servicespostid
		$filter = array
		(
			"postid" => $servicespostid,
			//"widgettype" => "service", // all types!
		);
		$widgetsmetadata = nxs_getwidgetsmetadatainpost_v2($filter);
		
		$items = nxs_getwidgetsmetadatainpost_v2($filter);
		foreach ($items as $placeholderid => $widgetmeta)
		{
			if ($widgetmeta["type"] == "service")
			{
				$semantic = $widgetmeta["semantic"];
				$semantic_fqn = "services.{$semantic}";
				$the_query = new WP_Query( array( 'post_type' => array('post', 'page'), 'meta_key' => "nxs_semantic", 'meta_value' => $semantic_fqn ) );
				if ( $the_query->have_posts() )
				{
					$the_query->the_post();
					global $post;
				
					$result["services"]["instances"][] = array
					(
						"type" => $widgetmeta["type"],
						"enabled" => $widgetmeta["enabled"],
						"content" => array
						(
							"post_title" => $post->post_title,
							"post_excerpt" => $post->post_excerpt,
							"post_content" => $post->post_content,
							"url" => nxs_geturl_for_postid($post->ID),
						),
					);
					
					wp_reset_postdata();
				}
			}
			else if ($widgetmeta["type"] == "customservice")
			{
				$postid = $widgetmeta["filter_postid"];
				$post = get_post($postid);
				
				$result["services"]["instances"][] = array
				(
					"type" => $widgetmeta["type"],
					"enabled" => $widgetmeta["enabled"],
					"content" => array
					(
						"post_title" => $post->post_title,
						"post_excerpt" => $post->post_excerpt,
						"post_content" => $post->post_content,
						"url" => nxs_geturl_for_postid($post->ID),
					),
				);
			}
			else 
			{
				// 
				echo "huhh?!";
				die();
			}
		}
	  
		/*
		// get the model
		$semantic_fqn = "contentmodel";
		// echo "locating semantic_fqn {$semantic_fqn} for getcontentmodel<br />";
		$the_query = new WP_Query( array( 'post_type' => array('post', 'page'), 'meta_key' => "nxs_semantic", 'meta_value' => $semantic_fqn ) );
		if ( $the_query->have_posts() )
		{
			$the_query->the_post();
			
			global $post;
			$postid = $post->ID;
			$jsonmodel = get_post_meta($postid, "nxs_contentmodeljson", true);
			$model = json_decode($jsonmodel, true);
			return $model;			
		}
		else
		{
			error_log("contentmodel not yet set? (resync to fix)");
			$model = array();
			return $model;
			//
			echo "contentmodel not found";
			die();
		}
		*/
		
		return $result;
	}
	
	function getwidgets($result, $widgetargs)
	{
		if ($nxsposttype == "post") 
		{
			$result[] = array("widgetid" => "semantic");
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
	
	function instance_init()
	{
		// 
		nxs_lazyload_plugin_widget(__FILE__, "semantic");
		
		
		
		// debugging handling
		if ($_REQUEST["resync"] == "true")
		{
			$this->resynccontentmodel();
		}
		if ($_REQUEST["changesiteid"] != "")
		{
			$this->changesiteid($_REQUEST["changesiteid"]);
		}
		if ($_REQUEST["dumpcontentmodel"] == "true")
		{
			echo "model:<br />";
			$contentmodel = $this->getcontentmodel();
			var_dump($contentmodel);
			die();
		}
	}
	
	function __construct()
  {
  	add_filter( 'init', array($this, "instance_init"), 31, 1);
		add_action( 'nxs_getwidgets',array( $this, "getwidgets"), 20, 2);
		add_shortcode( 'nxsarticlecontent', array($this, "sc_nxsarticlecontent"), 20, 2);
		add_shortcode( 'nxscomment', array($this, "sc_nxscomment"), 20, 2);
		add_filter("nxs_f_shouldrenderaddnewrowoption", array($this, "f_shouldrenderaddnewrowoption"), 1, 1);
		add_action('admin_head', array($this, "instance_admin_head"), 30, 1);
  }
  
	/* ---------- */
}

global $businesssite_instance;
$businesssite_instance = new businesssite_instance();