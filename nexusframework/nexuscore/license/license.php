<?php

function nxs_site_wipe()
{
	if (!is_super_admin())
	{
		echo "no super admin rights!";
		die();
	}
	
	//ob_start();
	
	global $wpdb;

	// we do so for truly EACH post (not just post, pages, but also for entities created by third parties,
	// as these can use the pagetemplate concept too. This saves development
	// time for plugins, and increases consistency of data for end-users
	
	$tables_to_delete = array();
	$tables_to_delete[] = "comments";
	$tables_to_delete[] = "commentmeta";
	$tables_to_delete[] = "links";
	$tables_to_delete[] = "terms";
	$tables_to_delete[] = "term_relationships";
	$tables_to_delete[] = "term_taxonomy";
	$tables_to_delete[] = "postmeta";
	$tables_to_delete[] = "posts";
	
	foreach ($tables_to_delete as $currenttabletodelete)
	{
		$q = "DELETE FROM " . $wpdb->prefix . $currenttabletodelete;
		//echo $q;
		$dbresult = $wpdb->get_results($q, ARRAY_A );
		//echo "sql output:<br />";
		//var_dump($dbresult);
		//echo "<br /><br />";
	}
	
  // now we remove the content of the site in the upload folder
	$x = wp_upload_dir();
	$uploadfolderforthissite = $x["basedir"];
	nxs_recursive_removedirectory($uploadfolderforthissite);

	// todo: clear the site's title too
}

function nxs_license_notifyregistersuccess()
{
  ?>
  <div class="updated">
    <p>Succesfully registered your license</p>
  </div>
  <?php
}

function nxs_license_getlicenseserverurl($purpose)
{
	//error_log("invoke; nxs_license_getlicenseserverurl; " . $purpose);
	return "http://license.nexusthemes.com/";
}

function nxs_license_notifynolicense()
{
	if ($_REQUEST["oneclickcontent"] == "true")
	{
		// during the installation, we will skip this warning message
	}
	else if ($_REQUEST["step"] == "1")
	{
		// during the installation, we will skip this warning message
	}
	else
	{
		$url = admin_url('admin.php?page=nxs_admin_license');
	  ?>
	  <div class="error">
	    <p>
	    	You are not receiving theme updates for this WordPress theme 
	    	because the site is not connected to a valid license. To connect
	    	your site to a valid license, enter the ordernumber of your order
	    	on the register page.
	    	<br />
	    	Please <a href='<?php echo $url;?>'>register</a> your license to enable theme updates.
	    </p>
	  </div>
	  <?php
	}
}

function nxs_license_notifyunregistersuccess()
{
  ?>
  <div class="updated">
    <p>Succesfully unregistered your license</p>
  </div>
  <?php
}

function nxs_license_notifyrequireswpupdate()
{
  ?>
  <div class="error">
    <p>The Nexus updater requires at least WP 3.4</p>
  </div>
  <?php
}

function nxs_license_clearupdatetransient()
{
	delete_transient("nxs_themeupdate");
}
add_filter('delete_site_transient_update_themes', 'nxs_license_clearupdatetransient');
add_action('load-update-core.php', 'nxs_license_clearupdatetransient');
add_action('load-themes.php', 'nxs_license_clearupdatetransient');

