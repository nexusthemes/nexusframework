<?php

function nxs_widgets_menuitemcustom_geticonid()
{
	//$widget_name = basename(dirname(__FILE__));
	//return "nxs-icon-" . $widget_name;
	return "nxs-icon-menucontainer";
}

function nxs_widgets_menuitemcustom_gettitle()
{
	return nxs_l18n__("Custom menu item[nxs:widgettitle]", "nxs_td");
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_menuitemcustom_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	$mixedattributes = array_merge($temp_array, $args);
	
	$title = $mixedattributes['title'];
	$destination_url = $mixedattributes['destination_url'];
	$depthindex = $mixedattributes['depthindex'];	// sibling or child

	if (nxs_has_adminpermissions())
	{
		$renderBeheer = true;
	}
	else
	{
		$renderBeheer = false;
	}
	
	if ($rendermode == "default")
	{
		if ($renderBeheer)
		{
			$shouldrenderhover = true;
		} 
		else
		{
			$shouldrenderhover = false;
		}
	}
	else if ($rendermode == "anonymous")
	{
		$shouldrenderhover = false;
	}
	else
	{
		echo "unsupported rendermode;" . $rendermode;
		die();
	}

	//
	//
	//
	
	$paddingleft = 30 * ($depthindex - 1);
	
	global $nxs_global_placeholder_render_statebag;
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["enable_decoratewidget"] = false;
	$hovermenuargs["enable_deletewidget"] = false;
	$hovermenuargs["enable_deleterow"] = true;
	$hovermenuargs["metadata"] = $mixedattributes;	
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
	//
	// render actual control / html
	//
		
	
	ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-menu-item " . "nxs-listitem-depth-" . $depthindex;
	
  if ($depthindex == 1)
  {
  	$positionerclass = "";
  } 
  else if ($depthindex == 2)
  {
  	$positionerclass = "nxs-margin-left30";
  }
  else if ($depthindex == 3)
  {
  	$positionerclass = "nxs-margin-left60";
  }
  else if ($depthindex == 4)
  {
  	$positionerclass = "nxs-margin-left90";
  }
  else if ($depthindex == 5)
  {
  	$positionerclass = "nxs-margin-left120";
  }
  else
  {
  	echo "max depth = 4";
  	$positionerclass = "nxs-margin-left120";
  }
  
  ?>
	<div class="nxs-padding-menu-item">
		<div class="content2 border <?php echo $positionerclass;?>">
	    <div class="box-content nxs-float-left"><p><?php echo $title; ?></p></div>
	    <div class="nxs-clear"></div>
	  </div> <!--END content-->
	</div>
	
	<?php 
	
	$html = ob_get_contents();
	ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

function nxs_menuitemcustom_menuitemarticletoggle($optionvalues, $args, $runtimeblendeddata)
{	
	//extract($runtimeblendeddata);
	extract($args["clientpopupsessioncontext"]);

 	?>
	<a href="#" class="nxsbutton nxs-float-left" onclick="nxs_js_switchtocustommenuitem(); return false;">Switch</a>
	<script type='text/javascript'>
		function nxs_js_switchtocustommenuitem()
		{
    	// wijzig type van deze placeholder naar 'x'
			// refresh de popup
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQuery.ajax
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
						"placeholdertemplate": 'menuitemarticle',
						"type": 'menuitemcustom'
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
							var pagecontainer = jQuery(".nxs-editable")[0];
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

// Define the properties of this widget
function nxs_widgets_menuitemcustom_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_menuitemcustom_gettitle(),
		"sheeticonid" => nxs_widgets_menuitemcustom_geticonid(),
		"fields" => array
		(
			array(
				"id" 					=> "menuitemarticletoggle",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_menuitemcustom_menuitemarticletoggle",
				"label" 			=> nxs_l18n__("Switch to article menu item", "nxs_td"),
			),
		
			// TITLE
			
			array( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
			),
			array
			(
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" => nxs_l18n__("Title goes here", "nxs_td"),
				"localizablefield"	=> true
			),
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend",
			),
			
			//
			
			array( 
				"id" 				=> "wrapper_link_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Link", "nxs_td"),
			),
			
			array
			(
				"id" 				=> "destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("URL", "nxs_td"),
				"placeholder" => nxs_l18n__("http://www.example.org", "nxs_td"),
				"localizablefield"	=> true
			),
			
			array(
				"id" 				=> "destination_target",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Target", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@empty@@@"=>nxs_l18n__("Auto", "nxs_td"),
					"_blank"=>nxs_l18n__("New window", "nxs_td"),
					"_self"=>nxs_l18n__("Current window", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "destination_relation",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Link relation", "nxs_td"),
				"dropdown" 			=> array
				(
					"nofollow"=>nxs_l18n__("No follow", "nxs_td"),
					"follow"=>nxs_l18n__("Do follow", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			
			//destination_target ()
			//destination_relation (nofollow, follow)
				
			array( 
				"id" 				=> "wrapper_link_end",
				"type" 				=> "wrapperend"
			),
		)
	);
	
	return $options;
}

//
// wordt aangeroepen bij het opslaan van data van deze placeholder
//
function nxs_widgets_menuitemcustom_initplaceholderdata($args)
{
	extract($args);

	$args["title"] = "Item";
	$args["depthindex"] = 1;
	$args["destination_target"] = "";
	$args["destination_relation"] = "nofollow";	
	$args['destination_url'] = nxs_geturl_home();
	$args['ph_margin_bottom'] = "0-0";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>