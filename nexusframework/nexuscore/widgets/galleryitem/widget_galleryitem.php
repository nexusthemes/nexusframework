<?php

function nxs_widgets_galleryitem_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-image"; // . $widget_name;
}

function nxs_widgets_galleryitem_gettitle() {
	return nxs_l18n__("Gallery item", "nxs_td");
}

function nxs_widgets_galleryitem_render_webpart_render_htmlvisualization($args) {
	
	//
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	$mixedattributes = array_merge($temp_array, $args);
	
	// Localize atts
	$mixedattributes = nxs_localization_localize($mixedattributes);
	
	$image_imageid = $mixedattributes['image_imageid'];
	$title = $mixedattributes['title'];
	$text = $mixedattributes['text'];
	$destination_articleid = $mixedattributes['destination_articleid'];
	
	$lookup = wp_get_attachment_image_src($image_imageid, 'full', true);
	
	$width = $lookup[1];
	$height = $lookup[2];		
	
	$lookup = wp_get_attachment_image_src($image_imageid, 'thumbnail', true);
	$url = $lookup[0];
	$url = nxs_img_getimageurlthemeversion($url);

	global $nxs_global_placeholder_render_statebag;

	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;
	$hovermenuargs["enable_deletewidget"] = false;
	$hovermenuargs["enable_deleterow"] = true;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs); 
		
	//
	// render actual control / html
	//
	
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-galleryitemr-item";
	
	?>
	<div class="nxs-dragrow-handler nxs-padding-menu-item">
	    <div class="content2">
	        <div class="box-content nxs-width20 nxs-float-left">
	        	<div class='fixed-image-container'>
	        		<img src="<?php echo $url; ?>" />
	        	</div>
	        	<p>
	        		<?php echo sprintf(nxs_l18n__("Dimensions %s px h:%s px[nxs:span]", "nxs_td"), $width, $height); ?>
	        	</p>
	        </div>
	        <div class="box-content nxs-width30 nxs-float-left"><?php echo nxs_render_html_escape_gtlt($title); ?></div>
	        <div class="box-content nxs-width50 nxs-float-left"><?php echo nxs_render_html_escape_gtlt($text); ?></div>
	        <div class="nxs-clear"></div>
	    </div> <!--END content-->
	</div>
	
	<!-- -->
	
	<?php 
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}


// Define the properties of this widget
function nxs_widgets_galleryitem_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_galleryitem_gettitle(),
		"sheeticonid" => nxs_widgets_galleryitem_geticonid(),
	
		"fields" => array
		(
			// GENERAL			
			
			array( 
				"id" 				=> "image_imageid",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Choose image", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to upload an image for your bio profile use this option.", "nxs_td"),
				"localizablefield"	=> true
			),
			array
			( 
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" => nxs_l18n__("Title goes here", "nxs_td"),
				"localizablefield"	=> true
			),
			array
			( 
				"id" 				=> "text",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Text", "nxs_td"),
				"placeholder" => nxs_l18n__("Text goes here", "nxs_td"),
				"rows" => 4,
				"localizablefield"	=> true
			),

			
			
		)
	);
	
	//nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

function nxs_widgets_galleryitem_initplaceholderdata($args)
{
	extract($args);

	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

// invoked by gallerybox contextprocessor
function nxs_widgets_galleryitem_getfullsizeurl($placeholdermetadata)
{
	$imageid = $placeholdermetadata['image_imageid'];
	$lookup = wp_get_attachment_image_src($imageid, 'full', true);
	$fullimageurl = $lookup[0];
	$fullimageurl = nxs_img_getimageurlthemeversion($fullimageurl);

	return $fullimageurl;
}

function nxs_widgets_galleryitem_renderingallery($args)
{
	extract($args);
		
	// hardcoded implementation... to be moved to the galleryitem widget eventually ...
	$imageid = $placeholdermetadata['image_imageid'];
	if ($orientation == "landscape"  || $orientation == "") 
	{
		$lookup = wp_get_attachment_image_src($imageid, 'nxs_cropped_320x200', true);
	} 
	else if ($orientation == "portrait") 
	{
		$lookup = wp_get_attachment_image_src($imageid, 'nxs_cropped_320x512', true);	
	}
	
	$thumbimageurl = $lookup[0];
	$thumbimageurl = nxs_img_getimageurlthemeversion($thumbimageurl);
	
	$item_title = $placeholdermetadata["title"];
	$item_text = $placeholdermetadata["text"];
	
	// Default modulo to separate lines of items when content height is variable
	if ($index % $numofcolumns == 0 && $index != 0) {
	   echo '<div class="nxs-clear"></div>';
	}
	
	// Exceptional modulo for two step separation with four column gallery
	if ($index % 2 == 0 && $index != 0) {
	   echo '<div class="nxs-clear multi-step-divider"></div>';
	}
	
	$firstinrow = '';
	//
	if 		($index % 2 == 0 && $numofcolumns == "2") { $firstinrow = 'firstinrow'; }
	else if ($index % 3 == 0 && $numofcolumns == "3") { $firstinrow = 'firstinrow'; }
	else if ($index % 4 == 0 && $numofcolumns == "4") { $firstinrow = 'firstinrow'; }
	else if ($index % 2 == 0 && $numofcolumns == "4") { $firstinrow = 'firstinrow-multistep'; }
	
	if 		($numofcolumns == "2") { $widthclass = "nxs-one-half"; }
	else if ($numofcolumns == "3") { $widthclass = "nxs-one-third";	}
	else if ($numofcolumns == "4") { $widthclass = "nxs-one-fourth"; }
	else 	{ $widthclass = "nxs-one-third";	}
	
	// Title importance (H1 - H6)
	if ($item_title_heading != "") {
		$itemheadingelement = "h" . $item_title_heading; }
	else {
		// TODO: derive the title_importance based on the title_fontsize
		//nxs_webmethod_return_nack("to be implemented; derive title_heading from title_fontsize");
		$itemheadingelement = "h1";
	}
	
	// Image shadow
	$image_shadow = 'nxs-shadow';
	if ($remove_image_shadow != "") { $image_shadow = ''; }
	
	// Image border
	$image_border_width_cssclass = nxs_getcssclassesforlookup("nxs-border-width-", $image_border_width);
	
	echo '
	<div id="nxs-galleryitem-'.$items_genericlistid .'-'.$index.'-'.$imageid.'" class="nxs-galleryitem '.$orientation.' '.$firstinrow.' numofcolumns'.$numofcolumns.'">
		<a href="#" onclick="nxs_js_opengalleryitemlightbox(this); return false;">';
		
			// Title
			if ($item_title != "") {
				echo'
				<div class="title-wrapper '.$widthclass.'">
					<'.$itemheadingelement.'>'.$item_title.'</'.$itemheadingelement.'>
				</div>';
			}
		
			// Image
			echo'
			<div class="nxs-relative" >
				<div class="nxs-clear"></div>
				<div class="image-wrapper '.$widthclass.' '.$image_shadow.'">
					<div class="image-cropper">
						<div class="image-container '.$image_border_width_cssclass.'">
							<img src="'.$thumbimageurl.'" />
						</div>
					</div>
				</div>';
				
				if ($item_text != "") { echo'<div class="nxs-clear padding"></div>'; }
				
			echo '</div>';						
			
			// Text
			if ($item_text != "") {
				echo'
				<div class="description-wrapper '.$widthclass.'">
					<p class="nxs-default-p nxs-padding-bottom0"><span>'.$item_text.'<span></p>
				</div>';
			}
			
		echo'
		</a>
	</div>';
}

?>