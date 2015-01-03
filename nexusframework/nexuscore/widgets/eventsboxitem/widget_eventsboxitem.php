<?php

function nxs_widgets_eventsboxitem_geticonid() {
	// there's no eventsboxitem image yet; we re-use the image icon
	// $widget_name = basename(dirname(__FILE__));
	return "nxs-icon-calendar";
}

function nxs_widgets_eventsboxitem_gettitle() {
	return nxs_l18n__("eventsboxitem[nxs:widgettitle]", "nxs_td");
}

function nxs_widgets_eventsboxitem_render_webpart_render_htmlvisualization($args) {
	//
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	$mixedattributes = array_merge($temp_array, $args);
	
	$image_imageid = $mixedattributes['image_imageid'];
	$title = $mixedattributes['title'];
	$text = $mixedattributes['text'];
	$destination_articleid = $mixedattributes['destination_articleid'];
	
	$lookup = wp_get_attachment_image_src($image_imageid, 'full', true);
	
	if ($destination_articleid != "") {
		$destination_articleid = nxs_geturl_for_postid($destination_articleid);
	}
	
	$width = $lookup[1];
	$height = $lookup[2];		
	
	$lookup = wp_get_attachment_image_src($image_imageid, 'thumbnail', true);
	$url = $lookup[0];
	$url = nxs_img_getimageurlthemeversion($url);

	if (nxs_has_adminpermissions()) {
		$renderBeheer = true;
	} else {
		$renderBeheer = false;
	}
	
	if ($rendermode == "default") {
		if ($renderBeheer) {
			$shouldrenderhover = true;
		} else {
			$shouldrenderhover = false;
		}
	} else if ($rendermode == "anonymous") {
		$shouldrenderhover = false;
	} else {
		echo "unsupported rendermode;" . $rendermode;
		die();
	}

	global $nxs_global_placeholder_render_statebag;

	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs); 
		
	//
	// render actual control / html
	//
	
	ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-eventsboxitem-item";
	
	echo '
	<div class="content2">
        <div class="box-content nxs-width10 nxs-float-left">'.nxs_render_html_escape_gtlt($date_dd_mm_yy).'</div>
		<div class="box-content nxs-width10 nxs-float-left">'.nxs_render_html_escape_gtlt($title).'</div>
		<div class="box-content nxs-width40 nxs-float-left">'.nxs_render_html_escape_gtlt($subtitle).'</div>
        <div class="box-content nxs-width20 nxs-float-left">'.nxs_render_html_escape_gtlt($destination_articleid).'</div>
        <div class="box-content nxs-width20 nxs-float-left">'.nxs_render_html_escape_gtlt($destination_url).'</div>
        <div class="nxs-clear"></div>
    </div> <!--END content-->
	'; 
	
	$html = ob_get_contents();
	ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

// Define the properties of this widget
function nxs_widgets_eventsboxitem_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_eventsboxitem_gettitle(),
		"sheeticonid" => nxs_widgets_eventsboxitem_geticonid(),
	
		"fields" => array
		(
			// GENERAL			
			
			array( 
				"id" 				=> "wrapper_input_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("General", "nxs_td"),
			),
			
			array
			( 
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" => nxs_l18n__("Title goes here", "nxs_td"),
			),
			
			array
			( 
				"id" 				=> "subtitle",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Subtitle", "nxs_td"),
				"placeholder" => nxs_l18n__("Subtitle goes here", "nxs_td"),
			),
			
			array
			( 
				"id" 				=> "date_dd_mm_yy",
				"type" 				=> "date",
				"dateformat" 		=> "dd-mm-yy",
				"label" 			=> nxs_l18n__("Date", "nxs_td"),
			),
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Article link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the menu item to an article within your site.", "nxs_td"),
			),
			array(
				"id" 				=> "destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("External link", "nxs_td"),
				"placeholder"		=> nxs_l18n__("http://www.nexusthemes.com", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an external source using the full url.", "nxs_td"),
				"localizablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_input_end",
				"type" 				=> "wrapperend"
			),

		)
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

function nxs_widgets_eventsboxitem_initplaceholderdata($args)
{
	extract($args);
	
	// 
	$args['ph_margin_bottom'] = "0-0";

	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);


	$result = array();
	$result["result"] = "OK";
	
	return $result;
}


?>
