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

function nxs_dataprotection_getexplicitconsentcookiename()
{
	return "nxs_dataprotection_explicit_content";
}

function nxs_dataprotection_gettypeofdataprotection()
{
	$result = "none";
	
	if (nxs_hassitemeta())
	{
		$sitemeta = nxs_getsitemeta();
		$result = $sitemeta["dataprotectiontype"];
	}

	if ($result == "")
	{
		$result = "none";
	}
	
	return $result;
}

function nxs_dataprotection_isactionallowed($action)
{
	$result = false;
	
	$dataprotectiontype = nxs_dataprotection_gettypeofdataprotection();
	if ($dataprotectiontype == "explicit_consent_by_cookie_wall")
	{
		if (nxs_browser_iscrawler())
		{
			// its a bot; no cookie wall; using cached page is ok
			$result = true;
		}
		else
		{
			// its not a bot, check if the cookie is set
			$cookiename = nxs_dataprotection_getexplicitconsentcookiename();
			$r = $_COOKIE[$cookiename];
			
			if ($r != "")
			{
				$result = true;
			}
		}
	}
	else if ($dataprotectiontype == "none")
	{
		// proceed; no data protection is enforced by the owner of the site
		$result = true;
	}
	else
	{
		nxs_webmethod_return_nack("error; nxs_dataprotection_isallowedtoreceivedcachedpage; unsupported dataprotectiontype ($dataprotectiontype)");
	}
	
	return $result;
}

