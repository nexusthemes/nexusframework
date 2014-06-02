<?php
	function nxs_failover_themespage_activated_getvalue($args)
	{
		$result = array();
				
		$expirationtimeinseconds = 1;
		
		$siteurl = nxs_geturl_home();
		
		ob_start();
		?>
		<div class='update-nag'>
			<h1><?php nxs_l18n_e("Congratulations[nxs:failover]", "nxs_td"); ?></h1>
			<p>
				<a href='<?php echo $data["site_url"]; ?>'><?php echo $siteurl; ?></a> <?php nxs_l18n_e("is now freely powered by Nexus Themes[nxs:failover]", "nxs_td"); ?><br />
				WordPress themes reinvented | Forever Free | Nexus Themes<br />
				<?php nxs_l18n_e("By using our theme you agree to our[nxs:failover]", "nxs_td"); ?> <a target='_blank' href='http://www.nexusthemes.com'><?php nxs_l18n_e("terms and conditions[nxs:failover]", "nxs_td"); ?></a><br />
			</p>
		</div>
		<?php

		$html = ob_get_contents();
		ob_end_clean();

		$result["html"] = $html;
		$result["transientduration"] = $expirationtimeinseconds;
		
		return $result;
	}
	
	function nxs_failover_themespage_overview_getvalue($args)
	{
		$result = array();
			
		$expirationtimeinseconds = 1;
		
		$result["leftboxes"] = "support;";
		$result["rightboxes"] = "logo;likeus;";
		
		$license_url = admin_url('admin.php?page=nxs_admin_license');
		$update_url = admin_url('admin.php?page=nxs_admin_update');
		$restart_url = admin_url('admin.php?page=nxs_admin_restart');
		
		$showcptsurl = nxs_geturlcurrentpage();
		$showcptsurl = nxs_addqueryparametertourl_v2($showcptsurl, "shownexustypesinbackend", "true", true, true);
		
		//
		// support
		//
		ob_start();
		?>
		<p>
			<ul>
				<li>
					<a href='http://nexusthemes.com/support'>Support</a>
					<ul style='padding-left: 20px;'>
						<li><a target='_blank' href='http://nexusthemes.com/support/getting-started/'>Getting started</a></li>
						<li><a target='_blank' href='http://nexusthemes.com/support/changing-and-adding-content/'>Changing &amp; adding content</a></li>
						<li><a target='_blank' href='http://nexusthemes.com/support/changing-and-adding-content/'>Building blocks</a></li>
						<li><a target='_blank' href='http://nexusthemes.com/support/faq/'>FAQ</a></li>
					</ul>
				</li>
				<li><a href='<?php echo $license_url; ?>'>License</a></li>
				<li><a href='<?php echo $update_url; ?>'>Update</a></li>
			</ul>
			<h3 style='padding-left: 0px;'>Links below are for system admins only</h3>
			<ul>
				<li><a href='<?php echo $restart_url; ?>' style='color: red;'>Restart</a></li>
			</ul>
		</p>
		<?php
		$result["support_htmlid"] = "support";
		$result["support_title"] = nxs_l18n__("Support", "nxs_td");
		$result["support_html"] = ob_get_contents();
		ob_end_clean();
		
		//
		// logo
		//
		ob_start();
		?>
		<a target='_blank' href='http://nexusthemes.com'>
			<img style='width: 200px;' src='<?php echo nxs_getframeworkurl() . '/images/logo.png'; ?>' />
		</a>
		<?php
		$result["logo_htmlid"] = "logo";
		$result["logo_title"] = "&nbsp;";
		$result["logo_html"] = ob_get_contents();
		ob_end_clean();
		
		$result["transientduration"] = $expirationtimeinseconds;
		
		return $result;
	}
?>