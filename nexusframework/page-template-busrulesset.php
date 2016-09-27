<?php	
	global $post;
	$postid = $post->ID;
	
	
	// process meta data
	$meta = nxs_get_postmeta($postid);
	$nxssubposttype = nxs_get_nxssubposttype($postid);
	//
	//
	//
	nxs_getheader("genericlist");

	$page_title = get_the_title();
	
	//
	
	$state = nxs_warranty_getwarrantystate();
	if ($state == "")
	{
		$homeurl = nxs_geturl_home();
		nxs_ob_start();
		?>
		<div>
			Please read carefully before proceeding.<br />
			<br />
			The functionality on this page is meant to be configured by resellers only.<br />
			It is not intended to be used by end users.<br />
			<br />
			Should you decide to proceed, you will void your warrantee / license.<br />
			<br />
			<a href=\'<?php echo $homeurl; ?>\' class=\'nxsbutton1\'>Back to safety</a>
			<a target=\'_blank\' href=\'https://nexusthemes.com/support/nexus-themes-implementation-partners/\' class=\'nxsbutton1\'>Find a reseller</a>
			<a href=\'#\' class=\'nxsbutton2\' onclick=\'nxs_js_voidwarrantee(); return false;\'>Proceed; void my warrantee</a>
		</div>		
		<?php
		$html = nxs_ob_get_contents();
		// remove line breaks
		$html = str_replace("\n", "", $html);
		$html = str_replace("\r", "", $html);
		nxs_ob_end_clean();
		?>
		<script>
			function nxs_js_voidwarrantee()
			{
				var r = confirm("Are you sure?");
				if (r == true) 
				{
					// todo: invoke a ajax call to void the warrantee
					
					jQuery(document).unbind('nxs_event_popup_closeunconditionally');
					nxs_js_closepopup_unconditionally();
					nxs_js_alert_veryshort("Warrantee voided...");
				} 
				else 
				{
				  //x = "You pressed Cancel!";
				}
			}
			jQ_nxs(window).bind("load", function() 
			{
				var html = '<?php echo $html; ?>';
				nxs_js_htmldialogmessageok("<span class='nxs-icon-notification'></span>Caution !", html);
				jQuery(document).bind
				(
					'nxs_event_popup_closeunconditionally', 
					function() 
					{
						// perhaps redirect ?
						nxs_js_redirect('<?php echo $homeurl; ?>');
					}
				);
			});
		</script>	
		<?php
	}
	
	?>
	
  <div id="wrap-header">
    <h2><span class="nxs-icon-busrulesset-<?php echo $nxssubposttype; ?>"></span><?php 
    	if ($page_title == "")
    	{
    		nxs_l18n_e("Generic list[nxs:heading]", "nxs_td");
    	}
    	else
    	{
    		echo $page_title;
    	}
    	?></h2>
  </div>
  <div>
			<a class="nxsbutton1 nxs-float-left clear" href="#" onclick="nxs_js_popup_page_neweditsession('<?php echo $postid;?>', 'dialogappendbusrulessetitem'); return false;"><?php nxs_l18n_e("Add[nxs:heading]", "nxs_td"); ?></a>
   
  	<div class='nxs-clear'></div>
  </div>  
  <div class="nxs-clear">&nbsp;</div>
  
	<!-- note, the widescreen setting of the busrulesset page is not configurable (its an admin page) -->
	<div id="nxs-content" class="nxs-containsimmediatehovermenu nxs-sitewide-element">
    <div class="block">
    	<!--
      <div class="nxs-admin-header">
          <h3 class="nxs-width20 nxs-float-left"><?php nxs_l18n_e("Image[nxs:heading]", "nxs_td"); ?></h3>
	        <h3 class="nxs-width30 nxs-float-left"><?php nxs_l18n_e("Title[nxs:heading]", "nxs_td"); ?></h3>
	        <h3 class="nxs-width50 nxs-float-left"><?php nxs_l18n_e("Text[nxs:heading]", "nxs_td"); ?></h3>
	        <div class="nxs-clear"></div>
      </div>      
      <div class="nxs-clear"></div>
      -->
	
			<div class='nxs-busrulesset-container nxs-elements-container nxs-layout-editable nxs-widgets-editable nxs-post-<?php echo $postid; ?>'>
				<?php 
	        // render the actual page contents (page rows)
	        echo nxs_getrenderedhtml($postid, "default");
				?>
        <div class="nxs-clear"></div>
			</div>			
					
		</div>
		
	  <div class="content2">
			<div class="box">
				<?php nxs_render_backbutton(); ?>
			</div>
			<div class="nxs-clear margin"></div>
		</div>
	</div>

	<?php
	nxs_getfooter("admin"); 
?>