<?php
function nxs_webmethod_wipe() 
{	
	extract($_REQUEST);

	if ($context == "placeholder")
	{
		if ($postid == "")
		{
			nxs_webmethod_return_nack("postid not specified /nxs_webmethod_wipe()/");
		}
		if ($placeholderid == "")
		{
			nxs_webmethod_return_nack("placeholderid not specified");
		}
		
		global $nxs_global_current_postid_being_rendered;
		$nxs_global_current_postid_being_rendered = $postid;
		global $nxs_global_current_postmeta_being_rendered;
		$nxs_global_current_postmeta_being_rendered = nxs_get_corepostmeta($postid);
	
		nxs_resetplaceholdermetadata($postid, $placeholderid);
		
		// update items that are derived (based upon the structure and contents of the page, such as menu's)
		nxs_after_postcontents_updated($postid);
	
		//
		// create response
		//
		$responseargs = array();
		nxs_webmethod_return_ok($responseargs);
	}
	else if ($context == "entity")
	{
		if ($postid == "")
		{
			nxs_webmethod_return_nack("postid not specified /nxs_webmethod_wipe()/");
		}
		
		// put post in trash first
		$force_delete = false;
		wp_delete_post($postid, $force_delete);
		
		//
		// create response
		//
		$responseargs = array();
		nxs_webmethod_return_ok($responseargs);
	}
	else
	{
		nxs_webmethod_return_nack("unsupported context; $context");
	}
}

function nxs_dataprotection_nexusframework_webmethod_wipe_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("webmethod-none");
}

?>