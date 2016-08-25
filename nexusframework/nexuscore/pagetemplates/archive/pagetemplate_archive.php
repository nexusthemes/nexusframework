<?php

function nxs_pagetemplate_archive_gettitle($args)
{
	return nxs_l18n__("Archive", "nxs_td");
}

function nxs_pagetemplate_handlecontent()
{	
	global $nxs_global_current_containerpostid_being_rendered;
	$containerpostid = $nxs_global_current_containerpostid_being_rendered;
	
	$page_title = get_the_title();
	
	$iswidescreen = nxs_iswidescreen("content");
	if ($iswidescreen)
	{
		$widescreenclass = "nxs-widescreen";
	}
	else
	{
		$widescreenclass = "";
	}
	
	// get css class for this specific page
	
	$cssclass = nxs_getcssclassesforsitepage();
	$cssclass = nxs_concatenateargswithspaces($widescreenclass, $cssclass);

	$maincontent_visibility = "";
	if (isset($meta["maincontent_visibility"]))
	{
		$maincontent_visibility = $meta["maincontent_visibility"];
	}
	
	if ($maincontent_visibility == "hidden")
	{
		// suppressed
	}
	else
	{
		?>
		<div id="nxs-content" class="nxs-sitewide-element <?php echo $cssclass; ?>">
			<?php 
			
			if (nxs_hastemplateproperties())
			{
				// derive the layout
				$templateproperties = nxs_gettemplateproperties();
				if ($templateproperties["result"] == "OK")
				{
					$existingsidebarid = $templateproperties["sidebar_postid"];
				}
				else
				{
					$existingsidebarid = 0;
				}
			}
			else
			{
				$existingsidebarid = nxs_pagetemplate_getsidebarid();
			}
			
			
			
			$hassidebar = ($existingsidebarid != ""); 
			if ($hassidebar)
			{
				$contentcontainerclass = "has-sidebar";
			}
			else
			{
				$contentcontainerclass = "has-no-sidebar";
			}
			
			if ($existingsidebarid == "" || $existingsidebarid == 0)
			{	
				$toonsidebar = false;
			}
			else
			{
				$toonsidebar = true;
			}
			
			$cssclass = $contentcontainerclass;
			
			?>
			<div id="nxs-content-container" class="nxs-containsimmediatehovermenu <?php echo $cssclass; ?>">
				
				<?php
				if ($toonsidebar)  
				{ 
					echo "<div class='nxs-main'>";
				}	
				
				//
				// ---------------------------- BEGIN RENDER BLOG POST TOP / SUBHEADER
				//
								
				if (nxs_hastemplateproperties())
				{
					// derive the layout
					$templateproperties = nxs_gettemplateproperties();
					if ($templateproperties["result"] == "OK")
					{
						$subheaderid = $templateproperties["subheader_postid"];
					}
					else
					{
						$subheaderid = 0;
					}
				}
				else
				{
					$subheaderid = $meta["subheader_postid"];
				}
					
				if ($subheaderid != "")
				{
					$cssclass = nxs_getcssclassesforrowcontainer($subheaderid);
					$cssclass = nxs_concatenateargswithspaces($widescreenclass, $cssclass);
					?>
					<div class='nxs-subheader-container nxs-layout-editable nxs-widgets-editable nxs-elements-container nxs-post-<?php echo $subheaderid . " " . $cssclass; ?>'>
						<?php					
						echo nxs_getrenderedhtmlincontainer($containerpostid, $subheaderid, "default");
						?>
					</div> <!-- end nxs-subheader-container -->
					<?php
				}
				
				//
				// ---------------------------- BEGIN RENDER ACTUAL ARTICLE
				//
				
				//
				// the postid to render the actual article container content,
				// is the postid being requested in most cases, however
				// this can be overruled (especially for archive pages, where
				// the postid is not available, but also for detail pages that
				// want to use a 'strict' template)
				//
				
				if (nxs_hastemplateproperties())
				{
					// derive the layout
					$templateproperties = nxs_gettemplateproperties();
					if ($templateproperties["result"] == "OK")
					{
						$contentpostid = $templateproperties["content_postid"];
						$wpcontenthandler = $templateproperties["wpcontenthandler"];
					}
					else
					{
						$contentpostid = 0;
						$wpcontenthandler = "";
					}
					
					
				}
				else
				{
					$contentpostid = get_the_ID();
					$wpcontenthandler = "";
				}
				
						
				
				if ($contentpostid != 0)
				{
					$cssclass = nxs_getcssclassesforrowcontainer($contentpostid);
		
					echo "<div class='nxs-article-container nxs-elements-container nxs-layout-editable nxs-widgets-editable nxs-post-" . $contentpostid  . " " . $cssclass . "'>";
				  echo nxs_getrenderedhtml($contentpostid, "default");
					echo "</div> <!-- END nxs-article-container -->";
					
					//
					// ---------------------------- BEGIN RENDER SHORTCUT TO ADD NEW ROW
					//
					if (nxs_has_adminpermissions())
					{
						?>
						<div class="nxs-hidewheneditorinactive">
							<div class="nxs-clear"></div>
							<div class="nxs-row-container">
								<a class="nxsbutton1 nxs-float-left clear nxs-margin-left30" href="#" onclick="nxs_js_popup_page_neweditsession('<?php echo $contentpostid;?>', 'dialogappendrow'); return false;">Add row</a>
							</div>
							<div class="nxs-clear"></div>
						</div>
						<?php
					}
				
					//
					// ---------------------------- BEGIN RENDER BLOG CONTENT
					//
					
					$sitemeta = nxs_getsitemeta();
					
					if ($wpcontenthandler == "")
					{
						// turn to default
						$wpcontenthandler = "@template@onlywhenset";
					}
					
					$shouldrender = true;
					if ($wpcontenthandler == "@template@never")
					{
						$shouldrender = false;
					}
					
					if ($shouldrender)
					{
						$wpbackendblogcontent = get_post_field('post_content', $contentpostid);
						$wpbackendblogcontent = wpautop($wpbackendblogcontent, true);

						$wordpressbackendurl = get_edit_post_link($contentpostid, array());
						
						$shouldrender = true;
						if ($wpcontenthandler == "@template@onlywhenset" && $wpbackendblogcontent == "")
						{
							$shouldrender = false;
						}
						
						if ($shouldrender)
						{
							// reguliere post/page
							?>
							<div class='nxs-wpcontent-container nxs-elements-container nxs-layout-editable nxs-widgets-editable nxs-content-<?php echo $contentpostid  . " " . $cssclass; ?>'>
								<div class="nxs-postrows">
									<div class="nxs-row   " id="nxs-pagerow-content">
										<div class="nxs-row-container nxs-containsimmediatehovermenu nxs-row1">				
											<ul class="nxs-placeholder-list"> 
												<li class='nxs-placeholder nxs-containshovermenu1 nxs-runtime-autocellsize nxs-one-whole '>
													<?php if (nxs_has_adminpermissions()) { ?>
													<div class='nxs-hover-menu-positioner'>
														<div class='nxs-hover-menu nxs-widget-hover-menu nxs-admin-wrap inside-right-top'>
													    <ul class="">
													      <li title='Edit' class='nxs-hovermenu-button'>
													      	<a href='#' title='Edit' class="nxs-defaultwidgetclickhandler" onclick="nxs_js_popup_postcontent_neweditsession('wpcontent'); return false;">
													        	<span class="nxs-icon-text"></span>
													        </a>
													    	</li>
													    	<li title='Edit' class='nxs-hovermenu-button'>
													      	<a href="<?php echo $wordpressbackendurl; ?>" title="<?php nxs_l18n_e("WordPress backend[nxs:adminmenu,tooltip]", "nxs_td"); ?>" class="site small-wordpress">
													        	<span class="nxs-icon-wordpresssidebar"></span>
													        </a>
													    	</li>
													  	</ul>
														</div>
													</div>
													<div class='nxs-runtime-autocellsize nxs-cursor nxs-drop-cursor'>
														<span class='nxs-runtime-autocellsize'></span>
													</div>
													<div title='Edit' class='nxs-runtime-autocellsize nxs-cursor nxs-cell-cursor'>
														<span class='nxs-runtime-autocellsize'></span>
													</div>
													<?php } ?>
													<div class="ABC nxs-height100  ">
														<div class="XYZ ">
															<div class="nxs-placeholder-content-wrap nxs-crop ">
																<div id="nxs-widget-l1206856119" class="nxs-widget nxs-widget-l1206856119  nxs-text ">
																	<div>
																		<div class="nxs-default-p nxs-applylinkvarcolor nxs-padding-bottom0 nxs-align-left   nxs-heightiq nxs-heightiq-p1-text " style="height: 24px;">
																			<?php 
																				if ($wpbackendblogcontent != "")
																				{
																					// apply shortcodes, and output the result
																					echo nxs_applyshortcodes($wpbackendblogcontent);
																				}
																				else
																				{
																					if (nxs_has_adminpermissions()) 
																					{
																						echo "<p class='nxs-hidewheneditorinactive' style='min-height: 30px;'>" . nxs_l18n__("Click here to start editing your content.", "nxs_td") . "</p>"; 
																					}
																				}
																			?>
																		</div>
																	</div>
																	<div class="nxs-clear">
																	</div>
																</div>
															</div>
														</div>
													</div>
												</li>
											</ul>
											<div class="nxs-clear"></div>
										</div>
									</div>
								</div>
							</div>
							<?php
						}
					}
					
					
				}
				else
				{
					// suppressed
				}
				
				// WP BACKEND CONTENT				
					
				$templateproperties = nxs_gettemplateproperties();
									
				if ($templateproperties["result"] == "OK")
				{
					$wpcontenthandler = $templateproperties["wpcontenthandler"];
					if ($wpcontenthandler == "")
					{
						// turn to default
						$wpcontenthandler = "@template@onlywhenset";
					}
					
					// 1111111
					if ($wpcontenthandler == "@template@never")
					{
						// ignore
					}
					else
					{
						// 
						$shouldrenderoriginaltemplate = true;
						global $nxs_gl_templates_wp;
						
						// dont use this approach on nexusthemes.com
						$homeurl = nxs_geturl_home();
						
						if ($homeurl == "http://nexusthemes.com/")
						{
							$wpposttype = nxs_getwpposttype($contentpostid);
							if ($wpposttype == "product")
							{
								$shouldrenderoriginaltemplate = false;
							}
						}
										
						if ($shouldrenderoriginaltemplate)
						{							
							echo "<!-- 4 original template; $nxs_gl_templates_wp -->";

							rewind_posts();

							nxs_ob_start();
							// delegate to the original template handler
							//nxs_ob_start();
							include($nxs_gl_templates_wp);
							$wpmaincontenthtml = nxs_ob_get_contents();
							nxs_ob_end_clean();
						
							// TODO: determine here whether we need to output this yes or no,
							// depending on the configuration of backend content, and whether or not
							// there actually is something relevant to render
							$wpmaincontenthtmlsize = strlen($wpmaincontenthtml);
							
							$shouldrenderthis = false;
							if ($wpcontenthandler == "@template@always")
							{
								$shouldrenderthis = true;
								if ($wpmaincontenthtmlsize == 0)
								{
									$wpmaincontenthtml = "&nbsp;";
								}
							}
							if ($wpmaincontenthtmlsize > 0)
							{
								$shouldrenderthis = true;
							}
						
							if ($shouldrenderthis == true)
							{
								// reguliere post/page
								?>
								<div class='nxs-wpcontent-container nxs-elements-container nxs-layout-editable nxs-widgets-editable entry-content nxs-content-<?php echo $contentpostid  . " " . $cssclass; ?>'>
									<div class="nxs-postrows">
										<div class="nxs-row   " id="nxs-pagerow-content">
											<div class="nxs-row-container nxs-containsimmediatehovermenu nxs-row1">				
												<ul class="nxs-placeholder-list"> 
													<li class='nxs-placeholder nxs-containshovermenu1 nxs-one-whole '>
														<!-- no front end editor -->
														<div class="ABC nxs-height100  ">
															<div class="XYZ ">
																<div class="nxs-placeholder-content-wrap nxs-crop ">
																	<div id="nxs-widget-l1206856119" class="nxs-widget nxs-widget-l11223344556 nxs-text ">
																		<div>
																			<div class="nxs-default-p nxs-applylinkvarcolor nxs-padding-bottom0 nxs-align-left">
																				<?php
																				echo $wpmaincontenthtml;
																				?>
																			</div>
																		</div>
																		<div class="nxs-clear">
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</li>
												</ul>
												<div class="nxs-clear"></div>
											</div>
										</div>
									</div>
								</div>
								<?php
							}
							else
							{
								?>
								<!-- wp back end content is empty, nothing to do here -->
								<?php
							}
						}
					}
					rewind_posts();						
					// 2222222
					
				}
				else
				{
					// no rendering
				}
				
				// END OF WP BACKEND CONTENT
					
				//
				// ---------------------------- BEGIN RENDER BLOG POST BOTTOM / SUBFOOTER
				//			
				
				if (nxs_hastemplateproperties())
				{
					// derive the layout
					$templateproperties = nxs_gettemplateproperties();
					if ($templateproperties["result"] == "OK")
					{
						$subfooterid = $templateproperties["subfooter_postid"];
					}
					else
					{
						$subfooterid = 0;
					}
				}
				else
				{
					$subfooterid = $meta["subfooter_postid"];
				}
				
				if ($subfooterid != "")
				{
					$cssclass = nxs_getcssclassesforrowcontainer($subfooterid);
					$cssclass = nxs_concatenateargswithspaces($widescreenclass, $cssclass);
	
					?>			
					<div class='nxs-subfooter-container nxs-layout-editable nxs-widgets-editable nxs-elements-container nxs-post-<?php echo $subfooterid . " " . $cssclass; ?>'>
						<?php
						echo nxs_getrenderedhtmlincontainer($containerpostid, $subfooterid, "default");
						?>
					</div> <!-- end nxs-subfooter-container -->
					<?php
				}
				
		  	if ($toonsidebar)  
				{ 
					echo "</div> <!-- END nxs-main -->";
				}
		
				if ($toonsidebar) 
				{
					$cssclass = nxs_getcssclassesforrowcontainer($existingsidebarid);
					$cssclass = nxs_concatenateargswithspaces($widescreenclass, $cssclass);
					
					echo "<aside>";
					echo "<div class='nxs-sidebar-container nxs-elements-container nxs-sidebar1 nxs-layout-editable nxs-widgets-editable nxs-post-" . $existingsidebarid . " " . $cssclass . "'>";
					echo nxs_getrenderedhtmlincontainer($containerpostid, $existingsidebarid, "default");
					echo "</div> <!-- end nxs-sidebar-container -->";
					echo "</aside>";
				} 
				?>
				
				<div class="nxs-clear">
				</div>
			
			</div>
			
		</div> <!-- END content -->
		<?php
	}
}

