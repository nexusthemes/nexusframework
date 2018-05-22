<?php

function nxs_webmethod_getsheet() 
{
	extract($_REQUEST);
	
	if (!isset($clientpopupsessioncontext)) {	nxs_webmethod_return_nack("clientpopupsessioncontext not set");	}
	
	$sheet = $clientpopupsessioncontext["sheet"];
	
	if ($sheet == "") { nxs_webmethod_return_nack("unspecified sheet"); }
	
	$result = nxs_genericpopup_getpopuphtml($_REQUEST);
	
	//
	nxs_webmethod_return($result);
}

function nxs_dataprotection_nexusframework_webmethod_getsheet_getprotecteddata($args)
{
	$result = array
	(
		"subactivities" => array
		(
			// intentionally left blank
		),
		"dataprocessingdeclarations" => array	
		(
			// intentionally left blank
		),
		"status" => "final",
	);
	return $result;
}

?>