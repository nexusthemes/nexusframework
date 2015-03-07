<?php

function nxs_widgets_text_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_text_gettitle() {
	return nxs_l18n__("Text[nxs:widgettitle]", "nxs_td");
}

// Unistyle
function nxs_widgets_text_getunifiedstylinggroup() {
	return "textwidget";
}

// Unicontent
function nxs_widgets_text_getunifiedcontentgroup() {
	return "textwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_text_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_text_gettitle(),
		"sheeticonid" 		=> nxs_widgets_text_geticonid(),
		"sheethelp" 		=> nxs_l18n__("http://nexusthemes.com/text-widget/"),
		"unifiedstyling" 	=> array("group" => nxs_widgets_text_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_text_getunifiedcontentgroup(),),
		"fields" => array
		(
			// TITLE
			
			array( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
			),
			
			array(
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" => nxs_l18n__("Title goes here", "nxs_td"),
				"unicontentablefield" => true,
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
				"id" 				=> "title_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Title fontzen", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_alignment",
				"type" 				=> "halign",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
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
				"id" 				=> "top_info_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Top info color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id"     			=> "top_info_padding",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Top info padding", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("padding"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "icon",
				"type" 				=> "icon",
				"label" 			=> nxs_l18n__("Icon", "nxs_td"),
				"unicontentablefield" => true,
			),
			array(
				"id"     			=> "icon_scale",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Icon scale", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("icon_scale"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),
			
			// TEXT
			
			array( 
				"id" 				=> "wrapper_text_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Text", "nxs_td"),
			),
			array(
				"id" 				=> "text",
				"type" 				=> "tinymce",
				"label" 			=> nxs_l18n__("Text", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Text goes here", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "text_alignment",
				"type" 				=> "halign",
				"label" 			=> nxs_l18n__("Text alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "wrapper_text_end",
				"type" 				=> "wrapperend"
			),
			
			// IMAGE
			
			array( 
				"id" 				=> "wrapper_image_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Image", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array( 
				"id" 				=> "image_imageid",
				"type" 				=> "image",
				"allow_featuredimage" => true,
				"label" 			=> nxs_l18n__("Choose image", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to upload an image for your bio profile use this option.", "nxs_td"),
				"unicontentablefield" => true,
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
				"placeholder" => nxs_l18n__("imagealtplaceholder", "nxs_td"),
				"unicontentablefield" => true,
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
				"id" 				=> "wrapper_image_begin",
				"type" 				=> "wrapperend"
			),
			
			// BUTTON
			array( 
				"id" 				=> "wrapper_button_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Button", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "button_text",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Button text", "nxs_td"),
				"placeholder"		=> "Read more",
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),	
			
			array(
				"id" 				=> "button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true,
			),
			array( 
				"id" 				=> "button_color",
				"type" 				=> "colorzen", // "select",
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "button_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Button fontzen", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "button_alignment",
				"type" 				=> "halign",
				"label" 			=> nxs_l18n__("Button alignment", "nxs_td"),
				"unistylablefield"	=> true,
			),
			array( 
				"id" 				=> "wrapper_button_end",
				"type" 				=> "wrapperend"
			),
			
			/* LINK
			---------------------------------------------------------------------------------------------------- */
			
			array( 
				"id" 				=> "wrapper_begin_link",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Link", "nxs_td"),
			),
			
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Article link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an article within your site.", "nxs_td"),
				"unicontentablefield" => true,
			),
			array(
				"id" 				=> "destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("External link", "nxs_td"),
				"placeholder"		=> nxs_l18n__("http://www.nexusthemes.com", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an external source using the full url.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			
			array(
				"id" 				=> "destination_js",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Javascript", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Apply javascript when the button is pressed.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true,
				"requirecapability" => nxs_cap_getdesigncapability(),
			),
		
			array(
				"id" 				=> "destination_target",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Target", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@empty@@@"=>nxs_l18n__("Auto", "nxs_td"),
					"_blank"=>nxs_l18n__("New window", "nxs_td"),
					"_self"=>nxs_l18n__("Current window", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end_link",
				"type" 				=> "wrapperend",
			),
			
			// MISCELLANEOUS
			
			array( 
				"id" 				=> "wrapper_misc_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Miscellaneous", "nxs_td"),
				"initial_toggle_state"	=> "closed",
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
				"id" 				=> "text_heightiq",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align texts", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's text will participate in the text alignment of other partipating widgets in this row", "nxs_td"),
				"unistylablefield"	=> true
			),			
			array( 
				"id" 				=> "text_showliftnote",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Liftnote", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("You can make the first paragraph stand out with this option.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "text_showdropcap",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Dropcap", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Enlarge the first character of the first paragraph with this option.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "text_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Text fontzen", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "enlarge",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Enlarge hover effect", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "grayscale",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Grayscale hover effect", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "fx",
				"type" 				=> "effects",
				"label" 			=> nxs_l18n__("Effects", "nxs_td"),
			),

			array( 
				"id" 				=> "wrapper_misc_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),
			
			// CALLOUT BANNER
			
			array( 
				"id" 				=> "wrapper_misc_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Callout banner", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "callout_text",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Callout text", "nxs_td"),
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "callout_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Callout heading", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "callout_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Callout banner color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample", "nxs_td"),
				"unistylablefield"	=> true
			),			
			array( 
				"id" 				=> "wrapper_misc_end",
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

function nxs_widgets_text_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	}
	
	// Blend unistyle properties
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_text_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_text_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	//$mixedattributes = $temp_array;
	$mixedattributes = array_merge($temp_array, $args);
	
	// Localize atts
	//$mixedattributes = nxs_localization_localize($mixedattributes);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title","text","button_text", "destination_url"));
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
	// Overruling of parameters
	if ($image_imageid == "featuredimg")
	{
		$image_imageid = get_post_thumbnail_id($containerpostid);
	}
	
	global $nxs_global_row_render_statebag;
	$pagerowtemplate = $nxs_global_row_render_statebag["pagerowtemplate"];
	if ($pagerowtemplate == "one")
	{
		$text_heightiq = "";	// off!
	}

	if ($postid != "" && $placeholderid != "")
	{
		//
		$hovermenuargs = array();
		$hovermenuargs["postid"] = $postid;
		$hovermenuargs["placeholderid"] = $placeholderid;
		$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
		$hovermenuargs["metadata"] = $mixedattributes;
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	}

	// Turn on output buffering
	ob_start();
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));
		
	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		global $nxs_global_placeholder_render_statebag;
		if ($shouldrenderalternative == true) {
			$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . "-warning ";
		} else {
			// Appending custom widget class
			$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " ";
		}
	}
	
	//
	// process FX
	//
	if ($fx != "")
	{
		// voor nu gaan we er voor het gemak nog even vanuit dat er maar 1 effect is
		$nxs_global_placeholder_render_statebag["ph_runtimecssclass"] .= "nxs-fx-lazyload nxs-fx-widgetslidein nxs-fx-untriggered";
	}
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;
	
	if ($image_imageid != "" && $image_url != "")
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Ambiguous: not clear which image to use (you picked an image, and entered the image url at the same time). Please only use one at a time.", "nxs_td");
	}
	
	if (
		($image_imageid == "" && $callout_text != "") ||
		($image_size != nxs_isimageautofit($image_size) && $callout_text != "")
	) {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("You need to upload an image with a 'stretch' configuration for the callout banner to be shown.", "nxs_td");
	}
	
	if (
		$image_imageid == "" &&
		$image_url == "" &&
		$title == "" &&
		$text == "" &&
		$button_text == ""
	) {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Minimal: image, title, text or button", "nxs_td");
	}
	
	// if both external and article link are set
	$verifydestinationcount = 0;
	if ($destination_url != "") {
		$verifydestinationcount++;
	}
	
	if ($destination_articleid != "") {
		$verifydestinationcount++;
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

	
	// If image alt isn't filled revert to title alt
	$image_title = $image_alt;
	
	$wrappingelement = "div";
	
	// convert video links to embedded videos
	$wp_embed = $GLOBALS['wp_embed'];
	$text = str_replace("<p>", "<p>\r\n", $text);
	$text = str_replace("</p>", "\r\n</p>", $text);
	$text = str_replace("<br />", "<br />\r\n", $text);
	$text = str_replace("<br>", "<br>\r\n", $text);
	
	// trailing </p>
	$text = $wp_embed->autoembed($text);
	

	// get html for each part	
	$htmltext = nxs_gethtmlfortext($text, $text_alignment, $text_showliftnote, $text_showdropcap, $wrappingelement, $text_heightiq, $text_fontzen);
	
	$button_heightiq = "";
	$htmlforbutton = nxs_gethtmlforbutton($button_text, $button_scale, $button_color, $destination_articleid, $destination_url, $destination_target, $button_alignment, $destination_js, $button_heightiq, $button_fontzen);
	$htmlfiller = nxs_gethtmlforfiller();
	
	// Callout color
	$callout_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $callout_color);
	
	
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
	
	if ($title_alignment == "center") { $top_info_title_alignment = "margin: 0 auto;"; } else
	if ($title_alignment == "right")  { $top_info_title_alignment = "margin-left: auto;"; } 
	
	// Title fontsize
	$title_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $title_fontsize);

	// Title height (across titles in the same row)
	// This function does not fare well with CSS3 transitions targeting "all"
	$heightiqprio = "p1";
	$title_heightiqgroup = "title";
  	$titlecssclasses = $title_fontsize_cssclass;
	$titlecssclasses = nxs_concatenateargswithspaces($titlecssclasses, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$title_heightiqgroup}");
	
	// Top info padding and color
	$top_info_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $top_info_color);
	$top_info_padding_cssclass = nxs_getcssclassesforlookup("nxs-padding-", $top_info_padding);
	
	// Icon scale
	$icon_scale_cssclass = nxs_getcssclassesforlookup("nxs-icon-scale-", $icon_scale);
		
	// Icon
	if ($icon != "") {$icon = '<span class="'.$icon.' '.$icon_scale_cssclass.'"></span>';}
	
	if ($title_schemaorgitemprop != "") {
		// bijv itemprop="name"
		$title_schemaorg_attribute = "itemprop='{$title_schemaorgitempro}'";
	} else {
		$title_schemaorg_attribute = "";	
	}
	
	if ($title_fontzen != "")
	{
		$title_fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen nxs-fontzen-", $title_fontzen);
	}
	
	$concatenatedcssclasses = nxs_concatenateargswithspaces("nxs-title", $title_alignment_cssclass, $title_fontsize_cssclass, $titlecssclasses, $title_fontzen_cssclass);
	
	
	// Title
	$titlehtml = "<{$title_heading} {$title_schemaorg_attribute} class='{$concatenatedcssclasses}'>{$title}</{$title_heading}>";
	
	if ($destination_target == "_self") {
		$destination_target_html = 'target="_self"';
	} else if ($destination_target == "_blank") {
		$destination_target_html = 'target="_blank"';
	} else {
		if ($destination_articleid != "") {
			$destination_target_html = 'target="_self"';
		} else {
			$homeurl = nxs_geturl_home();
 			if (nxs_stringstartswith($destination_url, $homeurl)) {
 				$destination_target_html = 'target="_self"';
 			} else {
 				$destination_target_html = 'target="_blank"';
 			}
		}
	}
	
	// fix tel links
	if ($destination_url != "") 
	{
		if (nxs_stringstartswith($destination_url, "tel:")) {
			// a phone link; if parenthesis or spaces are used; absorb them
			$url = $destination_url;
			$url = str_replace(" ", "", $url);
			$url = str_replace("(", "", $url);
			$url = str_replace(")", "", $url);
		} else {
			// regular link
			$url = $destination_url;
		}
		
		$destination_url = $url;
	}
	
	// Linked title
	if ($destination_articleid != "") {
		$titlehtml = '<a '.$destination_target_html.' href="'.$destination_url .'">'.$titlehtml.'</a>';
	} else if ($destination_url != "") {
		$titlehtml = '<a '.$destination_target_html.' href="'.$destination_url .'">'.$titlehtml.'</a>';
	}
	
	// Applying link colors to title
	if ($top_info_color_cssclass == "") { 
		$titlehtml = '<div class="nxs-applylinkvarcolor">'.$titlehtml.'</div>'; 
	}
	
	/* IMAGE
	---------------------------------------------------------------------------------------------------- */
	
	// Image properties
	$derived_imageurl = "";	// none
	if ($image_imageid != "" && $image_size != "-") {
		// Determines which image size, full or thumbnail, should be used    
		$wpsize = nxs_getwpimagesize($image_size);
		$imagemetadata= wp_get_attachment_image_src($image_imageid, $wpsize, true);
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$derived_imageurl = $imagemetadata[0];
		$derived_imageurl = nxs_img_getimageurlthemeversion($derived_imageurl);
	} else if ($image_url != "" && $image_size != "-") {
		// Determines which image size, full or thumbnail, should be used    
		$wpsize = nxs_getwpimagesize($image_size);
		$derived_imageurl = $image_url;
		$derived_imageurl = nxs_img_getimageurlthemeversion($derived_imageurl);
	}
	
	// Image alignment
	$image_alignment_cssclass = nxs_getimagecssalignmentclass($image_alignment);
	if ($image_size == 'auto-fit') {$image_alignment_cssclass = "";}
	
	// Image size
	$image_size_cssclass = nxs_getimagecsssizeclass($image_size);
	
	// Image border size
	$image_border_width = nxs_getcssclassesforlookup("nxs-border-width-", $image_border_width);
	
	// Image shadow
	if ($image_shadow != "") { $image_shadow = 'nxs-shadow'; }
	
	// Hover effects
	if ($enlarge != "") { $enlarge = 'nxs-enlarge'; }
	if ($grayscale != "") {	$grayscale = 'nxs-grayscale'; }
	
	// Image with border functionality
	$image = '
		<div class="nxs-image-wrapper '.$image_shadow.' '.$image_size_cssclass.' '.$image_alignment_cssclass.' '.'">
			<div style="right: 0; left: 0; top: 0; bottom: 0; border-style: solid;" class="'.$image_border_width.' nxs-overflow">
				<img src="'.$derived_imageurl.'" alt="'.$image_alt.'" title="'.$image_title.'" class="'.$enlarge.' '.$grayscale.'" />
			</div>
		</div>';
	
	// Linked image
	if ($destination_articleid != "") {
		$image = '<a '.$destination_target_html.' href="'.$destination_url .'">'.$image.'</a>';
	} else if ($destination_url != "") {
		$image = '<a '.$destination_target_html.' href="'.$destination_url .'">'.$image.'</a>';
	}
	
	// Default image 
	$default_image = '<img src="'.$derived_imageurl.'" alt="'.$image_alt.'" title="'.$image_title.'" class="'.$enlarge.' '.$image_size_cssclass.' '.$image_alignment_cssclass.'"/>';
	
	if ($image_url != "" && $image_size != "auto-fit") {
		$default_image = '';
		$default_image .= '<div class="nxs-image-wrapper ' . $image_alignment_cssclass . ' ' . $image_size_cssclass . '" style="background: url('. $derived_imageurl . '); background-repeat:no-repeat; background-attachment:scroll; background-position:center; background-size: cover;"><img style="visibility: hidden;" src="'. $derived_imageurl . '" />&nbsp;';
		$default_image .= '</div>';
	}
	
	// Linked default image
	if ($destination_articleid != "") {
		$default_image = '<a '.$destination_target_html.' href="'.$destination_url .'">'.$default_image.'</a>';
	} else if ($destination_url != "") {
		$default_image = '<a '.$destination_target_html.' href="'.$destination_url .'">'.$default_image.'</a>';
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
		
		/* Title and filler
		----------------------------------------------------------------------------------------------------*/
		if ($icon == "" && $title == "") {
			// nothing to show
		} else if (($top_info_padding_cssclass != "") || ($icon != "") || ($top_info_color_cssclass != "")) {
			 
			// Icon title
			echo '
			<div class="top-wrapper nxs-border-width-1-0 '.$top_info_color_cssclass.' '.$top_info_padding_cssclass.'">
				<div class="nxs-table" style="'.$top_info_title_alignment.'">';
				
					// Icon
					echo $icon;
					
					// Title
					if ($title != "")
					{
						echo $titlehtml;
					}
					echo '
				</div>
			</div>';
		
		} else {
		
			// Default title
			if ($title != "") {
				echo $titlehtml;
			}
		
		}
		
		if (
			($title != "" || $icon != "") && ($image_imageid != "" || $image_url != "") ||
			($title != "" || $icon != "") && $htmltext != "" ||
			($title != "" || $icon != "") && $htmlforbutton != "") { 
			echo $htmlfiller; 
		}
		
		/* Image and filler
		----------------------------------------------------------------------------------------------------*/
		if (nxs_isimageautofit($image_size) && $callout_text != "") {
			echo '<div class="callout-cropper ' . $image_shadow . '">';
			
			if ($callout_text != "") {
				echo '<div class="callout-banner '.$callout_color_cssclass.'"><h'.$callout_heading.'>'.$callout_text.'</h '.$callout_heading.'></div>';
			}
			
			if ($image_imageid != "" || $image_url != "") { echo $image; }
			
			echo '</div>';		
		} else {
			
			if ($image_imageid != "" || $image_url != "") { echo $image; }
			
		}
		
		if (
			($image_imageid != "" || $image_url != "") && $htmltext != "" && $image_size == "auto-fit" ||
			($image_imageid != "" || $image_url != "") && $htmltext == "" && $htmlforbutton != "") { 
			echo $htmlfiller; 
		}
		
		/* Text and filler
		----------------------------------------------------------------------------------------------------*/
		echo $htmltext;
		
		if (
			$htmltext != "" && $htmlforbutton != "") { 
			echo $htmlfiller; 
		}
		
		/* Button
		----------------------------------------------------------------------------------------------------*/
		echo $htmlforbutton; 
		
		echo '<div class="nxs-clear"></div>';
		
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

function nxs_widgets_text_initplaceholderdata($args)
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
	$unistylegroup = nxs_widgets_text_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_text_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
