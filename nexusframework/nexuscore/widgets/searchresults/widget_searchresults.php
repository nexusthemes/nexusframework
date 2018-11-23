<?php

function nxs_widgets_searchresults_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_searchresults_gettitle() 
{
	return nxs_l18n__("Searchresults[widgettitle]", "nxs_td");
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_searchresults_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$options = array
	(
		"sheettitle" => nxs_widgets_searchresults_gettitle(),
		"sheeticonid" => nxs_widgets_searchresults_geticonid(),
		"fields" => array
		(
			// -------------------------------------------------------			
			
			array( 
				"id" 						=> "wrapper_title_begin",
				"type" 					=> "wrapperbegin",
				"label" 				=> nxs_l18n__("Title properties", "nxs_td"),
			),
			
			array
			( 			
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"tooltip"			=> nxs_l18n__("If you want to give the entire widget a title, you can use this option.", "nxs_td"),
			),			
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),

			// -------------------------------------------------------

			array( 
				"id" 						=> "wrapper_output_begin",
				"type" 					=> "wrapperbegin",
				"initial_toggle_state"	=> "closed",				
				"label" 				=> nxs_l18n__("Output", "nxs_td"),
			),
			
			array(
				"id" 				=> "items_layout",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Layout", "nxs_td"),
				"dropdown" 			=> array
				(
					"extended"=>nxs_l18n__("extended", "nxs_td"),
					"minimal"=>nxs_l18n__("minimal", "nxs_td")
				),
			),
			array(
				"id" 				=> "item_title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title heading for item", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading")
			),		
			array(
				"id" 				=> "item_text_truncatelength",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Text max length", "nxs_td"),
				"dropdown" 			=> nxs_convertindexarraytoassociativearray(array("","100","110","120","130","140","150","160","170","180","190","200","210","220","230","240","250","260","270","280","290","300","400","500","600")),
			),
			array(
				"id" 				=> "item_text_appendchars",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Abbreviation characters", "nxs_td"),
				"placeholder"		=> nxs_l18n__("[...]", "nxs_td"),
			),
			array(
				"id" 				=> "item_image_size",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("image_size")
			),
			array( 
				"id" 				=> "item_image_shadow",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Image shadow", "nxs_td"),
			),
			array(
				"id" 				=> "item_image_border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image border size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_width")
			),
			array(
				"id" 				=> "item_button_text",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Button", "nxs_td"),
				"placeholder"		=> nxs_l18n__("Read more", "nxs_td"),
			),	
			array(
				"id" 				=> "item_button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button scale", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale")
			),
			array( 
				"id" 				=> "item_button_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample<br />text", "nxs_td"),
			),	
			array(
				"id" 				=> "item_button_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Button alignment", "nxs_td"),
			),
			array(
				"id" 				=> "font_icon",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Minimal icon", "nxs_td"),
				"dropdown" 			=> array
				(
					""=>nxs_l18n__("none", "nxs_td"),
					"nxs-icon-text"=>nxs_l18n__("article", "nxs_td")
				),
			),
			array( 
				"id" 				=> "item_showdate",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show date", "nxs_td"),
			),
			array( 
				"id" 				=> "item_showcats",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show categories", "nxs_td"),
			),
			array( 
				"id" 				=> "item_showauthor",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show author", "nxs_td"),
			),
			array( 
				"id" 				=> "wrapper_items_end",
				"type" 				=> "wrapperend"
			),
			
			
			array( 
				"id" 				=> "wrapper_advancedtitle_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Advanced properties: title", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),

			array(
				"id" 				=> "title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title heading markup", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading")
			),
			
			array(
				"id" 				=> "title_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
			),
						
			array(
				"id" 				=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title size", "nxs_td"),
				"dropdown"	 		=> nxs_style_getdropdownitems("fontsize")
			),
			array(
				"id" 				=> "title_heightiq",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align titles", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's title will participate in the title alignment of other partipating widgets in this row", "nxs_td")
			),
			array( 
				"id" 				=> "wrapper_advancedtitle_end",
				"type" 				=> "wrapperend"
			),
			
			// -------------------------------------------------------
		)		
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_searchresults_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));

	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
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
	
	global $nxs_global_placeholder_render_statebag;
	if ($shouldrenderalternative == true) {
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . "-warning ";
	} else {
		// Appending custom widget class
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " ";
	}
		
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
		
	// Minimal vs. extended
	if 			($items_layout == "minimal") 	{ $searchresultstype = 'nxs-searchresults-minimal nxs-blog'; } 
	else if 	($items_layout == "extended") 	{ $searchresultstype = 'nxs-searchresults-extended nxs-blog'; }
	
	// Create font icon class
	$iconcssclass = nxs_getcssclassesforlookup('', $font_icon);
	
	// Font icon
	if ($font_icon == "") { $font_icon = ''; } 
	else { $font_icon = '<span class="' . $iconcssclass . '"></span>'; }
	
	// Image shadow
	if ($item_image_shadow != "") 				{ $item_image_shadow = 'nxs-shadow'; }
	
	// Title importance (H1 - H6)
	if ($item_title_heading != "")
	{
		$itemheadingelement = "h" . $item_title_heading;

	}
	else
	{
		// TODO: derive the title_importance based on the title_fontsize
		//nxs_webmethod_return_nack("to be implemented; derive title_heading from title_fontsize");
		$itemheadingelement = "h1";
	}
	
	// Button scale
	$item_button_scale = nxs_getcssclassesforlookup("nxs-button-scale-", $item_button_scale);
	
	// Button alignment
	if 			($item_button_alignment == "left") 	{ $item_button_alignment = 'nxs-align-left'; } 
	else if 	($item_button_alignment == "right") { $item_button_alignment = 'nxs-align-right'; }	
	
	$searchphrase = $_REQUEST["s"];
	// if user is editing the widgets on the front-end site, the parameter "s" is not
	// specified in $_REQUEST, but in $_REQUEST["clientqueryparameters"]["s"] instead
	if (!isset($searchphrase) || count($searchphrase) == 0)
	{
		if (isset($_REQUEST["clientqueryparameters"]))
		{
			// use the searchphrase specified by the clientpopupsessioncontext
			$searchphrase = $_REQUEST["clientqueryparameters"]["s"];
		}
		else
		{
			//
		}
	}

	if (count($searchphrase) > 0)
	{
		$searchargs = array();
		$searchargs["phrase"] = $searchphrase;
		$pages = nxs_getsearchresults($searchargs);
	}
	else
	{
		$pages = array();
	}

	$item_button_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $item_button_color);
	
	// Default HMTL rendering
	$htmltitle = nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, "", "");
	$htmlfiller = nxs_gethtmlforfiller();
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
		
	echo '<div class="' . $searchresultstype . '">';
			
			
		echo $htmltitle;
		echo $htmlfiller;
	
		if (!isset($searchphrase) || $searchphrase == "")
		{
			echo "<p>";
			echo nxs_l18n__("No searchresults. Please enter a searchphrase first.", "nxs_td");
			echo "</p>";
		}
		else
		{
			echo "<p>";
			echo sprintf(nxs_l18n__("We found %s page(s) for search phrase %s[nxs:echo]", "nxs_td"), count($pages), $searchphrase);
			//echo $searchphrase;
			echo "</p>";
			
			echo $htmlfiller;
	
			
			if ($items_layout == "minimal") 
			{
				// Opsommingslijst
				echo '<ul>';
		
				foreach ($pages as $currentpost) {
					echo '
						<li class="nxs-applylinkvarcolor">
							' . $font_icon . '
							<a href="' . nxs_geturl_for_postid($currentpost->ID) . '">' . $currentpost->post_title . '</a>
						</li>';
				}
				
				echo '</ul>';
			
			} 
			else if ($items_layout == "extended") {
				$aantalpages = count($pages);
				$pageindex = 0;
				foreach ($pages as $currentpost) {
			
					$pageindex = $pageindex + 1;
					$currentpostid = $currentpost->ID;
		
					// Blog title			
					$blogtitel = '
						<' . $itemheadingelement . ' class="nxs-title nxs-applylinkvarcolor">
							<a href="' . nxs_geturl_for_postid($currentpostid) . '">' . $currentpost->post_title . '</a>
						</' . $itemheadingelement . '>';
				
					// Meta data
					if ($item_showdate != "" || $toontijdstip != "" || $item_showcats != "" || $item_showauthor != "") {
						
						// Date
						if ($item_showdate != "") {
							$monthhtml = nxs_getlocalizedmonth(mysql2date('m', $currentpost->post_date));
							$datum = '
								<span class="nxs-day">' 	. mysql2date('j', $currentpost->post_date) . '</span>
								<span class="nxs-month">' 	. $monthhtml . '</span>
								<span class="nxs-year">' 	. mysql2date('Y', $currentpost->post_date) . '</span>';
						}
						
						// Categories
						if ($item_showcats != "") {
							$categories = nxs_getcategorynameandslugs($currentpostid);
							if (count($categories) > 0) {
								foreach ($categories as $currentcategory) {
									$url = get_category_link($currentcategory["id"]);
									$categorien .= '
										<span class="nxs-categories">
											<a href="' . $url . '">' . $currentcategory["name"] . '</a>
										</span>';
								}
							}
						}
						
						// Author
						if ($item_showauthor != "") {
							$authorurl = get_author_posts_url($currentpost->post_author);
							$authorname = get_the_author_meta("display_name", $currentpost->post_author);
							$auteur = '
								<span class="nxs-author">
									<a href="' . $authorurl . '">' . $authorname . '</a>
								</span>';
						}
					}
					
					
					
					// find images used on page
					$imgblocks = nxs_get_images_in_post($currentpostid);
					// pick the first one
					$item_image_imageid = $imgblocks[0];	//
					
					$item_image_alt = $currentpost->post_title;
					$item_image_title = $currentpost->post_title;
					$htmlforimage = nxs_gethtmlforimage($item_image_imageid, $item_image_border_width, $item_image_size, $item_image_alignment, $item_image_shadow, $item_image_alt, $item_destination_articleid, $item_destination_url, $item_image_title);
					
					// Excerpt
					$currentexcerpt = "";
					if ($item_text_truncatelength != "0") {
						$textblocks = nxs_get_text_blocks_on_page_v2($currentpostid, "");
						
						// concatenate the blocks if multiple ones exist
						foreach ($textblocks as $currenttextblock) 
						{
							$currentexcerpt .= $currenttextblock;
						}
					}
					
					// Blog truncation
					if ($item_text_truncatelength != "" && $item_text_truncatelength != "0") {
						$currentexcerptbefore = $currentexcerpt;
						$currentexcerpt = nxs_truncate_string($currentexcerpt, intval($item_text_truncatelength));
						if ($currentexcerptbefore == $currentexcerpt)
						{
						}
						else
						{
							$currentexcerpt .= $item_text_appendchars;
						}
					}
		
					if ($item_text_truncatelength != "0") {
						$tekst = '<p class="nxs-default-p nxs-padding-bottom0">' . $currentexcerpt . '</p>';
					}
					
					// Blogentry button
					if ($item_button_text != "") {
						$button = '
							<p class="' . $item_button_alignment . ' nxs-padding-bottom0">
								<a class="nxs-button ' . $item_button_scale . ' ' . $item_button_color_cssclass . '" href="' . nxs_geturl_for_postid($currentpostid) . '">' . $item_button_text . '</a>
							</p>';
					}
					
					// Rendering of individual searchresultsentry
					echo '
						<div class="nxs-blogentry">';
		
							echo $blogtitel;
							
							echo '<div class="nxs-blog-meta">';
								echo $datum;
								if ( $datum != "" && $categorien != "" || $datum != "" && $auteur != "" ) { echo '<span class="nxs-seperator"> | </span>'; }
								echo $categorien;
								if ( $categorien != "" && $auteur != ""	) { echo '<span class="nxs-seperator"> | </span>'; }
								echo $auteur;
							echo '</div>';
		
							
							echo '<div class="nxs-clear"></div>';
							
							echo $htmlforimage;
							echo $tekst;
							echo $button;
							
							echo '<div class="nxs-clear nxs-padding-top20"></div>';
				
						echo '</div>';
				
					// Resetting categories
					$categorien = "";
				}
				
			}
			else
			{
				// 
				nxs_renderplaceholderwarning(nxs_l18n__("Unsupported items_layout; ", "nxs_td") . $items_layout);
			}
		}
	
	echo '</div>';
	
	
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

function nxs_widgets_searchresults_initplaceholderdata($args)
{
	extract($args);

	$args['title_heading'] = "2";
	$args['items_layout'] = 'extended';
	
	$args['item_showdate'] = "true";
	$args['item_showcats'] = "true";

	$args['item_text_truncatelength'] = "300";
	$args['item_text_appendchars'] = "[...]";
	$args['item_button_text'] = nxs_l18n__("Read more", "nxs_td");
	$args['item_button_color'] = "base2";
	$args['item_title_heading'] = "3";
	
	$args['item_image_size'] = "c@1-0";
	
	$args['title_heightiq'] = "true";	
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_dataprotection_nexusframework_widget_searchresults_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("widget-none");
}

?>