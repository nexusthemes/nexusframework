<?php

// kudos to http://www.php.net//manual/en/features.file-upload.multiple.php
function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

function nxs_webmethod_savemultifileupload() 
{	
	extract($_REQUEST);
		
	// note, we set the "html-upload" as the check_upload_size function would 
	// otherwise invoke wpdie()
	$_POST['html-upload'] = "nxs";

	$countarray = count($_FILES['file']['name']);
	
	$resultaat = "";
	
  $newarray = array();
  for($i=0;$i<$countarray;$i++)
  {
    $newarray[$i]['name']=$_FILES['file']['name'][$i];
    $newarray[$i]['type']=$_FILES['file']['type'][$i];
    $newarray[$i]['tmp_name']=$_FILES['file']['tmp_name'][$i];
    $newarray[$i]['error']=$_FILES['file']['error'][$i];
    $newarray[$i]['size']=$_FILES['file']['size'][$i];
  }
  
  $uploadedimages = array();
  
  foreach ($newarray as $index => $currentfileprops)
  {  	
		if ($currentfileprops["error"] > 0)
		{
			// see http://php.net/manual/en/features.file-upload.errors.php
			nxs_webmethod_return_alternativeflow("UPLOADERROR" . $currentfileprops["error"], $args);
		}
		
		$uploadeddata = file_get_contents($currentfileprops["tmp_name"]);
		
		$filename = $currentfileprops["name"];
		$mimetype = $currentfileprops["type"];
		$filesize = $currentfileprops["size"];
			
		$upload_overrides = array( 'test_form' => false ); 
		$uploaded_file = wp_handle_upload($currentfileprops, $upload_overrides);
		
		if (isset($uploaded_file["error"]) && $uploaded_file["error"] != "")
		{
			$args = array();
			$args["message"] = $uploaded_file["error"];
			nxs_webmethod_return_alternativeflow("SIZEERR", $args);
		}
		
		
		// If the wp_handle_upload call returned a local path for the image
		if(isset($uploaded_file['file'])) 
		{
			// The wp_insert_attachment function needs the literal system path, which was passed back from wp_handle_upload
			$file_name_and_location = $uploaded_file['file'];
					
			// Generate a title for the image that'll be used in the media library
			$file_title_for_media_library = $uploadtitel;
			
			// Set up options array to add this file as an attachment
	    $attachment = array
	    (
        'post_mime_type' => $mimetype,
        'post_title' => addslashes($file_title_for_media_library),
        'post_content' => '',
        'post_status' => 'inherit'
	    );
		
			// Run the wp_insert_attachment function. This adds the file to the media library 
			// and generates the thumbnails. If you wanted to attch this image to a post, you 
			// could pass the post id as a third param and it'd magically happen.
	    $imageid = wp_insert_attachment( $attachment, $file_name_and_location );
	    
	    $resultaat .= "nieuwe imageid: {$imageid};";
	    
	    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	    $attach_data = wp_generate_attachment_metadata( $imageid, $file_name_and_location );
	    wp_update_attachment_metadata($imageid,  $attach_data);
		
			$currentuploadedimage = array();
			$currentuploadedimage["filesize"] = $filesize;
			$currentuploadedimage["name"] = $filename;
			$currentuploadedimage["mimetype"] = $mimetype;
			$currentuploadedimage["imageid"] = $imageid;
			//$currentoutput["fileurl"] = $fileurl;
			//$currentoutput["title"] = $fileurl;
			//$currentoutput["file_name_and_location"] = $file_name_and_location;
			
			$uploadedimages[] = $currentuploadedimage;
		}
		else
		{
			echo "mmmm:";
			var_dump($uploaded_file);
			echo "punt";
			nxs_webmethod_return_nack("unable to upload file (#2612b); check: is file extension permitted?");
		}
		
	}
		
	// post processor; what should we do with the uploaded images?
	if ($postprocessor == "append")
	{
		if ($appendtype == "galleryitem")
		{
			// create a new row for each uploaded image
			foreach ($uploadedimages as $currentimage)
			{
				$imageid = $currentimage["imageid"];
				
				$resultaat .= "processing imageid: {$imageid};";
				
				// the structure to extend
				$poststructure = nxs_parsepoststructure($postid);
				
				$numberofrows = count($poststructure);
				
				$pagerowid = nxs_allocatenewpagerowid($postid);
			
				// create new row
				$pagerowtemplate = "one";	// row with one column
				$newrow = array();
				$newrow["rowindex"] = "new";
				$newrow["pagerowtemplate"] = $pagerowtemplate;
				$newrow["pagerowid"] = $pagerowid;
				$newrow["pagerowattributes"] = "pagerowtemplate='" . $pagerowtemplate . "' pagerowid='" . $pagerowid . "'";
				$newrow["content"] = nxs_getpagerowtemplatecontent($pagerowtemplate);
			
				// insert row into structure
				$updatedpoststructure = nxs_insertarrayindex($poststructure, $newrow, $numberofrows + 1);
				
				// persist structure
				$updateresult = nxs_storebinarypoststructure($postid, $updatedpoststructure);
				
				// configure the widget we just created...
				$placeholderid = nxs_parsepagerow($newrow["content"]);
		
				$clientpopupsessioncontext = array();
				$clientpopupsessioncontext["postid"] = $postid;
				$clientpopupsessioncontext["placeholderid"] = $placeholderid;
				$clientpopupsessioncontext["contextprocessor"] = "widgets";
				$clientpopupsessioncontext["sheet"] = "home";
		
				$args = array();
				$args["clientpopupsessioncontext"] = $clientpopupsessioncontext;
				$args["placeholdertemplate"] = $appendtype;
				
				// for downwards compatibility we replicate the postid and placeholderid to the 'root'
				$args["postid"] = $postid;
				$args["placeholderid"] = $placeholderid;
							
				nxs_initializewidget($args);

				// decorate the widget
				$args = array();
				$args["placeholdertemplate"] = $appendtype;
				$args["postid"] = $postid;
				$args["placeholderid"] = $placeholderid;
				
				$args["image_imageid"] = $imageid;	// the image we just uploaded
				$args["title"] = "Title";
				$args["text"] = "Text";
				
				nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
			}
		}
	}
	$result["pleaserefreshpage"] = "true";
	$result["resultaat"] = $resultaat;
	nxs_webmethod_return_ok($result);
}
?>