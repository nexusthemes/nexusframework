<?php
function nxs_webmethod_importcontent() 
{	
	extract($_REQUEST);
	
	if ($_FILES["file"]["error"] > 0)
	{
		nxs_webmethod_return_nack("Error: " . $_FILES["file"]["error"] . "<br />");
	}
	
	$uploadeddata = file_get_contents($_FILES["file"]["tmp_name"]);
	
	$filename = $_FILES["file"]["name"];
	$mimetype = $_FILES["file"]["type"];
	$filesize = $_FILES["file"]["size"];
		
	$upload_overrides = array( 'test_form' => false ); 
	$uploaded_file = wp_handle_upload($_FILES['file'], $upload_overrides);
	
	// If the wp_handle_upload call returned a local path for the image
	if(isset($uploaded_file['file'])) 
	{
		// The wp_insert_attachment function needs the literal system path, which was passed back from wp_handle_upload
		$file_name_and_location = $uploaded_file['file'];
		
		// read file
		
		$message = "start";
		$linenumber = 1;
		
		$isfileheaderaccepted = "";
		$expectingdata = "";
		
		if ($import == "appendpoststructureandwidgets")
		{
			if ($postid == "")
			{
				// 
				nxs_webmethod_return_nack("postid not set #247");
			}
			
			if ($fh = fopen($file_name_and_location, "r")) 
			{
				while (!feof($fh)) 
				{
					$linenumber += 1;
			  	$line = fgets($fh);
			  	if (nxs_stringstartswith($line, "# @"))
			  	{
			  		// get rid of the trailing end-of-line chars
			  		$line = rtrim($line);
			  		$linepieces = explode("@", $line);
			  		if (count($linepieces) < 2)
			  		{
			  			$message = "unsupported line;" . $line;
			  			continue;
			  		}
			  		
			  		if ($isfileheaderaccepted == "true")
			  		{
				  		if ($linepieces[1] == "poststructure")
				  		{
				  			// next line(s) contain the post structure
				  			$expectingdata = "poststructure";
				  			$message .= "[poststructure gevonden]";
				  		}
				  		else if ($linepieces[1] == "widget")
				  		{
				  			if (count($linepieces) < 3)
				  			{
				  				$message = "unsupported line;" . $line;
				  				continue;
				  			}
				  			
				  			// next line(s) contain the post structure
				  			$expectingdata = "widgetmetadata";
				  			$placeholderid = $linepieces[2];
				  			$message .= "[widget metadata gevonden]";
				  		}
				  		else
				  		{
				  			// unsupported; skipping
				  			$expectingdata = "unknown";
				  			$message .= "[unknown gevonden;" . $linepieces[1] .";" . $line . "]";
				  		}
				  	}
				  	else
				  	{
				  		if ($linepieces[1] == "export")
				  		{
				  			if (count($linepieces) < 4)
				  			{
				  				$message = "unsupported line;" . $line;
			  					continue;
				  			}
				  			if ($linepieces[2] == "poststructureandwidgets")
				  			{
				  				if ($linepieces[3] == "v1.0")
				  				{
				  					// OK!
				  					$isfileheaderaccepted = "true";
				  				}
				  			}
				  		}
				  	}
			  	}
			  	else
			  	{
			  		if ($isfileheaderaccepted == "true")
			  		{
				  		// data line
				  		if ($expectingdata == "poststructure")
				  		{
				  			// TODO: ensure the poststructure doesn't contain any widgets that are already used on the page,
				  			// otherwise a clone would be made which results in unexpected/unexplainable behaviour for the end user
				  			
				  			// append the lines 1:1 to the nxs_structure
				  			$currentstructure = nxs_getpoststructure($postid);
				  			$newstructure = $currentstructure . $line;
				  			//$newstructure = $line;	// uncomment this line to override existing content, instead of appending data
				  			nxs_updatepoststructure($postid, $newstructure);
				  			
				  			$message .= "poststructure uitgebreid";
				  			
				  			// we will assume the next line is not another poststructure (not sure if this statement is correct, requires testing)
				  			$expectingdata = "";
				  		}
				  		else if ($expectingdata == "widgetmetadata")
				  		{
				  			// TODO: ensure the widget doesn't yet exist? (if so, a "override" flag should first be set)
				  			// inject the raw meta data of the widget to the page
				  			
				  			//  remove trailing EOL characters
				  			$line = rtrim($line);
				  			
				  			$message .= "line:" . $line; 
				  			
								$widgetmeta = json_decode($line, true);
								
								$message .= "type:" . $widgetmeta["type"];
								
								nxs_overridewidgetmetadata($postid, $placeholderid, $widgetmeta);
								
								$message .= "metadata toegevoegd";
								
								// we will assume the next line is not another poststructure (not sure if this statement is correct, requires testing)
				  			$expectingdata = "";
				  		}
				  	}
				  	else
				  	{
				  		$message .= "ignoring line " . $line;
				  	}
			  	}
				}
				
				fclose($fh);
			}
			else
			{
				nxs_webmethod_return_nack("unable to open/read file #68537");
			}	
		}
		else
		{
			nxs_webmethod_return_nack("import is not supported;" . $import);
		}
		
		header('Content-type: application/json');
		$output = array();
		$output["filesize"] = $filesize;
		$output["name"] = $filename;
		$output["mimetype"] = $mimetype;
		$output["message"] = $message;
		
		nxs_webmethod_return_ok($output);
	}
	else
	{
		nxs_webmethod_return_nack("bestand kan niet worden geupload #2612a");
	}
}
?>