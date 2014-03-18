<?php

function nxs_postwizard_legacyupgrader_setuppost($args)
{
	//
	extract($args);
	
	if ($postid == "")
	{
		echo "postid niet meegegeven";
		return;
	}
	
	// notify the user we have upgraded this post/page
	
	$meta = nxs_getsitemeta();

	$parsedpoststructure = nxs_parsepoststructure($postid);
	if (count($parsedpoststructure) == 0)
	{
		$wpposttype = nxs_getwpposttype($postid);
		if ($wpposttype == "post")
		{
			// if the original item is a "post" type, convert to "blogentry" pagetemplate ,otherwise convert to a "webpage"
			$pagetemplate = "blogentry";
			$args["pagetemplate"] = $pagetemplate;
			$args["titel"] = nxs_gettitle_for_postid($postid);	// keep titel
			$args["slug"] = nxs_getslug_for_postid($postid);	// keep slug
			
			$args["poststatus"] = get_post_status($postid);	// keep the status
			
			$args["selectedcategoryids"] = nxs_getwpcategoryids_for_postid($postid); // keep the categories
			nxs_updatepagetemplate($args);
			
			//
			// build structure
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
									"head" => "",	// niets, de titel zit al in de toppagelet
									"level" => "1",
									"body" => nxs_getwpcontent_for_postid($postid),	// initiele content
									"thumbid" => get_post_thumbnail_id($postid),	// featured image
								),
							),
						)
					),
				)
			);
		}
		else if ($wpposttype == "page")
		{
			$pagetemplate = "webpage";
			$args["pagetemplate"] = $pagetemplate;
			$args["titel"] = nxs_gettitle_for_postid($postid);	// keep titel
			$args["slug"] = nxs_getslug_for_postid($postid);	// keep slug
			$args["selectedcategoryids"] = nxs_getwpcategoryids_for_postid($postid); // keep the categories
			$args["poststatus"] = get_post_status($postid);	// keep the status
			
			nxs_updatepagetemplate($args);
			
			//
			// build structure
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
									"head" => nxs_gettitle_for_postid($postid),
									"level" => "1",
									"body" => nxs_getwpcontent_for_postid($postid),	// initiele content
									"thumbid" => get_post_thumbnail_id($postid),	// featured image
								),
							),
						)
					),
				)
			);
		}
		else
		{		
			// TODO: make pluggable...
			
			// assumed webpage
			
			$pagetemplate = "webpage";
			$args["pagetemplate"] = $pagetemplate;
			$args["titel"] = nxs_gettitle_for_postid($postid);	// keep titel
			$args["slug"] = nxs_getslug_for_postid($postid);	// keep slug
			$args["selectedcategoryids"] = nxs_getwpcategoryids_for_postid($postid); // keep the categories
			nxs_updatepagetemplate($args);
			
			//
			// build structure
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
									"head" => nxs_gettitle_for_postid($postid),
									"level" => "1",
									"body" => nxs_getwpcontent_for_postid($postid),	// initiele content
									"thumbid" => get_post_thumbnail_id($postid),	// featured image
								),
							),
						)
					),
				)
			);
		}
	}
	else
	{
		// legacy pagina's die wel reeds een structuur hebben, maar nog geen pagetemplate,
		// zetten we handmatig op 'webpage'
		$pagetemplate = "webpage";
		$modifiedmetadata = array();
		$modifiedmetadata["pagetemplate"] = $pagetemplate;
		nxs_merge_postmeta($postid, $modifiedmetadata);
	}
}
