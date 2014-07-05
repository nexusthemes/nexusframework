<?php
function nxs_webmethod_getpostmetadatabyid() 
{
	extract($_REQUEST);
 	
 	if ($id == "")
 	{
 		nxs_webmethod_return_nack("id niet meegegeven");
 	}
 	
 	$post = get_post($id);
 	
 	$src = wp_get_attachment_image_src($id, 'thumbnail', true);
 	$url = $src[0];
	//
	//
	//
	
	$result = array
	(
		"result" => "OK",
		"title" => $post->post_title,
		"mime" => $post->post_mime_type,
		"alt" => get_post_meta($id, '_wp_attachment_image_alt', true),
		"src" => $url,
	);

	nxs_webmethod_return_ok($result);	
}
?>