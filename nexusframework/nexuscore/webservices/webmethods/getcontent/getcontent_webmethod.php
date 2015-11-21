<?php

function nxs_webmethod_getcontent() 
{
	extract($_REQUEST);
	
	// below = new

	$contextpieces = explode("_", $contentcontext);
	$entity = $contextpieces[0];	// bijv. "placeholder", "page", "site"...
	
	$args = $_REQUEST;
	
	if ($entity == "anonymouspost")
	{
		// format of the context =anonymouspost_{postid}_{postid}
		$containerpostid = $contextpieces[1];
		if ($containerpostid == "")
		{
			nxs_webmethod_return_nack("containerpostid not set in context");
		}
		$postid = $contextpieces[2];
		if ($postid == "")
		{
			nxs_webmethod_return_nack("postid not set in context");
		}
		
		//
		// for this specific webmethod we set the global render variables
		global $nxs_global_current_containerpostid_being_rendered;
		$nxs_global_current_containerpostid_being_rendered = $containerpostid;		
				
		$rendermode = "anonymous";
		
		$result = array();
		$result["html"] = nxs_getrenderedhtml($postid, $rendermode); 
		
		nxs_webmethod_return_ok($result);	
	}
	else if ($entity == "row")		// haalt de html op van een enkele row (GUI)
	{
		// format of the context = row_{postid}_{rowid}
		$containerpostid = $contextpieces[1];
		if ($containerpostid == "")
		{
			nxs_webmethod_return_nack("containerpostid not set in context");
		}
		$postid = $contextpieces[2];
		if ($postid == "")
		{
			nxs_webmethod_return_nack("postid not set in context");
		}
		$rowid = $contextpieces[3];
		if ($rowid == "")
		{
			nxs_webmethod_return_nack("rowid not set in context");
		}
		
		//
		// for this specific webmethod we set the global render variables
		global $nxs_global_current_containerpostid_being_rendered;
		$nxs_global_current_containerpostid_being_rendered = $containerpostid;		
		global $nxs_global_current_postid_being_rendered;
		$nxs_global_current_postid_being_rendered = $postid;
		global $nxs_global_current_postmeta_being_rendered;
		$nxs_global_current_postmeta_being_rendered = nxs_get_postmeta($postid);
		global $nxs_global_current_render_mode;
		$nxs_global_current_render_mode = "default";
		
		$args["postid"] = $postid;
		$args["rowid"] = $rowid;
		$args["rendermode"] = $nxs_global_current_render_mode;
		
		$filetobeincluded = NXS_FRAMEWORKPATH . "/nexuscore/row/row.php";
		if (file_exists($filetobeincluded))
		{
			require_once($filetobeincluded);
			
			if ($contenttype == "webpart")
			{
				$functionnametoinvoke = 'nxs_pagerow_render_webpart_row';
				//
				// invokefunction
				//
				if (function_exists($functionnametoinvoke))
				{
					$result = call_user_func($functionnametoinvoke, $args);
				}
				else
				{
					nxs_webmethod_return_nack("function not found; " . $functionnametoinvoke . " in " . $filetobeincluded);	
				}		
			}			
			else
			{
				nxs_webmethod_return_nack("unsupported contenttype");
			}
		}
		else
		{
			nxs_webmethod_return_nack("file not found " . $filetobeincluded);
		}

		nxs_webmethod_return_ok($result);		
	}
	else if ($entity == "rows")
	{
		// format of the context = row_{postid}_{rowid}
		$containerpostid = $contextpieces[1];
		if ($containerpostid == "")
		{
			nxs_webmethod_return_nack("containerpostid not set in context");
		}
		$postid = $contextpieces[2];
		if ($postid == "")
		{
			nxs_webmethod_return_nack("postid not set in context");
		}
		$rendermode = $contextpieces[3];
		if ($rendermode == "")
		{
			nxs_webmethod_return_nack("rendermode not set in context");
		}
		//bijv. $rendermode = "default";
		
		$result = array();
		$result["html"] = nxs_getrenderedhtmlincontainer($containerpostid, $postid, $rendermode);
		
		nxs_webmethod_return_ok($result);
	}
	else
	{
		nxs_webmethod_return_nack("entity " . $entity . " is not (yet?) supported / contentcontext:" . $contentcontext);
	}
}

?>