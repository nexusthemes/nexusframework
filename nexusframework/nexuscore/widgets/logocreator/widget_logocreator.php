<?php

function nxs_widgets_logocreator_geticonid() {
	return "nxs-icon-image";
}

// Setting the widget title
function nxs_widgets_logocreator_gettitle() {
	return nxs_l18n__("logocreator[nxs:widgettitle]", "nxs_td");
}

// Unistyle
function nxs_widgets_logocreator_getunifiedstylinggroup() {
	return "logocreatorwidget";
}

// Unicontent
function nxs_widgets_logocreator_getunifiedcontentgroup() {
	return "logocreatorwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_logocreator_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_logocreator_gettitle(),
		"sheeticonid" 		=> nxs_widgets_logocreator_geticonid(),
		"sheethelp" 		=> nxs_l18n__("http://nexusthemes.com/logocreator-widget/"),
		"unifiedstyling" 	=> array("group" => nxs_widgets_logocreator_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_logocreator_getunifiedcontentgroup(),),
		"fields" => array
		(
			// CONFIGURATION
			
			array( 
				"id" 				=> "wrapper_configuration_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Icon", "nxs_td"),
			),

			array(
				"id" 				=> "searchphrase",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Searchphrase", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("plumber", "nxs_td"),
			),

			array( 
				"id" 				=> "color1",
				"type" 				=> "colorzen",
				"colorset_lightgradient_enabled" => false,
				"colorset_mediumgradient_enabled" => false,
				"label" 			=> nxs_l18n__("Main color", "nxs_td"),
				"unistylablefield"	=> true
			),

			array( 
				"id" 				=> "color2",
				"type" 				=> "colorzen",
				"colorset_lightgradient_enabled" => false,
				"colorset_mediumgradient_enabled" => false,
				"label" 			=> nxs_l18n__("Secondary color", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Some logo's may not support a secondary color", "nxs_td"),
				"unistylablefield"	=> true
			),

			array
			(
				"id" 				=> "position",
				"type" 				=> "radiobuttons",
				"subtype"			=> "docking_position",
				"disable"			=> array(1, 3, 7, 9),
				"layout" 			=> "3x3",
				"default" 			=> "left center",
				"label" 			=> nxs_l18n__("Icon position", "nxs_td"),
			),

			array( 
					"type" 				=> "wrapperend"
			),

			array( 
				"id" 				=> "wrapper_configuration_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
			),

			array(
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Logo title", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Logo title goes here", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If your logo has a title put it here.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),

			array( 
				"id" 				=> "titlecolor",
				"type" 				=> "colorzen",
				"colorset_lightgradient_enabled" => false,
				"colorset_mediumgradient_enabled" => false,
				"label" 			=> nxs_l18n__("Title color", "nxs_td"),
				"unistylablefield"	=> true
			),

			array(
				"id" 				=> "titlefontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Title fontzen", "nxs_td"),
				"unistylablefield"	=> true
			),

			array(
				"id" 				=> "subtitle",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Logo subtitle", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Logo subtitle goes here", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If your logo has a subtitle put it here.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),

			array( 
				"id" 				=> "subtitlecolor",
				"type" 				=> "colorzen",
				"colorset_lightgradient_enabled" => false,
				"colorset_mediumgradient_enabled" => false,
				"label" 			=> nxs_l18n__("Subtitle color", "nxs_td"),
				"unistylablefield"	=> true
			),

			array(
				"id" 				=> "subtitlefontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Subtitle fontzen", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
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

function nxs_widgets_logocreator_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// Blend unistyle properties
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_logocreator_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_logocreator_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	//$mixedattributes = $temp_array;
	$mixedattributes = array_merge($temp_array, $args);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title","logocreator","button_logocreator", "destination_url"));
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
	global $nxs_global_row_render_statebag;
	$pagerowtemplate = $nxs_global_row_render_statebag["pagerowtemplate"];
	
	if ($postid != "" && $placeholderid != "")
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
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;

	if ($nxs_global_row_render_statebag["pagerowtemplate"] != "one") {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Warning: please move the logocreator to a row that has exactly 1 column", "nxs_td");
	}	

	// load the logos svg
	// $svg = simplexml_load_file('http://bartvanmerrienboer.nl/tedxlogo/11.svg');
	$svg = simplexml_load_file('http://marketing-agency.ted.c1.eu-w1.nexusthemes.com/wp-content/nexusframework/ted/artwork/logos.svg');

	// get the svg's viewbox size
	$viewbox = $svg->attributes()->viewBox;

	$logo = "";

	// get the logo depending on the searchphrase
	$logos = $svg->switch->g->g;
	foreach ($logos as $templogo)
	{
		if ($templogo->attributes()->id == $searchphrase)
		{
			$logo = $templogo;
		}
	}

	// logo with the searchphrase doesn't exists: render alternative
	if ($logo == "")
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Warning: No logo found for this theme", "nxs_td");
	}

	// logo with the searchphrase exists: continue
	else
	{
		// set default viewbox width and height
		$viewboxWidth = 500;
		$viewboxHeight = 500;

		// ---- LOGO ----

		// if no color is defined then fall back to the base2 color
		if ($color1 == "")
		{
			$color1 = "base2";
		}

		// if no color is defined then fall back to the base2 color
		if ($color2 == "")
		{
			$color2 = "base1";
		}

		// if logo is hidden: Show it
		if ($logo->attributes()->display == "none")
		{
			unset($logo->attributes()->display);
		}

		// replace the fill in the logo for the right colors class
		$icon_colorclass1 = "nxs-colorzen nxs-colorzen-{$color1}";
		$icon_colorclass2 = "nxs-colorzen nxs-colorzen-{$color2}";
		nxs_widgets_logocreator_replacecolorsinlogo($logo, $icon_colorclass1, $icon_colorclass2);


		// ---- TITLE AND SUBTITLE ----
		if ($title)
		{
			// set the default title options
			$titleoptions = array (
				"text"		=> $title,
				"anchor" 	=> "start",					// see: https://developer.mozilla.org/en-US/docs/Web/SVG/Attribute/text-anchor
				"baseline"	=> "text-before-edge",		// see: https://developer.mozilla.org/en-US/docs/Web/SVG/Attribute/dominant-baseline
				"font" 		=> $titlefontzen,
				"fontsize" 	=> 200,
				"color"		=> $titlecolor,
				"x" 		=> 550,
				"y" 		=> 145
			);

			// set the default subtitle options
			$subtitleoptions = array (
				"text"		=> $subtitle,
				"anchor" 	=> "start",
				"baseline"	=> "text-before-edge",
				"font" 		=> $subtitlefontzen,
				"fontsize" 	=> 140,
				"color"		=> $subtitlecolor,
				"x" 		=> 550,
				"y" 		=> 280
			);

			if ($position == "left center")
			{
				$viewboxHeight = 500;
				$viewboxWidth = 2000;

				if ($subtitle)
				{
					$titleoptions["y"] = 50;
				}
			}

			if ($position == "right center")
			{
				$viewboxHeight = 500;
				$viewboxWidth = 2000;

				$logo->addAttribute('style', 'transform: translateX(1500px);');

				$titleoptions["x"] = 1450;
				$titleoptions["anchor"] = "end";

				if ($subtitle)
				{
					$subtitleoptions["x"] = 1450;
					$subtitleoptions["anchor"] = "end";

					$titleoptions["y"] = 50;
				}
			}

			if ($position == "center bottom")
			{
				$viewboxHeight = 700;
				$viewboxWidth = 1500;

				$logo->addAttribute('style', 'transform: translateX(500px) translateY(190px);');

				$titleoptions["y"] = 0;
				$titleoptions["x"] = 750;
				$titleoptions["fontsize"] = 150;
				$titleoptions["anchor"] = "middle";

				if ($subtitle)
				{
					$viewboxHeight = 850;

					$subtitleoptions["y"] = 180;
					$subtitleoptions["x"] = 750;
					$subtitleoptions["fontsize"] = 90;
					$subtitleoptions["anchor"] = "middle";

					$logo->attributes()->style = 'transform: translateX(500px) translateY(350px);';
				}
			}

			if ($position == "center top")
			{
				$viewboxHeight = 700;
				$viewboxWidth = 1500;

				$logo->addAttribute('style', 'transform: translateX(500px) translateY(0px);');

				$titleoptions["y"] = 700;
				$titleoptions["x"] = 750;
				$titleoptions["fontsize"] = 150;
				$titleoptions["anchor"] = "middle";
				$titleoptions["baseline"] = "text-after-edge";

				if ($subtitle)
				{
					$viewboxHeight = 850;

					$titleoptions["y"] = 720;

					$subtitleoptions["y"] = 845;
					$subtitleoptions["x"] = 750;
					$subtitleoptions["fontsize"] = 90;
					$subtitleoptions["anchor"] = "middle";
					$subtitleoptions["baseline"] = "text-after-edge";
				}
			}

			if ($position == "center center")
			{
				$viewboxHeight = 700;
				$viewboxWidth = 1500;

				$logo->addAttribute('style', 'transform: translateX(500px) translateY(220px);');

				$titleoptions["y"] = 0;
				$titleoptions["x"] = 750;
				$titleoptions["fontsize"] = 150;
				$titleoptions["anchor"] = "middle";

				if ($subtitle)
				{
					$viewboxHeight = 850;

					$subtitleoptions["y"] = 845;
					$subtitleoptions["x"] = 750;
					$subtitleoptions["fontsize"] = 90;
					$subtitleoptions["anchor"] = "middle";
					$subtitleoptions["baseline"] = "text-after-edge";
				}
			}

			$title_text = nxs_widget_logocreator_createtextelement($titleoptions);
			$subtitle_text = nxs_widget_logocreator_createtextelement($subtitleoptions);
		}

		// echo "<pre>";
		// var_dump($logo);
		// echo "</pre>";

		// echo "<br><br>---------------<br><br>";

		// echo "<pre>";
		// var_dump($title_text);
		// echo "</pre>";

		// echo "<br><br>---------------<br><br>";

		// echo "<pre>";
		// var_dump($subtitle_text);
		// echo "</pre>";
	}

	// $position = "";

	// $maxwidth = 500;
	// $maxheight = "";
	// $border = 0;
	// $square = 'false';
	// $format = 'png32';
	// $background = 'transparent';

	/* Determine the scaling of the SVG file */
	// $im = new Imagick();

	// echo "<br><br><br>";
	// var_dump($im);

	// $im->setBackgroundColor(new ImagickPixel($background));
	// $im->readImageBlob($svg->asXML());
	// $im->setImageFormat("png32");
	// $im->trimImage(0);

	// if (!isset($maxwidth) && !isset($maxheight)) {
	// 	exit('No maximum height and maximum width provided. At least one must be given.');
	// } else {
	// 	if (isset($maxwidth) && (!is_numeric($maxwidth) || $maxwidth <= 0)) {
	// 		exit('Invalid maximum width value provided. Must be numeric and positive.');
	// 	}
	// 	if (isset($maxheight) && (!is_numeric($maxheight) || $maxheight <= 0)) {
	// 		exit('Invalid maximum height value provided. Must be numeric and positive.');
	// 	}
	// }
	// if (!is_numeric($border) || $border < 0) {
	// 	exit('No or invalid clear space value provided. Must be numeric and non-negative.');
	// }
	// if (isset($square) && $square == 'true') {
	// 		if (isset($maxheight) && isset($maxwidth)) {
	// 			$scaling = min(($maxwidth - 2*$border)/$im->getImageWidth(), ($maxheight - 2*$border)/$im->getImageHeight());
	// 		} elseif (isset($maxwidth)) {
	// 			$scaling = min(($maxwidth - 2*$border)/$im->getImageWidth(), ($maxwidth - 2*$border)/$im->getImageHeight());
	// 		} else {
	// 			$scaling = min(($maxheight - 2*$border)/$im->getImageWidth(), ($maxheight - 2*$border)/$im->getImageHeight());
	// 		}
	// } elseif ((isset($square) && $square == 'false') || !isset($square)) {
	// 	if (isset($maxwidth) && isset($maxheight)) {
	// 		$scaling = min(($maxwidth - 2*$border)/$im->getImageWidth(), ($maxheight - 2*$border)/$im->getImageHeight());
	// 	} elseif (isset($maxwidth)) {
	// 		$scaling = ($maxwidth - 2*$border)/$im->getImageWidth();
	// 	} else {
	// 		$scaling = ($maxheight - 2*$border)/$im->getImageHeight();
	// 	}
	// }	else {
	// 	exit('Invalid square value provided. Must be \'true\', \'false\', or empty.');
	// }

	// /* Clean up */
	// $im->clear();
	// $im->destroy();

	// /* Scale the SVG image */
	// $svg['width'] = substr($svg['width'], 0, -2) * $scaling;
	// $svg['height'] = substr($svg['height'], 0, -2) * $scaling;

	// /* Reload the scaled SVG data as image */
	// $im = new Imagick();
	// $im->setBackgroundColor(new ImagickPixel($background));
	// $im->readImageBlob($svg->asXML());

	// /* Choose the image format */
	// switch ($format) {
	// 	case 'png32':
	// 		$header = 'png';
	// 		break;
	// 	case 'jpeg':
	// 		if ($background == 'transparent') {
	// 			exit('Image format \'jpeg\' is not allowed with a transparent background.');
	// 		}
	// 		$header = 'jpg';
	// 		$im->setImageCompressionQuality(90);
	// 		break;
	// 	default:
	// 		exit('Invalid image format provided. Must be \'png32\' or \'jpeg\'.');
	// }

	// $im->setImageFormat($format);

	// /* Add the clear space */
	// $im->trimImage(0);
	// if ($square == 'true') {
	// 	if ($im->getImageWidth() > $im->getImageHeight()) {
	// 		$im->borderImage($background, $border, $border + ($im->getImageWidth() - $im->getImageHeight())/2);
	// 	} else {
	// 		$im->borderImage($background, $border + ($im->getImageHeight() - $im->getImageWidth())/2, $border);
	// 	}
	// } else {
	// 	$im->borderImage($background, $border, $border);
	// }

	// /* Sometimes the resolution doesn't exactly match because of rounding errors, 
	//  * so add a few pixels to the right or bottom to make it fit */
	// if ($square == 'true') { // Square image
	// 	if (isset($maxwidth) && isset($maxheight)) {
	// 		$im->extentImage(min($maxwidth, $maxheight), min($maxwidth, $maxheight), 0, 0);
	// 	} elseif (isset($maxwidth)) {
	// 		$im->extentImage($maxwidth, $maxwidth, 0, 0);
	// 	} else {
	// 		$im->extentImage($maxheight, $maxheight, 0, 0);
	// 	}
	// } else { // Rectangular
		
	// 	$xFill = (isset($maxwidth) ? $maxwidth - $im->getImageWidth() : PHP_INT_MAX);
	// 	$yFill = (isset($maxheight) ? $maxheight - $im->getImageHeight() : PHP_INT_MAX);
			
	// 	if ($xFill > 0 && $yFill > 0) { // The image is too small, both sides
	// 		if ($xFill <= $yFill) {
	// 			$im->extentImage($maxwidth, $im->getImageHeight(), 0, 0);
	// 		} else {
	// 			$im->extentImage($im->getImageWidth(), $maxheight, 0, 0);
	// 		}
	// 	} else { // It either fits, or is too big
	// 		if ($xFill < 0) { // The image is too wide
	// 			$im->extentImage($maxwidth, $im->getImageHeight(), 0, 0);
	// 		}
	// 		if ($yFill < 0) { // The image is too tall
	// 			$im->extentImage($im->getImageWidth(), $maxheight, 0, 0);
	// 		}
	// 	}
	// }

	// /* Save and display image */
	// // $im->writeImage('assets/' . time() . '-' . urldecode($title) . '.' . $header);
	// // header("Content-Type: image/" . $header);
	// // echo $im;

	// /* Clean up */
	// $im->clear();
	// $im->destroy();

	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) {
		if ($alternativehint == "") {
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} else {
		?>
		<div class="nxs_logocreator" id="logocreator_<?php echo $placeholderid;?>">
			<svg x="0px" y="0px" class="nxs-width30" viewBox="0 0 <?php echo $viewboxWidth; ?> <?php echo $viewboxHeight; ?>" preserveAspectRatio="none">
				<?php
					$logo->asXML('php://output');

					if ($title)
					{
						$title_text->asXML('php://output');

						if ($subtitle)
						{
							$subtitle_text->asXML('php://output');
						}
					}
				?>
			</svg>
		</div>
		<?php
	}
	
	/* ------------------------------------------------------------------------------------------------- */
	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-'.$placeholderid;
	return $result;
}

