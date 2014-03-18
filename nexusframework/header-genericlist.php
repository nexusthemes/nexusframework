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
	// ------------------------------------------------
	// ------------------------------------------------ COLORS / STYLE / CSS
	// ------------------------------------------------
	//
	// mutaties hierin ook doorvoeren in nxsmenu.php en header-post.php
	?>
	
	<style type="text/css" id="dynamicCssCurrentConfiguration">
		<?php
		$css = "";	

		// lettertypen
		$hetfont = str_replace("\'", "'", $sitemeta["vg_fontfam_1"]);		
		$css .= "body { font-family: " . $hetfont . "; }";
		$hetfont = str_replace("\'", "'", $sitemeta["vg_fontfam_2"]);		
		$css .= "h1, .nxs-size1, h2, .nxs-size2, h3, .nxs-size3, h4, .nxs-size4, h5, .nxs-size5, h6, .nxs-size6, .nxs-logo { font-family: " . $hetfont . "; }";

		// output		
		echo $css;
		?>
	</style>
	<style type="text/css" id="dynamicCssVormgevingKleuren"></style>
	<style type="text/css" id="dynamicCssVormgevingLettertypen"></style>
	<?php nxs_render_dynamicservercss($sitemeta); ?>
	<?php nxs_render_manualcss($sitemeta); ?>
	
	<?php
	// ------------------------------------------------
	// ------------------------------------------------
	// ------------------------------------------------
	?>
	
</head>
<body <?php body_class("nxs-admin-wrap"); ?>>
	<?php include(NXS_FRAMEWORKPATH . '/nexuscore/includes/nxsmenu.php'); ?>
	<?php require_once(NXS_FRAMEWORKPATH . '/nexuscore/includes/frontendediting.php'); ?>
	
	<div id="admin-container" class="nxs-containsimmediatehovermenu nxs-no-click-propagation center">