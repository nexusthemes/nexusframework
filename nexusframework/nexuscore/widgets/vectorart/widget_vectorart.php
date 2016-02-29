<?php

function nxs_widgets_vectorart_geticonid() {
	return "nxs-icon-image";
}

// Setting the widget title
function nxs_widgets_vectorart_gettitle() {
	return nxs_l18n__("vectorart[nxs:widgettitle]", "nxs_td");
}

// Unistyle
function nxs_widgets_vectorart_getunifiedstylinggroup() {
	return "vectorartwidget";
}

// Unicontent
function nxs_widgets_vectorart_getunifiedcontentgroup() {
	return "vectorartwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_vectorart_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_vectorart_gettitle(),
		"sheeticonid" 		=> nxs_widgets_vectorart_geticonid(),
		"sheethelp" 		=> nxs_l18n__("http://nexusthemes.com/vectorart-widget/"),
		"unifiedstyling" 	=> array("group" => nxs_widgets_vectorart_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_vectorart_getunifiedcontentgroup(),),
		"fields" => array
		(
			// CONFIGURATION
			
			array( 
				"id" 				=> "wrapper_configuration_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Vector art configuration", "nxs_td"),
			),

			array( 
				"id" 				=> "color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Color", "nxs_td"),
				"unistylablefield"	=> true
			),

			array(
				"id" 				=> "height",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Height", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("maxheight"),
				"tooltip" 			=> nxs_l18n__("Height of the vector art.", "nxs_td"),
				"unistylablefield"	=> true
			),

			array(
				"id" 				=> "width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Width", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("width_percentage"),
				"tooltip" 			=> nxs_l18n__("Width of the vector.", "nxs_td"),
				"unistylablefield"	=> true
			),

			array(
				"id" 				=> "flip",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Flip", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("flip"),
				"tooltip" 			=> nxs_l18n__("Flip vector art", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
					"type" 				=> "wrapperend"
			),

			array( 
				"id" 				=> "wrapper_shape_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Shape configuration", "nxs_td"),
			),

			array(
				"id" 				=> "repeat",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Repeat", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("repeat"),
				"tooltip" 			=> nxs_l18n__("Repeating shape in the vector art", "nxs_td"),
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

function nxs_widgets_vectorart_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_vectorart_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_vectorart_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	//$mixedattributes = $temp_array;
	$mixedattributes = array_merge($temp_array, $args);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title","vectorart","button_vectorart", "destination_url"));
	
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
		$alternativemessage = nxs_l18n__("Warning:please move the vectorart to a row that has exactly 1 column", "nxs_td");
	}

	// REPEAT
	$repeat = substr($repeat, 0, strrpos($repeat, "-"));
	$repeat = intval($repeat);

	// CLASSES
	$svgclass = "nxs-width{$width} nxs-height-{$height}";
	$pathclass = "nxs-colorzen nxs-colorzen-{$color}";

	// STYLES
	$svgstyle = "";
	// flip
	if ($flip == "vertical")
	{
		$svgstyle = "transform: scaleY(-1); ";
	}

	if ($flip == "horizontal")
	{
		$svgstyle = "transform: scaleX(-1);";
	}

	if ($flip == "both")
	{
		$svgstyle = "transform: scale(-1);";
	}

	// HEIGHT
	$height = str_replace("-", ".", $height);
	$height = floatval($height);
	$viewbox_default_height = 5.194;
	$viewbox_height = $viewbox_default_height * $height;

	// SHAPE
	$shape = 2;

	// PATH
	$path = '';
	$pathwidth = round(100 / $repeat, 1);
	$pathwidthhalf = round($pathwidth / 2, 1);

	// $test = 
	for ($i = 0; $i < $repeat; $i++)
	{
		$start = round($i * $pathwidth, 1);
		if ($shape == 0)
		{
			$y1 = 11.601  * $height;
			$path .= "<path class='{$pathclass}' d='M{$start},{$viewbox_height}c0,0,{$pathwidthhalf}-{$y1},{$pathwidth},0'/>";
		}

		else if ($shape == 1)
		{
			$y1 = 11.688  * $height;
			$path .= "<path class='{$pathclass}' d='M{$start},0c0,0,{$pathwidthhalf},{$y1},{$pathwidth},0v{$viewbox_height}H{$start}V0z'/>";
		}

		else if ($shape == 2)
		{
			$end = $start + $pathwidth;
			$middle = $start + $pathwidthhalf;
			$path .= "<polygon class='{$pathclass}' points='{$start},{$viewbox_height} {$middle},0 {$end},{$viewbox_height}'/>";
		}

		else if ($shape == 3)
		{
			$end = $start + $pathwidth;
			$middle = $start + $pathwidthhalf;
			// $path .= "<polygon class='{$pathclass}' points='{$start},{$viewbox_height} {$middle},0 {$end},{$viewbox_height}'/>";
			$path .= "<polygon class='{$pathclass}' points='{$start},0 {$middle},{$viewbox_height} {$end},0 {$end},{$viewbox_height} {$start},{$viewbox_height}'/>";
		}

		else if ($shape == 4)
		{
			$x1 = 15.875 / $repeat;
			$x2 = 32.936 / $repeat;
			$y1 = 9.688 * $height;
			$y2 = 2.598 * $height;
			$y3 = 6.843 * $height;
			$v1 = $viewbox_height / 2;
			$path .= "<path class='{$pathclass}' d='M{$start},0c0,0,{$x1},{$y1},{$pathwidthhalf},{$y2}c{$x2}-{$y3},{$pathwidthhalf},{$v1},{$pathwidthhalf},{$v1}H{$start}V0z'/>";
		}

		else if ($shape == 5)
		{
			$end = $start + $pathwidth;
			$path .= "<polygon class='{$pathclass}' points='{$start},0 {$end},{$viewbox_height} {$start},{$viewbox_height}'/>";
		}
	}
	
	?>
	
	<div class="nxs_vectorart" id="vectorart_<?php echo $placeholderid;?>">
		<svg class="<?php echo $svgclass; ?>" style="<?php echo $svgstyle; ?>" x="0px" y="0px" viewBox="0 0 100 <?php echo $viewbox_height; ?>" preserveAspectRatio="none">
			<defs>
				<linearGradient id="grad1" x1="0%" y1="0%" x2="0%" y2="100%">
					<stop offset="0%" style="stop-color:rgb(255,255,0);stop-opacity:1" />
					<stop offset="100%" style="stop-color:rgb(255,0,0);stop-opacity:1" />
				</linearGradient>
			</defs>
			
			<?php echo $path; ?>
		</svg>
	</div>

	<script type='text/javascript'>

    </script>

    <?php
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) {
		if ($alternativemessage != "" && $alternativemessage != null) {
			nxs_renderplaceholderwarning($alternativemessage);
		}
	} else {
		
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

function nxs_widgets_vectorart_initplaceholderdata($args)
{
	extract($args);

	// $args['button_color'] = "base2";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_vectorart_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_vectorart_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
