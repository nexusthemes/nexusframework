<?php

nxs_requirewidget("generic");

function nxs_widgets_seo_geticonid()
{
	return "nxs-icon-bars2";
}

// Setting the widget title
function nxs_widgets_seo_gettitle() {
	return nxs_l18n__("seo", "nxs_td");
}

// 
function nxs_widgets_seo_getunifiedstylinggroup() {
	return "seowidget";
}

// Unicontent
function nxs_widgets_seo_getunifiedcontentgroup() {
	return "seowidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_seo_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$options = array
	(
		"sheettitle" => nxs_widgets_seo_gettitle(),
		"sheeticonid" => nxs_widgets_seo_geticonid(),
		"sheethelp" => nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1764396204"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_seo_getunifiedstylinggroup(),
		),
		"unifiedcontent" 	=> array 
		(
			"group" => nxs_widgets_seo_getunifiedcontentgroup(),
		),
		"fields" => array
		(
			array
			( 
				"id" 				=> "wrapper_model_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Model", "nxs_td"),
			),
			array
      (
				"id" 					=> "modeluris",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Model URIs", "nxs_td"),
				"placeholder" => "for example m1:foo@bar,m2:foo{{humanid}}@schema",
			),
			array
			( 
				"id" 					=> "wrapper_model_end",
				"type" 				=> "wrapperend"
			),		
			// TITLES
				
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("SEO fields", "nxs_td"),
			),
			
			array
			( 
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("SEO title", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("SEO title goes here", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If your seo has an eye-popping title put it here.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array
      (
				"id" 					=> "title_lookuppicker",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_generic_modeltaxfieldpicker_popupcontent",
			),
			array(
				"id" 				=> "metadescription",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("SEO meta description", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("SEO meta description", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("The SEO meta description", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array
      (
				"id" 					=> "metadescription_lookuppicker",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_generic_modeltaxfieldpicker_popupcontent",
			),
			array(
				"id" 				=> "canonicalurl",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Canonical URL", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Canonical URL", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("The Canonical URL", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array
      (
				"id" 					=> "canonicalurl_lookuppicker",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_generic_modeltaxfieldpicker_popupcontent",
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

function nxs_widgets_seo_render_webpart_render_htmlvisualization($args) 
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
	
	// blend unistyle properties
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "")
	{
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_seo_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);	
	}

	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_seo_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}	
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title", "metadescription", "canonicalurl"));	
	
	// Translate model data
	$mixedattributes = nxs_filter_translatemodel($mixedattributes, array("title", "metadescription", "canonicalurl"));	
	
	// Translate urls
	$mixedattributes["canonicalurl"] = nxs_url_prettyfy($mixedattributes["canonicalurl"]);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
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
	
	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		global $nxs_global_placeholder_render_statebag;
		
		if ($shouldrenderalternative == true) 
		{
			$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . "-warning ";
		}
		else 
		{
			// Appending custom widget class
			// Responsive display
			if ($responsive_display == "") { $responsive_display = 'seo720'; }
			$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " " . $responsive_display . " " . $flex_box_height;
		}
	}
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;
	/*
	if ($badcondition)
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Minimal: title, subtitle or button", "nxs_td");
	}
	*/
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	//
	if (!is_user_logged_in())
	{
		// don't render the widget, nor even the row when user is not logged in
		global $nxs_global_row_render_statebag;
		$nxs_global_row_render_statebag["etchrow"] = true;
	}
	
	?>
	<div class='nxs-hidewheneditorinactive'>
		<div style="background-color: white; border: 2px solid black; color: black; padding: 5px;">
			seo title: <?php echo $title; ?><br />
			seo description: <?php echo $metadescription; ?><br />
			seo canonical url: <?php echo $canonicalurl; ?>
		</div>
	</div>
	<?php
	
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

function nxs_widgets_seo_initplaceholderdata($args)
{
	extract($args);

  $args['ph_margin_bottom'] = "0-0";

	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_seo_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_seo_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}
