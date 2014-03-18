<?php

function nxs_popup_contextprocessor_pagerow_getcustompopuphtml($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }

	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set"); }
 	if ($pagerowid == "") { nxs_webmethod_return_nack("pagerowid not set"); }
 	if ($sheet == "") { nxs_webmethod_return_nack("sheet not set"); }
 	
 	// downwards compatibility...
 	$args["postid"] = $postid;
 	$args["pagerowid"] = $pagerowid;

	// load row functions	
	$filetobeincluded = NXS_FRAMEWORKPATH . "/nexuscore/row/row.php";
	require_once($filetobeincluded);
	
	$functionnametoinvoke = 'nxs_pagerow_' . $sheet . '_getsheethtml';
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
			nxs_webmethod_return_nack("pagerow context; unable to render sheet $sheet; function $functionnametoinvoke not found and no genericpopup found");
		}
	}
	
	return $result;
}

function nxs_popup_contextprocessor_pagerow_supportsoptions($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }

	$result = false;
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set"); }
 	if ($pagerowid == "") { nxs_webmethod_return_nack("pagerowid not set"); }
 	if ($sheet == "") { nxs_webmethod_return_nack("sheet not set"); }
	
	// load row functions	
	$filetobeincluded = NXS_FRAMEWORKPATH . "/nexuscore/row/row.php";
	require_once($filetobeincluded);
	
	$functionnametoinvoke = 'nxs_pagerow_' . $sheet . '_getoptions';
	if (function_exists($functionnametoinvoke))
	{
		$result = true;
	}
	
	return $result;
}

function nxs_popup_contextprocessor_pagerow_getoptions($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }

	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set"); }
 	if ($pagerowid == "") { nxs_webmethod_return_nack("pagerowid not set"); }
 	if ($sheet == "") { nxs_webmethod_return_nack("sheet not set"); }
	
	// load row functions	
	$filetobeincluded = NXS_FRAMEWORKPATH . "/nexuscore/row/row.php";
	require_once($filetobeincluded);
	
	$functionnametoinvoke = 'nxs_pagerow_' . $sheet . '_getoptions';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		nxs_webmethod_return_nack("unable to get options; function not found; $functionnametoinvoke");
	}
	
	return $result;
}

//
// a popup processor is responsible for mapping specific args
// for this specific popup type. It "knows" how to map
// fields that are to be retrieved and persisted
//
function nxs_popup_contextprocessor_pagerow_getpersisteddata($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set in context"); }
	if ($pagerowid == "") { nxs_webmethod_return_nack("pagerowid not set in context"); }
	
	// delegate
	$result = nxs_getpagerowmetadata($postid, $pagerowid);
	
	return $result;
}

// _internal means the globalids (extended data) is already enriched!
function nxs_popup_contextprocessor_pagerow_mergedata_internal($args, $metadata)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set in context"); }
	if ($pagerowid == "") { nxs_webmethod_return_nack("pagerowid not set in context"); }
	
	// delegate
	nxs_mergepagerowmetadata_internal($postid, $pagerowid, $metadata);
	
	$result = array();
	$result["result"] = "OK";
	return $result;
}

// output the nxs_js_savegenericpopup function
function nxs_popup_contextprocessor_pagerow_render_nxs_js_savegenericpopup($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set in context"); }
	if ($pagerowid == "") { nxs_webmethod_return_nack("pagerowid not set in context"); }
	
	?>
	function nxs_js_savegenericpopup()
  {
		nxs_js_savegenericpopup_internal
		(
			function(response)
			{
				
	      // update UI, the 'current' id will be overriden because null is specified as third parameter
	      nxs_js_rerender_row_for_pagerow("<?php echo $postid;?>", "<?php echo $pagerowid;?>");
	
				// close the pop up
	      nxs_js_closepopup_unconditionally();
	    }
	  );
	}
	
	// internal function that handles saving of generic popup information,
  // if the saving is finished, the method invokes the specified function
  function nxs_js_savegenericpopup_internal(invokewhenavailable) 
  {
    nxs_js_setpopupdatefromcontrols();
    
    var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
    jQuery.ajax 
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