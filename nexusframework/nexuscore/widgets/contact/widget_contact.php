<?php

function nxs_widgets_contact_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_contact_gettitle() {
	return nxs_l18n__("contact[widgettitle]", "nxs_td");
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_contact_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_contact_gettitle(),
		"sheeticonid" => nxs_widgets_contact_geticonid(),

		"fields" => array
		(
			// RECIPIENT PROPERTIES			
			
			array( 
				"id" 				=> "wrapper_input_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Recipient properties", "nxs_td"),
			),		
			array(
				"id" 				=> "internal_email",		// used by webmethod
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Email address", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("To which address should the notification be send? (enter yours)", "nxs_td"),
			),
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Thank you page", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Thank the visitor for taking the time to send an email by redirecting them to this page.", "nxs_td"),
			),
			array( 
				"id" 				=> "wrapper_input_end",
				"type" 				=> "wrapperend"
			),
			
			// BUTTON PROPERTIES		
			
			array( 
				"id" 				=> "wrapper_button_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Button", "nxs_td"),
			),

			array(
				"id" 				=> "button_text",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Button text", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Read more", "nxs_td"),
			),
			array(
				"id" 				=> "button_icon_right",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button icon", "nxs_td"),
				"dropdown" 			=> array
				(
					""				=> nxs_l18n__("none", "nxs_td"),
					"nxs-icon-arrow-right-2"	=> nxs_l18n__("arrow right 2", "nxs_td"),
				),
			),
			array(
				"id" 				=> "button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale")
			),
			array(
				"id" 				=> "button_alignment",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_halignment")
			),
			array( 
				"id" 				=> "button_color",
				"type" 				=> "colorzen", // "select",
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
			),

			array( 
				"id" 				=> "wrapper_button_end",
				"type" 				=> "wrapperend"
			),
			
			// MISCELLANEOUS
			
			array( 
				"id" 				=> "wrapper_misc_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Miscellaneous", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "text_initialvalue",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Initial text", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Optionally add initial text to show", "nxs_td"),
			),
			array(
				"id" 				=> "text_placeholder",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Placeholder text", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Optionally add a placeholder to show when the message field is blank", "nxs_td"),
			),
			array(
				"id" 				=> "show_phonenumber",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Phonenumber", "nxs_td"),
			),	
			array(
				"id" 				=> "show_company",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Company", "nxs_td"),
			),			
			
			array( 
				"id" 				=> "wrapper_misc_end",
				"type" 				=> "wrapperend"
			),
		)
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}


/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_contact_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;	
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
	// Turn on output buffering
	ob_start();
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));
		
	global $nxs_global_placeholder_render_statebag;
	if ($shouldrenderalternative == true) {
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . "-warning ";
	} else {
		// Appending custom widget class
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " ";
	}
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;
	
	if (!(isset($destination_articleid) && $destination_articleid != "" && $destination_articleid != 0)) {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Minimal: destination article", "nxs_td");
	} if (!($internal_email != "" && nxs_isvalidemailaddress($internal_email))) {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Email address not set or invalid", "nxs_td");
	}
	
	if ($button_text == "") {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Minimal: button text", "nxs_td");
	}

	// Button icon
	$iconrightcssclass_button = nxs_getcssclassesforlookup('', $button_icon_right);
	if ($button_icon_right == "") { $button_icon_right = ''; } 
	else { $button_icon_right = '<span class="' . $iconrightcssclass_button . '"></span>'; }
		
	$button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $button_scale);
	$button_alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $button_alignment);
	$button_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $button_color);
	
	$invoke = "nxs_js_lazyexecute('/nexuscore/widgets/contact/js/contactform.js', true, 'nxs_js_to_verstuurcontactformulier(" .  $postid . ", &quot;" . $placeholderid . "&quot;);');";

	// Button
	$button = '
	<p class="' . $button_alignment_cssclass . '">
		<a id="' . $placeholderid . '_button" 
			class="nxs-button ' . $button_color_cssclass . ' ' . $button_scale_cssclass . '" 
			href="#" 
			onclick="' . $invoke . '; return false;">' 
			. $button_text . $button_icon_right . ' 
		</a>
	</p>';
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) {
		if ($alternativehint == "") {
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} 
	else 
	{
		echo'
		
		<div class="nxs-form">';
		
			// Name
			echo '
			<label class="field_name">' . nxs_l18n__("Name[contact,input]", "nxs_td") . '</label>
			<input type="text" id="' . $placeholderid . '_naam" class="field_name" />
			<div class="nxs-clear"></div>';
			
			// Email
			echo'
			<label class="field_email">' . nxs_l18n__("Email[contact,input]", "nxs_td") . '</label>
			<input type="text" id="' . $placeholderid . '_email" class="field_email" />
			<div class="nxs-clear"></div>';
			
			// Phonenumber
			if ($show_phonenumber != "") {
				echo '
				<label class="field_tel">' . nxs_l18n__("Phonenumber[contact,input]", "nxs_td") . '</label>
				<input type="text" id="' . $placeholderid . '_tel" class="field_tel" />
				<div class="nxs-clear"></div>';
			}
			
			// Company
			if ($show_company != "") {
				echo '
				<label class="field_company">' . nxs_l18n__("Company[contact,input]", "nxs_td") . '</label>
				<input type="text" id="' . $placeholderid . '_company" class="field_company" />
				<div class="nxs-clear"></div>';
			}
			
			// Message
			echo '
			<label class="field_msg">' . nxs_l18n__("Message[contact,input]", "nxs_td") . '</label>
			<textarea id="' . $placeholderid . '_msg" class="field_msg" placeholder="' . nxs_render_html_escape_doublequote($text_placeholder) . '">' . nxs_render_html_escape_gtlt($text_initialvalue) . '</textarea>
			<div class="nxs-clear nxs-padding-top20"></div>';
						
			echo $button;
			echo '
		
		</div>
		'; 
		      
	} 
	
	/* ------------------------------------------------------------------------------------------------- */
	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = ob_get_contents();
	ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	return $result;
}

function nxs_widgets_contact_initplaceholderdata($args)
{
	extract($args);
	
	global $current_user;
	get_currentuserinfo();
	
	$args['internal_email'] = $current_user->user_email;
	$args['button_text'] = nxs_l18n__("Send", "nxs_td");

	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
