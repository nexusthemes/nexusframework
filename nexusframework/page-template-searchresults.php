<?php	
	//
	// dit is de serp van de site
	//
	$sitemeta = nxs_getsitemeta();
	$postid = $sitemeta["serp_postid"];
	
	if ($postid != "")
	{
		// check if the current url == the url of the $postid
		// if not, redirect to the url of the $postid
		$specifiedpath = rtrim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");
		$correcturl = nxs_geturl_for_postid($postid);
		$correctpath = rtrim(parse_url($correcturl, PHP_URL_PATH), "/");
		
		if ($specifiedpath != $correctpath)
		{
			// redirect user to the actual search results page
			$url = $correcturl;
			$url = nxs_addqueryparametertourl($url, "s", $_REQUEST["s"]);
			$url = nxs_addqueryparametertourl($url, "trigger", $_REQUEST["trigger"]);
			$url = nxs_addqueryparametertourl($url, "nxsredirecttosearch", "true");
			wp_redirect($url, 301);
			die();
		}
		
		global $nxs_bypasssearchfrontendmod;
		$nxs_bypasssearchfrontendmod = "true";
		
		$wpposttype = nxs_getwpposttype($postid);
		$args = array
		(
		  'post_type' => $wpposttype,
		  'p' => $postid
		 );
		query_posts($args);
		
		if (have_posts())
		{
			$found = true;
			//
			$nxs_bypasssearchfrontendm1od = "false";
			
			require(NXS_FRAMEWORKPATH . '/page-template.php');
		}
		else
		{
			$found = false;
		}
	}	
	else
	{
		$found = false;
	}
	
	if (!$found)
	{
		echo "SERP didn't exist yet...";
		die();
	}
?>