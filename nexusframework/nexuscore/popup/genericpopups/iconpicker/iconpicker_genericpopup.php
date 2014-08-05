<?php

function nxs_popup_genericpopup_iconpicker_getpopup($args)
{
	extract($args);
	
	extract($clientpopupsessiondata);	
	extract($clientshortscopedata);

	$result = array();
	$result["result"] = "OK";
	
	ob_start();
	
	$padding = "";
	
	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">
			<?php nxs_render_popup_header(nxs_l18n__("Icon picker", "nxs_td")); ?>
			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
					
					<div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("General", "nxs_td"); ?></h4></div>
							<div class="box-content">
								<ul>
									<?php
									$iconset = "nxs-icon";
									$icontypes = array
									(
										"categories", 
										"gallerybox",  
										"socialsharing", 
										"callout", 
										"newspaper", 
										"text", 
										"image", 
										"contact", 
										
										"googlemap", 
										"sliderbox", 
										"twittertweets",
										"squeezebox", 
										"bio", 
										"signpost", 
										
										"comments",
										"quote",  
										"bubble",  
										"undefined", 
										"remove-sign", 
										"article-overview", 
										"article-new", 
										"dashboard", 
										
										"support", 
										"trash", 
										"network", 
										"page-settings", 
										"plug", 
										"lightning", 
										"cog", 
										"link", 
										
										"calendar", 
										"checkmark", 
										"pagedecorator", 
										"shirt", 
										"profile", 
										"history", 
										"clock", 
										"calendar-2", 
										"user", 
										
										"users", 
										"trophy", 
										"pie", 
										"disabled", 
										"commission", 
										"referee", 
										"matchresults",
										"drawer", 
										"zen",  
										
										"books", 
										"star2",
										"star3", 
										"quill", 
										"file2", 
										"construction", 
										"blocked", 
										"wand", 
										"phone", 
										"euro", 
										
										"fire", 
										"vacuum-cleaner", 
										"sofa", 
										"spray-can", 
										"window", 
										"oven", 
										"pencil2", 
										"pencil", 
										"blog", 
										"droplet", 
										
										"music", 
										"headphones", 
										"film", 
										"dice", 
										"pacman", 
										"spades", 
										"clubs", 
										"diamonds", 
										"connection", 
										"book", 
										
										"copy", 
										"copy2", 
										"copy3", 
										"paste", 
										"stack", 
										"tags", 
										"cart", 
										"credit", 
										"address-book", 
										"notebook", 
										
										"pushpin", 
										"compass", 
										"map", 
										"alarm", 
										"screen", 
										"mobile", 
										"mobile2", 
										"tablet", 
										"laptop", 
										"tv", 
										
										"keyboard", 
										"print", 
										"cabinet", 
										"drawer2", 
										"upload", 
										"disk", 
										"busy", 
										"binoculars",  
										"key", 
										"lock", 
										
										"unlocked", 
										"hammer", 
										"bug", 
										"stats", 
										"bars", 
										"bars2", 
										"gift", 
										"glass", 
										"mug", 
										"food", 
										
										"leaf", 
										"rocket", 
										"dashboard", 
										"hammer2", 
										"lab", 
										"magnet", 
										"briefcase", 
										"apple",
										"windows8", 
										"road", 
										
										"accessibility", 
										"target", 
										"switch", 
										"signup", 
										"tree", 
										"cloud",
										"cloud-download", 
										"cloud-upload", 
										"earth", 
										"flag", 
										"attachment", 
										
										"eye", 
										"eye-blocked", 
										"brightness-medium", 
										"brightness-contrast", 
										"contrast", 
										"star2", 
										"heart", 
										"heart-broken", 
										"thumbs-up", 
										"thumbs-up2", 
										
										"point-up", 
										"point-right", 
										"point-down", 
										"point-left",
										//"hardhat",
										//"yoga",
										"tooth",
										//"horseshoe",
										"dollar",
										//"bread",
										//"palette",
										
										"pound",
										//"herring",
										"bell",
										"lock2",
										
										"security-camera",
										"safe",
										"snowflake1",
										"stopwatch1",
										"trolley",
	
									);
										
									foreach($icontypes as $currenticontype)
									{
										$identification = $iconset . "-" . $currenticontype;
										?>
										<li class='nxs-float-left nxs-icon'>
											<a href='#' onclick='nxs_js_selecticonitem("<?php echo $identification;?>"); return false;'>
												<span class="<?php echo $identification; ?>"></span>
											</a>
										</li>
										<?php
									}
									?>
								</ul>
							</div>
						</div>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
                    
                    <div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("Buildings", "nxs_td"); ?></h4></div>
							<div class="box-content">
								<ul>
									<?php
									$iconset = "nxs-icon";
									$icontypes = array
									(
										"home",
										"hospital",
										"office", 
										"apartment",
										"company",
										"library", 
										"warehouse",
									);
										
									foreach($icontypes as $currenticontype)
									{
										$identification = $iconset . "-" . $currenticontype;
										?>
										<li class='nxs-float-left nxs-icon'>
											<a href='#' onclick='nxs_js_selecticonitem("<?php echo $identification;?>"); return false;'>
												<span class="<?php echo $identification; ?>"></span>
											</a>
										</li>
										<?php
									}
									?>
								</ul>
							</div>
						</div>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
                    
                    <div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("Controls", "nxs_td"); ?></h4></div>
							<div class="box-content">
								<ul>
									<?php
									$iconset = "nxs-icon";
									$icontypes = array
									(
										"warning", 
										"info", 
										"notification", 
										"question", 
										"close", 
										"plus", 
										"enter",
										"logout",  
										"play2", 
										"pause2", 
										
										"stop", 
										"backward", 
										"forward2", 
										"play", 
										"pause", 
										"stop2", 
										"backward2", 
										"forward3", 
										"first", 
										"last", 
										
										"previous", 
										"next", 
										"volume-high", 
										"volume-medium", 
										"volume-low", 
										"volume-mute", 
										"volume-mute2", 
										"volume-increase", 
										"volume-decrease",
										"crop", 
										
										"scissors",
										"search", 
										"searchresults", 
										"zoom-in", 
										"zoom-out",
										"list", 
										"list2", 
										"numbered-list", 
									);
										
									foreach($icontypes as $currenticontype)
									{
										$identification = $iconset . "-" . $currenticontype;
										?>
										<li class='nxs-float-left nxs-icon'>
											<a href='#' onclick='nxs_js_selecticonitem("<?php echo $identification;?>"); return false;'>
												<span class="<?php echo $identification; ?>"></span>
											</a>
										</li>
										<?php
									}
									?>
								</ul>
							</div>
						</div>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
                    
                    <div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("Arrows", "nxs_td"); ?></h4></div>
							<div class="box-content">
								<ul>
									<?php
									$iconset = "nxs-icon";
									$icontypes = array
									(
										"arrow-up-left3",
										"arrow-up3",
										"arrow-up-right3", 
										"arrow-right3", 
										"arrow-down-right3", 
										"arrow-down3", 
										"arrow-down-left3", 
										"arrow-left3", 
										"arrow-down",
										"arrow-up",
										
										"arrow-right",
										"arrow-left",
										"arrow-up-left", 
										"arrow-up-right", 
										"arrow-down-right", 
										"arrow-down-left", 
										"arrow-up-left2", 
										"arrow-up2", 
										"arrow-up-right2", 
										"arrow-right2", 
										
										"arrow-down-right2", 
										"arrow-down2", 
										"arrow-down-left2", 
										"arrow-left2", 
										"arrow-left-2", 
										"arrow-right-light", 
										"arrow-right-double", 
										"arrow-left-double", 
										"undo", 
										"redo", 
										
										"forward", 
										"reply", 
										"undo2", 
										"redo2", 
										"move", 
										"expand", 
										"contract", 
										"expand2", 
										"contract2", 
										"loop", 
										
										"loop2", 
										"shuffle", 
									);
										
									foreach($icontypes as $currenticontype)
									{
										$identification = $iconset . "-" . $currenticontype;
										?>
										<li class='nxs-float-left nxs-icon'>
											<a href='#' onclick='nxs_js_selecticonitem("<?php echo $identification;?>"); return false;'>
												<span class="<?php echo $identification; ?>"></span>
											</a>
										</li>
										<?php
									}
									?>
								</ul>
							</div>
						</div>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
                    
                    <div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("Social media", "nxs_td"); ?></h4></div>
							<div class="box-content">
								<ul>
									<?php
									$iconset = "nxs-icon";
									$icontypes = array
									(
										"social",
										"twitter-2",
										"facebook",
										"linkedin",
										"google-plus",
										"pinterest",
										"youtube",  
										"vimeo", 
										"rss",
										"google",
										"google-drive",
										"flickr",
										"wordpresssidebar",
										
									);
										
									foreach($icontypes as $currenticontype)
									{
										$identification = $iconset . "-" . $currenticontype;
										?>
										<li class='nxs-float-left nxs-icon'>
											<a href='#' onclick='nxs_js_selecticonitem("<?php echo $identification;?>"); return false;'>
												<span class="<?php echo $identification; ?>"></span>
											</a>
										</li>
										<?php
									}
									?>
								</ul>
							</div>
						</div>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
                    
                    <div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("Smilies", "nxs_td"); ?></h4></div>
							<div class="box-content">
								<ul>
									<?php
									$iconset = "nxs-icon";
									$icontypes = array
									(									
										"evil", 
										"grin",
										"happy", 
										"smiley", 
										"tongue", 
										"sad", 
										"wink", 
										"cool", 
										"angry", 
										"shocked", 
										
										"confused", 
										"neutral", 
										"wondering",  
									);
										
									foreach($icontypes as $currenticontype)
									{
										$identification = $iconset . "-" . $currenticontype;
										?>
										<li class='nxs-float-left nxs-icon'>
											<a href='#' onclick='nxs_js_selecticonitem("<?php echo $identification;?>"); return false;'>
												<span class="<?php echo $identification; ?>"></span>
											</a>
										</li>
										<?php
									}
									?>
								</ul>
							</div>
						</div>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
                    
                    <div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("Animals", "nxs_td"); ?></h4></div>
							<div class="box-content">
								<ul>
									<?php
									$iconset = "nxs-icon";
									$icontypes = array
									(									
										"flea", 
										"termite",
										"cockroach", 
										"fly", 
										"wasp",
										"moth",
										"fox", 
										"mouse", 
										"mole", 
										
										"pigeon", 
										"rabbit", 
										"squirrel",
										"rat",
										"dog",
										"cat",  
									);
										
									foreach($icontypes as $currenticontype)
									{
										$identification = $iconset . "-" . $currenticontype;
										?>
										<li class='nxs-float-left nxs-icon'>
											<a href='#' onclick='nxs_js_selecticonitem("<?php echo $identification;?>"); return false;'>
												<span class="<?php echo $identification; ?>"></span>
											</a>
										</li>
										<?php
									}
									?>
								</ul>
							</div>
						</div>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
                    
                    <div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("Vehicles", "nxs_td"); ?></h4></div>
							<div class="box-content">
								<ul>
									<?php
									$iconset = "nxs-icon";
									$icontypes = array
									(									
										//"landrover",
										//"toyota",
										//"mustang",
										//"truck2",
										"truck",
										"car",
										"airplane",
										"train1",
										"dry-van",
										"flat-bed",
										"truck3",
									);
										
									foreach($icontypes as $currenticontype)
									{
										$identification = $iconset . "-" . $currenticontype;
										?>
										<li class='nxs-float-left nxs-icon'>
											<a href='#' onclick='nxs_js_selecticonitem("<?php echo $identification;?>"); return false;'>
												<span class="<?php echo $identification; ?>"></span>
											</a>
										</li>
										<?php
									}
									?>
								</ul>
							</div>
						</div>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
                    
                    <div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("Zodiac signs", "nxs_td"); ?></h4></div>
							<div class="box-content">
								<ul>
									<?php
									$iconset = "nxs-icon";
									$icontypes = array
									(									
										"aries",
										"taurus",
										"gemini",
										"cancer",
										"leo",
										"virgo",
										"libra",
										"scorpio",
										"sagittarius",
										"capricorn",
										"aquarius",
										"pisces",
									);
										
									foreach($icontypes as $currenticontype)
									{
										$identification = $iconset . "-" . $currenticontype;
										?>
										<li class='nxs-float-left nxs-icon'>
											<a href='#' onclick='nxs_js_selecticonitem("<?php echo $identification;?>"); return false;'>
												<span class="<?php echo $identification; ?>"></span>
											</a>
										</li>
										<?php
									}
									?>
								</ul>
							</div>
						</div>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
						
				</div> <!-- END nxs-popup-content-canvas -->
			</div> <!-- END nxs-popup-content-canvas-cropper -->
	
			<div class="content2">
				<div class="box">
					<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_popup_navigateto("<?php echo $nxs_iconpicker_invoker; ?>"); return false;'><?php nxs_l18n_e("Back", "nxs_td"); ?></a>
					<?php 
					if ($nxs_iconpicker_currentvalue != "") 
					{
						?>
						<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton1 nxs-float-right" onclick='nxs_js_selecticonitem(""); return false;'><?php nxs_l18n_e("No icon", "nxs_td"); ?></a>
						<?php
					}
					?>
				</div>
				<div class="nxs-clear"></div>
			</div> <!-- END content -->
		</div> <!-- END block -->
	</div> <!-- END nxs-admin-wrap -->
	
	<script type='text/javascript'>
	
		function nxs_js_selecticonitem(item) 
		{
			nxs_js_popup_setsessiondata("<?php echo $nxs_iconpicker_targetvariable; ?>", item);
			nxs_js_popup_sessiondata_make_dirty();
			// toon eerste scherm in de popup
			nxs_js_popup_navigateto("<?php echo $nxs_iconpicker_invoker; ?>");
		}
	
	</script>
	<?php

	// Setting the contents of the output buffer into a variable and cleaning up te buffer
  $html = ob_get_contents();
  ob_end_clean();
    
  // Setting the contents of the variable to the appropriate array position
  // The framework uses this array with its accompanying values to render the page
  $result["html"] = $html;
  return $result;
}
?>
