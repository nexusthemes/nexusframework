<?php
function nxs_webmethod_logout() 
{	
	extract($_REQUEST);

	
	if (!is_user_logged_in())
	{
		$previousstate = "notauthenticated";
	}
	else
	{
		$previousstate = "authenticated";	
	}
	
	wp_logout();
	session_unset();
	
	global $nxs_gl_cache_hasadminpermissions;
	$nxs_gl_cache_hasadminpermissions = null;

	$responseargs = array();
	$responseargs["previousstate"] = $previousstate;
	nxs_webmethod_return_ok($responseargs);
}
?>