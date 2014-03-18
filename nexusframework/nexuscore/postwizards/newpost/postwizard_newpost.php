<?php

function nxs_postwizard_newpost_setuppost($args)
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
							"title" => "",
							"text" => nxs_l18n__("First line of dummy text for new post", "nxs_td"),
						),
					),
				)
			),
		)
	);
}
