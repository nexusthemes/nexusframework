<?php

function nxs_widgets_banner_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-dollar";
}

// Setting the widget title
function nxs_widgets_banner_gettitle() {
	return nxs_l18n__("banner", "nxs_td");
}

// Unistyle
function nxs_widgets_banner_getunifiedstylinggroup() {
	return "banner";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_banner_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_banner_gettitle(),
		"sheeticonid" 		=> nxs_widgets_banner_geticonid(),
		"sheethelp" 		=> nxs_l18n__("http://nexusthemes.com/banner-widget/"),
		"unifiedstyling" 	=> array("group" => nxs_widgets_banner_getunifiedstylinggroup(),),
		"fields" => array
		(
			
			/* TITLE
			---------------------------------------------------------------------------------------------------- */
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Title goes here", "nxs_td"),
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
			
			/* ITEMS
			---------------------------------------------------------------------------------------------------- */
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("banner items", "nxs_td"),
			),
			
			array(
				"id" 				=> "items_genericlistid",
				"type" 				=> "staticgenericlist_link",
				"label" 			=> nxs_l18n__("banner items", "nxs_td"),
			),

			/*
			isn't functioning properly
			
			array(
				"id" 				=> "image_border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Border size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_width"),
				"unistylablefield"	=> true
			),*/
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

			array(
				"id" 				=> "halign",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Horizontal alignment", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Align the banner items to the left, center or right from the placeholder.", "nxs_td"),
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
function nxs_widgets_banner_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_banner_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Localize atts
	$mixedattributes = nxs_localization_localize($mixedattributes);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);

	//
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
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
	
	// The banner widget needs the box-shadow inset to render borders because of the 3d transitions.
	if ($image_border_width != "") {
		if (strlen($image_border_width) > 3) { 
			$multiplier = substr($image_border_width, -4, 2); } else {
			$multiplier = substr($image_border_width, -3, 1);
		}
		
		settype($multiplier, "integer");
		$factor = 1;
		$image_border_width = $multiplier * $factor; 
		$image_border_width = 'box-shadow: inset 0 0 0 0 rgba(0,0,0,0.6), inset 0 0 0 '.$image_border_width.'px white, 0 2px 6px rgba(10, 10, 10, 0.3);';
	}
	
	// Grayscale
	if ($image_filter == "grayscale") { $image_filter = "nxs-grayscale"; }
	
	/* banner
	---------------------------------------------------------------------------------------------------- */
	
	$banner = array ();
	
	$structure = nxs_parsepoststructure($items_genericlistid);
	if (count($structure) == 0) {
		$alternativemessage = nxs_l18n__("Warning:no items found", "nxs_td");
	}
	else
	{
        
		$slideindex = 0;
		foreach ($structure as $pagerow) {
            $url = "";
            $target = "";
			$content = $pagerow["content"];
			$currentplaceholderid = nxs_parsepagerow($content);
			$placeholdermetadata = nxs_getwidgetmetadata($items_genericlistid, $currentplaceholderid);
			$placeholdertype = $placeholdermetadata["type"];					
			
			if ($placeholdertype == "") {
				// ignore
			} else if ($placeholdertype == "undefined") {
				// ignore
			} 
			else if ($placeholdertype == "banneritem") 
			{
                    
				$image_imageid = $placeholdermetadata['image_imageid'];
				$lookup = wp_get_attachment_image_src($image_imageid, 'full', true);
				
				$banner_imageurl 		= $lookup[0];
				$banner_imageurl = nxs_img_getimageurlthemeversion($banner_imageurl);
				$banner_imagewidth 		= $lookup[1]. "px";
				$banner_imageheight 	= $lookup[2]. "px";		
				
				$image_alt_attribute = "";
				if ($placeholdermetadata['image_alt'] != "") {
					$image_alt = $placeholdermetadata['image_alt'];
				$image_alt_attribute = 'alt="'.$image_alt.'"';
				}

				$destination_articleid = $placeholdermetadata['destination_articleid'];
				$destination_url = $placeholdermetadata['destination_url'];
				$destination_target = $placeholdermetadata['destination_target'];
				$destination_relation = $placeholdermetadata['destination_relation'];
				
                
				// Url
				if ($destination_articleid != "") { 
					$url = nxs_geturl_for_postid($destination_articleid);
					$target = 'target="_self"'; 
				} 
                
				
				if ($destination_url != "") {
					$url = $destination_url;
					$target = 'target="_blank"';
					
				}
			
				if ($destination_target != "@@@empty@@@") {
					$target = "target='".$destination_target."'";
				}

				$destination_relation_html = '';
				if ($destination_relation == "nofollow") {
					$destination_relation_html = 'rel="nofollow"';
				}

				// add image to html
				$image = '<img class="image image-background '.$image_filter.'" '.$image_alt_attribute.' src="'.$banner_imageurl.'" style="'.$image_border_width.'">';
				
				// add item to banner array
				if ($url != "") {
					$image = '<a href="'.$url.'"'.$target.'" '.$destination_relation_html.'>'.$image.'</a>';
				}
				
				$banner[] = $image;				
			
			} else {
				// ignore
			}
		}
        
	}

	/* HALIGN
	---------------------------------------------------------------------------------------------------- */

	if (!$halign) {
		$halign = "center";
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
		
		// Title
		if ( $title != "" ) { 
			echo $htmltitle;
			echo '<div class="nxs-clear nxs-padding-bottom20"></div>'; 
		}
		
		// Banners
		echo '<ul class="banners-wrapper nxs-table banner-' . $halign . '">';
		
			/* Single banner image
			---------------------------------------------------------------------------------------------------- */
			$lastElement = end($banner);
			for ($i = 0; $i < count($banner); $i++ ){
				// their should be no spaces between the li because it is a inline-block element
				echo '<li class="image-wrapper">
					<div class="nxs-table">
						<div class="nxs-table-cell">
							'.$banner[$i].'
						</div>
					</div>				
				</li>';
			}
			/* ------------------------------------------------------------------------------------------------- */
			
		echo '</ul> <!-- END banners-wrapper -->';
	
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

function nxs_widgets_banner_initplaceholderdata($args)
{
	extract($args);
	
	// create a new generic list with subtype carousel
	// assign the newly create list to the list property
	
	$subargs = array();
	$subargs["nxsposttype"] = "genericlist";
	$subargs["nxssubposttype"] = "banner";	// NOTE!
	$subargs["poststatus"] = "publish";
	$subargs["titel"] = nxs_l18n__("banner items", "nxs_td");
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
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_banner_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
