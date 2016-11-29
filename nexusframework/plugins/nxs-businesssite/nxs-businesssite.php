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
	
	function getservicescontainerid()
	{
		$servicescontainerid = $this->getservicescontainerid_internal();
		if ($servicescontainerid === false) 
		{ 
			$result = $this->createnewservicescontainer_internal();
			if ($result["RESULT"] != "OK") { echo "unexpected;"; var_dump($result); die(); }
			$servicescontainerid = $result["postid"];
		}
		return $servicescontainerid;
	}
	
	function getservicescontainerid_internal()
	{
		// published pagedecorators
		$publishedargs = array();
		$publishedargs["post_status"] = "publish";
		$publishedargs["post_type"] = "nxs_genericlist";
		$publishedargs["nxs_tax_subposttype"] = "serviceset";
		
		$publishedargs["orderby"] = "post_date";//$order_by;
		$publishedargs["order"] = "DESC"; //$order;
		$publishedargs["numberposts"] = -1;	// allemaal!
	  $pages = get_posts($publishedargs);
	  
	  $result = false;
	  if (count($pages) >= 1) 
	  {
	  	$result = $pages[0]->ID;
	  }
	  
	  return $result;
	}
	
	function createnewservicescontainer_internal()
	{
		//$postid = getservicescontainerid();
		//if ($postid !== false) { echo "servicescontainer already exists! ($postid)"; die(); } 
	
		$subargs = array();
		$subargs["nxsposttype"] = "genericlist";
		$subargs["nxssubposttype"] = "serviceset";	// NOTE!
		$subargs["poststatus"] = "publish";
		$subargs["titel"] = nxs_l18n__("Service Set", "nxs_td") . " " . nxs_generaterandomstring(6);
		$subargs["slug"] = $subargs["titel"];
		$subargs["postwizard"] = "defaultgenericlist";
		
		$response = nxs_addnewarticle($subargs);
		if ($response["result"] != "OK")
		{
			echo "failed to create servicescontainer?!";
			die();
		}
		
		// todo: perhaps initialize with some defaults using the server?
		
		return $response;
	}
	
	function getcontentmodel()
	{
		$result = array();
		
		// this invocation will make it, if its empty
		$servicescontainerid = $this->getservicescontainerid();
		
	  $result["services"]["postid"] = $servicescontainerid;
	  $result["services"]["url"] = nxs_geturl_for_postid($servicescontainerid);
	  
	  //echo "servicescontainerid: $servicescontainerid <br />";
	  
	  // find "services" in the post with id servicescontainerid
		$filter = array
		(
			"postid" => $servicescontainerid,
			//"widgettype" => "service", // all types!
		);
		$widgetsmetadata = nxs_getwidgetsmetadatainpost_v2($filter);
		
		$countservicesenabled = 0;
		$items = nxs_getwidgetsmetadatainpost_v2($filter);
		foreach ($items as $placeholderid => $widgetmeta)
		{
			if ($widgetmeta["type"] == "service")
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
				
				if ($widgetmeta["enabled"] != "")
				{
					$countservicesenabled++;
				}
			}
			else 
			{
				// 
				echo "huhh?!";
				die();
			}
		}
		
		$result["services"]["countenabled"] = $countservicesenabled;
	  
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
		add_shortcode( 'nxscomment', array($this, "sc_nxscomment"), 20, 2);
		add_filter("nxs_f_shouldrenderaddnewrowoption", array($this, "f_shouldrenderaddnewrowoption"), 1, 1);
		add_action('admin_head', array($this, "instance_admin_head"), 30, 1);
  }
  
	/* ---------- */
}

global $businesssite_instance;
$businesssite_instance = new businesssite_instance();