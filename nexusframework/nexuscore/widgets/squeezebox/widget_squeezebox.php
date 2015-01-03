<?php

function nxs_widgets_squeezebox_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_squeezebox_gettitle() {
	return nxs_l18n__("Squeezebox[nxs:widgettitle]", "nxs_td");
}

// Unistyle
function nxs_widgets_squeezebox_getunifiedstylinggroup() {
	return "squeezeboxwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_squeezebox_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$options = array
	(
		"sheettitle" => nxs_widgets_squeezebox_gettitle(),
		"sheeticonid" => nxs_widgets_squeezebox_geticonid(),
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/squeezebox-widget/"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_squeezebox_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			// TITLE
				
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array
			( 
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" 	=> nxs_l18n__("Title goes here", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If your callout has an eye-popping title put it here.", "nxs_td"),
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
				"id" 				=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Override title fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "subtitle",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Subtitle", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Subtitle goes here", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Place a descriptive teaser, if available, with this option.", "nxs_td"),
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "subtitle_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Subtitle size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "subtitle_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Override subtitle fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
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
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
			// FORM
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> "Form",
			),
			
			array(
				"id" 				=> "form",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Form html", "nxs_td"),
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "alignment",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Horizontal alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("halign"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "min_height",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Minimum height", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("-", "nxs_td"),
					"200px" => nxs_l18n__("200px", "nxs_td"),
					"300px" => nxs_l18n__("300px", "nxs_td"),
					"400px" => nxs_l18n__("400px", "nxs_td"),
					"500px" => nxs_l18n__("500px", "nxs_td"),
					"600px" => nxs_l18n__("600px", "nxs_td"),
				),
				"tooltip" 			=> nxs_l18n__("This option let's you set the sliders display at a certain viewport and up", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "squeezebox_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Squeezebox width", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@"=>"",
					"90%"=>"90%",
					"80%"=>"80%",
					"70%"=>"70%",
					"60%"=>"60%",
					"50%"=>"50%",
					"40%"=>"40%",
					"30%"=>"30%",
					"20%"=>"20%",
				),
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
				"id" 				=> "image_imageid",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Background image", "nxs_td"),
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
				"localizablefield"	=> true,
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

function nxs_widgets_squeezebox_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));

	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// Unistyle
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_squeezebox_getunifiedstylinggroup(), $unistyle);
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
	
	
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;
	if (
		$title == "" &&
		$subtitle == ""
	) {
		$shouldrenderalternative = true;
		$alternativehint = "Minimal: title, subtitle or button";
	}
	
	// Image metadata
	if ($image_imageid != "") {
		$imagemetadata= wp_get_attachment_image_src($image_imageid, 'full', true);
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$imageurl 		= $imagemetadata[0];
		$imageurl = nxs_img_getimageurlthemeversion($imageurl);
		$imagewidth 	= $imagemetadata[1] . "px";
		$imageheight 	= $imagemetadata[2] . "px";	
		
		$image_background = 'background: url(' . $imageurl . ') no-repeat top center;';
	}
	
	// fixed font size
	if ($fixed_font != "") { $fixed_font = 'fixed-font'; }
	
	// horizontal alignment
	if ($halign == "left")				{ $hclass = "";	} else 
	if ($halign == "center") 			{ $hclass = "nxs-align-center";	} else 
	if ($halign == "right") 			{ $hclass = "nxs-align-right"; }
	
	// Wrapper alignment 
	if ($alignment == "center") { $alignment = "nxs-center"; } else 
	if ($alignment == "right")  { $alignment = "nxs-right"; }
	
	// title
	$title_alignment = "";	// not used/not set (the wrapping div determines the alignment)
	$htmltitle = nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, "", "");
	
	// subtitle
	$title_alignment = "";	// not used/not set (the wrapping div determines the alignment)
	$htmlsubtitle = nxs_gethtmlfortitle($subtitle, $subtitle_heading, $title_alignment, $subtitle_fontsize, "", "");
	
	// Button 
	$button_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $button_color);
	$button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $button_scale);
	
	$button = "";
	if ($button_text != "")
	{
		$button = '<input name="submitadapter" onclick="nxs_js_log(\'nice\');jQuery(this).parent().find(\':submit\').click(); return false;" class="nxs-button ' . $button_scale_cssclass . ' ' . $button_color_cssclass . '" type="button" value="' . $button_text . '" />';
	}
	
	// Filler
	$htmlfiller = '<div class="nxs-clear nxs-filler"></div>';
	
	// Min height
	if ($min_height != "") { $min_height = "min-height: " . $min_height . ";"; }
	
	// Squeezebox padding and margin
	$text_padding_cssclass = nxs_getcssclassesforlookup("nxs-padding-", $text_padding);
	$text_margin_cssclass = nxs_getcssclassesforlookup("nxs-margin-", $text_margin);
	
	// Background Color
	$bgcolor_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $bgcolor);
	
	// Border radius
	$border_radius_cssclass = nxs_getcssclassesforlookup("nxs-border-radius-", $border_radius);
	
	// horizontal alignment
	$opacity = 'nxs-opacity-' . $opacity;
	
	// Squeezebox width
	if ($squeezebox_width != ""){
		$squeezebox_width = 'width: ' . $squeezebox_width . ';';
		// the "overflow: auto; property is necessary to prevent parent div's to move when a margin is set on the child div
		$overflow = 'overflow: auto;';
	}
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) 
	{
		if ($alternativehint == "")
		{
			$alternativehint = __("Missing input");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} 
	else
	{
		echo '
		<div class="image-background ' . $halign . ' ' . $fixed_font . '" style="' . $overflow . ' '. $image_background . ' ">
			<div 
				class="wrapper ' . 
				$alignment . ' ' .
				$opacity . ' ' . 
				$hclass . ' ' . 
				$text_padding_cssclass . ' '. 
				$text_margin_cssclass . ' ' . 
				$bgcolor_cssclass . ' ' . 
				$border_radius_cssclass . '" 
				
				style="' . 
				$min_height . ' ' . 
				$squeezebox_width . ' 
			">
				<div class="wrapped-element">';
					
					if ($htmltitle != "") {
						echo $htmltitle;
					}
					
					if ($htmlsubtitle != "") {
						// add filler if the title was set
						$addfiller = false;
						if ($htmltitle != "") { $addfiller = true; }
						if ($addfiller) {
							echo $htmlfiller; 
						}
						echo $htmlsubtitle;
					}
					
					if ($form != "") {
						// add filler if the subtitle was set
						// add filler too if the subtitle was not set, but the title was
						$addfiller = false;
						if ($htmlsubtitle != "") { $addfiller = true; }
						if ($htmlsubtitle == "" && $htmltitle != "") { $addfiller = true; }
						if ($addfiller) {
							echo $htmlfiller; 
						}
						
						if ($button != "")
						{
							// hide the submit button in the form part
							
							// incase sensitive replace when double quotes are used
							$form = str_ireplace("type=\"submit\"", "type=\"submit\" style=\"display:none;\"", $form);
							// incase sensitive replace when single quotes are used
							$form = str_ireplace("type='submit'", "type='submit' style='display:none;'", $form);
						}
						
						echo $form;
						echo '<div class="nxs-clear nxs-padding-bottom20"></div>';
						echo $button;
						echo'
						</form>';
					}
					echo '
					<div class="nxs-clear"></div>
				</div>
			</div>
		</div>';
 
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

function nxs_widgets_squeezebox_initplaceholderdata($args)
{
	extract($args);

	// initialize other fields too, like so:
	// $args['button_color'] = "base2";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_squeezebox_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
