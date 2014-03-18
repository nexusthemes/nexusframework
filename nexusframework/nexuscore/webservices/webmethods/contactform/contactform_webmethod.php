<?php
function nxs_webmethod_contactform() 
{
	extract($_REQUEST);
 	
 	if ($postid == "")
 	{
 		nxs_webmethod_return_nack("postid niet gevonden;");
 	}
 	if ($containerpostid == "")
 	{
 		nxs_webmethod_return_nack("containerpostid niet gevonden;");
 	}
 	if ($placeholderid == "")
 	{
 		nxs_webmethod_return_nack("placeholderid niet gevonden;");
 	}
 	if ($naam == "")
 	{
 		nxs_webmethod_return_nack("naam niet gevonden;");
 	}
 	if ($placeholderid == "")
 	{
 		nxs_webmethod_return_nack("placeholderid niet gevonden;");
 	}
 	if ($email == "")
 	{
 		nxs_webmethod_return_nack("email niet gevonden;");
 	}
 	/*
 	if ($tel == "")
 	{
 		nxs_webmethod_return_nack("tel niet gevonden;");
 	}
 	*/
 	if ($msg == "")
 	{
 		nxs_webmethod_return_nack("msg niet gevonden;");
 	}
 	if ($isakkoord == "")
 	{
 		nxs_webmethod_return_nack("isakkoord niet gevonden;");
 	}
 	
 	// haal properties op van de placeholder
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
 	
 	$url = nxs_geturl_for_postid($containerpostid)	;
 	$internalemail = $temp_array['internal_email'];
 	$texttop = $temp_array['texttop'];
 	$pageidaftersqueeze = $temp_array['destination_articleid'];

	if ($internalemail != "")
	{
		$headers = 'From: ' . $naam . ' <' . $email . '>' . "\r\n";
		$body = "";
		$body .= nxs_l18n__("Posted from url:", "nxs_td") . $url . " \r\n";
		$body .= nxs_l18n__("Name:", "nxs_td") . $naam . "\r\n";
		$body .= nxs_l18n__("Email:", "nxs_td") . $email . "\r\n";
		$body .= nxs_l18n__("Phone:", "nxs_td") . $tel . "\r\n";
		$body .= nxs_l18n__("Company:", "nxs_td") . $company . "\r\n";
		$body .= nxs_l18n__("Message:", "nxs_td") . $msg . "\r\n";
		$body .= nxs_l18n__("Conditions/terms:", "nxs_td") . $isakkoord . "\r\n";
		
		$subject = nxs_l18n__("Message from webform on your site:", "nxs_td");
		if ($texttop != "")
		{
			$subject .= "(" . $texttop . ")";
		}
		
		$mailresult = wp_mail($internalemail, $subject, $body, $headers);
		if (!$mailresult)
		{
			// nack!
			nxs_webmethod_return_nack("mail was not sent (contactform)");	
		}
 	}
 	else
 	{
 		echo "intern email adres niet gezet";
 		die();
 	}
 	
 	//
 	//
 	//
 	
 	$responseargs = array();
 	$responseargs["url"] = get_permalink($pageidaftersqueeze);
	nxs_webmethod_return_ok($responseargs);
}
?>