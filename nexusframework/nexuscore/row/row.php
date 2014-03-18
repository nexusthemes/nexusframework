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
		"sheettitle" => "Row styling",
		"fields" => array
		(
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
				"tooltip" 			=> "Color of row"
			),
			array( 
				"id" 				=> "r_linkcolorvar",
				"type" 				=> "colorvariation",
				"scope" 				=> "link",
				"label" 			=> "Link color",
			),
			array
			(
				"id" 					=> "r_margin_top",
				"type" 				=> "select",
				"label" 			=> "Margin top",
				"dropdown" 		=> nxs_style_getdropdownitems("margin")
			),
			
			array
			(
				"id" 				=> "r_padding_top",
				"type" 				=> "select",
				"label" 			=> "Padding top",
				"dropdown" 		=> nxs_style_getdropdownitems("padding")
			),
			
			array
			(
				"id" 				=> "r_padding_bottom",
				"type" 				=> "select",
				"label" 			=> "Padding bottom",
				"dropdown" 		=> nxs_style_getdropdownitems("padding")
			),
			
			array
			(
				"id" 				=> "r_margin_bottom",
				"type" 				=> "select",
				"label" 			=> "Margin bottom",
				"dropdown" 		=> nxs_style_getdropdownitems("margin")
			),
			
			array
			(
				"id" 				=> "r_border_top_width",
				"type" 				=> "select",
				"label" 			=> "Border top width",
				"dropdown" 		=> nxs_style_getdropdownitems("border_width")
			),
			
			array
			(
				"id" 				=> "r_border_right_width",
				"type" 				=> "select",
				"label" 			=> "Border right width",
				"dropdown" 		=> nxs_style_getdropdownitems("border_width")
			),
			
			array
			(
				"id" 				=> "r_border_left_width",
				"type" 				=> "select",
				"label" 			=> "Border left width",
				"dropdown" 		=> nxs_style_getdropdownitems("border_width")
			),
			
			array
			(
				"id" 				=> "r_border_bottom_width",
				"type" 				=> "select",
				"label" 			=> "Border bottom width",
				"dropdown" 		=> nxs_style_getdropdownitems("border_width")
			),
			
			array
			(
				"id" 				=> "r_border_radius",
				"type" 				=> "select",
				"label" 			=> "Border radius",
				"dropdown" 		=> nxs_style_getdropdownitems("border_radius")
			),
			
			array
			( 
				"id"				=> "r_cssclass",	// stands for row_cssclass
				"type" 				=> "input",
				"label" 			=> "CSS class",
				"tooltip" 			=> "CSS"
			),
			
			array( 
				"id" 				=> "wrapper_styling_end",
				"type" 				=> "wrapperend"
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