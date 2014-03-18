<?php
function nxs_webmethod_removepage() 
{	
	extract($_REQUEST);

	if ($postid == "")
	{
		nxs_webmethod_return_nack("postid not specified /nxs_webmethod_removerow/");
	}
	if ($howto == "")
	{
		nxs_webmethod_return_nack("howto not specified /nxs_webmethod_removerow/");
	}
	
	global $nxs_global_current_postid_being_rendered;
	$nxs_global_current_postid_being_rendered = $postid;
	global $nxs_global_current_postmeta_being_rendered;
	$nxs_global_current_postmeta_being_rendered = nxs_get_postmeta($postid);

	if ($howto == 'trash')
	{
		// note! wp_delete_post will actually force delete (no recycle ability) on custom post types...,
		// see http://wordpress.org/support/topic/wp_delete_post-wp_trash_post
		
		wp_trash_post($postid);
	}
	else
	{
		wp_delete_post($postid, true);
	}

	//
	// create response
	//
	$responseargs = array();
	nxs_webmethod_return_ok($responseargs);
}
?>