<?php

function nxs_postwizard_defaultblog_setuppost($args)
{
	//
	extract($args);
	
	if ($postid == "")
	{
		echo "postid not set";
		return;
	}
		
	//
	//
	//
	
	$someargs = array();
	$term = "nexus";
	$taxonomy = "category";
	if(!is_term($term, $taxonomy))
	{
  	$result = wp_insert_term($term, $taxonomy, $someargs);
  	$newcatid = $result["term_id"];
	}
	else
	{
		$result = get_term_by('name', $term, $taxanomy);
		$newcatid = $result->term_id;
	}
	
	$newcats = array();
	$newcats[] = strval($newcatid);

	// Update categories
	wp_set_post_categories($postid, $newcats);
	
	//
	// set pagetemplate
	//
	
	$args["pagetemplate"] = "blogentry";
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
