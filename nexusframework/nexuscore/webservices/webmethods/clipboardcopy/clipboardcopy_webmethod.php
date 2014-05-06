<?php
function nxs_webmethod_clipboardcopy() 
{
	extract($_REQUEST);
 	
 	if ($clipboardcontext == "") { nxs_webmethod_return_nack("clipboardcontext empty?"); }

	// first of all wipe the session data and reset the clipboard context,
	// the session data will be filled in the following steps
	
	// process based upon the selected context 	
 	if ($clipboardcontext == "widget")
 	{
	 	if ($postid == "") { nxs_webmethod_return_nack("postid empty? (shp)"); }
	 	if ($placeholderid == "") { nxs_webmethod_return_nack("placeholderid empty? (shp)"); }

	 	$placeholdertype = nxs_getplaceholdertemplate($postid, $placeholderid); 	
	 	$serializedmetadata = nxs_getwidgetmetadata_serialized($postid, $placeholderid);
	}
	else if ($clipboardcontext == "maincontent:contentbuilder")
	{
		if ($postid == "") { nxs_webmethod_return_nack("postid empty? (shp)"); }
		$metadata = array("sourcepostid"=>$postid);
		$serializedmetadata = json_encode($metadata);
		
		$title = nxs_gettitle_for_postid($postid);
		$growl = "Content builder data of post " . $title . " (postid " . $postid . " ) is stored in the clipboard";
	}
	else
	{
		nxs_webmethod_return_nack("unsupported clipboardcontext: " . $clipboardcontext);
	}
	
	// sanity checks before storing session data
	if (!isset($clipboardcontext)) { nxs_webmethod_return_nack("clipboardcontext not set"); }
	if (!isset($serializedmetadata)) { nxs_webmethod_return_nack("serializedmetadata not set"); }

	// store metadata in session
 	$clipboardmeta = array("clipboardcontext"=>$clipboardcontext,"serializedmetadata"=>$serializedmetadata);	
 	nxs_ensure_sessionstarted();
	$_SESSION["nxs_clipboardmeta"] = $clipboardmeta;
	
	if (!isset($growl))
	{
		$growl = nxs_l18n__("Information is saved to the clipboard. Use CTRL-V to paste it on any page[nxs:growl]", "nxs_td");
	}
	
	// create response
	$responseargs = array();
	$responseargs["growl"] = $growl;
	nxs_webmethod_return_ok($responseargs);
}
?>