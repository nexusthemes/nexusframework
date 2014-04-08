<?php

function nxs_license_clearupdatetransient()
{
	set_site_transient('nxs_themeupdate', "");
}
add_filter('delete_site_transient_update_themes', 'nxs_license_clearupdatetransient');
add_action('load-update-core.php', 'nxs_license_clearupdatetransient');
add_action('load-themes.php', 'nxs_license_clearupdatetransient');

function nxs_license_checkupdate($value)
{
	$shouldcheck = false;

	if ($_REQUEST["themeupdatecheck"] == "true") 
	{
		nxs_license_clearupdatetransient();
	}

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
		
		$successful = true;
	
	  // make sure the response was successful
	  if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) 
	  {
	  	$successful = false;
	  }
	  
	  $body = wp_remote_retrieve_body($response); 
		$update_data = json_decode($body, true);
		
	  if ($successful ) 
	  {
	 		$durationinsecs = 60 * 60 * 12;	// 12 hours
	 		set_site_transient("nxs_themeupdate", $update_data, $durationinsecs);
	 		
			$theme = $update_data["theme"];
			$value -> response[$theme] = $update_data;
			set_site_transient('update_themes', $value);
		}
		else
		{
			// skip for now... 
	    $durationinsecs = 60 * 60 * 12;	// 12 hours
      set_site_transient("nxs_themeupdate", "fail", $durationinsecs);
		}
	}
	
	return $value;
}
add_filter('site_transient_update_themes', 'nxs_license_checkupdate');

function nxs_license_triggerupdate()
{
	// deze regel is nodig om de logica te triggeren!
	$x = get_site_transient("update_themes");
	//var_dump($x);
}
nxs_license_triggerupdate();

add_action('admin_menu', 'nxs_license_theme_license_page', 11);
function nxs_license_theme_license_page()
{
	add_submenu_page("nxs_backend_overview", 'License', 'License', 'manage_options', 'license_admin', 'nxs_license_theme_license_page_content', '', 81 );
}

function plugin_admin_init()
{
	//All callbacks must be valid names of functions, even if provided functions are blank
	//register_setting('option_group', 'option_name', 'sanitize_callback' );
	add_settings_section('nxs_section_license', 'License key title', 'nxs_section_license_callback', 'nxs_section_page_type');
	add_settings_field('nxs_licensekey', 'Serial number', 'nxs_licensekey_callback', 'nxs_section_page_type', 'nxs_section_license');
	add_settings_section('nxs_section_update', 'Updates', 'nxs_section_update_callback', 'nxs_section_page_type');
}
add_action( 'admin_init', 'plugin_admin_init' );

function sanitize_callback()
{
	echo "sanitize_callback :)";
	die();
}

function nxs_section_license_callback()
{
	//echo "nxs_section_license_callback :)";
}

function nxs_section_update_callback()
{
	$theme = wp_get_theme();
	echo "Theme name: " . $theme -> name . "<br />";
	echo "Current version: " . $theme -> version . "<br />";

	$themeupdate = get_site_transient('nxs_themeupdate');
	if ($themeupdate["new_version"] != "")
	{
		// var_dump($theme);
		
		
		echo "A new version is available (version: " . $themeupdate["new_version"] . ")";
		// var_dump($themeupdate);
	}
	else
	{
		echo "Your theme is up to date :)";
	}
}

function nxs_license_theme_license_page_content() 
{
  ?>
  <div class="wrap">
    <h2>License key</h2>
    <form method="post">
      <?php 
      	settings_fields('option_group'); 
      	do_settings_sections('nxs_section_page_type');
      ?>
     <p class='submit'>
       <input name='submit' type='submit' id='submit' class='button-primary' value='<?php _e("Save Changes") ?>' />
     </p>
		</form>
	</div>
	<?php
}

function nxs_licensekey_callback()
{
	extract($_POST);
	if ($nxs_licensekey != "")
	{
		update_option('nxs_licensekey', $nxs_licensekey);
		// ensure checking for update..
		nxs_license_clearupdatetransient();
		// 
	}
	
  $licensekey = esc_attr(get_option('nxs_licensekey'));
	echo "<input type='text' name='nxs_licensekey' value='{$licensekey}' />";
}

?>