function nxs_pagetemplate_handlefooter()
{
	global $nxs_global_current_containerpostid_being_rendered;
	$containerpostid = $nxs_global_current_containerpostid_being_rendered;

	$meta = nxs_get_postmeta($containerpostid);
	
	if (nxs_hastemplateproperties())
	{
		// derive the layout
		$templateproperties = nxs_gettemplateproperties();
		if ($templateproperties["result"] == "OK")
		{
			$existingfooterid = $templateproperties["footer_postid"];
		}
		else
		{
			$existingfooterid = 0;
		}
	}
	else
	{
		$existingfooterid = $meta["footer_postid"];
	}
	
	
	$iswidescreen = nxs_iswidescreen("footer");
	if ($iswidescreen)
	{
		$widescreenclass = "nxs-widescreen";
	}
	else
	{
		$widescreenclass = "";
	}

	if ($existingfooterid != "")
	{
		$cssclass = nxs_getcssclassesforrowcontainer($existingfooterid);
	}
	else
	{
		$cssclass = "";
	}

	?>
	<div id="nxs-footer" class="nxs-containsimmediatehovermenu nxs-sitewide-element <?php echo $widescreenclass; ?>">
    <div id="nxs-footer-container" class="nxs-sitewide-container nxs-footer-container nxs-elements-container nxs-layout-editable nxs-widgets-editable nxs-post-<?php echo $existingfooterid . " " . $cssclass; ?>">
			<?php 
			if ($existingfooterid != "")
			{
				echo nxs_getrenderedhtmlincontainer($containerpostid, $existingfooterid, "default");
			}
			do_action("nxs_action_postfooterlink");
      ?>
    </div>
	</div> <!-- end #nxs-footer -->	
	<?php
	
	?>			
			
		</div> <!-- end #nxs-container -->	
		<?php get_template_part('includes/scripts'); ?>
		<?php wp_footer(); ?>
	</body>
</html>
<?php
}

