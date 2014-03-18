<?php

function nxs_postwizard_blankpost_setuppost($args)
{
	//
	extract($args);
	
	if ($postid == "")
	{
		echo "postid niet meegegeven";
		return;
	}
	
	//
	// set pagetemplate
	//
	
	$args["pagetemplate"] = "blogentry";
	nxs_updatepagetemplate($args);
	
	$the_post = get_page($postid);
	$title = $the_post->post_title;
}
