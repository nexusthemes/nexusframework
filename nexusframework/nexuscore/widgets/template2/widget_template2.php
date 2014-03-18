<?php

function nxs_widgets_template2_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-carousel";
}

// Setting the widget title
function nxs_widgets_template2_gettitle() {
	return nxs_l18n__("Text[nxs:widgettitle]", "nxs_td");
}

// Unistyle
function nxs_widgets_template2_getunifiedstylinggroup() {
	return "textwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_template2_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_template2_gettitle(),
		"sheeticonid" => nxs_widgets_template2_geticonid(),
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/text-widget/"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_template2_getunifiedstylinggroup(),
		),
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

function nxs_widgets_template2_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_template2_getunifiedstylinggroup(), $unistyle);
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

	//
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
	
	// Title fontsize
	$title_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $title_fontsize);

	// Title height (across titles in the same row)
	// This function does not fare well with CSS3 transitions targeting "all"
	$heightiqprio = "p1";
	$title_heightiqgroup = "title";
  	$titlecssclasses = $title_fontsize_cssclass;
	$titlecssclasses = nxs_concatenateargswithspaces($titlecssclasses, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$title_heightiqgroup}");
	
	// Title
	$htmltitle = '<' . $title_heading . ' class="nxs-title ' . $title_alignment_cssclass . ' ' . $title_fontsize_cssclass . ' ' . $titlecssclasses . '">' . $title . '</' . $title_heading . '>';

		
	function randomImage ( $array ) {
		$total = count($array);
		$call = rand(0,$total-1);
		return $array[$call];
	}
	
	$carousel = array (
		'<a href="http://www.nu.nl" target="_blank"><img src="http://89.18.175.44/oberon/wp-content/uploads/sites/43/2013/11/sponsor-vantol.png" /></a>',
		'<a href="http://www.nu.nl" target="_blank"><img src="http://89.18.175.44/oberon/wp-content/uploads/sites/43/2013/11/sponsor-vechtwijck.png" /></a>',
		'<a href="http://www.nu.nl" target="_blank"><img src="http://89.18.175.44/oberon/wp-content/uploads/sites/43/2013/11/sponsor-kassel.png" /></a>',
		'<img src="http://89.18.175.44/oberon/wp-content/uploads/sites/43/2013/11/sponsor-muhl.png" />',
		'<img src="http://89.18.175.44/oberon/wp-content/uploads/sites/43/2013/11/sponsor-dio.png" />',
		'<img src="http://89.18.175.44/oberon/wp-content/uploads/sites/43/2013/11/sponsor-dennis.png" />'
	);
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) {
		if ($alternativehint == "") {
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} else {
		
		echo $htmltitle;
		
		if ( $title != "" ) { echo '<div class="nxs-clear padding"></div>'; }
		
		echo '
		<div class="nxs-logo">
			' . randomImage($carousel) . '
		</div>	
		';
			
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

function nxs_widgets_template2_initplaceholderdata($args)
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
	$unistylegroup = nxs_widgets_template2_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
