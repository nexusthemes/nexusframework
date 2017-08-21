<?php

function nxs_pagetemplate_blogentry_gettitle($args)
{
	return nxs_l18n__("Blogentry[nxs:title]", "nxs_td");
}

function nxs_render_loginforpasswordprotectedcontent()
{
	$html = get_the_password_form();
	
	$enhanced_html = $html;
	// class="post-password-form" => class="post-password-form nxs-form"
	$enhanced_html = str_replace("post-password-form", "nxs-form post-password-form", $enhanced_html);
	?>
	<div id="nxs-content" class="nxs-sitewide-element">
		<div id="nxs-content-container" class="has-no-sidebar">
			<div class="nxs-article-container nxs-elements-container">
				<div class="nxs-postrows">
					<div class="nxs-row nxs-padding-top-1-0 nxs-padding-bottom-1-0 ">
						<div class="nxs-row-container nxs-containsimmediatehovermenu nxs-row1">
							<ul class="nxs-placeholder-list"> 
								<li class="nxs-placeholder nxs-containshovermenu1 nxs-runtime-autocellsize nxs-one-third nxs-unistyle-reference nxs-unistyled nxs-not-unicontented nxs-widgettype-text  nxs-column-1-3" style="height: 342px;">
									<div class="ABC">
										<div class="XYZ">
											<div class="nxs-placeholder-content-wrap nxs-crop ">
												<div id="nxs-widget-passwordidentifier" class="nxs-widget nxs-text">
													<div class="nxs-default-p nxs-applylinkvarcolor nxs-padding-bottom0 nxs-align-left   nxs-heightiq nxs-heightiq-p1-text " style="height: 121px;">
														<?php echo $enhanced_html; ?>
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
		</div>
	</div>
	<?php
}

function nxs_template_getfractionmarker($id)
{
	return "<!-- FRACTIONMARKER.$id -->";
}

