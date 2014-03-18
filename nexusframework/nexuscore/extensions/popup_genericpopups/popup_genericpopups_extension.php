<?php
//
// popup_genericpopup extensions
//

function nxs_ext_lazyload_popup_genericpopup($popup_genericpopup)
{
	$action = "nxs_ext_inject_popup_genericpopup_" . $popup_genericpopup;
	$ishandledbyplugin = has_action($action);
	if ($ishandledbyplugin)
	{
		// it appears this popup_genericpopup was already handled by a plugin,
		// we will assume the plugin will override the popup_genericpopup of the framework
		// in this case we won't inject the popup_genericpopup from the framework
	}	
	else
	{
		add_action($action, "nxs_ext_inject_popup_genericpopup");
	}
}

function nxs_ext_inject_popup_genericpopup($popup_genericpopup)
{
	$filetobeincluded = NXS_FRAMEWORKPATH . '/nexuscore/popup/genericpopups/' . $popup_genericpopup . '/' . $popup_genericpopup . '_genericpopup.php';
	require_once($filetobeincluded);
}

function nxs_genericpopup_exists($popup_genericpopup)
{
	$result = false;
	
	// loads popup_genericpopup extensions in memory
	$action = "nxs_ext_inject_popup_genericpopup_" . $popup_genericpopup;
	if (has_action($action))
	{
		$result = true;
	}
	
	return $result;
}

function nxs_requirepopup_genericpopup($popup_genericpopup)
{
	$result = array();

	// loads popup_genericpopup extensions in memory
	$action = "nxs_ext_inject_popup_genericpopup_" . $popup_genericpopup;
	if (!has_action($action))
	{
		// we gaan wel door, iemand kan per ongeluk of met opzet bijv. een plugin hebben uitgeschakeld
		
		if (nxs_has_adminpermissions())
		{
			echo "Warning; looks like the extension for popup_genericpopup '" . $popup_genericpopup . "' is missing (maybe you deactivated a required plugin?)";
		}
		else
		{
			echo "<!-- Warning; looks like popup_genericpopup '" . $popup_genericpopup . "' is missing (maybe you deactivated a required plugin?) -->";
		}
		
		$result["result"] = "NACK";		
	}
	else
	{
		do_action($action, $popup_genericpopup);
		
		$result["result"] = "OK";
	}
	
	return $result;
}

nxs_ext_lazyload_popup_genericpopup("mediapicker");
nxs_ext_lazyload_popup_genericpopup("tinymcepicklink");
nxs_ext_lazyload_popup_genericpopup("colorzenpicker");
nxs_ext_lazyload_popup_genericpopup("colorvariationpicker");
nxs_ext_lazyload_popup_genericpopup("categorieseditor");
nxs_ext_lazyload_popup_genericpopup("backgroundpatternpicker");
nxs_ext_lazyload_popup_genericpopup("iconpicker");
nxs_ext_lazyload_popup_genericpopup("customhtml");
nxs_ext_lazyload_popup_genericpopup("unistylepersister");
nxs_ext_lazyload_popup_genericpopup("datepicker");

?>