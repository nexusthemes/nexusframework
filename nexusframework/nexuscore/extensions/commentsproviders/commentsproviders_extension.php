<?php
//
// commentsproviders extensions
//
function nxs_ext_lazyload_commentsprovider($commentsprovider)
{
	add_action("nxs_ext_inject_commentsprovider_" . $commentsprovider, "nxs_ext_inject_commentsprovider");
}

function nxs_ext_inject_commentsprovider($commentsprovider)
{
	$filetobeincluded = NXS_FRAMEWORKPATH . '/nexuscore/commentsproviders/' . $commentsprovider . '/commentsprovider_' . $commentsprovider . '.php';
	require_once($filetobeincluded);
}

function nxs_requirecommentsprovider($commentsprovider)
{
	// loads commentsprovider extensions in memory
	$action = "nxs_ext_inject_commentsprovider_" . $commentsprovider;
	if (!has_action($action))
	{
		if (nxs_has_adminpermissions())
		{
			webmethod_return_nack("commentsprovider $commentsprovider not found (no action)");
		}
		else
		{
			echo "<!-- Warning; looks like commentsprovider '" . $commentsprovider . "' is missing (maybe you deactivated a required plugin?) -->";
		}
	}
	else
	{
		do_action($action, $commentsprovider);
	}
}

//
function nxs_getcommentsproviders_idtonames()
{
	$commentsproviders = nxs_getcommentsproviders();
	foreach ($commentsproviders as $currentcommentsprovider => $currentmeta)
	{
		nxs_requirecommentsprovider($currentcommentsprovider);
		$currenttitle = nxs_commentsprovider_gettitle($currentcommentsprovider);
		$result[$currentcommentsprovider] = $currenttitle;
	}
	return $result;
}

//
function nxs_getcommentsproviders()
{
	$result = array();
	$result = apply_filters("nxs_getcommentsproviders", $result, $args);
	return $result;
}

//
// enqueue available commentsproviders
//
add_action('nxs_getcommentsproviders', 'nxs_getcommentsproviders_functions_AF', 10, 2);
function nxs_getcommentsproviders_functions_AF($result, $args)
{
	$result["wordpressnative"] = array();
	return $result;
}

//
// lazy load commentsproviders
//
nxs_ext_lazyload_commentsprovider("wordpressnative");

function nxs_commentsprovider_getcurrent()
{
	$meta = nxs_getsitemeta();
	if (isset($meta["active_commentsprovider"]))
	{
		$result = $meta["active_commentsprovider"];
	}
	else
	{
		$result = "wordpressnative";
	}
	return $result;
}

function nxs_commentsprovider_setcurrent($commentsprovider)
{
	$modifiedmetadata = array();
	$modifiedmetadata["active_commentsprovider"] = $commentsprovider;
	nxs_mergesitemeta($modifiedmetadata);
}

function nxs_commentsprovider_getflyoutmenuhtml()
{
	$commentsprovider = nxs_commentsprovider_getcurrent();
	if (!isset($commentsprovider) || $commentsprovider == "")	{	return; }
	nxs_requirecommentsprovider($commentsprovider);
	
	$functionnametoinvoke = 'nxs_commentsprovider_' . $commentsprovider . '_getflyoutmenuhtml';
	if (!function_exists($functionnametoinvoke)) { nxs_webmethod_return_nack("functionnametoinvoke not found; " . $functionnametoinvoke); }
	
	$result = call_user_func($functionnametoinvoke);

	return $result;
}

function nxs_commentsprovider_getpostcommentcounthtml($postid)
{
	$commentsprovider = nxs_commentsprovider_getcurrent();
	if (!isset($commentsprovider) || $commentsprovider == "")	{	nxs_webmethod_return_nack("no active commentsprovider"); }
	nxs_requirecommentsprovider($commentsprovider);
	
	$functionnametoinvoke = 'nxs_commentsprovider_' . $commentsprovider . '_getpostcommentcounthtml';
	if (!function_exists($functionnametoinvoke)) { nxs_webmethod_return_nack("functionnametoinvoke not found; " . $functionnametoinvoke); }
	
	$p = array();
	$p["postid"] = $postid;
	$result = call_user_func($functionnametoinvoke, $p);

	return $result;
}

function nxs_commentsprovider_geticonid($commentsprovider)
{
	if (!isset($commentsprovider) || $commentsprovider == "")	{	nxs_webmethod_return_nack("commentsprovider is empty"); }
	nxs_requirecommentsprovider($commentsprovider);
	
	$functionnametoinvoke = 'nxs_commentsprovider_' . $commentsprovider . '_geticonid';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke);
	}
	else
	{
		nxs_webmethod_return_nack("functionnametoinvoke not found; " . $functionnametoinvoke);
	}
	
	return $result;
}

function nxs_commentsprovider_gettitle($commentsprovider)
{
	if (!isset($commentsprovider) || $commentsprovider == "")	{	nxs_webmethod_return_nack("commentsprovider is empty"); }
	nxs_requirecommentsprovider($commentsprovider);
	
	$functionnametoinvoke = 'nxs_commentsprovider_' . $commentsprovider . '_gettitle';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke);
	}
	else
	{
		nxs_webmethod_return_nack("functionnametoinvoke not found; " . $functionnametoinvoke);
	}
	
	return $result;
}

?>