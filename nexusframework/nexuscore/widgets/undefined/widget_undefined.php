<?php

function nxs_widgets_undefined_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

function nxs_widgets_undefined_gettitle()
{
	return nxs_l18n__("Empty[nxs:widgettitle]", "nxs_td");
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_undefined_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
		
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	$mixedattributes = array_merge($temp_array, $args);
	
	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		//	
		$hovermenuargs = array();
		$hovermenuargs["postid"] = $postid;
		$hovermenuargs["placeholderid"] = $placeholderid;
		$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
		$hovermenuargs["enable_deletewidget"] = false;
		$hovermenuargs["enable_deleterow"] = false;
		$hovermenuargs["metadata"] = $mixedattributes;
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	}
	
	//
	// render actual control / html
	//
	
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-undefined";

	?>

	<?php
	if (nxs_has_adminpermissions())
	{
		// we zetten de hoogte default op x pixels, alhoewel de programmatuur de hoogte opnieuw zal bepalen,
		// de hoogte die we hier hard zetten bepaalt de minimale hoogte die een undefined placeholder inneemt
		// dit gebeurt ook in de frontendediting.php; die bepaalt de hoogte in ieder geval,
		// het is nog niet helemaal duidelijk of de hoogte die hier wordt gezet noodzakelijk is, c.q. toegevoegde
		// waarde
		?>
	 
		<!-- -->
		<div class="empty nxs-runtime-autocellsize">
			<div class="nxs-border-dash nxs-runtime-autocellsize absolute border-radius autosize-smaller" style="left: 0; right: 0; top: 0; bottom: 0; display: grid;">
				
					<div style="display: flex; grow: 1; padding: 20px; box-sizing: border-box; border: 1px dashed black;">
			
					</div>			

				
			</div>
		</div>			
		
		<?php
	} 
	else 
	{
	?>
		<!-- empty -->
	<?php
	}
	?>
	
	<?php 
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	
	return $result;
}

