<?php	
	global $post;
	$postid = $post->ID;

	// process meta data
	$meta = nxs_get_postmeta($postid);
	
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
          <h3>Menu elementen</h3>
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
			<?php if ($_REQUEST["urlencbase64referringurl"] != "") { ?>
			<a href='<?php echo base64_decode(urldecode($_REQUEST["urlencbase64referringurl"])); ?>' class='nxsbutton nxs-float-right'><?php nxs_l18n_e("OK[nxs:newrow,button]", "nxs_td"); ?></a>
			<?php } ?>

			<a href="#" onclick="nieuwmenuitem(this); return false;" class="nxsbutton1"><?php nxs_l18n_e("Add item[nxs:newrow,button]", "nxs_td"); ?></a>
		</div>
	</div> <!-- END content -->
	
	<script type='text/javascript'>
		
		function nieuwmenuitem(element)
		{
			var e = jQuery(".nxs-layout-editable .nxs-postrows")[0];
			
			var waitgrowltoken = nxs_js_alert_wait_start("<?php nxs_l18n_e("Adding item[nxs:newrow,button]", "nxs_td"); ?>");
			
			var totalrows = jQuery(document).find(".nxs-row").length;
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
	</script>	
	
	<?php
				
	nxs_getfooter("admin"); 
?>