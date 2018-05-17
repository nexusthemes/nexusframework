<?php

//
function nxs_dataprotection_enforcedataprotectiontypeatstartwebrequest()
{
	$dataprotectiontype = nxs_dataprotection_gettypeofdataprotection();
	if ($dataprotectiontype == "explicit_content_by_cookie_wall")
	{
		if (nxs_browser_iscrawler())
		{
			// its a bot; no cookie wall
		}
		else
		{
			// its not a bot, check if the cookie is set
			$r = isset($_COOKIE[nxs_dataprotection_explicit_consent_cookiename()]);
			if ($r == "")
			{
				// nope, no consent yet
				?>
				<html></html>
				<?php
				echo "first give explicit consent, thanks";
				die();
			}
			else
			{
				// proceed; an expliciet consent is found
			}
		}
	}
	else if ($dataprotectiontype == "none")
	{
		// proceed; no data protection is enforced by the owner of the site
	}
	else
	{
		nxs_webmethod_return_nack("error; unsupported dataprotectiontype ($dataprotectiontype)");
	}
}

function nxs_dataprotection_explicit_consent_cookiename()
{
	return "nxs_dataprotection_explicit_content";
}

function nxs_dataprotection_gettypeofdataprotection()
{
	$result = "none";
	if ($_REQUEST["gdpr"] == "test")
	{
		$result = "explicit_content_by_cookie_wall";
	}
	
	return $result;
}