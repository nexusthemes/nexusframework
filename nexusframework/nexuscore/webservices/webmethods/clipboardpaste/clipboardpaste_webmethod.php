<?php
function nxs_webmethod_clipboardpaste() 
{
	extract($_REQUEST);
 	
 	if ($clipboardcontext == "") { nxs_webmethod_return_nack("clipboardcontext empty?"); }

	nxs_ensure_sessionstarted();
	$clipboardmeta = $_SESSION["nxs_clipboardmeta"];
 	$serializedmetadata = $clipboardmeta["serializedmetadata"];		
	
	if ($clipboardmeta == "")
	{
		$growl = nxs_l18n__("Clipboard is empty, nothing to paste[nxs:growl]", "nxs_td");
	}
	else if ($clipboardcontext == "")
	{
		$growl = nxs_l18n__("Clipboard is empty (no context), nothing to paste[nxs:growl]", "nxs_td");
	}
	else if ($serializedmetadata == "")
	{
		$growl = nxs_l18n__("Clipboard is empty (no metadata), nothing to paste[nxs:growl]", "nxs_td");
	}
	else if ($clipboardcontext != $clipboardmeta["clipboardcontext"])
	{
		$growl = nxs_l18n__("Context of copied data in clipboard is different that context of the destination", "nxs_td");
	}
	else
	{
		$responseargs = array();
		
		// process based upon the selected context 	
	 	if ($clipboardcontext == "widget")
	 	{
		 	if ($containerpostid == "") { nxs_webmethod_return_nack("containerpostid empty? (shp)"); }
		 	if ($postid == "") { nxs_webmethod_return_nack("postid empty? (shp)"); }
		 	if ($placeholderid == "") { nxs_webmethod_return_nack("placeholderid empty? (shp)"); }

			global $nxs_global_current_containerpostid_being_rendered;
			$nxs_global_current_containerpostid_being_rendered = $containerpostid;
		
			global $nxs_global_current_postid_being_rendered;
			$nxs_global_current_postid_being_rendered = $postid;
			
			global $nxs_global_current_postmeta_being_rendered;
			$nxs_global_current_postmeta_being_rendered = nxs_get_postmeta($postid);		

			$metadata = json_decode($serializedmetadata, true);
			$placeholdertemplate = $metadata['type'];
			// get placeholdertemplate from meta
			if (!nxs_iswidgetallowedonpost($postid, $placeholdertemplate, false))
			{
				$responseargs = array();
				$responseargs["msg"] = __("The type of widget that was copied in the clipboard is not allowed in this section");
				webmethod_return_alternativeflow("widgetnotallowed", $responseargs);
				// execution stops here
			}
			
			nxs_overridewidgetmetadata($postid, $placeholderid, $metadata);
		 	nxs_after_postcontents_updated($postid);
		 	$growl = nxs_l18n__("Paste succesful[nxs:growl]", "nxs_td");
		 	
		 	// parse page
		 	$parsedpoststructure = nxs_parsepoststructure($postid);
		 	$rowindex = nxs_getrowindex_for_placeholderid($parsedpoststructure, $placeholderid);
		 	$rendermode = "default";
			$html = nxs_getrenderedrowhtml($postid, $rowindex, $rendermode);
		 	
			//
			// create response
			//
			$responseargs["refresh"] = "row";
			$responseargs["rowindex"] = $rowindex;
			$responseargs["rowhtml"] = $html;
		}
		else if ($clipboardcontext == "row")
		{
			if ($containerpostid == "") { nxs_webmethod_return_nack("containerpostid empty? (shp)"); }
		 	if ($postid == "") { nxs_webmethod_return_nack("postid empty? (shp)"); }
		 	if ($rowindex == "") { nxs_webmethod_return_nack("rowindex empty? (shp)"); }

			global $nxs_global_current_containerpostid_being_rendered;
			$nxs_global_current_containerpostid_being_rendered = $containerpostid;
		
			global $nxs_global_current_postid_being_rendered;
			$nxs_global_current_postid_being_rendered = $postid;
			
			global $nxs_global_current_postmeta_being_rendered;
			$nxs_global_current_postmeta_being_rendered = nxs_get_postmeta($postid);		

			$clipboardata = json_decode($serializedmetadata, true);
			$rowtemplate = $clipboardata["rowtemplate"];
			
			$newrow = array();
			$newrow["rowindex"] = "new";
			$newrow["pagerowtemplate"] = $rowtemplate;
			$newrow["pagerowid"] = nxs_allocatenewpagerowid($postid);
			$newrow["pagerowattributes"] = "pagerowtemplate='" . $rowtemplate . "' pagerowid='" . $pagerowid . "'";
			$newrow["content"] = nxs_getpagerowtemplatecontent($rowtemplate);
		
			$updatedpoststructure = nxs_parsepoststructure($postid);		
			$updatedpoststructure[$rowindex] = $newrow;
		
			// insert row into structure
			//$updatedpoststructure = nxs_insertarrayindex($poststructure, $newrow, $rowindex);
			
			// persist structure
			$updateresult = nxs_storebinarypoststructure($postid, $updatedpoststructure);
			
			// decorate the widgets *in* the page row
			
			$content = $newrow["content"];
			$placeholderids = nxs_parseplaceholderidsfrompagerow($content);
			$placeholderindex = 0;
			foreach ($placeholderids as $placeholderid)
			{
				// get source metadata
				$metadata = $clipboardata["widgetsmetadata"][$placeholderindex];
				// store destination metadata
				nxs_overridewidgetmetadata($postid, $placeholderid, $metadata);
				
				$placeholderindex++;
			}
			
			// output / return; the HTML for the row we just pasted
			nxs_after_postcontents_updated($postid);
			// parse page
			$parsedpoststructure = nxs_parsepoststructure($postid);
			$rendermode = "default";
			$html = nxs_getrenderedrowhtml($postid, $rowindex, $rendermode);
			
			//
			// create response
			//
			$responseargs["refresh"] = "row";
			$responseargs["postid"] = $postid;
			$responseargs["rowindex"] = $rowindex;
			$responseargs["html"] = $html;
		}
		else if ($clipboardcontext == "maincontent:contentbuilder")
		{
			$metadata = json_decode($serializedmetadata, true);
			$sourcepostid = $metadata['sourcepostid'];
			//
			if ($destinationpostid == "") { nxs_webmethod_return_nack("destinationpostid empty? (shp)"); }
			if ($destinationpostid == "0") { nxs_webmethod_return_nack("destinationpostid empty? (shp)"); }
			if ($sourcepostid == "") { nxs_webmethod_return_nack("sourcepostid empty? (shp)"); }
			if ($sourcepostid == "0") { nxs_webmethod_return_nack("sourcepostid empty? (shp)"); }
			if ($sourcepostid == $destinationpostid) { nxs_webmethod_return_nack("sourcepostid is the same as the destination postid (shp)"); }
			
			// replicate the data structure and metafields from source to destination
			$structure = nxs_parsepoststructure($sourcepostid);
			nxs_storebinarypoststructure($destinationpostid, $structure);
			
			// replicate the data per row
			$rowindex = 0;
			foreach ($structure as $pagerow)
			{
				// ---------------- ROW META
				
				// replicate the metadata of the row
				$pagerowid = nxs_parserowidfrompagerow($pagerow);
				if (isset($pagerowid))
				{
					// get source meta
					$metadata = nxs_getpagerowmetadata($sourcepostid, $pagerowid);
					// store destination meta
					nxs_overridepagerowmetadata($destinationpostid, $pagerowid, $metadata);
				}
				
				// ---------------- WIDGET META
				
				// replicate the metadata of the widgets in the row
				$content = $pagerow["content"];
				$placeholderids = nxs_parseplaceholderidsfrompagerow($content);
				foreach ($placeholderids as $placeholderid)
				{
					// get source metadata
					$metadata = nxs_getwidgetmetadata($sourcepostid, $placeholderid);
					// store destination metadata
					nxs_overridewidgetmetadata($destinationpostid, $placeholderid, $metadata);
				}
			}
			
			// huray!
		}
		else
		{
			nxs_webmethod_return_nack("unsupported clipboardcontext: " . $clipboardcontext);
		}
	}
	
	$responseargs["growl"] = $growl;
	
	nxs_webmethod_return_ok($responseargs); 	
}
?>