<?php

function nxs_getmaxservercsschunks() {
	// todo: eventually make configurable
	return 4;
}

function nxs_showloadcover() {
	// todo: eventually make configurable
	return false;
}

function nxs_getcoloralphas() {
	$result = array();
	$result[] = 1;
	$result[] = 0.8;
	$result[] = 0.7;
	$result[] = 0.6;
	$result[] = 0.5;
	$result[] = 0.2;
	$result[] = 0.1;
	$result[] = 0.0;
	
	// enable themes to overrule the alphas
	$args = array();
	$result = apply_filters("nxs_getcoloralphas", $result, $args);
	
	return $result;
}

function nxs_getcolorsinpalette() {
	$result = array();
	$result[] = "base";
	$result[] = "c1";
	
	// enable themes to overrule the colors
	$args = array();
	$result = apply_filters("nxs_getcolorsinpalette", $result, $args);
	
	return $result;
}

function nxs_getcssclassesforlookup($prefix, $value) {
	$result = "";
	if (!isset($value) || $value == "" || $value == "inherit") {
		// suppress css class (will be inherited)
	} else {
		$derived = "";
		if (nxs_stringstartswith($prefix, "nxs-colorzen-")) {
			$derived = "nxs-colorzen";
		} else if (nxs_stringstartswith($prefix, "nxs-linkcolorvar-")) {
			$derived = "nxs-linkcolorvar";
		}
		$result = nxs_concatenateargswithspaces($result, $derived, $prefix . $value);
	}
	return $result;
}

