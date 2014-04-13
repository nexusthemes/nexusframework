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
	if (!nxs_hassitemeta())
	{
		return $value;
	}
	
	$shouldcheck = false;

	if ($_REQUEST["nxs_force_themeupdatecheck"] == "true") 
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
		if (!nxs_hassitemeta())
		{
			// if the site meta is not (yet) available, don't perform the license check!
			$shouldcheck = false;
		}
	}
	
	if ($shouldcheck)
	{
		
		// TODO: get from options
		$licensekey = "qwfjgq23ui4ytg";
		
		$sitemeta = nxs_getsitemeta();
		$theme = $sitemeta["catitem_themeid"];
		
		if ($theme == "")
		{
			echo "theme not set?";
			return $value;
			//var_dump($sitemeta);
			//die();
		}
		
		$site = nxs_geturl_home();
	
		$serviceparams = array
		(
			'timeout' => 15,
			'sslverify' => false,
			'body' => array
			(
				"nxs_license_action" => "get_version",
				"theme" => $theme,
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
			if ($theme == null)
			{
				echo "theme not set!	";
				var_dump($update_data);
				die();
			}
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

function nxs_license_periodictriggerupdate()
{
	if ($_REQUEST["nxs_force_themeupdatecheck"] == "true")
	{
		// wipe
		set_transient("nxs_themeupdater_freq", "");
	}
	
	$nxs_themeupdater_freq = get_transient("nxs_themeupdater_freq");
	if ($nxs_themeupdater_freq == false || $nxs_themeupdater_freq == "")
	{
		nxs_license_actualtriggerupdate();
		
		$hours = 4; // poll max 
		set_transient("nxs_themeupdater_freq", "cached", 60 * 60 * $hours);
	}
}
add_action('after_setup_theme', 'nxs_license_periodictriggerupdate');

function nxs_license_actualtriggerupdate()
{
	if (defined('NXS_FRAMEWORKSHARED'))
	{
		if (NXS_FRAMEWORKSHARED == "true")
		{
			// ignoring update; shared frameworks cannot be updated by the theme updater
			return;
		}
	}
	
	// deze regel is nodig om de logica te triggeren!
	set_site_transient('nxs_themeupdate', "");
	$x = get_site_transient("update_themes");
	set_site_transient('update_themes', $x);
	//var_dump($x);
}

add_action('admin_menu', 'nxs_license_addadminpages', 11);
function nxs_license_addadminpages()
{
	add_submenu_page("nxs_backend_overview", 'License', 'License', 'manage_options', 'nxs_admin_license', 'nxs_license_theme_license_page_content', '', 81 );
	add_submenu_page("nxs_backend_overview", 'Update', 'Update', 'manage_options', 'nxs_admin_update', 'nxs_license_update_page_content', '', 81 );
}

function plugin_admin_init()
{
	//All callbacks must be valid names of functions, even if provided functions are blank
	add_settings_section('nxs_section_license', 'License key title', 'nxs_section_license_callback', 'nxs_section_license_type');
	add_settings_field('nxs_licensekey', 'Serial number', 'nxs_licensekey_callback', 'nxs_section_license_type', 'nxs_section_license');
	add_settings_section('nxs_section_update', 'Updates', 'nxs_section_update_callback', 'nxs_section_update_type');
}
add_action( 'admin_init', 'plugin_admin_init' );

function nxs_section_license_callback()
{
}

function nxs_section_update_callback()
{	
	$theme = wp_get_theme();
	echo "Theme name: " . $theme -> name . "<br />";
	echo "Current version: " . $theme -> version . "<br />";

	$isframeworkshared = false;
	
	if (defined('NXS_FRAMEWORKSHARED'))
	{
		if (NXS_FRAMEWORKSHARED == "true")
		{
			$isframeworkshared = true;
		}
	}
	
	if ($isframeworkshared)
	{
		echo "Automatic updates are not available (the framework is shared)";
	}
	else
	{
		// call actual trigger
		nxs_license_actualtriggerupdate();

		if (is_multisite())
		{
			$updateurl = network_admin_url('themes.php');
		}
		else
		{
			$updateurl = admin_url('themes.php');
		}
	
		$themeupdate = get_site_transient('nxs_themeupdate');
		//var_dump($themeupdate);
		//die();
		
		if ($themeupdate["new_version"] != "")
		{
			echo "A new version is available (version: " . $themeupdate["new_version"] . ")";
			?>
			<p>
				<a class="button-primary" href="<?php echo $updateurl; ?>">Go to Themes</a>
	  	</p>
			<?php
			//echo $themeupdate["package"];

			set_site_transient('nxs_themeupdate', "");
			$x = get_site_transient("update_themes");
			set_site_transient('update_themes', $x);
			var_dump($x);
			echo "AAAAA";
			die();			
		}
		else
		{
			echo "Your theme is up to date :)";
		}
	}
}

function nxs_license_update_page_content() 
{
	?>
  <div class="wrap">
    <h2>Update</h2>
    <form method="post">
      <?php 
      	settings_fields('option_group'); 
      	do_settings_sections('nxs_section_update_type');
      ?>
      <!--
     	<p class='submit'>
       	<input name='submit' type='submit' id='submit' class='button-primary' value='<?php _e("Save Changes") ?>' />
     	</p>
     	-->     	
		</form>
	</div>
	<?php
}

function nxs_license_theme_license_page_content() 
{
  ?>
  <div class="wrap">
    <h2>License key</h2>
    <form method="post">
      <?php 
      	settings_fields('option_group'); 
      	do_settings_sections('nxs_section_license_type');
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