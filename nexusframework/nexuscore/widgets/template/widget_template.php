<?php

function nxs_widgets_template_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_template_gettitle() {
	return nxs_l18n__("Text[nxs:widgettitle]", "nxs_td");
}

// Unistyle
function nxs_widgets_template_getunifiedstylinggroup() {
	return "textwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_template_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_template_gettitle(),
		"sheeticonid" => nxs_widgets_template_geticonid(),
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/text-widget/"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_template_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			
			/* TITLE
			---------------------------------------------------------------------------------------------------- */
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
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
				"label" 			=> nxs_l18n__("Title heading", "nxs_td"),
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
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
			/* TEXT
			---------------------------------------------------------------------------------------------------- */
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Text", "nxs_td"),
			),

			array(
				"id" 				=> "text",
				"type" 				=> "tinymce",
				"label" 			=> nxs_l18n__("Text", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Text goes here", "nxs_td"),
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "text_alignment",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Text alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("text_halignment"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
			/* IMAGE
			---------------------------------------------------------------------------------------------------- */
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Image", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array( 
				"id" 				=> "image_imageid",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Choose image", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to upload an image for your bio profile use this option.", "nxs_td"),
				"localizablefield"	=> true
			),			
			array(
				"id" 				=> "image_alignment",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("image_halignment"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "image_size",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("image_size"),
				"unistylablefield"	=> true
			),		
			array( 
				"id" 				=> "image_shadow",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Image shadow", "nxs_td"),
				"unistylablefield"	=> true
			),		
			array(
				"id" 				=> "image_alt",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Image alt text", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("imagealtplaceholder", "nxs_td"),
				"localizablefield"	=> true,
				"requirecapability" => nxs_cap_getdesigncapability(),
			),
			array(
				"id" 				=> "image_border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image border width", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_width"),
				"unistylablefield"	=> true
			),	
				
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperend"
			),
			
			/* BUTTON
			---------------------------------------------------------------------------------------------------- */
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Button", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "button_text",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Button text", "nxs_td"),
				"placeholder"		=> "Read more",
				"localizablefield"	=> true
			),	
			array(
				"id" 				=> "button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button scale", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true,
			),
			array( 
				"id" 				=> "button_color",
				"type" 				=> "colorzen", 
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "button_alignment",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_halignment"),
				"unistylablefield"	=> true,
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
						
			/* LINK
			---------------------------------------------------------------------------------------------------- */
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Link", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Article link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an article within your site.", "nxs_td"),
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
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
			),
			
			
			/* ICON
			---------------------------------------------------------------------------------------------------- */
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Icon", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "icon",
				"type" 				=> "icon",
				"label" 			=> nxs_l18n__("Icon", "nxs_td"),
			),
			array(
				"id"     			=> "icon_scale",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Icon scale", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("icon_scale"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
			),
			
			
			/* MISCELLANEOUS
			---------------------------------------------------------------------------------------------------- */
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Miscellaneous", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array( 
				"id" 				=> "bgcolor",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Color example", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id"     			=> "padding",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Padding example", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("padding"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "border_radius",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Border radius example", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_radius"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
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

