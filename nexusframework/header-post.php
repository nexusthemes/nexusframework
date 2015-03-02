<?php		
	global $post;
	$postid = $post->ID;
	
	$pagemeta = nxs_get_postmeta($postid);
	$page_cssclass = $pagemeta["page_cssclass"];

	$sitemeta	= nxs_getsitemeta();
	$pagetemplate = nxs_getpagetemplateforpostid($postid);

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
	$analyticsUA = nxs_seo_getanalyticsua();
	
	$meta = nxs_theme_getmeta();
	$version = $meta["version"];
?>
<!DOCTYPE html>
<?php nxs_render_htmlstarttag(); ?>
<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo nxs_getcharset(); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
	<!-- Nexus Framework | http://nexusthemes.com -->	
	<meta name="generator" content="Nexus Themes | <?php echo nxs_getthemename(); ?> | <?php echo $version; ?>" />
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
	
	// if responsiveness is turned on
	if ($sitemeta["responsivedesign"] == "true")
	{
	?>
	<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width" />
	<?php
	}
	else
	{
		echo "<!-- responsive design deactivated -->";
	}
	
	nxs_render_headstyles();

	if ($analyticsUA != "") { ?>
	<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?php echo $analyticsUA; ?>']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

	</script>
	<?php } ?>
	
	
	<?php if (is_user_logged_in()) { ?>
	
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

<body <?php body_class(); ?>>
	<?php if (nxs_showloadcover()) { ?>
		<div id="nxs-load-cover" style='position: fixed; height: 100%; width: 100%; top:0; left: 0; background: #000; z-index:9999;'></div>
	<?php } else { ?>
		<div id="nxs-load-cover" style=''></div>
	<?php } ?>
	
	<?php do_action('nxs_bodybegin'); ?>
	<?php include(NXS_FRAMEWORKPATH . '/nexuscore/includes/nxsmenu.php'); ?>
	<?php require_once(NXS_FRAMEWORKPATH . '/nexuscore/includes/frontendediting.php'); ?>
	
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
	
						echo nxs_getrenderedhtmlincontainer($postid, $existingheaderid, "default");
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
  do_action('nxs_ext_betweenheadandcontent'); ?>