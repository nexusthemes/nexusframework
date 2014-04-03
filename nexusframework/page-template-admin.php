<?php	
	global $post;
	$postid = $post->ID;

	// process meta data
	$meta = nxs_get_postmeta($postid);

	if (!is_super_admin)
	{
		$url = wp_login_url(get_permalink());
		$url = nxs_addqueryparametertourl_v2($url, "nxsnotsuperadmin", "true", true, true);
		wp_redirect($url);
		die();		
	}	
	
	if ($_REQUEST["adminheader"] == "off")
	{
		// special case, used to enable admin
		// pages to output downloadable files (forms's CSV's), for example
	}
	else
	{
		// regular
		nxs_getheader("admin");
	}
	
	$page_title = get_the_title();
	
	extract($_GET);
	
	?>
	
	<?php

    if ($backendpagetype == "")
    {
        echo "Dit is een algemene admin pagina (zet de backendpagetype parameter)";
    }
    else
    {
        // doorlussen naar handler voor dit sub request
        $filetobeincluded = NXS_FRAMEWORKPATH . "/nexuscore/adminpages/" . $backendpagetype . "/" . $backendpagetype . ".php";
        if (file_exists($filetobeincluded))
        {
            require($filetobeincluded);
        }
        else
        {
            echo "Page not found; " . $filetobeincluded;
        }
    }
    ?>
    
	<?php nxs_getfooter("admin"); ?>