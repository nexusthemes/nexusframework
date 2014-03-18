<?php
function nxs_webmethod_updategenericpopupdata() 
{
	$clientpopupsessioncontext = array();	// can be overriden by the extract
	$clientshortscopedata = array();			// can be overriden by the extract

	extract($_REQUEST);
 	
	if (!isset($clientpopupsessioncontext)) { nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }
	if (!isset($clientpopupsessiondata)) { nxs_webmethod_return_nack("clientpopupsessiondata not set"); }
	
	$contextprocessor = $clientpopupsessioncontext["contextprocessor"];
	
	// load the context processor if its not yet loaded
	nxs_requirepopup_contextprocessor($contextprocessor);
	
	$blendedmetadata = array();
	$blendedmetadata = array_merge($blendedmetadata, $clientpopupsessiondata);
	
	// extend the data with global metadata
	$extendedmetadata = nxs_genericpopup_getderivedglobalmetadata($_REQUEST, $clientpopupsessiondata);
	$blendedmetadata = array_merge($blendedmetadata, $extendedmetadata);
	
	// delegate request to the contextprocessor
	$functionnametoinvoke = 'nxs_popup_contextprocessor_' . $contextprocessor . '_mergedata_internal';
	if (function_exists($functionnametoinvoke))
	{
		$args = $_REQUEST;
		$result = call_user_func($functionnametoinvoke, $args, $blendedmetadata);
		nxs_webmethod_return($result);
	}
	else
	{
		nxs_webmethod_return_nack("missing function name for option type; $functionnametoinvoke");
	}
}
?>