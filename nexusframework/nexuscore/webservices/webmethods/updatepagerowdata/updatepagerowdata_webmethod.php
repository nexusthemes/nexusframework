<?php
function nxs_webmethod_updatepagerowdata() 
{
	extract($_REQUEST);
 	
 	if ($postid == "") { nxs_webmethod_return_nack("postid empty"); }
 	if ($pagerowid == "") { nxs_webmethod_return_nack("pagerowid empty"); }

	// inject the php file that contains popup functionality and gui
	$filetobeincluded = NXS_FRAMEWORKPATH . "/nexuscore/row/row.php";
	require_once($filetobeincluded);
		
	$functionnametoinvoke = 'nxs_pagerow_updatepagerowdata';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $_REQUEST);
		
		// als de pagerow is bijgewerkt, dan impliceert dit dat de 
		// content op de pagina is aangepast. Hier zit een extensie
		// punt in het framework
		nxs_after_postcontents_updated($postid);
		
		if (!array_key_exists("result", $result))
		{
			nxs_webmethod_return_nack("In het resultaat zit geen 'result' key;");
		}
		$output=json_encode($result);
		echo $output;
		die();
	}
	else
	{
		nxs_webmethod_return_nack("functie niet gevonden;" . $functionnametoinvoke);
	}
}
?>