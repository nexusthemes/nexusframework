<?php

function nxs_postwizard_pagelet_default_blogposttop_setuppost($args)
{
	//
	extract($args);
	
	if ($postid == "")
	{
		echo "postid niet meegegeven";
		return;
	}
	/*
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
						"placeholdertemplate" => "wordpresstitle",
						"args" => array
						(
							"foo" => "bar",
						),
					),
				)
			),
		)
	);
	*/
}
