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
?>