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
		"sheethelp" => nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1764396204"),
		"fields" => array
		(
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
			
				<iframe class="nxs-vimeo-iframe nxs-youtube-iframe" src="'.$scheme.'://player.vimeo.com/video/'.$videoid.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
			
			</div>
		</div>';
						
	}
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
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
