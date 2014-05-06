<?php
//
// webmethod extensions
//
function nxs_requirewebmethod($webmethod)
{
	// loads webmethod extensions in memory
	$action = "nxs_ext_inject_webmethod_" . $webmethod;
	if (!has_action($action))
	{
		nxs_webmethod_return_nack("Webmethod not found. Add action: $action");
		/*
		if (nxs_has_adminpermissions())
		{
			// we gaan wel door, iemand kan per ongeluk of met opzet bijv. een plugin hebben uitgeschakeld
			echo "Warning; looks like webmethod '" . $webmethod . " is missing (maybe you deactivated a required plugin or forgot to ftp a new webmethod to your site?)";
		}
		else
		{
			echo "<!-- Warning; looks like webmethod '" . $webmethod . " is missing (maybe you deactivated a required plugin or forgot to ftp a new webmethod to your site?) -->";
		}
		*/
	}
	else
	{
		do_action($action, $webmethod);
	}
}

// theme helper

function nxs_ext_lazyload_theme_webmethod($webmethod)
{
	$action = "nxs_ext_inject_webmethod_" . $webmethod;
	$ishandledbyplugin = has_action($action);
	if ($ishandledbyplugin)
	{
		// it appears this webmethod was already handled by a plugin,
		// we will assume the plugin will override the webmethod of the theme
		// in this case we won't inject the webmethod
	}	
	else
	{
		add_action($action, "nxs_ext_inject_theme_webmethod");
	}
}

function nxs_ext_inject_theme_webmethod($webmethod)
{
	$filetobeincluded = NXS_THEMEPATH . '/webmethods/' . $webmethod . '/webmethod_' . $webmethod . '.php';
	if (!file_exists($filetobeincluded))
	{
		nxs_webmethod_return_nack("file not found;" . $filetobeincluded);
	}
	
	require_once($filetobeincluded);
}

//

function nxs_lazyload_plugin_webmethod($file, $webmethod)
{
	// store file loc in lookup (mem)
	global $nxs_gl_webmethod_file;
	if ($nxs_gl_webmethod_file == null)
	{
		$nxs_gl_webmethod_file = array();
	}
	$nxs_gl_webmethod_file[$webmethod] = $file;
		
	add_action("nxs_ext_inject_webmethod_" . $webmethod, "nxs_inject_plugin_webmethod");
}

// unfortunately we can't use anonymous functions to support older servers running old PHP versions...
function nxs_inject_plugin_webmethod($webmethod)
{
	if ($webmethod == "")
	{
		nxs_webmethod_return_nack("webmethod not set;" . $webmethod);
	}
	
	global $nxs_gl_webmethod_file;
	$file = $nxs_gl_webmethod_file[$webmethod];
	$path = plugin_dir_path($file);
	$filetobeincluded = $path . '/webmethods/' . $webmethod . '/' . $webmethod . '.php';
	if (!file_exists($filetobeincluded))
	{
		nxs_webmethod_return_nack("file not found;" . $filetobeincluded);
	}
	
	require_once($filetobeincluded);
}

?>