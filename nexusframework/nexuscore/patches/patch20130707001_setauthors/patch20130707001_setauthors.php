<?php

// upgrade de auteur van iedere post
function nxs_apply_patch20130707001_setauthors()
{
	global $wpdb;

	// display users
	$q = "
	SELECT ID, display_name FROM $wpdb->users
	";
	$dbresult = $wpdb->get_results($q, ARRAY_A );
	var_dump($dbresult);
	
	echo "patch finished";
  
  return $output;
}

?>