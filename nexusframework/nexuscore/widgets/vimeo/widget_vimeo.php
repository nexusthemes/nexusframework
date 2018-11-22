<?php

function nxs_widgets_vimeo_gettitle()
{
	return nxs_l18n__("vimeo[nxs:widgettitle]", "nxs_td");
}

function nxs_widgets_vimeo_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Define the properties of this widget
function nxs_widgets_vimeo_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$options = array
	(
		"sheettitle" => nxs_widgets_vimeo_gettitle(),
		"sheeticonid" => nxs_widgets_vimeo_geticonid(),
		"supporturl" => "https://www.wpsupporthelp.com/wordpress-questions/video-wordpress-questions-28/",
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
		
			// TITLE
			
			array
			( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"initial_toggle_state"	=> "closed-if-empty",
				"initial_toggle_state_id" => "title",
			),
			array(
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Title goes here", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array
      (
				"id" 					=> "title_lookuppicker",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_generic_modeltaxfieldpicker_popupcontent",
			),
			
			array
			(
				"id" 				=> "title_postprocessor",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title max length", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@empty@@@" => "None",
					"truncateall" => "Truncate all",
				),
			"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title importance", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Title font", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
				"unistylablefield"	=> true,
				"mobile_action_toggles" => ".nxs-viewport-dependent",
			),
			array(
				"id" 				=> "title_alignment_tablet",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("", "nxs_td"),
				"unistylablefield"	=> true,
				"display" => "noneifempty",
				"fortablets" => true,
				"enable_deselect" => true,
			),
			array(
				"id" 				=> "title_alignment_mobile",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("", "nxs_td"),
				"unistylablefield"	=> true,
				"display" => "noneifempty",
				"formobiles" => true,
				"enable_deselect" => true,
			),
			array(
				"id" 				=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "icon",
				"type" 				=> "icon",
				"label" 			=> nxs_l18n__("Icon", "nxs_td"),
				"unicontentablefield" => true,
			),
			array(
				"id"     			=> "icon_scale",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Icon size", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("icon_scale"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),
		
			// CONFIGURATION
			
			array( 
				"id" 				=> "wrapper_vimeo_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Vimeo settings", "nxs_td"),
			),
		
			array(
				"id" 				=> "videoid",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Video ID", "nxs_td"),
				"localizablefield"	=> true
			),		
			array
			(
				"id" 				=> "aspect_ratio",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Aspect Ratio", "nxs_td"),
				"dropdown" 			=> array
				(
					"16_9" 	=> "16:9 (default)",
					"21_9" 			=> "21:9",
				),
				"unistylablefield"	=> true
			),
			
			
			array( 
				"id" 				=> "wrapper_vimeo_end",
				"type" 				=> "wrapperend"
			),		
		),
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_vimeo_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	$mixedattributes = array_merge($temp_array, $args);
	
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
		$magicfields = array("title", "videoid");
		$translateargs = array
		(
			"lookup" => $combined_lookups,
			"items" => $mixedattributes,
			"fields" => $magicfields,
		);
		$mixedattributes = nxs_filter_translate_v2($translateargs);
	}
	
	extract($mixedattributes);
	
	global $nxs_global_placeholder_render_statebag;
	
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
		$hovermenuargs["enable_decoratewidget"] = false;
		$hovermenuargs["enable_deletewidget"] = true;
		$hovermenuargs["enable_deleterow"] = false;
		$hovermenuargs["metadata"] = $mixedattributes;
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	}
	
	if ($videoid == "")
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("No video set", "nxs_td");
	}
	
	//
	// render actual control / html
	//
	
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-vimeo";
	
	// Title
	// new implementation delegates rendering the title to the frontendframework
	$a = array
	(
		"title" => $title,
		"heading" => $title_heading,
		"align" => $title_alignment,
		"align_tablet" => $title_alignment_tablet,
		"align_mobile" => $title_alignment_mobile,
		"fontsize" => $title_fontsize,
		"heightiq" => "title",
		"destination_articleid" => $destination_articleid,
		"destination_url" => $destination_url,
		"destination_target" => $destination_target,
		"destination_relation" => $destination_relation,
		"shouldapplylinkvarcolor" => $shouldapplylinkvarcolor,
		// "microdata" => 
		"fontzen" => $title_fontzen,
	);
	$titlehtml = nxs_gethtmlfortitle_v4($a);
	
	// Aspect Ratio
	if ($aspect_ratio == "") 		{ $aspect_ratio = "56.25%"; } else
	if ($aspect_ratio == "16_9") 	{ $aspect_ratio = "56.25%"; } else
	if ($aspect_ratio == "21_9") 	{ $aspect_ratio = "42.86%"; }
	
	// Filler
	$htmlfiller = nxs_gethtmlforfiller();
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	if ($shouldrenderalternative) {
		if ($alternativehint == "") {
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} 
	else 
	{
		$src = "https://player.vimeo.com/video/{$videoid}";
		echo '
		<div '.$class.'>';
			
			
			echo $titlehtml;
			
			if ($titlehtml != "") { echo '<div class="nxs-clear nxs-filler"></div>'; }
			
			echo '
			<div class="video-container" style="padding-bottom: '.$aspect_ratio.';">
			   <iframe class="nxs-vimeo-iframe nxs-youtube-iframe" src="'.$src.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
			</div>
    	</div>
    ';
	}
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	// data protection handling
	if (true)
	{
		$activity = "nexusframework:widget_vimeo";
		if (!nxs_dataprotection_isactivityonforuser($activity))
		{
			// not allowed
			$result["html"] = "";
		}
	}

	return $result;
}

function nxs_widgets_vimeo_initplaceholderdata($args)
{
	extract($args);
	
	$args["videoid"] = nxs_l18n__("videoid[vimeo,sample,14692378]", "nxs_td");
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_dataprotection_nexusframework_widget_vimeo_getprotecteddata($args)
{
	$result = array
	(
		"controller_label" => "Vimeo videos",
		"subactivities" => array
		(
		),
		"dataprocessingdeclarations" => array	
		(
			array
			(
				"use_case" => "(belongs_to_whom_id) can browse a page of the website owned by the (controller) that renders vimdeo videos using the vimeo widget of the framework",
				"what" => "IP address of the (belongs_to_whom_id) as well as 'Request header fields' send by browser of ((belongs_to_whom_id)) (https://en.wikipedia.org/wiki/List_of_HTTP_header_fields#Request_fields)",
				"belongs_to_whom_id" => "website_visitor", // (has to give consent for using the "what")
				"controller" => "website_owner",	// who is responsible for this?
				"controller_options" => nxs_dataprotection_factory_getenableoptions("all"),
				"data_processor" => "Vimeo",	// the name of the data_processor or data_recipient
				"data_retention" => "See the terms https://vimeo.com",
				"program_lifecycle_phase" => "compiletime",
				"why" => "Not applicable (because this is a compiletime declaration)",
				"security" => "The data is transferred over a secure https connection. Security is explained in more detail here; https://vimeo.com",

			),
		),
		"status" => "final",
	);
	return $result;
}

?>