//
// het eerste /hoofd/ scherm dat wordt getoond in de popup als de gebruiker
// het editen van een placeholder initieert
//
function nxs_widgets_undefined_home_rendersheet($args)
{
	$clientpopupsessiondata = array();	// could be overriden in $args
	$clientshortscopedata = array();	// could be overriden in $args
	
	//
	extract($args);

	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);

	$type = $temp_array['type'];

	// clientpopupsessiondata bevat key values van de client side, deze overschrijven reeds bestaande variabelen
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	//
	//
	//
	if ($type == "")
	{
		$istypeset = false;
	}
	else
	{
		$istypeset = true;
	}	
			
	$result = array();
	$result["result"] = "OK";
	
	nxs_ob_start();
	
	//
	$pagedata = get_page($postid);
	$nxsposttype = nxs_getnxsposttype_by_wpposttype($pagedata->post_type);
	
	$posttype = $pagedata->post_type;
	$postmeta = nxs_get_corepostmeta($postid);
	$pagetemplate = nxs_getpagetemplateforpostid($postid);
	
	$phtargs = array();
	$phtargs["invoker"] = "nxsextundefined";
	$phtargs["wpposttype"] = $posttype;
	$phtargs["nxsposttype"] = nxs_getnxsposttype_by_wpposttype($posttype);
	$phtargs["pagetemplate"] = $pagetemplate;
	$widgets = nxs_getwidgets_v2($phtargs, true);
	
	$distincttags = array("all");
	
	// find distinct tags (where applicable)
	foreach ($widgets as $currentwidget)
	{
		$tags = $currentwidget["tags"];	// array
		foreach ($tags as $currenttag)
		{
			if (!in_array($currenttag, $distincttags))
			{
				$distincttags[] = $currenttag;
			}
		}
	}
	?>
	<div class="nxs-admin-wrap">
		<div class="block">	

     	<?php nxs_render_popup_header(nxs_l18n__("Select a widget[nxs:tooltip]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper" style="width: 900px;"> <!-- explicit max width -->
				<div class="nxs-popup-content-canvas">
					<style>
						.nxsfiltercontainer { margin-bottom: 20px;}
					</style>
		      <div class="content2">
		      	<div class="nxsfiltercontainer">
		      		Filters: 
		      		<?php
		      		foreach ($distincttags as $currenttag)
		      		{
		      			?>
			      		<a class="nxsbutton1 isotope-filter isotope-filter-<?php echo $currenttag; ?>" href="#" onclick="nxs_js_undefinedupdatefilter(this, '<?php echo $currenttag; ?>'); return false;"><?php echo $currenttag; ?></a>
			      		<?php
		      		}
		      		?>
		      	</div>
		        <div class="box nxs-linkcolorvar-base2-m">
		          <ul class="placeholder3 nxs-applylinkvarcolor isotope-grid">
		          	<style>	
		          		.isotope-item { cursor: pointer; } 
									li .nxs-widgetitem-submenu { display: none; position: absolute; z-index: 99999; }
									li .nxs-widgetitem-submenu li { width: auto !important;; height: auto !important; margin: 0 !important; padding: 2px;}
									li:hover .nxs-widgetitem-submenu { display: block; }
									
								</style>
								<?php
									// for each placeholder -->
									foreach ($widgets as $currentwidget)
									{
										$title = $currentwidget["title"];
										$tags = $currentwidget["tags"];	// array
										
										$abbreviatedtitle = $title;
										
										$breakuplength = 12;
										if (strlen($abbreviatedtitle) > $breakuplength)
										{
											if (!nxs_stringcontains($abbreviatedtitle, " "))
											{
												// te lang...
												$abbreviatedtitle = substr($abbreviatedtitle, 0, $breakuplength - 1) . "-" . substr($abbreviatedtitle, $breakuplength - 1);
											}
										}
										
										$maxlength = 14;
										if (strlen($abbreviatedtitle) > $maxlength)
										{
											// chop!
											$abbreviatedtitle = substr($abbreviatedtitle, 0, $maxlength - 1) . "..";
										}
										
										$widgetid = $currentwidget["widgetid"];
										$iconid = nxs_getwidgeticonid($widgetid);
										$supporturl = nxs_widget_getsupporturl($widgetid);
										
										$elementclass = "";
										foreach ($tags as $currenttag)
										{
											$elementclass .= $currenttag . " ";
										}
										?>
										<li class="isotope-item <?php echo $elementclass; ?>" href="#" onclick="selectplaceholdertype(this, '<?php echo $widgetid; ?>'); return false;">
											<?php
											if (isset($iconid) && $iconid != "")
											{
												?>
												<?php
												if ($supporturl != "")
												{
													?>
													<ul class='nxs-widgetitem-submenu'>
														<li title='Support'>
															<a href='<?php echo $supporturl; ?>' target='_blank'>
																<span class='nxs-icon-info'></span>
															</a>
														</li>
													</ul>
													<?php
												}
												?>
												<span class='nxs-widget-icon <?php echo $iconid; ?>'></span>
												<p title='<?php echo $title; ?>'><?php echo $abbreviatedtitle; ?></p>
												<?php
											}
											else
											{
												$iconid = nxs_getplaceholdericonid($widgetid);
												?>
												<span id='placeholdertemplate_<?php echo $widgetid; ?>' class='<?php echo $iconid; ?>'></span>
												<p title='<?php echo $title; ?>'><?php echo $abbreviatedtitle; ?></p>
												<?php
											}
											?>
										</li>
									
										<?php
									}
								?>
		        	</ul>
		        </div>
		        <div class="nxs-clear"></div>
		      </div>
		      
		      <?php
		      	$defaultmessagedata = array();
						$defaultmessagedata["html"] = "<div class='content2'>Message 10987098273</div>";
						$additionalparameters = array();
						
						$postmeta = nxs_get_corepostmeta($postid);
						$pagetemplate = nxs_getpagetemplateforpostid($postid);
						
						// posttype en pagetemplate ook meesturen
						$additionalparameters["pagetemplate"] = $pagetemplate;
						$additionalparameters["wpposttype"] = $pagedata->post_type;
						
						$messagedata = nxs_gettransientnexusservervalue("widgets", "undefined", $additionalparameters);
						echo $messagedata["html"];
		      ?>
		      
		    </div>
		  </div>
		  
      <div class="content2">
         <div class="box">
            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Save[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:button]", "nxs_td"); ?></a>
         </div>
         <div class="nxs-clear"></div>
      </div> <!--END content-->

		  <script src='<?php echo nxs_getframeworkurl(); ?>/js/isotope/isotope.pkgd.min.js'></script>
    	<script>
    		function onAnimationFinished()
    		{
				  // code to be executed after the animation finishes
				  nxs_js_alert("wop");
				};
					
    		function nxs_js_undefinedupdatefilter(element, filter)
    		{
    			jQuery(".isotope-filter").removeClass("nxsbutton").addClass("nxsbutton1");
    			jQuery(element).addClass("nxsbutton").removeClass("nxsbutton1");
    			
    			var thefilter = "." + filter;
    			if (filter == "all")
    			{
    				thefilter = "*";
    			}
    			
    			
    			
  				$('.isotope-grid').isotope
  				(
  					{
						  // options
						  itemSelector: '.isotope-item',
						  //layoutMode: 'fitRows',
						  filter: thefilter
						}
					);
					
					// 
    		}

				//nxs_js_undefinedupdatefilter(null, "all");
				jQuery(".isotope-filter-all").addClass("nxsbutton").removeClass("nxsbutton1");
    	</script>
    </div>
  </div>
	
	<script>
		
		function selectplaceholdertype(obj, placeholdertype)
		{
			jQuery('.placeholder-selected').css('background-color', ''); 
			jQuery('.placeholder-selected').removeClass('placeholder-selected');
			jQuery(obj).find('li').css('background-color', '#E9F1F9'); 
			jQuery(obj).find('li').addClass('placeholder-selected');
			
			nxs_js_popup_setsessiondata("type", placeholdertype);
			// nxs_js_popup_sessiondata_make_dirty();
			// auto save
			nxs_js_savegenericpopup();
		}
		
		function nxs_js_savegenericpopup()
		{
			var waitgrowltoken = nxs_js_alert_wait_start("<?php nxs_l18n_e("Storing widget[nxs:growl]","nxs_td"); ?>");
			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "initplaceholderdata",
						"clientpopupsessioncontext": nxs_js_getescaped_popupsession_context(),
						"placeholderid": "<?php echo $placeholderid;?>",
						"postid": "<?php echo $postid;?>",
						"containerpostid": nxs_js_getcontainerpostid(),
						"placeholdertemplate": nxs_js_popup_getsessiondata("type"),
						"type": nxs_js_popup_getsessiondata("type")
					},
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_alert_wait_finish(waitgrowltoken);
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// TODO: make function for the following logic... its used multiple times...
							// update the DOM
							var rowindex = response.rowindex;
							var rowhtml = response.rowhtml;
							var pagecontainer = jQuery(".nxs-layout-editable.nxs-post-<?php echo $postid;?>");
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
							nxs_js_alert(response.growl);
							
							// ----------------
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
						nxs_js_alert_wait_finish(waitgrowltoken);
						nxs_js_popup_notifyservererror();
						nxs_js_log(response);
					}										
				}
			);
		}
	</script>
	
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	$result["html"] = $html;
	
	return $result;
}

function nxs_widgets_undefined_initplaceholderdata($args)
{
	nxs_widgets_undefined_updateplaceholderdata($args);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

//
// wordt aangeroepen bij het opslaan van data van deze placeholder
//
function nxs_widgets_undefined_updateplaceholderdata($args)
{
	extract($args);
	
	$temp_array = array();
	
	$temp_array['type'] = "undefined";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $temp_array);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_dataprotection_nexusframework_widget_undefined_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("widget-none");
}


?>