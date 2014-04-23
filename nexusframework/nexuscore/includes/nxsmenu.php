<?php
	if (!is_user_logged_in())
	{
		// gebruikers die niet zijn ingelogd hebben hier niets te zoeken...
		return;
	}
	
 	global $current_user;
 	get_currentuserinfo();

	if (!nxs_has_adminpermissions())
	{
		// als er geen recht is om posts te editen, dan heeft het nxsmenu geen nut
		return;
	}
	
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
	if (is_singular())
	{
		global $post;
		$postid = $post->ID;
		$posttype = $post->post_type;
		$postmeta = nxs_get_postmeta($postid);
		$pagetemplate = nxs_getpagetemplateforpostid($postid);
		$nxsposttype = nxs_getnxsposttype_by_wpposttype($posttype);
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
		            <?php if (nxs_cap_hasdesigncapabilities()) { ?>
		            <li><a href="#tabs-css"><?php nxs_l18n_e("CSS[nxs:adminmenu,tab]", "nxs_td"); ?></a></li>
		          	<?php } ?>
		            <?php if ($nxsposttype == "post" && is_singular() && nxs_cap_hasdesigncapabilities()) { ?>
		            <li><a href="#tabs-seo"><?php nxs_l18n_e("SEO", "nxs_td"); ?></a></li>
		            <?php } ?>
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
		     		              	$palettenames = nxs_colorization_getpalettenames();
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
																		<div class="block nxs-width200 nxs-float-left nxs-margin-right10">
																			<div class="content2">
																				<div class="box">';
																					// Function to render color palette
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
			                          <div class="block nxs-width200">
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
														<a class="nxsbutton1 nxs-float-left" href='#' onclick="nxs_js_popup_site_neweditsession('managecolorization'); return false;">Manage</a>
			            					<a style='display: none;' href='#' class="nxs_menu_savekleurenbutton nxsbutton nxs-float-left" onclick='nxs_js_menu_savepalette(); return false;'><?php nxs_l18n_e("Save[nxs:btn]", "nxs_td"); ?></a>
			            					<a style='display: none;' href='#' class="nxs_menu_savekleurenbutton nxsbutton nxs-float-left" onclick='nxs_js_menu_createnewpalette(); return false;'><?php nxs_l18n_e("Create new", "nxs_td"); ?></a>
				                      
														<div class="nxs-clear padding"></div>
			                  	</div>
		     		              
													<script type='text/javascript'>
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
		                      
		                      <div class="nxs-float-left nxs-margin-right10">
		                          <div class="block">
		                              <div class="nxs-admin-header"><h3><?php nxs_l18n_e("Fonts[nxs:adminmenu,subtab,heading]", "nxs_td"); ?></h3></div>
		                              
																	<div class="content2">
		                                  <div class="box">
		                                  		
		                                      <div class="box-title2"><p><?php nxs_l18n_e("Texts[nxs:adminmenu,subtab,heading]", "nxs_td"); ?></p></div>
		                                      <div class="box-content3" style='width: auto;'>
																						<select id="vg_fontfam_1" onchange='nxs_menu_updatelettertypen();'>
																							<?php 
																								$vg_fontfam_1 = $sitemeta['vg_fontfam_1']; 
																								$fontlist = nxs_getfonts();
																								foreach ($fontlist as $fontid => $fontdata)
																								{
																									if ($vg_fontfam_1 == $fontid)
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
		                              
																	<div class="content2">
		                                  <div class="box">
		                                      <div class="box-title2"><p><?php nxs_l18n_e("Headings[nxs:adminmenu,subtab,heading]", "nxs_td"); ?></p></div>
		                                      <div class="box-content3" style='width: auto;'>
																						<select id="vg_fontfam_2" onchange='nxs_menu_updatelettertypen();'>
																							<?php
																							$vg_fontfam_2 = $sitemeta['vg_fontfam_2']; 
																							$fontlist = nxs_getfonts();
																							foreach ($fontlist as $fontid => $fontdata)
																							{
																								if ($vg_fontfam_2 == $fontid)
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
		                              
		                          </div> 
		                      </div> 
		                  
		                  		<div class="nxs-clear padding"></div>
		                  		
		     		              <a id='nxs_menu_savelettertypenbutton' style='display: none;' href='#' class="nxsbutton nxs-float-left" onclick='nxs_menu_savelettertypen(); return false;'><?php nxs_l18n_e("Save[nxs:btn]", "nxs_td"); ?></a>
		     		              
		     		              <div class="nxs-clear"></div>
		                  		
		                  </div>
		                  
		              </div> <!--END content-->
		                  
		          </div> <!--END tabs-->
		        </div> <!--END tabs-->
		        
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
		        if ($nxsposttype == "post" && is_singular() && nxs_cap_hasdesigncapabilities() && !defined('WPSEO_PATH')) 
		        {
		        	do_action('nxs_ext_seotab_pluginnotfound');
		        }
		        else if ($nxsposttype == "post" && is_singular() && nxs_cap_hasdesigncapabilities() && defined('WPSEO_PATH')) { ?>
		        <div id="tabs-seo">
		        	<script type='text/javascript'>
		        		function nxs_js_adjustangledelta(delta)
		        		{
		        			var angle = jQuery('#dyncolangle').val();
									angle = parseInt(angle);
		        			angle = angle + delta;
		        			jQuery('#dyncolangle').val(angle); 
		        			// update
		        			nxs_js_updatecolorwizard();
		        		}
		        		
		        		function nxs_js_showseoupdatebutton()
		        		{
		        			jQuery('#nxs-seofield-updatebutton').show();
		        		}
		        	</script>
		          <div class="content nxs-padding10">
		
								<div class="block">            	
		            	<div class="nxs-admin-header" onclick="jQuery('#nxssnipprev').toggleClass('nxs-toggle-hide'); nxs_js_refreshtopmenufillerheight()"><h3><?php nxs_l18n_e("Snippet preview[nxs:adminmenu,subtab,heading]", "nxs_td"); ?></h3></div>
		            	<div id='nxssnipprev' class="content2">
		                <div class="box">
		                	<div id='nxs-seo-snippetwrapper'></div>
						          <div class='nxs-clear'></div>
						        </div>
						      </div> <!-- end content2 -->
		            </div> <!-- end block -->
		          	
								<div id='nxs-seofields' style='display: none;'>
			            <div class="block">
			              <div class="nxs-admin-header" onclick="jQuery('#nxsseoinput').toggleClass('nxs-toggle-hide'); nxs_js_refreshtopmenufillerheight()"><h3><?php nxs_l18n_e("Input[nxs:adminmenu,subtab,heading]", "nxs_td"); ?></h3></div>
			              <div id="nxsseoinput">
				            	<div class="content2">
				                <div class="box">
				                  <div class="box-title"><p><?php nxs_l18n_e("Focus keyword[nxs:heading]", "nxs_td"); ?></p></div>
				                  <div class="box-content">
						              	<input id='nxs-seofocuskeyword' type='text' onkeydown="nxs_js_showseoupdatebutton();" />
							            </div>
							            <div class='nxs-clear'></div>
							          </div>
							        </div> <!-- end content2 -->
				            	<div class="content2">
				                <div class="box">
				                  <div class="box-title"><p><?php nxs_l18n_e("SEO title[nxs:heading]", "nxs_td"); ?></p> <span id='seotitlecharsused'></span></p></div>
				                  <div class="box-content">
						              	<input id='nxs-seotitle' type='text' onkeydown="nxs_js_shownumofchars('#nxs-seotitle', '#seotitlecharsused'); nxs_js_showseoupdatebutton();" />
							            </div>
							            <div class='nxs-clear'></div>
							        	</div>
							        </div> <!-- end content2 -->
				            	<div class="content2">
				                <div class="box">
				                  <div class="box-title"><p><?php nxs_l18n_e("SEO meta description[nxs:heading]", "nxs_td"); ?></p> <span id='seodescriptioncharsused'></span></p></div>
				                  <div class="box-content">
						              	<input id='nxs-seometadescription' type='text' onkeydown="nxs_js_shownumofchars('#nxs-seometadescription', '#seodescriptioncharsused'); nxs_js_showseoupdatebutton();" />
							            </div>
							            <div class='nxs-clear'></div>
							        	</div>
							        </div> <!-- end content2 -->
			
											<div class="content2">
				                <div class="box">
				                  <div class="box-title">
		     										<a id='nxs-seofield-updatebutton' style='display: none;' href='#' class="nxsbutton nxs-float-left" onclick='nxs_js_update_seoall(); return false;'><?php nxs_l18n_e("Update[nxs:button]", "nxs_td"); ?></p></a>
		     									</div>
		     									<div class="box-content">
		     									</div>
													<div class="nxs-clear padding"></div>
							        	</div>
							        </div>
						  </div>
			      </div> <!-- end block -->
			
									      
									<div class="block">            	
			            	<div class="nxs-admin-header" onclick="jQuery('#nxsseoanalysisoutput').toggleClass('nxs-toggle-hide'); nxs_js_refreshtopmenufillerheight();"><h3><?php nxs_l18n_e("Search engine analysis[nxs:adminmenu,subtab,heading]", "nxs_td"); ?></h3></div>
			            	<div id="nxsseoanalysisoutput" class="content2">
			                <div class="box">
			                	<div id='nxs-seo-output'></div>
							          <div class='nxs-clear'></div>
							        </div>
							       </div> <!-- end content2 -->
			            </div> <!-- end block -->
								<div class="nxs-clear padding"></div>
								</div>
		            <div class="nxs-clear"></div>
		          </div> <!--END content-->
		        </div> <!--END tabs-->        
		        <?php } ?>
		
		        <?php do_action('nxs_ext_injecttabcontent'); ?>
		    </div> <!--END tabs-->
		    
		    <?php
		  	}
		    ?>
		   </div>
		</div>
	</div> <!--END nxs-menu-wrap-->
	
	<div class="nxs-admin-wrap">
		<ul class="admin nxs-no-click-propagation ">			
			<?php
			$licensekey = esc_attr(get_option('nxs_licensekey'));
			if ($licensekey == "")
			{
				$url = admin_url('admin.php?page=nxs_admin_license');
				?>
				<li>
					<a href="<?php echo $url;?>" class='site nxs-hidewheneditorinactive' title="<?php nxs_l18n_e("Register your purchase to receive free updates and support", "nxs_td"); ?>">
						<span class='nxs-icon-key blink'></span>
					</a>
				</li>
				<?php
			}
			else
			{
				$url = admin_url('admin.php?page=nxs_admin_update');
				$themeupdate = get_transient("nxs_themeupdate");
				if ($themeupdate["nxs_updates"] == "yes")
				{
					?>
					<li>
						<a href="<?php echo $url; ?>" class='site nxs-hidewheneditorinactive' title="<?php nxs_l18n_e("Theme update available", "nxs_td"); ?>">
							<span class='nxs-icon-loop2'></span>
						</a>
					</li>
					<?php
				}
			}
			if (nxs_issiteinmaintenancemode())
			{
				?>
				<li>
					<a href="#" onclick="nxs_js_popup_site_neweditsession('maintenancehome'); return false;" class='site nxs-hidewheneditorinactive' title="<?php nxs_l18n_e("Maintenance mode activated", "nxs_td"); ?>">
						<span class='nxs-icon-construction'></span>
					</a>
				</li>
				<?php
			}
			?>
			<li>
				<a href="<?php bloginfo('url'); ?>" class='site nxs-hidewheneditorinactive' title="<?php nxs_l18n_e("Home[nxs:adminmenu,tooltip]", "nxs_td"); ?>">
					<span class='nxs-icon-home'></span>
				</a>
			</li>
		  <li class="nxs-hidewheneditorinactive">
		  	<a href="#" title="<?php nxs_l18n_e("New[nxs:adminmenu,tooltip]", "nxs_td"); ?>" onclick="nxs_js_popup_site_neweditsession('newposthome'); return false;" class="site">
		  		<span class='nxs-icon-article-new'></span>
		  	</a>
		  </li>
		  <li class="nxs-sub-menu nxs-hidewheneditorinactive">
		  	<a href="<?php echo home_url('/'); ?>?nxs_admin=admin&backendpagetype=artikelen" title="<?php nxs_l18n_e("Pages[nxs:adminmenu,tooltip]", "nxs_td"); ?>" class="site">
		  		<span class='nxs-icon-article-overview'></span>
		  	</a>
		    <ul> 	
		      <li>
		      	<a href="<?php echo home_url('/'); ?>?nxs_admin=admin&backendpagetype=mediamanager" title="<?php nxs_l18n_e("Media manager[nxs:adminmenu,tooltip]", "nxs_td"); ?>" class="site">
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
			      if (nxs_hastemplateproperties())
			      {
			      	$rulesid = nxs_gettemplatepropertiesid();
			      	$url = nxs_geturl_for_postid($rulesid);
				      ?>
				      <li>
		     		  	<a href="<?php echo $url; ?>" title="<?php nxs_l18n_e("Business rules", "nxs_td"); ?>" class="site">
						  		<span class='nxs-icon-wand'></span>
						  	</a>
				      </li>
			      	<?php
			      }
			      ?>
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
		  
		  if ($nxsposttype == "post" && is_singular())
		  {
				$wordpressbackendurl = get_edit_post_link($postid, array());	
		  } 
		  else
		  {
		  	$wordpressbackendurl = get_admin_url() . "admin.php?page=nxs_backend_overview&type=" . $nxsposttype . "&posttype=" . $posttype; 
		  }
		  
		  do_action('nxs_ext_injectmenuitem');
		  ?>
		  <li class="nxs-hidewheneditorinactive"><span class="nxs-menu-spacer">&nbsp;</span></li>
		  
		  <?php
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
		    		<a href="<?php echo $wordpressbackendurl; ?>" title="<?php nxs_l18n_e("WordPress backend[nxs:adminmenu,tooltip]", "nxs_td"); ?>" class="site small-wordpress">
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
				<a target="_blank" href="http://nexusthemes.com/getting-started-with-nexus-themes/" title="<?php nxs_l18n_e("Help[nxs:adminmenu,tooltip]", "nxs_td"); ?>" class="site">
					<span class='nxs-icon-support'></span>
				</a>
			</li>
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
	
	<div class="nxs-hidewheneditorinactive">
		<div class="nxs-hidewhenmenuinactive">
			<div id="menufillerinlinecontent">
				&nbsp;
			</div>
		</div>
	</div>
	
	<script type='text/javascript'>
		
		function nxs_js_getlimitedangle(min, max)
		{
			var angle = jQuery('#dyncolangle').val();
			angle = parseInt(angle);
			if (angle < min)
			{
				angle = min;
			}
			if (angle > max)
			{
				angle = max;
			}
			jQuery('#dyncolangle').val(angle);
			
			return angle;
		}
		
		function nxs_js_updatecolorwizard()
		{
			var dom = jQuery('#colorderiver');
	
			nxs_js_log('at your colorservice');
			
			// remove any existing dynamically added colorpickers
			jQuery("#wizarddyncolorcontainer .color-picker").miniColors("destroy");
			
			// remove dom
			jQuery('#wizarddyncolorcontainer').empty();	// clean up old stuff
	
			var hex = jQuery("#vg_color_main1_m").val();
			var rgb = nxs_js_hextorgb(hex);
			var hsl = nxs_js_rgbtohsl(rgb);
			
			// derive
			var selectedtype = jQuery(dom).val();
			var dyncolors = [];
			if (selectedtype == 'mono')
			{
				jQuery('#anglecontroller').hide();
				dyncolors = nxs_js_getmonohsl(hsl);
			}
			else if (selectedtype == 'complementary')
			{
				jQuery('#anglecontroller').hide();
				dyncolors = nxs_js_getcomplementaryhsl(hsl);
			}
			else if (selectedtype == 'splitcomplementary')		
			{
				jQuery('#anglecontroller').hide();
				dyncolors = nxs_js_getsplitcomplementaryhsl(hsl);  			
			}
			else if (selectedtype == 'splittriad')		
			{
				jQuery('#anglecontroller').show();
				var angle = nxs_js_getlimitedangle(0,90);
				dyncolors = nxs_js_gettriadbyanglehsl(hsl, angle);
			}
			else if (selectedtype == 'analogic')
			{
				jQuery('#anglecontroller').show();
				var angle = nxs_js_getlimitedangle(0,90);
				dyncolors = nxs_js_getanalogicbyanglehsl(hsl, angle);		
			}
			else if (selectedtype == 'accentedanalogic')
			{
				jQuery('#anglecontroller').show();
				var angle = nxs_js_getlimitedangle(0,90);
				dyncolors = nxs_js_getaccentedanalogicbyanglehsl(hsl, angle);		
			}
			else if (selectedtype == 'tetrad')
			{
				jQuery('#anglecontroller').show();
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
				jQuery("#wizarddyncolorcontainer").append(domtoappend);
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
		
		jQuery(document).ready(
			function() 
			{
				var nxs_tabs_initialized = false;
				jQuery(".tabs").tabs
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
		            //var tabscontainer = jQuery(ui).closest(".tabs");
		            var tabsid = jQuery(ui.panel).closest(".tabs").attr("id");
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
				jQuery(".nxs-menu-toggler").click
				(
					function() 
					{
						nxs_js_toggle_menu_state();
					}
				);
				
				// enable/disable de editor als er op het icoon wordt gedrukt
				jQuery(".nxs-editor-toggler").click
				(
					function() 
					{ 
						nxs_js_toggle_editor_state();
						nxs_js_alert("<?php nxs_l18n_e('Tip; press ESC to temporarily (de)activate the editor[nxs:growl]',"nxs_td"); ?>");
					}
				);
				
				jQuery('.tabs').bind('tabsshow', function(event, ui) 
				{
					nxs_js_refreshtopmenufillerheight();
					
					if (ui.index == 3)
					{
						// SEO tab activated
						nxs_js_refresh_seoanalysis();
					}
				});

				//				
				jQuery("#nxs-admin-tabs" ).tabs('select',oldindex);

	
				// stop het progageren van het event (bind("click") om te voorkomen dat onderliggende
				// elementen het click event gaan afhandelen (zoals het event dat de body click altijd opvangt...)
				jQuery("#nxs-menu-wrap").bind("click.stoppropagation", function(e) 
				{
					e.stopPropagation();
				});			
				jQuery("#nxs-menu-wrap").bind("dblclick.stoppropagation", function(e) 
				{
					e.stopPropagation();
				});
				jQuery("#nxs-content").bind("dblclick.stoppropagation", function(e) 
				{
					e.stopPropagation();
				});
				jQuery("#nxs-header").bind("dblclick.stoppropagation", function(e) 
				{
					e.stopPropagation();
				});
				
				nxs_init_vormgeving();
				
				jQuery(".nxs-menu-toggler").toggleClass("close");
				
				// bij het hoveren boven het menu item nxs-editor-toggler lichten we alle cursors op
				jQuery(".nxs-editor-toggler").bind("mouseover.glowcursors", function(e)
				{
					// ensure tags are not showing 2x's; remove first!
					jQuery(".scaffolding-editor-toggler").each
					(
						function(i)
						{
							jQuery(this).remove();
						}
					);
					
					// add tags
					jQuery(".nxs-elements-container").each
					(
						function(i)
						{
							var msg;
							if (jQuery(this).hasClass("nxs-header-container"))
							{
								msg = "Header content area";
							}
							else if (jQuery(this).hasClass("nxs-subheader-container"))
							{
								msg = "Subheader content area";
							}
							else if (jQuery(this).hasClass("nxs-sidebar-container"))
							{
								msg = "Sidebar content area";
							}
							else if (jQuery(this).hasClass("nxs-subfooter-container"))
							{
								msg = "Subfooter content area";
							}
							else if (jQuery(this).hasClass("nxs-footer-container"))
							{
								msg = "Footer content area";
							}
							else if (jQuery(this).hasClass("nxs-article-container"))
							{
								msg = "Main content area";
							}
							else
							{
								msg = "Container content area";
							}
							jQuery(this).find(".nxs-placeholder > .nxs-cell-cursor").each
							(
								function(j)
								{
									jQuery(this).append("<span class='scaffolding-editor-toggler' style='background-color: black; color: white; padding: 2px;'>" + msg + "</span>");
								}
							);
						}
					);
										
					// nxs_js_log('mouse over detected');
					jQuery(".nxs-cursor").addClass("nxs-hovering");
					jQuery(".nxs-cursor").addClass("nxs-overrule-suppress");
				}
				);
				jQuery(".nxs-editor-toggler").bind("mouseleave.dimcursors", function(e)
				{
					jQuery(".scaffolding-editor-toggler").each
					(
						function(i)
						{
							jQuery(this).remove();
						}
					);
					
					// nxs_js_log('mouse leave detected');
					jQuery(".nxs-cursor").removeClass("nxs-hovering");
					jQuery(".nxs-cursor").removeClass("nxs-overrule-suppress");
				}
				);
			}
		);
		
		function nxs_js_menu_updateoverridenmanualcss()
		{
			jQuery('#nxs_menu_savemanualcssbutton').show();				
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
						var kleuridentificatie = jQuery(this).attr("id");
						jQuery(this).data("kleur", hex);
	
						nxs_js_processcolorupdate(hex, rgb, kleuridentificatie);
						
						// update screen
						nxs_js_menu_handlecolorthemechanged();
					}
	      }
	    );
	    
	    //
	    // reposition the minicolors after user clicks on a color picker
	    //
	    jQuery(".miniColors-trigger").bind
	    (
		    "click", function(e)
		    {
		    	var x = jQuery(this).position().left + 30;
		    	var y = jQuery(this).position().top - jQuery(this).scrollTop() + 45;
		    	
		    	jQuery(".color-picker").each(function(i)
		    	{
		    		jQuery(this).css("left", "" + x + "px");
		    		jQuery(this).css("top", "" + y + "px");
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
						/*
						var oppositehsl = nxs_js_getoppositesaturationandlightforhsl(hsl);
						var oppositergb = nxs_js_hsltorgb(oppositehsl);
						var oppositehex = nxs_js_rgbtohex(oppositergb);
						$('#vg_color_<?php echo $identification; ?>_o_m').miniColors('value', oppositehex);
						*/
						/*
						var complementaryhsl = nxs_js_getcomplementaryhsl(hsl);
						var complementaryrgb = nxs_js_hsltorgb(complementaryhsl);
						var complementaryhex = nxs_js_rgbtohex(complementaryrgb);
						$('#vg_color_<?php echo $identification; ?>_o_m').miniColors('value', complementaryhex);
						*/
						nxs_js_menu_handlecolorthemechanged();
						
						// update GUI
						jQuery('.nxs_menu_savekleurenbutton').show();
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
			nxs_js_updatecss_themecss_actualrequest();
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
					valuestobeupdated["vg_color_<?php echo $identification;?>_m"] = jQuery("#vg_color_<?php echo $identification;?>_m").val();
					<?php
				}
			}
			?>
			
			//nxs_js_log("values to be updated:");
			//nxs_js_log(valuestobeupdated);
			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQuery.ajax
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
							jQuery('.nxs_menu_savekleurenbutton').fadeOut('slow', function() { nxs_js_refreshtopmenufillerheight(); } );
							
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
		
		function nxs_menu_updatelettertypen()
		{
			// mutaties hierin ook doorvoeren in header-post.php
			
			jQuery('#dynamicCssVormgevingLettertypen').html('');
			// append
			var u;
			u = "";
			u = u + "body { font-family: " + jQuery("#vg_fontfam_1").val() + "; }";
			u = u + "h1, .nxs-size1, h2, .nxs-size2, h3, .nxs-size3, h4, .nxs-size4, h5, .nxs-size5, h6, .nxs-size6, .nxs-logo { font-family: " + jQuery("#vg_fontfam_2").val() + "; }";	
			
			// great, thanks to MS we need a lame IE patch, see http://stackoverflow.com/questions/9050441/how-do-i-inject-styles-into-ie8
			if(jQuery.browser.msie)
		  {
		    jQuery('#dynamicCssVormgevingLettertypen').prop('styleSheet').cssText=u;
		  }
			else
			{
				jQuery('#dynamicCssVormgevingLettertypen').append(u);
			}
					
			jQuery('#nxs_menu_savelettertypenbutton').show();
			// recalculate height of menu
			nxs_js_refreshtopmenufillerheight();
		}
		
		function nxs_menu_savelettertypen()
		{
			var valuestobeupdated = {};
			valuestobeupdated["vg_fontfam_1"] = jQuery("#vg_fontfam_1").val();
			valuestobeupdated["vg_fontfam_2"] = jQuery("#vg_fontfam_2").val();
			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQuery.ajax
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
							jQuery('#nxs_menu_savelettertypenbutton').fadeOut('slow', function() { nxs_js_refreshtopmenufillerheight(); } );
							
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
			jQuery('#nxs_menu_saveinjectheadbutton').show();				
			// recalculate height of menu
			nxs_js_refreshtopmenufillerheight();
		}
		
		function nxs_menu_savemanualcss()
		{
			var valuestobeupdated = {};
			valuestobeupdated["vg_manualcss"] = jQuery("#vg_manualcss").val();
			
			
			var wrong = nxs_js_getescapeddictionary(valuestobeupdated);
			nxs_js_log("good:");
			nxs_js_log(valuestobeupdated);
			nxs_js_log("bad:");
			nxs_js_log(wrong);
	
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQuery.ajax
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
							jQuery('#nxs_menu_savemanualcssbutton').fadeOut('slow', function() { nxs_js_refreshtopmenufillerheight(); } );
							
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
			valuestobeupdated["vg_injecthead"] = jQuery("#vg_injecthead").val();
	
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQuery.ajax
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
							jQuery('#nxs_menu_saveinjectheadbutton').fadeOut('slow', function() { nxs_js_refreshtopmenufillerheight(); } );
							
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
