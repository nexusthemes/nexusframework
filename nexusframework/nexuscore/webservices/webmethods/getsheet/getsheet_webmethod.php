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

?>