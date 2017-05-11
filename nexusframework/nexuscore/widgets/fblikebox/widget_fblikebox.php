<?php

function nxs_widgets_fblikebox_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-facebook";
}

// Setting the widget title
function nxs_widgets_fblikebox_gettitle() {
	return nxs_l18n__("Like box", "nxs_td");
}

// Unistyle
function nxs_widgets_fblikebox_getunifiedstylinggroup() {
	return "fblikeboxwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_fblikebox_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_fblikebox_gettitle(),
		"sheeticonid" => nxs_widgets_fblikebox_geticonid(),
		"sheethelp" => nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1764396204"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_fblikebox_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			
			/* TITLE
			---------------------------------------------------------------------------------------------------- */
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
			),
			
			array(
				"id" 				=> "likebox_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Facebook page URL", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("The absolute URL of the Facebook Page that will be liked. This is a required setting.", "nxs_td"),
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "likebox_colorscheme",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Colorscheme", "nxs_td"),
				"dropdown" 			=> array
				(
					"light" => nxs_l18n__("light", "nxs_td"),
					"dark" => nxs_l18n__("dark", "nxs_td"),
				),
				"tooltip" 			=> nxs_l18n__("The color scheme used by the plugin. Can be 'light' or 'dark'.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "likebox_faces",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show faces", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Specifies whether to display profile photos of people who like the page.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "likebox_border",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show border", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Specifies whether or not to show a border around the plugin.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "likebox_stream",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show stream", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Specifies whether to display a stream of the latest posts by the Page.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "likebox_height",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Height", "nxs_td"),
				"placeholder" => nxs_l18n__("The height of the plugin in pixels. This is optional.", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("The height of the plugin in pixels. This is optional.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "likebox_color",
				"type" 				=> "colorzen", 
				"label" 			=> nxs_l18n__("Background color", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_input_end",
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

function nxs_widgets_fblikebox_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// Unistyle
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_fblikebox_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);

	//
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
	// Turn on output buffering
	nxs_ob_start();
	
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
	if (
		$likebox_url == ""
	) {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("You haven't set the url of the specific Facebook page.", "nxs_td");
	}
	
	// Likebox color
	$likebox_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $likebox_color);
	
	// if ($likebox_header == "") 	{ $likebox_header = "false"; } 	else { $likebox_header = "true"; }
	if ($likebox_faces == "") 	{ $likebox_faces = "false"; } 	else { $likebox_faces = "true"; }
	if ($likebox_border == "") 	{ $likebox_border = "false"; } 	else { $likebox_border = "true"; }
	if ($likebox_stream == "") 	{ $likebox_stream = "false"; } 	else { $likebox_stream = "true"; }
	
	if ($likebox_height == "" && $likebox_faces == "true" & $likebox_stream == "true") 	{ $likebox_height = "571px"; } else
	if ($likebox_height == "" && $likebox_faces == "true" & $likebox_stream == "false") { $likebox_height = "271px"; } else
	if ($likebox_height == "" && $likebox_faces == "false" & $likebox_stream == "true") { $likebox_height = "425px"; }
		
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) {
		if ($alternativehint == "") {
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} else {
		
		global $locale;

		// Likebox
		echo '
		<div class="fb-like-box-wrapper '.$likebox_color_cssclass.'" style="height: '.$likebox_height.'">
			<div class="fb-like-box" 
				data-href="'.$likebox_url.'" 
				data-height="'.$likebox_height.'" 
				data-colorscheme="'.$likebox_colorscheme.'" 
				data-show-faces="'.$likebox_faces.'" 
				data-header="true" 
				data-stream="'.$likebox_stream.'" 
				data-show-border="'.$likebox_border.'">
			</div>
		</div>
		
		<script>
    	window.fbAsyncInit = function() 
    	{
    		nxs_js_log("**** fbAsyncInit invoked");
    		
    		if (typeof(FB) != "undefined" && FB != null ) 
				{
					FB.XFBML.parse();

      		nxs_js_log("**** FB.XFBML.parse finished");

      		// tell the layout engine to post process the layout
					// after the DOM is updated
					nxs_gui_set_runtime_dimensions_enqueuerequest("nxs-framework-fbpostprocessor");

				}
    		
    	}
    </script>
		
		';		
		
		echo '
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/' . $locale . '/all.js#xfbml=1";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, \'script\', \'facebook-jssdk\'));
		</script>';
	
			
		
	}
	
	/* ------------------------------------------------------------------------------------------------- */
	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	return $result;
}

function nxs_widgets_fblikebox_initplaceholderdata($args)
{
	extract($args);

	$args['likebox_faces'] = "checked";
	$args['likebox_border'] = "checked";
	$args['likebox_stream'] = "checked";
	$args['likebox_colorscheme'] = "light";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_fblikebox_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
