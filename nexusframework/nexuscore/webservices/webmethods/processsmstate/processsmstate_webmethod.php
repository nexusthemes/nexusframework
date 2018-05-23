<?php
function nxs_webmethod_processsmstate() 
{	
	$statemachineid = $_REQUEST["statemachineid"];
	
	$result = array("nextstate"=>"finished", "log"=>"Todo: add log row");
	$result = apply_filters("nxs_sm_processstate_{$statemachineid}", $result);
	
	nxs_webmethod_return_ok($result);
}

function nxs_dataprotection_nexusframework_webmethod_processsmstate_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("webmethod-none");
}

?>