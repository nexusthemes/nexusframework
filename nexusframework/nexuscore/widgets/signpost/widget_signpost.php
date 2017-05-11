<?php

function nxs_widgets_signpost_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_signpost_gettitle() {
	return nxs_l18n__("Signpost[nxs:widgettitle]", "nxs_td");
}

// Unistyle
function nxs_widgets_signpost_getunifiedstylinggroup() {
	return "signpostwidget";
}

// Unicontent
function nxs_widgets_signpost_getunifiedcontentgroup() {
	return "signpostwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_signpost_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_signpost_gettitle(),
		"sheeticonid" => nxs_widgets_signpost_geticonid(),
		"sheethelp" => nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1764396204"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_signpost_getunifiedstylinggroup(),
		),
		"unifiedcontent" 	=> array 
		(
			"group" => nxs_widgets_signpost_getunifiedcontentgroup(),
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
				"placeholder" => nxs_l18n__("Title goes here", "nxs_td"),
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
				"unicontentablefield" => true,
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
					"500" => nxs_l18n__("500px", "nxs_td"),
					"600" => nxs_l18n__("600px", "nxs_td"),
				),
				"unistylablefield"	=> true
			),	
			array( 
				"id" 				=> "mask_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Mask color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Set's the color of the transparent sheet that transitions over the background image when hovered", "nxs_td"),
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
				"placeholder" => nxs_l18n__("Text goes here", "nxs_td"),
				"rows" => 4,
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
			// BUTTON
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Destination", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "button_text",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Button text", "nxs_td"),
				"unicontentablefield" => true,
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
				"sampletext"		=> nxs_l18n__("Sample<br />text", "nxs_td"),
				"unistylablefield"	=> true
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
				"tooltip" 			=> nxs_l18n__("Link the button to an external source using the full url.", "nxs_td"),
				"unicontentablefield" => true,
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
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
			// MISCELLANEOUS
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Miscellaneous", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array( 
				"id" 				=> "remove_shadow",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Remove shadow", "nxs_td"),
				"unistylablefield"	=> true
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

function nxs_widgets_signpost_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	if ($render_behaviour == "code")
	{
		//
		$temp_array = array();
	}
	else
	{
		$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	}
	
	// Blend unistyle properties
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") 
	{
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_signpost_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}

	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_signpost_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}	
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title","text","button_text", "destination_url"));
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
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
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));
			
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */

	// Downwards compatibility
	if ($container_height == "") { $container_height = "300"; }
	if ($title_bg == "") { $title_bg = "base2-dm"; }
	if ($mask_color == "") { $text_bg = "base2-dm"; }
	
	$shouldrenderalternative = false;
	
	// image is required
	if (!isset($image_imageid) || $image_imageid == 0) {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Image is required.", "nxs_td");
	}
	
	// title is required
	if (!isset($title) || $title == "") {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Title is required.", "nxs_td");
	}
	
	// Image
	if ($image_imageid != "") {     
		$imagemetadata= wp_get_attachment_image_src($image_imageid, 'full', true);
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$imageurl 		= $imagemetadata[0];
		$imageurl = nxs_img_getimageurlthemeversion($imageurl);
		$imagewidth 	= $imagemetadata[1] . "px";
		$imageheight 	= $imagemetadata[2] . "px";	
	}
	
	// Border size
	$image_border_width_cssclass = nxs_getcssclassesforlookup("nxs-border-width-", $image_border_width);
	
	// Link
	$destination_articleid_signpost = nxs_geturl_for_postid($destination_articleid);
	
	// Mask color
	$mask_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $mask_color);
	
	// Title background color
	$title_bg_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $title_bg);
	
	// Container height
	$container_height = $container_height . "px";
	
	// Image shadow
	if ($remove_shadow != "") {$remove_shadow = "box-shadow: none;";  }
	
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
		
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

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
	
	if ($destination_target == "") {
		$destination_target_html = 'target="_self"';
	} else if ($destination_target == "_blank") {
		 $destination_target_html = 'target="_blank"';
	} else {
		$destination_target_html = 'target="_self"';
	}
	
	if ($shouldrenderalternative) {
		if ($alternativehint == "") {
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} else {
		
		echo '  
			<!-- DEFAULT SCENARIO -->
			
			<div class="transition nxs-default" style="height: '.$container_height.';'.$remove_shadow.'">';
			
				if($destination_articleid_signpost != ""){
					echo'<a '.$destination_target_html.' href="'.$destination_articleid_signpost.'"><div class="border '. $image_border_width_cssclass.'"></div></a>';
				} else if ($destination_url != ""){
					echo '<a '.$destination_target_html.' href="'.$destination_url.'"><div class="border '. $image_border_width_cssclass.'"></div></a>';
				} else {
					echo '<div class="border '. $image_border_width_cssclass.'"></div>';
				}
				
				echo'
				<div class="image" style="background: url('.$imageurl.') no-repeat top center; height: '.$container_height.';"></div>
				<div class="mask-color '.$mask_color_cssclass.'" style="height: '.$container_height.';">
					<div class="mask-container nxs-default-p nxs-padding-bottom0">';
					
						echo $htmltext_default;
							if ($htmlforbutton != "") {
								echo '<div class="nxs-clear nxs-padding-bottom10"></div>';
							}
							
						echo $htmlforbutton;
						
					echo'
					</div>
				</div> 
				<div class="mask" style="height: '.$container_height.';">
					
					<div class="title-wrapper '.$title_bg_cssclass.'">';
						echo $htmltitle_default;
						echo '
					</div>
					
				</div>				
				
			</div> <!-- END default -->
			
			
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

function nxs_widgets_signpost_initplaceholderdata($args)
{
	extract($args);

	$args['title_heading'] = "4";
	$args['button_scale'] = "1-2";
	$args['container_height'] = "300";
	$args['title_bg'] = "base2-dm";
	$args['mask_color'] = "base2-dm";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_signpost_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
		// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_signpost_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}


?>
