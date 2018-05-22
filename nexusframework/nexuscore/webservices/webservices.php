<?php

// this function is responsible for handling nxs webmethods (webservices)
function nxs_handlewebmethods()
{
	if (nxs_isnxswebservicerequest())
	{
		if (!nxs_showphpwarnings())
		{
			// 2013 08 03; fixing unwanted WP3.6 notice errors
			// third party plugins and other php code (like sunrise.php) can
			// cause warnings that mess up the output of the webmethod
			// for example when activating the theme
			// to solve this, at this stage we clean the output buffer
			nxs_saveobclean();
		}

		// flag we are processing a webmethod
		define('NXS_DEFINE_NXSWEBWEBMETHOD', true);

		// allow system to do pluggable actions		
		do_action('nxs_action_webmethod_init');
		
		// delegate processing of webmethod
		require(NXS_FRAMEWORKPATH . '/nexuscore/webservices/nxs-ajax.php');
		
		// ensure no further processing occurs
		die();
	}
}

// both anonymous and authenticated webmethods are detected here
add_action('wp_ajax_nxs_ajax_webmethods', 'nxs_ajax_webmethods');
add_action('wp_ajax_nopriv_nxs_ajax_webmethods', 'nxs_ajax_webmethods');
function nxs_ajax_webmethods() 
{
	$webmethod = $_REQUEST["webmethod"];

	// AJAX webmethods that make us of FormData use POST instead of REQUEST
	if ($_POST["webmethod"] == "formboxsubmit")
	{
		$webmethod = $_POST["webmethod"];
	}

	if ($webmethod == "")
	{
		nxs_webmethod_return_nack("webmethod not specified;" . $webmethod);
	}
	
	// before we handle the request, we decode the values that are encoded with 
	// the nxs_js_getescapeddictionary js function (here: *426759487653456)
	
	// otherwise the values like aa'aa would be returned as aa\'aa
	$_POST = nxs_urldecodearrayvalues($_POST);
	$_REQUEST = nxs_urldecodearrayvalues($_REQUEST);

	// check permissions
	if (!nxs_has_adminpermissions())
	{
		// by default there's no access
		$hasaccess = false;
		
		if ($webmethod == "getsheet")
		{
			// getsheet doesnt use POST so we don't have to check for that
			$clientpopupsessioncontext = $_REQUEST["clientpopupsessioncontext"];			
			$contextprocessor = $clientpopupsessioncontext["contextprocessor"];
			$sheet = $clientpopupsessioncontext["sheet"];
			
			// load the context processor if its not yet loaded
			nxs_requirepopup_contextprocessor($contextprocessor);
			
			// TODO: the following logic actually could/should be delegated to the contextprocessor...
			
			if ($contextprocessor == "site")
			{
				if ($sheet == "loginhome")
				{
					// anonieme gebruikers moeten kunnen inloggen
					$hasaccess = true;
				}
				else if ($sheet == "logouthome")
				{
					// anonieme gebruikers moeten kunnen uitloggen
					$hasaccess = true;
				}
				else if ($sheet == "gallery")
				{
					// anonieme gebruikers moeten gallery popups kunnen zien/gebruiken
					$hasaccess = true;
				}
				else if ($sheet == "customhtml")
				{
					// anonieme gebruikers moeten customhtml popups kunnen zien/gebruiken
					$hasaccess = true;
				}
				else
				{
					nxs_webmethod_return_nack("no access granted for sheet $sheet");
				}
			}
			else if ($contextprocessor == "gallerybox")
			{
				if ($sheet == "detail")
				{
					// anonieme gebruikers moeten gallery popups kunnen zien/gebruiken
					$hasaccess = true;
				}
				else
				{
					nxs_webmethod_return_nack("no access granted for sheet $sheet");
				}
			}
			else
			{
				// last chance; allow plugins to allow the request by using the following filter
				$pluginargs = array();
				$pluginargs["contextprocessor"] = $contextprocessor;
				$pluginargs["webmethod"] = $webmethod;
				$pluginargs["sheet"] = $sheet;
				$hasaccess = apply_filters("nxs_iswebmethodallowed", $hasaccess, $pluginargs);
			}
		}
		else if ($webmethod == "login")
		{
			$hasaccess = true;
			// ok
		}
		else if ($webmethod == "patch")
		{
			$hasaccess = true;
			// ok
		}
		else if ($webmethod == "logout")
		{
			// lijkt onlogisch, maar dit kan komen als er sprake is van een multi site, gebruikt maken van folders;
			// een gebruik kan in dat geval wel geauthenticeerd zijn, maar geen edit post
			// rechten hebben en behoefte hebben om uit te loggen
			$hasaccess = true;
			// ok
		}
		else if ($webmethod == "addcomment")
		{
			$hasaccess = true;
			// ok
		}
		else if ($webmethod == "exportmedia")
		{
			$hasaccess = true;
			// ok (save-as used in media reference)
		}
		else if ($webmethod == "formboxsubmit")
		{
			$hasaccess = true;
		}
		else if ($webmethod == "lazyloadblog")
		{
			$hasaccess = true;
		}
		else if ($webmethod == "pagepopup")
		{
			$hasaccess = true;
		}
		
		if (!$hasaccess)
		{
			// last chance; allow plugins to allow the request by using the following filter
			$pluginargs = array();
			$pluginargs["webmethod"] = $webmethod;
			$hasaccess = apply_filters("nxs_iswebmethodallowed", $hasaccess, $pluginargs); // gjgj
			
			// allow plugin to allow the request with more specific filter
			$hasaccess = apply_filters("nxs_iswebmethodallowed_$contextprocessor_$webmethod", $hasaccess, $pluginargs);
		}
	}
	else
	{
		// admins are allowed to do anything
		$hasaccess = true;
	}
	
	// perform actual check
	if ($hasaccess == true)
	{
		// OK
	}
	else
	{
		// anything other than an expliciet "true" is false
		nxs_webmethod_return_nack("nxs no access for webmethod [" . $webmethod . "] and cp [$contextprocessor]. If this was not intended, tune the nxs_iswebmethodallowed and/or the nxs_iswebmethodallowed_$contextprocessor_$webmethod filter");
	}
	
	// doorlussen naar handler voor dit sub request
	$filefound = false;
	$filetobeincluded = dirname(__FILE__) . "/webmethods/" . $webmethod . "/" . $webmethod . "_webmethod.php";
	if (file_exists($filetobeincluded))
	{
		$filefound = true;
		require_once($filetobeincluded);
	}
	else
	{
		$filefound = false;
		nxs_requirewebmethod($webmethod);
	}
	
	$functionnametoinvoke = "nxs_webmethod_" . $webmethod;
	if (function_exists($functionnametoinvoke))
	{
		$args = array();
		$result = call_user_func($functionnametoinvoke, $args);

		nxs_webmethod_return_nack("functie eindigt niet in een die commando;" . $webmethod . "/" . $functionnametoinvoke);
	}
	else
	{
		nxs_webmethod_return_nack("function does not exist;" . $webmethod . "/" . $functionnametoinvoke . "/" . $filetobeincluded . "/" . $filefound);
	}
}
?>