function nxs_widgets_template_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// Unistyle
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_template_getunifiedstylinggroup(), $unistyle);
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

	//
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
	// Turn on output buffering
	ob_start();
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));
		
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
	/*$shouldrenderalternative = false;
	if (
		$image_imageid == "" &&
		$title == "" &&
		$text == "" &&
		$button_text == ""
	) {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Minimal: image, title, text or button", "nxs_td");
	}*/
	
	// if both external and article link are set
	$verifydestinationcount = 0;
	if ($destination_url != "") {
		$verifydestinationcount++;
	}
	
	if ($destination_articleid != "") {
		$$verifydestinationcount++;
	}
	
	if ($destination_js != "") {
		$verifydestinationcount++;
	}
	
	if ($verifydestinationcount > 1) {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Button: both external URL and article reference are set (ambiguous URL)", "nxs_td");
	}
	
	// if both external and article link are set
	if ($destination_url == "" && $destination_articleid == "" && $destination_js == "" && $button_text != "") {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Button: button is set, but no reference is set (no URL)", "nxs_td");
	}

	
	
	
	
	// Color
	$bgcolor_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $bgcolor);
	
	// Padding
	$padding_cssclass = nxs_getcssclassesforlookup("nxs-padding-", $padding);
	
	// Border radius
	$border_radius_cssclass = nxs_getcssclassesforlookup("nxs-border-radius-", $border_radius);
	
	
	
	
	
	/* LINK
	---------------------------------------------------------------------------------------------------- */
	
	// Article link
	if ($destination_articleid != "") {
		$destination_url = nxs_geturl_for_postid($destination_articleid);
	}
	
	
	/* TITLE
	---------------------------------------------------------------------------------------------------- */
	
	// Title heading
	if ($title_heading != "") {
		$title_heading = "h" . $title_heading;	
	} else {
		$title_heading = "h1";
	}

	// Title alignment
	$title_alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $title_alignment);
	
	// Title fontsize
	$title_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $title_fontsize);

	// Title height (across titles in the same row)
	// This function does not fare well with CSS3 transitions targeting "all"
	$heightiqprio = "p1";
	$title_heightiqgroup = "title";
  	$titlecssclasses = $title_fontsize_cssclass;
	$titlecssclasses = nxs_concatenateargswithspaces($titlecssclasses, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$title_heightiqgroup}");
	
	// Title
	$title = '<' . $title_heading . ' class="nxs-title ' . $title_alignment_cssclass . ' ' . $title_fontsize_cssclass . ' ' . $titlecssclasses . '">' . $title . '</' . $title_heading . '>';
	
	// Linked title
	if ($destination_articleid != "") {
		$title = '<a href="' . $destination_url .'">' . $title . '</a>';
	} else if ($destination_url != "") {
		$title = '<a href="' . $destination_url .'" target="_blank">' . $title . '</a>';
	}
	
	// Applying link colors to title
	$title = '<div class="nxs-applylinkvarcolor">' . $title . '</div>';


	/* TEXT
	---------------------------------------------------------------------------------------------------- */
	
	// Text alignment
	$text_alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $text_alignment);
	
	// Text height (across paragraphs in the same row)
	// This function does not fare well with CSS3 transitions targeting "all"
	if ($text_heightiq != "") {
		$heightiqprio = "p1";
		$text_heightiqgroup = "text";
		$textcssclasses = nxs_concatenateargswithspaces($cssclasses, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$text_heightiqgroup}");
	}
	
	// Text	
	$text = '<div class="nxs-text nxs-default-p nxs-applylinkvarcolor nxs-padding-bottom0 ' . $text_alignment_cssclass . ' ' . $textcssclasses . '">' . $text . '</div>';
	
	
	/* BUTTON
	---------------------------------------------------------------------------------------------------- */
	
	// Button aligment
	$button_alignment = nxs_getcssclassesforlookup("nxs-align-", $button_alignment);
	
	// Button color
	$button_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $button_color);
	
	// Button scale
	$button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $button_scale);
	
	// Button	
	if ($destination_articleid != "") {
		$button = '<a href="' . $destination_url .'" class="nxs-button ' . $button_color_cssclass .' ' . $button_scale_cssclass .'">' . $button_text . '</a>';
	} else if ($destination_url != "") {
		$button = '<a href="' . $destination_url .'" class="nx-button ' . $button_color_cssclass .' ' . $button_scale_cssclass .'" target="_blank">' . $button_text . '</a>';
	}
	
	// Applying alignment to button
	$button = '<p class="' . $button_alignment . ' nxs-padding-bottom0">' . $button . '</p>';		
	
	
	/* ICON
	---------------------------------------------------------------------------------------------------- */
	
	// Icon scale
	$icon_scale_cssclass = nxs_getcssclassesforlookup("nxs-icon-scale-", $icon_scale);
		
	// Icon
	if ($icon != "") {$icon = '<span class="' . $icon . ' ' . $icon_scale_cssclass . '"></span>';}
	
	
	/* IMAGE
	---------------------------------------------------------------------------------------------------- */
	
	// Image properties
	if ($image_imageid != "") {
		
		// Determines which image size, full or thumbnail, should be used    
		$wpsize = nxs_getwpimagesize($image_size);
		
		$imagemetadata= wp_get_attachment_image_src($image_imageid, $wpsize, true);
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$imageurl 		= $imagemetadata[0];
		$imageurl = nxs_img_getimageurlthemeversion($imageurl);
		$imagewidth 	= $imagemetadata[1] . "px";
		$imageheight 	= $imagemetadata[2] . "px";	
	}
	
	// Image alignment
	$image_alignment_cssclass = nxs_getimagecssalignmentclass($image_alignment);
	
	// Image size
	$image_size_cssclass = nxs_getimagecsssizeclass($image_size);
	
	// Image border size
	$image_border_width = nxs_getcssclassesforlookup("nxs-border-width-", $image_border_width);
	
	// Image shadow
	if ($image_shadow != "") { $image_shadow = 'nxs-shadow'; }
	
	// Image with border functionality
	$image = '
		<div class="nxs-image-wrapper ' . $image_shadow . ' ' . $image_size_cssclass . ' ' . $image_alignment_cssclass . ' ' . '">
			<div style="right: 0; left: 0; top: 0; bottom: 0; border-style: solid;" class="' . $image_border_width . '">
				<img src="' . $imageurl . '" alt="' . $image_alt . '" title="' . $image_title . '" />
			</div>
		</div>';
	
	// Linked image
	if ($destination_articleid != "") {
		$image = '<a href="' . $destination_url .'">' . $image . '</a>';
	} else if ($destination_url != "") {
		$image = '<a href="' . $destination_url .'" target="_blank">' . $image . '</a>';
	}
	
	// Default image 
	$default_image = '<img src="' . $imageurl . '" alt="' . $image_alt . '" title="' . $image_title . '" class="' . $image_size_cssclass . ' ' . $image_alignment_cssclass . '"/>';
	
	// Linked default image
	if ($destination_articleid != "") {
		$default_image = '<a href="' . $destination_url .'">' . $default_image . '</a>';
	} else if ($destination_url != "") {
		$default_image = '<a href="' . $destination_url .'" target="_blank">' . $default_image . '</a>';
	}
	
	if ($image_border_width != "") {
		$image = $image; 
	} else {
		$image = $default_image; 
	}
	
	
	
	
	
	
	
		
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) {
		if ($alternativehint == "") {
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} else {
		
				
			echo $title;
			
			echo $text;
			
			echo $button;
			
			echo $icon;
			
			echo $image;
		
		
		
	}
	
	/* ------------------------------------------------------------------------------------------------- */
	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = ob_get_contents();
	ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	return $result;
}

function nxs_widgets_template_initplaceholderdata($args)
{
	extract($args);

	$args['button_color'] = "base2";
	$args['title_heading'] = "2";
	$args['button_scale'] = "1-0";
	$args['icon_scale'] = "1-0";
	$args['image_size'] = "c@1-0";
	
	$args['title_heightiq'] = "true";
	$args['text_heightiq'] = "true";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_template_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
