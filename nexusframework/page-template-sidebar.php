<?php	
	global $post;
	$postid = $post->ID;

	// process meta data
	$meta = nxs_get_corepostmeta($postid);
	
	//
	//
	//
	nxs_getheader("sidebar");
	
	$page_title = get_the_title();
	
	?>
	
	<div style="background-color: white;">
  	<div class="nxs-aligncenter338 nxs-admin-wrap" style="position: static;">
		  <div id="wrap-header" style="padding-top: 30px;">
		    <h2><span class="nxs-icon-sidebar"></span><?php nxs_l18n_e("Sidebar[nxs:heading]", "nxs_td"); ?>: <?php echo $page_title; ?></h2>
		  </div>
		  <div>
		  	<a class='nxsbutton1 nxs-float-left' title="<?php nxs_l18n_e("Edit title[nxs:post,tooltip]", "nxs_td"); ?>" href="#" onclick="nxs_js_popup_page_neweditsession('<?php echo $postid;?>', 'edittitle'); return false;"><?php nxs_l18n_e("Edit title[nxs:newrow,button]", "nxs_td"); ?></li></a>
				<div class='nxs-clear'></div>
			</div>
		</div>
		<div class='padding'></div>
	</div>

	<div id="nxs-sidebaredit-container">
		<!-- note, the widescreen setting of the sidebar is not configurable (its an admin page) -->
		<div id="nxs-content" class="nxs-containsimmediatehovermenu nxs-sitewide-element">
			
  		<div class='nxs-sidebar-container nxs-elements-container nxs-sidebar1 nxs-post-<?php echo $postid;?>'>

				<div class="DISABLED_block">
		      <div class="nxs-clear"></div>						
					<div id="nxs-sidebar" class="nxs-containshovermenu1">
						<div class='nxs-sidebar-container nxs-elements-container nxs-layout-editable nxs-widgets-editable nxs-post-<?php echo $postid; ?>'>
							<?php 
			          // render the actual page contents (page rows)
			          echo nxs_getrenderedhtml($postid, "default");
			        ?>
			        <div class="nxs-clear"></div>
						</div>			
					</div>
				</div>
				
			</div>
			<div class="nxs-clear"></div>
			
		</div>
	</div> <!-- END content -->	
	
	<div style='background-color: white;'>
  	<div class='nxs-aligncenter338 nxs-admin-wrap' style="position: static;">
  		<div class='padding'></div>
  		<!-- knoppen -->
			<?php nxs_render_backbutton(); ?>
			<a href="#" onclick="nieuwmenuitem(this); return false;" class="nxsbutton1"><?php nxs_l18n_e("Add row[nxs:newrow,button]", "nxs_td"); ?></a>
			<div style='padding-bottom: 20px; overflow: hidden;'>&nbsp;</div>
  	</div>
  </div>
	

	<script>

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
			
			nxs_js_addnewrowwithtemplate('<?php echo $postid; ?>', insertafterindex, "one", "undefined", e, 
			function()
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
			},
			function()
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
			}
			);			
			// 
		}
	</script>	

	<?php
				
	nxs_getfooter("admin"); 
?>