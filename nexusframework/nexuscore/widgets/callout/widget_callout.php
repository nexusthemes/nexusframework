<?php

nxs_requirewidget("generic");

function nxs_widgets_callout_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_callout_gettitle() {
	return nxs_l18n__("callout", "nxs_td");
}

// 
function nxs_widgets_callout_getunifiedstylinggroup() {
	return "calloutwidget";
}

// Unicontent
function nxs_widgets_callout_getunifiedcontentgroup() {
	return "calloutwidget";
}


/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_callout_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$options = array
	(
		"sheettitle" => nxs_widgets_callout_gettitle(),
		"sheeticonid" => nxs_widgets_callout_geticonid(),
		"sheethelp" => nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1764396204"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_callout_getunifiedstylinggroup(),
		),
		"unifiedcontent" 	=> array 
		(
			"group" => nxs_widgets_callout_getunifiedcontentgroup(),
		),
		"fields" => array
		(
			// -------------------------------------------------------			
			
			// LOOKUPS
			
			array
			( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Lookups", "nxs_td"),
				"initial_toggle_state" => "closed",
			),
			array
      (
				"id" 					=> "lookups",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Lookup table (evaluated one time when the widget renders)", "nxs_td"),
			),
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),
					
			// TITLES
				
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title and subtitle", "nxs_td"),
			),
			
			array
			( 
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Title goes here", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If your callout has an eye-popping title put it here.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array
      (
				"id" 					=> "title_lookuppicker",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_generic_modeltaxfieldpicker_popupcontent",
			),			array(
				"id" 				=> "title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title heading", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),	
			array(
				"id" 				=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Title fontzen", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "subtitle",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Subtitle", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Subtitle goes here", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Place a descriptive teaser, if available, with this option.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array
      (
				"id" 					=> "subtitle_lookuppicker",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_generic_modeltaxfieldpicker_popupcontent",
			),			
			array(
				"id" 				=> "subtitle_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Subtitle heading", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),		
			array(
				"id" 				=> "subtitle_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Subtitle fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "subtitle_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Subtitle fontzen", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "callout_text_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Text wrapper width", "nxs_td"),
				"dropdown" 			=> array(""=>"","90%"=>"90%","80%"=>"80%","70%"=>"70%","60%"=>"60%","50%"=>"50%","40%"=>"40%","30%"=>"30%","20%"=>"20%"),
				"unistylablefield"	=> true
			),
			
			array(
				"id"     			=> "text_padding",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Text wrapper padding", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("padding"),
				"unistylablefield"	=> true
			),
			array(
				"id"     			=> "text_margin",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Text wrapper margin", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("margin"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "bgcolor",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Text wrapper background", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "overlay",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Overlay", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "linear_gradient",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Horizontal Gradient", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" 				=> nxs_l18n__("Select an Option", "nxs_td"),
					"light" => nxs_l18n__("light", "nxs_td"),
					"dark" 	=> nxs_l18n__("dark", "nxs_td"),
				),
				//"tooltip" 			=> nxs_l18n__("This option let's you set the sliders display at a certain viewport and up", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "border_radius",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Text wrapper border radius", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_radius"),
				"unistylablefield"	=> true
			),	
			array(
				"id" 				=> "halign",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Horizontal alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("halign"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "valign", 
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Vertical alignment", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" 	=> nxs_l18n__("Select an Option", "nxs_td"),
					"center"			=>nxs_l18n__("Center", "nxs_td"),
				),
				"unistylablefield"	=> true
			),

			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
			// BUTTON
			
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
				"placeholder" 		=> nxs_l18n__("Button text goes here", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Put a text on the call-to-action button.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),	
			array(
				"id" 				=> "button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button scale", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "button_color",
				"type" 				=> "colorzen",
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
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
			// LINK
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Link", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"unicontentablefield" => true,
				"label" 			=> nxs_l18n__("Button link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the callout button to an article on your site .", "nxs_td")
			),
			array(
				"id" 				=> "destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("External link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the callout button to any source.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array
      (
				"id" 					=> "destination_url_lookuppicker",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_generic_modeltaxfieldpicker_popupcontent",
			),
			array(
				"id" 				=> "destination_js",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Javascript", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Apply javascript when the button is pressed.", "nxs_td"),
				"unicontentablefield" => true,
				"requirecapability" => nxs_cap_getdesigncapability(),
			),			
			array(
				"id" 				=> "destination_target", 
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Target", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" 	=> nxs_l18n__("Select an Option", "nxs_td"),
					"_blank"			=>nxs_l18n__("New window", "nxs_td"),
					"_self"				=>nxs_l18n__("Current window", "nxs_td"),
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
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),

			// BACKGROUND IMAGE
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Background image", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array( 
				"id" 				=> "image_imageid",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Image", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
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
				"id" 					=> "title_lookuppicker",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_generic_modeltaxfieldpicker_popupcontent",
			),            
			array
			(
				"id" 				=> "image_position",
				"type" 				=> "radiobuttons",
				"layout" 			=> "3x3",
				"label" 			=> nxs_l18n__("Image position", "nxs_td"),
				"subtype"			=> "backgroundimage_position",
				"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "image_size",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("backgroundimage_size"),
				"unistylablefield"	=> true
			),
			
			/* Replaced with flex box
			
			array( 
				"id" 				=> "min_height",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Minimum height", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("300px", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("You can set the minimum height of your callout widget. Make sure to end with 'px'.", "nxs_td"),
				"unistylablefield"	=> true
			),*/
			
			array(
				"id" 				=> "flex_box_height",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Height", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("Select an Option", "nxs_td"),
					"nxs-flex-box40" 	=> nxs_l18n__("40%", "nxs_td"),
					"nxs-flex-box50" 	=> nxs_l18n__("50%", "nxs_td"),
					"nxs-flex-box60" 	=> nxs_l18n__("60%", "nxs_td"),
					"nxs-flex-box70" 	=> nxs_l18n__("70%", "nxs_td"),
					"nxs-flex-box80" 	=> nxs_l18n__("80%", "nxs_td"),
					"nxs-flex-box90" 	=> nxs_l18n__("90%", "nxs_td"),
					"nxs-flex-box100" 	=> nxs_l18n__("100%", "nxs_td"),
				),
				"tooltip" 			=> nxs_l18n__("This option let's you set the sliders display at a certain viewport and up", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
			// MOBILE CONFIGURATION
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Mobile configuration", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),			
			
			array(
				"id" 				=> "fixed_font",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Fixed font", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, font sizes will be a fixed size for smaller resolutions", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "responsive_display",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Responsive display", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("Select an Option", "nxs_td"),
					"never" 		=> nxs_l18n__("Never", "nxs_td"),
					"callout480" 	=> nxs_l18n__("480", "nxs_td"),
					"callout720" 	=> nxs_l18n__("720", "nxs_td"),
					"callout960" 	=> nxs_l18n__("960", "nxs_td"),
					"callout1200" 	=> nxs_l18n__("1200", "nxs_td"),
				),
				"tooltip" 			=> nxs_l18n__("This option forces the layout to best suit mobile devices", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
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
				"id" 				=> "subtitle_heightiq",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align subtitles", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's subtitle will participate in the subtitle alignment of other partipating widgets in this row", "nxs_td"),
				"unistylablefield"	=> true
			),
				
			array
			( 
				"id" 				=> "wrapper_misc_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
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

function nxs_widgets_callout_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));

	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	if ($render_behaviour == "code")
	{
		//
		$temp_array = array();
	}
	else
	{
		// Every widget needs it's own unique id for all sorts of purposes
		// The $postid and $placeholderid are used when building the HTML later on
		$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	}
	
	// blend unistyle properties
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "")
	{
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_callout_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);	
	}

	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_callout_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}	
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title","subtitle", "button_text","destination_url", "image_src"));
	
	// Translate model magical fields
	if (true)
	{
		global $nxs_g_modelmanager;
		
		$combined_lookups = nxs_lookups_getcombinedlookups_for_currenturl();
		$combined_lookups = array_merge($combined_lookups, nxs_parse_keyvalues($mixedattributes["lookups"]));
		
		// evaluate the lookups widget values line by line
		$sofar = array();
		foreach ($combined_lookups as $key => $val)
		{
			$sofar[$key] = $val;
			//echo "step 1; processing $key=$val sofar=".json_encode($sofar)."<br />";

			//echo "step 2; about to evaluate lookup tables on; $val<br />";
			// apply the lookup values
			$sofar = nxs_lookups_blendlookupstoitselfrecursively($sofar);

			// apply shortcodes
			$val = $sofar[$key];
			//echo "step 3; result is $val<br />";

			//echo "step 4; about to evaluate shortcode on; $val<br />";

			$val = do_shortcode($val);
			$sofar[$key] = $val;

			//echo "step 5; $key evaluates to $val (after applying shortcodes)<br /><br />";

			$combined_lookups[$key] = $val;
		}
		
		// apply the lookups and shortcodes
		$magicfields = array("title", "subtitle", "button_text", "destination_url", "image_src");
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
	
	global $nxs_global_row_render_statebag;
	$pagerowtemplate = $nxs_global_row_render_statebag["pagerowtemplate"];
	if ($pagerowtemplate == "one")
	{
		$subtitle_heightiq = "";	// off!
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
	nxs_ob_start();
	
	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		global $nxs_global_placeholder_render_statebag;
		
		if ($shouldrenderalternative == true) 
		{
			$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . "-warning ";
		}
		else 
		{
			// Appending custom widget class
			// Responsive display
			if ($responsive_display == "") { $responsive_display = 'callout720'; }
			$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " " . $responsive_display . " " . $flex_box_height;
		}
	}
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;
	if (
		$title == "" &&
		$subtitle == "" &&
		$button_text == ""
	) {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Minimal: title, subtitle or button", "nxs_td");
	}
	
	// if both external and article link are set
	$verifydestinationcount = 0;
	if ($destination_url != "") {
		$verifydestinationcount++;
	} if ($destination_articleid != "") {
		$verifydestinationcount++;
	} if ($destination_js != "") {
		$verifydestinationcount++;
	} if ($verifydestinationcount > 1) {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Button: both external URL and article reference are set (ambiguous URL)", "nxs_td");
	}
	
	// if both external and article link are set
	if ($destination_url == "" && $destination_articleid == "" && $destination_js == "" && $button_text != "") {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Button: button is set, but no reference is set (no URL)", "nxs_td");
	}
	
	// fixed font size
	if ($fixed_font != "") { $fixed_font = 'fixed-font'; }
	
	// horizontal alignment
	if 		($halign == "left") {  
	} 
	else if ($halign == "center") { 
		$hclass = "nxs-justify-content-center";
		$text_align = "nxs-align-center"; 
	} 
	else if ($halign == "right") { 
		$hclass = "nxs-justify-content-flex-end";
		$text_align = "nxs-align-right"; 
	}
	
	// vertical alignment
	if ($valign == "center") { $vclass = "nxs-align-items-center"; }	
	
	// Text width
	
	if 		( $callout_text_width == "" )  { $callout_text_width = "nxs-width100"; }
	else if ( $callout_text_width == "90%" )  { $callout_text_width = "nxs-width90"; }
	else if ( $callout_text_width == "80%" )  { $callout_text_width = "nxs-width80"; }
	else if ( $callout_text_width == "70%" )  { $callout_text_width = "nxs-width70"; }
	else if ( $callout_text_width == "60%" )  { $callout_text_width = "nxs-width60"; }
	else if ( $callout_text_width == "50%" )  { $callout_text_width = "nxs-width50"; }
	else if ( $callout_text_width == "40%" )  { $callout_text_width = "nxs-width40"; }
	else if ( $callout_text_width == "30%" )  { $callout_text_width = "nxs-width30"; }
	else if ( $callout_text_width == "20%" )  { $callout_text_width = "nxs-width20"; }	
	
	if ($text_margin != ""){
		// the "overflow: auto; property is necessary to prevent parent div's to move when a margin is set on the child div
		$overflow = 'overflow: auto;';
	}
	
	// Title heading
	if ($title_heading != "") {
		$title_heading = "h" . $title_heading;	
	} else {
		$title_heading = "h1";
	}
	
	// Title heightiq, size, font
	$heightiqprio = "p1";
	$title_heightiqgroup = "callout-title";
	$title_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $title_fontsize);
	$title_fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen-", $title_fontzen);
	
	$cssclasses = nxs_concatenateargswithspaces("nxs-title", $title_fontsize_cssclass, $title_fontzen_cssclass/*, "nxs-heightiq", "nxs-heightiq-".$heightiqprio."-".$title_heightiqgroup*/);
	
	// Title
	$a = array
	(
		"title" => $title,
		"heading" => $title_heading,
		"align" => $title_alignment,
		"fontsize" => $title_fontsize,
		"heightiq" => $title_heightiq,
		// ------
		// IMPORTANT; these links should NOT be set; in call out widgets the titles are NOT links (oposed to text widgets).
		// if it would, the links colors would apply (see the issue reported by Kacem; 
		// [13/09/2017, 13:43:57] Kacem TALEB: i have another customer complaining on the link color issue that i raised last time
		// as can be seen in https://www.laforge45.org
		// [13/09/2017, 13:44:08] Kacem TALEB: at the call out widget
		// "destination_articleid" => $destination_articleid,
		// "destination_url" => $destination_url,
		// "destination_target" => $destination_target,
		// "destination_relation" => $destination_relation,
		// -----
		"shouldapplylinkvarcolor" => false,		
		"microdata" => $microdata,
		"colorzen" => $derived_colorzen,
		// 
	);
	$htmltitle = nxs_gethtmlfortitle_v4($a);
	
	// Subtitle heading
	if ($subtitle_heading != "") 
	{
		$subtitle_heading = "h" . $subtitle_heading;	
	} 
	else 
	{
		$subtitle_heading = "h1";
	}
	
	// Subtitle size, font
	$subtitle_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $subtitle_fontsize);
	$subtitle_fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen-", $subtitle_fontzen);
	$cssclasses = nxs_concatenateargswithspaces("nxs-title", "nxs-subtitle", $subtitle_fontsize_cssclass, $subtitle_fontzen_cssclass);
	
	if ($subtitle_heightiq != "") 
	{
		$heightiqprio = "p1";
		$text_heightiqgroup = "callout-subtitle";
		$cssclasses = nxs_concatenateargswithspaces($cssclasses, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$text_heightiqgroup}");
	}
	
	// Subitle
	$htmlsubtitle = '<'.$subtitle_heading.' class="' . $cssclasses .'">'.$subtitle.'</'.$subtitle_heading.'>';	
	
	// Image metadata
	if ($image_imageid != "") 
	{
		$imagemetadata= nxs_wp_get_attachment_image_src($image_imageid, 'full', true);
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$imageurl 		= $imagemetadata[0];
		$imageurl = nxs_img_getimageurlthemeversion($imageurl);

		$imagewidth 	= $imagemetadata[1] . "px";
		$imageheight 	= $imagemetadata[2] . "px";			
	}
	if ($image_src != "")
	{
		$imageurl = $image_src;
	}
  if ($imageurl != "" ) 
  {
	  if(!$image_size)
	  { 
  		// for old sites, that did not supported the image alignment at that time
      $image_size = "cover";
	  }
	  
	  if(!$image_position)
	  { 
  		// for old sites, that did not supported the image alignment at that time
      if($image_vpos == "top"){
          $image_position = "center top";
      } elseif($image_vpos == "center"){
          $image_position = "center center";
      } elseif($image_vpos == "bottom"){
          $image_position = "center bottom";
      } else {
          $image_position = "center center";
      }
	  }
	
	  if($image_size == "-"){ // if image size is not set, the image will be 'auto', which is as much as 'cover'
	      $image_size = "auto";
	  }
	  
	  $image_background = 'background: url('.$imageurl.') no-repeat ' . $image_position . '; background-size: '.$image_size.';';
	}
	
	// Text padding and margin
	$text_padding_cssclass = nxs_getcssclassesforlookup("nxs-padding-", $widgetmetadata["text_padding"]);
	$text_margin_cssclass = nxs_getcssclassesforlookup("nxs-margin-", $text_margin);
	
	// Border radius
	//if ($border_radius == "") { $border_radius = '2-0'; }
	$border_radius_cssclass = nxs_getcssclassesforlookup("nxs-border-radius-", $border_radius);
	
	// Background Color
	//if ($bgcolor == "") { $bgcolor = 'base2-a0-6'; }
	$bgcolor_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $bgcolor);
	
	// Overlay
	$overlay_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $overlay);
	
	// Min-height
	if ($min_height != "") { $min_height = "min-height: " . $min_height . ";"; }
	
	// Filler
	$htmlfiller = '<div class="nxs-clear nxs-filler"></div>';
	
	// Linear Gradient
	if 		( $linear_gradient == "light" && $halign == "left" )  { $linear_gradient_cssclass = "nxs-linear-leftright-light"; }
	else if ( $linear_gradient == "dark" && $halign == "left" )   { $linear_gradient_cssclass = "nxs-linear-leftright-dark"; }
	else if ( $linear_gradient == "light" && $halign == "right" ) { $linear_gradient_cssclass = "nxs-linear-rightleft-light"; }
	else if ( $linear_gradient == "dark" && $halign == "right" )  { $linear_gradient_cssclass = "nxs-linear-rightleft-dark"; }
	  
	
	
	/* LINK
	---------------------------------------------------------------------------------------------------- */
	if ($destination_articleid != "") {
		$url = nxs_geturl_for_postid($destination_articleid);
		$onclick = "";
	} else if ($destination_url != "") {
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
		$onclick = "";
	} else if ($destination_js != "") {
		$url = "#";
		$onclick = "onclick='" . nxs_render_html_escape_singlequote($destination_js) . "' ";
	} else {
		// unsupported
		$url = "";
		$onclick = "";
	}
	
	// Onclick
	if ($onclick != "") {
		$onclick = " " . $onclick . " ";
 	}
 
 	if ($destination_target == "@@@empty@@@" || $destination_target == "") {
 		// auto
 		if ($destination_articleid != "") {
 			// local link = self
 			$destination_target = "_self";
 		} else {
 			$homeurl = nxs_geturl_home();
 			if (nxs_stringstartswith($url, $homeurl)) {
 				$destination_target = "_self";
 			} else {
 				$destination_target = "_blank";
 			}
 		}
 	}
 	
	// Link blank vs. self
	if ($destination_target == "_self") {
 		$destination_target = "_self";
 	} else if ($destination_target == "_blank") {
 		$destination_target = "_blank";
 	} else {
 		$destination_target = "_self";
	}

	$destination_relation_html = '';
	if ($destination_relation == "nofollow") {
		$destination_relation_html = 'rel="nofollow"';
	}
	
	// Button
	if ($url != "" && $button_text != "")
	{
		$button_alignment = nxs_getcssclassesforlookup("nxs-align-", $button_alignment);
		$button_color = nxs_getcssclassesforlookup("nxs-colorzen-", $button_color);
		$button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $button_scale);
		$button_fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen-", $button_fontzen);
		
		
		/*
		$htmlbutton = '
		
		<p class="' . $button_alignment . ' nxs-padding-bottom0">
			<span class="nxs-button ' . $button_scale_cssclass . ' ' . $button_color . ' ' . $button_fontzen_cssclass . '">' . $button_text . '</span>
		</p>';
		*/
		
		$destination_js_escaped = esc_attr($destination_js);
		$x = "[nxs_button text='{$button_text}' destination_articleid='{$destination_articleid}' destination_url='{$destination_url}' destination_js='{$destination_js_escaped}' destination_target='{$destination_target}' colorzen='{$button_color}' scale='{$button_scale}']";
		$htmlbutton = do_shortcode($x);
	}

	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) 
	{
		if ($alternativehint == "")
		{
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} 
	else
	{
		/*
		if ($url != "")
		{
			echo '<a target="'.$destination_target.'" '.$destination_relation_html.' '.$onclick.' href="' . $url . '">';
		}
		*/
		
		if (true)
		{
			nxs_ob_start();
			
			if ($title != "") 		{echo $htmltitle;}
			
			if ($title != "" && $subtitle != "") {
				echo '<div class="nxs-clear nxs-filler"></div>';
			}
			
			if ($subtitle != "")	{echo $htmlsubtitle;}
			
			if (($title != "" && $htmlbutton != "") || ($subtitle != "" && $htmlbutton != "")) {
				echo '<div class="nxs-clear nxs-filler"></div>';
			}
			
			if ($url != "")		{echo $htmlbutton;}
		
			echo '<div class="nxs-clear"></div>';
	
			$innercontent = nxs_ob_get_contents();
			nxs_ob_end_clean();
		}
		
		echo '
		<div class="'.$fixed_font.'" style="'.$image_background.' '.$overflow.' '.$min_height.'">
			<div class="nxs-flex '.$hclass.' '.$flex_box_height.' '.$overlay_cssclass.'">
				<div class="gradient-wrapper nxs-flex '.$vclass.' '.$hclass.' '.$flex_box_height.' '.$text_align.' '.$linear_gradient_cssclass.' '.$callout_text_width.'">';
					
					$padding = $widgetmetadata["text_padding"];
					$margin = $widgetmetadata["text_margin"];
					$bgcolor = $widgetmetadata["bgcolor"];
					$border_radius = $widgetmetadata["border_radius"];
					
					echo do_shortcode("[nxs_wrap border_radius='{$border_radius}' colorzen='{$bgcolor}' padding='{$padding}' margin='{$margin}']{$innercontent}[/nxs_wrap]");
					
				echo '</div>
			</div>
			<div class="nxs-clear"></div>
		</div>';
		
		/*
		if ($url != ""){
			echo '</a>';
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

function nxs_widgets_callout_initplaceholderdata($args)
{
	extract($args);

  $args['image_position'] = "left top";
  $args['image_size'] = "cover";
	$args['button_color'] = "base2";
	$args['title_heading'] = "1";
	$args['subtitle_heading'] = "2";
	$args['halign'] = "center";
	$args['button_scale'] = "2-0";
	
	$args['subtitle_heightiq'] = "true";

	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_callout_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_callout_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}
