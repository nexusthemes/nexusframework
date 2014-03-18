<?php

function nxs_gallerybox_detail_rendersheet($args)
{
	//
	extract($args);
	
	extract($clientpopupsessiondata);
	extract($clientpopupsessioncontext);
	extract($clientshortscopedata);
	
	$index = intval($index);

	$structure = nxs_parsepoststructure($galleryid);
	
	if ($galleryaction == "next")
	{
		//echo $index;
		$index = $index + 1;
		$maxcount = count($structure);
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
			if (count($structure) > 0)
			{
				$index = count($structure) - 1;
			} 
			else
			{
				$index = 0;
			}
		}
	}
	
	$numofitems = count($structure);
	
	$pagerow = $structure[$index];
	
	$rowcontent = $pagerow["content"];
	$placeholderid = nxs_parsepagerow($rowcontent);
	$placeholdermetadata = nxs_getwidgetmetadata($galleryid, $placeholderid);
	$placeholdertype = $placeholdermetadata["type"];
	
	if ($placeholdertype == "galleryitem")
	{
		$imageid = $placeholdermetadata['image_imageid'];
	}
	else
	{
		$imageid = -1;
	}
	
	$lookup = wp_get_attachment_image_src($imageid, 'full', true);
	$fullimageurl = $lookup[0];

	$result = array();
	
	ob_start();
	
	// dit rendert de popup van een individuele gallery slide (detail)
	
	?>
	
	<div class="nxs-table" style='opacity: 0'>
		<div class="nxs-table-cell">
			<img id='galleryimg' class='nxs-gallery-image' src="<?php echo $fullimageurl; ?>" />
			<ul class="icon-font-list nxs-center">
				<li>
					<a href='#' onclick="nxs_js_gal_left(); return false;">
						<span class="nxs-icon-arrow-left"></span>
					</a>
				</li>
				<!-- todo: add social media buttons here -->			
				<!-- next -->
				<li>
					<a href='#' onclick="nxs_js_gal_right(); return false;">
						<span class="nxs-icon-arrow-right"></span>
					</a>
				</li>
			</ul>
		</div>
	</div>
	
	<script type='text/javascript'>
		
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
			jQuery('#TB_window').addClass("nxs-gallerypopup");
			
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
			jQuery('#TB_window').removeClass("nxs-gallerypopup");
			
			jQuery(document).unbind("keydown.nxsgallerynav");
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

?>