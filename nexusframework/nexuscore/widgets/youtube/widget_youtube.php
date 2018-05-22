<?php

function nxs_widgets_youtube_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_youtube_gettitle() {
	return nxs_l18n__("Youtube[nxs:widgettitle]", "nxs_td");
}

// Unistyle
function nxs_widgets_youtube_getunifiedstylinggroup() {
	return "youtubewidget";
}

// Unicontent
function nxs_widgets_youtube_getunifiedcontentgroup() {
	return "youtubewidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_youtube_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_youtube_gettitle(),
		"sheeticonid" 		=> nxs_widgets_youtube_geticonid(),
		"supporturl" => "https://www.wpsupporthelp.com/wordpress-questions/video-youtube-yt-wordpress-questions-28/",
		"unifiedstyling" 	=> array("group" => nxs_widgets_youtube_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array("group" => nxs_widgets_youtube_getunifiedcontentgroup(),),
		"fields" => array
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
		
			// TITLE
			
			array
			( 
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
				"placeholder" 		=> nxs_l18n__("Title goes here", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array
      (
				"id" 					=> "title_lookuppicker",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_generic_modeltaxfieldpicker_popupcontent",
			),
			
			array
			(
				"id" 				=> "title_postprocessor",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title max length", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@empty@@@" => "None",
					"truncateall" => "Truncate all",
				),
			"unistylablefield"	=> true
			),
			array
			(
				"id" 				=> "title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title importance", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_fontzen",
				"type" 				=> "fontzen",
				"label" 			=> nxs_l18n__("Title fontzen", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
				"unistylablefield"	=> true,
				"mobile_action_toggles" => ".nxs-viewport-dependent",
			),
			array(
				"id" 				=> "title_alignment_tablet",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("", "nxs_td"),
				"unistylablefield"	=> true,
				"display" => "noneifempty",
				"fortablets" => true,
				"enable_deselect" => true,
			),
			array(
				"id" 				=> "title_alignment_mobile",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("", "nxs_td"),
				"unistylablefield"	=> true,
				"display" => "noneifempty",
				"formobiles" => true,
				"enable_deselect" => true,
			),
			array(
				"id" 				=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Override title fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
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
				"label"    			=> nxs_l18n__("Icon scale", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("icon_scale"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),
			
			// VIDEO
			array( 
				"id" 				=> "wrapper_youtube_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("youtube settings", "nxs_td"),
			),
		
			array(
				"id" 				=> "videoid_visualization",
				"altid" 			=> "videoid",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_youtube_videoid_popupcontent",
				"label" 			=> nxs_l18n__("Video URL", "nxs_td"),
				"localizablefield"	=> true,
				"unicontentablefield" => true
			),
			
			array(
				"id" 				=> "videoid",
				"type" 				=> "input",
				// "visibility" 		=> "hidden",
				"label" 			=> nxs_l18n__("Video ID", "nxs_td"),
				"localizablefield"	=> true,
				"unicontentablefield" => true
			),
			
			array(
				"id" 				=> "language",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Transcript language", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("For example: en", "nxs_td"),
				"localizablefield"	=> true,
			),

			array( 
				"id" 				=> "autoplay",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Autoplay", "nxs_td"),
				"localizablefield"	=> true,
			),

			array(
				"id" 				=> "playstartsecs",
				"type" 				=> "input",
				"visibility" 		=> "hidden",
				"label" 			=> nxs_l18n__("Video ID", "nxs_td"),
				"localizablefield"	=> true,
			),

			array( 
				"id" 				=> "playstartsecs_visualization",
				"altid" 			=> "playstartsecs",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_youtube_playsecs_popupcontent",
				"label" 			=> nxs_l18n__("Play Start", "nxs_td"),
				"localizablefield"	=> true,
			),

			array(
				"id" 				=> "playendsecs",
				"type" 				=> "input",
				"visibility" 		=> "hidden",
				"label" 			=> nxs_l18n__("Video ID", "nxs_td"),
				"localizablefield"	=> true,
			),

			array( 
				"id" 				=> "playendsecs_visualization",
				"altid" 			=> "playendsecs",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_youtube_playsecs_popupcontent",
				"label" 			=> nxs_l18n__("Play End", "nxs_td"),
				"localizablefield"	=> true,
			),
			
			array( 
				"id" 				=> "wrapper_youtube_end",
				"type" 				=> "wrapperend"
			),
			
			// MISCELLANEOUS
			
			array( 
				"id" 				=> "wrapper_misc_begin",
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
			
			array( 
				"id" 				=> "wrapper_youtube_end",
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
        
function nxs_widgets_youtube_render_webpart_render_htmlvisualization($args)
{
	// Importing variables
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	$mixedattributes = array_merge($temp_array, $args);
	
	// Translate model magical fields
	if (true)
	{
		global $nxs_g_modelmanager;
		
		$combined_lookups = nxs_lookups_getcombinedlookups_for_currenturl();
		$combined_lookups = array_merge($combined_lookups, nxs_parse_keyvalues($mixedattributes["lookups"]));
		
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
		$magicfields = array("title", "videoid", "videoid_visualization");
		$translateargs = array
		(
			"lookup" => $combined_lookups,
			"items" => $mixedattributes,
			"fields" => $magicfields,
		);
		$mixedattributes = nxs_filter_translate_v2($translateargs);
	}
	
	extract($mixedattributes);
	
	global $nxs_doing_seo;
	global $nxs_seo_output;
	if ($nxs_doing_seo === true)
	{
		$nxs_seo_output = "https://www.youtube.com/watch?v=u9hyOQEwB4c";
	}
	
	if ($language != "")
	{
		// &hl=fr&cc_lang_pref=fr&cc_load_policy=1 
		$transcriptparameter = "&cc_load_policy=1&cc_lang_pref=" . $language . "&hl=" . $language . "&yt:cc=on";
	}
	$additionalparameters = "&vq=hd1080&rel=0";
	
	if ($playstartsecs != "")
	{
		$additionalparameters .= "&start=" . $playstartsecs;
	}
	if ($playendsecs != "")
	{
		$additionalparameters .= "&end=" . $playendsecs;
	}	
	if ($autoplay != "")
	{
		$additionalparameters .= "&autoplay=1";
	}
	else
	{
		$additionalparameters .= "&autoplay=0";
	}
	
	if 
	(
		nxs_stringstartswith($videoid, "http") || 
		nxs_stringstartswith($videoid, "https") ||
		false
	)
	{
		// when its a url pointing to youtube
		$parsedurl = parse_url($videoid, PHP_URL_QUERY);
		parse_str($parsedurl, $params);
		$videoid = $params["v"];
	}
	
	if ($videoid == "" || nxs_stringcontains($videoid, "{{") || nxs_stringcontains($videoid, "}}"))
	{
		nxs_ob_start();
		?>	
		<div class="nxs-border-dash nxs-runtime-autocellwidth nxs-runtime-autocellsize border-radius autosize-smaller nxs-hidewheneditorinactive">
			<div class='placeholder-warning'>
				<p>No video</p>
			</div>
		</div>
		<?php
		$html = nxs_ob_get_contents();
		nxs_ob_end_clean();
	
		$result["html"] =  $html;	
		$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
		return $result;
	}
	
	
	
	// fallback scenario
	if (nxs_has_adminpermissions())
	{
		if ($videoid == "")
		{
			
			$videoid = "B6cg4ZoUwVU";
			$videourl = "https://www.youtube.com/watch?v=" . $videoid;
		}
		
		$renderBeheer = true;
	}
	else
	{
		$renderBeheer = false;
	}
	
	if ($rendermode == "default")
	{
		if ($renderBeheer)
		{
			$shouldrenderhover = true;
		} 
		else
		{
			$shouldrenderhover = false;
		}
	}
	else if ($rendermode == "anonymous")
	{
		$shouldrenderhover = false;
	}
	else
	{
		echo "unsupported rendermode;" . $rendermode;
		die();
	}

	global $nxs_global_placeholder_render_statebag;
	
	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		$hovermenuargs = array();
		$hovermenuargs["postid"] = $postid;
		$hovermenuargs["placeholderid"] = $placeholderid;
		$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
		$hovermenuargs["enable_decoratewidget"] = false;
		$hovermenuargs["enable_deletewidget"] = true;
		$hovermenuargs["enable_deleterow"] = false;
		$hovermenuargs["metadata"] = $mixedattributes;
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	}
	
	if ($videoid == "")
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("No video set", "nxs_td");
	}
	
	// new implementation delegates rendering the title to the frontendframework
	$a = array
	(
		"title" => $title,
		"heading" => $title_heading,
		"align" => $title_alignment,
		"align_tablet" => $title_alignment_tablet,
		"align_mobile" => $title_alignment_mobile,
		"fontsize" => $title_fontsize,
		"heightiq" => "title",
		"destination_articleid" => $destination_articleid,
		"destination_url" => $destination_url,
		"destination_target" => $destination_target,
		"destination_relation" => $destination_relation,
		"shouldapplylinkvarcolor" => $shouldapplylinkvarcolor,
		// "microdata" => 
		"fontzen" => $title_fontzen,
	);
	$htmltitle = nxs_gethtmlfortitle_v4($a);

	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-youtube";

	?>

	<div <?php echo $class; ?>>
		<?php echo $htmltitle; ?>
		<?php if ($htmltitle != "") {
			?>
			<div class="nxs-clear nxs-filler"></div>
			<?php
		}
		?>
        <div class="video-container">
            <iframe class="nxs-youtube-iframe" src="https://www.youtube.com/embed/<?php echo $videoid; ?>?wmode=transparent<?php echo $transcriptparameter . $additionalparameters; ?>" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
    <?php
	
	if ($nxs_global_row_render_statebag == null)
	{
		echo "warning; nxs_global_row_render_statebag is null";
	}
	else
	{
		//echo "width:" . $nxs_global_row_render_statebag["width"];
		//echo "pagerowtemplate:" . $nxs_global_row_render_statebag["pagerowtemplate"];
	}
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	
	// data protection handling
	if (true)
	{
		$activity = "nexusframework:widget_youtube";
		if (!nxs_dataprotection_isactivityonforuser($activity))
		{
			$result["html"] = nxs_dataprotection_renderexplicitconsentinput($activity);
		}
	}

	return $result;
}

function nxs_youtube_videoid_popupcontent($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);

	$value = $$altid;	// $id is the parametername, $$id is the value of that parameter

	nxs_ob_start();
	?>
	<input type='text' class='videourl-<?php echo $id; ?> nxs-float-left' placeholder='<?php nxs_l18n_e("For example https://www.youtube.com/watch?feature=player_embedded&v=Gvvw4lXcCXE[nxs:placeholder]", "nxs_td"); ?>' oninput='nxs_js_updatevideoid_<?php echo $id; ?>();' value='https://www.youtube.com/watch?v=<?php echo $value; ?>' />
	<div class="nxs-clear">&nbsp;</div>
	<a href='#' onclick="nxs_js_setvideotosample_<?php echo $id; ?>(); return false;" class='nxsbutton1 nxs-float-left'><?php nxs_l18n_e("Sample[nxs:ddl]", "nxs_td"); ?></a>
	<a href='https://www.youtube.com' target="_blank" class='nxsbutton1 nxs-float-left'><?php nxs_l18n_e("Open youtube[nxs:button]", "nxs_td"); ?></a>
	
	<script>
		nxs_js_requirescript('parseuri_js', 'js', '<?php echo nxs_getframeworkurl() . '/nexuscore/widgets/youtube/js/parseuri.js'; ?>', false);

		function nxs_js_setvideotosample_<?php echo $id; ?>()
		{
			jQuery('.videourl-<?php echo $id; ?>').val('<?php nxs_l18n_e("https://www.youtube.com/watch?v=B6cg4ZoUwVU", "nxs_td"); ?>');

			nxs_js_updatevideoid_<?php echo $id; ?>()
		}

		function nxs_js_updatevideoid_<?php echo $id; ?>()
		{

			var video = "";
			
			try
			{
				//
				var videourl = jQuery('.videourl-<?php echo $id; ?>').val();
				
				if (videourl.startsWith("{{"))
				{
					video = videourl;
				}
				else
				{
					var urlitems = parseUri(videourl);
	
					var video = "";
					if (urlitems.host == "youtu.be")
					{
						if (urlitems.path != "")
						{
							video = urlitems.path.substr(1);
						}
					}
					else
					{
						video = urlitems.queryKey.v;
					}
				}
				jQuery('#<?php echo $altid; ?>').val(video);
			}
			catch (err)
			{
				//
			}
			
			nxs_js_popup_sessiondata_make_dirty();
		}
	</script>

	<?php

	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

function nxs_youtube_playsecs_popupcontent($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);

	$value = $$altid;	// $id is the parametername, $$id is the value of that parameter
	$playsecs = $value;

	nxs_ob_start();
	?>

	<?php
		if ($playsecs != "")
		{
			$play_partmin = floor($playsecs / 60);
			$play_partsec = $playsecs - ($play_partmin * 60);
			if (strlen($play_partsec) == 1)
			{
				$play_partsec = "0" . $play_partsec;
			}
		}
		else
		{
			$play_partmin = "";
		}
	?>
	<input type='text' id='play_partmin_<?php echo $id; ?>' name='play_partmin' class='nxs-float-left nxs-playfield_<?php echo $id; ?> nxs-playfield' value='<?php echo $play_partmin; ?>' oninput='nxs_js_updateplay_<?php echo $id; ?>();' style='width: 40px;' />	                	
	<span class='nxs-float-left' > <?php nxs_l18n_e("m", "nxs_td"); ?> </span>
	<input type='text' id='play_partsec_<?php echo $id; ?>' name='play_partsec' class='nxs-float-left nxs-playfield_<?php echo $id; ?> nxs-playfield' value='<?php echo $play_partsec; ?>' oninput='nxs_js_updateplay_<?php echo $id; ?>();' style='width: 40px;' maxlength=2 size=2 />
	<a href="#" onclick="jQuery('.nxs-playfield_<?php echo $id; ?>').val(''); nxs_js_updateplay_<?php echo $id; ?>(); return false;" class="nxsbutton1 nxs-float-left"><?php nxs_l18n_e("Clear", "nxs_td"); ?></a>
	<script>
		function nxs_js_updateplay_<?php echo $id; ?>()
		{
			var minutes = jQuery('#play_partmin_<?php echo $id; ?>').val();
			if (minutes == '') 
			{
				minutes = "0";
			}
			nxs_js_log(minutes);
			var seconds = jQuery('#play_partsec_<?php echo $id; ?>').val();
			if (seconds == '') 
			{
				seconds = "0";
			}
			var shouldclear = false;
			nxs_js_log(seconds);
			try
			{
    			if (minutes != '0' || seconds != '0')
    			{
    				var totalsecs = parseInt(minutes) * 60 + parseInt(seconds);
    				if (nxs_js_isint(totalsecs))
    				{
    					jQuery('#<?php echo $altid; ?>').val(totalsecs);
        			}
        			else
    				{
    					nxs_js_log("a");
    					shouldclear = true;
    				}
    			}
    			else
				{
					nxs_js_log("b");
					shouldclear = true;
				}
			}<?php echo $htmltitle; ?>
			catch(err)
			{
				shouldclear = true;
				nxs_js_log(err);
			}
			
			if (shouldclear)
			{
				jQuery('#<?php echo $altid; ?>').val("");
				jQuery('#play_partmin_<?php echo $id; ?>').val("");
				jQuery('#play_partsec_<?php echo $id; ?>').val("");
			}
			
			nxs_js_popup_sessiondata_make_dirty();
		}
	</script>

	<?php

	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

function nxs_widgets_youtube_initplaceholderdata($args)
{
	extract($args);
	
	$args["title"] = nxs_l18n__("title[sample]", "nxs_td");
	$args['title_heightiq'] = "true";
	$args["videoid"] = nxs_l18n__("videoid[youtube,sample,B6cg4ZoUwVU]", "nxs_td");
	$args["videourl"] = nxs_l18n__("videourl[youtube,sample,https://www.youtube.com/watch?v=B6cg4ZoUwVU]", "nxs_td");
	$args["language"] = nxs_l18n__("language[sample,youtube]", "nxs_td");
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_youtube_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_youtube_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_dataprotection_nexusframework_widget_youtube_getprotecteddata($args)
{
	$result = array
	(
		"controller_label" => "YouTube videos",
		"subactivities" => array
		(
			// if widget has properties that pull information from other 
			// vendors (like scripts, images hosted on external sites, etc.) 
			// those need to be taken into consideration
			// responsibility for that is the person configuring the widget
			"custom-widget-configuration",	
		),
		"dataprocessingdeclarations" => array	
		(
			array
			(
				"use_case" => "(belongs_to_whom_id) can browse a page of the website owned by the (controller) that renders youtube videos using the YouTube widget of the framework",
				"what" => "IP address of the (belongs_to_whom_id) as well as 'Request header fields' send by browser of ((belongs_to_whom_id)) (https://en.wikipedia.org/wiki/List_of_HTTP_header_fields#Request_fields)",
				"belongs_to_whom_id" => "website_visitor", // (has to give consent for using the "what")
				"controller" => "website_owner",	// who is responsible for this?
				"controller_options" => nxs_dataprotection_factory_getenableoptions("all"),
				"data_processor" => "Google (YouTube)",	// the name of the data_processor or data_recipient
				"data_retention" => "See the terms https://cloud.google.com/terms/data-processing-terms#data-processing-and-security-terms-v20",
				"program_lifecycle_phase" => "compiletime",
				"why" => "Not applicable (because this is a compiletime declaration)",
				"security" => "The data is transferred over a secure https connection. Security is explained in more detail here; https://cloud.google.com/terms/data-processing-terms#7-data-security",
			),
		),
		"status" => "final",
	);
	return $result;
}