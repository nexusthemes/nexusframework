<?php	
	if (is_attachment())
	{
		$url = wp_get_attachment_url();
		wp_redirect($url, 301);
		die();
	}
	else
	{
		require_once(NXS_FRAMEWORKPATH . '/content.php');
	}
?>