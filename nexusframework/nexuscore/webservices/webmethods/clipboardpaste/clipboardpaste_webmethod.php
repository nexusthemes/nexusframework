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
			$nxs_global_current_postmeta_being_rendered = nxs_get_corepostmeta($postid);		

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
			
			// clone referenced fields in this widget when needed
			nxs_clonereferencedfieldsforwidget($postid, $placeholderid);
			
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
			$nxs_global_current_postmeta_being_rendered = nxs_get_corepostmeta($postid);		

			$clipboardata = json_decode($serializedmetadata, true);
			$rowtemplate = $clipboardata["rowtemplate"];
			
			$newrow = array();
			$newrow["rowindex"] = "new";
			$newrow["pagerowtemplate"] = $rowtemplate;
			$newrow["pagerowid"] = nxs_allocatenewpagerowid($postid);
			$newrow["pagerowattributes"] = "pagerowtemplate='" . $rowtemplate . "' pagerowid='" . $pagerowid . "'";
			$newrow["content"] = nxs_getpagerowtemplatecontent($rowtemplate);
		
			// override row in existing structure
			$updatedpoststructure = nxs_parsepoststructure($postid);		
			$updatedpoststructure[$rowindex] = $newrow;
			
			// persist structure
			$updateresult = nxs_storebinarypoststructure($postid, $updatedpoststructure);
			
			// override the metadata of the row itself
			$metadata = $clipboardata["rowmetadata"];
			$pagerowid = $newrow["pagerowid"];
			nxs_overridepagerowmetadata($postid, $pagerowid, $metadata);
			
			// override the metadata of the widgets in this row
			$content = $newrow["content"];
			$placeholderids = nxs_parseplaceholderidsfrompagerow($content);
			$placeholderindex = 0;
			foreach ($placeholderids as $placeholderid)
			{
				// get source metadata
				$metadata = $clipboardata["widgetsmetadata"][$placeholderindex];
				
				// store destination metadata
				nxs_overridewidgetmetadata($postid, $placeholderid, $metadata);
				
				// clone referenced fields in this widget when needed
				nxs_clonereferencedfieldsforwidget($postid, $placeholderid);

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
			
			$replicatemetadata = array
			(
				"sourcepostid" => $sourcepostid,
				"destinationpostid" => $destinationpostid,
			);
			nxs_replicatepoststructure($replicatemetadata);
			
			// loop over all widgets in the destination (replicated) post
			$placeholderidstometadatainpost = nxs_getwidgetsmetadatainpost($destinationpostid);
			foreach ($placeholderidstometadatainpost as $currentplaceholderid => $currentmetadata)
			{
				// clone the widgets that are referencing other posts (if any)
				nxs_clonereferencedfieldsforwidget($destinationpostid, $currentplaceholderid);
			}
		}
		else
		{
			nxs_webmethod_return_nack("unsupported clipboardcontext: " . $clipboardcontext);
		}
	}
	
	$responseargs["growl"] = $growl;
	
	nxs_webmethod_return_ok($responseargs); 	
}

function nxs_dataprotection_nexusframework_webmethod_clipboardpaste_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("webmethod-none");
}

?>