<?php

function nxs_pagetemplate_generic_layout_getoptions($args)
{	
	
	$postid = $args["clientpopupsessioncontext"]["postid"];
	$meta = nxs_get_corepostmeta($postid);
	$headerid = $meta["header_postid"];
	$subheaderid = $meta["subheader_postid"];
	$sidebarid = $meta["sidebar_postid"];
	$subfooterid = $meta["subfooter_postid"];
	$footerid = $meta["footer_postid"];
	
	$options = array
	(
		"sheettitle" => "Page layout",
		"fields" => array
		(
			array
			( 
				"id"								=> "header_postid",
				"type" 							=> "selectpost",
				"previewlink_enable"=> "false",
				"label" 						=> nxs_l18n__("Header", "nxs_td"),
				"tooltip" 					=> nxs_l18n__("Select a header to show on the top of your page", "nxs_td"),
				"postid"			=> $headerid,
				"post_type" 				=> "nxs_header",
				"buttontext" 				=> nxs_l18n__("Style header", "nxs_td"),
			),
			array
			( 
				"id"								=> "footer_postid",
				"type" 							=> "selectpost",
				"previewlink_enable"=> "false",
				"label" 						=> nxs_l18n__("Footer", "nxs_td"),
				"tooltip" 					=> nxs_l18n__("Select a header to show on the top of your page", "nxs_td"),
				"postid"			=> $footerid,
				"post_type" 				=> "nxs_footer",
			),
			array
			( 
				"id"								=> "sidebar_postid",
				"type" 							=> "selectpost",
				"previewlink_enable"=> "false",
				"label" 						=> nxs_l18n__("Sidebar", "nxs_td"),
				"tooltip" 					=> nxs_l18n__("Select a sidebar to show on the right side of your page", "nxs_td"),
				"postid"			=> $sidebarid,
				"post_type" 				=> "nxs_sidebar",
			),
			array
			( 
				"id"								=> "subheader_postid",
				"type" 							=> "selectpost",
				"previewlink_enable"=> "false",
				"label" 						=> nxs_l18n__("Sub header", "nxs_td"),
				"tooltip" 					=> nxs_l18n__("Select a sub header to show above your main content", "nxs_td"),
				"postid"			=> $subheaderid,
				"post_type" 				=> "nxs_subheader",
			),
			array
			( 
				"id"								=> "subfooter_postid",
				"type" 							=> "selectpost",
				"previewlink_enable"=> "false",
				"label" 						=> nxs_l18n__("Sub footer", "nxs_td"),
				"tooltip" 					=> nxs_l18n__("Select a sub footer to show below your main content", "nxs_td"),
				"postid"			=> $subfooterid,
				"post_type" 				=> "nxs_subfooter",
			),
			array
			( 
				"id"								=> "maincontent_visibility",
				"type" 							=> "select",
				"label" 						=> nxs_l18n__("Main content visibility", "nxs_td"),
				"tooltip" 					=> nxs_l18n__("Turn on / off the main content", "nxs_td"),
				"dropdown"					=> array
				(
					"" => "", 
					"hidden" => nxs_l18n__("Hidden", "nxs_td")
				),
				
			),
			/*
			array
			( 
				"id"								=> "pagedecorator_postid",
				"type" 							=> "selectpost",
				"previewlink_enable"=> "false",
				"label" 						=> nxs_l18n__("Decorator", "nxs_td"),
				"tooltip" 					=> nxs_l18n__("Select a decorator to decorate your page", "nxs_td"),
				"post_type" 				=> "nxs_genericlist",
			),
			*/
		),
	);
	
	return $options;
}

function nxs_pagetemplate_generic_styling_getoptions($args)
{
	$postid = $args["clientpopupsessioncontext"]["postid"];
	
	$templateproperties = nxs_gettemplateproperties();
	$headerid = $templateproperties["header_postid"];
	$subheaderid = $templateproperties["subheader_postid"];
	$sidebarid = $templateproperties["sidebar_postid"];
	$subfooterid = $templateproperties["subfooter_postid"];
	$footerid = $templateproperties["footer_postid"];
	
	$headertitle = nxs_gettitle_for_postid($headerid);
	$footertitle = nxs_gettitle_for_postid($footerid);
	
	//
	$result = array
	(
		"sheettitle" => "Page styling",
		"fields" => array
		(
		)
	);
	
	$result["fields"][] = array
	( 
		"id"				=> "ref_pagestyle",
		"type" 				=> "gotosheet",
		"label" 			=> nxs_l18n__("Content styling", "nxs_td"),
		"tooltip" 			=> nxs_l18n__("Content styling", "nxs_td"),
		"sheet" 			=> "sitepagestyling",
		"contextprocessor" 	=> "site",
		"buttontext"		=> nxs_l18n__("Change styling", "nxs_td"),
	);
	
	if ($headerid != 0)
	{
		$result["fields"][] = array
		( 
			"id"				=> "ref_bgstyle_header",
			"type" 				=> "gotosheet",
			"label" 			=> nxs_l18n__("Header styling", "nxs_td"),
			"tooltip" 			=> nxs_l18n__("Style this container", "nxs_td"),
			"sheet" 			=> "backgroundstyle",
			"contextprocessor" 	=> "rowscontainer",
			"postid"			=> $headerid,
			"buttontext"		=> nxs_l18n__("Change styling", "nxs_td") . " " . $headerid . " / " . $headertitle,
		);
	}
	
	if ($footerid != 0)
	{
		$result["fields"][] = array
		( 
			"id"				=> "ref_bgstyle_footer",
			"type" 				=> "gotosheet",
			"label" 			=> nxs_l18n__("Footer styling", "nxs_td"),
			"tooltip" 			=> nxs_l18n__("Style this container", "nxs_td"),
			"sheet" 			=> "backgroundstyle",
			"contextprocessor" 	=> "rowscontainer",
			"postid" 			=> $footerid,
			"buttontext"		=> nxs_l18n__("Change styling", "nxs_td") . " " . $footerid . " / " . $footertitle,
		);		
	}
	
	return $result;
}

?>
