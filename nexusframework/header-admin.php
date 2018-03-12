<?php
	global $post;
	$postid = $post->ID;
	$pagemeta = nxs_get_corepostmeta($postid);
	$sitemeta = nxs_getsitemeta();
	$pagetemplate = nxs_getpagetemplateforpostid($postid);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php nxs_render_htmlstarttag(); ?>
<head>
	<link rel="profile" href="http://gmpg.org/xfn/11"/>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo nxs_getcharset(); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<link rel="shortcut icon" href="<?php echo nxs_getframeworkurl(); ?>/favicon.ico" type="image/x-icon" />
	<script>	document.documentElement.className = 'js'; </script>
	<?php nxs_render_htmlcorescripts(); ?>
	<?php		
	nxs_hideadminbar();
	wp_enqueue_style('nxsbox');
	wp_head(); 
	?>
</head>
<body <?php body_class("nxs-admin-wrap"); ?>>
	<?php do_action("nxs_render_frontendeditor");?>

	<div id="admin-container" class="nxs-containsimmediatehovermenu nxs-no-click-propagation">