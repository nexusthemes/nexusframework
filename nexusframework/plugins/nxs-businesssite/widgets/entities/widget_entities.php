<?php

function nxs_widgets_entities_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-moving";
}

function nxs_widgets_entities_gettitle() {
	return nxs_l18n__("Entities", "nxs_td");
}

// Unistyle
function nxs_widgets_entities_getunifiedstylinggroup() {
	return "entitieswidget";
}

// Unicontent
function nxs_widgets_entities_getunifiedcontentgroup() {
	return "entitieswidget";
}

function nxs_entities_datasourcecustom_popupcontent($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	
	nxs_ob_start();
	?>
	<div>
		<?php
		// 
		global $businesssite_instance;
		$contentmodel = $businesssite_instance->getcontentmodel();
		$url = $contentmodel[$datasource]["url"];
		if ($url != "")
		{
			echo "<a class='nxsbutton' href='{$url}'>Edit / Re-Order {$datasource}</a>";
		}
		?>
	</div>
	<?php

	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_entities_home_getoptions($args) 
{
	global $businesssite_instance;
	$contentmodel = $businesssite_instance->getcontentmodel();
	
	$taxonomies = array();
	$taxonomiesmeta = nxs_business_gettaxonomiesmeta();
	foreach ($taxonomiesmeta as $taxonomy => $taxonomymeta)
	{
	 	if ($taxonomymeta["arity"] == "n")
	 	{
	 		$taxonomies[$taxonomy] = $taxonomymeta["title"];
	 	}
	}
	
	$datasource = $args["datasource"];
	
	$orderediturl = nxs_geturl_for_postid($contentmodel[$datasource]["postid"]);
	
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_entities_gettitle(),
		"sheeticonid" 		=> nxs_widgets_entities_geticonid(),
		"sheethelp" 		=> nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=826980725"),
		"unifiedstyling" 	=> array("group" => nxs_widgets_entities_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_entities_getunifiedcontentgroup(),),
		"footerfiller" => true,	// add some space at the bottom
		"fields" => array
		(
			// link to the business editor
			array(
          "id" 				=> "wrapper_items_begin",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Items", "nxs_td"),
      ),
 			array
      (
				"id" 					=> "datasource",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Datasource", "nxs_td"),
				"dropdown" 		=> $taxonomies,
			),
			array
      (
				"id" 					=> "datasource_custom",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_entities_datasourcecustom_popupcontent",
			),
			/*
			array
			(
				"id" 				=> "editorlink",
				"type" 				=> "custom",
				"custom"	=> "<div><a class='nxsbutton' href='{$orderediturl}'>Change Order</a></div>",
				"label" 			=> nxs_l18n__("Order", "nxs_td"),
			),
			*/
			array
			(
          "id" 				=> "wrapper_items_end",
          "type" 				=> "wrapperend",
      ),
			
			// VISUALIZATION
      array(
          "id" 				=> "wrapper_title_begin",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Visualization", "nxs_td"),
      ),
     
      array
      (
				"id" 					=> "itemsstyle",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Style", "nxs_td"),
				"dropdown" 		=> array
				(
					"text" => "Text",
					"target" => "Target",
					"bio" => "Bio",
					"quote" => "Quote",
				),
				"unistylablefield" => true,
			),
      array(
          "id" 				=> "wrapper_title_end",
          "type" 				=> "wrapperend",
      ),
      
      //
      // WIDGET SPECIFIC STYLING
      //

			// TEXT WIDGET SPECIFIC STYLING
      
      array(
          "id" 				=> "wrapper_title_begin",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Text widget styling", "nxs_td"),
      ),
      
      array
      (
				"id" 				=> "text_title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title importance", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "text_title_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Title fontzen", "nxs_td"),
				"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "text_title_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "text_title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Override title fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			array
			( 
				"id" 				=> "text_top_info_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Top info color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array
			(
				"id"     			=> "text_top_info_padding",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Top info padding", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("padding"),
				"unistylablefield"	=> true
			),
			array
			(
				"id"     			=> "text_icon_scale",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Icon scale", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("icon_scale"),
				"unistylablefield"	=> true
			),
			//
			array(
				"id" 				=> "text_text_truncatelength",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Text max length", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@empty@@@" => "No truncation",
					"none" => "Truncate all",
				),
				"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "text_text_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Text alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
			//
			array
			(
				"id" 				=> "text_image_alignment",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("image_halignment"),
				"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "text_image_size",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("image_size"),
				"unistylablefield"	=> true
			),		
			array
			( 
				"id" 				=> "text_image_shadow",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Image shadow", "nxs_td"),
				"unistylablefield"	=> true
			),	
			array
			(
				"id" 				=> "text_image_border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image border width", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_width"),
				"unistylablefield"	=> true
			),
			//
			array
			(
				"id" 				=> "text_button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true,
			),
			array
			( 
				"id" 				=> "text_button_color",
				"type" 				=> "colorzen", // "select",
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "text_button_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Button fontzen", "nxs_td"),
				"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "text_button_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Button alignment", "nxs_td"),
				"unistylablefield"	=> true,
			),	
			//
			array
			(
				"id" 				=> "text_destination_target",
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
			//
			array(
				"id" 				=> "text_title_heightiq",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align titles", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's title will participate in the title alignment of other partipating widgets in this row", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "text_text_heightiq",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align texts", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's text will participate in the text alignment of other partipating widgets in this row", "nxs_td"),
				"unistylablefield"	=> true
			),			
			array
			( 
				"id" 				=> "text_text_showliftnote",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Liftnote", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("You can make the first paragraph stand out with this option.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array
			( 
				"id" 				=> "text_text_showdropcap",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Dropcap", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Enlarge the first character of the first paragraph with this option.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "text_text_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Text fontzen", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array
			( 
				"id" 				=> "text_enlarge",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Enlarge hover effect", "nxs_td"),
				"unistylablefield"	=> true
			),
			array
			( 
				"id" 				=> "text_grayscale",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Grayscale hover effect", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array
			(
          "id" 				=> "wrapper_title_end",
          "type" 				=> "wrapperend",
      ),
      
      // TARGET WIDGET SPECIFIC STYLING
      
      array(
          "id" 				=> "target_wrapper_begin",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Target widget styling", "nxs_td"),
      ),
      
      array(
				"id" 				=> "target_title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title heading", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "target_title_alignment",
				"type" 				=> "radiobuttons",
				"subtype"  			=> "halign",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "target_title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Override title fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "target_text_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Text alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "target_button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button scale", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true,
			),
			array( 
				"id" 				=> "target_button_color",
				"type" 				=> "colorzen", 
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "target_button_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Button alignment", "nxs_td"),
				"unistylablefield"	=> true,
			),
			
			array( 
				"id" 				=> "target_bgcolor",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Icon background color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "target_border_radius",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Icon background border radius", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_radius"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "target_icon_size",
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
				"id" 				=> "target_layout",
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
				"id" 				=> "target_transition",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Remove transition effect", "nxs_td"),
				"unistylablefield"	=> true,
			),
			
      array
			(
          "id" 				=> "wrapper_title_end",
          "type" 				=> "wrapperend",
      ),
      
      // -----
      
      // BIO WIDGET SPECIFIC STYLING
      
      array(
          "id" 				=> "wrapper_bio_title_begin",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Bio widget styling", "nxs_td"),
      ),
      
      array(
				"id" 				=> "bio_title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title importance", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "bio_title_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "bio_image_shadow",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Image shadow", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "bio_image_size",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("image_size"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "bio_image_border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Border size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_width"),
				"unistylablefield"	=> true
			),	
			
			array(
				"id" 				=> "bio_subtitle_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Subtitle headings", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "bio_use_icon",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Use simple icons", "nxs_td"),
				"unistylablefield"	=> true
			),	
			
			array(
				"id" 				=> "bio_text_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Text alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "bio_title_heightiq",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align titles", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's title will participate in the title alignment of other partipating widgets in this row", "nxs_td"),
				"unistylablefield"	=> true
			),
			
      array
			(
          "id" 				=> "wrapper_title_end",
          "type" 				=> "wrapperend",
      ),
      
      // QUOTE WIDGET SPECIFIC STYLING
      
      array(
          "id" 				=> "wrapper_quote_title_begin",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Quote widget styling", "nxs_td"),
      ),
      
      array(
				"id" 				=> "quote_quote_textsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Text textsize", "nxs_td"),
				"dropdown" 			=> array
				(
          "14"	=>"1.4x",
          "12"	=>"1.3x",
          "11"	=>"1.1x",
          "10"	=>"1x",
          "09"	=>"0.9x",
          "08"	=>"0.8x",
        ),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "quote_source_textsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Source textsize", "nxs_td"),
				"dropdown" 			=> array
				(
          "14"	=>"1.4x",
          "12"	=>"1.3x",
          "11"	=>"1.1x",
          "10"	=>"1x",
          "09"	=>"0.9x",
          "08"	=>"0.8x",
        ),
				"unistylablefield"	=> true
			),
      
      array(
				"id" 				=> "quote_quote_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Quote width", "nxs_td"),
				"dropdown" 			=> array(""=>"","90%"=>"90%","80%"=>"80%","70%"=>"70%","60%"=>"60%","50%"=>"50%","40%"=>"40%","30%"=>"30%","20%"=>"20%"),
				"unistylablefield"	=> true				
			),
			
			array(
				"id" 				=> "quote_show_quote_icon",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show quote icon", "nxs_td"),
				"unistylablefield"	=> true
			),
      
      array
			(
          "id" 				=> "wrapper_title_end",
          "type" 				=> "wrapperend",
      ),
      
      
      // ---- ANY WIDGET - BACKGROUND & ALIGNMENT
      
      array
      (
          "id" 				=> "wrapper_any_background_title_begin",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("background & alignment (any type)", "nxs_td"),
      ),
      
      array(
				"id" 				=> "any_ph_padding",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Background spacing", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("padding"),
				"unistylablefield"	=> true
			),
			
			
			array(
				"id" 				=> "any_ph_border_radius",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Background border radius", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_radius"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "any_ph_border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Border width", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_radius"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "any_ph_margin_bottom",
				"type" 				=> "select",
				"label" 			=> "Margin bottom",
				"dropdown" 			=> nxs_style_getdropdownitems("margin"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "any_ph_valign",
				"type" 				=> "radiobuttons",
				"subtype"			=> "valign",
				"label" 			=> nxs_l18n__("Vertical alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
			),
      			
			// --- ANY WIDGET SPECIFIC STYLING; COLORS & TEXT
			
      array
      (
          "id" 				=> "any_wrapper_colorstext_begin",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("colors & text (any type)", "nxs_td"),
      ),
      
      array( 
				"id"				=> "any_ph_colorzen",
				"type" 				=> "colorzen",
				"focus"				=> "true",
				"label" 			=> nxs_l18n__("Color", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("The background color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "any_ph_linkcolorvar",
				"type" 				=> "colorvariation",
				"scope" 			=> "link",
				"label" 			=> nxs_l18n__("Link color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id"				=> "any_ph_text_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Text fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
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

function nxs_widgets_entities_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_entities_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_entities_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	//$mixedattributes = $temp_array;
	$mixedattributes = array_merge($temp_array, $args);
	
	// Localize atts
	//$mixedattributes = nxs_localization_localize($mixedattributes);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title","entities","button_entities", "destination_url"));
	
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
		$entities_heightiq = "";	// off!
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
	
	//global $nxs_global_current_containerpostid_being_rendered;
	//$posttype = get_post_type($nxs_global_current_containerpostid_being_rendered);
	
	global $nxs_global_current_postid_being_rendered;
	$posttype2 = get_post_type($nxs_global_current_postid_being_rendered);
	
	/*
	echo "<div>";
	echo "containerpostid; $nxs_global_current_containerpostid_being_rendered <br />";
	echo "posttype of container: $posttype <br />";
	echo "posttype of post: $posttype2 <br />";
	echo "</div>";
	*/
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	// the html can contain placeholders
	global $businesssite_instance;
	$contentmodel = $businesssite_instance->getcontentmodel();
	
	if ($datasource == "")
	{
		$taxonomy = "services";
	}
	else
	{
		$taxonomy = $datasource;
	}
	
	//
	$html .= "<div class='nxsgrid-container'>";
	
	
	
	//
	$count = $contentmodel[$taxonomy]["countenabled"];
	
	$numberofcolumns = 4;
	if ($count % 3 == 0)
	{
		$numberofcolumns = 3;
	}
	else if ($count % 4 == 0)
	{
		$numberofcolumns = 4;
	}
	else if ($count % 2 == 0)
	{
		$numberofcolumns = 2;
	}
	else
	{
		if ($count == 1)
		{
			$numberofcolumns = 1;
		}
		else if ($count == 5)
		{
			$numberofcolumns = 3;
		}
		else
		{
			$numberofcolumns = 4;
		}
	}
	
	if ($posttype2 == "nxs_sidebar")
	{
		$numberofcolumns = 1;
	}
	
	$index = -1;
	foreach ($contentmodel[$taxonomy]["instances"] as $instance)
	{
		$enabled = $instance["enabled"];
		if ($enabled == "") { continue; }
		$index++;
		$post_title = $instance["content"]["post_title"];		
		$post_excerpt = $instance["content"]["post_excerpt"];		
		$post_content = $instance["content"]["post_content"];		
		$url = $instance["content"]["url"];
		$image_imageid = $instance["content"]["post_thumbnail_id"];
		$post_icon = $instance["content"]["post_icon"];
		$post_source = $instance["content"]["post_source"];
		$post_rating_text = $instance["content"]["post_rating_text"];
		$post_stars = $instance["content"]["post_stars"];
		$post_role = $instance["content"]["post_role"];
		//
		//$post_imperative_m = $instance["content"]["post_imperative_m"];
		//$post_imperative_l = $instance["content"]["post_imperative_l"];
		//$post_destination_cta = $instance["content"]["post_destination_cta"];
		
		
		// obsolete?
		// $post_quote = $instance["content"]["post_quote"];
		
		// generic mapping
		$childargs = array
		(
			"render_behaviour" => "code",
			"title" => $post_title,
			"text" => $post_excerpt,
			"image_imageid" => $image_imageid,
			"destination_url" => $url,
			"icon" => $post_icon,
			"source" => $post_source,
			"rating_text" => $post_rating_text,
			"stars" => $post_stars,

		);
		
		// taxonomy specific mapping
		if ($taxonomy == "testimonials")
		{
			$childargs["source"] = $post_title;
			$childargs["text"] = $post_content;
		}
		if ($taxonomy == "employees")
		{
			$childargs["title"] = "";
			$childargs["person"] = $post_title;
			$childargs["line1"] = $post_role;
			$childargs["text"] = $post_content;
		}
		/*
		if ($taxonomy == "calltoactions")
		{
			if ($post_destination_cta == "tel")
			{
				$childargs["destination_url"] = "tel://{{tel}}";
			}
			else if ($post_destination_cta == "contact")
			{
				$childargs["destination_url"] = "contactpage";
			}
			else if ($post_destination_cta == "forms.instances.0")
			{
				$childargs["destination_url"] = "firstform";
			}
			else if ($post_destination_cta == "forms.instances.1")
			{
				$childargs["destination_url"] = "secondform";
			}
			// todo: do something smart with the fields "post_imperative_m" and "post_imperative_l"
		}
		*/
		
		// replicate styleable fields specific for "TEXT" widgets
		$fieldstoreplicate = array
		(
			"title_heading", "title_fontzen", "title_alignment", "title_fontsize", "top_info_color", "top_info_padding", 
			"icon_scale", "text_truncatelength", "text_alignment", "image_alignment", "image_size", "image_shadow", "image_border_width", 
			"button_scale", "button_color", "button_fontzen", "button_alignment",
			"destination_target", "title_heightiq", "text_heightiq", "text_showliftnote", "text_showdropcap", "text_fontzen", "enlarge", "grayscale",
		);
		foreach ($fieldstoreplicate as $fieldtoreplicate)
		{
			$childargs[$fieldtoreplicate] = $args["text_{$fieldtoreplicate}"];
		}
		
		// replicate styleable fields specific for "TARGET" widgets
		$fieldstoreplicate = array
		(
			"title_heading", "title_alignment", "title_fontsize", 
			"text_alignment", "button_scale", "button_color", 
			"button_alignment", "bgcolor", "border_radius", 
			"icon_size", "layout", "transition",
		);
		foreach ($fieldstoreplicate as $fieldtoreplicate)
		{
			$childargs[$fieldtoreplicate] = $args["target_{$fieldtoreplicate}"];
		}
		
		// replicate styleable fields specific for "BIO" widgets
		$fieldstoreplicate = array
		(
			"title_heading", "title_alignment", "image_shadow",
			"image_size", "image_border_width", "subtitle_heading",
			"use_icon", "text_alignment", "title_heightiq",
		);
		foreach ($fieldstoreplicate as $fieldtoreplicate)
		{
			$childargs[$fieldtoreplicate] = $args["bio_{$fieldtoreplicate}"];
		}
		
		// replicate styleable fields specific for "QUOTE" widgets
		$fieldstoreplicate = array
		(
			"quote_textsize", "source_textsize", "quote_width", "show_quote_icon",
		);
		foreach ($fieldstoreplicate as $fieldtoreplicate)
		{
			$childargs[$fieldtoreplicate] = $args["quote_{$fieldtoreplicate}"];
		}
		
		//
		// replicate styleable fields specific for "ANY" type of widgets
		//
		$fieldstoreplicate = array
		(
			"ph_padding", "ph_border_radius", "ph_border_width", 
			"ph_margin_bottom", "ph_valign", "ph_colorzen",
			"ph_linkcolorvar", "ph_text_fontsize",
		);
		foreach ($fieldstoreplicate as $fieldtoreplicate)
		{
			$childargs[$fieldtoreplicate] = $args["any_{$fieldtoreplicate}"];
		}
		
		// get rid of unistyles
		unset($childargs["unistyle"]);
		unset($childargs["postid"]);
		unset($childargs["placeholderid"]);
		
		if ($itemsstyle == "title")
		{
			unset($childargs["text"]);
			unset($childargs["image_imageid"]);
		}
		
		if ($itemsstyle == "target")
		{
			$childwidgettype = "target";
			$childargs["ph_cssclass"] .= " nxs-target";
		}
		else if ($itemsstyle == "bio")
		{
			$childwidgettype = "bio";
		}
		else if ($itemsstyle == "quote")
		{
			$childwidgettype = "quote";
			$childargs["ph_cssclass"] .= " nxs-quote";
		}
		else
		{
			$childwidgettype = "text";
		}
		
		// $childargs["aap"] = "noot";
		
		//
		// render wrap
		//
		
		$ph_colorzen = nxs_getcssclassesforlookup("nxs-colorzen-", $childargs["ph_colorzen"]);
		$ph_linkcolorvar = nxs_getcssclassesforlookup("nxs-linkcolorvar-", $childargs["ph_linkcolorvar"]);
		$ph_border_radius = nxs_getcssclassesforlookup("nxs-border-radius-", $childargs["ph_border_radius"]);
		$ph_borderwidth = nxs_getcssclassesforlookup("nxs-border-width-", $childargs["ph_border_width"]);
		$ph_cssclass = $childargs["ph_cssclass"];		
		$ph_padding = nxs_getcssclassesforlookup("nxs-padding-", $childargs["ph_padding"]);
		$ph_valign = $childargs["ph_valign"];

		$abc_concatenated_css = nxs_concatenateargswithspaces($ph_colorzen, $ph_linkcolorvar, $ph_border_radius, $ph_borderwidth);
		$xyz_concatenated_css = nxs_concatenateargswithspaces($ph_padding, $ph_valign);
		
		nxs_requirewidget($childwidgettype);
		$functionnametoinvoke = "nxs_widgets_{$childwidgettype}_render_webpart_render_htmlvisualization";
		$subresult = call_user_func($functionnametoinvoke, $childargs);

		$subhtml = "";
		$subhtml .= "<div class='{$ph_cssclass}'>";
		$subhtml .= "<div class='ABC {$heightclass} {$abc_concatenated_css}'>";
		$subhtml .= "<div class='XYZ {$xyz_concatenated_css}'>";
		$subhtml .= $subresult["html"];
		$subhtml .= "</div>";
		$subhtml .= "</div>";
		$subhtml .= "</div>";
				
		$remainder = $index % $numberofcolumns;
		
		$issecondelementinrow = ($remainder == 1);
		
		$isnewrow = $remainder == 0;
		$isfirst = $index == 0;
		$islastinrow = $remainder % $numberofcolumns == ($numberofcolumns - 1);

		$csslastcolumn = "";
		if ($islastinrow)
		{
			$csslastcolumn = "nxsgrid-lastcolumn";
		}
		$csssecondcolumn = "";
		if ($issecondelementinrow && $numberofcolumns == 4)
		{
			$csssecondcolumn = "nxsgrid-secondcolumn";
		}
		
		$html .= "<div class='index{$index} {$csssecondcolumn} remainder{$remainder} nxsgrid-item nxsgrid-margin-bottom20 nxsgrid-column-{$numberofcolumns} nxsgrid-float-left {$csslastcolumn}'>";
		$html .= $subhtml;
		$html .= "</div>";
		
		if ($islastinrow)
		{
			$html .= "<div class='nxsgrid-clear-both clear'></div>";	
		}
	}
	
	if ($count == 0)
	{
		//
		if (is_user_logged_in())
		{
			$taxonomiesmeta = nxs_business_gettaxonomiesmeta();
			$taxonomymeta = $taxonomiesmeta[$taxonomy];
			$title = $taxonomymeta["title"];
			global $businesssite_instance;
			$contentmodel = $businesssite_instance->getcontentmodel();
			$url = $contentmodel[$taxonomy]["url"];
			echo "No {$title} found. <a class='nxsbutton' href='{$url}'>Manage {$title}</a><br />";
		}
	}
	
	$html .= "</div>";	
	
	echo $html;
	// 
	
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

function nxs_widgets_entities_initplaceholderdata($args)
{
	extract($args);

	//$args['entities_heightiq'] = "true";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_entities_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_entities_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}
