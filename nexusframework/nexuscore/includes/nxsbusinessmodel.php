<?php

function nxs_business_gettaxonomiesmeta()
{
	// arity explanation;
	// * n means 0,1 or more
	// * 1 means exactly 1
	
	$result = array
	(
		// the entities representing the taxonomies themselves
		// (the singleton instance of each abstract taxonomy),
		// for example the abstract "nxs_service" taxonomy
		// which holds attributes like the "title", and "accompaniment_title"
		// of that taxonomy
		"nxs_taxonomy" => array
		(
			"title" => "Taxonomies",
			"icon" => "tree",
			"instance" => array
			(
				"defaultrendertype" => "text",
			),
			"show_ui" => true,	// false = hide from users in the backend
			"arity" => "n",
			"features" => array
			(
				"orderedinstances" => array
				(
					"enabled" => false,
				),
			),
			"caninstancesbereferenced" => false,	// false if virtual
			"aritymaxinstancecount" => 999,
			"instanceexistencecheckfield" => "title",
			"taxonomyfields" => array
			(
				//
			),
			"wpcreateinstructions" => array
			(
				"instances" => array
				(
					"type" => "page",
				),
			),
			"instancefields" => array
			(
				// determined @runtime			
			),
			"label" => "Taxonomies",
		),

		"nxs_companyname" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Company name",
			"label" => "Company name",
			"icon" => "apartment",
			"arity" => "1",	
			"features" => array
			(
			),			
			"aritymaxinstancecount" => 1,
			"wpcreateinstructions" => array
			(
			),
			// fields displayed in the WP backend of this post
			"instanceextendedproperties" => array
			(
			),
			"taxonomyextendedproperties" => array
			(
				"name" => array
				(
					"persisttype" => "wp_title",
					"label" => "Company name",
					"type" => "text",
				),
			),
		),
		"nxs_slogan" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Slogan",
			"icon" => "quote",
			"arity" => "1",	
			"features" => array
			(
			),			
			"aritymaxinstancecount" => 1,
			"wpcreateinstructions" => array
			(
			),
			// fields displayed in the WP backend of this post
			"instanceextendedproperties" => array
			(
			),
			"taxonomyextendedproperties" => array
			(
				"slogan" => array
				(
					"persisttype" => "wp_title",
					"type" => "text",
				),
			),
			"label" => "Slogan",
		),
		/*
		"nxs_logo" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Logo",
			"label" => "Logo",
			"icon" => "logo",
			"arity" => "1",	
			"aritymaxinstancecount" => 1,
			"features" => array
			(
			),
			"wpcreateinstructions" => array
			(
			),
			// fields displayed in the WP backend of this post
			"instanceextendedproperties" => array
			(
			),
			"taxonomyextendedproperties" => array
			(
				// uses the feature image
				
				//"name" => array
				//(
				//	"label" => "Logo",
				//	"type" => "text",
				//),
			),
		),
		*/
		"nxs_phone" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Phone",
			"icon" => "phone",
			"arity" => "1",	
			"features" => array
			(
			),
			"aritymaxinstancecount" => 1,
			"taxonomyfields" => array
			(
			),
			"wpcreateinstructions" => array
			(
			),
			"instancefields" => array
			(
			),
			// fields displayed in the WP backend of this post
			"instanceextendedproperties" => array
			(
			),
			"taxonomyextendedproperties" => array
			(
				"phonenumber" => array
				(
					"type" => "text",
				),
			),
			"label" => "Phone",
		),
				
		"nxs_socialaccount" => array
		(
			"title" => "Social Accounts",
			"label" => "Social Accounts",
			"icon" => "share",
			"instance" => array
			(
				"defaultrendertype" => "text",
			),
			// fields displayed in the WP backend of this post
			"instanceextendedproperties" => array
			(
				"icon" => array
				(
					"persisttype" => "wp_meta",
					"type" => "iconpicker",
				),
				"url" => array
				(
					"persisttype" => "wp_meta",
					"type" => "text",
				),
			),
			"arity" => "n",	
			"features" => array
			(
				"orderedinstances" => array
				(
					"enabled" => true,
				),
			),
			"caninstancesbereferenced" => false,	// false if virtual
			"aritymaxinstancecount" => 8,
			"instanceexistencecheckfield" => "title",
			"taxonomyfields" => array
			(
				"title" => array
				(
					"type" => "text",
				),
				"accompaniment_title" => array
				(
					"type" => "text",
				),
			),
			"wpcreateinstructions" => array
			(

			),
			"instancefields" => array
			(
				"title" => array
				(
					"type" => "text",
				),
				"icon" => array
				(
					"type" => "icon",
				),
				"excerpt" => array
				(
					"type" => "textarea",
				),
				"genericimage" => array
				(
					"type" => "media",
					"scope" => "businesstype",
				),				
				"image" => array
				(
					"type" => "media",
					"scope" => "searchphrase",
				),
				/*
				"pricingtable" => array
				(
					"type" => "textarea",
				),
				*/
				"problem" => array
				(
					"type" => "text",
				),
				"solution" => array
				(
					"type" => "text",
				),
				"imageset" => array
				(
					"type" => "mediaset",
					"scope" => "businesstype",
				),				
			),
		),		
	
		// todo: add "corestory" taxonomies
		// "brands" => array
		// "businesshours" => array
		// ----		
		"nxs_service" => array
		(
			"title" => "Services",
			"label" => "Services",
			"icon" => "publicrelations",
			"instance" => array
			(
				"defaultrendertype" => "text",
			),
			// fields displayed in the WP backend of this post
			"instanceextendedproperties" => array
			(
				"title" => array
				(
					"persisttype" => "wp_title",
					"type" => "text",
					"edittype" => "text",	// "html"
				),
				"icon" => array
				(
					"persisttype" => "wp_meta",
					"type" => "iconpicker",
				),
				"excerpt" => array
				(
					"persisttype" => "wp_excerpt",
					"type" => "text",
					"edittype" => "html",	// "html"
				),
				"content" => array
				(
					"persisttype" => "wp_content",
					"type" => "text",
					"edittype" => "html",	// "html"
				),
				"image" => array
				(
					"persisttype" => "wp_featimg",
					"type" => "img",
					"edittype" => "image",
				),
				"media" => array
				(
					"persisttype" => "wp_meta",
				),
			),
			"arity" => "n",	
			"features" => array
			(
				"orderedinstances" => array
				(
					"enabled" => true,
				),
				"contentattribution" => array
				(
					"enabled" => true,
				),
			),
			"caninstancesbereferenced" => true,	// false if virtual
			"aritymaxinstancecount" => 8,
			"wpcreateinstructions" => array
			(
				"instances" => array
				(
					"type" => "page",
				),
			),
		),
		"nxs_product" => array
		(
			"caninstancesbereferenced" => true,	// false if virtual
			"title" => "Products",
			"icon" => "gift",
			"instance" => array
			(
				"defaultrendertype" => "text",
			),
			"arity" => "n",	
			"aritymaxinstancecount" => 8,
			"instanceexistencecheckfield" => "title",
			"wpcreateinstructions" => array
			(
				"instances" => array
				(
					"type" => "page",
				),
			),
			"features" => array
			(
				"orderedinstances" => array
				(
					"enabled" => true,
				),
			),
			"label" => "Products",
		),
		"nxs_portfolioitem" => array
		(
			"caninstancesbereferenced" => true,	// false if virtual
			"title" => "Portfolio items",
			"icon" => "eye",
			"instance" => array
			(
				"defaultrendertype" => "text",
			),
			"arity" => "n",	
			"features" => array
			(
				"orderedinstances" => array
				(
					"enabled" => true,
				),
			),
			"aritymaxinstancecount" => 8,
			"instanceexistencecheckfield" => "title",
			"wpcreateinstructions" => array
			(
				"instances" => array
				(
					"type" => "page",
				),
			),
			"label" => "Portfolio Items",
		),

		"nxs_testimonial" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Testimonials",
			"icon" => "thumbs-up",		
			"instance" => array
			(
				"defaultrendertype" => "quote",
			),			
			"arity" => "n",
			"features" => array
			(
				"orderedinstances" => array
				(
					"enabled" => true,
				),
			),			
			"aritymaxinstancecount" => 5,
			"wpcreateinstructions" => array
			(
				"taxonomy" => array
				(
					"type" => "page",
				),
			),
			// fields displayed in the WP backend of this post
			"instanceextendedproperties" => array
			(
				"stars" => array
				(
					"persisttype" => "wp_meta",
					"type" => "text",
				),
			),
			//
			"instanceexistencecheckfield" => "source",
			"label" => "Testimonials",
		),
		"nxs_employee" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Employees",
			"icon" => "users",		
			"instance" => array
			(
				"defaultrendertype" => "bio",
			),
			"arity" => "n",
			"features" => array
			(
				"orderedinstances" => array
				(
					"enabled" => true,
				),
			),			
			"aritymaxinstancecount" => 8,
			"wpcreateinstructions" => array
			(
				"taxonomy" => array
				(
					"type" => "page",
				),
			),
			// fields displayed in the WP backend of this post
			"instanceextendedproperties" => array
			(
				"role" => array
				(
					"persisttype" => "wp_meta",
					"type" => "text",
				),			
			),			
			"instanceexistencecheckfield" => "name",
			"label" => "Employees",
		),
		"nxs_usp" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Unique Selling Propositions",
			"icon" => "brightness-medium",				
			"instance" => array
			(
				"defaultrendertype" => "target",
			),			
			"arity" => "n",	
			"features" => array
			(
				"orderedinstances" => array
				(
					"enabled" => true,
				),
			),			
			"aritymaxinstancecount" => 8,
			"instanceexistencecheckfield" => "title",
			// fields displayed in the WP backend of this post
			"instanceextendedproperties" => array
			(
			),			
			"label" => "Unique Selling Propositions",
			"wpcreateinstructions" => array
			(
			),
		),
		//
		"nxs_commercialmsg" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Commercial Messages",
			"label" => "Commercial Messages",
			"icon" => "bubble",					
			"arity" => "n",	
			"features" => array
			(
				"orderedinstances" => array
				(
					"enabled" => true,
				),
			),
			"aritymaxinstancecount" => 8,
			"instanceexistencecheckfield" => "title",
			"instanceextendedproperties" => array
			(
				"title" => array
				(
					"persisttype" => "wp_title",
					"type" => "text",
				),
				"excerpt" => array
				(
					"persisttype" => "wp_excerpt",
					"type" => "text",
				),
			),			
			"wpcreateinstructions" => array
			(
			),
		),
	);
	
	// allow plugins to extend the result
	$result = apply_filters('nxs_f_business_gettaxonomiesmeta', $result);
		
	return $result;
}
