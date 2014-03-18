<?php

function nxs_upgrade_post($override, $postid)
{	
	//
	// update page properties
	//
	$pagemeta = nxs_get_postmeta($postid);
	
	var_dump($pagemeta);
	
	$updatedvalues = array();	
	
	// header
	if ($pagemeta["header_postid"] == "" || $override == "true")
	{
		if ($pagemeta["header_pageid"] != "")
		{
			$updatedvalues["header_postid"] = $pagemeta["header_pageid"];
		}
	}	
	if ($pagemeta["header_postid_globalid"] == "" || $override == "true")
	{
		if ($pagemeta["header_pageid_globalid"] != "")
		{
			$updatedvalues["header_postid_globalid"] = $pagemeta["header_pageid_globalid"];
		}
	}	
	
	// sidebar
	if ($pagemeta["sidebar_postid"] == "" || $override == "true")
	{
		if ($pagemeta["sidebar_pageid"] != "")
		{
			$updatedvalues["sidebar_postid"] = $pagemeta["sidebar_pageid"];
		}
	}
	if ($pagemeta["sidebar_postid_globalid"] == "" || $override == "true")
	{
		if ($pagemeta["sidebar_pageid_globalid"] != "")
		{
			$updatedvalues["sidebar_postid_globalid"] = $pagemeta["sidebar_pageid_globalid"];
		}
	}
	
	// footer	
	if ($pagemeta["footer_postid"] == "" || $override == "true")
	{
		if ($pagemeta["footer_pageid"] != "")
		{
			$updatedvalues["footer_postid"] = $pagemeta["footer_pageid"];
		}
	}
	if ($pagemeta["footer_postid_globalid"] == "" || $override == "true")
	{
		if ($pagemeta["footer_pageid_globalid"] != "")
		{
			$updatedvalues["footer_postid_globalid"] = $pagemeta["footer_pageid_globalid"];
		}
	}
	
	nxs_merge_postmeta($postid, $updatedvalues);

	// 
	
	$parsedpoststructure = nxs_parsepoststructure($postid);
	
	$rowindex = 0;
	foreach ($parsedpoststructure as $pagerow)
	{
		$content = $pagerow["content"];
		
		echo "upgrading row in {$postid}";
		
		$placeholderids = nxs_parseplaceholderidsfrompagerow($content);
		foreach ($placeholderids as $placeholderid)
		{
			
			nxs_upgrade_widget($override, $postid, $placeholderid);
		}
	}
}

