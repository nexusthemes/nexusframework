<?php

function nxs_business_gettaxonomiesmeta()
{
	// arity explanation;
	// * n means 0,1 or more
	// * 1 means exactly 1
	
	$result = array
	(
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
			"singular" => "service",
			"arity" => "n",	
			"caninstancesbereferenced" => true,	// false if virtual
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
				"instances" => array
				(
					"type" => "page",
				),
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
				//
				//"options" =>  array
				//(
				//	"type" => "text",
				//	"comment" => "p.e. googlemap|some other value|...<br />use in combination with:<br />etchrowcondition=notcontains:products:options:googlemap",
				//),
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
				"title" => array
				(
					"type" => "text",
				),
				"icon" => array
				(
					"type" => "icon",
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
				"imageset" => array
				(
					"type" => "mediaset",
					"scope" => "businesstype",
				),
				"specifications" => array
				(
					"type" => "textarea",
					"comment" => "Insert like a CSV<br />Description,Value<br />Color,Blue",
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
				/*
				"options" =>  array
				(
					"type" => "text",
					"comment" => "p.e. googlemap|some other value|...<br />use in combination with:<br />etchrowcondition=notcontains:products:options:googlemap",
				),
				*/
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
				"title" => array
				(
					"type" => "text",
				),
				"icon" => array
				(
					"type" => "icon",
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
				"imageset" => array
				(
					"type" => "mediaset",
					"scope" => "businesstype",
				),
				"specifications" => array
				(
					"type" => "textarea",
					"comment" => "Insert like a CSV<br />Description,Value<br />Color,Blue",
				),
			),
			"label" => "Portfolio Items",
		),
		/*		
		"calltoactions" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Call to actions",
			"icon" => "callout",
			"singular" => "calltoaction",		
			"arity" => "n",	
			"aritymaxinstancecount" => 3,
			"filters" => array
			(
				"hatchwidget_widgetmeta_filters" => array
				(
					"nxs_stanser_cta_getwidgetmeta",
				),
			),
			"taxonomyfields" => array
			(

			),
			"wpcreateinstructions" => array
			(
			),
			"instanceexistencecheckfield" => "imperative_s",
			"instancefields" => array
			(
				"destination" => array	// # 73456873456834276
				(
					"type" => "ctadestination",
				),
				"imperative_s" => array
				(
					"type" => "text",
				),
				"imperative_m" => array
				(
					"type" => "text",
				),
				"imperative_l" => array
				(
					"type" => "text",
				),				
				"icon" => array
				(
					"type" => "icon",
				),
				"image" => array
				(
					"type" => "media",
					"scope" => "businesstype",
				),
				"description" => array
				(
					"type" => "textarea",
				)
			),
			"label" => "Call to actions",
		),	
		*/
		/*
		// Prices / Fees
		"prices" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Prices",
			"icon" => "dollar",
			"singular" => "pricingtable",		
			"arity" => "n",	
			"aritymaxinstancecount" => 8,
			"label" => "Prices",
			"taxonomyfields" => array
			(
				"title" => array
				(
					"type" => "text",
				),
			),
			"wpcreateinstructions" => array
			(
				"taxonomy" => array
				(
					"type" => "page",
				),
			),
			"instanceexistencecheckfield" => "title",
			"instancefields" => array
			(
				"title" => array
				(
					"type" => "text",
				),
				"pricingtable" => array
				(
					"type" => "textarea",
				),
			),
		),
		*/
		/*
		"forms" => array
		(
			"caninstancesbereferenced" => true,	// false if virtual
			"title" => "Forms",
			"icon" => "contact",		
			"singular" => "form",		
			"arity" => "n",
			"aritymaxinstancecount" => 2,
			"taxonomyfields" => array
			(
				"title" => array
				(
					"type" => "text",
				),
			),
			"wpcreateinstructions" => array
			(
				"instances" => array
				(
					"type" => "page",
				),
			),
			"instanceexistencecheckfield" => "title",
			"instancefields" => array
			(
				"title" => array
				(
					"type" => "text",
				),
				"form" => array
				(
					"type" => "textarea",
				),
			),
			"label" => "Forms",
		),
		*/
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
			"aritymaxinstancecount" => 5,
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
			
			"instancefields" => array
			(
				"source" => array
				(
					"type" => "text",
				),
				"text" => array
				(
					"type" => "textarea",
				),
			),
			"label" => "Testimonials",
		),
		/*
		"resources" => array
		(
			"caninstancesbereferenced" => true,	// false if virtual
			"title" => "Resources",
			"icon" => "usb1",
			"singular" => "resource",		
			"arity" => "n",
			"aritymaxinstancecount" => 5,
			"taxonomyfields" => array
			(
				"title" => array
				(
					"type" => "text",
				),
			),
			"wpcreateinstructions" => array
			(
				
				"instances" => array
				(
					"type" => "post",
				),
				"taxonomy" => array
				(
					"type" => "page",
				),
				
			),
			"instanceexistencecheckfield" => "title",
			"instancefields" => array
			(
				"title" => array
				(
					"type" => "hidden",
				),
				"image" => array
				(
					"type" => "media",
				),
			),
			"label" => "Resources / Tips",
		),
		*/
		/*
		"trusticons" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "Trust icons",
			"icon" => "shield",
			"singular" => "trusticon",		
			"arity" => "n",
			"aritymaxinstancecount" => 5,
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
			"wpcreateinstructions" => array(),
			"instanceexistencecheckfield" => "image",
			"instancefields" => array
			(
				"image" => array
				(
					"type" => "media",
				),
			),
			"label" => "Trust icon",
		),
		*/
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
			"aritymaxinstancecount" => 8,
			"taxonomyfields" => array
			(
				"title" => array
				(
					"type" => "text",
				),
			),			
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
			"instancefields" => array
			(
				"name" => array
				(
					"type" => "text",
				),
				"role" => array
				(
					"type" => "text",
				),
				"image" => array
				(
					"type" => "media",
				),
			),
			"label" => "Employees",
		),
		/*
		"faq" => array
		(
			"caninstancesbereferenced" => false,	// false if virtual
			"title" => "FAQ",
			"icon" => "question",		
			"singular" => "faq",		
			"label" => "FAQ",
			"arity" => "n",
			"aritymaxinstancecount" => 10,
			"taxonomyfields" => array
			(
				"title" => array
				(
					"type" => "text",
				),
			),	
			"wpcreateinstructions" => array
			(
				"taxonomy" => array
				(
					"type" => "page",
				),
			),
			"instanceexistencecheckfield" => "title",			
			"instancefields" => array
			(
				"title" => array
				(
					"type" => "text",
				),
				"text" => array
				(
					"type" => "hidden",
				),
			),
		),
		*/
		/*
		"vacancies" => array
		(
			"caninstancesbereferenced" => true,	// false if virtual
			"title" => "Vacancies",
			"icon" => "resume",		
			"singular" => "vacancy",		
			"label" => "Vacancies",
			"arity" => "n",
			"aritymaxinstancecount" => 5,
			"taxonomyfields" => array
			(
				"title" => array
				(
					"type" => "text",
				),
				"text" => array
				(
					"type" => "text",
				),
			),	
			"instanceexistencecheckfield" => "title",			
			"wpcreateinstructions" => array
			(
				"instances" => array
				(
					"type" => "post",
				),
				"taxonomy" => array
				(
					"type" => "page",
				),
			),
			"instancefields" => array
			(
				"title" => array
				(
					"type" => "text",
				),
			),
		),
		*/
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
				"description" => array
				(
					"type" => "textarea",
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
				"description" => array
				(
					"type" => "textarea",					
				),
			),
			"label" => "Unique Selling Propositions",
		),	
	);
	
	// allow plugins to extend the result
	$result = apply_filters('nxs_f_business_gettaxonomiesmeta', $result);
		
	return $result;
}
