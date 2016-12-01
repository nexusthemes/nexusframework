<?php

// taken from http://www.htmlcenter.com/blog/wordpress-import-images-from-another-website/
function nxs_site_importattachment_url($url, $title, $globalid)
{
	if( !class_exists( 'WP_Http' ) )
	  include_once( ABSPATH . WPINC. '/class-http.php' );

	$http = new WP_Http();
	$http = $http->request($url);
	if( $http['response']['code'] != 200 )
	{
		return false;
	}

	// TODO: derive file extension and name from url
	$filename = basename($url); // 'synced-' . nxs_getrandompostname() . ".png";

	$attachment = wp_upload_bits($filename, null, $http['body'], date("Y-m", strtotime( $http['headers']['last-modified'] ) ) );
	if( !empty( $attachment['error'] ) )
		return false;

	$filetype = wp_check_filetype( basename( $attachment['file'] ), null );

	$postinfo = array(
		'post_mime_type'	=> $filetype['type'],
		'post_title'		=> $title,
		'post_content'		=> '',
		'post_status'		=> 'inherit',
	);
	$filename = $attachment['file'];
	$attach_id = wp_insert_attachment( $postinfo, $filename);
	
	if ($attach_id === false)
	{
		return false;
	}

	if( !function_exists( 'wp_generate_attachment_data' ) )
	{
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	}
	
	$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
	wp_update_attachment_metadata( $attach_id,  $attach_data );
	
	// override global id of the attachment
	nxs_reset_globalidtovalue($attach_id, $globalid);

	return $attach_id;	
}

function nxs_site_createcontent($args)
{
	$postid = $args["postid"];
	if ($postid == "") { echo "postid not set"; die(); } 
	$overridetitle = $args["overridetitle"];
	
	require_once(NXS_FRAMEWORKPATH . '/nexuscore/license/license.php');
	$licensekey = nxs_license_getlicensekey();
	
	$contenturl = "https://turnkeypagesprovider.websitesexamples.com/?contentprovider=getcontent&postid={$postid}&licensekey={$licensekey}";
	$contentjson = file_get_contents($contenturl);
	$content = json_decode($contentjson, true);
	
	global $nxs_site_content;
	$nxs_site_content = $content;
	
	$nxs_site_content["semanticglobalidmapping"]["ROOTSOURCE"] = nxs_create_guid();
	$nxs_site_content["semanticglobalidmapping"]["HOME"] = nxs_gethomeglobalid();
	
	$subargs = array();
	$subargs["currentpostglobalid"] = "ROOTSOURCE";
	$subargs["preventendlessloop"] = 20;	// max nr of posts to be created
	if ($overridetitle != "")
	{
		$subargs["overridetitle"] = $overridetitle;
	}
	
	nxs_site_createcontent_internal($subargs);
	
	// 
}

