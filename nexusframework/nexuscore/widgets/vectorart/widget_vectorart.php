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
				"label" 			=> nxs_l18n__("Configuration", "nxs_td"),
			),
			
			array( 
					"type" 				=> "wrapperend"
			),
		)
	);
	
	// nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
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

	$style = "width:100%;";
	$color = "#000000";

	?>

	<div class="nxs_vectorart" id="vectorart_<?php echo $placeholderid;?>">
		<svg style="<?php echo $style; ?>" x="0px" y="0px" viewBox="0 0 100 5.194" preserveAspectRatio="none">
			<path fill="<?php echo $color; ?>" d="M0,5.194c0,0,50-11.601,100,0"/>
		</svg>
		<svg style="<?php echo $style; ?>" x="0px" y="0px" viewBox="0 0 100 5.194" preserveAspectRatio="none">
			<path fill="<?php echo $color; ?>" d="M0,0c0,0,49.88,11.688,100,0v5.193H0V0z"/>
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
