<?php

function nxs_postwizard_landingpage_gettitle($args)
{
	return __("Landing page", "nxs_td");
}

function nxs_postwizard_landingpage_renderpreview($args)
{
	?>
	<script>
		nxs_js_popup_site_neweditsession('newlandingpage'); 
	</script>	
	<?php
}

function nxs_postwizard_postwizard_home_getsheethtml($args)
{
	echo "not implemented";
	die();
}

function nxs_postwizard_postwizard_setuppost($args)
{
	// 
	echo "not implemented";
	die();
}

