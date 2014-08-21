<?php

function nxs_widgets_formitemcaptcha_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-key";
}

function nxs_widgets_formitemcaptcha_gettitle()
{
	return nxs_l18n__("Captcha input", "nxs_td");
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
	$publickey = "6Lfw-fgSAAAAAB1jhlOKAo4mYJzGlQAUjp3nDZIz";	// $metadata["recaptcha_publickey"];
	$privatekey = "6Lfw-fgSAAAAAMZG_6j-oQPrcFfyvqD0h9ANEUq8";	// $metadata["recaptcha_privatekey"];
	
	// $metadata contains the submitted data
	if ($_REQUEST["recaptcha_response_field"]) 
	{
		require_once(NXS_FRAMEWORKPATH . '/plugins/recaptcha/recaptchalib.php');
		
  	$resp = recaptcha_check_answer
  	(
  		$privatekey,
      $_SERVER["REMOTE_ADDR"],
      $_REQUEST["recaptcha_challenge_field"],
      $_REQUEST["recaptcha_response_field"]
    );

    if ($resp->is_valid) 
    {
    	// echo "You got it!";
    	$result["validationerrors"][] = nxs_l18n__("Captcha was correctly entered", "nxs_td");
    } 
    else 
    {
    	$result["validationerrors"][] = nxs_l18n__("Wrong Captcha", "nxs_td");
      # set the error code so that we can display it
      // $error = $resp->error;
    }
	}
	else
	{
		$result["validationerrors"][] = nxs_l18n__("The captcha is required", "nxs_td");
	}
	
	/*
	nxs_requirewidget("contactbox");
	$prefix = nxs_widgets_contactbox_getclientsideprefix($postid, $placeholderid);
	
	if ($overriddenelementid != "")
	{
		$key = $overriddenelementid;
	}
	else
	{
		$key = $prefix . $elementid;
	}	
	$value = $_REQUEST[$key];
	*/
	
	//$result["output"] = "$formlabel: $value";
	
	return $result;
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_formitemcaptcha_renderincontactbox($args)
{
	extract($args);
	
	extract($metadata, EXTR_PREFIX_ALL, "metadata");
	
	//
	require_once(NXS_FRAMEWORKPATH . '/plugins/recaptcha/recaptchalib.php');

	// TODO: get publickey from the configuration
	$publickey = "6Lfw-fgSAAAAAB1jhlOKAo4mYJzGlQAUjp3nDZIz";	// $metadata["recaptcha_publickey"];
  
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
	
	ob_start();
	$metadata_isrequired = "true";
	?>
	
  <label class="field_name"><?php echo $metadata_formlabel;?><?php if ($metadata_isrequired != "") { ?>*<?php } ?></label>
  <?php
  echo recaptcha_get_html($publickey);
  
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
				Recaptcha.reload();
			}
		);
  </script>
  <?php
  
	// var_dump($args);
	
	$html = ob_get_contents();
	ob_end_clean();

	
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
	
	$lookup = wp_get_attachment_image_src($image_imageid, 'full', true);
	
	$width = $lookup[1];
	$height = $lookup[2];		
	
	$lookup = wp_get_attachment_image_src($image_imageid, 'thumbnail', true);
	$url = $lookup[0];

	global $nxs_global_placeholder_render_statebag;
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["enable_decoratewidget"] = false;
	$hovermenuargs["enable_deletewidget"] = false;
	$hovermenuargs["enable_deleterow"] = true;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);	
	
	/* ADMIN EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-formitemcaptcha-item";
	
	/* ADMIN OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	echo '
	<div ' . $class . '>
		<div class="content2">
			<div class="box">
	        	<div class="box-title">
					<h4>Text input element: ' . $formlabel . '</h4>
				</div>
				<div class="box-content"></div>
			</div>
			<div class="nxs-clear"></div>
		</div>
	</div>';
	
	/* ------------------------------------------------------------------------------------------------- */

	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = ob_get_contents();
	ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

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
			// GENERAL			
			
			array
			( 
				"id" 				=> "formlabel",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Label", "nxs_td"),
				"placeholder" => nxs_l18n__("Label goes here", "nxs_td"),
			),
			
			array
			( 
				"id" 				=> "elementid",
				"type" 				=> "input",
				"visibility"	=> "hide",
				"label" 			=> nxs_l18n__("Element ID", "nxs_td"),
				"placeholder" => nxs_l18n__("Enter a unique ID for this element", "nxs_td"),
			),
			/*
			can only be set by code			
			array
			( 
				"id" 				=> "overriddenelementid",
				"type" 				=> "input",
				"visibility"	=> "text",
				"label" 			=> nxs_l18n__("Override default element ID", "nxs_td"),
				"placeholder" => nxs_l18n__("Leave blank to use default", "nxs_td"),
			),
			*/
			array
			( 
				"id" 				=> "secretvalue",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Secret value", "nxs_td"),
			),
			
			array
			( 
				"id" 				=> "placeholder",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Placeholder", "nxs_td"),
			),

			array
			( 
				"id" 				=> "initialtext",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Initial text", "nxs_td"),
			),
			
			array(
				"id" 				=> "numofrows",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Number of lines", "nxs_td"),
				"dropdown" 			=> array
				(
					"1"	=> "1", 
					"2"	=> "2",
					"3"	=> "3",
					"4"	=> "4",
					"5"	=> "5",
					"6"	=> "6",
					"7"	=> "7",
					"8"	=> "8",
					"9"	=> "9",
					"10"	=> "10"
				),
			),
		)
	);
	
	return $options;
}

function nxs_widgets_formitemcaptcha_initplaceholderdata($args)
{
	extract($args);

	$args["elementid"] = nxs_generaterandomstring(6);
	$args["numofrows"] = "1";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
