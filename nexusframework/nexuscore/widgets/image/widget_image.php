<?php

function nxs_widgets_image_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_image_gettitle() {
	return nxs_l18n__("Image[nxs:widgettitle]", "nxs_td");
}

// 
function nxs_widgets_image_getunifiedstylinggroup() {
	return "imagewidget";
}
/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_image_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$options = array
	(
		"sheettitle" => nxs_widgets_image_gettitle(),
		"sheeticonid" => nxs_widgets_image_geticonid(),
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/image-widget/"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_image_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			// EFFECTS
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Effects", "nxs_td"),
				"initial_toggle_state"	=> "closed",
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "grayscale",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Grayscale hover effect", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "enlarge",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Enlarge hover effect", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),
			
			// TITLE
			
			array( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			array
			( 
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Title goes here", "nxs_td"),
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title importance", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "title_alignment",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_halignment"),
				"unistylablefield"	=> true
			),
						
			array(
				"id" 				=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Override title fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_heightiq",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align titles", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's title will participate in the title alignment of other partipating widgets in this row", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),
		
			// IMAGE
			
			array( 
				"id" 				=> "wrapper_input_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Image", "nxs_td"),
			),
			
			array
			( 
				"id" 				=> "image_imageid",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Image", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to upload an image for your bio profile use this option.", "nxs_td"),
				"localizablefield"	=> true
			),
			array
			( 
				"id" 				=> "image_shadow",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Image shadow", "nxs_td"),
				"unistylablefield"	=> true
			),						
			array
			(
				"id" 				=> "image_alt",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Alternate text", "nxs_td"),
				"localizablefield"	=> true,
				"requirecapability" => nxs_cap_getdesigncapability(),
			),		
			array
			(
				"id" 				=> "image_border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Border size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_width"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "image_size",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image size", "nxs_td"),
				"dropdown" 			=> array
				(
					"stretch" 	=> nxs_l18n__("stretch", "nxs_td"),
					"original" 	=> nxs_l18n__("original", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "image_alignment",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image alignment", "nxs_td"),
				"dropdown" 			=> array
				(
					"left" 		=> nxs_l18n__("left", "nxs_td"),
					"center" 	=> nxs_l18n__("center", "nxs_td"),
					"right" 	=> nxs_l18n__("right", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_input_end",
				"type" 				=> "wrapperend"
			),
		
			// LINK
			
			array( 
				"id" 				=> "wrapper_link_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Link", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Article link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the image to an article within your site.", "nxs_td"),
			),
			array(
				"id" 				=> "destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("External link", "nxs_td"),
				"placeholder"		=> nxs_l18n__("http://www.nexusthemes.com", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the image to an external source using the full url.", "nxs_td"),
				"localizablefield"	=> true
			),
			array( 
				"id" 				=> "wrapper_link_end",
				"type" 				=> "wrapperend"
			),
		)
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_image_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);

	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));

	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "")
	{
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_image_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);	
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Localize atts
	$mixedattributes = nxs_localization_localize($mixedattributes);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs); 
		
	// Turn on output buffering
	ob_start();
	
	global $nxs_global_placeholder_render_statebag;
	if ($shouldrenderalternative == true) {
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . "-warning ";
	} else {
		// Appending custom widget class
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " ";
	}
	
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;
	if (
	$image_imageid == "" &&
	$title == "" &&
	nxs_has_adminpermissions()) {
		$shouldrenderalternative = true;
	}
	
	// Image metadata
	if ($image_imageid != "") {
		$imagemetadata= wp_get_attachment_image_src($image_imageid, 'full', true);
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$imageurl 		= $imagemetadata[0];
		$imagewidth 	= $imagemetadata[1] . "px";
		$imageheight 	= $imagemetadata[2] . "px";	
	}
	
	// Title
	$htmltitle = nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, $destination_articleid, $destination_url);

	$image_border_width = nxs_getcssclassesforlookup("nxs-border-width-", $image_border_width);
	
	// Image shadow
	if ($image_shadow != "") { $image_shadow = 'nxs-shadow'; }
	
	// Hover effects
	if ($enlarge != "") { $enlarge = 'nxs-enlarge'; }
	if ($grayscale != "") {	$grayscale = 'nxs-grayscale'; }
	
	// Original vs stretched images
	if ($image_size == 'original') {
		$html = '<img src="'.$imageurl.'" class=" '.$grayscale.' '.$enlarge.'" style="display: block;" alt="'.$image_alt.'">';
	} else {
		$html = '<img src="'.$imageurl.'" class="nxs-stretch '.$grayscale.' '.$enlarge.'" style="display: block;" alt="'.$image_alt.'">';
	}

	// Image max size
	if ($image_size == 'original') { $max_width = $imagewidth; }
	
	// Image alignment
	if ($image_size == 'original' && $image_alignment == 'center') { $image_alignment = "nxs-margin-auto"; } else
	if ($image_size == 'original' && $image_alignment == 'right') { $image_alignment = "nxs-margin-auto-right"; }
	
	
	
	if ($image_border_width != "" ) {
		$html = '<div style="right: 0; left: 0; top: 0; bottom: 0; border-style: solid;" class="'.$image_border_width.' nxs-overflow">'.$html.'</div>'; 
	}	
	
	$destination_articleid = nxs_geturl_for_postid($destination_articleid);
	
	// Image link
	if 		($destination_articleid != "") 		{ $html = '<a href="'.$destination_articleid .'">'.$html.'</a>'; } 
	else if ($destination_url != "") 			{ $html = '<a href="'.$destination_url .'" target="_blank">'.$html.'</a>'; }
	
	// Image
	if ($image_imageid != "") 
	{
		// if image is 'set'
		$image_imageid = '
			<div class="nxs-relative nxs-overflow '.$image_shadow.' '.$image_alignment.'" style="max-width: '.$max_width.'">
				'.$html.'
			</div>';
	}
	
	// Filler
	$filler = '<div class="nxs-clear nxs-filler"></div>';
		
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) {
		
		nxs_renderplaceholderwarning(nxs_l18n__("Missing input", "nxs_td")); 
		
	} else {
		
		// logo class is necessary to enable autoscaling for "original" sized images
		echo '<div class="nxs-applylinkvarcolor nxs-logo">';
	
			echo $htmltitle;
			if ($htmltitle != "" && $image_imageid != "") { 
				echo $filler; 
			}
			echo $image_imageid;
			
		echo '<div>';
		
		
		      
	} 
	
	/* ------------------------------------------------------------------------------------------------- */
	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = ob_get_contents();
	ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-'.$placeholderid;
	return $result;
}

function nxs_widgets_image_initplaceholderdata($args)
{
	extract($args);
	
	global $current_user;
	get_currentuserinfo();

	$args["title_heading"] = 2;
	$args['title_heightiq'] = "true";
		
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_image_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
