<?php
/**
 * Row and associated meta deletion
 */
function nxs_webmethod_removerow() {

	extract($_REQUEST);

	$result = array();

    global $nxs_global_current_postid_being_rendered;
    global $nxs_global_current_postmeta_being_rendered;

    $nxs_global_current_postid_being_rendered = $postid;
    $nxs_global_current_postmeta_being_rendered = nxs_get_postmeta($postid);

  	if ($postid == "") {
		nxs_webmethod_return_nack("postid not specified /nxs_webmethod_removerow/");
	}

	if ($rowid == "") {
		nxs_webmethod_return_nack("rowid not specified");
	}

    // Fetches widgetid from AJAX call, uses wp built-in function to delete row in DB
    if (isset($widgetid)) {

        $test = str_replace('nxs-widget-', 'nxs_ph_' , $widgetid);
        delete_post_meta($postid, $test);
    }

    $poststructure = nxs_parsepoststructure($postid);

	unset($poststructure[$rowid]);
	$poststructure = array_values($poststructure);
	nxs_storebinarypoststructure($postid, $poststructure);
	
	// update items that are derived (based upon the structure and contents of the page, such as menu's)
	$updateresult = nxs_after_postcontents_updated($postid);
	if ($updateresult["pagedirty"] == "true") {
		$result["pagedirty"] = "true";
	}

	// create response
	nxs_webmethod_return_ok($result);
}