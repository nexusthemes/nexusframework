<?php

function nxs_postwizard_defaultgenericlist_setuppost($args)
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
		)
	);
}
