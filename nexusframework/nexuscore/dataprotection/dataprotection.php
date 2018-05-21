<?php

function nxs_dataprotection_getcanonicalactivity($component)
{
	$result = strtolower(preg_replace('/[^A-Za-z0-9]/', '_', $component)); // Removes special chars.
	return $result;
}

function nxs_dataprotection_buildrecursiveprotecteddata($args)
{
	$result = array();
		
	$rootactivity = $args["rootactivity"];
	if ($rootactivity == "")
	{
		nxs_webmethod_return_nack("error; nxs_dataprotection; rootactivity not set?");
	}
	
	$queue = array($rootactivity);
	$processed = array();
	
	while($currentactivity = array_pop($queue))
	{			
		$currentactivity = nxs_dataprotection_getcanonicalactivity($currentactivity);
		$processed[] = $currentactivity;
		
		// delegate the request to the theme for theme-specific gdpr meta
		$functionnametoinvoke = "nxs_dataprotection_{$currentactivity}_getprotecteddata";
		if (function_exists($functionnametoinvoke))
		{
			$currentresult = call_user_func($functionnametoinvoke, $args);
		
			// enqueue subactivities
			if (true)
			{
				$subactivities = $currentresult["subactivities"];
				foreach ($subactivities as $subactivity)
				{						
					$subactivity = nxs_dataprotection_getcanonicalactivity($subactivity);
					
					$result["activities"][$currentactivity]["includes"][] = $subactivity;
					
					if (!in_array($subactivity, $processed))
					{
						$queue[] = $subactivity;
					}
					else
					{
						// for some we already know this will happen; those will be ignored
						$expected_to_be_used_multiple_times = array("custom_widget_configuration");
						$was_expected_to_be_used_multiple_times = in_array($subactivity, $expected_to_be_used_multiple_times);
						if (!$was_expected_to_be_used_multiple_times)
						{ 
							$result["warning"][] = "activity was enqueued multiple times (only the first time it was processed); $subactivity";
						}
					}
				}
			}
							
			// store result of the current component
			$result["activities"][$currentactivity]["dataprocessingdeclarations"] = $currentresult["dataprocessingdeclarations"];
		}
		else
		{
			$result["errors"][] = "no valid gdprmeta implemented ( {$functionnametoinvoke} )";
		}
	}
	
	return $result;
}

function nxs_dataprotection_getlatestupdatebycontroller()
{
	if (nxs_hassitemeta())
	{
		$prefix = nxs_dataprotection_getprefix();		
		$sitemeta = nxs_getsitemeta();
		$result = $sitemeta["{$prefix}controllerlatestversion"];
	}
	
	if ($result == "")
	{
		$result = "none";
	}
	
	return $result;
}

function nxs_dataprotection_getcookieretentionindays()
{
	if (nxs_hassitemeta())
	{
		$prefix = nxs_dataprotection_getprefix();		
		$sitemeta = nxs_getsitemeta();
		$result = $sitemeta["{$prefix}cookieretention"];
	}
	
	if ($result == "")
	{
		$result = 30;
	}
	
	return $result;
}

function nxs_dataprotection_iscacheallowed()
{
	// todo: to be implemented
	return false;
}

function nxs_dataprotection_iscookiewallactivity($activity)
{
	$activity = nxs_dataprotection_getcanonicalactivity($activity);
	if ($activity == "nexusframework_usecookiewall")
	{
		$result = true;
	}
	else
	{
		$result = false;
	}
}

