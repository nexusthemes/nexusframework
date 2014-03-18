<?php

//
// pagetemplate extensions
//

// theme helper

function nxs_ext_lazyload_theme_pagetemplate($pagetemplate)
{
	add_action("nxs_ext_inject_pagetemplate_" . $pagetemplate, "nxs_ext_inject_theme_pagetemplate");
}

// unfortunately we can't use anonymous functions to support older servers running old PHP versions...
function nxs_ext_inject_theme_pagetemplate($pagetemplate)
{
	if ($pagetemplate == "")
	{
		webmethod_return_nack("pagetemplate $pagetemplate not specified");
	}

	$folder = dirname(__FILE__);
	$filetobeincluded = NXS_THEMEPATH . '/pagetemplates/' . $pagetemplate . '/pagetemplate_' . $pagetemplate . '.php';
	
	require_once($filetobeincluded);
}

// ------------------

function nxs_ext_lazyload_pagetemplate($pagetemplate)
{
	add_action("nxs_ext_inject_pagetemplate_" . $pagetemplate, "nxs_ext_inject_pagetemplate");
}

function nxs_ext_inject_pagetemplate($pagetemplate)
{
	$filetobeincluded = NXS_FRAMEWORKPATH . '/nexuscore/pagetemplates/' . $pagetemplate . '/pagetemplate_' . $pagetemplate . '.php';
	require_once($filetobeincluded);
}

function nxs_requirepagetemplate($pagetemplate)
{
	// loads pagetemplate extensions in memory
	$action = "nxs_ext_inject_pagetemplate_" . $pagetemplate;
	if (!has_action($action))
	{
		if (nxs_has_adminpermissions())
		{
			webmethod_return_nack("pagetemplate not found [{$pagetemplate}], action not found; [{$action}]");
		}
		else
		{
			echo "<!-- Warning; looks like pagetemplate '" . $pagetemplate . "' is missing (maybe you deactivated a required plugin?) -->";
		}
	}
	else
	{
		do_action($action, $pagetemplate);
	}
}

//
// enqueue available pagetemplates
//
add_action('nxs_getpagetemplates', 'nxs_getpagetemplates_functions_AF', 10, 2);
function nxs_getpagetemplates_functions_AF($result, $args)
{
	$result[] = array("pagetemplate" => "blogentry");
	$result[] = array("pagetemplate" => "webpage");
	
	return $result;
}

//
// lazy load pagetemplates
//
nxs_ext_lazyload_pagetemplate("blogentry");
nxs_ext_lazyload_pagetemplate("webpage");
nxs_ext_lazyload_pagetemplate("generic");
nxs_ext_lazyload_pagetemplate("archive");
?>