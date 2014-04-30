<?php
function nxs_webmethod_geturl() 
{
	extract($_REQUEST);
	
	$result = array();
 	
 	if ($destination == "")
 	{
 		nxs_webmethod_return_nack("destination niet meegegeven");
 	}
 	
 	if ($destination == "header")
 	{
 		if ($nxsrefurlspecial == "")
 		{
 			nxs_webmethod_return_nack("nxsrefurlspecial niet meegegeven");
 		}
 		if ($postid == "")
 		{
 			nxs_webmethod_return_nack("postid niet meegegeven");
 		}
 		
 		$url = get_home_url() . "/?nxs_header=" . urlencode(nxs_getslug_for_postid($postid)) . "&nxsrefurlspecial=" . $nxsrefurlspecial;
 		$result["url"] = $url;
 	}
 	else if ($destination == "footer")
 	{
 		if ($nxsrefurlspecial == "")
 		{
 			nxs_webmethod_return_nack("nxsrefurlspecial niet meegegeven");
 		}
 		if ($postid == "")
 		{
 			nxs_webmethod_return_nack("postid niet meegegeven");
 		}
 		
 		$url = get_home_url() . "/?nxs_footer=" . urlencode(nxs_getslug_for_postid($postid)) . "&nxsrefurlspecial=" . $nxsrefurlspecial;
 		$result["url"] = $url;
 	}
 	else if ($destination == "sidebar")
 	{
 		if ($nxsrefurlspecial == "")
 		{
 			nxs_webmethod_return_nack("nxsrefurlspecial niet meegegeven");
 		}
 		if ($postid == "")
 		{
 			nxs_webmethod_return_nack("postid niet meegegeven");
 		}
 		
 		$url = get_home_url() . "/?nxs_sidebar=" . urlencode(nxs_getslug_for_postid($postid)) . "&nxsrefurlspecial=" . $nxsrefurlspecial;
 		$result["url"] = $url;
 	}
 	else if ($destination == "pagelet")
 	{
 		if ($nxsrefurlspecial == "")
 		{
 			nxs_webmethod_return_nack("nxsrefurlspecial niet meegegeven");
 		}
 		if ($postid == "")
 		{
 			nxs_webmethod_return_nack("postid niet meegegeven");
 		}
 		
 		$url = get_home_url() . "/?containerpostid=" . $containerpostid . "&nxs_pagelet=" . urlencode(nxs_getslug_for_postid($postid)) . "&nxsrefurlspecial=" . $nxsrefurlspecial;
 		$result["url"] = $url;
 	}
 	else if ($destination == "postid")
 	{
 		if ($postid == "")
 		{
 			nxs_webmethod_return_nack("postid niet meegegeven");
 		}
 		
 		$url = nxs_geturl_for_postid($postid);
 		$result["url"] = $url;
 	}
 	else
 	{
 		nxs_webmethod_return_nack("destination not supported;" . $destination);
 	}

	nxs_webmethod_return_ok($result);
}
?>