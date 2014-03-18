<?php
function nxs_webmethod_updateseooption() 
{
	extract($_REQUEST);

	if ($key == "")
	{
		nxs_webmethod_return_nack("key not set");
	}
	if ($postid == "")
	{
		nxs_webmethod_return_nack("postid not set");
	}
	
	$keyallowed = false;
	if ($key == "focuskw")
	{
		$keyallowed = true;
	}
	else if ($key == "title")
	{
		$keyallowed = true;
	}
	else if ($key == "metadesc")
	{
		$keyallowed = true;
	}
	
	nxs_updateseooption($postid, $key, $val);
	
	$responseargs = array();	
	nxs_webmethod_return_ok($responseargs);
}
?>