<?php

nxs_requirewidget("generic");

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
		"supporturl" => "https://www.wpsupporthelp.com/wordpress-questions/text-widget-wordpress-questions-176/",
		"unifiedstyling" 	=> array("group" => nxs_widgets_text_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_text_getunifiedcontentgroup(),),
		"fields" => array
		(
			// -------------------------------------------------------			
			
			// LOOKUPS
			
			array
			( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "ext_loopups_wrapperbegin",
				"label" 			=> nxs_l18n__("Lookups", "nxs_td"),
				"initial_toggle_state"	=> "closed-if-empty",
				"initial_toggle_state_id" => "lookups",
			),
			array
      (
				"id" 					=> "lookups",
				"type" 				=> "ext_loopups_textarea",
				"label" 			=> nxs_l18n__("Lookup table (evaluated one time when the widget renders)", "nxs_td"),
			),
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "ext_loopups_wrapperend"
			),
			
			// TITLE
			
			array
			( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"initial_toggle_state"	=> "closed-if-empty",
				"initial_toggle_state_id" => "title",
			),
			array(
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Title goes here", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array
      (
				"id" 					=> "title_lookuppicker",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_generic_modeltaxfieldpicker_popupcontent",
			),
			/* phased out; use shortcodes instead
			array
			(
				"id" 				=> "title_postprocessor",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title max length", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@empty@@@" => "None",
					"truncateall" => "Truncate all",
				),
				"unistylablefield"	=> true
			),
			*/
			array
			(
				"id" 				=> "title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title heading markup", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Title font", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
				"unistylablefield"	=> true,
				"mobile_action_toggles" => ".nxs-viewport-dependent",
			),
			array(
				"id" 				=> "title_alignment_tablet",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("", "nxs_td"),
				"unistylablefield"	=> true,
				"display" => "noneifempty",
				"fortablets" => true,
				"enable_deselect" => true,
			),
			array(
				"id" 				=> "title_alignment_mobile",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("", "nxs_td"),
				"unistylablefield"	=> true,
				"display" => "noneifempty",
				"formobiles" => true,
				"enable_deselect" => true,
			),
			array(
				"id" 				=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "top_info_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Title background color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id"     			=> "top_info_padding",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Title padding", "nxs_td"),
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
				"label"    			=> nxs_l18n__("Icon size", "nxs_td"),
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
			array
      (
				"id" 					=> "text_lookuppicker",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_generic_modeltaxfieldpicker_popupcontent",
			),		
			/* obsolete; if this is needed, use shortcodes and lookups instead
			array(
				"id" 				=> "text_truncatelength",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Text max length", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@empty@@@" => "No truncation",
					"none" => "Truncate all",
				),
				"unistylablefield"	=> true
			),
			*/
			array(
				"id" 				=> "text_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Text font", "nxs_td"),
				"unistylablefield"	=> true
			),			
			array(
				"id" 				=> "text_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
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
			array
			( 
				"id" 				=> "image_src",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Image src", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to reference an external image, use this field.", "nxs_td"),
				"unicontentablefield" => true,
			),				
			array
      (
				"id" 					=> "image_src_lookuppicker",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_generic_modeltaxfieldpicker_popupcontent",
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
				"id" 				=> "button_title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Button hover text", "nxs_td"),
				"placeholder"		=> "",
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
				"label" 			=> nxs_l18n__("Button font", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "button_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
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
				"posttypes" => array("page", "post", "nxs_templatepart"),
				"label" 			=> nxs_l18n__("Link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an article within your site.", "nxs_td"),
				"unicontentablefield" => true,
			),
						
			array(
				"id" 				=> "destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Link (url)", "nxs_td"),
				"placeholder"		=> nxs_l18n__("https://www.example.org", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an external source using the full url.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			
			array
			( 
				"id" 				=> "destination_data",
				"type" 				=> "ext_text_destination_data_input",
				"label" 			=> nxs_l18n__("Link (programmatic)", "nxs_td"),
				"unicontentablefield" => true,
			),
			
			array
      (
				"id" 					=> "destination_url_lookuppicker",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_generic_modeltaxfieldpicker_popupcontent",
			),
			
			array(
				"id" 				=> "destination_js",
				"type" 				=> "ext_text_destination_js_input",
				"label" 			=> nxs_l18n__("Javascript", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Apply javascript when the button is pressed.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true,
				"requirecapability" => nxs_cap_getdesigncapability(),
			),
		
			array(
				"id" 				=> "destination_target",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Target (where to open the linked document)", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@empty@@@"=>nxs_l18n__("Auto", "nxs_td"),
					"_blank"=>nxs_l18n__("New window", "nxs_td"),
					"_self"=>nxs_l18n__("Current window", "nxs_td"),
					"_nxspopup"=>nxs_l18n__("Popup window", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "destination_relation", 
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Link relation", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("link_relation"),
			),	
			
			array( 
				"id" 				=> "wrapper_end_link",
				"type" 				=> "wrapperend",
			),
			
			// MISCELLANEOUS
			
			array( 
				"id" 				=> "wrapper_misc_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Advanced", "nxs_td"),
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
			/* phased out; too confusing
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
			*/
			
			
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
				"id" 				=> "wrapper_misc_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),
			
			/*
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
			*/
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
		$temp_array = $args;
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
	$mixedattributes = array_merge($temp_array, $args);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title","text","button_text", "destination_url"));
	
	// Translate model magical fields
	if (true)
	{
		global $nxs_g_modelmanager;
		
		$combined_lookups = nxs_lookups_getcombinedlookups_for_currenturl();
		$combined_lookups = array_merge($combined_lookups, nxs_parse_keyvalues($mixedattributes["lookups"]));
		$combined_lookups = nxs_lookups_evaluate_linebyline($combined_lookups);
		
		// replace values in mixedattributes with the lookup dictionary
		$magicfields = array("title", "text", "button_text", "destination_url", "image_src", "destination_data");
		$translateargs = array
		(
			"lookup" => $combined_lookups,
			"items" => $mixedattributes,
			"fields" => $magicfields,
		);
		$mixedattributes = nxs_filter_translate_v2($translateargs);
	}
	
	// allow plugins to decorate (and also do something with) the mixedattributes 
	// (an example of "doing something" would be for example to apply QA rules)
	$filterargs = array
	(
		"mixedattributes" => $mixedattributes
	);
	$mixedattributes = apply_filters("nxs_f_widgetvisualizationdecorateatts", $mixedattributes, $filterargs);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
	if ($title_postprocessor === "truncateall") 
	{
		$title = "";
	}
	
	// Overruling of parameters
	if ($image_imageid == "featuredimg")
	{
		$image_imageid = get_post_thumbnail_id($containerpostid);
	}
	
	if ($media != "")
	{
		$width = "300";
		$height = "300";
		
		// media_meta = "w:300;h:100";
		$metapieces = explode(";", $media_meta);
		foreach ($metapieces as $metapiece)
		{
			// metapiece = "w:300";
			$subpieces = explode(":", $metapiece);
			if ($subpieces[0] == "w")
			{
				$width = $subpieces[1];
			}
			else if ($subpieces[0] == "h")
			{
				$height = $subpieces[1];
			}
		}
		
		//$image_src = "https://mediamanager.websitesexamples.com/?nxs_imagecropper=true&requestedwidth={$width}&requestedheight={$height}&debug=tru&url={$media}&scope=lazydetect";
		//error_log("text img; $image_src");
		
		$image_src = "https://d3mwusvabcs8z9.cloudfront.net/?nxs_imagecropper=true&requestedwidth={$width}&requestedheight={$height}&debug=tru&url={$media}&scope=lazydetect";
	}
	
	global $nxs_global_row_render_statebag;
	$pagerowtemplate = $nxs_global_row_render_statebag["pagerowtemplate"];
	if ($pagerowtemplate == "one")
	{
		$text_heightiq = "";	// off!
	}

	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		$hovermenuargs = array();
		$hovermenuargs["postid"] = $postid;
		$hovermenuargs["placeholderid"] = $placeholderid;
		$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
		$hovermenuargs["metadata"] = $mixedattributes;
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	}

	// Turn on output buffering
	nxs_ob_start();
	
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
	
	if ($image_imageid != "" && $image_src != "")
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Ambiguous: not clear which image to use (you picked an image, and entered the image url at the same time). Please only use one at a time.", "nxs_td");
	}
	
	if ($callout_text == "test") { $callout_text = ""; }
	
	if (
		($image_imageid == "" && $callout_text != "") ||
		($image_size != nxs_isimageautofit($image_size) && $callout_text != "")
	) {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("You need to upload an image with a 'stretch' configuration for the callout banner to be shown.", "nxs_td");
	}
	
	if (
		$image_imageid == "" &&
		$image_src == "" &&
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
	
	if ($destination_data != "")
	{
		$verifydestinationcount++;
	}
	
	if ($destination_articleid != "") 
	{
		$verifydestinationcount++;
	}
	
	if ($destination_js != "") 
	{
		$verifydestinationcount++;
	}
	
	if ($verifydestinationcount > 1) 
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Button: multiple link destinations are set; please only configure one", "nxs_td");
	}
	
	if ($button_text != "" && $destination_url == "" && $destination_articleid == "" && $destination_popuparticleid == "" && $destination_js == "" && $destination_data == "") 
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Button: button is set, but no destination is set (no URL)", "nxs_td");
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
	
	// prevent users from entering <script> tags in the html source
	if (nxs_stringcontains_v2($text, "<script", true))
	{
		$text = str_replace("<", "&lt;", $text);
		$text = str_replace(">", "&gt;", $text);
	}

	// get html for each part	
	$htmltext = nxs_gethtmlfortext($text, $text_alignment, $text_showliftnote, $text_showdropcap, $wrappingelement, $text_heightiq, $text_fontzen, $ph_text_fontsize);
	
	if ($text_postprocessor === "truncateall") 
	{
		$htmltext = "";
	}
	
	$button_heightiq = "";
	
	//
	// the image_data field is already evaluated by the magic field if we end up here
	if ($destination_data != "")
	{
		if (is_numeric($destination_data))
		{
			$destination_articleid = $destination_data;
		}
		else
		{
			$destination_url = $destination_data;
		}
	}
	
	$buttonargs = array
	(
		"title" => $button_title,
		"text" => $button_text,
		"scale" => $button_scale,
		"colorzen" => $button_color,
		"destination_articleid" => $destination_articleid,
		"destination_popuparticleid" => $destination_popuparticleid,
		"destination_url" => $destination_url,
		"destination_target" => $destination_target,
		"align" => $button_alignment,
		"destination_js" => $destination_js,
		"text_heightiq" => $text_heightiq,
		"fontzen" => $button_fontzen,
		"destination_relation" => $destination_relation
	);
	$htmlforbutton = nxs_gethtmlforbutton_v2($buttonargs);
	
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
	
	// apply shortcode on the title
	$title = do_shortcode($title);
	
	// Title
	$titlehtml = "<{$title_heading} {$title_schemaorg_attribute} class='{$concatenatedcssclasses}'>{$title}</{$title_heading}>";
	
	// new implementation delegates rendering of the title to the frontendframework
	
	
	
	if ($destination_target == "_self") 
	{
		$destination_target_html = 'target="_self"';
	} 
	else if ($destination_target == "_blank") 
	{
		$destination_target_html = 'target="_blank"';
	} 
	else 
	{
		if ($destination_articleid != "") 
		{
			$destination_target_html = 'target="_self"';
		} 
		else 
		{
			$homeurl = nxs_geturl_home();
 			if (nxs_stringstartswith($destination_url, $homeurl)) 
 			{
 				$destination_target_html = 'target="_self"';
 			} 
 			else 
 			{
 				$destination_target_html = 'target="_blank"';
 			}
		}
	}

	$destination_relation_html = '';
	if ($destination_relation == "nofollow") {
		$destination_relation_html = 'rel="nofollow"';
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
	if ($destination_articleid != "") 
	{
		$titlehtml = '<a '.$destination_target_html.' '.$destination_relation_html.' href="'.$destination_url .'">'.$titlehtml.'</a>';
	} 
	else if ($destination_url != "") 
	{
		$titlehtml = '<a '.$destination_target_html.' '.$destination_relation_html.' href="'.$destination_url .'">'.$titlehtml.'</a>';
	}
	
	$shouldapplylinkvarcolor = false;
	
	// Applying link colors to title
	if ($top_info_color_cssclass == "") 
	{
		// applicable here; http://www.sylviedeloge.fr/identite-visuelle/
		$shouldapplylinkvarcolor = true; 
	}
	
	
	
	// new implementation delegates rendering the title to the frontendframework
	$a = array
	(
		"title" => $title,
		"heading" => $title_heading,
		"align" => $title_alignment,
		"align_tablet" => $title_alignment_tablet,
		"align_mobile" => $title_alignment_mobile,
		"fontsize" => $title_fontsize,
		"heightiq" => "title",
		"destination_articleid" => $destination_articleid,
		"destination_url" => $destination_url,
		"destination_target" => $destination_target,
		"destination_relation" => $destination_relation,
		"shouldapplylinkvarcolor" => $shouldapplylinkvarcolor,
		// "microdata" => 
		"fontzen" => $title_fontzen,
	);
	$titlehtml = nxs_gethtmlfortitle_v4($a);
	
	/* IMAGE
	---------------------------------------------------------------------------------------------------- */
	
	// Image properties
	$derived_imageurl = "";	// none
	if ($image_imageid != "" && $image_size != "-") {
		// Determines which image size, full or thumbnail, should be used    
		$wpsize = nxs_getwpimagesize($image_size);
		$imagemetadata= nxs_wp_get_attachment_image_src($image_imageid, $wpsize, true);
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$derived_imageurl = $imagemetadata[0];
		$derived_imageurl = nxs_img_getimageurlthemeversion($derived_imageurl);
	} else if ($image_src != "" && $image_size != "-") {
		// Determines which image size, full or thumbnail, should be used    
		$wpsize = nxs_getwpimagesize($image_size);
		$derived_imageurl = $image_src;
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
	
	if ($image_alt == "" && $image_imageid != 0)
	{
		// fallback; use the alt text as specified in the media manager
		$image_alt = get_post_meta($image_imageid, '_wp_attachment_image_alt', true);
	}
	
	// Image with border functionality
	$image = '
		<div class="nxs-image-wrapper '.$image_shadow.' '.$image_size_cssclass.' '.$image_alignment_cssclass.' '.'">
			<div style="right: 0; left: 0; top: 0; bottom: 0; border-style: solid;" class="'.$image_border_width.' nxs-overflow">
				<img src="'.$derived_imageurl.'" alt="'.$image_alt.'" title="'.$image_title.'" class="'.$enlarge.' '.$grayscale.'" />
			</div>
		</div>';
	
	// Linked image
	if ($destination_articleid != "") 
	{
		$image = '<a '.$destination_target_html.' '.$destination_relation_html.' href="'.$destination_url .'">'.$image.'</a>';
	} 
	else if ($destination_url != "") 
	{
		$image = '<a '.$destination_target_html.' '.$destination_relation_html.' href="'.$destination_url .'">'.$image.'</a>';
	}
	
	// Default image 
	$default_image = '<img src="'.$derived_imageurl.'" alt="'.$image_alt.'" title="'.$image_title.'" class="'.$enlarge.' '.$image_size_cssclass.' '.$image_alignment_cssclass.'"/>';
	
	if ($image_src != "" && $image_size != "auto-fit") {
		$default_image = '';
		$default_image .= '<div class="nxs-image-wrapper ' . $image_alignment_cssclass . ' ' . $image_size_cssclass . '" style="background: url('. $derived_imageurl . '); background-repeat:no-repeat; background-attachment:scroll; background-position:center; background-size: cover;"><img style="visibility: hidden;" src="'. $derived_imageurl . '" />&nbsp;';
		$default_image .= '</div>';
	}
	
	// Linked default image
	if ($destination_articleid != "") 
	{
		$default_image = '<a '.$destination_target_html.' '.$destination_relation_html.' href="'.$destination_url .'">'.$default_image.'</a>';
	} 
	else if ($destination_url != "") 
	{
		$default_image = '<a '.$destination_target_html.' '.$destination_relation_html.' href="'.$destination_url .'">'.$default_image.'</a>';
	}
	
	if ($image_border_width != "") {
		$image = $image; 
	} else {
		$image = $default_image; 
	}
	
	if ($image_size == "-")
	{
		// indicates "none"
		$image = "";
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
		
		$shouldrenderfiller = false;
		if (($title != "" || $icon != "") && ($image_imageid != "" || $image_src != "") && $image_size != "-")
		{
			$shouldrenderfiller = true;
		}
		else if (($title != "" || $icon != "") && $htmltext != "")
		{
			$shouldrenderfiller = true;
		}
		else if (($title != "" || $icon != "") && $htmlforbutton != "")
		{
			$shouldrenderfiller = true;
		}
		
		if ($shouldrenderfiller)
		{ 
			echo $htmlfiller; 
		}
		
		/*
        if($text_heightiq != ""){
            echo '<div class="nxs-heightiq nxs-heightiq-p1-text">';
        }
        */
        
		/* Image and filler
		----------------------------------------------------------------------------------------------------*/
		if (nxs_isimageautofit($image_size) && $callout_text != "") {
			echo '<div class="callout-cropper ' . $image_shadow . '">';
			
			if ($callout_text != "") {
				echo '<div class="callout-banner '.$callout_color_cssclass.'"><h'.$callout_heading.'>'.$callout_text.'</h '.$callout_heading.'></div>';
			}
			
			if ($image_imageid != "" || $image_src != "") { echo $image; }
			
			echo '</div>';		
		} else {
			
			if ($image_imageid != "" || $image_src != "") { echo $image; }
			
		}
		
		if (
			($image_imageid != "" || $image_src != "") && $htmltext != "" && $image_size == "auto-fit" ||
			($image_imageid != "" || $image_src != "") && $htmltext == "" && $htmlforbutton != "") 
		{
			if ($image_size != "-")
			{ 
				echo $htmlfiller; 
			}
			else
			{
				// indicates "no" image, so also "no" filler
			}
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
        
        /*
        if($text_heightiq != ""){
            echo '</div>';
        }
        */
		
	}
	
	/* ------------------------------------------------------------------------------------------------- */
	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
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

function nxs_dataprotection_nexusframework_widget_text_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("widget-none");
}