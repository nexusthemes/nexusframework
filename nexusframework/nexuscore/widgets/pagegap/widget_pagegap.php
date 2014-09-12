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
	
	add_action('nxs_beforeend_head', 'nxs_widgets_pagegap_beforeend_head');
	add_action('nxs_ext_betweenheadandcontent', 'nxs_widgets_pagegap_betweenheadandcontent');
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
		"sheethelp" 		=> nxs_l18n__("http://nexusthemes.com/pagegap-widget/"),
		"unifiedstyling" 	=> array("group" => nxs_widgets_pagegap_getunifiedstylinggroup(),),
		"fields" => array
		(
			// SLIDES			
			
			array( 
				"id" 				=> "wrapper_pagegap_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Page video", "nxs_td"),
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
	
	ob_start();
	
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
	<div '.$class.'>
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
	$html = ob_get_contents();
	ob_end_clean();

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
	
	/* OUTPUT
	----------------------------------------------------------------------------------------------------*/

	?>
	<div id='nxs-gap' style='height: 600px; width: 100%;'>&nbsp;
	</div>
	<?php
}

?>
