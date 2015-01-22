<?php

	function nxs_webmethod_getframeworksupport() 
	{
		extract($_REQUEST);
		
		header('HTTP/1.0 200 OK');
		
		// no data, or expired data
		$licensekey = get_option('nxs_licensekey');
		$serviceparams = array
		(
			'timeout' => 15,
			'sslverify' => false,
			'body' => array
			(
				"q" => $q,
				"action" => "nxs_ajax_webmethods",
				"webmethod" => "getsupportfeedback",
				"licensekey" => $licensekey,
				"type" => "framework",
			)
		);
		
		$url = "http://support.nexusthemes.com/";
		$response = wp_remote_post($url, $serviceparams);
		$body = wp_remote_retrieve_body($response);
	
		echo $body;
		die();
	}
	
?>