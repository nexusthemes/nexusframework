<?php
function nxs_webmethod_squeeze() 
{
	extract($_REQUEST);
 	
 	if ($postid == "")
 	{
 		nxs_webmethod_return_nack("postid niet gevonden;");
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
 	if ($isakkoord == "")
 	{
 		nxs_webmethod_return_nack("isakkoord niet gevonden;");
 	}
 	
 	// haal properties op van de placeholder
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
 	
 	$internalemail = $temp_array['internalemail'];
 	$texttop = $temp_array['texttop'];
 	$pageidaftersqueeze = $temp_array['pageidaftersqueeze'];
 	$mailsendername = $temp_array['mailsendername'];
 	$activitylog = $temp_array['activitylog'];
 	
 	if ($counter == "")
 	{
 		$counter = 0;
 	}
 	
 	if ($mailsendername == "")
 	{
 		$mailsendername = "Not Yet Configured";
 	}
 	
	$mailsenderaddress = $temp_array['mailsenderaddress'];
	if ($mailsenderaddress == "")
 	{
 		$mailsenderaddress = "notyetconfigured@example.org";
 	}

	if ($internalemail != "")
	{
		// update activity log
		$activitylog = $activitylog . "[" . $naam . "/" . $email . "]";
		$temp_array = array();
		$temp_array["activitylog"] = $activitylog;
		nxs_mergewidgetmetadata_internal($postid, $placeholderid, $temp_array);

		// send mail!		
		$headers = "From: " . $mailsendername . " <" . $mailsenderaddress . ">" . "\r\n";
		$body = "Naam:" . $naam . ", email: " . $email . ", akkoord met voorwaarden:" . $isakkoord;
		
		global $nxs_global_mail_fromname;
		$nxs_global_mail_fromname = $mailsendername;
		global $nxs_global_mail_fromemail;
		$nxs_global_mail_fromemail = $mailsenderaddress;
		
		$mailresult = wp_mail($internalemail, $texttop, $body, $headers);
		if (!$mailresult)
		{
			// nack!
			nxs_webmethod_return_nack("mail was not sent (squeeze)");	
		}
				
		// update activity log
		$activitylog = $activitylog . "[OK]";
		$temp_array = array();
		$temp_array["activitylog"] = $activitylog;
		nxs_mergewidgetmetadata_internal($postid, $placeholderid, $temp_array);
 	}
 	else
 	{
 	}
 	
 	//
 	//
 	//
 	
 	$responseargs = array();
 	$responseargs["url"] = get_permalink($pageidaftersqueeze);
	nxs_webmethod_return_ok($responseargs);
}
?>