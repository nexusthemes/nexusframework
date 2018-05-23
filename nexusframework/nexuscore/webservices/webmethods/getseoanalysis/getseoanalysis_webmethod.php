<?php

function nxs_orderseo($a, $b)
{
  if ($a["val"] == $b["val"]) 
  {
  	return 0;
  }
  return ($a["val"] < $b["val"]) ? -1 : 1;
}

function nxs_webmethod_getseoanalysis() 
{
	global $post;
	
	extract($_REQUEST["inputparameters"]);
	
	$result = array();
 	
	if ($postid == "")
	{
		nxs_webmethod_return_nack("postid niet meegegeven");
	}
	
	if (!defined('WPSEO_PATH'))
	{
		nxs_webmethod_return_nack("please install the WordPress Seo plugin by Yoast (1)");
	}
		
	if (!class_exists ("WPSEO_Metabox"))
	{
		require WPSEO_PATH.'admin/class-metabox.php';
	}
	if (!class_exists ("WPSEO_Admin"))
	{
		require WPSEO_PATH.'admin/class-admin.php';
	}
	
	require_once ABSPATH . 'wp-admin/includes/post.php';
	
	$result["focuskw"] = wpseo_get_value('focuskw', $postid);
	if ($result["focuskw"] === false)
	{
		$result["focuskw"] = "";
	}
	$result["title"] = wpseo_get_value('title', $postid);
	if ($result["title"] === false)
	{
		$result["title"] = "";
	}
	$result["metadesc"] = wpseo_get_value('metadesc', $postid);
	if ($result["metadesc"] === false)
	{
		$result["metadesc"] = "";
	}
	
	if (class_exists("wpseo_Video_Sitemap"))
	{
		// if the video seo plugin is installed,
		// the wpseo_filter does not take into consideration the snippet_preview of the 
		$wpseo_Video_Sitemap = new wpseo_Video_Sitemap();
		add_filter( 'wpseo_snippet', array($wpseo_Video_Sitemap, 'snippet_preview' ), 10, 3 );
	}
	
	if (class_exists("WPSEO_Admin"))
	{
		// >= 1.5 of Yoast
		$GLOBALS['wpseo_admin'] = new WPSEO_Admin;
		
		$posttoanalyze = get_post( $postid);
		$post = $posttoanalyze;
		$wpseo_metabox = new WPSEO_Metabox();
		$calculatedresults = $wpseo_metabox->calculate_results($posttoanalyze);
	}
	else
	{
		// < 1.5
		$wpseo_metabox = new WPSEO_Metabox();
		$calculatedresults = $wpseo_metabox->calculate_results();
	}
	
		
	// result can be a wp_error, for example, if the focuskey is not set
	if (is_wp_error($calculatedresults))
	{
		$errorcodes = $calculatedresults->get_error_codes();
		$result["errors"] = array();
		foreach ($errorcodes as $currentcode)
		{
			$thiserrors = $calculatedresults->get_error_messages($currentcode);
			$thiserror = $calculatedresults->get_error_message($currentcode);
			
			if (count($thiserrors) > 1) 
			{
				$thiserror .= " (only showing first, skipping remaining errors)";
			}
			
			$result["wperrors"][] = $thiserror;
		}
	}
	else
	{	
		$snippet = $wpseo_metabox->snippet();
		$result["snippet"] = $snippet;
				
		// enrich the scores
		$i = 0;
		foreach ($calculatedresults as $currentcalculatedresultkey => $currentcalculatedresult)
		{
			$value = $calculatedresults[$currentcalculatedresultkey]["val"];
			$score = wpseo_translate_score($value);
			
			$calculatedresults[$currentcalculatedresultkey]["indicator"] = $score;
			$i++;
		}
		
		// re-order
		usort($calculatedresults, "nxs_orderseo");

		$result["calculatedresults"] = $calculatedresults;
	}	
	
	nxs_webmethod_return_ok($result);
}

function nxs_dataprotection_nexusframework_webmethod_getseoanalysis_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("webmethod-none");
}

?>