<?php

function nxs_widgets_blog_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_blog_gettitle() {
	return nxs_l18n__("blog[widgettitle]", "nxs_td");
}

// 
function nxs_widgets_blog_getunifiedstylinggroup() {
	return "blogwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_blog_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$options = array
	(
		"sheettitle" => nxs_widgets_blog_gettitle(),
		"sheeticonid" => nxs_widgets_blog_geticonid(),
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/blog-widget/"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_blog_getunifiedstylinggroup(),
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
			/*array(
				"id" 				=> "title_height",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align titles", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's title will participate in the title alignment of other partipating widgets in this row", "nxs_td"),
				"unistylablefield"	=> true
			),*/
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
			// SEARCHCRITERIA

			array( 
				"id" 				=> "wrapper_items_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Searchcriteria", "nxs_td"),
			),	
			array(
				"id" 				=> "items_filter_catids",
				"type" 				=> "categories",
				"label" 			=> nxs_l18n__("Categories", "nxs_td"),
				"unicontentablefield" => true,
				"editable" 			=> "false",
			),	
			
			array(
				"id" 				=> "items_filter_skipcount",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Skip posts", "nxs_td"),
				"dropdown" 			=> array
				("0"=>"0","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9","10"=>"10","11"=>"11","12"=>"12","13"=>"13","14"=>"14","15"=>"15","16"=>"16","17"=>"17","18"=>"18","19"=>"19","20"=>"20")
			),
			array(
				"id" 				=> "items_filter_maxcount",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Number of posts", "nxs_td"),
				"dropdown" 			=> array("1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9","10"=>"10","20"=>"20","30"=>"30","40"=>"40","50"=>"50","100"=>"100")
			),			
			array(
				"id" 				=> "items_order",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Order", "nxs_td"),
				"dropdown" 			=> array(
					"present to past"=>nxs_l18n__("present to past", "nxs_td"), 
					"past to present"=>nxs_l18n__("past to present", "nxs_td"),
					"title az"=>nxs_l18n__("title a-z", "nxs_td"),
					"title za"=>nxs_l18n__("title z-a", "nxs_td"),
					"random"=>nxs_l18n__("random", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "item_text_truncatelength",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Text max length", "nxs_td"),
				"dropdown" 			=> nxs_convertindexarraytoassociativearray
				(
					array("", "0","100","110","120","130","140","150","160","170","180","190","200","210","220","230","240","250","260","270","280","290","300","400","500","600","1000","1400")
				),
				"unistylablefield"	=> true
			),
						
			array(
				"id" 				=> "item_text_appendchars",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Abbreviation characters", "nxs_td"),
				"placeholder"		=> nxs_l18n__("[...]", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_items_end",
				"type" 				=> "wrapperend"
			),
			
			// SINGLE BLOG ENTRY TITLE
			
			array( 
				"id" 					=> "wrapper_output_begin",
				"type" 					=> "wrapperbegin",
				"initial_toggle_state"	=> "closed",
				"label" 				=> nxs_l18n__("Single blog entry title", "nxs_td"),
				"unistylablefield"	=> true
			),	
			
			/*	
			array( 
				"id" 				=> "hide_title",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Hide title", "nxs_td"),
				"unistylablefield"	=> true
			),
			*/
			
			array(
				"id" 				=> "item_title_format",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title format", "nxs_td"),
				"dropdown" 			=> array
				(
					""							=> nxs_l18n__("Default", "nxs_td"),
					"{{{title}}}"				=> nxs_l18n__("Title", "nxs_td"),
					"{{{title}}} {{{date}}}"				=> nxs_l18n__("Title date", "nxs_td"),
					"none"		=> nxs_l18n__("Hidden", "nxs_td")
				),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "item_title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title heading for item", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "item_title_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Title fontzen", "nxs_td"),
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
				"id" 				=> "wrapper_advanceditems_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),

			// SINGLE BLOG TEXT

			array( 
				"id" 					=> "wrapper_output_begin",
				"type" 					=> "wrapperbegin",
				"initial_toggle_state"	=> "closed",
				"label" 				=> nxs_l18n__("Single blog entry text", "nxs_td"),
				"unistylablefield"	=> true
			),

			array(
				"id" 				=> "item_text_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Text fontzen", "nxs_td"),
				"unistylablefield"	=> true
			),

			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
			// SINGLE BLOG ENTRY IMAGE
			
			array( 
				"id" 					=> "wrapper_output_itemimage_begin",
				"type" 					=> "wrapperbegin",
				"initial_toggle_state"	=> "closed",
				"label" 				=> nxs_l18n__("Single blog entry image", "nxs_td"),
				"unistylablefield"		=> true
			),
			
			array(
				"id" 				=> "item_image_size",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("image_size"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "item_image_shadow",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Image shadow", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "item_image_border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image border size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_width"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "grayscale",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Grayscale hover effect", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "enlarge",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Enlarge hover effect", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_advanceditems_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),
			
			// SINGLE BLOG ENTRY BUTTON

			array( 
				"id" 				=> "wrapper_items_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Single blog entry button", "nxs_td"),
				"initial_toggle_state"	=> "closed",		
				"unistylablefield"	=> true		
			),
			
			array(
				"id" 				=> "item_button_text",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Button", "nxs_td"),
				"placeholder"		=> nxs_l18n__("Read more", "nxs_td"),
				"localizablefield"	=> true,
				"unistylablefield"	=> false
			),	
			array(
				"id" 				=> "item_button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button scale", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "item_button_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample<br />text", "nxs_td"),
				"unistylablefield"	=> true
			),	
			array(
				"id" 				=> "item_button_alignment",
				"type" 				=> "radiobuttons",
				"subtype"  			=> "halign",
				"label" 			=> nxs_l18n__("Button alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "item_button_icon_right",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Icon", "nxs_td"),
				"dropdown" 			=> array
				(
					""							=> nxs_l18n__("none", "nxs_td"),
					"nxs-icon-text"				=> nxs_l18n__("article", "nxs_td"),
					"nxs-icon-arrow-right-2"	=> nxs_l18n__("arrow right 2", "nxs_td"),
					"nxs-icon-arrow-down-2"		=> nxs_l18n__("arrow down 2", "nxs_td")
				),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_items_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),
			
			// METADATA
			
			array( 
				"id" 					=> "wrapper_metadata_begin",
				"type" 					=> "wrapperbegin",
				"label" 				=> nxs_l18n__("Metadata", "nxs_td"),
				"initial_toggle_state"	=> "closed",
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "item_showdate",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show date", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "item_showcats",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show categories", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "item_showauthor",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show author", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "item_showcommentscount", 
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show commentcount", "nxs_td"),
				"tooltip"			=> nxs_l18n__("The commentcount will be displayed by the configured comments provider (see site settings)", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "twitter",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Twitter", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "facebook",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Facebook", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "linkedin",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("LinkedIn", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "googleplus",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Google Plus", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "metadata_layout",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Metadata layout", "nxs_td"),
				"dropdown" 			=> array
				(
					"default"			=>nxs_l18n__("default", "nxs_td"),
					"date-highlight"	=>nxs_l18n__("date highlight", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "month_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Date highlight month color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "day_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Date highlight day color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_metadata_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),
			
			// PAGING
			
			array( 
				"id" 					=> "wrapper_items_begin",
				"type" 					=> "wrapperbegin",
				"label" 				=> nxs_l18n__("Pagination", "nxs_td"),
				"initial_toggle_state"	=> "closed",				
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "pagingpagination_enable",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Enable pagination", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "pagingpagination_queryparameter",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Query parameter", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "pagination_button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Pagination button scale", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "pagination_button_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Pagination button color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample<br />text", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_items_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),
			
			// LAZYLOADING
			
			array( 
				"id" 					=> "wrapper_items_begin",
				"type" 					=> "wrapperbegin",
				"label" 				=> nxs_l18n__("Lazyloading", "nxs_td"),
				"initial_toggle_state"	=> "closed",
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "paginglazyload_enable",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Enable lazy load pagination", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 			
				"id" 				=> "paginglazyload_button_text",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Button text", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "paginglazyload_button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button scale", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "paginglazyload_button_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample<br />text", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "paginglazyload_button_icon_right",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Icon", "nxs_td"),
				"dropdown" 			=> array
				(
					""				=> nxs_l18n__("none", "nxs_td"),
					"nxs-icon-text"	=> nxs_l18n__("article", "nxs_td"),
					"nxs-icon-arrow-right-2"	=> nxs_l18n__("arrow right 2", "nxs_td"),
					"nxs-icon-arrow-down-2"	=> nxs_l18n__("arrow down 2", "nxs_td")
				),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "wrapper_items_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),
			
			
			// MISCELLANEOUS
			
			array( 
				"id" 					=> "wrapper_misc_begin",
				"type" 					=> "wrapperbegin",
				"initial_toggle_state"	=> "closed",
				"label" 				=> nxs_l18n__("Miscellaneous", "nxs_td"),
				"unistylablefield"	=> true
			),	
			
			array(
				"id" 				=> "items_layout",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Layout", "nxs_td"),
				"dropdown" 			=> array
				(
					"extended"			=>nxs_l18n__("extended", "nxs_td"),
					"minimal-image"		=>nxs_l18n__("image", "nxs_td"),
					"minimal"			=>nxs_l18n__("minimal", "nxs_td")
				),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "font_icon",
				"type" 				=> "icon",
				"label" 			=> nxs_l18n__("Minimal icon", "nxs_td"),
				"unicontentablefield" => true,
			),
			array( 
				"id" 				=> "altflow_nomatches_alttext",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("No matches", "nxs_td"),
				"tooltip"			=> nxs_l18n__("What message (if any) should be shown if no blogs match the searchcriteria?", "nxs_td"),
				"localizablefield"	=> true,
				"unistylablefield"	=> true
			),
				
			array( 
				"id" 				=> "wrapper_misc_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
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
				"localizablefield"	=> true
			),	
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Article link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an article within your site.", "nxs_td"),
			),
			array(
				"id" 				=> "destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("External link", "nxs_td"),
				"placeholder"		=> nxs_l18n__("http://www.nexusthemes.com", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an external source using the full url.", "nxs_td"),
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
		)		
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_blog_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));

	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "")
	{
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_blog_getunifiedstylinggroup(), $unistyle);
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
	
	if ($item_showcommentscount != "")
	{
		$commentsprovider = nxs_commentsprovider_getcurrent();
		if ($commentsprovider == "")
		{
			$shouldrenderalternative = true;
			$alternativehint = nxs_l18n__("No comments provider is configured while commentcount is active", "nxs_td");
		}
	}
	
	if ($items_filter_catids == "")
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Select at least one category", "nxs_td");
	}
	
	if ($pagingpagination_enable != "" && $paginglazyload_enable != "")
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Ambiguous pagination (only one pagination can be used at a time)", "nxs_td");
	}
	
	if ($pagingpagination_queryparameter == "p" || $pagingpagination_queryparameter == "page" || $pagingpagination_queryparameter == "post")
	{
		$pagingpagination_queryparameter = "pagenr";
	}
	
	// Turn on output buffering
	nxs_ob_start();
	
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
		
		global $nxs_global_placeholder_render_statebag;
		if ($shouldrenderalternative == true) {
			$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . "-warning ";
		} else {
			// Appending custom widget class
			$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " ";
		}
			
		/* EXPRESSIONS
		---------------------------------------------------------------------------------------------------- */
		
		// Warning
		if (count($items_filter_catidsarray) == 1 && $items_filter_catidsarray[0] == "") {
			// implements 1148
			$pages = array();
			nxs_renderplaceholderwarning(nxs_l18n__("No categories selected[nxs:warning]", "nxs_td"));	
		}
		
		/* TITLE
		---------------------------------------------------------------------------------------------------- */
		
		// Title heading
		if ($title_heading != "") 	{ $title_heading = "h" . $title_heading; } else 
									{ $title_heading = "h1"; }

		// Title font-zen
		if ($title_fontzen != "") { 
			$title_fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen nxs-fontzen-", $title_fontzen);
		}
		$title_fontzen_cssclass = nxs_concatenateargswithspaces($title_fontzen_cssclass);
	
		// Title alignment
		$title_alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $title_alignment);
		
		if ($title_alignment == "center") { $top_info_title_alignment = "margin: 0 auto;"; } else
		if ($title_alignment == "right")  { $top_info_title_alignment = "margin-left: auto;"; } 
		
		// Title fontsize
		$title_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $title_fontsize);
		
		// Top info padding and color
		$top_info_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $top_info_color);
		$top_info_padding_cssclass = nxs_getcssclassesforlookup("nxs-padding-", $top_info_padding);
		
		// Icon scale
		$icon_scale_cssclass = nxs_getcssclassesforlookup("nxs-icon-scale-", $icon_scale);
			
		// Icon
		if ($icon != "") {$icon = '<span class="'.$icon.' '.$icon_scale_cssclass.'"></span>';}
		
		if ($title_schemaorgitemprop != "") {
			// bijv itemprop="name"
			$title_schemaorg_attribute = 'itemprop="' . $title_schemaorgitemprop . '"';
		} else {
			$title_schemaorg_attribute = "";	
		}
		
		// Title
		if (strlen($title) > 0 && strlen(trim($title)) == 0)
		{
			// string of spaces is replaced by a nbsp; (otherwise the title is not rendered properly)
			$title = "&nbsp;";
		}
		$titlehtml = '<'.$title_heading.' ' . $title_schemaorg_attribute . ' class="nxs-title '.$title_fontzen_cssclass.' '.$title_alignment_cssclass.' '.$title_fontsize_cssclass.' '.$titlecssclasses.'">'.$title.'</'.$title_heading.'>';
		
		// Filler
		$htmlfiller = nxs_gethtmlforfiller();
		
		if ($title_heightiq != "")
		{
			// Single blog item heightiq	
			$heightiqprio = "p1";
			$title_heightiqgroup = "title";
			$cssclasses = nxs_concatenateargswithspaces("nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$title_heightiqgroup}");
		}
		
		// Minimal vs. extended
		if 			($items_layout == "minimal") 	{ $blogtype = 'nxs-blog-minimal'; } 
		else if 	(!isset($items_layout) || $items_layout == "" || $items_layout == "extended") 	{ $blogtype = 'nxs-blog-extended'; }
		
		// Minimal layout icon
		$iconcssclass_font = nxs_getcssclassesforlookup('', $font_icon);
		if 	($font_icon == "") { $font_icon = ''; } 
		else { 
			$font_icon = '<span class="' . $iconcssclass_font . ' font-icon"></span>'; 
			$minimal_padding = "nxs-padding-left20";
		}
		
		/* SINGLE BLOG ENTRY BUTTON
		---------------------------------------------------------------------------------------------------- */
	
			// Icon
			$iconrightcssclass_button = nxs_getcssclassesforlookup('', $item_button_icon_right);
			if ($item_button_icon_right == "") { $item_button_icon_right = ''; } 
			else { $item_button_icon_right = '<span class="' . $iconrightcssclass_button . '"></span>'; }
			
			// Scale
			$item_button_scale = nxs_getcssclassesforlookup("nxs-button-scale-", $item_button_scale);
			
			// Color
			$item_button_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $item_button_color);
			
			// Alignment
			if 			($item_button_alignment == "left") 	{ $item_button_alignment = 'nxs-align-left'; } 
			else if 	($item_button_alignment == "right") { $item_button_alignment = 'nxs-align-right'; }


		/* LAZY LOAD BUTTON
		---------------------------------------------------------------------------------------------------- */
			
		// Icon
		$paginglazyload_button_icon_right = nxs_getcssclassesforlookup('', $paginglazyload_button_icon_right);
		if ($paginglazyload_button_icon_right != "") {
			$paginglazyload_button_icon_right_html = '<span class="' . $paginglazyload_button_icon_right . '"></span>';
		} else {
			$paginglazyload_button_icon_right_html = "";
		}
		
		// Scale
		$paginglazyload_button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $paginglazyload_button_scale);
		
		// Color
		$paginglazyload_button_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $paginglazyload_button_color);
		
		
		/* ---- */
		
		$paging_page = "";
		if ($paginglazyload_enable != "") {
			if (isset($_REQUEST["paging_page"])) {
				$paging_page = $_REQUEST["paging_page"];
			}
		}
		
		if ($paging_page == "") {
			$paging_page_class = "0";
		} else {
			$paging_page_class = $paging_page;
		}
		
		if ($pagingpagination_enable != "") {
			$queryparameter = $pagingpagination_queryparameter;
			if ($queryparameter == "") {
				// derive
				$queryparameter = "paging_page_" . $postid . "_" . $placeholderid;
			}
			
			if (isset($_REQUEST[$queryparameter])) {
				$paging_page = $_REQUEST[$queryparameter];
			}
		}
		
		if ($items_filter_skipcount != "" || $paging_page != "") {
			$items_filter_skipcount = intval($items_filter_skipcount);
			$paging_page = intval($paging_page);
			
			$paging_pagesize = intval($items_filter_maxcount);			
			$offset = ($paging_page * $paging_pagesize) + $items_filter_skipcount;
		} else {
			$offset = 0;
		}
		
		
		/* ------------------------------------------------------------------------------------------------- */
		
		// Image shadow
		if ($item_image_shadow != "") { $item_image_shadow = 'nxs-shadow'; }
		
		// Title importance (H1 - H6)
		if ($item_title_heading != "") {
			$itemheadingelement = "h" . $item_title_heading; }
		else {
			// TODO: derive the title_importance based on the title_fontsize
			//nxs_webmethod_return_nack("to be implemented; derive title_heading from title_fontsize");
			$itemheadingelement = "h1";
		}

		// Singe Entry Title Fontzen
		if ($item_title_fontzen != "") { 
			$item_title_fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen nxs-fontzen-", $item_title_fontzen);
		}
		$item_title_fontzen_cssclass = nxs_concatenateargswithspaces($item_title_fontzen_cssclass);

		// Singe Entry Text Fontzen
		if ($item_text_fontzen != "") { 
			$item_text_fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen nxs-fontzen-", $item_text_fontzen);
		}
		$item_text_fontzen_cssclass = nxs_concatenateargswithspaces($item_text_fontzen_cssclass);
			
		$items_filter_catids = nxs_convert_stringwithbracketlist_to_stringwithcommas($items_filter_catids); // bijv. [1][2][10] -> 1,2,10
		$items_filter_catidsarray = explode(",", $items_filter_catids);
	
		$publishedargs = array();
		$publishedargs["post_status"] = "publish";

		// query all posts types
		$ptargs = array
		(
   		'public'   => true
   	);
		$post_types = get_post_types($ptargs);
		
		$publishedargs["post_type"] = $post_types;
		$publishedargs["category"] = $items_filter_catids;
		
		// Order of posts
		if (!isset($items_order) || $items_order == "" || $items_order == "present to past") {
			$publishedargs["orderby"] = "post_date";
			$publishedargs["order"] = "DESC";
		} else if ($items_order == "past to present") {
			$publishedargs["orderby"] = "post_date";
			$publishedargs["order"] = "ASC"; 
		} else if ($items_order == "title az") {
			$publishedargs["orderby"] = "title";
			$publishedargs["order"] = "ASC"; 
		} else if ($items_order == "title za") {
			$publishedargs["orderby"] = "title";
			$publishedargs["order"] = "DESC"; 
		} else if ($items_order == "random") {
			$publishedargs["orderby"] = "rand";
		}
		else
		{
			// unknown
		}
		
		
		// Skip number of posts
		$publishedargs["offset"] = $offset;	// start bij de eerste
		
		// Maximum number of posts
		if ($items_filter_maxcount == "") {
			$publishedargs["numberposts"] = -1;	// allemaal!
		} else {
			$publishedargs["numberposts"] = intval($items_filter_maxcount);
		}
		
		// Date highlight colors
		$day_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $day_color);
		$month_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $month_color);
		
		$pages = get_posts($publishedargs);
		
		if (!isset($altflow_nomatches_alttext) || $altflow_nomatches_alttext == "")
		{
			if (count($pages) == 0) {
				nxs_renderplaceholderwarning(nxs_l18n__("No matches[nxs:warning]", "nxs_td"));
			}
		}
		
		
		/* PAGINATION
		---------------------------------------------------------------------------------------------------- */
		
		// Scale
		$pagination_button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $pagination_button_scale);
			
		// Color
		$pagination_button_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $pagination_button_color);
			
			
		
				
		if ($pagingpagination_enable != "" && $items_filter_skipcount != "") {
			$shouldrenderalternative = true;
			$alternativehint = nxs_l18n__("Its not supported to use both skipcount and pagination", "nxs_td");
		}
		
		$paginghtml = "";
		
		if ($pagingpagination_enable != "") {
			// paginate!
			$publishedargs["numberposts"] = -1;	// allemaal!
			$allposts = get_posts($publishedargs);
			$totalrows = count($allposts);
			$itemsperpage = intval($items_filter_maxcount);
			$totalpages = (int) ceil($totalrows / $itemsperpage);
			
			$currenturl = nxs_geturlcurrentpage();
			$queryparameter = $pagingpagination_queryparameter;
			
			if ($queryparameter == "") {
				// derive
				$queryparameter = "paging_page_" . $postid . "_" . $placeholderid;
			}
			
			$currenturlwithoutqueryparameter = nxs_removequeryparameterfromurl($currenturl, $queryparameter);
			$urlfirstpage = nxs_addqueryparametertourl($currenturl, $queryparameter, 0);
			
			if ($paging_page > 0) {
				$urlpreviouspage = nxs_addqueryparametertourl($currenturl, $queryparameter, $paging_page - 1);
			} else {
				$urlpreviouspage = nxs_addqueryparametertourl($currenturl, $queryparameter, 0);
			}
			
			if (($paging_page + 1) < $totalpages - 1) {
				$urlnextpage = nxs_addqueryparametertourl($currenturl, $queryparameter, $paging_page + 1);
			} else {
				$urlnextpage = nxs_addqueryparametertourl($currenturl, $queryparameter, $totalpages - 1);
			}
			
			$urllastpage = nxs_addqueryparametertourl($currenturl, $queryparameter, $totalpages - 1);
			
			
			nxs_ob_start();
			
			if ($totalpages > 1) { 
				echo'
				<div class="nxs-pagination nxs-pagination-' . $queryparameter . '">';
				
					// First and previous button
					if ($paging_page > 0) { 
						echo'
						<div class="nxs-float-left nxs-width30">
							<a class="current nxs-button ' . $pagination_button_scale_cssclass . ' ' . $pagination_button_color_cssclass . '" href="' . $urlfirstpage . '"><span class="nxs-icon-arrow-left-double"></a>
							<a class="current nxs-button ' . $pagination_button_scale_cssclass . ' ' . $pagination_button_color_cssclass . '" href="' . $urlpreviouspage . '"><span class="nxs-icon-arrow-left-2"></span></a>
						</div>';
					} else {
						echo'
						<div class="nxs-float-left nxs-width30" style="height: 1px;"></div>';
					}
					
					// Current page info
					echo '
					<p class="nxs-default-p nxs-padding-bottom0 nxs-float-left nxs-width40 nxs-align-center">
						<span>';
							echo $paging_page + 1 . " " . nxs_l18n__("of page", "nxs_td") . " " . $totalpages; 
							echo '
						</span>
					</p>';
					
					// Last and next button
					if (($paging_page +1) < $totalpages) { 
						echo '
						<div class="nxs-float-right">
							<a class="current nxs-button ' . $pagination_button_scale_cssclass . ' ' . $pagination_button_color_cssclass . '" href="' . $urlnextpage . '"><span class="nxs-icon-arrow-right-light"></span></a>
							<a class="current nxs-button ' . $pagination_button_scale_cssclass . ' ' . $pagination_button_color_cssclass . '" href="' . $urllastpage . '"><span class="nxs-icon-arrow-right-double"></a>
						</div>';
					} 
				echo '
				</div>
				<div class="nxs-clear"></div>';
				
				?>
				
				<script type='text/javascript'>
					jQuery(".nxs-pagination-<?php echo $queryparameter; ?> input").unbind("keyup.defaultenter");
					jQuery(".nxs-pagination-<?php echo $queryparameter; ?> input").bind("keyup.defaultenter", function(e) {
						if (e.keyCode == 13) {
							var pagenr = parseInt(jQuery(this).val());
							if (isNaN(pagenr)) {
								//ignore
								nxs_js_alert(nxs_js_gettrans('Invalid pagenumber'));
							} else {
								// check range
								if (pagenr < 1) {
									nxs_js_alert(nxs_js_gettrans('Invalid pagenumber'));
								} else if ((pagenr) > <?php echo $totalpages; ?>) {
									nxs_js_alert(nxs_js_gettrans('Invalid pagenumber'));
								} else {
									// pagenr is valid
									var url = '<?php echo $currenturlwithoutqueryparameter; ?>';
									url = nxs_js_addqueryparametertourl(url, '<?php echo $queryparameter; ?>', pagenr - 1);
									nxs_js_redirect(url);
								}
							}
							return false;
						}
					});
				</script>
				
				<?php 
				
			}
			
			$paginghtml = nxs_ob_get_contents();
			nxs_ob_end_clean();
		}
		
		/* BUTTON
		---------------------------------------------------------------------------------------------------- */
		
		// Button aligment
		$button_alignment = nxs_getcssclassesforlookup("nxs-align-", $button_alignment);
		
		// Button color
		$button_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $button_color);
		
		// Button scale
		$button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $button_scale);
		
		// Button	
		$button_html = "";
		if ($destination_articleid != "") {
			$destination_url = nxs_geturl_for_postid($destination_articleid); 
			
			$button_html = '<a href="' . $destination_url .'" class="nxs-button ' . $button_color_cssclass .' ' . $button_scale_cssclass .'">' . $button_text . '</a>';
			
		} else if ($destination_url != "") {
			$button_html = '<a href="' . $destination_url .'" class="nx-button ' . $button_color_cssclass .' ' . $button_scale_cssclass .'" target="_blank">' . $button_text . '</a>';
		}
		
		// Applying alignment to button
		$button_html = '<p class="' . $button_alignment . ' nxs-padding-bottom0">' . $button_html . '</p>';
		
		
		/* OUTPUT
		---------------------------------------------------------------------------------------------------- */
		
		echo '<div class="' . $blogtype . ' nxs-blogentries nxs-paging-page-' . $paging_page_class . ' ' . $metadata_layout . '">';
		
		/* TITLE
		---------------------------------------------------------------------------------------------------- */
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
		
		echo $htmlfiller;
			
			// MINIMAL ICON
			if ($items_layout == "minimal") {
				
				echo '<ul>';
		
				foreach ($pages as $currentpost) {
					echo '
						<li class="nxs-applylinkvarcolor nxs-default-p nxs-padding-bottom0 '.$minimal_padding.'">
							' . $font_icon . '
							<a href="' . nxs_geturl_for_postid($currentpost->ID) . '"><span>' . $currentpost->post_title . '</span></a>
						</li>';
				}
				
				if (count($pages) == 0 && isset($altflow_nomatches_alttext) && $altflow_nomatches_alttext != "")
				{
					echo '
						<li class="nxs-applylinkvarcolor nxs-default-p nxs-padding-bottom0 '.$minimal_padding.'">
							' . $font_icon . $altflow_nomatches_alttext . '
						</li>';
				}
				
				echo '</ul>';
			if ($paginghtml != "" ) { echo '<div class="nxs-clear nxs-margin"></div>'; }
			
			// MINIMAL IMAGE
			} else if ($items_layout == "minimal-image") {
				$aantalpages = count($pages);
				$pageindex = 0;
				foreach ($pages as $currentpost) {
			
					$pageindex = $pageindex + 1;
					$currentpostid = $currentpost->ID;
	
					// Blog title			
					$blogtitel = '
						<' . $itemheadingelement . ' class="nxs-title nxs-applylinkvarcolor ' . $item_title_fontzen_cssclass . '">
							<a href="' . nxs_geturl_for_postid($currentpostid) . '">' . $currentpost->post_title . '</a>
						</' . $itemheadingelement . '>';
				
					// find images used on page
					$imgblocks = nxs_get_images_in_post($currentpostid);
					// pick the first one
					$item_image_imageid = $imgblocks[0];	//
					$item_destination_articleid = $currentpostid;
					
					$item_image_alt = $currentpost->post_title;
					$item_image_title = $currentpost->post_title;
					$htmlforimage = nxs_gethtmlforimage($item_image_imageid, $item_image_border_width, $item_image_size, $item_image_alignment, $item_image_shadow, $item_image_alt, $item_destination_articleid, $item_destination_url, $item_image_title, $grayscale, $enlarge);
									
					// Rendering of individual blogentry
					echo '
						<div class="nxs-blogentry minimal-image-entry">';
		
							echo $htmlforimage;
							echo $blogtitel;
							
							echo '<div class="nxs-clear"></div>';
				
						echo '</div>';
				
					// Resetting categories
					$categorien = "";
				}
				if (count($pages) == 0 && isset($altflow_nomatches_alttext) && $altflow_nomatches_alttext != "")
				{
					// Rendering of individual blogentry
					echo '
						<div class="nxs-blogentry minimal-image-entry">';
		
							echo $altflow_nomatches_alttext;
							
							echo '<div class="nxs-clear"></div>';
				
						echo '</div>';
				}
			
			// DEFAULT				
			} else if 	(!isset($items_layout) || $items_layout == "" || $items_layout == "extended") 
			{
				
				$aantalpages = count($pages);
				$pageindex = 0;
				foreach ($pages as $currentpost) {
			
					$pageindex = $pageindex + 1;
					$currentpostid = $currentpost->ID;
					
					$currentposturl = nxs_geturl_for_postid($currentpostid);
					
					$post_password_required = post_password_required($currentpostid);
					
					$currentencodedposturl = urlencode($currentposturl);
					$currentposttitle = $currentpost->post_title;
					$currentpostdate = strtotime($currentpost->post_date);
					$localizeddate = date_i18n(get_option('date_format'), $currentpostdate);
					$currentencodedtitle = urlencode($currentposttitle);
					$item_destination_articleid = $currentpostid;
					
					// Blog title		
					if ($hide_title != "")
					{
						// no title (obsolete)
					}
					else if ($item_title_format == "none")
					{
						// no title
					}
					else
					{
						if ($item_title_format == "")
						{
							// default
							$item_title_format = "{{{title}}}";
						}
						$title_value = $item_title_format;
						$title_value = str_replace("{{{title}}}", $currentposttitle, $title_value);	
						$title_value = str_replace("{{{date}}}", $localizeddate, $title_value);	
					
						$blogtitel = '
						<' . $itemheadingelement . ' class="nxs-title nxs-applylinkvarcolor '. $cssclasses .' ' . $item_title_fontzen_cssclass . '">
							<a href="' . $currentposturl . '">' . $title_value . '</a>
						</' . $itemheadingelement . '>';
					}
				
					// Meta data
					if ($item_showdate != "" || $toontijdstip != "" || $item_showcats != "" || $item_showauthor != "") {
						
						// Date
						if ($item_showdate != "" && ($metadata_layout == "" || $metadata_layout == "default")) {
                            $get_wordpress_date_format = get_option('date_format');  
							$date_of_current_post = get_the_date( $get_wordpress_date_format, $currentpostid );
						
						} else if ($item_showdate != "" && $metadata_layout == "date-highlight") {
							$currentpostdate = $currentpost->post_date;
							$monthhtml = nxs_getlocalizedmonth(mysql2date('m', $currentpostdate));
							$date_of_current_post = '
							<div class="nxs-date">
								<h4 class="month nxs-border-width-1-0 ' . $month_color_cssclass . '">' 	. $monthhtml . '</h4>
								<h4 class="day nxs-border-width-1-0 ' . $day_color_cssclass . '">' 	. mysql2date('j', $currentpostdate) . '</h4>	
							</div>';
						}
						
						// Categories
						if ($item_showcats != "") {
							$categories = nxs_getcategorynameandslugs($currentpostid);
							if (count($categories) > 0) {
								foreach ($categories as $currentcategory) {
									$url = get_category_link($currentcategory["id"]);
									$categorien .= '
										<span class="nxs-categories">
											<a href="' . $url . '">' . $currentcategory["name"] . '</a>
										</span>';
								}
							}
						}
						
						// Author
						if ($item_showauthor != "") {
							$authorurl = get_author_posts_url($currentpost->post_author);
							$authorname = get_the_author_meta("display_name", $currentpost->post_author);
							$auteur = '
								<span class="nxs-author">
									<a href="' . $authorurl . '">' . $authorname . '</a>
								</span>';
						}
					}
					
					// find images used on page
					$imgblocks = nxs_get_images_in_post($currentpostid);
					// pick the first one
					$item_image_imageid = $imgblocks[0];	//
					
					$item_image_alt = $currentposttitle;
					$item_image_title = $currentposttitle;
					$htmlforimage = nxs_gethtmlforimage($item_image_imageid, $item_image_border_width, $item_image_size, $item_image_alignment, $item_image_shadow, $item_image_alt, $item_destination_articleid, $item_destination_url, $item_image_title, $grayscale, $enlarge);
					
					if ($post_password_required)
					{
						// suppress
						$htmlforimage = "";
					}
					
					// Excerpt
					$currentexcerpt = "";
					if ($item_text_truncatelength != "") 
					{						
						// if the excerpt is set, use that one
						$postforexcerpt = get_post($currentpostid);
						$currentexcerpt = $postforexcerpt->post_excerpt;
						
						if (empty($currentexcerpt))
						{							
							$textblocks = nxs_get_text_blocks_on_page_v2($currentpostid, "");
		
							/* ---- */
							
							//$currentexcerpt = "TEST;$currentpostid;" . $currentexcerpt;
							//var_dump($textblocks);
	
							// concatenate the blocks if multiple ones exist
							foreach ($textblocks as $currenttextblock) 
							{
								$currentexcerpt .= $currenttextblock;
							}
						}
						else
						{
							// stick to the excerpt as defined in the post
						}
					}
					
					// Blog truncation
					if ($item_text_truncatelength != "" && $item_text_truncatelength != "0") 
					{
						$currentexcerptbefore = $currentexcerpt;
						$currentexcerpt = nxs_truncate_string($currentexcerpt, intval($item_text_truncatelength));
						if ($currentexcerptbefore == $currentexcerpt)
						{
						}
						else
						{
							$currentexcerpt .= $item_text_appendchars;
						}
					}
		
					if ($item_text_truncatelength != "" && $item_text_truncatelength != "0") 
					{
						$tekst = '<p class="nxs-default-p nxs-padding-bottom0 ' . $item_text_fontzen_cssclass . '"><span>' . $currentexcerpt . '</span></p>';
					}
					else
					{
					}
					
					// Blogentry button
					if ($item_button_text != "") {
						$item_button_html = '
							<p class="' . $item_button_alignment . ' nxs-padding-bottom0">
								<a class="nxs-button ' . $item_button_scale . ' ' . $item_button_color_cssclass . '" href="' . $currentposturl . '">' . $item_button_text . $item_button_icon_right . '</a>
							</p>';
					}

					/*					
					// Social media and comments button
					if ($item_showcommentscount != "" && $commentsprovider != ""){
						nxs_requirecommentsprovider($commentsprovider);
												
						$postcommentcounthtml = nxs_commentsprovider_getpostcommentcounthtml($currentpostid);
						$comments = '<li>' . $postcommentcounthtml . '</li>'; 
					}
					*/
					
					if ($twitter != "") {
						$twitter = '
						<li>
							<a target="_blank" href="https://twitter.com/share?url=' . $currentencodedposturl . '&text=' . $currentencodedtitle . '">
								<span class="nxs-icon-twitter-2"></span>
				  			</a>
				  		</li>
				  	';
					}
					if ($facebook != "") {
						$facebook = '
						<li>
							<a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' . $currentencodedposturl . '">
								<span class="nxs-icon-facebook"></span>
							</a>
						</li>
						';
					}
					if ($linkedin != "") { 
						$linkedin = '
						<li>
							<a target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&url=' . $currentencodedposturl . '&title=' . $currentencodedtitle . '">
								<span class="nxs-icon-linkedin"></span>
							</a>
						</li>
						'; 
					}
					if ($googleplus != "") {
						$googleplus = '
						<li>
							<a target="_blank" href="https://plus.google.com/share?url=' . $currentencodedposturl . '" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\'); return false;">
								<span class="nxs-icon-google-plus"></span>
							</a>
						</li>
						';
					}
				
					/*
					if ($comments != "") {
						$icon_font_list_comments ='	
								<ul class="icon-font-list nxs-float-right nxs-relative comments">'
									. $comments
									. '
								</ul>';
					}
					*/
					
					if ($twitter != "" || $facebook != "" || $linkedin!= "" || $googleplus != "") {
						$icon_font_list_sharing ='	
								<ul class="icon-font-list nxs-float-right">'
									. $twitter
									. $facebook
									. $linkedin
									. $googleplus
									. '
								</ul>';
					}

					// password
					if ($post_password_required)
					{
						$currentexcerpt = "Protected content";
						$tekst = '<p class="nxs-default-p nxs-padding-bottom0"><span>' . $currentexcerpt . '</span></p>';
					}
									
					/* RENDERING OF INDIVIDUAL BLOGENTRY
					---------------------------------------------------------------------------------------------------- */
					
					echo '
						<div class="nxs-blogentry nxs-relative">
							<div class="info-wrapper">';
		
								echo $blogtitel;
								
								echo '<div class="nxs-applylinkvarcolor">';
								
									// Meta data
									echo '<div class="nxs-blog-meta">';
										echo $date_of_current_post;
										if ( $date_of_current_post != "" && $categorien != "" || $date_of_current_post != "" && $auteur != "" ) { echo '<span class="nxs-separator first"> | </span>'; }
										echo $categorien;
										if ( $categorien != "" && $auteur != ""	) { echo '<span class="nxs-separator"> | </span>'; }
										echo $auteur;
									echo '</div>';
									
									// (Sharing) Buttons
									echo '<div class="nxs-blog-sharing">';	
										echo $icon_font_list_sharing;
										if ( $icon_font_list_comments != "" && $icon_font_list_sharing != "" ) { echo '<span class="nxs-separator nxs-float-right"> | </span>'; }
										echo $icon_font_list_comments;
									echo '</div>';
									
									echo '<div class="nxs-clear"></div>';
								
								echo '</div>
							
							</div> ';
							
							echo $htmlforimage;
							echo $tekst;
							echo $item_button_html;
							
							if ($items_filter_maxcount != 1) { echo '<div class="nxs-clear nxs-padding-top20"></div>'; }
				
						echo '</div>';
				
					// Resetting categories
					$categorien = "";
					
					/* ---------------------------------------------------------------------------------------------------- */
				}
				if (count($pages) == 0 && isset($altflow_nomatches_alttext) && $altflow_nomatches_alttext != "")
				{
					// Rendering of individual blogentry
					
					echo '

						<div class="nxs-blogentry nxs-relative">
							<div class="info-wrapper">';
								echo $altflow_nomatches_alttext;
							echo '<div class="nxs-clear"></div>';
						echo '</div>';
				}
				
				if ($facebookcounters != "")
				{
					// re-trigger rendering of FB like boxes
				  ?>
				 	<script type='text/javascript'>
						// load and render DOM
						nxs_js_inject_facebook();
					</script>
				  <?php
				}
			} else {
				nxs_renderplaceholderwarning(nxs_l18n__("Unsupported items_layout; ", "nxs_td") . $items_layout);
			}
			
		
			// lazy loading 
			if ($paginglazyload_enable != "") {
				echo '<a class="nxs-button load-more ' . $paginglazyload_button_scale_cssclass . ' ' . $paginglazyload_button_color_cssclass . '" href="#" onclick="nxs_js_lazyloadmoreblogs(this); return false;">' . $paginglazyload_button_text . $paginglazyload_button_icon_right_html . '</a>';
			}
			
			// Pagination
			if ($paginghtml != "") {
				echo $paginghtml;
			}

		echo '</div>';
	}
	
	if ($button_text != "") {echo '<div class="nxs-clear padding"></div>';}
		echo $button_html;

		
	/* ------------------------------------------------------------------------------------------------- */
	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	return $result;
}

function nxs_widgets_blog_initplaceholderdata($args)
{
	extract($args);

	$args['items_filter_maxcount'] = 3;
	$args['items_layout'] = 'extended';
	$args['items_order'] = "present to past";
	$args['title_heading'] = "2";
	$args['title_fontzen'] = "";
	$args['item_showdate'] = "true";
	$args['item_showcats'] = "true";
	
	$args['item_text_truncatelength'] = "300";
	$args['item_text_appendchars'] = "[...]";
	$args['item_button_text'] = nxs_l18n__("Read more", "nxs_td");
	$args['item_button_color'] = "base2";
	$args['item_title_heading'] = "3";
	$args['item_title_fontzen'] = "";
	$args['item_text_fontzen'] = "";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_blog_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
