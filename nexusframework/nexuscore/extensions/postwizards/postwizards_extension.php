<?php
//
// postwizards extensions
//

function nxs_ext_lazyload_postwizard($postwizard)
{
	add_action("nxs_ext_inject_postwizard_" . $postwizard, "nxs_ext_inject_postwizard");
}

// unfortunately we can't use anonymous functions to support older servers running old PHP versions...
function nxs_ext_inject_postwizard($postwizard)
{
	if ($postwizard == "")
	{	
		echo "geen postwizard meegegeven";
		die();
	}
	
	require_once(NXS_FRAMEWORKPATH . '/nexuscore/postwizards/' . $postwizard . '/postwizard_' . $postwizard . '.php');
}

function nxs_ext_lazyload_theme_postwizard($postwizard)
{
	add_action("nxs_ext_inject_postwizard_" . $postwizard, "nxs_ext_inject_theme_postwizard");
}

// unfortunately we can't use anonymous functions to support older servers running old PHP versions...
function nxs_ext_inject_theme_postwizard($postwizard)
{
	if ($postwizard == "")
	{	
		echo "geen postwizard meegegeven";
		die();
	}
	
	require_once(NXS_THEMEPATH . '/postwizards/' . $postwizard . '/postwizard_' . $postwizard . '.php');
}

function nxs_requirepostwizard($postwizard)
{
	if ($postwizard == "")
	{
		
		echo "geen postwizard meegegeven?";
		die();
	}
	
	// inject postwizard extensions
	$action = "nxs_ext_inject_postwizard_" . $postwizard;
	if (!has_action($action))
	{
		if (nxs_has_adminpermissions())
		{
			// we gaan wel door, iemand kan per ongeluk of met opzet bijv. een plugin hebben uitgeschakeld
			echo "Warning; looks like postwizard '" . $postwizard . " is missing (maybe you deactivated a required plugin?)";
		}
		else
		{
			// we gaan wel door, iemand kan per ongeluk of met opzet bijv. een plugin hebben uitgeschakeld
			echo "<!-- Warning; looks like postwizard '" . $postwizard . " is missing (maybe you deactivated a required plugin?) -->";
		}
	}
	else
	{
		do_action($action, $postwizard);
	}
}

//
// lazy load postwizards
//

nxs_ext_lazyload_postwizard("blankpost");
nxs_ext_lazyload_postwizard("default404");
nxs_ext_lazyload_postwizard("pagelet_default_blogpostbottom");
nxs_ext_lazyload_postwizard("pagelet_default_blogposttop");
nxs_ext_lazyload_postwizard("default_footer");
nxs_ext_lazyload_postwizard("default_header");
nxs_ext_lazyload_postwizard("defaulthome");
nxs_ext_lazyload_postwizard("defaultblog");
nxs_ext_lazyload_postwizard("defaultlist");
nxs_ext_lazyload_postwizard("defaultmenu");
nxs_ext_lazyload_postwizard("default_sidebar");
nxs_ext_lazyload_postwizard("defaultgenericlist");
nxs_ext_lazyload_postwizard("legacyupgrader");
nxs_ext_lazyload_postwizard("list3dummyitems");
nxs_ext_lazyload_postwizard("newpost");
nxs_ext_lazyload_postwizard("newsidebar");
nxs_ext_lazyload_postwizard("wppost");
nxs_ext_lazyload_postwizard("generic");
nxs_ext_lazyload_postwizard("pdt1");
nxs_ext_lazyload_postwizard("pdt2");
nxs_ext_lazyload_postwizard("pdt3");
nxs_ext_lazyload_postwizard("template");

//
// enqueue available postwizards
//
add_filter("nxs_getpostwizards", "nxs_ext_getpostwizards", 10, 2);	// default prio, 2 arguments (args and result)
function nxs_ext_getpostwizards($result, $args)
{
	if ($args["invoker"] == "newinteractive")
	{
		$result[] = array("postwizard" => "pdt2");
		$result[] = array("postwizard" => "pdt1");
		
		if (nxs_enableconceptualwidgets())
		{
			$result[] = array("postwizard" => "template");
		}
	}
	
	return $result;
}

/* ******************** */

// nxs_lazyload_plugin_postwizard

function nxs_lazyload_plugin_postwizard($file, $postwizard)
{
	// store file loc in lookup (mem)
	global $nxs_gl_postwizard_file;
	if ($nxs_gl_postwizard_file == null)
	{
		$nxs_gl_postwizard_file = array();
	}
	$nxs_gl_postwizard_file[$postwizard] = $file;
		
	add_action("nxs_ext_inject_postwizard_" . $postwizard, "nxs_inject_plugin_postwizard");
}

// unfortunately we can't use anonymous functions to support older servers running old PHP versions...
function nxs_inject_plugin_postwizard($postwizard)
{
	if ($postwizard == "")
	{	
		echo "geen postwizard meegegeven";
		die();
	}
	
	global $nxs_gl_postwizard_file;
	$file = $nxs_gl_postwizard_file[$postwizard];
	$path = plugin_dir_path($file);
	$filetobeincluded = $path . '/postwizards/' . $postwizard . '/postwizard_' . $postwizard . '.php';
	require_once($filetobeincluded);
}

/* ******************** */

?>