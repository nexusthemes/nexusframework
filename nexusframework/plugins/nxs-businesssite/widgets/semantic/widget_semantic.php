<?php

function nxs_widgets_semantic_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-publicrelations";
}

function nxs_widgets_semantic_gettitle() {
	return nxs_l18n__("Semantic", "nxs_td");
}

// Unistyle
function nxs_widgets_semantic_getunifiedstylinggroup() {
	return "semanticwidget";
}

// Unicontent
function nxs_widgets_semantic_getunifiedcontentgroup() {
	return "semanticwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_semantic_home_getoptions($args) 
{
	global $businesssite_instance;
	$contentmodel = $businesssite_instance->getcontentmodel();
	
	$servicesediturl = nxs_geturl_for_postid($contentmodel["services"]["postid"]);
	
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_semantic_gettitle(),
		"sheeticonid" 		=> nxs_widgets_semantic_geticonid(),
		"sheethelp" 		=> nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=826980725"),
		"unifiedstyling" 	=> array("group" => nxs_widgets_semantic_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_semantic_getunifiedcontentgroup(),),
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
				"id" 				=> "editorlink",
				"type" 				=> "custom",
				"custom"	=> "<div><a class='nxsbutton' href='{$servicesediturl}'>Edit</a></div>",
				"label" 			=> nxs_l18n__("Services", "nxs_td"),
			),
			 array(
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
					"text" => "Text",
					"target" => "Target",
				),
				"unistylablefield" => true,
			),
      array(
          "id" 				=> "wrapper_title_end",
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

function nxs_widgets_semantic_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_semantic_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_semantic_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	//$mixedattributes = $temp_array;
	$mixedattributes = array_merge($temp_array, $args);
	
	// Localize atts
	//$mixedattributes = nxs_localization_localize($mixedattributes);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title","semantic","button_semantic", "destination_url"));
	
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
		$semantic_heightiq = "";	// off!
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
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	// the html can contain placeholders
	global $businesssite_instance;
	$contentmodel = $businesssite_instance->getcontentmodel();
	
	$taxonomy = "services";
	
	?>
	<script>
		(
			function($) 
			{
				$html = $('html');
		
			function nxs_js_portfolio_resize_actual() 
			{
				if ($("#nxsgrid-container").width() < 481) 
				{
				  return $html.addClass('nxsgrid-mobile-480');
				}
				else	        	
				{
					$html.removeClass('nxsgrid-mobile-480');
				}
				
				if ($("#nxsgrid-container").width() < 721) 
				{
				  return $html.addClass('nxsgrid-mobile-720');
				}
				else	        	
				{
					$html.removeClass('nxsgrid-mobile-720');
				}
			}
			
				// invoke (throttled) if user resizes	      
				var nxs_js_portfolio_resize_doit;
				window.onresize = function()
				{
					// do a "throttled" invocation, 
					// to prevent stressing the CPU while resizing the screen
				  clearTimeout(nxs_js_portfolio_resize_doit);
				  nxs_js_portfolio_resize_doit = setTimeout(nxs_js_portfolio_resize_actual, 100);
				};	   
				
				// invoke once at the moment the page is loaded for the first time
				// (proper initialization of the responsive aspects)
				$(window).load
		  (
			function()
			{
				$(window).trigger('resize');
			}
		  );
	
			}
		)
		(jQuery);
	</script>
	<?php
	$html .= "<div id='nxsgrid-container'>";
	
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
	
	$index = -1;
	foreach ($contentmodel[$taxonomy]["instances"] as $instance)
	{
		$enabled = $instance["enabled"];
		if ($enabled == "") { continue; }
		$index++;
		$post_title = $instance["content"]["post_title"];		
		$post_excerpt = $instance["content"]["post_excerpt"];
		$url = $instance["content"]["url"];
		
		//
		
		$image_src = "";
		
		
		$args = array
		(
			"render_behaviour" => "code",
			"title" => $post_title,
			"text" => $post_excerpt,
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

function nxs_widgets_semantic_initplaceholderdata($args)
{
	extract($args);

	//$args['semantic_heightiq'] = "true";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_semantic_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_semantic_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>