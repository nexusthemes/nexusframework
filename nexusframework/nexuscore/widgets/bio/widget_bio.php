<?php

function nxs_widgets_bio_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_bio_gettitle() {
	return nxs_l18n__("bio", "nxs_td");
}

// Unistyling
function nxs_widgets_bio_getunifiedstylinggroup() {
	return "biowidget";
}

// Unicontent
function nxs_widgets_bio_getunifiedcontentgroup() {
	return "biowidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_bio_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" 	 	=> nxs_widgets_bio_gettitle(),
		"sheeticonid" 	 	=> nxs_widgets_bio_geticonid(),
		"sheethelp" 	 	=> nxs_l18n__("http://nexusthemes.com/bio-widget/"),
		"unifiedstyling" 	=> array ("group" => nxs_widgets_bio_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_bio_getunifiedcontentgroup(),),
		"fields" => array
		(
			// TITLE
			
			array( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array
			( 
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
				"label" 			=> nxs_l18n__("Title importance", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_alignment",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_halignment"),
				"unistylablefield"	=> true
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
			),

			array
			( 
				"id" 				=> "image_imageid",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Choose image", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to upload an image for your bio profile use this option.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),			
			array( 
				"id" 				=> "image_shadow",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Image shadow", "nxs_td"),
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
				"id" 				=> "image_border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Border size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_width"),
				"unistylablefield"	=> true
			),	

			array( 
				"id" 				=> "wrapper_image_end",
				"type" 				=> "wrapperend"
			),
			
			// GENERAL PERSON INFORMATION
			
			array( 
				"id" 				=> "wrapper_main_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("General person information", "nxs_td"),
			),
			array(
				"id" 				=> "subtitle_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Subtitle headings", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "person",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Person", "nxs_td"),
				"placeholder"		=> nxs_l18n__("John Doe", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Place the content here, most likely this will be a person's name.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "line1",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Line 1", "nxs_td"),
				"placeholder"		=> nxs_l18n__("johndoe.com", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Place the subcontent here, most likely this will be a person's website.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "line2",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Line 2", "nxs_td"),
				"placeholder"		=> nxs_l18n__("012 34567890", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Place the subcontent here, use this field for telephone numbers or something.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Article link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an article within your site.", "nxs_td"),
			),
			array(
				"id" 				=> "person_destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Person URL", "nxs_td"),
				"placeholder"		=> nxs_l18n__("http://www.johndoe.com", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("You can link the line1 to any url", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "line1_destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Line 1 URL", "nxs_td"),
				"placeholder"		=> nxs_l18n__("http://www.johndoe.com", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("You can link the line1 to any url", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "line2_destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Line 2 URL", "nxs_td"),
				"placeholder"		=> nxs_l18n__("http://www.johndoe.com", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("You can link the line2 to any url", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array
			(
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),

			// SOCIAL ACCOUNTS

			array( 
				"id" 				=> "wrapper_icons_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Social accounts", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),

			array(
				"id" 				=> "rss",
				"type" 				=> "input",
				"label" 			=> "RSS link",
				"placeholder" 		=> nxs_l18n__("Use full url", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to place a link to the RSS feed, place it here. Use the full url!", "nxs_td"),
				"unicontentablefield" => true,
			),
			array(
				"id" 				=> "twitter",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Twitter link", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Use full url", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to place a link to the Twitter account, place it here. Don't use the full url!", "nxs_td"),
				"unicontentablefield" => true,
			),
			array(
				"id" 				=> "facebook",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Facebook link", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Use full url", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to place a link to the Facebook account, place it here. Don't use the full url!", "nxs_td"),
				"unicontentablefield" => true,
			),
			array(
				"id" 				=> "linkedin",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("LinkedIn link", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Use full url", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to place a link to the Facebook account, place it here. Don't use the full url!", "nxs_td"),
				"unicontentablefield" => true,
			),
			array(
				"id" 				=> "google",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Google+ link", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Use full url", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to place a link to the Google+ account, place it here. Don't use the full url!", "nxs_td"),
				"unicontentablefield" => true,
			),
			array(
				"id" 				=> "youtube",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Youtube link", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Use full url", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to place a link to the Youtube account, place it here. Don't use the full url!", "nxs_td"),
				"unicontentablefield" => true,
			),
			array(
				"id" 				=> "skypechat",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Skype account (chat)", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to place a link to your Skype (chat) account, place it here.", "nxs_td"),
				"unicontentablefield" => true,
			),
			array(
				"id" 				=> "emailaddress",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Send e-mail", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to link to your e-mail address, place it here.", "nxs_td"),
				"unicontentablefield" => true,
			),
			array( 
				"id" 				=> "icon_rss",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Custom RSS icon", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("With this option you can upload a custom image for the RSS icon.", "nxs_td"),
				"unicontentablefield" => true,
			),
			array( 
				"id" 				=> "icon_twitter",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Custom Twitter icon", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("With this option you can upload a custom image for the Twitter icon.", "nxs_td"),
				"unicontentablefield" => true,
			),
			array( 
				"id" 				=> "icon_facebook",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Custom Facebook icon", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("With this option you can upload a custom image for the Facebook icon.", "nxs_td"),
				"unicontentablefield" => true,
			),
			array( 
				"id" 				=> "icon_linkedin",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Custom LinkedIn icon", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("With this option you can upload a custom image for the LinkedIn icon.", "nxs_td"),
				"unicontentablefield" => true,
			),
			array( 
				"id" 				=> "icon_google",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Custom Google+ icon", "nxs_td"),
				"toltip" 			=> nxs_l18n__("With this option you can upload a custom image for the Google+ icon.", "nxs_td"),
				"unicontentablefield" => true,
			),	
			array( 
				"id" 				=> "icon_youtube",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Custom Youtube icon", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("With this option you can upload a custom image for the Youtube icon.", "nxs_td"),
				"unicontentablefield" => true,
			),	
			array( 
				"id" 				=> "icon_skypechat",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Custom Skype chat icon", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("With this option you can upload a custom image for the Skype chat icon.", "nxs_td"),
				"unicontentablefield" => true,
			),	
			array( 
				"id" 				=> "icon_emailaddress",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Custom email icon", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("With this option you can upload a custom image for the email icon.", "nxs_td"),
				"unicontentablefield" => true,
			),	
			array( 
				"id" 				=> "use_icon",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Use simple icons", "nxs_td"),
				"unistylablefield"	=> true
			),	
			
			array( 
				"id" 				=> "wrapper_icons_end",
				"type" 				=> "wrapperend"
			),				
				
			// TEXT
				
			array
			(
				"id" 				=> "wrapper_text_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Text", "nxs_td"),
				"initial_toggle_state"	=> "closed"
			),
			
			array(
				"id" 				=> "text",
				"type" 				=> "tinymce",
				"label" 			=> nxs_l18n__("Text", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Text goes here", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "text_alignment",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Text alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("text_halignment"),
				"unistylablefield"	=> true
			),
			
			array( 
			"id" 					=> "wrapper_image_end",
			"type" 					=> "wrapperend"
			),
			
			// MISCELLANEOUS

			array( 
				"id" 				=> "wrapper_headingadvanced_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Miscellaneous", "nxs_td"),
				"initial_toggle_state"	=> "closed",
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "title_heightiq",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align titles", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's title will participate in the title alignment of other partipating widgets in this row", "nxs_td"),
				"unistylablefield"	=> true
			),
			/*array(
				"id" 				=> "header_vertical_alignment",
				"type" 				=> "select",
				"label" 			=> "Header vertical alignment",
				"dropdown" 			=> array
				(
					"top"		=> nxs_l18n__("Top", "nxs_td"),
					"bottom"	=> nxs_l18n__("Bottom", "nxs_td")
				),
				"tooltip" 			=> nxs_l18n__("The complete top information can be swapped with the textual information with this option.", "nxs_td"),
				"unistylablefield"	=> true
			),*/
			
			array( 
				"id" 				=> "wrapper_headingadvanced_end",
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

function nxs_widgets_bio_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);

	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));

	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// blend unistyle properties
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {		
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_bio_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);	
	}
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_bio_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Localize atts
	$mixedattributes = nxs_localization_localize($mixedattributes);
	
	// Lookup atts
	$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title","line1","line2","text","line1_destination_url","line2_destination_url"));
	
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
	$person == "" &&
	nxs_has_adminpermissions()) {
		$shouldrenderalternative = true;
	}
	
	// Link color
	$ph_linkcolorvar = nxs_getcssclassesforlookup("nxs-linkcolorvar-", $widgetmetadata["ph_linkcolorvar"]);
	
	// Article link
	$destination_articleid = nxs_geturl_for_postid($destination_articleid);
	
	
	// RSS
	// If the accountname is set and there's no custom icon
	if ($rss != "" && $icon_rss == "") {
		
		$rss_url = '<a href="' . $rss . '" target="_new" class="nxs-social-rss" ><li></li></a>';
	
	// If both the accountname and a custom icon is set
	} else if ($rss != "" && $icon_rss != "") {
	
		$imagemetadata= wp_get_attachment_image_src($icon_rss, 'full', true);
		
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$rss_imageurl 		= $imagemetadata[0];
		$rss_imageurl = nxs_img_getimageurlthemeversion($rss_imageurl);
		$rss_imagewidth 	= $imagemetadata[1] . "px";
		$rss_imageheight 	= $imagemetadata[2] . "px";
	
		$rss_url = '
			<a href="' . $rss . '" target="_new" style="width: ' . $rss_imagewidth . '; height: ' . $rss_imageheight . ';">
				<li style="background: url(' . $rss_imageurl . ') no-repeat; width: ' . $rss_imagewidth . '; height: ' . $rss_imageheight . ';"></li>
			</a>';	
	}
	
	// TWITTER
	// If the accountname is set and there's no custom icon
	if ($twitter != "" && $icon_twitter == "") {
		
		$twitter_url = '<a href="' . $twitter . '" target="_new" class="nxs-social-twitter" ><li></li></a>';
	
	// If both the accountname and a custom icon is set
	} else if ($twitter != "" && $icon_twitter != "") {
	
		$imagemetadata= wp_get_attachment_image_src($icon_twitter, 'full', true);
		
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$twitter_imageurl 		= $imagemetadata[0];
		$twitter_imageurl = nxs_img_getimageurlthemeversion($twitter_imageurl);
		$twitter_imagewidth 	= $imagemetadata[1] . "px";
		$twitter_imageheight 	= $imagemetadata[2] . "px";
	
		$twitter_url = '
			<a href="' . $twitter . '" target="_new" style="width: ' . $twitter_imagewidth . '; height: ' . $twitter_imageheight . ';">
				<li style="background: url(' . $twitter_imageurl . ') no-repeat; width: ' . $twitter_imagewidth . '; height: ' . $twitter_imageheight . ';"></li>
			</a>';	
	}
	
	// FACEBOOK
	// If the accountname is set and there's no custom icon
	if ($facebook != "" && $icon_facebook == "") {
		
		$facebook_url = '<a href="' . $facebook . '" target="_new" class="nxs-social-facebook" ><li></li></a>';
	
	// If both the accountname and a custom icon is set
	} else if ($facebook != "" && $icon_facebook != "") {
	
		$imagemetadata= wp_get_attachment_image_src($icon_facebook, 'full', true);
		
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$facebook_imageurl 		= $imagemetadata[0];
		$facebook_imageurl = nxs_img_getimageurlthemeversion($facebook_imageurl);
		$facebook_imagewidth 	= $imagemetadata[1] . "px";
		$facebook_imageheight	= $imagemetadata[2] . "px";	
	
		$facebook_url = '
			<a href="' . $facebook . '" target="_new" style="width: ' . $facebook_imagewidth . '; height: ' . $facebook_imageheight . ';">
				<li style="background: url(' . $facebook_imageurl . ') no-repeat; width: ' . $facebook_imagewidth . '; height: ' . $facebook_imageheight . ';"></li>
			</a>';	
	}
	
	// LINKEDIN
	// If the accountname is set and there's no custom icon
	if ($linkedin != "" && $icon_linkedin == "") {
		
		$linkedin_url = '<a href="' . $linkedin . '" target="_new" class="nxs-social-linkedin" ><li></li></a>';
	
	// If both the accountname and a custom icon is set
	} else if ($linkedin != "" && $icon_linkedin != "") {
	
		$imagemetadata= wp_get_attachment_image_src($icon_linkedin, 'full', true);
		
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$linkedin_imageurl 		= $imagemetadata[0];
		$linkedin_imageurl = nxs_img_getimageurlthemeversion($linkedin_imageurl);
		$linkedin_imagewidth 	= $imagemetadata[1] . "px";
		$linkedin_imageheight 	= $imagemetadata[2] . "px";	
	
		$linkedin_url = '
			<a href="' . $linkedin . '" target="_new" style="width: ' . $linkedin_imagewidth . '; height: ' . $linkedin_imageheight . ';">
				<li style="background: url(' . $linkedin_imageurl . ') no-repeat; width: ' . $linkedin_imagewidth . '; height: ' . $linkedin_imageheight . ';"></li>
			</a>';	
	}
	
	// GOOGLE+
	// If the accountname is set and there's no custom icon
	if ($google != "" && $icon_google == "") {
		
		$google_url = '<a href="' . $google . '" target="_new" class="nxs-social-google" ><li></li></a>';
	
	// If both the accountname and a custom icon is set
	} else if ($google != "" && $icon_google != "") {
	
		$imagemetadata= wp_get_attachment_image_src($icon_google, 'full', true);
		
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$google_imageurl 		= $imagemetadata[0];
		$google_imageurl = nxs_img_getimageurlthemeversion($google_imageurl);
		$google_imagewidth 	= $imagemetadata[1] . "px";
		$google_imageheight = $imagemetadata[2] . "px";	
	
		$google_url = '
			<a href="' . $google . '" target="_new" style="width: ' . $google_imagewidth . '; height: ' . $google_imageheight . ';">
				<li style="background: url(' . $google_imageurl . ') no-repeat; width: ' . $google_imagewidth . '; height: ' . $google_imageheight . ';"></li>
			</a>';	
	}
	
	// YOUTUBE
	// If the accountname is set and there's no custom icon
	if ($youtube != "" && $icon_youtube == "") {
		
		$youtube_url = '<a href="' . $youtube . '" target="_new" class="nxs-social-youtube" ><li></li></a>';
	
	// If both the accountname and a custom icon is set
	} else if ($youtube != "" && $icon_youtube != "") {
	
		$imagemetadata= wp_get_attachment_image_src($icon_youtube, 'full', true);
		
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$youtube_imageurl 		= $imagemetadata[0];
		$youtube_imageurl = nxs_img_getimageurlthemeversion($youtube_imageurl);
		$youtube_imagewidth 	= $imagemetadata[1] . "px";
		$youtube_imageheight 	= $imagemetadata[2] . "px";	
	
		$youtube_url = '
			<a href="' . $youtube . '" target="_new" style="width: ' . $youtube_imagewidth . '; height: ' . $youtube_imageheight . ';">
				<li style="background: url(' . $youtube_imageurl . ') no-repeat; width: ' . $youtube_imagewidth . '; height: ' . $youtube_imageheight . ';"></li>
			</a>';	
	}
	
	// EMAIL 
	
	// SEND EMAIL 
	// If the emailaddress is set and there's no custom icon
	if ($emailaddress != "" && $icon_emailaddress == "") {
		
		$emailaddress_url = '<a target="_blank" href="mailto:' . $emailaddress . '" class="nxs-social-emailaddress" ><li></li></a>'; 
	
	// If both the emailaddress and a custom icon is set
	} else if ($emailaddress != "" && $icon_emailaddress != "") {
	
		$imagemetadata= wp_get_attachment_image_src($icon_emailaddress, 'full', true);
		
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$emailaddress_imageurl 		= $imagemetadata[0];
		$emailaddress_imageurl = nxs_img_getimageurlthemeversion($emailaddress_imageurl);
		$emailaddress_imagewidth 	= $imagemetadata[1] . "px";
		$emailaddress_imageheight 	= $imagemetadata[2] . "px";	
	
		$emailaddress_url = '
			<a target="_blank" href="mailto:' . $emailaddress . '" style="width: ' . $emailaddress_imagewidth . '; height: ' . $emailaddress_imageheight . ';">
				<li style="background: url(' . $emailaddress_imageurl . ') no-repeat; width: ' . $emailaddress_imagewidth . '; height: ' . $emailaddress_imageheight . ';"></li>
			</a>';	
	}
	
	// SKYPE CHAT
	// If the accountname is set and there's no custom icon
	if ($skypechat != "" && $icon_skypechat == "") {
		
		$skypechat_url = '<a href="skype:' . $skypechat . '?chat" class="nxs-social-skypechat" ><li></li></a>'; 
	
	// If both the accountname and a custom icon is set
	} else if ($skypechat != "" && $icon_skypechat != "") {
	
		$imagemetadata= wp_get_attachment_image_src($icon_skypechat, 'full', true);
		
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$skypechat_imageurl 		= $imagemetadata[0];
		$skypechat_imageurl = nxs_img_getimageurlthemeversion($skypechat_imageurl);
		
		$skypechat_imagewidth 	= $imagemetadata[1] . "px";
		$skypechat_imageheight 	= $imagemetadata[2] . "px";	
	
		$skypechat_url = '
			<a href="skype:' . $skypechat . '?chat" style="width: ' . $skypechat_imagewidth . '; height: ' . $skypechat_imageheight . ';">
				<li style="background: url(' . $skypechat_imageurl . ') no-repeat; width: ' . $skypechat_imagewidth . '; height: ' . $skypechat_imageheight . ';"></li>
			</a>';	
	}
	
	
	
	// Social list
	if ($rss == "" && $twitter == "" && $facebook == "" && $linkedin == "" && $google == "" && $youtube == "" && $skypechat == "") {
		// do nothing
	} else {
		$social_list = '
			<ul class="nxs-social-list">'. 
				$rss_url . 
				$twitter_url . 
				$facebook_url . 
				$linkedin_url . 
				$google_url . 
				$youtube_url . 
				$skypechat_url . 
				$emailaddress_url . '
			</ul>
		';
	}	
	
	// Icon font
	if ($use_icon != "") {
		
		if ($rss != "") 		{ $rss = 			'<li><a target="_blank" href="' . $rss . '">		<span class="nxs-icon-rss"></span></a></li>'; }
		if ($twitter != "") 	{ $twitter = 		'<li><a target="_blank" href="' . $twitter . '">	<span class="nxs-icon-twitter-2"></span></a></li>'; }
		if ($facebook != "") 	{ $facebook = 		'<li><a target="_blank" href="' . $facebook . '">	<span class="nxs-icon-facebook"></span></a></li>'; }
		if ($linkedin != "") 	{ $linkedin = 		'<li><a target="_blank" href="' . $linkedin . '">	<span class="nxs-icon-linkedin"></span></a></li>'; }
		if ($googleplus != "") 	{ $googleplus = 	'<li><a target="_blank" href="' . $googleplus . '">	<span class="nxs-icon-google-plus"></span></a></li>'; }
		if ($youtube != "") 	{ $youtube = 		'<li><a target="_blank" href="' . $youtube . '">	<span class="nxs-icon-youtube"></span></a></li>'; }
		if ($skypechat != "") 	{ $skypechat = 		'<li><a href="skype:' . $skypechat . '?chat">		<span class="nxs-icon-skype"></span></a></li>'; }
		if ($emailaddress != "") 	{ $emailaddress = 		'<li><a target="_blank" href="mailto:' . $emailaddress . '">		<span class="nxs-icon-contact"></span></a></li>'; }
		
		if 		($halign == 'left') 	{ $alignment = ''; } 
		else if ($halign == 'center') 	{ $alignment = 'nxs-center'; } 
		else if ($halign == 'right') 	{ $alignment = 'nxs-float-right'; }

		$icon_font_list ='
			<div class="nxs-applylinkvarcolor ' . $alignment . '">	
				<ul class="icon-font-list">'
					. $rss  
					. $twitter
					. $facebook
					. $linkedin
					. $googleplus
					. $youtube
					. $skypechat
					. $emailaddress
					. '
				</ul>
			</div>
		';
	}
	
	// Type of list
	if 		($use_icon == "")	{$list = $social_list;}
	else if ($use_icon != "")	{$list = $icon_font_list;}
	
	// Wrapperheight
	if 		($image_size == 'c@1-0')	{$multiplier = 1;}
	else if ($image_size == 'c@1-5')	{$multiplier = 1.5;}
	else if ($image_size == 'c@2-0')	{$multiplier = 2;}
	
	$factor = 80;
	$wrapper_height = $factor * $multiplier;
	
	// Image link
	// if ($person_destination_url != "" && $image != "") { $image = '<a href="' . $person_destination_url .'" target="_blank">' . $image . '</a>'; } 
	
	// line1
	if ($line1 != "" && $line1_destination_url != "") 	{ $line1 = '<a href="' . $line1_destination_url . '" target="_blank">' . $line1 . '</a>'; }
	
	// line2
	if ($line2 != "" && $line2_destination_url != "") 	{ $line2 = '<a href="' . $line2_destination_url . '" target="_blank">' . $line2 . '</a>'; }
	
	// Title heading
	if ($subtitle_heading != "") { 
		$subtitle_heading = "h" . $subtitle_heading; 
	} 
	else { $subtitle_heading = "h4"; }
	
	
	// General top info
	if ($person != "" && $person_destination_url != "" && $destination_articleid == "") { 
		$person = '<a href="' . $person_destination_url . '" target="_blank">' . $person . '</a>'; 
	} else if ($person != "" && $destination_articleid != "") { 
		$person = '<a href="' . $destination_articleid . '" target="_self">' . $person . '</a>'; 
	}
	
	if ($person != "") { 
		$person = '<' . $subtitle_heading . ' class="nxs-title">' . $person . '</' . $subtitle_heading . '>'; 
	}
	if ($line1 != "") { $line1 = '
		<div class="nxs-clear nxs-margin-top5"></div>
		<' . $subtitle_heading . ' class="nxs-title">' . $line1 . '</' . $subtitle_heading . '>'; 
	}
	if ($line2 != "") { $line2 = '
		<div class="nxs-clear nxs-margin-top5"></div>
		<' . $subtitle_heading . ' class="nxs-title">' . $line2 . '</' . $subtitle_heading . '>'; 
	}
	
	$htmltitle = nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, "", "");
	
	// General top info
	if ($person != "") {
		$info = '	
		<div class="wrapper" style="height: ' . $wrapper_height . 'px;">
			<div class="wrapper-container">'. 
				$person .
				$line1 .
				$line2 .
				$list .
				'<div class="nxs-clear"></div>
			</div>
		</div>';
	}
		
	// Variabele needed to render tinymce paragraphs the right way
	$wrappingelement = "div";
	
	// Image alignment
	$image_alignment = "left";
	
	// Default HTML
	$htmltext = nxs_gethtmlfortext($text, $text_alignment, $text_showliftnote, $text_showdropcap, $wrappingelement, $text_heightiq);
	$htmlforimage = nxs_gethtmlforimage($image_imageid, $image_border_width, $image_size, $image_alignment, $image_shadow, $image_alt, $image_title);
	
	// FILLER
	if ($htmltext != "") {
		$content_filler = '<div class="nxs-clear nxs-padding-top15"></div>';
	} else {
		$content_filler = '<div class="nxs-clear"></div>';
	}
	
	$bio_result = '
		<div class="header-wrapper">' .
			$htmlforimage .
			$afterimagefiller . 
			$info .
		'</div>' .
		$content_filler .
		$htmltext;
	
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) {
		
		nxs_renderplaceholderwarning(nxs_l18n__("Missing input", "nxs_td")); 
		
	} else {	
		
		echo $htmltitle;
		if ($htmltitle != "") 
		{
			$htmlfiller = nxs_gethtmlforfiller();
			echo $htmlfiller;
		}
		
		echo '
		<div class="' . $ph_linkcolorvar . ' ">
			<div class="nxs-applylinkvarcolor">';
				echo $bio_result;
				echo '
			</div>
		</div>';
	} 
	
	/* ------------------------------------------------------------------------------------------------- */
	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = ob_get_contents();
	ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	return $result;
}

function nxs_widgets_bio_initplaceholderdata($args)
{
	extract($args);

	$args['image_size'] = "c@1-0";
	$args['image_size'] = "c@1-0";
	$args['header_vertical_alignment'] = "top";
	$args['title_heading'] = "2";
	$args['title_heightiq'] = "true";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_bio_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}


?>
