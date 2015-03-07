<?php

function nxs_widgets_tumbler_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_tumbler_gettitle() 
{
	return nxs_l18n__("Tumbler[nxs:widgettitle]", "nxs_td");
}

// Unistyle
function nxs_widgets_tumbler_getunifiedstylinggroup() {
	return "tumblerwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_tumbler_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_tumbler_gettitle(),
		"sheeticonid" => nxs_widgets_tumbler_geticonid(),
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/tumbler-widget/"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_tumbler_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			// TITLE
		
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				//"initial_toggle_state"	=> "closed",
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
				"label" 			=> nxs_l18n__("Title importance", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
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
				"tooltip" 			=> nxs_l18n__("If you want to upload an image for your bio profile use this option.", "nxs_td"),
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
				"id" 				=> "container_height",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Container height", "nxs_td"),
				"dropdown" 			=> array
				(
					"100" => nxs_l18n__("100px", "nxs_td"),
					"150" => nxs_l18n__("150px", "nxs_td"),
					"200" => nxs_l18n__("200px", "nxs_td"),
					"250" => nxs_l18n__("250px", "nxs_td"),
					"300" => nxs_l18n__("300px", "nxs_td"),
					"400" => nxs_l18n__("400px", "nxs_td"),
				),
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
				//"initial_toggle_state"	=> "closed",
			),	
					
			array(
				"id" 				=> "text",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Text", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Text goes here", "nxs_td"),
				"rows" 				=> "8",
				"localizablefield"	=> true,
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
				"placeholder" 		=> "Read more",
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
				"unistylablefield"	=> true
			),	
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Button link", "nxs_td"),
			),
			array(
				"id" 				=> "destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("External link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an external source using the full url.", "nxs_td"),
			),
			array(
				"id" 				=> "destination_js",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Javascript", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Apply javascript when the button is pressed.", "nxs_td"),
				"requirecapability" => nxs_cap_getdesigncapability(),
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

function nxs_widgets_tumbler_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_tumbler_getunifiedstylinggroup(), $unistyle);
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
	if ($container_height == "") { $container_height = "300"; }
	if ($title_bg == "") { $title_bg = "base2-dm"; }
	if ($text_bg == "") { $text_bg = "base2-dm"; }
	
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;
	
	if ($button_text == "") {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Button text not set", "nxs_td");
	}	
	
	if ($image_imageid == "") {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Image not set", "nxs_td");
	}
	
	// if both external and article link are set
	$verifydestinationcount = 0;
	if ($destination_url != "")
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
		$alternativehint = nxs_l18n__("Button: both external URL and article reference are set (ambiguous URL)", "nxs_td");
	}
	
	// if both external and article link are set
	if ($destination_url == "" && $destination_articleid == "" && $destination_js == "" && $button_text != "") {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Button: button is set, but no reference is set (no URL)", "nxs_td");
	}
	
	// Image
	if ($image_imageid != "") {     
		$imagemetadata= wp_get_attachment_image_src($image_imageid, 'full', true);
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$imageurl 		= $imagemetadata[0];
		$imagewidth 	= $imagemetadata[1] . "px";
		$imageheight 	= $imagemetadata[2] . "px";	
	}
	
	// Tumbler transform
	$transform_content = ($container_height / 2).'px';
	$transform_content = "
	transform: 			rotateX(-90deg) translateY({$transform_content}) translateZ(50px);
	-o-transform: 		rotateX(-90deg) translateY({$transform_content}) translateZ(50px);
	-moz-transform: 	rotateX(-90deg) translateY({$transform_content}) translateZ(50px);
	-webkit-transform:  rotateX(-90deg) translateY({$transform_content}) translateZ(50px); ";
	
	$container_height .= "px";
	
	// The Tumbler widget needs the box-shadow inset to render borders because of the 3d transitions.
	$image_border_width_tumbler = $image_border_width;
	
	if (strlen($image_border_width_tumbler) > 3) {
		$multiplier = substr($image_border_width_tumbler, -4, 2);
	} else {
		$multiplier = substr($image_border_width_tumbler, -3, 1);
	}
	
	settype($multiplier, "integer");
	$factor = 1;
	$image_border_width_tumbler = $multiplier * $factor; 
	$image_border_width_tumbler_title = 'box-shadow: inset '.$image_border_width_tumbler.'px 0px 0px 0px white, inset -'.$image_border_width_tumbler.'px 0 0px 0px white;';
	$image_border_width_tumbler = 'box-shadow: 0px 0px 0px '.$image_border_width_tumbler.'px white inset !important;';
	
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
	$title_default = nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, "", "", "");
	$htmltext_default 	= nxs_gethtmlfortext($text, $text_alignment, $text_showliftnote, $text_showdropcap, $wrappingelement, "");
	
	
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
		echo '
			
			<!-- DEFAULT SCENARIO -->
			
			<div class="transform nxs-default">
		
				<div class="wrapper" style="height: '.$container_height.';"> 
					<div class="wrapper-container"> 
						<div class="item">
							<div class="image-wrapper" style="height: '.$container_height.';">  
								<span class="image" style="height: '.$container_height.'; '. $image_border_width_tumbler.' background: url('.$imageurl.') no-repeat top center;">
								
									<div class="title-wrapper '.$title_bg_cssclass.'" style="'.$image_border_width_tumbler_title.'">';
									
										echo $title_default;
										echo '
										
									</div>
								
								</span>
							</div>  
							<span class="content '.$text_bg_cssclass.'" style="height: '.$container_height.'; '.$transform_content.'">
								<div class="content-shadow" style="height: '.$container_height.';">
									<div class="content-wrapper" style="height: '.$container_height.'; '. $image_border_width_tumbler.' '.$display_table.'">
										<div class="content-container nxs-default-p">';
								
											echo $htmltext_default;
											if ($htmlforbutton != "") {
												echo '<div class="nxs-clear nxs-padding-bottom20"></div>';
											}
											echo $htmlforbutton;
											echo '  
										
										</div>
									</div>				  
								</div>
							</span>  
						</div>  
					</div>
				</div> 
			
			</div> <!-- END default -->
			
			
			<!-- FALLBACK SCENARIO -->
			
			<div class="transform nxs-fallback">';
			
				$html = nxs_gethtmlfortitleimagetextbutton($htmltitle, $htmlforimage, $image_size, $htmltext, $htmlforbutton, $htmlfiller);
				echo $html;
				echo '<div class="nxs-clear"></div>
				
			</div> <!-- END fallback --> ';
		      
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

function nxs_widgets_tumbler_initplaceholderdata($args)
{
	extract($args);
	
	$args['title_alignment'] = "center";
	$args['title_heading'] = "4";
	$args['button_scale'] = "1-2";
	$args['container_height'] = "300";
	$args['title_heightiq'] = "true";
	$args['title_bg'] = "base2-dm";
	$args['text_bg'] = "base2-dm";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_tumbler_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
