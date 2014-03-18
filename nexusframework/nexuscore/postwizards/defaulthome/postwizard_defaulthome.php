<?php

function nxs_postwizard_defaulthome_setuppost($args)
{
	//
	extract($args);
	
	if ($postid == "")
	{
		echo "postid niet meegegeven";
		return;
	}
		
	//
	//
	//
	
	
	//
	// set pagetemplate
	//
	
	$args["pagetemplate"] = "webpage";
	nxs_updatepagetemplate($args);

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
						"placeholdertemplate" => "undefined",
						"args" => array
						(
						),
					),
					// 
				),
			),	
			
		)
	);
	
					
	
}
