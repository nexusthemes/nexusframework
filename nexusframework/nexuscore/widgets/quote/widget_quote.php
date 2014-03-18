<?php

function nxs_widgets_quote_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_quote_gettitle() {
	return nxs_l18n__("Quote[nxs:widgettitle]", "nxs_td");
}


function nxs_widgets_quote_getunifiedstylinggroup() {
	return "quotewidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */


// Define the properties of this widget
function nxs_widgets_quote_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_quote_gettitle(),
		"sheeticonid" => nxs_widgets_quote_geticonid(),
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/quote-widget/"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_quote_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			array( 
				"id" 				=> "wrapper_text_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Quote", "nxs_td"),
			),
			
			array(
				"id" 				=> "text",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Text", "nxs_td"),		
				"placeholder" 		=> nxs_l18n__("Text goes here", "nxs_td"),
				"localizablefield"	=> true
			),		
			array(
				"id" 				=> "source",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Source", "nxs_td"),
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Source URL", "nxs_td")
			),
			array(
				"id" 				=> "quote_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Quote width", "nxs_td"),
				"dropdown" 			=> array(""=>"","90%"=>"90%","80%"=>"80%","70%"=>"70%","60%"=>"60%","50%"=>"50%","40%"=>"40%","30%"=>"30%","20%"=>"20%"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "show_quote_icon",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show quote icon", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_button_end",
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

function nxs_widgets_quote_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "")
	{
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_quote_getunifiedstylinggroup(), $unistyle);
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
	
	if ($text == "")
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Quote not set", "nxs_td");
	}
			
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	if ($shouldrenderalternative == true && $alternativehint == "") {
		$alternativehint = "The widget isn't configured enough to render properly. Define more options.";
	}
	
	// Text
	if ($show_quote_icon != "") { $show_quote_icon = '<span class="nxs-icon-quotes-left"></span>'; }
	
	// Text
	if ($text != "") { $text = $show_quote_icon . '<span class="nxs-default-p quote">' . $text . '</span>';	
	}
	
	// Quote width
	if ($quote_width != ""){
		$quote_width = 'width: ' . $quote_width . ';';
		$quote_alignment = 'nxs-margin-auto';
	}
	
	// Source
	if ($source != "" && $destination_url == ""){ 
		$source = '<p class="nxs-default-p source nxs-padding-bottom0 nxs-padding-top10">' . $source . '</p>'; 
	} else if ($source != "" && $destination_url != ""){
		$source = '
			<a href="' . $destination_url . '" target="_new">
				<p class="nxs-default-p source nxs-padding-bottom0 nxs-padding-top10">' . $source . '</p>
			</a>'; 
	}
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) {
		if ($alternativehint == "") {
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} else {
		
		echo '<div class="nxs-applylinkvarcolor nxs-relative ' . $quote_alignment . '" style="' . $quote_width . '">';
			echo $text;
			echo $source;
		echo '</div>';			
			
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

function nxs_widgets_quote_initplaceholderdata($args)
{
	extract($args);

	$args['show_quote_icon'] = "true";
	$args["text"] = nxs_l18n__("Sample", "nxs_td");

	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_quote_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
