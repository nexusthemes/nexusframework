<?php

$requirewidgetresult = nxs_requirewidget("formbox");

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
		if ($widget == "undefined" || $widget == "")
		{
			$result = array
			(
				"result" => "OK",
			);
		}
		else
		{
			nxs_webmethod_return_nack("function not found; " . $functionnametoinvoke);	
		}
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
 	$mixedattributes = nxs_getwidgetmetadata($postid, $placeholderid);
 	
	// Translate model magical fields
	if (true)
	{
		global $nxs_g_modelmanager;
		
		$combined_lookups = array();
		$combined_lookups = array_merge($combined_lookups, nxs_lookups_getcombinedlookups_for_currenturl());		
		$combined_lookups = array_merge($combined_lookups, nxs_parse_keyvalues($mixedattributes["lookups"]));
		$combined_lookups = nxs_lookups_evaluate_linebyline($combined_lookups);
		
		//error_log("formboxsubmit; combined_lookups; " . json_encode($combined_lookups));
		
		// replace values in mixedattributes with the lookup dictionary
		$magicfields = array("items_data", "destination_url");
		$translateargs = array
		(
			"lookup" => $combined_lookups,
			"items" => $mixedattributes,
			"fields" => $magicfields,
		);
		$mixedattributes = nxs_filter_translate_v2($translateargs);
	}
 	
 	$widgetmetadata = $mixedattributes;
 	$items_genericlistid = $widgetmetadata["items_genericlistid"];
 	$items_data = $widgetmetadata["items_data"];
 	$structure = nxs_parsepoststructure($items_genericlistid);
	if (count($structure) == 0 && $items_data == "") 
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
 	$subresults = array();
 	
 	// load the form fields, and delegate handling to the form elements
 	$index = -1;
	// the design-time items
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
		 		$submitargs["containerpostid"] = $containerpostid;
		 		$submitargs["postid"] = $postid;
		 		$submitargs["placeholderid"] = $placeholderid;
		 		$submitargs["metadata"] = $currentplaceholdermetadata;
		 		//

		 		// gets results from here
		 		$subresult = nxs_widgets_formboxitem_getformitemsubmitresult($widget, $submitargs);
		 		$subresults[] = $subresult;

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
	
	// the model driven items
	$otheritems = array();
	if ($items_data == "")
	{
		// ignore
	}
	else if (nxs_stringstartswith($items_data, "json:"))
	{
		$json = substr($items_data, 5);
		$otheritems = json_decode($json, true);
	}
	else
	{
		// ignore
	}
	foreach ($otheritems as $otheritem)
	{
		if ($otheritem == "")
		{
			continue;
		}
		
		$index = $index + 1;
		$widget = $otheritem["type"];
		
		// override the elementid
		$otheritem["overriddenelementid"] = "nxs_fb_{$postid}_{$placeholderid}_{$index}";
		
		// =======
		
		$widget = $otheritem["type"];
		
		// special type; contactitemreplyto is used to 
		if ($widget == "contactitemreplyto")
		{
			$key = $otheritem["overriddenelementid"];
 			$replytoemailaddress = $_POST[$key];
		}
		
		if ($widget != "")
		{
			$requirewidgetresult = nxs_requirewidget($widget);
		 	if ($requirewidgetresult["result"] == "OK")
		 	{
		 		$submitargs = array();
		 		// 
		 		$submitargs["containerpostid"] = $containerpostid;
		 		$submitargs["postid"] = $postid;
		 		$submitargs["placeholderid"] = $placeholderid;
		 		$submitargs["metadata"] = $otheritem;
		 		//

		 		// gets results from here
		 		$subresult = nxs_widgets_formboxitem_getformitemsubmitresult($widget, $submitargs);
		 		$subresults[] = $subresult;

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
		
		// =======
		
	}
	
	if ($atleastoneerrorfound === false)
	{
		// so far no errors were found
	 		 	
		// Get widget properties
		$mixedattributes = nxs_getwidgetmetadata($postid, $placeholderid);
		
		// Translate model magical fields
		if (true)
		{
			global $nxs_g_modelmanager;
			
			$combined_lookups = array();
			$combined_lookups = array_merge($combined_lookups, nxs_lookups_getcombinedlookups_for_currenturl());		
			$combined_lookups = array_merge($combined_lookups, nxs_parse_keyvalues($mixedattributes["lookups"]));
			$combined_lookups = nxs_lookups_evaluate_linebyline($combined_lookups);
			
			//error_log("formboxsubmit; combined_lookups; " . json_encode($combined_lookups));
			
			// replace values in mixedattributes with the lookup dictionary
			$magicfields = array("internal_email", "sender_email", "destination_url");
			$translateargs = array
			(
				"lookup" => $combined_lookups,
				"items" => $mixedattributes,
				"fields" => $magicfields,
			);
			$mixedattributes = nxs_filter_translate_v2($translateargs);
		}

	 	extract($mixedattributes);
	 	
	 	// upload files
		foreach ($fileuploads as $fileupload)
		{
			$fileuploadstorageabsfolder = nxs_widgets_formbox_getfileuploadstorageabsfolder($mixedattributes);

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
			foreach ($outputlines as $key => $value) 
			{
				$pos = strpos($value, $filetempname);
				if ($pos)
				{
					$fileuploadstorageabsfolderurl = nxs_widgets_formbox_getfileuploadstorageabsfolderurl($mixedattributes);
					$filedestinationurl = "{$fileuploadstorageabsfolderurl}{$newfilename}.{$fileext}";

					$formlabel = substr($value, 0, $pos);
					$start = 0;
					$end = strlen($filedestinationurl) - $start + 1;
					$fileurl = substr($filedestinationurl, -$end);
					$outputlines[$key] = "{$formlabel}<a href='{$fileurl}'>{$filename}</a>";	
				}
			}
		}

		// store data in csv
		$shouldstoreonfs = nxs_widgets_formbox_shouldstoreonfilesystem($mixedattributes);
		if ($shouldstoreonfs)
		{
			$storageabspath = nxs_widgets_formbox_getpath($mixedattributes);
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
						
						// strip new lines
						error_log("submit;" . $currentoutputline);
						$currentoutputline = str_replace("\r\n", "(linebreak)", $currentoutputline);
						$currentoutputline = str_replace(":", "(colon)", $currentoutputline);
						$currentoutputline = str_replace("\'", "(quote)", $currentoutputline);
						$currentoutputline = str_replace("\"", "(quote)", $currentoutputline);
						$currentoutputline = str_replace($columnseperator, "(columnseperator)", $currentoutputline);
					}
					
					$datatoappend .= $currentoutputline . $columnseperator;
				}
				
				// append url
				$url = $_SERVER['HTTP_REFERER'];
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
		
		if (nxs_stringcontains($internal_email, "{"))
		{
	 		$responseargs = array();
	 		$validationerrors []= nxs_l18n__("The form is configured to deliver to an invalid e-mail address (ACCOLADE OPEN)", "nxs_td");
		}
		else if (nxs_stringcontains($internal_email, "}"))
		{
	 		$responseargs = array();
	 		$validationerrors []= nxs_l18n__("The form is configured to deliver to an invalid e-mail address (ACCOLADE CLOSE)", "nxs_td");
		}
		
		// allow plugins to also validate the form
		$validationerrors = apply_filters('nxs_formboxsubmit_verify', $validationerrors, $mixedattributes);
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
			$body = "";
			if ($mail_body_includesourceurl != "")
			{
				$url = $_SERVER['HTTP_REFERER'];
				$body .= nxs_l18n__("This form was posted from url: ", "nxs_td") . $url . "<br /><br />";
			}
			foreach ($outputlines as $currentoutputline)
			{
				$tunedcurrentoutputline = $currentoutputline;
				$tunedcurrentoutputline = str_replace("\n","<br />",$tunedcurrentoutputline);
				$body .= $tunedcurrentoutputline . "<br /><br />";
			}
			$ccemail = "";
			$bccemail = "";
			$mailresult = nxs_sendhtmlmail_v3($sender_name, $sender_email, $internal_email, $ccemail, $bccemail, $replytoemailaddress, $subject_email, $body);
			
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
			
			if ($destination_url != "")
			{
				$responseargs["url"] = $destination_url;
			}
			else
			{
				$url = get_permalink($destination_articleid);
				if ($url === false || $url == "")
				{
					$url = $_SERVER['HTTP_REFERER'];
					error_log("formbox submit; err; no url set? destination_articleid:$destination_articleid");
					//$url = "#";
				}
		 		$responseargs["url"] = $url;
		 	}
		 	
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