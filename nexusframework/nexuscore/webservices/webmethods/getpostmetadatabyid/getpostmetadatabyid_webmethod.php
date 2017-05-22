<?php
function nxs_webmethod_getpostmetadatabyid() 
{
	extract($_REQUEST);
 	
 	if ($postid == "")
 	{
 		nxs_webmethod_return_nack("postid not set");
 	}
 	
 	$post = get_post($postid);
 	
 	/*
 	$src = nxs_wp_get_attachment_image_src($id, 'thumbnail', true);
 	$url = $src[0];
 	$url = nxs_img_getimageurlthemeversion($url);
 	*/
 	
	//
	//
	//
	
	$result = array
	(
		"result" => "OK",
		"editurl" => get_edit_post_link($postid),
	);

	nxs_webmethod_return_ok($result);	
}