function nxs_upgrade_widget($override, $postid, $placeholderid)
{
	echo "upgrading {$postid}-{$placeholderid}-{$override}";
	
	
	if (!nxs_has_adminpermissions())
	{
		nxs_webmethod_return_nack("no access");
	}
	
	// enhance widget's metafields from old - new styled properties, keep old ones intact
	$widgetmetadata = nxs_getwidgetmetadata($postid, $placeholderid);
	$updatedvalues = array();
	
	if ($widgetmetadata["type"] == "articlereference" && $override == "true")
	{
		echo "found";
		$updatedvalues["type"] = "text";
	}
	
	if ($widgetmetadata["type"] == "contactform" && $override == "true")
	{
		echo "found";
		$updatedvalues["type"] = "contact";
	}
	
	if ($widgetmetadata["type"] == "wordpressshortcode" && $override == "true")
	{
		echo "found";
		$updatedvalues["type"] = "text";
	}
	
	
	if ($widgetmetadata["type"] == "externalreference" && $override == "true")
	{
		echo "found";
		$updatedvalues["type"] = "text";
	}
	
	if ($widgetmetadata["type"] == "blogentries" && $override == "true")
	{
		echo "found";
		$updatedvalues["type"] = "blog";
		$updatedvalues["items_layout"] = "extended";
		if ($widgetmetadata["gebruikminiatuur"] == "checked=true")
		{
			// item_image_size:c@1-0
			$updatedvalues["item_image_size"] = "c@1-0";
		}
		if ($widgetmetadata["item_text_truncatelength"] == "")
		{
			$widgetmetadata["items_filter_maxcount"] == "10";
		}
	}
	
	if ($widgetmetadata["type"] == "blog" && $override == "true")
	{
		if ($widgetmetadata["items_layout"] == "")
		{
			$updatedvalues["items_layout"] = "extended";
		}
	}
	
	if ($widgetmetadata["type"] == "testimonial" && $override == "true")
	{
		echo "found";
		$updatedvalues["type"] = "bio";
	}
	
	if ($widgetmetadata["type"] == "searchentry" && $override == "true")
	{
		echo "found";
		$updatedvalues["type"] = "search";
	}
	
	if ($widgetmetadata["type"] == "socialfollowus" && $override == "true")
	{
		echo "found";
		$updatedvalues["type"] = "social";
	}
	
	
	if ($widgetmetadata["type"] == "medewerker" && $override == "true")
	{
		echo "found";
		$updatedvalues["type"] = "text";
		$updatedvalues["title"] = $widgetmetadata["naammedewerker"];
		$updatedvalues["text"] = $widgetmetadata["quotemedewerker"];
	}
	
	// title
	if ($widgetmetadata["title"] == "" || $override == "true")
	{
		if ($widgetmetadata["head"] != "")
		{
			// locate title info
			$updatedvalues["title"] = $widgetmetadata["head"];
		}
		else if ($widgetmetadata["koptekst"] != "")
		{
			// locate title info
			$updatedvalues["title"] = $widgetmetadata["koptekst"];
		}
		else if ($widgetmetadata["kop"] != "")
		{
			// locate title info
			$updatedvalues["title"] = $widgetmetadata["kop"];
		}
		else if ($widgetmetadata["menutitle"] != "")
		{
			// locate title info
			$updatedvalues["title"] = $widgetmetadata["menutitle"];
		}
	}
	
	// text
	if ($widgetmetadata["text"] == "" || $override == "true")
	{
		if ($widgetmetadata["body"] != "")
		{
			// locate title info
			$updatedvalues["text"] = $widgetmetadata["body"];
		}
		else if ($widgetmetadata["bodytekst"] != "")
		{
			// locate title info
			$updatedvalues["text"] = $widgetmetadata["bodytekst"];
		}
		else if ($widgetmetadata["ervaring"] != "")
		{
			// locate title info
			$updatedvalues["text"] = $widgetmetadata["ervaring"];
		}
		else if ($widgetmetadata["quote"] != "")
		{
			// locate title info
			$updatedvalues["text"] = $widgetmetadata["quote"];
		}
	}

	// image
	if ($widgetmetadata["image_imageid"] == "" || $override == "true")
	{
		if ($widgetmetadata["thumbid"] != "")
		{
			// locate title info
			$updatedvalues["image_imageid"] = $widgetmetadata["thumbid"];
			
			// zet ook de afmeting; thumbs zijn klein!
			if ($widgetmetadata["image_size"] == "" || $override == "true")
			{
				$updatedvalues["image_size"] = "c@1-0";
			}
			// zet ook image align
			if ($widgetmetadata["image_alignment"] == "" || $override == "true")
			{
				$updatedvalues["image_alignment"] = "left";
			}
		}
		else if ($widgetmetadata["miniatuurimageid"] != "")
		{
			// locate title info
			$updatedvalues["image_imageid"] = $widgetmetadata["miniatuurimageid"];
		}
		else if ($widgetmetadata["imageid"] != "")
		{
			// locate title info
			$updatedvalues["image_imageid"] = $widgetmetadata["imageid"];
		}
	}
	
	// image border
	// 
	if ($widgetmetadata["image_border_width"] == "" || $override == "true")
	{
		if ($widgetmetadata["randtoevoegen"] != "")
		{
			// locate title info
			$updatedvalues["image_border_width"] = "4-0";
		}
	}

	// image (globalid)
	if ($widgetmetadata["image_imageid_globalid"] == "" || $override == "true")
	{
		if ($widgetmetadata["thumbid_globalid"] != "")
		{
			// locate title info
			$updatedvalues["image_imageid_globalid"] = $widgetmetadata["thumbid_globalid"];
		}
		else if ($widgetmetadata["miniatuurimageid_globalid"] != "")
		{
			// locate title info
			$updatedvalues["image_imageid_globalid"] = $widgetmetadata["miniatuurimageid_globalid"];
		}
		else if ($widgetmetadata["imageid_globalid"] != "")
		{
			// locate title info
			$updatedvalues["image_imageid_globalid"] = $widgetmetadata["imageid_globalid"];
		}
	}
	
	// image align
	
	if ($widgetmetadata["image_alignment"] == "" || $override == "true")
	{
		if ($widgetmetadata["imghalign"] == "l")
		{
			$updatedvalues["image_alignment"] = "left";
		}
	}
	
	// image size
	if ($widgetmetadata["image_size"] == "" || $override == "true")
	{
		if ($widgetmetadata["imgformat"] == "choppedsquare")
		{
			$updatedvalues["image_size"] = "c@1-0";
		}
	}
	
	// button tekst
	if ($widgetmetadata["button_text"] == "" || $override == "true")
	{
		if ($widgetmetadata["buttontekst"] != "")
		{
			$updatedvalues["button_text"] = $widgetmetadata["buttontekst"];
		}
	}		
	
	// video
	if ($widgetmetadata["videoid"] == "" || $override == "true")
	{
		if ($widgetmetadata["video"] != "")
		{
			$updatedvalues["videoid"] = $widgetmetadata["video"];
		}
	}
	
	// destination article id
	if ($widgetmetadata["destination_articleid"] == "" || $override == "true")
	{
		if ($widgetmetadata["targetpageid"] != "")
		{
			$updatedvalues["destination_articleid"] = $widgetmetadata["targetpageid"];
		}
		else if ($widgetmetadata["artikelid"] != "")
		{
			$updatedvalues["destination_articleid"] = $widgetmetadata["artikelid"];
		}
	}
	
	// destination article id (global)
	if ($widgetmetadata["destination_articleid_globalid"] == "" || $override == "true")
	{
		if ($widgetmetadata["targetpageid_globalid"] != "")
		{
			$updatedvalues["destination_articleid_globalid"] = $widgetmetadata["targetpageid_globalid"];
		}
		else if ($widgetmetadata["artikelid_globalid"] != "")
		{
			$updatedvalues["destination_articleid_globalid"] = $widgetmetadata["artikelid_globalid"];
		}
	}
	
	// person
	if ($widgetmetadata["person"] == "" || $override == "true")
	{
		if ($widgetmetadata["klantnaam"] != "")
		{
			$updatedvalues["person"] = $widgetmetadata["klantnaam"];
		}
	}
	
	// line 1 (bio)
	if ($widgetmetadata["line1"] == "" || $override == "true")
	{
		if ($widgetmetadata["bedrijfnaam"] != "")
		{
			$updatedvalues["line1"] = $widgetmetadata["bedrijfnaam"];
		}
	}
	// line 1 (bio)
	if ($widgetmetadata["line1_destination_url"] == "" || $override == "true")
	{
		if ($widgetmetadata["bedrijfurl"] != "")
		{
			$updatedvalues["line1_destination_url"] = $widgetmetadata["bedrijfurl"];
		}
	}
	
	
	
	// css class
	if ($widgetmetadata["ph_cssclass"] == "" || $override == "true")
	{
		if ($widgetmetadata["cssclass"] != "")
		{
			$updatedvalues["ph_cssclass"] = $widgetmetadata["cssclass"];
		}
	}
	
	// menu_menuid
	if ($widgetmetadata["menu_menuid"] == "" || $override == "true")
	{
		if ($widgetmetadata["menuid"] != "")
		{
			$updatedvalues["menu_menuid"] = $widgetmetadata["menuid"];
		}
	}
	
	// menu_menuid globalid
	if ($widgetmetadata["menu_menuid_globalid"] == "" || $override == "true")
	{
		if ($widgetmetadata["menuid_globalid"] != "")
		{
			$updatedvalues["menu_menuid_globalid"] = $widgetmetadata["menuid_globalid"];
		}
	}
	
	// blog cat ids
	if ($widgetmetadata["items_filter_catids"] == "" || $override == "true")
	{
		if ($widgetmetadata["selectedcategoryids"] != "")
		{
			// locate title info
			$updatedvalues["items_filter_catids"] = $widgetmetadata["selectedcategoryids"];
		}
	}
	// blog cat ids
	if ($widgetmetadata["items_filter_catids_globalids"] == "" || $override == "true")
	{
		if ($widgetmetadata["selectedcategoryids_globalids"] != "")
		{
			// locate title info
			$updatedvalues["items_filter_catids_globalids"] = $widgetmetadata["selectedcategoryids_globalids"];
		}
	}
	
	
	// social / twitter
	if ($widgetmetadata["twitter_url"] == "" || $override == "true")
	{
		if ($widgetmetadata["twitteraccount"] != "")
		{
			// locate title info
			$updatedvalues["twitter_url"] = $widgetmetadata["twitteraccount"];
		}
	}
	
	// social / twitter
	if ($widgetmetadata["facebook_url"] == "" || $override == "true")
	{
		if ($widgetmetadata["facebookurl"] != "")
		{
			// locate title info
			$updatedvalues["facebook_url"] = $widgetmetadata["facebookurl"];
		}
	}
	
	// google+
	if ($widgetmetadata["googleplus_url"] == "" || $override == "true")
	{
		if ($widgetmetadata["googleplusurl"] != "")
		{
			// locate title info
			$updatedvalues["googleplus_url"] = $widgetmetadata["googleplusurl"];
		}
	}
	
	// rss
	if ($widgetmetadata["rss_url"] == "" || $override == "true")
	{
		if ($widgetmetadata["rss20"] != "")
		{
			// locate title info
			$updatedvalues["rss_url"] = bloginfo('rss2_url');;
		}
	}
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $updatedvalues);
}
?>
