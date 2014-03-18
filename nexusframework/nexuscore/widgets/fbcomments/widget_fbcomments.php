<?php
/* 
	TABLE OF CONTENTS
	----------------------------------------------------------------------------------------------------
	- WIDGET HTML
	- WIDGET POPUP
	- MEDIA MANAGER
	- UPDATING WIDGET DATA
*/

// Setting the widget title
function nxs_widgets_fbcomments_gettitle() {
	$widget_name = basename(dirname(__FILE__));
	return __($widget_name);
}


/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_fbcomments_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	// The following array is used in the "popup.php" file: the main php file that renders the widget popup
	// and returns the user's chosen values and options.
	// You can choose the from the following popup UI options: input, textarea, image, article_link, select
	// its also used in the updateplaceholder function.
	
	// Each UI option has the following required and optional values:
	
	// ID: 				The internal identification used to store the sessiondata with javascript, this ID corresponds to the PHP variable name used in the htmlvisualization function below
	// TYPE:			Denotes the type of UI option 
	// LABEL:			The label used in the popup to explain what the UI does (e.g. "Button text" or "Choose image")
	// PLACEHOLDER: 	Value containing optional textarea and input placeholder text
	// INITIALVALUE: 	Defines the value that is used when the widget is constructed (dragged on the screen)
	// DROPDOWN: 		Array containing the values shown when using the "select" type
	
	// It's a best practice to prefix the used variables with the name of the widget folder and an underscore ("_") to prevent PHP naming conflicts

	$options = array
	(
		"sheettitle" => nxs_widgets_fbcomments_gettitle(),
		"sheeticonid" => nxs_widgets_fbcomments_geticonid(),

		"fields" => array
		(
			/*
			array(
			"id"				=> "foobar",
			"type" 				=> "input",
			"label" 			=> "Foo bar",
			"tooltip" 			=> "Foo bar."
			)
			*/
		)		
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}


/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_fbcomments_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));

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
	
	$url = nxs_geturl_for_postid($postid);
	$postcount = 10;	// todo: make configurable
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	if ($shouldrenderalternative) {
		nxs_renderplaceholderwarning(nxs_l18n__("Missing input", "nxs_td"));
	} 
	else 
	{
		echo '<h2>comments below:</h2>';
		// note; the data-width needs to be overriden in the CSS to support 100% width
		echo '	
		<div class="fb-comments" data-href="' . $url . '" data-width="470" data-num-posts="' . $postcount . '"></div>
		'; 
		
		echo '	
		<!-- enable refresh of widgets after AJAX update -->
		<script type="text/javascript">
			// todo: check if this works ok... if the method on the following line is not found, it means we have
			// to execute the following line in a window.load/ready event... and see if that is executed correctly,
			// after an ajax update. If not, we have to inject the lines of the function inline.
			nxs_js_rerender_facebookbom();
		</script>
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

?>
