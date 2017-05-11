<?php

function nxs_widgets_slidesincat_geticonid()
{
	// there's no slide image yet; we re-use the image icon
	// $widget_name = basename(dirname(__FILE__));
	return "nxs-icon-categories";	// . $widget_name;
}

function nxs_widgets_slidesincat_gettitle()
{
	return nxs_l18n__("Slides in category", "nxs_td");
}


/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_slidesincat_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_slidesincat_gettitle(),
		"sheeticonid" => nxs_widgets_slidesincat_geticonid(),
		"fields" => array
		(
			array( 
				"id" 				=> "wrapper_filter_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Filter", "nxs_td"),
			),

			array(
				"id" 				=> "items_filter_catids",
				"type" 				=> "categories",
				"label" 			=> nxs_l18n__("Categories", "nxs_td"),
				"editable" 			=> "false",
			),	
			array(
				"id" 				=> "items_filter_skipcount",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Skip posts", "nxs_td"),
				"dropdown" 			=> array
				("0"=>"0","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9","10"=>"10","11"=>"11","12"=>"12","13"=>"13","14"=>"14","15"=>"15","16"=>"16","17"=>"17","18"=>"18","19"=>"19","20"=>"20")
			),
			array(
				"id" 				=> "items_filter_maxcount",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Number of posts", "nxs_td"),
				"dropdown" 			=> array("@@@empty@@@"=>"all","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9","10"=>"10","20"=>"20","30"=>"30","40"=>"40","50"=>"50","100"=>"100")
			),			
			array(
				"id" 				=> "items_order",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Order", "nxs_td"),
				"dropdown" 			=> array(
					"present to past"=>nxs_l18n__("present to past", "nxs_td"), 
					"past to present"=>nxs_l18n__("past to present", "nxs_td"),
					"title az"=>nxs_l18n__("title a-z", "nxs_td"),
					"title za"=>nxs_l18n__("title z-a", "nxs_td"),
					"random"=>nxs_l18n__("random", "nxs_td"),
				),
			),
			array(
				"id" 				=> "item_text_truncatelength",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Text max length", "nxs_td"),
				"dropdown" 			=> nxs_convertindexarraytoassociativearray(array("","100","110","120","130","140","150","160","170","180","190","200","210","220","230","240","250","260","270","280","290","300","400","500","600")),
			),
			array(
				"id" 				=> "item_text_appendchars",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Abbreviation characters", "nxs_td"),
				"placeholder"		=> nxs_l18n__("[...]", "nxs_td"),
			),
			array
			( 
				"id" 				=> "item_image_fallbackimageid",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Choose image", "nxs_td"),
			),
			array( 
				"id" 				=> "wrapper_filter_end",
				"type" 				=> "wrapperend"
			),
		)
	);
	
	return $options;
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_slidesincat_render_webpart_render_htmlvisualization($args)
{
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

	// Link
	if ($destination_articleid != "")
	{
		$theurl = nxs_geturl_for_postid($destination_articleid);
	}
	else
	{
		$theurl = $destination_url;
	}

	global $nxs_global_placeholder_render_statebag;

	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;
	$hovermenuargs["enable_deleterow"] = true;
	$hovermenuargs["enable_deletewidget"] = false;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
	//
	// render actual control / html
	//
	
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-slider-item";
	
	?>
	<div class="nxs-dragrow-handler nxs-padding-menu-item">
	    <div class="content2">
	    	<div class="box-content nxs-width10 nxs-float-left">
	    		<?php
	    			$commaseperated = nxs_convert_stringwithbracketlist_to_stringwithcommas($items_filter_catids);	
						if ($commaseperated == "")
						{
							$output = "Please select a category first";
						}
						else
						{
							$output = wp_list_categories("hide_empty=0&include=$commaseperated&style=none&echo=0");
							//$output = str_replace("<br />", " ", $output);
							$output = str_replace("<a ", "<a target='_blank' ", $output);
							$output = "Slides using featured image, titles and descriptions of posts and pages in category " . $output;
						}
						echo $output;
	    		?>
	    	</div>
	    	<div class="box-content nxs-width20 nxs-float-left"><?php echo nxs_render_html_escape_gtlt($title); ?></div>
	    	<div class="box-content nxs-width40 nxs-float-left"><?php echo nxs_render_html_escape_gtlt($text); ?></div>
	    	<div class="box-content nxs-width30 nxs-float-left"><?php echo nxs_render_html_escape_gtlt($theurl); ?></div>
	    	<div class="nxs-clear"></div>
	    </div> <!--END content-->
	</div>
	
	<?php 
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

function nxs_widgets_slidesincat_initplaceholderdata($args)
{
	extract($args);
	
	$args['ph_margin_bottom'] = "0-0";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>