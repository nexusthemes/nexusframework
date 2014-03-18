<?php

	// we explicitly choose to use this approach in favor of the traditional 'WP' way,
	// since plugins often behave different when DOING_AJAX is set, for example
	// the NextGen plugin loads less files. Impact is that these plugins won't render
	// correctly when DOING_AJAX is set.
	// this AJAX implementation does not set DOING_AJAX, solving our problems
	
	// credits to NextGen Gallery ngg-config.php
	
	/** Define the server path to the file wp-config here, if you placed WP-CONTENT outside the classic file structure */
	$path  = ''; // It should be end with a trailing slash    
	/** That's all, stop editing from here **/
	
	nxs_ensureloadpathisset();
	
	// let's load WordPress
	require_once(WP_LOAD_PATH . 'wp-load.php');
	require_once(WP_LOAD_PATH . 'wp-admin/includes/admin.php');

	//send_nosniff_header();
	
	//do_action('admin_init');
	
	nxs_ajax_webmethods();
	die();
?>