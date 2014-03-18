<?php
function nxs_webmethod_removemenuitem() 
{	
	extract($_REQUEST);

	$result = array();

	if ($postid == "")
	{
		nxs_webmethod_return_nack("postid not specified /nxs_webmethod_removemenuitem/");
	}
	if ($rowid == "")
	{
		nxs_webmethod_return_nack("rowid not specified");
	}
	
	$parsedpoststructure = nxs_parsepoststructure($postid);
	
	$headrows = array();		// bevat items boven de items die verplaatst moeten worden
	$deletedrows = array();		// de items die verwijderd moeten worden
	$footerrows = array();	// de items onder de items die verplaatst moeten worden
	$currentrowindex = -1;
	$infootersection = false;
	$indeletedsection = false;
	
	$sourcerowindex = $rowid;
	
	foreach ($parsedpoststructure as $pagerow)
	{
		$currentrowindex++;
		if ($currentrowindex < $sourcerowindex && $indeletedsection == false && $infootersection == false)		
		{
			$headrows[] = $pagerow;
		}
		else if ($currentrowindex == $sourcerowindex)
		{
			// eerste regel van de deleted sectie
			$indeletedsection == true;
			
			$deletedrows[] = $pagerow;
			// get original depth of deleted item
			
			$content = $pagerow["content"];
			$placeholderid = nxs_parsepagerow($content);
			
			$origdepthofdeletedroot = nxs_getdepth($postid, $placeholderid);
		}
		else
		{
			if ($infootersection)
			{
				$footerrows[] = $pagerow;
			}
			else
			{			
				$content = $pagerow["content"];
				$placeholderid = nxs_parsepagerow($content);
				$depthcurrentrow = nxs_getdepth($postid, $placeholderid);
				if ($depthcurrentrow <= $origdepthofdeletedroot)
				{
					// dit impliceert dat we het eerste item van de footer hebben bereikt
					$footerrows[] = $pagerow;
					$infootersection = true;
				}
				else
				{
					// dit impliceert dat dit item een child is van het item dat verwijderd wordt
					$deletedrows[] = $pagerow;
				}
			}
		}
	}
	
	// construct the new page structure
	$updatedpoststructure = array();
	$updatedpoststructure = array_merge($updatedpoststructure, $headrows);
	// dus niet de deleted rows
	$updatedpoststructure = array_merge($updatedpoststructure, $footerrows);
	
	// persist structure
	$updateresult = nxs_storebinarypoststructure($postid, $updatedpoststructure);
	
	// update items that are derived (based upon the structure and contents of the page, such as menu's)
	nxs_after_postcontents_updated($postid);

	//
	// create response
	//
	$rendermode = "default";
	$result = array();
	$result["html"] = nxs_getrenderedhtml($postid, $rendermode);	
	// that's it :)
	nxs_webmethod_return_ok($result);
}
?>