<?php

function nxs_popup_genericpopup_iconpicker_getpopup($args)
{
	extract($args);
	
	extract($clientpopupsessiondata);	
	extract($clientshortscopedata);

	$result = array();
	$result["result"] = "OK";
	
	nxs_ob_start();
	
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
										"newspaper",
										"pencil2",
										"pencil",
										"quill",
										"pen",
										"blog",
										"droplet",
										"pagedecorator",
										"image",
										"sliderbox",
										"gallerybox",
										"music",
										"film",
										"camera-2",
										"dice",
										"pacman",
										"spades",
										"clubs",
										"diamonds",
										"callout",
										"connection",
										"book",
										"books",
										"text",
										"profile",
										"bug",
										"stack",
										"folder",
										"tag",
										"tags",
										"qrcode",
										"ticket",
										"cart",
										"credit",
										"support",
										"phone",
										"address-book",
										"notebook",
										"contact",
										"pushpin",
										"googlemap",
										"compass",
										"map",
										"history",
										"alarm",
										"bell",
										"stopwatch1",
										"calendar-2",
										"calendar",
										"print",
										"keyboard",
										"screen",
										"laptop",
										"mobile",
										"tablet",
										"tv",
										"cabinet",
										"drawer",
										"drawer2",
										"box-add",
										"box-remove",
										"undefined",
										"upload",
										"undo",
										"redo",
										"undo2",
										"redo2",
										"forward",
										"reply",
										"quote",
										"comments",
										"user",
										"users",
										"quotes-left",
										"busy",
										"binoculars",
										"search",
										"zoom-out",
										"zoom-in",
										"expand",
										"contract",
										"expand2",
										"contract2",
										"key",
										"lock2",
										"lock",
										"unlocked",
										"equalizer",
										"cog",
										"wand",
										"aid",
										"pie",
										"stats",
										"bars",
										"gift",
										"trophy",
										"glass",
										"food",
										"leaf",
										"dashboard2",
										"hammer",
										"fire",
										"lab",
										"magnet",
										"trash",
										"briefcase",
										"road",
										"accessibility",
										"target",
										"shield",
										"lightning",
										"switch",
										"plug",
										"signup",
										"list",
										"list2",
										"numbered-list",
										"tree",
										"cloud",
										"cloud-download",
										"cloud-upload",
										"network",
										"earth",
										"link",
										"flag",
										"attachment",
										"eye",
										"brightness-medium",
										"brightness-contrast",
										"star",
										"star3",
										"star2",
										"heart",
										"socialsharing",
										"heart-broken",
										"thumbs-up",
										"thumbs-up2",
										"point-up",
										"point-down",
										"point-right",
										"point-left",
										"disk",
										"wrench",
										"shuffle",
										"radio-checked",
										"radio-unchecked",
										"crop",
										"scissors",
										"filer",
										"template",
										"new-tab",
										"embed",
										"file-pdf",
										"clock",
										"headphones",
										"loop",
										"loop2",
										"loop3",
										"bubble",
										"copy",
										"copy2",
										"copy3",
										"paste",
										"mobile2",
										"bars2",
										"eye-blocked",
										"contrast",
										"google-drive",
										
										/* Custom */
										"header",
										"sidebar",
										"footer",
										"subfooter",
										"subheader",
										"construction",
										"dashboard",
										"article-new",
										"article-overview",
										"dollar",
										"euro",
										"pound",
										"menucontainer",
										"carousel",
										"csv",
										"htmlcustom",
										"logo",
										"signpost",
										"wordpresstitle",
										"faucet",
										"hardhat",
										"herring",
										"horseshoe",
										"matchresults",
										"mug",
										"oven",
										"palette",
										"paw",
										"plunger",
										"safe",
										"referee",
										"searchresults",
										"vacuum-cleaner",
										"window",
										"yoga",
										"zen",
										"security-camera",
										"dry-van",
										"snowflake",
										"flat-bed",
										"trolley",
										"spray-can",
										"sofa",
										"sewerage",
										"toilet",
										"tooth",
										"shirt",
										"commission",
										"birthdaycake",
										"blowtorch",
										"fan",
										"categories",
										"tumbler",
										"remove-sign",
										"disabled",
										"file2",
										"move",
										"ribbon",
										"headset",
										"mobilelove",
										"hands",
										"joint",
										"pelvis",
										"ear",
										"spinal",
										"changecontent",
										"firework",
										"buildingblock",
										"brakes",
										"tire",
										"brokenglass",
										"steeringwheel",
										"puzzle",
										"googleanalytic",
										"ghost",
										"handshake",
										"frontenddesign",
										"ring",
										"musicnote",
										"mask",
										"receptionbell",
										"it",
										"footsteps",
										"golf",
										"fuel",
										"jumpstart",
										"leather",
										"iron",
										"leftalign",
										"centeralign",
										"rightalign",
										"topalign",
										"middlealign",
										"bottomalign",
										"lightbulb",
										"shovel",									
										"fence",
										"carpet",
										"radiation",
										"container",
										"wreck",
										"divemask",
										"waves",
										"oxygen",
										"popup",
										"inpagemenu",
										"section",
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
										"notification",
										"question",
										"info",
										"blocked",
										"cancel-circle",
										"checkmark-circle",
										"close",
										"checkmark",
										"minus",
										"plus",
										"enter",
										"exit",
										"play",
										"pause",
										"stop",
										"backward",
										"forward2",
										"play2",
										"pause2",
										"stop2",
										"backward2",
										"forward2",
										"first",
										"last",
										"previous",
										"next",
										"eject",
										"volume-high",
										"volume-medium",
										"volume-low",
										"volume-mute",
										"volume-mute2",
										"volume-decrease",
										"volume-increase",
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
										"arrow-up-left",
										"arrow-up",
										"arrow-up-right",
										"arrow-right",
										"arrow-down-right",
										"arrow-down",
										"arrow-down-left",
										"arrow-left",
										"arrow-up-left2",
										"arrow-up2",
										"arrow-up-right2",
										"arrow-right2",
										"arrow-down-right2",
										"arrow-down2",
										"arrow-down-left2",
										"arrow-left2",
										"arrow-up-left3",
										"arrow-up3",
										"arrow-up-right3",
										"arrow-right3",
										"arrow-down-right3",
										"arrow-down3",
										"arrow-down-left3",
										"arrow-left3",
										"arrow-down-light",
										"arrow-left-light",
										"arrow-up-light",
										"arrow-right-light",
										"arrow-double-left-light",
										"arrow-double-right-light",
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
										"share",
										"google",
										"google-plus",
										"facebook",
										"instagram",
										"twitter",
										"twitter-2",
										"feed",
										"youtube",
										"vimeo",
										"vimeo2",
										"flickr",
										"wordpress",
										"apple",
										"windows8",
										"soundcloud",
										"skype",
										"linkedin",
										"pinterest",
										"houzz",
										
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
										"happy",
										"smiley",
										"tongue",
										"sad",
										"wink",
										"grin",
										"cool",
										"angry",
										"evil",
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
										"spider",
										"worm",
										"ant",
										"mosquito",
										"fleas",  
										"mite",
										"silverfish",
										"caterpillar",
										"beetle",
										"bone",  
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
										"truck",
										"car",
										"airplane",
										"train1",
										"dry-van",
										"flat-bed",
										"truck3",
										"taxi",
										"carside",
										"bus",
										"rocket",
										"rocket2",
										"towtruck",
										"cartruck",
										"boat",
										
										/* Crappicons */
										"landrover",
										"mustang",
										"toyota",
										"truck2",
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
  $html = nxs_ob_get_contents();
  nxs_ob_end_clean();
    
  // Setting the contents of the variable to the appropriate array position
  // The framework uses this array with its accompanying values to render the page
  $result["html"] = $html;
  return $result;
}
?>
