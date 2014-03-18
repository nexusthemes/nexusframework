<?php

function nxs_postwizard_default_footer_setuppost($args)
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
				"pagerowtemplate" => "131313", 
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
					array
					(
						"placeholdertemplate" => "undefined",
						"args" => array
						(
						),
					),					
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
			
			array
			(
				"pagerowtemplate" => "1212", 
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