function nxs_widgets_logocreator_replacecolorsinlogo($elem, $icon_colorclass1, $icon_colorclass2)
{
	$color1 = "#FF00FF";
	$color2 = "#00FF00";

	foreach ($elem as $key => $value) {
		// replace the magenta color for the color class
		if ($value->attributes()->fill == $color1)
		{
			unset($value->attributes()->fill);
			$value->addAttribute('class', $icon_colorclass1);
		}

		else if ($value->attributes()->fill == $color2)
		{
			unset($value->attributes()->fill);
			$value->addAttribute('class', $icon_colorclass2);
		}

		if (count($value) > 0)
		{
			nxs_widgets_logocreator_replacecolorsinlogo($value, $icon_colorclass1, $icon_colorclass2);
		}
	}
}

function nxs_widget_logocreator_createtextelement($args)
{
	extract($args);

	// set color to base2 if it wasn't defined
	$color = ($color) ? $color : "base2";
	$colorclass = "nxs-colorzen nxs-colorzen-{$color}";

	// set font to 1 if it wasn't defined
	$font = ($font) ? $font : 1;
	$fontclass = "nxs-fontzen nxs-fontzen-{$font}";

	$class = "{$colorclass} {$fontclass}";
	$style = "text-shadow:none;";

	$result = "<text class='{$class}' dominant-baseline='{$baseline}' text-anchor='{$anchor}' x='{$x}' y='{$y}' font-size='{$fontsize}' style='{$style}'>{$text}</text>";
	$result = simplexml_load_string($result);
	return $result;
}

function nxs_widgets_logocreator_initplaceholderdata($args)
{
	extract($args);

	$args['position'] = "center center";

	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_logocreator_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_logocreator_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
