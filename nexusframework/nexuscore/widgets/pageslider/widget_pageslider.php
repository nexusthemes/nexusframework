<?php

function nxs_widgets_pageslider_geticonid() {
	return "nxs-icon-sliderbox";
}

// Setting the widget title
function nxs_widgets_pageslider_gettitle() {
	return nxs_l18n__("Page slider", "nxs_td");
}

// Unistyle
function nxs_widgets_pageslider_getunifiedstylinggroup() {
	return "pagesliderwidget";
}

function nxs_widgets_pageslider_getunifiedcontentgroup() {
	return "pagesliderwidget";
}

function nxs_widgets_pageslider_registerhooksforpagewidget($args)
{
	$pagedecoratorid = $args["pagedecoratorid"]; 
	$pagedecoratorwidgetplaceholderid = $args["pagedecoratorwidgetplaceholderid"];
	
	$widget_metadata = nxs_getwidgetmetadata($pagedecoratorid, $pagedecoratorwidgetplaceholderid);
	
	if ($widget_metadata["hide_for_touchdevices"] != "" && nxs_ishandheld())
	{
		// note; it would be better to do this check client-side, if the page would be cached,
		// while its first accessed on a touch device this will mean all subsequent pages will be server
		// wrongly (and the other way around)...
		
		// ignore; not available on mobiles/tablets
	}
	else
	{
		$pagesliderid = $widget_metadata['items_genericlistid'];
		
		if (isset($pagesliderid))
		{
			// for now we use a global variable to store the pagesliderid, this is not the best solution,
			// but works for now
			
			global $nxs_pageslider_pagedecoratorid;
			$nxs_pageslider_pagedecoratorid = $pagedecoratorid;
			global $nxs_pageslider_pagedecoratorwidgetplaceholderid;
			$nxs_pageslider_pagedecoratorwidgetplaceholderid = $pagedecoratorwidgetplaceholderid;
			
			global $nxs_pageslider_pagesliderid;
			$nxs_pageslider_pagesliderid = $pagesliderid;
			
			add_action('nxs_beforeend_head', 'nxs_widgets_pageslider_beforeend_head');
			add_action('nxs_ext_betweenheadandcontent', 'nxs_widgets_pageslider_betweenheadandcontent');
		}
	}
}