function nxs_pagetemplate_handleheader()
{
	global $nxs_global_current_containerpostid_being_rendered;
	$containerpostid = $nxs_global_current_containerpostid_being_rendered;
	
	$pagemeta = nxs_get_postmeta($containerpostid);
	$page_cssclass = $pagemeta["page_cssclass"];

	$sitemeta	= nxs_getsitemeta();

	if (nxs_hastemplateproperties())
	{
		// derive the layout
		$templateproperties = nxs_gettemplateproperties();
		
		if ($templateproperties["result"] == "OK")
		{
			$existingheaderid = $templateproperties["header_postid"];
		}
		else
		{
			$existingheaderid = 0;
		}
	}
	else
	{
		$existingheaderid = $pagemeta["header_postid"];
	}
	
	if (isset($sitemeta["faviconid"]))
	{
		$faviconid = $sitemeta["faviconid"];
		$faviconlookup = wp_get_attachment_image_src($faviconid, 'full', true);
		$faviconurl = $faviconlookup[0];
		$faviconurl = nxs_img_getimageurlthemeversion($faviconurl);
	}
	else
	{
		$faviconid = "";
	}
?>
<!DOCTYPE html>
<?php nxs_render_htmlstarttag(); ?>
<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo nxs_getcharset(); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
	<!-- Nexus Framework | http://nexusthemes.com -->	
	<meta name="generator" content="Nexus Themes | <?php echo nxs_getthemename(); ?>" />
	<title><?php wp_title(''); ?></title>
	<?php nxs_render_htmlcorescripts(); ?>
	<?php 
	nxs_hideadminbar();	
	wp_enqueue_style('nxsbox');
	// the wp_head alters the $post variable,
	// to prevent this from happening, we store the post
	$beforepost = $post;
	wp_head();
	// the wp_head alters the $post variable,
	// to prevent this from happening, we restore the post
	$post = $beforepost;
	?>
	<?php	if (isset($faviconurl)) { ?>
	<link rel="shortcut icon" href="<?php echo $faviconurl; ?>" type="image/x-icon" />
	<?php	} ?>
	<?php

	// dit wordt niet op goede plek ge-enqueued
	
	?>
	<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width" />
	<?php
		
	//
	nxs_render_headstyles();
	nxs_analytics_handleanalytics();
	
	if (is_user_logged_in()) { ?>
	
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
	
	<?php } ?>
	
	<?php
	
	if (nxs_has_adminpermissions() && $_REQUEST["customhtml"] == "off")
	{
		// suppress
	}
	else
	{
		echo $sitemeta["vg_injecthead"];
	}

	do_action('nxs_beforeend_head');
	?>
</head>

<body <?php body_class(); ?> <?php do_action('nxs_render_bodyatts'); ?>>
	<?php do_action('nxs_render_bodyatts'); ?>
	<?php if (nxs_showloadcover()) { ?>
		<div id="nxs-load-cover" style='position: fixed; height: 100%; width: 100%; top:0; left: 0; background: #000; z-index:9999;'></div>
	<?php } else { ?>
		<div id="nxs-load-cover" style=''></div>
	<?php } ?>
	
	<?php do_action('nxs_bodybegin'); ?>

	<?php include(NXS_FRAMEWORKPATH . '/nexuscore/includes/nxsmenu.php'); ?>
	<?php require_once(NXS_FRAMEWORKPATH . '/nexuscore/includes/frontendediting.php'); ?>

	<?php
	global $nxs_global_current_containerpostid_being_rendered;
	$containerpostid = $nxs_global_current_containerpostid_being_rendered;
	?>
	
	<?php global $nxs_global_extendrootclass; ?>
 <div id="nxs-container" class="nxs-containsimmediatehovermenu nxs-no-click-propagation <?php echo $page_cssclass . " " . $nxs_global_extendrootclass; ?>">
	<?php
	$iswidescreen = nxs_iswidescreen("header");
	if ($iswidescreen)
	{
		$widescreenclass = "nxs-widescreen";
	}
	else
	{
		$widescreenclass = "";
	}
	
	if (isset($existingheaderid) && $existingheaderid != 0)
	{
		$cssclass = nxs_getcssclassesforrowcontainer($existingheaderid);
		?>
		<div id="nxs-header" class="nxs-containshovermenu1 nxs-sitewide-element <?php echo $widescreenclass; ?>">
			<div id="nxs-header-container" class="nxs-sitewide-container nxs-header-container nxs-elements-container nxs-layout-editable nxs-widgets-editable nxs-containshovermenu1 nxs-post-<?php echo $existingheaderid . " " . $cssclass; ?>">
				<?php 
					if ($existingheaderid != "")
					{
						?>
						<div class="nxs-header-topfiller"></div>
						<?php
						
						echo nxs_getrenderedhtmlincontainer($containerpostid, $existingheaderid, "default");
					}
					else
					{
						// don't render anything if its not there
					}
				?>
	    </div>
	    <div class="nxs-clear"></div>
	  </div> <!-- end #nxs-header -->
	  <?php 
	}
}

