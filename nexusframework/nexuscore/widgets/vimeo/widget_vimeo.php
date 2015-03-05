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
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/vimeo-widget/"),
		"fields" => array
		(
			/* TITLE
			
			array( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array
			( 
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Title goes here", "nxs_td"),
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title importance", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading")
			),
			
			array(
				"id" 				=> "title_alignment",
				"type" 				=> "halign",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
			),
						
			array(
				"id" 				=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Override title fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize")
			),
			array(
				"id" 				=> "title_heightiq",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align titles", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's title will participate in the title alignment of other partipating widgets in this row", "nxs_td")
			),
		
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),
			*/
			
			
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
	
	// Localize atts
	$mixedattributes = nxs_localization_localize($mixedattributes);
	
	extract($mixedattributes);
	
	global $nxs_global_placeholder_render_statebag;
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["enable_decoratewidget"] = false;
	$hovermenuargs["enable_deletewidget"] = true;
	$hovermenuargs["enable_deleterow"] = false;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
	if ($videoid == "")
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("No video set", "nxs_td");
	}
	
	//
	// render actual control / html
	//
	
	ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-vimeo";
	
	// Title
	$htmltitle = nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, "", "");
	
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
		$scheme = "http";
		if (is_ssl()) 
		{
			$scheme = "https";
		}
		
		echo '
		<div '.$class.'>';
	
			echo '   
			<div class="video-container">
			
				<iframe class="nxs-vimeo-iframe nxs-youtube-iframe" src="'.$scheme.'://player.vimeo.com/video/'.$videoid.'" frameborder="0"></iframe>
			
			</div>
		</div>';
						
	}
	
	$html = ob_get_contents();
	ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	// outbound statebag
	// $nxs_global_row_render_statebag["foo"] = "bar";

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
?>
