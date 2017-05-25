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
 	$subresults = array();
 	
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

	if ($atleastoneerrorfound === false)
	{
		// die();

		// so far no errors were found
	 	$url = nxs_geturl_for_postid($containerpostid);

		// Get widget properties
		$mixedattributes = nxs_getwidgetmetadata($postid, $placeholderid);
		
		// Lookup translation
		$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("internal_email", "sender_email"));
		
		// ** START
		// these lines are required, as otherwise the {{email}} is not properly interpreted/returned
		$templateproperties = nxs_gettemplateproperties();
		$modelmapping = $templateproperties["templaterules_lookups_lookup"];
		//error_log("modelmappingX;".json_encode($modelmapping));
		// ** END

		
		// phase 2; translate the magic fields using the lookup tables of all referenced models
		$lookupargs = array
		(
			"modeluris" => $modeluris,
		);
		global $nxs_g_modelmanager;
		$lookup = $nxs_g_modelmanager->getlookups_v2($lookupargs);
		
		
		error_log("formboxsubmit;".json_encode($lookup));
		
		$magicfields = array("internal_email", "sender_email");
		$translateargs = array
		(
			"lookup" => $lookup,
			"items" => $mixedattributes,
			"fields" => $magicfields,
		);
		$mixedattributes = nxs_filter_translate_v2($translateargs);

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
			// temporary implementation to see how creating new model instances could/should work
			if (true)
			{
				//
				if ($widgetmetadata["debug"] == "submitmodel")
			 	{
			 		$lookup = array();
			 		
			 		// first the lookup table as defined in the pagetemplaterules
					if (true)
					{
						$templateruleslookups = nxs_gettemplateruleslookups();
						$lookup = array_merge($lookup, $templateruleslookups);
					}
			 		
			 		// second; include the raw list of submitted key/values
			 		if (true)
			 		{
				 		$submittedlookups = array(); 
				 		foreach ($subresults as $subresult)
				 		{
				 			$key = $subresult["formlabel"];
				 			$value = $subresult["value"];
				 			$submittedlookups[$key] = $value;
				 		}
				 		$lookup = array_merge($lookup, $submittedlookups);
			 		}
			 		
			 		// third; include the debug_lookups mapping
			 		if ($debug_lookups != "")
					{
						$mappinglookups = array();
						
						$lines = explode("\n", $debug_lookups);
						foreach ($lines as $line)
						{
							$limit = 2;	// 
							$pieces = explode("=", $line, $limit);
							$key = trim($pieces[0]);
							
							if ($key == "")
							{
								// empty line, ignore
							}
							else if (nxs_stringstartswith($key, "//"))
							{
								// its a comment, ignore
							}
							else
							{
								$val = $pieces[1];
								$mappinglookups[$key] = $val;
							}
						}
						$lookup = array_merge($lookup, $mappinglookups);
					}
					
					// recursively apply/blend the lookup table to the values, until nothing changes or when we run out of attempts 
					if (true)
					{			
						// now that the entire lookup table is filled,
						// recursively apply the lookup tables to its values
						// for those keys that have one or more placeholders in their values
						$triesleft = 4;
						while ($triesleft > 0)
						{
							//
							
							$triesleft--;
							
							$didsomething = false;
							foreach ($lookup as $key => $val)
							{
								if (nxs_stringcontains($val, "{{"))
								{
									$origval = $val;
									
									$translateargs = array
									(
										"lookup" => $lookup,
										"item" => $val,
									);
									$val = nxs_filter_translate_v2($translateargs);
									
									$somethingchanged = ($val != $origval);
									if ($somethingchanged)
									{
										$lookup[$key] = $val;
										$didsomething = true;
									}
									else
									{
										// continue;
									}
								}
							}
							
							if (!$didsomething)
							{
								break;
							}
							else
							{
							}
						}
					}
					
					// apply shortcodes
					if (true)
					{
						foreach ($lookup as $key => $val)
						{
							$lookup[$key] = do_shortcode($val);
						}
					}
					
					// evaluate the modeluri which will be used to store the submitted information
					$translateargs = array
					(
						"lookup" => $lookup,
						"item" => $debug_modeluri,
					);
					$debug_modeluri = nxs_filter_translate_v2($translateargs);
					$pieces = explode("@", $debug_modeluri);
					$humanmodelidentification = $pieces[0];
					$schema = $pieces[1];
					
					// instead of invoking the businessmodel logic right here,
					// the system should use a webmethod invocation
					
					require_once("/srv/generic/plugins-available/nxs-contentprovider/businessmodellogic.php");
					
					// create a new model for this line
					$createmodelargs = array
					(
						"schema" => $schema,
						"humanmodelidentification" => $humanmodelidentification,
						"extendlistmodel" => true,
					);
					// populate the model
					// TODO: instead of setting all key/values of the lookup,
					// we should only set the ones as defined as taxonomy properties according to the schema
					foreach ($lookup as $key => $value)
					{
						$createmodelargs["unwrappedmodel"]["properties"]["taxonomy"][$key] = $value;
					}
					$createresult = nxs_businessmodel_createmodel($createmodelargs);
					//
					$responseargs = array();
	 		
			 		$responseargs["validationerrorhead"] = nxs_l18n__("DEBUG TEST", "nxs_td");
			 		$validationerrors = array();
			 		$validationerrors []= "submitted:" . json_encode($lookup);
			 		$validationerrors []= "storing model as $debug_modeluri";
			 		$validationerrors []= "store result " . json_encode($createresult);
			 		
				 	$responseargs["validationerrors"] = $validationerrors;
				 	$responseargs["markclientsideelements"] = $markclientsideelements;
					nxs_webmethod_return_ok($responseargs);
			 	}
			}
			
			$body = "";
			if ($mail_body_includesourceurl != "")
			{
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