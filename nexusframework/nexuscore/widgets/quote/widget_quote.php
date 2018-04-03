<?php

function nxs_widgets_quote_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_quote_gettitle() {
	return nxs_l18n__("Quote[nxs:widgettitle]", "nxs_td");
}


function nxs_widgets_quote_getunifiedstylinggroup() {
	return "quotewidget";
}

function nxs_widgets_quote_getunifiedcontentgroup()
{
	return "quotewidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */


// Define the properties of this widget
function nxs_widgets_quote_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array (
		"sheettitle" 		=> nxs_widgets_quote_gettitle(),
		"sheeticonid" 		=> nxs_widgets_quote_geticonid(),
		"supporturl" => "https://www.wpsupporthelp.com/wordpress-questions/quote-widget-wordpress-questions-179/",
		"unifiedstyling" 	=> array ("group" => nxs_widgets_quote_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_quote_getunifiedcontentgroup(),),
		"fields" 			=> array
		(

			// -------------------------------------------------------			
			
			// LOOKUPS
			
			array
			( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Lookups", "nxs_td"),
      	"initial_toggle_state" => "closed-if-empty",
      	"initial_toggle_state_id" => "lookups",
			),
			array
      (
				"id" 					=> "lookups",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Lookup table (evaluated one time when the widget renders)", "nxs_td"),
			),
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),


// IMAGE
			
			array( 
				"id" 				=> "wrapper_image_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Image", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array( 
				"id" 				=> "image_imageid",
				"type" 				=> "image",
				"allow_featuredimage" => true,
				"label" 			=> nxs_l18n__("Choose image", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),			
			array(
				"id" 				=> "image_alignment",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("image_halignment"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "image_size",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("image_size"),
				"unistylablefield"	=> true
			),		
			array( 
				"id" 				=> "image_shadow",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Image shadow", "nxs_td"),
				"unistylablefield"	=> true
			),		
			array(
				"id" 				=> "image_alt",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Image alt text", "nxs_td"),
				"placeholder" => nxs_l18n__("imagealtplaceholder", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true,
				"requirecapability" => nxs_cap_getdesigncapability(),
			),
			array(
				"id" 				=> "image_border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image border width", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_width"),
				"unistylablefield"	=> true
			),	
			array
			( 
				"id" 				=> "image_src",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Image src", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to reference an external image, use this field.", "nxs_td"),
				"unicontentablefield" => true,
			),				
			array
      (
				"id" 					=> "image_src_lookuppicker",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_generic_modeltaxfieldpicker_popupcontent",
			),
			array( 
				"id" 				=> "wrapper_image_begin",
				"type" 				=> "wrapperend"
			),

			//
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Quote", "nxs_td"),
			),
			
			array(
				"id" 				=> "text",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Text", "nxs_td"),		
				"placeholder" 		=> nxs_l18n__("Text goes here", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),	
			array(
				"id" 				=> "source",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Source", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			
			
			
			array(
				"id" 				=> "destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Source URL", "nxs_td"),
				"unicontentablefield" => true,				
			),
			array(
				"id" 				=> "quote_textsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Text textsize", "nxs_td"),
				"dropdown" 			=> array
				(
          "14"	=>"1.4x",
          "12"	=>"1.3x",
          "11"	=>"1.1x",
          "10"	=>"1x",
          "09"	=>"0.9x",
          "08"	=>"0.8x",
        ),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "source_textsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Source textsize", "nxs_td"),
				"dropdown" 			=> array
				(
          "14"	=>"1.4x",
          "12"	=>"1.3x",
          "11"	=>"1.1x",
          "10"	=>"1x",
          "09"	=>"0.9x",
          "08"	=>"0.8x",
        ),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "quote_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Quote width", "nxs_td"),
				"dropdown" 			=> array(""=>"","90%"=>"90%","80%"=>"80%","70%"=>"70%","60%"=>"60%","50%"=>"50%","40%"=>"40%","30%"=>"30%","20%"=>"20%"),
				"unistylablefield"	=> true				
			),
			array(
				"id" 				=> "stars",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Number of stars", "nxs_td"),
				"dropdown" 			=> array
				(
					""		=>"",
					"5"		=>"5",
					"4.5"	=>"4.5",
					"4"		=>"4",
					"3.5"	=>"3.5",
					"3"		=>"3",
					"2.5"	=>"2.5",
					"2"		=>"2",
					"1.5"	=>"1.5",
					"1"		=>"1",
					"0.5"	=>"0.5",
					"0"		=>"0",
				),
				"unicontentablefield" => true,
			),
			array(
				"id" 				=> "rating_text",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Text before stars", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "show_quote_icon",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show quote icon", "nxs_td"),
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

function nxs_widgets_quote_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	}
	
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "")
	{
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_quote_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);	
	}
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") 
	{
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_quote_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Translate model magical fields
	if (true)
	{
		global $nxs_g_modelmanager;
		
		$combined_lookups = nxs_lookups_getcombinedlookups_for_currenturl();
		$combined_lookups = array_merge($combined_lookups, nxs_parse_keyvalues($mixedattributes["lookups"]));
		
		//
		
		// evaluate the lookups widget values line by line
		$sofar = array();
		foreach ($combined_lookups as $key => $val)
		{
			$sofar[$key] = $val;
			//echo "step 1; processing $key=$val sofar=".json_encode($sofar)."<br />";

			//echo "step 2; about to evaluate lookup tables on; $val<br />";
			// apply the lookup values
			$sofar = nxs_lookups_blendlookupstoitselfrecursively($sofar);

			// apply shortcodes
			$val = $sofar[$key];
			//echo "step 3; result is $val<br />";

			//echo "step 4; about to evaluate shortcode on; $val<br />";

			$val = do_shortcode($val);
			$sofar[$key] = $val;

			//echo "step 5; $key evaluates to $val (after applying shortcodes)<br /><br />";

			$combined_lookups[$key] = $val;
		}
		
		// apply the lookups and shortcodes to the customhtml
		$magicfields = array("text", "source", "destination_url");
		$translateargs = array
		(
			"lookup" => $combined_lookups,
			"items" => $mixedattributes,
			"fields" => $magicfields,
		);
		$mixedattributes = nxs_filter_translate_v2($translateargs);
	}	
	
	
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
	
	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		global $nxs_global_placeholder_render_statebag;
		if ($shouldrenderalternative == true) {
			$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . "-warning ";
		} else {
			// Appending custom widget class
			$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " ";
		}
	}
	
	if ($text == "")
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Quote not set", "nxs_td");
	}
			
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	if ($shouldrenderalternative == true && $alternativehint == "") {
		$alternativehint = "The widget isn't configured enough to render properly. Define more options.";
	}
	
	// Text
	if ($show_quote_icon != "") { $show_quote_icon = '<span class="nxs-icon-quotes-left"></span>'; }
	
	// Text
	if ($text != "") { 
		$quote_fontsize = nxs_getcssclassesforlookup("nxs-quote-fontsize", $quote_textsize);
		$text = $show_quote_icon . '<span class="nxs-default-p quote ' . $quote_fontsize . ' ">' . $text . '</span>';	}
	
	// Quote width
	if ($quote_width != "")	{
		$quote_width = 'width: ' . $quote_width . ';';
		$quote_alignment = 'nxs-margin-auto';
	}
	
	
	
	// Stars
	$empty_star = floor(5 - $stars);
	$full_star = 5 - (ceil(5 - $stars));
	if ($full_star + $empty_star != 5) { $half_star = 1; }  
	
	for ($i = 0; $i < $empty_star; $i++ ) { $empty_star_html .= '<span class="nxs-icon-star"></span>'; }
	for ($i = 0; $i < $full_star; $i++ )  { $full_star_html .= '<span class="nxs-icon-star2"></span>'; }
	for ($i = 0; $i < $half_star; $i++ )  { $half_star_html .= '<span class="nxs-icon-star3"></span>'; }
	
	if ($stars != 0) { $stars = $rating_text . " " . $full_star_html.$half_star_html.$empty_star_html; }
	
	
	
	if (true)
	{	
		// Stars and source
		if ($source != "" && $destination_url == "")
		{ 
			$source_textsize = nxs_getcssclassesforlookup("nxs-quote-fontsize", $source_textsize);
	
			$source = '
				<p class="nxs-default-p source nxs-padding-bottom0">
					<strong>'.$stars.'</strong>
					<strong><span class="source '.$source_textsize.'">' . $source . '</span></strong>
				</p>';
		} 
		else if ($source != "" && $destination_url != "")
		{
			$source = '		
				<p class="nxs-default-p source nxs-padding-bottom0">
					<strong>'.$stars.'</strong>
					<a href="' . $destination_url . '" target="_new"><strong><span class="source '.$source_textsize.'">' . $source . '</span></strong></a>
				</p>'; 
		}
		else if ($source == "" && $stars == "")
		{
			$source_textsize = nxs_getcssclassesforlookup("nxs-quote-fontsize", $source_textsize);
	
			$source = '
				<p class="nxs-default-p source nxs-padding-bottom0">
					<strong>'.$stars.'</strong>
					<strong><span class="source '.$source_textsize.'">' . $source . '</span></strong>
				</p>';
		}
	}
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) {
		if ($alternativehint == "") {
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} 
	else 
	{
		if ($image_imageid != "" || $image_src != "")
		{
			
			$image_html = do_shortcode("[nxs_img image_imageid='{$image_imageid}' image_src='{$image_src}' destination_articleid='{$destination_articleid}' destination_url='{$destination_url}' destination_target='{$destination_target}' margin_bottom='0-5' image_size='{$image_size}' image_alignment='{$image_alignment}' image_border_width='{$image_border_width}' image_shadow='{$image_shadow}']");
			echo $image_html;
		}
		
		echo '<div class="nxs-applylinkvarcolor nxs-relative ' . $quote_alignment . '" style="' . $quote_width . '">';
		echo $text;
		echo '<div class="nxs-clear nxs-padding-bottom10"></div>';
		echo $source;
		echo '</div>
		<div class="nxs-clear"></div>';
	} 
				
	/* ------------------------------------------------------------------------------------------------- */
	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	return $result;
}

function nxs_widgets_quote_initplaceholderdata($args)
{
	extract($args);

	$args['show_quote_icon'] = "true";
	$args["text"] = nxs_l18n__("Sample", "nxs_td");

	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_quote_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_quote_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);

	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>