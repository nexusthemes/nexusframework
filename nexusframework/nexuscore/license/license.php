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

add_action('admin_menu', 'nxs_license_addadminpages', 11);
function nxs_license_addadminpages()
{
	add_submenu_page("nxs_backend_overview", 'Updates', 'Updates', 'switch_themes', 'nxs_admin_updates', 'nxs_admin_updates_page_content', '', 81 );
	add_submenu_page("nxs_backend_overview", 'Restart', 'Restart', 'switch_themes', 'nxs_admin_restart', 'nxs_license_restart_page_content', '', 81 );
	add_submenu_page("nxs_backend_overview", 'ThemeSwitch', 'ThemeSwitch', 'switch_themes', 'nxs_admin_themeswitch', 'nxs_license_themeswitch_page_content', '', 81 );
}

function plugin_admin_init()
{
}
add_action( 'admin_init', 'plugin_admin_init' );

function nxs_admin_updates_page_content()
{
	$thememeta = nxs_theme_getmeta();
	$version = $thememeta["version"];
	
	$latestversionurl = "https://s3.amazonaws.com/devices.nexusthemes.com/!api/latestthemeversion.txt";
	$args = array
	(
		"url" => $latestversionurl,
	);
	$latestversion = nxs_geturlcontents($args);

	if ($version == $latestversion)
	{
		?>
		<div class="wrap">
    <h2>Updates</h2>
    <p>
    	Your site is using the latest version of the theme.
    </p>
    <?php
	}
	else
	{
		?>
		<div class="wrap">
	    <h2>Updates</h2>
	    <p>
	    	A new version of the theme has been released. Use the portal (link: <a target='_blank' href='https://my.nexusthemes.com/'>https://my.nexusthemes.com/</a>) to update.<br />
	    </p>
	  </div>
	  <?php
	}
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
	    	on the support page for <a target='_blank' href='https://nexusthemes.com/support/how-to-install-a-wordpress-theme/'>provisioning your WordPress theme</a>.
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

?>