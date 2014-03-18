<?php
function nxs_webmethod_sethomepage() 
{
	extract($_REQUEST);
 	
 	if ($postid == "")
 	{
 		echo "postid empty? (shp)";
 		die();
 	}
 	
 	nxs_sethomepage($postid);
 	
	//
	// create response
	//
	$responseargs = array();
	nxs_webmethod_return_ok($responseargs); 	
}
?>