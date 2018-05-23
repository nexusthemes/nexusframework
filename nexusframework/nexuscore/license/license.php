<?php

function nxs_site_wipepostsinposttype($posttype)
{
	if (!is_super_admin())
	{
		echo "no super admin rights!";
		die();
	}
	
	global $wpdb;
	
	// delete metadata
	$p = $wpdb->prepare(
	"DELETE FROM " . $wpdb->prefix . "postmeta where post_id in (SELECT p.id FROM " . $wpdb->prefix . "posts p where p.post_type='%s')", $posttype
	);
	
	$r = $wpdb->query($p);
	//var_dump($r);
	//die();	
	
	// delete posts
	$p = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "posts WHERE post_type='%s'", $posttype);
	$r = $wpdb->query($p);	
}

function nxs_site_wipe()
{
	if (!is_super_admin())
	{
		echo "no super admin rights!";
		die();
	}
	
	//nxs_ob_start();
	
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
}

function nxs_license_notifyregistersuccess()
{
  ?>
  <div class="updated">
    <p>Succesfully registered your license</p>
  </div>
  <?php
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
	    	You are currently missing out on important features since you site is not yet (or no longer) connected
	    	to a valid license. Have a valid license? <a href='<?php echo $url;?>'>Enter the license here</a>. <a href='<?php echo $url;?>'>Read more</a>
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

// When a theme gets deleted, WordPress fires the 'delete_site_transient_update_themes' action before it deletes the transient.
// add_filter('delete_site_transient_update_themes', 'nxs_license_clearupdatetransient');

function nxs_license_load_themes()
{
	if ( isset( $_GET['activated'] ) ) 
	{ 
		// executed when the theme is activated by the user/system
		nxs_license_clearupdatetransient();
	}
}
add_action('load-themes.php', 'nxs_license_load_themes');

function nxs_license_site_transient_update_themes($result)
{
	if (!function_exists("wp_get_theme"))
	{
		add_action('admin_notices', 'nxs_license_notifyrequireswpupdate');
		return $result;
	}
	
	$themeobject = wp_get_theme();
	$parent = $themeobject->parent();
	if ($parent != null)
	{
		$themeobject = $parent;
	}
	$theme = $themeobject->name;
	$currentversion = $themeobject->version;
	
	// ---

	$shouldcheck = true;

	$nxs_themeupdate = get_transient("nxs_themeupdate");
	
	$enforcecheck = false;
	
	$url = nxs_geturlcurrentpage();
	if (nxs_stringcontains($url, "nxs_admin_update"))
	{
		// always refresh if the user accesses the admin_update page
		$enforcecheck = true;
	}
	
	// dont do version check on own infra
	if ($shouldcheck === true)
	{
		if ($licensekey == "fromowninfra")
		{
			// dont poll when we host ourselves as we do the updates in another way there
			$shouldcheck = false;
			$enforcecheck = false;
		}
	}
	
	// dont do verion check if we already know there's an update available
	if ($shouldcheck === true)
	{
		$themeupdate = get_transient("nxs_themeupdate");
		if ($themeupdate["nxs_updates"] == "yes")
		{
			// no need to proceed to poll, we already know an update is available
			$shouldcheck = false;
		}
	}
	
	if ($shouldcheck === true || $enforcecheck === true)
	{
		// todo: add transient here too to avoid polling too often
		
		// pull latest version from proxy first to reduce stressing the license server
		// TODO: use CNAME instead would be better
		$proxyurl = "https://s3.amazonaws.com/devices.nexusthemes.com/!api/latestthemeversion.txt";
		$latestversion = nxs_geturlcontents(array("url" => $proxyurl));
		if ($latestversion == "")
		{
			$shouldcheck = false;
		}
		else
		{
			// its set, compare version with current
			
			if (version_compare($latestversion, $currentversion , "<="))
			{
				// nothing to check
				$shouldcheck = false;
			}
			else
			{
				// an update is available, store that fact so it will be visible in the front end
				set_transient("nxs_themeupdate", array("nxs_updates" => "yes"), 60 * 60 * 24 * 7);
				
				// dont poll the license server (useless)
				$shouldcheck = false;
			}
		}
	}
	
	//
	//
	//
	
	return $result;
}
add_filter('site_transient_update_themes', 'nxs_license_site_transient_update_themes', 10, 1);

function nxs_license_after_setup_theme()
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
		
		$hours = 24; // poll max 
		set_transient("nxs_themeupdater_freq", "cached", 60 * 60 * $hours);
	}
}
add_action('after_setup_theme', 'nxs_license_after_setup_theme');

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
}

