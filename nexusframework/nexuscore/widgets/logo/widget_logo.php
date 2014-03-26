<?php

function nxs_widgets_logo_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

function nxs_widgets_logo_getimg() {
	return "404.png";
}

// Setting the widget title
function nxs_widgets_logo_gettitle() {
	return nxs_l18n__("Logo", "nxs_td");
}

// Unistyle
function nxs_widgets_logo_getunifiedstylinggroup() {
	return "logowidget";
}

// Unicontent
function nxs_widgets_logo_getunifiedcontentgroup() {
	return "logowidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_logo_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$options = array
	(
		"sheettitle" 		=> nxs_widgets_logo_gettitle(),
		"sheeticonid" 		=> nxs_widgets_logo_geticonid(),
		"sheethelp" 		=> nxs_l18n__("http://nexusthemes.com/logo-widget/"),		
		"unifiedstyling" 	=> array ("group" => nxs_widgets_logo_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_logo_getunifiedcontentgroup(),),
		"fields" 			=> array
		(
			// LOGO			
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Logo properties", "nxs_td"),
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
				"id" 				=> "image_maxheight",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Max height", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("maxheight"),
				"tooltip" 			=> nxs_l18n__("Maximize the height of the image.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "halign",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Logo alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("halign"),
				"tooltip" 			=> nxs_l18n__("Align your logo to the left, center or right from the placeholder.", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
			),
			
			// MISCELLANEOUS

			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Miscellaneous", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Logo title", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Logo title goes here", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If your logo has a title put it here.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "subtitle",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Logo subtitle", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Logo subtitle goes here", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If your logo has a subtitle put it here.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Override title fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "subtitle_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Override subtitle fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Article link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("It's a best practice to give a logo a link to the homepage, but you're free to let it point to another page.", "nxs_td"),
				"unicontentablefield" => true
			),
			array(
				"id" 				=> "destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("External link", "nxs_td"),
				"placeholder"		=> nxs_l18n__("http://www.nexusthemes.com", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an external source using the full url.", "nxs_td"),
				"unicontentablefield" => true
			),

			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
			),
			
			// ABSOLUTE POSITIONING

			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Absolute positioning", "nxs_td"),
				"initial_toggle_state"	=> "closed",
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "stop_absolute",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Stop absolute positioning", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" 	=> nxs_l18n__("Never", "nxs_td"),
					"stop-absolute480" 	=> nxs_l18n__("480", "nxs_td"),
					"stop-absolute720" 	=> nxs_l18n__("720", "nxs_td"),
					"stop-absolute960" 	=> nxs_l18n__("960", "nxs_td"),
					"stop-absolute1200" => nxs_l18n__("1200", "nxs_td"),
				),
				"tooltip" 			=> nxs_l18n__("This option stops the absolute positioning of the logo.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "top",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Top", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "left",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Left", "nxs_td"),
				"unistylablefield"	=> true
			),

			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),
			
			
			// BACKGROUND IMAGE
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Background image", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			
			array( 
				"id" 				=> "bg_image_imageid",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Background image", "nxs_td"),
			),
			array(
				"id" 				=> "min_height",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Minimum height", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("-", "nxs_td"),
					"0px" => nxs_l18n__("0px", "nxs_td"),
					"100px" => nxs_l18n__("100px", "nxs_td"),
					"200px" => nxs_l18n__("200px", "nxs_td"),
					"300px" => nxs_l18n__("300px", "nxs_td"),
					"400px" => nxs_l18n__("400px", "nxs_td"),
					"500px" => nxs_l18n__("500px", "nxs_td"),
					"600px" => nxs_l18n__("600px", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			/*array(
				"id"     			=> "logo_padding",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Logo padding", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("padding"),
				"unistylablefield"	=> true
			),*/
			array(
				"id"     			=> "logo_margin",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Logo margin", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("margin"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "bgcolor",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Logo wrapper background", "nxs_td"),
				"unistylablefield"	=> true
			),
			/*array(
				"id" 				=> "border_radius",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Border radius example", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_radius"),
				"unistylablefield"	=> true
			),*/
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
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

function nxs_widgets_logo_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));

	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
		
	// Blend unistyle properties
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_logo_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);	
	}
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_logo_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Localize atts
	$mixedattributes = nxs_localization_localize($mixedattributes);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title","subtitle"));	
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs); 
	
	// Turn on output buffering
	ob_start();
		
	global $nxs_global_placeholder_render_statebag;
	if ($shouldrenderalternative == true) {
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . "-warning ";
	} else {
		// Appending custom widget class
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " ";
	}
		
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
 	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;
	if (
		$image_imageid == "" &&
		$title == "" &&
		$subtitle == "" &&
		nxs_has_adminpermissions()) {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Minimal: image, title or subtitle", "nxs_td");
	}
	
	if ($destination_url != "" && $destination_articleid != "") {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Minimal: destination", "nxs_td");
	}
	
 	// Image
	if ($image_imageid != "") {     
		// Core WP function returns ID ($logo_id), size of image (thumbnail, medium, large or full)
		// This is a generic function to return a variable which contains the image chosen from the media manager
		$imagemetadata= wp_get_attachment_image_src($image_imageid, 'full', true);
		
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$imageurl 		= $imagemetadata[0];
		$imagewidth 	= $imagemetadata[1] . "px";
		$imageheight 	= $imagemetadata[2] . "px";	
	}

	$alignment_image = "";
	// Alignment: text and image
	if ($halign != "") {
				
		if ($halign == "left") {
			
			$alignment_image 		= "float: left; margin-right: 15px;";
			$alignment_table_text 	= "display: table; float: left;";
			$alignment_cell_text 	= "display: table-cell; vertical-align: middle; text-align: left;";
			$alignment_imageheight	= $imageheight;
		
		} else if ($halign == "center") {
		
			$alignment_image 		= "margin-left: auto; margin-right: auto";
			$alignment_cell_text 	= "text-align: center;";
		
		} if ($halign == "right") {
			
			$alignment_image 		= "float: right; margin-left: 15px;";
			$alignment_table_text 	= "display: table; float: right;";
			$alignment_cell_text 	= "display: table-cell; vertical-align: middle; text-align: right;";
			$alignment_imageheight	= $imageheight;
		}
		
	} 
 
	// Url
	if ($destination_articleid != "") { 
		$url = nxs_geturl_for_postid($destination_articleid); 
		$target = "";
	} 
	
	if ($destination_url != "") {
		$url = $destination_url; 
		$target = " target='_blank' ";
	}
	
	// Positioning
	if ($top != "" || $left != "") 	{ $absolute = 'nxs-absolute'; }
	if ($top != "") 				{ $top = 'top: '.$top.';'; }
	if ($left != "") 				{ $left = 'left: '.$left.';'; }
	
	$image_alt = trim($image_alt);
	$image_alt = str_replace("\"", "&quote;", $image_alt);
	
	$image_alt_attribute = "";
	if ($image_alt != "") {
		$image_alt_attribute = 'alt="'.$image_alt.'" ';
	}
	
	// Padding and margin
	$logo_padding_cssclass = nxs_getcssclassesforlookup("nxs-padding-", $logo_padding);
	$logo_margin_cssclass = nxs_getcssclassesforlookup("nxs-margin-", $logo_margin);
	
	// Border radius
	$border_radius_cssclass = nxs_getcssclassesforlookup("nxs-border-radius-", $border_radius);
	
	// Background Color
	$bgcolor_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $bgcolor);
	
	// Min height
	if ($min_height != "") { 
		$min_height = "min-height: " . $min_height . ";"; 
		$table = 'nxs-table';
		$table_cell = 'nxs-table-cell';
	}
	
	// Logo
	if ($image_imageid != "") {
		$image_maxheight_cssclass = nxs_getcssclassesforlookup("nxs-maxheight-", $image_maxheight);
		
		$inlinemaxheightstyle = '';
		if ($image_maxheight != '') {
			$inlinemaxheightstyle = '';
			$inlineheightstyle = '';
		}

		$logo = '
			<div class="logo-image">
				<img '.$image_alt_attribute.' src="'.$imageurl.'" class="'.$image_maxheight_cssclass.' '.$logo_margin_cssclass.' '.$border_radius_cssclass.'" style="'.$alignment_image.' "/>
			</div>';
	}
	
	// Image background
	if ($bg_image_imageid != "") {
		$imagemetadata= wp_get_attachment_image_src($bg_image_imageid, 'full', true);
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$imageurl 		= $imagemetadata[0];
		$imagewidth 	= $imagemetadata[1] . "px";
		$imageheight 	= $imagemetadata[2] . "px";	
		
		$image_background = 'background: url(' . $imageurl . ') no-repeat top center;';
	}
	
	// Title
	$title_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $title_fontsize);
	$cssclasses = nxs_concatenateargswithspaces("title", "nxs-title", $title_fontsize_cssclass);
	if ($title != "") 		{ $title = '<span class="'.$cssclasses.'">'.$title.'</span>'; }

	// Subtitle
	$subtitle_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $subtitle_fontsize);
	$cssclasses = nxs_concatenateargswithspaces("subtitle", "nxs-title", $subtitle_fontsize_cssclass);
	if ($subtitle != "") 	{ $subtitle = '<span class="'.$cssclasses.'">'.$subtitle.'</span>'; }
	
	if ($image_background != "") { $image_background_cssclass = 'image-background'; }
	
	// Media query class
	if (($image_imageid != "" && $title != "") || ($image_imageid != "" && $subtitle != "")) { $aligning_content = "aligning-content"; }
	
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	if ($shouldrenderalternative) 
	{
		if ($alternativehint == "") {
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} else {
		echo '	
		<div class="'.$image_background_cssclass.' '.$table.' '.$aligning_content.'" style="'.$image_background.' '.$min_height.'">
			
			<div class="wrapper nxs-applylinkvarcolor '.$table_cell.' '.$bgcolor_cssclass.'">';
				
				if ($url != "") { echo '<a class="'.$stop_absolute.' '.$absolute.'" style="'.$top.' '.$left.'" '.$target.' href="'.$url.'">'; }
				
				echo $logo; 
				
				if ($title != "" || $subtitle != "") {
					echo '
					<div class="title-wrapper '.$image_maxheight_cssclass.' '.$logo_margin_cssclass.'" style="height: '.$alignment_imageheight.'; '.$inlinemaxheightstyle.' '.$alignment_table_text.'">
						<div style="'. $alignment_cell_text.'">';
							echo $title;
							echo $subtitle;
						echo 
						'</div>
					</div>';
				}
				
				
				if ($url != "") { echo '</a>'; }
				
				echo '
				<div class="nxs-clear"></div>
				
			</div> <!-- END wrapper -->
			
		</div>
		'; 
	}
	
	/* ------------------------------------------------------------------------------------------------- */
    	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = ob_get_contents();
	ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-'.$placeholderid;
	return $result;
}


function nxs_widgets_logo_initplaceholderdata($args)
{
	extract($args);

	$homepageid = nxs_gethomepageid();
	$args['destination_articleid'] = $homepageid;
	$args['destination_articleid_globalid'] = nxs_get_globalid($homepageid, true);

	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_logo_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
