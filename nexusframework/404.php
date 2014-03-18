<?php

	//
	// dit is de 404 van de site
	//
	
	// only do something if site meta exists,
	// if there's no sitemeta, or multiple sitemeta's,
	// the system will crash
	nxs_ensure_validsitemeta();
	
	
	if (nxs_has_adminpermissions())
	{
		$postid = get_option("nxs_404_postid");
		$found = true;
		if ($postid == "")
		{
			// reload the postid...
			$postid = nxs_getpostid_for_title_and_wpposttype('fourofour', 'post');
			if ($postid == null)
			{
				$postid = nxs_getpostid_for_title_and_wpposttype('fourofour', 'page');
			}
			if ($postid == "")
			{
				// not steeds niets gevonden? Dan maar een reguliere text output...
				echo "Page not found (v2)";
				die();
			}
		}
		
		//
		// intentionally we don't show the 404 page here,
		// as that one would become editable too, 
		// which would be very confusing
		//
		
		$wpposttype = nxs_getwpposttype($postid);
		$args = array
		(
		  'post_type' => $wpposttype,
		  'p' => $postid
		);
		query_posts($args);
		if (have_posts())
		{
			the_post();
			
			nxs_getheader("admin");
	
			$true404url = nxs_geturl_for_postid($postid);
				
			echo "404; Page not found<br />";
			echo "Since you are logged in and have edit rights we wont show you the ";
			echo "<a href='" . $true404url . "'>site's 404 page</a>.<br />";
			echo "<br />";
			echo "The page/post/entry you were looking for no longer exists, or the page/post/entry is available, but is no longer published (perhaps got a 'draft' status)";
			echo "";
		
			nxs_getfooter("admin");
			
			die();
		}
		else
		{
			$found = false;
			echo "Page not found (3)";
			die();
		}
	}
	else
	{
		// mark as 404
		header('HTTP/1.0 404 Not Found 404');	
		
		$postid = get_option("nxs_404_postid");
		$found = true;
		if ($postid == "")
		{
			// reload the postid...
			$postid = nxs_getpostid_for_title_and_wpposttype('fourofour', 'post');
			if ($postid == "" || $postid == null || $postid == 0)
			{
				$postid = nxs_getpostid_for_title_and_wpposttype('fourofour', 'page');
			}
			if ($postid == "")
			{
				// not steeds niets gevonden? Dan maar een reguliere text output...
				echo "Page not found (404)";
				die();
			}
		}
		
		$wpposttype = nxs_getwpposttype($postid);
		$args = array
		(
		  'post_type' => $wpposttype,
		  'p' => $postid
		);
		query_posts($args);
		if (have_posts())
		{
			//
			require(NXS_FRAMEWORKPATH . '/page-template.php');
		}
		else
		{
			$found = false;
			echo "Page not found (3)";
			die();
		}
	}
?>