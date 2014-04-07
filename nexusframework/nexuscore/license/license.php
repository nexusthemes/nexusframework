<?php

function nxs_license_clearupdatetransient()
{
	set_site_transient('nxs_themeupdate', "");
}
add_filter('delete_site_transient_update_themes', 'nxs_license_clearupdatetransient');
add_action('load-update-core.php', 'nxs_license_clearupdatetransient');
add_action('load-themes.php', 'nxs_license_clearupdatetransient');
if ($_REQUEST["themeupdatecheck"] == "true") { nxs_license_clearupdatetransient(); }

function nxs_license_checkupdate($value)
{
	$shouldcheck = false;

	$nxs_themeupdate = get_site_transient('nxs_themeupdate');
	
	//var_dump($nxs_themeupdate);
	
	if ($nxs_themeupdate == false || $nxs_themeupdate == "")
	{
		$shouldcheck = true;
	}	
	else
	{
		//echo "wont check!";
		//die();
	}
	
	if ($shouldcheck)
	{
		
		// TODO: get from options
		$licensekey = "qwfjgq23ui4ytg";
		
		$sitemeta = nxs_getsitemeta();
		$catitem_themeid = $sitemeta["catitem_themeid"];
	
		$serviceparams = array
		(
			'timeout' => 15,
			'sslverify' => false,
			'body' => array
			(
				"nxs_license_action" => "get_version",
				"theme" => $catitem_themeid,
				"licensekey" => $licensekey,
				"site" => $site
			)
		);
		
		$site = home_url();
		$url = "http://license.nexusthemes.com/";
		$response = wp_remote_post($url, $serviceparams);
		
		$failed = false;
	
	  // make sure the response was successful
	  if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) 
	  {
	  	$failed = true;
	  }
	  
	  $body = wp_remote_retrieve_body($response); 
		$update_data = json_decode($body, true);
		
	  if ( $failed ) 
	  {
	    // skip for now... 
      set_site_transient("nxs_themeupdate", "fail", strtotime( '+1 hours' ) );
	 	}
	 	else
	 	{
	 		// 
	 		set_site_transient("nxs_themeupdate", "succes", strtotime( '+12 hours' ) );
	 		
			$theme = $update_data["theme"];
			$value -> response[$theme] = $update_data;
			set_site_transient('update_themes', $value);
		}
	}
	
	return $value;
}
add_filter('site_transient_update_themes', 'nxs_license_checkupdate');

//$x = get_site_transient("update_themes");
//var_dump($x);

add_action('admin_menu', 'nxs_license_theme_license_page', 11);
function nxs_license_theme_license_page()
{
	add_submenu_page("nxs_backend_overview", 'License', 'License', 'manage_options', 'license_admin', 'nxs_license_theme_license_page_content', '', 81 );
}

function nxs_license_theme_license_page_content() 
{
  $license = get_option(NXS_LICENSE_OPTION_KEY);
  $status = get_option(NXS_LICENSE_STATUS_OPTION_KEY);
  ?>
  <div class="wrap">
    <h2>License key</h2>
    <form method="post" action="options.php">
      <?php settings_fields('nxs_license_theme_license'); ?>
		</form>
	</div>
	<?php
}
?>