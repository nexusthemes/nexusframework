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
					
					$currentresult["dataprocessingdeclarations"]["includes"][] = $subactivity;
					
					if (!in_array($subactivity, $processed))
					{
						$queue[] = $subactivity;
					}
					else
					{
						// 
						$result["warning"][] = "activity was enqueued multiple times (only the first time it was processed); $subactivity";
					}
				}
			}
							
			// store result of the current component
			$result["activities"][$currentactivity] = $currentresult["dataprocessingdeclarations"];
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
	
	if ($_REQUEST["dataprotection"] != "")
	{
		$result = $_REQUEST["dataprotection"];
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
				$backgroundimage_url = "";
				if (nxs_hassitemeta())
				{
					$sitemeta = nxs_getsitemeta();
					$cookie_wall_image_imageid = $sitemeta["cookie_wall_image_imageid"];
					
					$imagemetadata = nxs_wp_get_attachment_image_src($cookie_wall_image_imageid, 'full', true);
					// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
					$backgroundimage_url 		= $imagemetadata[0];
					$backgroundimage_url = nxs_img_getimageurlthemeversion($backgroundimage_url);
				}
				
				$jquery_url = nxs_getframeworkurl() . "/js/jquery-1.11.1/jquery.min.js";
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
							
							body::before
							{
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
							}
							
							body
							{
							  background-image: linear-gradient( rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5) ), url("<?php echo $backgroundimage_url; ?>"); 
							  background-size: 0 0;  /* image should not be drawn here */
							  width: 100%;
							  height: 100%;
							  position: fixed; /* or absolute for scrollable backgrounds */  
							  font-family: arial;
							}
							
							#nxsdataprotectionback
							{
								width: 100vw;
								height: 100vh;
								display: flex;
								align-items: center;
  							justify-content: center;
  						}
  						
							#nxsdataprotectionwrap
							{
								padding: 20px;
								
								border-radius: 20px;
								background-color: white;
								color: black;
								font-size: 20px;
								box-shadow: 7px 7px 5px 0px rgba(50, 50, 50, 0.75);
								max-width: 40vw;
							}
							
							input[type=submit] 
							{
						    padding:5px 15px; 
						    background:#ccc; 
						    border:0 none;
						    cursor:pointer;
						    -webkit-border-radius: 5px;
						    border-radius: 5px;
						    font-size: 20px;
							}
							
							#nxsdataprotectionterms
							{
								border: 1px solid black;
								max-height: 20vh;
								max-width: 100%;
								overflow-y: scroll;
								padding-top: 10px;
								padding-bottom: 10px;
								
							}
							
							::-webkit-scrollbar {
						        -webkit-appearance: none;
						        width: 7px;
					    }
					    ::-webkit-scrollbar-thumb {
					        border-radius: 4px;
					        background-color: rgba(0,0,0,.5);
					        -webkit-box-shadow: 0 0 1px rgba(255,255,255,.5);
					    }
							
						</style>
						<div id='nxsdataprotectionback'>
							<div id='nxsdataprotectionwrap'>
								<p>
									First give explicit consent to our terms and conditions, thanks
								</p>
								<div>
									<h2>Terms and conditions</h2>
									<div id='nxsdataprotectionterms'>
										Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sit amet faucibus sapien. Pellentesque non odio sit amet elit rutrum volutpat. Curabitur facilisis ut sapien tincidunt luctus. Integer vehicula lorem enim, eget rutrum magna lobortis nec. Aliquam sed sodales leo. Cras eget vehicula nunc. Proin sit amet felis quis arcu ullamcorper vulputate. Vivamus convallis ipsum sodales magna condimentum, vel mattis lectus porttitor. Quisque pulvinar, nunc in laoreet commodo, leo urna blandit sem, sit amet rutrum massa nibh quis tortor. In bibendum justo eget lectus sagittis congue. Etiam ac lorem id sapien sagittis rutrum ac sit amet eros. Fusce ultrices gravida nulla, quis vestibulum nunc faucibus vel. Ut dapibus tellus a risus rhoncus pulvinar. Morbi consectetur orci ac imperdiet eleifend.
									</div>
								</div>
								<form id='nxsdataprotectionform'>
									<input type='checkbox' name='nxsexplicitconsent' id='nxsexplicitconsent' />
									<label for="nxsexplicitconsent">Yes i totally agree to the <a href='#'>terms and conditions</a></label>
									
									<br />
									<input type='submit' />
								</form>
							</div>
						</div>
						<div>
							<?php 
							/*
							$args = array
							(
								"rootactivity" => "wordpress:use_theme_on_any_site",
							);
							$activitiesandinformation = nxs_dataprotection_buildrecursiveprotecteddata($args);
							$json = json_encode($activitiesandinformation);
							echo $json;
							*/
							?>
						</div>
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