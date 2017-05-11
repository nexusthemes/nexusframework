<?php

function nxs_widgets_target_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_target_gettitle() {
	return nxs_l18n__("Target", "nxs_td");
}

// Unistyle
function nxs_widgets_target_getunifiedstylinggroup() {
	return "targetwidget";
}

// Unicontent
function nxs_widgets_target_getunifiedcontentgroup() {
	return "targetwidget";
}
/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_target_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_target_gettitle(),
		"sheeticonid" 		=> nxs_widgets_target_geticonid(),
		"unifiedstyling" 	=> array("group" => nxs_widgets_target_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_target_getunifiedcontentgroup(),),
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
				"id" 				=> "title_alignment",
				"type" 				=> "radiobuttons",
				"subtype"  			=> "halign",
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
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "text_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Text alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
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
				"unicontentablefield" => true,
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
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Button alignment", "nxs_td"),
				"unistylablefield"	=> true,
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
			
			/* ICON
			---------------------------------------------------------------------------------------------------- */
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Icon", "nxs_td"),
			),
			
			array(
				"id" 				=> "icon",
				"type" 				=> "icon",
				"label" 			=> nxs_l18n__("Icon", "nxs_td"),
				"unicontentablefield" => true,
			),
			
			
			array
			( 
				"id" 				=> "icon_imageid",
				"type" 				=> "image",
				"allow_featuredimage" => true,
				"label" 			=> nxs_l18n__("Choose image", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to use a custom SVG file, select it here.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			
			array( 
				"id" 				=> "bgcolor",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Icon background color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "border_radius",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Icon background border radius", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_radius"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "icon_size",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Icon size", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@empty@@@"	=>nxs_l18n__("Auto", "nxs_td"),
					"1-0"			=>nxs_l18n__("1x", "nxs_td"),
					"2-0"			=>nxs_l18n__("2x", "nxs_td"),
					"3-0"			=>nxs_l18n__("3x", "nxs_td"),
					"4-0"			=>nxs_l18n__("4x", "nxs_td"),
					"5-0"			=>nxs_l18n__("5x", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "layout",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Layout", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@empty@@@"		=>nxs_l18n__("Auto", "nxs_td"),
					"default"			=>nxs_l18n__("default", "nxs_td"),
					"icon-top-left"		=>nxs_l18n__("icon top left", "nxs_td"),
					"icon-top-center"	=>nxs_l18n__("icon top center", "nxs_td"),
					"icon-top"			=>nxs_l18n__("icon top fullwidth", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
			),
			
			/* LINK
			---------------------------------------------------------------------------------------------------- */
			
			array( 
				"id" 				=> "wrapper_begin",
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
				"placeholder"		=> nxs_l18n__("http://www.example.org", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an external source using the full url.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "transition",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Remove transition effect", "nxs_td"),
				"unistylablefield"	=> true,
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

function nxs_widgets_target_render_webpart_render_htmlvisualization($args) 
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
	
	// blend unistyle properties
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_target_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_target_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title","text","button_text"));
	
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
	
	if ($postid != "" && $placeholderid != "")
	{
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
		if ($shouldrenderalternative == true) 
		{
			$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . "-warning ";
		} 
		else 
		{
			// Appending custom widget class
			$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " ";
		}
	}
	
	/* EXPRESSIONS
	----------------------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------------------------
	---------------------------------------------------------------------------------------------------- */
	
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;
	if (
		$title == "" &&
		$text == ""
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
		$$verifydestinationcount++;
	}
	
	if ($verifydestinationcount > 1) {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Button: both external URL and article reference are set (ambiguous URL)", "nxs_td");
	}
	
	/* LINK
	---------------------------------------------------------------------------------------------------- */
	
	// Article link
	if ($destination_articleid != "") {
		$destination_url = nxs_geturl_for_postid($destination_articleid);
	}
	
	// Hover class
	if ($destination_articleid != "" || $destination_url != "") { $hover_state = 'hover'; }
	// Hover class
	if ($transition != "") { $hover_state = ''; }
	
	
	/* ICON
	---------------------------------------------------------------------------------------------------- */
	
	// Icon scale
	$icon_scale_cssclass = nxs_getcssclassesforlookup("nxs-icon-scale-", $icon_scale);
	
	// Icon background color
	$color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $bgcolor);
	
	// Icon color class
	if ($color_cssclass != "") { $icon_color = "colored"; }
	
	// Icon background border radius
	$border_radius_cssclass = nxs_getcssclassesforlookup("nxs-border-radius-", $border_radius);
		
	// Icon
	if ($icon != "") 
	{
		$icon = '<span class="icon nxs-border-width-1-0 '.$icon.' '.$color_cssclass.' '.$icon_color.' '.$border_radius_cssclass.'"></span>';
	}
	
	if ($icon_imageid != "")
	{
		$imagemetadata= wp_get_attachment_image_src($icon_imageid, $wpsize, true);
		$derived_imageurl = $imagemetadata[0];
		$derived_imageurl = nxs_img_getimageurlthemeversion($derived_imageurl);
		
		$icon = "<img src='{$derived_imageurl}' class='nxs-border-width-1-0 {$color_cssclass} {$border_radius_cssclass}' style='padding: 16px; width: 32px; position: absolute;' /></span>";
	}
		
	
	/* TITLE
	---------------------------------------------------------------------------------------------------- */
	
	// Title heading
	if ($title_heading != "") {
		$title_heading = "h" . $title_heading;	
	} else {
		$title_heading = "h1";
	}
	
	// Title alignment, fontsize, heightiq
	$title_alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $title_alignment);
	$title_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $title_fontsize);
	$heightiqprio = "p1";
	$title_heightiqgroup = "title";
	$titlecssclasses = nxs_concatenateargswithspaces($title_alignment_cssclass, $title_fontsize_cssclass, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$title_heightiqgroup}");
	
	// Title
	$title = '<'.$title_heading.' class="nxs-title main '.$titlecssclasses.'">'.$title.'</'.$title_heading.'>';
	
	
	/* TEXT
	---------------------------------------------------------------------------------------------------- */
	
	// Text alignment
	$text_alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $text_alignment);
	
	if ($text_heightiq != "") {
		$heightiqprio = "p1";
		$text_heightiqgroup = "text";
		$textcssclasses = nxs_concatenateargswithspaces($cssclasses, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$text_heightiqgroup}");
	}
	
	$keeptags = '<p><a>';
	$text = strip_tags($text, $keeptags);
	// Text	
	$text = '<div class="nxs-applylinkvarcolor sub nxs-text nxs-default-p nxs-padding-bottom0 '.$text_alignment_cssclass.' '.$textcssclasses.'">'.$text.'</div>';
	

	// apply shortcode on text widget
	$text = do_shortcode($text);
	
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
		$button = '<a href="'.$destination_url .'" class="nxs-button '.$button_color_cssclass.' '.$button_scale_cssclass.'">'.$button_text.'</a>';
	} else if ($destination_url != "") {
		$button = '<a href="'.$destination_url .'" class="nxs-button '.$button_color_cssclass.' '.$button_scale_cssclass.'" target="_blank">'.$button_text.'</a>';
	}
	
	// Applying alignment to button
	if ($button_text != "") {
		$button = '<p class="'.$button_alignment.' nxs-padding-bottom0">'.$button.'</p>';
	} else {
		$button = "";	
	}
	
	/* ICON
	---------------------------------------------------------------------------------------------------- */
	// Fallback
	if($icon_size == ""){$icon_size = "1-0";}
	
	
	/* OUTPUT
	----------------------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------------------------
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) {
		if ($alternativehint == "") {
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} else {
		
		echo '
		<div class=" nxs-width100 '.$layout.' '.$hover_state.' icon-size-'.$icon_size.'">';
				
			// Icon			
			if ($destination_url != "") { echo '<a href="'.$destination_url.'">'; }
				echo $icon;
			if ($destination_url != "") { echo '</a>'; }

			echo'
			<div class="content '.$icon_color.'">';
				// Title
				echo '<div class="nxs-applylinkvarcolor">';
				
					if ($destination_url != "") { echo '<a href="'.$destination_url.'">'; }
						echo $title;
					if ($destination_url != "") { echo '</a>'; }
				
				echo '</div>';
					
					if ($title != "" && $text != "") { echo '<div class="nxs-padding-bottom10"></div>'; } 
				
				// Text
				echo $text;
				
				if (($title != "" && $button != "") || ($text != "" && $button != "")) { 
					echo '<div class="nxs-margin"></div>'; 
				}
				
				// Button
				echo $button;
			
				echo'
			</div>
		</div>
			
		<div class="nxs-clear"></div>';
		
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

function nxs_widgets_target_initplaceholderdata($args)
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
	$unistylegroup = nxs_widgets_target_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_target_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
