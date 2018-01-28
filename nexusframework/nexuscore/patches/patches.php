<?php
function nxs_applypatch($patchname, $args)
{
	// check privileges
	if (!nxs_has_adminpermissions())
	{
 		nxs_webmethod_return_nack("whoops, no permission :)");
	}
	
	// delegate work
	$filetobeincluded = dirname(__FILE__) . "/" . $patchname . "/" . $patchname . ".php";
	if (file_exists($filetobeincluded))
	{
		require_once($filetobeincluded);
		
		$functionnametoinvoke = 'nxs_apply_' . $patchname;
		if (function_exists($functionnametoinvoke))
		{
			echo "function found;" . $functionnametoinvoke;
			echo "<br />";
			call_user_func($functionnametoinvoke, $args);
		}
		else
		{
	 		nxs_webmethod_return_nack("function not found; {$functionnametoinvoke}");
		}
	}
	else
	{
 		nxs_webmethod_return_nack("file not found; {$filetobeincluded}");
	}
}
