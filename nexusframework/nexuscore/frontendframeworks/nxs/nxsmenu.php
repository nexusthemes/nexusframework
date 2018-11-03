<?php
	if (!is_user_logged_in())
	{
		// gebruikers die niet zijn ingelogd hebben hier niets te zoeken...
		return;
	}
	if (!nxs_hassitemeta())
	{
		return;
	}
	
 	$current_user = wp_get_current_user();
 	

	if (!nxs_has_adminpermissions())
	{
		// als er geen recht is om posts te editen, dan heeft het nxsmenu geen nut
		return;
	}
	
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
	if (nxs_shouldshowadminbar())
	{
		$adminbarclass = "wpadminbar";
	}
	
	if (is_singular())
	{
		global $post;
		$postid = $post->ID;
		if ($postid == "" || $postid == 0)
		{
			$pagetemplate = "archive";
			$posttype = "post";
			$nxsposttype = nxs_getnxsposttype_by_wpposttype($posttype);
		}
		else
		{
			$posttype = $post->post_type;
			$postmeta = nxs_get_corepostmeta($postid);
			$pagetemplate = nxs_getpagetemplateforpostid($postid);
			$nxsposttype = nxs_getnxsposttype_by_wpposttype($posttype);
		}
	}
	else
	{
		$pagetemplate = "archive";
		$posttype = "post";
		$nxsposttype = nxs_getnxsposttype_by_wpposttype($posttype);
	}
	
	$prtargs = array();
	$prtargs["invoker"] = "nxsmenu";
	$prtargs["wpposttype"] = $posttype;
	$prtargs["nxsposttype"] = $nxsposttype;
	$prtargs["pagetemplate"] = $pagetemplate;
	$postrowtemplates = nxs_getpostrowtemplates($prtargs);
	$sitemeta = nxs_getsitemeta();
	
	$googlewebfont_activity = "nexusframework:usegooglefonts";
	if (nxs_dataprotection_isactivityonforuser($googlewebfont_activity))
	{
		?>
		<!-- loading all fonts -->
		
		<script data-cfasync="false"  src="https://www.google.com/jsapi"></script>
		<script data-cfasync="false" >
			google.load('webfont','1');
		</script>
		<?php
		/* FONT HANDLING v2 START */
		// alle fonts worden hier ingeladen, anders kan gebruiker niet zien welke keuze hij maakt
		$allfontfams = array();
		$allfonts = nxs_getfonts();
		foreach ($allfonts as $currentfontid=>$meta)
		{
			$currentfontfams = nxs_getmappedfontfams($currentfontid);
			foreach ($currentfontfams as $currentfontfam)
			{
				$allfontfams[] = $currentfontfam;
			}
		}
		?>
		<script> 
			// loading fonts (nxsmenu)
			
			WebFont.load
			(
				{
					google: 
					{ 
		      	families: 
		      	[
		      		<?php
		      		// some fonts produce a 403 or 400, we skip these	
		      		$skipfonts = nxs_font_getskipfonts();
		      		foreach ($skipfonts as $skipfont)
		      		{
		      			if(($key = array_search($skipfont, $allfontfams)) !== false) 
		      			{
						   	 unset($allfontfams[$key]);
								}
							}
		      		
		      		$isfirstfont = true;
		      		foreach ($allfontfams as $currentfont)
		      		{
		      			// skip invalid items
		      			if (true)
		      			{
			      			//
			      			$googlewebfontspiece = $currentfont;
			      			$googlewebfontspiece = trim($googlewebfontspiece);
									
									$isvalid = true;
									
									if ($googlewebfontspiece == "")
									{
										$isvalid = false;
									}
									
									$quotecount = substr_count ($googlewebfontspiece, "'");
									if ($quotecount != 0 && $quotecount != 2)
									{
										$isvalid = false;
									}
									
									$quotecount = substr_count ($googlewebfontspiece, "\"");
									if ($quotecount != 0 && $quotecount != 2)
									{
										$isvalid = false;
									}
									
									if (!$isvalid)
									{
										// skip this item
										continue;
									}
								}
									
		      			//
		      			
		      			if ($isfirstfont == false)
		      			{
		      				echo ",";
		      			}
		      			else
		      			{
		      				$isfirstfont = false;
		      			}
		      			
		      			
		      			
		      			if (nxs_stringcontains($currentfont, "'"))
		      			{
		      				echo "{$currentfont}";
		      			}
		      			else
		      			{
		      				// als het font al quotes bevat, dan niet wrappen in single QUOTES!!!!!
		      				echo "'{$currentfont}'";
		      			}
		      		}
		      		?>
		      	] 
		      }
				}
			); 
		</script>
		<?php
	}
