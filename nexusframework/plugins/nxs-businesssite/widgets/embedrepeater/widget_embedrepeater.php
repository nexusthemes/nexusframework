<?php

nxs_requirewidget("generic");

function nxs_widgets_embedrepeater_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-loop2";
}

function nxs_widgets_embedrepeater_gettitle() {
	return nxs_l18n__("Embed Repeater", "nxs_td");
}

// Unistyle
function nxs_widgets_embedrepeater_getunifiedstylinggroup() {
	return "embedrepeaterwidget";
}

// Unicontent
function nxs_widgets_embedrepeater_getunifiedcontentgroup() {
	return "embedrepeaterwidget";
}

function nxs_embedrepeater_datasourcecustom_popupcontent($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	
	$modeluri = $runtimeblendeddata["modeluri"];
	$datasource = $runtimeblendeddata["datasource"];
	global $nxs_g_modelmanager;
	$contentmodel = $nxs_g_modelmanager->getcontentmodel($modeluri);
	$taxonomiesmeta = $nxs_g_modelmanager->getcontentschema($modeluri);
	
	foreach ($taxonomiesmeta as $taxonomy => $taxonomymeta)
	{
	 	if ($taxonomymeta["arity"] == "n")
	 	{
	 		$taxonomies[$taxonomy] = $taxonomymeta["title"];
	 	}
	}	

	nxs_ob_start();
	?>
	<div>
		<?php  
		echo "<a href='#' onclick='jQuery(\"#datasource\").val(\"\"); nxs_js_popup_sessiondata_make_dirty(); return false;'>Reset</a>&nbsp;";
		foreach ($taxonomies as $key => $val)
		{
			$display = $val;
			echo " | ";
			echo "<a href='#' onclick='jQuery(\"#datasource\").val(\"{$val}\"); nxs_js_popup_sessiondata_make_dirty(); return false;'>{$display}</a>&nbsp;";
		}
		?>
	</div>
	<?php

	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

function nxs_embedrepeater_fieldoftaxonomycustom_popupcontent($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	
	$targetid = $optionvalues["targetid"];
	$modeluri = $runtimeblendeddata["modeluri"];
	$datasource = $runtimeblendeddata["datasource"];
	global $nxs_g_modelmanager;
	$taxonomiesmeta = $nxs_g_modelmanager->getcontentschema($modeluri);
	$instanceextendedproperties = $taxonomiesmeta[$datasource]["instanceextendedproperties"];
	
	$options = array();
	
	foreach ($instanceextendedproperties as $fieldid => $fieldmeta)
	{
	 	$options[$fieldid] = $fieldmeta["label"];
	}

	nxs_ob_start();
	?>
	<div>
		<?php  
		$isfirst = true;
		foreach ($options as $key => $val)
		{
			$display = $val;
			$output = '{{' . $datasource . ".instance." . $key . '}}';
			if (!$isfirst)
			{
				echo " | ";
			}
			echo "<a href='#' onclick='jQuery(\"#{$targetid}\").val(\"{$output}\"); nxs_js_popup_sessiondata_make_dirty(); return false;'>{$display}</a>&nbsp;";
			$isfirst = false;
		}
		?>
	</div>
	<?php

	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_embedrepeater_home_getoptions($args) 
{
	$taxonomies = array();
	
	$modeluri = $args["modeluri"];
	global $nxs_g_modelmanager;
	$contentmodel = $nxs_g_modelmanager->getcontentmodel($modeluri);
	$taxonomiesmeta = $nxs_g_modelmanager->getcontentschema($modeluri);
	
	foreach ($taxonomiesmeta as $taxonomy => $taxonomymeta)
	{
	 	if ($taxonomymeta["arity"] == "n")
	 	{
	 		$taxonomies[$taxonomy] = $taxonomymeta["title"];
	 	}
	}
	
	if (nxs_iswebmethodinvocation())
	{
		$clientpopupsessioncontext = $_REQUEST["clientpopupsessioncontext"];
		$clientpopupsessiondata = $_REQUEST["clientpopupsessiondata"];
		//
		$postid = $clientpopupsessioncontext["postid"];
		$placeholderid = $clientpopupsessioncontext["placeholderid"];
		
		//error_log("pagepopup; $postid $placeholderid");
		
		// load the widget's data from the persisted db
		$placeholdermetadata = nxs_getwidgetmetadata($postid, $placeholderid);
		$item_templatepostid = $placeholdermetadata["item_templatepostid"];
		
		if ($clientpopupsessiondata["item_templatepostid"] != "")
		{
			$item_templatepostid = $clientpopupsessiondata["item_templatepostid"];
		}
		
		if ($item_templatepostid != "")
		{
			$url = nxs_geturl_for_postid($item_templatepostid);
			$preview_template_field = array
			(
				"id" 				=> "item_template_open",
				"type" 				=> "custom",
				"customcontent" => "<a href='{$url}' target='_blank'>Open template</a>",
			);
		}
	}
		
	$datasource = $args["datasource"];
	
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_embedrepeater_gettitle(),
		"sheeticonid" 		=> nxs_widgets_embedrepeater_geticonid(),
		"supporturl" => "https://www.wpsupporthelp.com/wordpress-questions/embed-repeater-widget-wordpress-questions-194/",
		"unifiedstyling" 	=> array("group" => nxs_widgets_embedrepeater_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_embedrepeater_getunifiedcontentgroup(),),
		"footerfiller" => true,	// add some space at the bottom
		"fields" => array
		(
			// LOOKUPS
			
			array
			( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "ext_loopups_wrapperbegin",
				"label" 			=> nxs_l18n__("Lookups", "nxs_td"),
				"initial_toggle_state"	=> "closed-if-empty",
				"initial_toggle_state_id" => "lookups",
			),
			array
      (
				"id" 					=> "lookups",
				"type" 				=> "ext_loopups_textarea",
				"label" 			=> nxs_l18n__("Lookup table (evaluated one time when the widget renders)", "nxs_td"),
			),
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "ext_loopups_wrapperend"
			),
			
			// datasource
			array
			(
          "id" 				=> "wrapper_items_begin",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Iterator", "nxs_td"),
      ),

      array
      (
				"id" 					=> "iterator_datasource",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Iterator datasource", "nxs_td"),
				"unicontentablefield" => true,
			),
			
			array
			(
        "id" 				=> "wrapper_items_end",
        "type" 				=> "wrapperend",
      ),
      
      array
			(
          "id" 				=> "wrapper_items_begin",
          "type" 				=> "wrapperbegin",
          "label" 			=> nxs_l18n__("Pagination", "nxs_td"),
          "initial_toggle_state" => "closed",
      ),
			
			array
      (
				"id" 					=> "filter_items_indexconstrained_min",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Number of index items to skip", "nxs_td"),
				"footernote" => "<div>(blank=no skipping)</div>",
			),
			
			array
      (
				"id" 					=> "filter_items_indexconstrained_max",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Max index to process", "nxs_td"),
				"footernote" => "<div>(blank=process all)</div>",
			),
			
			array
      (
				"id" 					=> "filter_pagination_pagesize",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Max number of items per page", "nxs_td"),
				"footernote" => "<div>(blank=no pagination)</div>",
			),
						
			array
			(
        "id" 				=> "wrapper_items_end",
        "type" 				=> "wrapperend",
      ),
			
			// item lookup table
			array
			(
        "id" 				=> "wrapper_items_begin",
        "type" 				=> "wrapperbegin",
        "label" 			=> nxs_l18n__("Item lookup table", "nxs_td"),
        "initial_toggle_state" => "closed-if-empty",
        "initial_toggle_state_id" => "item_lookups",
      ),
      array
      (
				"id" 					=> "item_lookups",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Lookup table (evaluated for each iteration of the datasource)", "nxs_td"),
			),
			array
			(
        "id" 				=> "wrapper_items_end",
        "type" 				=> "wrapperend",
      ),
      
      //
      array
			(
        "id" 				=> "wrapper_items_begin",
        "type" 				=> "wrapperbegin",
        "label" 			=> nxs_l18n__("Filters", "nxs_td"),
        "initial_toggle_state" => "closed-if-empty",
        "initial_toggle_state_id" => "filter_items_where",
      ),
      array
      (
				"id" 					=> "filter_items_where",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Items where", "nxs_td"),
			),
			
			array
			(
        "id" 				=> "wrapper_items_end",
        "type" 				=> "wrapperend",
      ),			
			
			// layout
			array
			(
        "id" 				=> "wrapper_items_begin",
        "type" 				=> "wrapperbegin",
        "label" 			=> nxs_l18n__("Layout", "nxs_td"),
        "initial_toggle_state" => "closed",
      ),
			array
			(
				"id" 				=> "items_direction",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Direction", "nxs_td"),
				"dropdown" 			=> array(
					""		=>"default",
					"row"		=>"row (items from left to right)",
					"column"		=>"column (stack from top to bottom)",
					"row-reverse"		=>"row-reverse (from right to left)",
					"column-reverse"		=>"column-reverse (stack from bottom to top)",
					
				),
				"unistylablefield"	=> true
			),
			
			array
			(
				"id" 				=> "max_items_horizontally",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Max items horizontally", "nxs_td"),
				"dropdown" 			=> array(
					""		=>"default",
					"1"		=>"1",
					"2"		=>"2",
					"3"		=>"3",
					"4"		=>"4",
					"5"		=>"5",
					"6"		=>"6",
					"7"		=>"7",
					"8"		=>"8",
				),
				"unistylablefield"	=> true
			),
			
			array
			(
				"id" 				=> "align_items",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Align items", "nxs_td"),
				"dropdown" 			=> array(
					""		=> "default",
					"flex-start" => "flex-start",
					"flex-end"		=>"flex-end",
					"center"		=> "center",
					"baseline"		=>"baseline",
					"stretch"		=>"stretch",
				),
				"unistylablefield"	=> true
				
			),
			
			array
			(
				"id" 				=> "justify_content",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Justify content", "nxs_td"),
				"dropdown" 			=> array(
					""		=>"default",
					"space-between"		=>"space-between",
					"flex-start"		=>"flex-start",
					"flex-end"		=>"flex-end",
					"center"		=>"center",
					"space-around"		=>"space-around",
					"space-evenly"		=>"space-evenly",
				),
				"unistylablefield"	=> true
			),
			array
			(
        "id" 				=> "wrapper_items_end",
        "type" 				=> "wrapperend",
      ),			
			
			
			/*
			// item_htmltemplate
			array
			(
        "id" 				=> "wrapper_items_begin",
        "type" 				=> "wrapperbegin",
        "label" 			=> nxs_l18n__("Before Template", "nxs_td"),
        "initial_toggle_state" => "closed-if-empty",
        "initial_toggle_state_id" => "widget_start_htmltemplate",
      ),
      array
      (
				"id" 					=> "widget_start_htmltemplate",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Widget start html (renders 1x above the items)", "nxs_td"),
				"unistylablefield"	=> true,
			),

			array
			(
        "id" 				=> "wrapper_items_end",
        "type" 				=> "wrapperend",
      ),
      */
      
      array
			(
        "id" 				=> "wrapper_items_begin",
        "type" 				=> "wrapperbegin",
        "label" 			=> nxs_l18n__("Item Template", "nxs_td"),
      ),
      
      array(
				"id" 				=> "item_templatepostid",
				"type" 				=> "article_link",
				"posttypes" => array("nxs_templatepart"),
				"label" 			=> nxs_l18n__("Item template", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("The content template to be rendered for each item", "nxs_td"),
				"enable_mediaselect" => false,
				"popuprefreshonchange" => "true",
			),
			
			$preview_template_field,
			
			array
			(
        "id" 				=> "wrapper_items_end",
        "type" 				=> "wrapperend",
      ),
			
			
      
      /*
			array
			(
        "id" 				=> "wrapper_items_begin",
        "type" 				=> "wrapperbegin",
        "label" 			=> nxs_l18n__("Item template", "nxs_td"),
      ),
			
      array
      (
				"id" 					=> "item_htmltemplate_a",
				"type" 				=> "textarea",
				"unistylablefield"	=> true,
				"label" 			=> nxs_l18n__("Template (renders for each iterated item)", "nxs_td"),
			),
			
			array
			(
        "id" 				=> "wrapper_items_end",
        "type" 				=> "wrapperend",
      ),
      */
      
      /*
			array
			(
        "id" 				=> "wrapper_items_begin",
        "type" 				=> "wrapperbegin",
        "label" 			=> nxs_l18n__("After template", "nxs_td"),
        "initial_toggle_state" => "closed-if-empty",
        "initial_toggle_state_id" => "widget_end_htmltemplate",
      ),			
			 array
      (
				"id" 					=> "widget_end_htmltemplate",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Widget end html (renders 1x below the items)", "nxs_td"),
				"unistylablefield"	=> true,
			),
			
			array
			(
        "id" 				=> "wrapper_items_end",
        "type" 				=> "wrapperend",
      ),
      */
			
			// --- ANY WIDGET SPECIFIC STYLING; COLORS & TEXT
			
      array
      (
          "id" 				=> "any_wrapper_colorstext_begin",
          "type" 				=> "wrapperbegin",
          "initial_toggle_state" => "closed",
          "label" 			=> nxs_l18n__("Child colors & text", "nxs_td"),
      ),
      
      array( 
				"id"				=> "any_ph_colorzen",
				"type" 				=> "colorzen",
				"focus"				=> "true",
				"label" 			=> nxs_l18n__("Color", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("The background color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "any_ph_linkcolorvar",
				"type" 				=> "colorvariation",
				"scope" 			=> "link",
				"label" 			=> nxs_l18n__("Link color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id"				=> "any_ph_text_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Text fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "custom",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_embedrepeater_custom_popupcontent",
				"label" 			=> nxs_l18n__("...", "nxs_td"),
				"layouttype"		=> "custom",
			),
		)
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

function nxs_embedrepeater_getdefaultitemsstyle($modeluri, $datasource)
{
	$result = "htmlcustom";

	global $nxs_g_modelmanager;
	$taxonomiesmeta = $nxs_g_modelmanager->getcontentschema($modeluri);

	foreach ($taxonomiesmeta as $taxonomy => $meta)
	{
		if ($taxonomy == $datasource)
		{
			if (isset($meta["instance"]["defaultrendertype"]))
			{
				$result = $meta["instance"]["defaultrendertype"];
			}
		}
	}
	
	return $result;
}

function nxs_embedrepeater_geticon($datasource)
{
	$result = "moving";

	global $nxs_g_modelmanager;
	$taxonomiesmeta = $nxs_g_modelmanager->getcontentschema();
	
	foreach ($taxonomiesmeta as $taxonomy => $meta)
	{
		if ($taxonomy == $datasource)
		{
			$result = $meta["icon"];
		}
	}
	
	return $result;
}

function nxs_embedrepeater_custom_popupcontent($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);

	$itemsstyle = nxs_embedrepeater_getdefaultitemsstyle($modeluri, $datasource);

	nxs_ob_start();
	?>
	<script>
		//var style = '<?php echo $itemsstyle; ?>';
		//nxs_js_alert("enabling styles for '"+style+"' :)");
		//
		jQuery(".custom-filter").hide();
		jQuery(".custom-filter-<?php echo $itemsstyle; ?>").show();
	</script>
	<?php
	
	// ----
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_embedrepeater_render_webpart_render_htmlvisualization($args) 
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
	if (isset($unistyle) && $unistyle != "") 
	{
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_embedrepeater_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") 
	{
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_embedrepeater_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	//$mixedattributes = $temp_array;
	$mixedattributes = array_merge($temp_array, $args);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title", "embedrepeater", "button_embedrepeater", "destination_url"));
	
	// Translate model data (apply modeluris, lookups, shortcodes)
	$mixedattributes = nxs_filter_translatemodel($mixedattributes, array("modeluri"));
	
	// allow plugins to decorate (and also do something with) the mixedattributes 
	// (an example of "doing something" would be for example to apply QA rules)
	$filterargs = array
	(
		"mixedattributes" => $mixedattributes
	);
	$mixedattributes = apply_filters("nxs_f_widgetvisualizationdecorateatts", $mixedattributes, $filterargs);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
	global $nxs_g_modelmanager;
	global $nxs_global_placeholder_render_statebag;
	$nxs_global_placeholder_render_statebag["data_atts"]["nxs-datasource"] = $datasource;

	$itemsstyle = nxs_embedrepeater_getdefaultitemsstyle($modeluri, $datasource);
	$childwidgettype = $itemsstyle;	
	
	// Overruling of parameters
	
	global $nxs_global_row_render_statebag;
	$pagerowtemplate = $nxs_global_row_render_statebag["pagerowtemplate"];
	if ($pagerowtemplate == "one")
	{
		$embedrepeater_heightiq = "";	// off!
	}

	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		//
		$hovermenuargs = array();
		$hovermenuargs["postid"] = $postid;
		$hovermenuargs["placeholderid"] = $placeholderid;
		$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
		$hovermenuargs["metadata"] = $mixedattributes;
		$hovermenuargs["enable_addentity"] = true;
		
		
		
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	}

	// Turn on output buffering
	nxs_ob_start();
	
	global $nxs_global_current_postid_being_rendered;
	$posttype2 = get_post_type($nxs_global_current_postid_being_rendered);
	
	$combinedlookups_for_currenturl = nxs_lookups_getcombinedlookups_for_currenturl();
	
	$lookups_widget = array_merge($combinedlookups_for_currenturl, nxs_parse_keyvalues($lookups));
	
	// evaluate the lookups widget values line by line
	$sofar = array();
	foreach ($lookups_widget as $key => $val)
	{
		$sofar[$key] = $val;
		//echo "step 1; processing $key=$val sofar=".json_encode($sofar)."<br />";

		//echo "step 2; about to evaluate lookup tables on; $val<br />";
		// apply the lookup values
		$sofar = nxs_lookups_blendlookupstoitselfrecursively($sofar);

		// apply shortcodes
		$val = $sofar[$key];
		//echo "step 3; result is $val<br />";

		//echo "step 4; about to evaluate shortcode on; $val<br />";

		$val = do_shortcode($val);
		$sofar[$key] = $val;

		//echo "step 5; $key evaluates to $val (after applying shortcodes)<br /><br />";

		$lookups_widget[$key] = $val;
	}
	
	// interpret the iterator_datasource by applying the lookup tables from the pagetemplate_rules and the lookup table of the widget
	
	$dslookups = array();
	$dslookups = array_merge($dslookups, nxs_gettemplateruleslookups());
	$dslookups = array_merge($dslookups, $lookups_widget);
	
	$translateargs = array
	(
		"item" => $iterator_datasource,
		"lookup" => $dslookups,
	);
	$iterator_datasource = nxs_filter_translate_v2($translateargs);
	
	// apply lookups on parameters
	$translateargs = array
	(
		"item" => $filter_pagination_pagesize,
		"lookup" => $dslookups,
	);
	$filter_pagination_pagesize = nxs_filter_translate_v2($translateargs);
	
	// todo; use template selected by user
	
	if ($item_templatepostid != "")
	{
		$item_htmltemplate_a = "<div class='repeater-item'>[nxs_embed embeddabletypemodeluri={$item_templatepostid}@wp.post]</div>";
	}
	else
	{
		$item_htmltemplate_a = "[nxs_title title='Select template']";
	}
	
	// handle fallbacks / ease of use scenarios
	if (is_archive())
	{
		if ($iterator_datasource == "")
		{
			$iterator_datasource = do_shortcode("[nxs_string ops=archive_modeluris]");
		}
		if ($item_htmltemplate_a == "")
		{
			$item_htmltemplate_a = "[nxs_title heading=2 destination_articleid={{i.postid}} title='{{i.post_title}}']";
		}
	}
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	$datasource_isvalid = true;
	
	// perhaps someone used a shortcode directly; if so, replace it!
	$iterator_datasource = do_shortcode($iterator_datasource);
	
	if ($iterator_datasource == "" || nxs_stringcontains($iterator_datasource, "{{"))
	{
		$datasource_isvalid	= false;
	}
	
	if (!$datasource_isvalid)
	{
		// guaranteed failure
		if (nxs_has_adminpermissions())
		{
			// output some dummy items to at least show something to the administrator
			$modeluriset = array
			(
				//"0", "0", "0", "0", "0", 
			);
		}
		else
		{
			// 
		}
	}
	else
	{
		//
		$settype = "unsupported";
		$iterator_datasource = trim($iterator_datasource);
		if (nxs_stringstartswith($iterator_datasource, "json:"))
		{
			$settype = "json";
			$jsonstring= substr($iterator_datasource, 5);
			$modeluriset = json_decode($jsonstring, true);
		}
		else if (nxs_stringcontains($iterator_datasource, "@"))
		{
			$settype = "custom";
		}
		else
		{
			$settype = "complete";
		}
		
		if ($settype == "complete")
		{
			$iteratormodeluri = "singleton@embedrepeaterof{$iterator_datasource}";
			
			$contentmodel = $nxs_g_modelmanager->getcontentmodel($iteratormodeluri);
			$instances = $contentmodel[$iterator_datasource]["instances"];
			foreach ($instances as $instance)
			{
				$itemhumanmodelid = $instance["content"]["humanmodelid"];
				$modeluriset[] = "{$itemhumanmodelid}@{$iterator_datasource}";
			}
		}
		else if ($settype == "custom")
		{
			$modeluriset = array();
			
			$canonical_iterator_datasource = $iterator_datasource;
			// iterator_datasource is for example (foobar1@bar,foobar2@bar)
			$canonical_iterator_datasource = trim($canonical_iterator_datasource, "()");
			$canonical_iterator_datasource = trim($canonical_iterator_datasource, "[]");
			// iterator_datasource is for example foobar1@bar,foobar2@bar
			$canonical_iterator_datasource = str_replace(",", ";", $canonical_iterator_datasource);
			$canonical_iterator_datasource = str_replace("|", ";", $canonical_iterator_datasource);
			$canonical_iterator_datasource = str_replace(" ", ";", $canonical_iterator_datasource);
			$pieces = explode(";", $canonical_iterator_datasource);
			foreach ($pieces as $piece)
			{
				$itemhumanmodelid = trim($piece);
				if ($itemhumanmodelid != "")
				{
					$modeluriset[] = $itemhumanmodelid;
					
					if ($_REQUEST["debug99"] == "true")
					{
						echo "we voegen toe: ($itemhumanmodelid) afmeting is nu;" . count($modeluriset) . "<br />";
					}
				}
			}
			
			if ($_REQUEST["debug99"] == "true")
			{
				var_dump($modeluriset);
				//die();
			}

		}
		else if ($settype == "json")
		{
			//
		}
	}
	
	$databindindex = -1;
	$databindindexafterfilter = -1;
	
	// fill the lookups
	$lookup = array();
	
	// first the lookup table as defined in the pagetemplaterules
	if (true)
	{
		$templateruleslookups = nxs_gettemplateruleslookups();
		$lookup = array_merge($lookup, $templateruleslookups);
	}
	
	// add the lookup values from the widget itself
	$lookup = array_merge($lookup, $lookups_widget);
	
	/*
	// ------------
	// head
	$translateargs = array
	(
		"lookup" => $lookup,
		"item" => $widget_start_htmltemplate,
	);
	$widget_start_htmltemplate = nxs_filter_translate_v2($translateargs);
	
	// apply shortcodes
	$widget_start_htmltemplate = do_shortcode($widget_start_htmltemplate);
	
	*/
	
	$prefix = ".nxs-widget-{$placeholderid}";
	
	if ($items_direction == "")
	{
		$items_direction = "row";
	}
	
	if ($items_direction == "column" || $items_direction == "column-reverse")
	{
		$max_items_horizontally = 1;
	}
	
	if ($max_items_horizontally == 0)
	{
		$max_items_horizontally = 3;
	}
	
	$max_width = 100;
	if ($max_items_horizontally > 1)
	{
		$max_width = floor(100 / $max_items_horizontally);
	}
	
	if ($justify_content == "")
	{
		$justify_content = "space-between";
	}
	
	if ($align_items == "")
	{
		$align_items == "baseline";
	}
	
	$gutter = 10;
	
	ob_start();
	?>
	<style>
		<?php echo $prefix; ?> .repeater-item 
		{
		  flex: 1 0 <?php echo $max_width; ?>%;
		  width: <?php echo $max_width; ?>%;
			padding: <?php echo $gutter; ?>px;
			box-sizing: border-box;
		}
	</style>
	<div style='display: flex; flex-wrap: wrap; justify-content: <?php echo $justify_content; ?>; flex-direction: <?php echo $items_direction; ?>; align-items: <?php echo $align_items; ?>'>
	<?php
	$widget_start_htmltemplate = ob_get_clean();
	
	// ------------
	
	// footer
	
	/*
	$translateargs = array
	(
		"lookup" => $lookup,
		"item" => $widget_end_htmltemplate,
	);
	$widget_end_htmltemplate = nxs_filter_translate_v2($translateargs);
	
	// apply shortcodes
	$widget_end_htmltemplate = do_shortcode($widget_end_htmltemplate);
	*/
	
	$widget_end_htmltemplate = "</div>";
	
	$shouldrendercolumns = $lookup["nxs_embedrepeater_layout"] == "flexauto"; 
	
	if ($shouldrendercolumns)
	{
		//
		$html .= "<div class='nxsgrid-container' id='nxsgrid-c-{$placeholderid}'>";
	}
	
	$html .= $widget_start_htmltemplate;
	$actuallyrendered = 0;
	foreach ($modeluriset as $modeluri)
	{
		$databindindex++;
		
		if ($filter_items_indexconstrained_min != "")
		{
			if ($databindindex <= $filter_items_indexconstrained_min)
			{
				// ignore
				continue;
			}
		}
		
		if ($filter_items_indexconstrained_max != "")
		{
			if ($databindindex >= $filter_items_indexconstrained_max)
			{
				// ignore
				break;
			}
		}
		
		//
		
		$pieces = explode("@", $modeluri);
		$itemhumanmodelid = $pieces[0];
		$itemschema = $pieces[1];

		if ($settype == "custom" || $settype == "complete")
		{
			// combine the iterator model together with any other additional models the template needs
			$iteratormodeluri = "iterator:{$itemhumanmodelid}@{$itemschema}";
			$combinedmodeluris = "{$iteratormodeluri}";
		}
		
		// fill the lookups
		$lookup = nxs_lookups_getcombinedlookups_for_currenturl();
		
		// add the lookup values from pluggable sources
		$context = array
		(
			"id" => "widget_embedrepeater",
			"prefix" => "i.",
			"modeluri" => $modeluri,
		);
		$add = nxs_lookups_getlookups_for_context($context);
		$lookup = array_merge($lookup, $add);
		
		// add the lookup values from the widget itself
		$lookup = array_merge($lookup, $lookups_widget);
		
		if ($settype == "custom" || $settype == "complete")
		{
			// third, set (override) lookup key/values as defined by referenced models
			// (such as iterator:properties.{xyz})
			if ($combinedmodeluris != "")
			{
				$lookupargs = array
				(
					"modeluris" => $combinedmodeluris,
				);
				$iteratorlookups = $nxs_g_modelmanager->getlookups_v2($lookupargs);
				$lookup = array_merge($lookup, $iteratorlookups);
			}
		}
		else if ($settype == "json")
		{
			$iteratorlookups = array();
			foreach ($modeluri as $key => $val)
			{
				$iteratorlookups["iterator:properties.$key"] = $val;
			}
			$lookup = array_merge($lookup, $iteratorlookups);
		}
		
		// set (override) lookup key/values as defined within the widget itself
		if ($item_lookups != "")
		{
			$lookup = array_merge($lookup, nxs_parse_keyvalues($item_lookups));
		}
		
		// apply lookups to one-self
		if (true)
		{
			$lookup = nxs_lookups_blendlookupstoitselfrecursively($lookup);
		}
		
		global $nxs_gl_sc_currentscope;
		$nxs_gl_sc_currentscope["embedrepeater.iterator.filter"] = true;
		
		// apply shortcodes
		if (true)
		{
			foreach ($lookup as $key => $val)
			{
				$lookup[$key] = do_shortcode($val);
			}
		}
		
		global $nxs_gl_sc_currentscope;
		$nxs_gl_sc_currentscope["embedrepeater.iterator.filter"] = false;
		
		if ($datasource_isvalid)
		{
			if ($filter_items_where != "")
			{
				// apply the lookup values on the where variable
				$filterargs = array
				(
					"lookup" => $lookup,
					"item" => $filter_items_where,
				);
				$evaluated_filter_items_where = nxs_filter_translate_v2($filterargs);
				
				// apply the shortcodes
				$evaluated_filter_items_where = do_shortcode($evaluated_filter_items_where);
			}
			
					
			//
			if ($evaluated_filter_items_where != "")
			{
				$filters = $evaluated_filter_items_where;
				$filters = str_replace(";", "&&", $filters);
				
				$filterconditionvalue = true;
				
				$filterpieces = explode("&&", $filters);
				
				foreach ($filterpieces as $filterpiece)
				{
					$filterpiece = trim($filterpiece);
					$evaluation = $filterpiece;
					
					if ($evaluation != "true")
					{
						$filterconditionvalue = false;
						// it failed
						break;
					}
					else
					{
						// it succeeded, continu to next ops
					}
				}
				
				if ($filterconditionvalue == false)
				{
					// proceed to the next item
					continue;
				}
				else
				{
					//var_dump($lookup);
					//die();
				}
			}
		}
		else
		{
			// skip filter; we want to show dummy items
		}
		
		// optionally evaluate/apply runtime filters based upon the values as defined in the models and lookups
		// this can be used to filter items, like only display items for which a particular attribute meets specific
		// condition(s).
		// TODO (see entities widget on how this can be implemented)
		
		// if we reach this far, the filters apply
		// increase counter of items within filter
		$indexwithinfilter++;
		
		if ($item_htmltemplate_a == "")
		{
			if (is_user_logged_in())
			{
				$item_htmltemplate_a = "<div>Empty template</div>";
			}
		}
		
		// replace the placeholders in the template
		$subhtml = $item_htmltemplate_a;
		$actuallyrendered++;
		
		if ($_REQUEST["embedrepeatercustom"] == "true")
		{
			var_dump($subhtml);
			var_dump($lookup);
			die();
		}
	
		if ($datasource_isvalid)
		{
			// ----
			
			// content			
			$translateargs = array
			(
				"lookup" => $lookup,
				"item" => $subhtml,
			);
			$subhtml = nxs_filter_translate_v2($translateargs);
			
			// auto generate nested templates (nxs_embed's of local wp posts)
			// to re-use the properties of the item we are iterating over
			$lookupscontext = "embedrepeaterwidget_{$postid}_{$placeholderid}_embedrepeateritem";
			
			// add the lookup values from pluggable sources prior to applying the shortcodes
			if (true)
			{
				$context = array
				(
					"id" => "widget_embedrepeater",
					"prefix" => "parent.",
					"modeluri" => $modeluri,
				);
				$sublookups = nxs_lookups_getlookups_for_context($context);
				nxs_lookups_context_adddynamiclookups($lookupscontext, $sublookups);
			}
			
			// apply shortcodes
			$subhtml = do_shortcode($subhtml);
			
			// remove properties of nested template
			if (true)
			{
				nxs_lookups_context_removedynamiclookups($lookupscontext);
			}
			
			// ----
		}
		
		$styleatt = "";
		if (count($styleatts) > 0)
		{
			$values = "";
			foreach ($styleatts as $k => $v)
			{
				$values .= "{$k}: {$v};";
			}
			$styleatt = "style='" . $values . "'";
		}
		
		if ($shouldrendercolumns)
		{
			$columnsvariable = '{{NXS.NUMCOLUMNS}}';
			
			//$html .= "<div class='nxsgrid-item nxsgrid-column-{$columnsvariable} nxs-entity' data-id='{$post_id}' {$styleatt}>";
			$html .= "<div class='nxsgrid-item nxssolidgrid-column-{$columnsvariable} nxs-entity' data-id='{$post_id}' {$styleatt}>";
		}
				
		$html .= $subhtml;
		
		if ($shouldrendercolumns)
		{
			$html .= "</div>";
		}
		
		// break the loop if this was the max pagesize
		if ($filter_pagination_pagesize != "")
		{
			if ($indexwithinfilter >= $filter_pagination_pagesize)
			{
				$reachedpaginationmax = true;
				break;
			}
		}
	}
	
	if ($actuallyrendered > 0)
	{
		// fill up tail items to fix flex box wrap ...
		$numbertofillup = ($max_items_horizontally % $actuallyrendered);
		while ($numbertofillup > 0)
		{
			$html .= "<div class='repeater-item'></div>";
			$numbertofillup--;
		}
	}
	
	$html .= $widget_end_htmltemplate;
	
	if ($shouldrendercolumns)
	{
		$html .= "</div>";
	}
	
	$numberofitemsactuallyshowing = $indexwithinfilter;
	
	// derive the number of columns to render
	if (true)
	{
		$numberofcolumns = 4;
		if ($numberofitemsactuallyshowing % 3 == 0)
		{
			$numberofcolumns = 3;
		}
		else if ($numberofitemsactuallyshowing % 4 == 0)
		{
			$numberofcolumns = 4;
		}
		else if ($numberofitemsactuallyshowing % 2 == 0)
		{
			$numberofcolumns = 2;
		}
		else
		{
			if ($numberofitemsactuallyshowing == 1)
			{
				$numberofcolumns = 1;
			}
			else if ($numberofitemsactuallyshowing == 5)
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
		
		if ($columnsmax != "")
		{
			if ($numberofcolumns > $columnsmax)
			{
				$numberofcolumns = $columnsmax;
			}
		}
		if ($columnsmin != "")
		{
			if ($numberofcolumns < $columnsmin)
			{
				$numberofcolumns = $columnsmin;
			}
		}
		
		// apply the conclusion to the html
	}
	
	// apply the number of columns to the rendered result
	if (true)
	{
		$html = str_replace($columnsvariable, $numberofcolumns, $html);
	}
	
	//
	
	if (is_user_logged_in())
	{
		if ($indexwithinfilter <= 0)
		{
			if (true)
			{
				//
				if (is_user_logged_in())
				{
					if ($iterator_datasource == "")
					{
						
						$html .= nxs_getplaceholderwarning("<div>The iterator_datasource is not yet set</div>");
						$nxs_global_row_render_statebag["hidewheneditorinactive"] = true;
					}
					else
					{
						$html .= nxs_getplaceholderwarning("<div>No {$iterator_datasource} items found (indexwithinfilter:{$indexwithinfilter}, valid? " . json_encode($datasource_isvalid) . ")</div>");
						$nxs_global_row_render_statebag["hidewheneditorinactive"] = true;
					}
				}
				else
				{
					// hide for anynomous users
				}
			}
		}
	
		if ($reachedpaginationmax)
		{
			$html .= nxs_getplaceholderwarning("<div class='nxs-hidewheneditorinactive' style='background-color: red; color: white; padding: 2px; margin: 2px;'><br/>hint; reached pagination max, possibly additional items are not rendered</div>");
		}
		
		$indexcount = count($modeluriset);
		// $html .= "<div class='nxs-hidewheneditorinactive' style'display: block;'>hint; iterated over $indexcount items, rendered $indexwithinfilter items</div><br />";
	
		if (nxs_iswebmethodinvocation())
		{
			// $html .= "<div class='nxs-hidewheneditorinactive' style'display: block; background-color: red; color: white; margin: 2px; padding: 2px;'>You might need to refresh the page to get actual results</div><br />";
		}
		
		if (!$datasource_isvalid)
		{
			$html .= nxs_getplaceholderwarning("<div class='nxs-hidewheneditorinactive' style'display: block; background-color: red; color: white; margin: 2px; padding: 2px;'>No, or invalid datasource; '$iterator_datasource'</div><br />");
		}
	}
	
	//
	
	if (nxs_has_adminpermissions() && $_REQUEST["customhtml"] == "off")
	{
		$html = "<div>ITEMS</div>";
	}
	
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

function nxs_widgets_embedrepeater_initplaceholderdata($args)
{
	extract($args);

	// 
	$args['any_ph_margin_bottom'] = "1-0";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_embedrepeater_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_embedrepeater_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}