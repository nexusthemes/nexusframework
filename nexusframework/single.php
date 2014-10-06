<?php	
	if (is_attachment())
	{
		/*
		$mimetype = get_post_mime_type();
  	echo "mimetype; $mimetype <br />";
		echo "attachment url: $url <br />";
		*/
		$url = wp_get_attachment_url();
		wp_redirect($url, 301);
		die();
	}
	else
	{
		require_once(NXS_FRAMEWORKPATH . '/content.php');
	}
?>