<?php

// upgrade iedere post in de site naar v3 (phase 2)
function nxs_apply_patch20131011001_turbo()
{
	global $wpdb;

	// we do so for truly EACH post (not just post, pages, but also for entities created by third parties,
	// as these can use the pagetemplate concept too. This saves development
	// time for plugins, and increases consistency of data for end-users
	$q = "
			select ID postid
			from $wpdb->posts
		";
		
	$dbresult = $wpdb->get_results($q, ARRAY_A );
	
	echo nxs_gettimestampasstring();
	echo "<br />";
	if (count($dbresult) > 0)
	{
  	foreach ($dbresult as $dbrow)
  	{
  		$postid = $dbrow["postid"];
  		
  		$parsedpoststructure = nxs_parsepoststructure($postid);
		
			$rowindex = 0;
			foreach ($parsedpoststructure as $pagerow)
			{
				$content = $pagerow["content"];
				
				echo "upgrading row in {$postid}";
				
				$placeholderids = nxs_parseplaceholderidsfrompagerow($content);
				foreach ($placeholderids as $placeholderid)
				{
					// enhance widget's metafields from old - new styled properties, keep old ones intact
					$widgetmetadata = nxs_getwidgetmetadata($postid, $placeholderid);
					
					if ($widgetmetadata["type"] == "turbo")
					{
						/*
						if ($widgetmetadata["background_color"] == "tertiary2-dm")
						{
							$updatedvalues = array();
							$updatedvalues["unistyle"] = "highlightgreen";
							$updatedvalues["type"] = "text";
							$updatedvalues["oldtype"] = "turbo";
							nxs_mergewidgetmetadata_internal($postid, $placeholderid, $updatedvalues);
						}
						else if ($widgetmetadata["background_color"] == "tertiary1-dm")
						{
							$updatedvalues = array();
							$updatedvalues["unistyle"] = "highlightwhite";
							$updatedvalues["type"] = "text";
							$updatedvalues["oldtype"] = "turbo";
							nxs_mergewidgetmetadata_internal($postid, $placeholderid, $updatedvalues);
						}
						else if ($widgetmetadata["background_color"] == "base1-dm")
						{
							$updatedvalues = array();
							$updatedvalues["unistyle"] = "highlightbase1dm";
							$updatedvalues["type"] = "text";
							$updatedvalues["oldtype"] = "turbo";
							nxs_mergewidgetmetadata_internal($postid, $placeholderid, $updatedvalues);
						}*/
						/*
						if ($widgetmetadata["background_color"] == "none")
						{
							$updatedvalues = array();
							$updatedvalues["unistyle"] = "highlightnone";
							$updatedvalues["type"] = "text";
							$updatedvalues["oldtype"] = "turbo";
							nxs_mergewidgetmetadata_internal($postid, $placeholderid, $updatedvalues);
						}
						*/
						
						if ($widgetmetadata["background_color"] == "secondary2-ml")
						{
							$updatedvalues = array();
							$updatedvalues["unistyle"] = "highlightred";
							$updatedvalues["type"] = "text";
							$updatedvalues["oldtype"] = "turbo";
							nxs_mergewidgetmetadata_internal($postid, $placeholderid, $updatedvalues);
						}
						
						
					}
					
					/*
					
					if ($widgetmetadata["article_link"] != "")
					{
						$updatedvalues = array();
						$updatedvalues["destination_articleid"] = $widgetmetadata["article_link"];
						$updatedvalues["destination_articleid_globalid"] = $widgetmetadata["article_link_global_id"];
						nxs_mergewidgetmetadata_internal($postid, $placeholderid, $updatedvalues);
					}
					*/
					/*
					if ($widgetmetadata["image"] != ""&& $widgetmetadata["image_global_id"] != "")
					{
						$updatedvalues = array();
						$updatedvalues["image_imageid"] = $widgetmetadata["image"];
						$updatedvalues["image_imageid_globalid"] = $widgetmetadata["image_global_id"];
						nxs_mergewidgetmetadata_internal($postid, $placeholderid, $updatedvalues);
					}
					*/
				}
			}
		}
	}
  
  //
  
	echo "patch finished";
  
  $output = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	echo "output:" . $output;
	
	return $output;
}

?>