function nxs_getstyletypeoptions() {
	// TODO: add translations, and cache result in mem (global var) instead of having
	// to reload it everytime its used
	$options = array
	(
		"button_scale" => array (
			"subtype" 			=> "multiplier",
			"values" 			=> array(0.8, 1, 1.2, 1.4, 1.5, 1.6, 1.8, 2.0, 2.2, 2.5, 2.6, 3.0),
		),
		"icon_scale" => array (
			"subtype" 			=> "multiplier",
			"values" 			=> array("",0.5, 1.0, 1.5, 2.0, 2.5, 3.0),	// steps of 32 pixels
		),
		"title_heading" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array("1"=>"H1", "2"=>"H2", "3"=>"H3", "4"=>"H4", "5"=>"H5", "6"=>"H6")
		),
		"image_halignment" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array("left"=>nxs_l18n__("left", "nxs_td"), "right"=>nxs_l18n__("right", "nxs_td")),
			"icons" 			=> array("left"=>nxs_l18n__("leftalign", "nxs_td"), "right"=>nxs_l18n__("rightalign", "nxs_td"))
		),
		"button_halignment" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array("left"=>nxs_l18n__("left", "nxs_td"), "center"=>nxs_l18n__("center", "nxs_td"), "right"=>nxs_l18n__("right", "nxs_td")),
			"icons" 			=> array("left"=>nxs_l18n__("leftalign", "nxs_td"), "center"=>nxs_l18n__("centeralign", "nxs_td"), "right"=>nxs_l18n__("rightalign", "nxs_td"))
		),
		"title_halignment" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array("left"=>nxs_l18n__("left", "nxs_td"), "center"=>nxs_l18n__("center", "nxs_td"), "right"=>nxs_l18n__("right", "nxs_td")),
			"icons" 			=> array("left"=>nxs_l18n__("leftalign", "nxs_td"), "center"=>nxs_l18n__("centeralign", "nxs_td"), "right"=>nxs_l18n__("rightalign", "nxs_td"))
		),
		"text_halignment" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array("left"=>nxs_l18n__("left", "nxs_td"), "center"=>nxs_l18n__("center", "nxs_td"), "right"=>nxs_l18n__("right", "nxs_td")),
			"icons" 			=> array("left"=>nxs_l18n__("leftalign", "nxs_td"), "center"=>nxs_l18n__("centeralign", "nxs_td"), "right"=>nxs_l18n__("rightalign", "nxs_td"))
		),
		"delaypopup_seconds" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array("0"=>nxs_l18n__("No delay", "nxs_td"), "5"=>nxs_l18n__("5 seconds", "nxs_td"), "20"=>nxs_l18n__("20 seconds", "nxs_td"))
		),
		"repeatpopup_scope" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array
			(
				"eachrequest"	=>nxs_l18n__("1x per request", "nxs_td"), 
				"eachnewsession"=>nxs_l18n__("1x per session", "nxs_td"), 
				"onlyonce"		=>nxs_l18n__("1x per cookie", "nxs_td"),
			)
		),
		"border_width" => array (
			"subtype" 			=> "multiplier",
			"values" 			=> array("", 0, 1, 2, 3, 4, 5, 6, 8, 10),
		),
		"maxheight" => array (
			"subtype" 			=> "multiplier",
			"values" 			=> array("", 10,20,30,40,50,60,70,80,90,100),
		),
		"maxwidth" => array (
			"subtype" 			=> "multiplier",
			"values" 			=> array("", 10,20,30,40,50,60,70,80,90,100),
		),
		"fontsize" => array (
			"subtype" 			=> "multiplier",
			"values" 			=> array("", 0.8, 1.0, 1.2, 1.4, 1.6, 1.8, 2.0, 2.2, 2.4, 2.6, 2.8, 3.0, 4.0, 5.0, 6.0),
		),
		"halign" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array("left"=>nxs_l18n__("left", "nxs_td"), "center"=>nxs_l18n__("center", "nxs_td"), "right"=>nxs_l18n__("right", "nxs_td")),
			"icons" 			=> array("left"=>nxs_l18n__("leftalign", "nxs_td"), "center"=>nxs_l18n__("centeralign", "nxs_td"), "right"=>nxs_l18n__("rightalign", "nxs_td"))
		),
		"orientation" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array("horizontal"=>nxs_l18n__("horizontal", "nxs_td"),"vertical"=>nxs_l18n__("vertical", "nxs_td")),
		),
		"valign" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array("nxs-valign-top"=>nxs_l18n__("top", "nxs_td"),"nxs-valign-middle"=>nxs_l18n__("center", "nxs_td"),"nxs-valign-bottom"=>nxs_l18n__("bottom", "nxs_td")),
			"icons" 			=> array("nxs-valign-top"=>nxs_l18n__("topalign", "nxs_td"), "nxs-valign-middle"=>nxs_l18n__("middlealign", "nxs_td"), "nxs-valign-bottom"=>nxs_l18n__("bottomalign", "nxs_td"))
		),
		"image_size" => array (
			"subtype" 			=> "encodedmultiplier",
			"values" 			=> array
				(
					"orig@contain@4-0"	=>nxs_l18n__("width 4x (contained)", "nxs_td"),
					"orig@contain@10-0"	=>nxs_l18n__("width 10x (contained)", "nxs_td"),
					"orig@contain@15-0"	=>nxs_l18n__("width 15x (contained)", "nxs_td"),
					"orig@contain@22-0"	=>nxs_l18n__("width 22x (contained)", "nxs_td"),

					"c@0-75"		=>nxs_l18n__("cropped icon 0.75x", "nxs_td"),
				 	"c@1-0"		=>nxs_l18n__("cropped icon 1x", "nxs_td"),
				 	"c@1-5"		=>nxs_l18n__("cropped icon 1.5x", "nxs_td"),
				 	"c@2-0"		=>nxs_l18n__("cropped icon 2x", "nxs_td"),

					"auto-fit"	=>nxs_l18n__("stretch (contained)", "nxs_td"),
					"orig@contain"	=>nxs_l18n__("as-is (contained)", "nxs_td"),
					"-"			=>nxs_l18n__("none", "nxs_td"),
				),
		),
		"backgroundimage_repeat" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array
				(
					"-"			=>nxs_l18n__("none", "nxs_td"),
					"repeatx"	=>nxs_l18n__("repeatx", "nxs_td"), 
					"repeaty"	=>nxs_l18n__("repeaty", "nxs_td"), 
					"repeatxy"	=>nxs_l18n__("repeatxy", "nxs_td"), 
				)
		),
		"backgroundimage_size" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array
				(
					"-"			=>nxs_l18n__("as-is", "nxs_td"),
					"cover"		=>nxs_l18n__("cover", "nxs_td"), 
					"contain"	=>nxs_l18n__("contain", "nxs_td"), 
				)
		),
		"backgroundimage_position" =>  array (
			"subtype" 			=> "textlookup",
			"values" 			=> array 
			(
				"left top"			=> nxs_l18n__("left top", "nxs_td"),
				"center top"		=> nxs_l18n__("center top", "nxs_td"), 
				"right top"			=> nxs_l18n__("right top", "nxs_td"), 
				"left center"		=> nxs_l18n__("left center", "nxs_td"),
				"center center"		=> nxs_l18n__("center center", "nxs_td"), 
				"right center"		=> nxs_l18n__("right center", "nxs_td"), 
				"left bottom"		=> nxs_l18n__("left bottom", "nxs_td"),
				"center bottom"		=> nxs_l18n__("center bottom", "nxs_td"), 
				"right bottom"		=> nxs_l18n__("right bottom", "nxs_td"),
			),
			"icons"				=> array 
			(
				"left top"			=> nxs_l18n__("arrow-down-right", "nxs_td"),
				"center top"		=> nxs_l18n__("arrow-down", "nxs_td"), 
				"right top"			=> nxs_l18n__("arrow-down-left", "nxs_td"), 
				"left center"		=> nxs_l18n__("arrow-right", "nxs_td"),
				"center center"		=> nxs_l18n__("minus", "nxs_td"), 
				"right center"		=> nxs_l18n__("arrow-left", "nxs_td"), 
				"left bottom"		=> nxs_l18n__("arrow-up-right", "nxs_td"),
				"center bottom"		=> nxs_l18n__("arrow-up", "nxs_td"), 
				"right bottom"		=> nxs_l18n__("arrow-up-left", "nxs_td"),
			)
		),
		"docking_position" =>  array (
			"subtype" 			=> "textlookup",
			"values" 			=> array 
			(
				"left top"			=> nxs_l18n__("left top", "nxs_td"),
				"center top"		=> nxs_l18n__("center top", "nxs_td"), 
				"right top"			=> nxs_l18n__("right top", "nxs_td"), 
				"left center"		=> nxs_l18n__("left center", "nxs_td"),
				"center center"		=> nxs_l18n__("center center", "nxs_td"), 
				"right center"		=> nxs_l18n__("right center", "nxs_td"), 
				"left bottom"		=> nxs_l18n__("left bottom", "nxs_td"),
				"center bottom"		=> nxs_l18n__("center bottom", "nxs_td"), 
				"right bottom"		=> nxs_l18n__("right bottom", "nxs_td"),
			),
			"icons"				=> array 
			(
				"left top"			=> nxs_l18n__("stop2", "nxs_td"),
				"center top"		=> nxs_l18n__("stop2", "nxs_td"), 
				"right top"			=> nxs_l18n__("stop2", "nxs_td"), 
				"left center"		=> nxs_l18n__("stop2", "nxs_td"),
				"center center"		=> nxs_l18n__("stop2", "nxs_td"),
				"right center"		=> nxs_l18n__("stop2", "nxs_td"), 
				"left bottom"		=> nxs_l18n__("stop2", "nxs_td"),
				"center bottom"		=> nxs_l18n__("stop2", "nxs_td"), 
				"right bottom"		=> nxs_l18n__("stop2", "nxs_td"),
			)
		),
		"valid_dates" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array
				(
					"any"				=>nxs_l18n__("any", "nxs_td"),
					"pastonly"			=>nxs_l18n__("past only", "nxs_td"), 
					"todayandfuture"	=>nxs_l18n__("today and the future", "nxs_td"), 
					"tomorrowandfuture"	=>nxs_l18n__("tomorrow and the future", "nxs_td"), 
				)
		),
		"fixedheader_display" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array
				(
					""				=>nxs_l18n__("default", "nxs_td"),
					"inline"		=>nxs_l18n__("inline", "nxs_td"), 
					"float"			=>nxs_l18n__("float", "nxs_td"), 
				)
		),
		"shadow" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array
				(
					""				=>nxs_l18n__("default", "nxs_td"),
					"none"			=>nxs_l18n__("none", "nxs_td"), 
				)
		),
		"responsive_display" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array
				(
					""				=> nxs_l18n__("Default", "nxs_td"),
                    "display0"		=> nxs_l18n__("Never", "nxs_td"),
                    "display480"	=> nxs_l18n__("480", "nxs_td"),
                    "display720"	=> nxs_l18n__("720", "nxs_td"),
                    "display960"	=> nxs_l18n__("960", "nxs_td"),
                    "display1200"	=> nxs_l18n__("1200", "nxs_td"),
                    "display1440"	=> nxs_l18n__("1440", "nxs_td"),
				)
		),
		"inpagesectionmenu_style" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array
				(
					"blocks"			=>nxs_l18n__("blocks", "nxs_td"),
					"blocksicons"			=>nxs_l18n__("blocks with icons", "nxs_td"),
					"circles"			=>nxs_l18n__("circles", "nxs_td"),
					"circlesline"		=>nxs_l18n__("circles with line", "nxs_td"),
				)
		),
		"inpagesectionmenu_items" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array
				(
					"sections"			=>nxs_l18n__("all sections", "nxs_td"),
					"prevnext"			=>nxs_l18n__("previous and next", "nxs_td"),
					"prevactivenext"	=>nxs_l18n__("previous, active section and next", "nxs_td"),
					"prevallnext"		=>nxs_l18n__("previous, all sections and next", "nxs_td"),
				)
		),
		"inpagesectionmenu_showtitle" => array (
			"subtype" 			=> "textlookup",
			"values" 			=> array
				(
					"onchange"			=>nxs_l18n__("Show title as tooltip on change", "nxs_td"),
					"onhover"			=>nxs_l18n__("Show title as tooltip on hover", "nxs_td"),
					"always"			=>nxs_l18n__("Always show title", "nxs_td"),
				)
		),
		"margin" => array (
			"subtype" 			=> "multiplier",
			"values" 			=> array("", 0, 0.1, 0.2, 0.3, 0.5, 0.7, 1, 1.5, 2, 2.5, 3, 3.5),
		),
		"padding" => array (
			"subtype" 			=> "multiplier",
			"values" 			=> array("", 0, 0.1, 0.2, 0.3, 0.5, 1, 1.5, 2, 2.5, 3, 3.5),
		),
		"border_radius" => array (
			"subtype" 			=> "multiplier",
			"values" 			=> array("", 0, 1, 2, 3, 4, 5, 10, 15),
		),
		"minheight" => array (
			"subtype" 			=> "multiplier",
			"values" 			=> array("", 40, 80, 120, 160, 200, 240, 280, 320, 360, 400, 440, 480),
		),
		"maxheight" => array (
			"subtype" 			=> "multiplier",
			"values" 			=> array("",  0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0, 1.2, 1.4, 1.6, 1.8, 2.0, 3.0),
		),
		"distance" => array (
			"subtype" 			=> "multiplier",
			"values" 			=> array("", 0, 0.1, 0.2, 0.3, 0.5, 0.7, 1, 1.5),
		),
		"offset" => array (
			"subtype" 			=> "multiplier",
			"values" 			=> array("", 40, 80, 120, 160, 200, 240, 280, 320, 360, 400, 440, 480),
		),
		"items_scale" => array (
			"subtype" 			=> "multiplier",
			"values" 			=> array(0.8, 1, 1.2, 1.4, 1.5, 1.6, 1.8, 2.0),
		),
	);
	return $options;
}