function nxs_pagetemplate_handlepagedecorator($pagedecoratorid)
{
	if (isset($pagedecoratorid))
	{
		$poststatus = get_post_status($pagedecoratorid);
		if ($poststatus == "publish")
		{
			$parsedpagedecoratorstructure = nxs_parsepoststructure($pagedecoratorid);
					
			foreach ($parsedpagedecoratorstructure as $currentdecoratoritem)
			{
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
				 		// now that the widget is loaded, instruct the widget to register the needed hooks
				 		// if it has some
				 		$hookargs = array();
				 		$hookargs["pagedecoratorid"] = $pagedecoratorid;
				 		$hookargs["pagedecoratorwidgetplaceholderid"] = $pagewidgetplaceholderid;
				 		nxs_widgets_registerhooksforpagewidget($widget, $hookargs);
				 	}
				 	else
				 	{
				 		// 
				 		echo "[warning, widget not found?]";
				 	}
				}
				else
				{
					// blank
				}
			}
		}
		else
		{
			// not published
		}
	}
	else
	{
		// no pagedecorator found
	}
}

function nxs_pagetemplate_getsidebarid()
{
	if (nxs_hastemplateproperties())
	{		
		// derive the layout
		$templateproperties = nxs_gettemplateproperties();		
		if ($templateproperties["result"] == "OK")
		{
			$existingsidebarid = $templateproperties["sidebar_postid"];
		}
		else
		{
			$existingsidebarid = 0;
		}
	}
	else
	{
		$existingsidebarid = $pagemeta["sidebar_postid"];
	}
	return $existingsidebarid;
}

