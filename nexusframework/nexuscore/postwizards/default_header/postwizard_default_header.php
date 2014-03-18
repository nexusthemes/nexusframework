<?php

function nxs_postwizard_default_header_setuppost($args)
{
	//
	extract($args);
	
	if ($postid == "")
	{
		echo "postid niet meegegeven";
		return;
	}
	
	$menupostid = nxs_getpostid_for_title_and_nxstype("menu", 'menu');
	
	$imageid = nxs_getpostid_for_title_and_wpposttype("screenshot.png", "attachment");
	
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
						"placeholdertemplate" => "socialfollowus",
						"args" => array
						(
							"halign" => "right",
							"twitteraccount" => "@nexusthemes",
							"facebookurl" => "https://www.facebook.com",
						),
					),
					// 
				),
			),			
			
			array
			(
				"pagerowtemplate" => "twothirdonethird", 
				"pagerowid" => nxs_getrandompagerowid(), 
				"pagerowtemplateinitializationargs" => array
				(
					array
					(
						"placeholdertemplate" => "text",
						"args" => array
						(
							"head" => get_bloginfo('name'),
							"level" => '1',
						),
					),
					array
					(
						"placeholdertemplate" => "image",
						"args" => array
						(
							"imageid" => $imageid,
						),
					),
					// 
				),
			),
		)
	);
}
