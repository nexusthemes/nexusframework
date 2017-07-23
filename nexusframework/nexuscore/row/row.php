<?php

// is invoked by a webmethod
function nxs_pagerow_render_webpart_row($args)
{
	//
	extract($args);
	
	$rendermode = "default";	
	
	$html = nxs_getrenderedrowhtml($postid, $rowid, $rendermode);
	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_pagerow_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => "Row properties",
		"unifiedstyling" => array
		(
			"group" => nxs_row_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			// -------------------------------------------------------			
			
			// LOOKUPS
			
			array
			( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Lookups", "nxs_td"),
				"initial_toggle_state" => "closed",
			),
			array
      (
				"id" 					=> "r_lookups",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Lookup table", "nxs_td"),
				"footer"			=> "Evaluated line by line when the row renders",
			),
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),
		
			// -------------------------------------------------------
			
			array( 
				"id" 						=> "wrapper_styling_begin",
				"type" 					=> "wrapperbegin",
				"label" 				=> "Styling",
			),
					
			array( 
				"id"				=> "r_colorzen",	// stands for row_color
				"type" 				=> "colorzen",
				"label" 			=> "Color",
				"tooltip" 			=> "Color of row",
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "r_linkcolorvar",
				"type" 				=> "colorvariation",
				"scope" 				=> "link",
				"label" 			=> "Link color",
				"unistylablefield"	=> true
			),
			array
			(
				"id" 					=> "r_margin_top",
				"type" 				=> "select",
				"label" 			=> "Margin top",
				"dropdown" 		=> nxs_style_getdropdownitems("margin"),
				"unistylablefield"	=> true
			),
			
			array
			(
				"id" 				=> "r_padding_top",
				"type" 				=> "select",
				"label" 			=> "Padding top",
				"dropdown" 		=> nxs_style_getdropdownitems("padding"),
				"unistylablefield"	=> true
			),
			
			array
			(
				"id" 				=> "r_padding_bottom",
				"type" 				=> "select",
				"label" 			=> "Padding bottom",
				"dropdown" 		=> nxs_style_getdropdownitems("padding"),
				"unistylablefield"	=> true
			),
			
			array
			(
				"id" 				=> "r_margin_bottom",
				"type" 				=> "select",
				"label" 			=> "Margin bottom",
				"dropdown" 		=> nxs_style_getdropdownitems("margin"),
				"unistylablefield"	=> true
			),
			
			array
			(
				"id" 				=> "r_border_top_width",
				"type" 				=> "select",
				"label" 			=> "Border top width",
				"dropdown" 		=> nxs_style_getdropdownitems("border_width"),
				"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "r_border_bottom_width",
				"type" 				=> "select",
				"label" 			=> "Border bottom width",
				"dropdown" 		=> nxs_style_getdropdownitems("border_width"),
				"unistylablefield"	=> true
			),
			
			array
			(
				"id" 				=> "r_border_radius",
				"type" 				=> "select",
				"label" 			=> "Border radius",
				"dropdown" 		=> nxs_style_getdropdownitems("border_radius"),
				"unistylablefield"	=> true
			),
			
			array
			( 
				"id"				=> "r_cssclass",	// stands for row_cssclass
				"type" 				=> "input",
				"label" 			=> "CSS class",
				"tooltip" 			=> "CSS",
				"unistylablefield"	=> true
			),
			
			array
			(
				"id" 				=> "r_widescreen",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Widescreen row", "nxs_td"),
				"footernote" 			=> nxs_l18n__("Only applies if rows has 1 colum and if the sidebar is turned off for this page", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array
			(
				"id" 				=> "r_enabled",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Enabled", "nxs_td"),
				"footernote" 			=> nxs_l18n__("blank or true if you want the row to be rendered, or something else otherwise", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			
			array
			( 
				"id" 				=> "wrapper_styling_end",
				"type" 				=> "wrapperend"
			),
			
			// UNISTYLE & UNICONTENT
			
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

			// -------------------------------------------------------
			
		),
	);
	
	return $options;
}

function nxs_pagerow_home_getsheethtml($args)
{
	$result = nxs_genericpopup_getpopuphtml($args);
	return $result;
}

?>