function nxs_font_getfontidentifiers() {
	$result = array();
	$result[] = "1";
	$result[] = "2";
	$result[] = "3";
	 
	// enable themes to overrule or extends the fonts
	$args = array();
	$result = apply_filters("nxs_font_getfontidentifiers", $result, $args);
	
	return $result;
}

function nxs_getmappedfontfams($val)
{
	$result = array();
	
	if ($val == "Arial, Arial, Helvetica, sans-serif")
	{
		$result[] = "Arial";
	}
	else if ($val == "Arial Black, Arial Black, Gadget, sans-serif")
	{
		// NOT ALLOWED; NONE-EXISTING OR COPYRIGHTED FONT, IF THIS ONE IS SELECTED, WILL RESULT IN A 403!
		//$result[] = "Arial Black";
	}
	else if ($val == "Courier New, Courier New, monospace")
	{
		// NOT ALLOWED; NONE-EXISTING OR COPYRIGHTED FONT, IF THIS ONE IS SELECTED, WILL RESULT IN A 403! 
		//$result[] = "Courier New";
	}
	else if ($val == "'Georgia', serif")
	{
		$result[] = "Georgia";
	}
	else if ($val == "Tahoma, Geneva, sans-serif")
	{
		// NOT ALLOWED; NONE-EXISTING OR COPYRIGHTED FONT, IF THIS ONE IS SELECTED, WILL RESULT IN A 403!
		//$result[] = "Tahoma";
	}
	else if ($val == "Times New Roman, Times New Roman, Times, serif")
	{
		$result[] = "Times New Roman";
	}
	else if ($val == "Verdana, Verdana, Geneva, sans-serif")
	{
		$result[] = "Verdana";
	}
	else if ($val == "'Droid Serif', serif")
	{
		$result[] = "Droid Serif:400,700,400italic,700italic";
	}
	else if ($val == "'Droid Sans', sans-serif")
	{
		$result[] = "Droid Sans:400,700";
	}
	else if ($val == "'Crafty Girls', cursive")
	{
		$result[] = "Crafty Girls";
	}
	else if ($val == "'Trade Winds', cursive")
	{
		$result[] = "Trade Winds";
	}
	else if ($val == "'Cherry Cream Soda', cursive")
	{
		$result[] = "Cherry Cream Soda";
	}
	else if ($val == "'Federo', sans-serif")
	{
		$result[] = "Federo";
	}
	else if ($val == "'Smokum', cursive")
	{
		$result[] = "Smokum";
	}
	else if ($val == "'Lobster', cursive")
	{
		$result[] = "Lobster";
	}
	else if ($val == "'Rock Salt', cursive")
	{
		$result[] = "Rock Salt";
	}
	else if ($val == "'Kranky', cursive")
	{
		$result[] = "Kranky";
	}
	else if ($val == "'Sancreek', cursive")
	{
		$result[] = "Sancreek";
	}
	else if ($val == "'Righteous', cursive")
	{
		$result[] = "Righteous";
	}
	else if ($val == "'UnifrakturMaguntia', cursive")
	{
		$result[] = "UnifrakturMaguntia";
	}
	else if ($val == "'Raleway', cursive")
	{
		$result[] = "Raleway:100";
	}
	else if ($val == "'Helvetica Neue',Helvetica,sans-serif")
	{
		$result[] = "Helvetica";
	}
	else if ($val == "'Vidaloka', serif")
	{
		$result[] = "Vidaloka";
	}
	else if ($val == "'Great Vibes', serif")
	{
		$result[] = "Great Vibes";
	}
	else if ($val == "'Oswald', sans-serif")
	{
		$result[] = "Oswald:400,300";
	}
	else if ($val == "'Open Sans', sans-serif")
	{
		$result[] = "Open Sans";
	}
	else
	{
		// default; we will assume that the val represents the fontfamily itself (this is the new style)
		$result[] = $val;
	}
	
	// $result = array();
	
	return $result;
}

