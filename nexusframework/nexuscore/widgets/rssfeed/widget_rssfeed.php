<?php

function nxs_widgets_rssfeed_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-rss"; // . $widget_name;
}

// Setting the widget title
function nxs_widgets_rssfeed_gettitle() {
	return nxs_l18n__("RSS feed", "nxs_td");
}

// Unistyle
function nxs_widgets_rssfeed_getunifiedstylinggroup() {
	return "textwidget";
}

function nxs_widgets_rssfeed_getoutput($rss, $args = array())
{
	nxs_ob_start();
	nxs_widgets_rssfeed_echooutput($rss, $args = array());
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

function nxs_widgets_rssfeed_echooutput($rss, $args = array())
{
	if ( is_string( $rss ) ) {
		$rss = fetch_feed($rss);
	} elseif ( is_array($rss) && isset($rss['url']) ) {
		$args = $rss;
		$rss = fetch_feed($rss['url']);
	} elseif ( !is_object($rss) ) {
		return;
	}

	if ( is_wp_error($rss) ) {
		if ( is_admin() || current_user_can('switch_themes') )
			echo '<p>' . sprintf( __('<strong>RSS Error</strong>: %s'), $rss->get_error_message() ) . '</p>';
		return;
	}

	$default_args = array( 'show_author' => 0, 'show_date' => 0, 'show_summary' => 0 );
	$args = wp_parse_args( $args, $default_args );
	extract( $args, EXTR_SKIP );

	$items = (int) $items;
	if ( $items < 1 || 20 < $items )
		$items = 10;
	$show_summary  = (int) $show_summary;
	$show_author   = (int) $show_author;
	$show_date     = (int) $show_date;

	if ( !$rss->get_item_quantity() ) {
		echo '<ul><li>' . __( 'An error has occurred, which probably means the feed is down. Try again later.' ) . '</li></ul>';
		$rss->__destruct();
		unset($rss);
		return;
	}

	echo '<ul class="nxs-applylinkvarcolor">';
	foreach ( $rss->get_items(0, $items) as $item ) {
		$link = $item->get_link();
		while ( stristr($link, 'http') != $link )
			$link = substr($link, 1);
		$link = esc_url(strip_tags($link));
		$title = esc_attr(strip_tags($item->get_title()));
		if ( empty($title) )
			$title = __('Untitled');

		$desc = str_replace( array("\n", "\r"), ' ', esc_attr( strip_tags( @html_entity_decode( $item->get_description(), ENT_QUOTES, get_option('blog_charset') ) ) ) );
		$excerpt = wp_html_excerpt( $desc, $item_text_truncatelength );

		// Append ellipsis. Change existing [...] to [&hellip;].
		/*if ($item_text_appendchars == substr( $excerpt, 0-strlen($item_text_appendchars)))
		{
			$excerpt = substr( $excerpt, 0, -5 ) . '[&hellip;]';
		}
		else
		*/
		if ( $item_text_appendchars != substr( $excerpt, 0-strlen($item_text_appendchars) ) && $desc != $excerpt )
		{
			$excerpt .= $item_text_appendchars;
		}

		$excerpt = esc_html( $excerpt );
		
		//
		if ($item_text_truncatelength == 0)
		{
			$excerpt = "";
		}

		if ( $show_summary ) {
			$summary = "$excerpt";
		} else {
			$summary = '';
		}

		$date = '';
		if ( $show_date ) {
			$date = $item->get_date( 'U' );

			if ( $date ) {
				$date = ' ' . date_i18n( get_option( 'date_format' ), $date ) . '';
			}
		}

		$author = '';
		if ( $show_author ) {
			$author = $item->get_author();
			if ( is_object($author) ) {
				$author = $author->get_name();
				$author = ' <cite>' . esc_html( strip_tags( $author ) ) . '</cite>';
			}
		}

		if ( $link == '' ) {
			echo "<li>$title{$date}{$summary}{$author}</li>";
		} 
		else 
		{
			echo "<li>";
			// echo "<a class='rsswidget' href='$link' target='_blank' title='$desc'>$title</a>";
			
			$destination_url = $link;
			$htmltitle = nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, $destination_articleid, $destination_url);
			echo $htmltitle;
			
			$text = "{$date} {$summary} {$author}";
			$htmltext = nxs_gethtmlfortext($text, $text_alignment, $text_showliftnote, $text_showdropcap, $wrappingelement, $text_heightiq);			
			echo $htmltext;
			echo "</li>";
		}
	}
	echo '</ul>';
	$rss->__destruct();
	unset($rss);
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_rssfeed_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_rssfeed_gettitle(),
		"sheeticonid" => nxs_widgets_rssfeed_geticonid(),
		"sheethelp" => nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1764396204"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_rssfeed_getunifiedstylinggroup(),
		),
		"fields" => array
		(
		array( 
				"id" 					=> "wrapper_datasource_begin",
				"type" 					=> "wrapperbegin",
				"initial_toggle_state"	=> "closed",
				"label" 				=> nxs_l18n__("Datasource", "nxs_td"),
				"unistylablefield"		=> true
			),
		
			// URL
			array(
				"id"     			=> "url",
				"type"     			=> "input",
				"label"    			=> nxs_l18n__("Url", "nxs_td"),
			),
			
			array( 
				"id" 				=> "wrapper_daasource_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),
		
					// TITLE
			
			array( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
			),
			array
			(
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" => nxs_l18n__("Title goes here", "nxs_td"),
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title importance", "nxs_td"),
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
				"id" 				=> "top_info_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Top info color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id"     			=> "top_info_padding",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Top info padding", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("padding"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "icon",
				"type" 				=> "icon",
				"label" 			=> nxs_l18n__("Icon", "nxs_td"),
			),
			array(
				"id"     			=> "icon_scale",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Icon scale", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("icon_scale"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),
			array( 
				"id" 				=> "wrapper_items_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Searchcriteria", "nxs_td"),
			),	
			array(
				"id" 				=> "items_filter_maxcount",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Number of posts", "nxs_td"),
				"dropdown" 			=> array("1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9","10"=>"10","20"=>"20","30"=>"30","40"=>"40","50"=>"50","100"=>"100")
			),
			array( 
				"id" 				=> "wrapper_items_end",
				"type" 				=> "wrapperend"
			),
			array( 
				"id" 					=> "wrapper_output_begin",
				"type" 					=> "wrapperbegin",
				"initial_toggle_state"	=> "open",
				"label" 				=> nxs_l18n__("Item", "nxs_td"),
				"unistylablefield"	=> true
			),	

			array(
				"id" 				=> "item_title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title heading for item", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),	
				
		
			array(
				"id" 				=> "item_text_truncatelength",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Text max length", "nxs_td"),
				"dropdown" 			=> nxs_convertindexarraytoassociativearray(array("", "0","100","110","120","130","140","150","160","170","180","190","200","210","220","230","240","250","260","270","280","290","300","400","500","600")),
				"unistylablefield"	=> true
			),
						
			array(
				"id" 				=> "item_text_appendchars",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Abbreviation characters", "nxs_td"),
				"placeholder"		=> nxs_l18n__("[...]", "nxs_td"),
				"unistylablefield"	=> true
			),			
			array( 
				"id" 				=> "item_showdate",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show date", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "wrapper_advanceditems_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
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

function nxs_widgets_rssfeed_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_rssfeed_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title","text","button_text"));
	
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
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " ";

	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;
	if (
		$url == ""
	) {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Minimal: url", "nxs_td");
	}
	
	//
	//
	//
	
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
	
	if ($title_alignment == "center") { $top_info_title_alignment = "margin: 0 auto;"; } else
	if ($title_alignment == "right")  { $top_info_title_alignment = "margin-left: auto;"; } 
	
	// Title fontsize
	$title_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $title_fontsize);

	// Title height (across titles in the same row)
	// This function does not fare well with CSS3 transitions targeting "all"
	$heightiqprio = "p1";
	$title_heightiqgroup = "title";
  	$titlecssclasses = $title_fontsize_cssclass;
	$titlecssclasses = nxs_concatenateargswithspaces($titlecssclasses, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$title_heightiqgroup}");
	
	// Top info padding and color
	$top_info_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $top_info_color);
	$top_info_padding_cssclass = nxs_getcssclassesforlookup("nxs-padding-", $widgetmetadata["top_info_padding"]);
	
	// Icon scale
	$icon_scale_cssclass = nxs_getcssclassesforlookup("nxs-icon-scale-", $icon_scale);
		
	// Icon
	if ($icon != "") {$icon = '<span class="' . $icon . ' ' . $icon_scale_cssclass . '"></span>';}
	
	// Title
	$titlehtml = '<' . $title_heading . ' class="nxs-title ' . $title_alignment_cssclass . ' ' . $title_fontsize_cssclass . ' ' . $titlecssclasses . '">' . $title . '</' . $title_heading . '>';
	
	$destination_url = $url;	// links to RSS url
	// Linked title
	if ($destination_articleid != "") {
		$titlehtml = '<a href="' . $destination_url .'">' . $titlehtml . '</a>';
	} else if ($destination_url != "") {
		$titlehtml = '<a href="' . $destination_url .'" target="_blank">' . $titlehtml . '</a>';
	}
	
	// Applying link colors to title
	if ($top_info_color_cssclass == "") { 
		$titlehtml = '<div class="nxs-applylinkvarcolor">' . $titlehtml . '</div>'; 
	}
	
	//
	//
	//
	
	$rssargs = array();
	$rssargs["url"] = $url;
	
	// item_text_truncatelength
	
	$rssargs["show_summary"] = true;
	$rssargs["show_author"] = true;
	$rssargs["show_date"] = ($item_showdate != "");
	$rssargs["items"] = $items_filter_maxcount;
	$rssargs["item_text_appendchars"] = $item_text_appendchars;
	$rssargs["item_text_truncatelength"] = $item_text_truncatelength;
	$rssargs["item_showdate"] = $item_showdate;
	$rssargs["title_heading"] = $item_title_heading;
	
	$rsshtml = nxs_widgets_rssfeed_getoutput($rssargs);
	
	/*
	// get html for each part	
	//$htmltitle = nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, $destination_articleid, $destination_url);
	$htmltext = nxs_gethtmlfortext($text, $rssfeed_alignment, $rssfeed_showliftnote, $rssfeed_showdropcap, $wrappingelement, $rssfeed_heightiq);
	// $htmlforimage = nxs_gethtmlforimage($image_imageid, $image_border_width, $image_size, $image_alignment, $image_shadow, $image_alt, $destination_articleid, $destination_url, $image_title);
	$htmlforbutton = nxs_gethtmlforbutton($button_text, $button_scale, $button_color, $destination_articleid, $destination_url, $destination_target, $button_alignment, $destination_js);
	$htmlfiller = nxs_gethtmlforfiller();
	
	// Callout color
	$callout_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $callout_color);
	*/
	
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
		echo $titlehtml;
		echo $rsshtml;
		
		echo '<div class="nxs-clear"></div>';
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

function nxs_widgets_rssfeed_initplaceholderdata($args)
{
	extract($args);

	$args['button_color'] = "base2";
	$args['title_heading'] = "2";
	$args['button_scale'] = "1-0";
	$args['icon_scale'] = "1-0";
	$args['image_size'] = "c@1-0";
	
	$args['title_heightiq'] = "true";
	$args['rssfeed_heightiq'] = "true";

	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_rssfeed_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
