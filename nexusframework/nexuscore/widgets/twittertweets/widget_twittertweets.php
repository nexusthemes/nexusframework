<?php

// probably should be moved to the twitter widget?
//require_once(NXS_FRAMEWORKPATH . '/plugins/display-tweets-php/includes/Twitter/api.php');

//
function nxs_widgets_twittertweets_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_twittertweets_gettitle() {
	return nxs_l18n__("Twitter tweets", "nxs_td");
}

// Unistyle
function nxs_widgets_twittertweets_getunifiedstylinggroup() {
	return "twittertweetswidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_twittertweets_getsupporturl()
{
	$result = "https://www.wpsupporthelp.com/wordpress-questions/twitter-widget-wordpress-questions-178/";
	return $result;
}

// Define the properties of this widget
function nxs_widgets_twittertweets_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$supporturl = nxs_widgets_twittertweets_getsupporturl();

	$options = array
	(
		"sheettitle" => nxs_widgets_twittertweets_gettitle(),
		"sheeticonid" => nxs_widgets_twittertweets_geticonid(),
		"supporturl" => $supporturl,
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_twittertweets_getunifiedstylinggroup(),
		),	
		"fields" => array
		(
			// TITLE
			
			array
			( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Support", "nxs_td"),
			),
			
			array
			(
				"id" 				=> "supportsection",
				"type" 				=> "custom",
				"custom"	=> "<div><a class='nxsbutton' href='{$supporturl}'>Integration</a></div>",
				"label" 			=> nxs_l18n__("Support", "nxs_td"),
			),
			
			array
			( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),

		),
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */


function nxs_widgets_twittertweets_render_webpart_render_htmlvisualization($args)
{
	// Importing variables
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// Unistyle
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_twittertweets_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Widget specific variables
	extract($mixedattributes);

	global $nxs_global_placeholder_render_statebag;
	
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

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-tweets ";
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	$supporturl = nxs_widgets_twittertweets_getsupporturl();
	
	nxs_renderplaceholderwarning("<a target='_blank' href='{$supporturl}'>See Twitter Integration Support</a>"); 
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	?>
	<?php
	
	/* ------------------------------------------------------------------------------------------------- */
	
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	
	// data protection handling
	if (true)
	{
		$activity = "nexusframework:widget_twittertweets";
		if (!nxs_dataprotection_isactivityonforuser($activity))
		{
			// not allowed
			$result["html"] = "";
		}
	}
	
	return $result;
}

function nxs_widgets_twittertweets_initplaceholderdata($args)
{
	extract($args);

  $meta = nxs_getsitemeta();
	$twitteraccount = $meta["twitteraccount"];
	
	$args['twitteraccount'] = $twitteraccount;
	$args['title_heading'] = '2';
	$args['title_alignment'] = 'l';
	
	nxs_widgets_twittertweets_updateplaceholderdata($args);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_widgets_twittertweets_updateplaceholderdata($args)
{
	extract($args);
	
	$temp_array = array();
	
	// its required to also set the 'type' (used when dragging an item from the toolbox to existing placeholder)
	$temp_array['type'] = 'twittertweets';

	// placeholder specifieke data
//$temp_array['twitteraccount'] = $twitteraccount;
	
	$args['title_heightiq'] = "true";	
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_twittertweets_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $temp_array);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_dataprotection_nexusframework_widget_twittertweets_getprotecteddata($args)
{
	$result = array
	(
		"controller_label" => "Twitter Tweets",
		"subactivities" => array
		(
		),
		"dataprocessingdeclarations" => array	
		(
			// "See the terms https://gdpr.twitter.com/en.html"
		
			array
			(
				"use_case" => "(belongs_to_whom_id) can browse a page of the website owned by the (controller) that renders tweets using the TwitterTweets widget of the framework",
				"what" => "IP address of the (belongs_to_whom_id) as well as 'Request header fields' send by browser of ((belongs_to_whom_id)) (https://en.wikipedia.org/wiki/List_of_HTTP_header_fields#Request_fields)",
				"belongs_to_whom_id" => "website_visitor", // (has to give consent for using the "what")
				"controller" => "website_owner",	// who is responsible for this?
				"controller_options" => nxs_dataprotection_factory_getenableoptions("all"),
				"data_processor" => "Twitter",	// the name of the data_processor or data_recipient
				"data_retention" => "See https://gdpr.twitter.com/en.html",
				"program_lifecycle_phase" => "compiletime",
				"why" => "Not applicable (because this is a compiletime declaration)",
				"security" => "The data is transferred over a secure https connection. Security is explained in more detail here; See https://gdpr.twitter.com/en.html",
			),
		),
		"status" => "final",
	);
	return $result;
}