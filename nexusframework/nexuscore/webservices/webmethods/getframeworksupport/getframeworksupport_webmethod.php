<?php

	function nxs_webmethod_getframeworksupport() 
	{
		extract($_REQUEST);
		
		header('HTTP/1.0 200 OK');
					
		$serviceparams = array
		(
			'timeout' => 15,
			'sslverify' => false,
			'body' => array
			(
				"q" => $q,
				"action" => "nxs_ajax_webmethods",
				"webmethod" => "getsupportfeedback",
				"type" => "framework",
			)
		);
		
		$url = "https://support1805.nexusthemes.com/";
		$response = wp_remote_post($url, $serviceparams);
		$body = wp_remote_retrieve_body($response);
	
		echo $body;
		die();
	}
	
	function nxs_dataprotection_nexusframework_webmethod_getframeworksupport_getprotecteddata($args)
{
	$result = array
	(
		"subactivities" => array
		(
			// intentionally left blank
		),
		"dataprocessingdeclarations" => array	
		(
			array
			(
				"use_case" => "(belongs_to_whom_id) can request for support if there's a valid support license",
				"what" => "the support question, as well as the license, and the server's IP address",
				"belongs_to_whom_id" => "website_owner", // (has to give consent for using the "what")
				"controller" => "website_owner",	// who is responsible for this?
				"controller_options" => nxs_dataprotection_factory_getenableoptions("license"),
				"data_processor" => "nexusthemes",	// the name of the data_processor or data_recipient
				"data_retention" => "See terms and conditions of the license",
				"program_lifecycle_phase" => "compiletime",
				"why" => "Without the question we cannot provide an answer, the license is needed so we can verify the license is active, the IP is required since otherwise there would not be a connection to our server",
				"security" => "The data is transferred over a secure https connection.",
			),
		),
		"status" => "final",
	);
	return $result;
}

?>