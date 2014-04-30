<?php	
	global $post;
	$postid = $post->ID;

	// process meta data
	$meta = nxs_get_postmeta($postid);
	
	//
	//
	//
	global $nxs_global_extendrootclass;
	$nxs_global_extendrootclass = "nxs-front-end";
	
	nxs_getheader("header");
	
	$page_title = get_the_title();
	?>

	<div style="background-color: white;">
  	<div class="nxs-aligncenter960 nxs-admin-wrap" style='position:static;'>
		  <div id="wrap-header" style="padding-top: 30px;">
		    <h2><span class="nxs-icon-subfooter"></span><?php nxs_l18n_e("Subfooter[nxs:heading]", "nxs_td"); ?>: <?php echo $page_title; ?></h2>
		  </div>
		  <div>
		  	<a class='nxsbutton1 nxs-float-left' title="<?php nxs_l18n_e("Edit title[nxs:post,tooltip]", "nxs_td"); ?>" href="#" onclick="nxs_js_popup_page_neweditsession('<?php echo $postid;?>', 'edittitle'); return false;"><?php nxs_l18n_e("Edit title[nxs:newrow,button]", "nxs_td"); ?></li></a>
				<div class='nxs-clear'></div>
			</div>
		</div>
		<div class='padding'></div>
	</div>

	<div id="nxs-container">
		<div>
	    <div class="DISABLED_block">
	      <div class="nxs-clear"></div>
	      
	      <?php
				$iswidescreen = nxs_iswidescreen("content");
				if ($iswidescreen)
				{
					$widescreenclass = "nxs-widescreen";
				}
				else
				{
					$widescreenclass = "";
				}
				?>
	      				
				<div id="nxs-content" class="nxs-containshovermenu1 nxs-sitewide-element <?php echo $widescreenclass; ?>">
					<div class='nxs-subfooter-container nxs-elements-container nxs-layout-editable nxs-widgets-editable nxs-post-<?php echo $postid; ?>'>
						<?php 
		          // render the actual page contents (page rows)
		          if ($_REQUEST["containerpostid"] == "")
		          {
		          	$containerpostid = $postid;
		          }
		          else
		          {
		          	$containerpostid = $_REQUEST["containerpostid"];
		          }
		          echo nxs_getrenderedhtmlincontainer($containerpostid, $postid, "default");
		          //echo nxs_getrenderedhtml($postid, "default");
		        ?>
		        <div class="nxs-clear"></div>
					</div>			
				</div>
			</div>
		</div>
	</div> <!-- END content -->

	<div style='background-color: white;'>
  	<div class='nxs-aligncenter960 nxs-admin-wrap' style='position:static;'>
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