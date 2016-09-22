<?php

function nxs_widgets_pagegap_geticonid() {
	return "nxs-icon-expand";
}

// Setting the widget title
function nxs_widgets_pagegap_gettitle() {
	return nxs_l18n__("Page gap", "nxs_td");
}

// Unistyle
function nxs_widgets_pagegap_getunifiedstylinggroup() {
	return "pagegapwidget";
}

function nxs_widgets_pagegap_registerhooksforpagewidget($args)
{
	$pagedecoratorid = $args["pagedecoratorid"]; 
	$pagedecoratorwidgetplaceholderid = $args["pagedecoratorwidgetplaceholderid"];
	
	global $nxs_pagegap_pagedecoratorid;
	$nxs_pagegap_pagedecoratorid = $pagedecoratorid;
	global $nxs_pagegap_pagedecoratorwidgetplaceholderid;
	$nxs_pagegap_pagedecoratorwidgetplaceholderid = $pagedecoratorwidgetplaceholderid;
	
	$enabled = true;
	$pagevideo_metadata = nxs_getwidgetmetadata($nxs_pagegap_pagedecoratorid, $nxs_pagegap_pagedecoratorwidgetplaceholderid);
	$condition_enable = $pagevideo_metadata["condition_enable"];
	if ($condition_enable == "desktoponly")
	{
		if (!nxs_isdesktop())
		{
			$enabled = false;
		}
	}
	
	if ($enabled)
	{
		add_action('nxs_beforeend_head', 'nxs_widgets_pagegap_beforeend_head');
		add_action('nxs_ext_betweenheadandcontent', 'nxs_widgets_pagegap_betweenheadandcontent');
	}
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_pagegap_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_pagegap_gettitle(),
		"sheeticonid" 		=> nxs_widgets_pagegap_geticonid(),
		"sheethelp" => nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1764396204"),
		"unifiedstyling" 	=> array("group" => nxs_widgets_pagegap_getunifiedstylinggroup(),),
		"fields" => array
		(
			// SLIDES			
			
			array( 
				"id" 				=> "wrapper_pagegap_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Page gap", "nxs_td"),
			),
			
			array(
				"id" 				=> "condition_enable",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Enable", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("Always", "nxs_td"),
					"desktoponly" => nxs_l18n__("Desktops only", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "layoutposition",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Layout position", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("Default", "nxs_td"),
					"betweenheadandcontent" => nxs_l18n__("Between head and content", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "height",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Height", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("Default", "nxs_td"),
					"height_1_0" => nxs_l18n__("1x", "nxs_td"), // 80 pixels
					"height_2_0" => nxs_l18n__("2x", "nxs_td"), // 160 pixels
					"height_3_0" => nxs_l18n__("3x", "nxs_td"), // 240 pixels
					"height_4_0" => nxs_l18n__("4x", "nxs_td"), // 320 pixels
					"height_5_0" => nxs_l18n__("5x", "nxs_td"), // 400 pixels
					"height_6_0" => nxs_l18n__("6x", "nxs_td"), // 480 pixels
					"screenheight" => nxs_l18n__("Screenheight", "nxs_td"),
					"screenheight90%" => nxs_l18n__("90% of screen height", "nxs_td"),
					"ratioofscreenwidth16:9" => nxs_l18n__("16:9 ratio of screen width", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_pagegap_end",
				"type" 				=> "wrapperend"
			),
		)
	);

	nxs_extend_widgetoptionfields($options, array("unistyle"));	
	
	return $options;
}


/* ADMIN PAGE HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_pagegap_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_pagegap_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	
	global $nxs_global_row_render_statebag;
	
	$items_genericlistid = $mixedattributes['items_genericlistid'];

	/* ADMIN PAGE HOVER MENU HTML
	---------------------------------------------------------------------------------------------------- */
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["enable_decoratewidget"] = false;
	$hovermenuargs["enable_deletewidget"] = false;
	$hovermenuargs["enable_deleterow"] = true;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
	/* ADMIN EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	nxs_ob_start();
	
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-custom-html nxs-applylinkvarcolor";
		
	$shouldrenderalternative = false;
	$trimmedhtmlcustom = $htmlcustom;
	$trimmedhtmlcustom = preg_replace('/<!--(.*)-->/Uis', '', $trimmedhtmlcustom);
	$trimmedhtmlcustom = trim($trimmedhtmlcustom);
	if ($trimmedhtmlcustom == "" && nxs_has_adminpermissions())
	{
		$shouldrenderalternative = true;
	}
	
	/* ADMIN OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	echo '
	<div class="nxs-dragrow-handler nxs-padding-menu-item">
		<div class="content2">
			<div class="box">
	        	<div class="box-title">
					<h4>Page gap</h4>
				</div>
				<div class="box-content"></div>
			</div>
			<div class="nxs-clear"></div>
		</div>
	</div>';
	
	/* ------------------------------------------------------------------------------------------------- */
	
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-'.$placeholderid;

	// outbound statebag
	return $result;
}

/* INITIATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_pagegap_initplaceholderdata($args)
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	
	//$args["item_durationvisibility"] = "5000";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_pagegap_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	$result = nxs_widgets_initplaceholderdatageneric($args, $widgetname);
	return $result;
}

/* UPDATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_pagegap_updateplaceholderdata($args) 
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	$result = nxs_widgets_updateplaceholderdatageneric($args, $widgetname);
	return $result;
}

/* PAGE SLIDER HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_pagegap_beforeend_head()
{
	// do something useful here if thats needed
	
	?>
	<?php
}

/* OUTPUT
----------------------------------------------------------------------------------------------------*/
	
function nxs_widgets_pagegap_betweenheadandcontent()
{
	// get meta of the slider itself (such as transition time, etc.)
	global $nxs_pagegap_pagedecoratorid;
	global $nxs_pagegap_pagedecoratorwidgetplaceholderid;	
	$pagegap_metadata = nxs_getwidgetmetadata($nxs_pagegap_pagedecoratorid, $nxs_pagegap_pagedecoratorwidgetplaceholderid);
	
	// Unistyle
	$unistyle = $pagegap_metadata["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_pagegap_getunifiedstylinggroup(), $unistyle);
		$pagegap_metadata = array_merge($pagegap_metadata, $unistyleproperties);
	}
	
	extract($pagegap_metadata);
	
	/* EXPRESSIONS
	----------------------------------------------------------------------------------------------------*/
	$styleatts = "";
	
	$styleatts .= "width: 100%;";
	
	if ($height == "screenheight" || $height == "screenheight90%")
	{
		$perc = 100;
		if ($height == "screenheight90%")
		{
			$perc = 90;
		}
		
		$heightformulescript = "
			var viewportheight = jQuery(window).height();
			height = viewportheight * " . $perc . " / 100;
		";
	}
	else if ($height == "ratioofscreenwidth16:9")
	{
		$heightformulescript = "
			var viewportwidth = jQuery(window).width();
			height = viewportwidth / 16 * 9;
		";
	}
	else if (nxs_stringstartswith($height, "height_"))
	{
		$pieces = explode("_", $height);
		$factor = $pieces[1];	// bijv. 3 bij height_3_0
		$height = $factor * 80; // bijv. 240 bij factor 3
		$heightformulescript = "
			height = " . $height;";
		";
	}
		
	$script .= "
	<script type='text/javascript'>	
		function nxs_js_gap_updateheight()
		{
			var height;
			" . $heightformulescript . "
			jQuery('#nxs-gap').height(height);
		}
		jQuery(document).bind('nxs_event_resizeend', function() { nxs_js_gap_updateheight(); } );
		jQuery(document).bind('nxs_event_viewportchanged', function() { nxs_js_gap_updateheight(); } );
		// first time
		
		jQ_nxs( window ).load(function() {
		  // Run code
		  nxs_js_gap_updateheight();
		});
		
	</script>
	";
	
	/* OUTPUT
	----------------------------------------------------------------------------------------------------*/

	?>
	<div id='nxs-gap' style='<?php echo $styleatts; ?>'>&nbsp;</div>
	<?php
	echo $script;
}

?>
