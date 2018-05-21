<?php

function nxs_widgets_carousel_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-carousel";
}

// Setting the widget title
function nxs_widgets_carousel_gettitle() {
	return nxs_l18n__("Carousel", "nxs_td");
}

// Unistyle
function nxs_widgets_carousel_getunifiedstylinggroup() {
	return "carousel";
}

// Unicontent
function nxs_widgets_carousel_getunifiedcontentgroup() {
	return "carousel";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_carousel_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_carousel_gettitle(),
		"sheeticonid" 		=> nxs_widgets_carousel_geticonid(),
		"unifiedstyling" 	=> array("group" => nxs_widgets_carousel_getunifiedstylinggroup(),),
		"fields" => array
		(
			
			/* TITLE
			---------------------------------------------------------------------------------------------------- */
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"initial_toggle_state"	=> "closed-if-empty",
				"initial_toggle_state_id" => "title",
			),
			
			array(
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Title goes here", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title heading", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Override title fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
			// LOGO			
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Logo properties", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array
			( 
				"id"				=> "image_imageid",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Choose logo image", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to upload an image for your logo use this option.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "image_alt",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Image alt text", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("imagealtplaceholder", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true,
				"requirecapability" => nxs_cap_getdesigncapability(),
			),
			array(
				"id" 				=> "halign",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Logo alignment", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Align your logo to the left, center or right from the placeholder.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "max_height_percentage",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Max height percentage", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@empty@@@"	=>nxs_l18n__("100%", "nxs_td"),
					"90%"			=>nxs_l18n__("90%", "nxs_td"),
					"80%"			=>nxs_l18n__("80%", "nxs_td"),
					"70%"			=>nxs_l18n__("70%", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
			),
			
			/* ITEMS
			---------------------------------------------------------------------------------------------------- */
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Carousel items", "nxs_td"),
			),
			
			array(
				"id" 				=> "items_genericlistid",
				"type" 				=> "staticgenericlist_link",
				"label" 			=> nxs_l18n__("Carousel items", "nxs_td"),
			),
			array(
				"id" 				=> "image_size",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image size", "nxs_td"),
				"dropdown" 			=> array
				(
					"stretch" 	=> nxs_l18n__("stretch", "nxs_td"),
					"original" 	=> nxs_l18n__("original", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "image_filter",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image filter", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@empty@@@"	=>nxs_l18n__("None", "nxs_td"),
					"grayscale"		=>nxs_l18n__("Grayscale", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			
			array
			( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
			/* OVERLAY
			---------------------------------------------------------------------------------------------------- */
			
			array
			( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Overlay", "nxs_td"),
			),
			
			array( 
				"id" 				=> "object1_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Object1 color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "object2_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Object2 color", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
		)
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}


/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */
function nxs_widgets_carousel_randomImage_2 ( $array ) {
	$total = count($array);
	$call = rand(0,$total-1);
	return $array[$call];
}
	
function nxs_widgets_carousel_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// Unistyle
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_carousel_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);

	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		//	
		$hovermenuargs = array();
		$hovermenuargs["postid"] = $postid;
		$hovermenuargs["placeholderid"] = $placeholderid;
		$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
		$hovermenuargs["metadata"] = $mixedattributes;
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	}
	
	// Turn on output buffering
	nxs_ob_start();
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));
		
	global $nxs_global_placeholder_render_statebag;
	if ($shouldrenderalternative == true) {
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . "-warning ";
	} else {
		// Appending custom widget class
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " ";
	}
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	// Color
	$object1_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $object1_color);
	$object2_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $object2_color);
	
	/* IMAGE
	---------------------------------------------------------------------------------------------------- */

	// Image
	if ($image_imageid != "") {     
		// Core WP function returns ID ($logo_id), size of image (thumbnail, medium, large or full)
		// This is a generic function to return a variable which contains the image chosen from the media manager
		$imagemetadata= nxs_wp_get_attachment_image_src($image_imageid, 'full', true);
		
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$imageurl 		= $imagemetadata[0];
		$imageurl = nxs_img_getimageurlthemeversion($imageurl);

		$imagewidth 	= $imagemetadata[1] . "px";
		$imageheight 	= $imagemetadata[2] . "px";	
	}
	
	// Max height
	if ($max_height_percentage == "") 			{ $max_height_percentage = "nxs-max-height-100"; } else
	if ($max_height_percentage == "90%") 		{ $max_height_percentage = "nxs-max-height-90"; } else
	if ($max_height_percentage == "80%") 		{ $max_height_percentage = "nxs-max-height-80"; } else
	if ($max_height_percentage == "70%") 		{ $max_height_percentage = "nxs-max-height-70"; }
	
	if ($image_filter == "grayscale") { $image_filter = "nxs-grayscale"; }
	
	/* CAROUSEL
	---------------------------------------------------------------------------------------------------- */
	
	$carousel = array ();
	
	$structure = nxs_parsepoststructure($items_genericlistid);
	if (count($structure) == 0) {
		$alternativemessage = nxs_l18n__("Warning:no items found", "nxs_td");
	}
	else
	{
		$slideindex = 0;
		foreach ($structure as $pagerow) {
			$content = $pagerow["content"];
			$currentplaceholderid = nxs_parsepagerow($content);
			$placeholdermetadata = nxs_getwidgetmetadata($items_genericlistid, $currentplaceholderid);
			$placeholdertype = $placeholdermetadata["type"];					
			
			if ($placeholdertype == "") {
				// ignore
			} else if ($placeholdertype == "undefined") {
				// ignore
			} 
			else if ($placeholdertype == "carouselitem") 
			{
				$image_imageid = $placeholdermetadata['image_imageid'];
				$lookup = nxs_wp_get_attachment_image_src($image_imageid, 'full', true);
				
				$carousel_imageurl 		= $lookup[0];
				$carousel_imageurl = nxs_img_getimageurlthemeversion($carousel_imageurl);
				
				$carousel_imagewidth 	= $lookup[1]. "px";
				$carousel_imageheight 	= $lookup[2]. "px";		
				
				$destination_articleid = $placeholdermetadata['destination_articleid'];
				$destination_url = $placeholdermetadata['destination_url'];
				
				if ($destination_articleid != 0 && $destination_articleid != "") {
					$destinationurl = nxs_geturl_for_postid($destination_articleid);
				} else if ($destination_url != "") {
					$destinationurl = $destination_url;
				} else {
					$destinationurl = "";
				}
				
				// add image to html
				$image = '<img src="'.$carousel_imageurl.'" class="'.$image_filter.'"/>';
				
				// add item to carousel array
				if ($destinationurl != "") {
					// wrap link
					$target = "_blank";
					$image = '<a href="'.$destinationurl.'" target="'.$target.'">'.$image.'</a>';
				}
				
				$carousel[] = $image;				
			
			} else {
				// ignore
			}
		}
	}
	
	// Image size
	if ($image_size == "original" || $image_size == "") {
		$image_size = "nxs-logo";
	} else {
		$image_size = "nxs-stretch";
	}
	
	/* TITLE
	---------------------------------------------------------------------------------------------------- */
	
	// Title heading
	if ($title_heading != "") {
		$title_heading = "h" . $title_heading;	
	} else {
		$title_heading = "h1";
	}

	// Title alignment
	$title_alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $title_alignment);
	
	// Title fontsize
	$title_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $title_fontsize);

	// Title height (across titles in the same row)
	$heightiqprio = "p1";
	$title_heightiqgroup = "title";
  	$titlecssclasses = $title_fontsize_cssclass;
	$titlecssclasses = nxs_concatenateargswithspaces($titlecssclasses, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$title_heightiqgroup}");
	
	// Title
	$htmltitle = '<'.$title_heading.' class="nxs-title '.$title_alignment_cssclass.' '.$title_fontsize_cssclass.' '.$titlecssclasses.'">'.$title.'</'.$title_heading.'>';
	
		
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) {
		if ($alternativehint == "") {
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} else {
		
		if ( $title != "" ) { 
			echo $htmltitle;
			echo '<div class="nxs-clear padding"></div>'; 
		}
		
		// Logo
		$image_alt = trim($image_alt);
	  $image_alt_attribute = "";
	  if ($image_alt != "") 
	  {
	    $image_alt_attribute = 'alt="' . htmlspecialchars($image_alt) . '" ';
	  }
		
		if ($imageurl != "") 
		{
			echo '
			<div class="logo-wrapper nxs-absolute nxs-logo">
				<img ' . $image_alt_attribute . ' src="'.$imageurl.'" class="nxs-absolute '.$halign.' '.$max_height_percentage.'">
			</div>';
		}
		
		// Objects
		if ($object1_color != "" || $object2_color != ""){
			echo '
			<div class="color-wrapper nxs-absolute">
				<div class="object1 nxs-absolute '.$object1_color_cssclass.'"></div>
				<div class="object2 nxs-absolute '.$object2_color_cssclass.'"></div>
				<div class="object3 nxs-absolute '.$object2_color_cssclass.'"></div>
				
				<div class="object4 nxs-absolute '.$object1_color_cssclass.'"></div>
				<div class="object5 nxs-absolute '.$object2_color_cssclass.'"></div>
				<div class="object6 nxs-absolute '.$object2_color_cssclass.'"></div>
				<div class="object7 nxs-absolute '.$object2_color_cssclass.'"></div>
			</div>';
		}
		
		// Carousel
		echo '
		<div class="carousel-wrapper nxs-align-center '.$image_size.'" >
			'.nxs_widgets_carousel_randomImage_2($carousel).'
		</div>';
			
	}
	
	/* ------------------------------------------------------------------------------------------------- */
	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-'.$placeholderid;
	return $result;
}

function nxs_widgets_carousel_initplaceholderdata($args)
{
	extract($args);
	
	// create a new generic list with subtype carousel
	// assign the newly create list to the list property
	
	$subargs = array();
	$subargs["nxsposttype"] = "genericlist";
	$subargs["nxssubposttype"] = "carousel";	// NOTE!
	$subargs["poststatus"] = "publish";
	$subargs["titel"] = nxs_l18n__("Carousel items", "nxs_td");
	$subargs["slug"] = $subargs["titel"] . " " . nxs_generaterandomstring(6);
	$subargs["postwizard"] = "defaultgenericlist";
	
	$response = nxs_addnewarticle($subargs);
	if ($response["result"] == "OK")
	{
		$args["items_genericlistid"] = $response["postid"];
		$args["items_genericlistid_globalid"] = nxs_get_globalid($response["postid"], true);
	} else {
	}
	
	$args['halign'] = "center";
	$args['max_height_percentage'] = "100%";
	$args['image_size'] = "stretch";
	$args['image_filter'] = "none";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_carousel_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_carousel_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_dataprotection_nexusframework_widget_carousel_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("widget-none");
}


?>