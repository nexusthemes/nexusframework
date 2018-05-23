<?php
	function nxs_failover_themespage_activated_getvalue($args)
	{
		$result = array();
				
		$expirationtimeinseconds = 1;
		
		$siteurl = nxs_geturl_home();
		
		nxs_ob_start();
		?>
		<div class='update-nag'>
			<h1><?php nxs_l18n_e("Congratulations[nxs:failover]", "nxs_td"); ?></h1>
			<p>
				<a href='<?php echo $data["site_url"]; ?>'><?php echo $siteurl; ?></a> <?php nxs_l18n_e("is now freely powered by Nexus Themes[nxs:failover]", "nxs_td"); ?><br />
				WordPress themes reinvented | Forever Free | Nexus Themes<br />
				<?php nxs_l18n_e("By using our theme you agree to our[nxs:failover]", "nxs_td"); ?> <a target='_blank' href='https://nexusthemes.com'><?php nxs_l18n_e("terms and conditions[nxs:failover]", "nxs_td"); ?></a><br />
			</p>
		</div>
		<?php

		$html = nxs_ob_get_contents();
		nxs_ob_end_clean();

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
		$themeswitch_url = admin_url('admin.php?page=nxs_admin_themeswitch');
		$allthemes_url = admin_url('admin.php?page=nxs_admin_allthemes');
		$backuprestore_url = admin_url('admin.php?page=nxs_admin_backup_and_restore');
		
		$showcptsurl = nxs_geturlcurrentpage();
		$showcptsurl = nxs_addqueryparametertourl_v2($showcptsurl, "shownexustypesinbackend", "true", true, true);
		
		//
		// support
		//
		nxs_ob_start();
		?>
		<p>
			<a href='https://www.wpsupporthelp.com/' target='_blank'>https://www.wpsupporthelp.com/</a><br />
			<iframe style="width: 100%; height: 70vh;" src="https://www.wpsupporthelp.com/answer/how-to-find-the-best-answer-to-my-wordpress-questions-1624/" />
		</p>
		<?php
		$result["support_htmlid"] = "support";
		$result["support_title"] = nxs_l18n__("Support", "nxs_td");
		$result["support_html"] = nxs_ob_get_contents();
		nxs_ob_end_clean();
		
		//
		// logo
		//
		nxs_ob_start();
		?>
		<a target='_blank' href='https://nexusthemes.com'>
			<img style='width: 200px;' src='<?php echo nxs_getframeworkurl() . '/images/logo.png'; ?>' />
		</a>
		<?php
		$result["logo_htmlid"] = "logo";
		$result["logo_title"] = "&nbsp;";
		$result["logo_html"] = nxs_ob_get_contents();
		nxs_ob_end_clean();
		
		$result["transientduration"] = $expirationtimeinseconds;
		
		return $result;
	}
?>