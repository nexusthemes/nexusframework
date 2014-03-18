<?php
function nxs_webmethod_sanitizecontent() 
{	
	extract($_REQUEST);

	$maxstep = 13;
	$currentstep = $_REQUEST["currentstep"];
	
	if ($currentstep == 0 || $currentstep == "")
	{
		$currentstep = 1;
	}

	ob_start();

	if ($currentstep <= $maxstep) 
	{
		require_once(NXS_FRAMEWORKPATH . '/nexuscore/dataconsistency/dataconsistency.php');
		$currentstepstring = (string)$currentstep;
		echo nxs_ensuredataconsistency($currentstepstring);
	}
	
	$log = ob_get_contents();
	ob_end_clean();

	//	
	$currentstep = $currentstep + 1;
	
	$responseargs = array();
	$responseargs["log"] = $log;
	$responseargs["nextstep"] = $currentstep;
	$responseargs["maxstep"] = $maxstep;
	
	nxs_webmethod_return_ok($responseargs);	
}
?>