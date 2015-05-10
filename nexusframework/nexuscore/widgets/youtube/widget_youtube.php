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
		"sheethelp" 		=> nxs_l18n__("http://nexusthemes.com/youtube-widget/"),
		"unifiedstyling" 	=> array("group" => nxs_widgets_youtube_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array("group" => nxs_widgets_youtube_getunifiedcontentgroup(),),
		"fields" => array
		(
			// TITLE
			
			array( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
			),
			
			array(
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
				"id" 				=> "videoid",
				"type" 				=> "input",
				"visibility" 		=> "hidden",
				"label" 			=> nxs_l18n__("Video ID", "nxs_td"),
				"localizablefield"	=> true
			),
		
			array(
				"id" 				=> "videoid_visualization",
				"altid" 			=> "videoid",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_youtube_videoid_popupcontent",
				"label" 			=> nxs_l18n__("Video URL", "nxs_td"),
				"localizablefield"	=> true
			),
			
			array(
				"id" 				=> "language",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Transcript language", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("For example: en", "nxs_td"),
				"localizablefield"	=> true
			),

			array( 
				"id" 				=> "autoplay",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Autoplay", "nxs_td"),
				"localizablefield"	=> true
			),

			array(
				"id" 				=> "playstartsecs",
				"type" 				=> "input",
				"visibility" 		=> "hidden",
				"label" 			=> nxs_l18n__("Video ID", "nxs_td"),
				"localizablefield"	=> true
			),

			array( 
				"id" 				=> "playstartsecs_visualization",
				"altid" 			=> "playstartsecs",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_youtube_playsecs_popupcontent",
				"label" 			=> nxs_l18n__("Play Start", "nxs_td"),
				"localizablefield"	=> true
			),

			array(
				"id" 				=> "playendsecs",
				"type" 				=> "input",
				"visibility" 		=> "hidden",
				"label" 			=> nxs_l18n__("Video ID", "nxs_td"),
				"localizablefield"	=> true
			),

			array( 
				"id" 				=> "playendsecs_visualization",
				"altid" 			=> "playendsecs",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_youtube_playsecs_popupcontent",
				"label" 			=> nxs_l18n__("Play End", "nxs_td"),
				"localizablefield"	=> true
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
	
	// Localize atts
	$mixedattributes = nxs_localization_localize($mixedattributes);
	
	extract($mixedattributes);
	
	global $nxs_doing_seo;
	global $nxs_seo_output;
	if ($nxs_doing_seo === true)
	{
		$nxs_seo_output = "http://www.youtube.com/watch?v=u9hyOQEwB4c";
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

	if (nxs_has_adminpermissions())
	{
		if ($videoid == "")
		{
			
			$videoid = "B6cg4ZoUwVU";
			$videourl = "http://www.youtube.com/watch?v=" . $videoid;
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
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["enable_decoratewidget"] = false;
	$hovermenuargs["enable_deletewidget"] = true;
	$hovermenuargs["enable_deleterow"] = false;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
	if ($videoid == "")
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("No video set", "nxs_td");
	}
	
	$htmltitle = nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, "", "");

	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-youtube";
	
	$scheme = "http";
	if (is_ssl()) 
	{
		$scheme = "https";
	}

	?>

	<div <?php echo $class; ?>>
		<?php echo $htmltitle; ?>
        <div class="video-container">
            <iframe class="nxs-youtube-iframe" src="<?php echo $scheme; ?>://www.youtube.com/embed/<?php echo $videoid; ?>?wmode=transparent<?php echo $transcriptparameter . $additionalparameters; ?>" frameborder="0"></iframe>
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
	
	$html = ob_get_contents();
	ob_end_clean();

	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	// outbound statebag
	// $nxs_global_row_render_statebag["foo"] = "bar";

	return $result;
}

function nxs_youtube_videoid_popupcontent($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);

	$value = $$altid;	// $id is the parametername, $$id is the value of that parameter

	ob_start();
	?>
	<input type='text' class='videourl-<?php echo $id; ?> nxs-float-left' placeholder='<?php nxs_l18n_e("For example http://www.youtube.com/watch?feature=player_embedded&v=Gvvw4lXcCXE[nxs:placeholder]", "nxs_td"); ?>' oninput='nxs_js_updatevideoid_<?php echo $id; ?>();' value='http://www.youtube.com/watch?v=<?php echo $value; ?>' />
	<div class="nxs-clear">&nbsp;</div>
	<a href='#' onclick="nxs_js_setvideotosample_<?php echo $id; ?>(); return false;" class='nxsbutton1 nxs-float-left'><?php nxs_l18n_e("Sample[nxs:ddl]", "nxs_td"); ?></a>
	<a href='http://www.youtube.com' target="_blank" class='nxsbutton1 nxs-float-left'><?php nxs_l18n_e("Open youtube[nxs:button]", "nxs_td"); ?></a>
	
	<script type='text/javascript'>
		nxs_js_requirescript('parseuri_js', 'js', '<?php echo nxs_getframeworkurl() . '/nexuscore/widgets/youtube/js/parseuri.js'; ?>', false);

		function nxs_js_setvideotosample_<?php echo $id; ?>()
		{
			jQuery('.videourl-<?php echo $id; ?>').val('<?php nxs_l18n_e("http://www.youtube.com/watch?v=B6cg4ZoUwVU", "nxs_td"); ?>');

			nxs_js_updatevideoid_<?php echo $id; ?>()
		}

		function nxs_js_updatevideoid_<?php echo $id; ?>()
		{

			var video = "";
			
			try
			{ 
				var videourl = jQuery('.videourl-<?php echo $id; ?>').val();
				nxs_js_log(videourl);
				var urlitems = parseUri(videourl);
				nxs_js_log(urlitems);
				video = urlitems.queryKey.v;

				nxs_js_log(video);

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

	$result = ob_get_contents();
	ob_end_clean();
	return $result;
}

function nxs_youtube_playsecs_popupcontent($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);

	$value = $$altid;	// $id is the parametername, $$id is the value of that parameter
	$playsecs = $value;

	ob_start();
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
	<script type='text/javascript'>
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

	$result = ob_get_contents();
	ob_end_clean();
	return $result;
}

function nxs_widgets_youtube_initplaceholderdata($args)
{
	extract($args);
	
	$args["title"] = nxs_l18n__("title[sample]", "nxs_td");
	$args["videoid"] = nxs_l18n__("videoid[youtube,sample,B6cg4ZoUwVU]", "nxs_td");
	$args["videourl"] = nxs_l18n__("videourl[youtube,sample,http://www.youtube.com/watch?v=B6cg4ZoUwVU]", "nxs_td");
	$args["language"] = nxs_l18n__("language[sample,youtube]", "nxs_td");
	
	nxs_widgets_youtube_updateplaceholderdata($args);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

//
// wordt aangeroepen bij het opslaan van data van deze placeholder
//
function nxs_widgets_youtube_updateplaceholderdata($args)
{
	extract($args);
	
	$temp_array = array();
	
	// its required to also set the 'type' (used when dragging an item from the toolbox to existing placeholder)
	$temp_array['type'] = 'youtube';
	$temp_array['videoid'] = $videoid;
	$temp_array['autoplay'] = $autoplay;
	$temp_array['videourl'] = $videourl;
	$temp_array['language'] = $language;
	$temp_array['playstartsecs'] = $playstartsecs;
	$temp_array['playendsecs'] = $playendsecs;
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $temp_array);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>