/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_pageslider_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_pageslider_gettitle(),
		"sheeticonid" 		=> nxs_widgets_pageslider_geticonid(),
		"supporturl" => "https://www.wpsupporthelp.com/wordpress-questions/background-slider-page-slider-wordpress-questions-14/",
		"unifiedstyling" 	=> array("group" => nxs_widgets_pageslider_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_pageslider_getunifiedcontentgroup(),),
		"fields" => array
		(
			// SLIDES			
			
			array( 
				"id" 				=> "wrapper_slides_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Slides", "nxs_td"),
			),
			
			array(
				"id" 				=> "items_genericlistid",
				"type" 				=> "staticgenericlist_link",
				"label" 			=> nxs_l18n__("Edit slides", "nxs_td"),
				"preview_theme" => "gallerythumbs",
				"unicontentablefield" => true,
			),
			array(
				"id" 				=> "item_durationvisibility",	
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Visibility duration", "nxs_td"),
				"dropdown" 			=> nxs_convertindexarraytoassociativearray(array("3000","4000","5000","6000","9000","12000")),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "caption_container_height",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Caption container height", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("None", "nxs_td"),
					"300px" => nxs_l18n__("300px", "nxs_td"),
					"400px" => nxs_l18n__("400px", "nxs_td"),
					"500px" => nxs_l18n__("500px", "nxs_td"),
					"600px" => nxs_l18n__("600px", "nxs_td"),
					"screenheight" => nxs_l18n__("Height of screen", "nxs_td"),
				),
				"tooltip" 			=> nxs_l18n__("This option set's the height of the caption container between the header and the rest of the content", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "hide_for_touchdevices",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Hide for handheld devices", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "remove_thumbnail_navigation",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Remove thumbnail navigation", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "show_thumb_tray",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show thumb tray", "nxs_td"),
				"unistylablefield"	=> true
			),
			/*
			deprecated
			
			array(
				"id" 				=> "ken_burns",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Ken Burns effect", "nxs_td"),
				"unistylablefield"	=> true
			),*/
			
			array( 
				"id" 				=> "wrapper_slides_end",
				"type" 				=> "wrapperend"
			),
			
			// CAPTIONS			
			
			array( 
				"id" 				=> "wrapper_captions_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Captions", "nxs_td"),
				"initial_toggle_state"	=> "closed",
				"unistylablefield"	=> true
			),
			
			
			array(
				"id" 				=> "show_metadata",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Captions", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked this option will show a title, description and link if available", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "no_blink",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Disable caption blink", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked this option will disable the blinking of caption in unison with slide transitions.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "caption_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Caption width", "nxs_td"),
				"dropdown" 			=> array
				(
					"100"=>"100%",
					"50"=>"50%",
					"40"=>"40%",
					"30"=>"30%",
					"20"=>"20%"
				),
				"unistylablefield"	=> true
			),
			array(
				"id"     			=> "text_padding",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Text padding", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("padding"),
				"unistylablefield"	=> true
			),
			array(
				"id"     			=> "text_margin",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Text margin", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("margin"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Title font", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "description_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Description fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "description_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Description fontzen", "nxs_td"),
				"unistylablefield"	=> true
			),			
			
			array(
				"id" 				=> "halign",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Caption alignment", "nxs_td"),
				"dropdown" 			=> 
				array(
					"left"			=>"left",
					"center"		=>"center",
					"right"			=>"right",
					"top left"		=>"top left",
					"top center"	=>"top center",
					"top right"		=>"top right",
					"bottom left"	=>"bottom left",
					"bottom center"	=>"bottom center",
					"bottom right"	=>"bottom right",
				),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "bgcolor",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Wrapper background", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "fixed_font",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Fixed font", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, font sizes will be a fixed size for smaller resolutions", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "responsive_display",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Responsive display", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("Always", "nxs_td"),
					"pageslider480" => nxs_l18n__("480", "nxs_td"),
					"pageslider720" => nxs_l18n__("720", "nxs_td"),
					"pageslider960" => nxs_l18n__("960", "nxs_td"),
					"pageslider1200" => nxs_l18n__("1200", "nxs_td"),
				),
				"tooltip" 			=> nxs_l18n__("This option let's you set the sliders display at a certain viewport and up", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_captions_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),
			
			// BUTTON
			
			array( 
				"id" 				=> "wrapper_button_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Button", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),

			array(
				"id" 				=> "button_text",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Button text", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Button text goes here", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Put a text on the call-to-action button.", "nxs_td"),
				"unicontentablefield" => true,
			),	
			array(
				"id" 				=> "button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button scale", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "button_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "wrapper_button_end",
				"type" 				=> "wrapperend"
			),
		)
	);

	nxs_extend_widgetoptionfields($options, array("unistyle", "unicontent"));	
	
	return $options;
}


/* ADMIN PAGE HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_pageslider_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_pageslider_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_pageslider_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	
	global $nxs_global_row_render_statebag;
	
	$items_genericlistid = $mixedattributes['items_genericlistid'];

	/* ADMIN PAGE HOVER MENU HTML
	---------------------------------------------------------------------------------------------------- */
	
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
		$hovermenuargs["enable_decoratewidget"] = false;
		$hovermenuargs["enable_deletewidget"] = false;
		$hovermenuargs["enable_deleterow"] = true;
		$hovermenuargs["metadata"] = $mixedattributes;
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	}
	
	/* ADMIN EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	nxs_ob_start();
	
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-custom-html nxs-applylinkvarcolor";
		
	$shouldrenderalternative = false;
	$trimmedhtmlcustom = $htmlcustom;
	$trimmedhtmlcustom = preg_replace('/<!--(.*)-->/Uis', '', $trimmedhtmlcustom);
	$trimmedhtmlcustom = trim($trimmedhtmlcustom);
	if ($trimmedhtmlcustom == "" && nxs_has_adminpermissions())
	{
		$shouldrenderalternative = true;
	}
	
	/* ADMIN OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	echo '
	<div class="nxs-dragrow-handler nxs-padding-menu-item">
		<div class="content2">
			<div class="box">
	        	<div class="box-title">
					<h4>Background slider</h4>
				</div>
				<div class="box-content"></div>
			</div>
			<div class="nxs-clear"></div>
		</div>
	</div>';
	
	/* ------------------------------------------------------------------------------------------------- */
	
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-'.$placeholderid;

	// outbound statebag
	return $result;
}

/* INITIATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_pageslider_initplaceholderdata($args)
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	
	// create a new generic list with subtype gallery
	// assign the newly create list to the list property
	
	$subargs = array();
	$subargs["nxsposttype"] 				= "genericlist";
	$subargs["nxssubposttype"] 				= "pageslider";	// NOTE!
	$subargs["poststatus"] 					= "publish";
	$subargs["titel"] = nxs_l18n__("Slider items[title]", "nxs_td");
	$subargs["slug"]= $subargs["titel"] . " " . nxs_generaterandomstring(6);
	$subargs["postwizard"] 					= "defaultgenericlist";
	
	$response = nxs_addnewarticle($subargs);
	if ($response["result"] == "OK") {
		$args["items_genericlistid"] 		= $response["postid"];
		$args["items_genericlistid_globalid"] = nxs_get_globalid($response["postid"], true);
	} else {
		var_dump($response);
		die();
	}
	
	$args["item_durationvisibility"] 		= "4000";
	$args['caption_container_height'] 		= "400px";
	$args['hide_for_touchdevices'] 			= 'checked';
	$args['remove_thumbnail_navigation'] 	= 'checked';
	$args['show_metadata'] 					= 'checked';
	$args['caption_width'] 					= '50';
	$args['title_fontsize'] 				= '4-0';
	$args['description_fontsize'] 			= '1-8';
	$args['halign'] 						= 'left';
	$args['bgcolor'] 						= 'base2-a0-0';
	$args['fixed_font'] 					= 'checked';
	$args['responsive_display'] 			= 'pageslider960';
	$args['button_color'] 					= 'c22';
	$args['button_scale'] 					= '2-0';
	
	
	$args["item_transitionduration"] 		= "300";
	$args['ph_margin_bottom'] 				= "0-0";

	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_pageslider_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	$result = nxs_widgets_initplaceholderdatageneric($args, $widgetname);
	return $result;
}

/* UPDATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_pageslider_updateplaceholderdata($args) 
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	$result = nxs_widgets_updateplaceholderdatageneric($args, $widgetname);
	return $result;
}

/* PAGE SLIDER HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_pageslider_beforeend_head()
{
	
	// the global $nxs_pageslider_pagesliderid is set in nxs_widgets_pageslider_registerhooksforpagewidget($args)
	global $nxs_pageslider_pagesliderid;
	
	$structure = nxs_parsepoststructure($nxs_pageslider_pagesliderid);
	$aantalslides = count($structure);
	
	// get meta of the slider itself (such as transition time, etc.)
	global $nxs_pageslider_pagedecoratorid;
	global $nxs_pageslider_pagedecoratorwidgetplaceholderid;
	$pageslider_metadata = nxs_getwidgetmetadata($nxs_pageslider_pagedecoratorid, $nxs_pageslider_pagedecoratorwidgetplaceholderid);
	
	// Lookup atts
	$pageslider_metadata = nxs_filter_translatelookup($pageslider_metadata, array("title","text", "button_text","destination_url"));	

	
	// Unistyle
	$unistyle = $pageslider_metadata["unistyle"];
	if (isset($unistyle) && $unistyle != "") 
	{
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_pageslider_getunifiedstylinggroup(), $unistyle);
		$pageslider_metadata = array_merge($pageslider_metadata, $unistyleproperties);
	}
	
	// Blend unicontent properties
	$unicontent = $pageslider_metadata["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_pageslider_getunifiedcontentgroup(), $unicontent);
		$pageslider_metadata = array_merge($pageslider_metadata, $unicontentproperties);
	}
	
	extract($pageslider_metadata);
	
	/* EXPRESSIONS
	----------------------------------------------------------------------------------------------------*/
	
	$button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $button_scale);
	$button_alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $button_alignment);
	$button_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $button_color);
	
	// Text padding and margin
	$text_padding_cssclass = nxs_getcssclassesforlookup("nxs-padding-", $pageslider_metadata["text_padding"]);
	$text_margin_cssclass = nxs_getcssclassesforlookup("nxs-margin-", $text_margin);
	
	// Title and description fontsizes
	$title_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $title_fontsize);
	
	if ($title_fontzen != "")
	{
		$title_fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen nxs-fontzen-", $title_fontzen);
	}

	$description_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-text-fontsize-", $description_fontsize);
	if ($description_fontzen != "")
	{
		$description_fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen nxs-fontzen-", $description_fontzen);
	}


	
	// Caption alignment
	if ($halign == 'left'   || $halign == 'top left'   || $halign == 'bottom left') 	{ $text_align = 'nxs-align-left'; } else 
	if ($halign == 'center' || $halign == 'top center' || $halign == 'bottom center') 	{ $text_align = 'nxs-align-center'; } else 
	if ($halign == 'right'  || $halign == 'top right'  || $halign == 'bottom right') 	{ $text_align = 'nxs-align-right'; } 
	
	// Background Color
	if ($bgcolor == "") { $bgcolor = 'base2-a0-6'; }
	$bgcolor_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $bgcolor);
	
	// Metadata transition (blink feature)
	if 		($item_durationvisibility == "3000") { $metadata_transition = "blink3-3"; }
	else if ($item_durationvisibility == "4000") { $metadata_transition = "blink4-3"; }
	else if ($item_durationvisibility == "4000") { $metadata_transition = "blink5-3"; }
	else if ($item_durationvisibility == "6000") { $metadata_transition = "blink6-3"; }
	
	if ($no_blink != "") { $metadata_transition = "no-blink"; }
	
	// Transition duration
	$item_transitionduration = '300';
	if ($item_durationvisibility == '')
	{
		$item_durationvisibility = '3000';
	}
	
	if ($aantalslides > 0)
	{
		// the startslide and autoplay can be specified in the url
		// this is for generating screenshots for the product image
		$startslide = 1;	// value 1 = start at the first one (0 would mean start at a random slide)
		
		$autoplay = 1;
		if ($_REQUEST['screenshot'] == "true")
		{
			$metadata_transition = "no-blink";
			$autoplay = 0;
		}
		?>
		<link rel="stylesheet" href="<?php echo nxs_getframeworkurl(); ?>/js/supersized/slideshow/css/supersized.css" type="text/css" media="screen" />
		<script src="<?php echo nxs_getframeworkurl(); ?>/js/supersized/slideshow/js/altsupersized.js"></script>
		<script src="<?php echo nxs_getframeworkurl(); ?>/js/supersized/slideshow/theme/nxssupersizedshutter.js"></script>
		
		<script>
				
				jQuery(document).ready
				(
					function() 
					{
						// handling of "is handheld" is already taken care of;
						// if we reach this point we should always render the slider
						var shouldrenderslider = true;
						if(shouldrenderslider)
						{
										
							jQ_nxs(function($){
							
							$.supersized({
							
								// Functionality
								slideshow               :   1,												// Slideshow on/off
								autoplay				:	<?php echo $autoplay; ?>,						// Slideshow starts playing automatically
								start_slide             :   <?php echo $startslide; ?>,						// Start slide (0 is random)
								image_path				:	'',
								stop_loop				:	0,												// Pauses slideshow on last slide
								random					: 	0,												// Randomize slide order (Ignores start slide)
								slide_interval          :   <?php echo $item_durationvisibility; ?>,		// Length between transitions
								transition              :   1, 												// 0-None, 1-Fade, 2-Slide Top, 3-Slide Right, 4-Slide Bottom, 5-Slide Left, 6-Carousel Right, 7-Carousel Left
								transition_speed		:	<?php echo $item_transitionduration; ?>,		// Speed of transition
								new_window				:	0,												// Image links open in new window/tab
								pause_hover             :   0,												// Pause slideshow on hover
								keyboard_nav            :   1,												// Keyboard navigation on/off
								performance				:	2,												// 0-Normal, 1-Hybrid speed/quality, 2-Optimizes image quality, 3-Optimizes transition speed // (Only works for Firefox/IE, not Webkit)
								image_protect			:	0,												// Disables image dragging and right click with Javascript
																		   
								// Size & Position						   
								min_width		        :   0,												// Min width allowed (in pixels)
								min_height		        :   0,												// Min height allowed (in pixels)
								vertical_center         :   1,												// Vertically center background
								horizontal_center       :   1,												// Horizontally center background
								fit_always				:	0,												// Image will never exceed browser width or height (Ignores min. dimensions)
								fit_portrait         	:   1,												// Portrait images will not exceed browser height
								fit_landscape			:   0,												// Landscape images will not exceed browser width
																		   
								// Components							
								slide_links				:	'blank',										// Individual links for each slide (Options: false, 'number', 'name', 'blank')
								thumb_links				:	1,												// Individual thumb links for each slide
								thumbnail_navigation    :   0,												// Thumbnail navigation
								
								progress_bar			:	1,												// Timer for each slide							
								mouse_scrub				:	1,
								
								slides 					:  	[												// Slideshow Images
								<?php
									$isfirst = true;
									$max = count($structure);
									
									foreach ($structure as $pagerow) 
									{
										if ($_REQUEST["screenshot"] == "true")
										{
											$slideindextorender = intval($_REQUEST["slider_startslide"]) % $max;
											$pagerow = $structure[$slideindextorender];
										}
										
										$content = $pagerow["content"];
										$slideplaceholderid = nxs_parsepagerow($content);
										$placeholdermetadata = nxs_getwidgetmetadata($nxs_pageslider_pagesliderid, $slideplaceholderid);
										$placeholdertype = $placeholdermetadata["type"];					
										
										// Lookup atts
										$placeholdermetadata = nxs_filter_translatelookup($placeholdermetadata, array("title","text", "button_text","destination_url", "image_src"));	

										if ($placeholdertype == "" || $placeholdertype == "undefined" || !isset($placeholdertype)) {
											// fix Wendy
										} else if ($placeholdertype == "slide") {
											$koptekst = $placeholdermetadata['title'];
											
											$bodytekst = $placeholdermetadata['text'];
											
											$imageid = $placeholdermetadata['image_imageid'];
											$imagesrc = $placeholdermetadata['image_src'];
											$destination_url = $placeholdermetadata['destination_url'];
											$targetpageid = $placeholdermetadata['destination_articleid'];
											
											if ($targetpageid != 0) {
												$destinationurl = nxs_geturl_for_postid($targetpageid);
											} else {
												$destinationurl = "";
											}
											
											if ($destination_url != "" && $destinationurl == "")
											{
												$destinationurl = $destination_url;
											}
											
											$link = '';
											
											$slide_button_text = $placeholdermetadata['button_text'];
											$slide_button_scale = $placeholdermetadata['button_scale'];
											$slide_button_alignment = $placeholdermetadata['button_alignment'];
											$slide_button_color = $placeholdermetadata['button_color'];
											
											$slide_button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $slide_button_scale);
											$slide_button_alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $slide_button_alignment);
											$slide_button_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $slide_button_color);

											if ($slide_button_text != '')
											{
												if ($destinationurl != "") {
													$link .= '<div class="nxs-clear nxs-margin-top20"><a class="nxs-button '.$button_color_cssclass.' '.$button_scale_cssclass.' '.$metadata_transition.'" href="'.$destinationurl.'">'.$slide_button_text.'</a></div>';
												}
											}
											
											/* Legacy option
											if ($placeholdermetadata['text'] != "") {
												if ($destinationurl != "") {
													$link .= '<div class="nxs-clear nxs-margin-top20"><a class="nxs-button '.$button_color_cssclass.' '.$button_scale_cssclass.' '.$metadata_transition.'" href="'.$destinationurl.'">'.$button_text.'</a></div>';
												}
											}*/
											
											$lookup = nxs_wp_get_attachment_image_src($imageid, 'full', true);
											$imageurl = $lookup[0];
											$imageurl = nxs_img_getimageurlthemeversion($imageurl);		
											
											if ($imageid == "")
											{
												$imageurl = $imagesrc;
											}									
											
											if ($isfirst) {
												$isfirst = false;
											} else {
												echo ",";
											}
											
											// Optional filler between title and text
											if ($bodytekst != "") { $filler = '<div class="nxs-margin-bottom20"></div>'; }
											
											if ($show_metadata != "")
											{
												$kophtml = "";
												if ($koptekst != "")
												{
													$kophtml = "<h2 class='nxs-title $title_fontsize_cssclass $title_fontzen_cssclass $metadata_transition'>" . nxs_render_html_escape_singlequote($koptekst) . "</h2>" . $filler;
												}
												$bodyhtml = "<div class='nxs-placeholder $description_fontsize_cssclass $description_fontzen_cssclass'><div class='nxs-default-p nxs-padding-bottom0 $metadata_transition'><p>" . nxs_render_html_escape_singlequote($bodytekst) . "</p></div></div>";
												
												$titlevalue = "<div class='slidecaption-container $text_padding_cssclass $bgcolor_cssclass $text_align $text_margin_cssclass'>{$kophtml}{$bodyhtml}{$link}</div>";
												$titlevalue = str_replace("'", "\"", $titlevalue);
												$titlevalue = str_replace("\n", "", $titlevalue);
											}
											else
											{
												$titlevalue = "";
											}
											
											?>									
											{
												image : '<?php echo $imageurl; ?>',
												title : '<?php echo $titlevalue; ?>', 
												thumb : '<?php echo $imageurl; ?>',
												url : '<?php echo $destinationurl; ?>'
											}
											<?php
										}
										else
										{
											echo "Placeholdertype is not (yet?) supported;a[" . $placeholdertype . "]";
										}
									}
								?>
								]
							});
					    });
					  }
					  else
				  	{
				  		jQuery('#nxs-supersized').remove();
				  		jQuery('#supersized-loader').remove();
				  		
				  	}
					}
				);
		</script>
		<?php
	}
	else
	{
		// no slides
		// echo "no slides";
	}
}

