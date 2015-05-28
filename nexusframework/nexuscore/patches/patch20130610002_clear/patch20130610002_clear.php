<?php

// WIPES ALL DATA FROM YOUR SYSTEM!
function nxs_apply_patch20130610002_clear()
{
	$timestamp = nxs_gettimestampasstring();
	if ($_REQUEST["yeah_i_know_what_i_am_doing_clear_the_entire_db"] != $timestamp)
	{
		echo "nothing removed as you didn't explicitly agreed ... ";
		echo "if you are sure, please add the 'yeah_i_know_what_i_am_doing_clear_the_entire_db=$timestamp' query parameter";
		die();
	}
	
	nxs_ob_start();
	
	global $wpdb;

	// we do so for truly EACH post (not just post, pages, but also for entities created by third parties,
	// as these can use the pagetemplate concept too. This saves development
	// time for plugins, and increases consistency of data for end-users
	
	echo "<br /><br />";
	
	$tables_to_delete = array();
	$tables_to_delete[] = "comments";
	$tables_to_delete[] = "commentmeta";
	$tables_to_delete[] = "links";
	$tables_to_delete[] = "terms";
	$tables_to_delete[] = "term_relationships";
	$tables_to_delete[] = "term_taxonomy";
	$tables_to_delete[] = "postmeta";
	$tables_to_delete[] = "posts";
	
	foreach ($tables_to_delete as $currenttabletodelete)
	{
		$q = "DELETE FROM " . $wpdb->prefix . $currenttabletodelete;
		echo $q;
		$dbresult = $wpdb->get_results($q, ARRAY_A );
		echo "sql output:<br />";
		var_dump($dbresult);
		echo "<br /><br />";
	}
	
  //
  echo "patch finished";
	
  echo "<br /><br />";
  
  $output = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	echo "output:" . $output;
	
	echo "-----------<br />";
	echo "DO NOT FORGET TO ALSO REMOVE THE WP-CONTENT FILES (IMAGES/ATTACHMENTS!!!!)";
	
	return $output;
}

?>