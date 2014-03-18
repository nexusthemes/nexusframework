<?php

function nxs_postwizard_createpost_noparameters($slug, $titel, $nxsposttype, $postwizard)
{
	$args = array();
	$args["slug"] = $slug;
	$args["titel"] = $titel;
	$args["nxsposttype"] = $nxsposttype;
	$response = nxs_addnewarticle($args);
	if ($response["result"] != "OK")
	{
		echo "post kon niet worden aangemaakt?";
		var_dump($response);
		return;
	}
	else
	{
		$postid = $response["postid"];
		nxs_postwizard_setuppost_noparameters($postid, $postwizard);
	}
	
	return $postid;
}


function nxs_postwizard_setuppost_noparameters($postid, $postwizard)
{
	$args["postid"] = $postid;
	$args["postwizard"] = $postwizard;
	nxs_postwizard_setuppost($args);
}

function nxs_postwizard_setuppost($args) 
{
	extract($args);
	
	if ($postwizard == "")
	{
		echo "postwizard niet geset?";
		return;
	}
	
	nxs_requirepostwizard($postwizard);

	$functionnametoinvoke = 'nxs_postwizard_' . $postwizard . '_setuppost';
	
	//
	// invokefunction
	//
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		echo "function not found; " . $functionnametoinvoke . " in " . $filetobeincluded;
	}
	
	return $result; 	
}
?>