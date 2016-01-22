<?php

// function used to collect errors on a per-form-item basis. This function
// is for example used to figure out whether a required field is indeed entered.
function nxs_widgets_formboxitem_getformitemsubmitresult($widget, $args)
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
		nxs_webmethod_return_nack("function not found; " . $functionnametoinvoke);	
	}
	
	return $result;
}

function nxs_webmethod_formboxsubmit() 
{
	extract($_POST);
 	
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
	
	$replytoemailaddress = "";
 	
 	$atleastoneerrorfound = false;
 	
 	$validationerrors = array();
 	$markclientsideelements = array();
 	$outputlines = array();
 	$fileuploads = array();
 	
 	// load the form fields, and delegate handling to the form elements
 	$index = -1;
	foreach ($structure as $pagerow)
	{
		$index = $index + 1;
		$rowcontent = $pagerow["content"];
		$currentplaceholderid = nxs_parsepagerow($rowcontent);
		$currentplaceholdermetadata = nxs_getwidgetmetadata($items_genericlistid, $currentplaceholderid);
		$widget = $currentplaceholdermetadata["type"];
		
		// special type; contactitemreplyto is used to 
		if ($widget == "contactitemreplyto")
		{
			nxs_requirewidget("contactbox");
			$prefix = nxs_widgets_contactbox_getclientsideprefix($postid, $placeholderid);
			
			$elementid = $currentplaceholdermetadata["elementid"];
			$key = $prefix . $elementid;
			$replytoemailaddress = $_POST[$key];
		}
		
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


		 		// gets results from here
		 		$subresult = nxs_widgets_formboxitem_getformitemsubmitresult($widget, $submitargs);

		 		// var_dump($subresult);

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

		 			$fileuploads[] = $subresult["fileupload"];

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
		// die();

		// so far no errors were found
	 	$url = nxs_geturl_for_postid($containerpostid);

		// Get widget properties
		$metadata = nxs_getwidgetmetadata($postid, $placeholderid);
		
		// Localize atts
		$metadata = nxs_localization_localize($metadata);

		// Lookup translation
		$metadata = nxs_filter_translatelookup($metadata, array("internal_email", "sender_email"));

	 	extract($metadata);

	 	// upload files
		foreach ($fileuploads as $fileupload)
		{
			$fileuploadstorageabsfolder = nxs_widgets_formbox_getfileuploadstorageabsfolder($metadata);

			$filename = $fileupload["name"];
			$fileext = pathinfo($filename, PATHINFO_EXTENSION);
			$filetempname = $fileupload['tmp_name'];

			$newfilename = nxs_create_guid();

			$filedestination = "{$fileuploadstorageabsfolder}{$newfilename}.{$fileext}";

			$moveuploadedfileresult = move_uploaded_file($filetempname, $filedestination);

			if (!$moveuploadedfileresult)
			{
				$atleastoneerrorfound = true;
 				$validationerrors[] = "Failed to upload file";
			}

			// Change the outputlines for the file
			foreach ($outputlines as $key => $value) {
				$pos = strpos($value, $filetempname);
				if ($pos)
				{
					$formlabel = substr($value, 0, $pos);
					$start = strpos($filedestination, "wp-content");
					$end = strlen($filedestination) - $start + 1;
					$fileurl = substr($filedestination, -$end);
					$outputlines[$key] = "{$formlabel}<a href='{$fileurl}'>{$filename}</a>";	
				}
			}
		}

		// store data in csv
		$storageabspath = nxs_widgets_formbox_getpath($metadata);
		if ($storageabspath != "")
		{
			$lineseperator = "\r\n";
			$columnseperator = ";";
			$removenewlines = true;
			$datatoappend = "";
			
			// append submitted form data
			foreach ($outputlines as $currentoutputline)
			{
				$removecolumnseperators = true;
				if ($removecolumnseperators)
				{
					// if user would use ; himself in the output,
					// the CSV cannot be used
					$currentoutputline = str_replace($columnseperator, "(columnseperator)", $currentoutputline);
				}
				
				$datatoappend .= $currentoutputline . $columnseperator;
			}
			
			// append url
			$datatoappend .= "Url: " . $url . $columnseperator;
			
			// append date and time
			$currentdatetime = date('Y-m-d H:i:s');
			$datatoappend .= "Timestamp: " . $currentdatetime . $columnseperator;
			
			// append client IP
			$clientip = $_SERVER['REMOTE_ADDR'];
			$datatoappend .= "Client IP: " . $clientip . $columnseperator;
			
			if ($removenewlines)
			{
				// if user would use \r\n himself in the output,
				// the CSV cannot be used
				$datatoappend = str_replace($lineseperator, " ", $datatoappend);
			}


			// store form
			file_put_contents($storageabspath, $datatoappend, FILE_APPEND | LOCK_EX);
			
			// store new line for each row
			file_put_contents($storageabspath, $lineseperator, FILE_APPEND | LOCK_EX);
		}
		else
		{
			// no output writing to file
		}
		
		$validationerrors = array();
		
		if ($internal_email == "info@example.org")
	 	{
	 		$responseargs = array();

	 		$validationerrors []= nxs_l18n__("The form is configured to deliver to the dummy e-mail address info@example.org", "nxs_td");
		}
		
		// allow plugins to also validate the form
		$validationerrors = apply_filters('nxs_formboxsubmit_verify', $validationerrors, $metadata);
		if (count($validationerrors) > 0)
		{
			$responseargs = array();
 		
	 		$responseargs["validationerrorhead"] = nxs_l18n__("Form submit failed", "nxs_td");
		 	$responseargs["validationerrors"] = $validationerrors;
		 	$responseargs["markclientsideelements"] = $markclientsideelements;
			nxs_webmethod_return_ok($responseargs);
		}
		else if ($internal_email != "" && nxs_isvalidemailaddress($internal_email))
		{
			$headers = 'From: ' . $sender_name . ' <' . $sender_email . '>' . "\r\n";
			
			if ($replytoemailaddress != "")
			{	
				$headers = "Reply-to: {$replytoemailaddress}\r\n";
			}
			
			$body = "";
			
			if ($mail_body_includesourceurl != "")
			{
				$body .= nxs_l18n__("This form was posted from url:", "nxs_td") . $url . " \r\n";
			}
			
			foreach ($outputlines as $currentoutputline)
			{
				$body .= $currentoutputline . " \r\n";
			}
			
			global $nxs_global_mail_fromname;
			$nxs_global_mail_fromname = $sender_name;
			global $nxs_global_mail_fromemail;
			$nxs_global_mail_fromemail = $sender_email;

			$mailresult = wp_mail($internal_email, $subject_email, $body, $headers);
			if (!$mailresult)
			{
				global $ts_mail_errors;
				global $phpmailer;
				if (!isset($ts_mail_errors)) $ts_mail_errors = array();
				if (isset($phpmailer)) 
				{
					$ts_mail_errors[] = $phpmailer->ErrorInfo;
				}
				
				$responseargs = array();
 		
		 		$responseargs["validationerrorhead"] = nxs_l18n__("Cannot submit this form; error sending mail", "nxs_td");
		 		$validationerrors = array();
		 		$validationerrors []= nxs_l18n__("Please notify the site administrator; error sending mail", "nxs_td");
			 	$responseargs["validationerrors"] = $validationerrors;
			 	$responseargs["debugerrors"] = $ts_mail_errors;
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