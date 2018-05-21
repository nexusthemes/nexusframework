<?php

function nxs_dataprotection_renderwebsitevisitorprivacyoptions_actual()
{
	$usecookiewallactivity = "nexusframework:usecookiewall";	
	$cookiename = nxs_dataprotection_getexplicitconsentcookiename($usecookiewallactivity);

	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */

	if (nxs_hassitemeta())
	{
		$sitemeta = nxs_getsitemeta();
	}
	
	// Background Image

	$backgroundimage_url = "";
	$cookie_wall_image_imageid = $sitemeta["cookie_wall_image_imageid"];
	$imagemetadata = nxs_wp_get_attachment_image_src($cookie_wall_image_imageid, 'full', true);
	$backgroundimage_url = $imagemetadata[0];
	$backgroundimage_url = nxs_img_getimageurlthemeversion($backgroundimage_url);
	
	// GDPR Trust Icon			
	$gdpr_imageid = $sitemeta["gdpr_imageid"];
	$imagemetadata = nxs_wp_get_attachment_image_src($gdpr_imageid, 'full', true);
	$gdprimage_url = $imagemetadata[0];
	$gdprimage_url = nxs_img_getimageurlthemeversion($gdprimage_url);
	
	// GDPR Content
	$text = $sitemeta["text"];
	
	// Background Color
	$background_color = $sitemeta["background_color"];
	
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
					background: #4285f4;
					color: white;
					margin-top: 20px;
				}
				input[type=submit]:hover { cursor: pointer; }
				
				

				
			</style> 
                  
	<?php
	
	// render form
	if (true) {
		
		nxs_ob_start();
		
		// Begin Form HTML
		echo'<form id="nxsdataprotectionform">';
	      	
		$a = array("rootactivity" => "nexusframework:process_request",);
		$controllable_activities = nxs_dataprotection_get_controllable_activities($a);
		$controllable_activities = array_reverse($controllable_activities);
				
		foreach ($controllable_activities as $controllable_activity => $control_options)
		{
			$controllable_activity = nxs_dataprotection_getcanonicalactivity($controllable_activity);
			$dataprotectiontype = nxs_dataprotection_getdataprotectiontype($controllable_activity);
			
			if (in_array($dataprotectiontype, array("enabled_after_cookie_component_consent_or_robot", "enabled_disabled_for_robots")))
			{
				$cookiename = nxs_dataprotection_getexplicitconsentcookiename($controllable_activity);
				$checkedattribute = nxs_dataprotection_isexplicitconsentgiven($controllable_activity) ? "checked" : "";
				$items[] = $cookiename;
					echo'
					<input type="checkbox" class="nxsexplicituserconsent" data-cookiename="'.$cookiename.'" id="'.$cookiename.'" '.$checkedattribute.' />
					<label for="'.$cookiename.'">'.$controllable_activity.'</label>
					<br />
					';
				}
			}
			echo'<input type="submit" value="Update my privacy settings" />
			
			</form>';
	      	$form = nxs_ob_get_contents();
			nxs_ob_end_clean();
		}
			
			echo '
			<div id="nxsdataprotectionback">
				<div id="nxsdataprotectionwrap" style="background-color: '.$background_color.';">
					
					<p style="text-align: center;">
						<img src="'.$gdprimage_url.'">
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
			
			$finishedurl = $_REQUEST["returnto"];
			if ($finishedurl == "")
			{
				$finishedurl = nxs_geturl_home();
			}
			
			$days = nxs_dataprotection_getcookieretentionindays();
			
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