function nxs_getfonts() 
{
	$result = array(
		"Arial, Arial, Helvetica, sans-serif" 					=> array("text" => "Arial, Arial, Helvetica, sans-serif"),
		"Arial Black, Arial Black, Gadget, sans-serif" 			=> array("text"  => "Arial Black, Arial Black, Gadget, sans-serif"),
		"Courier New, Courier New, monospace" 					=> array("text"  => "Courier New, Courier New, monospace"),
		"'Georgia', serif" 										=> array("text"  => "'Georgia', serif"),
		"Tahoma, Geneva, sans-serif" 							=> array("text"  => "Tahoma, Geneva, sans-serif"),
		"Times New Roman, Times New Roman, Times, serif" 		=> array("text"  => "Times New Roman, Times New Roman, Times, serif"),
		"Verdana, Verdana, Geneva, sans-serif" 					=> array("text"  => "Verdana, Verdana, Geneva, sans-serif"),
		"'Droid Serif', serif" 									=> array("text"  => "'Droid Serif	', serif"),
		"'Droid Sans', sans-serif" 								=> array("text"  => "'Droid Sans', sans-serif"),
		"'Crafty Girls', cursive" 								=> array("text"  => "'Crafty Girls', cursive"),		
		"'Trade Winds', cursive" 								=> array("text"  => "'Trade Winds', cursive"),
		"'Cherry Cream Soda', cursive" 							=> array("text"  => "'Cherry Cream Soda', cursive"),
		"'Federo', sans-serif" 									=> array("text"  => "'Federo', sans-serif"),		
		"'Smokum', cursive" 									=> array("text"  => "'Smokum', cursive"),
		"'Lobster', cursive" 									=> array("text"  => "'Lobster', cursive"),
		"'Rock Salt', cursive" 									=> array("text"  => "'Rock Salt', cursive"),
		"'Kranky', cursive" 									=> array("text"  => "'Kranky', cursive"),
		"'Sancreek', cursive" 									=> array("text"  => "'Sancreek', cursive"),
		"'Righteous', cursive" 									=> array("text"  => "'Righteous', cursive"),
		"'UnifrakturMaguntia', cursive" 						=> array("text"  => "'UnifrakturMaguntia', cursive"),
		"'Raleway', cursive" 									=> array("text"  => "'Raleway', cursive"),
		"'Helvetica Neue',Helvetica,sans-serif" 				=> array("text"  => "'Helvetica Neue',Helvetica,sans-serif"),
		"'Vidaloka', serif" 									=> array("text"  => "'Vidaloka',serif",),
		"'Great Vibes', serif" 									=> array("text"  => "'Great Vibes',serif",),
		"'Oswald', sans-serif" 									=> array("text"  => "'Oswald', sans-serif",),
		"'Open Sans', sans-serif" 								=> array("text"  => "'Open Sans', sans-serif",),
		//"'Noto+Sans::latin,greek-ext,greek'" 								=> array("text"  => "'Noto+Sans::latin,greek-ext,greek'",),
	);
	
	// add fonts as configured in the site management
	$nackwhenerror = false;
	$sitemeta = nxs_getsitemeta_internal($nackwhenerror);
	$googlewebfonts = $sitemeta["googlewebfonts"];
	$googlewebfontspieces = explode("\n", $googlewebfonts);
	foreach ($googlewebfontspieces as $googlewebfontspiece)
	{
		$googlewebfontspiece = trim($googlewebfontspiece);
		if ($googlewebfontspiece != "")
		{
			$result[$googlewebfontspiece] = array("text" => "custom:" . $googlewebfontspiece);
		}
	}
	
	//
	// todo: enable filter such that framework/plugins/themes can extend the list of fonts
	return apply_filters("nxs_getfonts", $result, $args);
}

?>
