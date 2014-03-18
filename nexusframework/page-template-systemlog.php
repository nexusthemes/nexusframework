<?php	
	
	global $post;
	$postid = $post->ID;

	// process meta data
	$meta = nxs_get_postmeta($postid);

	if (!is_super_admin)
	{
		wp_redirect(wp_login_url(get_permalink()));
		die();		
	}	
	
	nxs_getheader("admin");
	
	$page_title = get_the_title();
	
	extract($_GET);
	
?>
	
<?php

	echo nxs_getwpcontent_for_postid($postid);
    
 ?>
    
<?php nxs_getfooter("admin"); ?>