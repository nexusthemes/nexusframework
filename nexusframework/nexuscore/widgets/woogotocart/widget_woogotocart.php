<?php

function nxs_widgets_woogotocart_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_woogotocart_gettitle() {
	return nxs_l18n__("woogotocart", "nxs_td");
}

// Unistyle
function nxs_widgets_woogotocart_getunifiedstylinggroup() {
	return "woogotocartwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_woogotocart_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$options = array
	(
		"sheettitle" => nxs_widgets_woogotocart_gettitle(),
		"sheeticonid" => nxs_widgets_woogotocart_geticonid(),
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/woogotocart-widget/"),		
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_woogotocart_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			// BUTTON
			
			array( 
				"id" 				=> "wrapper_button_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Button", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "button_text",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Go to cart text", "nxs_td"),
				"placeholder"		=> "Cart",
				"localizablefield"	=> true
			),	
			
			array(
				"id" 				=> "button_text_empty",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Text when cart is empty", "nxs_td"),
				"placeholder"		=> "Alternative",
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "destination_url_empty",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("External link", "nxs_td"),
				"placeholder"		=> nxs_l18n__("http://www.nexusthemes.com", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an external source using the full url.", "nxs_td"),
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "destination_articleid_empty",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Article link empty cart", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an alternative article if the cart is empty.", "nxs_td"),
			),
			
			array(
				"id" 				=> "button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true,
			),
			array( 
				"id" 				=> "button_color",
				"type" 				=> "colorzen", // "select",
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "button_alignment",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_halignment"),
				"unistylablefield"	=> true,
			),
			array( 
				"id" 				=> "wrapper_button_end",
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

function nxs_widgets_woogotocart_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));

	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_woogotocart_getunifiedstylinggroup(), $unistyle);
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
	$image_imageid == "" &&
	$title == "" &&
	$subtitle == "" &&
	nxs_has_adminpermissions()) {
		$shouldrenderalternative = true;
	}
	
 	// Image
	if ($image_imageid != "") {     
		// Core WP function returns ID ($woogotocart_id), size of image (thumbnail, medium, large or full)
		// This is a generic function to return a variable which contains the image chosen from the media manager
		$imagemetadata= wp_get_attachment_image_src($image_imageid, 'full', true);
		
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$imageurl 		= $imagemetadata[0];
		$imagewidth 	= $imagemetadata[1] . "px";
		$imageheight 	= $imagemetadata[2] . "px";	
	}

	$alignment_image = "";
	// Alignment: text and image
	if ($halign != "") {
				
		if ($halign == "left") {
			
			$alignment_image 		= "float: left; margin-right: 15px;";
			$alignment_table_text 	= "display: table; float: left;";
			$alignment_cell_text 	= "display: table-cell; vertical-align: middle; text-align: left;";
			$alignment_imageheight	= $imageheight;
		
		} else if ($halign == "center") {
		
			$alignment_image 		= "margin: 0 auto;";
			$alignment_cell_text 	= "text-align: center;";
		
		} if ($halign == "right") {
			
			$alignment_image 		= "float: right; margin-left: 15px;";
			$alignment_table_text 	= "display: table; float: right;";
			$alignment_cell_text 	= "display: table-cell; vertical-align: middle; text-align: right;";
			$alignment_imageheight	= $imageheight;
		}
	} 
	
	// Title
	$title_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $title_fontsize);
	$cssclasses = nxs_concatenateargswithspaces("title", "nxs-title", $title_fontsize_cssclass);
	if ($title != "") 		{ $title = '<span class="' . $cssclasses . '">' . $title . '</span>'; }

	// Subtitle
	$subtitle_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $subtitle_fontsize);
	$cssclasses = nxs_concatenateargswithspaces("subtitle", "nxs-title", $subtitle_fontsize_cssclass);
	if ($subtitle != "") 	{ $subtitle = '<span class="' . $cssclasses . '">' . $subtitle . '</span>'; }
 
 	global $post;
 	//var_dump($post);
 	//
	global $woocommerce;
	$product_id = $post->ID;
	
	$found = false;
	//check if product already in cart
	if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) 
	{
		$found = true;
	}
	else 
	{
		// not found	
		//echo "cart is empty";
	}
 
	$target = "_self";
	
	// Positioning
	if ($top != "" || $left != "") 	{ $absolute = 'nxs-absolute'; }
	if ($top != "") 				{ $top = 'top: ' . $top . ';'; }
	if ($left != "") 				{ $left = 'left: ' . $left . ';'; }
	
	$image_alt = trim($image_alt);
	$image_alt = str_replace("\"", "&quote;", $image_alt);
	
	$image_alt_attribute = "";
	if ($image_alt != "")
	{
		$image_alt_attribute = 'alt="' . $image_alt . '" ';
	}
	
	// woogotocart
	if ($image_imageid != "") {
		$woogotocart = '
			<div class="woogotocart-image" style="max-width: ' . $imagewidth . ';  ' . $alignment_image . ' ">
				<img ' . $image_alt_attribute . ' src="' . $imageurl . '" class="nxs-stretch" style="max-width: ' . $imagewidth . ';" />
			</div>';
	}
	
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
		global $product;
		global $post;

		if (nxs_is_nxswebservice())
		{
			global $wp_query;
			if ($wp_query->have_posts())
			{
				the_post();
				$product = get_product(get_the_ID());
			}
		}
		
		if ($found)
		{
			//echo "<a href='{$url}'>Already in cart</a>";
			$cart_post_id = woocommerce_get_page_id( 'cart' );
			$url = nxs_geturl_for_postid($cart_post_id);
			$destination_url = $url;
			$destination_articleid = 0;
			$destination_js = "";
			$button_text = $button_text;
			$destination_target = "_self";
			$htmlforbutton = nxs_gethtmlforbutton($button_text, $button_scale, $button_color, $destination_articleid, $destination_url, $destination_target, $button_alignment, $destination_js);
			echo $htmlforbutton;
		}
		else
		{
			if (!is_product())
			{
				// if its not a product, don't show anything
				$button_text = "";
			}
			else 
			{
				$destination_url = $destination_url_empty;
				$destination_articleid = $destination_articleid_empty;
				$destination_js = "";
				$button_text = $button_text_empty;
				$destination_target = "_self";
				$htmlforbutton = nxs_gethtmlforbutton($button_text, $button_scale, $button_color, $destination_articleid, $destination_url, $destination_target, $button_alignment, $destination_js);
				echo $htmlforbutton;
			}
		}
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

function nxs_widgets_woogotocart_initplaceholderdata($args)
{
	extract($args);

	$homepageid = nxs_gethomepageid();
	$args['title'] = nxs_l18n__("title", "nxs_td");
	$args['subtitle'] = nxs_l18n__("subtitle", "nxs_td");
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_woogotocart_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
