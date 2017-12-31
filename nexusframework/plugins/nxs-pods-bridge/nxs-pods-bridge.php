<?php

// invoked directly after the theme is initialized and before anything else
add_action('after_setup_theme', 'nxs_pods_bridge_theme_loaded');
function nxs_pods_bridge_theme_loaded()
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