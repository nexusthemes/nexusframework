<?php

// TEST URL:
// http://joeplumbermodeleditor.testgj.c1.us-e1.nexusthemes.com/api/1/prod/businessmodel/?nxs=businessmodel-api

$currenturi = nxs_geturicurrentpage();
$method = $_SERVER['REQUEST_METHOD'];
$pieces = explode("/", $currenturi);

if ($method == "GET")
{
	if ($pieces[4] == "businessmodel")
	{
		//
		$result = array();
		
		$nxs_siteid = $_REQUEST["nxs_siteid"];
		
		// grab the services based upon the structure of the "services" page in this site
		$servicespostmeta = get_page_by_path("/{$nxs_siteid}", ARRAY_A, 'page');
		$servicespostid = $servicespostmeta["ID"];
		// find "serviceset" widgets on this page
		$filter = array
		(
			"postid" => $servicespostid,
			"widgettype" => "service",
		);
		$widgetsmetadata = nxs_getwidgetsmetadatainpost_v2($filter);
		
		$items = nxs_getwidgetsmetadatainpost_v2($filter);
		foreach ($items as $placeholderid => $widgetmeta)
		{
			$result["services"]["instances"][] = array
			(
			
				"semantic" => $widgetmeta["semantic"],
				"flavor" => $widgetmeta["flavor"],
				"enabled" => $widgetmeta["enabled"],
			);
		}
		
		$result["businesstype"] = array
		(
			"id" => "plumber",
		);
		
		nxs_webmethod_return_ok($result);
	}
	// ---
	else
	{
		echo "API call; uri not supported; $currenturi";
	}
}
else
{
	echo "API call; method not supported; $method";
}