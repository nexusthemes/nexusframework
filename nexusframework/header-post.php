<?php		
	global $post;
	$postid = $post->ID;
	
	$pagemeta = nxs_get_corepostmeta($postid);
	$page_cssclass = $pagemeta["page_cssclass"];

	$sitemeta	= nxs_getsitemeta();
	$pagetemplate = nxs_getpagetemplateforpostid($postid);

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
	$version = nxs_theme_getversion();
	
	$headmeta = nxs_getheadmeta();
?>
<!DOCTYPE html>
<?php nxs_render_htmlstarttag(); ?>
<head>
	<link rel="profile" href="http://gmpg.org/xfn/11"/>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo nxs_getcharset(); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<!-- Nexus Framework | https://nexusthemes.com -->
	<!-- Nexus Meta | v1 | <?php echo $headmeta; ?> x -->
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
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=12.0, minimum-scale=.25, user-scalable=yes" />
	<?php
	
	nxs_render_headstyles();
	nxs_analytics_handleanalytics();
	
	if (nxs_cap_hasdesigncapabilities()) { ?>
	
	<input type="hidden" id="nxs-refreshed-indicator" value="no" />
	<script>
		onload=function()
		{
			/* refresh the screen when the user pushes the back button */
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
	<?php do_action('nxs_bodybegin'); ?>
	<?php do_action("nxs_render_frontendeditor");?>
	
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