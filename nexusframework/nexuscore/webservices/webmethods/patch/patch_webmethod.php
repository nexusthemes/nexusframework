<?php
function nxs_webmethod_patch() 
{	
	extract($_REQUEST);
	
	if (false) // $_REQUEST["patch"] == "menu")
	{
		// anyone can invoke this (anonymous)
		
		require_once(NXS_FRAMEWORKPATH . '/nexuscore/patches/menu/menu.php');
		
		//
		nxs_ob_start();
		nxs_apply_menu(false);
		$html = nxs_ob_get_contents();
		nxs_ob_end_clean();
		
		$result["output"] = $html;
	}
	else
	{
		echo "patch $path not supported";
		die();
	}
	
	nxs_webmethod_return_ok($result);
}

function nxs_dataprotection_nexusframework_webmethod_patch_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("webmethod-none");
}

?>