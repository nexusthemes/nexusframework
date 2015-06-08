<?php
function nxs_webmethod_updatemenuitemlocation() 
{	
	extract($_REQUEST);

	$result = array();
	
	// temp debugging
	//nxs_ob_start();
	//var_dump($_REQUEST);
	//$result["vars"] = nxs_ob_get_clean();
	//
	
	if ($postid == "")
	{
		nxs_webmethod_return_nack("postid niet gezet");
	}
	if ($placeholderid == "")
	{
		nxs_webmethod_return_nack("placeholderid niet gezet");
	}	
	if ($insertafterrowindex == "")
	{
		nxs_webmethod_return_nack("insertafterrowindex niet gezet");
	}
	if ($depth == "")
	{
		nxs_webmethod_return_nack("depth niet gezet");
	}
	
	$depth = intval($depth);
	
	$parsedpoststructure = nxs_parsepoststructure($postid);
	
	//
	// step 1; determine which rows should be moved
	//
	$itemstomove = array();
	$currentrowindex = -1;
	$sourcerowindex = nxs_getrowindex_for_placeholderid($parsedpoststructure, $placeholderid);
	$isindragroot = false;
	$depthofdragroot = -1;
	$placeholderidsbeingdragged = array();
	foreach ($parsedpoststructure as $pagerow)
	{
		$content = $pagerow["content"];
		$placeholderid = nxs_parsepagerow($content);
		
		$currentrowindex++;
		if ($currentrowindex == $sourcerowindex)
		{
			$isindragroot = true;
			$currentdepth = nxs_getdepth($postid, $placeholderid);
			$depthofdragroot = $currentdepth;
		
			// root item dat verplaatst wordt, kan child elementen bevatten...
			$itemstomove[] = array
			(
				"origrowindex" => $currentrowindex,
				"origdepth" => $currentdepth,
				"depthreltodragroot" => $currentdepth - $depthofdragroot,
				"placeholderid" => $placeholderid,
				"pagerow" => $pagerow,
			);
			$placeholderidsbeingdragged[$placeholderid] = "true";
		}
		else if ($isindragroot)
		{
			$currentdepth = nxs_getdepth($postid, $placeholderid);
			if ($currentdepth > $depthofdragroot)
			{
				$itemstomove[] = array
				(
					"origrowindex" => $currentrowindex,
					"origdepth" => $currentdepth,
					"depthreltodragroot" => $currentdepth - $depthofdragroot,
					"placeholderid" => $placeholderid,
					"pagerow" => $pagerow,
				);
				$placeholderidsbeingdragged[$placeholderid] = "true";
			}
			else
			{
				// reached the end of the drag
				$isindragroot = false;
			}
		}
	}
	
	//
	// step 2; setup new structure
	//
	$updatedpoststructure = array();
	$currentrowindex = -1;
	foreach ($parsedpoststructure as $pagerow)
	{
		$currentrowindex++;
		$content = $pagerow["content"];
		$placeholderid = nxs_parsepagerow($content);
		
		if ($placeholderidsbeingdragged[$placeholderid] == "true")
		{
			// skippen; item is verplaatst
		}
		else
		{
			// neem de regel over
			$updatedpoststructure[] = $pagerow;
			if ($currentrowindex == $insertafterrowindex)
			{
				// voeg tevens de regels toe die gedragged zijn
				$currentdepth = nxs_getdepth($postid, $placeholderid);
				
				foreach ($itemstomove as $itemtomove)
				{
					$updatedpoststructure[] = $itemtomove["pagerow"];					
				}
			}
		}
	}

	//
	// step 3; persist structure
	//
	$updateresult = nxs_storebinarypoststructure($postid, $updatedpoststructure);
	
	//
	// step 4; update depth of dragged placeholders
	//
	foreach ($itemstomove as $itemtomove)
	{
		$placeholderid = $itemtomove["placeholderid"];
		$depthreltodragroot = $itemtomove["depthreltodragroot"];
		$updateddepth = $depthreltodragroot + $depth;
		
		$temp_array = array();
		$temp_array['depthindex'] = $updateddepth;
		nxs_mergewidgetmetadata_internal($postid, $placeholderid, $temp_array);
	}
	
	//
	// step 5; update items that are derived (based upon the structure and contents of the page, such as menu's)
	// 
	nxs_after_postcontents_updated($postid);

	//
	// create response
	//
	$rendermode = "default";
	$result["html"] = nxs_getrenderedhtml($postid, $rendermode);	
	// that's it :)
	nxs_webmethod_return_ok($result);
}
?>