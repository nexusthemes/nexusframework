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
				$the_query = new WP_Query( array( 'post_type' => 'post', 'meta_key' => "nxs_semantic", 'meta_value' => $semantic_fqn ) );
				if ( $the_query->have_posts() )
				{
					echo "semantic_fqn $semantic_fqn already exists, updating ... <br />";
					$the_query->the_post();
					global $post;
					
					$title_old = $post->post_title;
					if ($title != $title_old)
					{
						echo "semantic_fqn $semantic_fqn old title {$title_old} becomes new title {$title} <br />";
						// update title, slug and categories
						$my_post = array
						(
							'ID' => $post->ID,
							'post_title' => $title,
						);
						wp_update_post($my_post);
						// update in mem
						$post->post_title = $title;
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
						$my_post = array
						(
							'ID' => $post->ID,
							'post_name' => $slug,
						);
						wp_update_post($my_post);
						// update in mem
						$post->post_name = $slug;
					}
					else
					{
						echo "semantic_fqn $semantic_fqn slug unchanged ($slug)<br />";
					}
					
					// excerpt
					echo "found new excerpt; $excerpt<br />";
					$old = $post->post_excerpt;
					if ($excerpt != $old && $excerpt != "")
					{
						echo "semantic_fqn $semantic_fqn old excerpt ({$old}) becomes ({$excerpt}) <br />";
						// update content
						$my_post = array
						(
							'ID' => $post->ID,
							'post_excerpt' => $excerpt,
						);
						wp_update_post($my_post);
						// update in mem
						$post->post_excerpt = $excerpt;
					}
					else
					{
						echo "semantic_fqn $semantic_fqn content unchanged ($slug)<br />";
					}
					
					// content
					echo "found new content; $content<br />";
					$content_old = $post->post_content;
					if ($content != $content_old && $content != "")
					{
						echo "semantic_fqn $semantic_fqn old content ({$content_old}) becomes ({$content}) <br />";
						// update content
						$my_post = array
						(
							'ID' => $post->ID,
							'post_content' => $content,
						);
						wp_update_post($my_post);
						// update in mem
						$post->post_content = $content;
					}
					else
					{
						echo "semantic_fqn $semantic_fqn content unchanged ($slug)<br />";
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
					$args["nxsposttype"] = nxs_getnxsposttype_by_wpposttype("post");
					//$args["createpage"] = "true";
					$args["postmetas"] = array("nxs_semantic" => $semantic_fqn);
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
				$the_query = new WP_Query( array( 'post_type' => 'post', 'meta_key' => "nxs_semantic", 'meta_value' => $semantic_fqn ) );
				if ( $the_query->have_posts() )
				{
					$the_query->the_post();
					global $post;
					$postid = $post->ID;
					$url = nxs_geturl_for_postid($postid);
					
					// enrich the url
					$model[$taxonomy]["instances"][$index]["content"]["url"] = $url;
	
					echo "enriched model {$taxonomy} {$semantic} url to {$url} <br />";
						
					//
					wp_reset_postdata();
				}
			}
		}
		
		// stage 3;  store the (enriched) model
		$jsonmodel = json_encode($model);

		$semantic_fqn = "contentmodel";
		// echo "locating semantic_fqn {$semantic_fqn} for resyncing<br />";
		$the_query = new WP_Query( array( 'post_type' => 'page', 'meta_key' => "nxs_semantic", 'meta_value' => $semantic_fqn ) );
		if ( $the_query->have_posts() )
		{
			echo "semantic_fqn $semantic_fqn already exists, updating ... ";
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
			$args["postmetas"] = array("nxs_semantic" => $semantic_fqn, "nxs_contentmodeljson" => $jsonmodel);
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
		// get the model
		$semantic_fqn = "contentmodel";
		// echo "locating semantic_fqn {$semantic_fqn} for getcontentmodel<br />";
		$the_query = new WP_Query( array( 'post_type' => 'page', 'meta_key' => "nxs_semantic", 'meta_value' => $semantic_fqn ) );
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
	}
	
	function getwidgets($result, $widgetargs)
	{
		$result[] = array("widgetid" => "semantic");
		
		return $result;
	}
	
	function instance_init()
	{
		nxs_lazyload_plugin_widget(__FILE__, "semantic");
		
		
		
		// 
		
		//
		if ($_REQUEST["resync"] == "true")
		{
			$this->resynccontentmodel();
		}
		if ($_REQUEST["changesiteid"] != "")
		{
			$this->changesiteid($_REQUEST["changesiteid"]);
		}
	}
	
	function __construct()
  {
  	add_filter( 'init', array($this, "instance_init"), 1, 1);
		add_action( 'nxs_getwidgets',array( $this, "getwidgets"), 20, 2);
		add_shortcode( 'nxsarticlecontent', array($this, "sc_nxsarticlecontent"), 20, 2);
  }
  
	/* ---------- */
}

global $businesssite_instance;
$businesssite_instance = new businesssite_instance();