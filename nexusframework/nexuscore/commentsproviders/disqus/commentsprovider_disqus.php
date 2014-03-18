<?php

function nxs_commentsprovider_disqus_gettitle()
{
	return nxs_l18n__("Disqus[nxs:commentsprovidertitle]", "nxs_td");
}

function nxs_commentsprovider_disqus_geticonid()
{
	return "nxs-icon-commentsprovider-nativecomments";
}

function nxs_commentsprovider_disqus_getflyoutmenuhtml()
{
		// Turn on output buffering
	ob_start();
	?>
		<!-- disqus has no menu item -->
	<?php
	$result = ob_get_contents();
	ob_end_clean();
	return $result;
}

/*
// Define the properties of this widget
function nxs_commentsprovider_disqus_widgets_home_getoptions() 
{
	// CORE WIDGET OPTIONS

	//$initialcommentstate = $temp_array['initialcommentstate'];

	$options = array
	(
		"sheettitle" => nxs_widgets_comments_gettitle(),
		"sheeticonid" => nxs_widgets_comments_geticonid(),

		"fields" => array
		(
			array
			( 
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Title goes here", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If your callout has an eye-popping title put it here.", "nxs_td")
			),
			array
			( 
				"id" 				=> "initialcommentstate",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Initial comment state", "nxs_td"),
				"dropdown" 			=> array(""=>nxs_l18n__("hold", "nxs_td"), "approved"=>nxs_l18n__("approved", "nxs_td"))
			),
			array( 
				"id" 				=> "comment_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Comment color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample text", "nxs_td"),
			),
			array( 
				"id" 				=> "button_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample", "nxs_td"),
			),
			array(
				"id" 				=> "button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale")
			),
			array( 
				"id" 					=> "padding",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Padding", "nxs_td"),
				"dropdown"   	=> nxs_style_getdropdownitems("padding"),
			),	
			array( 
				"id" 					=> "border_radius",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Border radius", "nxs_td"),
				 "dropdown"   => nxs_style_getdropdownitems("border_radius"),
			),		
			array
			( 
				"id" 					=> "border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Comment border width", "nxs_td"),
				 "dropdown"   => nxs_style_getdropdownitems("border_width"),
			),	
			array
			( 
				"id" 					=> "avatar_border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Avatar border width", "nxs_td"),
				 "dropdown"   => nxs_style_getdropdownitems("border_width"),
			),	
			array
			( 
				"id" 				=> "avatar_shadow",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Avatar shadow", "nxs_td"),
			),		
			array
			(
				"id" 					=> "avatar_size",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image size", "nxs_td"),
				"dropdown" 		=> nxs_style_getdropdownitems("image_size")
			),	
		),
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}
*/

// Define the properties of this widget
function nxs_commentsprovider_disqus_home_getoptions() 
{
	// CORE WIDGET OPTIONS

	// The following array is used in the "popup.php" file: the main php file that renders the widget popup
	// and returns the user's chosen values and options.
	// You can choose the from the following popup UI options: input, textarea, image, article_link, select
	// its also used in the updateplaceholder function.
	
	// Each UI option has the following required and optional values:
	
	// ID: 		The internal identification used to store the sessiondata with javascript, this ID corresponds to the PHP variable name used in the htmlvisualization function below
	// TYPE:		Denotes the type of UI option 
	// LABEL:		The label used in the popup to explain what the UI does (e.g. "Button text" or "Choose image")
	// PLACEHOLDER: Value containing optional textarea and input placeholder text
	// INITIALVALUE: Defines the value that is used when the widget is constructed (dragged on the screen)
	// DROPDOWN: 	Array containing the values shown when using the "select" type
	
	// It's a best practice to prefix the used variables with the name of the widget folder and an underscore ("_") to prevent PHP naming conflicts

	$options = array
	(
		"sheettitle" => "WordPress Native Comments Provider",
		"sheeticonid" => nxs_commentsprovider_disqus_geticonid(),

		"fields" => array
		(
			// TODO: add fields here to configure the behaviour for wordpress native comments 
		),
	);
	
	return $options;
}

// invoked by for example the wordpresstitle widget
function nxs_commentsprovider_disqus_getpostcommentcounthtml($args)
{
	// preconditions
	if (!isset($args["postid"])){ nxs_webmethod_return_nack("postid not set"); }
	
	//
	$postid = $args["postid"];
	$url = nxs_geturl_for_postid($postid);

	// Turn on output buffering
	ob_start();
	?>
	<span class="nxs-icon-comments-2"></span>
	<a href="<?php echo $url; ?>#disqus_thread" class="nxs-comments-count" data-disqus-identifier="<?php echo $postid; ?>"></a>
	<?php
	$result = ob_get_contents();
	ob_end_clean();
	
	return $result;	
}
?>