<?php

function nxs_popup_contextprocessor_gallerybox_getcustompopuphtml($args)
{
	if (!isset($args["clientpopupsessioncontext"]))	{	nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }

	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	extract($clientpopupsessioncontext);
	
	if ($galleryid == "") { nxs_webmethod_return_nack("galleryid not set"); }
 	if ($imageid == "") { nxs_webmethod_return_nack("imageid not set"); }
 	if ($index == "") { nxs_webmethod_return_nack("index not set"); }
 	if ($sheet == "") { nxs_webmethod_return_nack("sheet not set"); }
 	
 	require_once(NXS_FRAMEWORKPATH . '/nexuscore/gallerybox/gallerybox.php');
	
	$functionnametoinvoke = 'nxs_gallerybox_' . $sheet . '_rendersheet';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		if (nxs_genericpopup_exists($sheet))
		{
			return nxs_popup_rendergenericpopup($sheet, $args);	
		}
		else
		{
			nxs_webmethod_return_nack("gallerybox context; unable to render sheet $sheet; function $functionnametoinvoke not found and no genericpopup found");
		}
	}
	
	return $result;
}

function nxs_popup_contextprocessor_gallerybox_supportsoptions($args)
{
	return false;
}

function nxs_popup_contextprocessor_gallerybox_getoptions($args)
{
	nxs_webmethod_return_nack("not supported");
}

//
// a popup processor is responsible for mapping specific args
// for this specific popup type. It "knows" how to map
// fields that are to be retrieved and persisted
//
function nxs_popup_contextprocessor_gallerybox_getpersisteddata($args)
{
	nxs_webmethod_return_nack("not supported");
}

// _internal means the globalids (extended data) is already enriched!
function nxs_popup_contextprocessor_gallerybox_mergedata_internal($args, $metadata)
{
	nxs_webmethod_return_nack("not supported");
}

// output the nxs_js_savegenericpopup function
function nxs_popup_contextprocessor_gallerybox_render_nxs_js_savegenericpopup($args)
{
	nxs_webmethod_return_nack("not supported");
}

?>