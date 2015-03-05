<?php

// the functions in here are used (invoked and mixed) by the function 
// nxs_extend_widgetoptionfields(&$existingoptions, $extendoptions)
// of nxsfunctions.php

// extended options (injected by nxs_extend_widgetoptionfields)
function nxs_widgets_generic_title_getoptions($args)
{
	$options = array
	(
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
				"placeholder" 		=> nxs_l18n__("Title goes here", "nxs_td"),
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
				"type" 				=> "halign",
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
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
		)
	);
	return $options;
}

// extended options (injected by nxs_extend_widgetoptionfields)
function nxs_widgets_generic_image_getoptions($args)
{
	$options = array
	(
		"fields" => array
		(
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
				"label" 			=> nxs_l18n__("Image", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to upload an image for your bio profile use this option.", "nxs_td"),
				"localizablefield"	=> true
			),
			array
			( 
				"id" 				=> "image_shadow",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Image shadow", "nxs_td"),
				"unistylablefield"	=> true
			),						
			array
			(
				"id" 				=> "image_alt",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Alternate text", "nxs_td"),
				"localizablefield"	=> true,
				"requirecapability" => nxs_cap_getdesigncapability(),
			),		
			array
			(
				"id" 				=> "image_border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Border size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_width"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
		)
	);
	return $options;
}

// extended options (injected by nxs_extend_widgetoptionfields)
function nxs_widgets_generic_link_getoptions($args)
{
	$options = array
	(
		"fields" => array
		(
			// LINK
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Link", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Article link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the image to an article within your site.", "nxs_td"),
			),
			array(
				"id" 				=> "destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("External link", "nxs_td"),
				"placeholder"		=> nxs_l18n__("http://www.nexusthemes.com", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the image to an external source using the full url.", "nxs_td"),
				"localizablefield"	=> true
			),
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
		)
	);
	return $options;
}

