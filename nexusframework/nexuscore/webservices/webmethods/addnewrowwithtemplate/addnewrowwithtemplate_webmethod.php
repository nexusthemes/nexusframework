<?php
function nxs_webmethod_addnewrowwithtemplate() 
{	
	extract($_REQUEST);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set"); }
	if ($insertafterrowindex == "") { nxs_webmethod_return_nack("insertafterrowindex not set"); }	
	if ($pagerowtemplate == "") { nxs_webmethod_return_nack("pagerowtemplate niet gezet"); }	
	
	$wpposttype = nxs_getwpposttype($postid);
	$nxsposttype = nxs_getnxsposttype_by_postid($postid);
	$pagetemplate = nxs_getpagetemplateforpostid($postid);
	
	//
	//
	//
	
	$prtargs = array();
	$prtargs["invoker"] = "rowdrop";
	$prtargs["wpposttype"] = $wpposttype;
	$prtargs["nxsposttype"] = $nxsposttype;
	$prtargs["pagetemplate"] = $pagetemplate;		
	$postrowtemplates = nxs_getpostrowtemplates($prtargs);

	// verify the pagerowtemplate is allowed to be placed
	if (!in_array($pagerowtemplate, $postrowtemplates))
	{
		$responseargs = array();
		$responseargs["msg"] = __("This row cannot be placed here");
		$responseargs["wpposttype"] = $wpposttype;
		$responseargs["nxsposttype"] = $nxsposttype;
		$responseargs["pagetemplate"] = $pagetemplate;
		$responseargs["pagerowtemplate"] = $pagerowtemplate;
			
		webmethod_return_alternativeflow("rowtemplatenotallowed", $responseargs);
		// execution ends here
	}
	
	// get poststructure (list of rowindex, pagerowtemplate, pagerowattributes, content)
	$poststructure = nxs_parsepoststructure($postid);
	
	$pagerowid = nxs_allocatenewpagerowid($postid);

	// create new row
	$newrow = array();
	$newrow["rowindex"] = "new";
	$newrow["pagerowtemplate"] = $pagerowtemplate;
	$newrow["pagerowid"] = $pagerowid;
	$newrow["pagerowattributes"] = "pagerowtemplate='" . $pagerowtemplate . "' pagerowid='" . $pagerowid . "'";
	$newrow["content"] = nxs_getpagerowtemplatecontent($pagerowtemplate);

	// insert row into structure
	$updatedpoststructure = nxs_insertarrayindex($poststructure, $newrow, $insertafterrowindex+1);
	
	// persist structure
	$updateresult = nxs_storebinarypoststructure($postid, $updatedpoststructure);
	
	if ($placeholdertemplate != "")
	{
		// apply placeholdertemplates for the placeholders just created...
		$slideplaceholderid = nxs_parsepagerow($newrow["content"]);

		$clientpopupsessioncontext = array();
		$clientpopupsessioncontext["postid"] = $postid;
		$clientpopupsessioncontext["placeholderid"] = $slideplaceholderid;
		$clientpopupsessioncontext["contextprocessor"] = "widgets";
		$clientpopupsessioncontext["sheet"] = "home";

		$args = array();
		$args["clientpopupsessioncontext"] = $clientpopupsessioncontext;
		$args["placeholdertemplate"] = $placeholdertemplate;
		
		// for downwards compatibility we replicate the postid and placeholderid to the 'root'
		$args["postid"] = $postid;
		$args["placeholderid"] = $placeholderid;
		
		nxs_initializewidget($args);
	}
	else
	{
		// geen verdere initialisatie
	}
	
	// ensure all placeholderids are correctly initialized
	$placeholderids = nxs_parseplaceholderidsfrompagerow($newrow["content"]);
	
	foreach ($placeholderids as $placeholderid)
	{
		$template = nxs_getplaceholdertemplate($postid, $placeholderid);
		if ($template == "")
		{
			// requires initialization
			$args = array();
			
			$clientpopupsessioncontext = array();
			
			$clientpopupsessioncontext["postid"] = $postid;
			$clientpopupsessioncontext["placeholderid"] = $placeholderid;
			$clientpopupsessioncontext["contextprocessor"] = "widgets";
			$clientpopupsessioncontext["sheet"] = "home";

			$args["placeholdertemplate"] = "undefined";
			$args["clientpopupsessioncontext"] = $clientpopupsessioncontext;
			
			// for downwards compatibility we replicate the postid and placeholderid to the 'root'
			$args["postid"] = $postid;
			$args["placeholderid"] = $placeholderid;

			$result = nxs_initializewidget($args);
			
			//
			//
			//
			$template = nxs_getplaceholdertemplate($postid, $placeholderid);
			if ($template == "")
			{
				nxs_webmethod_return_nack("template not set");
			}
		}
		else
		{
			// already initialized
		}
	}

	// update items that are derived (based upon the structure and contents of the page, such as menu's)
	nxs_after_postcontents_updated($postid);
	
	//
	// create response
	//
	$responseargs = $updateresult;
	$responseargs["template"] = $placeholdertemplate;
	$responseargs["resultstatus"] = "ok";
	$responseargs["placeholderids"] = $placeholderids;
	$responseargs["rowindex"] = $insertafterrowindex + 1;
	$responseargs["postid"] = $postid;
	
	nxs_webmethod_return_ok($responseargs);
}
