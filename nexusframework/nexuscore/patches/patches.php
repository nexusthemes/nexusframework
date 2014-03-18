<?php
function nxs_applypatch($patchname, $args)
{
	// check privileges
	if (!nxs_has_adminpermissions())
	{
		echo "whoops, no permission :)";
		die();
	}
	
	// delegate work
	// doorlussen naar handler
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
			echo "function not found;" . $functionnametoinvoke;
			echo "<br />";
			die();
		}
	}
	else
	{
		echo "file not found;" . $filetobeincluded;
		die();
	}
}
?>