<?php

function nxs_widgets_radial_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_radial_gettitle() {
	return nxs_l18n__("Radial[nxs:widgettitle]", "nxs_td");
}

// Unistyle
function nxs_widgets_radial_getunifiedstylinggroup() {
	return "radialwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_radial_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_radial_gettitle(),
		"sheeticonid" => nxs_widgets_radial_geticonid(),
		"supporturl" => "https://www.wpsupporthelp.com/wordpress-questions/widgets-radial-wordpress-questions-95/",
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_radial_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			// TITLE
			
			array( 
				"id" 				=> "wrapper_begin",
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
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title heading markup", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "title_bg",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Title background color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
			// IMAGE
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Image", "nxs_td"),
			),
			
			array
			( 
				"id" 				=> "image_imageid",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Choose image", "nxs_td"),
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "image_border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Border size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_width"),
				"unistylablefield"	=> true
			),		
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
			// TEXT
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Text", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "text",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Text", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Text goes here", "nxs_td"),
				"localizablefield"	=> true
			),	
			array( 
				"id" 				=> "text_bg",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Text background color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample", "nxs_td"),
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
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "button_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample text", "nxs_td"),
				"unistylablefield"	=> true
			),	
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Article link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an article within your site.", "nxs_td")
			),
			array(
				"id" 				=> "destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("External link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an external source using the full url.", "nxs_td")
			),
			
			array( 
				"id" 				=> "wrapper_end",
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

function nxs_widgets_radial_render_webpart_render_htmlvisualization($args) 
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
	
	// Unistyle
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_radial_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
	if ($render_behaviour == "code")
	{
		//
	}
	else
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
	
	// Downwards compatibility
	if ($title_bg == "") { $title_bg = "base2-dm"; }
	if ($text_bg == "") { $text_bg = "base2-dm"; }
	
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;
	if ( false ) {
		$shouldrenderalternative = true;
		$alternativehint = "The widget isn't configured enough to render properly. Define more options.";
	}
	
	// The radial needs the imageurl variable for the default part
	if ($image_imageid != "") {     
		$imagemetadata= nxs_wp_get_attachment_image_src($image_imageid, 'full', true);
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$imageurl 		= $imagemetadata[0];
		$imageurl = nxs_img_getimageurlthemeversion($imageurl);
		$imagewidth 	= $imagemetadata[1] . "px";
		$imageheight 	= $imagemetadata[2] . "px";	
	}
	
	// The Tumbler widget needs the box-shadow inset to render borders because of the 3d transitions.
	$image_border_width_radial = $image_border_width;
	
	if (strlen($image_border_width_radial) > 3) {
		$multiplier = substr($image_border_width_radial, -4, 2);
	} else {
		$multiplier = substr($image_border_width_radial, -3, 1);
	}
	
	settype($multiplier, "integer");
	$factor = 1;
	$image_border_width_radial = $multiplier * $factor; 
	$image_border_width_radial = 'box-shadow: inset 0 0 0 0 rgba(0,0,0,0.6), inset 0 0 0 '.$image_border_width_radial.'px white, 0 2px 6px rgba(10, 10, 10, 0.3)';
	
	// Title and text bg color
	$title_bg_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $title_bg);
	$text_bg_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $text_bg);

	
	/* FALLBACK & DEFAULT
	---------------------------------------------------------------------------------------------------- */
	
	// Height iq
	$title_heightiq = true;
	
	// Image size
	$image_size = 'auto-fit';
	
	// Image shadow
	if ($image_border_width != "") { $image_shadow = 'nxs-shadow'; }
	
	// Default HMTL rendering
	$htmltitle = 		nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, "", "");
	$htmltext = 		nxs_gethtmlfortext($text, $text_alignment, $text_showliftnote, $text_showdropcap, $wrappingelement, $title_heightiq);
	$htmlforimage = 	nxs_gethtmlforimage($image_imageid, $image_border_width, $image_size, $image_alignment, $image_shadow, $image_alt, $destination_articleid, $destination_url, $image_title);
	$htmlforbutton = 	nxs_gethtmlforbutton($button_text, $button_scale, $button_color, $destination_articleid, $destination_url, $destination_target, $button_alignment, $destination_js);
	$htmlfiller = 		nxs_gethtmlforfiller();
	
	// Default scenario
	$htmltitle_default 	= nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, "", "", "");
	$htmltext_default 	= nxs_gethtmlfortext($text, $text_alignment, $text_showliftnote, $text_showdropcap, $wrappingelement, "");
	
	
	/* LINK
	---------------------------------------------------------------------------------------------------- */
	
	// Article link
	if ($destination_articleid != "") {
		$destination_url = nxs_geturl_for_postid($destination_articleid);
	}
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) {
		if ($alternativehint == "") {
			$alternativehint = __("Missing input");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} else {
		
		echo '	
		
		<!-- DEFAULT SCENARIO -->
		
		<div class="transition nxs-default">
		
			<div class="radial '.$text_bg_cssclass.'" style="'.$image_border_width_radial.';">';
			
				// TEXT
				
				echo '
				<div class="text-wrapper nxs-default-p nxs-padding-bottom0">';
				
					echo $htmltext_default;
					if ($htmltext != "" && $htmlforbutton != "") {
						echo $htmlfiller;
					}
					echo $htmlforbutton ; 
					
					echo'
				</div>
			
			</div>
			
			<div class="radial" style="'.$image_border_width_radial.'; z-index: 25;"></div>
			
			<div class="radial" style="background: url('.$imageurl.') no-repeat center;">';
				
				// TITLE
				
				if ($htmltitle_default != "") { echo '
					<div class="title-wrapper '.$title_bg_cssclass.'">
						'.$htmltitle_default.'
					</div>';
				}
			
			echo '
			</div>
		
		</div>		
		
		<!-- FALLBACK SCENARIO -->
			
		<div class="transition nxs-fallback">';
			
			$html = nxs_gethtmlfortitleimagetextbutton($htmltitle, $htmlforimage, $image_size, $htmltext, $htmlforbutton, $htmlfiller);
			echo $html;
			echo '<div class="nxs-clear"></div>
			
		</div> <!-- END fallback --> ';
			
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

function nxs_widgets_radial_initplaceholderdata($args)
{
	extract($args);
	
	$args['title_alignment'] = "center";
	$args['title_heading'] = "4";
	$args['button_scale'] = "1-2";
	$args['title_bg'] = "base2-dm";
	$args['text_bg'] = "base2-dm";
	// $args['title_heightiq'] = "true";

	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_radial_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_dataprotection_nexusframework_widget_radial_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("widget-none");
}

?>