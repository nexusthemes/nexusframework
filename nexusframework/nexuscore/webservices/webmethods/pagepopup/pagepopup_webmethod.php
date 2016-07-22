<?php

function nxs_webmethod_pagepopup() 
{
	extract($_REQUEST);
	
	if ($subaction == "prefetch")
	{	
		$widgetmetadata = nxs_getwidgetmetadata($pagedecoratorid, $placeholderid);
		if ($widgetmetadata["type"] != "pagepopup") { nxs_webmethod_return_nack("unexpected type?" . $widgetmetadata["type"]); }
		
		if ($repeatpopup_scope == "")
		{
			$repeatpopup_scope = "eachrequest";
		}
		
		$shouldshow = "no";
		$shouldsetcookie = false;
		
		$repeatpopup_scope = $widgetmetadata["repeatpopup_scope"];
		$destination_articleid = $widgetmetadata["destination_articleid"];
		
		if ($repeatpopup_scope == "eachrequest")
		{
			$shouldshow = "yes";
		}
		else if ($repeatpopup_scope == "eachnewsession")
		{
			// don't create a new one if its not there yet
			nxs_initializesessionfrombrowsercookieifexists();
			if (!isset($_SESSION["nxs_pagepopup_shown_$pagedecoratorid_$placeholderid"]))
			{
				$shouldshow = "yes";	
			}
		}
		else if ($repeatpopup_scope == "onlyonce")
		{
			if (!isset($_COOKIE["nxs_pagepopup_shown_$pagedecoratorid_$placeholderid"]))
			{
				$shouldsetcookie = true;
				$shouldshow = "yes";
			}
		}
		else
		{
			nxs_webmethod_return_nack("unexpected repeatpopup_scope?" . $repeatpopup_scope);
		}
		
		if (nxs_isdebug())
		{
			//$shouldsetcookie = true;
			$shouldshow = "yes";
		}
		
		$responseargs = array();
		$responseargs["shouldshow"] = $shouldshow;
		if ($shouldshow == "yes")
		{
			// 
			$html = nxs_getrenderedhtml($destination_articleid, "default");
			$responseargs["html"] = $html;
		}	
		
		if ($shouldsetcookie)
		{
			$responseargs["setcookie"] = "nxs_pagepopup_shown_$pagedecoratorid_$placeholderid";
		}
		
		nxs_webmethod_return_ok($responseargs);
	}
	else if ($subaction == "tag")
	{	
		//
		$widgetmetadata = nxs_getwidgetmetadata($pagedecoratorid, $placeholderid);
		if ($widgetmetadata["type"] != "pagepopup") { nxs_webmethod_return_nack("unexpected type?" . $widgetmetadata["type"]); }
		
		$repeatpopup_scope = $widgetmetadata["repeatpopup_scope"];
		$destination_articleid = $widgetmetadata["destination_articleid"];
		
		if ($repeatpopup_scope == "eachnewsession")
		{
			// this creates a new session
			nxs_ensure_sessionstarted();
			$_SESSION["nxs_pagepopup_shown_$pagedecoratorid_$placeholderid"] = true;
			
			$responseargs = array();
			$responseargs["markedasshown"] = "true";
			
			$p = array();
			$p["widgetmetadata"] = $widgetmetadata;
			do_action('nxs_pagepopup_tag', $p);
			
			nxs_webmethod_return_ok($responseargs);
		}
		else
		{
			nxs_webmethod_return_nack("unexpected repeatpopup_scope?" . $repeatpopup_scope);
		}
	}
	else
	{
		nxs_webmethod_return_nack("not yet supported; $subaction");
	}
}

?>