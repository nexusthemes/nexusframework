<?php

// probably should be moved to the twitter widget?
require_once(NXS_FRAMEWORKPATH . '/plugins/display-tweets-php/includes/Twitter/api.php');

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

function nxs_widgets_twittertweets_connection($optionvalues, $args, $runtimeblendeddata) 
{
	nxs_ob_start();
	extract($optionvalues);
	?>
	<div>
		<?php
		if (nxs_twitter_isconnected())
		{
			$url = nxs_geturl_home();
			$url = nxs_addqueryparametertourl($url, "twitter", "disconnect");
			$url = nxs_addqueryparametertourl($url, "returnurl", nxs_geturlcurrentpage());
			?>
			<a class='nxsbutton1 nxs-float-right' href='<?php echo $url; ?>'>Disconnect</a>
			Connected
			<?php
		}
		else
		{
			$url = nxs_geturl_home();
			$url = nxs_addqueryparametertourl($url, "twitter", "login");
			$url = nxs_addqueryparametertourl($url, "returnurl", nxs_geturlcurrentpage());
			?>
			<a class='nxsbutton2 blink nxs-float-right' href='<?php echo $url; ?>'>Connect</a>
			Disconnected
			<?php
		}
		?>
	</div>
  <div class="nxs-clear"></div>
  <?php
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_twittertweets_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$options = array
	(
		"sheettitle" => nxs_widgets_twittertweets_gettitle(),
		"sheeticonid" => nxs_widgets_twittertweets_geticonid(),
		"supporturl" => "https://www.wpsupporthelp.com/wordpress-questions/twitter-widget-wordpress-questions-178/",
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_twittertweets_getunifiedstylinggroup(),
		),	
		"fields" => array
		(
			// TITLE
			
			array( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"initial_toggle_state"	=> "closed-if-empty",
				"initial_toggle_state_id" => "title",
			),
			
			array(
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" => nxs_l18n__("Title goes here", "nxs_td"),
				"unicontentablefield" => true,
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
				"label" 			=> nxs_l18n__("Title size", "nxs_td"),
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
				"unicontentablefield" => true,
			),
			array(
				"id"     			=> "icon_scale",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Icon size", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("icon_scale"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),
			
			// CONFIGURATION
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Configuration", "nxs_td"),
			),
			
			array
			( 
				"id" 				=> "connection_status",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_widgets_twittertweets_connection",
				"label" 			=> nxs_l18n__("Connection status", "nxs_td"),
			),
			array(
				"id" 				=> "twitteraccount",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Twitter account", "nxs_td"),
			),		
			array(
				"id" 				=> "maxtweets",
				"type" 				=> "select",
				"dropdown" 			=> array(
					"1"=>"1", 
					"2"=>"2", 
					"3"=>"3", 
					"4"=>"4", 
					"5"=>"5", 
					"6"=>"6", 
					"7"=>"7", 
					"8"=>"8", 
					"9"=>"9", 
					"10"=>"10",
					"11"=>"11", 
					"12"=>"12", 
					"13"=>"13", 
					"14"=>"14", 
					"15"=>"15", 
					"16"=>"16", 
					"17"=>"17", 
					"18"=>"18", 
					"19"=>"19", 
					"20"=>"20"
					),
				"label" 			=> nxs_l18n__("Number of tweets", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "datetime_format",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Datetime format", "nxs_td"),
			),	
			// 
			
			array( 
				"id" 				=> "wrapper_end",
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

// kudus to http://blog.jacobemerick.com/web-development/parsing-twitter-feeds-with-php/
function nxs_widgets_twittertweets_twitterifyv2($tweet)
{
	$hashtag_link_pattern = '<a href="http://twitter.com/search?q=%%23%s&src=hash" rel="nofollow" target="_blank">#%s</a>';
	$url_link_pattern = '<a href="%s" rel="nofollow" target="_blank" title="%s">%s</a>';
	$user_mention_link_pattern = '<a href="http://twitter.com/%s" rel="nofollow" target="_blank" title="%s">@%s</a>';
	$media_link_pattern = '<a href="%s" rel="nofollow" target="_blank" title="%s">%s</a>';
	
	$text = $tweet->text;

	$entity_holder = array();

	foreach($tweet->entities->hashtags as $hashtag)
	{
		$entity = new stdclass();
		$entity->start = $hashtag->indices[0];
		$entity->end = $hashtag->indices[1];
		$entity->length = $hashtag->indices[1] - $hashtag->indices[0];
		$entity->replace = sprintf($hashtag_link_pattern, strtolower($hashtag->text), $hashtag->text);

		$entity_holder[$entity->start] = $entity;
	}

	foreach($tweet->entities->urls as $url)
	{
		$entity = new stdclass();
		$entity->start = $url->indices[0];
		$entity->end = $url->indices[1];
		$entity->length = $url->indices[1] - $url->indices[0];
		$entity->replace = sprintf($url_link_pattern, $url->url, $url->expanded_url, $url->display_url);

		$entity_holder[$entity->start] = $entity;
	}

	foreach($tweet->entities->user_mentions as $user_mention)
	{
		$entity = new stdclass();
		$entity->start = $user_mention->indices[0];
		$entity->end = $user_mention->indices[1];
		$entity->length = $user_mention->indices[1] - $user_mention->indices[0];
		$entity->replace = sprintf($user_mention_link_pattern, strtolower($user_mention->screen_name), $user_mention->name, $user_mention->screen_name);

		$entity_holder[$entity->start] = $entity;
	}

	foreach($tweet->entities->media as $media)
	{
		$entity = new stdclass();
		$entity->start = $media->indices[0];
		$entity->end = $media->indices[1];
		$entity->length = $media->indices[1] - $media->indices[0];
		$entity->replace = sprintf($media_link_pattern, $media->url, $media->expanded_url, $media->display_url);

		$entity_holder[$entity->start] = $entity;
	}

	krsort($entity_holder);
	foreach($entity_holder as $entity)
	{
		$text = mb_substr($text, 0, $entity->start).$entity->replace.mb_substr($text, $entity->start+$entity->length);
	}

	return $text;
}

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
	
	$shouldrenderalternative = false;
	if ($twitteraccount != "" && $twittersearchphrase != ""){
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Warning: provide the twitter account or the searchphrase, not both", "nxs_td");
	}
	
	// Turn on output buffering
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-tweets ";
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	// Title heading
	if ($title_heading != "") 	{ $title_heading = "h" . $title_heading; } else 
								{ $title_heading = "h1"; }

	// Title alignment
	$title_alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $title_alignment);
	
	if ($title_alignment == "center") { $top_info_title_alignment = "margin: 0 auto;"; } else
	if ($title_alignment == "right")  { $top_info_title_alignment = "margin-left: auto;"; } 
	
	// Title fontsize
	$title_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $title_fontsize);
	
	// Top info padding and color
	$top_info_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $top_info_color);
	$top_info_padding_cssclass = nxs_getcssclassesforlookup("nxs-padding-", $top_info_padding);
	
	// Icon scale
	$icon_scale_cssclass = nxs_getcssclassesforlookup("nxs-icon-scale-", $icon_scale);
		
	// Icon
	if ($icon != "") {$icon = '<span class="'.$icon.' '.$icon_scale_cssclass.'"></span>';}
	
	if ($title_schemaorgitemprop != "") {
		// bijv itemprop="name"
		$title_schemaorg_attribute = 'itemprop="' . $title_schemaorgitemprop . '"';
	} else {
		$title_schemaorg_attribute = "";	
	}		
	
	// Title
	$titlehtml = '<'.$title_heading.' ' . $title_schemaorg_attribute . ' class="nxs-title '.$title_alignment_cssclass.' '.$title_fontsize_cssclass.' '.$titlecssclasses.'">'.$title.'</'.$title_heading.'>';
	
	
	
	
	// Filler
	$htmlfiller = nxs_gethtmlforfiller();
	
	// Max tweets
	$tweets = nxs_twitter_gettweets($twitteraccount, $maxtweets);
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	

		if ($shouldrenderalternative) {
			if ($alternativehint == "") {
				$alternativehint = nxs_l18n__("Missing input", "nxs_td");
			}
			nxs_renderplaceholderwarning($alternativehint); 
		} else {
			
			echo '<div ' .$class . '>';
			
				/* TITLE
				---------------------------------------------------------------------------------------------------- */
				if ($icon == "" && $title == "") {
					// nothing to show
				} else if (($top_info_padding_cssclass != "") || ($icon != "") || ($top_info_color_cssclass != "")) {
					 
					// Icon title
					echo '
					<div class="top-wrapper nxs-border-width-1-0 '.$top_info_color_cssclass.' '.$top_info_padding_cssclass.'">
						<div class="nxs-table" style="'.$top_info_title_alignment.'">';
						
							// Icon
							echo $icon;
							
							// Title
							if ($title != "") {	echo $titlehtml; }
							
							echo '
						</div>
					</div>';
				
				} else {
				
					// Default title
					if ($title != "") {
						echo $titlehtml;
					}
				
				}
				
				echo $htmlfiller; 
		
				/* TWEETS
				---------------------------------------------------------------------------------------------------- */
				
				if ($twitteraccount != "" || $twittersearchphrase != "") {
					
					echo '<div id="tweets_' . $placeholderid . '">';

						if (nxs_twitter_isconnected()) {
							
							foreach ($tweets as $currenttweetobj)
							{
								$currenttweet =  (array) $currenttweetobj;
								$currenttweetownerobject = $currenttweet["user"];
								$currenttweetowner =  (array) $currenttweetownerobject;
							
								extract($currenttweet, EXTR_PREFIX_ALL, "curtweet");
								extract($currenttweetowner, EXTR_PREFIX_ALL, "cuttweetowner");

								//$datetime_format = apply_filters( 'displaytweets_datetime_format', "M j" );
								if ($datetime_format == "")
								{
									// empty defaults to the format in the backend
									$datetime_format = get_option("date_format");
								}
								$tweetdate = apply_filters( 'displaytweets_posted_since', date_i18n( $datetime_format , strtotime($curtweet_created_at)));
								
								echo ' 
								
								<div class="twitter-content">';
								
									if (is_ssl())
									{
										$imgurl = $cuttweetowner_profile_image_url_https;
									}
									else
									{
										$imgurl = $cuttweetowner_profile_image_url;
									}
								
									// Image
									echo '<img src=' . $imgurl . ' />  ';
									
									// Name
									echo '
										<p class="nxs-default-p nxs-padding-bottom nxs-inline">
											<span class="nxs-twitter-name">' . $cuttweetowner_name . '</span>
										</p>';
									
									// Screenname
									echo '
										<p class="nxs-default-p nxs-padding-bottom nxs-inline">
											<span class="nxs-twitter-screenname nxs-applylinkvarcolor">
												<a href="https://www.twitter.com/' . $cuttweetowner_screen_name . '" target="_new">&nbsp;&#64;' . $cuttweetowner_screen_name . '</a>
											</span>
										</p>';
									
									// Date
									echo '
										<p class="nxs-default-p nxs-padding-bottom nxs-inline">
											<span class="nxs-twitter-date">' . $tweetdate . '</span>
										</p>';			
																				
									echo '<div class="nxs-clear"></div>';
									
									// turns text into text with links
									$curtweet_text = nxs_widgets_twittertweets_twitterifyv2($currenttweetobj);
									
									// Text
									echo '
										<p class="nxs-default-p nxs-padding-bottom0">
											<span class="nxs-applylinkvarcolor">' . $curtweet_text . '</span>
										</p>';
										
								echo '</div> <!-- END twitter-content -->';
							}
						} else {
							nxs_renderplaceholderwarning(nxs_l18n__("Disconnected. Click on the Twitter icon of this widget to connect.", "nxs_td"));
						}
				echo '</div>';
			} else {
				nxs_renderplaceholderwarning(nxs_l18n__("Not linked[nxs:warning]", "nxs_td"));
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

	$temp_array['twitteraccount'] = $twitteraccount;
	$temp_array['title_alignment'] = $title_alignment;
	$temp_array['title_heading'] = $title_heading;
	$temp_array['title'] = $title;
	$temp_array['twitteraccount'] = "@nexusthemes";
	$temp_array['maxtweets'] = "3";
	
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