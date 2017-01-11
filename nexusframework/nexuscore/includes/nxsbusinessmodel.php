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
		// for example the abstract "services" taxonomy
		// which holds attributes like the "title", and "accompaniment_title"
		// of that taxonomy
		"taxonomies" => array
		(
			"title" => "Taxonomies",
			"icon" => "tree",
			"instance" => array
			(
				"defaultrendertype" => "text",
			),
			"show_ui" => true,	// false = hide from users in the backend
			"singular" => "taxonomy",
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
		
		"socialaccounts" => array
		(
			"title" => "Social Accounts",
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
					"type" => "iconpicker",
				),
				"url" => array
				(
					"type" => "text",
				),
			),
			"singular" => "socialaccount",
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
			"label" => "Services",
		),		
	
		// todo: add "corestory" taxonomies
		// "brands" => array
		// "businesshours" => array
		// ----		
		"services" => array
		(
			"title" => "Services",
			"icon" => "publicrelations",
			"instance" => array
			(
				"defaultrendertype" => "text",
			),
			// fields displayed in the WP backend of this post
			"instanceextendedproperties" => array
			(
				"icon" => array
				(
					"type" => "iconpicker",
				),
			),
			"singular" => "service",
			"arity" => "n",	
			"features" => array
			(
				"orderedinstances" => array
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
			"label" => "Services",
		),
		"products" => array
		(
			"caninstancesbereferenced" => true,	// false if virtual
			"title" => "Products",
			"icon" => "gift",
			"instance" => array
			(
				"defaultrendertype" => "text",
			),
			"singular" => "product",		
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
		"portfolioitems" => array
		(
			"caninstancesbereferenced" => true,	// false if virtual
			"title" => "Portfolio items",
			"icon" => "eye",
			"instance" => array
			(
				"defaultrendertype" => "text",
			),
			"singular" => "portfolioitem",		
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
		"companyname" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Company name",
			"label" => "Company name",
			"icon" => "apartment",
			"singular" => "companyname",
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
					"label" => "Company name",
					"type" => "text",
				),
			),
		),
		"slogan" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Slogan",
			"icon" => "quote",
			"singular" => "slogan",
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
					"type" => "text",
				),
			),
			"label" => "Slogan",
		),
		"logo" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Logo",
			"label" => "Logo",
			"icon" => "logo",
			"singular" => "logo",
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
				
				/*
				"name" => array
				(
					"label" => "Logo",
					"type" => "text",
				),
				*/
			),
		),
		"phone" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Phone",
			"icon" => "phone",
			"singular" => "phone",
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
		"testimonials" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Testimonials",
			"icon" => "thumbs-up",		
			"singular" => "testimonial",		
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
					"type" => "text",
				),
			),
			//
			"instanceexistencecheckfield" => "source",
			"label" => "Testimonials",
		),
		"employees" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Employees",
			"icon" => "users",		
			"instance" => array
			(
				"defaultrendertype" => "bio",
			),
			"singular" => "employee",		
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
					"type" => "text",
				),			
			),			
			"instanceexistencecheckfield" => "name",
			"label" => "Employees",
		),
		"uniquesellingpropositions" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Unique Selling Propositions",
			"icon" => "brightness-medium",				
			"instance" => array
			(
				"defaultrendertype" => "target",
			),			
			"singular" => "usp",		
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
		"commercialmsgs" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Commercial Messages",
			"label" => "Commercial Messages",
			"icon" => "bubble",					
			"singular" => "commercialmsg",		
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