function nxs_dataprotection_isactivityonforuser($activity)
{
	$dataprotectiontype = nxs_dataprotection_getdataprotectiontype($activity);
	
	if (nxs_dataprotection_iscookiewallactivity($activity))
	{
		if ($dataprotectiontype == "")
		{
			$result = false;	// defaults to false
		}
		else if ($dataprotectiontype == "disabled")
		{
			$result = false;	// defaults to false
		}
		else if ($enabled_disabled_for_robots == "disabled")
		{
			if (nxs_browser_iscrawler())
			{
				$result = false;	// 
			}
			else
			{
				if (nxs_dataprotection_isexplicitconsentgiven($activity))
				{
					$result = false;	// 
				}
				else
				{
					$result = true;	// 
				}
			}
		}	
	}
	else
	{
		if ($dataprotectiontype == "")
		{
			$result = true;
		}
		else if ($dataprotectiontype == "enabled")
		{
			$result = true;
		}
		else if ($dataprotectiontype == "enabled_after_cookie_wall_consent_or_robot")
		{
			if (nxs_browser_iscrawler())
			{
				$result = true;
			}
			else
			{
				$usecookiewallactivity = "nexusframework:usecookiewall";
				if (nxs_dataprotection_isexplicitconsentgiven($usecookiewallactivity))
				{
					$result = true;
				}
				else
				{
					$result = false;
				}
			}
		}
		else if ($dataprotectiontype == "enabled_after_cookie_component_consent_or_robot")
		{
			if (nxs_browser_iscrawler())
			{
				$result = true;
			}
			else
			{
				if (nxs_dataprotection_isexplicitconsentgiven($activity))
				{
					$result = true;
				}
				else
				{
					$result = false;
				}			
			}
		}
		else if ($dataprotectiontype == "disabled")
		{
			$result = false;
		}
		else
		{
			nxs_webmethod_return_nack("unsupported dataprotectiontype; $dataprotectiontype");
		}
	}
	
	

	
	return $result;
}

function nxs_dataprotection_isexplicitconsentgiven($activity)
{
	
	// its not a bot, check if the cookie is set
	$cookiename = nxs_dataprotection_getexplicitconsentcookiename($activity);
	
	$r = $_COOKIE[$cookiename];
	if ($r == "")
	{
		$result = false;
	}
	else
	{
		$result = true;
	}
	return $result;
}

function nxs_dataprotection_getexplicitconsentcookiename($activity)
{
	if ($activity == "") { nxs_webmethod_return_nack("nxs_dataprotection_getexplicitconsentcookiename; no activity specified"); }
	
	$activity = nxs_dataprotection_getcanonicalactivity($activity);
	$theme_meta = nxs_theme_getmeta();
	$theme_version = $theme_meta["version"];
	$latestcontrollerupdate = nxs_dataprotection_getlatestupdatebycontroller();
	$result = "nxs_dataprotection_explicit_consent_{$activity}_{$theme_version}_{$latestcontrollerupdate}";
	$result = preg_replace('/[^A-Za-z0-9\_]/', '', $result); // Removes special chars.
	
	return $result;
}

function nxs_dataprotection_getprefix()
{
	$prefix = "dataprotectiontype_";
	return $prefixl;
}

function nxs_dataprotection_getdataprotectiontype($activity)
{
	if (nxs_hassitemeta())
	{
		$prefix = nxs_dataprotection_getprefix();
		$activity = nxs_dataprotection_getcanonicalactivity($activity);
		
		$sitemeta = nxs_getsitemeta();
		$key = "{$prefix}{$activity}";
		$result = $sitemeta[$key];
		
		//error_log("nxs_dataprotection_getdataprotectiontype; $activity; $key; ($result) ");
	}
	
	return $result;
}