function nxs_pagetemplate_archive_render($args)
{
	if (is_singular())
	{
		// the containerpostid is the id of the (one and only) post
		global $post;
		$containerpostid = $post->ID;
	}
	else if (is_archive() || is_404() || is_home() || is_search())
	{
		$templateproperties = nxs_gettemplateproperties();
		
		if ($templateproperties["result"] == "OK")
		{
			$containerpostid = $templateproperties["content_postid"];
			if (!isset($containerpostid) || $containerpostid == 0)
			{
				$containerpostid = "SOMEARCHIVE_A";
			}
			else
			{
				//
			}
		}
		else
		{
			$containerpostid = "SOMEARCHIVE_B";
		}
	}
	else
	{
		// unexpected?
		nxs_webmethod_return_nack("unsupported; no singular and no archive?");
	}
	
	global $nxs_global_current_containerpostid_being_rendered;
	$nxs_global_current_containerpostid_being_rendered = $containerpostid;
	
	$pagemeta = nxs_get_postmeta($containerpostid);
	$page_cssclass = $pagemeta["page_cssclass"];

	$sitemeta = nxs_getsitemeta();
	$site_cssclass = $sitemeta["site_cssclass"];
	$site_colorzen = nxs_getcssclassesforlookup("nxs-colorzen-", $sitemeta["site_colorzen"]);
	$site_linkcolorvar = nxs_getcssclassesforlookup("nxs-linkcolorvar-", $sitemeta["site_linkcolorvar"]);
	$site_bg_pattern = $sitemeta["site_bg_pattern"];
	$site_text_fontsize = nxs_getcssclassesforlookup("nxs-text-fontsize-", $sitemeta["site_text_fontsize"]);
	
	$concatenated_css = nxs_concatenateargswithspaces($site_cssclass, $site_colorzen, $site_bg_pattern, $site_linkcolorvar, $site_text_fontsize);
	
	// inject cssclass to body html tag
	global $nxs_global_extendrootclass;
	$nxs_global_extendrootclass .= $concatenated_css;
	
	
	
	$existingsidebarid = nxs_pagetemplate_getsidebarid();
	
	if ($existingsidebarid == "" || $existingsidebarid == 0)
	{	
		$toonsidebar = false;
	}
	else
	{
		$toonsidebar = true;
	}
	
	//
	// load the page decorator (if any)
	//
	
	// derive the layout
	$templateproperties = nxs_gettemplateproperties();
	if ($templateproperties["result"] == "OK")
	{
		
		$sitewideelements = nxs_pagetemplates_getsitewideelements();
		foreach($sitewideelements as $currentsitewideelement)
  	{
  		$pagemeta[$currentsitewideelement] = $templateproperties[$currentsitewideelement];
  	}
	}
	else
	{
		//
	}
	
	//
	// ACTUAL RENDERING OF PAGE
	//
	
	// derive the layout
	if (nxs_hastemplateproperties())
	{	
		$templateproperties = nxs_gettemplateproperties();
		
		if ($templateproperties["result"] == "OK")
		{
			$pagedecorator_postid = $templateproperties["pagedecorator_postid"];
		}
		else
		{
			$pagedecorator_postid = 0;
		}
	}
	else
	{
		$pagedecorator_postid = $pagemeta["pagedecorator_postid"];
	}
	
	nxs_pagetemplate_handlepagedecorator($pagedecorator_postid);
	
	//
	//
	//
	
	nxs_pagetemplate_handleheader();
	
	//
	
	do_action('nxs_ext_betweenheadandcontent');
	
	//
	
	nxs_pagetemplate_handlecontent();

	//

	nxs_pagetemplate_handlefooter();
}

