<?php
function nxs_webmethod_login() 
{	
	extract($_REQUEST);
	
	if ($gebruikersnaam == "")
	{
		nxs_webmethod_return_nack("gebruikersnaam niet ingevuld");
	}
	if ($wachtwoord == "")
	{
		nxs_webmethod_return_nack("wachtwoord niet ingevuld");
	}
	
	if (is_user_logged_in())
	{
		wp_logout();
		//nxs_webmethod_return_nack("gebruiker is reeds ingelogd");
	}
	
	// 
	
	$creds = array();
	$creds['user_login'] = $gebruikersnaam;
	$creds['user_password'] = $wachtwoord;
	$creds['remember'] = false;

	$logonsuccesful = false;
	
	$user = wp_signon($creds, false);

	$responseargs = array();
	
	if (is_wp_error($user))
	{
		$responseargs["logonsuccesful"] = false;
		$responseargs["message"] = $user->get_error_message();
	}
	else
	{
		// the is_user_logged_in only works after the next refresh (cookie is set),
		// or when we explicitly set the user
		wp_set_current_user($user->ID);
		if (is_user_logged_in())
		{
			$responseargs["logonsuccesful"] = true;
		}
		else
		{
			$responseargs["logonsuccesful"] = false;
			$responseargs["message"] = "User not logged in";
		}
	}
	
	global $nxs_gl_cache_hasadminpermissions;
	$nxs_gl_cache_hasadminpermissions = null;
		
	
	nxs_webmethod_return_ok($responseargs);
}
?>