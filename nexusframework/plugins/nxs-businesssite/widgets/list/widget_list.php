<?php

nxs_requirewidget("generic");

function nxs_widgets_list_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-moving";
}

function nxs_widgets_list_gettitle() {
	return nxs_l18n__("list", "nxs_td");
}

// Unistyle
function nxs_widgets_list_getunifiedstylinggroup() {
	return "listwidget";
}

// Unicontent
function nxs_widgets_list_getunifiedcontentgroup() {
	return "listwidget";
}

function nxs_list_datasourcecustom_popupcontent($optionvalues, $args, $runtimeblendeddata) 
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

function nxs_list_fieldoftaxonomycustom_popupcontent($optionvalues, $args, $runtimeblendeddata) 
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
function nxs_widgets_list_home_getoptions($args) 
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
	
	$datasource = $args["datasource"];
	
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_list_gettitle(),
		"sheeticonid" 		=> nxs_widgets_list_geticonid(),
		"sheethelp" 		=> nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=826980725"),
		"unifiedstyling" 	=> array("group" => nxs_widgets_list_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_list_getunifiedcontentgroup(),),
		"footerfiller" => true,	// add some space at the bottom
		"fields" => array
		(
			array
      (
				"id" 					=> "lookups",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Lookup table (evaluated one time when the widget renders)", "nxs_td"),
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
				"placeholder" => "For example 'foobar' to iterate over singleton@listoffoobar models, or a list like (foo@model;bar@model) for a specific set of models",
			),
			
			array
      (
				"id" 					=> "filter_items_indexconstrained_min",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Number of index items to ignore (blank=no skipping)", "nxs_td"),
			),
			
			array
      (
				"id" 					=> "filter_items_indexconstrained_max",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Max index to process (blank=no skipping)", "nxs_td"),
			),
			
			array
      (
				"id" 					=> "filter_pagination_pagesize",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Max number of items per page (blank=no paging)", "nxs_td"),
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
			
			// item_htmltemplate
			array
			(
        "id" 				=> "wrapper_items_begin",
        "type" 				=> "wrapperbegin",
        "label" 			=> nxs_l18n__("Design", "nxs_td"),
      ),
      array
      (
				"id" 					=> "widget_start_htmltemplate",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Widget start html (renders 1x above the items)", "nxs_td"),
			),
      array
      (
				"id" 					=> "item_htmltemplate_a",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Template (renders for each iterated item)", "nxs_td"),
			),
			 array
      (
				"id" 					=> "widget_end_htmltemplate",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Widget end html (renders 1x below the items)", "nxs_td"),
			),
			
			array
      (
				"id" 					=> "columnsmin",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Columns min", "nxs_td"),
				"dropdown" 		=> array
				(
					"@@@empty@@@" => "Default",
					"1" => "1",
					"2" => "2",
					"3" => "3",
					"4" => "4",
					"5" => "5",
					"6" => "6",
				),
				"unistylablefield" => true,
			),
			
			array
      (
				"id" 					=> "columnsmax",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Columns max", "nxs_td"),
				"dropdown" 		=> array
				(
					"@@@empty@@@" => "Default",
					"1" => "1",
					"2" => "2",
					"3" => "3",
					"4" => "4",
					"5" => "5",
					"6" => "6",
				),
				"unistylablefield" => true,
			),
			
			array
			(
        "id" 				=> "wrapper_items_end",
        "type" 				=> "wrapperend",
      ),
			
			
			// --- ANY WIDGET SPECIFIC STYLING; COLORS & TEXT
			
      array
      (
          "id" 				=> "any_wrapper_colorstext_begin",
          "type" 				=> "wrapperbegin",
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
				"customcontenthandler"	=> "nxs_list_custom_popupcontent",
				"label" 			=> nxs_l18n__("...", "nxs_td"),
				"layouttype"		=> "custom",
			),
		)
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

function nxs_list_getdefaultitemsstyle($modeluri, $datasource)
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

function nxs_list_geticon($datasource)
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

function nxs_list_custom_popupcontent($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);

	$itemsstyle = nxs_list_getdefaultitemsstyle($modeluri, $datasource);

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

function nxs_widgets_list_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_list_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") 
	{
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_list_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	//$mixedattributes = $temp_array;
	$mixedattributes = array_merge($temp_array, $args);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title", "list", "button_list", "destination_url"));
	
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

	$itemsstyle = nxs_list_getdefaultitemsstyle($modeluri, $datasource);
	$childwidgettype = $itemsstyle;	
	
	// Overruling of parameters
	
	global $nxs_global_row_render_statebag;
	$pagerowtemplate = $nxs_global_row_render_statebag["pagerowtemplate"];
	if ($pagerowtemplate == "one")
	{
		$list_heightiq = "";	// off!
	}

	if ($postid != "" && $placeholderid != "")
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
	
	$trlookups = nxs_gettemplateruleslookups();
	
	$lookups_widget = array();
	if ($lookups != "")
	{
		$lookups_widget = nxs_parse_keyvalues($lookups);
		
		//
		$lookups_widget = array_merge($trlookups, $lookups_widget);
		
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
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	$datasource_isvalid = true;
	
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
				"0", "0", "0", "0", 
				"0", "0", "0", "0", 
				"0", "0", "0", "0", 
				"0", "0", "0", "0", 

				"0", "0", "0", "0", 
				"0", "0", "0", "0", 
				"0", "0", "0", "0", 
				"0", "0", "0", "0", 

				"0", "0", "0", "0", 
				"0", "0", "0", "0", 
				"0", "0", "0", "0", 
				"0", "0", "0", "0", 

				"0", "0", "0", "0", 
				"0", "0", "0", "0", 
				"0", "0", "0", "0", 
				"0", "0", "0", "0", 
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
		if (nxs_stringcontains($iterator_datasource, "@"))
		{
			$settype = "custom";
		}
		else
		{
			$settype = "complete";
		}
		
		if ($settype == "complete")
		{
			$iteratormodeluri = "singleton@listof{$iterator_datasource}";
			
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
			$canonical_iterator_datasource = $iterator_datasource;
			// iterator_datasource is for example (foobar1@bar,foobar2@bar)
			$canonical_iterator_datasource = trim($canonical_iterator_datasource, "()");
			$canonical_iterator_datasource = trim($canonical_iterator_datasource, "[]");
			// iterator_datasource is for example foobar1@bar,foobar2@bar
			$canonical_iterator_datasource = str_replace(",", ";", $canonical_iterator_datasource);
			$canonical_iterator_datasource = str_replace("|", ";", $canonical_iterator_datasource);
			$canonical_iterator_datasource = str_replace(" ", ";", $canonical_iterator_datasource);
			$pieces = split(";", $canonical_iterator_datasource);
			foreach ($pieces as $piece)
			{
				$itemhumanmodelid = trim($piece);
				$modeluriset[] = $itemhumanmodelid;
			}
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
	// ------------
	
	// footer
	$translateargs = array
	(
		"lookup" => $lookup,
		"item" => $widget_end_htmltemplate,
	);
	$widget_end_htmltemplate = nxs_filter_translate_v2($translateargs);
	
	// apply shortcodes
	$widget_end_htmltemplate = do_shortcode($widget_end_htmltemplate);
	
	$shouldrendercolumns = $lookup["nxs_list_layout"] == "";
	
	if ($shouldrendercolumns)
	{
		//
		$html .= "<div class='nxsgrid-container' id='nxsgrid-c-{$placeholderid}'>";
	}
	
	$html .= $widget_start_htmltemplate;
	
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
		
		$pieces = split("@", $modeluri);
		$itemhumanmodelid = $pieces[0];
		$itemschema = $pieces[1];

		// combine the iterator model together with any other additional models the template needs
		$iteratormodeluri = "iterator:{$itemhumanmodelid}@{$itemschema}";
		$combinedmodeluris = "{$iteratormodeluri}|{$item_modeluris}";
		
		// translates variables in the combinedmodeluris
		$evaluateargs = array
		(
			"modeluris" => $combinedmodeluris,
			"shouldapply_templaterules_lookups" => true,
		);
		$combinedmodeluris = $nxs_g_modelmanager->evaluatereferencedmodelsinmodeluris_v2($evaluateargs);
		
		// fill the lookups
		$lookup = array();
		
		// first the lookup table as defined in the pagetemplaterules
		if (true)
		{
			$templateruleslookups = nxs_gettemplateruleslookups();
			$lookup = array_merge($lookup, $trlookups);
		}
		
		// add the lookup values from the widget itself
		$lookup = array_merge($lookup, $lookups_widget);
		
		
		
		// second, set (override) lookup key/values as defined within the widget itself
		if ($item_lookups != "")
		{
			$lookup = array_merge($lookup, nxs_parse_keyvalues($item_lookups));
		}
		
		// third, set (override) lookup key/values as defined by referenced models
		// (such as iterator:properties.{xyz})
		if ($combinedmodeluris != "")
		{
			$lookupargs = array
			(
				"modeluris" => $combinedmodeluris,
			);
			$widgetreferencedmodelslookup = $nxs_g_modelmanager->getlookups_v2($lookupargs);
			$lookup = array_merge($lookup, $widgetreferencedmodelslookup);
		}
		
		// apply lookups to one-self
		if (true)
		{
			$lookup = nxs_lookups_blendlookupstoitselfrecursively($lookup);
		}
		
		/*
		if ($_REQUEST["huh"] == "b2")
		{
			foreach ($lookup as $k => $v)
			{
				echo "$k<br />value<br />$v<br /><br />";
			}
			var_dump($lookup);
			die();
		}
		*/
		
		global $nxs_gl_sc_currentscope;
		$nxs_gl_sc_currentscope["list.iterator.filter"] = true;
		
		// apply shortcodes
		if (true)
		{
			foreach ($lookup as $key => $val)
			{
				$lookup[$key] = do_shortcode($val);
			}
		}
		
		global $nxs_gl_sc_currentscope;
		$nxs_gl_sc_currentscope["list.iterator.filter"] = false;
		
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
			
			// apply shortcodes
			$subhtml = do_shortcode($subhtml);
			
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
		
		$shouldrendercolumns = $lookup["nxs_list_layout"] == "";
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
	
	$html .= $widget_end_htmltemplate;
	
	if ($shouldrendercolumns)
	{
		$html .= "</div>";
	}
	
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
						$html .= "<div>The iterator_datasource is not yet set</div>";
						$nxs_global_row_render_statebag["hidewheneditorinactive"] = true;
					}
					else
					{
						$html .= "<div>No {$iterator_datasource} items found</div>";
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
			$html .= "<br/><div class='nxs-hidewheneditorinactive' style='background-color: red; color: white; padding: 2px; margin: 2px;'>hint; reached pagination max, possibly additional items are not rendered</div>";
		}
		
		$indexcount = count($modeluriset);
		$html .= "<div class='nxs-hidewheneditorinactive' style'display: block;'>hint; iterated over $indexcount items, rendered $indexwithinfilter items</div><br />";
	
		if (nxs_iswebmethodinvocation())
		{
			$html .= "<div class='nxs-hidewheneditorinactive' style'display: block; background-color: red; color: white; margin: 2px; padding: 2px;'>You might need to refresh the page to get actual results</div><br />";
		}
		
		if (!$datasource_isvalid)
		{
			$html .= "<div class='nxs-hidewheneditorinactive' style'display: block; background-color: red; color: white; margin: 2px; padding: 2px;'>No, or invalid datasource; '$iterator_datasource'</div><br />";
		}
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

function nxs_widgets_list_initplaceholderdata($args)
{
	extract($args);

	// 
	$args['any_ph_margin_bottom'] = "1-0";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_list_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_list_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}