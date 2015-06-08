<?php
function nxs_webmethod_installoneclickcontent() 
{	
	extract($_REQUEST);

	$maxstep = 3;
	$currentstep = $_REQUEST["currentstep"];
	
	if ($currentstep == 0 || $currentstep == "")
	{
		$currentstep = 1;
	}

	nxs_ob_start();

	if ($currentstep == 1) 
	{
		echo "<h2>" . nxs_l18n__("Started importing", "nxs_td") . "</h2>";
		
		// mark that 1clickcontent is passed, this will ensure site settings and site contents
		// is not overriden in upcoming theme activations
		$modifiedmetadata = array();
		$date = date('d-m-Y H:i:s');
		$modifiedmetadata['passed1clickcontent'] = "Passed on " . $date;	
		// explicitly marked false, as its 99% chance multiple sitesettings
		// are active at this stage (the current site has one, and the imported data
		// will have a sitesetting too (unless they imported will have ignored
		// it if it detects its the same).
		$performsanitycheck = false;
		nxs_mergesitemeta_internal($modifiedmetadata, $performsanitycheck);
	}
	else if ($currentstep == 2) 
	{
		require_once(NXS_FRAMEWORKPATH . '/nexuscore/importers/oneclick/nxsimporter.php');
		$importer = new NXS_importer();
	
		// inner stacked ob_start		
		nxs_ob_start();
		$importer->dispatch();
		$importoutput = nxs_ob_get_contents();
		nxs_ob_end_clean();
		
 		echo "<span title='More information in the HTML DOM'>[...]</span>";
		
		if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
		{
  		echo "<!-- ";
  	}
  	echo $importoutput;
		if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
		{
  		echo " -->";
  	}
	}
	else  
	{
		echo "<h2>" . nxs_l18n__("Finished importing", "nxs_td") . "</h2>";
	}
	
	$log = nxs_ob_get_contents();
	nxs_ob_end_clean();

	//	
	$currentstep = $currentstep + 1;
	
	$responseargs = array();
	$responseargs["log"] = $log;
	$responseargs["nextstep"] = $currentstep;
	$responseargs["maxstep"] = $maxstep;
	
	nxs_webmethod_return_ok($responseargs);
}
?>