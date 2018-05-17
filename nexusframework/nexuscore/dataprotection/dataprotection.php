<?php

function nxs_dataprotection_getcanonicalactivity($component)
{
	$result = strtolower(preg_replace('/[^A-Za-z0-9]/', '_', $component)); // Removes special chars.
	return $result;
}

function nxs_dataprotection_buildrecursiveprotecteddata($args)
{
	$result = array();
	
	$rootactivity = $args["rootactivity"];
	$queue = array($rootactivity);
	$processed = array();
	
	while($currentactivity = array_pop($queue))
	{			
		$currentactivity = nxs_dataprotection_getcanonicalactivity($currentactivity);
		$processed[] = $currentactivity;
		
		// delegate the request to the theme for theme-specific gdpr meta
		$functionnametoinvoke = "nxs_dataprotection_{$currentactivity}_getprotecteddata";
		if (function_exists($functionnametoinvoke))
		{
			$currentresult = call_user_func($functionnametoinvoke, $args);
		
			// enqueue subactivities
			if (true)
			{
				$subactivities = $currentresult["subactivities"];
				foreach ($subactivities as $subactivity)
				{						
					$subactivity = nxs_dataprotection_getcanonicalactivity($subactivity);
					
					$currentresult["dataprocessingdeclarations"]["includes"][] = $subactivity;
					
					if (!in_array($subactivity, $processed))
					{
						$queue[] = $subactivity;
					}
					else
					{
						// 
						$result["warning"][] = "activity was enqueued multiple times (only the first time it was processed); $subactivity";
					}
				}
			}
							
			// store result of the current component
			$result["activities"][$currentactivity] = $currentresult["dataprocessingdeclarations"];
		}
		else
		{
			$result["errors"][] = "no valid gdprmeta implemented ( {$functionnametoinvoke} )";
		}
	}
	
	return $result;
}

function nxs_dataprotection_getexplicitconsentcookiename()
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

function nxs_dataprotection_isactionallowed($action)
{
	$result = false;
	
	$dataprotectiontype = nxs_dataprotection_gettypeofdataprotection();
	if ($dataprotectiontype == "explicit_content_by_cookie_wall")
	{
		if (nxs_browser_iscrawler())
		{
			// its a bot; no cookie wall; using cached page is ok
			$result = true;
		}
		else
		{
			// its not a bot, check if the cookie is set
			$cookiename = nxs_dataprotection_getexplicitconsentcookiename();
			$r = $_COOKIE[$cookiename];
			
			if ($r != "")
			{
				$result = true;
			}
		}
	}
	else if ($dataprotectiontype == "none")
	{
		// proceed; no data protection is enforced by the owner of the site
		$result = true;
	}
	else
	{
		nxs_webmethod_return_nack("error; nxs_dataprotection_isallowedtoreceivedcachedpage; unsupported dataprotectiontype ($dataprotectiontype)");
	}
	
	return $result;
}

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
			$cookiename = nxs_dataprotection_getexplicitconsentcookiename();
			$r = $_COOKIE[$cookiename];
			
			if ($r == "")
			{
				// nope, no consent yet
				
				$jquery_url = nxs_getframeworkurl() . "/js/jquery-1.11.1/jquery.min.js";
				?>
				<html>
					<head>
						<script data-cfasync="false" type="text/javascript" src="<?php echo $jquery_url; ?>"></script>
						<?php nxs_setjQ_nxs(); ?>
						<script>
						function nxs_js_isuserloggedin() { return <?php if (is_user_logged_in()) { echo "true"; } else { echo "false"; } ?>; } 
						function nxs_js_gettrans(msg)	{ return msg; }
						function nxs_js_enableguieffects() { return false; }
						function nxs_js_isinfrontend() { return <?php echo (!is_admin()); ?>; }
						function nxs_js_getframeworkurl() { return "<?php echo nxs_getframeworkurl(); ?>"; }
						function nxs_js_userhasadminpermissions() { return <?php if (nxs_has_adminpermissions()) { echo "true"; } else { echo "false"; } ?>; }
						</script>
						<script data-cfasync="false" type="text/javascript" src="<?php echo nxs_getframeworkurl(); ?>/nexuscore/includes/nxs-script.js?v=<?php echo nxs_getthemeversion(); ?>"></script>
					</head>
					<body>
						first give explicit consent to our terms and conditions, thanks
						<form id='nxsdataprotectionform'>
							<input type='checkbox' name='nxsexplicitconsent' id='nxsexplicitconsent' />
							<input type='submit' />
						</form>
						<script>
							
							$('#nxsdataprotectionform').submit
							(
								function(ev) 
								{
								    ev.preventDefault(); // to stop the form from submitting
								    var isconfirmed = document.getElementById("nxsexplicitconsent").checked;
								    nxs_js_store_expliciet_cookie_consent(isconfirmed);
								}
							);
							
							function nxs_js_store_expliciet_cookie_consent(isconfirmed)
							{
								if (isconfirmed)
								{
									// one year
									var expiretime = 365 * 24 * 60 * 60 * 1000;
									// set cookie
									nxs_js_setcookie("<?php echo $cookiename; ?>", 'confirmed', expiretime);
									
									// reload the page 
									location.reload();
								}
								else
								{
									alert('you cannot proceed if you dont agree');
								}
							}
						</script>
					</body>
				</html>
				<?php
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
		nxs_webmethod_return_nack("error; nxs_dataprotection_enforcedataprotectiontypeatstartwebrequest; unsupported dataprotectiontype ($dataprotectiontype)");
	}
}