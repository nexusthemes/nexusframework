<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php nxs_render_htmlstarttag(); ?>
<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo nxs_getcharset(); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
	<link rel="shortcut icon" href="<?php echo nxs_getframeworkurl(); ?>/favicon.ico" type="image/x-icon" />
	<?php nxs_render_htmlcorescripts(); ?>
	<?php		
	nxs_hideadminbar();
	wp_enqueue_style('nxsbox');
	wp_head(); 
	?>
	<?php
	//
	nxs_render_headstyles();
	?>
	
	<?php if (is_user_logged_in()) { ?>
	<input type="hidden" id="nxs-refreshed-indicator" value="no" />
	<script>
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
<body <?php body_class("nxs-admin-wrap nxs-front-end"); ?>>
	<?php do_action("nxs_render_frontendeditor");?>
		
<div id="admin-container" class="nxs-containsimmediatehovermenu nxs-no-click-propagation center">