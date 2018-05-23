<?php
function nxs_webmethod_swap() 
{	
	extract($_REQUEST);

	if ($context == "placeholders")
	{
		if ($sourcecontainerpostid == "")
		{
			nxs_webmethod_return_nack("sourcecontainerpostid not specified /nxs_webmethod_swapplaceholders()/");
		}
		if ($sourcepostid == "")
		{
			nxs_webmethod_return_nack("sourcepostid not specified /nxs_webmethod_swapplaceholders()/");
		}
		if ($sourceplaceholderid == "")
		{
			nxs_webmethod_return_nack("sourceplaceholderid not specified");
		}
		
		if ($destinationcontainerpostid == "")
		{
			nxs_webmethod_return_nack("destinationcontainerpostid not specified /nxs_webmethod_swapplaceholders()/");
		}
		if ($destinationpostid == "")
		{
			nxs_webmethod_return_nack("destinationpostid not specified /nxs_webmethod_swapplaceholders()/");
		}
		if ($destinationplaceholderid == "")
		{
			nxs_webmethod_return_nack("destinationplaceholderid not specified");
		}
		
		$sourceplaceholdertemplate = nxs_getplaceholdertemplate($sourcepostid, $sourceplaceholderid);
		$destinationplaceholdertemplate = nxs_getplaceholdertemplate($destinationpostid, $destinationplaceholderid);
		if (!nxs_iswidgetallowedonpost($sourcepostid, $sourceplaceholdertemplate, true))
		{
			$responseargs = array();
			$responseargs["msg"] = __("The widget can not be swapped");
			$responseargs["explanation"] = "source post id: {$sourcepostid} source placeholdertemplate: $sourceplaceholdertemplate";
			webmethod_return_alternativeflow("widgetnotallowed", $responseargs);
		}
		else if (!nxs_iswidgetallowedonpost($destinationpostid, $destinationplaceholdertemplate, true))
		{
			$responseargs = array();
			$responseargs["msg"] = __("The widget can not be swapped");
			$responseargs["explanation"] = "destination post id: {$destinationpostid} destination placeholdertemplate: $destinationplaceholdertemplate";
			webmethod_return_alternativeflow("widgetnotallowed", $responseargs);
		}
			
		$oldmetadatasource = nxs_getwidgetmetadata($sourcepostid, $sourceplaceholderid);
		$oldmetadatadestination = nxs_getwidgetmetadata($destinationpostid, $destinationplaceholderid);
	
		nxs_overridewidgetmetadata($destinationpostid, $destinationplaceholderid, $oldmetadatasource);
		nxs_overridewidgetmetadata($sourcepostid, $sourceplaceholderid, $oldmetadatadestination);
	
		// parse source page
		$parsedpoststructuresource = nxs_parsepoststructure($sourcepostid);
		// update items that are derived (based upon the structure and contents of the page, such as menu's)
		nxs_after_postcontents_updated($sourcepostid);
		// get rowindexs of placeholders
		$sourcerowindex = nxs_getrowindex_for_placeholderid($parsedpoststructuresource, $sourceplaceholderid);
	
		//parse destination page
		if ($sourcepostid != $destinationpostid)
		{
			$parsedpoststructuredestination = nxs_parsepoststructure($destinationpostid);
			// update items that are derived (based upon the structure and contents of the page, such as menu's)
			nxs_after_postcontents_updated($destinationpostid);
		}
		else
		{
			$parsedpoststructuredestination = $parsedpoststructuresource;
		}
		$destinationrowindex = nxs_getrowindex_for_placeholderid($parsedpoststructuredestination, $destinationplaceholderid);
		
		// get html of rows
		// we nemen voor nu aan dat we renderen in een 'default' modus
		
		// volgende variabelen worden reeds gezet door nxs_getrenderedrowhtml()... en kunnen dus weg
		$rendermode = "default";
		global $nxs_global_current_containerpostid_being_rendered;
		$nxs_global_current_containerpostid_being_rendered = $sourcecontainerpostid;
		$sourcerowhtml = nxs_getrenderedrowhtml($sourcepostid, $sourcerowindex, $rendermode);
	
		// volgende variabelen worden reeds gezet door nxs_getrenderedrowhtml()... en kunnen dus weg
		$rendermode = "default";
		global $nxs_global_current_containerpostid_being_rendered;
		$nxs_global_current_containerpostid_being_rendered = $destinationcontainerpostid;
		$destinationrowhtml = nxs_getrenderedrowhtml($destinationpostid, $destinationrowindex, $rendermode);
	
		//
		// create response
		//
		$responseargs = array();
		
		$responseargs["sourcerowindex"] = $sourcerowindex;
		$responseargs["sourcerowhtml"] = $sourcerowhtml;
	
		$responseargs["destinationrowindex"] = $destinationrowindex;
		$responseargs["destinationrowhtml"] = $destinationrowhtml;
		
		// that's it :)
		nxs_webmethod_return_ok($responseargs);
	}
	else if ($context == "entities")
	{
		global $nxs_g_modelmanager;
		$contentmodel = $nxs_g_modelmanager->getcontentmodel();
		
		$ordersetid = $contentmodel[$taxonomy]["postid"];
		if (!isset($ordersetid))
		{
			nxs_webmethod_return_nack("orderset not found for taxonomy '{$taxonomy}'");
		}
		
		$poststructure = nxs_parsepoststructure($ordersetid);
		
		error_log("swap; step 1; struct before:" . json_encode($poststructure));
		
		//
		$source = $poststructure[$oldindex];
		
		//error_log("swap; step 1.5; source row:" . json_encode($source));
		
		// remove source row
		unset($poststructure[$oldindex]);
		
		// re-index
		$poststructure = array_values($poststructure);
		
		//error_log("swap; step 2; struct re-index after remove oldindex $oldindex:" . json_encode($poststructure));
		
		// insert at new index
		$poststructure = nxs_insertarrayindex($poststructure, $source, $newindex);
		
		//error_log("swap; step 3; struct after adding newindex $newindex:" . json_encode($poststructure));
		
		// persist structure
		$updateresult = nxs_storebinarypoststructure($ordersetid, $poststructure);

		// that's it :)
		$responseargs = array();
		nxs_webmethod_return_ok($responseargs);
	}
	else
	{
		nxs_webmethod_return_nack("unsupported context; $context");
	}
}

function nxs_dataprotection_nexusframework_webmethod_swap_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("webmethod-none");
}

?>