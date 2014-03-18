<?php

function nxs_postwizard_default404_setuppost($args)
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
	
	$args["pagetemplate"] = "webpage";
	nxs_updatepagetemplate($args);
	//
	//
	//
	
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
							"head" => nxs_l18n__("This page cannot be found[nxs:body]", "nxs_td"),
						),
					),
				)
			),
		)
	);
}
