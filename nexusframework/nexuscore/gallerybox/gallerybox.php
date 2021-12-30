<?php

function nxs_gallerybox_detail_rendersheet($args)
{
	//
	extract($args);
	
	if ($clientpopupsessiondata != null) { extract($clientpopupsessiondata); }
	if ($clientpopupsessioncontext != null) { if ($clientpopupsessioncontext != null) { extract($clientpopupsessioncontext); } }
	if ($clientshortscopedata != null) { extract($clientshortscopedata); }
	
	global $nxs_global_current_containerpostid_being_rendered;
	$nxs_global_current_containerpostid_being_rendered = $containerpostid;
	
	$index = intval($index);

	$structure = nxs_parsepoststructure($galleryid);
	if ($items_data != "")
	{
		$moreitems = explode("|", $items_data);
	}
	else
	{
		$moreitems = array();
	}
	
	if ($galleryaction == "next")
	{
		//echo $index;
		$index = $index + 1;
		$maxcount = count($structure) + count($moreitems);
		//echo "/";
		//echo $maxcount;
		if ($index >= $maxcount)
		{
			$index = 0;
		}
	}
	else if ($galleryaction == "prev")
	{
		$index = $index - 1;
		if ($index < 0)
		{
			if ((count($structure) + count($moreitems)) > 0)
			{
				$index = count($structure) + count($moreitems) - 1;
			} 
			else
			{
				$index = 0;
			}
		}
	}
	
	$numofitems = count($structure) + count($moreitems);
	
	if (isset($structure[$index]))
	{
		$pagerow = $structure[$index];
		$rowcontent = $pagerow["content"];
		$placeholderid = nxs_parsepagerow($rowcontent);
		$placeholdermetadata = nxs_getwidgetmetadata($galleryid, $placeholderid);
		$placeholdertype = $placeholdermetadata["type"];
		
		nxs_requirewidget($placeholdertype);
		$detailhtml = nxs_function_invokefunction("nxs_widgets_{$placeholdertype}_getgallerydetailhtml", $placeholdermetadata);
	}
	else
	{
		$image_imageid = $moreitems[$index - count($structure)];
		
		$lookup = nxs_wp_get_attachment_image_src($image_imageid, 'full', true);
		$fullimageurl = $lookup[0];
		$fullimageurl = nxs_img_getimageurlthemeversion($fullimageurl);
		
		$placeholdertype = "galleryitem";
		nxs_requirewidget($placeholdertype);
		$a = array
		(
			"image_imageid" => $moreitems[$index - count($structure)],
			"fullimageurl" => $fullimageurl,
		);
		$detailhtml = nxs_function_invokefunction("nxs_widgets_{$placeholdertype}_getgallerydetailhtml", $a);
	}

	$result = array();
	
	nxs_ob_start();
	
	// dit rendert de popup van een individuele gallery slide (detail)
	
	?>
	
	<div class="nxs-table" style='opacity: 0'>
		<div class="nxs-table-cell">
			<?php echo $detailhtml; ?>
      <ul class="icon-font-list nxs-center">
				<li>
					<a href='#' onclick="nxs_js_gal_left(); return false;">
						<span class="nxs-icon-arrow-left"></span>
					</a>
				</li>
        <li></li>
				<!-- todo: add social media buttons here -->			
				<!-- next -->
        <li>
	        <a class="nxs-popupcloser" href='#' onclick="nxs_js_closepopup_unconditionally_if_not_dirty(); return false;">
	        	<span class="nxs-icon-close"></span>
	        </a>
				</li>
        <li></li>
				<li>
					<a href='#' onclick="nxs_js_gal_right(); return false;">
						<span class="nxs-icon-arrow-right"></span>
					</a>
				</li>
			</ul>
		</div>
	</div>
	
	<script>
		
		function nxs_js_gal_left()
		{
			nxs_js_popup_setshortscopedata('galleryaction', 'prev');
			nxs_js_popup_refresh_v2(false);
		}
		
		function nxs_js_gal_right()
		{
			nxs_js_popup_setshortscopedata('galleryaction', 'next');
			nxs_js_popup_refresh_v2(false);
		}

		function nxs_js_execute_after_popup_shows()
		{
			// do something useful here, for example make the lightbox to fit the screen's dimensions?
			nxs_js_popup_setsessioncontext('index', '<?php echo $index; ?>');
			//nxs_js_alert('this popup shows when the gallery lightbox is opened');
			//nxs_js_alert('<?php echo $index;?> / <?php echo $galleryid;?>');
			
			jQuery('#galleryimg').click
			(
				function() 
				{
					// if user clicks on the image, close the popup
	      	nxs_js_closepopup_unconditionally_if_not_dirty();
	    	}
    	);
    	
    	jQuery('#galleryimg').load
    	(
    		function()
    		{
    			
    		}
    	)
    	
    	//			
			jQuery('#nxsbox_window').addClass("nxs-gallerypopup");
			
			// navigation using left/right arrow
			jQuery(document).unbind("keydown.nxsgallerynav");
			jQuery(document).bind("keydown.nxsgallerynav", function(e)
	  	{
	  		if (e.keyCode == 37) 
	  		{ 
	  			// links
	  			nxs_js_gal_left();
	     		return false;
	     	}
	     	else if (e.keyCode == 39 || e.keyCode == 32) 
	  		{ 
	  			// rechts of spatie
	  			nxs_js_gal_right();
	     		return false;
	     	}
	    });    
		}
		
		function nxs_js_execute_before_popup_closes()
		{
			jQuery('#nxsbox_window').removeClass("nxs-gallerypopup");
			
			jQuery(document).unbind("keydown.nxsgallerynav");
		}
		
		// overriden
		function nxs_js_showwarning_when_trying_to_close_dirty_popup()
		{
			return false;
		}
		
		
		
	</script>	
	
	<?php
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

?>
