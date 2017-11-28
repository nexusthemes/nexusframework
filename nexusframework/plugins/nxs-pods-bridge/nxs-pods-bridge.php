<?php
add_action('plugins_loaded', 'nxs_pods_bridge_plugins_loaded');
function nxs_pods_bridge_plugins_loaded()
{
	// ignore if the pods plugin is not loaded
	if (!defined('PODS_VERSION'))
	{
		return;
	}
	if (function_exists("nxs_getpods"))
	{
		return;
	}
	
	require_once("nxs-pods-bridge-actual.php");
}
?>