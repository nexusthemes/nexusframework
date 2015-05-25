<?php

function nxs_popup_contextprocessor_postwizard_getcustompopuphtml($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }

	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postwizard == "") { nxs_webmethod_return_nack("postwizard not set"); }
 	if ($sheet == "") { nxs_webmethod_return_nack("sheet not set"); }
 	 
 	nxs_requirepostwizard($postwizard);
	 
	$functionnametoinvoke = 'nxs_postwizard_' . $postwizard. "_" . $sheet . '_getsheethtml';
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
			nxs_webmethod_return_nack("postwizard context; unable to render sheet '$sheet'/'$postwizard'; function $functionnametoinvoke not found and no genericpopup found");
		}
	}
	
	return $result;
}

function nxs_popup_contextprocessor_postwizard_supportsoptions($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }

	$result = false;
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postwizard == "") { nxs_webmethod_return_nack("postwizard not set"); } 	
 	if ($sheet == "") { nxs_webmethod_return_nack("sheet not set"); }
	
	nxs_requirepostwizard($postwizard);
	
	$functionnametoinvoke = 'nxs_postwizard_' . $postwizard. "_" . $sheet . '_getoptions';
	if (function_exists($functionnametoinvoke))
	{
		$result = true;
	}
	else
	{
		// second try; maybe a generic implementation is found shared by all postwizards....
		$postwizard = "generic";
		nxs_requirepostwizard($postwizard);
		
		$functionnametoinvoke = 'nxs_postwizard_' . $postwizard . "_" . $sheet . '_getoptions';
		if (function_exists($functionnametoinvoke))
		{
			$result = true;
		}
	}
	
	return $result;
}

function nxs_popup_contextprocessor_postwizard_getoptions($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }

	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set"); } 	
 	if ($sheet == "") { nxs_webmethod_return_nack("sheet not set"); }
 	$pagemeta = nxs_get_postmeta($postid);
	$postwizard = nxs_getpostwizard($pagemeta);
	
	nxs_requirepostwizard($postwizard);
	
	$functionnametoinvoke = 'nxs_postwizard_' . $postwizard . "_" . $sheet . '_getoptions';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		// second try; see if we implemented it in generic postwizard ...
		
		$postwizard = "generic";
		nxs_requirepostwizard($postwizard);

		$functionnametoinvokealt = 'nxs_postwizard_' . $postwizard . "_" . $sheet . '_getoptions';
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
function nxs_popup_contextprocessor_postwizard_getpersisteddata($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set in context"); }
	
	$pagemeta = nxs_get_postmeta($postid);
	$postwizard = nxs_getpostwizard($pagemeta);
	
	// delegate
	$result = nxs_get_postmeta($postid);
	
	return $result;
}

// _internal means the globalids (extended data) is already enriched!
function nxs_popup_contextprocessor_postwizard_mergedata_internal($args, $metadata)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set in context"); }
	
	// we store the values in the meta of the post, as there is always just one single
	// postwizard active (no need to store in a key that has postwizard variable)
	nxs_merge_postmeta($postid, $metadata);
	
	$result = array();
	$result["result"] = "OK";
	return $result;
}

// output the nxs_js_savegenericpopup function
function nxs_popup_contextprocessor_postwizard_render_nxs_js_savegenericpopup($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set in context"); }
	
	$pagemeta = nxs_get_postmeta($postid);
	$postwizard = nxs_getpostwizard($pagemeta);
	
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