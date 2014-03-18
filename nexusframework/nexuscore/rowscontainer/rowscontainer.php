<?php

function nxs_rowscontainer_backgroundstyle_getoptions($args)
{
	$options = array
	(
		"sheettitle" => "Container styling",
		"fields" => array
		(
			array
			( 
				"id"				=> "rc_colorzen",
				"type" 				=> "colorzen",
				"label" 			=> "Color",
				"focus"				=> "true",
				"tooltip" 			=> "The background color"
			),
			array( 
				"id" 				=> "rc_linkcolorvar",
				"type" 				=> "colorvariation",
				"scope" 				=> "link",
				"label" 			=> "Link color",
			),
			array
			(
				"id" 				=> "rc_margin_top",
				"type" 				=> "select",
				"label" 			=> "Margin top",
				"dropdown" 			=> nxs_style_getdropdownitems("margin")
			),
			array
			(
				"id" 				=> "rc_padding_top",
				"type" 				=> "select",
				"label" 			=> "Padding top",
				"dropdown" 			=> nxs_style_getdropdownitems("padding")
			),
			array
			(
				"id" 				=> "rc_padding_bottom",
				"type" 				=> "select",
				"label" 			=> "Padding bottom",
				"dropdown" 			=> nxs_style_getdropdownitems("padding")
			),
			array
			(
				"id" 				=> "rc_margin_bottom",
				"type" 				=> "select",
				"label" 			=> "Margin bottom",
				"dropdown" 			=> nxs_style_getdropdownitems("margin")
			),
			array
			(
				"id" 				=> "rc_border_top_width",
				"type" 				=> "select",
				"label" 			=> "Border top width",
				"dropdown" 			=> nxs_style_getdropdownitems("border_width")
			),
			array
			(
				"id" 				=> "rc_border_right_width",
				"type" 				=> "select",
				"label" 			=> "Border right width",
				"dropdown" 			=> nxs_style_getdropdownitems("border_width")
			),
			array
			(
				"id" 				=> "rc_border_left_width",
				"type" 				=> "select",
				"label" 			=> "Border left width",
				"dropdown" 			=> nxs_style_getdropdownitems("border_width")
			),
			array
			(
				"id" 				=> "rc_border_bottom_width",
				"type" 				=> "select",
				"label" 			=> "Border bottom width",
				"dropdown" 			=> nxs_style_getdropdownitems("border_width")
			),
			array
			(
				"id" 				=> "rc_border_radius",
				"type" 				=> "select",
				"label" 			=> "Border radius",
				"dropdown" 			=> nxs_style_getdropdownitems("border_radius")
			),
			array
			(
				"id" 				=> "rc_cssclass",
				"type" 				=> "input",
				"label" 			=> "CSS Class",
				"placeholder"		=> "class1 class2 class3",
				"tooltip" 			=> "Seperate the items with a space to add multiple ones."
			)
		)
	);
	return $options;
}

?>