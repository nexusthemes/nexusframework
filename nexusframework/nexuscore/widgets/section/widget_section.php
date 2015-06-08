<?php

function nxs_widgets_section_geticonid() {
	return "nxs-icon-link";
}

function nxs_widgets_section_gettitle()
{
	return nxs_l18n__("section", "nxs_td");
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_section_render_webpart_render_htmlvisualization($args)
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
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("section"));
	
	$section = $mixedattributes['section'];

	global $nxs_global_placeholder_render_statebag;

	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs); 
		
	global $nxs_global_placeholder_render_statebag;
	$nxs_global_placeholder_render_statebag["widgetcropping"] = "no";		// de custom html kent geen cropping (praktisch voor bijv. absolute/fixed positionering van elementen)

	/* EXPRESSIONS
	----------------------------------------------------------------------------------------------------*/
	
	
	/* OUTPUT
	----------------------------------------------------------------------------------------------------*/
		
	// Icon
	if ($icon != "") {
		$icon = '<span class="icon ' . $icon . '"></span>';
	}

	$hash = str_replace(' ', '-', $section);
	
	ob_start();

	$container_class = 'hide';
	if (nxs_has_adminpermissions()) {
		$container_class = 'nxs-hidewheneditorinactive';
	} 

	?>

	<div class="section"></div>
	<div id="<?php echo $hash; ?>" class="nxs-section">
		<div class="nxs-section-container <?php echo $container_class; ?>">
			<h5>
				<div class="serperator-before"></div>
				<?php echo $icon; ?>
				<span class="nxs-section-title"><?php echo $section ?></span>
				<div class="serperator-after"></div>
			</h5>
			<div class="nxs-section-icon">
				<?php echo $icon; ?>
			</div>
		</div>
	</div>


	<?php
	
	$shouldrenderalternative = false;
	$trimmedsection = $section;
	$trimmedsection = preg_replace('/<!--(.*)-->/Uis', '', $trimmedsection);
	$trimmedsection = trim($trimmedsection);
	if ($trimmedsection == "" && nxs_has_adminpermissions())
	{
		$shouldrenderalternative = true;
	}
	
	?>
	
	<!-- -->
	
	
	<?php 
	
	$html = ob_get_contents();
	ob_end_clean();

	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	// outbound statebag
	
	return $result;
}


/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_section_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_section_gettitle(),
		"sheeticonid" => nxs_widgets_section_geticonid(),
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/html-widget/"),
		"fields" => array
		(
			// -------------------------------------------------------			
			
			array( 
				"id" 				=> "wrapper_input_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Configuration", "nxs_td"),
			),

			array(
				"id" 				=> "icon",
				"type" 				=> "icon",
				"label" 			=> nxs_l18n__("Icon", "nxs_td"),
			),
			
			array(
				"id" 				=> "section",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
			),
			
			array( 
				"id" 				=> "wrapper_input_end",
				"type" 				=> "wrapperend"
			),
			// -------------------------------------------------------
			
		),
	);
		
	return $options;
}

function nxs_widgets_section_initplaceholderdata($args)
{
	extract($args);
	
	$args['ph_margin_bottom'] = "0-0";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>