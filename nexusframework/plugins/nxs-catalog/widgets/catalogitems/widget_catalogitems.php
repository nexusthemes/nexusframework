<?php

function nxs_widgets_catalogitems_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-book";
}

function nxs_widgets_catalogitems_gettitle() {
	return nxs_l18n__("Catalog Items", "nxs_td");
}

// Unistyle
function nxs_widgets_catalogitems_getunifiedstylinggroup() {
	return "catalogitemswidget";
}

// Unicontent
function nxs_widgets_catalogitems_getunifiedcontentgroup() {
	return "catalogitemswidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_catalogitems_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_catalogitems_gettitle(),
		"sheeticonid" 		=> nxs_widgets_catalogitems_geticonid(),
		"sheethelp" 		=> nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=826980725"),
		"unifiedstyling" 	=> array("group" => nxs_widgets_catalogitems_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_catalogitems_getunifiedcontentgroup(),),
		"footerfiller" => true,	// add some space at the bottom
		"fields" => array
		(
			// link to the business editor
			array
			(
	      "id" 				=> "wrapper_items_begin",
	      "type" 				=> "wrapperbegin",
	      "label" 			=> nxs_l18n__("Items", "nxs_td"),
      ),
			array
      (
				"id" 					=> "businesstype",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Businesstype", "nxs_td"),
			),
			array
			(
        "id" 				=> "wrapper_items_end",
        "type" 				=> "wrapperend",
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

function nxs_widgets_catalogitems_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	}
	
	// Blend unistyle properties
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_catalogitems_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_catalogitems_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	//$mixedattributes = $temp_array;
	$mixedattributes = array_merge($temp_array, $args);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("businesstype"));
	
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
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	$html .= "<div class='nxsgrid-container'>";
	
	// grab instances from the cache
	$key = "cat_instances_" . md5($businesstype);
	$rawdata = get_transient($key);
	if ($rawdata === false)
	{
		// cache is invalid / non existing; refetch!
		$args = array
		(
			"hostname" => "nexusthemes.com",
			"apiurl" => "/businesstype/{$businesstype}/",
			"queryparameters" => array
			(
				"nxs" => "api",
			),
		);
		$rawdata = nxs_connectivity_invoke_api_get($args);
		$transientdurationsecs = 60 * 60 * 1;	// max 1x per 1 uur
		set_transient($key, $rawdata, $transientdurationsecs);
	}
	
	if (count($rawdata["themes"]) == 0)
	{
		if (is_user_logged_in())
		{
			// echo "No catalog items found<br />Businesstype: {$businesstype}";
			// lets insert some dummy items
			$rawdata["themes"] = array
			(
				"sample1" => array
				(
					"name" => "Sample 1",
					"previewurl" => "sample url",
					"thumburl" => "https://d2ue5m8i6umntv.cloudfront.net/herbal_medicine_wordpress_theme/herbal_medicine_wordpress_theme_1280x1024_macbook.png",
				),
				"sample2" => array
				(
					"name" => "Sample 2",
					"previewurl" => "sample url",
					"thumburl" => "https://d2ue5m8i6umntv.cloudfront.net/herbal_medicine_wordpress_theme/herbal_medicine_wordpress_theme_1280x1024_macbook.png",
				),
				"sample3" => array
				(
					"name" => "Sample 3",
					"previewurl" => "sample url",
					"thumburl" => "https://d2ue5m8i6umntv.cloudfront.net/herbal_medicine_wordpress_theme/herbal_medicine_wordpress_theme_1280x1024_macbook.png",
				),
				"sample4" => array
				(
					"name" => "Sample 4",
					"previewurl" => "sample url",
					"thumburl" => "https://d2ue5m8i6umntv.cloudfront.net/herbal_medicine_wordpress_theme/herbal_medicine_wordpress_theme_1280x1024_macbook.png",
				),
			);
		}
	}
	
	// convert rawdata to instances
	$instances = array();
	foreach ($rawdata["themes"] as $themeid => $thememeta)
	{
		$instances[] = array
		(
			"content" => array
			(
				"post_title" => $thememeta["name"],
				"url" => $thememeta["previewurl"],
				"image_src" => $thememeta["thumburl"],
			)
		);
	}
	
	//
	$count = count($instances);
	
	
	
	$numberofcolumns = 4;
	if ($count % 3 == 0)
	{
		$numberofcolumns = 3;
	}
	else if ($count % 4 == 0)
	{
		$numberofcolumns = 4;
	}
	else if ($count % 2 == 0)
	{
		$numberofcolumns = 2;
	}
	else
	{
		if ($count == 1)
		{
			$numberofcolumns = 1;
		}
		else if ($count == 5)
		{
			$numberofcolumns = 3;
		}
		else
		{
			$numberofcolumns = 4;
		}
	}
	
	$itemsstyle = "text";
	$index = -1;
	foreach ($instances as $instance)
	{
		$index++;
		$post_title = $instance["content"]["post_title"];		
		$post_excerpt = $instance["content"]["post_excerpt"];
		$url = $instance["content"]["url"];
		$image_imageid = $instance["content"]["post_thumbnail_id"];
		$image_src = $instance["content"]["image_src"];
		
		$args = array
		(
			"render_behaviour" => "code",
			"title" => $post_title,
			"text" => $post_excerpt,
			"image_imageid" => $image_imageid,
			"image_src" => $image_src,
			"destination_url" => $url,
		);
		
		nxs_requirewidget($itemsstyle);
		$functionnametoinvoke = "nxs_widgets_{$itemsstyle}_render_webpart_render_htmlvisualization";

		$subresult = call_user_func($functionnametoinvoke, $args);
		$subhtml = $subresult["html"];
				
		$remainder = $index % $numberofcolumns;
		
		$issecondelementinrow = ($remainder == 1);
		
		$isnewrow = $remainder == 0;
		$isfirst = $index == 0;
		$islastinrow = $remainder % $numberofcolumns == ($numberofcolumns - 1);

		$csslastcolumn = "";
		if ($islastinrow)
		{
			$csslastcolumn = "nxsgrid-lastcolumn";
		}
		$csssecondcolumn = "";
		if ($issecondelementinrow && $numberofcolumns == 4)
		{
			$csssecondcolumn = "nxsgrid-secondcolumn";
		}
		
		$html .= "<div class='index{$index} {$csssecondcolumn} remainder{$remainder} nxsgrid-item nxsgrid-margin-bottom20 nxsgrid-column-{$numberofcolumns} nxsgrid-float-left {$csslastcolumn}'>";
		$html .= $subhtml;
		$html .= "</div>";
		
		if ($islastinrow)
		{
			$html .= "<div class='nxsgrid-clear-both clear'></div>";	
		}
	}
	
	$html .= "</div>";	
	
	echo $html;
	// 
	
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

function nxs_widgets_catalogitems_initplaceholderdata($args)
{
	extract($args);

	//$args['catalogitems_heightiq'] = "true";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_catalogitems_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_catalogitems_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}