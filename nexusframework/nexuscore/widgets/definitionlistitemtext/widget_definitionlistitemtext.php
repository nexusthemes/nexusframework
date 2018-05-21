<?php

function nxs_widgets_definitionlistitemtext_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-text";
}

function nxs_widgets_definitionlistitemtext_gettitle()
{
	return nxs_l18n__("Text input", "nxs_td");
}

// Unicontent
function nxs_widgets_definitionlistitemtext_getunifiedcontentgroup() {
	return "dlitext";
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_definitionlistitemtext_renderindefinitionlistbox($args)
{
	extract($args);
	
	$metadata = nxs_filter_translatelookup($metadata, array("title", "text"));

	extract($metadata, EXTR_PREFIX_ALL, "metadata");
	extract($container_metadata, EXTR_PREFIX_ALL, "container_metadata");
	
	$result = array();
	$result["result"] = "OK";
	
	//
	// render actual control / html
	//
	
	nxs_ob_start();

	if ($container_metadata_items_title_fontzen != "")
	{
		$title_fontzen_cssclass = ' class="' . nxs_getcssclassesforlookup("nxs-fontzen nxs-fontzen-", $container_metadata_items_title_fontzen) . '"';
	}

	if ($container_metadata_items_title_fontzen != "")
	{
		$description_fontzen_cssclass = ' class="' . nxs_getcssclassesforlookup("nxs-fontzen nxs-fontzen-", $container_metadata_items_description_fontzen) . '"';
		$patterns = '<li';
		$replacements = '<li' . $description_fontzen_cssclass;
		$metadata_text = str_replace($patterns, $replacements, $metadata_text);
	}

	?>
	<span>
		<em<?php echo $title_fontzen_cssclass; ?>><?php echo $metadata_title; ?></em>
		<div<?php echo $description_fontzen_cssclass; ?>><?php echo $metadata_text; ?></div>
	</span>
	<?php
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

function nxs_widgets_definitionlistitemtext_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// Blend unistyle properties
	// N/A for this widget
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") 
	{
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_definitionlistitemtext_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	$mixedattributes = array_merge($temp_array, $args);
	
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title", "text"));

	$title = $mixedattributes['title'];
	$text = $mixedattributes['text'];

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
		$hovermenuargs["metadata"] = $mixedattributes;
		$hovermenuargs["enable_deletewidget"] = false;
		$hovermenuargs["enable_deleterow"] = true;
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs); 
	}
		
	/* ADMIN EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-definitionlistitemtext-item";
	
	/* ADMIN OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	echo '
	<div class="nxs-dragrow-handler nxs-padding-menu-item"> 
		<div class="content2">
			<div class="box">
				<h4>' . $title . '</h4>
				<p>' . $text . '</p>
			</div>
			<div class="nxs-clear"></div>
		</div>
	</div>';
	
	/* ------------------------------------------------------------------------------------------------- */

	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

// Define the properties of this widget
function nxs_widgets_definitionlistitemtext_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_definitionlistitemtext_gettitle(),
		"sheeticonid" => nxs_widgets_definitionlistitemtext_geticonid(),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_definitionlistitemtext_getunifiedcontentgroup(),),
		"fields" => array
		(
			array
			(
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" => nxs_l18n__("Title goes here", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			
			array(
				"id" 				=> "text",
				"type" 				=> "tinymce",
				"label" 			=> nxs_l18n__("Text", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Text goes here", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
		)
	);
	
	return $options;
}

function nxs_widgets_definitionlistitemtext_initplaceholderdata($args)
{
	extract($args);
	
	// current values as defined by unistyle prefail over the above "default" props
	// N/A
	
	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_definitionlistitemtext_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);

	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_dataprotection_nexusframework_widget_definitionlistitemtext_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("widget-none");
}

?>