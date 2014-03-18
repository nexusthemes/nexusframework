<?php
function nxs_webmethod_updatewpoption() 
{
	extract($_REQUEST);

	if ($key == "")
	{
		nxs_webmethod_return_nack("key not set");
	}
	
	$keyallowed = false;
	if ($key == "nxs_do_postthemeactivation")
	{
		$keyallowed = true;
	}
	
	update_option($key, $value);
	
	$responseargs = array();	
	nxs_webmethod_return_ok($responseargs);
}
?>