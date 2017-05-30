<?php

function nxs_widgets_socialaccounts_geticonid() 
{
	//$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-social";
}

// Setting the widget title
function nxs_widgets_socialaccounts_gettitle() 
{
	return nxs_l18n__("Social Accounts", "nxs_td");
}

function nxs_widgets_socialaccounts_getunifiedstylinggroup()
{
	$result = "socialaccounts";
	return $result;
}

function nxs_widgets_socialaccounts_getunifiedcontentgroup()
{
	$result = "socialaccounts";
	return $result;
}


/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_socialaccounts_home_getoptions($args) 
{
	global $nxs_g_modelmanager;
	$contentmodel = $nxs_g_modelmanager->getcontentmodel();
	$taxonomy = "nxs_socialaccount";
	$abstractpostid = $contentmodel[$taxonomy]["taxonomy"]["postid"];
	
	$editurl = get_edit_post_link($abstractpostid);
	$reorderurl = $contentmodel[$taxonomy]["url"];
	
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_socialaccounts_gettitle(),
		"sheeticonid" 		=> nxs_widgets_socialaccounts_geticonid(),
		"sheethelp" => nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1764396204"),
		"unifiedstyling" 	=> array("group" => nxs_widgets_socialaccounts_getunifiedstylinggroup(),),	
		"unifiedcontent" 	=> array ("group" => nxs_widgets_socialaccounts_getunifiedcontentgroup(),),
		"fields" 			=> array
		(
			array
			( 
				"id" 				=> "wrapper_input_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("socialaccounts", "nxs_td"),
			),
	
			//
			array
	    (
				"id" 					=> "content_custom",
				"type" 				=> "custom",
				"custom"	=> "<div><a class='nxsbutton' href='{$editurl}'>Edit</a></div><div><a class='nxsbutton' href='{$reorderurl}'>Re-Order/Edit items</a></div>",
			),		
			
			array
			( 
				"id" 				=> "wrapper_input_end",
				"type" 				=> "wrapperend"
			),
			
			// TITLE
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"initial_toggle_state" 	=> "closed",
			),
		
			array(
				"id" 				=> "title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title importance", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
						
			array(
				"id" 				=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Override title fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_heightiq",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align titles", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's title will participate in the title alignment of other partipating widgets in this row", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
				
			),
			
			
			// ITEMS
			
			array(  
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Items", "nxs_td"),
				"initial_toggle_state" 	=> "closed",
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "halign",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Alignment", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Align your accounts to the left, center or right from the placeholder.", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
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

function nxs_widgets_socialaccounts_render_webpart_render_htmlvisualization($args) 
{
	// Importing variables
	extract($args);
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));

	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	if ($render_behaviour == "code")
	{
		//
		$temp_array = array();
	}
	else
	{
		// Every widget needs it's own unique id for all sorts of purposes
		// The $postid and $placeholderid are used when building the HTML later on
		$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	}
	//unset($temp_array["postid"]);
	//unset($temp_array["placeholderid"]);
	
	// Blend unistyle properties
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_socialaccounts_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
		
		
	}
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_socialaccounts_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);

	// Lookup atts
	//$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title","text","button_text", "destination_url"));
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
	global $nxs_global_placeholder_render_statebag;
	$nxs_global_placeholder_render_statebag["data_atts"]["nxs-datasource"] = "nxs_socialaccount";
	

	// in some configurations the mixedattributes contain faulty
	// postid; in that case, 
	//$postid = $args["postid"];
	
	if ($postid != "" && $placeholderid != "")
	{
		$hovermenuargs = array();
		$hovermenuargs["postid"] = $postid;
		$hovermenuargs["placeholderid"] = $placeholderid;
		$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
		$hovermenuargs["enable_addentity"] = true;
		$hovermenuargs["metadata"] = $mixedattributes;
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	}
	
	// Turn on output buffering
	nxs_ob_start();
	
	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		global $nxs_global_placeholder_render_statebag;
		if ($shouldrenderalternative == true) {
			$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . "-warning ";
		} else {
			// Appending custom widget class
			$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " ";
		}
	}
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;

	if 		($halign == 'left') 	{ $alignment = ''; } 
	else if ($halign == 'center') 	{ $alignment = 'nxs-center'; } 
	else if ($halign == 'right') 	{ $alignment = 'nxs-float-right'; }

	$icon_font_list = "";
	$icon_font_list .= "<div class='nxs-applylinkvarcolor {$alignment}'>";
	$icon_font_list .= "<ul class='icon-font-list'>";
	
	global $nxs_g_modelmanager;
	$contentmodel = $nxs_g_modelmanager->getcontentmodel();
	
	$taxonomy = "nxs_socialaccount";
	$index = -1;
	foreach ($contentmodel[$taxonomy]["instances"] as $instance)
	{
		// var_dump($instance);
		$url = $instance["content"]["url"];
		$icon = $instance["content"]["icon"];
		
		//
		$icon_font_list .= "<li>";
		$icon_font_list .= "<a target='_blank' href='{$url}'>";
		$icon_font_list .= "<span class='{$icon}'></span>";
		$icon_font_list .= "</a>";
		$icon_font_list .= "</li>";
		
		// var_du
	}

	$icon_font_list .= "</ul>";
	$icon_font_list .= "</div>";
	
	if (count($contentmodel[$taxonomy]["instances"]) == 0)
	{
		//
		if (is_user_logged_in())
		{
			global $nxs_g_modelmanager;
			global $nxs_global_row_render_statebag;
			$contentmodel = $nxs_g_modelmanager->getcontentmodel();
			$url = $contentmodel["nxs_socialaccount"]["url"];
			$icon_font_list .= "<div>No social accounts configured <a class='nxsbutton' href='{$url}'>Manage</a></div>";
			$nxs_global_row_render_statebag["hidewheneditorinactive"] = true;
		}
		else
		{
			global $nxs_global_row_render_statebag;
			$nxs_global_row_render_statebag["etchrow"] = true;
			// $html = "";
		}
	}
	
	// Alignment
	if 		($halign == 'left') {
		$text_alignment = 'text-align: left;';
	} else if ($halign == 'center') {
		$alignment = 'margin: 0 auto;' . ' width: ' . $social_wrapper . 'px; padding-left: 5px;';
		$text_alignment = 'text-align: center;';
	} else if ($halign == 'right') {
		$alignment = 'float: right;';
		$text_alignment = 'text-align: right;';
	}
	
	$htmltitle = nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, "", "");
	
	if 		($halign == 'center') {$halign = 'nxs-center'; }
	else if ($halign == 'right') {$halign = 'nxs-float-right'; }
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) 
	{
		nxs_renderplaceholderwarning(nxs_l18n__("Missing input", "nxs_td"));
	} 
	else 
	{
		echo $htmltitle;
		echo '<div class="nxs-clear"></div>';
		echo $icon_font_list;
		echo '<div class="nxs-clear"></div>';    
	} 
	
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

/* INITIATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_socialaccounts_initplaceholderdata($args)
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	$result = nxs_widgets_initplaceholderdatageneric($args, $widgetname);
	return $result;
}