function nxs_pagetemplate_archive_renderpreview($args)
{
	?>
	<div class="content2">
    <div class="box">
        <div class="box-title">
            <h4>&nbsp;</h4>
         </div>
        <div class="box-content">
        	<span class='title'>
        		<?php nxs_l18n_e("Description of archive preview[nxs:preview]", "nxs_td"); ?>
        	</span>
        </div>
    </div>
    <div class="nxs-clear"></div>
  </div> <!--END content-->
	<?php
}

function nxs_pagetemplate_archive_home_getsheethtml($args)
{
	//
	extract($args);
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	$result = array();
	
	nxs_ob_start();

	?>

	<div class="nxs-admin-wrap">
		<div class="block">	
      
     	<?php nxs_render_popup_header(nxs_l18n__("Archive", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		      
		 			<div class="content2">
		        <div class="box">
		          <div class="box-title">
								<h4><?php nxs_l18n_e("Todo", "nxs_td"); ?></h4>		                
		          </div>
		          <div class="box-content">
								<p>todo</p>
		          </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->    		

	      </div>
	    </div>
      
      <!-- footer -->
      
      <div class="content2">
        <div class="box">
        	<!--
        	<a href='#' class="nxsbutton1 nxs-float-left" onclick='nxs_js_popup_navigateto("appendstruct"); return false;'>Append data</a>
        	<a href='#' class="nxsbutton1 nxs-float-left" onclick='nxs_js_popup_navigateto("exportstruct"); return false;'>Export data</a>
        	-->
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Save[nxs:button]", "nxs_td"); ?></a>
          <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:button]", "nxs_td"); ?></a>
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:button]", "nxs_td"); ?></a>
       	</div>
        <div class="nxs-clear">
        </div>
      </div> <!--END content-->
		</div>
	</div>
	
	<script type='text/javascript'>
			
		function nxs_js_savepopupdata()
		{
			//
		}
		
		function nxs_js_execute_after_popup_shows()
		{
			//
		}
		
		function nxs_js_savegenericpopup()
		{
			nxs_js_alert('todo');
		}		
	</script>
	
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

?>