//
function nxs_dataprotection_enforcedataprotectiontypeatstartwebrequest()
{
	if ($_REQUEST["nxs"] == "privacysettings")
	{
		nxs_dataprotection_renderwebsitevisitorprivacyoptions();
		die();
	}
	
	$usecookiewallactivity = "nexusframework:usecookiewall";
	$dataprotectiontype_usecookie = nxs_dataprotection_getdataprotectiontype($usecookiewallactivity);
	if ($dataprotectiontype_usecookie == "")
	{
		// ignore this; default = no cookie wall
	}
	else if ($dataprotectiontype_usecookie == "disabled")
	{
		// ignore this; default = no cookie wall
	}
	else if ($dataprotectiontype_usecookie == "enabled_disabled_for_robots")
	{
		// its turned on
		
		if (nxs_browser_iscrawler())
		{
			// its a bot; no cookie wall
		}
		else
		{
			// its not a bot, check if the cookie is set
			if (!nxs_dataprotection_isexplicitconsentgiven($usecookiewallactivity))
			{
				nxs_dataprotection_renderwebsitevisitorprivacyoptions();
				die();
			}
			else
			{
				// proceed; an expliciet consent is found
			}
		}
	}
	else
	{
		nxs_webmethod_return_nack("error; nxs_dataprotection_enforcedataprotectiontypeatstartwebrequest; unsupported dataprotectiontype ($dataprotectiontype)");
	}
}

function nxs_dataprotection_renderexplicitconsentinput($activity)
{
	$dataprotectiontype = nxs_dataprotection_getdataprotectiontype($activity);
	
	if ($dataprotectiontype == "enabled_after_cookie_component_consent_or_robot")
	{
		$currenturl = nxs_geturlcurrentpage();
		$where_to_give_consent_url = nxs_geturl_home();
		$where_to_give_consent_url = nxs_addqueryparametertourl_v2($where_to_give_consent_url, "nxs", "privacysettings", true, true);
		$where_to_give_consent_url = nxs_addqueryparametertourl_v2($where_to_give_consent_url, "returnto", $currenturl, true, true);
		
		nxs_ob_start();
		?>
		<div><a href='<?php echo $where_to_give_consent_url; ?>'>Click here to give your consent to render <?php echo $activity; ?></a></div>
		<?php
		$result = nxs_ob_get_contents();
		nxs_ob_end_clean();
	}
	else if ($dataprotectiontype == "enabled_after_cookie_wall_consent_or_robot")
	{
		nxs_ob_start();
		?>
		<div><?php echo $activity; ?>; disabled</div>
		<?php
		$result = nxs_ob_get_contents();
		nxs_ob_end_clean();
	}
	else if ($dataprotectiontype == "disabled")
	{
		if (is_user_logged_in())
		{
			nxs_ob_start();
			?>
			<div class='hidewheneditorinactive'><?php echo $activity; ?>; disabled by website owner (controller)</div>
			<?php
			$result = nxs_ob_get_contents();
			nxs_ob_end_clean();
		}
		else
		{
			nxs_ob_start();
			?>
			<div><?php echo $activity; ?>; <?php echo $dataprotectiontype; ?></div>
			<?php
			$result = nxs_ob_get_contents();
			nxs_ob_end_clean();
		}
	}
	else
	{
		nxs_ob_start();
		?>
		<div>Unexpected; <?php echo $activity; ?>; <?php echo $dataprotectiontype; ?></div>
		<?php
		$result = nxs_ob_get_contents();
		nxs_ob_end_clean();
	}
	
	return $result;
}

function nxs_dataprotection_renderwebsitevisitorprivacyoptions()
{
	require_once("nxs-privacysettings.php");
	nxs_dataprotection_renderwebsitevisitorprivacyoptions_actual();
}

// todo: move to nexuscore/dataprotection/nxs-dataprotection.php
function nxs_dataprotection_factor_createprotecteddata($type)
{
	if ($type == "widget-none")
	{
		$result = array
		(
			"subactivities" => array
			(
				// if widget has properties that pull information from other 
				// vendors (like scripts, images hosted on external sites, etc.) 
				// those need to be taken into consideration
				// responsibility for that is the person configuring the widget
				"custom-widget-configuration",	
			),
			"dataprocessingdeclarations" => array	
			(
			),
			"status" => "final",
		);
	}
	else if ($type == "widget-defaultformitem")
	{
		// NOTE; only use this approach for default form items (such as text input, select input, etc.)
		// it should NOT be used for form item types that DO process user meta in their own
		// specific ways (such for example as the ReCaptcha item)
		$result = array
		(
			"subactivities" => array
			(
				// if widget has properties that pull information from other 
				// vendors (like scripts, images hosted on external sites, etc.) 
				// those need to be taken into consideration
				// responsibility for that is the person configuring the widget
				"custom-widget-configuration",
				// delegate handling of the user data to default widget_formbox implementation
				"nexusframework:widget_formbox",	
			),
			"dataprocessingdeclarations" => array	
			(
				// intentionally left blank (sub activities are responsible for proper handling)
			),
			"status" => "final",
		);
	}
	else
	{
		nxs_webmethod_return_nack("error; nxs_dataprotection_factor_createprotecteddata; unsupported type; {$type}");
	}
	
	return $result;
}

