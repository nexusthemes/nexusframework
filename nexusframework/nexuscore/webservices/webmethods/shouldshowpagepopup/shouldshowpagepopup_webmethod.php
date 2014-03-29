<?php

function nxs_webmethod_shouldshowpagepopup() 
{
	extract($_REQUEST);
	
	$widgetmetadata = nxs_getwidgetmetadata($pagedecoratorid, $placeholderid);
	if ($widgetmetadata["type"] != "pagepopup") { nxs_webmethod_return_nack("unexpected type?" . $widgetmetadata["type"]); }
	
	if ($repeatpopup_scope == "")
	{
		$repeatpopup_scope = "eachrequest";
	}
	
	$shouldshow = "no";
	$shouldsetcookie = false;
	
	$repeatpopup_scope = $widgetmetadata["repeatpopup_scope"];
	
	if ($repeatpopup_scope == "eachrequest")
	{
		$shouldshow = "yes";
	}
	else if ($repeatpopup_scope == "eachnewsession")
	{
		nxs_ensure_sessionstarted();
		if (!isset($_SESSION["nxs_pagepopup_shown"]))
		{
			$_SESSION["nxs_pagepopup_shown"] = true;
			$shouldshow = "yes";	
		}
	}
	else if ($repeatpopup_scope == "onlyonce")
	{
		if (!isset($_COOKIE["nxs_cookie_pagepopup_shown"]))
		{
			$shouldsetcookie = true;
			$shouldshow = "yes";
		}
	}
	else
	{
		nxs_webmethod_return_nack("unexpected repeatpopup_scope?" . $repeatpopup_scope);
	}
	
	//var_dump($_COOKIE);
	//nxs_webmethod_return_nack("lala?" . $repeatpopup_scope);

	
	$responseargs = array();
	$responseargs["shouldshow"] = $shouldshow;
	
	if ($shouldsetcookie)
	{
		$responseargs["setcookie"] = "nxs_cookie_pagepopup_shown";
	}
	
	nxs_webmethod_return_ok($responseargs);
}

?>