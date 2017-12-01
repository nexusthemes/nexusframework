<?php

function nxs_widgets_formitemhtml_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-htmlcustom";
}

function nxs_widgets_formitemhtml_gettitle()
{
	return nxs_l18n__("Html", "nxs_td");
}

function nxs_widgets_formitemhtml_getformitemsubmitresult($args)
{
	// server side validation	
	extract($args);
	
	$result = array();
	$result["result"] = "OK";
	$result["validationerrors"] = array();
	$result["markclientsideelements"] = array();
	
	return $result;
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_formitemhtml_renderincontactbox($args)
{
	extract($args);
	
	// extract($metadata, EXTR_PREFIX_ALL, "metadata");
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($metadata, array("htmlcustom"));
	
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
		$magicfields = array("htmlcustom");
		$translateargs = array
		(
			"lookup" => $combined_lookups,
			"items" => $mixedattributes,
			"fields" => $magicfields,
		);
		$mixedattributes = nxs_filter_translate_v2($translateargs);
	}
	
	//
	
	$htmlcustom = "<div class='nxs-text nxs-default-p nxs-applylinkvarcolor nxs-padding-bottom0'>{$mixedattributes['htmlcustom']}</div>";
  $htmlcustom = do_shortcode($htmlcustom);
  //
  
	$result = array();
	$result["result"] = "OK";
	$result["html"] = $htmlcustom;
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

// render in the editor of the formbox
function nxs_widgets_formitemhtml_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	$mixedattributes = array_merge($temp_array, $args);
	
	//

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
		$hovermenuargs["enable_deletewidget"] = false;
		$hovermenuargs["enable_deleterow"] = true;
		$hovermenuargs["metadata"] = $mixedattributes;
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);	
	}
	
	/* ADMIN EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-formitemhtml-item";
	
	/* ADMIN OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	echo '
	<div class="nxs-dragrow-handler nxs-padding-menu-item">
		<div class="content2">
			<div class="box">
	      <div class="box-title">
	      	<span class="nxs-icon-htmlcustom" style="font-size: 16px;">Html</span>
				</div>
				<div class="box-content"></div>
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
function nxs_widgets_formitemhtml_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_formitemhtml_gettitle(),
		"sheeticonid" => nxs_widgets_formitemhtml_geticonid(),
	
		"fields" => array
		(
			array
			( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Lookups", "nxs_td"),
				"initial_toggle_state" => "closed",
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
		)
	);
	
	return $options;
}

function nxs_widgets_formitemhtml_initplaceholderdata($args)
{
	extract($args);

	$args["htmlcustom"] = "<div>Put your custom HTML here</div>";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>