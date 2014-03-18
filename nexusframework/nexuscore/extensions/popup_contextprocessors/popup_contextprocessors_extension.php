<?php
//
// popup_contextprocessor extensions
//
function nxs_ext_lazyload_popup_contextprocessor($popup_contextprocessor)
{
	$action = "nxs_ext_inject_popup_contextprocessor_" . $popup_contextprocessor;
	$ishandledbyplugin = has_action($action);
	if ($ishandledbyplugin)
	{
		// it appears this popup_contextprocessor was already handled by a plugin,
		// we will assume the plugin will override the popup_contextprocessor of the framework
		// in this case we won't inject the popup_contextprocessor from the framework
	}	
	else
	{
		add_action($action, "nxs_ext_inject_popup_contextprocessor");
	}
}

function nxs_ext_inject_popup_contextprocessor($popup_contextprocessor)
{
	$filetobeincluded = NXS_FRAMEWORKPATH . '/nexuscore/popup/contextprocessors/' . $popup_contextprocessor . '/' . $popup_contextprocessor . '_contextprocessor.php';
	require_once($filetobeincluded);
}

function nxs_ext_lazyload_popup_theme_contextprocessor($popup_contextprocessor)
{
	$action = "nxs_ext_inject_popup_contextprocessor_" . $popup_contextprocessor;
	$ishandledbyplugin = has_action($action);
	if ($ishandledbyplugin)
	{
		// it appears this popup_contextprocessor was already handled by a plugin,
		// we will assume the plugin will override the popup_contextprocessor of the framework
		// in this case we won't inject the popup_contextprocessor from the framework
	}	
	else
	{
		add_action($action, "nxs_ext_inject_popup_theme_contextprocessor");
	}
}

function nxs_ext_inject_popup_theme_contextprocessor($popup_contextprocessor)
{
	$filetobeincluded = NXS_THEMEPATH . '/contextprocessors/' . $popup_contextprocessor . '/' . $popup_contextprocessor . '_contextprocessor.php';
	require_once($filetobeincluded);
}

function nxs_requirepopup_contextprocessor($popup_contextprocessor)
{
	if ($popup_contextprocessor == "") { nxs_webmethod_return_nack("popup_contextprocessor not set"); }
	
	$result = array();

	// loads popup_contextprocessor extensions in memory
	$action = "nxs_ext_inject_popup_contextprocessor_" . $popup_contextprocessor;
	if (!has_action($action))
	{
		// we gaan wel door, iemand kan per ongeluk of met opzet bijv. een plugin hebben uitgeschakeld
		
		if (nxs_has_adminpermissions())
		{
			echo "Warning; looks like popup_contextprocessor '" . $popup_contextprocessor . "' is missing (maybe you deactivated a required plugin?)";
			nxs_dumpstacktrace();
		}
		else
		{
			echo "<!-- ";
			echo "Warning; looks like popup_contextprocessor '" . $popup_contextprocessor . "' is missing (maybe you deactivated a required plugin?)";
			echo "--> ";
		}
		
		$result["result"] = "NACK";		
	}
	else
	{
		do_action($action, $popup_contextprocessor);
		
		$result["result"] = "OK";
	}
	
	return $result;
}

//
// lazy load popup_contextprocessors
// note, if plugins load a popup_contextprocessor with the same name,
// that popup_contextprocessor will load first, ignoring this one
//
nxs_ext_lazyload_popup_contextprocessor("widgets");
nxs_ext_lazyload_popup_contextprocessor("pagerow");
nxs_ext_lazyload_popup_contextprocessor("post");
nxs_ext_lazyload_popup_contextprocessor("postcontent");
nxs_ext_lazyload_popup_contextprocessor("site");
nxs_ext_lazyload_popup_contextprocessor("pagetemplate");
nxs_ext_lazyload_popup_contextprocessor("postwizard");
nxs_ext_lazyload_popup_contextprocessor("rowscontainer");
nxs_ext_lazyload_popup_contextprocessor("gallerybox");
?>