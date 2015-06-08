<?php

function nxs_popup_contextprocessor_pagetemplate_getcustompopuphtml($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }

	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set"); }
	if ($pagetemplate == "") { nxs_webmethod_return_nack("pagetemplate not set"); }
 	if ($sheet == "") { nxs_webmethod_return_nack("sheet not set"); }
 	
 	//$pagemeta = nxs_get_postmeta($postid);
	//$pagetemplate = nxs_getpagetemplateforpostid($postid);	// no! the pagetemplate should be in context!
	nxs_requirepagetemplate($pagetemplate);
 	 	
 	// downwards compatibility...
 	$args["postid"] = $postid;
 	$args["pagetemplate"] = $pagetemplate;
	
	$functionnametoinvoke = 'nxs_pagetemplate_' . $pagetemplate . "_" . $sheet . '_getsheethtml';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		if (nxs_genericpopup_exists($sheet))
		{
			return nxs_popup_rendergenericpopup($sheet, $args);	
		}
		else
		{
			nxs_webmethod_return_nack("pagetemplate context; unable to render sheet '$sheet' for '$pagetemplate'; function $functionnametoinvoke not found and no genericpopup found");
		}
	}
	
	return $result;
}

function nxs_popup_contextprocessor_pagetemplate_supportsoptions($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }

	$result = false;
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set"); } 	
 	if ($sheet == "") { nxs_webmethod_return_nack("sheet not set"); }
 	
 	if (isset($pagetemplate))
 	{
 		// already set by browser
 	}
 	else
 	{
	 	$pagemeta = nxs_get_postmeta($postid);
		$pagetemplate = nxs_getpagetemplateforpostid($postid);
	}
	
	nxs_requirepagetemplate($pagetemplate);
	
	$functionnametoinvoke = 'nxs_pagetemplate_' . $pagetemplate . "_" . $sheet . '_getoptions';
	if (function_exists($functionnametoinvoke))
	{
		$result = true;
	}
	else
	{
		// second try; maybe a generic implementation is found shared by all pagetemplates....
		$pagetemplate = "generic";
		nxs_requirepagetemplate($pagetemplate);
		
		$functionnametoinvoke = 'nxs_pagetemplate_' . $pagetemplate . "_" . $sheet . '_getoptions';
		if (function_exists($functionnametoinvoke))
		{
			$result = true;
		}
	}
	
	return $result;
}

function nxs_popup_contextprocessor_pagetemplate_getoptions($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }

	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set"); } 	
 	if ($sheet == "") { nxs_webmethod_return_nack("sheet not set"); }
 	$pagemeta = nxs_get_postmeta($postid);
	$pagetemplate = nxs_getpagetemplateforpostid($postid);
	
	nxs_requirepagetemplate($pagetemplate);
	
	$functionnametoinvoke = 'nxs_pagetemplate_' . $pagetemplate . "_" . $sheet . '_getoptions';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		// second try; see if we implemented it in generic pagetemplate ...
		
		$pagetemplate = "generic";
		nxs_requirepagetemplate($pagetemplate);

		$functionnametoinvokealt = 'nxs_pagetemplate_' . $pagetemplate . "_" . $sheet . '_getoptions';
		if (function_exists($functionnametoinvokealt))
		{
			$result = call_user_func($functionnametoinvokealt, $args);
		}
		else
		{
			nxs_webmethod_return_nack("unable to get options; functions not found; $functionnametoinvoke nor $functionnametoinvokealt");
		}
	}
	
	return $result;
}

//
// a popup processor is responsible for mapping specific args
// for this specific popup type. It "knows" how to map
// fields that are to be retrieved and persisted
//
function nxs_popup_contextprocessor_pagetemplate_getpersisteddata($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set in context"); }
	
	$pagemeta = nxs_get_postmeta($postid);
	$pagetemplate = nxs_getpagetemplateforpostid($postid);
	
	// delegate
	$result = nxs_get_postmeta($postid);
	
	return $result;
}

// _internal means the globalids (extended data) is already enriched!
function nxs_popup_contextprocessor_pagetemplate_mergedata_internal($args, $metadata)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set in context"); }
	
	// we store the values in the meta of the post, as there is always just one single
	// pagetemplate active (no need to store in a key that has pagetemplate variable)
	nxs_merge_postmeta($postid, $metadata);
	
	$result = array();
	$result["result"] = "OK";
	return $result;
}

// output the nxs_js_savegenericpopup function
function nxs_popup_contextprocessor_pagetemplate_render_nxs_js_savegenericpopup($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set in context"); }
	
	$pagemeta = nxs_get_postmeta($postid);
	$pagetemplate = nxs_getpagetemplateforpostid($postid);
	
	?>
	function nxs_js_savegenericpopup()
  {
		nxs_js_savegenericpopup_internal
		(
			function(response)
			{
				nxs_js_refreshcurrentpage();
	    }
	  );
	}
	
	// internal function that handles saving of generic popup information,
  // if the saving is finished, the method invokes the specified function
  function nxs_js_savegenericpopup_internal(invokewhenavailable) 
  {
    nxs_js_setpopupdatefromcontrols();
    
    var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
    jQ_nxs.ajax 
    (
      {
        type: 'POST',
        data: 
        {
          "action": "nxs_ajax_webmethods",
          "webmethod": "updategenericpopupdata",
					"clientpopupsessioncontext": nxs_js_getescaped_popupsession_context(),
					"clientpopupsessiondata": nxs_js_getescaped_popupsession_data(),
					"clientshortscopedata": nxs_js_popup_getescapedshortscopedata()            
        },
        dataType: 'JSON',
        url: ajaxurl, 
        success: function(response) 
        {
          nxs_js_log(response);
          if (response.result == "OK") 
          {
            invokewhenavailable(response);                       
          } 
          else 
          {
            nxs_js_popup_notifyservererror();
            nxs_js_log(response);
          }
        },
        error: function(response) 
        {
          nxs_js_popup_notifyservererror();
          nxs_js_log(response);
        }										
      }
    );
  }
  <?php
}

?>