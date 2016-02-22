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
					
					<!-- INTERFACE -->					
					<div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("Interface", "nxs_td"); ?></h4></div>
							<div class="box-content">
								<ul>
									<?php
									$iconset = "nxs-icon";
									$icontypes = array
									(
										"pagedecorator",
										"cog",
										"wand",
										"wrench",
										"crop",
										"scissors",
										"filer",
										"image",
										"sliderbox",
										"music",
										"musicnote",
										"lightbulb",
										"radio-checked",
										"radio-unchecked",
										"clock",
										"brightness-medium",
										"brightness-contrast",
										"star",
										"star3",
										"star2",
										"heart",
										"socialsharing",
										"heart-broken",
										"tree",
										"cloud",
										"cloud-download",
										"cloud-upload",
										"network",
										"earth",
										"link",
										"flag",
										"connection",
										"support",
										"phone",
										"address-book",
										"notebook",
										"contact",
										"pushpin",
										"googlemap",
										"compass",
										"map",
										"alarm",
										"bell",
										"stopwatch1",
										"calendar-2",
										"calendar",
										"cabinet",
										"box-add",
										"box-remove",
										"undefined",
										"upload",
										"bubble",
										"comments",
										"quote",
										"user",
										"users",
										"quotes-left",
										"csv",
										"logo",
										"signpost",
										"callout",
										"blog",
										"trash",
										"palette",
										"target",
										"shield",
										"lightning",
										"switch",
										"plug",
										"eye",
										"bars2",
										"eye-blocked",
										"contrast",
										"book",
										"books",
										"busy",
										"categories",
										"tumbler",

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
					
					<!-- TEXT & DOCUMENTS -->
					<div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("Text & documents", "nxs_td"); ?></h4></div>
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
										"folder",
										"text",
										"profile",
										"copy",
										"copy2",
										"copy3",
										"paste",
										"file-pdf",
										"disk",
										"article-new",
										"article-overview",
										"file2",
										"wordpresstitle",
										"attachment",
										"signup",
										"changecontent",

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
						
					<!-- ORGANIZE -->
					<div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("Organize", "nxs_td"); ?></h4></div>
							<div class="box-content">
								<ul>
									<?php
									$iconset = "nxs-icon";
									$icontypes = array
									(
										"equalizer",
										"list",
										"list2",
										"numbered-list",
										"leftalign",
										"centeralign",
										"rightalign",
										"topalign",
										"middlealign",
										"bottomalign",
										"template",
										"header",
										"sidebar",
										"footer",
										"subfooter",
										"subheader",
										"dashboard",
										"pagepopup",
										"fixedheader",
										"sections",
										"inpage",
										"menucontainer",
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

					<!-- DEVICES -->
                    <div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("Devices", "nxs_td"); ?></h4></div>
							<div class="box-content">
								<ul>
									<?php
									$iconset = "nxs-icon";
									$icontypes = array
									(
										"mobilelove",
										"film",
										"camera-2",
										"keyboard",
										"screen",
										"laptop",
										"frontenddesign",
										"mobile",
										"tablet",
										"tv",
										"print",
										"headphones",
										"headset",
										"mobile2",
										"security-camera",
										"gallerybox",
										"usb1",
										"usb2",
										"hdd",
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


					<!-- COMMERCE -->
					<div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("Commerce", "nxs_td"); ?></h4></div>
							<div class="box-content">
								<ul>
									<?php
									$iconset = "nxs-icon";
									$icontypes = array
									(
										"tag",
										"tags",
										"qrcode",
										"ticket",
										"cart",
										"credit",
										"dollar",
										"euro",
										"pound",
										"pie",
										"stats",
										"bars",
										"googleanalytic",
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

                    <!-- CONTROLS -->
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
										"search",
										"searchresults",
										"zoom-out",
										"zoom-in",
										"new-tab",
										"embed",
										"htmlcustom",
										"stack",
										"remove-sign",
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
                                        
                    <!-- ARROWS -->
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
										"undo",
										"redo",
										"undo2",
										"redo2",
										"forward",
										"reply",
										"loop",
										"loop2",
										"loop3",
										"history",
										"expand",
										"shuffle",
										"contract",
										"expand2",
										"contract2",
										"move",
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
					
					<!-- GENERAL -->
					<div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("General", "nxs_td"); ?></h4></div>
							<div class="box-content">
								<ul>
									<?php
									$iconset = "nxs-icon";
									$icontypes = array
									(
										"droplet",
										"dice",
										"pacman",
										"spades",
										"clubs",
										"diamonds",
										"bug",
										"drawer",
										"drawer2",
										"binoculars",
										"key",
										"lock2",
										"lock",
										"unlocked",
										"aid",
										"gift",
										"trophy",
										"glass",
										"food",
										"leaf",
										"hammer",
										"fire",
										"lab",
										"magnet",
										"briefcase",
										"road",
										"accessibility",
										"thumbs-up",
										"thumbs-up2",
										"point-up",
										"point-down",
										"point-right",
										"point-left",
										
										/* Custom */
										"construction",
										"carousel",
										"faucet",
										"hardhat",
										"herring",
										"horseshoe",
										"matchresults",
										"mug",
										"oven",
										"plunger",
										"safe",
										"referee",
										"vacuum-cleaner",
										"window",
										"yoga",
										"zen",
										"snowflake",
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
										"disabled",
										"ribbon",
										"hands",
										"joint",
										"pelvis",
										"ear",
										"spinal",
										"firework",
										"buildingblock",
										"puzzle",
										"ghost",
										"handshake",
										"ring",
										"mask",
										"receptionbell",
										"it",
										"footsteps",
										"golf",
										"leather",
										"iron",
										"shovel",									
										"fence",
										"carpet",
										"radiation",
										"container",
										"wreck",
										"divemask",
										"waves",
										"oxygen",
										"education",
										"beer",
										"scaffold",
										"bowling",
										"needle",
										"tennisball",
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

					<!-- SOCIAL -->
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
										"google-drive",
										"yelp",
										
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
					
					<!-- BUILDINGS -->
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
                    
                    <!-- VEHICLES -->
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
										"bike",
										"dry-van",
										"flat-bed",
										"fuel",
										"jumpstart",
										"brakes",
										"tire",
										"brokenglass",
										"steeringwheel",
										"dashboard2",
										"mot",
										
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

                    <!-- SMILIES -->
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
                    
                    <!-- ANIMALS -->
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
										"paw",  
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
                                        
                    <!-- ZODIAC -->
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
