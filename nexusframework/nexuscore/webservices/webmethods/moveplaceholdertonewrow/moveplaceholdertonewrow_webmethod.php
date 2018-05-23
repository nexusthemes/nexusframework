<?php
function nxs_webmethod_moveplaceholdertonewrow() 
{	
	extract($_REQUEST);
	
	if ($postid == "")
	{
		nxs_webmethod_return_nack("postid niet gezet");
	}
	if ($insertafterrowindex == "")
	{
		nxs_webmethod_return_nack("insertafterrowindex niet gezet");
	}	
	if ($moveplaceholderid == "")
	{
		nxs_webmethod_return_nack("moveplaceholderid niet gezet");
	}
	
	// clone existing placeholder
	$newplaceholderid = nxs_cloneplaceholder($postid, $moveplaceholderid);
	$pagerowid = nxs_allocatenewpagerowid($postid);
	
	// create new "one" row, add placeholder
	$newrow = array();
	$newrow["rowindex"] = "new";
	$newrow["pagerowtemplate"] = "one";
	$newrow["pagerowid"] = $pagerowid;
	$newrow["pagerowattributes"] = "pagerowtemplate='one' pagerowid='" . $pagerowid . "'";
	$newrow["content"] = "";
	$newrow["content"] .= "[nxsphcontainer width=\"1\"][nxsplaceholder placeholderid='" . $newplaceholderid . "'][/nxsplaceholder][/nxsphcontainer]";

	// get poststructure (list of rowindex, pagerowtemplate, pagerowattributes, content)
	$poststructure = nxs_parsepoststructure($postid);

	// insert row into structure
	$updatedpoststructure = nxs_insertarrayindex($poststructure, $newrow, $insertafterrowindex+1);
	
	// persist structure
	$updateresult = nxs_storebinarypoststructure($postid, $updatedpoststructure);
	
	// clear placeholder metadata
	nxs_resetplaceholdermetadata($postid, $moveplaceholderid);
	
	
	//
	// op dit moment is de DOM bijgewerkt
	//
	
	// update items that are derived (based upon the structure and contents of the page, such as menu's)
	nxs_after_postcontents_updated($postid);

	// re-parse updated page
	$poststructure = nxs_parsepoststructure($postid);
	
	$rowindexafterinsert = nxs_getrowindex_for_placeholderid($poststructure, $newplaceholderid);
	$firstinserthtmlafterindex = $rowindexafterinsert - 1;
	$afterwardsupdaterowindex = nxs_getrowindex_for_placeholderid($poststructure, $moveplaceholderid);
	
	// get html of these rows
	// we nemen voor nu aan dat we renderen in een 'default' modus
	$rendermode = "default";

	$firstinserthtml = nxs_getrenderedrowhtml($postid, $rowindexafterinsert, $rendermode);
	$afterwardsupdatehtml = nxs_getrenderedrowhtml($postid, $afterwardsupdaterowindex, $rendermode);

	//
	// create response
	//
	$responseargs = array();
	
	$responseargs["firstinserthtmlafterindex"] = $firstinserthtmlafterindex;
	$responseargs["firstinserthtml"] = $firstinserthtml;

	$responseargs["afterwardsupdaterowindex"] = $afterwardsupdaterowindex;
	$responseargs["afterwardsupdatehtml"] = $afterwardsupdatehtml;
	
	// that's it :)
	nxs_webmethod_return_ok($responseargs);
}

function nxs_dataprotection_nexusframework_webmethod_moveplaceholdertonewrow_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("webmethod-none");
}

?>