function nxs_dataprotection_nexusframework_use_framework_getprotecteddata($args)
{
	$subactivities = array();
	
	$subactivities[] = "nexusframework:use_framework_on_any_site";
	$subactivities[] = "nexusframework:use_framework_on_this_site";
	
	//
	
	$result = array
	(
		"subactivities" => $subactivities,
		"dataprocessingdeclarations" => array	
		(
		)
	);
	
	return $result;
}

// todo: move to nexuscore/dataprotection/nxs-dataprotection.php
function nxs_dataprotection_nexusframework_use_framework_on_any_site_getprotecteddata($args)
{
	// for any user
	$subactivities[] = "nexusframework:usecookiewall";
	$subactivities[] = "nexusframework:usegooglefonts";
	$subactivities[] = "google:loadjsapi";
	$subactivities[] = "google:loadwebfont";
	$subactivities[] = "google:loadspecificfontsdependingonhowconfigured";
	$subactivities[] = "google:loadanalytics";
	$subactivities[] = "google:loadspecificanalyticsifconfigured";
	$subactivities[] = "nexusframework:handleexplicitcookieconsent";
	
	// for logged in users;
	$subactivities[] = "nexusframework:support";
	$subactivities[] = "nexusframework:selectlanguage_nxs_cookie_hl";
	
	
	$subactivities[] = "themeuser:nexusthemes:usesupport";
	$subactivities[] = "themeuser:google:usesupport";
	
	$subactivities[] = "nexusframework:updates";
	$subactivities[] = "dpa:nexus:usegooglefonts";

	$subactivities[] = "nexusframework:license";
	$subactivities[] = "nexusframework:ixplatform";
	
	
	// include webmethods
	if (true)
	{
		$folder = NXS_FRAMEWORKPATH . "/nexuscore/webservices/webmethods";
		$folders = glob($folder . '/*' , GLOB_ONLYDIR);
		foreach ($folders as $folder)
		{
			$name = basename($folder);			
			$subactivities[] = "nexusframework:webmethod:{$name}";
			
			$path = "{$folder}/{$name}_webmethod.php";			
			require_once($path);
		}
	}
	
	// include widgets
	if (true)
	{
		$folder = NXS_FRAMEWORKPATH . "/nexuscore/widgets";
		$folders = glob($folder . '/*' , GLOB_ONLYDIR);
		foreach ($folders as $folder)
		{
			$name = basename($folder);			
			$subactivities[] = "nexusframework:widget:{$name}";
			
			$path = "{$folder}/widget_{$name}.php";			
			require_once($path);
		}
	}
	

	//
	
	$result = array
	(
		"subactivities" => $subactivities,
		"dataprocessingdeclarations" => array	
		(
		)
	);
	
	return $result;
}

