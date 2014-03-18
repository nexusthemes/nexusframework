<?php

function nxs_postwizard_list3dummyitems_setuppost($args)
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
						"placeholdertemplate" => "listitem",
						"args" => array
						(
							"title" => "kenmerk 1",
						),
					),
				)
			),
			array
			(
				"pagerowtemplate" => "one", 
				"pagerowid" => nxs_getrandompagerowid(), 
				"pagerowtemplateinitializationargs" => array
				(
					array
					(
						"placeholdertemplate" => "listitem",
						"args" => array
						(
							"title" => "kenmerk 2",
						),
					),
				)
			),
						array
			(
				"pagerowtemplate" => "one", 
				"pagerowid" => nxs_getrandompagerowid(), 
				"pagerowtemplateinitializationargs" => array
				(
					array
					(
						"placeholdertemplate" => "listitem",
						"args" => array
						(
							"title" => "kenmerk 3",
						),
					),
				)
			),
		)
	);
}
