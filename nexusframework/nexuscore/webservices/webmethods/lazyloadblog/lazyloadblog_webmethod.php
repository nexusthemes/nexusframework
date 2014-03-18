<?php

function nxs_webmethod_lazyloadblog() 
{
	extract($_REQUEST);
	
	$result = "ajax content";
	
	// bijv. ...?action=nxs_ajax_webmethods&webmethod=lazyloadblog&postcontainerid=6&postid=6&page=1&placeholderid=l1162511773
	
	$widgetmetadata = nxs_getwidgetmetadata($postid, $placeholderid);
	if ($widgetmetadata["type"] != "blog") { nxs_webmethod_return_nack("unexpected type?" . $widgetmetadata["type"]); }
	
	$mixedattributes = array();
	$mixedattributes["postid"] = $postid;
	$mixedattributes["rendermode"] = "anonymous";
	$mixedattributes["contenttype"] = "webpart";
	$mixedattributes["webparttemplate"] = "render_htmlvisualization";
	$mixedattributes["placeholderid"] = $placeholderid;
	$mixedattributes["placeholdertemplate"] = "blog";	// verplicht
	
	$placeholderrenderresult = nxs_getrenderedwidget($mixedattributes);
	
	$responseargs = array();
	// $responseargs["appendhtml"] = "hello";
	$responseargs["appendhtmlraw"] = $placeholderrenderresult;
	$responseargs["paging_page"] = $_REQUEST["page"];
	nxs_webmethod_return_ok($responseargs);
}

?>