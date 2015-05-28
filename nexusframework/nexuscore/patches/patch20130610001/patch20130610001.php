<?php

// upgrade iedere post in de site naar v3
function nxs_apply_patch20130610001()
{
	// delegate the upgrade request to specific function
	require_once(NXS_FRAMEWORKPATH . '/nexuscore/includes/nxsupgrader.php');
	
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
  		$override = "true";
			nxs_upgrade_post($override, $postid);
			
			$url = nxs_geturl_for_postid($postid);
			echo nxs_gettimestampasstring();
			echo "<br />";
			echo "<a href='$url' target='_blank'>{$postid}</a>";
			echo "<br />";
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