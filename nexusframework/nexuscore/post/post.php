<?php


// Define the properties of this widget
function nxs_postcontent_wpcontent_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_l18n__("Content", "nxs_td"),
		"sheeticonid" => "nxs-icon-text",

		"fields" => array
		(
			// TEXT
			
			array(
				"id" 				=> "wpcontent",
				"type" 				=> "tinymce",
				"wpautop" => true,
				"focus" 	=> "true",
				"validelementsallowed" => "any",
				"placeholder" 		=> nxs_l18n__("Text goes here", "nxs_td"),
			)
			
		)
	);
	
	return $options;
}


function nxs_post_home_rendersheet($args)
{
	//
	extract($args);
	
	if ($postid== "")
	{
		echo "postid is niet geset? (gpm)";
		die();
	}
	
	$pagemeta = nxs_get_postmeta($postid);
	$pagetemplate = nxs_getpagetemplateforpostid($postid);
	$posttype = nxs_getwpposttype($postid);
	
	$prtargs = array();
	$prtargs["invoker"] = "nxspage";
	$prtargs["wpposttype"] = $posttype;
	$prtargs["nxsposttype"] = nxs_getnxsposttype_by_wpposttype($posttype);
	$prtargs["pagetemplate"] = $pagetemplate;
  $pagetemplates = nxs_getpagetemplates($prtargs);

	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	$result = array();
	
	ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	

     	<?php nxs_render_popup_header(nxs_l18n__("Set page template[nxs:popup,heading]", "nxs_td")); ?>
            
      <!--  -->
      
			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
	
					<div class="content2">
						<div class="box-title">
	            <h4><?php nxs_l18n_e("Templates[nxs:popup,heading]", "nxs_td"); ?></h4>
	         	</div>
						<div class="box-content">
	          	<select id='pagetemplate' onchange="nxs_js_savepopupdata(); nxs_js_popup_refresh_keep_focus(this);">
	          		<option <?php if ($pagetemplate=='') echo "selected='selected'"; ?> value=''><?php nxs_l18n_e("Select your template", "nxs_td"); ?></option>
	          		<?php
	          		foreach ($pagetemplates as $currentpagetemplate)
	          		{
	          			$title = $currentpagetemplate["title"];
	          			$currenttemplate = $currentpagetemplate["pagetemplate"];
	          			?>
	          			<option <?php if ($pagetemplate==$currenttemplate) echo "selected='selected'"; ?> value='<?php echo $currenttemplate; ?>'><?php echo $title; ?></option>
	          			<?php
	          		}
	          		?>
	          	</select>
	          </div>
	        <div class="nxs-clear"></div>
	      </div> <!--END content-->
	  
	      <!-- preview -->
	      
	      <?php 
	      if ($pagetemplate != "")
	      {
	      	// show the preview
	      	nxs_renderpagetemplatepreview($pagetemplate, $args);
	    	}
	    	?>
	    
	  	</div>
	  </div>
		    	
    <div class="content2">
      <div class="box">
        <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='showpagetemplatepopup(); return false;'><?php nxs_l18n_e("Next", "nxs_td"); ?></a>
        <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>
        <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
     	</div>
      <div class="nxs-clear">
      </div>
    </div> <!--END content-->
	</div>
	
	<script type='text/javascript'>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}
		
		function nxs_js_savepopupdata()
		{
			nxs_js_popup_storestatecontroldata_dropdown('pagetemplate', 'pagetemplate');			
		}
		
		function showpagetemplatepopup()
		{
			if (nxs_js_popup_anyobjectionsforopeningnewpopup())
			{
				return;
			}
			nxs_js_savepopupdata();

			nxs_js_popup_sessiondata_clear_dirty();	// don't annoy user with warnings when switching context
			
			var pagetemplate = nxs_js_popup_getsessiondata('pagetemplate');
			
			if (pagetemplate == '')
			{      
				nxs_js_popup_negativebounce('<?php nxs_l18n_e("Select a template[nxs:negativebounce]", "nxs_td"); ?>');

      	jQuery('#pagetemplate').focus();
				return;
			}
			
			// we slaan hierbij nog niets op, maar we redirecten naar de popup van de pagetemplate,
			// dit impliceert dat hiermee de popupcontext switcht!
			var postid = nxs_js_getcontainerpostid();
			nxs_js_popupsession_data_clear();
			nxs_js_popup_setsessioncontext("contextprocessor", "pagetemplate");
			nxs_js_popup_setsessioncontext("postid", postid);
			nxs_js_popup_setsessioncontext("pagetemplate", pagetemplate);
			nxs_js_popup_navigateto('home');
		}
		
		function nxs_js_execute_after_popup_shows()
		{
			jQuery('#postwizard').focus();
		}
		
		// overriden
		function nxs_js_showwarning_when_trying_to_close_dirty_popup()
		{
			return false;
		}
		
	</script>
	<?php
	
	$html = ob_get_contents();
	ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_post_dialogappendrow_rendersheet($args)
{
	//
	extract($args);
			
	$result = array();
	
	if (isset($clientpopupsessiondata)) { extract($clientpopupsessiondata); }
	if (isset($clientshortscopedata)) { extract($clientshortscopedata); }
	
	ob_start();

	$pagedata = get_page($postid);
	$nxsposttype = nxs_getnxsposttype_by_wpposttype($pagedata->post_type);
	
	$posttype = $pagedata->post_type;
	$postmeta = nxs_get_postmeta($postid);
	$pagetemplate = nxs_getpagetemplateforpostid($postid);	
	
	$prtargs = array();
	$prtargs["invoker"] = "nxsmenu";
	$prtargs["wpposttype"] = $posttype;
	$prtargs["nxsposttype"] = nxs_getnxsposttype_by_wpposttype($posttype);
	$prtargs["pagetemplate"] = $pagetemplate;	
	$templates = nxs_getpostrowtemplates($prtargs);

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	

     	<?php nxs_render_popup_header(nxs_l18n__("Add row[nxs:popup,heading]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		
					<div class="content2">
		        <div class="box">
		          <div class="box-title" style='width: 400px;'>
		            <h4><?php nxs_l18n_e("Select a row layout for the new row[nxs:popup,newrow]", "nxs_td"); ?></h4>
		          </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		
		      <div class="content2">
		        <div class="box">
		        	
		          <ul class="nxs-fraction drag nxs-admin-wrap">
								<?php
									// for each placeholder -->
									foreach ($templates as $currentpostrowtemplate)
									{
										?>
										<a href="#" onclick="select(this, '<?php echo $currentpostrowtemplate; ?>'); return false;">
											<li>
												<?php
												require_once(NXS_FRAMEWORKPATH . '/nexuscore/pagerows/templates/' . $currentpostrowtemplate . '/' . $currentpostrowtemplate . '_render.php');
												$functionnametoinvoke = 'nxs_pagerowtemplate_render_' . $currentpostrowtemplate . "_toolbox";
												$args = array();
												$args["postid"] = $postid;
												$args["pagerowtemplate"] = $currentpostrowtemplate;
												if (function_exists($functionnametoinvoke))
												{
													call_user_func($functionnametoinvoke, $args);
												}
												else
												{
													echo "function not found;" . $functionnametoinvoke;
												}
												?>
											</li>
										</a>									
										<?php
									}
								?>
		        	</ul>
		        </div>
		        <div class="nxs-clear"></div>
		      </div>
		    
			  </div>
			</div>
      
      <div class="content2">
         <div class="box">
            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Add[nxs:popup,newrow,button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>                    
         </div>
         <div class="nxs-clear"></div>
      </div> <!--END content-->
    	
    </div>
  </div>
	
	<script type='text/javascript'>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}
		
		function select(obj, pagerowtemplate)
		{
			if (jQuery(obj).hasClass('nxs-processing'))
			{
				return;
			}
			jQuery(obj).addClass('nxs-processing');
			
			var waitgrowltoken = nxs_js_alert_wait_start("<?php nxs_l18n_e("Adding row[nxs:growl]", "nxs_td"); ?>");
			var e = jQuery(".nxs-layout-editable.nxs-post-<?php echo $postid;?> .nxs-postrows")[0];
			var totalrows = jQuery(e).find(".nxs-row").length;
			var insertafterindex;
			insertafterindex = totalrows - 1;
			
			nxs_js_addnewrowwithtemplate('<?php echo $postid; ?>', insertafterindex, pagerowtemplate, "undefined", e, 
			function()
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				nxs_js_closepopup_unconditionally();
				jQuery(obj).removeClass('nxs-processing');
			},
			function()
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				nxs_js_closepopup_unconditionally();
				jQuery(obj).removeClass('nxs-processing');
			});
		}
	</script>
	
	<?php
	
	$html = ob_get_contents();
	ob_end_clean();

	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_post_edittitle_rendersheet($args)
{
	//
	extract($args);

	$pagedata = get_post($postid);
	$titel = $pagedata->post_title;

	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
		
	$result = array();
		
	ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	
      
     	<?php nxs_render_popup_header(nxs_l18n__("Change title[nxs:popup,heading]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		
					<div class="content2">
		        <div class="box">
		            <div class="box-title">
		                <h4><?php nxs_l18n_e("Title[nxs:heading]", "nxs_td"); ?></h4>
		             </div>
		            <div class="box-content">
		            	<input id="titel" name="titel" type='text' value='<?php echo nxs_render_html_escape_singlequote($titel); ?>' />
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		            
		    </div>
		  </div>
		            
      <div class="content2">
          <div class="box">
            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='save(); return false;'><?php nxs_l18n_e("Save[nxs:popup,button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
         	</div>
          <div class="nxs-clear">
          </div>
      </div> <!--END content-->
		</div>
	</div>
	
	<script type='text/javascript'>
		
		function nxs_js_setpopupdatefromcontrols()
		{
			nxs_js_popup_storestatecontroldata_textbox("titel", "titel");
		}
		
		function save()
		{
			nxs_js_setpopupdatefromcontrols();
			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQuery.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updatepagedata",
						"postid": "<?php echo $postid;?>",
						"updatesectionid": "edittitle",
						"titel": nxs_js_popup_getsessiondata("titel")
					},
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// close the pop up
							nxs_js_closepopup_unconditionally();
							
							// refresh current page (if the footer is updated we could decide to
							// update only the footer, but this is needless; an update of the page is ok too)
							nxs_js_refreshcurrentpage();
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
			jQuery('#titel').focus();
		}
		
	</script>
		
	<?php
	
	$html = ob_get_contents();
	ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

//
// wordt aangeroepen bij het opslaan van data
//
function nxs_ws_page_updatedata($args)
{
	extract($args);
	
	$modifiedmetadata = array();
	
	if ($updatesectionid == "home")
	{	
		// update meta data page
		$modifiedmetadata = array();
		$modifiedmetadata["pagetemplate"] = $pagetemplate;
		
		nxs_merge_postmeta($postid, $modifiedmetadata);
	}
	else if ($updatesectionid == "edittitle")
	{
		nxs_disabledwprevisions();
		
		// update title, slug and categories
		$my_post = array();
		$my_post['ID'] = $postid;
		$my_post['post_title'] = $titel;

		wp_update_post($my_post);
	}
	else
	{
		nxs_webmethod_return_nack("Unsupported updatesectionid;" . $updatesectionid);
	}
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_post_dialogappendgenericlistitem_rendersheet($args)	
{
	//
	extract($args);
			
	$result = array();
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	ob_start();

	$pagedata = get_page($postid);
	$nxsposttype = nxs_getnxsposttype_by_wpposttype($pagedata->post_type);
	
	$posttype = $pagedata->post_type;
	$nxssubposttype = nxs_get_nxssubposttype($postid);

	$postmeta = nxs_get_postmeta($postid);
	$pagetemplate = nxs_getpagetemplateforpostid($postid);	
	
	$phtargs = array();
	$phtargs["invoker"] = "nxsextundefined";
	$phtargs["wpposttype"] = $posttype;
	$phtargs["nxsposttype"] = nxs_getnxsposttype_by_wpposttype($posttype);
	$phtargs["nxssubposttype"] = $nxssubposttype;	// NOTE
	$phtargs["pagetemplate"] = $pagetemplate;
	
	$widgets = nxs_getwidgets($phtargs);
	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	

     	<?php nxs_render_popup_header(nxs_l18n__("Add[nxs:popup,heading]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		
					<div class="content2">
		        <div class="box">
		          <div class="box-title" style='width: 400px;'>
		            <h4><?php nxs_l18n_e("Select a widget to append[nxs:popup,newrow]", "nxs_td"); ?></h4>
		          </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		
		      <div class="content2">
		        <div class="box">
		        	<ul class="placeholder3">
								<?php
									// for each placeholder -->
									foreach ($widgets as $currentwidget)
									{
										$title = $currentwidget["title"];
										$widgetid = $currentwidget["widgetid"];
										$iconid = nxs_getplaceholdericonid($widgetid);
										?>
										
		        				<a href="#" onclick="selectplaceholdertype(this, '<?php echo $widgetid; ?>'); return false;">
											<li>
												<span id='placeholdertemplate_<?php echo $widgetid; ?>' class='nxs-widget-icon <?php echo $iconid; ?>'></span>
												<p><?php echo $title; ?></p>
											</li>
										</a>
										<?php
									}
								?>
		        	</ul>
		        </div>
		        <div class="nxs-clear"></div>
		      </div>
		    
			  </div>
			</div>
      
      <div class="content2">
         <div class="box">
            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Add[nxs:popup,newrow,button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>                    
         </div>
         <div class="nxs-clear"></div>
      </div> <!--END content-->
    	
    </div>
  </div>
	
	<script type='text/javascript'>
		
		<?php
		$numofoptions = count($widgets);
		if ($numofoptions == 1)
		{
			// get the first one
			$currentwidget = $widgets[0];
			$widgetid = $currentwidget["widgetid"];
			//
			?>
			nxs_js_log("UX improvement; since theres only one option, pick that one automatically ...");
			selectplaceholdertype(this, '<?php echo $widgetid; ?>');
			<?php
		}
		?>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}
		
		function selectplaceholdertype(obj, placeholdertype)
		{
			nxs_js_popup_setsessiondata("type", placeholdertype);
			// auto save
			nxs_js_savegenericpopup();
		}
		
		function nxs_js_savegenericpopup()
		{
			var e = jQuery(".nxs-layout-editable .nxs-postrows")[0];
			
			var waitgrowltoken = nxs_js_alert_wait_start("<?php nxs_l18n_e("Adding slide[nxs:growl]","nxs_td"); ?>");
			
			var totalrows = jQuery(document).find(".nxs-row").length;
			nxs_js_log("totalrows:" + totalrows);
			
			var insertafterindex;
			insertafterindex = totalrows - 1;
			
			nxs_js_log("inserting after index:" + insertafterindex);
			
			// voeg een "one" row toe
			var widget = nxs_js_popup_getsessiondata("type");
			nxs_js_log('inserting widget of type:' + widget);
			nxs_js_addnewrowwithtemplate('<?php echo $postid; ?>', insertafterindex, "one", widget, e, 
			function()
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				
				// question; is the rowelement fully added when we reach this point?
				nxs_js_log("postid:<?php echo $postid; ?>");
				var rowelement = nxs_js_getrowelement('<?php echo $postid; ?>', totalrows);
				nxs_js_log(rowelement);
				
				var placeholderids = nxs_js_getplaceholderidsinrow(rowelement);
				nxs_js_log(placeholderids);
				
				// there should be exactly one placeholderid in the list
				var placeholderid = placeholderids[0];
				
				// clear dirty indicator, if its present...
				nxs_js_popup_sessiondata_clear_dirty()

				// open popup to edit the newly added widget
				var domelementinwidget = jQuery('.nxs-post-<?php echo $postid; ?> #nxs-widget-' + placeholderid);
				nxs_js_edit_widget(domelementinwidget);
			},
			function()
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				// bummer....
			}
			);			
		}
		
	</script>
	
	<?php
	
	$html = ob_get_contents();
	ob_end_clean();

	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_post_dialogappendbusrulessetitem_rendersheet($args)	
{
	//
	extract($args);
			
	$result = array();
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	ob_start();

	$pagedata = get_page($postid);
	$nxsposttype = nxs_getnxsposttype_by_wpposttype($pagedata->post_type);
	
	$posttype = $pagedata->post_type;
	$nxssubposttype = nxs_get_nxssubposttype($postid);

	$postmeta = nxs_get_postmeta($postid);
	$pagetemplate = nxs_getpagetemplateforpostid($postid);	
	
	$phtargs = array();
	$phtargs["invoker"] = "nxsextundefined";
	$phtargs["wpposttype"] = $posttype;
	$phtargs["nxsposttype"] = nxs_getnxsposttype_by_wpposttype($posttype);
	$phtargs["nxssubposttype"] = $nxssubposttype;	// NOTE
	$phtargs["pagetemplate"] = $pagetemplate;
	
	$widgets = nxs_getwidgets($phtargs);
	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	

     	<?php nxs_render_popup_header(nxs_l18n__("Add[nxs:popup,heading]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		
					<div class="content2">
		        <div class="box">
		          <div class="box-title" style='width: 400px;'>
		            <h4><?php nxs_l18n_e("Select a widget to append[nxs:popup,newrow]", "nxs_td"); ?></h4>
		          </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		
		      <div class="content2">
		        <div class="box">
		        	<ul class="placeholder3">
								<?php
									// for each placeholder -->
									foreach ($widgets as $currentwidget)
									{
										$title = $currentwidget["title"];
										$widgetid = $currentwidget["widgetid"];
										$iconid = nxs_getplaceholdericonid($widgetid);
										?>
										
		        				<a href="#" onclick="selectplaceholdertype(this, '<?php echo $widgetid; ?>'); return false;">
											<li>
												<span id='placeholdertemplate_<?php echo $widgetid; ?>' class='nxs-widget-icon <?php echo $iconid; ?>'></span>
												<p><?php echo $title; ?></p>
											</li>
										</a>
										<?php
									}
								?>
		        	</ul>
		        </div>
		        <div class="nxs-clear"></div>
		      </div>
		    
			  </div>
			</div>
      
      <div class="content2">
         <div class="box">
            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Add[nxs:popup,newrow,button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>                    
         </div>
         <div class="nxs-clear"></div>
      </div> <!--END content-->
    	
    </div>
  </div>
	
	<script type='text/javascript'>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}
		
		function selectplaceholdertype(obj, placeholdertype)
		{
			nxs_js_popup_setsessiondata("type", placeholdertype);
			// auto save
			nxs_js_savegenericpopup();
		}
		
		function nxs_js_savegenericpopup()
		{
			var e = jQuery(".nxs-layout-editable .nxs-postrows")[0];
			
			var waitgrowltoken = nxs_js_alert_wait_start("<?php nxs_l18n_e("Adding rule[nxs:growl]","nxs_td"); ?>");
			
			var totalrows = jQuery(document).find(".nxs-row").length;
			nxs_js_log("totalrows:" + totalrows);
			
			var insertafterindex;
			insertafterindex = -1; // totalrows - 1;
			
			nxs_js_log("inserting after index:" + insertafterindex);
			
			// voeg een "one" row toe
			var widget = nxs_js_popup_getsessiondata("type");
			nxs_js_log('inserting widget of type:' + widget);
			nxs_js_addnewrowwithtemplate('<?php echo $postid; ?>', insertafterindex, "one", widget, e, 
			function()
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				
				nxs_js_log("postid:<?php echo $postid; ?>");
				// var rowindexer = totalrows; // use this to add at the end
				// var rowindexer = 0;
				var rowelement = nxs_js_getrowelement('<?php echo $postid; ?>', insertafterindex + 1);
				nxs_js_log(rowelement);
				
				var placeholderids = nxs_js_getplaceholderidsinrow(rowelement);
				nxs_js_log(placeholderids);
				
				// there should be exactly one placeholderid in the list
				var placeholderid = placeholderids[0];
				
				// clear dirty indicator, if its present...
				nxs_js_popup_sessiondata_clear_dirty()

				// open popup to edit the newly added widget
				var domelementinwidget = jQuery('.nxs-post-<?php echo $postid; ?> #nxs-widget-' + placeholderid);
				nxs_js_edit_widget(domelementinwidget);
			},
			function()
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				// bummer....
			}
			);			
		}
		
	</script>
	
	<?php
	
	$html = ob_get_contents();
	ob_end_clean();

	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}


function nxs_post_dialogappendtemplateitem_rendersheet($args)	
{
	//
	extract($args);
			
	$result = array();
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	ob_start();

	$pagedata = get_page($postid);
	$nxsposttype = nxs_getnxsposttype_by_wpposttype($pagedata->post_type);
	
	$posttype = $pagedata->post_type;
	$nxssubposttype = nxs_get_nxssubposttype($postid);

	$postmeta = nxs_get_postmeta($postid);
	$pagetemplate = nxs_getpagetemplateforpostid($postid);	
	
	$phtargs = array();
	$phtargs["invoker"] = "nxsextundefined";
	$phtargs["wpposttype"] = $posttype;
	$phtargs["nxsposttype"] = nxs_getnxsposttype_by_wpposttype($posttype);
	$phtargs["nxssubposttype"] = $nxssubposttype;	// NOTE
	$phtargs["pagetemplate"] = $pagetemplate;
	
	$widgets = nxs_getwidgets($phtargs);
	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	

     	<?php nxs_render_popup_header(nxs_l18n__("Add[nxs:popup,heading]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		
					<div class="content2">
		        <div class="box">
		          <div class="box-title" style='width: 400px;'>
		            <h4><?php nxs_l18n_e("Select a widget to append[nxs:popup,newrow]", "nxs_td"); ?></h4>
		          </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		
		      <div class="content2">
		        <div class="box">
		        	<ul class="placeholder3">
								<?php
									// for each placeholder -->
									foreach ($widgets as $currentwidget)
									{
										$title = $currentwidget["title"];
										$widgetid = $currentwidget["widgetid"];
										$iconid = nxs_getplaceholdericonid($widgetid);
										?>
										
		        				<a href="#" onclick="selectplaceholdertype(this, '<?php echo $widgetid; ?>'); return false;">
											<li>
												<span id='placeholdertemplate_<?php echo $widgetid; ?>' class='nxs-widget-icon <?php echo $iconid; ?>'></span>
												<p><?php echo $title; ?></p>
											</li>
										</a>
										<?php
									}
								?>
		        	</ul>
		        </div>
		        <div class="nxs-clear"></div>
		      </div>
		    
			  </div>
			</div>
      
      <div class="content2">
         <div class="box">
            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Add[nxs:popup,newrow,button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>                    
         </div>
         <div class="nxs-clear"></div>
      </div> <!--END content-->
    	
    </div>
  </div>
	
	<script type='text/javascript'>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}
		
		function selectplaceholdertype(obj, placeholdertype)
		{
			nxs_js_popup_setsessiondata("type", placeholdertype);
			// auto save
			nxs_js_savegenericpopup();
		}
		
		function nxs_js_savegenericpopup()
		{
			var e = jQuery(".nxs-layout-editable .nxs-postrows")[0];
			
			var waitgrowltoken = nxs_js_alert_wait_start("<?php nxs_l18n_e("Adding rule[nxs:growl]","nxs_td"); ?>");
			
			var totalrows = jQuery(document).find(".nxs-row").length;
			nxs_js_log("totalrows:" + totalrows);
			
			var insertafterindex;
			insertafterindex = totalrows - 1;
			
			nxs_js_log("inserting after index:" + insertafterindex);
			
			// voeg een "one" row toe
			var widget = nxs_js_popup_getsessiondata("type");
			nxs_js_log('inserting widget of type:' + widget);
			nxs_js_addnewrowwithtemplate('<?php echo $postid; ?>', insertafterindex, "one", widget, e, 
			function()
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				
				// question; is the rowelement fully added when we reach this point?
				nxs_js_log("postid:<?php echo $postid; ?>");
				var rowelement = nxs_js_getrowelement('<?php echo $postid; ?>', totalrows);
				nxs_js_log(rowelement);
				
				var placeholderids = nxs_js_getplaceholderidsinrow(rowelement);
				nxs_js_log(placeholderids);
				
				// there should be exactly one placeholderid in the list
				var placeholderid = placeholderids[0];
				
				// clear dirty indicator, if its present...
				nxs_js_popup_sessiondata_clear_dirty()

				// open popup to edit the newly added widget
				var domelementinwidget = jQuery('.nxs-post-<?php echo $postid; ?> #nxs-widget-' + placeholderid);
				nxs_js_edit_widget(domelementinwidget);
			},
			function()
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				// bummer....
			}
			);			
		}
		
	</script>
	
	<?php
	
	$html = ob_get_contents();
	ob_end_clean();

	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_post_dialogappendbulkgenericlistitems_rendersheet($args)	
{
	//
	extract($args);
			
	$result = array();
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	$nxssubposttype = nxs_get_nxssubposttype($postid);
	if ($nxssubposttype == "gallery")
	{
		$bulkappendtype = "galleryitem";
	}
	else if ($nxssubposttype == "banner")
	{
		$bulkappendtype = "banneritem";
	}
	else
	{
		nxs_webmethod_return_nack("Unsupported nxssubposttype;" . $nxssubposttype);
	}
	
	ob_start();
	?>
	<div class="nxs-admin-wrap">
		<div class="block">
     	<?php nxs_render_popup_header(nxs_l18n__("Add bulk", "nxs_td")); ?>
			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
					<div class="content2">
		        <div class="box">
		          <div class="box-title" style='width: 400px;'>
		            <h4>BULK UPLOADER</h4>
		          </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
		      <div class="content2">
						<?php
						$fileuploadurl = admin_url('admin-ajax.php');
						?>
						<form id="nxsuploadform" action="<?php echo $fileuploadurl;?>" method="POST" enctype="multipart/form-data">
							
							<fieldset>
								<legend>HTML File Upload</legend>
								
								<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="300000" />
								
								<div>
									<label for="file">Files to upload:</label>
									<input type="file" id="file" name="file[]" multiple="multiple" onchange="nxs_js_storefile();" />
									<!-- <div id="filedrag">or drop files here</div> -->
								</div>
								
								<div id="submitbutton">
									<button type="submit">Upload Files</button>
								</div>
								
							</fieldset>
						
						</form>

		      </div>
		      
			  </div>
			</div>
			
			<script type='text/javascript'>
				function nxs_js_storefile()
				{
					nxs_js_log("nxs_js_storefile; STORING FILE");
					var options = 
		      { 
		        data:
		        {
		            action: "nxs_ajax_webmethods",
		            webmethod: "savemultifileupload",
		            uploadtitel: jQuery("#nxs_titel").val(),
 		            postprocessor: "append",
 		            appendtype: "<?php echo $bulkappendtype; ?>",
		            postid: <?php echo $postid; ?>,
		            unusedclosingelement: true
		        },
		        dataType: 'json',
		        iframe: true,
		        success: function(response) 
						{
							nxs_js_log(response);
							if (response.result == "OK")
							{
								// refresh current page (if the footer is updated we could decide to
								// update only the footer, but this is needless; an update of the page is ok too)
								nxs_js_refreshcurrentpage();
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
		    	};
		        
					jQuery('#nxsuploadform').ajaxForm(options);
				}
				
				function nxs_js_processsingleupload(data, type)
				{
					var e = jQuery(".nxs-layout-editable .nxs-postrows")[0];
					
					var waitgrowltoken = nxs_js_alert_wait_start("<?php nxs_l18n_e("Adding slide[nxs:growl]","nxs_td"); ?>");
					
					var totalrows = jQuery(document).find(".nxs-row").length;
					nxs_js_log("totalrows:" + totalrows);
					
					var insertafterindex;
					insertafterindex = totalrows - 1;
					
					nxs_js_log("inserting after index:" + insertafterindex);
					
					// voeg een "one" row toe
					var widget = type;
					nxs_js_log('inserting widget of type:' + widget);
					nxs_js_addnewrowwithtemplate
					(
						'<?php echo $postid; ?>', 
						insertafterindex, 
						"one", 
						widget, 
						e, 
						function()
						{
							nxs_js_alert_wait_finish(waitgrowltoken);
							
							// question; is the rowelement fully added when we reach this point?
							nxs_js_log("postid:<?php echo $postid; ?>");
							var rowelement = nxs_js_getrowelement('<?php echo $postid; ?>', totalrows);
							nxs_js_log(rowelement);
							
							var placeholderids = nxs_js_getplaceholderidsinrow(rowelement);
							nxs_js_log(placeholderids);
							
							// there should be exactly one placeholderid in the list
							var placeholderid = placeholderids[0];
							
							// clear dirty indicator, if its present...
							nxs_js_popup_sessiondata_clear_dirty()
			
							// open popup to edit the newly added widget
							var domelementinwidget = jQuery('.nxs-post-<?php echo $postid; ?> #nxs-widget-' + placeholderid);
							nxs_js_edit_widget(domelementinwidget);
						},
						function()
						{
							nxs_js_alert_wait_finish(waitgrowltoken);
							// bummer....
						}
					);			
				}

			</script>
			
      <!--
      <div class="content2">
         <div class="box">
            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Add[nxs:popup,newrow,button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>                    
         </div>
         <div class="nxs-clear"></div>
      </div> -->
    	
    </div>
  </div>
	
	<script type='text/javascript'>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}
		
		function selectplaceholdertype(obj, placeholdertype)
		{
			nxs_js_popup_setsessiondata("type", placeholdertype);
			// auto save
			nxs_js_savegenericpopup();
		}
		
		function nxs_js_savegenericpopup()
		{
			var e = jQuery(".nxs-layout-editable .nxs-postrows")[0];
			
			var waitgrowltoken = nxs_js_alert_wait_start("<?php nxs_l18n_e("Adding slide[nxs:growl]","nxs_td"); ?>");
			
			var totalrows = jQuery(document).find(".nxs-row").length;
			nxs_js_log("totalrows:" + totalrows);
			
			var insertafterindex;
			insertafterindex = totalrows - 1;
			
			nxs_js_log("inserting after index:" + insertafterindex);
			
			// voeg een "one" row toe
			var widget = nxs_js_popup_getsessiondata("type");
			nxs_js_log('inserting widget of type:' + widget);
			nxs_js_addnewrowwithtemplate('<?php echo $postid; ?>', insertafterindex, "one", widget, e, 
			function()
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				
				// question; is the rowelement fully added when we reach this point?
				nxs_js_log("postid:<?php echo $postid; ?>");
				var rowelement = nxs_js_getrowelement('<?php echo $postid; ?>', totalrows);
				nxs_js_log(rowelement);
				
				var placeholderids = nxs_js_getplaceholderidsinrow(rowelement);
				nxs_js_log(placeholderids);
				
				// there should be exactly one placeholderid in the list
				var placeholderid = placeholderids[0];
				
				// clear dirty indicator, if its present...
				nxs_js_popup_sessiondata_clear_dirty()

				// open popup to edit the newly added widget
				var domelementinwidget = jQuery('.nxs-post-<?php echo $postid; ?> #nxs-widget-' + placeholderid);
				nxs_js_edit_widget(domelementinwidget);
			},
			function()
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				// bummer....
			}
			);			
		}
		
	</script>
	
	<?php
	
	$html = ob_get_contents();
	ob_end_clean();

	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}



?>