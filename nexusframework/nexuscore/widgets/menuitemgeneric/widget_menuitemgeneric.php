<?php

function nxs_menuitemgeneric_switchtype($optionvalues, $args, $runtimeblendeddata)
{
	//extract($runtimeblendeddata);
	extract($args["clientpopupsessioncontext"]);
	
	$excludeitem = $optionvalues["excludeitem"];
	
	if ($excludeitem != "menuitemcustom")
	{
	 	?>
		<a href="#" class="nxsbutton nxs-float-left" onclick="nxs_js_switchtomenuitemtype('menuitemcustom'); return false;">Custom</a>
		<?php
	}
	if ($excludeitem != "menuitemcategory")
	{
	 	?>
		<a href="#" class="nxsbutton nxs-float-left" onclick="nxs_js_switchtomenuitemtype('menuitemcategory'); return false;">Category</a>
		<?php
	}
	if ($excludeitem != "menuitemarticle")
	{
	 	?>
		<a href="#" class="nxsbutton nxs-float-left" onclick="nxs_js_switchtomenuitemtype('menuitemarticle'); return false;">Post or page</a>
		<?php
	}
	?>
	<script>
		function nxs_js_switchtomenuitemtype(menuitemtype)
		{
    	// wijzig type van deze placeholder naar 'x'
			// refresh de popup
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "initplaceholderdata",
						"placeholderid": "<?php echo $placeholderid;?>",
						"postid": "<?php echo $postid;?>",
						"containerpostid": nxs_js_getcontainerpostid(),
						"clientpopupsessioncontext": nxs_js_getescaped_popupsession_context(),
						"placeholdertemplate": menuitemtype,
						"type": menuitemtype
					},
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// TODO: make function for the following logic... its used multiple times...
							// update the DOM
							var rowindex = response.rowindex;
							var rowhtml = response.rowhtml;
							var pagecontainer = jQuery(".nxs-layout-editable")[0];
							var pagerowscontainer = jQuery(pagecontainer).find(".nxs-postrows")[0];
							var element = jQuery(pagerowscontainer).children()[rowindex];
							jQuery(element).replaceWith(rowhtml);
							
							// update the GUI step 1
							// invoke execute_after_clientrefresh_XYZ for each widget in the affected first row, if present
							var container = jQuery(pagerowscontainer).children()[rowindex];
							nxs_js_notify_widgets_after_ajaxrefresh(container);
							// update the GUI step 2
							nxs_js_reenable_all_window_events();
							
							// growl!
							//nxs_js_alert(response.growl);
							
							// ------------
							nxs_js_popupsession_data_clear();
							
							// open new popup
							nxs_js_popup_placeholder_neweditsession("<?php echo $postid; ?>", "<?php echo $placeholderid; ?>", "<?php echo $rowindex; ?>", "home"); 
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
	</script>
	<?php
}

?>