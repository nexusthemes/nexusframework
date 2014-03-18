<?php
function nxs_webmethod_removecategory() 
{	
	extract($_REQUEST);
	
	if ($catid == "")
	{
		nxs_webmethod_return_nack("catid niet gevuld");
	}
	
	$result = wp_delete_category($catid);
	if ($result === true)
	{
		// ok
	}
	else
	{
		nxs_webmethod_return_nack("unable to delete category; " . $name);
	}
	
	//
	// create response
	//

	$responseargs = array();
	nxs_webmethod_return_ok($responseargs);
}
?>