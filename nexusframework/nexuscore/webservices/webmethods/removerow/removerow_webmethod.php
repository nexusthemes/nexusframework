<?php
/**
 * Row and associated meta deletion
 */
function nxs_webmethod_removerow() {

	extract($_REQUEST);

	if ($postid == "") { nxs_webmethod_return_nack("postid not specified /nxs_webmethod_removerow/"); }
	if ($rowid == "") { nxs_webmethod_return_nack("rowid not specified"); }
	if ($rowindex == "") { nxs_webmethod_return_nack("rowindex not specified"); }

	$result = array();

  global $nxs_global_current_postid_being_rendered;
  global $nxs_global_current_postmeta_being_rendered;

  $nxs_global_current_postid_being_rendered = $postid;
  $nxs_global_current_postmeta_being_rendered = nxs_get_corepostmeta($postid);

	$poststructure = nxs_parsepoststructure($postid);
	$currentrow = $poststructure[$rowindex];
	
	$rowidaccordingtoindex = nxs_parserowidfrompagerow($currentrow);
	if (isset($rowidaccordingtoindex))
	{
		if ($rowidaccordingtoindex == $rowid)
		{
			// ok
		}
		else
		{
			nxs_webmethod_return_nack("row not found");
		}
	}
	else
	{
		// assumed ok (backwards compatibility)
	}
	
	$content = $currentrow["content"];

	// delete metadata of placeholders in row	
	$placeholderids = nxs_parseplaceholderidsfrompagerow($content);
	foreach ($placeholderids as $placeholderid)
	{
		nxs_purgeplaceholdermetadata($postid, $placeholderid);
  }

	// delete row
	unset($poststructure[$rowindex]);
	$poststructure = array_values($poststructure);
	nxs_storebinarypoststructure($postid, $poststructure);
	
	// update items that are derived (based upon the structure and contents of the page, such as menu's)
	$updateresult = nxs_after_postcontents_updated($postid);
	if ($updateresult["pagedirty"] == "true") 
	{
		$result["pagedirty"] = "true";
	}

	// create response
	nxs_webmethod_return_ok($result);
}