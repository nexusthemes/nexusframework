<?php

function nxs_popup_contextprocessor_pagerow_getcustompopuphtml($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }

	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	if ($clientpopupsessioncontext != null) { if ($clientpopupsessioncontext != null) { extract($clientpopupsessioncontext); } }
	
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
	if ($clientpopupsessioncontext != null) { if ($clientpopupsessioncontext != null) { extract($clientpopupsessioncontext); } }
	
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
	if ($clientpopupsessioncontext != null) { if ($clientpopupsessioncontext != null) { extract($clientpopupsessioncontext); } }
	
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
	
	// allow plugins to enhance the output of the options
	$args = array
	(
		"contextprocessor" => "pagerow",
		"sheet" => $sheet,
	);
	$result = apply_filters("nxs_f_getpopupoptions", $result, $args);
	
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
	if ($clientpopupsessioncontext != null) { if ($clientpopupsessioncontext != null) { extract($clientpopupsessioncontext); } }
	
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
	if ($clientpopupsessioncontext != null) { if ($clientpopupsessioncontext != null) { extract($clientpopupsessioncontext); } }
	
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
	if ($clientpopupsessioncontext != null) { if ($clientpopupsessioncontext != null) { extract($clientpopupsessioncontext); } }
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set in context"); }
	if ($pagerowid == "") { nxs_webmethod_return_nack("pagerowid not set in context"); }
	$temp_array = nxs_getpagerowmetadata($postid, $pagerowid);
	$runtimemetadata = $args["clientpopupsessiondata"];
	if ($runtimemetadata == "")
	{
		$runtimemetadata = array();
	}
	$mixedattributes = array_merge($temp_array, $runtimemetadata);
	$unistyle = $mixedattributes["unistyle"];
	
	?>
	function nxs_js_savegenericpopup()
  {
		nxs_js_savegenericpopup_internal
		(
			function(response)
			{
				// mark 'this' row (container) as dirty
				jQ_nxs("#nxs-pagerow-<?php echo $pagerowid;?> .nxs-row-container").addClass("nxs-dirty");
				
	      <?php
	      // if this widget has a unistyle it means potential other rows of the screen need to be refreshed too
	      if ($unistyle != "")
	      {
	      	?>
	      	var selector = ".nxs-row.nxs-unistyle-<?php echo $unistyle; ?> .nxs-row-container";
	      	nxs_js_log("komt ie:");
	      	nxs_js_log(selector);
	      	// mark all rows with same unistyle as dirty
	      	jQ_nxs(selector).addClass("nxs-dirty");
	      	<?php
	      }
	      else
	      {
	      	?>
	      	nxs_js_log("komt ie niet");
	      	<?php
	      }
	      ?>
	      
	      // rerender all dirty items!
	      nxs_js_rerender_dirty_rowcontainers();
	
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