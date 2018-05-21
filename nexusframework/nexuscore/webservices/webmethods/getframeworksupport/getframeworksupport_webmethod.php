<?php

	function nxs_webmethod_getframeworksupport() 
	{
		extract($_REQUEST);
		
		header('HTTP/1.0 200 OK');
		
		$licensekey = nxs_license_getlicensekey();
		
		if ($licensekey == "")
		{
			// we are sure the license is not valid; don't attempt to query the server
			
			// 
			?>
			{
			  "hints": [
			    {
			      "title": "The support section requires a valid Nexus license. Your site is currently not connected to a valid Nexus license. <a target='_blank' href='https://www.wpsupporthelp.com/answer/how-to-register-your-wordpress-theme-purchase-to-get-updates-1091/'>Learn more</a>",
			      "type": "text",
			      "youtubeid": null,
			      "thumbimgurl": null,
			      "meta": ""
			    }
			  ],
			  "autolivechat": "show",
			  "result": "OK"
			}
			<?php
			die();
		}
		else
		{
			// the licensekey is set; it could be valid, but it could also not be valid
						
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
			
			$url = "https://support1805.nexusthemes.com/";
			$response = wp_remote_post($url, $serviceparams);
			$body = wp_remote_retrieve_body($response);
		
			echo $body;
			die();
		}
	}
?>