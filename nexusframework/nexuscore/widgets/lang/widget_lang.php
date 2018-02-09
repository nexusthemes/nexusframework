<?php

nxs_requirewidget("generic");

function nxs_widgets_lang_geticonid()
{
	return "nxs-icon-flag";
}

// Setting the widget title
function nxs_widgets_lang_gettitle() {
	return nxs_l18n__("lang", "nxs_td");
}

// 
function nxs_widgets_lang_getunifiedstylinggroup() {
	return "langwidget";
}

// Unicontent
function nxs_widgets_lang_getunifiedcontentgroup() {
	return "langwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_lang_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$options = array
	(
		"sheettitle" => nxs_widgets_lang_gettitle(),
		"sheeticonid" => nxs_widgets_lang_geticonid(),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_lang_getunifiedstylinggroup(),
		),
		"unifiedcontent" 	=> array 
		(
			"group" => nxs_widgets_lang_getunifiedcontentgroup(),
		),
		"fields" => array
		(
			// -------------------------------------------------------			
			
			// LOOKUPS
			
			array
			( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Lookups", "nxs_td"),
      	"initial_toggle_state" => "closed-if-empty",
      	"initial_toggle_state_id" => "lookups",
			),
			array
      (
				"id" 					=> "lookups",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Lookup table (evaluated one time when the widget renders)", "nxs_td"),
			),
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),			
			
			// TITLES
				
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("lang fields", "nxs_td"),
			),
			
			array
			( 
				"id" 				=> "language",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("lang", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("lang goes here", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("The language this page is in.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
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

function nxs_widgets_lang_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_lang_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);	
	}

	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_lang_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}	
	
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	//
	// Translate model magical fields
	if (true)
	{
		global $nxs_g_modelmanager;
		
		$combined_lookups = nxs_lookups_getcombinedlookups_for_currenturl();
		$combined_lookups = array_merge($combined_lookups, nxs_parse_keyvalues($mixedattributes["lookups"]));

		// evaluate the lookups widget values line by line
		$sofar = array();
		foreach ($combined_lookups as $key => $val)
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

			$combined_lookups[$key] = $val;
		}
		
		// apply the lookups and shortcodes to the customhtml
		$magicfields = array("language");
		$translateargs = array
		(
			"lookup" => $combined_lookups,
			"items" => $mixedattributes,
			"fields" => $magicfields,
		);
		$mixedattributes = nxs_filter_translate_v2($translateargs);
	}
	//
	
	
	
	//
	// Translate model magical fields
	if (true)
	{
		global $nxs_g_modelmanager;
		
		$combined_lookups = nxs_lookups_getcombinedlookups_for_currenturl();
		$combined_lookups = array_merge($combined_lookups, nxs_parse_keyvalues($mixedattributes["lookups"]));

		// evaluate the lookups widget values line by line
		$sofar = array();
		foreach ($combined_lookups as $key => $val)
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

			$combined_lookups[$key] = $val;
		}
		
		// apply the lookups and shortcodes to the customhtml
		$magicfields = array("lang");
		$translateargs = array
		(
			"lookup" => $combined_lookups,
			"items" => $mixedattributes,
			"fields" => $magicfields,
		);
		$mixedattributes = nxs_filter_translate_v2($translateargs);
	}
	//
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
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
			if ($responsive_display == "") { $responsive_display = 'lang720'; }
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

	global $nxs_global_row_render_statebag;
	$nxs_global_row_render_statebag["requiredcapabilities"][] = nxs_cap_getdesigncapability();

	?>
	<style>
		.nxs-can-err
		{
			color: red !important;
			user-select: all;
		}
		.nxs-robots-custom
		{
			color: orange !important;
		}
		.nxs-robots-err
		{
			color: red !important;
		}
	</style>
	<div class='nxs-hidewheneditorinactive'>
		<div style="background-color: white; border: 2px solid black; color: black; padding: 5px;" class="nxs-default-p">
			language: <?php echo $language; ?><br />
		</div>
	</div>
	<script>
		document.title = "<?php echo htmlentities($title); ?>";
	</script>
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

function nxs_widgets_lang_initplaceholderdata($args)
{
	extract($args);

  $args['ph_margin_bottom'] = "0-0";

	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_lang_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_lang_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}
