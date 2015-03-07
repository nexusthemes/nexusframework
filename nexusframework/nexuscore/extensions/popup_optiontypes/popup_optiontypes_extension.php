<?php
//
// popup_optiontype extensions
//

function nxs_ext_lazyload_popup_optiontype($popup_optiontype)
{
	$action = "nxs_ext_inject_popup_optiontype_" . $popup_optiontype;
	$ishandledbyplugin = has_action($action);
	if ($ishandledbyplugin)
	{
		// it appears this popup_optiontype was already handled by a plugin,
		// we will assume the plugin will override the popup_optiontype of the framework
		// in this case we won't inject the popup_optiontype from the framework
	}	
	else
	{
		add_action($action, "nxs_ext_inject_popup_optiontype");
	}
}

function nxs_ext_inject_popup_optiontype($popup_optiontype)
{
	$filetobeincluded = NXS_FRAMEWORKPATH . '/nexuscore/popup/optiontypes/' . $popup_optiontype . '/' . $popup_optiontype . '_optiontype.php';
	require_once($filetobeincluded);
}

function nxs_requirepopup_optiontype($popup_optiontype)
{
	$result = array();

	// loads popup_optiontype extensions in memory
	$action = "nxs_ext_inject_popup_optiontype_" . $popup_optiontype;
	if (!has_action($action))
	{
		// we gaan wel door, iemand kan per ongeluk of met opzet bijv. een plugin hebben uitgeschakeld
		
		if (nxs_has_adminpermissions())
		{
			echo "Warning; looks like popup_optiontype '" . $popup_optiontype . "' is missing (maybe you deactivated a required plugin?)";
		}
		else
		{
			echo "<!-- Warning; looks like popup_optiontype '" . $popup_optiontype . "' is missing (maybe you deactivated a required plugin?) -->";
		}
		
		$result["result"] = "NACK";		
	}
	else
	{
		do_action($action, $popup_optiontype);
		
		$result["result"] = "OK";
	}
	
	return $result;
}

//
// lazy load popup_optiontypes
// note, if plugins load a popup_optiontype with the same name,
// that popup_optiontype will load first, ignoring this one
//

nxs_ext_lazyload_popup_optiontype("tinymce");
nxs_ext_lazyload_popup_optiontype("article_link");
nxs_ext_lazyload_popup_optiontype("checkbox");
nxs_ext_lazyload_popup_optiontype("image");
nxs_ext_lazyload_popup_optiontype("input");
nxs_ext_lazyload_popup_optiontype("select");
nxs_ext_lazyload_popup_optiontype("selectpost");
nxs_ext_lazyload_popup_optiontype("staticgenericlist_link");
nxs_ext_lazyload_popup_optiontype("textarea");
nxs_ext_lazyload_popup_optiontype("categories");
nxs_ext_lazyload_popup_optiontype("colorzen");
nxs_ext_lazyload_popup_optiontype("fontzen");
nxs_ext_lazyload_popup_optiontype("colorvariation");
nxs_ext_lazyload_popup_optiontype("gotosheet");
nxs_ext_lazyload_popup_optiontype("custom");
nxs_ext_lazyload_popup_optiontype("backgroundpattern");
nxs_ext_lazyload_popup_optiontype("backgroundposition");
nxs_ext_lazyload_popup_optiontype("wrapperbegin");
nxs_ext_lazyload_popup_optiontype("wrapperend");
nxs_ext_lazyload_popup_optiontype("icon");
nxs_ext_lazyload_popup_optiontype("date");
nxs_ext_lazyload_popup_optiontype("unistyle");
nxs_ext_lazyload_popup_optiontype("unicontent");
nxs_ext_lazyload_popup_optiontype("wooprod_link");
nxs_ext_lazyload_popup_optiontype("lock");
nxs_ext_lazyload_popup_optiontype("widgettypeconverter");
nxs_ext_lazyload_popup_optiontype("effects");
nxs_ext_lazyload_popup_optiontype("halign");
?>