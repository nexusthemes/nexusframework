<?php

// strange thing; whenever we import xml file and images are inserted, their image filenames get a "1" in front of the ".".
// changing grey -> color (or vice versa) fails in that case.
// steps to use: 
// 1) activate (import; grey)
// 2) chown: chown -R apache:wordpress /var/www/html/wp-content/blogs.dir/109
// 3) delete
// 4) ftp files (color)
// 5) run this patch

// WIPES ALL DATA FROM YOUR SYSTEM!
function nxs_apply_patch20131003001_imgrename()
{
	$timestamp = nxs_gettimestampasstring();
	if ($_REQUEST["yeah_i_know_what_i_am_doing_clear_the_entire_db"] != $timestamp)
	{
		echo "nothing removed as you didn't explicitly agreed ... ";
		echo "if you are sure, please add the 'yeah_i_know_what_i_am_doing_clear_the_entire_db=$timestamp' query parameter";
		die();
	}
	
	nxs_ob_start();
	
	global $wpdb;

	// we do so for truly EACH post (not just post, pages, but also for entities created by third parties,
	// as these can use the pagetemplate concept too. This saves development
	// time for plugins, and increases consistency of data for end-users
	
	echo "<br /><br />";

	// get all postids of images
	$q = "
					select ID postid
					from $wpdb->posts
				";
	
	$postids = $wpdb->get_results($q, ARRAY_A);	
	// LOOP 5; images
	foreach ($postids as $origrow)
	{
		$origpostid = $origrow["postid"];
		$image = get_post($origpostid);
				
		if ('attachment' == $image->post_type)
		{			
			echo "image found<br />";
			// var_dump($image);

			
			$fullsizepath = get_attached_file( $image->ID );
			$newfilename = nxs_stringreplacefirst($fullsizepath, "1.jpg", ".jpg");
			
			echo $newfilename . "<br />";
			echo $origpostid . "<br />";
			
			$metadata = wp_generate_attachment_metadata( $image->ID, $newfilename);
			wp_update_attachment_metadata($origpostid, $metadata);
		}
		
	}
	

  //
  echo "patch finished";
	
  echo "<br /><br />";
  
  $output = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	echo "output:" . $output;
	
	echo "-----------<br />";
	echo "DO NOT FORGET TO ALSO REMOVE THE WP-CONTENT FILES (IMAGES/ATTACHMENTS!!!!)";
	
	return $output;
}

?>