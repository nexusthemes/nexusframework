<?php

// Setting the widget image
function nxs_widgets_entity_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-moving";
}

// Setting the widget title
function nxs_widgets_entity_gettitle() {
	return nxs_l18n__("Entity", "nxs_td");
}

// Unistyling
function nxs_widgets_entity_getunifiedstylinggroup() {
	return "entitywidget";
}

// Unicontent
function nxs_widgets_entity_getunifiedcontentgroup() {
	return "entitywidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_entity_home_getoptions() 
{
	$cpts = array();
		
	$taxonomiesmeta = nxs_business_gettaxonomiesmeta();
	foreach ($taxonomiesmeta as $taxonomy => $taxonomymeta)
	{
	 	if ($taxonomymeta["arity"] == "n")
	 	{
		 	$singular = $taxonomymeta["singular"];
		 	$cpts[] = "nxs_" . $singular;
		}
	}
	
	$posttypes = array_merge(array("post","page"), $cpts);
	
	$options = array
	(
		"sheettitle" 		=> "Entity",
		"sheeticonid" 		=> nxs_widgets_entity_geticonid(),
		"sheethelp" => nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1764396204"),
		"unifiedstyling" 	=> array ("group" => nxs_widgets_entity_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_entity_getunifiedcontentgroup(),),
		"footerfiller" => true,	// add some space at the bottom
		"fields" => array
		(
			array
			( 
				"id"								=> "filter_postid",
				"type" 							=> "selectpost",
				"post_status"				=> array("publish", "future"),
				"previewlink_enable"=> "false",
				"label" 						=> nxs_l18n__("Post or page", "nxs_td"),
				"tooltip" 					=> nxs_l18n__("Select the post or page that represents this entity", "nxs_td"),
				// we still enable posts and pages too, as some people might be using the "old" implementation
				"post_type" 				=> $posttypes,
			),
			array
			(
				"id" 				=> "enabled",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Enabled", "nxs_td"),
			),
		),
	);
	
	// nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_entity_render_webpart_render_htmlvisualization($args) 
{
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	if ($render_behaviour == "code")
	{
		//
		$mixedattributes = $args;
	}
	else
	{
		// Every widget needs it's own unique id for all sorts of purposes
		// The $postid and $placeholderid are used when building the HTML later on
		$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
		// Blend unistyling properties
		$unistyle = $temp_array["unistyle"];
		if (isset($unistyle) && $unistyle != "") 
		{
			// blend unistyle properties
			$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_entity_getunifiedstylinggroup(), $unistyle);
			$temp_array = array_merge($temp_array, $unistyleproperties);	
		}
		
		// Blend unicontent properties
		$unicontent = $temp_array["unicontent"];
		if (isset($unicontent) && $unicontent != "") 
		{
			// blend unistyle properties
			$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_entity_getunifiedcontentgroup(), $unicontent);
			$temp_array = array_merge($temp_array, $unicontentproperties);
		}
		
		// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
		$mixedattributes = array_merge($temp_array, $args);
		
		$hovermenuargs = array();
		$hovermenuargs["postid"] = $postid;
		$hovermenuargs["placeholderid"] = $placeholderid;
		$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
		$hovermenuargs["metadata"] = $mixedattributes;
		$hovermenuargs["enable_deleterow"] = true;
		$hovermenuargs["enable_deletewidget"] = false;
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs); 
	}
	
	
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));	
	
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;
	
	// Turn on output buffering
	nxs_ob_start();
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));
	
	// Appending custom widget class
	global $nxs_global_placeholder_render_statebag;
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " " . $cssclass;
	
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */	
	
	
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	?>
	<div class="nxs-dragrow-handler nxs-padding-menu-item">
	    <div class="content2">
				<?php
				
				
				if ($enabled != "")
				{
					$color = "green";
				}
				else
				{
					$color = "red";
				}
				
				if ($filter_postid != "")
				{
					$title = nxs_gettitle_for_postid($filter_postid);
					if ($title == "") { $title = "not set ($filter_postid)"; }
					echo "<div style='background-color: {$color}; color: white; margin: 10px; padding: 10px;'>semantic:" . $title . "</div>";
				}
				else
				{
					echo "<div style='background-color: yellow; color: black; margin: 10px; padding: 10px;'>not set</div>";
				}
				
				?>	    	
	    	<div class="nxs-clear"></div>
	    </div> <!--END content-->
	</div>
	<?php
	
	/* ------------------------------------------------------------------------------------------------- */
	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	return $result;
}

function nxs_widgets_entity_initplaceholderdata($args)
{
	extract($args);

	// initialize fields here if needed ...
	$args['enabled'] = "true";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_entity_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_entity_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
