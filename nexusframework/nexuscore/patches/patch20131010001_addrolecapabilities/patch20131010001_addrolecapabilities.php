<?php

// inserts roles and capabilities to the WP instance. Normally this happens
// when the theme is activated. For themes already activated its possible
// to invoke this method too, using this patch.
function nxs_apply_patch20131010001_addrolecapabilities()
{
	nxs_setuprolesandcapabilities();
	echo "role and capabilities setup finished";
	die();
}

?>