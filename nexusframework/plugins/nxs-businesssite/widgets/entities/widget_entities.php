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
			// SECTION
			
			array( 
				"id" 				=> "wrapper_input_begin",
				"type" 				=> "wrapperbegin",
				"initial_toggle_state" => "closed",
				"label" 			=> nxs_l18n__("Section", "nxs_td"),
			),

			array(
				"id" 				=> "section_icon",
				"type" 				=> "icon",
				"label" 			=> nxs_l18n__("Icon", "nxs_td"),
			),
			
			array(
				"id" 				=> "section_title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
			),
			
			array( 
				"id" 				=> "wrapper_input_end",
				"type" 				=> "wrapperend"
			),
		
			// TITLE
			
			array
			( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"initial_toggle_state" => "closed",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
			),
			array(
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
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
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
			
			// ITEMS
		
			array(
          "id" 				=> "wrapper_items_begin",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Items", "nxs_td"),
      ),
 			array
      (
				"id" 					=> "datasource",
				"type" 				=> "select",
				"popuprefreshonchange" => "true",
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
     	/*
      array(
          "id" 				=> "wrapper_title_begin",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Child Visualization", "nxs_td"),
      ),
     
      array
      (
				"id" 					=> "itemsstyle",
				"type" 				=> "select",
				"popuprefreshonchange" => "true",
				"label" 			=> nxs_l18n__("Style", "nxs_td"),
				"dropdown" 		=> array
				(
					"text" => "Text",
					"target" => "Target",
					"signpost" => "Signpost",
					"bio" => "Bio",
					"quote" => "Quote",
					"htmlcustom" => "Html Custom",
				),
				"unistylablefield" => true,
			),
			
      array(
          "id" 				=> "wrapper_title_end",
          "type" 				=> "wrapperend",
      ),
      */
      
      //
      // WIDGET SPECIFIC STYLING
      //

			// TEXT WIDGET SPECIFIC STYLING
      
      array
      (
          "id" 				=> "wrapper_itemsstyle_text_begin",
          "cssclasses" => "custom-filter custom-filter-text",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Child text widget styling", "nxs_td"),
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
          "id" 				=> "wrapper_itemsstyle_target_begin",
          "cssclasses" => "custom-filter custom-filter-target",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Child target widget styling", "nxs_td"),
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
          "id" 				=> "wrapper_itemsstyle_bio_begin",
          "cssclasses" => "custom-filter custom-filter-bio",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Child bio widget styling", "nxs_td"),
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
      
      // SIGNPOST SPECIFIC STYLING
      
      array
      (
          "id" 				=> "wrapper_itemsstyle_signpost_begin",
          "cssclasses" => "custom-filter custom-filter-signpost",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Child signpost widget styling", "nxs_td"),
      ),
      
      array(
				"id" 				=> "signpost_title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title heading", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "signpost_title_bg",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Title background color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "signpost_image_border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Border size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_width"),
				"unistylablefield"	=> true
			),	
			array(
				"id" 				=> "signpost_container_height",
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
				"id" 				=> "signpost_mask_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Mask color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Set's the color of the transparent sheet that transitions over the background image when hovered", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "signpost_button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "signpost_button_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample<br />text", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "signpost_destination_target",
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
				"id" 				=> "signpost_remove_shadow",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Remove shadow", "nxs_td"),
				"unistylablefield"	=> true
			),	
      
      array
			(
          "id" 				=> "signpost_wrapper_title_end",
          "type" 				=> "wrapperend",
      ),
      
      // QUOTE WIDGET SPECIFIC STYLING
      
      array(
          "id" 				=> "wrapper_itemsstyle_quote_begin",
          "cssclasses" => "custom-filter custom-filter-quote",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Child quote widget styling", "nxs_td"),
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
          "label" 			=> nxs_l18n__("Child background & alignment", "nxs_td"),
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
          "label" 			=> nxs_l18n__("Child colors & text", "nxs_td"),
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
			
			array(
				"id" 				=> "custom",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_entities_custom_popupcontent",
				"label" 			=> nxs_l18n__("...", "nxs_td"),
				"layouttype"		=> "custom",
			),
			
		)
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

function nxs_entities_getdefaultitemsstyle($datasource)
{
	$result = "htmlcustom";
	
	$taxonomiesmeta = nxs_business_gettaxonomiesmeta();
	foreach ($taxonomiesmeta as $taxonomy => $meta)
	{
		if ($taxonomy == $datasource)
		{
			if (isset($meta["instance"]["defaultrendertype"]))
			{
				$result = $meta["instance"]["defaultrendertype"];
			}
		}
	}
	
	return $result;
}

function nxs_entities_geticon($datasource)
{
	$result = "moving";
	
	$taxonomiesmeta = nxs_business_gettaxonomiesmeta();
	foreach ($taxonomiesmeta as $taxonomy => $meta)
	{
		if ($taxonomy == $datasource)
		{
			$result = $meta["icon"];
		}
	}
	
	return $result;
}

function nxs_entities_custom_popupcontent($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);

	$itemsstyle = nxs_entities_getdefaultitemsstyle($datasource);

	nxs_ob_start();
	?>
	<script>
		//var style = '<?php echo $itemsstyle; ?>';
		//nxs_js_alert("enabling styles for '"+style+"' :)");
		//
		jQuery(".custom-filter").hide();
		jQuery(".custom-filter-<?php echo $itemsstyle; ?>").show();
	</script>
	<?php
	
	// ----
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
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
	if (isset($unistyle) && $unistyle != "") 
	{
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_entities_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") 
	{
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

	$itemsstyle = nxs_entities_getdefaultitemsstyle($datasource);
	$childwidgettype = $itemsstyle;	
	
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
	
	/* SECTION
	---------------------------------------------------------------------------------------------------- */
	
	// Icon
	if ($section_icon != "") {
		$section_icon = '<span class="icon ' . $section_icon . '"></span>';
	}

	$hash = str_replace(' ', '-', $section_title);
	
	ob_start();

	$container_class = 'hide';
	if (nxs_has_adminpermissions()) {
		$container_class = 'nxs-hidewheneditorinactive';
	} 

	if ($section_title != "" || $section_icon != "")
	{
		?>
		<div class="section"></div>
		<div id="<?php echo $hash; ?>" class="nxs-section">
			<div class="nxs-section-container <?php echo $container_class; ?>">
				<h5>
					<div class="seperator-before"></div>
					<?php echo $section_icon; ?>
					<span class="nxs-section-title"><?php echo $section_title; ?></span>
					<div class="seperator-after"></div>
				</h5>
				<div class="nxs-section-icon">
					<?php echo $section_icon; ?>
				</div>
			</div>
		</div>
		<?php
	}
	
	//	
	
	$section_html = ob_get_contents();
	ob_end_clean();
	
	$html .= $section_html;
	
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
	
	ob_start();
	
	$titlehtml = "<{$title_heading} {$title_schemaorg_attribute} class='{$concatenatedcssclasses}'>{$title}</{$title_heading}>";
	
	/* Title and filler
	----------------------------------------------------------------------------------------------------*/
	if ($icon == "" && $title == "") 
	{
		// nothing to show
	} 
	else if (($top_info_padding_cssclass != "") || ($icon != "") || ($top_info_color_cssclass != "")) {
		 
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
	
	} 
	else 
	{
	
		// Default title
		if ($title != "") 
		{
			echo $titlehtml;
			
		}
	}
	
	$shouldrenderfiller = false;
	if ($title != "" || $icon != "")
	{
		$shouldrenderfiller = true;
	}
	
	if ($shouldrenderfiller)
	{
		$htmlfiller = nxs_gethtmlforfiller();
		echo $htmlfiller; 
	}
	
	$titlehtml = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	$html .= $titlehtml;
	
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
		$post_id = $instance["content"]["post_id"];
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
		
		// replicate styleable fields specific for "TEXT" widgets
		if ($childwidgettype == "text" || $childwidgettype == "")
		{
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
		}
		
		// replicate styleable fields specific for "TARGET" widgets
		if ($childwidgettype == "target")
		{
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
		}
		
		// replicate styleable fields specific for "SIGNPOST" widgets
		if ($childwidgettype == "signpost")
		{
			$fieldstoreplicate = array
			(
				"title_heading",
				"title_bg",
				"image_border_width",
				"container_height",
				"mask_color",
				"button_scale",
				"button_color",
				"destination_target",
				"remove_shadow",
			);
			foreach ($fieldstoreplicate as $fieldtoreplicate)
			{
				$childargs[$fieldtoreplicate] = $args["signpost_{$fieldtoreplicate}"];
			}
		}
		
		// replicate styleable fields specific for "BIO" widgets
		if ($childwidgettype == "bio")
		{
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
		}
		
		// replicate styleable fields specific for "QUOTE" widgets
		if ($childwidgettype == "quote")
		{
			$fieldstoreplicate = array
			(
				"quote_textsize", "source_textsize", "quote_width", "show_quote_icon",
			);
			foreach ($fieldstoreplicate as $fieldtoreplicate)
			{
				$childargs[$fieldtoreplicate] = $args["quote_{$fieldtoreplicate}"];
			}
		}
		
		//
		// replicate styleable fields specific for "ANY" type of widgets
		//
		if (true)
		{
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
		}
		
		// get rid of things we don't want
		unset($childargs["unistyle"]);
		unset($childargs["postid"]);
		unset($childargs["placeholderid"]);
		
		if ($itemsstyle == "")
		{
			$childwidgettype = "text";
		}
		else if ($itemsstyle == "target")
		{
			$childargs["ph_cssclass"] .= " nxs-target";
		}
		else if ($itemsstyle == "quote")
		{
			$childargs["ph_cssclass"] .= " nxs-quote";
		}
		else if ($itemsstyle == "signpost")
		{
			$childargs["ph_cssclass"] .= " nxs-signpost";
		}
		
		$childargs["type"] = $childwidgettype;
		
		//
		// render wrap
		//
		
		$child_ph_colorzen = nxs_getcssclassesforlookup("nxs-colorzen-", $childargs["ph_colorzen"]);
		$child_ph_linkcolorvar = nxs_getcssclassesforlookup("nxs-linkcolorvar-", $childargs["ph_linkcolorvar"]);
		$child_ph_border_radius = nxs_getcssclassesforlookup("nxs-border-radius-", $childargs["ph_border_radius"]);
		$child_ph_borderwidth = nxs_getcssclassesforlookup("nxs-border-width-", $childargs["ph_border_width"]);
		$child_ph_cssclass = $childargs["ph_cssclass"];		
		$child_ph_margin_bottom = nxs_getcssclassesforlookup("nxs-margin-bottom-", $childargs["ph_margin_bottom"]);
		
		$child_ph_padding = nxs_getcssclassesforlookup("nxs-padding-", $childargs["ph_padding"]);
		$child_ph_valign = $childargs["ph_valign"];

		$abc_concatenated_css = nxs_concatenateargswithspaces($child_ph_colorzen, $child_ph_linkcolorvar, $child_ph_border_radius, $child_ph_borderwidth);
		$xyz_concatenated_css = nxs_concatenateargswithspaces($child_ph_padding, $child_ph_valign);
		
		// allow plugins to extend the child args (fill custom fields, or override fields, whatever)
		$filterargs = array
		(
			"instance" => $instance,
			"taxonomy" => $taxonomy,
			"childwidgettype" => $childwidgettype,
		);
		$childargs = apply_filters('nxs_f_entity_getchildargs', $childargs, $filterargs);
		
		nxs_requirewidget($childwidgettype);
		$functionnametoinvoke = "nxs_widgets_{$childwidgettype}_render_webpart_render_htmlvisualization";
		$subresult = call_user_func($functionnametoinvoke, $childargs);

		$subhtml = "";
		$subhtml .= "<div class='{$child_ph_cssclass} {$child_ph_margin_bottom}'>";
				
		$subhtml .= "<div class='ABC {$heightclass} {$abc_concatenated_css}'>";
		
		// 
		$shouldrendereditor = (is_user_logged_in());
		
		if ($shouldrendereditor)
		{
			nxs_ob_start();
			
			$icon = nxs_entities_geticon($datasource);
			?>
			<div class="nxs-hover-menu-positioner">
				<div class="nxs-hover-menu nxs-widget-hover-menu nxs-admin-wrap inside-left-top" style="pointer-events:auto;">
				  <ul class="">
				    <li title="Edit" class="nxs-hovermenu-button">
				  		<a href="#" title="Edit" onclick="event.stopPropagation(); nxs_js_edit_entity(this); return false;">
				      	<span class="nxs-icon-<?php echo $icon; ?>"></span>
				      </a>
						</li>
					</ul>
				</div>
			</div>
			<?php
			$popup = nxs_ob_get_contents();
			nxs_ob_end_clean();
	
			$subhtml .= "<style>";
			$subhtml .= ".nxsgrid-item .ABC { position: relative;}";
			$subhtml .= ".overlay { display: none; background-color: rgba(100,100,100,0.5); position: absolute; pointer-events:none; left:0; right:0;top:0;bottom:0; }";
			$subhtml .= ".nxsgrid-item:hover .overlay { display: block; }";
			$subhtml .= "</style>";
			$subhtml .= "<div class='nxs-hidewheneditorinactive'>";
			$subhtml .= "<div class='overlay'>";		
			$subhtml .= $popup;
			$subhtml .= "</div>";
			$subhtml .= "</div>";
		}
		
		$subhtml .= "<div class='XYZ {$xyz_concatenated_css}'>";
		$subhtml .= $subresult["html"];
		$subhtml .= "</div>";
		$subhtml .= "</div>";
		
		//
		
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
		
		$html .= "<div class='index{$index} {$csssecondcolumn} remainder{$remainder} nxsgrid-item nxsgrid-column-{$numberofcolumns} nxsgrid-float-left {$csslastcolumn} nxs-entity' data-id='{$post_id}'>";
		$html .= $subhtml;
		$html .= "</div>";
		
		if ($islastinrow)
		{
			$html .= "<div class='nxsgrid-clear-both clear'></div>";	
		}
	}
	
	
	
	$html .= "</div>";
	
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
			$html .= "<div>No {$title} found <a class='nxsbutton' href='{$url}'>Manage {$title}</a></div>";
			$nxs_global_row_render_statebag["hidewheneditorinactive"] = true;
		}
		else
		{
			global $nxs_global_row_render_statebag;
			$nxs_global_row_render_statebag["etchrow"] = true;
			// $html = "";
		}
	}
	
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

	// 
	$args['any_ph_margin_bottom'] = "1-0";
	
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