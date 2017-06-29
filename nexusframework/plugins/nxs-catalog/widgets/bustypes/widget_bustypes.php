<?php

function nxs_widgets_bustypes_geticonid() {
	//$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-eye";
}

// Setting the widget title
function nxs_widgets_bustypes_gettitle() {
	return nxs_l18n__("Business Types", "nxs_td");
}

// obsolete
function nxs_widgets_bustypes_getunifiedstylingprefix()
{
	$result = "bustypeswidget";
	return $result;
}

function nxs_widgets_bustypes_getunifiedstylinggroup()
{
	$result = "bustypeswidget";
	return $result;
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_bustypes_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_bustypes_gettitle(),
		"sheeticonid" => nxs_widgets_bustypes_geticonid(),
		
		"unifiedstyling" => array
		(
			// obsolete
			"prefix" => nxs_widgets_bustypes_getunifiedstylingprefix(),
			"group" => nxs_widgets_bustypes_getunifiedstylinggroup(),
		),		
			
		"fields" => array
		(
						
			
			// CONFIGURATION
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				//"initial_toggle_state"	=> "closed",
				"label" 			=> nxs_l18n__("Configuration", "nxs_td"),
			),
			
			array( 
				"id" 				=> "primarycolor",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Primary Color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "defaultcolor",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Default Color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "layout",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Layout", "nxs_td"),
				"dropdown" 			=> array(
					""		=>"default",
					"primary"		=>"primary",
				),
				"unistylablefield"	=> true
			),	
			array(
				"id" 				=> "numofcolumns",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Columns", "nxs_td"),
				"dropdown" 			=> array(
					""				=>"default",
					"2"				=>"2",
					"3"				=>"3",
					"4"				=>"4",
					"6"				=>"6",
					"8"				=>"8",
				),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "subnumofcolumns",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Sub Columns", "nxs_td"),
				"dropdown" 			=> array(
					""				=>"default",
					"2"				=>"2",
					"3"				=>"3",
					"4"				=>"4",
					"6"				=>"6",
					"8"				=>"8",
				),
				"unistylablefield"	=> true
			),					
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
		)
	);
	
	return $options;
}