add_action('admin_menu', 'nxs_license_addadminpages', 11);
function nxs_license_addadminpages()
{
	add_submenu_page("nxs_backend_overview", 'License', 'License', 'switch_themes', 'nxs_admin_license', 'nxs_license_theme_license_page_content', '', 81 );
	add_submenu_page("nxs_backend_overview", 'Update', 'Update', 'switch_themes', 'nxs_admin_update', 'nxs_license_update_page_content', '', 81 );
	add_submenu_page("nxs_backend_overview", 'Restart', 'Restart', 'switch_themes', 'nxs_admin_restart', 'nxs_license_restart_page_content', '', 81 );
	add_submenu_page("nxs_backend_overview", 'ThemeSwitch', 'ThemeSwitch', 'switch_themes', 'nxs_admin_themeswitch', 'nxs_license_themeswitch_page_content', '', 81 );
	add_submenu_page("nxs_backend_overview", 'All Themes', 'All Themes', 'switch_themes', 'nxs_admin_allthemes', 'nxs_redirect_to_all_themes', '', 81 );
	add_submenu_page("nxs_backend_overview", 'Backup and Restore', 'Backup &amp; Restore', 'switch_themes', 'nxs_admin_backup_and_restore', 'nxs_redirect_to_backup_and_restore', '', 81 );
}

function plugin_admin_init()
{
	//All callbacks must be valid names of functions, even if provided functions are blank
	add_settings_section('nxs_section_license', 'Registration', 'nxs_section_license_callback', 'nxs_section_license_type');
	add_settings_field('nxs_register', 'Register', 'nxs_licenseregister_callback', 'nxs_section_license_type', 'nxs_section_license');
	add_settings_section('nxs_section_update', 'Updates', 'nxs_section_update_callback', 'nxs_section_update_type');
	
	if ($_REQUEST["nxsmsg"] == "registeredsuccesfully")
	{
		add_action('admin_notices', 'nxs_license_notifyregistersuccess');
	}
	else if ($_REQUEST["nxsmsg"] == "unregisteredsuccesfully")
	{
		add_action('admin_notices', 'nxs_license_notifyunregistersuccess');
	}
	
	$licensekey = nxs_license_getlicensekey();
	if ($licensekey == "")
	{
		add_action('admin_notices', 'nxs_license_notifynolicense');
	}
}
add_action( 'admin_init', 'plugin_admin_init' );

function nxs_license_getlicensekey()
{
	$result = esc_attr(get_option('nxs_licensekey'));
	$result = apply_filters("nxs_f_licensekey", $result);
	
	return $result;
}

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
		return;
	}
	
	// invoke
	if (!function_exists("wp_get_theme"))
	{
		add_action('admin_notices', 'nxs_license_notifyrequireswpupdate');
		return;
	}
	
	$nxs_licensenr = nxs_license_getlicensekey();
	
	$site = nxs_geturl_home();
	$themeobject = wp_get_theme();
	
	$parent = $themeobject->parent();
	if ($parent != null)
	{
		$themeobject = $parent;
	}
	
	$version = $themeobject->version;
	$theme = $themeobject->name;

	//
	
	$shouldcheck = false;
	$proxyurl = "https://s3.amazonaws.com/devices.nexusthemes.com/!api/latestthemeversion.txt";
	$latestversion = nxs_geturlcontents(array("url" => $proxyurl));
	if ($latestversion == "")
	{
		$shouldcheck = false;
	}
	else
	{
		// its set, compare version with current
		
		if (version_compare($latestversion, $version , "<="))
		{
			// nothing to check
			$shouldcheck = false;
		}
		else
		{
			// an update is available, store that fact so it will be visible in the front end
			set_transient("nxs_themeupdate", array("nxs_updates" => "yes"), 60 * 60 * 24 * 7);
			
			// dont poll the license server (useless)
			$shouldcheck = true;
		}
	}
	
	if ($shouldcheck)
	{
		//
	
		if (function_exists('nxs_theme_getmeta'))
		{
			$meta = nxs_theme_getmeta();
			$version = $meta["version"];
			$theme = $meta["id"];
		}
		
		$url = nxs_license_getlicenseserverurl("get_version");
		$url = nxs_addqueryparametertourl_v2($url, "nxs_license_action", "get_version", true, true);
		$url = nxs_addqueryparametertourl_v2($url, "version", $version, true, true);
		$url = nxs_addqueryparametertourl_v2($url, "theme", $theme, true, true);
		$url = nxs_addqueryparametertourl_v2($url, "ordernr", $nxs_licensenr, true, true);	// obsolete
		$url = nxs_addqueryparametertourl_v2($url, "licensekey", $nxs_licensenr, true, true);
		$url = nxs_addqueryparametertourl_v2($url, "site", $site, true, true);
		
		$body = nxs_geturlcontents(array("url" => $url));
		$themeupdate = json_decode($body, true);
		// END INVOKE
		
		if (is_multisite())
		{
			$updateurl = network_admin_url('themes.php');
		}
		else
		{
			$updateurl = admin_url('themes.php');
		}
	
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
	else
	{
		echo "Nice, you ARE using the latest version! <!-- ($latestversion, $version) -->";
		set_transient("nxs_themeupdate", array());
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
		</form>
		
		<p>
			&nbsp;
		</p>
		<p>
			Frequently asked questions:
		</p>
		<ul>
			<li><a href='https://www.wpsupporthelp.com/answer/how-can-i-see-what-is-being-updated-to-a-theme-when-it-indicates-1081/' target='_blank'>What has changed in the new version?</a></li>
			<li><a href='https://www.wpsupporthelp.com/answer/is-it-safe-to-update-the-wordpress-theme-how-can-i-ensure-an-up-1410/' target='_blank'>Is it safe to update the WordPress theme?</a></li>
			<li><a href='https://www.wpsupporthelp.com/wordpress-questions/updates-update-upgrade-wordpress-questions-27/' target='_blank'>All update related questions</a></li>
		</ul>
		
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
			
			do_action("nxs_wiped_manual");
			
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
	    	on the support page for <a target='_blank' href='https://nexusthemes.com/support/how-to-install-a-wordpress-theme/'>activating your WordPress theme</a>.
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
	if ($response_data["altflowid"] == "NOUPDATE")
	{
		echo "You are NOW using the latest version :)";
		
		//echo "about to clear update transient";
		nxs_license_clearupdatetransient();
		return;
	}
	
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
			Operation failed. No help info supplied. Please <a target='_blank' href='mailto:support@nexusthemes.com'>contact us</a> at <a target='_blank' href='mailto:support@nexusthemes.com'>support@nexusthemes.com</a>.
			<!-- <?php echo $response_data["altflowid"]; ?> -->
		</p>
		<?php
	}
}

