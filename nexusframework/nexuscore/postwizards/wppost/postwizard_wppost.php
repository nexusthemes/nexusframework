<?php

function nxs_postwizard_wppost_setuppost($args)
{
	//
	extract($args);
	
	if ($postid == "")
	{
		echo "postid niet meegegeven";
		return;
	}
	
	/*
	$the_post = get_page($postid);
	$originalcontent = $the_post->post_content;
	$title = $the_post->post_title;
	
	nxs_append_posttemplate
	(
		$postid, 
		array
		(
			array
			(
				"pagerowtemplate" => "one", 
				"pagerowid" => nxs_getrandompagerowid(), 
				"pagerowtemplateinitializationargs" => array
				(
					array
					(
						"placeholdertemplate" => "text",
						"args" => array
						(
							"level" => "1",
							"head" => $title,
							"body" => $originalcontent,
						),
					),
				)
			),			
		)
	);
	*/
}