/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_bustypes_render_webpart_render_htmlvisualization($args) 
{
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
	// Turn on output buffering
	nxs_ob_start();
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));

	global $nxs_global_row_render_statebag;
	global $nxs_global_placeholder_render_statebag;
		
	// Appending custom widget class
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-bustypes ";
	
	// EXPRESSIONS
	// ---------------------------------------------------------------------------------------------------- 
	
	//if ($button_text == "") 
	//{
	//	$alternativemessage = nxs_l18n__("Warning: no button text", "nxs_td");
	//}


	// https://nexusthemes.com/api/1/prod/businesstypes/?nxs=api&nxs_json_output_format=prettyprint
	// https://nexusthemes.com/api/1/prod/primary-businesstypes/?nxs=api&fingerprint=johan&lang=en&nxs_json_output_format=prettyprint

	$url = "https://nexusthemes.com/api/1/prod/primary-businesstypes/?nxs=api&fingerprint=sitemap&lang=en&nxs_json_output_format=prettyprint";
	$primarybusinesstypesjson = nxs_geturlcontents(array("url" => $url));
	$primarybusinesstypes = json_decode($primarybusinesstypesjson, true);
	$primarybusinesstypes = $primarybusinesstypes["primary_businesstypes"];
	
	// Color
	$primarycolor_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $primarycolor);
	$defaultcolor_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $defaultcolor);
	
	// OUTPUT
	// ----------------------------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------------------------- 
	
	if ($numofcolumns == "") { $numofcolumns = '6'; }
	if ($subnumofcolumns == "") { $subnumofcolumns = '8'; }
	
	echo '<div id="nxsgrid-container">';
	
	// PRIMARY version
	// ----------------------------------------------------------------------------------------------------
	
	if ($layout == "primary") {
    
		$index = 0;
		foreach ($primarybusinesstypes as $industry => $industrymeta) {
			
			// Default modulo to separate lines of items when content height is variable
			if ($index % $numofcolumns == 0 && $index != 0) { echo '<div class="nxs-clear"></div>'; }
			
			// Exceptional modulo for two step separation with four column gallery
			if ($index % 2 == 0 && $index != 0) { echo '<div class="nxs-clear multi-step-divider"></div>'; }
		
			$index = $index + 1;
			$salesurl = $industrymeta["salesurl"];
			$name = $industrymeta["name"];
			$businesstypes = $industrymeta["businesstypes"];
			
			// PRIMARY BUSINESS TYPES
			// ----------------------------------------------------------------------------------------------------
			echo '
			<div class="nxsgrid-item nxssolidgrid-column-'.$numofcolumns.' nxsgrid-float-left">
			
				<div class="nxsgrid-item-container nxs-padding10 nxs-align-center '.$primarycolor_cssclass.'">
					<span class="nxs-icon-'.$industry.' nxs-icon-scale-1-0 nxs-margin-bottom10"></span>		
					<h6>'.$name.'</h6>
				</div>';
				
				// BUSINESS TYPES
				// ----------------------------------------------------------------------------------------------------
				echo'<div class="nxsgrid-subcontainer">';
				
					echo '
					<div class="nxs-padding-top10 nxs-padding-left10">
						<span class="nxs-icon-'.$industry.' nxs-icon-scale-1-0 nxs-float-left"></span>
						<h2 class="nxs-padding-left10 nxs-inline" style="line-height: 32px;">'.$name.'</h2>
					</div>
					<div class="nxs-clear"></div>
					';
					
				$subindex = 0;
				foreach ($businesstypes as $businesstype => $businesstypemeta) {	
				
				// Default modulo to separate lines of items when content height is variable
				if ($subindex % $subnumofcolumns == 0 && $subindex != 0) { echo '<div class="nxs-clear"></div>'; }
				
				// Exceptional modulo for two step separation with four column gallery
				if ($subindex % 2 == 0 && $subindex != 0) { echo '<div class="nxs-clear multi-step-divider"></div>'; }
				
				$subindex = $subindex + 1;
				$name = $businesstypemeta["name"];
				
					echo'
					<div class="nxsgrid-subitem nxssolidgrid-column-'.$subnumofcolumns.' nxsgrid-float-left">
						<div class="nxsgrid-item-container nxs-padding10  nxs-align-center '.$defaultcolor_cssclass.'">
							<span class="nxs-icon-'.$businesstype.' nxs-icon-scale-1-0 nxs-margin-bottom10"></span>
							<h6>'.$name.'</h6>
						</div>
					</div>';
				}
				echo'</div> <!-- END nxsgrid-subcontainer -->';
			
			echo '</div> <!-- END nxsgrid-item -->';
		
		}
	
	// NON-PRIMARY version
	// ----------------------------------------------------------------------------------------------------

	} else if ($layout == "default" || $layout == "") {
	
		foreach ($primarybusinesstypes as $industry => $industrymeta) {
			
			$salesurl = $industrymeta["salesurl"];
			$name = $industrymeta["name"];
			$businesstypes = $industrymeta["businesstypes"];
			
			foreach ($businesstypes as $businesstype => $businesstypemeta) {
				
				$name = $businesstypemeta["name"];
				
				// BUSINESS TYPES
				// ----------------------------------------------------------------------------------------------------
				echo '
				<div class="nxsgrid-item nxssolidgrid-column-'.$numofcolumns.' nxsgrid-float-left">
					
					<div class="nxsgrid-item-container nxs-padding10  nxs-align-center '.$primarycolor_cssclass.'">
						<span class="nxs-icon-'.$businesstype.' nxs-icon-scale-1-0 nxs-margin-bottom10"></span>		
						<h6 class="nxs-heightiq nxs-heightiq-p1-title">'.$name.'</h6>
					</div>';
					
				echo '</div> <!-- END nxsgrid-item -->';
			}
		
		}
	
	}
	
	
	echo '</div> <!-- END nxsgrid-container -->';

	// note, we set the generic widget hover menu AFTER rendering, as the blog widget
	// will also set the generic hover menu; we don't want to see the generic hover
	// menu of the blog, we want to see it of this specific wrapping type
	nxs_widgets_setgenericwidgethovermenu($postid, $placeholderid, $placeholdertemplate);

	// -------------------------------------------------------------------------------------------------
	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	
	return $result;
}

/* INITIATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_bustypes_initplaceholderdata($args)
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	$result = nxs_widgets_initplaceholderdatageneric($args, $widgetname);
	return $result;
}