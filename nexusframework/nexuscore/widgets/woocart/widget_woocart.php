<?php

function nxs_widgets_woocart_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_woocart_gettitle() {
	return nxs_l18n__("woocart", "nxs_td");
}

// Unistyle
function nxs_widgets_woocart_getunifiedstylinggroup() {
	return "woocartwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_woocart_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$options = array
	(
		"sheettitle" => nxs_widgets_woocart_gettitle(),
		"sheeticonid" => nxs_widgets_woocart_geticonid(),
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/woocart-widget/"),		
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_woocart_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			array(
				"id" 				=> "layout_type",
				"type" 				=> "select",
				"popuprefreshonchange"	=> "true",
				"label" 			=> nxs_l18n__("Type", "nxs_td"),
				"dropdown" 			=> array
				(
					"cartdetail"	=>nxs_l18n__("Cart detail page", "nxs_td"),
					"cartsummary" =>nxs_l18n__("Cart summary", "nxs_td"),
				),
				"unistylablefield"	=> true
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

function nxs_widgets_woocart_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_woocart_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);	
	}
	
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
	if (
	$image_imageid == "" &&
	$title == "" &&
	$subtitle == "" &&
	nxs_has_adminpermissions()) {
		$shouldrenderalternative = true;
	}
	
	if (
	$destination_url != "" && $destination_articleid != ""
	) 
	{
		$shouldrenderalternative = true;
	}
	
 	// Image
	if ($image_imageid != "") {     
		// Core WP function returns ID ($woocart_id), size of image (thumbnail, medium, large or full)
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
 
	// Url
	if ($destination_articleid != "") 
	{ 
		$url = nxs_geturl_for_postid($destination_articleid); 
		$target = "";
	} 
	if ($destination_url != "") 
	{
		$url = $destination_url; 
		$target = " target='_blank' ";
	}
	
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
	
	// woocart
	if ($image_imageid != "") {
		$woocart = '
			<div class="woocart-image" style="max-width: ' . $imagewidth . ';  ' . $alignment_image . ' ">
				<img ' . $image_alt_attribute . ' src="' . $imageurl . '" class="nxs-stretch" style="max-width: ' . $imagewidth . ';" />
			</div>';
	}
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	if ($shouldrenderalternative) 
	{
		nxs_renderplaceholderwarning(nxs_l18n__("Missing input", "nxs_td"));
	} 
	else 
	{
		if ($layout_type == "cartdetail" || $layout_type == "")
		{
			echo do_shortcode("[woocommerce_cart]");	
		} 
		else if ($layout_type == "cartsummary")
		{
			global $woocommerce;
			?>
			<ul class="mini-cart nav">
			  <li>
			  	<a href="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" title="<?php esc_attr_e('View your shopping cart', 'woothemes'); ?>" class="cart-parent">
			  		<span>
		    			<?php 
		    				echo sprintf(_n('<mark>%d item</mark>', '<mark>%d items</mark>', $woocommerce->cart->cart_contents_count, 'woothemes' ), $woocommerce->cart->cart_contents_count);
			    			echo $woocommerce->cart->get_cart_total();
		    			?>
    				</span> 
    			</a>
    			<?php
		      	echo '<ul class="cart_list">';
            echo '<li class="cart-title"><h3>' . nxs_l18n__("Cart", "nxs_td") . '</h3></li>';
            if (sizeof($woocommerce->cart->cart_contents)>0) : 
            	foreach ($woocommerce->cart->cart_contents as $cart_item_key => $cart_item) :
								$_product = $cart_item['data'];
		            if ($_product->exists() && $cart_item['quantity']>0) :
									echo '<li class="cart_list_product"><a href="' . esc_url( get_permalink( intval( $cart_item['product_id'] ) ) ) . '">';
			    	     	echo $_product->get_image();
	                echo apply_filters( 'woocommerce_cart_widget_product_title', $_product->get_title(), $_product ) . '</a>';
					        if($_product instanceof woocommerce_product_variation && is_array($cart_item['variation'])) :
	                   echo woocommerce_get_formatted_variation( $cart_item['variation'] );
	                endif;
	                echo '<span class="quantity">' . $cart_item['quantity'] . ' &times; ' . woocommerce_price( $_product->get_price() ) . '</span></li>';
	  	          endif;
    	        endforeach;
           	else: 
           		echo '<li class="empty">' . __( 'No products in the cart.', 'woothemes' ) . '</li>'; 
           	endif;
          	if ( sizeof( $woocommerce->cart->cart_contents ) > 0 ) :
            	echo '<li class="total">';
              if ( get_option( 'js_prices_include_tax' ) == 'yes' ) :
			          _e( 'Total', 'woothemes' );
              else :
                _e( 'Subtotal', 'woothemes' );
              endif;
              echo ':' . $woocommerce->cart->get_cart_total() . '</li>';
              echo '<li class="buttons"><a href="' . esc_url( $woocommerce->cart->get_cart_url() ) . '" class="button">' . nxs_l18n__("View cart", "nxs_td") . '</a> <a href="' . esc_url( $woocommerce->cart->get_checkout_url() ) . '" class="button checkout">' . nxs_l18n__("Checkout", "nxs_td") . '</a></li>';
            endif;
            echo '</ul>';
	        ?>
		  	</li>
			</ul>
			<?php
		}
		else
		{
			// nothing to do here
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

function nxs_widgets_woocart_initplaceholderdata($args)
{
	extract($args);

	$args['layout_type'] = "cartdetail";

	$homepageid = nxs_gethomepageid();
	$args['destination_articleid'] = $homepageid;
	$args['destination_articleid_globalid'] = nxs_get_globalid($homepageid, true);	// global referentie
	$args['title'] = nxs_l18n__("title", "nxs_td");
	$args['subtitle'] = nxs_l18n__("subtitle", "nxs_td");
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_woocart_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
