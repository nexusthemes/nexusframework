<?php

function nxs_dataprotection_rendercookiewall_actual()
{
	if (!nxs_dataprotection_isprivacysupported_and_configured())
	{
		if (is_user_logged_in())
		{
			$backendurl = get_admin_url();
		}
		else
		{
			$backendurl = wp_login_url();
		}
		
		//
		?>
		Error<br />
		unable to render cookiewall - privacy policy is not configured or not published (requires WP 4.9.6 or above)<br />
		<a href='<?php echo $backendurl; ?>'>Go to the WP backend</a>
		<?php
		die();
	}
	
	$cookiewallactivity = nxs_dataprotection_getcookiewallactivity();
	$cookiename = nxs_dataprotection_getexplicitconsentcookiename($cookiewallactivity);

	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */

	if (nxs_hassitemeta())
	{
		$sitemeta = nxs_getsitemeta();
	}
	
	// Background Image

	$cookiewall_desktop_imageid = $sitemeta["cookiewall_desktop_imageid"];
	$imagemetadata = nxs_wp_get_attachment_image_src($cookiewall_desktop_imageid, 'full', true);
	$backgroundimage_url = $imagemetadata[0];
	$backgroundimage_url = nxs_img_getimageurlthemeversion($backgroundimage_url);
	
	// GDPR Trust Icon			
	$cookie_wall_trust_imageid = $sitemeta["cookie_wall_trust_imageid"];
	$imagemetadata = nxs_wp_get_attachment_image_src($cookie_wall_trust_imageid, 'full', true);
	$cookie_wall_trust_imageurl = $imagemetadata[0];
	$cookie_wall_trust_imageurl = nxs_img_getimageurlthemeversion($cookie_wall_trust_imageurl);
	
	// GDPR Content
	$text = $sitemeta["cookie_wall_text"];
	
	// Background Color
	$cookiewall_wrapcolorzen = $sitemeta["cookiewall_wrapcolorzen"];
	$background_color = "#222222";
	
	// Privacy Policy
	$privacy_policy_title = nxs_dataprotection_getprivacypolicytitle();
	$privacy_policy_text = wpautop(nxs_dataprotection_getprivacypolicytext());
	
	$jquery_url = nxs_getframeworkurl() . "/js/jquery-1.11.1/jquery.min.js";
	        
          /* OUTPUT
	---------------------------------------------------------------------------------------------------- */
  ?>  
	<html>
		<head>
			<meta name="robots" content="noindex">
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
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
				  margin: 0px;
				  padding: 0px;
				}
				#nxsdataprotectionback { width: 100vw; height: 100vh; display: flex; align-items: center; justify-content: center; overflow-y: scroll; }
				@media only screen and ( max-width: 959px ) { #nxsdataprotectionback { display: block; } }
				
				#nxsdataprotectionwrap { padding: 20px; border-radius: 3px;	color: white; font-size: 16px; box-shadow: 7px 7px 5px 0px rgba(50, 50, 50, 0.75); max-width: 40%; overflow-y: scroll; }
				
				@media only screen and ( max-width: 1439px ) { #nxsdataprotectionwrap { max-width: 60%;} }
				@media only screen and ( max-width: 1199px ) { #nxsdataprotectionwrap { max-width: 80%;} }
				@media only screen and ( max-width: 959px ) { #nxsdataprotectionwrap { max-width: 100%;} }
				#nxsdataprotectionwrap p { margin: 1em; }
				
				::-webkit-scrollbar { -webkit-appearance: none; width: 0px;	}
				::-webkit-scrollbar-thumb { border-radius: 4px; background-color: rgba(0,0,0,.5); -webkit-box-shadow: 0 0 1px rgba(255,255,255,.5); }
				
				/* Acordeon styles 
				---------------------------------------------------------------------------------------------------- */
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
				.gdpr-accordion-wrapper .tab-content h6,
				#nxsdataprotectionform { margin: 1em; }
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
				
				input[type=submit] {
					border-radius: 2px;
					border: hidden;
					padding: 10px 8px; 
					cursor: default;
					font-size: 14px;
					text-align: center;
					text-transform: uppercase;
					background: #2196F3;
					color: white;
					margin-top: 20px;
				}
				input[type=submit]:hover { cursor: pointer; }
				
				/* Switch
				---------------------------------------------------------------------------------------------------- */			
				.switch { position: relative; display: inline-block; width: 100%; height: 34px; line-height: 34px; text-indent: 80px; margin-bottom: 1.2em;}
				.switch input {display:none;}
				.slider {
					position: absolute;
					cursor: pointer;
					top: 0;
					left: 0;
					right: 0;
					bottom: 0;
					background-color: #ccc;
					-webkit-transition: .4s;
					transition: .4s;
				}
				.slider:before {
					position: absolute;
					content: "";
					height: 26px;
					width: 26px;
					left: 4px;
					bottom: 4px;
					background-color: white;
					-webkit-transition: .4s;
					transition: .4s;
				}
				input:checked + .slider { background-color: #2196F3; }
				input:focus + .slider { box-shadow: 0 0 1px #2196F3; }
				input:checked + .slider:before {
					-webkit-transform: translateX(26px);
					-ms-transform: translateX(26px);
					transform: translateX(26px);
				}
				.slider.round { border-radius: 34px; width: 60px; }
				.slider.round:before { border-radius: 50%; }
			
            </style> 
                  
	<?php
	
	// render form
	if (true) 
	{
		$submit_button_text = nxs_dataprotection_getcookiewallbuttontext();
		$explicit_consent_cookiewall_label = nxs_dataprotection_getcookiewallconsenttext();
		
		nxs_ob_start();
		
		// Begin Form HTML
		echo'<form id="nxsdataprotectionform">';
	  
	  
	  $cookiewallactivity = nxs_dataprotection_getcookiewallactivity();
		$controllable_activities = array($cookiewallactivity);
				
		foreach ($controllable_activities as $controllable_activity)
		{
			$control_options = nxs_dataprotection_getactivityprotecteddata($controllable_activity);
			
			$controller_label = $control_options["controller_label"];
			if (nxs_dataprotection_iscookiewallactivity($controllable_activity))
			{
				$controller_label = $explicit_consent_cookiewall_label;
			}
			
			$controllable_activity = nxs_dataprotection_getcanonicalactivity($controllable_activity);
			
			$is_operational = nxs_dataprotection_isoperational($controllable_activity);
			
			if ($is_operational)
			{
				$cookiename = nxs_dataprotection_getexplicitconsentcookiename($controllable_activity);
				$checkedattribute = nxs_dataprotection_isexplicitconsentgiven($controllable_activity) ? "checked" : "";
				$items[] = $cookiename;
					echo'
					
					
					<label class="switch" for="'.$cookiename.'">
						<input type="checkbox" class="nxsexplicituserconsent" data-cookiename="'.$cookiename.'" id="'.$cookiename.'" '.$checkedattribute.' />
						<span class="slider round"></span>
						<span>'.$controller_label.'</span>
					</label>
					<br/>
					';
				}
			}
			echo'<input type="submit" value="'.$submit_button_text.'" />
			
			</form>';
	      	$form = nxs_ob_get_contents();
			nxs_ob_end_clean();
		}
			
			echo '
			<div id="nxsdataprotectionback">
				<div id="nxsdataprotectionwrap" style="background-color: '.$background_color.';">
					
					
					<p style="text-align: center;">
						';
						
						if ($cookie_wall_trust_imageid != "")
						{
							echo '<img src="'.$cookie_wall_trust_imageurl.'">';
						}
						
						echo '
					</p>
					
					<p>'.$text.'</p>
					
					'.$form.'
				
					<div class="gdpr-accordion-wrapper">

						<div class="tab">
						  <input id="tab-two" type="checkbox" name="tabs">
						  <label for="tab-two">'.$privacy_policy_title.'</label>
						  <div class="tab-content">'.$privacy_policy_text.'</div>
						</div>
					
					</div>
					
					
				</div>
			</div>';
			
			$returnqueryparameter = nxs_dataprotection_getreturnqueryparameter();
			$finishedurl = $_REQUEST[$returnqueryparameter];
			if ($finishedurl == "")
			{
				$finishedurl = nxs_geturl_home();
			}
			
			$days = nxs_dataprotection_getcookieconsentretentionindays();
			
			?>
			<script>
				
				$('#nxsdataprotectionform').submit
				(
					function(ev) 
					{
				    ev.preventDefault(); // to stop the form from submitting
				    
				    <?php
				    foreach ($items as $item)
				    {
				    	?>
					    var isconfirmed = document.getElementById("<?php echo $item; ?>").checked;
					    nxs_js_store_expliciet_cookie_consent("<?php echo $item; ?>", isconfirmed);
					    <?php
					  }
					  ?>
					  
					  // reload the page 
					  window.location.href = '<?php echo $finishedurl; ?>';
					}
				);
				
				function nxs_js_store_expliciet_cookie_consent(name, isconfirmed)
				{
					if (isconfirmed)
					{
						// one year
						var expiretime = <?php echo $days; ?> * 24 * 60 * 60 * 1000;
						// set cookie
						nxs_js_setcookie(name, isconfirmed, expiretime);
					}
					else
					{
						// one year in the past means it will be gone
						var expiretime = - 365 * 24 * 60 * 60 * 1000;
						// set cookie
						nxs_js_setcookie(name, "", expiretime);
					}
				}
			</script>
		</body>
	</html>
	<?php
	die();
}
