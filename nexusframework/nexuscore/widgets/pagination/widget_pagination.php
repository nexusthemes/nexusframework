<?php

function nxs_widgets_pagination_geticonid()
{
    $widget_name = basename(dirname(__FILE__));
    return "nxs-icon-" . $widget_name;
}

function nxs_widgets_pagination_gettitle()
{
    return nxs_l18n__("pagination", "nxs_td");
}

function nxs_widgets_pagination_getunifiedstylinggroup() {
    return "pagination";
}


/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_pagination_home_getoptions($args)
{
    // CORE WIDGET OPTIONS

    $options = array
    (
        "sheettitle" => nxs_widgets_pagination_gettitle(),
        "sheeticonid" => nxs_widgets_pagination_geticonid(),
     		"sheethelp" => nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1764396204"),
		"unifiedstyling" => array
		(
            "group" => nxs_widgets_pagination_getunifiedstylinggroup(),
        ),
        "fields" => array
        (
            // GENERAL STYLING
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("General styling", "nxs_td"),
			),
			
			array( 
				"id" 				=> "button_color",
				"type" 				=> "colorzen", 
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "current_button_color",
				"type" 				=> "colorzen", 
				"label" 			=> nxs_l18n__("Current button color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button scale", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true,
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
        ),
    );

    nxs_extend_widgetoptionfields($options, array("backgroundstyle"));

    return $options;
}





/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_pagination_render_webpart_render_htmlvisualization($args)
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_pagination_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Localize atts
	$mixedattributes = nxs_localization_localize($mixedattributes);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);

	//
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
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
	
	// Buttons 
	$button_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $button_color);
	$button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $button_scale);
	
	$current_button_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $current_button_color);
	
	
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	/**
     * Display a paginated navigation to next/previous set of posts,
     * when applicable.
     *
     * @since 4.1.0
     *
     * @param array $args Optional. See {@see get_the_posts_pagination()} for available arguments.
     *                    Default empty array.
     */
    the_posts_pagination( array(
        'prev_text'          => __( 'Previous page', 'nxs_td' ),
        'next_text'          => __( 'Next page', 'nxs_td' ),
        'mid-size'           => 2,
    ) );

	?>
    
	<!-- Start of client side output manipulation -->
    <script>
		function nxs_js_decorate_pagination_step() {
			
				<!-- Title -->
				jQuery('.nxs-widget-<?php echo $placeholderid; ?>.nxs-pagination h2').addClass('nxs-display-none');
				
				<!-- Anchors -->
				jQuery('.nxs-widget-<?php echo $placeholderid; ?>.nxs-pagination').addClass('nxs-align-center');
				jQuery('.nxs-widget-<?php echo $placeholderid; ?>.nxs-pagination .nav-links').wrap('<div class="nxs-default-p nxs-inline-block"></div>');
				jQuery('.nxs-widget-<?php echo $placeholderid; ?>.nxs-pagination .nav-links .page-numbers').addClass('nxs-margin5');
				jQuery('.nxs-widget-<?php echo $placeholderid; ?>.nxs-pagination .nav-links a.page-numbers').addClass('nxs-button <?php echo $button_scale_cssclass; ?> <?php echo $button_color_cssclass; ?>');
				jQuery('.nxs-widget-<?php echo $placeholderid; ?>.nxs-pagination .nav-links .page-numbers.next').html('<span class="nxs-icon-arrow-right-light"></span>');
				jQuery('.nxs-widget-<?php echo $placeholderid; ?>.nxs-pagination .nav-links .page-numbers.prev').html('<span class="nxs-icon-arrow-left-light"></span>');
				jQuery('.nxs-widget-<?php echo $placeholderid; ?>.nxs-pagination .nav-links .page-numbers span').addClass('nxs-margin-left0');
				jQuery('.nxs-widget-<?php echo $placeholderid; ?>.nxs-pagination .nav-links .page-numbers.current').addClass('nxs-button <?php echo $button_scale_cssclass; ?> <?php echo $current_button_color_cssclass; ?>');
				
		}
		<!-- Explicitly load last -->
		jQuery(window).load(
			function(){
				nxs_js_decorate_pagination_step();
			}
		);
    </script>
    <?php
	
	/* ------------------------------------------------------------------------------------------------- */
    	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
    $html = nxs_ob_get_contents();

    nxs_ob_end_clean();

    $result["html"] = $html;
    $result["replacedomid"] = 'nxs-widget-' . $placeholderid;

// outbound statebag

    return $result;
}

function nxs_widgets_pagination_initplaceholderdata($args)
{
    extract($args);

//    $args["htmlcustom"] = nxs_l18n__("Sample htmlcustom[nxs:default]", "nxs_td");
//    $args['ph_margin_bottom'] = "0-0";

    nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);

    $result = array();
    $result["result"] = "OK";

    return $result;
}

?>
