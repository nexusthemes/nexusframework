<?php

function nxs_getwidgetmetadata_serialized($postid, $placeholderid)
{
	$widgetmetadata = nxs_getwidgetmetadata($postid, $placeholderid);
	$serialized = json_encode($widgetmetadata);
		
	return $serialized;
}

function nxs_getrow_serialized($postid, $rowindex)
{
	$result = array();
	
	$rows = nxs_parsepoststructure($postid);
	$row = $rows[$rowindex];
	// $row is bijv. 
	// array(6) 
	// {
	//  ["rowindex"]=> int(0)
	//  ["pagerowtemplate"]=> string(4) "1212"
	//  ["pagerowid"]=> string(14) "prid1101840585"
	//  ["pagerowattributes"]=> string(50) " pagerowid="prid1101840585" pagerowtemplate="1212""
	//  ["content"]=>  string(220) [nxsphcontainer width="1/2"]  [nxsplaceholder placeholderid='a1080165985'][/nxsplaceholder] [/nxsphcontainer] [nxsphcontainer width="1/2"]  [nxsplaceholder placeholderid='b1080165985'][/nxsplaceholder] [/nxsphcontainer]"?  ["outercontent"]=>?  string(295) "[nxspagerow pagerowid="prid1101840585" pagerowtemplate="1212"] [nxsphcontainer width="1/2"]  [nxsplaceholder placeholderid='a1080165985'][/nxsplaceholder] [/nxsphcontainer] [nxsphcontainer width="1/2"]  [nxsplaceholder placeholderid='b1080165985'][/nxsplaceholder] [/nxsphcontainer][/nxspagerow]"?}
	
	$rowtemplate = $row["pagerowtemplate"];
	$result["rowtemplate"] = $rowtemplate;
	$pagerowid = $row["pagerowid"];
	
	$rowcontent = $row["content"];
	
	// rowmeta
	$rowmetadata = nxs_getpagerowmetadata($postid, $pagerowid);
	$result["rowmetadata"] = $rowmetadata;
	
	// widgetmeta
	$placeholderids = nxs_parseplaceholderidsfrompagerow($rowcontent);
	foreach ($placeholderids as $placeholderid)
	{
		$widgetmetadata = nxs_getwidgetmetadata($postid, $placeholderid);
		$result["widgetsmetadata"][] = $widgetmetadata;
	}
	
	//
	//
	//
	$serialized = json_encode($result);
	
	return $serialized;
}

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

	 	$serializedmetadata = nxs_getwidgetmetadata_serialized($postid, $placeholderid);
	}
	else if ($clipboardcontext == "row")
 	{
	 	if ($postid == "") { nxs_webmethod_return_nack("postid empty"); }
	 	if ($rowindex == "") { nxs_webmethod_return_nack("rowindex empty"); }
	 	
	 	$serializedmetadata = nxs_getrow_serialized($postid, $rowindex);
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