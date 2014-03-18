<?php

function nxs_widgets_wordpresssidebar_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

function nxs_widgets_wordpresssidebar_gettitle()
{
	return nxs_l18n__("WordPress sidebar", "nxs_td");
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_wordpresssidebar_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
			
	$result = array();
	$result["result"] = "OK";

	// sommige eigenschappen zijn vastgelegd op placeholder meta, andere op pagina, en andere daarbuiten
	
	// metadata velden
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	$mixedattributes = array_merge($temp_array, $args);
	$wpsidebarid = $mixedattributes['wpsidebarid'];						// OK

	global $nxs_global_placeholder_render_statebag;
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs); 
	
	//
	// render actual control / html
	//
	
	ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-wordpress-sidebar nxs-applylinkvarcolor";
	
	if ($wpsidebarid == "")
	{
		$wpsidebarid = "1";
	}
	
	?>
	<!-- -->
	
	<div <?php echo $class; ?>>
		<?php 
		ob_start();
		dynamic_sidebar(intval($wpsidebarid));
		$sidebarcontent = ob_get_contents();
		ob_end_clean();

		if ($sidebarcontent == "")
		{
			nxs_renderplaceholderwarning(nxs_l18n__("No widgets found in widget area[nxs:warning]", "nxs_td"));
		}			
		else
		{
			?>
			<ul class='nxs-sidebar-widgets'>
				<?php echo $sidebarcontent; ?>
			</ul>
			<?php				
		}
		?>
	</div>
	
	<?php 
	
	$html = ob_get_contents();
	ob_end_clean();

	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	
	return $result;
}

function nxs_widgets_wordpresssidebar_home_rendersheet($args)
{
	//
	extract($args);
	
	if ($postid == "")
	{
		nxs_webmethod_return_nack("postid not set in context (nxs_ptrtph)");
	}
	if ($placeholderid == "")
	{
		nxs_webmethod_return_nack("placeholderid not set in context (nxs_ptrtph)");
	}
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	$wpsidebarid = $temp_array['wpsidebarid'];

	// clientpopupsessiondata bevat key values van de client side, deze overschrijven reeds bestaande variabelen
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);

	$result = array();
	$result["result"] = "OK";

	ob_start();

	?>
	
    <div class="nxs-admin-wrap">
      <div class="block">
      
       	<?php nxs_render_popup_header(nxs_l18n__("WordPress sidebar[nxs:title]", "nxs_td")); ?>

				<div class="nxs-popup-content-canvas-cropper">
					<div class="nxs-popup-content-canvas">
			
						<div class="content2">
					    <div class="box">
					      <div class="box-title">
					          <h4><?php nxs_l18n_e("Area[nxs:heading]", "nxs_td"); ?></h4>
					       </div>
					      <div class="box-content">
					      	<select id='wpsidebarid' onchange="nxs_js_popup_setsessiondata('wpsidebarid', jQuery(this).val());">
					      		<option <?php if ($wpsidebarid=='1') echo "selected='selected'"; ?> value='1'>WordPress Backend Widget area 1</option>
					      		<option <?php if ($wpsidebarid=='2') echo "selected='selected'"; ?> value='2'>WordPress Backend Widget area 2</option>
					      		<option <?php if ($wpsidebarid=='3') echo "selected='selected'"; ?> value='3'>WordPress Backend Widget area 3</option>
					      		<option <?php if ($wpsidebarid=='4') echo "selected='selected'"; ?> value='4'>WordPress Backend Widget area 4</option>
					      		<option <?php if ($wpsidebarid=='5') echo "selected='selected'"; ?> value='5'>WordPress Backend Widget area 5</option>
					      		<option <?php if ($wpsidebarid=='6') echo "selected='selected'"; ?> value='6'>WordPress Backend Widget area 6</option>
					      		<option <?php if ($wpsidebarid=='7') echo "selected='selected'"; ?> value='7'>WordPress Backend Widget area 7</option>
					      		<option <?php if ($wpsidebarid=='8') echo "selected='selected'"; ?> value='8'>WordPress Backend Widget area 8</option>
					      	</select>
					      </div>
					    </div>
					    <div class="nxs-clear"></div>
					  </div> <!--END content-->	
					  
					  <div class="content2">
					    <div class="box">
					      <div class="box-title">
					          <h4><?php nxs_l18n_e("Styling", "nxs_td"); ?></h4>
					       </div>
					      <div class="box-content">
					      	<a href='#' onclick='nxs_js_popup_navigateto("backgroundstyle"); return false;'>Styling</a>
					      </div>
					    </div>
					    <div class="nxs-clear"></div>
					  </div> <!--END content-->	
					  
		      
			    </div>
			  </div>
		      
	      <div class="content2">
          <div class="box">
            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Save[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:button]", "nxs_td"); ?></a>
	         </div>
          <div class="nxs-clear margin"></div>
	      </div> <!--END content-->
  	
	    </div>
	  </div>

  <script type='text/javascript'>
				
		function nxs_js_savegenericpopup()
		{
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQuery.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updateplaceholderdata",
						"placeholderid": "<?php echo $placeholderid;?>",
						"postid": "<?php echo $postid;?>",
						"placeholdertemplate": "wordpresssidebar",
						"wpsidebarid": jQuery('#wpsidebarid').val()
					},
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// update UI, the 'current' id will be overriden because null is specified as third parameter
							
							nxs_js_rerender_row_for_placeholder("<?php echo $postid;?>", "<?php echo $placeholderid;?>");
							
							// close the pop up
							nxs_js_closepopup_unconditionally();
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
		
		function nxs_js_execute_after_popup_shows()
		{
			
		}
		
	</script>
  

	<?php
	
	$html = ob_get_contents();
	ob_end_clean();

	$result["html"] = $html;
	
	return $result;
}

//
// wordt aangeroepen bij het opslaan van data van deze placeholder
//
function nxs_widgets_wordpresssidebar_updateplaceholderdata($args)
{
	extract($args);
	
	$temp_array = array();
	
	// its required to also set the 'type' (used when dragging an item from the toolbox to existing placeholder)
	$temp_array['type'] = 'wordpresssidebar';
	$temp_array['wpsidebarid'] = $wpsidebarid; 	// there's no need of a globalid for this attribute

	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $temp_array);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>