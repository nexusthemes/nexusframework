<?php
function nxs_webmethod_sethomepage() 
{
	extract($_REQUEST);
 	
 	if ($postid == "")
 	{
		nxs_webmethod_return_nack("postid empty? (shp)");
 	}
 	
 	nxs_sethomepage($postid);
 	
	//
	// create response
	//
	$responseargs = array();
	nxs_webmethod_return_ok($responseargs); 	
}

function nxs_dataprotection_nexusframework_webmethod_sethomepage_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("webmethod-none");
}

?>