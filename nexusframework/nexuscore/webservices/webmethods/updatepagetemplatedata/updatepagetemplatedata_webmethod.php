<?php
function nxs_webmethod_updatepagetemplatedata() 
{
	extract($_REQUEST);
 	
 	if ($postid == "") { nxs_webmethod_return_nack("postid not set"); }
 	if ($pagetemplate == "") { nxs_webmethod_return_nack("pagetemplate not set"); }
 	
 	// inject!
 	nxs_requirepagetemplate($pagetemplate);
 	
 	//
 	$args = array();
 	$args["postid"] = $postid;
 	$args["pagetemplate"] = $pagetemplate;
 	nxs_updatepagetemplate($args);
 	
	$functionnametoinvoke = 'nxs_pagetemplate_' . $pagetemplate . '_updatedata';
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
?>