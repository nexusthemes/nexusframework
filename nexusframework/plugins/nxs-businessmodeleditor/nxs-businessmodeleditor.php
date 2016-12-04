<?php
/*
Plugin Name: Nexus Business Model Editor
Version: 1.0.0
Plugin URI: http://nexusthemes.com
Description: Helper
Author: Gert-Jan Bark
Author URI: http://nexusthemes.com
*/

function nxs_businessmodeleditor_init()
{
	if (!defined('NXS_FRAMEWORKLOADED'))
	{
		function nxs_businessmodeleditor_frameworkmissing() {
	    ?>
	    <div class="error">
	      <p>The nxs_businessmodeleditor plugin is not initialized; NexusFramework dependency is missing (hint: activate a WordPress theme from NexusThemes.com first)</p>
	    </div>
	    <?php
		}
		add_action( 'admin_notices', 'nxs_businessmodeleditor_frameworkmissing' );
		return;
	}
  
	// widgets
	nxs_lazyload_plugin_widget(__FILE__, "serviceset");
	nxs_lazyload_plugin_widget(__FILE__, "service");
	
	// if this is an API call, delegate it
	if 
	(
		$_REQUEST["nxs"] == "businessmodel-api" || 
		false
	)
	{
		require_once("nxs-api-dispatcher.php");
		echo "<br />Nexus API Dispatcher Error #87432";
		// if we reach this stage, the api didn't die
		die();
	}
}
add_action("init", "nxs_businessmodeleditor_init");

function nxs_businessmodeleditor_getwidgets($result, $widgetargs)
{
	$nxsposttype = $widgetargs["nxsposttype"];
	$pagetemplate = $widgetargs["pagetemplate"];
	
	if 
	(
		$nxsposttype == "post" || 
		false
	)
	{
		$result[] = array("widgetid" => "serviceset", "tags" => array("businessmodeleditor"));
	}

	/* GENERIC LISTS POSTTYPE
	---------------------------------------------------------------------------------------------------- */
	
	if ($nxsposttype == "genericlist") 
	{
		$nxssubposttype = $widgetargs["nxssubposttype"];
		
		// bijv. service_set
		
		$taxonomiesmeta = nxs_business_gettaxonomiesmeta();
		foreach ($taxonomiesmeta as $taxonomy => $taxonomymeta)
		{
		 	if ($taxonomymeta["arity"] == "n")
		 	{
		 		$singular = $taxonomymeta["singular"];
		 		
		 		if ($nxssubposttype == "{$singular}_set") 
		 		{
					$result[] = array("widgetid" => "service", "tags" => array("businessmodeleditor"));
		 		}
		 	}
		}
	}
	
	

	//		
	return $result;
}
add_action("nxs_getwidgets", "nxs_businessmodeleditor_getwidgets", 10, 2);	// default prio 10, 2 parameters (result, args)

// -------

?>