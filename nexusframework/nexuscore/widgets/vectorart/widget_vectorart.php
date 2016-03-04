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
				"colorset_lightgradient_enabled" => false,
				"colorset_mediumgradient_enabled" => false,
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
				"id" 				=> "alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Alignment", "nxs_td"),
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
				"id" 				=> "shape",
				"type" 				=> "shape",
				"label" 			=> nxs_l18n__("Shape", "nxs_td"),
				"unistylablefield"	=> true
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

	if ($shape == "")
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Minimal: shape", "nxs_td");
	}

	if ($nxs_global_row_render_statebag["pagerowtemplate"] != "one") {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Warning: please move the vectorart to a row that has exactly 1 column", "nxs_td");
	}

	// REPEAT
	$repeat = substr($repeat, 0, strrpos($repeat, "-"));
	$repeat = intval($repeat);

	if ($repeat == "")
	{
		$repeat = 1;
	}

	// COLOR
	if ($color == "")
	{
		$color = "base2";
	}

	// SVG CLASS
	if ($alignment == "" || is_null($alignment))
	{
		$alignment = "center";
	}
	$svgclass = "nxs-width{$width} align{$alignment}";

	// SVG STYLES
	$svgstyle = "";
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
	if ($height == "")
	{
		$height = "1-0";
	}
	$height = str_replace("-", ".", $height);
	$height = floatval($height);

	// VIEWBOX
	$default_viewbox_height = 5;
	$viewbox_height = $default_viewbox_height * $height;

	// get the basic shape path
	$shapepaths = nxs_getshapepaths();
	$path = $shapepaths[$shape];

	// we strip the basic shape path of some text
	// we will build up the path later on again
	$stripstrings = array("fill='#000000' ", "/>", "></path>", "></polygon>", "></rect>", "></ellipse>", "></circle>");
	foreach ($stripstrings as $str) {
		$path = str_replace($str, "", $path);
	}

	// duplicates m$ excel's ceiling function
	// some small gaps may occur when using the round() for the scaleX;
	// this function is fix that
	if( !function_exists('ceiling') )
	{
	    function ceiling($number, $significance = 1)
	    {
	        return ( is_numeric($number) && is_numeric($significance) ) ? (ceil($number/$significance)*$significance) : false;
	    }
	}

	// calculating the scale X and Y
	$scaleX = ceiling(1 / $repeat, 0.001);
	$scaleY = $height;

	// we will build each path here and place in into $paths.
	$paths = "";
	for ($i = 0; $i < $repeat; $i++)
	{
		// calculating the translateX (depends on how many paths their should be)
		$translateX = round(100 / $repeat * $i, 3);

		// building the path class (for color fill)
		$pathclass = " class='nxs-colorzen nxs-colorzen-{$color}'";

		// building the pathstyle
		$pathstyle = " style='transform: translateX({$translateX}%) scaleX({$scaleX}) scaleY({$scaleY});'";

		// add the path to $paths
		$paths .= $path . $pathclass . $pathstyle . "/>";
	}

	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) {
		if ($alternativehint == "") {
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} else {
		?>
		<div class="nxs_vectorart" id="vectorart_<?php echo $placeholderid;?>">
			<svg class="<?php echo $svgclass; ?>" style="<?php echo $svgstyle; ?>" x="0px" y="0px" viewBox="0 0 100 <?php echo $viewbox_height; ?>" preserveAspectRatio="none">
				<?php echo $paths; ?>
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

function nxs_widgets_vectorart_initplaceholderdata($args)
{
	extract($args);

	$args['height'] = "1-0";
	$args['width'] = "100";
	$args['alignment'] = "center";

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
