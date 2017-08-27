<?php

$filetoinclude = NXS_FRAMEWORKPATH . '/nexuscore/frontendframeworks/nxs/frontendframework_nxs.php';
require_once($filetoinclude);

function nxs_frontendframework_alt_gethtmlforbutton($args)
{
	return nxs_frontendframework_nxs_gethtmlforbutton($args);
}

function nxs_frontendframework_alt_gethtmlfortitle($args)
{
	return nxs_frontendframework_nxs_gethtmlfortitle($args);
}

function nxs_frontendframework_alt_gethtmlforimage($args)
{
	return nxs_frontendframework_nxs_gethtmlforimage($args);
}

function nxs_frontendframework_alt_setgenericwidgethovermenu($args)
{
	// do nothing
}

function nxs_frontendframework_alt_gethtmlfortext($args)
{
	return nxs_frontendframework_nxs_gethtmlfortext($args);
}

function nxs_frontendframework_alt_init()
{
	// add_action('nxs_render_frontendeditor', 'nxs_frontendframework_nxs_render_frontendeditor');

	add_action('wp_enqueue_scripts', 'nxs_frontendframework_nxs_theme_styles');
	add_action('admin_enqueue_scripts', 'nxs_frontendframework_nxs_theme_styles');
	add_shortcode('nxspagerow', 'nxs_frontendframework_nxs_sc_nxspagerow');
	add_shortcode('nxsphcontainer', 'nxs_frontendframework_nxs_sc_nxsphcontainer');
	add_shortcode('nxsplaceholder', 'nxs_frontendframework_nxs_sc_nxsplaceholder');
	add_shortcode('nxs_wrap', 'nxs_frontendframework_nxs_sc_wrap');
	add_shortcode('nxs_image', 'nxs_frontendframework_nxs_sc_image');

	nxs_frontendframework_nxs_clearunwantedscripts();
}