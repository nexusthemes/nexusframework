<?php

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
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/callout-widget/"),	
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
			// TITLES
				
			array( 
				"id" 				=> "wrapper_title_begin",
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
			array(
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
				"label" 			=> nxs_l18n__("Callout text width", "nxs_td"),
				"dropdown" 			=> array(""=>"","90%"=>"90%","80%"=>"80%","70%"=>"70%","60%"=>"60%","50%"=>"50%","40%"=>"40%","30%"=>"30%","20%"=>"20%"),
				"unistylablefield"	=> true
			),	
			array(
				"id" 				=> "halign",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Horizontal alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("halign"),
				"unistylablefield"	=> true
			),

			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),
			
			// BUTTON
			
			array( 
				"id" 				=> "wrapper_action_begin",
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
					"@@@empty@@@"=>nxs_l18n__("Auto", "nxs_td"),
					"_blank"=>nxs_l18n__("New window", "nxs_td"),
					"_self"=>nxs_l18n__("Current window", "nxs_td"),
				),
				"unistylablefield"	=> true
			),			
			array( 
				"id" 				=> "wrapper_action_end",
				"type" 				=> "wrapperend"
			),

			// BACKGROUND IMAGE
			
			array( 
				"id" 				=> "wrapper_input_begin",
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
			
			array( 
				"id" 				=> "image_vpos",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Vertical position", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@empty@@@"=>nxs_l18n__("Auto", "nxs_td"),
					"top"=>nxs_l18n__("Top", "nxs_td"),
					"center"=>nxs_l18n__("Center", "nxs_td"),
					"bottom"=>nxs_l18n__("Bottom", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "min_height",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Minimum height", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("300px", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("You can set the minimum height of your callout widget. Make sure to end with 'px'.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id"     			=> "text_padding",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Text padding", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("padding"),
				"unistylablefield"	=> true
			),
			array(
				"id"     			=> "text_margin",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Text margin", "nxs_td"),
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
				"id" 				=> "border_radius",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Border radius example", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_radius"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "fixed_font",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Fixed font", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, font sizes will be a fixed size for smaller resolutions", "nxs_td"),
				"unistylablefield"	=> true
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
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
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
	
	// Localize atts
	$mixedattributes = nxs_localization_localize($mixedattributes);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title","subtitle", "button_text","destination_url"));	
	
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
	
	// fixed font size
	if ($fixed_font != "") { $fixed_font = 'fixed-font'; }
	
	// horizontal alignment
	if 		($halign == "left") {  
	} 
	else if ($halign == "center") { 
		$hclass = "nxs-align-center";	
		$center = "margin-left: auto; margin-right: auto;"; 
	} 
	else if ($halign == "right") { 
		$float = "nxs-float-right";
	}
	
	// Text width
	if ($callout_text_width != ""){
		$callout_text_width = 'width: '.$callout_text_width.';';
	}
	
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
	
	$cssclasses = nxs_concatenateargswithspaces("nxs-title", $title_fontsize_cssclass, $title_fontzen_cssclass, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$title_heightiqgroup}");
	
	// Title
	$htmltitle = '<'.$title_heading.' class="'.$cssclasses.'">'.$title.'</'.$title_heading.'>';
	
	// Subtitle heading
	if ($subtitle_heading != "") {
		$subtitle_heading = "h" . $subtitle_heading;	
	} else {
		$subtitle_heading = "h1";
	}
	
	// Subtitle heightiq, size, font
	$heightiqprio = "p1";
	$subtitle_heightiqgroup = "callout-subtitle";
	$subtitle_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $subtitle_fontsize);
	$subtitle_fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen-", $subtitle_fontzen);
	$cssclasses = nxs_concatenateargswithspaces("nxs-title", "nxs-subtitle", $subtitle_fontsize_cssclass, $subtitle_fontzen_cssclass, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$subtitle_heightiqgroup}");
	
	// Subitle
	$htmlsubtitle = '<'.$subtitle_heading.' class="' . $cssclasses .'">'.$subtitle.'</'.$subtitle_heading.'>';	
	
	// Image metadata
	if ($image_imageid != "") {
		$imagemetadata= wp_get_attachment_image_src($image_imageid, 'full', true);
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$imageurl 		= $imagemetadata[0];
		$imagewidth 	= $imagemetadata[1] . "px";
		$imageheight 	= $imagemetadata[2] . "px";	
		
		$verticalalignmentattribute = "top";	// defaults to "top"
		if ($image_vpos == "top")
		{
			$verticalalignmentattribute = "top";
		}
		else if ($image_vpos == "center")
		{
			$verticalalignmentattribute = "center";
		}
		else if ($image_vpos == "bottom")
		{
			$verticalalignmentattribute = "bottom";
		}
		
		$image_background = 'background: url('.$imageurl.') no-repeat ' . $verticalalignmentattribute . ' center;';
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
	
	// Min-height
	if ($min_height != "") { $min_height = "min-height: " . $min_height . ";"; }
	
	// Filler
	$htmlfiller = '<div class="nxs-clear nxs-filler"></div>';
	
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
	
	// if both external and article link are set
	if ($url != "" && $button_text == "") {
		$button_text = "Place button text here";
	}
	
	// Button
	if ($url != ""){
		$button_alignment = nxs_getcssclassesforlookup("nxs-align-", $button_alignment);
		$button_color = nxs_getcssclassesforlookup("nxs-colorzen-", $button_color);
		$button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $button_scale);
		$button_fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen-", $button_fontzen);
		
		$htmlbutton = '
		<p class="' . $button_alignment . ' nxs-padding-bottom0">
			<span class="nxs-button ' . $button_scale_cssclass . ' ' . $button_color . ' ' . $button_fontzen_cssclass . '">' . $button_text . '</span>
		</p>';
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
		if ($url != ""){
			echo '<a target="'.$destination_target.'" '.$onclick.' href="' . $url . '">';
		}
		
			echo '
			<div class="image-background '.$hclass.' '.$fixed_font.'" style="'.$image_background.' '.$overflow.' '.$min_height.'">
				<div class="text-wrapper '.$bgcolor_cssclass.' '.$text_padding_cssclass.' '.$text_margin_cssclass.' '.$border_radius_cssclass.' '.$float.'" style="'.$callout_text_width.' '.$center.'">';
					
					if ($title != "") 		{echo $htmltitle;}
					
					if ($title != "" && $subtitle != "") {
						echo '<div class="nxs-clear nxs-filler"></div>';
					}
					
					if ($subtitle != "")	{echo $htmlsubtitle;}
					
					if (($title != "" && $htmlbutton != "") || ($subtitle != "" && $htmlbutton != "")) {
						echo '<div class="nxs-clear nxs-filler"></div>';
					}
					
					if ($url != "")		{echo $htmlbutton;}
					
					echo '
					<div class="nxs-clear"></div>
				</div>
				<div class="nxs-clear"></div>
			</div>';
		
		if ($destination_target != ""){
			echo '</a>';
		}
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

function nxs_widgets_callout_initplaceholderdata($args)
{
	extract($args);

	$args['button_color'] = "base2";
	$args['title_heading'] = "1";
	$args['subtitle_heading'] = "2";
	$args['halign'] = "center";
	$args['button_scale'] = "2-0";

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

?>
