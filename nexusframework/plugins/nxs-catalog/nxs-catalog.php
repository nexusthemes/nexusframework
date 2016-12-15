<?php
/*
Plugin Name: Nxs Catalog
Version: 1.0.12
Plugin URI: https://github.com/TODO
Description: TODO
Author: GJ
Author URI: https://github.com/TODO/
*/

class nxs_catalog
{
	function instance_init()
	{
		// 
		nxs_lazyload_plugin_widget(__FILE__, "catalogitems");
		nxs_lazyload_plugin_widget(__FILE__, "bustypes");
	}
	
	function getwidgets($result, $widgetargs)
	{
		$nxsposttype = $widgetargs["nxsposttype"];
		if ($nxsposttype == "post") 
		{
			$result[] = array
			(
				"widgetid" => "catalogitems",
				"tags" => array("catalog"),
			);
		}
		
		
		$result[] = array
		(
			"widgetid" => "bustypes",
			"tags" => array("catalog")
		);
		
		return $result;
	}
	
	function __construct()
  {
  	add_filter( 'init', array($this, "instance_init"), 5, 1);
		add_action( 'nxs_getwidgets',array( $this, "getwidgets"), 20, 2);
  }
  
	/* ---------- */
}

global $nxs_catalog_instance;
$nxs_catalog_instance = new nxs_catalog();