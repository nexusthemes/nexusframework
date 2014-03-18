<?php

function nxs_widgets_wooprodlist_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-books";
}

// Setting the widget title
function nxs_widgets_wooprodlist_gettitle() {
	return nxs_l18n__("wooprodlist", "nxs_td");
}

// 
function nxs_widgets_wooprodlist_getunifiedstylinggroup() {
	return "wooprodlistwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_wooprodlist_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$options = array
	(
		"sheettitle" => nxs_widgets_wooprodlist_gettitle(),
		"sheeticonid" => nxs_widgets_wooprodlist_geticonid(),
		//"sheethelp" => nxs_l18n__("http://nexusthemes.com/wooprodlist-widget/"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_wooprodlist_getunifiedstylinggroup(),
		),
		"fields" => array
		(
		
			
		
			// TITLE
			
			array( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array
			( 			
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"tooltip"			=> nxs_l18n__("If you want to give the entire widget a title, you can use this option.", "nxs_td"),
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
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_halignment"),
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
				"id" 				=> "title_height",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align titles", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's title will participate in the title alignment of other partipating widgets in this row", "nxs_td"),
				"unistylablefield"	=> true
			),			
					
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),

			// ------------------------------------------------------------------------------------------------------------
			// SEARCHCRITERIA

			array( 
				"id" 				=> "wrapper_items_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Searchcriteria", "nxs_td"),
			),
			
			
			array(
				"id" 				=> "items_filter_type",
				"type" 				=> "select",
				"popuprefreshonchange"	=> "true",
				"label" 			=> nxs_l18n__("Type", "nxs_td"),
				"dropdown" 			=> array
				(
					"featuredprods"	=>nxs_l18n__("Featured products", "nxs_td"),
					"bestsellerspaid" =>nxs_l18n__("Best sellers (paid)", "nxs_td"),
					"productsonsale" =>	nxs_l18n__("Products on sale", "nxs_td"),
					"recentlyviewed" =>	nxs_l18n__("Recently viewed", "nxs_td"),
					"recentproducts" => nxs_l18n__("Most recently added", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			
			/*
			array(
				"id" 				=> "items_filter_skipcount",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Skip posts", "nxs_td"),
				"dropdown" 			=> array
				("0"=>"0","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9","10"=>"10","11"=>"11","12"=>"12","13"=>"13","14"=>"14","15"=>"15","16"=>"16","17"=>"17","18"=>"18","19"=>"19","20"=>"20")
			),
			*/
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
				"dropdown" 			=> nxs_convertindexarraytoassociativearray(array("", "0","100","110","120","130","140","150","160","170","180","190","200","210","220","230","240","250","260","270","280","290","300","400","500","600")),
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
			
			// SINGLE wooprodlist ENTRY TITLE
			
			array( 
				"id" 					=> "wrapper_output_begin",
				"type" 					=> "wrapperbegin",
				"initial_toggle_state"	=> "closed",
				"label" 				=> nxs_l18n__("Single wooprodlist entry title", "nxs_td"),
				"unistylablefield"	=> true
			),	
				
			array( 
				"id" 				=> "hide_title",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Hide title", "nxs_td"),
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
				"id" 				=> "wrapper_advanceditems_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),
			
			// SINGLE wooprodlist ENTRY IMAGE
			
			array( 
				"id" 					=> "wrapper_output_itemimage_begin",
				"type" 					=> "wrapperbegin",
				"initial_toggle_state"	=> "closed",
				"label" 				=> nxs_l18n__("Single wooprodlist entry image", "nxs_td"),
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
				"id" 				=> "wrapper_advanceditems_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),
			
			// SINGLE wooprodlist ENTRY BUTTON

			array( 
				"id" 				=> "wrapper_items_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Single wooprodlist entry button", "nxs_td"),
				"initial_toggle_state"	=> "closed",		
				"unistylablefield"	=> true		
			),
			
			array(
				"id" 				=> "item_button_text",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Button", "nxs_td"),
				"placeholder"		=> nxs_l18n__("Read more", "nxs_td"),
				"unistylablefield"	=> true
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
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_halignment"),
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
			
			// ------------------------------------------------------------
			
			// EMPTY RESULT
			
			array( 
				"id" 					=> "wrapper_altflow_begin",
				"type" 					=> "wrapperbegin",
				"label" 				=> nxs_l18n__("Handling of alternative flows", "nxs_td"),
				"initial_toggle_state"	=> "closed",				
			),
			
			array( 
				"id" 				=> "altflow_nomatches_alttext",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("No matches", "nxs_td"),
				"tooltip"			=> nxs_l18n__("What message (if any) should be shown if no wooprodlists match the searchcriteria?", "nxs_td"),
			),
			
			array( 
				"id" 				=> "wrapper_altflow_end",
				"type" 				=> "wrapperend"
			),
			
			
			// ------------------------------------------------------------
			
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
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Minimal icon", "nxs_td"),
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

function nxs_widgets_wooprodlist_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_wooprodlist_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);	
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
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
	
	if ($pagingpagination_enable != "" && $paginglazyload_enable != "")
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Ambiguous pagination (only one pagination can be used at a time)", "nxs_td");
	}
	
	if ($pagingpagination_queryparameter == "p" || $pagingpagination_queryparameter == "page" || $pagingpagination_queryparameter == "post")
	{
		$pagingpagination_queryparameter = "pagenr";
	}
	
	global $woocommerce;	
		
	if ($items_filter_type == "featuredprods" || $items_filter_type == "")
	{
		$query_args = array('posts_per_page' => $items_filter_maxcount, 'no_found_rows' => 1, 'post_status' => 'publish', 'post_type' => 'product');
		$query_args['meta_query'] = $woocommerce->query->get_meta_query();
		$query_args['meta_query'][] = array(
			'key' => '_featured',
			'value' => 'yes'
		);
	}
	else if ($items_filter_type == "bestsellerspaid")
	{
		$query_args = array('posts_per_page' => $items_filter_maxcount, 'no_found_rows' => 1, 'post_status' => 'publish', 'post_type' => 'product');
		$query_args['meta_key'] = 'total_sales';
		$query_args['orderby'] = 'meta_value_num';
		$query_args['meta_query'][] = array(
		    'key'     => '_price',
		    'value'   => 0,
		    'compare' => '>',
		    'type'    => 'DECIMAL',
		);
	}
	else if ($items_filter_type == "productsonsale")
	{
		// Get products on sale
		$product_ids_on_sale = woocommerce_get_product_ids_on_sale();
		$product_ids_on_sale[] = 0;

		$meta_query = $woocommerce->query->get_meta_query();

  	$query_args = array(
  		'posts_per_page' 	=> $items_filter_maxcount,
  		'no_found_rows' => 1,
  		'post_status' 	=> 'publish',
  		'post_type' 	=> 'product',
  		'orderby' 		=> 'date',
  		'order' 		=> 'ASC',
  		'meta_query' 	=> $meta_query,
  		'post__in'		=> $product_ids_on_sale
  	);
	}
	else if ($items_filter_type == "recentlyviewed")
	{
		$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
		$viewed_products = array_filter( array_map( 'absint', $viewed_products ) );
		
		$query_args = array('posts_per_page' => $number, 'no_found_rows' => 1, 'post_status' => 'publish', 'post_type' => 'product', 'post__in' => $viewed_products, 'orderby' => 'rand');

		$query_args['meta_query'] = array();
    $query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
    $query_args['meta_query'] = array_filter( $query_args['meta_query'] );
	}
	else if ($items_filter_type == "recentproducts")
	{
		$query_args = array('posts_per_page' => $number, 'no_found_rows' => 1, 'post_status' => 'publish', 'post_type' => 'product');

    $query_args['meta_query'] = array();

    if ( $show_variations == '0' ) 
    {
	    $query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();
			$query_args['parent'] = '0';
    }

    $query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
    $query_args['meta_query']   = array_filter( $query_args['meta_query'] );
	}
	else
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Unsupported filter", "nxs_td") . " " . $items_filter_type;
	}
	
	// Turn on output buffering
	ob_start();
	
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
		$r = new WP_Query($query_args);
		
		global $nxs_global_placeholder_render_statebag;
		if ($shouldrenderalternative == true) {
			$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . "-warning ";
		} else {
			// Appending custom widget class
			$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " ";
		}
			
		/* EXPRESSIONS
		---------------------------------------------------------------------------------------------------- */
		
		
		// Default HMTL rendering
		$htmltitle = nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, "", "");
		
		// Minimal vs. extended
		$wooprodlisttype = 'nxs-wooprodlist-extended';
		
		// Minimal layout icon
		$iconcssclass_font = nxs_getcssclassesforlookup('', $font_icon);
		if 	($font_icon == "") { $font_icon = ''; } 
		else { $font_icon = '<span class="' . $iconcssclass_font . ' font-icon"></span>'; }
		
		/* SINGLE wooprodlist ENTRY BUTTON
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
		$items_filter_catidsarray = explode(",", $items_filter_catids);
	
		$publishedargs = array();
		$publishedargs["post_status"] = "publish";
		$publishedargs["post_type"] = array("page", "post");
		
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
			
			
			ob_start();
			
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
							<a class="current nxs-button ' . $pagination_button_scale_cssclass . ' ' . $pagination_button_color_cssclass . '" href="' . $urlnextpage . '"><span class="nxs-icon-arrow-right-2"></span></a>
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
			
			$paginghtml = ob_get_contents();
			ob_end_clean();
		}
		
		/* OUTPUT
		---------------------------------------------------------------------------------------------------- */
		
		echo '<div class="' . $wooprodlisttype . ' nxs-wooprodlistentries nxs-paging-page-' . $paging_page_class . ' ' . $metadata_layout . '">';
		
		echo $htmltitle;

		$pageindex = 0;
		while ($r->have_posts())
		{
			$r->the_post();
			global $post;
			global $product;
			
			$currentpost = $post;
			$currentproduct = $product;
	
			$pageindex = $pageindex + 1;
			$currentpostid = $currentpost->ID;
			
			$currentposturl = nxs_geturl_for_postid($currentpostid);
			$currentencodedposturl = urlencode($currentposturl);
			$currenttitle = $currentpost->post_title;
			$currentencodedtitle = urlencode($currenttitle);
			$currentpricehtml = $currentproduct->get_price_html();
			$item_destination_articleid = $currentpostid;

			// wooprodlist title			
			if ($hide_title == "") {
				$wooprodlisttitel = '
				<' . $itemheadingelement . ' class="nxs-title nxs-applylinkvarcolor">
					<a href="' . $currentposturl . '">' . $currenttitle . '</a>
				</' . $itemheadingelement . '>';
			}
		
			// Meta data
			if ($item_showdate != "" || $toontijdstip != "" || $item_showcats != "" || $item_showauthor != "") {
				
				// Date
				if ($item_showdate != "" && ($metadata_layout == "" || $metadata_layout == "default")) {
					
					$monthhtml = nxs_getlocalizedmonth(mysql2date('m', $currentpost->post_date));
					$datum = '
						<span class="nxs-day ' . $day_color_cssclass . '">' 	. mysql2date('j', $currentpost->post_date) . '</span>
						<span class="nxs-month ' . $month_color_cssclass . '">' 	. $monthhtml . '</span>
						<span class="nxs-year">' 	. mysql2date('Y', $currentpost->post_date) . '</span>';
				
				} else if ($item_showdate != "" && $metadata_layout == "date-highlight") {
					
					$monthhtml = nxs_getlocalizedmonth(mysql2date('m', $currentpost->post_date));
					$datum = '
					<div class="nxs-date">
						<h4 class="month ' . $month_color_cssclass . '">' 	. $monthhtml . '</h4>
						<h4 class="day ' . $day_color_cssclass . '">' 	. mysql2date('j', $currentpost->post_date) . '</h4>	
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
			
			$item_image_alt = $currenttitle;
			$item_image_title = $currenttitle;
			$htmlforimage = nxs_gethtmlforimage($item_image_imageid, $item_image_border_width, $item_image_size, $item_image_alignment, $item_image_shadow, $item_image_alt, $item_destination_articleid, $item_destination_url, $item_image_title);
			
			// Excerpt
			$currentexcerpt = "";
			if ($item_text_truncatelength != "") {
				$textblocks = nxs_get_text_blocks_on_page_v2($currentpostid, "");
				
				// concatenate the blocks if multiple ones exist
				foreach ($textblocks as $currenttextblock) 
				{
					$currentexcerpt .= $currenttextblock;
				}
			}
			
			// truncation
			if ($item_text_truncatelength != "" && $item_text_truncatelength != "0") {
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

			if ($item_text_truncatelength != "" && $item_text_truncatelength != "0") {
				$tekst = '<p class="nxs-default-p nxs-padding-bottom0"><span>' . $currentexcerpt . '</span></p>';
			}
			
			// wooprodlistentry button
			if ($item_button_text != "") {
				$button = '
					<p class="' . $item_button_alignment . ' nxs-padding-bottom0">
						<a class="nxs-button ' . $item_button_scale . ' ' . $item_button_color_cssclass . '" href="' . $currentposturl . '">' . $item_button_text . $item_button_icon_right . '</a>
					</p>';
			}
			
			// Social media and comments button
			if ($item_showcommentscount != "" && $commentsprovider != ""){
				nxs_requirecommentsprovider($commentsprovider);
										
				$postcommentcounthtml = nxs_commentsprovider_getpostcommentcounthtml($currentpostid);
				$comments = '<li>' . $postcommentcounthtml . '</li>'; 
			}
			
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
		
			if ($comments != "") {
				$icon_font_list_comments ='	
						<ul class="icon-font-list nxs-float-right nxs-relative comments">'
							. $comments
							. '
						</ul>';
			}
			
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
							
			/* RENDERING OF INDIVIDUAL wooprodlistENTRY
			---------------------------------------------------------------------------------------------------- */
			
			echo '
				<div class="nxs-wooprodlistentry nxs-relative">
					
					<div class="info-wrapper">';

						echo $wooprodlisttitel;
						
						echo '<div class="nxs-applylinkvarcolor">';
						
							// Meta data
							echo '<div class="nxs-wooprodlist-meta">';
								echo $datum;
								if ( $datum != "" && $categorien != "" || $datum != "" && $auteur != "" ) { echo '<span class="nxs-separator first"> | </span>'; }
								echo $categorien;
								if ( $categorien != "" && $auteur != ""	) { echo '<span class="nxs-separator"> | </span>'; }
								echo $auteur;
							echo '</div>';
							
							// (Sharing) Buttons
							echo '<div class="nxs-wooprodlist-sharing">';	
								echo $icon_font_list_sharing;
								if ( $icon_font_list_comments != "" && $icon_font_list_sharing != "" ) { echo '<span class="nxs-separator nxs-float-right"> | </span>'; }
								echo $icon_font_list_comments;
							echo '</div>';
							
							echo '<div class="nxs-clear"></div>';
						
						echo '</div>
					
					</div> ';
					
					echo $htmlforimage;
					echo $tekst;
					echo "PRIJS:" . $currentpricehtml;
					echo $button;
					
					echo '<div class="nxs-clear nxs-padding-top20"></div>';
		
				echo '</div>';
		
			// Resetting categories
			$categorien = "";
			
			/* ---------------------------------------------------------------------------------------------------- */
		}
		if (!$r->have_posts() && isset($altflow_nomatches_alttext) && $altflow_nomatches_alttext != "")
		{
			// Rendering of individual wooprodlistentry
			
			echo '

				<div class="nxs-wooprodlistentry nxs-relative">
					<div class="info-wrapper">';
						echo $altflow_nomatches_alttext;
					echo '<div class="nxs-clear"></div>';
				echo '</div>';
		}
		
		if (!isset($altflow_nomatches_alttext) || $altflow_nomatches_alttext == "")
		{
			if (!$r->have_posts())
			{
				nxs_renderplaceholderwarning(nxs_l18n__("No matches[nxs:warning]", "nxs_td"));
			}
		}
		
		// lazy loading 
		if ($paginglazyload_enable != "") {
			echo '<a class="nxs-button load-more ' . $paginglazyload_button_scale_cssclass . ' ' . $paginglazyload_button_color_cssclass . '" href="#" onclick="nxs_js_lazyloadmorewooprodlists(this); return false;">' . $paginglazyload_button_text . $paginglazyload_button_icon_right_html . '</a>';
		}
		
		// Pagination
		if ($paginghtml != "") {
			echo $paginghtml;
		}

		echo '</div>';
		
		// reset main query to where we were
		wp_reset_postdata();
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

function nxs_widgets_wooprodlist_initplaceholderdata($args)
{
	extract($args);
	
	$args['items_filter_type'] = 'featuredprods';

	$args['items_filter_maxcount'] = 3;
	$args['items_layout'] = 'extended';
	$args['items_order'] = "present to past";
	$args['title_heading'] = "2";
	$args['item_showdate'] = "true";
	$args['item_showcats'] = "true";
	
	$args['item_text_truncatelength'] = "300";
	$args['item_text_appendchars'] = "[...]";
	$args['item_button_text'] = nxs_l18n__("Read more", "nxs_td");
	$args['item_button_color'] = "base2";
	$args['item_title_heading'] = "3";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_wooprodlist_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
