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
		
		$wipeurl = nxs_geturl_home();
		$wipeurl = nxs_addqueryparametertourl_v2($wipeurl, "nxspatch", "patch20130610002_clear", true, true);
		
		$showcptsurl = nxs_geturlcurrentpage();
		$showcptsurl = nxs_addqueryparametertourl_v2($showcptsurl, "shownexustypesinbackend", "true", true, true);
		
		//
		// support
		//
		ob_start();
		?>
		<p><h1>Support</h1></p>
		<?php nxs_l18n_e("<p><a target='_blank' href='http://nexusthemes.com'>Contact us</a></p>", "nxs_td"); ?>
		<p><hr /></p>
		<p><h1>Links below are for system admins only</h1></p>
		<p><a href='<?php echo $showcptsurl; ?>'>Display Nexus custom post types</a></p>
		<p><a target='_blank' href='<?php echo $wipeurl; ?>'>Wipe environment (irreversible!)</a></p>
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
		
		/*
		//
		// like us
		//
		ob_start();
		?>
		<?php nxs_l18n_e("<p><a target='_blank' href='http://nexusthemes.com'>Contact us</a></p>", "nxs_td"); ?>
		<p>
			This 3 WordPress theme is primarily developed, maintained, supported and documented by <a href='http://www.nexusstudios.nl' target='_blank'>Nexus Studios</a> with a 
			lot of love & effort. Any kind of contribution would be highly appreciated. Thanks!
		</p>
		<ul>
			<li>
				<a href='http://en.wikipedia.org/wiki/Word_of_mouth' target='_blank'>Word of mouth</a>
			</li>
			<li>
				<a href='http://nexusthemes.com/?track=backendoverviewlikeus' target='_blank'>Visit the theme's homepage</a>
			</li>
			<!--
			todo: add link to github
			-->
		</ul>
		<?php
		$result["likeus_htmlid"] = "likeus";
		$result["likeus_title"] = nxs_l18n__("Help us help you!", "nxs_td");
		$result["likeus_html"] = ob_get_contents();
		ob_end_clean();
		*/
		
		$result["transientduration"] = $expirationtimeinseconds;
		
		return $result;
	}
?>