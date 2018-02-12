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
			
			array
			( 
				"id" 				=> "robots",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("SEO robots", "nxs_td"),
				"footer" 		=> "Possible values: blank (default=index,follow), <a href='#' onclick='jQuery(\"#robots\").val(\"index,follow\");return false;'>index,follow</a> | <a href='#' onclick='jQuery(\"#robots\").val(\"noindex,nofollow\");return false;'>noindex,nofollow</a>",
				"tooltip" 			=> nxs_l18n__("For example index,follow", "nxs_td"),
				//"unicontentablefield" => true,
				//"localizablefield"	=> true
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
		$magicfields = array("title", "metadescription", "canonicalurl", "robots");
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
	if (!nxs_cap_hasdesigncapabilities())
	{
		// don't render the widget
		global $nxs_global_row_render_statebag;
		$nxs_global_row_render_statebag["etchrow"] = true;
	}

	global $nxs_global_row_render_statebag;
	$nxs_global_row_render_statebag["requiredcapabilities"][] = nxs_cap_getdesigncapability();

	// this is needed... 
	$canonicalurl = do_shortcode($canonicalurl);
	$currenturl = nxs_geturlcurrentpage();
	
	$canonicalstate = "";
	$canonicaliconhtml = "";
	if ($canonicalurl != $currenturl)
	{
		$canonicalstate = "nxs-can-err";
	}
	$robots = trim($robots);
	if ($robots == "")
	{
		if ($canonicalurl != "")
		{
			$robots_visualization = "index,follow (default)";
		}
		else
		{
			$robots_visualization = "(default)";
		}
	}
	else 
	{
		$robots_visualization = $robots;
		$expectedparts = array("noindex", "index", "follow", "nofollow", "none", "noarchive", "nosnippet", "noodp", "noodp");
		$foundunexpected = false;
		$pieces = explode(",", $robots_visualization);
		foreach ($pieces as $piece)
		{
			$piece = trim($piece);
			if (!in_array($piece, $expectedparts))
			{
				$foundunexpected = true;
			}
		}
		if ($foundunexpected)
		{
			$robotsstate = "nxs-robots-err";
		}
		else 
		{
			if ($robots != "index,follow")
			{
				$robotsstate = "nxs-robots-custom";
			}
		}
	}
	
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
			seo title: <?php echo $title; ?><br />
			seo description: <?php echo $metadescription; ?><br />
			seo canonical url: <a class='<?php echo $canonicalstate; ?>' href='<?php echo $canonicalurl; ?>' target='_blank'><?php echo $canonicalurl; ?></a><br />
			seo robots: <span class='<?php echo $robotsstate; ?>'><?php echo $robots_visualization; ?></span>
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
	
	if (!nxs_cap_hasdesigncapabilities())
	{
		$result["html"] = "";	
	}
	
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
