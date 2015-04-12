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
	
	if ($key == "nxs_do_postthemeactivation")
	{
		do_action("nxs_activation_contentloaded");
	}
	
	$responseargs = array();	
	nxs_webmethod_return_ok($responseargs);
}
?>