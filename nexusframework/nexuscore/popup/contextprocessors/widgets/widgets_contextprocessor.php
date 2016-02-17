<?php

function nxs_popup_contextprocessor_widgets_getcustompopuphtml($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }

	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set"); }
 	if ($placeholderid == "") { nxs_webmethod_return_nack("placeholderid not set"); }
 	if ($sheet == "") { nxs_webmethod_return_nack("sheet not set"); }
 	
 	// downwards compatibility...
 	$args["postid"] = $postid;
 	$args["placeholderid"] = $placeholderid;
	
	// retrieve the placeholdertemplate
	$widget = nxs_getplaceholdertemplate($postid, $placeholderid);
	if (nxs_widgetexists($widget))
	{
		nxs_requirewidget($widget);
	}
	else
	{
		// could be a generic implementation, so we won't crash in a fatal way here...
	}
	
	$functionnametoinvoke = 'nxs_widgets_' . $widget . '_' . $sheet . '_rendersheet';
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
			nxs_webmethod_return_nack("widgetcontext; unable to render sheet $sheet; function $functionnametoinvoke not found and no genericpopup found");
		}
	}
	
	return $result;
}

function nxs_popup_contextprocessor_widgets_supportsoptions($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }

	$result = false;
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set"); }
 	if ($placeholderid == "") { nxs_webmethod_return_nack("placeholderid not set"); }
 	if ($sheet == "") { nxs_webmethod_return_nack("sheet not set"); }
	
	// retrieve the placeholdertemplate
	$widget = nxs_getplaceholdertemplate($postid, $placeholderid);
	if (nxs_widgetexists($widget))
	{
		nxs_requirewidget($widget);
	}
	else
	{
		// could be a generic implementation, so we won't crash in a fatal way here...
	}
	
	$functionnametoinvoke = 'nxs_widgets_' . $widget . '_' . $sheet . '_getoptions';
	if (function_exists($functionnametoinvoke))
	{
		$result = true;
	}
	else
	{
		$widget = "generic";
		nxs_requirewidget($widget);
		
		$functionnametoinvoke = 'nxs_widgets_' . $widget . '_' . $sheet . '_getoptions';
		if (function_exists($functionnametoinvoke))
		{
			$result = true;
		}
	}
	
	return $result;
}

function nxs_popup_contextprocessor_widgets_getoptions($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }

	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set"); }
 	if ($placeholderid == "") { nxs_webmethod_return_nack("placeholderid not set"); }
 	if ($sheet == "") { nxs_webmethod_return_nack("sheet not set"); }
	
	// retrieve the placeholdertemplate
	$widget = nxs_getplaceholdertemplate($postid, $placeholderid);
	if (nxs_widgetexists($widget))
	{
		nxs_requirewidget($widget);
	}
	else
	{
		// could be a generic implementation, so we won't crash in a fatal way here...
	}
	
	$result = nxs_popup_contextprocessor_widgets_getoptions_widgetsheet($widget, $sheet);
	
	return $result;
}

function nxs_popup_contextprocessor_widgets_getoptions_widgetsheet($widget, $sheet)
{
	$functionnametoinvoke = 'nxs_widgets_' . $widget . '_' . $sheet . '_getoptions';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		// alternative: generic implementation for widgets
		$widget = "generic";
		nxs_requirewidget($widget);
	
		$functionnametoinvokealt = 'nxs_widgets_' . $widget . '_' . $sheet . '_getoptions';
		if (function_exists($functionnametoinvokealt))
		{
			$result = call_user_func($functionnametoinvokealt, $args);
		}
		else
		{
			nxs_webmethod_return_nack("unable to get options; function not found; $functionnametoinvokealt");
		}
	}
	
	// allow plugins to enhance the output of the options
	$args = array
	(
		"contextprocessor" => "widgets",
		"widget" => $widget,
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
function nxs_popup_contextprocessor_widgets_getpersisteddata($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set in context"); }
	if ($placeholderid == "") { nxs_webmethod_return_nack("placeholderid not set in context"); }
	
	// delegate
	$result = nxs_getwidgetmetadata($postid, $placeholderid);
	return $result;
}

// _internal means the globalids (extended data) is already enriched!
function nxs_popup_contextprocessor_widgets_mergedata_internal($args, $metadata)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set in context"); }
	if ($placeholderid == "") { nxs_webmethod_return_nack("placeholderid not set in context"); }
	
	// delegate
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $metadata);
	
	$result = array();
	$result["result"] = "OK";
	return $result;
}

// output the nxs_js_savegenericpopup function
function nxs_popup_contextprocessor_widgets_render_nxs_js_savegenericpopup($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set in context"); }
	if ($placeholderid == "") { nxs_webmethod_return_nack("placeholderid not set in context"); }
	
	// get widget metadata so we can derive the unistyle and unicontent
	$widgetmetadata = nxs_getwidgetmetadata($postid, $placeholderid);
	$unistyle_cssclass = "nxs-unistyle-" . nxs_stripspecialchars($widgetmetadata["unistyle"]);
	$unicontent_cssclass = "nxs-unicontent-" . nxs_stripspecialchars($widgetmetadata["unicontent"]);
	$widgettype_cssclass = "nxs-widgettype-" . nxs_stripspecialchars($widgetmetadata["type"]);
	?>
	function nxs_js_savegenericpopup()
  {
		nxs_js_savegenericpopup_internal
		(
			function(response)
			{
				// behaviour: refresh page
				var onsaverefreshpage = nxs_js_popup_getsessioncontext("onsaverefreshpage");
				if (onsaverefreshpage == true)
				{
					// this is used for example when the user
					// requests a popup of a pagedecorator,
					// (containerpostid != pagedecoratorid)
					// in that case the saving of the popup should trigger a refresh of the page
					nxs_js_refreshcurrentpage();
					return;
				}
			
				// behaviour else:
			
				// mark row of this widget as dirty
				jQ_nxs(".nxs-post-<?php echo $postid;?> .nxs-widget-<?php echo $placeholderid;?>").closest(".nxs-row-container").addClass("nxs-dirty");
				
	      <?php
	      // if this widget has a unistyle, or unicontent, it means potential other
	      // parts of the screen need to be refreshed too
	      if ($unistyle_cssclass != "" || $unicontent_cssclass != "")
	      {
	      	?>
	      	// mark all rows of all widgets of same type with same unistyle as dirty
	      	jQ_nxs(".<?php echo $widgettype_cssclass;?>.<?php echo $unistyle_cssclass;?> .nxs-widget").closest(".nxs-row-container").addClass("nxs-dirty");
	      	// mark all rows of all widgets of same type with same unicontent as dirty
	      	jQ_nxs(".<?php echo $widgettype_cssclass;?>.<?php echo $unicontent_cssclass;?> .nxs-widget").closest(".nxs-row-container").addClass("nxs-dirty");
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