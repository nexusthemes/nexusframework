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

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_definitionlistitemtext_renderindefinitionlistbox($args)
{
	extract($args);
	
	// Localize atts
	$metadata = nxs_localization_localize($metadata);

	
	extract($metadata, EXTR_PREFIX_ALL, "metadata");
	
	$result = array();
	$result["result"] = "OK";
	
	//
	// render actual control / html
	//
	
	ob_start();

	?>
	<span>
		<em><?php echo $metadata_title; ?></em>
		<?php echo $metadata_text; ?>
	</span>
	<?php
	$html = ob_get_contents();
	ob_end_clean();

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
	$mixedattributes = array_merge($temp_array, $args);
	
	// Localize atts
	$mixedattributes = nxs_localization_localize($mixedattributes);
	
	$title = $mixedattributes['title'];
	
	global $nxs_global_placeholder_render_statebag;

	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs); 
		
	/* ADMIN EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-definitionlistitemtext-item";
	
	/* ADMIN OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	echo '
	<div ' . $class . '>
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
	$html = ob_get_contents();
	ob_end_clean();

	
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
	
		"fields" => array
		(
			array
			(
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" => nxs_l18n__("Title goes here", "nxs_td"),
				"localizablefield"	=> true
			),
			
			array(
				"id" 				=> "text",
				"type" 				=> "tinymce",
				"label" 			=> nxs_l18n__("Text", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Text goes here", "nxs_td"),
				"localizablefield"	=> true
			),
		)
	);
	
	return $options;
}

function nxs_widgets_definitionlistitemtext_initplaceholderdata($args)
{
	extract($args);
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
