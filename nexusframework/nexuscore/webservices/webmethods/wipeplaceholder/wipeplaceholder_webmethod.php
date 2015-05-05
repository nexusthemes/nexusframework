<?php
function nxs_webmethod_wipeplaceholder() 
{	
	extract($_REQUEST);

    
	if ($postid == "")
	{
		nxs_webmethod_return_nack("postid not specified /nxs_webmethod_swapplaceholders()/");
	}
	if ($placeholderid == "")
	{
		nxs_webmethod_return_nack("placeholderid not specified");
	}
	
	global $nxs_global_current_postid_being_rendered;
	$nxs_global_current_postid_being_rendered = $postid;
	global $nxs_global_current_postmeta_being_rendered;
	$nxs_global_current_postmeta_being_rendered = nxs_get_postmeta($postid);

	nxs_resetplaceholdermetadata($postid, $placeholderid);
	
	// update items that are derived (based upon the structure and contents of the page, such as menu's)
	nxs_after_postcontents_updated($postid);

	//
	// create response
	//
	$responseargs = array();
	nxs_webmethod_return_ok($responseargs);
}
?>