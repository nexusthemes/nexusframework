<?php

//$x = get_transient("nxs_themeupdate");
//var_dump($x);

//die();

function nxs_license_clearupdatetransient()
{
	delete_transient("nxs_themeupdate");
}
add_filter('delete_site_transient_update_themes', 'nxs_license_clearupdatetransient');
add_action('load-update-core.php', 'nxs_license_clearupdatetransient');
add_action('load-themes.php', 'nxs_license_clearupdatetransient');

function nxs_license_checkupdate($value)
{
	if (!nxs_hassitemeta())
	{
		// dont use this if the sitemeta is not available
		return $value;
	}
	
	// ---
	
	$sitemeta = nxs_getsitemeta();
	$theme = $sitemeta["catitem_themeid"];
	if ($theme == "")
	{
		// dont use this if the theme is not updateble
		return $value;
	}
	
	// ---
	
	$shouldcheck = false;
	$url = nxs_geturlcurrentpage();
	
	$nxs_themeupdate = get_transient("nxs_themeupdate");
	if (nxs_stringcontains($url, "nxs_admin_update"))
	{
		// always refresh if the user accesses the admin_update page
		$shouldcheck = true;
	}	
	else if ($nxs_themeupdate == false || $nxs_themeupdate == "")
	{
		$shouldcheck = true;
	}	
	else
	{
		// 
	}
	
	if ($shouldcheck)
	{		
		$licensekey = get_option('nxs_licensekey');
		//var_dump($licensekey);
		
		$site = nxs_geturl_home();
		$themeobject = wp_get_theme();
		$version = $themeobject->version;
	
		$serviceparams = array
		(
			'timeout' => 15,
			'sslverify' => false,
			'body' => array
			(
				"nxs_license_action" => "get_version",
				"version" => $version,
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
	 		$before = get_transient("nxs_themeupdate");
	 		
	 		set_transient("nxs_themeupdate", $update_data, $durationinsecs);

	 		if ("no" == $update_data["nxs_updates"])
	 		{
	 			$value = null;
	 		}
	 		else if ("enterlicensekey" == $update_data["nxs_updates"])
	 		{
	 			$value = null;
	 		}
	 		else if ("yes" == $update_data["nxs_updates"])
	 		{
				$theme = $update_data["theme"];
				if ($theme == null)
				{
					echo "theme not set!	";
					var_dump($update_data);
					die();
				}
				$value -> response[$theme] = $update_data;
				//echo "JAAAA" . $theme;
				//die();
			}
			else
			{
				$value = null;
			}
			
			set_site_transient('update_themes', $value);
		}
		else
		{
			// skip for now... 
	    $durationinsecs = 60 * 60 * 12;	// 12 hours
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
		nxs_license_clearupdatetransient();
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
	nxs_license_clearupdatetransient();
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
	add_settings_section('nxs_section_license', 'Registration', 'nxs_section_license_callback', 'nxs_section_license_type');
	add_settings_field('nxs_register', 'Register', 'nxs_licenseregister_callback', 'nxs_section_license_type', 'nxs_section_license');
	
	//add_settings_field('nxs_licensekey', 'Serial number', 'nxs_licensekey_callback', 'nxs_section_license_type', 'nxs_section_license');
	
		
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
	
		$themeupdate = get_transient("nxs_themeupdate");
		//var_dump($themeupdate);
		//die();
		
		$newversionexists = false;
		
		if ($themeupdate["nxs_updates"] == "enterlicensekey")
		{
			echo "Please enter a license key first";
		}
		else if ($themeupdate["nxs_updates"] == "yes")
		{
			if (version_compare($themeupdate["new_version"], $theme->version) > 0)
			{
				$newversionexists = true;
			}
			
			if ($newversionexists)
			{
				echo "A new version (" . $themeupdate["new_version"] . ") is available";
				echo "<!-- " . $themeupdate["new_version"] . " vs " . $theme->version . " -->";
				?>
				<p>
					<a class="button-primary" href="<?php echo $updateurl; ?>">Update theme</a>
		  	</p>
				<?php
				
			}
			else
			{
				echo "Your theme is up to date";
				echo "<!-- latest: " . version_compare($themeupdate["new_version"]) . " -->";
			}
		}
		else
		{
			echo "Your theme is up to date <!-- (2) -->";
		}
		
		if ($themeupdate["nxs_messagehtml"] != "")
		{
			echo $themeupdate["nxs_messagehtml"];
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
    <h2>License</h2>
    <form method="post">
      <?php 
      	settings_fields('option_group'); 
      	do_settings_sections('nxs_section_license_type');
      ?>
		</form>
	</div>
	<?php
}

function nxs_licensekey_stripspecialchars($input) 
{
	$input = strtolower($input);
	$input = preg_replace('/[^A-Za-z0-9.]/', '', $input); // Removes special chars.
	$result = $input;
	return $result;
}

function nxs_licenseregister_invoke()
{
	$ordernr = $_REQUEST["nxs_ordernr"];
	
	$site = nxs_geturl_home();
	$themeobject = wp_get_theme();
	$version = $themeobject->version;
	
	if (!nxs_hassitemeta())
	{
		// if the site meta is not (yet) available, don't perform the license check!
		$shouldcheck = false;
		$theme = "notset1";
	}
	else
	{
		$sitemeta = nxs_getsitemeta();
		$theme = $sitemeta["catitem_themeid"];
	}

	$serviceparams = array
	(
		'timeout' => 15,
		'sslverify' => false,
		'body' => array
		(
			"nxs_license_action" => "register",
			"version" => $version,
			"theme" => $theme,
			"ordernr" => $ordernr,
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
  	//var_dump($response);
  }
  
  $body = wp_remote_retrieve_body($response); 
	$response_data = json_decode($body, true);
	
	if ($successful ) 
  {
  	if ($response_data["result"] == "OK")
  	{
	  	$nxs_licensekey = $response_data["nxs_licensekey"];
  		// store serial
  		update_option('nxs_licensekey', $nxs_licensekey);
  		
  		$dummy = new stdClass();
			nxs_license_checkupdate($dummy);

  		// reload current page
  		$url = nxs_geturlcurrentpage();
  		?>
  		<script type='text/javascript'>
  			var url = '<?php echo $url; ?>';
  			window.location = url;
  		</script>
  		<?php
  		?>
  		<p>
  			Thank you for your registration
  		</p>
			<p>
				&nbsp;
			</p>
			<p>
	  		<a class='button-primary' href=''>Reload the page</a>
	  	</p>
  		<?php
  	}
  	else if ($response_data["result"] == "ALTFLOW")
  	{
  		
  		if ($response_data["altflowid"] == "WRONGSITE")
  		{
  			// license is already in use on another site
  			update_option('nxs_licensekey', "");
  			//var_dump($response_data);
  			if ($response_data["helphtml"] != "")
  			{
  				echo $response_data["helphtml"];
  			}
  			else
  			{
  				?>
		  		<p>
		  			This ordernumber is already registered and actively being used on another website.
		  			If you want to transfer the license to this new domain, please contact us at info@nexusthemes.com.
		  		</p>
  				<?php
  			}
	  		?>
  			<p>
		  		<a class='button-primary' href=''>Reload the page</a>
		  	</p>
			  <?php
  		}
  		else
  		{
  			update_option('nxs_licensekey', "");
	  		?>
	  		<p>
	  			Unable to complete your registration<!-- ALT FLOW <?php echo $response_data["altflowid"]; ?> -->.<br />If you made a valid purchase
	  			and want to register your theme, please try again later, or contact us at info@nexusthemes.com<br />
	  		</p>
				<p>
					&nbsp;
				</p>
				<p>
		  		<a class='button-primary' href=''>Reload the page</a>
		  	</p>
	  		<?php
	  		//var_dump($response);
  		}
  	}
  	else
  	{
  		update_option('nxs_licensekey', "");
  		?>
  		<p>
  			Unable to complete your registration<!-- 1 -->.<br />If you made a valid purchase
  			and want to register your theme, please try again later, or contact us at info@nexusthemes.com<br />
  		</p>
			<p>
				&nbsp;
			</p>
			<p>
	  		<a class='button-primary' href=''>Reload the page</a>
	  	</p>
  		<?php
  		//var_dump($response);
  	}
  }
  else
  {
  	update_option('nxs_licensekey', "");
		?>
		<p>
			Unable to complete your registration<!-- 2 -->.<br />If you made a valid purchase
			and want to register your theme, please try again later, or contact us at info@nexusthemes.com<br />
		</p>
		<p>
			&nbsp;
		</p>
		<p>
  		<a class='button-primary' href=''>Restart registration</a>
  	</p>
		<?php
  	//var_dump($response);
  }
}

function nxs_licenseregister_callback()
{
	$licensekey = esc_attr(get_option('nxs_licensekey'));
	if ($licensekey == "")
	{
		if ($_REQUEST["nxs_license_register"] == "true")
		{
			nxs_licenseregister_invoke();
		}
		else
		{
			$url = nxs_geturlcurrentpage();
			$url = nxs_addqueryparametertourl_v2($url, "nxs_license_register", "true", true, true);
			$noncedurl = wp_nonce_url($url, 'register');
			$site = nxs_geturl_home();
			?>
			<p>
				Enter your ordernumber below to register your purchase.<br />
				Registering your site will enable best effort support and theme updates for one year
				for this specific site.<br />
			</p>
			<p>
				&nbsp;
			</p>
			<p>
				Site
			</p>
			<p>
				<input type='text' name='nxs_site' readonly onkeydown='jQuery("#nxsregbutton").show();' onchange='jQuery("#nxsregbutton").show();' value='<?php echo $site; ?>' style='width:30%' />
			</p>
			<p>
				&nbsp;
			</p>
			<p>
				Ordernumber
			</p>
			<input type='text' name='nxs_ordernr' onkeydown='jQuery("#nxsregproceed").show();' onchange='jQuery("#nxsregproceed").show();' value='' style='width:30%' />
			<p>
				&nbsp;
			</p>
			<p id='nxsregproceed' style='' >
				<input name="nxs_license_register" type="hidden" value="true" />
				<input name='submit' type='submit' id='submit' class='button-primary' value='<?php _e("Register") ?>' />
				<i>NOTE; your purchase can be registered <u>one SINGLE time</u>.</i>
			</p>
			<p>
				&nbsp;
			</p>
			<p>
				<ul>
					<li>Don't have an ordernumber? <a href='http://nexusthemes.com' target='_blank'>Purchase now</a></li>
				</ul>
			</p>
			<?php
		}
	}
	else
	{
		if ($_REQUEST["nxs_license_unregister"] == "true")
		{
			update_option('nxs_licensekey', "");
			nxs_license_clearupdatetransient();
			// reload
			?>
			<p>
				License was removed.
			</p>
			<p>
				&nbsp;
			</p>
			<p>
	  		<a class='button-primary' href=''>Reload the page</a>
	  	</p>
	  	<?php
  		// reload current page
  		$url = nxs_geturlcurrentpage();
  		?>
  		<script type='text/javascript'>
  			var url = '<?php echo $url; ?>';
  			window.location = url;
  		</script>
			<?php
			die();
		}
		else
		{
			$licensekey = esc_attr(get_option('nxs_licensekey'));
			$checkupdatesurl = admin_url('admin.php?page=nxs_admin_update');
			?>
			<p>
				Existing license: <?php echo $licensekey; ?><br />
				Check the update section to see if this license is still valid.
			</p>
			<p>
				&nbsp;
			</p>
			<input name="nxs_license_unregister" type="hidden" value="true" />
			<a href="<?php echo $checkupdatesurl; ?>" class='button-primary'>Check for updates</a>
			<input name='submit' type='submit' id='submit' class='button-secondary' value='<?php _e("Remove license") ?>' />
			<?php
		}
	}
}

function nxs_licensekey_callback()
{
	extract($_POST);
	
	if ($nxs_licensekey != "")
	{
		$nxs_licensekey = nxs_licensekey_stripspecialchars($nxs_licensekey);
		update_option('nxs_licensekey', $nxs_licensekey);

		// ensure checking for update..
		nxs_license_clearupdatetransient();
	}
	
  $licensekey = esc_attr(get_option('nxs_licensekey'));
	echo "<input type='text' name='nxs_licensekey' onkeydown='jQuery(\"#submit\").show();' onchange='jQuery(\"#submit\").show();' value='{$licensekey}' style='width:30%' />";
	?>
  <p class='submit'>
  	<input name='submit' type='submit' id='submit' style='display: none;' class='button-primary' value='<?php _e("Save Changes") ?>' />
 	</p>

	<?php
}
?>