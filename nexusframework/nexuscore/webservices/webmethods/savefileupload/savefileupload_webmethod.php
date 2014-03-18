<?php
function nxs_webmethod_savefileupload() 
{	
	extract($_REQUEST);
		
	// note, we set the "html-upload" as the check_upload_size function would 
	// otherwise invoke wpdie()
	$_POST['html-upload'] = "nxs";
	
	if ($_FILES["file"]["error"] > 0)
	{
		// see http://php.net/manual/en/features.file-upload.errors.php
		nxs_webmethod_return_alternativeflow("UPLOADERROR" . $_FILES["file"]["error"], $args);
	}
	
	$uploadeddata = file_get_contents($_FILES["file"]["tmp_name"]);
	
	$filename = $_FILES["file"]["name"];
	$mimetype = $_FILES["file"]["type"];
	$filesize = $_FILES["file"]["size"];
		
	$upload_overrides = array( 'test_form' => false ); 
	$uploaded_file = wp_handle_upload($_FILES['file'], $upload_overrides);
	
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
    $attachment = array(
        'post_mime_type' => $mimetype,
        'post_title' => addslashes($file_title_for_media_library),
        'post_content' => '',
        'post_status' => 'inherit'
    );
	
		// Run the wp_insert_attachment function. This adds the file to the media library 
		// and generates the thumbnails. If you wanted to attch this image to a post, you 
		// could pass the post id as a third param and it'd magically happen.
    $imageid = wp_insert_attachment( $attachment, $file_name_and_location );
    
    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $imageid, $file_name_and_location );
    wp_update_attachment_metadata($imageid,  $attach_data);
	
		$output = array();
		$output["filesize"] = $filesize;
		$output["name"] = $filename;
		$output["mimetype"] = $mimetype;
		$output["imageid"] = $imageid;
		//$output["fileurl"] = $fileurl;
		//$output["title"] = $fileurl;
		//$output["file_name_and_location"] = $file_name_and_location;
		
		nxs_webmethod_return_ok($output);
	}
	else
	{
		echo "mmmm:";
		var_dump($uploaded_file);
		echo "punt";
		nxs_webmethod_return_nack("unable to upload file (#2612b); check: is file extension permitted?");
	}
}
?>