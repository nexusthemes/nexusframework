<?php

function nxs_widgets_categories_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_categories_gettitle() {
	return nxs_l18n__("categories", "nxs_td");
}

// Unistyle
function nxs_widgets_categories_getunifiedstylinggroup() {
	return "categorieswidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_categories_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_categories_gettitle(),
		"sheeticonid" => nxs_widgets_categories_geticonid(),
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/categories-widget/"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_categories_getunifiedstylinggroup(),
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
				"tooltip"			=> nxs_l18n__("If you want to give the entire widget a title, you can use this option.", "nxs_td"),
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
				"id" 				=> "title_heightiq",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align titles", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's title will participate in the title alignment of other partipating widgets in this row", "nxs_td"),
				"unistylablefield"	=> true
			),
				
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
						
			// DISPLAY
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Display", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "items_filter_includeuncategorized",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Display uncategorized", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Decide if you want to include the uncategorized category (checked), or not (unchecked)", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "items_filter_includeempty",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Display empty", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to include categories that are not linked to articles, check this box", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "items_show_hierarchical",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Alphabetical order", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("To show categories in alphabetical order, check this box", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "font_icon",
				"type" 				=> "icon",
				"label" 			=> nxs_l18n__("Categories icon", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "item_showcount",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Display article count", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("To display the number of articles in each category, check this box", "nxs_td"),
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

function nxs_widgets_categories_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// Unistyle
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_categories_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	$mixedattributes = array_merge($temp_array, $args);
	
	// Localize atts
	$mixedattributes = nxs_localization_localize($mixedattributes);
	
	$title = $mixedattributes['title'];
	$items_filter_includeuncategorized = $mixedattributes['items_filter_includeuncategorized'];
	$items_filter_includeempty = $mixedattributes['items_filter_includeempty'];
	$items_show_hierarchical = $mixedattributes['items_show_hierarchical'];
	$item_showcount = $mixedattributes['item_showcount'];
	
	global $nxs_global_placeholder_render_statebag;

	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;	
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);

	// Turn on output buffering
	ob_start();
	
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-categories ";
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	$cat_args = array('orderby' => 'name', 'title_li' => '');
	if ($items_show_hierarchical != "") {
		$cat_args["hierarchical"] = true;
	} if ($items_filter_includeuncategorized != "") {
		// include uncategorized
	} else {
		// exclude uncategorized
		$cat_args["exclude"] = 1;
	} if ($item_showcount != "") {
		// yes, count!
		$cat_args["show_count"] = true;
	} if ($items_filter_includeempty != "") {
		// yes, show empty items too!
		$cat_args["hide_empty"] = false;
	} else {
		$cat_args["hide_empty"] = true;
	}
	
	// Create font icon class
	$iconcssclass = nxs_getcssclassesforlookup('', $font_icon);
	
	// Font icon
	if ($font_icon == "") { $font_icon = ''; } 
	else { $font_icon = '<span class="' . $iconcssclass . '"></span>'; }
	
	// Return value instead of echo (0)
	$cat_args["echo"] = 0;
	
	$html_categories = wp_list_categories(apply_filters('widget_categories_args', $cat_args));
	
	$html_categories = str_replace('<a', $font_icon . '<a', $html_categories);
	
	// Default HMTL rendering
	$htmltitle = nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, "", "");
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	echo '
	<div ' . $class . '>
		<div class="nxs-applylinkvarcolor">';
	
			echo $htmltitle;
			echo '
			
			<ul>';
				
				echo $html_categories;
				echo '
			
			</ul>
		
		</div>
	</div>';

	/* ------------------------------------------------------------------------------------------------- */	
	
	$html = ob_get_contents();
	ob_end_clean();

	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

function nxs_widgets_categories_initplaceholderdata($args)
{
	extract($args);
	
	$args["items_filter_includeuncategorized"] = "";	// no
	$args["items_filter_includeempty"] = "";	// no
	$args["item_showcount"] = "true";
	$args["items_show_hierarchical"] = "true"; 	
	$args['title_heightiq'] = "true";	
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_categories_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
