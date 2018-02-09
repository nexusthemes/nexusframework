<?php

function nxs_widgets_search_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_search_gettitle() {
	return nxs_l18n__("Search[widget]", "nxs_td");
}

// Unistyle
function nxs_widgets_search_getunifiedstylinggroup() {
	return "searchwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_search_home_getoptions($args) {

	$options = array
	(
		"sheettitle" => nxs_widgets_search_gettitle(),
		"sheeticonid" => nxs_widgets_search_geticonid(),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_search_getunifiedstylinggroup(),
		),
		"fields" => array (
		
			// TITLE
			
			array ( 
				"id" 				=> "wrapperbegin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title properties", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array ( 
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Title goes here", "nxs_td"),
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
				"id" 				=> "title_heightiq",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align titles", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's title will participate in the title alignment of other partipating widgets in this row", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array ( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
			// DISPLAY
			
			array ( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Display", "nxs_td"),
			),
			
			array ( 
				"id" 				=> "searchplaceholder",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Search placeholder", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("This is the text people see when no text is entered", "nxs_td"),
			),
			array ( 
				"id" 				=> "button_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array ( 
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

function nxs_widgets_search_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_search_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
	if ($render_behaviour == "code")
	{
		//
	}
	else
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
	$shouldrenderalternative = false;
	
	$htmltitle = nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, "", "");
	
	$button_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $button_color);
		
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) {
		if ($alternativehint == "")
		{
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} else {	
		
		echo '<div>';
	
			echo $htmltitle;
			?>
        <div class="search-container">
					<form id="nxs-searchentry-<?php echo $placeholderid; ?>" class="nxs-form" action="<?php echo nxs_geturl_home(); ?>" method="get">
						<input type="hidden" name="trigger" value="nxssearch" />
						<input type="text" value="<?php echo nxs_render_html_escape_doublequote($_REQUEST['s']); ?>" name="s" placeholder="<?php echo nxs_render_html_escape_doublequote($searchplaceholder); ?>" onkeydown="if (event.keyCode == 13) { jQuery('#nxs-searchentry-<?php echo $placeholderid; ?>').submit(); return false; }" />
					</form>
				</div>
				<a href="#" onclick="jQuery('#nxs-searchentry-<?php echo $placeholderid; ?>').submit(); return false;" class="nxs-button <?php echo $button_color_cssclass; ?>">
					<span class="nxs-icon-search"></span>
				</a>
				<div class="nxs-clear"></div>    
			<?php
		
		echo '</div>';
		      
	} 
	
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


function nxs_widgets_search_initplaceholderdata($args)
{
	extract($args);
	
	$args["title"] = nxs_l18n__("title[sample]", "nxs_td");
	$args["title_heading"] = 2;	
	$args["searchplaceholder"] = nxs_l18n__("placeholder[sample]", "nxs_td");
	$args["button_color"] = "base2";
	$args['title_heightiq'] = "true";	
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_search_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>