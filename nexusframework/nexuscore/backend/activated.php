<?php

	?>
	
	<input type="hidden" id="nxs-refreshed-indicator" value="no" />
	<script type="text/javascript">
		onload=function()
		{
			// refresh the screen when the user pushes the back button
			var e=document.getElementById("nxs-refreshed-indicator");
			if(e.value=="no")e.value="yes";
			else
			{
				e.value="no";
				location.reload();
			}
		}
	</script>
	
	<?php

	$nxs_do_postthemeactivation = get_option("nxs_do_postthemeactivation");
	if ($nxs_do_postthemeactivation != "true")
	{
		// user pressed back button
		$url = get_admin_url('admin.php') . '?page=nxs_backend_overview';
		wp_redirect($url, 301);
		die();
	}
	
	if ($_REQUEST["step"] == 0 || $_REQUEST["step"] == '')
	{
		global $nxs_global_overviewservicevalue;
		
		require_once(NXS_FRAMEWORKPATH . '/nexuscore/includes/frontendediting.php');
		
		//
		// two options; 
		// 
		// option a; user has succesfully processed the turnkey feature at least 1x before
		// in that case, we ask the user what he wants to do: repeat the turnkey, or skip the turn key option,
		// defaulting to: skip it (as it might override settings and page contents)
		// 
		// option b; user has not yet before processed the turnkey feature for this site,
		// in that case, we start up the process automatically to apply the turnkey feature (without asking)
		//
		
		$sitemeta = nxs_getsitemeta_internal(false);
		$passed1clickcontent = $sitemeta['passed1clickcontent'];
		
		if (count($sitemeta) == 0)
		{
			// no "evidence" whatsoever that this site was activated before using a nxs theme,
			// likely a site was using another theme, or the site was wiped, thus we go for 
			// the 1clickcontent
			$activate1clickcontent = true;
		}		
		else if ($passed1clickcontent === "")
		{
			// no "evidence" was found that this site was activated before using a nxs theme, thus we go for 
			// the 1clickcontent
			$activate1clickcontent = true;
		}
		else if ($passed1clickcontent == null)
		{
			// the site was already activated before this passed1clickcontent feature was added, thus we skip
			// the 1clickcontent
			$activate1clickcontent = false;
		}
		else
		{
			// some "evidence" found that this site was activated before using a nxs theme, thus we skip
			// the 1clickcontent
			$activate1clickcontent = false;
		}
		
		//
		//
		//
		
		if ($activate1clickcontent)
		{
			// activate WITH 1clickcontent
			?>
			<script type='text/javascript'>
				jQuery(window).ready
				(
					function()
					{
						// submit the processing!
						nxs_js_log('Auto activate');
						jQuery('#nxssubmitturnkey').submit(); return false;
					}
				);
			</script>
			<?php
		}
		else
		{
			// SKIP 1clickcontent
			?>
			<script type='text/javascript'>
				jQuery(window).load
				(
					function()
					{
						// submit the processing!
						nxs_js_log('Auto activate');
						jQuery('#nxssubmitwithoutturnkey').submit(); return false;
					}
				);
			</script>
			<?php
		}
		?>
		
		<div class='nxs-clear'></div>
			
		<div class='nxs-width100 nxs-align-center nxs-margin-top60'>
			<img src='<?php echo nxs_getframeworkurl(); ?>/images/logo-x.png' />
			<p><?php echo nxs_l18n__("Please hold on while we activate the theme", "nxs_td"); ?></p>
		</div>
        
        <div class='nxs-clear nxs-padding-top20'></div>

			<form method="get" id="nxssubmitturnkey">
				<input type='hidden' name='page' value='<?php echo $_REQUEST["page"]; ?>' />
				<input type='hidden' name='step' value='1' />
				<?php
				// nxs_init_themeboot relies on "oneclickcontent" too, don't rename
				?>
				<input type='hidden' name='oneclickcontent' value='true' />
			</form>
			<form method="get" id="nxssubmitwithoutturnkey">
				<input type='hidden' name='page' value='<?php echo $_REQUEST["page"]; ?>' />
				<input type='hidden' name='step' value='1' />
			</form>
		<?php
	}
	else if ($_REQUEST["step"] == "1")
	{
		global $nxs_global_overviewservicevalue;
		
		$imageurl = nxs_getframeworkurl() . "/images/animations/waitwhileloading4.gif";
		require_once(NXS_FRAMEWORKPATH . '/nexuscore/includes/frontendediting.php'); 
		?>
		<div class='nxs-width100 nxs-align-center nxs-margin-top40'>
			<h1><?php echo nxs_l18n__("Activating your theme", "nxs_td"); ?></h1>
		</div>
        
    <div class='nxs-clear nxs-padding-top20'></div>
        
		<div class='nxs-width100 nxs-align-center'>
			<div style='width: 600px; margin: 0 auto; border: 1px; background-color: #EEE; border-color: #DDD; border-style: solid; border-width: 3px; padding: 5px;' class="nxs-gray nxs-border-radius5">
				<p>
					<div id='nxsprocessingwrapper' style='height: 300px; overflow-y:scroll;'>
						<div id='nxsprocessingindicator'></div>
						<span id='nxsprocessingspacer'></span><span id='nxsprocessingspacer2'>...</span><img id='nxsspinner' style='padding-left: 10px;' src='<?php echo $imageurl; ?>' />
					</div>
				</p>
			</div>
		</div>
		<div id='waitwrap' style='display:none;'>
			<form method="get">
				<div class='nxs-width100 nxs-align-center'>
					<h1><?php echo nxs_l18n__("One moment ...", "nxs_td"); ?></h1>
				</div>			
			</form>
		</div>
        
    <div class='nxs-clear nxs-padding-top20'></div>
        
		<div id='finishedwrap' style='display:none;'>
			<?php
			if ($licensekey == "") {
				$url = admin_url('admin.php?page=nxs_admin_license');
				$button_text = nxs_l18n__("Enable automatic updates", "nxs_td");
			}
			else {
				$url = nxs_geturl_home();
				$button_text = nxs_l18n__("View Home", "nxs_td");
			}
			?>
			<div class='nxs-width100 nxs-align-center'>
				<a href='<?php echo $url; ?>' class='nxs-big-button nxs-green nxs-border-radius5'><?php echo $button_text; ?></a>
			</div>			
		</div>
		
		<div id='errorwrap' style='display:none;'>
			<?php
			$url = nxs_geturl_home();
			?>
			<div class='nxs-width100 nxs-align-center'>
				<a href='<?php echo $url; ?>' class='nxs-big-button nxs-green nxs-border-radius5'><?php echo nxs_l18n__("Continu anyways", "nxs_td"); ?></a>
			</div>			
		</div>
		
		<script type='text/javascript'>
			jQuery(document).ready
			(
				function() 
				{
					nxs_js_serversideprocessing();
				}
			);			
		</script>
		<?php
	}
	else
	{
		//
	}
	
	?>
	<div id="jGrowl" class="top-right jGrowl"><div class="jGrowl-notification"></div></div>
	<script type='text/javascript'>
		
		var nxs_js_interval_serversideprocessing_1;
		var nxs_js_interval_serversideprocessing_2;
		var nxs_js_interval_heartbeat;
		
		var nxs_js_heartbeatpollinterval = 1000;	// the speed at which we output "zZz" to the user
		var nxs_js_serversideinvocationinterval = 1000;	// in msecs, lower value means more stress on server
		
		function nxs_js_serversideprocessing()
		{
			jQuery("#waitwrap").show();
			nxs_js_interval_heartbeat = setInterval(nxs_js_heartbeat, nxs_js_heartbeatpollinterval);
			
			<?php
			if ($_REQUEST["oneclickcontent"] != "")
			{
				// add content!
				?>
				//nxs_js_extendlog('<p>Invoking step 1; (if needed) add one click content</p>', true);
				nxs_js_interval_serversideprocessing_1 = setInterval(nxs_js_serversideprocessing_1, nxs_js_serversideinvocationinterval);
				<?php
			}
			else
			{
				// no content, please, continue with step 2; data consistency
				?>
				nxs_js_starttask2();
				<?php
			}
			?>
		}
		
		function nxs_js_heartbeat()
		{
			if (maintask >= 1 && maintask <= 2)
			{
				nxs_js_extendspacer("zZ", false);
			}
			else
			{
				// stop it!
				clearInterval(nxs_js_interval_heartbeat);
				jQuery('#nxsprocessingspacer').hide();
				jQuery('#nxsprocessingspacer2').hide();
				jQuery('#nxsspinner').hide();
			}
		}
		
		function nxs_js_extendspacer(log, shouldscroll)
		{
			jQuery('#nxsprocessingspacer').append(log);
			
			if (jQuery('#nxsprocessingspacer').html().length > 40)
			{
				jQuery('#nxsprocessingspacer').html('.');
			}
			
			if (shouldscroll)
			{
				nxs_js_logscrolldown();
			}
			
			jQuery('img').load
			(
				function()
				{
					if (shouldscroll)
					{
						nxs_js_logscrolldown();
					}
				}
			);
		}
		
		function nxs_js_extendlog(log, shouldscroll)
		{
			// empty
			jQuery("#nxsprocessingspacer").html("");
			
			jQuery('#nxsprocessingindicator').append(log);
			if (shouldscroll)
			{
				nxs_js_logscrolldown();
			}
			
			jQuery('img').load
			(
				function()
				{
					if (shouldscroll)
					{
						nxs_js_logscrolldown();
					}
				}
			);
		}
		
		function nxs_js_logscrolldown()
		{
			//nxs_js_log('scrolling down');
			var height = jQuery('#nxsprocessingwrapper')[0].scrollHeight;
			jQuery('#nxsprocessingwrapper').stop();
		  jQuery('#nxsprocessingwrapper').animate({scrollTop: height}, 1000);
		}

		var currentstep = 0;
		var moresteps = true;
		var busy = false;
		var maintask = 1;
		
		function nxs_js_serversideprocessing_1()
		{
			if (maintask != 1)
			{
				// no thanks
				return;
			}
			
			//nxs_js_log('trying for maintask:' + maintask);
			//nxs_js_log('trying for step:' + currentstep);
			
			// todo: add condition whether we should do this in the first place ...
			if (!busy)
			{
				//nxs_js_log('not busy, there we go');
				busy = true;

				if (moresteps)
				{
					//nxs_js_log('more steps, there we go');
					
					var ajaxurl = nxs_js_get_adminurladminajax();
					jQuery.ajax
					(
						{
							type: 'POST',
							data: 
							{
								"action": "nxs_ajax_webmethods",
								"webmethod": "installoneclickcontent",
								"currentstep": currentstep,
							},
							cache: false,
							dataType: 'JSON',
							url: ajaxurl,
							async: true,
							success: function(response) 
							{
								nxs_js_log(response);
								if (response.result == "OK")
								{
									//nxs_js_log("next step:" + response.nextstep + "/" + response.maxstep);
									if (currentstep < response.maxstep)
									{
										currentstep = response.nextstep;
										moresteps = true;
									}
									else
									{
										// no more
										moresteps = false;
										currentstep = 1;
									}
									nxs_js_extendlog("<p>" + response.log + "</p>", true);

									// allow next async thread to execute next request
									busy = false;
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
								// stop spinning! (!)
								nxs_js_handleunexpectederrorwhileactivating(response);
							}
						}
					);
				}
				
				if (moresteps == false)
				{
					// no more!
					clearInterval(nxs_js_interval_serversideprocessing_1);
					// start 2e deel proces
					
					nxs_js_starttask2();
				}
			}
			else
			{
				// busy!
				//nxs_js_log("working on it...");
			}
		}
		
		function nxs_js_handleunexpectederrorwhileactivating(response)
		{
			nxs_js_alert_sticky("<?php echo nxs_l18n__("Bad news; an error occured while activating the theme. Please first check our <a target='_blank' href='http://nexusthemes.com/support/how-to-install-a-wordpress-theme/'>installation guide</a>. The good news is that we can try to help you out, if you <a target='_blank' href='http://www.nexusthemes.com'>contact us</a>.", "nxs_td"); ?>");
			maintask = -999;
			jQuery("#waitwrap").hide();
			jQuery("#errorwrap").show();
			
			if (response != null)
			{
				if (response.responseText != null)
				{
					var lowercase = response.responseText.toLowerCase();
					if (lowercase.indexOf("under development") > -1)
					{
						nxs_js_alert_sticky("<?php echo nxs_l18n__("Hint: site is under development.", "nxs_td"); ?>");
					}
					else if (lowercase.indexOf("bytes exhausted (tried to allocate") > -1)
					{
						// solutions; http://wordpress.org/support/topic/memory-exhausted-error-in-admin-panel-after-upgrade-to-28
						nxs_js_alert_sticky("<?php echo nxs_l18n__("Hint: not enough memory. See http://wordpress.org/support/topic/memory-exhausted-error-in-admin-panel-after-upgrade-to-28", "nxs_td"); ?>");
					}
					else if (lowercase.indexOf("maximum execution time") > -1 && lowercase.indexOf("exceeded") > -1)
					{
						nxs_js_alert_sticky("<?php echo nxs_l18n__("Problem: max time-out exceeded. Solution; Import the initial content manually.", "nxs_td"); ?>");
					}
					else
					{
						nxs_js_alert_sticky("<?php echo nxs_l18n__("Sorry, no hint available", "nxs_td"); ?>");
					}
				}
			}
		}
		
		function nxs_js_starttask2()
		{
			maintask = 2;
			currentstep = 0;
			moresteps = true;
			busy = false;
					
			nxs_js_interval_serversideprocessing_2 = setInterval(nxs_js_serversideprocessing_2, nxs_js_serversideinvocationinterval);
		}	
		
		function nxs_js_serversideprocessing_2()
		{
			if (maintask != 2)
			{
				// no thanks
				return;
			}
				
			//nxs_js_log('trying for maintask:' + maintask);
			//nxs_js_log('trying for step:' + currentstep);
			
			// todo: add condition whether we should do this in the first place ...
			if (!busy)
			{
				//nxs_js_log('not busy, there we go');
				busy = true;

				if (moresteps)
				{
					nxs_js_log('more steps, there we go');
					nxs_js_log(currentstep);
					
					var ajaxurl = nxs_js_get_adminurladminajax();
					jQuery.ajax
					(
						{
							type: 'POST',
							data: 
							{
								"action": "nxs_ajax_webmethods",
								"webmethod": "sanitizecontent",
								"chunkedsteps": currentstep,
							},
							async: true,
							cache: false,
							dataType: 'JSON',
							url: ajaxurl,
							
							success: function(response) 
							{
								nxs_js_log(response);
								if (response.result == "OK")
								{
									nxs_js_extendlog("<p>" + response.log + "</p>", true);
									if (response.nextchunkedsteps == "finished")
									{
										moresteps = false;
									}
									else
									{
										// proceed to next step
										currentstep = response.nextchunkedsteps;
										moresteps = true;
									}

									// allow next async thread to execute next request
									busy = false;
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
								// stop spinning! (2)
								nxs_js_handleunexpectederrorwhileactivating(response);
							}
						}
					);
				}
				
				if (moresteps == false)
				{
					// no more!
					clearInterval(nxs_js_interval_serversideprocessing_2);
					// start volgende deel proces op

					maintask = 3;
					currentstep = 0;
					moresteps = true;
					busy = false;
					
					var ajaxurl = nxs_js_get_adminurladminajax();
					jQuery.ajax
					(
						{
							type: 'POST',
							data: 
							{
								"action": "nxs_ajax_webmethods",
								"webmethod": "updatewpoption",
								"key": "nxs_do_postthemeactivation",
								"value": "false"
							},
							async: false,
							cache: false,
							dataType: 'JSON',
							url: ajaxurl,
							
							success: function(response) 
							{
								nxs_js_log(response);
								if (response.result == "OK")
								{
									// ok
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
								// stop spinning! (3)
								nxs_js_handleunexpectederrorwhileactivating(response);
							}
						}
					);
					
					jQuery("#waitwrap").hide();
					jQuery("#finishedwrap").show();
					// nxs_js_interval_serversideprocessing_2 = setInterval(nxs_js_serversideprocessing_2, nxs_js_serversideinvocationinterval);
				}
			}
			else
			{
				// busy!
				//nxs_js_log("working on it...");
			}
		}
		
	</script>