<?php

function nxs_postwizard_defaultmenu_setuppost($args)
{
	//
	extract($args);
	
	if ($postid == "")
	{
		echo "postid niet meegegeven";
		return;
	}
	
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
						"placeholdertemplate" => "menuitemarticle",
						"args" => array
						(
							"title" => "Home",
							"destination_articleid" => nxs_gethomepageid(),
							"depthindex" => 1,
						),
					),
				)
			),
		)
	);
}
