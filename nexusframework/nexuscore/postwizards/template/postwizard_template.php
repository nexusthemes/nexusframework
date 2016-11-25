<?php

function nxs_postwizard_template_gettitle($args)
{
	return __("Template", "nxs_td");
}

function nxs_postwizard_template_renderpreview($args)
{
	?>
	<script>
		nxs_js_popup_site_neweditsession('newtemplate'); 
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