function nxs_license_checkupdate($value)
{
	if (!function_exists("wp_get_theme"))
	{
		add_action('admin_notices', 'nxs_license_notifyrequireswpupdate');
		return $value;
	}
	
	$themeobject = wp_get_theme();
	
	$parent = $themeobject->parent();
	if ($parent != null)
	{
		$themeobject = $parent;
	}
	
	$theme = $themeobject->name;
	
	// ---
	
	$shouldcheck = false;
	$url = nxs_geturlcurrentpage();
	
	$nxs_themeupdate = get_transient("nxs_themeupdate");
	if (nxs_stringcontains($url, "nxs_admin_update"))
	{
		// always refresh if the user accesses the admin_update page
		$shouldcheck = true;
	}
	else if ($nxs_themeupdate == "nostressing")
	{
		error_log("detected: no stressing, wont invoke get_version");
		$shouldcheck = false;
	}
	else if ($nxs_themeupdate == false || $nxs_themeupdate == "")
	{
		$shouldcheck = true;
	}	
	else
	{
		// 
	}
	
	$licensekey = esc_attr(get_option('nxs_licensekey'));
	if ($licensekey == "")
	{
		$shouldcheck = false;
		
		// wp updates are disabled; we always require users to
		// download the updates themselves
		$template = get_template();
		$value -> response[$template] = null;
	}
	
	if ($shouldcheck)
	{
		$site = nxs_geturl_home();
		$themeobject = wp_get_theme();
		
		$parent = $themeobject->parent();
		if ($parent != null)
		{
			$themeobject = $parent;
		}
		
		$version = $themeobject->version;
		$maxexecutiontime = ini_get('max_execution_time'); 
		
		if (function_exists('nxs_theme_getmeta'))
		{
			$meta = nxs_theme_getmeta();
			$version = $meta["version"];
			$theme = $meta["id"];
		}
	
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
				"site" => $site,
				"maxexecutiontime" => $maxexecutiontime,
			)
		);
		
		$site = home_url();
		$url = nxs_license_getlicenseserverurl("get_version");
		
		global $nxs_glb_license_response; // prevent server from making multiple invocations per request
		if ($nxs_glb_license_response == null)
		{		
			$response = wp_remote_post($url, $serviceparams);
			$nxs_glb_license_response = $response;
		}
		else
		{
			$response = $nxs_glb_license_response;
		}
		
		if (true)
		{
			$successful = true;
		
		  // make sure the response was successful
		  if ( is_wp_error( $response )) 
		  {
		  	$successful = false;
		  	error_log("detected failure response, message:");
		    error_log($response->get_error_message());
		  }
		  
		  $body = wp_remote_retrieve_body($response); 
		  $update_data = json_decode($body, true);
		  
		  if ($successful ) 
		  {
		  	if ($update_data["result"] == "OK" || $update_data["result"] == "ALTFLOW")
		  	{
			 		$durationinsecs = 60 * 60 * 24 * 14;	// 1x every 2 weeks
			 		$before = get_transient("nxs_themeupdate");
			 		
			 		set_transient("nxs_themeupdate", $update_data, $durationinsecs);
		
			 		if ("no" == $update_data["nxs_updates"])
			 		{
			 			$value = nxs_license_updatetheme($value, null);
			 		}
			 		else if ("enterlicensekey" == $update_data["nxs_updates"])
			 		{
			 			nxs_licenseresetkey();
			 			$value = nxs_license_updatetheme($value, null);
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
						
						//var_dump($update_data);
						//die();
						//echo "KOMT IE";
						
						if ($update_data["nxs_enablewpupdater"] == "yes")
						{
							$value = nxs_license_updatetheme($value, $update_data);
						}
						else
						{
							$value = nxs_license_updatetheme($value, null);
						}
					}
					else
					{
						// $value = null;
					}
				}
				else
				{
					// skip for now... 
		    	$durationinsecs = 60 * 60 * 12;	// x hours
		 	  	//error_log("instructing to prevent stressing");
			    set_transient("nxs_themeupdate", "nostressing", $durationinsecs);
			    
		    	$value = nxs_license_updatetheme($value, null);
				}
			}
			else
			{
				// skip for now... 
		    $durationinsecs = 60 * 60 * 8;	// x hours
	
	 	  	//error_log("instructing to prevent stressing");
		    set_transient("nxs_themeupdate", "nostressing", $durationinsecs);
		    
		    $value = nxs_license_updatetheme($value, null);
			}
		}
		else
		{
			// already processed it
		}
	}

	return $value;
}
add_filter('site_transient_update_themes', 'nxs_license_checkupdate');

