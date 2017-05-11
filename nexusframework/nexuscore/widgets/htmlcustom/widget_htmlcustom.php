<?php

function nxs_widgets_htmlcustom_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

function nxs_widgets_htmlcustom_gettitle()
{
	return nxs_l18n__("HTML[nxs:widgettitle]", "nxs_td");
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_htmlcustom_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	if ($render_behaviour == "code")
	{
		//
		$temp_array = array();
	}
	else
	{
		$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	}
	
	//
	
	$mixedattributes = array_merge($temp_array, $args);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("htmlcustom"));
	
	$htmlcustom = $mixedattributes['htmlcustom'];

	global $nxs_global_placeholder_render_statebag;

	if ($render_behaviour == "code")
	{
	}
	else
	{
		$hovermenuargs = array();
		$hovermenuargs["postid"] = $postid;
		$hovermenuargs["placeholderid"] = $placeholderid;
		$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
		$hovermenuargs["metadata"] = $mixedattributes;
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs); 
	}
			
	global $nxs_global_placeholder_render_statebag;
	$nxs_global_placeholder_render_statebag["widgetcropping"] = "no";		// de custom html kent geen cropping (praktisch voor bijv. absolute/fixed positionering van elementen)
	
	//
	// render actual control / html
	//
	
	nxs_ob_start();
	
	if ($render_behaviour == "code")
	{
		//
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-applylinkvarcolor";
	}
	else
	{
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-custom-html nxs-applylinkvarcolor";
	}
	
	?>

	<?php
	
	$shouldrenderalternative = false;
	$trimmedhtmlcustom = $htmlcustom;
	$trimmedhtmlcustom = preg_replace('/<!--(.*)-->/Uis', '', $trimmedhtmlcustom);
	$trimmedhtmlcustom = trim($trimmedhtmlcustom);
	if ($trimmedhtmlcustom == "" && nxs_has_adminpermissions())
	{
		$shouldrenderalternative = true;
	}
	
	?>
	
	<!-- -->
	
	<div <?php echo $class; ?>>
		<?php
		if ($shouldrenderalternative) 
		{
			nxs_renderplaceholderwarning(nxs_l18n__("Empty HTML[nxs:warning]", "nxs_td"));
		} 
		else
		{
			if (nxs_has_adminpermissions() && $_REQUEST["customhtml"] == "off")
			{
				nxs_renderplaceholderwarning(nxs_l18n__("Custom html", "nxs_td"));
			}
			else
			{
				echo do_shortcode($htmlcustom);
			}
		}
		?>
	</div>
	
	<?php 
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

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
function nxs_widgets_htmlcustom_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_htmlcustom_gettitle(),
		"sheeticonid" => nxs_widgets_htmlcustom_geticonid(),
		"sheethelp" => nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1764396204"),
		"fields" => array
		(
			// -------------------------------------------------------			
			
			array( 
				"id" 					=> "wrapper_input_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("HTML properties", "nxs_td"),
			),
			
			array(
				"id" 					=> "htmlcustom",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("HTML", "nxs_td"),
				"rows"				=> "15",
				"placeholder" => nxs_l18n__("Enter your custom HTML here. Ensure the HTML is XHTML compliant", "nxs_td"),
				"localizablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_input_end",
				"type" 				=> "wrapperend"
			),
			// -------------------------------------------------------
			
		),
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

function nxs_widgets_htmlcustom_initplaceholderdata($args)
{
	extract($args);
	
	$args["htmlcustom"] = nxs_l18n__("Sample htmlcustom[nxs:default]", "nxs_td");
	$args['ph_margin_bottom'] = "0-0";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}