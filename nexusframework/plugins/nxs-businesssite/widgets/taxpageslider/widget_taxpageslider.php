<?php

function nxs_widgets_taxpageslider_geticonid() {
	return "nxs-icon-sliderbox";
}

// Setting the widget title
function nxs_widgets_taxpageslider_gettitle() {
	return nxs_l18n__("Taxonomy Page Slider", "nxs_td");
}

// Unistyle
function nxs_widgets_taxpageslider_getunifiedstylinggroup() {
	return "taxpagesliderwidget";
}

function nxs_widgets_taxpageslider_registerhooksforpagewidget($args)
{
	$pagedecoratorid = $args["pagedecoratorid"]; 
	$pagedecoratorwidgetplaceholderid = $args["pagedecoratorwidgetplaceholderid"];
	
	$widget_metadata = nxs_getwidgetmetadata($pagedecoratorid, $pagedecoratorwidgetplaceholderid);
	
	if ($widget_metadata["hide_for_touchdevices"] != "" && nxs_ishandheld())
	{
		// ignore; not available on mobiles/tablets
	}
	else
	{
		$taxpagesliderid = $widget_metadata['datasource'];
		
		if (isset($taxpagesliderid))
		{
			// for now we use a global variable to store the taxpagesliderid, this is not the best solution,
			// but works for now
			
			global $nxs_taxpageslider_pagedecoratorid;
			$nxs_taxpageslider_pagedecoratorid = $pagedecoratorid;
			global $nxs_taxpageslider_pagedecoratorwidgetplaceholderid;
			$nxs_taxpageslider_pagedecoratorwidgetplaceholderid = $pagedecoratorwidgetplaceholderid;
			
			global $nxs_taxpageslider_metadata;
			$nxs_taxpageslider_metadata = $widget_metadata;
			
			add_action('nxs_beforeend_head', 'nxs_widgets_taxpageslider_beforeend_head');
			add_action('nxs_ext_betweenheadandcontent', 'nxs_widgets_taxpageslider_betweenheadandcontent');
		}
	}
}


/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_taxpageslider_home_getoptions($args) 
{
	global $businesssite_instance;
	$contentmodel = $businesssite_instance->getcontentmodel();
	
	$taxonomiesddl = array();
	$taxonomiesmeta = nxs_business_gettaxonomiesmeta("nexusthemescompany");
	foreach ($taxonomiesmeta as $taxonomy => $taxonomymeta)
	{
	 	if ($taxonomymeta["arity"] == "n")
	 	{
	 		$taxonomiesddl[$taxonomy] = $taxonomymeta["title"];
	 	}
	}
	
	$datasource = $args["datasource"];	
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_taxpageslider_gettitle(),
		"sheeticonid" 		=> nxs_widgets_taxpageslider_geticonid(),
		"sheethelp" => nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1764396204"),
		"unifiedstyling" 	=> array("group" => nxs_widgets_taxpageslider_getunifiedstylinggroup(),),
		"fields" => array
		(
			// SLIDES			
			
			array( 
				"id" 				=> "wrapper_slides_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Slides", "nxs_td"),
			),
			
 			array
      (
				"id" 					=> "datasource",
				"type" 				=> "select",
				"popuprefreshonchange" => "true",
				"label" 			=> nxs_l18n__("Datasource", "nxs_td"),
				"dropdown" 		=> $taxonomiesddl,
			),
			
			// MEDIA META
			
			array(
				"id" 				=> "media_meta",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Media meta", "nxs_td"),
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
				"label" 			=> nxs_l18n__("Title fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Title fontzen", "nxs_td"),
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
					"taxpageslider480" => nxs_l18n__("480", "nxs_td"),
					"taxpageslider720" => nxs_l18n__("720", "nxs_td"),
					"taxpageslider960" => nxs_l18n__("960", "nxs_td"),
					"taxpageslider1200" => nxs_l18n__("1200", "nxs_td"),
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
				"tooltip" 			=> nxs_l18n__("Put a text on the call-to-action button.", "nxs_td")
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

	nxs_extend_widgetoptionfields($options, array("unistyle"));	
	
	return $options;
}


/* ADMIN PAGE HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_taxpageslider_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_taxpageslider_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	
	global $nxs_global_row_render_statebag;
	
	$datasource = $mixedattributes['datasource'];

	/* ADMIN PAGE HOVER MENU HTML
	---------------------------------------------------------------------------------------------------- */
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["enable_decoratewidget"] = false;
	$hovermenuargs["enable_deletewidget"] = false;
	$hovermenuargs["enable_deleterow"] = true;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
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
function nxs_widgets_taxpageslider_initplaceholderdata($args)
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	
	// create a new generic list with subtype gallery
	// assign the newly create list to the list property
	
	$args["datasource"] 		= "nxs_commercialmsg";
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
	$args['responsive_display'] 			= 'taxpageslider960';
	$args['button_color'] 					= 'c22';
	$args['button_scale'] 					= '2-0';
	
	
	$args["item_transitionduration"] 		= "300";
	$args['ph_margin_bottom'] 				= "0-0";

	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_taxpageslider_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	$result = nxs_widgets_initplaceholderdatageneric($args, $widgetname);
	return $result;
}

/* UPDATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_taxpageslider_updateplaceholderdata($args) 
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

function nxs_widgets_taxpageslider_beforeend_head()
{
	// the global $nxs_taxpageslider_metadata is set in nxs_widgets_taxpageslider_registerhooksforpagewidget($args)
	global $nxs_taxpageslider_metadata;
	$nxs_taxpageslider_taxonomy = $nxs_taxpageslider_metadata["datasource"];
	$media_meta = $nxs_taxpageslider_metadata["media_meta"];
	global $businesssite_instance;
	$contentmodel = $businesssite_instance->getcontentmodel();
	$structure = $contentmodel[$nxs_taxpageslider_taxonomy]["instances"];
	$aantalslides = count($structure);
	
	// get meta of the slider itself (such as transition time, etc.)
	global $nxs_taxpageslider_pagedecoratorid;
	global $nxs_taxpageslider_pagedecoratorwidgetplaceholderid;	
	$taxpageslider_metadata = nxs_getwidgetmetadata($nxs_taxpageslider_pagedecoratorid, $nxs_taxpageslider_pagedecoratorwidgetplaceholderid);
	
	// Lookup atts
	$taxpageslider_metadata = nxs_filter_translatelookup($taxpageslider_metadata, array("title","text", "button_text","destination_url"));	

	
		// Unistyle
	$unistyle = $taxpageslider_metadata["unistyle"];
	if (isset($unistyle) && $unistyle != "") 
	{
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_taxpageslider_getunifiedstylinggroup(), $unistyle);
		$taxpageslider_metadata = array_merge($taxpageslider_metadata, $unistyleproperties);
	}
	
	extract($taxpageslider_metadata);
	
	/* EXPRESSIONS
	----------------------------------------------------------------------------------------------------*/
	
	$button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $button_scale);
	$button_alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $button_alignment);
	$button_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $button_color);
	
	// Text padding and margin
	$text_padding_cssclass = nxs_getcssclassesforlookup("nxs-padding-", $taxpageslider_metadata["text_padding"]);
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
		$startslide = 0;
		if ($_REQUEST['nxs_screenshot_format'] != "")
		{
			$startslide = $_REQUEST['slider_startslide'];

			if (intval($startslide) > $aantalslides)
			{
				$startslide = 1;
			}
		}

		$autoplay = 1;
		if ($_REQUEST['nxs_screenshot_format'] != "")
		{
			$metadata_transition = "no-blink";
			$autoplay = 0;

			// ---

			// the global $nxs_taxpageslider_taxonomy is set in nxs_widgets_taxpageslider_registerhooksforpagewidget($args)
			global $nxs_taxpageslider_metadata;
			$nxs_taxpageslider_taxonomy = $nxs_taxpageslider_metadata["datasource"];
			global $businesssite_instance;
			$contentmodel = $businesssite_instance->getcontentmodel();
			$structure = $contentmodel[$nxs_taxpageslider_taxonomy]["instances"];
			$firstslide = $structure[0]; // grab first entity /slide/
			$media = $firstslide["content"]["media"];
			if ($media != "")
			{
				$width = "600";
				$height = "300";
				
				// media_meta = "w:300;h:100";
				$metapieces = explode(";", $media_meta);
				foreach ($metapieces as $metapiece)
				{
					// metapiece = "w:300";
					$subpieces = explode(":", $metapiece);
					if ($subpieces[0] == "w")
					{
						$width = $subpieces[1];
					}
					else if ($subpieces[0] == "h")
					{
						$height = $subpieces[1];
					}
				}
				
				//$image_src = "https://mediamanager.websitesexamples.com/?nxs_imagecropper=true&requestedwidth={$width}&requestedheight={$height}&debug=tru&url={$media}&scope=lazydetect";
				//error_log("text img; $image_src");
				
				$imageurl = "https://d3mwusvabcs8z9.cloudfront.net/?nxs_imagecropper=true&requestedwidth={$width}&requestedheight={$height}&debug=tru&url={$media}&scope=lazydetect";
			}
			
			if ($_REQUEST["screenshot"] == "true")
			{
				if ($imageurl != "") 
				{
					?>
					<script>
						jQ_nxs(document).ready
						(
							function() 
							{
								var html = "<div class='screenshothelper' style='position: absolute; opacity: 0;'><img src='<?php echo $imageurl; ?>' /></div>";
								$('body').prepend(html);
								console.log("screenshothelper installed");
							}
						);
					</script>
					<?php
				}
			}

			// ---
			
			?>
			<style>
				.supersized {  }
				#staticbg 
				{ 
					/* background-color: red; */
					position: fixed;
					width: 100%;
				}
				#staticbg img
				{ 
					<?php
					if ($_REQUEST['nxs_screenshot_format'] == "macbook")
					{
						?>
						height: 1100px;
						<?php
					}
					else if ($_REQUEST['nxs_screenshot_format'] == "imac")
					{
						?>
						height: 1200px;
						<?php
					}
					else 
					{
						// best practise to hide the slider for any handheld
						?>
						height: 0px;
						<?php
					}
					?>
				}
			</style>
			<script>
				jQuery(document).ready
				(
					function() 
					{
						jQuery("body").prepend(jQuery("<div id='staticbg'><img src='<?php echo $imageurl; ?>' /></div>"));
					}
				);
			</script>
			<?php
			return;
		}
		?>
		<link rel="stylesheet" href="<?php echo nxs_getframeworkurl(); ?>/js/supersized/slideshow/css/supersized.css" type="text/css" media="screen" />
		<script type="text/javascript" src="<?php echo nxs_getframeworkurl(); ?>/js/supersized/slideshow/js/altsupersized.js"></script>
		<script type="text/javascript" src="<?php echo nxs_getframeworkurl(); ?>/js/supersized/slideshow/theme/nxssupersizedshutter.js"></script>
		<script type="text/javascript">
				
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
								image_path				:	'http://demo4.horecamasters.nl/wordpress/wp-content/themes/rsw/images/supersized/',
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
									
									foreach ($structure as $instance) 
									{										
										$koptekst = $instance["content"]["title"];
										$bodytekst = $instance["content"]["excerpt"];
										$media = $instance["content"]["media"];
										if ($media != "")
										{
											$width = "600";
											$height = "300";
											
											// media_meta = "w:300;h:100";
											$metapieces = explode(";", $media_meta);
											foreach ($metapieces as $metapiece)
											{
												// metapiece = "w:300";
												$subpieces = explode(":", $metapiece);
												if ($subpieces[0] == "w")
												{
													$width = $subpieces[1];
												}
												else if ($subpieces[0] == "h")
												{
													$height = $subpieces[1];
												}
											}
											
											//$image_src = "https://mediamanager.websitesexamples.com/?nxs_imagecropper=true&requestedwidth={$width}&requestedheight={$height}&debug=tru&url={$media}&scope=lazydetect";
											//error_log("text img; $image_src");
											
											$imagesrc = "https://d3mwusvabcs8z9.cloudfront.net/?nxs_imagecropper=true&requestedwidth={$width}&requestedheight={$height}&debug=tru&url={$media}&scope=lazydetect";
											$imageurl = $imagesrc;
										}
										$destinationurl = "";
										$destination_url = "";
										
										$link = '';
										
										$slide_button_text = ""; // $placeholdermetadata['button_text'];
										$slide_button_scale = ""; //$placeholdermetadata['button_scale'];
										$slide_button_alignment = ""; //$placeholdermetadata['button_alignment'];
										$slide_button_color = ""; //$placeholdermetadata['button_color'];
										
										$slide_button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $slide_button_scale);
										$slide_button_alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $slide_button_alignment);
										$slide_button_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $slide_button_color);

										if ($slide_button_text != '')
										{
											if ($destinationurl != "") {
												$link .= '<div class="nxs-clear nxs-margin-top20"><a class="nxs-button '.$button_color_cssclass.' '.$button_scale_cssclass.' '.$metadata_transition.'" href="'.$destinationurl.'">'.$slide_button_text.'</a></div>';
											}
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
	
function nxs_widgets_taxpageslider_betweenheadandcontent()
{
	// the global $nxs_taxpageslider_taxonomy is set in nxs_widgets_taxpageslider_registerhooksforpagewidget($args)
	global $nxs_taxpageslider_metadata;
	$nxs_taxpageslider_taxonomy = $nxs_taxpageslider_metadata["datasource"];
	
	global $businesssite_instance;
	$contentmodel = $businesssite_instance->getcontentmodel();
	$structure = $contentmodel[$nxs_taxpageslider_taxonomy]["instances"];
	$aantalslides = count($structure);
	
	// get meta of the slider itself (such as transition time, etc.)
	global $nxs_taxpageslider_pagedecoratorid;
	global $nxs_taxpageslider_pagedecoratorwidgetplaceholderid;	
	$taxpageslider_metadata = nxs_getwidgetmetadata($nxs_taxpageslider_pagedecoratorid, $nxs_taxpageslider_pagedecoratorwidgetplaceholderid);
	
	// Unistyle
	$unistyle = $taxpageslider_metadata["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_taxpageslider_getunifiedstylinggroup(), $unistyle);
		$taxpageslider_metadata = array_merge($taxpageslider_metadata, $unistyleproperties);
	}
	
	extract($taxpageslider_metadata);
	
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
	$text_padding_cssclass = nxs_getcssclassesforlookup("nxs-padding-", $taxpageslider_metadata["text_padding"]);
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
		<script type='text/javascript'>
			jQuery(window).ready(
				function() {
					// jQuery('html').addClass('nxs-taxpageslider kenburns');
					// kenburns is disabled for the time being ...
					jQuery('html').addClass('nxs-taxpageslider');
				}
			);
		</script>
		";
	} else {
		$script = "
		<script type='text/javascript'>
			jQuery(window).ready(
				function() {
					jQuery('html').addClass('nxs-taxpageslider');
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
			<script type='text/javascript'>
				
				function nxs_js_resettaxpagesliderheight_to_screenheight()
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
						nxs_js_resettaxpagesliderheight_to_screenheight();
					}
				);
				
				jQuery(document).bind
				(
					'nxs_event_resizeend', 
					function() 
					{
						nxs_js_log('resize time');
						nxs_js_resettaxpagesliderheight_to_screenheight();
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
                <div class="taxpagesliderplaypausetoggle">
                    <a href="#" onclick="api.playToggle(); jQuery('.taxpagesliderplaypausetoggle').toggle(); return false;">
                        <span class="nxs-toggle general-ui-styling nxs-icon-pause"></span>
                    </a>
                </div>
                
                <!-- initial state is play, thus we hide the wrap -->
                <div class="taxpagesliderplaypausetoggle" style="display: none;">
                    <a href="#" onclick="api.playToggle(); jQuery('.taxpagesliderplaypausetoggle').toggle(); return false;">
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
                    <span class="taxpagesliderthumbtrayshowhide nxs-toggle general-ui-styling nxs-icon-arrow-up"></span>
                  </a>
                </div>
                
                <!-- SLIDE CAPTIONS -->
                <div id="slidecaption"></div>
                
                <?php
                if ($_REQUEST['nxs_screenshot_format'] != "")
                {
                	// ignore for screenshot
              	}
              	else
              	{
              		?>                
	                <script type='text/javascript'>
	                  jQuery(window).load
	                  (
	                    function()
	                    {
	                    	if (api == null)
	                    	{
	                    		nxs_js_log("taxpageslider api not (yet?) found");
	                    	}
	                    	else
	                    	{
	                        if (!api.options.autoplay)
	                        {
	                            jQuery(".taxpagesliderplaypausetoggle").toggle();
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

?>