function nxs_pagetemplate_handlecontent()
{
	global $nxs_global_current_containerpostid_being_rendered;
	$containerpostid = $nxs_global_current_containerpostid_being_rendered;
	
	$pagemeta = nxs_get_corepostmeta($containerpostid);
	
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

	// derive the layout
	$templateproperties = nxs_gettemplateproperties();
	if ($templateproperties["result"] == "OK")
	{
		// when the content, subheader, subfooter, and sidebar are suppressed,
		// the entire wrap (maincontent) should be set to hidden
		if (
			$templateproperties["sidebar_postid"] == "@suppressed" &&
			$templateproperties["subheader_postid"] == "@suppressed" &&
			$templateproperties["subfooter_postid"] == "@suppressed" &&
			$templateproperties["content_postid"] == "@suppressed"
		)
		{
			$maincontent_visibility = "hidden";
		}
		// also if just the content if suppressed, also hide the content
		else if ($templateproperties["content_postid"] == "@suppressed")
		{
			
			$maincontent_visibility = "hidden";
		}
		else
		{
			
		}
	}
	
	$showcontent = true;
	
	if ($maincontent_visibility == "hidden")
	{
		// suppressed
		$showcontent = false;
	}
	
	$contentpostid = $templateproperties["content_postid"];
	$isremotetemplate = nxs_isremotetemplate($contentpostid);
	if (!$isremotetemplate && post_password_required($contentpostid)) 
	{
		nxs_render_loginforpasswordprotectedcontent();
		$showcontent = false;
	}
	
	if ($showcontent)
	{
		// derive the layout
		$templateproperties = nxs_gettemplateproperties();
		if ($templateproperties["result"] == "OK")
		{
			$wpcontenthandler = $templateproperties["wpcontenthandler"];
			$existingsidebarid = $templateproperties["sidebar_postid"];
			$subheaderid = $templateproperties["subheader_postid"];
			$contentpostid = $templateproperties["content_postid"];
			$subfooterid = $templateproperties["subfooter_postid"];
		}
		else
		{
			$wpcontenthandler = "";
			$existingsidebarid = 0;
			$subheaderid = 0;
			$contentpostid = 0;
			$subfooterid = 0;
		}
		
		if ($existingsidebarid == "" || $existingsidebarid == 0)
		{	
			$toonsidebar = false;
		}
		else
		{
			$toonsidebar = true;
		}
		
		// by default we render wp regular content
		$renderdelegatedcontent = true;
		// exception
		if ($contentpostid == 0)
		{
			if (!is_singular() && !is_archive())
			{
				$renderdelegatedcontent = true;
				$contentpostid = "SUPPRESSED";
			}
			else
			{
				$renderdelegatedcontent = false;
			}
		}
		
		$shouldrenderaddnewrowoption = false;
		$isremotetemplate = nxs_isremotetemplate($contentpostid);
		if (nxs_has_adminpermissions() && $contentpostid != "SUPPRESSED" && $contentpostid != -999001 && !$isremotetemplate)
		{
			$shouldrenderaddnewrowoption = true;
		}
		?>
		<div id="nxs-content" class="nxs-sitewide-element <?php echo $cssclass; ?>">
			<?php 
			$hassidebar = ($existingsidebarid != ""); 
			if ($hassidebar)
			{
				$contentcontainerclass = "has-sidebar";
			}
			else
			{
				$contentcontainerclass = "has-no-sidebar";
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
	
				if ($subheaderid != "")
				{
					$cssclass = nxs_getcssclassesforrowcontainer($subheaderid);
					$cssclass = nxs_concatenateargswithspaces($widescreenclass, $cssclass);
					?><div class='nxs-subheader-container <?php echo $cssclass; ?>'><?php					
						echo nxs_getrenderedhtmlincontainer($containerpostid, $subheaderid, "default");
						?>
					</div>
					<?php
				}
				
				//
				// ---------------------------- CONTENTSTART FRACTION MARKER
				//
				echo nxs_template_getfractionmarker("start");
				
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
				
				if ($renderdelegatedcontent)
				{
					$cssclass = nxs_getcssclassesforrowcontainer($contentpostid);

					if (nxs_iseditor())
					{
						$cssclass.= " nxs-layout-editor-editable";
					}
					?>
					<div class='nxs-article-container <?php echo $cssclass; ?>'>
						<?php
				  	echo nxs_getrenderedhtml($contentpostid, "default");
				  	?>
				  </div> <!-- END nxs-article-container -->
				  <?php
					
					//
					// ---------------------------- BEGIN RENDER SHORTCUT TO ADD NEW ROW
					//
					
					if (false)
					{
						$shouldrenderaddnewrowoption = apply_filters('nxs_f_shouldrenderaddnewrowoption', $shouldrenderaddnewrowoption);
						if ($shouldrenderaddnewrowoption)
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
					}
					
					//
					// ---------------------------- BEGIN RENDER PLUGGABLE CONTENT
					//
					
					do_action("nxs_action_rendercontent");
				
					//
					// ---------------------------- BEGIN RENDER BACKEND CONTENT
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
							echo "<!-- 4 original template; [$nxs_gl_templates_wp] -->";

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
								if ($isremotetemplate)
								{
									$contentclass = "nxs-content-remotetemplate";
								}
								else
								{
									$contentclass = "nxs-content-{$contentpostid}";
								}
								
								?>
								<div class='nxs-wpcontent-container nxs-elements-container nxs-layout-editable nxs-widgets-editable entry-content <?php echo $contentclass . " " . $cssclass; ?>'>
									<div class="nxs-postrows">
										<div class="nxs-row   " id="nxs-pagerow-content">
											<div class="nxs-row-container nxs-containsimmediatehovermenu nxs-row1">				
												<ul class="nxs-placeholder-list"> 
													<li class='nxs-placeholder nxs-containshovermenu1 nxs-one-whole '>
														<?php 
														if (nxs_has_adminpermissions() && !$isremotetemplate) 
														{ 
															$wordpressbackendurl = get_edit_post_link($contentpostid, array());	
															?>
															<div class='nxs-hover-menu-positioner'>
																<div class='nxs-hover-menu nxs-widget-hover-menu nxs-admin-wrap inside-right-top'>
															    <ul class="">
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
															<?php 
														} 
														?>
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
				}
				else
				{
					// suppressed
				}
				
				//
				// ---------------------------- CONTENTEND FRACTION MARKER
				//
				echo nxs_template_getfractionmarker("end");
					
				//
				// ---------------------------- BEGIN RENDER BLOG POST BOTTOM / SUBFOOTER
				//			
				
				if ($subfooterid != "")
				{
					$cssclass = nxs_getcssclassesforrowcontainer($subfooterid);
					$cssclass = nxs_concatenateargswithspaces($widescreenclass, $cssclass);
					?><div class='nxs-subfooter-container <?php echo $cssclass; ?>'><?php					
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
					echo "<div class='nxs-sidebar-container nxs-sidebar1 " . $cssclass . "'>";
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

function nxs_pagetemplate_handlecontent_fraction($scope)
{
	// todo: this function can be improved by caching the output
	// in a global variable such that the handlecontent is not invoked 2x...
	
	// renders a fraction of the regular handlecontent content
	nxs_ob_start();
	$content = nxs_pagetemplate_handlecontent();
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	if ($scope == "top")
	{
		// return everything from the buffer up to the fractionstart
		$fraction = nxs_template_getfractionmarker("start");
		$pos = strpos($html, $fraction);
		$html = substr($html, 0, $pos + strlen($fraction));
		echo $html;
		echo "<div class='nxs-text nxs-default-p nxs-row1 entry-content'>";
	}
	else if ($scope == "bottom")
	{
		// return everything from the buffer from the fraction to the end
		$fraction = nxs_template_getfractionmarker("end");
		$pos = strpos($html, $fraction);
		$html = substr($html, $pos);
		echo "</div>";
		echo $html;
	}
	else
	{
		nxs_webmethod_return_nack("unsupported scope");
	}
}

function nxs_pagetemplate_handlefooter()
{
	global $nxs_global_current_containerpostid_being_rendered;
	$containerpostid = $nxs_global_current_containerpostid_being_rendered;

	$meta = nxs_get_corepostmeta($containerpostid);
	
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

	if ($existingfooterid != "")
	{
		?>
		<div id="nxs-footer" class="nxs-containsimmediatehovermenu nxs-sitewide-element <?php echo $widescreenclass; ?>">
	    <div id="nxs-footer-container" class="nxs-sitewide-container nxs-footer-container <?php echo $cssclass; ?>">
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
	}
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
		
	$pagemeta = nxs_get_corepostmeta($containerpostid);
	$page_cssclass = $pagemeta["page_cssclass"];

	$sitemeta	= nxs_getsitemeta();

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
	
	if (isset($sitemeta["faviconid"]))
	{
		$faviconid = $sitemeta["faviconid"];
		$faviconlookup = nxs_wp_get_attachment_image_src($faviconid, 'full', true);
		$faviconurl = $faviconlookup[0];
		$faviconurl = nxs_img_getimageurlthemeversion($faviconurl);
	}
	else
	{
		$faviconid = "";
	}

	$meta = nxs_theme_getmeta();
	$version = $meta["version"];
	
	$headmeta = trim(nxs_getheadmeta()) . " v1";
?>
<!DOCTYPE html>
<?php nxs_render_htmlstarttag(); ?>
<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo nxs_getcharset(); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
	<!-- Nexus Framework | http://nexusthemes.com -->	
	<!-- Nexus Meta | <?php echo $headmeta; ?> -->
	<meta name="generator" content="Nexus Themes | <?php echo nxs_getthemeid(); ?> | <?php echo $version; ?>" />
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
	
	// Google Tag Manager; HEAD PART - NEW STYLE
	if ($sitemeta["googletagmanagerid"] != "")
	{
		if (!is_user_logged_in())
		{
			?>
			<!-- Google Tag Manager -->
			<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
			new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
			j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
			'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
			})(window,document,'script','dataLayer','<?php echo $sitemeta["googletagmanagerid"]; ?>');</script>
			<!-- End Google Tag Manager -->
			<?php
		}
		else
		{
			?><!-- Google Tag Manager is suppressed for authenticated users (see https://github.com/nexusthemes/communityrfc/issues/60) --><?php
		}
	}
	do_action('nxs_beforeend_head');
	?>
</head>

<body <?php body_class(); ?> <?php do_action('nxs_render_bodyatts'); ?>>
	<?php
	// Google Tag Manager; OLD STYLE
	if ($sitemeta["googletagmanager"] != "" && $sitemeta["googletagmanagerid"] == "")
	{
		if (!is_user_logged_in())
		{
			echo "<!-- Google Tag Manager -->";
			echo $sitemeta["googletagmanager"];
		}
		else
		{
			?><!-- Google Tag Manager is suppressed for authenticated users (see https://github.com/nexusthemes/communityrfc/issues/60) --><?php
		}
	}
	// Google Tag Manager; BODY PART - NEW STYLE
	if ($sitemeta["googletagmanagerid"] != "")
	{
		if (!is_user_logged_in())
		{
			?>
			<!-- Google Tag Manager (noscript) -->
			<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $sitemeta["googletagmanagerid"]; ?>"
			height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
			<!-- End Google Tag Manager (noscript) -->
			<?php
		}
		else
		{
			?><!-- Google Tag Manager is suppressed for authenticated users (see https://github.com/nexusthemes/communityrfc/issues/60) --><?php
		}
	}
	?>
	<?php if (nxs_showloadcover()) { ?>
		<div id="nxs-load-cover" style='position: fixed; height: 100%; width: 100%; top:0; left: 0; background: #000; z-index:9999;'></div>
	<?php } else { ?>
		<div id="nxs-load-cover" style=''></div>
	<?php } ?>
	
	<?php do_action('nxs_bodybegin'); ?>
	
	<?php
	// loading the frontendediting overrides the containerpostid,
	// we therefore store it first
	global $nxs_global_current_containerpostid_being_rendered;
	$nxs_global_current_containerpostid_being_rendered = $containerpostid;
	?>
	<?php do_action("nxs_render_frontendeditor"); ?>
	<?php
	// loading the frontendediting overrides the containerpostid?
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

		//		
		$metadata = nxs_get_corepostmeta($existingheaderid);
		$rc_colorzen = nxs_getcssclassesforlookup("nxs-colorzen-", $metadata["rc_colorzen"]);
		$styles = array();
		$styles["colorzen"] = $rc_colorzen;
		$compiled[0] = nxs_frontendframework_nxs2_compilestyle($styles);
		
		?>
		<div id="nxs-header" class="nxs-containshovermenu1 nxs-sitewide-element <?php echo $widescreenclass; ?>">
			<div id="nxs-header-container" class="<?php echo $compiled[0]["id"]; ?> nxs-sitewide-container nxs-header-container nxs-containshovermenu1 <?php echo $cssclass; ?>">
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
	    <?php
	    foreach ($compiled as $id => $compiledresult)
			{
				$stylehtml = "";
				$class = $compiledresult["id"];
				$rules = $compiledresult["rules"];
				if (count($rules) > 0)
				{
					$stylehtml .= '<style>';
					$stylehtml .= ".{$class} { " . implode($rules) . " }";
					$stylehtml .= '</style>';
				}
				echo $stylehtml;
			}
	    ?>
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

function nxs_pagetemplate_blogentry_render($args)
{	
	if (is_attachment())
	{
		$templateproperties = nxs_gettemplateproperties();
		if ($templateproperties["lastmatchingrule"] == "busruleisattachment")
		{
			// the templateproperties will render the correct output
		}
		else 
		{
			$attachmentid = $_REQUEST["attachment_id"];
			?>
			<h1><?php the_title(); ?></h1>
			<?php 
			if (wp_attachment_is_image($attachmentid))
			{
				$att_image = nxs_wp_get_attachment_image_src($attachmentid, "full"); 
				$att_image_src = $att_image[0];
				$att_image_src = nxs_img_getimageurlthemeversion($att_image_src);
				?>
			  <img src="<?php echo $att_image_src;?>" width="<?php echo $att_image[1];?>" height="<?php echo $att_image[2];?>"  class="attachment-medium" alt="<?php $post->post_excerpt; ?>" />
				<?php 
			}
			else 
			{
				//header('Content-type: application/pdf');
				// redirect to the attachment url
				$attachmenturl = wp_get_attachment_url($attachmentid);
				wp_redirect($attachmenturl);
			}
			
			return;
		}
	}
	
	if (is_404())
	{
		$templateproperties = nxs_gettemplateproperties();
		global $nxs_global_current_containerpostid_being_rendered;
		$nxs_global_current_containerpostid_being_rendered = $templateproperties["content_postid"];
		$containerpostid = $nxs_global_current_containerpostid_being_rendered;
	}
	else if (is_singular())
	{
		// the containerpostid is the id of the (one and only) post
		global $post;
		$containerpostid = $post->ID;
	}
	else if (is_archive())
	{
		$containerpostid = "ARCHIVE";
	}
	else
	{
		// this happens if a plugin has a specific URL 
		// rewritten to a specific template include.
		// in that case we will render that specific content,
		// even though the front end editor features will be suppressed
		$containerpostid = "SUPPRESSED";
	}

	global $nxs_global_current_containerpostid_being_rendered;
	$nxs_global_current_containerpostid_being_rendered = $containerpostid;
	
	$pagemeta = nxs_get_corepostmeta($containerpostid);
	
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
	
	//
	// load the page decorator (if any)
	//
	
	// derive the layout
	$templateproperties = nxs_gettemplateproperties();
	
	if ($templateproperties["result"] == "OK")
	{
		$pagedecorator_postid = $templateproperties["pagedecorator_postid"];
	}
	else
	{
		$pagedecorator_postid = 0;
	}
	
	//
	// ACTUAL RENDERING OF PAGE
	//
	
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

function nxs_pagetemplate_blogentry_renderpreview($args)
{
	?>
	<div class="content2">
    <div class="box">
        <div class="box-title">
            <h4>&nbsp;</h4>
         </div>
        <div class="box-content">
        	<span class='title'>
        		<?php nxs_l18n_e("Description of blogentry preview[nxs:preview]", "nxs_td"); ?>
        	</span>
        </div>
    </div>
    <div class="nxs-clear"></div>
  </div> <!--END content-->
	<?php
}

function nxs_pagetemplate_blogentry_home_getsheethtml($args)
{
	//
	extract($args);
	
	$pagemeta = nxs_get_corepostmeta($postid);
	$iscurrentpagethehomepage = nxs_ishomepage($postid);
	$iscurrentpagethe404page = nxs_is404page($postid);
	$selectedcategories = get_the_category($postid);
	$pagemeta = nxs_get_corepostmeta($postid);
	$titel = nxs_gettitle_for_postid($postid);
	$slug = nxs_getslug_for_postid($postid);
	$poststatus = get_post_status($postid);
	
	$selectedcategoryids = "";
	// we explicitly set the pagetemplate; if the page has a different pagetemplate,
	// and the user changes the pagetemplate it would still invoke the 'updatedata' of
	// the old pagetemplate and the pagetemplate itself would not be changed :)
	$pagetemplate = "blogentry";

	foreach ($selectedcategories as $selectedcategory) 
	{
		$additional = "[" . $selectedcategory->term_id . "]";
		$selectedcategoryids .= $additional;
	}
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	$result = array();
	
	$catargs = array();
	$catargs['hide_empty'] = 0;
	$categories = get_categories($catargs);
	
	$categoriesfilters = array();
  $categoriesfilters["uncategorized"] = "skip";
  
	if ($datepublished == "")
	{
		$datepublished = get_the_date('d-m-Y', $postid);
	}

  nxs_getfilteredcategories($categories, $categoriesfilters);	

	$currentlayout = get_post_meta($postid, 'nxs_semanticlayout', true);
	$editurl = get_edit_post_link($postid);
		
	nxs_ob_start();

	?>

	<div class="nxs-admin-wrap">
		<div class="block">	
      
     	<?php nxs_render_popup_header(nxs_l18n__("Page (blogpost)[nxs:popup,header]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">

					<!-- address --> 
		      <div class="content2">
		        <div class="box">
		          <div class="box-title">
								<h4><?php nxs_l18n_e("Internet address (url)[nxs:heading]", "nxs_td"); ?></h4>		                
		           </div>
		          <div class="box-content">
		          	<?php
		          	$actualurl = nxs_geturl_for_postid($postid);
		          	$urlwithoutslug = nxs_str_lastreplace($slug . "/", "", $actualurl);
		          	if (!nxs_stringendswith($urlwithoutslug, "/"))
		          	{
		          		$urlwithoutslug .= "/";
		          	}
		          	$containsslug = nxs_stringcontains($actualurl, $slug);
		          	?>
		          	<span class="nxs-float-left title"><?php echo $urlwithoutslug; echo $trailer?></span>
		          	<?php if ($containsslug) { ?>
		          	<input id="slug" type='text' class="nxs-width60" name="slug" value='<?php echo nxs_render_html_escape_singlequote($slug); ?>' />
		          	<?php } else { ?>
		          	<input id="slug" type='hidden' name="slug" value='<?php echo nxs_render_html_escape_singlequote($slug); ?>' />
		          	<?php } ?>
		        	</div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->

					<!-- title -->
					<div class="content2">
		        <div class="box">
		          <div class="box-title">
								<h4><?php nxs_l18n_e("Title[nxs:heading]", "nxs_td"); ?></h4>		                
		           </div>
		          <div class="box-content">
		          	<input id="titel" type='text' name="titel" value='<?php echo nxs_render_html_escape_singlequote($titel); ?>' />
		        	</div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
		      <!-- categories --> 
		 			<div class="content2">
		        <div class="box">
		          <div class="box-title">
								<h4><?php nxs_l18n_e("Categories[nxs:heading]", "nxs_td"); ?></h4>		                
		          </div>
		          <div class="box-content">
								<ul class="cat-checklist" id='selectedcategoryids'>
						      <?php 
						    	foreach ($categories as $category)
						    	{
						    		$termid = $category->term_id;
						    		$name = $category->name;
		
						    		$key = "[" . $termid . "]";
						    		if (nxs_stringcontains($selectedcategoryids, $key))
						    		{
						    			$possiblyselected = "checked='checked'";
						    		}
						    		else
						    		{
						    			$possiblyselected = "";
						    		}
						    		
						    		?>
										<li>
											<label>
			            			<input class='selectable_category' id="catid_<?php echo $termid; ?>" type="checkbox" <?php echo $possiblyselected; ?> onchange="nxs_js_popup_sessiondata_make_dirty();" />
			            			<?php echo $name; ?>
			            		</label>
			            	</li>
							    	<?php
							    }
							    ?>	   
							  </ul>
							  <a class="nxsbutton1 nxs-float-left" href="<?php echo admin_url('edit-tags.php?taxonomy=category'); ?>" style='margin-top:10px;'><?php nxs_l18n_e("Edit categories[nxs:popup,button]", "nxs_td"); ?></a>
		          </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->   		      

					<!-- layout -->
					<div class="content2">
		        <div class="box">
		          <div class="box-title">
	          		<h4><?php nxs_l18n_e("Layout", "nxs_td"); ?></h4>
		           </div>
		          <div class="box-content">
		          	<span><?php echo $currentlayout; ?></span>
					      <a class='nxsbutton1 nxs-float-right' href='<?php echo $editurl; ?>'>Change</a>
					    </div>
					  </div>
					  <div class="nxs-clear"></div>
					</div>

					<!-- styling -->
					<div class="content2">
		        <div class="box">
		          <div class="box-title">
	          		<h4><?php nxs_l18n_e("Styling[nxs:heading]", "nxs_td"); ?></h4>
		           </div>
		          <div class="box-content">
					      <a class='nxsbutton1 nxs-float-right' title="<?php nxs_l18n_e("Change", "nxs_td"); ?>" href='#' onclick="nxs_js_popup_navigateto('styling'); return false;"><?php nxs_l18n_e("Change[nxs:popup,header]", "nxs_td"); ?></a>
					    </div>
					  </div>
					  <div class="nxs-clear"></div>
					</div>
									
					<!-- type -->
		      <div class="content2">
		        <div class="box">
		          <div class="box-title">
									<h4><?php nxs_l18n_e("Type", "nxs_td"); ?></h4>		                
		           </div>
		          <div class="box-content">
					      <a class='nxsbutton1 nxs-float-right' title="<?php nxs_l18n_e("Change[nxs:tooltip]", "nxs-ext-pagetemplate-searchresults"); ?>" href='#' onclick="nxs_js_popup_page_neweditsession('<?php echo $postid;?>', 'home'); return false;">
					      	<?php nxs_l18n_e("Change type", "nxs_td"); ?>
					      </a>
					      <?php  nxs_l18n_e("Page (blogpost)[nxs:popup,header]", "nxs_td"); ?>
					    </div>
					  </div>
					  <div class="nxs-clear"></div>
					</div>
					
					
		
		      <div class="content2">
		        <div class="box">
		
		          <div class="box-title">
								<h4><?php nxs_l18n_e("Set as homepage[nxs:heading]", "nxs_td"); ?></h4>
		           </div>
		
				      <?php if ($iscurrentpagethehomepage) { ?>
		
		            <div class="box-content">
		            	<span class="nxs-title"><?php nxs_l18n_e("Already set[nxs:label]", "nxs_td"); ?></span>
		            	<input id="markashomepage" name="markashomepage" type="checkbox" <?php echo $markashomepage; ?> onchange="nxs_js_popup_sessiondata_make_dirty();" style="display: none;" />
		            </div>
		            
							<?php } else { ?>
		
		            <div class="box-content">
									<input id="markashomepage" name="markashomepage" type="checkbox" <?php echo $markashomepage; ?> onchange="nxs_js_popup_sessiondata_make_dirty();" />
		            </div>
		
							<?php } ?>
		            
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
			nxs_js_popup_storestatecontroldata_textbox("titel", "titel");
			//nxs_js_popup_storestatecontroldata_dropdown("poststatus", "poststatus");
			nxs_js_popup_storestatecontroldata_textbox("slug", "slug");
			nxs_js_popup_storestatecontroldata_textbox("cssclass", "cssclass");
			//nxs_js_popup_storestatecontroldata_textbox("datepublished", "datepublished");
			
			nxs_js_popup_storestatecontroldata_checkbox('markashomepage', 'markashomepage');
			nxs_js_popup_storestatecontroldata_checkbox('markas404page', 'markas404page');
			
			nxs_js_popup_storestatecontroldata_listofcheckbox('selectedcategoryids', 'selectable_category', 'selectedcategoryids');
		}
		
		function nxs_js_execute_after_popup_shows()
		{
			jQ_nxs('#titel').focus();
			//
			
			<?php 
			$persistedpagetemplate = nxs_getpagetemplateforpostid($postid);
			if ($persistedpagetemplate != $pagetemplate) 
			{ 
				?>
				// we start by making this popup session dirty,
				// because it appears the pagetemplate we see here,
				// is not equal to the one persisted (which can
				// only be the case if the user is modifying the
				// pagetemplate
				nxs_js_popup_sessiondata_make_dirty();
				<?php 
			} 
			?>
		}
	
		function nxs_js_startcategorieseditor()
		{
			nxs_js_savepopupdata(); 
			nxs_js_popup_setsessiondata("nxs_categorieseditor_invoker", nxs_js_popup_getcurrentsheet()); 
			nxs_js_popup_setsessiondata("nxs_categorieseditor_appendnewitemsto", "<?php echo $id;?>"); 
			
			nxs_js_popup_navigateto("categorieseditor");
		}
		
		function nxs_js_savegenericpopup()
		{
			//
			nxs_js_savepopupdata();			
			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updatepagetemplatedata",
						"postid": "<?php echo $postid;?>",
						"pagetemplate": "<?php echo $pagetemplate;?>",
						"updatesectionid": "home",
						"titel": nxs_js_popup_getsessiondata("titel"),
						"slug": nxs_js_popup_getsessiondata("slug"),
						"cssclass": nxs_js_popup_getsessiondata("cssclass"),
						//"poststatus": nxs_js_popup_getsessiondata("poststatus"),
						"markashomepage": nxs_js_popup_getsessiondata("markashomepage"),
						"markas404page": nxs_js_popup_getsessiondata("markas404page"),
						"selectedcategoryids": nxs_js_popup_getsessiondata("selectedcategoryids"),
						//"datepublishedddmmyyyy": nxs_js_popup_getsessiondata("datepublished")
					},
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// close the pop up
							nxs_js_closepopup_unconditionally();
							
							// refresh current page (if the footer is updated we could decide to
							// update only the footer, but this is needless; an update of the page is ok too)
							nxs_js_redirecttopostid(<?php echo $postid;?>);
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
	
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_pagetemplate_blogentry_edittitle_getsheethtml($args)
{
	//
	extract($args);

	$pagedata = get_post($postid);
	$titel = $pagedata->post_title;

	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
		
	$result = array();
		
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	
      
     	<?php nxs_render_popup_header(nxs_l18n__("Change title[nxs:popup,header]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		
					<div class="content2">
		        <div class="box">
		            <div class="box-title">
									<h4><?php nxs_l18n_e("Title[nxs:heading]", "nxs_td"); ?></h4>		                
		             </div>
		            <div class="box-content">
		            	<input id="titel" name="titel" type='text' value='<?php echo nxs_render_html_escape_singlequote($titel); ?>' />
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		            
		    </div>
		  </div>
		            
      <div class="content2">
          <div class="box">
            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='save(); return false;'><?php nxs_l18n_e("Save[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:button]", "nxs_td"); ?></a>
         	</div>
          <div class="nxs-clear">
          </div>
      </div> <!--END content-->
		</div>
	</div>
	
	<script type='text/javascript'>
		
		function nxs_js_setpopupdatefromcontrols()
		{
			nxs_js_popup_storestatecontroldata_textbox("titel", "titel");
		}
		
		function save()
		{
			nxs_js_setpopupdatefromcontrols();
			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updatepagetemplatedata",
						"postid": "<?php echo $postid;?>",
						"pagetemplate": "blogentry",
						"updatesectionid": "edittitle",
						"titel": nxs_js_popup_getsessiondata("titel")
					},
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// close the pop up
							nxs_js_closepopup_unconditionally();
							
							// refresh current page (if the footer is updated we could decide to
							// update only the footer, but this is needless; an update of the page is ok too)
							nxs_js_redirecttopostid(<?php echo $postid;?>);
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
			jQ_nxs('#titel').focus();
		}
		
	</script>
		
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_pagetemplate_blogentry_dialogappendrow_getsheethtml($args)
{
	//
	extract($args);
			
	$result = array();
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	nxs_ob_start();

	$pagedata = get_page($postid);
	$nxsposttype = nxs_getnxsposttype_by_wpposttype($pagedata->post_type);
	
	$posttype = $pagedata->post_type;
	$postmeta = nxs_get_corepostmeta($postid);
	$pagetemplate = nxs_getpagetemplateforpostid($postid);	
	
	$prtargs = array();
	$prtargs["invoker"] = "nxsmenu";
	$prtargs["wpposttype"] = $posttype;
	$prtargs["nxsposttype"] = nxs_getnxsposttype_by_wpposttype($posttype);
	$prtargs["pagetemplate"] = $pagetemplate;		
	$templates = nxs_getpostrowtemplates($prtargs);

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	

     	<?php nxs_render_popup_header(nxs_l18n__("Add row[nxs:popup,header]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		
					<div class="content2">
		        <div class="box">
		          <div class="box-title" style='width: 400px;'>
								<h4><?php nxs_l18n_e("Select a column layout for the new row[nxs:heading]", "nxs_td"); ?></h4>		                
		          </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		
		      <div class="content2">
		        <div class="box">
		        						
		        	
		        	
		          <ul class="nxs-fraction drag nxs-admin-wrap">
								<?php
									// for each placeholder -->
									foreach ($templates as $currentpostrowtemplate)
									{
										?>
										<a href="#" onclick="select(this, '<?php echo $currentpostrowtemplate; ?>'); return false;">
											<li>
												<?php
												require_once(NXS_FRAMEWORKPATH . '/nexuscore/pagerows/templates/' . $currentpostrowtemplate . '/' . $currentpostrowtemplate . '_render.php');
												$functionnametoinvoke = 'nxs_pagerowtemplate_render_' . $currentpostrowtemplate . "_toolbox";
												$args = array();
												$args["postid"] = $postid;
												$args["pagerowtemplate"] = $currentpostrowtemplate;
												if (function_exists($functionnametoinvoke))
												{
													call_user_func($functionnametoinvoke, $args);
												}
												else
												{
													echo "function not found;" . $functionnametoinvoke;
												}
												?>
											</li>
										</a>									
										<?php
									}
								?>
		        	</ul>
		        </div>
		        <div class="nxs-clear"></div>
		      </div>
		    
			  </div>
			</div>
      
      <div class="content2">
         <div class="box">
            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Add row[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:button]", "nxs_td"); ?></a>                    
         </div>
         <div class="nxs-clear"></div>
      </div> <!--END content-->
    	
    </div>
  </div>
	
	<script type='text/javascript'>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}
		
		function select(obj, pagerowtemplate)
		{
			var waitgrowltoken = nxs_js_alert_wait_start("<?php nxs_l18n_e("Adding row[nxs:growl]", "nxs_td"); ?>");
			var e = jQ_nxs(".nxs-layout-editable.nxs-post-<?php echo $postid;?> .nxs-postrows")[0];
			var totalrows = jQ_nxs(e).find(".nxs-row").length;
			var insertafterindex;
			insertafterindex = totalrows - 1;
			
			nxs_js_addnewrowwithtemplate('<?php echo $postid; ?>', insertafterindex, pagerowtemplate, "undefined", e, 
			function()
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				nxs_js_closepopup_unconditionally();
			},
			function()
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				nxs_js_closepopup_unconditionally();
			});
		}
	</script>
	
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

// import data
function nxs_pagetemplate_blogentry_appendstruct_getsheethtml($args)
{
	//
	//
	//
	extract($args);
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	$fileuploadurl = admin_url( 'admin-ajax.php');
	
	nxs_ob_start();

	?>

  <div class="nxs-admin-wrap">
    <div class="block">
     
     	<?php nxs_render_popup_header(nxs_l18n__("Append rows[nxs:popup,header]", "nxs_td")); ?>
      
      <div class="content2">
      
          <form id='nxsuploadform' action="<?php echo $fileuploadurl;?>" method="post" enctype="multipart/form-data">
              <input type="file" name="file" id="file" class="nxs-float-left" onchange="storefile();" />
          </form>		
          <script type="text/javascript">
        	
        		function setupfiletransfer()
        		{
        			var filename = jQ_nxs('#file').val().split(/\\|\//).pop();
        			var options = 
              { 
                data:
                {
                    action: "nxs_ajax_webmethods",
                    webmethod: "importcontent",
                    uploadtitel: filename,
                    import: 'appendpoststructureandwidgets',
                    postid: <?php echo $postid; ?>
                },
                dataType: 'json',
                iframe: true,
                success: processResponse,
            	};
                
        			jQ_nxs('#nxsuploadform').ajaxForm(options);
        		}
        	
            function storefile()
            {           
              // 
              // setup form to support ajax submission (file transfer using html5 features)
              //
              setupfiletransfer();
              

							if (!verifyFileSelected())
              {
                  return;
              }
              
              // submit form
              jQ_nxs("#nxsuploadform").submit(); 
          	}
            
            function verifyFileSelected()
            {
                var f = document.getElementById("file");
                if (f.value == "")
                {
                    alert("Je hebt nog geen digitaal bestand gekozen");
                    return false;
                }
                else
                {
                    return true;
                }
            }

            function processResponse(data, statusText, xhr, $form)  
            {
              if (data.result == "OK")
              {
              	nxs_js_alert('<?php nxs_l18n_e("Data was updated. Please refresh the page to see the result[nxs:growl]", "nxs_td"); ?>');
              	nxs_js_log(data.message);
              }
              else
              {
                alert("Er is een fout opgetreden bij het uploaden van het document");
              }
            }
            
          </script>
      </div> <!--END content-->
      <div class="content2">
          <div class="box">
            <a href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_popup_navigateto("home"); return false;'><?php nxs_l18n_e("Cancel[nxs:button]", "nxs_td"); ?></a>
         </div>
          <div class="nxs-clear margin"></div>
      </div> <!--END content-->
    </div> <!--END block-->
  </div>
    
  <script type='text/javascript'>
		function nxs_js_execute_after_popup_shows()
		{
			
		}
	</script>
    
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

// export data
function nxs_pagetemplate_blogentry_exportstruct_getsheethtml($args)
{
	//
	//
	//
	extract($args);
	
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	$filedownloadurl = admin_url('admin-ajax.php?action=nxs_ajax_webmethods&webmethod=exportcontent&export=poststructureandwidgets&postid=' . $postid);
	
	nxs_ob_start();

	?>

  <div class="nxs-admin-wrap">
    <div class="block">
     
     	<?php nxs_render_popup_header(nxs_l18n__("Export page[nxs:popup,header]", "nxs_td")); ?>
      
      <div class="content2">
      	<a href='<?php echo $filedownloadurl;?>'>Download</a>
      </div> <!--END content-->
      <div class="content2">
          <div class="box">
            <a href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_popup_navigateto("home"); return false;'><?php nxs_l18n_e("Cancel[nxs:button]", "nxs_td"); ?></a>
         </div>
          <div class="nxs-clear margin"></div>
      </div> <!--END content-->
    </div> <!--END block-->
  </div>
    
  <script type='text/javascript'>
		function nxs_js_execute_after_popup_shows()
		{
			
		}
	</script>
    
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

//
// wordt aangeroepen bij het opslaan van data
//
function nxs_pagetemplate_blogentry_updatedata($args)
{
	extract($args);
	
	nxs_disabledwprevisions();
	
	$wpposttype = nxs_getwpposttype($postid);
	if ($wpposttype == "page")
	{
		$iscurrentpagethehomepage = nxs_ishomepage($postid);
		if ($iscurrentpagethehomepage)
		{
			// leave as-is
		}
		else
		{
			//echo "converting to post";
			$r = nxs_converttopost($postid);
			//$wpposttype = nxs_getwpposttype($postid);
		}
	}
	
	if ($updatesectionid == "home" || $updatesectionid == "")
	{	
		$modifiedmetadata = array();
		
		// update 404
		if ($markas404page != "")
		{
			nxs_set404page($postid);
		}

		//
		// update title, slug and categories
		//

		$modifiedmetadata["titel"] = $titel;
		$modifiedmetadata["slug"] = $slug;
		$modifiedmetadata["selectedcategoryids"] = $selectedcategoryids;

		$newcats = array();
		$splitted = explode("[", $selectedcategoryids);
		foreach($splitted as $splittedpiece)
		{
			// bijv. "1]"

			if ($splittedpiece == "")
			{
				// ignore
			}
			else
			{
				// bijv. "1]"
				$newcats[] = substr($splittedpiece, 0, -1);
			}			
		}

		$datepublishedddmmyyyytime = strtotime($datepublishedddmmyyyy);
		$datepublishedddmmyyyyformatted = date('Y-m-d H:i:s', $datepublishedddmmyyyytime);   

		// Update the post into the database
		$my_post = array();
		$my_post['ID'] = $postid;
		$my_post['post_title'] = $titel;
		$my_post['post_name'] = $slug;
		//$my_post['post_status'] = $poststatus;
		
		/*
		if ($datepublishedddmmyyyy != "")
		{
			$my_post['edit_date'] = true; // see http://kovshenin.com/2009/wordpress-the-wp_update_post-dates-in-drafts/
			$my_post['post_date'] = $datepublishedddmmyyyyformatted;
		}
		*/
		wp_update_post($my_post);
		
		// Update categories
		wp_set_post_categories($postid, $newcats);			
	
		// update homepage, LET OP, moet het laatste zijn dat we doen...	
		if ($markashomepage != "")
		{
			nxs_sethomepage($postid);
		}
	}
	
	if ($updatesectionid == "edittitle" || $updatesectionid == "")
	{
		$modifiedmetadata = array();

		$modifiedmetadata["titel"] = $titel;
		
		// update title, slug and categories
		$my_post = array();
		$my_post['ID'] = $postid;
		$my_post['post_title'] = $titel;

		wp_update_post($my_post);
		
		// persist values
		nxs_merge_postmeta($postid, $modifiedmetadata);
	}
		
	if ($updatesectionid == "header" || $updatesectionid == "")
	{
		$modifiedmetadata = array();
		
		$modifiedmetadata["header_postid"] = $header_postid;
		$modifiedmetadata['header_postid_globalid'] = nxs_get_globalid($header_postid, true);	// global referentie

		// persist values
		nxs_merge_postmeta($postid, $modifiedmetadata);
	}
	
	if ($updatesectionid == "pagedecorator" || $updatesectionid == "")
	{
		$modifiedmetadata = array();
		
		$modifiedmetadata["pagedecorator_postid"] = $pagedecorator_postid;
		$modifiedmetadata['pagedecorator_postid_globalid'] = nxs_get_globalid($pagedecorator_postid, true);	// global referentie

		// persist values
		nxs_merge_postmeta($postid, $modifiedmetadata);
	}
	
	if ($updatesectionid == "sidebar" || $updatesectionid == "")
	{
		$modifiedmetadata = array();

		$modifiedmetadata["sidebar_postid"] = $sidebar_postid;
		$modifiedmetadata['sidebar_postid_globalid'] = nxs_get_globalid($sidebar_postid, true);	// global referentie

		// persist values
		nxs_merge_postmeta($postid, $modifiedmetadata);
	}	
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}
