<?php

function nxs_widgets_youtube_gettitle()
{
	return nxs_l18n__("Youtube[nxs:widgettitle]", "nxs_td");
}

function nxs_widgets_youtube_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_youtube_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	$mixedattributes = array_merge($temp_array, $args);
	
	$title = $mixedattributes['title'];
	$autoplay = $mixedattributes['autoplay'];
	$videourl = $mixedattributes['videourl'];
	$language = $mixedattributes['language'];
	$videoid = $mixedattributes['videoid'];
	$playstartsecs = $mixedattributes['playstartsecs'];
	$playendsecs = $mixedattributes['playendsecs'];
	
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
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs); 
	
	//
	// render actual control / html
	//
	
	ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-youtube";
	
	$scheme = "http";
	if (is_ssl()) 
	{
		$scheme = "https";
	}
	
	echo '
	<div '.$class.'>';

 		echo '   
        <div class="video-container">
        
            <iframe class="nxs-youtube-iframe" src="'.$scheme.'://www.youtube.com/embed/'.$videoid.'?wmode=transparent'.$transcriptparameter . $additionalparameters.'" frameborder="0"></iframe>
        
        </div>
    </div>';
	

	
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

//
// het eerste /hoofd/ scherm dat wordt getoond in de popup als de gebruiker
// het editen van een placeholder initieert
//
function nxs_widgets_youtube_home_rendersheet($args)
{
	//
	extract($args);
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);

	$title = $temp_array['title'];
	$videoid = $temp_array['videoid'];
	$videourl = $temp_array['videourl'];
	$language = $temp_array['language'];
	$autoplay = $temp_array['autoplay'];
	$playstartsecs = $temp_array['playstartsecs'];
	$playendsecs = $temp_array['playendsecs'];
	
	// clientpopupsessiondata bevat key values van de client side, deze overschrijven reeds bestaande variabelen
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);

	$result = array();
	$result["result"] = "OK";

	ob_start();

	?>
    <div class="nxs-admin-wrap">
      <div class="block">

       	<?php nxs_render_popup_header(nxs_l18n__("Youtube[nxs:widgettitle]", "nxs_td")); ?>
    	          
				<div class="nxs-popup-content-canvas-cropper">
					<div class="nxs-popup-content-canvas">
		
		        <div class="content2">
		            <div class="box">
		                <div class="box-title">
		                  <h4><?php nxs_l18n_e("Youtube url", "nxs_td"); ?></h4>
		                 </div>
		                <div class="box-content">
		                	<input type='text' id='videourl' name='videourl' class='nxs-float-left' placeholder='<?php nxs_l18n_e("For example http://www.youtube.com/watch?feature=player_embedded&v=Gvvw4lXcCXE[nxs:placeholder]", "nxs_td"); ?>' value='<?php echo $videourl; ?>' />
		                	<div class="nxs-clear">&nbsp;</div>
		                	<a href='#' onclick="jQuery('#videourl').val('<?php nxs_l18n_e("http://www.youtube.com/watch?feature=player_embedded&amp;v=B6cg4ZoUwVU[nxs:sample,url]", "nxs_td"); ?>'); nxs_js_popup_sessiondata_make_dirty(); return false;" class='nxsbutton1 nxs-float-left'><?php nxs_l18n_e("Sample[nxs:ddl]", "nxs_td"); ?></a>
		                	<a href='http://www.youtube.com' target="_blank" class='nxsbutton1 nxs-float-left'><?php nxs_l18n_e("Open youtube[nxs:button]", "nxs_td"); ?></a>
		                </div>
		            </div>
		            <div class="nxs-clear"></div>
		        </div> <!--END content-->
		        
		        <!-- hu -->
		        <div class="content2">
		            <div class="box">
		                <div class="box-title">
		                  <h4><?php nxs_l18n_e("Transcript language[nxs:heading]", "nxs_td"); ?></h4>
		                 </div>
		                <div class="box-content">
		                	<input type='text' id='language' name='language' class='nxs-float-left' placeholder='<?php nxs_l18n_e("For example nl[nxs:placeholder]", "nxs_td"); ?>' value='<?php echo $language; ?>' />
		                	<div class="nxs-clear">&nbsp;</div>
		                </div>
		            </div>
		            <div class="nxs-clear"></div>
		        </div> <!--END content-->

						<?php
						if ($autoplay != '')
						{
							$autoplayattribute = ' checked="checked" ';
						}
						else
						{
							$autoplayattribute = '';
						}
						
						?>
		        
		        <!-- auto play -->
		        <div class="content2">
		            <div class="box">
		                <div class="box-title">
		                  <h4><?php nxs_l18n_e("Autoplay", "nxs_td"); ?></h4>
		                 </div>
		                <div class="box-content">
		                	<input type='checkbox' id='autoplay' name='autoplay' class='nxs-float-left' <?php echo $autoplayattribute; ?> />
		                </div>
		            </div>
		            <div class="nxs-clear"></div>
		        </div> <!--END content-->
		        
		        <!-- play start -->
		        <div class="content2">
	            <div class="box">
	                <div class="box-title">
	                  <h4><?php nxs_l18n_e("Start", "nxs_td"); ?></h4>
	                 </div>
	                <div class="box-content">
	                	<input type='hidden' id='playstartsecs' name='playstartsecs' class='nxs-float-left' value='<?php echo $playstartsecs; ?>' />
										<?php
										if ($playstartsecs != "")
										{
											$playstart_partmin = floor($playstartsecs / 60);
											$playstart_partsec = $playstartsecs - ($playstart_partmin * 60);
											if (strlen($playstart_partsec) == 1)
											{
												$playstart_partsec = "0" . $playstart_partsec;
											}
										}
										else
										{
											$playstart_partmin = "";
										}
										?>
	                	<input type='text' id='playstart_partmin' name='playstart_partmin' class='nxs-float-left nxs-playstartfield' value='<?php echo $playstart_partmin; ?>' oninput='nxs_js_updateplaystart();' style='width: 40px;' />	                	
	                	<span class='nxs-float-left' ><?php nxs_l18n_e("m", "nxs_td"); ?></span>
	                	<input type='text' id='playstart_partsec' name='playstart_partsec' class='nxs-float-left nxs-playstartfield' value='<?php echo $playstart_partsec; ?>' oninput='nxs_js_updateplaystart();' style='width: 40px;' maxlength=2 size=2 />
	                	<a href="#" onclick="jQuery('.nxs-playstartfield').val(''); nxs_js_updateplaystart(); return false;" class="nxsbutton1 nxs-float-left"><?php nxs_l18n_e("Clear", "nxs_td"); ?></a>
	                	<script type='text/javascript'>
	                		function nxs_js_updateplaystart()
	                		{
	                			var minutes = jQuery('#playstart_partmin').val();
	                			if (minutes == '') 
	                			{
	                				minutes = "0";
	                			}
	                			nxs_js_log(minutes);
	                			var seconds = jQuery('#playstart_partsec').val();
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
			                				jQuery('#playstartsecs').val(totalsecs);
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
	                			} 
	                			catch(err)
	                			{
	                				
                					shouldclear = true;
	                				nxs_js_log(err);
	                			}
	                			
	                			if (shouldclear)
	                			{
	                				jQuery('#playstartsecs').val("");
	                				jQuery('#playstart_partmin').val("");
													jQuery('#playstart_partsec').val("");
	                			}
	                			
	                			//
	                			nxs_js_popup_sessiondata_make_dirty();
	                		}
	                	</script>
	                </div>
	            </div>
	            <div class="nxs-clear"></div>
		        </div> <!--END content-->
		        
		        <!-- play end -->
		        <div class="content2">
	            <div class="box">
                <div class="box-title">
                  <h4><?php nxs_l18n_e("End", "nxs_td"); ?></h4>
                 </div>
                <div class="box-content">
                	<input type='hidden' id='playendsecs' name='playendsecs' class='nxs-float-left' value='<?php echo $playendsecs; ?>' />
									<?php
									if ($playendsecs != "")
									{
										$playend_partmin = floor($playendsecs / 60);
										$playend_partsec = $playendsecs - ($playend_partmin * 60);
										if (strlen($playend_partsec) == 1)
										{
											$playend_partsec = "0" . $playend_partsec;
										}
									}
									else
									{
										$playend_partmin = "";
									}
									?>
                	<input type='text' id='playend_partmin' name='playend_partmin' class='nxs-float-left nxs-playendfield' value='<?php echo $playend_partmin; ?>' oninput='nxs_js_updateplayend();' style='width: 40px;' />	                	
                	<span class='nxs-float-left' ><?php nxs_l18n_e("m", "nxs_td"); ?></span>
                	<input type='text' id='playend_partsec' name='playend_partsec' class='nxs-float-left nxs-playendfield' value='<?php echo $playend_partsec; ?>' oninput='nxs_js_updateplayend();' style='width: 40px;' maxlength=2 size=2 />
                	<a href="#" onclick="jQuery('.nxs-playendfield').val(''); nxs_js_updateplayend(); return false;" class="nxsbutton1 nxs-float-left"><?php nxs_l18n_e("Clear", "nxs_td"); ?></a>
                	<script type='text/javascript'>
                		function nxs_js_updateplayend()
                		{
                			var minutes = jQuery('#playend_partmin').val();
                			if (minutes == '') 
                			{
                				minutes = "0";
                			}
                			nxs_js_log(minutes);
                			var seconds = jQuery('#playend_partsec').val();
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
		                				jQuery('#playendsecs').val(totalsecs);
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
                			} 
                			catch(err)
                			{
                				
              					shouldclear = true;
                				nxs_js_log(err);
                			}
                			
                			if (shouldclear)
                			{
                				jQuery('#playendsecs').val("");
                				jQuery('#playend_partmin').val("");
												jQuery('#playend_partsec').val("");
                			}
                			
                			//
                			nxs_js_popup_sessiondata_make_dirty();
                		}
                	</script>
                </div>
	            </div>
	            <div class="nxs-clear"></div>
		        </div> <!--END content-->
		        
		        
	       	</div>
        </div>        
        
        <div class="content2">
          <div class="box">
            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Save[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:button]", "nxs_td"); ?></a>
           </div>
          <div class="nxs-clear margin"></div>
        </div> <!--END content-->
        
      </div> <!--END block-->
    </div>

    <script type='text/javascript'>
		
		function nxs_js_savegenericpopup()
		{
			nxs_js_requirescript('parseuri_js', 'js', '<?php echo nxs_getframeworkurl() . '/nexuscore/widgets/youtube/js/parseuri.js'; ?>', nxs_js_savegenericpopup_step2);
		}

		function nxs_js_savegenericpopup_step2()
		{		
			var video = "";
			
			try
			{ 
				var videourl = jQuery('#videourl').val();
				var urlitems = parseUri(videourl);
				video = urlitems.queryKey.v;
			}
			catch (err)
			{
				//
			}
			
			if (video == "" || video == null)
			{
				nxs_js_alert("<?php nxs_l18n_e("Video not found; please enter the complete url of the Youtube video[nxs:growl]", "nxs_td"); ?>");
				jQuery('#videourl').focus();
				return;
			}
			
			var autoplayvalue;
			if (jQuery('#autoplay').is(":checked"))
			{
				autoplayvalue = 'checked';
			}
			else
			{
				autoplayvalue = '';
			}
			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQuery.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updateplaceholderdata",
						"placeholderid": "<?php echo $placeholderid;?>",
						"postid": "<?php echo $postid;?>",
						"placeholdertemplate": "youtube",
						"autoplay": autoplayvalue,
						"title": jQuery('#title').val(),
						"videoid": video,
						"videourl": jQuery('#videourl').val(),
						"language": jQuery('#language').val(),
						"playstartsecs": jQuery('#playstartsecs').val(),
						"playendsecs": jQuery('#playendsecs').val()
					},
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// update UI, the 'current' id will be overriden because null is specified as third parameter
							nxs_js_rerender_row_for_placeholder("<?php echo $postid;?>", "<?php echo $placeholderid;?>");
														
							// close the pop up
							nxs_js_closepopup_unconditionally();
						}
						else
						{
							nxs_js_popup_notifyservererror();
							nxs_js_log(response);
						}
					},
					error: function(response)
					{
						nxs_js_popup_notifyservererror();
						nxs_js_log(response);
					}										
				}
			);
		}
		
		function nxs_js_execute_after_popup_shows()
		{
			jQuery('#title').focus();
		}

		
	</script>

<?php
	
	$html = ob_get_contents();
	ob_end_clean();

	$result["html"] = $html;
	
	return $result;   
}

function nxs_widgets_youtube_initplaceholderdata($args)
{
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
