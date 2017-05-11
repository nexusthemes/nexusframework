<?php	
	global $post;
	$postid = $post->ID;

	// process meta data
	$meta = nxs_get_corepostmeta($postid);
	
	//
	//
	//
	nxs_getheader("menu");
	
	$page_title = get_the_title();
	
	?>
	
	<div id="wrap-header">
    <h2><span class="nxs-icon-menucontainer"></span><?php nxs_l18n_e("Menu[nxs:heading]", "nxs_td"); ?>: <?php echo $page_title; ?></h2>
  </div>

	<div>
		<a class='nxsbutton1 nxs-float-left' title="<?php nxs_l18n_e("Edit title[nxs:post,tooltip]", "nxs_td"); ?>" href="#" onclick="nxs_js_popup_page_neweditsession('<?php echo $postid;?>', 'edittitle'); return false;"><?php nxs_l18n_e("Edit title[nxs:newrow,button]", "nxs_td"); ?></li></a>
		<div class='nxs-clear'></div>
	</div>

	<div class='padding'>
    <div class="block">
      <div class="nxs-admin-header">
          <h3>Menu items</h3>
          <div class="nxs-clear"></div>
      </div>
      <div class="nxs-clear"></div>		
	
			<div class='nxs-menu-container nxs-elements-container nxs-layout-editable nxs-widgets-editable nxs-post-<?php echo $postid; ?>'>
				<?php 
          // render the actual page contents (page rows)
          echo nxs_getrenderedhtml($postid, "default");
        ?>
        <div class="nxs-clear"></div>
			</div>			
		</div>
				
    <div>			
			<?php nxs_render_backbutton(); ?>
			<a href="#" onclick="nxs_js_nieuwmenuitem_v2(this); return false;" class="nxsbutton1"><?php nxs_l18n_e("Add item[nxs:newrow,button]", "nxs_td"); ?></a>
		</div>
	</div> <!-- END content -->
	
	<script type='text/javascript'>
		
		function nieuwmenuitem(element)
		{
			var e = jQ_nxs(".nxs-layout-editable .nxs-postrows")[0];
			
			var waitgrowltoken = nxs_js_alert_wait_start("<?php nxs_l18n_e("Adding item[nxs:newrow,button]", "nxs_td"); ?>");
			
			var totalrows = jQ_nxs(document).find(".nxs-row").length;
			nxs_js_log("totalrows:" + totalrows);
			
			var insertafterindex;
			insertafterindex = totalrows - 1;
			
			nxs_js_log("inserting after index:" + insertafterindex);
			
			// voeg een "one" row toe
			
			nxs_js_addnewrowwithtemplate('<?php echo $postid; ?>', insertafterindex, "one", "menuitemarticle", e, 
			function()
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
			},
			function()
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
			});
			
			// 
		}
		
		function nxs_js_nieuwmenuitem_v2(element)
		{
			<?php
			$widgetargs = array("nxsposttype"=>"menu");
			$filterobsoletewidgets = true;
			$widgets = nxs_getwidgets_v2($widgetargs, $filterobsoletewidgets);
			?>
			
			// step 1; show popup asking user to select item type (article ref, custom ref, category ref, ...)
			
			var html = "";
			html += "<div class=\"box nxs-linkcolorvar-base2-m\">";
			html += "<ul class=\"placeholder3 nxs-applylinkvarcolor\">";

			<?php
				// for each placeholder -->
				foreach ($widgets as $currentwidget)
				{
					$title = $currentwidget["title"];
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
					?>
					
					html += "<a href=\"#\" data-widgetid=\"<?php echo $widgetid; ?>\" onclick=\"nxs_js_selectplaceholdertype(this);  return false;\">";
					html += "<li>";
												
					<?php
					if (isset($iconid) && $iconid != "")
					{
						?>
						
						html += "<span class=\"nxs-widget-icon <?php echo $iconid; ?>\"></span>";
						html += "<p title=\"<?php echo $title; ?>\"><?php echo $abbreviatedtitle; ?></p>";
						
						<?php
					}
					else
					{
						$iconid = nxs_getplaceholdericonid($widgetid);
						?>
						
						html += "<span id=\"placeholdertemplate_<?php echo $widgetid; ?>\" class=\"<?php echo $iconid; ?>\"></span>";
						html += "<p title=\"<?php echo $title; ?>\"><?php echo $abbreviatedtitle; ?></p>";
						
						<?php
					}
					?>
					
					html += "</li>";
					html += "</a>";
					
					<?php
				}
			?>
			
			html += "</ul>";
			html += "</div>";
			html += "<div class=\"nxs-clear\"></div>";
			
			// ---------------------
			
			nxs_js_htmldialogmessageok_v2("Select the item you want to add", html, "basic");
			
			// 
		}
		
		function nxs_js_selectplaceholdertype(ref)
		{
			var menuitemtype = jQ_nxs(ref).data("widgetid");
			var e = jQ_nxs(".nxs-layout-editable .nxs-postrows")[0];
			var waitgrowltoken = nxs_js_alert_wait_start("<?php nxs_l18n_e("Adding item[nxs:newrow,button]", "nxs_td"); ?>");
			var totalrows = jQ_nxs(document).find(".nxs-row").length;
			nxs_js_log("totalrows:" + totalrows);
			var insertafterindex;
			insertafterindex = totalrows - 1;
			nxs_js_log("inserting after index:" + insertafterindex);
			// voeg een "one" row toe
			nxs_js_addnewrowwithtemplate
			(
				'<?php echo $postid; ?>', 
				insertafterindex, 
				"one", 
				menuitemtype, 
				e, 
				function(insertresponse, renderresponse)
				{
					// happy flow
					nxs_js_alert_wait_finish(waitgrowltoken);
					
					var postid = insertresponse.postid;
					var placeholderids = insertresponse.placeholderids;
					// here there should always be just one
					var placeholderid = placeholderids[0];
					var rowindex = insertresponse.rowindex;

					// open new popup for the placeholderid that was just added
					nxs_js_popup_placeholder_neweditsession(postid, placeholderid, rowindex, "home"); 
				},
				function()
				{
					// alt flow
					nxs_js_alert_wait_finish(waitgrowltoken);
				}
			);
		}
	</script>	
	
	<?php
				
	nxs_getfooter("admin"); 
?>