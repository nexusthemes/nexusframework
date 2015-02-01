<?php
function nxs_webmethod_sanitizecontent() 
{	
	extract($_REQUEST);

	require_once(NXS_FRAMEWORKPATH . '/nexuscore/dataconsistency/dataconsistency.php');
	$result = nxs_ensuredataconsistency_chunked($chunkedsteps);
	
	$responseargs = array();
	$responseargs["log"] = $result["log"];
	$responseargs["nextchunkedsteps"] = $result["nextchunkedsteps"] ;
	
	nxs_webmethod_return_ok($responseargs);	
}
?>