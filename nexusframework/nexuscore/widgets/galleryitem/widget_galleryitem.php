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
	
	ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-galleryitemr-item";
	
	?>
	    
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
	
	<!-- -->
	
	<?php 
	
	$html = ob_get_contents();
	ob_end_clean();

	
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

?>
