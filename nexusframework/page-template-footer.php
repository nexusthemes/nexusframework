<?php	
	global $post;
	$postid = $post->ID;

	// process meta data
	$meta = nxs_get_postmeta($postid);
	
	//
	//
	//
	nxs_getheader("footer");
	
	$page_title = get_the_title();
	
	?>

	<div style="background-color: white;">
  	<div class="nxs-aligncenter960 nxs-admin-wrap" style="position: static;">
		  <div id="wrap-header" style="padding-top: 30px;">
		    <h2><span class="nxs-icon-footer"></span><?php nxs_l18n_e("Footer[nxs:heading]", "nxs_td"); ?>: <?php echo $page_title; ?></h2>
		  </div>
		  <div>
		  	<a class='nxsbutton1 nxs-float-left' title="<?php nxs_l18n_e("Edit title[nxs:post,tooltip]", "nxs_td"); ?>" href="#" onclick="nxs_js_popup_page_neweditsession('<?php echo $postid;?>', 'edittitle'); return false;"><?php nxs_l18n_e("Edit title[nxs:newrow,button]", "nxs_td"); ?></li></a>
				<div class='nxs-clear'></div>
			</div>
		</div>
		<div class='padding'></div>
	</div>	

	<?php
	$iswidescreen = nxs_iswidescreen("footer");
	if ($iswidescreen)
	{
		$widescreenclass = "nxs-widescreen";
	}
	else
	{
		$widescreenclass = "";
	}
	?>

	<div id="nxs-container">
		<div>
	    <div class="DISABLED_block">
	      <div class="nxs-clear"></div>						
	      <div id="nxs-footer" class="nxs-containshovermenu1 nxs-sitewide-element <?php echo $widescreenclass; ?>">
					<div id='nxs-footer-container'  class="nxs-sitewide-container nxs-footer-container nxs-elements-container nxs-layout-editable nxs-widgets-editable nxs-containshovermenu1 nxs-post-<?php echo $postid;?>">
						<?php 
		          // render the actual page contents (page rows)
		          echo nxs_getrenderedhtml($postid, "default");
		        ?>
		        <div class="nxs-clear"></div>
					</div>			
				</div>
			</div>
		</div>
	</div> <!-- END content -->

	<div style='background-color: white;'>
  	<div class='nxs-aligncenter960 nxs-admin-wrap' style="position: static;">
  		<div class='padding'></div>
  		<!-- knoppen -->
				<?php nxs_render_backbutton(); ?>
				<a class="nxsbutton1 nxs-float-left" href="#" onclick="nxs_js_popup_page_neweditsession('<?php echo $postid;?>', 'dialogappendrow'); return false;"><?php nxs_l18n_e("Add row[nxs:newrow,button]", "nxs_td"); ?></a>
			<div style='padding-bottom: 20px; overflow: hidden;'>&nbsp;</div>
  	</div>
  </div>
	<?php
	nxs_getfooter("admin"); 
?>