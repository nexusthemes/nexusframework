<?php

// function used to collect errors on a per-form-item basis. This function
// is for example used to figure out whether a required field is indeed entered.
function nxs_widgets_contactboxitem_getformitemsubmitresult($widget, $args)
{
	$functionnametoinvoke = 'nxs_widgets_' . $widget . '_getformitemsubmitresult';
	//
	// invokefunction
	//
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		if ($widget == "undefined" || $widget == "")
		{
			$result = array
			(
				"result" => "OK";
			);
		}
		else
		{
			nxs_webmethod_return_nack("function not found; " . $functionnametoinvoke);	
		}
	}
	
	return $result;
}

function nxs_webmethod_contactboxsubmit() 
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
 	
 	
 	
 	// ensure the widget exists
 	$widgetmetadata = nxs_getwidgetmetadata($postid, $placeholderid);
 	$items_genericlistid = $widgetmetadata["items_genericlistid"];
 	$structure = nxs_parsepoststructure($items_genericlistid);
	if (count($structure) == 0) 
	{
		// error?
		nxs_webmethod_return_nack("form structure not found? [" . $items_genericlistid . "]");
	}
 	
 	$atleastoneerrorfound = false;
 	
 	$validationerrors = array();
 	$markclientsideelements = array();
 	$outputlines = array();
 	
 	// load the form fields, and delegate handling to the form elements
 	$index = -1;
	foreach ($structure as $pagerow)
	{
		$index = $index + 1;
		$rowcontent = $pagerow["content"];
		$currentplaceholderid = nxs_parsepagerow($rowcontent);
		$currentplaceholdermetadata = nxs_getwidgetmetadata($items_genericlistid, $currentplaceholderid);
		$widget = $currentplaceholdermetadata["type"];
		
		if ($widget != "")
		{
			$requirewidgetresult = nxs_requirewidget($widget);
		 	if ($requirewidgetresult["result"] == "OK")
		 	{
		 		$submitargs = array();
		 		// 
		 		$submitargs["postid"] = $postid;
		 		$submitargs["placeholderid"] = $placeholderid;
		 		$submitargs["metadata"] = $currentplaceholdermetadata;
		 		//
		 		$subresult = nxs_widgets_contactboxitem_getformitemsubmitresult($widget, $submitargs);
		 		if ($subresult["result"] == "OK")
		 		{
		 			$newerrors = $subresult["validationerrors"];
		 			if (count($newerrors) > 0)
		 			{
		 				$atleastoneerrorfound = true;
		 				foreach ($newerrors as $currentnewerror)
		 				{
		 					$validationerrors[] = $currentnewerror;
		 				}
		 			}
		 			$newmarkclientsideelements = $subresult["markclientsideelements"];
		 			if (count($newmarkclientsideelements) > 0)
		 			{
		 				foreach ($newmarkclientsideelements as $currentnewmarkclientsideelement)
		 				{
		 					$markclientsideelements[] = $currentnewmarkclientsideelement;
		 				}
		 			}

		 			$newoutput = $subresult["output"];
		 			$outputlines[] = $newoutput;
		 		}
		 		else
		 		{
			 		//
			 		nxs_webmethod_return_nack("An error occured when verifying the form;" . $widget);	
		 		}
		 	}
		 	else
		 	{
		 		// 
		 		// TODO: dit moet beter worden afgehandeld; het systeem moet de meldingen client side tonen,
		 		// zodat de gebruiker opnieuw kan submitten
		 		nxs_webmethod_return_nack("missing form element;" . $widget);
		 	}
		}
		else
		{
			// empty widget is ignored
		}
	}
	
	if ($atleastoneerrorfound === false)
	{
	 	$url = nxs_geturl_for_postid($containerpostid)	;

		// send the mail!
		$metadata = nxs_getwidgetmetadata($postid, $placeholderid);
		
		// Localize atts
		$metadata = nxs_localization_localize($metadata);

	 	extract($metadata);
	
		if ($internal_email != "" && nxs_isvalidemailaddress($internal_email))
		{
			$headers = 'From: ' . $sender_name . ' <' . $sender_email . '>' . "\r\n";
			$body = "";
			
			if ($mail_body_includesourceurl != "")
			{
				$body .= nxs_l18n__("This form was posted from url:", "nxs_td") . $url . " \r\n";
			}
			
			foreach ($outputlines as $currentoutputline)
			{
				$body .= $currentoutputline . " <br /><br />\r\n \r\n";
			}

			global $nxs_global_mail_fromname;
			$nxs_global_mail_fromname = $sender_name;
			global $nxs_global_mail_fromemail;
			$nxs_global_mail_fromemail = $sender_email;
			
			$mailresult = wp_mail($internal_email, $subject_email, $body, $headers);
			if (!$mailresult)
			{
				$responseargs = array();
 		
		 		$responseargs["validationerrorhead"] = nxs_l18n__("Cannot submit this form; error sending mail", "nxs_td");
		 		$validationerrors = array();
		 		$validationerrors []= nxs_l18n__("Please notify the site administrator; error sending mail", "nxs_td");
			 	$responseargs["validationerrors"] = $validationerrors;
			 	$responseargs["markclientsideelements"] = $markclientsideelements;
				nxs_webmethod_return_ok($responseargs);
			}
			
			$responseargs = array();
		 	$responseargs["url"] = get_permalink($destination_articleid);
		 	
			nxs_webmethod_return_ok($responseargs);
	 	}
	 	else
	 	{
	 		$responseargs = array();
 		
	 		$responseargs["validationerrorhead"] = nxs_l18n__("Cannot submit this form; the recipient is not configured correctly", "nxs_td");
	 		$validationerrors = array();
	 		$validationerrors []= nxs_l18n__("Please notify the site administrator the form is not configured correctly", "nxs_td");
		 	$responseargs["validationerrors"] = $validationerrors;
		 	$responseargs["markclientsideelements"] = $markclientsideelements;
			nxs_webmethod_return_ok($responseargs);
	 	}
 	}
 	else
 	{
 		$responseargs = array();
 		
 		$responseargs["validationerrorhead"] = nxs_l18n__("Please correct the form", "nxs_td");
	 	$responseargs["validationerrors"] = $validationerrors;
	 	$responseargs["markclientsideelements"] = $markclientsideelements;
		nxs_webmethod_return_ok($responseargs);
 	}
}
?>