function nxs_dataprotection_nexusframework_usecookiewall_getprotecteddata($args)
{
	$result = array
	(
		"subactivities" => array
		(
		),
		"dataprocessingdeclarations" => array	
		(
			array
			(
				"use_case" => "(belongs_to_whom_id) can give a consent over various activities of the site that deal with user data according to the rules specified by the controlled",
				"what" => "As explained in the terms and conditions as well as privacy statement that can be configured in the site",
				"belongs_to_whom_id" => "website_visitor", // (has to give consent for using the "what")
				"controller" => "website_owner",	// who is responsible for this?
				"controller_options" => nxs_dataprotection_factory_getenableoptions("cookiewall"),
				"data_processor" => "Various (see terms and conditions and privacy statement)",	// the name of the data_processor or data_recipient
				"data_retention" => "Various (see terms and conditions and pricacy statement)",
				"program_lifecycle_phase" => "runtime",	// determined by how the controlled fills in the privacy statement and terms and conditions
				"why" => "As explained in the terms and conditions as well as privacy statement that can be configured in the site",
				"security" => "As explained in the terms and conditions as well as privacy statement that can be configured in the site",
			),
		),
		"status" => "final",
	);
	return $result;
}

function nxs_dataprotection_nexusframework_usegooglefonts_getprotecteddata($args)
{
	$result = array
	(
		"subactivities" => array
		(
		),
		"dataprocessingdeclarations" => array	
		(
			array
			(
				"use_case" => "(belongs_to_whom_id) can browse a page of the website that uses fonts that are pulled using google jsapi",
				"what" => "IP address of the (belongs_to_whom_id) as well as 'Request header fields' send by browser of ((belongs_to_whom_id)) (https://en.wikipedia.org/wiki/List_of_HTTP_header_fields#Request_fields)",
				"belongs_to_whom_id" => "website_visitor", // (has to give consent for using the "what")
				"controller" => "website_owner",	// who is responsible for this?
				"controller_options" => nxs_dataprotection_factory_getenableoptions("all"),
				"data_processor" => "Google (Fonts)",	// the name of the data_processor or data_recipient
				"data_retention" => "See the terms https://cloud.google.com/terms/data-processing-terms#data-processing-and-security-terms-v20",
				"program_lifecycle_phase" => "compiletime",
				"why" => "Not applicable (because this is a compiletime declaration)",
				"security" => "The data is transferred over a secure https connection. Security is explained in more detail here; https://cloud.google.com/terms/data-processing-terms#7-data-security",
			),
		),
		"status" => "final",
	);
	return $result;
}

function nxs_dataprotection_factory_getenableoptions($type)
{
	if ($type == "all")
	{
		$result = array
		(
			"" => "Enabled (default)",
			"enabled" => "Enabled",
			"enabled_after_cookie_wall_consent_or_robot" => "For website visitors its conditionally enabled only after the website visitor gave a cookie wall consent. For robots its enabled.",
			"enabled_after_cookie_component_consent_or_robot" => "For website visitors its conditionally enabled only after the website visitor gave a component cookie consent. For robots its enabled.",
			"disabled" => "Disabled",
		);
	}
	else if ($type == "widget:enabled|cookiewall|disabled")
	{
		$result = array
		(
			"" => "Enabled (default)",
			"enabled" => "Enabled",
			"enabled_after_cookie_wall_consent_or_robot" => "For website visitors its conditionally enabled only after the website visitor gave a cookie wall consent. For robots its enabled.",
			"disabled" => "Disabled",
		);
	}
	else if ($type == "cookiewall")
	{
		$result = array
		(
			"" => "Disabled (default)",
			"disabled" => "Disabled",
			"enabled_disabled_for_robots" => "Enabled (for robots its disabled)",
		);
	}
	else
	{
		nxs_webmethod_return_nack("not supported; $type");
	}
	
	return $result;
}

function nxs_dataprotection_get_controllable_activities($args)
{
	$result = array();
	
	$protecteddata = nxs_dataprotection_buildrecursiveprotecteddata($args);
	$activities = $protecteddata["activities"];
	foreach ($activities as $activity => $activity_meta)
	{
		$dataprocessingdeclarations = $activity_meta["dataprocessingdeclarations"];
		foreach ($dataprocessingdeclarations as $dataprocessingdeclaration_meta)
		{
			$controller_options = $dataprocessingdeclaration_meta["controller_options"];
			if (isset($controller_options))
			{
				$result[$activity] = $controller_options;
			}
		}
	}
	
	return $result;
}
