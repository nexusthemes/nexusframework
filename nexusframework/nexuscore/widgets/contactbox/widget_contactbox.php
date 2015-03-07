<?php

function nxs_widgets_contactbox_geticonid() {
	//$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-contact";
}

// Setting the widget title
function nxs_widgets_contactbox_gettitle() {
	return nxs_l18n__("Contact box", "nxs_td");
}

// Unistyle
function nxs_widgets_contactbox_getunifiedstylinggroup() {
	return "contactboxwidget";
}

// Used by the individual contactitem widgets to determine an automated unique ID
function nxs_widgets_contactbox_getclientsideprefix($postid, $placeholderid) {
	$result = "nxs_cf_" . $postid . "_" . $placeholderid . "_";
	return $result;
}

function nxs_widgets_renderincontactbox($widget, $args) {
	$functionnametoinvoke = 'nxs_widgets_' . $widget . '_renderincontactbox';
	// invokefunction
	if (function_exists($functionnametoinvoke)) {
		$result = call_user_func($functionnametoinvoke, $args);
	} else {
		nxs_webmethod_return_nack("function not found; " . $functionnametoinvoke);	
	}
	return $result;
}


/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_contactbox_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_contactbox_gettitle(),
		"sheeticonid" => nxs_widgets_contactbox_geticonid(),
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/contact-box-widget/"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_contactbox_getunifiedstylinggroup(),
		),		
		"fields" => array
		(
			// TITLE	
			
			array( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> "Title",
				"initial_toggle_state"	=> "closed",
			),
			
			array
			(
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" => nxs_l18n__("Title goes here", "nxs_td"),
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title importance", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "title_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
						
			array(
				"id" 				=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Override title fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "top_info_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Top info color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id"     			=> "top_info_padding",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Top info padding", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("padding"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "icon",
				"type" 				=> "icon",
				"label" 			=> nxs_l18n__("Icon", "nxs_td"),
			),
			array(
				"id"     			=> "icon_scale",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Icon scale", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("icon_scale"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_heightiq",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align titles", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's title will participate in the title alignment of other partipating widgets in this row", "nxs_td"),
				"unistylablefield"	=> true,
			),
			
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),			
			
			// Form data
			
			array( 
				"id" 				=> "wrapper_selection_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Form data", "nxs_td"),
			),
			
			array
			(
				"id" 				=> "internal_email",
				"type" 				=> "input",
				"label" 			=> "Internal email",
				"placeholder" 		=> "Internal email",
			),

			array
			(
				"id" 				=> "subject_email",
				"type" 				=> "input",
				"label" 			=> "Subject email",
				"placeholder" 		=> "Subject email",
				"localizablefield"	=> true
			),

			array
			(
				"id" 				=> "sender_email",
				"type" 				=> "input",
				"label" 			=> "Sender email",
				"placeholder" 		=> "Internal email",
			),
			
			array
			(
				"id" 				=> "sender_name",
				"type" 				=> "input",
				"label" 			=> "Sender name",
				"placeholder" 		=> "Name of email sender",
			),
			
			array(
				"id" 				=> "items_genericlistid",
				"type" 				=> "staticgenericlist_link",
				"label" 			=> nxs_l18n__("Form elements", "nxs_td")
			),
			
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Thank you page", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Thank the visitor for taking the time to send an email by redirecting them to this page.", "nxs_td"),
			),
			
			array(
				"id" 				=> "mail_body_includesourceurl",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Include form source URL", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the source URL from where this form was posted will be included in the email send", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_selection_end",
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
				"localizablefield"	=> true
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
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "button_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Button alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "button_color",
				"type" 				=> "colorzen", // "select",
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"unistylablefield"	=> true
			),

			array( 
				"id" 				=> "wrapper_button_end",
				"type" 				=> "wrapperend",
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

function nxs_widgets_contactbox_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// Set options with unistyled params
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_contactbox_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);	
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Localize atts
	$mixedattributes = nxs_localization_localize($mixedattributes);
	
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

	global $nxs_global_row_render_statebag;
	global $nxs_global_placeholder_render_statebag;
		
	// Appending custom widget class
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-contact ";
	
	
	
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */

	$structure = nxs_parsepoststructure($items_genericlistid);
	
	if ($button_text == "") 
	{
		$alternativemessage = nxs_l18n__("Warning: no button text", "nxs_td");
	}
	
	if (!(isset($destination_articleid) && $destination_articleid != "" && $destination_articleid != 0)) 
	{
		$alternativemessage = nxs_l18n__("Minimal: destination article", "nxs_td");
	} 
	
	if ($internal_email == "") 
	{
		$alternativemessage = nxs_l18n__("Warning: internal email is not set", "nxs_td");
	}
	else
	{
		// ensure its valid
		if (!nxs_isvalidemailaddress($internal_email))
		{
			$alternativemessage = nxs_l18n__("Warning: internal email is not filled with a valid email address", "nxs_td");
		}	
	}
	
	if (count($structure) == 0) {
		$alternativemessage = nxs_l18n__("Warning:no items found", "nxs_td");
	}
	
	// Button icon
	$iconrightcssclass_button = nxs_getcssclassesforlookup('', $button_icon_right);
	if ($button_icon_right == "") { $button_icon_right = ''; } 
	else { $button_icon_right = '<span class="' . $iconrightcssclass_button . '"></span>'; }
		
	$button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $button_scale);
	$button_alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $button_alignment);
	$button_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $button_color);
	
	$invoke = "nxs_js_lazyexecute('/nexuscore/widgets/contactbox/js/contactbox.js', true, 'nxs_js_contactbox_send(" .  $postid . ", &quot;" . $placeholderid . "&quot;);');";

	// Button
	$htmlbutton = '
	<p class="' . $button_alignment_cssclass . '">
		<a id="' . $placeholderid . '_button" 
			class="nxs-button ' . $button_color_cssclass . ' ' . $button_scale_cssclass . '" 
			href="#" 
			onclick="' . $invoke . '; return false;">' 
			. $button_text . $button_icon_right . ' 
		</a>
	</p>';
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	/* TITLE
	---------------------------------------------------------------------------------------------------- */
	
	// Title heading
	if ($title_heading != "") {
		$title_heading = "h" . $title_heading;	
	} else {
		$title_heading = "h1";
	}

	// Title alignment
	$title_alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $title_alignment);
	
	if ($title_alignment == "center") { $top_info_title_alignment = "margin: 0 auto;"; } else
	if ($title_alignment == "right")  { $top_info_title_alignment = "margin-left: auto;"; } 
	
	// Title fontsize
	$title_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $title_fontsize);

	// Title height (across titles in the same row)
	// This function does not fare well with CSS3 transitions targeting "all"
	$heightiqprio = "p1";
	$title_heightiqgroup = "title";
  	$titlecssclasses = $title_fontsize_cssclass;
	$titlecssclasses = nxs_concatenateargswithspaces($titlecssclasses, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$title_heightiqgroup}");
	
	// Top info padding and color
	$top_info_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $top_info_color);
	$top_info_padding_cssclass = nxs_getcssclassesforlookup("nxs-padding-", $top_info_padding);
	
	// Icon scale
	$icon_scale_cssclass = nxs_getcssclassesforlookup("nxs-icon-scale-", $icon_scale);
		
	// Icon
	if ($icon != "") {$icon = '<span class="'.$icon.' '.$icon_scale_cssclass.'"></span>';}
	
	// Title
	$titlehtml = '<'.$title_heading.' class="nxs-title '.$title_alignment_cssclass.' '.$title_fontsize_cssclass.' '.$titlecssclasses.'">'.$title.'</'.$title_heading.'>';	
	
	// Filler
	$htmlfiller = nxs_gethtmlforfiller();
	

	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	if ($alternativemessage != "" && $alternativemessage != null)
	{
		nxs_renderplaceholderwarning($alternativemessage);
	} 
	else 
	{
		?>
		<script type='text/javascript'>
			// opens a contact thumbnail in a lightbox
			function nxs_js_opencontactitemlightbox(element)
			{
				if (nxs_js_popup_anyobjectionsforopeningnewpopup())
				{
					// opening a new popup is not allowed; likely some other popup is already opened
					return;
				}
				
				var contactitem = jQuery(element).closest(".nxs-contactitem")[0];
				//nxs_js_log("contactitem:");
				//nxs_js_log(contactitem);
				var contactitemid = contactitem.id;	// bijv. nxs-contactitem-{contactid}-{index}-{imageid}
				var contactid = contactitemid.split("-")[2];
				var index = contactitemid.split("-")[3];
				var imageid = contactitemid.split("-")[4];
				
				// initiate a new popupsession data as this is a new session
				nxs_js_popupsession_startnewcontext();
				
				// move contactbox sheet implementation to seperate file, not in site.php
				nxs_js_popup_setsessioncontext("popup_current_dimensions", "contactbox");
				nxs_js_popup_setsessioncontext("contextprocessor", "contactbox");
				nxs_js_popup_setsessioncontext("contactid", contactid);
				nxs_js_popup_setsessioncontext("imageid", imageid);
				nxs_js_popup_setsessioncontext("index", '' + index + '');
			
				// show the popup
				nxs_js_popup_navigateto("detail");
			}
		</script>
		
		<?php
		
		if ($icon == "" && $title == "")
		{
			// nothing to show
		}		
		else if (($top_info_padding_cssclass != "") || ($icon != "") || ($top_info_color_cssclass != "")) {
			 
			// Icon title
			echo '
			<div class="top-wrapper nxs-border-width-1-0 '.$top_info_color_cssclass.' '.$top_info_padding_cssclass.'">
				<div class="nxs-table" style="'.$top_info_title_alignment.'">';
				
					// Icon
					echo $icon;
					
					// Title
					if ($title != "")
					{
						echo $titlehtml;
					}
					echo '
				</div>
			</div>';
		
		} else {
		
			// Default title
			if ($title != "")
			{
				echo $titlehtml;
			}
		
		}
		
		if ($title != "" || $icon != "") { 
			echo $htmlfiller; 
		}
		
		echo "<div class='nxs-form'>";
		
		$index = -1;
		foreach ($structure as $pagerow)
		{
			$index = $index + 1;
			$rowcontent = $pagerow["content"];
			$currentplaceholderid = nxs_parsepagerow($rowcontent);
			$currentplaceholdermetadata = nxs_getwidgetmetadata($items_genericlistid, $currentplaceholderid);
			$widget = $currentplaceholdermetadata["type"];
			
			if ($widget != "" && $widget != "undefined")
			{
				$requirewidgetresult = nxs_requirewidget($widget);
			 	if ($requirewidgetresult["result"] == "OK")
			 	{
			 		// now that the widget is loaded, instruct the widget to register the needed hooks
			 		// if it has some
			 		$hookargs = array();
			 		$hookargs["postid"] = $postid;
			 		$hookargs["placeholderid"] = $placeholderid;
			 		$hookargs["metadata"] = $currentplaceholdermetadata;
			 		
			 		
			 		$subresult = nxs_widgets_renderincontactbox($widget, $hookargs);
			 		if ($subresult["result"] == "OK")
			 		{
			 			// append subresult to the overall result
			 			echo $subresult["html"];
			 		}
			 		else
			 		{
			 			echo "[warning, widget found, but returned an error?]";
			 			var_dump($subresult);
			 		}
			 	}
			 	else
			 	{
			 		// 
			 		echo "[warning, widget not found?]";
			 	}
			}
			else
			{
				// empty widget is ignored
			}
		}
		
		echo $htmlfiller;
		echo $htmlbutton;
		
		echo "</div>";	// end of nxs-form
		
		echo "<div class='nxs-clear'></div>";
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

/* INITIATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_contactbox_initplaceholderdata($args)
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	
	// create a new generic list with subtype contact
	// assign the newly create list to the list property
	
	$subargs = array();
	$subargs["nxsposttype"] = "genericlist";
	$subargs["nxssubposttype"] = "contact";	// NOTE!
	$subargs["poststatus"] = "publish";
	$subargs["titel"] = nxs_l18n__("contact items", "nxs_td");
	$subargs["slug"] = $subargs["titel"];
	$subargs["postwizard"] = "defaultgenericlist";
	
	$response = nxs_addnewarticle($subargs);
	if ($response["result"] == "OK")
	{
		$args["items_genericlistid"] = $response["postid"];
		$args["items_genericlistid_globalid"] = nxs_get_globalid($response["postid"], true);
	}
	else
	{
		var_dump($response);
		die();
	}
	
	global $current_user;
	get_currentuserinfo();
	
	$args["internal_email"] = $current_user->user_email;
	
	$args["title_heading"] = "2";	
	$args['title_heightiq'] = "true";		
	$args['mail_body_includesourceurl'] = "true";
	$args["subject_email"] = nxs_l18n__("A webform was submitted", "nxs_td");
	
	$args["button_text"] = nxs_l18n__("Send", "nxs_td");
	$args["button_color"] = "base2";
	$args["button_scale"] = "1-6";
	$args["button_alignment"] = "left";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_contactbox_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	$result = nxs_widgets_initplaceholderdatageneric($args, $widgetname);
	return $result;
}

?>