// extended options (injected by nxs_extend_widgetoptionfields)
function nxs_widgets_generic_backgroundstyle_getoptions($args) 
{
	$options = array
	(
		"fields" => array
		(			
			
			// Background & Alignment
						
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"heading_cssclass"	=> "generic",
				"label" 			=> nxs_l18n__("Advanced properties: background & alignment", "nxs_td"),
				"initial_toggle_state"	=> "closed",
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "ph_padding",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Background spacing", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("padding"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "ph_border_radius",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Background border radius", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_radius"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "ph_border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Border width", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_radius"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "ph_margin_bottom",
				"type" 				=> "select",
				"label" 			=> "Margin bottom",
				"dropdown" 			=> nxs_style_getdropdownitems("margin"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "ph_valign",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Vertical alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("valign"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),
			
			// COLORS & TEXT
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"heading_cssclass"	=> "generic",
				"label" 			=> nxs_l18n__("Advanced properties: colors & text", "nxs_td"),
				"initial_toggle_state"	=> "closed",
				"unistylablefield"	=> true
			),
			
			array( 
				"id"				=> "ph_colorzen",
				"type" 				=> "colorzen",
				"focus"				=> "true",
				"label" 			=> nxs_l18n__("Color", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("The background color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "ph_linkcolorvar",
				"type" 				=> "colorvariation",
				"scope" 			=> "link",
				"label" 			=> nxs_l18n__("Link color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id"				=> "ph_text_fontsize",
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
			
			// UNISTYLE & UNICONTENT
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"heading_cssclass"	=> "generic",
				"label" 			=> nxs_l18n__("Advanced properties: unistyle & unicontent", "nxs_td"),
				"initial_toggle_state"	=> "closed",
				"requirecapability" => nxs_cap_getdesigncapability(),
			),
			
			array(
				"type" 				=> "unistyle",
				"requirecapability" => nxs_cap_getdesigncapability(),
			),
			array(
				"id" 				=> "unicontent",
				"type" 				=> "unicontent",
				"label" 			=> nxs_l18n__("Unicontent", "nxs_td"),
				"tooltip" => nxs_l18n__("Re-use the content properties of this widget type on your site by entering the same unicontent.", "nxs_td"),
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
				"requirecapability" => nxs_cap_getdesigncapability(),
			),
			
			// CSS
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"heading_cssclass"	=> "generic",
				"label" 			=> nxs_l18n__("Advanced properties: CSS class", "nxs_td"),
				"initial_toggle_state"	=> "closed",
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "ph_cssclass",
				"type" 				=> "input",
				"label" 			=> "CSS class",
				"placeholder"		=> nxs_l18n__("class1 class2 class3", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Seperate the items with a space to add multiple ones.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),

			// LOCK
			
			array( 
				"id"				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"heading_cssclass"	=> "generic",
				"label" 			=> nxs_l18n__("Advanced properties: lock", "nxs_td"),
				"initial_toggle_state"	=> "closed",
				"requirecapability" => nxs_cap_getdesigncapability(),
			),
			
			array(
				"type" 				=> "lock",
				"requirecapability" => nxs_cap_getdesigncapability(),
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
				"requirecapability" => nxs_cap_getdesigncapability(),
			),

		)
	);
	
	// include the typeconverter feature too
	// nxs_extend_widgetoptionfields($options, array("widgettypeconverter"));
	
	return $options;
}

function nxs_widgets_generic_widgettypeconverter_getoptions($args) 
{
	$options = array
	(
		"fields" => array
		(
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"heading_cssclass"	=> "generic",
				"label" 			=> nxs_l18n__("Advanced properties: type converter", "nxs_td"),
				"initial_toggle_state"	=> "closed",
				"requirecapability" => nxs_cap_getdesigncapability(),
			),
			
			array(
				"type" 				=> "widgettypeconverter",
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
				"requirecapability" => nxs_cap_getdesigncapability(),
			),
		)
	);
	
	return $options;
}

// UNISTYLE
function nxs_widgets_generic_unistyle_getoptions($args) 
{
	$options = array
	(
		"fields" => array
		(
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"heading_cssclass"	=> "generic",
				"label" 			=> nxs_l18n__("Advanced properties: unistyle", "nxs_td"),
				"initial_toggle_state"	=> "closed",
				"requirecapability" => nxs_cap_getdesigncapability(),
			),
			
			array(
				"type" 				=> "unistyle",
				"requirecapability" => nxs_cap_getdesigncapability(),
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
				"requirecapability" => nxs_cap_getdesigncapability(),
			),
		)
	);
	
	return $options;
}

//

function nxs_widgets_generic_metaview($optionvalues, $args, $runtimeblendeddata) 
{
	ob_start();
	
	extract($optionvalues);
	
	$placeholderid = $args["clientpopupsessioncontext"]["placeholderid"];
	$postid = $args["clientpopupsessioncontext"]["postid"];
	$containerpostid = $args["clientpopupsessioncontext"]["containerpostid"];
	
	// handle triggers
	$action = $args["clientshortscopedata"]["action"];
	if ($action == "convertthemereference")
	{
		$updatedvalues = array();
		$updatedvalues["type"] = "themereference";
		nxs_mergewidgetmetadata_internal($postid, $placeholderid, $updatedvalues);
	}
	else if ($action == "convertwidget")
	{
		$action = $args["clientshortscopedata"]["override"];
		
		// delegate the upgrade request to specific function
		require_once(NXS_FRAMEWORKPATH . '/nexuscore/includes/nxsupgrader.php');
		nxs_upgrade_widget($action, $postid, $placeholderid);
		
		// trigger the client to refresh the row for the updated placeholder
		?>
		<script type='text/javascript'>
			nxs_js_rerender_row_for_placeholder('<?php echo $postid;?>', '<?php echo $placeholderid;?>');
		</script>
		<?php
	}
	
	$widgetmetadata = nxs_getwidgetmetadata($postid, $placeholderid);
	// sort by key
	ksort($widgetmetadata);
	
	$output .= "<div class='nxs-debug-meta'>";
	$output .= "<ul>";
	foreach ($widgetmetadata as $currentkey => $currentval)
	{
		$output .= "<li>";
		$output .= nxs_render_html_escape_gtlt("'$currentkey' => '$currentval',");
		$output .= "</li>";
	}
	$output .= "</ul>";
	$output .= "</div>";	
	echo $output;
	
	?>
	<!--
	<a href='#' onclick="nxs_js_popup_setshortscopedata('action', 'convertwidget'); nxs_js_popup_setshortscopedata('override', 'false'); nxs_js_popup_refresh(); return false;" class='nxsbutton1'>CONVERT (no overrides)</a>
	<a href='#' onclick="nxs_js_popup_setshortscopedata('action', 'convertwidget'); nxs_js_popup_setshortscopedata('override', 'true'); nxs_js_popup_refresh(); return false;" class='nxsbutton1'>OVERRIDE</a>
	-->
	<!--
	<a href='#' onclick="nxs_js_popup_setshortscopedata('action', 'convertthemereference'); nxs_js_popup_refresh(); return false;" class='nxsbutton1'>CONVERT TO THEMEREFERENCE</a>
	-->
	<?php
	
	$result = ob_get_contents();
	ob_end_clean();
	return $result;
}

function nxs_widgets_generic_debug_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_l18n__("Debug", "nxs_td"),
		"sheeticonid" => "nxs-icon-search",
		"fields" => array
		(			
						
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"heading_cssclass"	=> "generic",
				"label" 			=> nxs_l18n__("Debug", "nxs_td"),
			),

			array(
				"id" 				=> "metaview",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_widgets_generic_metaview",
				"label" 			=> nxs_l18n__("Metaview", "nxs_td"),
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),			
		)
	);
	return $options;
}

function nxs_widgets_generic_unlock_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_l18n__("Unlock", "nxs_td"),
		"sheeticonid" => "nxs-icon-unlocked",
		"fields" => array
		(			
						
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"heading_cssclass"	=> "generic",
				"label" 			=> nxs_l18n__("Unlock", "nxs_td"),
			),
			
			array(
				"type" 				=> "lock",
			),
			
			array( 
				"id"				=> "wrapper_end",
				"type"				=> "wrapperend"
			),
		)
	);
	return $options;
}

?>
