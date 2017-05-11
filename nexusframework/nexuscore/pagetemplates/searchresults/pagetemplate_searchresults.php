<?php

function nxs_pagetemplate_searchresults_gettitle($args)
{
	return nxs_l18n__("Searchresults[nxs:title]", "nxs_td");
}

function nxs_pagetemplate_searchresults_render($args)
{
		// delegate to pagetemplate of blogentry; it renders in an identical way
	nxs_requirepagetemplate("blogentry");
	nxs_pagetemplate_blogentry_render($args);	
}

function nxs_pagetemplate_searchresults_renderpreview($args)
{
	?>
	<div class="content2">
    <div class="box">
        <div class="box-title">
            <h4>&nbsp;</h4>
         </div>
        <div class="box-content">
        	<span class='title'>
        		<?php nxs_l18n_e("Description of searchresults preview[nxs:preview]", "nxs_td"); ?>
        	</span>
        </div>
    </div>
    <div class="nxs-clear"></div>
  </div> <!--END content-->
	<?php
}

function nxs_pagetemplate_searchresults_home_getsheethtml($args)
{
		//
	extract($args);
	
	$pagemeta = nxs_get_corepostmeta($postid);
	$pagetemplate = nxs_getpagetemplateforpostid($postid);
	
	$iscurrentpagetheserppage = nxs_isserppage($postid);
	$selectedcategories = get_the_category($postid);
	$pagemeta = nxs_get_corepostmeta($postid);
	$titel = nxs_gettitle_for_postid($postid);
	$slug = nxs_getslug_for_postid($postid);
	$selectedcategoryids = "";
	// we explicitly set the pagetemplate; if the page has a different pagetemplate,
	// and the user changes the pagetemplate it would still invoke the 'updatedata' of
	// the old pagetemplate and the pagetemplate itself would not be changed :)
	$pagetemplate = "searchresults";

	foreach ($selectedcategories as $selectedcategory) 
	{
		$additional = "[" . $selectedcategory->term_id . "]";
		$selectedcategoryids .= $additional;
	}
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	$result = array();
	
	$catargs = array();
	$catargs['hide_empty'] = 0;
	$categories = get_categories($catargs);
	
	$categoriesfilters = array();
  $categoriesfilters["uncategorized"] = "skip";
  nxs_getfilteredcategories($categories, $categoriesfilters);	
		
	nxs_ob_start();

	?>

	<div class="nxs-admin-wrap">
		<div class="block">	
      
     	<?php nxs_render_popup_header(nxs_l18n__("Template searchresults[nxs:popup,header]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">

		      <div class="content2">
		        <div class="box">
		          <div class="box-title">
	          		<h4><?php nxs_l18n_e("Template[nxs:heading]", "nxs_td"); ?></h4>
		           </div>
		          <div class="box-content">
					      <a class='nxsbutton1 nxs-float-right' title="" . nxs_l18n__("Change template[nxs:tooltip]", "nxs_td") . "" href='#' onclick="nxs_js_popup_page_neweditsession('<?php echo $postid;?>', 'home'); return false;"><?php nxs_l18n_e("Change template[nxs:button]", "nxs_td"); ?></a>
					      <?php nxs_l18n_e("Template searchresults[nxs:popup,header]", "nxs_td"); ?>
					    </div>
					  </div>
					  <div class="nxs-clear"></div>
					</div>
					
					<!-- design -->
					<div class="content2">
		        <div class="box">
		          <div class="box-title">
	          		<h4><?php nxs_l18n_e("Styling[nxs:heading]", "nxs_td"); ?></h4>
		           </div>
		          <div class="box-content">
					      <a class='nxsbutton1 nxs-float-right' title="<?php nxs_l18n_e("Change", "nxs_td"); ?>" href='#' onclick="nxs_js_popup_navigateto('styling'); return false;"><?php nxs_l18n_e("Change[nxs:popup,header]", "nxs_td"); ?></a>
					    </div>
					  </div>
					  <div class="nxs-clear"></div>
					</div>
					      
					<div class="content2">
		        <div class="box">
		          <div class="box-title">
			      		<h4><?php nxs_l18n_e("Title[nxs:heading]", "nxs_td"); ?></h4>
		           </div>
		          <div class="box-content">
		          	<input id="titel" type='text' name="titel" value='<?php echo nxs_render_html_escape_singlequote($titel); ?>' />
		        	</div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
					
					<div class="content2">
		        <div class="box">
		          <div class="box-title">
								<h4><?php nxs_l18n_e("Internet address (url)[nxs:heading]", "nxs_td"); ?></h4>		                
		           </div>
		          <div class="box-content">
		          	<?php
		          	$actualurl = nxs_geturl_for_postid($postid);
		          	$urlwithoutslug = nxs_str_lastreplace($slug . "/", "", $actualurl);
		          	if (!nxs_stringendswith($urlwithoutslug, "/"))
		          	{
		          		$urlwithoutslug .= "/";
		          	}
		          	$containsslug = nxs_stringcontains($actualurl, $slug);
		          	?>
		          	<span class="nxs-float-left title"><?php echo $urlwithoutslug; echo $trailer?></span>
		          	<?php if ($containsslug) { ?>
		          	<input id="slug" type='text' class="nxs-width60" name="slug" value='<?php echo nxs_render_html_escape_singlequote($slug); ?>' />
		          	<?php } else { ?>
		          	<input id="slug" type='hidden' name="slug" value='<?php echo nxs_render_html_escape_singlequote($slug); ?>' />
		          	<?php } ?>
		        	</div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
		      <div class="content2">
		        <div class="box">
		
		          <div class="box-title">
	          		<h4><?php nxs_l18n_e("Set as resultpage[nxs:heading]", "nxs_td"); ?></h4>
		           </div>
		
				      <?php if ($iscurrentpagetheserppage) { ?>
		
		            <div class="box-content">
		            	<span class="nxs-title"><?php nxs_l18n_e("Already set[nxs:label]", "nxs_td"); ?></span>
		            	<input id="markasserppage" name="markasserppage" type="checkbox" <?php echo $markasserppage; ?> onchange="nxs_js_popup_sessiondata_make_dirty();" style="display: none;" />
		            </div>
		            
							<?php } else { ?>
		
		            <div class="box-content">
									<input id="markasserppage" name="markasserppage" type="checkbox" <?php echo $markasserppage; ?> onchange="nxs_js_popup_sessiondata_make_dirty();" />
		            </div>
		
							<?php } ?>
		            
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->      

	      </div>
	    </div>
      
      <!-- footer -->
      
      <div class="content2">
        <div class="box">
        	<!--
        	<a href='#' class="nxsbutton1 nxs-float-left" onclick='nxs_js_popup_navigateto("appendstruct"); return false;'>Append data</a>
        	<a href='#' class="nxsbutton1 nxs-float-left" onclick='nxs_js_popup_navigateto("exportstruct"); return false;'>Export data</a>
        	-->
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Save[nxs:button]", "nxs_td"); ?></a>
          <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:button]", "nxs_td"); ?></a>
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:button]", "nxs_td"); ?></a>
       	</div>
        <div class="nxs-clear">
        </div>
      </div> <!--END content-->
		</div>
	</div>
	
	<script type='text/javascript'>
			
		function nxs_js_savepopupdata()
		{
			nxs_js_popup_storestatecontroldata_textbox("titel", "titel");
			nxs_js_popup_storestatecontroldata_textbox("slug", "slug");
			
			nxs_js_popup_storestatecontroldata_checkbox('markasserppage', 'markasserppage');
			
			nxs_js_popup_storestatecontroldata_listofcheckbox('selectedcategoryids', 'selectable_category', 'selectedcategoryids');
		}
		
		function nxs_js_execute_after_popup_shows()
		{
			jQuery('#titel').focus();
			//
			
			<?php 
			$persistedpagetemplate = nxs_getpagetemplateforpostid($postid);
			if ($persistedpagetemplate != $pagetemplate) { ?>
				// we start by making this popup session dirty,
				// because it appears the pagetemplate we see here,
				// is not equal to the one persisted (which can
				// only be the case if the user is modifying the
				// pagetemplate
				nxs_js_popup_sessiondata_make_dirty();
			<?php } ?>
		}
		
		function nxs_js_savegenericpopup()
		{
			//
			nxs_js_savepopupdata();			
			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updatepagetemplatedata",
						"postid": "<?php echo $postid;?>",
						"pagetemplate": "<?php echo $pagetemplate;?>",
						"updatesectionid": "home",
						"pagetemplate": "searchresults",
						"titel": nxs_js_popup_getsessiondata("titel"),
						"slug": nxs_js_popup_getsessiondata("slug"),
						"markasserppage": nxs_js_popup_getsessiondata("markasserppage")
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
							nxs_js_redirecttopostid(<?php echo $postid;?>);
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
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_pagetemplate_searchresults_edittitle_getsheethtml($args)
{
	//
	extract($args);

	$pagedata = get_post($postid);
	$titel = $pagedata->post_title;

	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
		
	$result = array();
		
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	
      
     	<?php nxs_render_popup_header(nxs_l18n__("Change title[nxs:popup,header]", "nxs_td")); ?>

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
            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='save(); return false;'><?php nxs_l18n_e("Save[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:button]", "nxs_td"); ?></a>
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
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updatepagetemplatedata",
						"postid": "<?php echo $postid;?>",
						"pagetemplate": "searchresults",
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
							nxs_js_redirecttopostid(<?php echo $postid;?>);
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
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_pagetemplate_searchresults_headerhome_getsheethtml($args)
{
	//
	extract($args);
			
	$result = array();
	
	$meta = nxs_get_corepostmeta($postid);
	$header_postid = $meta["header_postid"];
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	if ($nieuweheader == "true")
	{
		// creeer nieuwe header
		// initialiseer nieuwe header
		// set header_postid op nieuwe header
		
		$identifier = "Header " . nxs_gettimestampasstring();
		
		$args = array();
		$args["slug"] = $identifier;
		$args["titel"] = $identifier;
		$args["nxsposttype"] = "header";
		$args["postwizard"] = "default_header";
		$response = nxs_addnewarticle($args);
		$header_postid = $response["postid"];
	}
	
	$titleofexistingheader = nxs_gettitle_for_postid($header_postid);
	$slugofexistingheader = nxs_getslug_for_postid($header_postid);
	
	//		
	$publishedargs = array();
	$publishedargs["post_status"] = "publish";
	$publishedargs["post_type"] = "nxs_header";
	//$publishedargs["orderby"] = $order_by;
	//$publishedargs["order"] = $order;
	$publishedargs["numberposts"] = -1;	// allemaal!
  $showpages = get_posts($publishedargs);
  // apply runtime filters (outside SQL)
  
// add custom filters	
  $filters = array();
  $filters["nxsposttype"] = "header";
  
  nxs_getfilteredposts($showpages, $filters);
	
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	
      
     	<?php nxs_render_popup_header(nxs_l18n__("Set header[nxs:popup,header]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		
		      <?php if ($header_postid != "") { ?>
		      
		      <div class="content2">
		        <div class="box">
		            <div class="box-title">
	          			<h4><?php nxs_l18n_e("Header[nxs:heading]", "nxs_td"); ?></h4>
	              </div>
		            <div class="box-content">
		            	<a class="nxsbutton1 nxs-float-right" href="#" onclick="nxs_js_popup_sessiondata_make_dirty(); nxs_js_popup_setsessiondata('header_postid', ''); nxs_js_popup_refresh();"><?php nxs_l18n_e("Select other header[nxs:button]", "nxs_td"); ?></a>
		            	<span class='title'><?php echo $titleofexistingheader; ?></span>
		            	<div class="nxs-clear"></div>
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
					<?php } ?>
		
					<?php
					if ($header_postid == "")
					{
					?>
		
					<div class="content2">
		        <div class="box">
		            <div class="box-title">
		          		<h4><?php nxs_l18n_e("Header[nxs:heading]", "nxs_td"); ?></h4>
		             </div>
		            <div class="box-content">
		            	<a class="nxsbutton1 nxs-float-right" href="#" onclick="nxs_js_popup_sessiondata_make_dirty(); nxs_js_popup_setshortscopedata('nieuweheader', 'true'); nxs_js_popup_refresh();"><?php nxs_l18n_e("New header[nxs:button]", "nxs_td"); ?></a>
		            	
		          		<select id='header_postid' name='header_postid' onchange="nxs_js_popup_sessiondata_make_dirty(); nxs_js_popup_setsessiondata('header_postid', jQuery(this).val()); nxs_js_popup_refresh();">
		            		<option value=''><?php nxs_l18n_e("Suppress header[nxs:ddl]", "nxs_td"); ?></option>
		            		<?php 		    		
		            		// loop over available pages
				    				foreach ($showpages as $currentpost)
				    				{
				    					$posttitle = $currentpost->post_title;
				    					$headerid = $currentpost->ID;
				    					if ($header_postid == $headerid)
				    					{
				    						$selected = "selected='selected'";
				    					}
				    					else
				    					{
				    						$selected = "";
				    					}	
				    					echo "<option " . $selected . " value='" . $headerid . "'>" . $posttitle . "</option>";
				    				}
				    				?>
		            	</select>
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
		      <?php 
			    } else {
			    ?>
			    	<input id='header_postid' name='header_postid' value='<?php echo $header_postid; ?>' type='hidden' />
			    <?php
			  	}
			    ?>
			  
				</div>
			</div>
      
      <div class="content2">
          <div class="box">
            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Save[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:button]", "nxs_td"); ?></a>
         	</div>
          <div class="nxs-clear">
          </div>
      </div> <!--END content-->
		</div>
	</div>
	
	<script type='text/javascript'>
		
		function nxs_js_saveandopenheader()
		{
			nxs_js_savegenericpopup_core(function() {
				var url = "<?php echo get_home_url() . "/?nxs_header=" . urlencode(nxs_getslug_for_postid($header_postid)); ?>" + "&nxsrefurlspecial=" + nxs_js_get_nxsrefurlspecial()();
				nxs_js_redirect(url);
			});
		}
		
		function nxs_js_savegenericpopup()
		{
			nxs_js_savegenericpopup_core(function() {
				// close the pop up
				nxs_js_closepopup_unconditionally();
				
				// since the slug can be adjusted, we will refresh to the postid instead of the current url
				nxs_js_redirecttopostid(<?php echo $postid;?>);
			});
		}
		
		function nxs_js_savegenericpopup_core(invokewhenavailable)
		{
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updatepagetemplatedata",
						"postid": "<?php echo $postid;?>",
						"pagetemplate": "searchresults",
						"updatesectionid": "header",
						"header_postid": jQuery('#header_postid').val()
					},
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							invokewhenavailable();
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
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_pagetemplate_searchresults_sidebarhome_getsheethtml($args)
{
	//
	extract($args);
			
	$result = array();
	
	$meta = nxs_get_corepostmeta($postid);
	$sidebar_postid = $meta["sidebar_postid"];
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	if ($nieuwesidebar == "true")
	{
		// creeer nieuwe sidebar
		// initialiseer nieuwe sidebar
		// set sidebar_postid op nieuwe sidebar
		
		$identifier = "Sidebar " . nxs_gettimestampasstring();
		
		$args = array();
		$args["slug"] = $identifier;
		$args["titel"] = $identifier;
		$args["nxsposttype"] = "sidebar";
		$args["postwizard"] = "default_sidebar";
		$response = nxs_addnewarticle($args);
		$sidebar_postid = $response["postid"];
	}
	
	$titleofexistingsidebar = nxs_gettitle_for_postid($sidebar_postid);
	$slugofexistingsidebar = nxs_getslug_for_postid($sidebar_postid);
	
	//		
	$publishedargs = array();
	$publishedargs["post_status"] = "publish";
	$publishedargs["post_type"] = "nxs_sidebar";
	//$publishedargs["orderby"] = $order_by;
	//$publishedargs["order"] = $order;
	$publishedargs["numberposts"] = -1;	// allemaal!
  $showpages = get_posts($publishedargs);
  // apply runtime filters (outside SQL)
 
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	
      
     	<?php nxs_render_popup_header(nxs_l18n__("Set sidebar[nxs:button]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		
		      <?php if ($sidebar_postid != "") { ?>
		      
		      <div class="content2">
		        <div class="box">
							<div class="box-title">
							  <h4><?php nxs_l18n_e("Sidebar[nxs:label]", "nxs_td"); ?></h4>
							</div>
		        </div>
		        <div class="box-content">
		        	<a class="nxsbutton1 nxs-float-right" href="#" onclick="nxs_js_popup_sessiondata_make_dirty(); nxs_js_popup_setsessiondata('sidebar_postid', ''); nxs_js_popup_refresh();"><?php nxs_l18n_e("Select different sidebar(webpage)[nxs:popup,header]", "nxs_td"); ?></a>
		        	
		        	<span class='title'><?php echo $titleofexistingsidebar; ?></span>
		        	<div class="nxs-clear"></div>
		      	</div>
		        
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
					<?php } ?>
		
					<?php
					if ($sidebar_postid == "")
					{
					?>
		
					<div class="content2">
		        <div class="box">
		            <div class="box-title">
	          			<h4><?php nxs_l18n_e("Sidebar[nxs:heading]", "nxs_td"); ?></h4>
		            </div>
		            <div class="box-content">
		            	<a class="nxsbutton1 nxs-float-right" href="#" onclick="nxs_js_popup_sessiondata_make_dirty(); nxs_js_popup_setshortscopedata('nieuwesidebar', 'true'); nxs_js_popup_refresh();"><?php nxs_l18n_e("New sidebar[nxs:popup,button]", "nxs-ext-pagetemplate-searchresult"); ?></a>
		            	
		          		<select id='sidebar_postid' name='sidebar_postid' onchange="nxs_js_popup_sessiondata_make_dirty(); nxs_js_popup_setsessiondata('sidebar_postid', jQuery(this).val()); nxs_js_popup_refresh();">
		            		echo "<option value=''><?php nxs_l18n_e("Suppress sidebar[nxs:popup,ddl,sidebars]", "nxs_td"); ?></option>";
		            		<?php 		    		
		            		// loop over available pages
				    				foreach ($showpages as $currentpost)
				    				{
				    					$posttitle = $currentpost->post_title;
				    					$sidebarid = $currentpost->ID;
				    					if ($sidebar_postid == $sidebarid)
				    					{
				    						$selected = "selected='selected'";
				    					}
				    					else
				    					{
				    						$selected = "";
				    					}	
				    					echo "<option " . $selected . " value='" . $sidebarid . "'>" . $posttitle . "</option>";
				    				}
				    				?>
		            	</select>
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
		      <?php 
			    } else {
			    ?>
			    	<input id='sidebar_postid' name='sidebar_postid' value='<?php echo $sidebar_postid; ?>' type='hidden' />
			    <?php
			  	}
			    ?>
			    
			  </div>
			</div>
      
      <div class="content2">
          <div class="box">
            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Save[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:button]", "nxs_td"); ?></a>
         	</div>
          <div class="nxs-clear">
          </div>
      </div> <!--END content-->
		</div>
	</div>
	
	<script type='text/javascript'>
		
		function nxs_js_saveandopensidebar()
		{
			nxs_js_savegenericpopup_core(function() 
			{
				var url = "<?php echo get_home_url() . "/?nxs_sidebar=" . urlencode(nxs_getslug_for_postid($sidebar_postid)); ?>" + "&nxsrefurlspecial=" + nxs_js_get_nxsrefurlspecial();
				nxs_js_redirect(url);	
			});
		}
		
		function nxs_js_savegenericpopup()
		{
			nxs_js_savegenericpopup_core(function() {
				// close the pop up
				nxs_js_closepopup_unconditionally();
				
				// refresh current page (if the sidebar is updated we could decide to
				// update only the sidebar, but this is needless; an update of the page is ok too)
				nxs_js_redirecttopostid(<?php echo $postid;?>);
			});
		}
		
		function nxs_js_savegenericpopup_core(invokewhenavailable)
		{
			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updatepagetemplatedata",
						"postid": "<?php echo $postid;?>",
						"pagetemplate": "searchresults",
						"updatesectionid": "sidebar",
						"sidebar_postid": jQuery('#sidebar_postid').val()
					},
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							invokewhenavailable();
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
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_pagetemplate_searchresults_footerhome_getsheethtml($args)
{
	//
	extract($args);
			
	$result = array();
	
	$meta = nxs_get_corepostmeta($postid);
	$footer_postid = $meta["footer_postid"];
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	if ($nieuwefooter == "true")
	{
		// creeer nieuwe footer
		// initialiseer nieuwe footer
		// set footer_postid op nieuwe footer
		
		$identifier = "Footer " . nxs_gettimestampasstring();
		
		$args = array();
		$args["slug"] = $identifier;
		$args["titel"] = $identifier;
		$args["nxsposttype"] = "footer";
		$args["postwizard"] = "default_footer";
		$response = nxs_addnewarticle($args);
		$footer_postid = $response["postid"];
	}
	
	$titleofexistingfooter = nxs_gettitle_for_postid($footer_postid);
	$slugofexistingfooter = nxs_getslug_for_postid($footer_postid);
	
	//		
	$publishedargs = array();
	$publishedargs["post_status"] = "publish";
	$publishedargs["post_type"] = "nxs_footer";
	//$publishedargs["orderby"] = $order_by;
	//$publishedargs["order"] = $order;
	$publishedargs["numberposts"] = -1;	// allemaal!
  $showpages = get_posts($publishedargs);
  // apply runtime filters (outside SQL)
  
// add custom filters	
  $filters = array();
  $filters["nxsposttype"] = "footer";
  
  nxs_getfilteredposts($showpages, $filters);  	
	
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	
      
     	<?php nxs_render_popup_header(nxs_l18n__("Set footer[nxs:popup,header]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		
		      <?php if ($footer_postid != "") { ?>
		      
		      <div class="content2">
		        <div class="box">
	            <div class="box-title">
	          		<h4><?php nxs_l18n_e("Footer[nxs:heading]", "nxs_td"); ?></h4>
              </div>
	            <div class="box-content">
	            	<a class="nxsbutton1 nxs-float-right" href="#" onclick="nxs_js_popup_sessiondata_make_dirty(); nxs_js_popup_setsessiondata('footer_postid', ''); nxs_js_popup_refresh();"><?php nxs_l18n_e("Select other footer[nxs:button]", "nxs_td"); ?></a>
	            	<span class='title'><?php echo $titleofexistingfooter; ?></span>
	            	<div class="nxs-clear"></div>
	            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
					<?php } ?>
		
					<?php
					if ($footer_postid == "")
					{
					?>
		
					<div class="content2">
		        <div class="box">
		            <div class="box-title">
		          		<h4><?php nxs_l18n_e("Footer[nxs:heading]", "nxs_td"); ?></h4>
		             </div>
		            <div class="box-content">
		            	<a class="nxsbutton1 nxs-float-right" href="#" onclick="nxs_js_popup_sessiondata_make_dirty(); nxs_js_popup_setshortscopedata('nieuwefooter', 'true'); nxs_js_popup_refresh();"><?php nxs_l18n_e("New footer[nxs:button]", "nxs_td"); ?></a>
		            	
		          		<select id='footer_postid' name='footer_postid' onchange="nxs_js_popup_sessiondata_make_dirty(); nxs_js_popup_setsessiondata('footer_postid', jQuery(this).val()); nxs_js_popup_refresh();">
		            		<option value=''><?php nxs_l18n_e("Suppress footer[nxs:ddl]", "nxs_td"); ?></option>
		            		<?php 		    		
		            		// loop over available pages
				    				foreach ($showpages as $currentpost)
				    				{
				    					$posttitle = $currentpost->post_title;
				    					$footerid = $currentpost->ID;
				    					if ($footer_postid == $footerid)
				    					{
				    						$selected = "selected='selected'";
				    					}
				    					else
				    					{
				    						$selected = "";
				    					}	
				    					echo "<option " . $selected . " value='" . $footerid . "'>" . $posttitle . "</option>";
				    				}
				    				?>
		            	</select>
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
		      <?php 
			    } else {
			    ?>
			    	<input id='footer_postid' name='footer_postid' value='<?php echo $footer_postid; ?>' type='hidden' />
			    <?php
			  	}
			    ?>
			    
		    </div>
		  </div>
		      
      <div class="content2">
        <div class="box">
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Save[nxs:button]", "nxs_td"); ?></a>
          <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:button]", "nxs_td"); ?></a>
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:button]", "nxs_td"); ?></a>
       	</div>
        <div class="nxs-clear">
        </div>
      </div> <!--END content-->
		</div>
	</div>
	
	<script type='text/javascript'>
		
		function nxs_js_saveandopenfooter()
		{
			nxs_js_savegenericpopup_core(function() {
				var url = "<?php echo get_home_url() . "/?nxs_footer=" . urlencode(nxs_getslug_for_postid($footer_postid)); ?>" + "&nxsrefurlspecial=" + nxs_js_get_nxsrefurlspecial();
				nxs_js_redirect(url);
			});
		}
		
		function nxs_js_savegenericpopup()
		{
			nxs_js_savegenericpopup_core(function() {
				// close the pop up
				nxs_js_closepopup_unconditionally();
				
				// refresh current page (if the footer is updated we could decide to
				// update only the footer, but this is needless; an update of the page is ok too)
				nxs_js_redirecttopostid(<?php echo $postid;?>);
			});
		}
		
		function nxs_js_savegenericpopup_core(invokewhenavailable)
		{
			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updatepagetemplatedata",
						"postid": "<?php echo $postid;?>",
						"pagetemplate": "searchresults",
						"updatesectionid": "footer",
						"footer_postid": jQuery('#footer_postid').val()
					},
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							invokewhenavailable();
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
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_pagetemplate_searchresults_dialogappendrow_getsheethtml($args)
{
	//
	extract($args);
			
	$result = array();
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	nxs_ob_start();

	$pagedata = get_page($postid);
	$nxsposttype = nxs_getnxsposttype_by_wpposttype($pagedata->post_type);

	$posttype = $pagedata->post_type;
	$postmeta = nxs_get_corepostmeta($postid);
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

     	<?php nxs_render_popup_header(nxs_l18n__("Add row[nxs:popup,header]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		
					<div class="content2">
		        <div class="box">
		          <div class="box-title" style='width: 400px;'>
	          		<h4><?php nxs_l18n_e("Select the column layout for the new row[nxs:heading]", "nxs_td"); ?></h4>
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
            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Add row[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:button]", "nxs_td"); ?></a>
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
			},
			function()
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				nxs_js_closepopup_unconditionally();
			});
		}
	</script>
	
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

// import data
function nxs_pagetemplate_searchresults_appendstruct_getsheethtml($args)
{
	//
	//
	//
	extract($args);
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	$fileuploadurl = admin_url( 'admin-ajax.php');
	
	nxs_ob_start();

	?>

  <div class="nxs-admin-wrap">
    <div class="block">
     
     	<?php nxs_render_popup_header(nxs_l18n__("Append rows[nxs:popup,header]", "nxs_td")); ?>
      
      <div class="content2">
      
          <form id='nxsuploadform' action="<?php echo $fileuploadurl;?>" method="post" enctype="multipart/form-data">
              <input type="file" name="file" id="file" class="nxs-float-left" onchange="storefile();" />
          </form>		
          <script type="text/javascript">
        	
        		function setupfiletransfer()
        		{
        			var filename = jQuery('#file').val().split(/\\|\//).pop();
        			var options = 
              { 
                data:
                {
                    action: "nxs_ajax_webmethods",
                    webmethod: "importcontent",
                    uploadtitel: filename,
                    import: 'appendpoststructureandwidgets',
                    postid: <?php echo $postid; ?>
                },
                dataType: 'json',
                iframe: true,
                success: processResponse,
            	};
                
        			jQuery('#nxsuploadform').ajaxForm(options);
        		}
        	
            function storefile()
            {           
              // 
              // setup form to support ajax submission (file transfer using html5 features)
              //
              setupfiletransfer();
              

							if (!verifyFileSelected())
              {
                  return;
              }
              
              // submit form
              jQuery("#nxsuploadform").submit(); 
          	}
            
            function verifyFileSelected()
            {
                var f = document.getElementById("file");
                if (f.value == "")
                {
                    alert("Je hebt nog geen digitaal bestand gekozen");
                    return false;
                }
                else
                {
                    return true;
                }
            }

            function processResponse(data, statusText, xhr, $form)  
            {
              if (data.result == "OK")
              {
              	nxs_js_alert('<?php nxs_l18n_e("Data was updated. Please refresh the page to see the result[nxs:growl]", "nxs_td"); ?>');
              	nxs_js_log(data.message);
              }
              else
              {
                alert("Er is een fout opgetreden bij het uploaden van het document");
              }
            }
            
          </script>
      </div> <!--END content-->
      <div class="content2">
          <div class="box">
            <a href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_popup_navigateto("home"); return false;'><?php nxs_l18n_e("Cancel[nxs:button]", "nxs_td"); ?></a>
         </div>
          <div class="nxs-clear margin"></div>
      </div> <!--END content-->
    </div> <!--END block-->
  </div>
    
  <script type='text/javascript'>
		function nxs_js_execute_after_popup_shows()
		{
			
		}
	</script>
    
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

// export data
function nxs_pagetemplate_searchresults_exportstruct_getsheethtml($args)
{
	//
	//
	//
	extract($args);
	
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	$filedownloadurl = admin_url('admin-ajax.php?action=nxs_ajax_webmethods&webmethod=exportcontent&export=poststructureandwidgets&postid=' . $postid);
	
	nxs_ob_start();

	?>

  <div class="nxs-admin-wrap">
    <div class="block">
     
     	<?php nxs_render_popup_header(nxs_l18n__("Export page[nxs:popup,header]", "nxs_td")); ?>
      
      <div class="content2">
      	<a href='<?php echo $filedownloadurl;?>'>Download</a>
      </div> <!--END content-->
      <div class="content2">
          <div class="box">
            <a href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_popup_navigateto("home"); return false;'><?php nxs_l18n_e("Cancel[nxs:button]", "nxs_td"); ?></a>
         </div>
          <div class="nxs-clear margin"></div>
      </div> <!--END content-->
    </div> <!--END block-->
  </div>
    
  <script type='text/javascript'>
		function nxs_js_execute_after_popup_shows()
		{
			
		}
	</script>
    
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

//
// wordt aangeroepen bij het opslaan van data
//
function nxs_pagetemplate_searchresults_updatedata($args)
{
	extract($args);
	
	nxs_disabledwprevisions();
	
	$wpposttype = nxs_getwpposttype($postid);
	if ($wpposttype == "post")
	{
		$r = nxs_converttopage($postid);
		$wpposttype = nxs_getwpposttype($postid);
	}
	
	// 
		
	if ($updatesectionid == "home" || $updatesectionid == "")
	{	
		$modifiedmetadata = array();

		// update homepage
		if ($markasserppage != "")
		{
			nxs_setserppage($postid);
		}
		
		// update 404
		if ($markas404page != "")
		{
			nxs_set404page($postid);
		}

		//
		// update title, slug and categories
		//

		$modifiedmetadata["titel"] = $titel;
		$modifiedmetadata["slug"] = $slug;
		$modifiedmetadata["selectedcategoryids"] = $selectedcategoryids;

		$newcats = array();
		$splitted = explode("[", $selectedcategoryids);
		foreach($splitted as $splittedpiece)
		{
			// bijv. "1]"
			if ($splittedpiece == "")
			{
				// ignore
			}
			else
			{
				// bijv. "1]"
				$newcats[] = substr($splittedpiece, 0, -1);
			}			
		}

		// Update the post into the database
		$my_post = array();
		$my_post['ID'] = $postid;
		$my_post['post_title'] = $titel;
		$my_post['post_name'] = $slug;
		wp_update_post($my_post);
		
		// Update categories
		wp_set_post_categories($postid, $newcats);	
		
		// update meta data page
		$modifiedmetadata = array();
		$modifiedmetadata["pagetemplate"] = $pagetemplate;
		
		// persist values
		nxs_merge_postmeta($postid, $modifiedmetadata);
	}
	
	if ($updatesectionid == "edittitle" || $updatesectionid == "")
	{
		$modifiedmetadata = array();

		$modifiedmetadata["titel"] = $titel;
		
		// update title, slug and categories
		$my_post = array();
		$my_post['ID'] = $postid;
		$my_post['post_title'] = $titel;

		wp_update_post($my_post);
		
		// persist values
		nxs_merge_postmeta($postid, $modifiedmetadata);
	}
	
	if ($updatesectionid == "header" || $updatesectionid == "")
	{
		$modifiedmetadata = array();
		
		$modifiedmetadata["header_postid"] = $header_postid;
		$modifiedmetadata['header_postid_globalid'] = nxs_get_globalid($header_postid, true);	// global referentie

		// persist values
		nxs_merge_postmeta($postid, $modifiedmetadata);
	}
	
	if ($updatesectionid == "sidebar" || $updatesectionid == "")
	{
		$modifiedmetadata = array();

		$modifiedmetadata["sidebar_postid"] = $sidebar_postid;
		$modifiedmetadata['sidebar_postid_globalid'] = nxs_get_globalid($sidebar_postid, true);	// global referentie

		// persist values
		nxs_merge_postmeta($postid, $modifiedmetadata);
	}	

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>