function nxs_license_getnolicensetip_invoke()
{
	echo "No license found";
}

function nxs_license_getlicenseserverurl($purpose)
{
	//error_log("invoke; nxs_license_getlicenseserverurl; " . $purpose);
	return "https://license1802.nexusthemes.com/";
}

function nxs_licenseregister_invoke()
{
	if (!function_exists("wp_get_theme"))
	{
		add_action('admin_notices', 'nxs_license_notifyrequireswpupdate');
		return;
	}
	
	$nxs_licensenr = $_REQUEST["nxs_licensenr"];
	$nxs_explicitconsent = $_POST["nxs_explicitconsent"];
	
	if ($nxs_explicitconsent == "")
	{
		update_option('nxs_licensekey', "");
		
		?>
		<p>
			Without your explicit consent the license cannot be registered<br />
		</p>
		<p>
			&nbsp;
		</p>
		<p>
  		<a class='button-primary' href=''>Reload the page</a>
  	</p>
		<?php
	}
	else
	{
		$clientip = $_SERVER['REMOTE_ADDR'];
		
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
		
		$url = nxs_license_getlicenseserverurl("register");
		$url = nxs_addqueryparametertourl_v2($url, "nxs_license_action", "register", true, true);
		$url = nxs_addqueryparametertourl_v2($url, "version", $version, true, true);
		$url = nxs_addqueryparametertourl_v2($url, "theme", $theme, true, true);
		$url = nxs_addqueryparametertourl_v2($url, "ordernr", $nxs_licensenr, true, true);	// obsolete
		$url = nxs_addqueryparametertourl_v2($url, "licensenr", $nxs_licensenr, true, true);
		$url = nxs_addqueryparametertourl_v2($url, "site", $site, true, true);
		$url = nxs_addqueryparametertourl_v2($url, "nxs_explicitconsent", $nxs_explicitconsent, true, true);
		$url = nxs_addqueryparametertourl_v2($url, "clientip", $clientip, true, true);
		
		$body = nxs_geturlcontents(array("url" => $url));
		
		$response_data = json_decode($body, true);
		
		if ($response_data["result"] == "OK")
		{
	  	$nxs_licensekey = $response_data["nxs_licensekey"];
			// store serial
			update_option('nxs_licensekey', $nxs_licensekey);
			
			// check for updates
			$dummy = new stdClass();
			nxs_license_site_transient_update_themes($dummy);
	
			// reload current page
			$url = nxs_geturlcurrentpage();
			$url = nxs_addqueryparametertourl_v2($url, "nxsmsg", "registeredsuccesfully", true, true);
			?>
			<script>
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
			
			if (nxs_stringcontains($body, "Access Denied"))
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
				<?php
				echo "<!-- ";
				echo $url;
				echo "\r\n";
				echo "\r\n";
				echo $body;
				echo "--> ";
				?>
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
}

function nxs_licenseregister_callback()
{
	$licensekey = nxs_license_getlicensekey();
	if ($licensekey == "")
	{
		if ($_REQUEST["nxs_license_register"] == "true")
		{
			nxs_licenseregister_invoke();
		}
		else
		{
			$terms_url = "https://nexusthemes.com/terms-and-conditions-1006/";
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
				License key
			</p>
			<input type='text' name='nxs_licensenr' placeholder='V3.nexus.x.x.x' onkeydown='jQuery("#nxsregproceed").show();' onchange='jQuery("#nxsregproceed").show();' value='' style='width:30%' />
			<p>
				&nbsp;
			</p>
			<input type='checkbox' id='nxs_explicitconsent' name='nxs_explicitconsent' />
			<label for="nxs_terms">
				I hereby acknowledge that I have read and understood the terms and conditions<br /> 
				as provided in the 'Terms and Conditions' as available at <a target='_blank' href='<?php echo $terms_url; ?>'><?php echo $terms_url; ?></a><br />
				and I agree to all of the terms.
			</label>
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
			<p>
				Frequently asked questions:
			</p>
			<ul>
				<li><a href='https://www.wpsupporthelp.com/answer/can-i-register-the-license-on-a-dev-environment-too-i-plan-to-b-677/' target='_blank'>Can I use the license for a development, staging or test environment too?</a></li>
				<li><a href='https://www.wpsupporthelp.com/wordpress-questions/license-wordpress-questions-82/' target='_blank'>All license related questions</a></li>
			</ul>
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
  		<script>
  			var url = '<?php echo $url; ?>';
  			window.location = url;
  		</script>
			<?php
			die();
		}
		else
		{
			$licensekey = nxs_license_getlicensekey();
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
	
  $licensekey = nxs_license_getlicensekey();
	echo "<input type='text' name='nxs_licensekey' onkeydown='jQuery(\"#submit\").show();' onchange='jQuery(\"#submit\").show();' value='{$licensekey}' style='width:30%' />";
	?>
  <p class='submit'>
  	<input name='submit' type='submit' id='submit' style='display: none;' class='button-primary' value='<?php _e("Save Changes") ?>' />
 	</p>

	<?php
}

function nxs_license_themeswitch_page_content()
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
   		// reset the globalid of the homepage to some other value
   		if (nxs_hassitemeta())
   		{
   			$postid = nxs_gethomepageid();
   			nxs_reset_globalid($postid);
   			
   			$postids = nxs_get_postidsaccordingtoglobalid("activesitesettings");
   			$postid = $postids[0];
   			nxs_reset_globalid($postid);
   			
   			global $nxs_gl_cache_sitemeta;
   			$nxs_gl_cache_sitemeta = null;
   		}
			
			// Remove all headers, subheaders, sidebars, subfooters, footers, menus and page decorators from the system.
			nxs_site_wipepostsinposttype("nxs_settings");
			nxs_site_wipepostsinposttype("nxs_header");
			nxs_site_wipepostsinposttype("nxs_subheader");
			nxs_site_wipepostsinposttype("nxs_sidebar");
			nxs_site_wipepostsinposttype("nxs_subfooter");
			nxs_site_wipepostsinposttype("nxs_footer");
			nxs_site_wipepostsinposttype("nxs_menu");
			nxs_site_wipepostsinposttype("nxs_admin");
			
			nxs_site_wipepostsinposttype("nxs_systemlog");
			nxs_site_wipepostsinposttype("nxs_templatepart");
			nxs_site_wipepostsinposttype("nxs_busrulesset");
			
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
	    	All theme specific elements were succesfully wiped from your system.
	    </p>
	   </div>
	  <?php
	}
	else
	{
		?>
	  <div class="wrap">
	    <h2>Theme Switch (for system admins only!)</h2>
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

function nxs_redirect_to_absolute_url($redirect_url)
{
	echo "<p>Redirecting...</p>";
	echo "<p>If redirecting takes to long, please click <a href='" . $redirect_url . "'>here</a></p>";
	echo "<script> window.location.href = '" . $redirect_url . "'; </script>";
}

function nxs_redirect_to_all_themes()
{
	$redirect_url = 'https://nexusthemes.com/wordpress-themes-1002/';
	nxs_redirect_to_absolute_url($redirect_url);
}

function nxs_redirect_to_backup_and_restore()
{
	$redirect_url = 'https://www.wpsupporthelp.com/answer/could-you-explain-in-more-detail-what-the-automated-backup-featu-1562/';
	nxs_redirect_to_absolute_url($redirect_url);
}

?>