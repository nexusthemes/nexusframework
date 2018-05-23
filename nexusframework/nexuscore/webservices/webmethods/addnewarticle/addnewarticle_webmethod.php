<?php
function nxs_webmethod_addnewarticle() 
{
	$combinedargs = array();

	// 
	//
	//
	$args = $_REQUEST;
	
	foreach ($args as $k => $v) 
	{
		if ($k != "args")
		{
	  	$combinedargs[$k] = $v;
	  }
	}
	
	$args = $_REQUEST["args"];
	
	//
	// add additional args
	//		
	foreach ($args as $k => $v) 
	{
  	$combinedargs[$k] = $args[$k];
	}
	
	extract($combinedargs);
	if ($titel == "")
	{
		nxs_webmethod_return_nack("title not set");
	}
	
	$response = nxs_addnewarticle($combinedargs);
	nxs_webmethod_return($response);
}

function nxs_dataprotection_nexusframework_webmethod_addnewarticle_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("webmethod-none");
}

?>