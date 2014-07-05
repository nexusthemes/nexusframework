<?php
function nxs_webmethod_updatesitedata() 
{
	extract($_REQUEST);
 	
	// doorlussen naar handler voor dit sub request
	$filetobeincluded = NXS_FRAMEWORKPATH . '/nexuscore/site/site.php';
	if (file_exists($filetobeincluded))
	{
		require_once($filetobeincluded);
		
		$functionnametoinvoke = 'nxs_ws_site_updatesitedata';
		if (function_exists($functionnametoinvoke))
		{
			if ($data == "")
			{
				nxs_webmethod_return_nack("data not set");
			}
			
			$data["updatesectionid"] = $_REQUEST["updatesectionid"];
			
			$result = call_user_func($functionnametoinvoke, $data);
			
			if (!array_key_exists("result", $result))
			{
				nxs_webmethod_return_nack("In het resultaat zit geen 'result' key;");
			}
			
			nxs_webmethod_return_ok($result);
		}
		else
		{
			nxs_webmethod_return_nack("functie niet gevonden;" . $functionnametoinvoke);
		}
	}
	else
	{
		nxs_webmethod_return_nack("bestand " . $filetobeincluded . " not found");
	}
}
?>