function nxs_site_createcontent_internal($args)
{
	$result = array();
	
	global $nxs_site_content;
	$nxs_site_content["preventendlessloop"]--;
	if ($nxs_site_content["preventendlessloop"] < 0)
	{
		echo "preventing endless loop; max tries reached";
		die();
	}

	extract($args);
	
	if ($currentpostglobalid == "")
	{
		echo "currentpostglobalid not specified?";
		die();
	}
	
	$origcurrentpostglobalid = $currentpostglobalid;

	// map any semantic globalid to the value it should 
	// have (for example "HOME", or "ROOTSOURCE", ...)
	$issemantic = false;
	if (isset($nxs_site_content["semanticglobalidmapping"][$currentpostglobalid]))
	{
		$issemantic = true;
		$currentpostglobalid = $nxs_site_content["semanticglobalidmapping"][$currentpostglobalid];
	}
	
	// create new ids for items that are create from templates
	$istemplated = false;
	if (nxs_stringstartswith($origcurrentpostglobalid, "CLONE_"))
	{
		$istemplated = true;
		$currenttemplateglobalid = str_replace("CLONE_", "", $origcurrentpostglobalid);
		$currentpostglobalid = nxs_create_guid();
		$result["globalid"] = $currentpostglobalid;
	}
	
	if (isset($nxs_site_content["globalidscreatedduringsession"][$currentpostglobalid]))
	{
		// prevent endless loops
		return $result;
	}
	
	if (nxs_global_globalidexists($currentpostglobalid))
	{
		// if the globalid is already there, we won't override it
		// for the stats/debugging we mark it as being part of the process, but already there
		$nxs_site_content["globalidsalreadytherebeforesession"][$currentpostglobalid] = "true";
		return $result;
	}
	
	// its not here yet; mark it as newly created during this session as thats what we will 
	// do right now this is done to prevent the item from being recreated endless times; 
	// we only want one
	$nxs_site_content["globalidscreatedduringsession"][$currentpostglobalid] = "true";
	
	// grab the content to be cloned from the "source"
	if ($istemplated)
	{
		// for templates, use the template
		$contentcurrentpost = $nxs_site_content["posts"][$currenttemplateglobalid];
		if (count($contentcurrentpost) == 0)
		{
			echo "referenced TEMPLATE ($currentpostglobalid) not present in content?!";
			die();
		}
	}
	else if ($issemantic)
	{
		// for semantic references, use the "original" (semantic) identifier from the source
		$contentcurrentpost = $nxs_site_content["posts"][$origcurrentpostglobalid];
		if (count($contentcurrentpost) == 0)
		{
			echo "referenced currentpostglobalid ($currentpostglobalid) not present in content?! ($origcurrentpostglobalid)";
			die();
		}
	}
	else 
	{
		// default (use the one specified)
		$contentcurrentpost = $nxs_site_content["posts"][$currentpostglobalid];
		if (count($contentcurrentpost) == 0)
		{
			echo "referenced currentpostglobalid ($currentpostglobalid) not present in content?!";
			die();
		}
	}
	
	// step 1; create a new entity
	$posttype = $contentcurrentpost["posttype"];
	$nxsposttype = $contentcurrentpost["nxsposttype"];
	$nxssubposttype = $contentcurrentpost["nxssubposttype"];
	$destinationpostid = "";
	$destinationglobalid = "";
	
	if ($posttype == "attachment")
	{
		$url = $contentcurrentpost["url"];
		$title = $contentcurrentpost["title"];
		nxs_site_importattachment_url($url, $title, $currentpostglobalid);
	}
	else if (in_array($posttype, array("nxs_templatepart", "nxs_genericlist", "page")))
	{
		$postmetas = array();
		if ($contentcurrentpost["nxs_semanticlayout"] != "")
		{
			$postmetas["nxs_semanticlayout"] = $contentcurrentpost["nxs_semanticlayout"];
		}
		if ($contentcurrentpost["nxs_semantic_taxonomy"] != "")
		{
			$postmetas["nxs_semantic_taxonomy"] = $contentcurrentpost["nxs_semantic_taxonomy"];
		}
		$nxs_semantic_media_postid = false;
		$nxs_semantic_media = $contentcurrentpost["nxs_semantic_media"];
		if ($nxs_semantic_media != "")
		{
			// check if there's already an image in the media manager with this "tag"
			$nxs_semantic_media_postid = nxs_wp_getpostidbymeta("nxs_semantic_media", $nxs_semantic_media);
			$imagealreadyexists = ($nxs_semantic_media_postid != "" && $nxs_semantic_media_postid > 0);
			if (!$imagealreadyexists)
			{
				$optionkey = "stansmeta";
				$stansmeta = get_option($optionkey);
				$businesstype = $stansmeta["businesstype"];
				if ($businesstype != "")
				{
					$filepath = "https://mediamanager.websitesexamples.com/?nxs_imagecropper=true&requestedwidth=300&requestedheight=150&scope=businesstype&businesstype={$businesstype}&debug=tru&url={$nxs_semantic_media}";
					error_log("importing using businesstype $businesstype $filepath");
				}
				else
				{
					error_log("importing using themeid $themeid");
					$filepath = "https://mediamanager.websitesexamples.com/?nxs_imagecropper=true&requestedwidth=300&requestedheight=150&scope=themeid&themeid={$themeid}&debug=tru&url={$nxs_semantic_media}";
				}
				
				$fraction = str_replace("nxsmedia://", "", $nxs_semantic_media);
				// p.e. "123rf|15302771"
				$pieces = explode("|", $fraction);
				// 
				$photo_license = $pieces[0];
				$photo_id = $pieces[1];
				
				// import image
				$importmeta = array
				(
					"filepath" => $filepath,
					//"basename" => "lala",
					"postmetas" => array
					(
						"nxs_semantic_media" => $nxs_semantic_media,
						"stockphoto_id" => $photo_id,
						"stockphoto_license" => $photo_license,
					),
				);
				$r = nxs_import_file_to_media_v2($importmeta);
				$nxs_semantic_media_postid = $r["postid"];

				error_log("importing media results in " . json_encode($r));
				
				//echo "imported?";
				//var_dump($r);
				//die();
			}
			else
			{
				//echo "already there 2352345 ($nxs_semantic_media_postid) <br />";
				//echo "nxs_semantic_media_postid: $nxs_semantic_media_postid";
				//die();
				
				// nothing to do here
			}
			
			// 
		}
		
		$newpost_args = array();
		$newpost_args["slug"] = $contentcurrentpost["slug"];
		$newpost_args["titel"] = $contentcurrentpost["title"];
		
		$newpost_args["featuredimageid"] = $nxs_semantic_media_postid;
		
		if (isset($overridetitle))
		{
			$newpost_args["titel"] = $overridetitle;
			$newpost_args["slug"] = $overridetitle;
		}
		
		$newpost_args["post_excerpt"] = $contentcurrentpost["excerpt"];
		$newpost_args["post_content"] = $contentcurrentpost["content"];
		
		$newpost_args["wpposttype"] = $posttype;
		$newpost_args["nxsposttype"] = $nxsposttype;
		$newpost_args["nxssubposttype"] = $nxssubposttype;
		$newpost_args["postwizard"] = "skip";
		/*
		if ($posttype == "page")
		{
			$newpost_args["createpage"] = "true";	// ensure a PAGE is created (not a post)
		}
		*/
		
		$newpost_args["globalid"] = $currentpostglobalid;
		$newpost_args["postmetas"] = $postmetas;
		$response = nxs_addnewarticle($newpost_args);
		$destinationpostid = $response["postid"];
		
		error_log("just inserted destinationpostid: $destinationpostid");
		
		$destinationglobalid = $response["globalid"];
		
		// replicate the structure of the post
		$structure = $contentcurrentpost["struct"];
		nxs_storebinarypoststructure($destinationpostid, $structure);
		
		// replicate the data per row
		$rowindex = 0;
		foreach ($structure as $pagerow)
		{
			// ---------------- ROW META
			
			// replicate the metadata of the row
			$pagerowid = nxs_parserowidfrompagerow($pagerow);
			if (isset($pagerowid))
			{
				// get source meta
				$rowmetadata = $contentcurrentpost["rowsmetadata"][$pagerowid];
				// store destination meta
				nxs_overridepagerowmetadata($destinationpostid, $pagerowid, $rowmetadata);
			}
			
			// ---------------- WIDGET META
			
			// replicate the metadata of the widgets in the row
			$pagerowcontent = $pagerow["content"];
			$placeholderids = nxs_parseplaceholderidsfrompagerow($pagerowcontent);
			foreach ($placeholderids as $placeholderid)
			{
				// get source metadata
				$widgetmetadata = $contentcurrentpost["widgetsmetadata"][$placeholderid];
				
				// tune the widget such that any referenced entities
				// are pointing to the appropriate placed
				// this part will also ensure recursively referenced entities will be created
				foreach ($widgetmetadata as $key => $val)
				{
					if (nxs_stringendswith($key, "_globalid"))	 // p.e. image_imageid_globalid
					{
						// this field references another entity (for example an image, or a list, or a post, ...)
						
						// replace any SEMANTIC references, if applicable
						if (isset($nxs_site_content["semanticglobalidmapping"][$val]))
						{
							$val = $nxs_site_content["semanticglobalidmapping"][$val];
						}
		
						// ensure that globalid exists by invoking this method recursively
						$recursiveargs = array
						(
							"currentpostglobalid" => $val,
						);
						$createresult = nxs_site_createcontent_internal($recursiveargs);
						
						// handle clone templates (applies when referencing genericlists, like banner items, sliders, etc.)
						// in those cases, we should update the value to the result of the create invocation
						// (the globalid is determined at runtime when the new entity is created)
						if (nxs_stringstartswith($val, "CLONE_"))
						{
							$val = $createresult["globalid"];
						}
						
						// when we come here we, the referenced item has to exist
						$localpostid = nxs_get_postidaccordingtoglobalid($val);
						$localkey = str_replace("_globalid", "", $key);
						$widgetmetadata[$localkey] = $localpostid;
					}
				}
				
				// store destination metadata
				nxs_overridewidgetmetadata($destinationpostid, $placeholderid, $widgetmetadata);
			}
		}
		
		// post processing
		if ($contentcurrentpost["nxs_semantic_taxonomy"] != "")
		{
			$taxonomy = $contentcurrentpost["nxs_semantic_taxonomy"];
			
			// this post represents a taxonomy like for exapmle a service; 
			// we automatically add the newly created post to the list of 
			// services
			global $businesssite_instance;
			$contentmodel = $businesssite_instance->getcontentmodel();
			$servicesetpostid = $contentmodel[$taxonomy]["postid"];
			// add an additional row to that post
			// appends a new "one" row, with the specified widget properties to an existing post

			$args = array
			(
				"postid" => $servicesetpostid,
				"widgetmetadata" => array
				(
					"type" => "service",
					"filter_postid" => $destinationpostid,	// 
					"enabled" => "true",
				),
			);
			$r = nxs_add_widget_to_post($args);
		}
	}
	else
	{
		echo "posttype not yet supported; $posttype";
		die();
	}
	
	return $result;
}