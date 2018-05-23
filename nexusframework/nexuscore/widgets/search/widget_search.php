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
		"supporturl" => "https://www.wpsupporthelp.com/wordpress-questions/search-wordpress-questions-113/",
		"unifiedstyling" 	=> array
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
	
	// data protection handling
	if (true)
	{
		$activity = "nexusframework:widget_search";
		if (!nxs_dataprotection_isactivityonforuser($activity))
		{
			// not allowed
			$result["html"] = "";
		}
	}
	
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

function nxs_dataprotection_nexusframework_widget_search_getprotecteddata($args)
{
	$result = array
	(
		"controller_label" => "Site Search",
		"subactivities" => array
		(
			// if widget has properties that pull information from other 
			// vendors (like scripts, images hosted on external sites, etc.) 
			// those need to be taken into consideration
			// responsibility for that is the person configuring the widget
			"custom-widget-configuration",
			"wordpress-search",
		),
		"dataprocessingdeclarations" => array	
		(
			array
			(
				"use_case" => "(belongs_to_whom_id) can search for information in the website owned by the (controller) that renders captchas using the search widget of the framework",
				"what" => "The searchphrase, IP address of the (belongs_to_whom_id) as well as 'Request header fields' send by browser of ((belongs_to_whom_id)) (https://en.wikipedia.org/wiki/List_of_HTTP_header_fields#Request_fields)",
				"belongs_to_whom_id" => "website_visitor", // (has to give consent for using the "what")
				"controller" => "website_owner",	// who is responsible for this?
				"controller_options" => nxs_dataprotection_factory_getenableoptions("widget:enabled|cookiewall|disabled"),
				"data_processor" => "website_owner", // its not really "processed"; its used to return a list of posts from WP, nothing more nothing less
				"data_retention" => "The search widget of the framework itself does not store the user data",
				"program_lifecycle_phase" => "compiletime",
				"why" => "Its used to allow you (the website visitor) to find information about a specific word or phrase in the website",
				"security" => "Depends; if the site is hosted on a HTTPS website, the user data is transferred over a secure https connection, else its not secure.",
			),
		),
		"status" => "final",
	);
	return $result;
}

?>