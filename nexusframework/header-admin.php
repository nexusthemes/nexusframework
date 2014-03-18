<?php
	global $post;
	$postid = $post->ID;
	$pagemeta = nxs_get_postmeta($postid);
	$sitemeta = nxs_getsitemeta();
	$pagetemplate = nxs_getpagetemplateforpostid($postid);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php nxs_render_htmlstarttag(); ?>
<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
	<title><?php wp_title(''); ?></title>
	<link rel="shortcut icon" href="<?php echo nxs_getframeworkurl(); ?>/favicon.ico" type="image/x-icon" />
	<script type="text/javascript">	document.documentElement.className = 'js'; </script>
	<?php nxs_render_htmlcorescripts(); ?>
	<?php		
	nxs_hideadminbar();
	wp_enqueue_style('thickbox');
	wp_head(); 
	?>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
</head>
<body <?php body_class("nxs-admin-wrap"); ?>>
	<?php include(NXS_FRAMEWORKPATH . '/nexuscore/includes/nxsmenu.php'); ?>
	<?php require_once(NXS_FRAMEWORKPATH . '/nexuscore/includes/frontendediting.php'); ?>

	<div id="admin-container" class="nxs-containsimmediatehovermenu nxs-no-click-propagation">