//
function nxs_dataprotection_enforcedataprotectiontypeatstartwebrequest()
{
	$dataprotectiontype = nxs_dataprotection_gettypeofdataprotection();
	if ($dataprotectiontype == "explicit_consent_by_cookie_wall")
	{
		if (nxs_browser_iscrawler())
		{
			// its a bot; no cookie wall
		}
		else
		{
			// its not a bot, check if the cookie is set
			$cookiename = nxs_dataprotection_getexplicitconsentcookiename();
			$r = $_COOKIE[$cookiename];
			
			if ($r == "")
			{
				// nope, no consent yet; show the cookie wall
				
				/* EXPRESSIONS
				---------------------------------------------------------------------------------------------------- */
				
				$sitemeta = nxs_getsitemeta();
				
				// Background Image
				$backgroundimage_url = "";
				if (nxs_hassitemeta())
				{
					$cookie_wall_image_imageid = $sitemeta["cookie_wall_image_imageid"];
					$imagemetadata = nxs_wp_get_attachment_image_src($cookie_wall_image_imageid, 'full', true);
					$backgroundimage_url = $imagemetadata[0];
					$backgroundimage_url = nxs_img_getimageurlthemeversion($backgroundimage_url);
				}
				
				// GDPR Trust Icon			
				$gdpr_imageid = $sitemeta["gdpr_imageid"];
				$imagemetadata = nxs_wp_get_attachment_image_src($gdpr_imageid, 'full', true);
				$gdprimage_url = $imagemetadata[0];
				$gdprimage_url = nxs_img_getimageurlthemeversion($gdprimage_url);
				
				// GDPR Content
				$text = $sitemeta["text"];
				
				// Terms and Condtions
				$terms_and_conditions_title = $sitemeta["terms_and_conditions_title"];
				$terms_and_conditions_text = $sitemeta["terms_and_conditions_text"];
				
				// Privacy Policy
				$privacy_policy_title = $sitemeta["privacy_policy_title"];
				$privacy_policy_text = $sitemeta["privacy_policy_text"];
				
				
				$jquery_url = nxs_getframeworkurl() . "/js/jquery-1.11.1/jquery.min.js";
				
                
                /* OUTPUT
				---------------------------------------------------------------------------------------------------- */
                ?>
                
				<html>
					<head>
						<script data-cfasync="false" type="text/javascript" src="<?php echo $jquery_url; ?>"></script>
						<?php nxs_setjQ_nxs(); ?>
						<script>
						function nxs_js_isuserloggedin() { return <?php if (is_user_logged_in()) { echo "true"; } else { echo "false"; } ?>; } 
						function nxs_js_gettrans(msg)	{ return msg; }
						function nxs_js_enableguieffects() { return false; }
						function nxs_js_isinfrontend() { return <?php echo (!is_admin()); ?>; }
						function nxs_js_getframeworkurl() { return "<?php echo nxs_getframeworkurl(); ?>"; }
						function nxs_js_userhasadminpermissions() { return <?php if (nxs_has_adminpermissions()) { echo "true"; } else { echo "false"; } ?>; }
						</script>
						<script data-cfasync="false" type="text/javascript" src="<?php echo nxs_getframeworkurl(); ?>/nexuscore/includes/nxs-script.js?v=<?php echo nxs_getthemeversion(); ?>"></script>
					</head>
					
                    <body>
						<style>
							
							body::before {
							  content: ""; /* important */
							  z-index: -1; /* important */
							  position: inherit;  
							  left: inherit;
							  top: inherit;
							  width: inherit;                                                                               
							  height: inherit;  
							  background-image: inherit;
							  background-size: cover; 
							  background-position: center center;
							  filter: blur(8px);
							  transform: scale(1.05);
							}
							
							body {
							  background-image: linear-gradient( rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5) ), url("<?php echo $backgroundimage_url; ?>"); 
							  background-size: 0 0;  /* image should not be drawn here */
							  width: 100%;
							  height: 100%;
							  position: fixed; /* or absolute for scrollable backgrounds */  
							  font-family: arial;
							}
							
							#nxsdataprotectionback {
								width: 100vw;
								height: 100vh;
								display: flex;
								align-items: center;
  								justify-content: center;
  							}
  						
							#nxsdataprotectionwrap {
								padding: 20px;
								border-radius: 3px;
								background-color: #003399;
								color: white;
								font-size: 16px;
								box-shadow: 7px 7px 5px 0px rgba(50, 50, 50, 0.75);
								max-width: 40vw;
							}
							#nxsdataprotectionwrap p { margin: 0 0 0 1em; }
							
							input[type=submit]  {
								padding:5px 15px; 
								background:#ccc; 
								border:0 none;
								cursor:pointer;
								-webkit-border-radius: 5px;
								border-radius: 5px;
								font-size: 20px;
							}
							
							
							
							::-webkit-scrollbar {
						        -webkit-appearance: none;
						        width: 7px;
					    	}
							::-webkit-scrollbar-thumb { border-radius: 4px; background-color: rgba(0,0,0,.5); -webkit-box-shadow: 0 0 1px rgba(255,255,255,.5); }
							


							/* Acordeon styles */
							.gdpr-accordion-wrapper .tab { position: relative; margin-bottom: 1px; width: 100%; color: #fff; overflow: hidden; }
							.gdpr-accordion-wrapper input { position: absolute; opacity: 0; z-index: -1; }
							.gdpr-accordion-wrapper label {
							  position: relative;
							  display: block;
							  padding: 0 0 0 1em;
							  border: 2px solid white;
							  font-weight: bold;
							  line-height: 3;
							  cursor: pointer;
							  margin-bottom: 0.1em;
							}
							.gdpr-accordion-wrapper .tab-content {
							  max-height: 0;
							  overflow-y: scroll;
							  background: white;
							  color: grey;
							  -webkit-transition: max-height .35s;
							  -o-transition: max-height .35s;
							  transition: max-height .35s;
							}
							.gdpr-accordion-wrapper .tab-content p,
							.gdpr-accordion-wrapper .tab-content h1,
							.gdpr-accordion-wrapper .tab-content h2,
							.gdpr-accordion-wrapper .tab-content h3,
							.gdpr-accordion-wrapper .tab-content h4,
							.gdpr-accordion-wrapper .tab-content h5,
							.gdpr-accordion-wrapper .tab-content h6 { margin: 1em; }
							/* :checked */
							.gdpr-accordion-wrapper input:checked ~ .tab-content { max-height: 10em; }
							/* Icon */
							.gdpr-accordion-wrapper label::after {
							  position: absolute;
							  right: 0;
							  top: 0;
							  display: block;
							  width: 3em;
							  height: 3em;
							  line-height: 3;
							  text-align: center;
							  -webkit-transition: all .35s;
							  -o-transition: all .35s;
							  transition: all .35s;
							}
							.gdpr-accordion-wrapper input[type=checkbox] + label::after { content: "+"; }
							.gdpr-accordion-wrapper input[type=radio] + label::after { content: "\25BC"; }
							.gdpr-accordion-wrapper input[type=checkbox]:checked + label::after { transform: rotate(315deg); }
							.gdpr-accordion-wrapper input[type=radio]:checked + label::after { transform: rotateX(180deg); }

							
						</style>
                        
                        
                        <?php
						
						echo '
						<div id="nxsdataprotectionback">
							<div id="nxsdataprotectionwrap">
								
								<p style="text-align: center;">
									<img src="'.$gdprimage_url.'">
								</p>
								
								<p>'.$text.'</p>
							
								<div class="gdpr-accordion-wrapper">
    
									<div class="tab">
									  <input id="tab-one" type="checkbox" name="tabs">
									  <label for="tab-one">'.$terms_and_conditions_title.'</label>
									  <div class="tab-content">'.$terms_and_conditions_text.'</div>
									</div>
									
									<div class="tab">
									  <input id="tab-two" type="checkbox" name="tabs">
									  <label for="tab-two">'.$privacy_policy_title.'</label>
									  <div class="tab-content">'.$privacy_policy_text.'</div>
									</div>
								
								</div>
  
								<form id="nxsdataprotectionform">
									<input type="checkbox" name="nxsexplicitconsent" id="nxsexplicitconsent" />
									<label for="nxsexplicitconsent">Yes i totally agree</label>
									
									<br />
									<input type="submit" />
								</form>
								
							</div>
						</div>'
						?>
						<script>
							
							$('#nxsdataprotectionform').submit
							(
								function(ev) 
								{
								    ev.preventDefault(); // to stop the form from submitting
								    var isconfirmed = document.getElementById("nxsexplicitconsent").checked;
								    nxs_js_store_expliciet_cookie_consent(isconfirmed);
								}
							);
							
							function nxs_js_store_expliciet_cookie_consent(isconfirmed)
							{
								if (isconfirmed)
								{
									// one year
									var expiretime = 365 * 24 * 60 * 60 * 1000;
									// set cookie
									nxs_js_setcookie("<?php echo $cookiename; ?>", 'confirmed', expiretime);
									
									// reload the page 
									location.reload();
								}
								else
								{
									alert('you cannot proceed if you dont agree');
								}
							}
						</script>
					</body>
				</html>
				<?php
				die();
			}
			else
			{
				// proceed; an expliciet consent is found
			}
		}
	}
	else if ($dataprotectiontype == "none")
	{
		// proceed; no data protection is enforced by the owner of the site
	}
	else
	{
		nxs_webmethod_return_nack("error; nxs_dataprotection_enforcedataprotectiontypeatstartwebrequest; unsupported dataprotectiontype ($dataprotectiontype)");
	}
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
	else if ($type == "cookiewall")
	{
		$result = array
		(
			"" => "Disabled (default)",
			"disabled" => "Disabled",
			"enabled" => "Enabled",
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
