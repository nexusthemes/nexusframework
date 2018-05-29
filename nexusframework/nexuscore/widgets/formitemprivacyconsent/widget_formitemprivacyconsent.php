<?php

function nxs_widgets_formitemprivacyconsent_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-handshake";
}

function nxs_widgets_formitemprivacyconsent_gettitle()
{
	return nxs_l18n__("Privacy consent", "nxs_td");
}

function nxs_widgets_formitemprivacyconsent_getformitemsubmitresult($args)
{
	// error
	$result["validationerrors"][] = "Premium feature is not enabled";
	$result["markclientsideelements"][] = $key;

	$result["output"] = "<b>?:</b> $value";
	
	return $result;
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_formitemprivacyconsent_renderinformbox($args)
{
	extract($args);
	
	extract($metadata, EXTR_PREFIX_ALL, "metadata");
	
	$result = array();
	$result["result"] = "OK";
	
	nxs_requirewidget("formbox");
	$prefix = nxs_widgets_formbox_getclientsideprefix($postid, $placeholderid);
	
	//
	// render actual control / html
	//
	
	nxs_ob_start();

	?>
	<div class='nxs-hidewheneditorinactive' style='margin-bottom: 10px;'>
  	This feature requires the form privacy consent plugin
  </div>
	<?php
	// var_dump($args);
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

function nxs_widgets_formitemprivacyconsent_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	$mixedattributes = array_merge($temp_array, $args);
	
	$image_imageid = $mixedattributes['image_imageid'];
	$title = $mixedattributes['title'];
	$text = $mixedattributes['text'];
	$destination_articleid = $mixedattributes['destination_articleid'];
	
	$lookup = nxs_wp_get_attachment_image_src($image_imageid, 'full', true);
	
	$width = $lookup[1];
	$height = $lookup[2];		
	
	$lookup = nxs_wp_get_attachment_image_src($image_imageid, 'thumbnail', true);
	$url = $lookup[0];
	$url = nxs_img_getimageurlthemeversion($url);

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

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-formitemprivacyconsent-item";
	
	/* ADMIN OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	echo '
	<div class="nxs-dragrow-handler nxs-padding-menu-item">
		<div class="content2">
			<div class="box">
	        	<div class="box-title nxs-width40"><h4><span class="nxs-icon-handshake" style="font-size: 16px;" /> Privacy consent</h4></div>
				<div class="box-content  nxs-width60">'.$formlabel.'</div>
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
function nxs_widgets_formitemprivacyconsent_home_getoptions($args) 
{
	return nxs_popup_factory_createnotificationoptions("Form item privacy policy", "This widget requires the form privacy consent extension.");
}

function nxs_widgets_formitemprivacyconsent_initplaceholderdata($args)
{
	extract($args);

	$args["elementid"] = nxs_generaterandomstring(6);
	$args["popuptextifnoconsent"] = "We cannot process your form without your consent to the privacy policy";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_dataprotection_nexusframework_widget_formitemprivacyconsent_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("widget-defaultformitem");
}

?>