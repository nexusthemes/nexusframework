<?php
function nxs_webmethod_updatepagedata() 
{
	extract($_REQUEST);
 	
 	if ($postid == "") { nxs_webmethod_return_nack("postid empty"); }
 	
	// doorlussen naar handler voor dit sub request
	$filetobeincluded = NXS_FRAMEWORKPATH . '/nexuscore/post/post.php';
	if (file_exists($filetobeincluded))
	{
		require_once($filetobeincluded);
		
		$functionnametoinvoke = 'nxs_ws_page_updatedata';
		if (function_exists($functionnametoinvoke))
		{
			$result = call_user_func($functionnametoinvoke, $_REQUEST);
			
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