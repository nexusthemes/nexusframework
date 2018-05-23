<?php
function nxs_webmethod_initplaceholderdata() 
{
	extract($_REQUEST);
 	
 	if ($postid == "") { nxs_webmethod_return_nack("postid empty"); }
 	if ($placeholdertemplate == "") { nxs_webmethod_return_nack("placeholdertemplate empty"); }
 	if ($placeholderid == "") { nxs_webmethod_return_nack("placeholderid empty"); }

	global $nxs_global_current_postid_being_rendered;
	$nxs_global_current_postid_being_rendered = $postid;
	global $nxs_global_current_postmeta_being_rendered;
	$nxs_global_current_postmeta_being_rendered = nxs_get_corepostmeta($postid);
	global $nxs_global_current_containerpostid_being_rendered;
	$nxs_global_current_containerpostid_being_rendered = $containerpostid;
	
	//
	//
	//
	
 	$result = nxs_initializewidget($_REQUEST);
 	
	// als de placeholder is bijgewerkt, dan impliceert dit dat de 
	// content op de pagina is aangepast. Het kan zou zijn dat 
	// er hierdoor ook meer moet worden aangepast. Bijvoorbeeld:
	// als een gebruiker de url van een menu item aanpast,
	// dan moet het menu worden bijgewerkt.
	nxs_after_postcontents_updated($postid);
	
	// parse page
 	$parsedpoststructure = nxs_parsepoststructure($postid);
 	$rowindex = nxs_getrowindex_for_placeholderid($parsedpoststructure, $placeholderid);
 	
 	$rendermode = "default";
	$html = nxs_getrenderedrowhtml($postid, $rowindex, $rendermode);
 	
	//
	// create response
	//
	$responseargs = array();
	$responseargs["growl"] = nxs_l18n__("Widget initialized", "nxs_td");
	$responseargs["rowindex"] = $rowindex;
	$responseargs["rowhtml"] = $html;
	
	nxs_webmethod_return_ok($responseargs); 	
}

function nxs_dataprotection_nexusframework_webmethod_initplaceholderdata_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("webmethod-none");
}

?>