function nxs_license_updatetheme($value, $data)
{
	// the result should be stored in $template key,
	// not in the $theme key, otherwise people that use
	// custom theme folder names will get a notification
	// that a theme has a new version, but in the updater
	// they wont find anything
	
	// we by-pass the update mechanism of WP,
	// since that could result in time out issues,
	// and .maintenance mode troubles
	$template = get_template();
	$value -> response[$template] = $data;
	
	return $value;
}

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
		
		$hours = 9; // poll max 
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
	add_submenu_page("nxs_backend_overview", 'Restart', 'Restart', 'manage_options', 'nxs_admin_restart', 'nxs_license_restart_page_content', '', 81 );
}

function plugin_admin_init()
{
	//All callbacks must be valid names of functions, even if provided functions are blank
	add_settings_section('nxs_section_license', 'Registration', 'nxs_section_license_callback', 'nxs_section_license_type');
	add_settings_field('nxs_register', 'Register', 'nxs_licenseregister_callback', 'nxs_section_license_type', 'nxs_section_license');
	
	//add_settings_field('nxs_licensekey', 'Serial number', 'nxs_licensekey_callback', 'nxs_section_license_type', 'nxs_section_license');
	
		
	add_settings_section('nxs_section_update', 'Updates', 'nxs_section_update_callback', 'nxs_section_update_type');
	
	if ($_REQUEST["nxsmsg"] == "registeredsuccesfully")
	{
		add_action('admin_notices', 'nxs_license_notifyregistersuccess');
	}
	else if ($_REQUEST["nxsmsg"] == "unregisteredsuccesfully")
	{
		add_action('admin_notices', 'nxs_license_notifyunregistersuccess');
	}
	
	$licensekey = esc_attr(get_option('nxs_licensekey'));
	if ($licensekey == "")
	{
		add_action('admin_notices', 'nxs_license_notifynolicense');
	}
}
add_action( 'admin_init', 'plugin_admin_init' );

function nxs_section_license_callback()
{
}

