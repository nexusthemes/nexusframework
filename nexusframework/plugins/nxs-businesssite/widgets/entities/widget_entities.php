<?php

function nxs_widgets_entities_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-moving";
}

function nxs_widgets_entities_gettitle() {
	return nxs_l18n__("Entities", "nxs_td");
}

// Unistyle
function nxs_widgets_entities_getunifiedstylinggroup() {
	return "entitieswidget";
}

// Unicontent
function nxs_widgets_entities_getunifiedcontentgroup() {
	return "entitieswidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_entities_home_getoptions($args) 
{
	global $businesssite_instance;
	$contentmodel = $businesssite_instance->getcontentmodel();
	
	$taxonomies = array();
	$taxonomiesmeta = nxs_business_gettaxonomiesmeta();
	foreach ($taxonomiesmeta as $taxonomy => $taxonomymeta)
	{
	 	if ($taxonomymeta["arity"] == "n")
	 	{
	 		$taxonomies[$taxonomy] = $taxonomymeta["title"];
	 	}
	}
	
	/*
	$taxonomies = array
	(
		"services" => "Services",
		"testimonials" => "Testimonials",
		"employees" => "Employees",
	);
	*/

	$datasource = $args["datasource"];
	
	$orderediturl = nxs_geturl_for_postid($contentmodel[$datasource]["postid"]);
	
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_entities_gettitle(),
		"sheeticonid" 		=> nxs_widgets_entities_geticonid(),
		"sheethelp" 		=> nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=826980725"),
		"unifiedstyling" 	=> array("group" => nxs_widgets_entities_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_entities_getunifiedcontentgroup(),),
		"footerfiller" => true,	// add some space at the bottom
		"fields" => array
		(
			// link to the business editor
			array(
          "id" 				=> "wrapper_items_begin",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Items", "nxs_td"),
      ),
 			array
      (
				"id" 					=> "datasource",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Datasource", "nxs_td"),
				"dropdown" 		=> $taxonomies,
			),
			/*
			array
			(
				"id" 				=> "editorlink",
				"type" 				=> "custom",
				"custom"	=> "<div><a class='nxsbutton' href='{$orderediturl}'>Change Order</a></div>",
				"label" 			=> nxs_l18n__("Order", "nxs_td"),
			),
			*/
			array
			(
          "id" 				=> "wrapper_items_end",
          "type" 				=> "wrapperend",
      ),
			
			// VISUALIZATION
      array(
          "id" 				=> "wrapper_title_begin",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Visualization", "nxs_td"),
      ),
     
      array
      (
				"id" 					=> "itemsstyle",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Style", "nxs_td"),
				"dropdown" 		=> array
				(
					"title" => "Title",
					"text" => "Text",
					"target" => "Target",
					"bio" => "Bio",
				),
				"unistylablefield" => true,
			),
      array(
          "id" 				=> "wrapper_title_end",
          "type" 				=> "wrapperend",
      ),
      
      //
      // styling;
      //
      
      array(
          "id" 				=> "wrapper_title_begin",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Text widget styling", "nxs_td"),
      ),
      
      array
      (
				"id" 				=> "text_title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title importance", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "text_title_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Title fontzen", "nxs_td"),
				"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "text_title_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "text_title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Override title fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			array
			( 
				"id" 				=> "text_top_info_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Top info color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array
			(
				"id"     			=> "text_top_info_padding",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Top info padding", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("padding"),
				"unistylablefield"	=> true
			),
			array
			(
				"id"     			=> "text_icon_scale",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Icon scale", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("icon_scale"),
				"unistylablefield"	=> true
			),
			//
			array
			(
				"id" 				=> "text_text_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Text alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
			//
			array
			(
				"id" 				=> "text_image_alignment",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("image_halignment"),
				"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "text_image_size",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("image_size"),
				"unistylablefield"	=> true
			),		
			array
			( 
				"id" 				=> "text_image_shadow",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Image shadow", "nxs_td"),
				"unistylablefield"	=> true
			),	
			array
			(
				"id" 				=> "text_image_border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image border width", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_width"),
				"unistylablefield"	=> true
			),
			//
			array
			(
				"id" 				=> "text_button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true,
			),
			array
			( 
				"id" 				=> "text_button_color",
				"type" 				=> "colorzen", // "select",
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "text_button_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Button fontzen", "nxs_td"),
				"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "text_button_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Button alignment", "nxs_td"),
				"unistylablefield"	=> true,
			),	
			//
			array
			(
				"id" 				=> "text_destination_target",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Target", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@empty@@@"=>nxs_l18n__("Auto", "nxs_td"),
					"_blank"=>nxs_l18n__("New window", "nxs_td"),
					"_self"=>nxs_l18n__("Current window", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			//
			array(
				"id" 				=> "text_title_heightiq",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align titles", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's title will participate in the title alignment of other partipating widgets in this row", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "text_text_heightiq",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align texts", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's text will participate in the text alignment of other partipating widgets in this row", "nxs_td"),
				"unistylablefield"	=> true
			),			
			array
			( 
				"id" 				=> "text_text_showliftnote",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Liftnote", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("You can make the first paragraph stand out with this option.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array
			( 
				"id" 				=> "text_text_showdropcap",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Dropcap", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Enlarge the first character of the first paragraph with this option.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "text_text_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Text fontzen", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array
			( 
				"id" 				=> "text_enlarge",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Enlarge hover effect", "nxs_td"),
				"unistylablefield"	=> true
			),
			array
			( 
				"id" 				=> "text_grayscale",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Grayscale hover effect", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array
			(
          "id" 				=> "wrapper_title_end",
          "type" 				=> "wrapperend",
      ),
      			
			//
		)
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}


/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_entities_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_entities_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_entities_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	//$mixedattributes = $temp_array;
	$mixedattributes = array_merge($temp_array, $args);
	
	// Localize atts
	//$mixedattributes = nxs_localization_localize($mixedattributes);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title","entities","button_entities", "destination_url"));
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
	// Overruling of parameters
	if ($image_imageid == "featuredimg")
	{
		$image_imageid = get_post_thumbnail_id($containerpostid);
	}
	
	global $nxs_global_row_render_statebag;
	$pagerowtemplate = $nxs_global_row_render_statebag["pagerowtemplate"];
	if ($pagerowtemplate == "one")
	{
		$entities_heightiq = "";	// off!
	}

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
	
	//global $nxs_global_current_containerpostid_being_rendered;
	//$posttype = get_post_type($nxs_global_current_containerpostid_being_rendered);
	
	global $nxs_global_current_postid_being_rendered;
	$posttype2 = get_post_type($nxs_global_current_postid_being_rendered);
	
	/*
	echo "<div>";
	echo "containerpostid; $nxs_global_current_containerpostid_being_rendered <br />";
	echo "posttype of container: $posttype <br />";
	echo "posttype of post: $posttype2 <br />";
	echo "</div>";
	*/
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	// the html can contain placeholders
	global $businesssite_instance;
	$contentmodel = $businesssite_instance->getcontentmodel();
	
	if ($datasource == "")
	{
		$taxonomy = "services";
	}
	else
	{
		$taxonomy = $datasource;
	}
	
	//
	$html .= "<div class='nxsgrid-container'>";
	
	
	
	//
	$count = $contentmodel[$taxonomy]["countenabled"];
	
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
	
	if ($posttype2 == "nxs_sidebar")
	{
		$numberofcolumns = 1;
	}
	
	$index = -1;
	foreach ($contentmodel[$taxonomy]["instances"] as $instance)
	{
		$enabled = $instance["enabled"];
		if ($enabled == "") { continue; }
		$index++;
		$post_title = $instance["content"]["post_title"];		
		$post_excerpt = $instance["content"]["post_excerpt"];
		$url = $instance["content"]["url"];
		$image_imageid = $instance["content"]["post_thumbnail_id"];
		$post_icon = $instance["content"]["post_icon"];
		
		$childargs = array
		(
			"render_behaviour" => "code",
			"title" => $post_title,
			"text" => $post_excerpt,
			"image_imageid" => $image_imageid,
			"destination_url" => $url,
			"icon" => $post_icon,
		);
		
		// replicate styleable fields
		$fieldstoreplicate = array
		(
			"title_heading", "title_fontzen", "title_alignment", "title_fontsize", "top_info_color", "top_info_padding", 
			"icon_scale", "text_alignment", "image_alignment", "image_size", "image_shadow", "image_border_width", 
			"button_scale", "button_color", "button_fontzen", "button_alignment",
			"destination_target", "title_heightiq", "text_heightiq", "text_showliftnote", "text_showdropcap", "text_fontzen", "enlarge", "grayscale",
		);
		foreach ($fieldstoreplicate as $fieldtoreplicate)
		{
			$childargs[$fieldtoreplicate] = $args["text_{$fieldtoreplicate}"];
		}
		
		if ($itemsstyle == "title")
		{
			unset($child["text"]);
			unset($child["image_imageid"]);
		}
		
		if ($itemsstyle == "target")
		{
			$widgettype = "target";
		}
		else if ($itemsstyle == "bio")
		{
			$widgettype = "bio";
		}
		else
		{
			$widgettype = "text";
		}
		
		nxs_requirewidget($widgettype);
		$functionnametoinvoke = "nxs_widgets_{$widgettype}_render_webpart_render_htmlvisualization";

		$subresult = call_user_func($functionnametoinvoke, $childargs);
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
	
	if ($count == 0)
	{
		//
		if (is_user_logged_in())
		{
			echo "0 entities found...";
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

function nxs_widgets_entities_initplaceholderdata($args)
{
	extract($args);

	//$args['entities_heightiq'] = "true";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_entities_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_entities_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>