<?php
function nxs_webmethod_exportcontent() 
{	
	extract($_REQUEST);
	
	if ($export == "")
	{
		nxs_webmethod_return_nack("export not set");
	}
	
	if ($export == "poststructureandwidgets")
	{
		if ($postid == "")
		{
			// 
			nxs_webmethod_return_nack("postid not set #247");
		}
		
		header('Content-type: text/plain');
		header('Content-Disposition: attachment; filename="post-export-' . $postid . '-' . nxs_getslug_for_postid($postid) . '.txt"');
		
		echo "# \r\n";
		echo "# Nexus Themes Post export file\r\n";
		echo "# Source " . nxs_geturl_for_postid($postid) . "\r\n";
		echo "# \r\n";
		echo "# @export@poststructureandwidgets@v1.0\r\n";
		echo "# \r\n";
		
		$parsedpoststructure = nxs_parsepoststructure($postid);
		$newpostcontents = nxs_getcontentsofpoststructure($parsedpoststructure);
		$globalid = nxs_get_globalid($postid, false);
		
		echo "# @postid@" . $postid . "\r\n";
		echo "# @globalid@" . $globalid . "\r\n";
		echo "# @poststructure\r\n";
		echo $newpostcontents;
		if (!nxs_stringendswith("\r\n", $newpostcontents))
		{
			echo "\r\n";
		}
		echo "# @widgets\r\n";
		
		$rowindex = 0;
		foreach ($parsedpoststructure as $pagerow)
		{
			$content = $pagerow["content"];		
			$placeholderids = nxs_parseplaceholderidsfrompagerow($content);
	
			foreach ($placeholderids as $placeholderid)
			{
				// get all widget meta
				$widgetmetadata = nxs_getwidgetmetadata($postid, $placeholderid);
				$serialized = json_encode($widgetmetadata);
				echo "# @widget@" . $placeholderid . "\r\n";				
				echo $serialized;
				if (!nxs_stringendswith("\r\n", $serialized))
				{
					echo "\r\n";
				}
			}
		}
		echo "# @end\r\n";		
		die();
	}
	else if ($export == "siteallpoststructuresandwidgets")
	{
		header("Content-type: text/plain");
		header("Content-Disposition: attachment; filename='site-export-" . nxs_getsiteslug() . ".txt");
		
		echo "# \r\n";
		echo "# Nexus Themes Site export file\r\n";
		echo "# Source " . nxs_geturl_home() . "\r\n";
		echo "# \r\n";
		echo "# @export@siteallpoststructuresandwidgets@v1.0\r\n";
		echo "# \r\n";

		// nxs_settings expliciet niet; deze bevat geen nxs_structure		
		$publishedargs["post_type"] = array("post", "page", "nxs_footer", "nxs_header", "nxs_sidebar", "nxs_menu", "nxs_slideset", "nxs_admin", "nxs_list", "nxs_pagelet");
		$publishedargs["orderby"] = "post_date";
		$publishedargs["order"] = "DESC";	
		$publishedargs["numberposts"] = -1;	// allemaal!
				
	  $posts = get_posts($publishedargs);
	  foreach ($posts as $currentpost)
	  {
	    $postid = $currentpost->ID;

			$parsedpoststructure = nxs_parsepoststructure($postid);
			$newpostcontents = nxs_getcontentsofpoststructure($parsedpoststructure);
			$globalid = nxs_get_globalid($postid, false);	// indien nog niet bestond, maak 'm dan maar aan
			
			echo "# @postid@" . $postid . "\r\n";
			echo "# @globalid@" . $globalid . "\r\n";
			echo "# @poststructure\r\n";
			echo $newpostcontents;
			if (!nxs_stringendswith("\r\n", $newpostcontents))
			{
				echo "\r\n";
			}
			echo "# @widgets\r\n";
			
			$rowindex = 0;
			foreach ($parsedpoststructure as $pagerow)
			{
				$content = $pagerow["content"];		
				$placeholderids = nxs_parseplaceholderidsfrompagerow($content);
		
				foreach ($placeholderids as $placeholderid)
				{
					// get all widget meta
					$widgetmetadata = nxs_getwidgetmetadata($postid, $placeholderid);
					$serialized = json_encode($widgetmetadata);
					echo "# @widget@" . $placeholderid . "\r\n";				
					echo $serialized;
					if (!nxs_stringendswith("\r\n", $serialized))
					{
						echo "\r\n";
					}
				}
			}
		}
		
		echo "# @end\r\n";		
		die();
	}
	else
	{
		nxs_webmethod_return_nack("export not supported;" . $export);
	}
}
?>