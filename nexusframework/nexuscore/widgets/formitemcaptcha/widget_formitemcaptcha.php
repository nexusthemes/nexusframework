<?php

function nxs_widgets_formitemcaptcha_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-user";
}

function nxs_widgets_formitemcaptcha_gettitle()
{
	return nxs_l18n__("ReCaptcha 2.0", "nxs_td");
}

function nxs_widgets_formitemcaptcha_getformitemsubmitresult($args)
{
	// server side validation	
	extract($args);
	
	$result = array();
	$result["result"] = "OK";
	$result["validationerrors"] = array();
	$result["markclientsideelements"] = array();
	
	// TODO: retrieve public and private key from configured properties
	$publickey = $metadata["recaptcha_publickey"];
	if ($publickey == "") { $result["validationerrors"][] = nxs_l18n__("Public key of ReCaptcha is not configured", "nxs_td"); };

	$privatekey = $metadata["recaptcha_privatekey"];
	if ($privatekey == "") { $result["validationerrors"][] = nxs_l18n__("Private key of ReCaptcha is not configured", "nxs_td"); };
	
	// $metadata contains the submitted data
	$response=$_POST["g-recaptcha-response"];
	if ($response) 
	{
		$verify=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$privatekey}&response={$response}");
		$captcha_success=json_decode($verify);
		if ($captcha_success->success==true)
    {
    	// echo "You got it!";
    	// $result["validationerrors"][] = nxs_l18n__("Captcha was correctly entered", "nxs_td");
    } 
    else 
    {
    	$result["validationerrors"][] = nxs_l18n__("Wrong Captcha", "nxs_td");
      // set the error code so that we can display it
      // $error = $resp->error;
    }
	}
	else
	{
		$result["validationerrors"][] = nxs_l18n__("The captcha is required", "nxs_td");
	}
	
	return $result;
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_formitemcaptcha_renderincontactbox($args)
{
	extract($args);
	
	extract($metadata, EXTR_PREFIX_ALL, "metadata");
	
  $publickey = $metadata["recaptcha_publickey"];
  $privatekey = $metadata["recaptcha_privatekey"];
  
	$result = array();
	$result["result"] = "OK";
	
	nxs_requirewidget("contactbox");
	$prefix = nxs_widgets_contactbox_getclientsideprefix($postid, $placeholderid);
	
	if ($metadata_overriddenelementid != "")
	{
		$key = $metadata_overriddenelementid;
	}
	else
	{
		$key = $prefix . $metadata_elementid;
	}
	
	if (!isset($value) || $value == "")
	{
		$value = $metadata_initialtext;
	}
	
	//
	// render actual control / html
	//
	
	nxs_ob_start();
	$metadata_isrequired = "true";
	?>
	
  <label class="field_name"><?php echo $metadata_formlabel;?><?php if ($metadata_isrequired != "") { ?>*<?php } ?></label>
  <?php
  
  if ($publickey != "" && $privatekey != "")
  {
  	$use_ssl = nxs_ishttps();
  	?>
  	<script src='https://www.google.com/recaptcha/api.js'></script>
  	<div class="g-recaptcha" data-sitekey="<?php echo $publickey; ?>"></div>
  	<?php
  }
  else
  {
  	?>
  	<br />Configure the ReCaptcha widget first
  	<?php
  }
  
  ?>
  <script type='text/javascript'>
  	nxs_js_log("unbinding nxs_js_trigger_formvalidationfailed.captcha");
  	jQuery(window).unbind("nxs_js_trigger_formvalidationfailed.captcha");
  	nxs_js_log("binding nxs_js_trigger_formvalidationfailed.captcha");
		jQuery(window).bind 
		(
			"nxs_js_trigger_formvalidationfailed.captcha", 
			function(e) 
			{
				//
				nxs_js_log("redrawing captchas");
				// Recaptcha.reload();
				grecaptcha.reset();
			}
		);
  </script>
  <?php
  
	// var_dump($args);
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

function nxs_widgets_formitemcaptcha_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	$mixedattributes = array_merge($temp_array, $args);
	
	$image_imageid = $mixedattributes['image_imageid'];
	$title = $mixedattributes['title'];
	$text = $mixedattributes['text'];
	$destination_articleid = $mixedattributes['destination_articleid'];
	
	$lookup = nxs_wp_get_attachment_image_src($image_imageid, 'full', true);
	
	$width = $lookup[1];
	$height = $lookup[2];		
	
	$lookup = nxs_wp_get_attachment_image_src($image_imageid, 'thumbnail', true);
	$url = $lookup[0];
	$url = nxs_img_getimageurlthemeversion($url);

	global $nxs_global_placeholder_render_statebag;
	
	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		//	
		$hovermenuargs = array();
		$hovermenuargs["postid"] = $postid;
		$hovermenuargs["placeholderid"] = $placeholderid;
		$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
		$hovermenuargs["enable_decoratewidget"] = false;
		$hovermenuargs["enable_deletewidget"] = false;
		$hovermenuargs["enable_deleterow"] = true;
		$hovermenuargs["metadata"] = $mixedattributes;
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);	
	}
	
	/* ADMIN EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-formitemcaptcha-item";
	
	/* ADMIN OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	echo '
	<div class="nxs-dragrow-handler nxs-padding-menu-item">
		<div class="content2">
			<div class="box">
	      <div class="box-title">
	      	<span class="nxs-icon-user" style="font-size: 16px;">ReCaptcha2.0</span>
				</div>
				<div class="box-content"></div>
			</div>
			<div class="nxs-clear"></div>
		</div>
	</div>';
	
	/* ------------------------------------------------------------------------------------------------- */

	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

function nxs_widgets_formitemcaptcha_help($optionvalues, $args, $runtimeblendeddata) 
{
	nxs_ob_start();
	
	//$headingid = "heading";
	
	extract($optionvalues);
	
	//$containerpostid = $args["clientpopupsessioncontext"]["containerpostid"];
		
	?>
	<div>
		reCAPTCHA is a free service from Google. To use reCAPTCHA you must get an API key from <a target='_blank' href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>
	</div>
  <div class="nxs-clear"></div>
  <?php
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

// Define the properties of this widget
function nxs_widgets_formitemcaptcha_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_formitemcaptcha_gettitle(),
		"sheeticonid" => nxs_widgets_formitemcaptcha_geticonid(),
	
		"fields" => array
		(
			// HELP
			
			
			// KEYS			
			array
			( 
				"id" 				=> "help",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_widgets_formitemcaptcha_help",
				"label" 			=> nxs_l18n__("Help", "nxs_td"),
			),

			
			// KEYS			
			array
			( 
				"id" 				=> "recaptcha_publickey",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("ReCaptcha Site key", "nxs_td"),
			),
			
			array
			( 
				"id" 				=> "recaptcha_privatekey",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("ReCaptcha Secret key", "nxs_td"),
			),
		)
	);
	
	return $options;
}

function nxs_widgets_formitemcaptcha_initplaceholderdata($args)
{
	extract($args);

	//$args["foo"] = "bar";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
