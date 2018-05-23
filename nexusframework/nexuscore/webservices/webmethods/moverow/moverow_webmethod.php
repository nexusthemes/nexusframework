<?php
function nxs_webmethod_moverow() 
{
	extract($_REQUEST);
 	
 	if ($containerpostid == "") { nxs_webmethod_return_nack("containerpostid empty"); }
 	if ($sourcepostid == "") { nxs_webmethod_return_nack("sourcepostid empty"); }
 	if ($sourcerowindex == "") { nxs_webmethod_return_nack("sourcerowindex empty"); }
 	if ($destinationpostid == "") { nxs_webmethod_return_nack("destinationpostid empty"); }
 	if ($destinationrowindex == "") { nxs_webmethod_return_nack("destinationrowindex empty"); }
	
	if ($sourcepostid == $destinationpostid)
	{
		// row was moved in the same post id; in that case only thing that has to change is the 
		// order in the same struct
		
		// override row in existing structure
		$updatedpoststructure = nxs_parsepoststructure($sourcepostid);		
		// load source row data
		$sourcerow = $updatedpoststructure[$sourcerowindex];
		// remove source row
		unset($updatedpoststructure[$sourcerowindex]);
		// re-index the array
		$updatedpoststructure = array_values($updatedpoststructure);
		// insert row into structure
		$updatedpoststructure = nxs_insertarrayindex($updatedpoststructure, $sourcerow, $destinationrowindex);
		// store the structure
		nxs_storebinarypoststructure($destinationpostid, $updatedpoststructure);
	}
	else
	{
		nxs_webmethod_return_nack("failed; currently you can only move items within the same container");
	}

	//
	// create response
	//
	$responseargs = array();
	nxs_webmethod_return_ok($responseargs); 	
}

function nxs_dataprotection_nexusframework_webmethod_moverow_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("webmethod-none");
}

?>