function nxs_section_update_callback()
{
	if (!function_exists("wp_get_theme"))
	{
		add_action('admin_notices', 'nxs_license_notifyrequireswpupdate');
		return;
	}
	
	$theme = wp_get_theme();

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
		
		
		if ($themeupdate["result"] == "OK")
		{
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
					if ($themeupdate["helphtml"] != "")
					{
						$helphtml = nxs_license_getoutputhelphtml($themeupdate);
						echo $helphtml;
					}
					else
					{
						echo "A new version (" . $themeupdate["new_version"] . ") is available";
						echo "<!-- " . $themeupdate["new_version"] . " vs " . $theme->version . " -->";
						?>
						<p>
							<a class="button-primary" href="<?php echo $updateurl; ?>">Update theme</a>
				  	</p>
						<?php
					}
				}
				else
				{
					if ($themeupdate["helphtml"] != "")
					{
						$helphtml = nxs_license_getoutputhelphtml($themeupdate);
						echo $helphtml;
					}
					else
					{
						echo "Your theme is up to date";
						// var_dump($themeupdate);
						echo "<!-- latest: " . version_compare($themeupdate["new_version"]) . " -->";
					}
				}
			}
			else
			{
				echo "Your theme is up to date <!-- (2) -->";
			}
		}
		else if ($themeupdate["result"] == "ALTFLOW")
		{
			nxs_license_handlealtflow($themeupdate);
		}
		else
		{
			//
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

function nxs_license_restart_page_content()
{
	$iswiped = false;
	
	$nxsaction = $_REQUEST["nxsaction"];
	if ($nxsaction == "wipesite")
	{
		$valid = true;
		
		// check nonce
		if (! isset( $_POST['wipenonce']) || ! wp_verify_nonce( $_POST['wipenonce'], 'nxswipesite'))
		{
   		echo "Invalid noncetext<br />";
			$valid = false;
   	}
   	
   	$confirmtext = $_REQUEST["confirmtext"];
   	if ($confirmtext != "DELETE")
   	{
   		echo "Invalid confirmation text<br />";
   		$valid = false;
   	}
   	
   	//
   	
   	if ($valid)
   	{
   		// check 
			
			nxs_site_wipe();
			
			$url = nxs_geturlcurrentpage();
			$url = nxs_addqueryparametertourl_v2($url, "nxsaction", "wipesitefinished", true, true);
			?>
			<script>
				window.location = '<?php echo $url; ?>';
			</script>
			<?php
			wp_redirect($url, 301);
			die();
		}
		else
		{
			//echo "Invalid request";
		}
	}
	else if ($nxsaction == "wipesitefinished")
	{
		$iswiped = true;
	}
	
	if ($iswiped)
	{
		?>
		 <div class="wrap">
	    <h2>Restart</h2>
	    <p>
	    	All data was succesfully wiped from your system.
	    </p>
	   </div>
	  <?php
	}
	else
	{
		?>
	  <div class="wrap">
	    <h2>Restart (for system admins only!)</h2>
	    <p>
	    	If for whatever reason you are totally not happy with the content on your site, 
	    	you might want to cleanup the entire site. <br />
	    	To avoid having to delete every post,
	    	page, media items, etc per individual item, we have added a feature in this theme
	    	to wipe the entire site with basically one click. <br />
	    	NOTE that this is a dangerous 
	    	operation as there is no way back. So proceed with caution!<br />
	    	<br />
	    	We have a created a video that explains in more detail why you would want
	    	to delete your entire site (and start from scratch).<br />
	    	Its defined at the section 'How to remove all WordPress content and start from scratch'
	    	on the support page for <a target='_blank' href='http://nexusthemes.com/support/how-to-install-a-wordpress-theme/'>activating your WordPress theme</a>.
	    </p>
	    <p>
				<b>Be sure to make a backup, and proceed only if you know what you are doing!</b><br />
				<br />
				To continue erasing your entire site, enter the text <b>DELETE</b> (capitalized) in the field below and push the button.<br />
				Clicking the button below will wipe ALL information from your site; all images, all posts, pages, etc.etc. This can NOT be reverted.<br />
				<form method="POST">
					<?php wp_nonce_field('nxswipesite','wipenonce'); ?>
					<input type='hidden' name='nxsaction' value='wipesite' />
					Confirmation text: <input type='text' name='confirmtext' /><br /><br />
					<input class='button button-primary' type='submit' value='Wipe all content (irreversable)' />
				</form>
			</p>
		</div>
		<?php
	}
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

function nxs_license_getoutputhelphtml($response_data)
{
	$helphtml = $response_data["helphtml"];
	
	if (is_multisite())
	{
		$updateurl = network_admin_url('themes.php');
	}
	else
	{
		$updateurl = admin_url('themes.php');
	}
		
	$lookup = array
	(
		"{{nxslicenseurl}}" => admin_url('admin.php?page=nxs_admin_license'),
		"{{nxsupdateurl}}" => $updateurl,
	);
	
	foreach ($lookup as $key => $val)
	{
		$helphtml = str_replace($key, $val, $helphtml);
	}
	
	echo $helphtml;
}

function nxs_licenseresetkey()
{
	update_option('nxs_licensekey', "");
}

function nxs_license_handlealtflow($response_data)
{ 		
	if ($response_data["keeplicense"] == "true" || $response_data["keeplicense"] == true)
	{
		// 
	}
	else
	{
		// by default the alternativeflow will wipe the licensekey
		nxs_licenseresetkey();
	}
	
	//var_dump($response_data);
	if ($response_data["helphtml"] != "")
	{
		$helphtml = nxs_license_getoutputhelphtml($response_data);
		echo $helphtml;
	}
	else
	{
		?>
		<p>
			Operation failed. No help info supplied. Please <a target='_blank' href='mailto:info@nexusthemes.com'>contact us</a> at <a target='_blank' href='mailto:info@nexusthemes.com'>info@nexusthemes.com</a>.
			<!-- <?php echo $response_data["altflowid"]; ?> -->
		</p>
		<?php
	}
}

function nxs_license_getnolicensetip_invoke()
{
	$response_data = get_transient("nxs_nolicensetip");
	if ($response_data == false || $_REQUEST["nxs_nolicensetip_cache"] == "true")
	{
		// no data, or expired data
		
		$site = nxs_geturl_home();
		$themeobject = wp_get_theme();
		$version = $themeobject->version;
		$theme = $themeobject->name;
		
		if (function_exists('nxs_theme_getmeta'))
		{
			$meta = nxs_theme_getmeta();
			$version = $meta["version"];
			$theme = $meta["id"];
		}
	
		$serviceparams = array
		(
			'timeout' => 15,
			'sslverify' => false,
			'body' => array
			(
				"nxs_license_action" => "getnolicensetip",
				"version" => $version,
				"theme" => $theme,
				"ordernr" => $ordernr,
				"site" => $site
			)
		);
		
		$site = home_url();
		$url = nxs_license_getlicenseserverurl("tipnolicense");
		$response = wp_remote_post($url, $serviceparams);
		
		$successful = true;
	
	  // make sure the response was successful
	  if ( is_wp_error( $response )) 
	  {
	  	$successful = false;
	  	//var_dump($response);
	  }
	  
	  $body = wp_remote_retrieve_body($response); 
		$response_data = json_decode($body, true);
		
		if ($successful)
	  {
  		$hours = 10; // poll max 
	 		set_transient("nxs_nolicensetip", $response_data, 60 * 60 * $hours);
	  }
	  else
	  {
	  	$hours = 11; // poll max 
			set_transient("nxs_nolicensetip", "", 60 * 60 * $hours);
	  }
	}
	
	if ($response_data != false && $response_data != "")
	{
		nxs_license_handlealtflow($response_data);
	}
	else
	{
		// ignore
	}
}

function nxs_licenseregister_invoke()
{
	if (!function_exists("wp_get_theme"))
	{
		add_action('admin_notices', 'nxs_license_notifyrequireswpupdate');
		return;
	}
	
	$ordernr = $_REQUEST["nxs_ordernr"];
	
	$site = nxs_geturl_home();
	$themeobject = wp_get_theme();
	
	$parent = $themeobject->parent();
	if ($parent != null)
	{
		$themeobject = $parent;
	}
	
	$version = $themeobject->version;
	$theme = $themeobject->name;

	if (function_exists('nxs_theme_getmeta'))
	{
		$meta = nxs_theme_getmeta();
		$version = $meta["version"];
		$theme = $meta["id"];
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
	$url = nxs_license_getlicenseserverurl("register");
	$response = wp_remote_post($url, $serviceparams);
	
	$successful = true;

  // make sure the response was successful
  if ( is_wp_error( $response )) 
  {
  	$successful = false;
  	
  	$firstmsg = $response->get_error_message();
  	if ($firstmsg == "couldn't connect to host")
  	{
  		echo "It looks like your host cannot connect to $url<br />";
  		echo "To solve this problem:<br />";
  		echo "Contact your host to ensure they are not blocking access to our servers<br /><br />";
  	}
  	else
  	{
	  	var_dump($url);
  		var_dump($response);	
  	}
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
  		$url = nxs_addqueryparametertourl_v2($url, "nxsmsg", "registeredsuccesfully", true, true);
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
 			nxs_license_handlealtflow($response_data);
  	}
  	else
  	{
  		update_option('nxs_licensekey', "");
  		
  		if (nxs_stringcontains($response["body"], "Access Denied"))
  		{
  			?>
  			<p>
  				Unable to reach the license server at<br />
  				<?php echo $url; ?><br />
  				The most likely explanation why this happens, is that your host blocks
  				access to our server. Contact your hosting company and ask them to 
  				verify if they block access to servers, and if they do, whether they
  				can enable ('white-list') our server.
  			</p>
  			<?php
  		}
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
			//
			nxs_license_getnolicensetip_invoke();
			
			$url = nxs_geturlcurrentpage();
			$url = nxs_addqueryparametertourl_v2($url, "nxs_license_register", "true", true, true);
			$noncedurl = wp_nonce_url($url, 'register');
			$site = nxs_geturl_home();
			?>
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
			</p>
			<p>
				&nbsp;
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
				License found :) <!-- <?php echo $licensekey; ?> -->
			</p>
			<p>
				&nbsp;
			</p>
			<input name="nxs_license_unregister" type="hidden" value="true" />
			<input name="nxsmsg" type="hidden" value="unregisteredsuccesfully" />
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