<?php
	global $post;
	$postid = $post->ID;
	$pagemeta = nxs_get_corepostmeta($postid);
	$sitemeta = nxs_getsitemeta();
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
	
	$faviconid = $sitemeta["faviconid"];
	$faviconlookup = nxs_wp_get_attachment_image_src($faviconid, 'full', true);
	$faviconurl = $faviconlookup[0];
	$faviconurl = nxs_img_getimageurlthemeversion($faviconurl);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php nxs_render_htmlstarttag(); ?>
<head>
	<link rel="profile" href="http://gmpg.org/xfn/11"/>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo nxs_getcharset(); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<link rel="shortcut icon" href="<?php echo nxs_getframeworkurl(); ?>/favicon.ico" type="image/x-icon" />
	<?php nxs_render_htmlcorescripts(); ?>
	<?php		
	nxs_hideadminbar();
	wp_enqueue_style('nxsbox');
	wp_head(); 
	?>
	<link rel="shortcut icon" href="<?php echo $faviconurl; ?>" type="image/x-icon" />
	<!-- -->
	<script>	document.documentElement.className = 'js'; </script>
	<?php 
	
	//
	nxs_render_headstyles();
	
	if (nxs_has_adminpermissions() && $_REQUEST["customhtml"] == "off")
	{
		// suppress
	}
	else
	{
		echo $sitemeta["vg_injecthead"];
	}

	?>
	
	<?php if (nxs_cap_hasdesigncapabilities()) { ?>
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
			
</head>

<body <?php body_class(); ?>>
	<?php do_action("nxs_render_frontendeditor");?>

	<div>