/* OUTPUT
----------------------------------------------------------------------------------------------------*/
	
function nxs_widgets_pageslider_betweenheadandcontent()
{
	// the global $nxs_pageslider_pagesliderid is set in nxs_widgets_pageslider_registerhooksforpagewidget($args)
	global $nxs_pageslider_pagesliderid;
	
	$structure = nxs_parsepoststructure($nxs_pageslider_pagesliderid);
	$aantalslides = count($structure);
	
	// get meta of the slider itself (such as transition time, etc.)
	global $nxs_pageslider_pagedecoratorid;
	global $nxs_pageslider_pagedecoratorwidgetplaceholderid;	
	$pageslider_metadata = nxs_getwidgetmetadata($nxs_pageslider_pagedecoratorid, $nxs_pageslider_pagedecoratorwidgetplaceholderid);
	
	// Unistyle
	$unistyle = $pageslider_metadata["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_pageslider_getunifiedstylinggroup(), $unistyle);
		$pageslider_metadata = array_merge($pageslider_metadata, $unistyleproperties);
	}
	
	// Blend unicontent properties
	$unicontent = $pageslider_metadata["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_pageslider_getunifiedcontentgroup(), $unicontent);
		$pageslider_metadata = array_merge($pageslider_metadata, $unicontentproperties);
	}
	
	extract($pageslider_metadata);
	
	/* EXPRESSIONS
	----------------------------------------------------------------------------------------------------*/
	
	// CAPTION WIDTH
	$caption_width = 'nxs-width'.$caption_width;
	
	// CONTAINER HEIGHT
	if ($caption_container_height != "") {
		if ($caption_container_height == "screenheight") {
			// height will be determined in the runtime (javascript) 
			$supersized_style = "";
		} else if ($caption_container_height == "@@@nxsempty@@@") {
			// no height
			$supersized_style = "";
		} else {
			$supersized_style = 'height: '.$caption_container_height.';'; 	
		}
	}
	
	// Show thumb tray
	if ($show_thumb_tray != ""){
		$show_thumb_tray = "style='bottom: 0px !important;'";
	} else {
		$show_thumb_tray = "";	
	}
	
	// Text padding and margin
	$text_padding_cssclass = nxs_getcssclassesforlookup("nxs-padding-", $pageslider_metadata["text_padding"]);
	$text_margin_cssclass = nxs_getcssclassesforlookup("nxs-margin-", $text_margin);
	
	// Caption alignment
	if ($halign == 'left') 			{  } else
	if ($halign == 'center') 		{ $halign = "nxs-center"; } else
	if ($halign == 'right') 		{ $halign = "nxs-absolute nxs-right"; } else
	if ($halign == 'top left') 		{ $inline = "nxs-inline"; } else
	if ($halign == 'top center') 	{ $inline = "nxs-inline"; $halign = "nxs-center"; } else
	if ($halign == 'top right') 	{ $inline = "nxs-inline"; $halign = "nxs-absolute nxs-right"; } 
	if ($halign == 'bottom left' || $halign == 'bottom center' || $halign == 'bottom center') { $vertical_align = 'vertical-align: bottom;'; } 
	
	// Thumbnail navigation
	if ($remove_thumbnail_navigation != "") { $remove_thumbnail_navigation = "remove-thumbnail-navigation"; }
	
	// 
	if ($caption_container_height == "") {$height = "no-height"; } else
	if ($caption_container_height != "") {$height = "has-height"; } 
	
	// Ken Burns effect
	if ($ken_burns != "") { 
		$script = "
		<script>
			jQuery(window).ready(
				function() {
					// jQuery('html').addClass('nxs-pageslider kenburns');
					// kenburns is disabled for the time being ...
					jQuery('html').addClass('nxs-pageslider');
				}
			);
		</script>
		";
	} else {
		$script = "
		<script>
			jQuery(window).ready(
				function() {
					jQuery('html').addClass('nxs-pageslider');
				}
			);
		</script>
		";
	}
	
	// fixed font size
	if ($fixed_font != "") { $fixed_font = 'fixed'; }
	
	/* OUTPUT
	----------------------------------------------------------------------------------------------------*/
	
	echo $script;

	// render hover edit menu
  if (nxs_cap_hasdesigncapabilities())
  {
  	$widgeticonid = nxs_widgets_pageslider_geticonid();
  	
		$pagedecoratorid = $nxs_pageslider_pagedecoratorid;
		$pagewidgetplaceholderid = $nxs_pageslider_pagedecoratorwidgetplaceholderid;
		$rowindex = -1;	// ?
  	
  	$title = nxs_widgets_pageslider_gettitle();
		$invoke = "var args={containerpostid:'$pagedecoratorid',postid:'$pagedecoratorid',placeholderid:'$pagewidgetplaceholderid',rowindex:'$rowindex',sheet:'home',onsaverefreshpage:true}; nxs_js_popup_placeholder_neweditsession_v2(args); return false;";
		
		$supporturl = "https://www.wpsupporthelp.com/wordpress-questions/background-slider-page-slider-wordpress-questions-14/";
		
  	?>
  	<!--
  	<div class='nxs-hover-menu-positioner'>
			
		</div>
		<div class='nxs-runtime-autocellsize nxs-cursor nxs-cell-cursor'>
			test
		</div>
		-->
		<style>
			.nxs-cursorlayer
			{
				display: none;
			}
			.nxs-editor-active .nxs-cursorlayer
			{
				display: flex;
				position: absolute; 
				z-index: 114;
			}
			.nxs-editor-active .nxs-cursorlayer.nxs-hovering
			{
				visibility: visible;
				//background-color: rgba(120,0,0,0.5);
			}
		</style>
		<div class="nxs-hidewheneditorinactive nxs-hoverable nxs-cursorlayer nxs-cursor" style="width: 100vw; <?php echo $supersized_style; ?>">
			<div class='nxs-hover-menu nxs-widget-hover-menu nxs-admin-wrap inside-right-top'>
				<ul class="">
					<li title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>' class='nxs-hovermenu-button'>
						<a href="#" onclick="<?php echo $invoke; ?>" class="site" title="<?php echo $title; ?>">
			  		<!-- <a href='#' title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>'  class="nxs-defaultwidgetclickhandler" onclick="nxs_js_edit_widget_v2(this, 'unlock'); return false;"> -->
			      	<span class='<?php echo $widgeticonid; ?>'></span>
			      </a>
			      <ul style='position: absolute; left: -45px; top: 0px;'>
			 				<li title='<?php nxs_l18n_e("Support", "nxs_td"); ?>' class='nxs-hovermenu-button'>
					  		<a target='_blank' href='<?php echo $supporturl; ?>' title='<?php nxs_l18n_e("Support", "nxs_td"); ?>'>
					      	<span class='nxs-icon-info'></span>
					      </a>
					    </li>
			 			</ul>
			      <ul class="">
			      	<a class='nxs-no-event-bubbling' href='https://www.wpsupporthelp.com/answer/how-to-remove-a-page-slider-from-your-wordpress-theme-919/' target="_blank">
		           	<li title='<?php nxs_l18n_e("Delete[tooltip]", "nxs_td"); ?>'>
		           		<span class='nxs-icon-trash'></span>
		           	</li>
		        	</a>
			      </ul>
		    	</li>
				</ul>	
			</div>
		</div>
		<script>
			jQ_nxs(".nxs-hoverable").unbind("mouseover.glowwidget");
			jQ_nxs(".nxs-hoverable").bind("mouseover.glowwidget", function(e)
			{
				if (!nxs_js_nxsisdragging)
				{
					jQ_nxs(this).find(".nxs-hover-menu").addClass("nxs-hovering");
					jQ_nxs(this).addClass("nxs-hovering");
				}
			}
			);
			
			jQ_nxs(".nxs-hoverable").unbind("mouseleave.glowwidget");
			jQ_nxs(".nxs-hoverable").bind("mouseleave.glowwidget", function(e)
			{
				jQ_nxs(this).removeClass("nxs-hovering");
				jQ_nxs(this).find(".nxs-hover-menu").removeClass("nxs-hovering");
			}
			);
		</script>
  	<?php
	}
  
	echo '
	<div id="nxs-supersized" class="nxs-sitewide-element nxs-containshovermenu1 '.$fixed_font.' '.$responsive_display.' '.$csswidescreenclass.' '.$remove_thumbnail_navigation.' '.$height.'" style="'.$supersized_style.'">';
    
    
    
    // SLIDE CAPTIONS
		if ($show_metadata != "") {
			echo '
		  <div class="caption-aligner '.$caption_width.' '.$halign.'" style="'.$supersized_style.'">			
			  <div id="slidecaption" class="nxs-placeholder '.$inline.'" style="'.$vertical_align.'">
				
				  
				
			  </div>
		  </div>';
		}
		?>
		
		<?php
		if ($caption_container_height == "screenheight") 
		{
			// reset the height of the caption aligner initially the page is loaded,
			// and reset if after the screen size is adjusted
			?>
			<script>
				
				function nxs_js_resetpagesliderheight_to_screenheight()
				{
					var windowheight = jQuery(window).height();
					var headerheight = jQuery("#nxs-header").height();
					var updatedheight = windowheight - headerheight;
					nxs_js_log("windowheight:" + windowheight);
					nxs_js_log("headerheight:" + headerheight);
					nxs_js_log("new height:" + updatedheight);
					jQuery('#nxs-supersized').height(updatedheight);
					jQuery('#nxs-supersized .caption-aligner').height(updatedheight);
				}
				
				jQuery(window).ready
				(
					function()
					{
						nxs_js_log('first time');
						nxs_js_resetpagesliderheight_to_screenheight();
					}
				);
				
				jQuery(document).bind
				(
					'nxs_event_resizeend', 
					function() 
					{
						nxs_js_log('resize time');
						nxs_js_resetpagesliderheight_to_screenheight();
					}
				);
			</script>
			<?php
		}
		?>
        
		<!-- Progress bar causes exorbitant usage of browser / computer cpu, thus is a dangerous option 
        PROGRESS BAR -->
        <div id="progress-back" class="load-item">
            <div id="progress-bar"></div>
        </div>
        
        <!-- THUMB TRAY -->
            <div id="thumb-tray" class="load-item" <?php echo $show_thumb_tray ?>>
                <div id="thumb-back" class="general-nav">
                	<span class="general-ui-styling nxs-icon-arrow-left">
                </div>
                <div id="thumb-forward" class="general-nav">
                	<span class="general-ui-styling nxs-icon-arrow-right">
                </div>
            </div> 
                    
        <!-- CONTROLS WRAPPER 
        --------------------------------------------------------------------------------> 
        <div id="controls-wrapper" class="load-item" style="display: block;">
            <div id="controls">
                
                <!-- PLAY/PAUSE TOGGLE -->
                <div class="pagesliderplaypausetoggle">
                    <a href="#" onclick="api.playToggle(); jQuery('.pagesliderplaypausetoggle').toggle(); return false;">
                        <span class="nxs-toggle general-ui-styling nxs-icon-pause"></span>
                    </a>
                </div>
                
                <!-- initial state is play, thus we hide the wrap -->
                <div class="pagesliderplaypausetoggle" style="display: none;">
                    <a href="#" onclick="api.playToggle(); jQuery('.pagesliderplaypausetoggle').toggle(); return false;">
                        <span class=" nxs-toggle general-ui-styling nxs-icon-play"></span>
                    </a>
                </div>
            
                <!-- SLIDE COUNTER -->
                <div id="slidecounter">
                    <span class="slidenumber"></span> / <span class="totalslides"></span>
                </div>
                
                <!-- THUMB TRAY TOGGLE -->                   
                <div class="">
                  <a id="tray-button" href="#" onclick="return false;">
                    <span class="pagesliderthumbtrayshowhide nxs-toggle general-ui-styling nxs-icon-arrow-up"></span>
                  </a>
                </div>
                
                <!-- SLIDE CAPTIONS -->
                <div id="slidecaption"></div>
                
                <?php
                if (true)
              	{
              		?>                
	                <script>
	                  jQuery(window).load
	                  (
	                    function()
	                    {
	                    	if (api == null)
	                    	{
	                    		nxs_js_log("pageslider api not (yet?) found");
	                    	}
	                    	else
	                    	{
	                        if (!api.options.autoplay)
	                        {
	                            jQuery(".pagesliderplaypausetoggle").toggle();
	                        }
	                      }
	                    }
	                  );
	                </script>
                	<?php
                }
                ?>
                <!--Navigation-->
                <ul id="slide-list"></ul>
                
            </div> <!-- END controls -->
        </div> <!-- END controls-wrapper -->
        
   
</div>		

<?php
}

function nxs_dataprotection_nexusframework_widget_pageslider_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("widget-none");
}

?>