?>
<div id="nxs-menu-outerwrap" style='display: none;'>
	<div id="nxs-menu-wrap" class='nxs-admin-wrap'>
		<div class="nxs-hidewheneditorinactive" style='display: none;'>
			<div class="nxs-hidewhenmenuinactive" style='display: none;'>
				<?php 
				if 
				(
					$nxsposttype == "post" ||
					$nxsposttype == "sidebar" ||
					$nxsposttype == "pagelet" ||
					$nxsposttype == "header" ||
					$nxsposttype == "footer" ||
					$nxsposttype == "subheader" ||			
					$nxsposttype == "subfooter"
				)
				{
				?>
			
		    <div id="nxs-admin-tabs" class="tabs">
		
		        <ul class="tabs">
		            <li><a href="#tabs-content"><?php nxs_l18n_e("Content[nxs:adminmenu,tab]", "nxs_td"); ?></a></li>
		            <li><a href="#tabs-design"><?php nxs_l18n_e("Design[nxs:adminmenu,tab]", "nxs_td"); ?></a></li>
		            <?php 
		            if (nxs_cap_hasdesigncapabilities()) 
		            {
		            	if ($_REQUEST["customcss"] == "true") 
									{		            	
			            	?>
		            			<li><a href="#tabs-css"><?php nxs_l18n_e("CSS[nxs:adminmenu,tab]", "nxs_td"); ?></a></li>
		          			<?php 
		          		} 
		          	}
		          	?>
		            <?php do_action('nxs_ext_injecttab'); ?>
		        </ul>
		        
		        <div id="tabs-content">
			        <div class="tabs nxs-vertical-tabs" >
			            
			            <ul class="nxs-vertical-tabs nxs-clear">
			                <li><a href="#tabs-rows"><?php nxs_l18n_e("Layout[nxs:adminmenu,subtab]", "nxs_td"); ?></a></li>
			            </ul>
			            
			            <div class="content nxs-margin-tabs nxs-padding10">
			                
			                <div id="tabs-rows">
			                	<ul class="drag nxs-fraction">
			                		<?php    	
															// for each placeholder -->
															foreach ($postrowtemplates as $currentpostrowtemplate)
															{
																require_once(NXS_FRAMEWORKPATH . '/nexuscore/pagerows/templates/' . $currentpostrowtemplate . '/' . $currentpostrowtemplate . '_render.php');
																
																$functionnametoinvoke = 'nxs_pagerowtemplate_render_' . $currentpostrowtemplate . "_toolbox";
																
																$args = array();
																$args["postid"] = $postid;
																$args["pagerowtemplate"] = $currentpostrowtemplate;
																if (function_exists($functionnametoinvoke))
																{
																	echo "<li class='nxs-toolbox-item nxs-draggable nxs-dragtype-pagerowtemplate' id='nxs_pagerowtemplate_" . $currentpostrowtemplate . "' title='" . nxs_l18n__("Drag and drop this row below[nxs:adminmenu,tooltip]", "nxs_td") . "'>";
																	call_user_func($functionnametoinvoke, $args);
																	echo "</li>";
																}
																else
																{
																	echo "function not found;" . $functionnametoinvoke;
																}
															}
														?>
												</ul>                        
											
		                  	<div class="nxs-clear"></div>
		              
			                </div> <!--END tabs-->
			            
			            </div> <!--END content-->
			                
			        </div> <!--END tabs-->
		        </div> <!--END tabs-->
		        
		        <div id="tabs-design">
		          <div class="tabs nxs-vertical-tabs" >
		              
		              <ul class="nxs-vertical-tabs nxs-clear">
	                  <li><a href="#tabs-kleuren"><?php nxs_l18n_e("Colors[nxs:adminmenu,subtab]", "nxs_td"); ?></a></li>
	                  <li><a href="#tabs-lettertypen"><?php nxs_l18n_e("Fonts[nxs:adminmenu,subtab]", "nxs_td"); ?></a></li>
		              </ul>
		              
		              <div class="content nxs-margin-tabs nxs-padding10">
		
		                  <div id="tabs-kleuren">
		
													<?php
													$coloradjustdisplaystyle = "";
													if (!nxs_cap_hasdesigncapabilities())
													{
														// important; we only HIDE the color palette,
														// we _DO_ render it on purpose, this is because some
														// functions depend on the DOM elements! (change palette)
														$coloradjustdisplaystyle = "display: none;";
													}
													?>
		
		                  		<div>	
		     		            
                          <?php
		     		              	$palettenames = nxs_colorization_getpalettenames(false);
		     		              	$shouldrenderpalette = count($palettenames) > 1;
														if ($shouldrenderpalette)
														{
			     		              	$activepalettename = nxs_colorization_getactivepalettename();
															$foundatleastone = false;
															
															$what = array("showactiveitems", "showinactiveitems");
															
															foreach ($what as $whatnow)
															{
																foreach ($palettenames as $key=>$currentpalettename) 
																{
																	if ($whatnow == "showactiveitems")
																	{
																		// only show active one
																		if ($currentpalettename != $activepalettename)
																		{
																			continue;
																		}
																	}
																	else if ($whatnow == "showinactiveitems")
																	{
																		// only show active one
																		if ($currentpalettename == $activepalettename)
																		{
																			continue;
																		}
																	}
																	
																	if ($key == "@@@nxsempty@@@") {
																		// skip
																		$currentpalettename = "";
																	} else {
																		$foundatleastone = true;
																		
																		echo'
																			<div class="block disablednxs-width200 nxs-float-left nxs-margin-right10">
																				<div class="content2">
																					<div class="box">';
																						
																						nxs_colorization_renderpalette($currentpalettename);
																						
																						// Activate button
																						if ($activepalettename != $currentpalettename) {																
																							echo'<a class="nxsbutton1 nxs-float-right" href="#" onclick="nxs_js_menu_activatepalette(\'' . $currentpalettename . '\'); return false;">Activate</a>';
																							//echo "[" . $activepalettename . "] / [" ;
																							//echo $currentpalettename;
																							//echo "]";
																						}
																					
																						echo'
																						<div class="nxs-clear"></div>
																					</div> <!-- END box -->
																				</div> <!-- END content -->
																			</div> <!-- END block -->';
																	}
																}
															}
															?>
			     		              	<div class="nxs-clear"></div>
		     		              	<?php
		     		              	}
		     		              	?>
		     		              </div>

		                      <div style='<?php echo $coloradjustdisplaystyle; ?>'>
			                      
			                  		<?php
														$colortypes = nxs_getcolorsinpalette();
														foreach($colortypes as $currentcolortype)
														{
															
															if ($currentcolortype == "base")
			                        {
			                        	$hidecolorboxstyle = "style='display: none;'";
			                        }
			                        else 
			                        {
			                        	$hidecolorboxstyle = "";
			                        }												
															?>
			
									              <!-- <?php echo $identification; ?> color -->
					                      
				                      <div class="nxs-float-left nxs-margin-right10" <?php echo $hidecolorboxstyle; ?>>
			                          <div class="block disablednxs-width200">
																<?php
															
															$palettename = nxs_colorization_getactivepalettename();
															
															if (isset($palettename) && $palettename != "")
															{
																if (!nxs_colorization_paletteexists($palettename))
																{
																	$colorizationproperties = array();
																}
																else
																{
																	// use colorization v2 implementation
																	$colorizationproperties = nxs_colorization_getpersistedcolorizationproperties($palettename);
																	//var_dump($colorizationproperties);
																}
															}
															
															$subtypes = array("1", "2");
															foreach($subtypes as $currentsubtype)
															{
																$identification = $currentcolortype . $currentsubtype;
																
																if (isset($colorizationproperties))
																{
																	// use colorization v2 implementation
																	if (isset($colorizationproperties["colorvalue_" . $identification]))
																	{
																		$currentcolorhexvalue = $colorizationproperties["colorvalue_" . $identification];
																	}
																	else
																	{
																		// color is not (yet) supported in this palette
																		$currentcolorhexvalue = "777777";
																	}
																}
																else
																{
																	$currentcolorhexvalue = $sitemeta["vg_color_" . $identification . "_m"];
																}
																
																if ($currentsubtype == "1")
			                          {
			                          	$hidecolorpickerstyle = "style='display: none;'";
			                          }
			                          else 
			                          {
			                          	$hidecolorpickerstyle = "";
			                          }
			                          
			                          if ($currentsubtype == "1") 
			                          {
			                          	$currentcolorhexvalue = "C6C6C6";	
			                          }
			                          else if ($currentsubtype == "2") 
			                          {
			                          	if ($currentcolortype == "base")
			                          	{
			                          		$currentcolorhexvalue = "333333";
			                          	}
			                          }
																?>
																
			                          <!-- <?php echo $identification; ?> color middle -->
			                          
			                          <div class="content2" <?php echo $hidecolorpickerstyle; ?>>
			                              <div class="box">
			                                  <div class="box-title2"><p><?php nxs_l18n_e("Color[nxs:colorpalette]", "nxs_td"); ?></p></div>
			                                  	<div class="box-content2"><input type="text" id='vg_color_<?php echo $identification; ?>_m' class="color-picker" size="6" value="<?php echo $currentcolorhexvalue; ?>" /></div>
			                                  <div class="nxs-clear"></div>
			                              </div>
			                          </div> <!--END content-->
			                          
			                          <!--
			                          <div class="content2" style=''>
			                              <div class="box">
			                                  <div class="box-title2"><p><?php nxs_l18n_e("Opposite Color[nxs:colorpalette]", "nxs_td"); ?></p></div>
			                                  <div class="box-content2"><input type="text" id='vg_color_<?php echo $identification; ?>_o_m' class="color-picker" size="6" value="<?php echo $sitemeta["vg_color_" . $identification . "_o_m"];?>" /></div>
			                                  <div class="nxs-clear"></div>
			                              </div>
			                          </div>
			                          -->
			                          
					                     	<?php
					                    }
					                    ?>
					                    
					                    
					                     </div> <!--END block -->
					                      </div> <!--END div-->
					                    <?php
				                   	}
				                    ?>
	
				                    <!-- temporarily turned off -->
			                      <div class="nxs-float-left nxs-margin-right10" style="display: none;">
			                        <div class="block nxs-width300">
			                          <div class="nxs-admin-header"><h3>Color Wizard</h3></div>
			                          <div class="content2" style=''>
			                            <div class="box">
			                              <div class="box-title2">Type</div>
			                              <div class="box-content2">
			                              	<select name='colorderiver' id='colorderiver' onchange='nxs_js_updatecolorwizard();'>
			                              		<option value=''></option>
			                              		<option value='mono'>Mono</option>
			                              		<option value='complementary'>Complementary</option>
			                              		<option value='splitcomplementary'>Split complementary</option>
			                              		<option value='splittriad'>Split triad</option>
			                              		<option value='analogic'>Analogic</option>
			                              		<option value='accentedanalogic'>Accented analogic</option>
			                              		<option value='tetrad'>Tetrad</option>
			                              	</select>
			                              </div>
			                              <div class="nxs-clear"></div>                              
			                              <div class="box-title2">Delta</div>
			                              <div class="box-content2">
			                              	<div id='anglecontroller' style='display: none;'>
			                              		<a href='#' class='nxsbutton1' onclick="nxs_js_adjustangledelta(-5);">-</a>
			                              		<input type='text' id='dyncolangle' value='30' onkeydown="nxs_js_updatecolorwizard();" />
			                              		<a href='#' class='nxsbutton1' onclick="nxs_js_adjustangledelta(5);">+</a>
			                              	</div>
			                              </div>
			                              <div class="nxs-clear"></div>
			                              <div id='wizarddyncolorcontainer'>
			                              </div>
			                              <div class="nxs-clear"></div>
			                            </div>
			                          </div> <!--END content-->
			                      	</div>
			                      </div>

														<div class="nxs-clear padding"></div>
														<a class="nxsbutton1 nxs-float-left" href='#' onclick="nxs_js_popup_site_neweditsession('managecolorization'); return false;"><?php nxs_l18n_e("Manage", "nxs_td"); ?></a>
			            					<a style='display: none;' href='#' class="nxs_menu_savekleurenbutton nxsbutton nxs-float-left" onclick='nxs_js_menu_savepalette(); return false;'><?php nxs_l18n_e("Save[nxs:btn]", "nxs_td"); ?></a>
			            					<a style='display: none;' href='#' class="nxs_menu_savekleurenbutton nxsbutton nxs-float-left" onclick='nxs_js_menu_createnewpalette(); return false;'><?php nxs_l18n_e("Create new", "nxs_td"); ?></a>
				                      
														<div class="nxs-clear padding"></div>
			                  	</div>
		     		              
													<script>
														function nxs_js_menu_activatepalette(palettename)
														{
															var initialcontext = 
															{
																"palettename" : palettename
															};
															nxs_js_popup_site_neweditsession_v2("doactivatepalette", initialcontext);
															// 
														}
														function nxs_js_menu_savepalette()
														{
															var initialcontext = 
															{
																"how" : "override"
															};
															nxs_js_popup_site_neweditsession_v2("dosavepalette", initialcontext);
															// 
														}
														function nxs_js_menu_createnewpalette()
														{
															var initialcontext = 
															{
																"how" : "new"
															};
															nxs_js_popup_site_neweditsession_v2("dosavepalette", initialcontext);
															// 
														}														
													</script>
		     		              
		     		              <div class="nxs-clear"></div>
		                  
		                  </div> <!--END tabs-->
		                  
											<div id="tabs-lettertypen">
												<?php
												$googlewebfont_activity = "nexusframework:usegooglefonts";
												if (nxs_dataprotection_isactivityonforuser($googlewebfont_activity))
												{
													?>
		                      <div class="nxs-float-left nxs-margin-right10">
		                          <div class="block">
		                            <div class="nxs-admin-header"><h3><?php nxs_l18n_e("Fonts[nxs:adminmenu,subtab,heading]", "nxs_td"); ?></h3></div>
		                              <?php
	                              	$fontidentifiers = nxs_font_getfontidentifiers();
	                              	foreach ($fontidentifiers as $currentfontidentifier)
	                              	{
	                              		//
	                              		?>
																		<div class="content2">
		                                  <div class="box">
	                                      <div class="box-title2"><p><?php echo "Font {$currentfontidentifier}"; ?></p></div>
	                                      <div class="box-content3" style='width: auto;'>
																					<select id="vg_fontfam_<?php echo $currentfontidentifier; ?>" onchange='nxs_js_font_updatefonts();'>
																						<?php 
																							$vg_fontfam = $sitemeta["vg_fontfam_{$currentfontidentifier}"];
																							$fontlist = nxs_getfonts();
																							foreach ($fontlist as $fontid => $fontdata)
																							{
																								if ($vg_fontfam == $fontid)
																								{
																									$selected = "selected='selected'";	
																								}
																								else
																								{
																									$selected = "";
																								}
																								?>
																								<option <?php echo $selected; ?> value="<?php echo $fontid; ?>"><?php echo $fontid; ?></option>
																								<?php
																							}
																						?>
																					</select>                                        	
	                                      </div>
	                                      <div class="nxs-clear"></div>
		                                  </div>
			                              </div> <!--END content-->
			                             	<?php
			                            }
		                              ?>
		                          </div> 
		                      </div> 
		                  
		                  		<div class="nxs-clear padding"></div>
		                  		
		                  		<a href="#" onclick="nxs_js_popup_site_neweditsession('webfontshome'); return false;" class="nxsbutton1 nxs-float-left"><?php nxs_l18n_e("Manage", "nxs_td"); ?></a>
		     		              <a id='nxs_menu_savelettertypenbutton' style='display: none;' href='#' class="nxsbutton nxs-float-left" onclick='nxs_js_font_savefonts(); return false;'><?php nxs_l18n_e("Save[nxs:btn]", "nxs_td"); ?></a>
		     		              
		     		              <div class="nxs-clear"></div>
	                  			<?php
	                  		}
	                  		else
	                  		{
	                  			// not allowed
	                  		}
	                  		?>
		              	</div>
		                  
		              </div> <!--END content-->
		                  
		          </div> <!--END tabs-->
		        </div> <!--END tabs-->
		        
		        <?php
		        if ($_REQUEST["customcss"] == "true")
		        {
			      	?>
			        <div id="tabs-css" style='<?php if (!nxs_cap_hasdesigncapabilities()) { ?>display: none;<?php } ?>'>
				        <div class="content nxs-padding10">
				          <div class="box">
				            <textarea id='vg_manualcss' onkeyup='nxs_js_menu_updateoverridenmanualcss();'><?php echo nxs_render_html_escape_gtlt($sitemeta["vg_manualcss"]);?></textarea>
				          </div>
									<div class="nxs-clear padding"></div>
				          <a id='nxs_menu_savemanualcssbutton' style='display: none;' href='#' class="nxsbutton nxs-float-left" onclick='nxs_menu_savemanualcss(); return false;'><?php nxs_l18n_e("Save[nxs:btn]", "nxs_td"); ?></a>
	
				          <div class="nxs-clear"></div>
				        </div> <!--END content-->
			        </div> <!--END tabs-->
			        <?php
			      }
			      ?>
			      
		        <?php do_action('nxs_ext_injecttabcontent'); ?>
		    </div> <!--END tabs-->
		    
		    <?php
		  	}
		    ?>
		   </div>
		</div>
	</div> <!--END nxs-menu-wrap-->
	
	<div class="nxs-admin-wrap">
		<ul class="admin nxs-no-click-propagation <?php echo $adminbarclass; ?>">			
			<?php
			
			//
			$tag = "nxs_menu_renderlicensenotification";
			if (has_action($tag))
			{
				do_action($tag);
			}
			else
			{
				// default framework generic
			
				$licensekey = nxs_license_getlicensekey();
				if ($licensekey == "")
				{
					$shouldshow = true;
					
					if (NXS_FRAMEWORKSHARED === "true")
					{
						// nothing to do here
						$shouldshow = false;
					}
					
					if ($shouldshow)
					{
						$url = admin_url('admin.php?page=nxs_admin_license');
						
						$notificationargs = array
						(
							"circle_color" => "#FF0000", 
							"text_color" => "#FFFFFF", 
							"text" => "1"
						);
						$notification = nxs_gethtmlfornotification($notificationargs);
						?>
						<li class="nxs-hidewheneditorinactive">
							<?php echo $notification; ?>
							<a href="<?php echo $url;?>" class='site' title="<?php nxs_l18n_e("Register your purchase to receive free updates and support", "nxs_td"); ?>">
								<span class='nxs-icon-key'></span>
							</a>
						</li>
						<?php
					}
				}
				else
				{
					$url = admin_url('admin.php?page=nxs_admin_update');
					$themeupdate = get_transient("nxs_themeupdate");
					
					$shouldrender = false;
					if ($themeupdate["nxs_updates"] == "yes")
					{
						$shouldrender = true;
					}
					if (NXS_FRAMEWORKSHARED === "true")
					{
						$shouldrender = false;
					}
					
					if ($shouldrender)
					{
						$notificationargs = array
						(
							"circle_color" => "#FF0000", 
							"text_color" => "#FFFFFF", 
							"text" => "1"
						);
						$notification = nxs_gethtmlfornotification($notificationargs);
						
						?>
						<li class="nxs-hidewheneditorinactive">
							<?php echo $notification; ?>
							<a href="<?php echo $url; ?>" class='site' title="<?php nxs_l18n_e("Theme update available", "nxs_td"); ?>">
								<span class='nxs-icon-loop2'></span>
							</a>
						</li>
						<?php
					}
				}
			}
			
			//
			
			if (nxs_issiteinmaintenancemode())
			{
				?>
				<li class="nxs-hidewheneditorinactive">
					<a href="#" onclick="nxs_js_popup_site_neweditsession('maintenancehome'); return false;" class='site' title="<?php nxs_l18n_e("Maintenance mode activated", "nxs_td"); ?>">
						<span class='nxs-icon-construction'></span>
					</a>
				</li>
				<?php
			}
			
			
			$cookiewallactivity = nxs_dataprotection_getcookiewallactivity();
			if (nxs_dataprotection_isoperational($cookiewallactivity))
			{
				?>
				<li class="nxs-hidewheneditorinactive">
					<a href="#" class='site' onclick="nxs_js_popup_site_neweditsession('dataprotectioncookiewallhome'); return false;" title="<?php nxs_l18n_e("Cookie wall (active)", "nxs_td"); ?>">
						<span class='nxs-icon-concrete'></span>
					</a>
				</li>
				<?php
			}
			
			?>
			<li class="nxs-hidewheneditorinactive">
				<a href="<?php bloginfo('url'); ?>" title="<?php nxs_l18n_e("Home[nxs:adminmenu,tooltip]", "nxs_td"); ?>" class='site' >
					<span class='nxs-icon-home'></span>
				</a>
			</li>
		  <li class="nxs-hidewheneditorinactive">
		  	<a href="#" title="<?php nxs_l18n_e("New[nxs:adminmenu,tooltip]", "nxs_td"); ?>" onclick="nxs_js_popup_site_neweditsession('newposthome'); return false;" class="site">
		  		<span class='nxs-icon-article-new'></span>
		  	</a>
		  </li>
		  <!-- -->
		  <li class="nxs-sub-menu nxs-hidewheneditorinactive">
		  	<a href="<?php echo admin_url('edit.php?post_type=page&orderby=date&order=desc'); ?>" title="<?php nxs_l18n_e("Pages[nxs:adminmenu,tooltip]", "nxs_td"); ?>" class="site">
		  		<span class='nxs-icon-article-overview'></span>
		  	</a>
		    <ul> 	
		    	<li>
		      	<a href="<?php echo admin_url('edit.php?orderby=date&order=desc'); ?>" title="<?php nxs_l18n_e("Blog Posts", "nxs_td"); ?>" class="site">
		      		<span class='nxs-icon-book'></span>		
		      	</a>
		      </li>
		      <li>
		      	<a href="<?php echo admin_url('upload.php'); ?>" title="<?php nxs_l18n_e("Media manager[nxs:adminmenu,tooltip]", "nxs_td"); ?>" class="site">
		      		<span class='nxs-icon-image'></span>		
		      	</a>
		      </li>
		      <?php
		      if (nxs_cap_hasdesigncapabilities())
		      {
			      ?>
			      <li>
	     		  	<a href="<?php echo home_url('/'); ?>?nxs_admin=admin&backendpagetype=templateparts" title="<?php nxs_l18n_e("Template parts", "nxs_td"); ?>" class="site">
					  		<span class='nxs-icon-template'></span>
					  	</a>
			      </li>
			      <li>
			      	<a href="<?php echo home_url('/'); ?>?nxs_admin=admin&backendpagetype=pagedecorators" title="<?php nxs_l18n_e("Pagedecorators[nxs:adminmenu,tooltip]", "nxs_td"); ?>" class="site">
			      		<span class='nxs-icon-pagedecorator'></span>
			      	</a>
			      </li>
			      <?php
			     	$url = nxs_geturl_home();
						$url = nxs_addqueryparametertourl_v2($url, "nxs_busrulesset", "pagetemplaterules", true, true);
		      	?>
			      <li>
	     		  	<a href="<?php echo $url; ?>" title="<?php nxs_l18n_e("Business rules", "nxs_td"); ?>" class="site">
					  		<span class='nxs-icon-wand'></span>
					  	</a>
			      </li>
			      <li>
			      	<a href="<?php echo home_url('/'); ?>?nxs_admin=admin&backendpagetype=forms" title="<?php nxs_l18n_e("Forms", "nxs_td"); ?>" class="site">
			      		<span class='nxs-icon-pencil2'></span>
			      	</a>
			      </li>
			      <?php
			    }
			    ?>
		    </ul>
		  </li>
		  <?php
		  	// comments icon => delegate implementation to active comments provider
				echo nxs_commentsprovider_getflyoutmenuhtml();
		  ?>

		  <li class="nxs-hidewheneditorinactive">
		  	<span class="nxs-menu-spacer">&nbsp;</span>
		 	</li>
		 
		    
		  <!-- menu -->
		  
		  <?php 
		  if 
		  (
		  	$nxsposttype == "post" ||
		  	$nxsposttype == "sidebar" ||
		  	$nxsposttype == "pagelet" ||
		  	$nxsposttype == "subheader" ||
		  	$nxsposttype == "subfooter" ||
		  	$nxsposttype == "header" ||
		  	$nxsposttype == "footer"
		  )
		  {
		  ?>
		  <li class="nxs-hidewheneditorinactive">
		  	<a href="#" id='nxsmenu' class="nxs-menu-toggler site">
		  		<span class='nxs-icon-arrow-up' style='display: none;'></span>
		  		<span class='nxs-icon-arrow-down' style='display: none;'></span>
		  	</a>
		  </li>
		  <?php 
			}
		  ?>    
		  
		  <?php 
		  
	  	global $post;
	  	if ($_REQUEST["nxs_templatepart"] != "")
	  	{
	  		$wordpressbackendurl = get_admin_url();
	  	}
	  	else if ($post != null && $post->post_type == "nxs_vtemplate")
	  	{
	  	  $wordpressbackendurl = get_admin_url();
	  	}
		  else if (in_array($nxsposttype, array("post", "service")) && is_singular())
		  {
				$wordpressbackendurl = get_edit_post_link($postid, array());	
		  } 
		  else if (is_archive())
		  {
		  	global $wp_query;
		  	$tq = $wp_query->tax_query;
		  	$qs = $tq->queries;
		  	$q = $qs[0];
		  	$taxonomy = $q["taxonomy"];
		  	if ($taxonomy != "")
		  	{
			  	$terms = $q["terms"];
			  	$term = $terms[0];
			  	$termobj = get_term_by("slug", $term, $taxonomy);
			  	
			  	$termid = $termobj->term_id;
					//var_dump($termid);		  	
				  $wordpressbackendurl = admin_url("edit-tags.php?action=edit&taxonomy={$taxonomy}&tag_ID={$termid}");
				}
			 	else
			 	{
			 		$wordpressbackendurl = get_admin_url();
				}
		  }
		  else
		  {
		  	// fall back
		  	$wordpressbackendurl = get_admin_url();
		  	$wordpressbackendurl = nxs_addqueryparametertourl_v2($wordpressbackendurl, "nxsposttype", $nxsposttype, true, true);
		  }
		  
		  do_action('nxs_ext_injectmenuitem');
		  ?>
		  <li class="nxs-hidewheneditorinactive"><span class="nxs-menu-spacer">&nbsp;</span></li>
		  
		  <?php
		  
	  	$pagedecoratorexists = false;
	  	
		  // page decorator items
			$templateproperties = nxs_gettemplateproperties();
		
			if ($templateproperties["result"] == "OK")
			{
				$pagedecoratorid = $templateproperties["pagedecorator_postid"];
				if (isset($pagedecoratorid))
				{
					$poststatus = get_post_status($pagedecoratorid);
					if ($poststatus == "publish" || $poststatus == "future")
					{
						$pagedecoratorexists = true;
						
						$currenturl = nxs_geturlcurrentpage();
						$nxsrefurlspecial = urlencode(base64_encode($currenturl));
						$refurl = nxs_geturl_for_postid($pagedecoratorid);
						$refurl = nxs_addqueryparametertourl_v2($refurl, "nxsrefurlspecial", $nxsrefurlspecial, false);
						
						?>
						<li class="nxs-hidewheneditorinactive nxs-sub-menu">
							<a class='site' title="<?php nxs_l18n_e("Page decorator", "nxs_td"); ?>" href='<?php echo $refurl; ?>'>
								<span class='nxs-icon-pagedecorator'></span>
							</a>
							<ul>
							<?php
							$parsedpagedecoratorstructure = nxs_parsepoststructure($pagedecoratorid);
							$rowindex = -1;
							foreach ($parsedpagedecoratorstructure as $currentdecoratoritem)
							{
								$rowindex++;
								$content = $currentdecoratoritem["content"];
								$pagewidgetplaceholderid = nxs_parsepagerow($content);
								$placeholdermetadata = nxs_getwidgetmetadata($pagedecoratorid, $pagewidgetplaceholderid);
								$widget = $placeholdermetadata["type"];
								if (isset($widget) && $widget != "" && $widget != "undefined")
								{										
									// load the type in mem
									// inject widget if not already loaded, implements *dsfvjhgsdfkjh*
								 	$requirewidgetresult = nxs_requirewidget($widget);
								 	if ($requirewidgetresult["result"] == "OK")
								 	{
										$iconid = nxs_getwidgeticonid($widget);

										$title = nxs_getplaceholdertitle($widget);
										$invoke = "var args={containerpostid:'$pagedecoratorid',postid:'$pagedecoratorid',placeholderid:'$pagewidgetplaceholderid',rowindex:'$rowindex',sheet:'home',onsaverefreshpage:true}; nxs_js_popup_placeholder_neweditsession_v2(args); return false;";
								 		?>
										<!-- page decorator has sub items -->
										<li>
							      	<a href="#" onclick="<?php echo $invoke; ?>" class="site" title="<?php echo $title; ?>">
							      		<span class='<?php echo $iconid; ?>'></span>
							      	</a>				
							      </li>
						 				<?php
						 			}
						 		}
						 	}
							?>
							</ul>
						</li>
						<?php
					}
				}
			}
			
			if (!$pagedecoratorexists)
			{
				?>
				<li class="nxs-hidewheneditorinactive nxs-sub-menu" style='opacity: 0.5;'>
					<a class='site' title="<?php nxs_l18n_e("Page decorator", "nxs_td"); ?>" href='#' onclick="nxs_js_alert_sticky('This page has no pagedecorator configured. To learn to enable it, see <a target=\'_blank\' style=\'color: blue; text-decoration: underline;\' href=\'https://www.youtube.com/watch?v=gz0p0J0iUHQ&feature=youtu.be\'>this support video</a>');  return false;">
						<span class='nxs-icon-pagedecorator'></span>
					</a>
				</li>
				<?php
			}
		  
		  $shouldshowpagesettings = false;
	  	if (nxs_has_adminpermissions())
	  	{
	  		if (is_404())
			  {
			  	$shouldshowpagesettings = false;
			  }
			  else if (is_archive())
			  {
			  	$shouldshowpagesettings = false;
	  		}
	  		else
	  		{
	  			$shouldshowpagesettings = true;
	  		}
	  	}
		  ?>		  
		  
			<?php 
			if ($shouldshowpagesettings) 
			{ 
				?>
				<li class="nxs-hidewheneditorinactive nxs-sub-menu">
					<a class='site' title="<?php nxs_l18n_e("Page settings[nxs:title]", "nxs_td"); ?>" href='#' onclick="nxs_js_popup_pagetemplate_neweditsession('home'); return false;">
						<span class='nxs-icon-page-settings'></span>
					</a>
					<ul>
						<!-- page settings has extendable sub items -->
						<?php
						do_action('nxs_menu_pagesettings_addsubmenuitem');
						?>
					</ul>
				</li>
				<?php
				do_action('nxs_menu_afterpagesettings');
			} 
			?>
		  <?php
		  if (nxs_cap_hasdesigncapabilities())
		  {
		  	?>
		  	<li class="nxs-sub-menu nxs-hidewheneditorinactive">    
		  	<a href="#" title="<?php nxs_l18n_e("Dashboard[nxs:adminmenu,tooltip]", "nxs_td"); ?>" onclick="nxs_js_popup_site_neweditsession('dashboardhome'); return false;"  class="site">
		  		<span class='nxs-icon-dashboard'></span>
		  	</a>
		    <ul> 	
		    	<li>
		    		<a href="<?php echo $wordpressbackendurl; ?>" title="<?php nxs_l18n_e("WordPress backend[nxs:adminmenu,tooltip]", "nxs_td"); ?>" class="site small-wordpress nxs-defaultwidgetclickhandler">
		    			<span class='nxs-icon-wordpresssidebar'></span>
		    		</a>
		    	</li>
		    	<?php if (is_multisite() && is_super_admin()) { ?>
		    		<li>
		    			<a href="<?php echo network_admin_url(); ?>" title="<?php nxs_l18n_e("WordPress network backend[nxs:adminmenu,tooltip]", "nxs_td"); ?>" class="site">
		    				<span class='nxs-icon-network'></span>
		    			</a>
		    		</li>
		    	<?php } ?>
		    	<!--
		    	<li><a href="<?php echo home_url('/'); ?>?nxs_admin=admin&backendpagetype=systemlogs" title="<?php nxs_l18n_e("System logs[nxs:adminmenu,tooltip]", "nxs_td"); ?>" class="site small-systemlogs"></a></li>
		    	-->
				</ul>
				</li>
				<?php
			}
			?>
		  <li class="nxs-hidewheneditorinactive">
		  	<a href="#" title="<?php nxs_l18n_e("Log out[nxs:adminmenu,tooltip]", "nxs_td"); ?> <?php echo $current_user->user_login; ?>"	onclick="nxs_js_popup_site_neweditsession('logouthome'); return false;" class="site">
		  		<span class='nxs-icon-logout'></span>
		  	</a>
		  </li>
		  
			<!-- editor -->
		  
		  <?php 
		  if 
		  (
		  	$nxsposttype == "post" ||
		  	$nxsposttype == "sidebar" ||
		  	$nxsposttype == "pagelet" ||
		  	$nxsposttype == "subheader" ||
		  	$nxsposttype == "subfooter" ||
		  	$nxsposttype == "header" ||
		  	$nxsposttype == "footer"
		  )
		  {
		  ?>
		  <li>
		  	<a href="#" class='nxs-editor-toggler site'>
		  		<span class='nxs-icon-pause' style='display: none;'></span>
		  		<span class='nxs-icon-play blink' style='display: none;'></span>
		  	</a>
		  </li>
		  <?php 
			}
			else
			{
				
			}
		  ?>    
		  
		  
		</ul>
		
	</div>

	<?php 
	if (true)
	{
	  if 
	  (
	  	$nxsposttype == "post" ||
	  	$nxsposttype == "sidebar" ||
	  	$nxsposttype == "pagelet" ||
	  	$nxsposttype == "subheader" ||
	  	$nxsposttype == "subfooter" ||
	  	$nxsposttype == "header" ||
	  	$nxsposttype == "footer"
	  )
	  {
	  ?>
	  <style>
	  	.reactivate-editor-wrap
	  	{
	  		display: none;
	  		z-index: 120;
	  		position: fixed;
		    right: 13px;
		    top: -1px;
		    z-index: 1030;	  		
	  	}
	  	html.nxs-loadfinished.nxs-editor-inactive .reactivate-editor-wrap
	  	{
	  		display: inherit;
	  	}
	  	html.nxs-loadfinished.nxs-editor-inactive .reactivate-editor-wrap a span
	  	{
  	    font-size: 16px;
		    line-height: 45px;
		    -webkit-transition: all .2s;
		    -moz-transition: all .2s;
		    -o-transition: all .2s;
		    transition: all .2s;
		    color: black;
		    text-shadow: 1px 1px 1px white;
			}
			
			html.nxs-loadfinished.nxs-editor-inactive .reactivate-editor-wrap a
			{
			  z-index: 10;
		    background-color: #FCFCFC;
		    border-bottom-left-radius: 3px;
		    border-bottom-right-radius: 3px;
		    height: 45px;
		    width: 46px;
		    display: block;
		    text-decoration: none;
		    box-shadow: 0 2px 6px rgba(10, 10, 10, 0.6);
		    text-align: center;
			}
	  	
	  </style>
		<ul class="reactivate-editor-wrap">
		  <li>
		  	<a href="#" class='nxs-editor-toggler site'>
		  		<span class='nxs-icon-pause' style='display: none;'></span>
		  		<span class='nxs-icon-play blink' style='display: none;'></span>
		  	</a>
		  </li>
		</ul>
	  <?php 
		}
		else
		{
			
		}
	}
  ?>
  
	<div class="nxs-hidewheneditorinactive">
		<div class="nxs-hidewhenmenuinactive">
			<div id="menufillerinlinecontent">
				&nbsp;
			</div>
		</div>
	</div>
	
	<script>
		
		function nxs_js_getlimitedangle(min, max)
		{
			var angle = jQ_nxs('#dyncolangle').val();
			angle = parseInt(angle);
			if (angle < min)
			{
				angle = min;
			}
			if (angle > max)
			{
				angle = max;
			}
			jQ_nxs('#dyncolangle').val(angle);
			
			return angle;
		}
		
		function nxs_js_updatecolorwizard()
		{
			var dom = jQ_nxs('#colorderiver');
	
			nxs_js_log('at your colorservice');
			
			// remove any existing dynamically added colorpickers
			jQuery("#wizarddyncolorcontainer .color-picker").miniColors("destroy");
			
			// remove dom
			jQ_nxs('#wizarddyncolorcontainer').empty();	// clean up old stuff
	
			var hex = jQ_nxs("#vg_color_main1_m").val();
			var rgb = nxs_js_hextorgb(hex);
			var hsl = nxs_js_rgbtohsl(rgb);
			
			// derive
			var selectedtype = jQ_nxs(dom).val();
			var dyncolors = [];
			if (selectedtype == 'mono')
			{
				jQ_nxs('#anglecontroller').hide();
				dyncolors = nxs_js_getmonohsl(hsl);
			}
			else if (selectedtype == 'complementary')
			{
				jQ_nxs('#anglecontroller').hide();
				dyncolors = nxs_js_getcomplementaryhsl(hsl);
			}
			else if (selectedtype == 'splitcomplementary')		
			{
				jQ_nxs('#anglecontroller').hide();
				dyncolors = nxs_js_getsplitcomplementaryhsl(hsl);  			
			}
			else if (selectedtype == 'splittriad')		
			{
				jQ_nxs('#anglecontroller').show();
				var angle = nxs_js_getlimitedangle(0,90);
				dyncolors = nxs_js_gettriadbyanglehsl(hsl, angle);
			}
			else if (selectedtype == 'analogic')
			{
				jQ_nxs('#anglecontroller').show();
				var angle = nxs_js_getlimitedangle(0,90);
				dyncolors = nxs_js_getanalogicbyanglehsl(hsl, angle);		
			}
			else if (selectedtype == 'accentedanalogic')
			{
				jQ_nxs('#anglecontroller').show();
				var angle = nxs_js_getlimitedangle(0,90);
				dyncolors = nxs_js_getaccentedanalogicbyanglehsl(hsl, angle);		
			}
			else if (selectedtype == 'tetrad')
			{
				jQ_nxs('#anglecontroller').show();
				var angle = nxs_js_getlimitedangle(0,90);
				dyncolors = nxs_js_gettetradbyanglehsl(hsl, angle);		
			}
			
			// re-render		
			for (var i = 0; i < dyncolors.length; i++) 
			{
		    var currenthsl = dyncolors[i];
		    var currentrgb = nxs_js_hsltorgb(currenthsl);
		    var currenthex = nxs_js_rgbtohex(currentrgb);
		    // inject new colorpicker
				var domtoappend = "<div class='content2'><div class='box'><div class='box-title2'><p>todo</p></div><div class='box-content2'><input type='text' class='color-picker' size='6' value='" + currenthex + "' /></div><div class='nxs-clear'></div></div></div> <!--END content-->";
				jQ_nxs("#wizarddyncolorcontainer").append(domtoappend);
		  }
		  
		  // activate colorpickers
	    jQuery("#wizarddyncolorcontainer .color-picker").miniColors
			(
				{
					readonly: true,
					opacity: true,
					letterCase: 'uppercase',
	      }
	    );
			
		}
		
		jQ_nxs(document).ready(
			function() 
			{
				var nxs_tabs_initialized = false;
				jQ_nxs(".tabs").tabs
				(
					{
						event: "click",
						show : function( event, ui )
						{
	            //  Get future value
	            if (nxs_tabs_initialized)
	            {
		            var newIndex = ui.index;
		            //nxs_js_log(ui.panel);
		            //var tabscontainer = jQ_nxs(ui).closest(".tabs");
		            var tabsid = jQ_nxs(ui.panel).closest(".tabs").attr("id");
		            nxs_js_setcookie('nxs_cookie_acttab_' + tabsid, newIndex);
		          }
		        }
					}
				);

				nxs_tabs_initialized = true;
				
				var oldindex = 0;
		    try 
		    {
					oldindex = nxs_js_getcookie('nxs_cookie_acttab_nxs-admin-tabs');
		    } 
		    catch(e) 
		    {
		    	nxs_js_log(e);
		    }
		    
		    if (oldindex == 0)
		    {
		    	oldindex = 1;	// design tab
		    }
		    oldindex = parseInt(oldindex);
				
				// klap menu open/dicht als er op het icoon wordt gedrukt
				jQ_nxs(".nxs-menu-toggler").click
				(
					function() 
					{
						nxs_js_toggle_menu_state();
					}
				);
				
				// enable/disable de editor als er op het icoon wordt gedrukt
				jQ_nxs(".nxs-editor-toggler").click
				(
					function() 
					{ 
						nxs_js_toggle_editor_state();
						nxs_js_alert("<?php nxs_l18n_e('Tip; press ESC to temporarily (de)activate the editor[nxs:growl]',"nxs_td"); ?>");
					}
				);
				
				jQ_nxs(".tabs").on('tabsactivate', function(event, ui)
				{
					var index = ui.newTab.index();
					nxs_js_refreshtopmenufillerheight();
					
					if (index == 3)
					{
						// SEO tab activated
						nxs_js_refresh_seoanalysis();
					}
				});

				//				
				jQ_nxs("#nxs-admin-tabs" ).tabs( "option", "active", oldindex);

	
				// stop het progageren van het event (bind("click") om te voorkomen dat onderliggende
				// elementen het click event gaan afhandelen (zoals het event dat de body click altijd opvangt...)
				jQ_nxs("#nxs-menu-wrap").bind("click.stoppropagation", function(e) 
				{
					e.stopPropagation();
				});			
				jQ_nxs("#nxs-menu-wrap").bind("dblclick.stoppropagation", function(e) 
				{
					e.stopPropagation();
				});
				jQ_nxs("#nxs-content").bind("dblclick.stoppropagation", function(e) 
				{
					e.stopPropagation();
				});
				jQ_nxs("#nxs-header").bind("dblclick.stoppropagation", function(e) 
				{
					e.stopPropagation();
				});
				
				nxs_init_vormgeving();
				
				jQ_nxs(".nxs-menu-toggler").toggleClass("close");
				
				// bij het hoveren boven het menu item nxs-editor-toggler lichten we alle cursors op
				jQ_nxs(".nxs-editor-toggler").bind("mouseover.glowcursors", function(e)
				{
					// ensure tags are not showing 2x's; remove first!
					jQ_nxs(".scaffolding-editor-toggler").each
					(
						function(i)
						{
							jQ_nxs(this).remove();
						}
					);
					
					// add tags
					jQ_nxs(".nxs-elements-container").each
					(
						function(i)
						{
							var msg;
							if (jQ_nxs(this).hasClass("nxs-header-container"))
							{
								msg = "Header content area";
							}
							else if (jQ_nxs(this).hasClass("nxs-subheader-container"))
							{
								msg = "Subheader content area";
							}
							else if (jQ_nxs(this).hasClass("nxs-sidebar-container"))
							{
								msg = "Sidebar content area";
							}
							else if (jQ_nxs(this).hasClass("nxs-subfooter-container"))
							{
								msg = "Subfooter content area";
							}
							else if (jQ_nxs(this).hasClass("nxs-footer-container"))
							{
								msg = "Footer content area";
							}
							else if (jQ_nxs(this).hasClass("nxs-article-container"))
							{
								msg = "Main content area";
							}
							else
							{
								msg = "Container content area";
							}
							jQ_nxs(this).find(".nxs-placeholder > .nxs-cell-cursor").each
							(
								function(j)
								{
									jQ_nxs(this).append("<span class='scaffolding-editor-toggler' style='background-color: black; color: white; padding: 2px;'>" + msg + "</span>");
								}
							);
						}
					);
										
					// nxs_js_log('mouse over detected');
					jQ_nxs(".nxs-cursor").addClass("nxs-hovering");
					jQ_nxs(".nxs-cursor").addClass("nxs-overrule-suppress");
				}
				);
				jQ_nxs(".nxs-editor-toggler").bind("mouseleave.dimcursors", function(e)
				{
					jQ_nxs(".scaffolding-editor-toggler").each
					(
						function(i)
						{
							jQ_nxs(this).remove();
						}
					);
					
					// nxs_js_log('mouse leave detected');
					jQ_nxs(".nxs-cursor").removeClass("nxs-hovering");
					jQ_nxs(".nxs-cursor").removeClass("nxs-overrule-suppress");
				}
				);
			}
		);
		
		function nxs_js_menu_updateoverridenmanualcss()
		{
			jQ_nxs('#nxs_menu_savemanualcssbutton').show();				
			// recalculate height of menu
			nxs_js_refreshtopmenufillerheight();
			
			// update screen
			nxs_js_menu_handlecolorthemechanged();
		}
	  
	  function nxs_init_vormgeving()
		{
			// lazy load minicolors
			var scripturl = '/js/minicolors/jquery.miniColors.js';
			var functiontoinvoke = 'nxs_init_vormgeving_actual()';
			nxs_js_lazyexecute(scripturl, true, functiontoinvoke);
		}
	  
		function nxs_init_vormgeving_actual()
		{
			jQuery(".color-picker").miniColors
			(
				{
					opacity: true,
					letterCase: 'uppercase',
					change: function(hex, rgb) 
					{
						var kleuridentificatie = jQ_nxs(this).attr("id");
						jQ_nxs(this).data("kleur", hex);
	
						nxs_js_processcolorupdate(hex, rgb, kleuridentificatie);
						
						// update screen
						nxs_js_menu_handlecolorthemechanged();
					}
	      }
	    );
	    
	    //
	    // reposition the minicolors after user clicks on a color picker
	    //
	    jQ_nxs(".miniColors-trigger").bind
	    (
		    "click", function(e)
		    {
		    	var x = jQ_nxs(this).position().left + 30;
		    	var y = jQ_nxs(this).position().top - jQ_nxs(this).scrollTop() + 45;
		    	
		    	jQ_nxs(".color-picker").each(function(i)
		    	{
		    		jQ_nxs(this).css("left", "" + x + "px");
		    		jQ_nxs(this).css("top", "" + y + "px");
		    	});
		    }
	    );
		}
		
		function nxs_js_processcolorupdate(hex, rgb, kleuridentificatie)
		{
			if (false) {}
			<?php
			$colortypes = nxs_getcolorsinpalette();
			foreach($colortypes as $currentcolortype)
			{				
				$subtypes = array("1", "2");
				foreach($subtypes as $currentsubtype)
				{
					$identification = $currentcolortype . $currentsubtype;
					?>
					else if (kleuridentificatie == 'vg_color_<?php echo $identification; ?>_m')
					{
						// get HSL of RGB
						var rgb = nxs_js_hextorgb(hex);
						var hsl = nxs_js_hextohsl(hex);
						<?php
						/*
						var oppositehsl = nxs_js_getoppositesaturationandlightforhsl(hsl);
						var oppositergb = nxs_js_hsltorgb(oppositehsl);
						var oppositehex = nxs_js_rgbtohex(oppositergb);
						jQ_nxs('#vg_color_<?php echo $identification; ?>_o_m').miniColors('value', oppositehex);
						*/
						/*
						var complementaryhsl = nxs_js_getcomplementaryhsl(hsl);
						var complementaryrgb = nxs_js_hsltorgb(complementaryhsl);
						var complementaryhex = nxs_js_rgbtohex(complementaryrgb);
						jQ_nxs('#vg_color_<?php echo $identification; ?>_o_m').miniColors('value', complementaryhex);
						*/
						?>
						nxs_js_menu_handlecolorthemechanged();
						
						// update GUI
						jQ_nxs('.nxs_menu_savekleurenbutton').show();
						// recalculate height of menu
						nxs_js_refreshtopmenufillerheight();
					}
					<?php
				}
			}
			?>
			else
			{
				nxs_js_log("not yet supported;" + kleuridentificatie);
			}
		}
		
		/*
		*/
		
		// kleuren
		
		function nxs_js_menu_handlecolorthemechanged()
		{
			if (nxs_js_isruntimecssrefreshqueued == true)
			{
				// performance boost; we gaan hier niet nogmaals
				// alle berekeningen doorvoeren; er is reeds een 
				// request ingepland
				// nxs_js_log('skipping refresh, already queued');
				return;
			}
			else
			{
				// nxs_js_log('nothing in queue yet, enqueueing request');
				// enqueue!
				nxs_js_isruntimecssrefreshqueued = true;
	
				var nxs_max_frequency_in_msecs = 200;	// lager betekent meer overhead
				
				// optionally perform an immediate redraw to get a snappier user experience
				// nxs_gui_set_runtime_dimensions_actualrequest(); 
				setTimeout
				(
					function() 
					{
						//nxs_js_log('executing actual refresh work');
						
						// first we dequeue! 
						nxs_js_isruntimecssrefreshqueued = false; 
						nxs_js_menu_handlecolorthemechanged_actualrequest(); 
					},nxs_max_frequency_in_msecs
				);
			}
		}
		
		function nxs_js_menu_handlecolorthemechanged_actualrequest()
		{
			// if the colortheme is adjusted, we need to update both
			// the css of the serverside theme, as well as the manual overriden css
			nxs_js_updatecss_themecss_actualrequest(false, true);	// dont use the cache, DO update the dom
			nxs_js_updatecss_manualcss_actualrequest();
		}
		
		function nxs_menu_savekleuren()
		{
			var valuestobeupdated = {};
			
			//
			<?php
			$colortypes = nxs_getcolorsinpalette();
			foreach($colortypes as $currentcolortype)
			{
				$subtypes = array("1", "2");
				foreach($subtypes as $currentsubtype)
				{
					$identification = $currentcolortype . $currentsubtype;
					?>
					valuestobeupdated["vg_color_<?php echo $identification;?>_m"] = jQ_nxs("#vg_color_<?php echo $identification;?>_m").val();
					<?php
				}
			}
			?>
			
			//nxs_js_log("values to be updated:");
			//nxs_js_log(valuestobeupdated);
			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updatesitedata",
						"updatesectionid": "menuvormgevingkleuren",
						"data": nxs_js_getescapeddictionary(valuestobeupdated)
					},
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							jQ_nxs('.nxs_menu_savekleurenbutton').fadeOut('slow', function() { nxs_js_refreshtopmenufillerheight(); } );
							
							nxs_js_alert("<?php nxs_l18n_e("Colors saved[nxs:growl]","nxs_td"); ?>");
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
		
		// lettertypen
		
		function nxs_js_font_getcleanfontfam(fontfamily)
		{
			// some fonts have extensions like Playball::latin
			// this function removes those "noise"

			fontfamily = fontfamily.replace(/\+/g, " ");
			fontfamily = fontfamily.replace(/\'/g, "");

			var splitted = fontfamily.split(':');
			var result = splitted[0];
			
			//nxs_js_log("0--0-0-0-0-0");
			//nxs_js_log(result);
			
			return result;
		}
		
		function nxs_js_font_updatefonts()
		{
			// (sync modifications here; *236i84325) (nxsfunctions.php)
			
			jQ_nxs('#dynamicCssVormgevingLettertypen').html('');
			// append
			var u;
			// old style :)
			u = "";
			u = u + "body { font-family: " + nxs_js_font_getcleanfontfam(jQ_nxs("#vg_fontfam_1").val()) + "; }";
			u = u + ".nxs-title, .nxs-logo { font-family: " + nxs_js_font_getcleanfontfam(jQ_nxs("#vg_fontfam_2").val()) + "; }";	
			// old style++
			u = u + ".entry-content h1, .entry-content h2, .entry-content h3, .entry-content h4, .entry-content h5, .entry-content h6 { font-family: " + nxs_js_font_getcleanfontfam(jQ_nxs("#vg_fontfam_2").val()) + "; }";	
			
			// new style :)
			<?php
			$fontidentifiers = nxs_font_getfontidentifiers();
			foreach ($fontidentifiers as $currentfontidentifier)
			{
				?>
				u = u + ".nxs-fontzen-<?php echo $currentfontidentifier; ?> { font-family: " + nxs_js_font_getcleanfontfam(jQ_nxs("#vg_fontfam_<?php echo $currentfontidentifier; ?>").val()) + "; }";	
				<?php
			}
			?>
			
			// great, thanks to MS we need a lame IE patch, see http://stackoverflow.com/questions/9050441/how-do-i-inject-styles-into-ie8
			if(jQuery.browser.msie)
		  {
		    jQ_nxs('#dynamicCssVormgevingLettertypen').prop('styleSheet').cssText=u;
		  }
			else
			{
				jQ_nxs('#dynamicCssVormgevingLettertypen').append(u);
			}
					
			jQ_nxs('#nxs_menu_savelettertypenbutton').show();
			// recalculate height of menu
			nxs_js_refreshtopmenufillerheight();
		}
		
		function nxs_js_font_savefonts()
		{
			var valuestobeupdated = {};
			<?php
			$fontidentifiers = nxs_font_getfontidentifiers();
			foreach ($fontidentifiers as $currentfontidentifier)
			{
				?>
				valuestobeupdated["vg_fontfam_<?php echo $currentfontidentifier; ?>"] = jQ_nxs("#vg_fontfam_<?php echo $currentfontidentifier; ?>").val();
				<?php
			}
			?>
			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updatesitedata",
						"updatesectionid": "menuvormgevinglettertypen",
						"data": nxs_js_getescapeddictionary(valuestobeupdated)
					},
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							jQ_nxs('#nxs_menu_savelettertypenbutton').fadeOut('slow', function() { nxs_js_refreshtopmenufillerheight(); } );
							
							nxs_js_alert("<?php nxs_l18n_e("Fonts saved[nxs:growl]","nxs_td"); ?>");
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
		
		//
		
	
		
		function nxs_menu_updateinjecthead()
		{
			jQ_nxs('#nxs_menu_saveinjectheadbutton').show();				
			// recalculate height of menu
			nxs_js_refreshtopmenufillerheight();
		}
		
		function nxs_menu_savemanualcss()
		{
			var valuestobeupdated = {};
			valuestobeupdated["vg_manualcss"] = jQ_nxs("#vg_manualcss").val();
			
			
			var wrong = nxs_js_getescapeddictionary(valuestobeupdated);
			nxs_js_log("good:");
			nxs_js_log(valuestobeupdated);
			nxs_js_log("bad:");
			nxs_js_log(wrong);
	
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updatesitedata",
						"updatesectionid": "menuvormgevingmanualcss",
						"data": nxs_js_getescapeddictionary(valuestobeupdated)
					},
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							jQ_nxs('#nxs_menu_savemanualcssbutton').fadeOut('slow', function() { nxs_js_refreshtopmenufillerheight(); } );
							
							nxs_js_alert("<?php nxs_l18n_e("CSS saved[nxs:growl]","nxs_td"); ?>");
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
		
		function nxs_menu_saveinjecthead()
		{
			var valuestobeupdated = {};
			valuestobeupdated["vg_injecthead"] = jQ_nxs("#vg_injecthead").val();
	
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updatesitedata",
						"updatesectionid": "menuvormgevinginjecthead",
						"data": nxs_js_getescapeddictionary(valuestobeupdated)
					},
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							jQ_nxs('#nxs_menu_saveinjectheadbutton').fadeOut('slow', function() { nxs_js_refreshtopmenufillerheight(); } );
							
							nxs_js_alert("<?php nxs_l18n_e("Head saved, please refresh the screen[nxs:growl]","nxs_td"); ?>");
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
	</script>
</div>

<?php do_action('nxs_action_after_menu'); ?>
