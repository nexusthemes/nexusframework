<?php
	global $post;
	$postid = $post->ID;
	$pagemeta = nxs_get_postmeta($postid);
	$sitemeta = nxs_getsitemeta();
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
	
	$faviconid = $sitemeta["faviconid"];
	$faviconlookup = wp_get_attachment_image_src($faviconid, 'full', true);
	$faviconurl = $faviconlookup[0];
	
	$analyticsUA = $sitemeta["analyticsUA"];			
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php nxs_render_htmlstarttag(); ?>
<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
	<title><?php wp_title(''); ?></title>
	<link rel="shortcut icon" href="<?php echo nxs_getframeworkurl(); ?>/favicon.ico" type="image/x-icon" />
	<?php nxs_render_htmlcorescripts(); ?>
	<?php		
	nxs_hideadminbar();
	wp_enqueue_style('thickbox');
	wp_head(); 
	?>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
	<link rel="shortcut icon" href="<?php echo $faviconurl; ?>" type="image/x-icon" />
	<!-- -->
	<script type="text/javascript">	document.documentElement.className = 'js'; </script>
	<?php 
	
	//
	nxs_render_headstyles();
	echo $sitemeta["vg_injecthead"];
	?>
	
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
			
</head>

<body <?php body_class(); ?>>
	<?php include(NXS_FRAMEWORKPATH . '/nexuscore/includes/nxsmenu.php'); ?>
	<?php require_once(NXS_FRAMEWORKPATH . '/nexuscore/includes/frontendediting.php'); ?>

	<div>