<?php
global $post;

nxs_handle_maintenancemode();
nxs_handle_404();
nxs_handle_homepagerequest();
nxs_handlebasicrequestpermission();

rewind_posts();

//
//
//
if (is_singular())
{
	// Iterate the post index in The Loop. Retrieves the next post, sets up the post, 
	// sets the 'in the loop' property to true.
	the_post();
	
	$nxsposttype = nxs_getnxsposttype_by_wpposttype($post->post_type);
		
	$tag = 'nxs_process_pagetemplate_' . $nxsposttype;
	if (has_action($tag))
	{
		do_action($tag);
	}
	else
	{
		// default framework generic
		$filetobeincluded = NXS_FRAMEWORKPATH . '/page-template-' . $nxsposttype . '.php';
		if (file_exists($filetobeincluded))
		{
			// rendering continues by the included file...
			require_once($filetobeincluded);
		}
		else
		{
			nxs_webmethod_return_nack("No hook found, using default framework implementation. No file found in framework to handle this nxsposttype;" . $filetobeincluded);
		}
	}
}
else if (is_archive())
{
	$args = array();
	$pagetemplate = "archive";		
	nxs_renderpagetemplate($pagetemplate, $args);
}
else
{
	// this happens if a plugin has a specific URL 
	// rewritten to a specific template include.
	// in that case we will render that specific content,
	// even though the front end editor features will be suppressed	
	$args = array();
	$pagetemplate = "webpage";		
	nxs_renderpagetemplate($pagetemplate, $args);
}

function nxs_maintenance_getretryafter()
{
	$maintenancedurationinsecs = nxs_getmaintenancedurationinsecs();
	if (!isset($maintenancedurationinsecs) || $maintenancedurationinsecs == 0 || $maintenancedurationinsecs == "")
	{
		$maintenancedurationinsecs = 60 * 60;	// 1 hour
	}
	return $maintenancedurationinsecs;
}

function nxs_handle_maintenancemode()
{
	if (nxs_issiteinmaintenancemode())
	{
		header("Retry-After: " . nxs_maintenance_getretryafter());
		header("HTTP/1.0 503 Maintenance mode");

		// if the user is not logged on
		if (!is_user_logged_in() && !nxs_iswploginpage())
		{
			if (nxs_hastemplateproperties())
			{
				$templateproperties = nxs_gettemplateproperties();
				if ($templateproperties["lastmatchingrule"] == "busrulemaintenance")
				{
					// the templateproperties will render the correct output
				}
				else if ($templateproperties["lastmatchingrule"] == "busrule404")
				{
					// the templateproperties will render the correct output
				}
				else
				{
					nxs_webmethod_return_nack("Website is in maintenance mode; add a maintenance business rule, or move it up in the hierarchy display to customize this message");
				}
			}
			else
			{
				nxs_webmethod_return_nack("Sorry, website is in maintenance mode; add a business rule set to improve the visualization");
			}
		}
		else
		{
			// for authenticated users the maintenance mode is ignored
		}
	}
	else
	{
		// site is not in maintenance mode
	}
}

function nxs_handle_404()
{
	if (is_404())
	{
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found Page Template");
		header("Status: 404 Not Found Page Template 2");
		$_SERVER['REDIRECT_STATUS'] = 404;
		
		if (nxs_hastemplateproperties())
		{
			$templateproperties = nxs_gettemplateproperties();
			if ($templateproperties["lastmatchingrule"] == "busrule404")
			{
				// the templateproperties will render the correct output
			}
			else if ($templateproperties["lastmatchingrule"] == "busrulemaintenance")
			{
				// the templateproperties will render the correct output
			}
			else
			{
				nxs_webmethod_return_nack("Page not found; add a 404 business rule, or move it up in the hierarchy display to customize this message");
			}
		}
		else
		{
			nxs_webmethod_return_nack("Page not found; add a business rule set to improve the visualization");
		}
	}
	else
	{
		// page is found
	}
}

function nxs_handle_homepagerequest()
{
	if (is_home() || is_front_page())
	{
		if (nxs_hastemplateproperties())
		{
			// template will process the page like it should
		}
		else
		{
			// downwards compatibility
			global $post;
			
			// redirect to homepage if homepage is requested and the url requested is not the homepage
			if (nxs_ishomepage($post->ID))
			{
				$url = get_home_url();
				$correctpath = rtrim(parse_url($url, PHP_URL_PATH), "/");
				$specifiedpath = rtrim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");
				
				if ($specifiedpath == $correctpath)
				{
					// OK
				}
				else
				{
					if ($_SERVER["REQUEST_URI"] == $_SERVER["ORIG_PATH_INFO"])
					{
						// op sommige servers (Pepper) wordt een alternatieve mapping gedaan,
						// de Index.php serveert dan alle pagina, als volgt:
						// http://domain.tld/index.php/pageslug/
						// check if that's the case the $specifiedpath holds "http://domain.tld/index.php",
						// which is in that case a "valid" URL that should not be redirected
						
						// no redirect
					}
					else
					{
						// the post has to be a "page", otherwise endless loop will occur
						$r = nxs_converttopage($post->ID);
						$url = get_home_url() . "?redirectedtohome=true";
						wp_redirect($url, 301);
						die();
					}
				}
			}
		}
	}
}

function nxs_handlebasicrequestpermission()
{
	global $post;
	$nxsposttype = nxs_getnxsposttype_by_wpposttype($post->post_type);
	
	if (is_search())
	{
		// ok
	}
	else
	{
		// high level permission check
		if ($nxsposttype != "post" && $nxsposttype != "page")
		{
			// requires access rights
			if (!is_user_logged_in())
			{
				// redirect to login page
				$url = wp_login_url();
				wp_redirect($url, 301);
				die();			
			}
			else
			{
				if (!nxs_has_adminpermissions())
				{
					$url = nxs_geturl_home();
					wp_redirect($url, 401);	// insufficient rights for current credentials
					die();
				}
				// permission ok
			}
		}
	}
}
?>