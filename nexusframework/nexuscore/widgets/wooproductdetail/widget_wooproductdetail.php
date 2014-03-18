<?php

function nxs_widgets_wooproductdetail_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_wooproductdetail_gettitle() {
	return nxs_l18n__("wooproductdetail", "nxs_td");
}

// Unistyle
function nxs_widgets_wooproductdetail_getunifiedstylinggroup() {
	return "wooproductdetailwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_wooproductdetail_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$options = array
	(
		"sheettitle" => nxs_widgets_wooproductdetail_gettitle(),
		"sheeticonid" => nxs_widgets_wooproductdetail_geticonid(),
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/wooproductdetail-widget/"),		
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_wooproductdetail_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			// wooproductdetail
		)
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_wooproductdetail_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_wooproductdetail_getunifiedstylinggroup(), $unistyle);
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
	
	if (!is_product())
	{
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("No product context found", "nxs_td");
	}
	
	if (
	$destination_url != "" && $destination_articleid != ""
	) 
	{
		$shouldrenderalternative = true;
	}
	
 	// Image
	if ($image_imageid != "") {     
		// Core WP function returns ID ($wooproductdetail_id), size of image (thumbnail, medium, large or full)
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
	
	// wooproductdetail
	if ($image_imageid != "") {
		$wooproductdetail = '
			<div class="wooproductdetail-image" style="max-width: ' . $imagewidth . ';  ' . $alignment_image . ' ">
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

		woocommerce_get_template_part( 'content', 'single-product' );
		
		/*
		
		if (nxs_is_nxswebservice())
		{
			global $wp_query;
			if ($wp_query->have_posts())
			{
				the_post();
				$product = get_product(get_the_ID());
				
				//var_dump($product);
			}
		}
		else
		{
			if (is_product())
			{
				rewind_posts();
				$product = get_product(get_the_ID());
				
				//var_dump($product);
			}
			else
			{
				echo "geen product gevonden";
			}
		}
				
		if (isset($product))
		{
			// TITLE
			
			?>
			<h1 itemprop="name" class="product_title entry-title"><?php the_title(); ?></h1>
			<?php
			
			// SHORT DESCRIPTION
			
			if ($post->post_excerpt )
			{
				?>
				<div itemprop="description">
					<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?>
				</div>
				<?php			
			}
			
			// DESCRIPTION
			
			echo "<p>" . get_the_content() . "</p>";
			echo "<br />";
			
			// IMAGE
			
			if ( has_post_thumbnail() )
			{
				?>		
				<a itemprop="image" href="<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>" class="zoom" rel="thumbnails" title="<?php echo get_the_title( get_post_thumbnail_id() ); ?>"><?php echo get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) ) ?></a>
				<?php 
			}
			else 
			{
				?>
				<img src="<?php echo woocommerce_placeholder_img_src(); ?>" alt="Placeholder" />
				<?php
			}
			
			echo "<br />";
			
			// PRICE
			
			?>
			<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">

				<p itemprop="price" class="price"><?php echo $product->get_price_html(); ?></p>
			
				<meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
				<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />
			
			</div>
			<?php
			
			echo "<br />";
			
			// METADATA (CATEGORIES / TAGS)
		
			?>	
			<div class="product_meta">
	
				<?php if ( $product->is_type( array( 'simple', 'variable' ) ) && get_option('woocommerce_enable_sku') == 'yes' && $product->get_sku() ) : ?>
					<span itemprop="productID" class="sku"><?php _e('SKU:', 'woocommerce'); ?> <?php echo $product->get_sku(); ?>.</span>
				<?php endif; ?>
				
				<?php echo $product->get_categories( ', ', ' <span class="posted_in">'.__('Category:', 'woocommerce').' ', '.</span>'); ?>
				
				<?php echo $product->get_tags( ', ', ' <span class="tagged_as">'.__('Tags:', 'woocommerce').' ', '.</span>'); ?>
			
			</div>
			
			<?php
			
			echo "<br />";
			
			// ATTRIBUTES
									
			$alt = 1;
			$attributes = $product->get_attributes();
			
			if ( empty( $attributes ) && ( ! $product->enable_dimensions_display() || ( ! $product->has_dimensions() && ! $product->has_weight() ) ) )
			{
			}
			else
			{
				?>
				<table class="shop_attributes">
							
					<?php if ( $product->enable_dimensions_display() ) : ?>	
						
						<?php if ( $product->has_weight() ) : $alt = $alt * -1; ?>
						
							<tr class="<?php if ( $alt == 1 ) echo 'alt'; ?>">
								<th><?php _e('Weight', 'woocommerce') ?></th>
								<td><?php echo $product->get_weight() . ' ' . get_option('woocommerce_weight_unit'); ?></td>
							</tr>
						
						<?php endif; ?>
						
						<?php if ($product->has_dimensions()) : $alt = $alt * -1; ?>
						
							<tr class="<?php if ( $alt == 1 ) echo 'alt'; ?>">
								<th><?php _e('Dimensions', 'woocommerce') ?></th>
								<td><?php echo $product->get_dimensions(); ?></td>
							</tr>		
						
						<?php endif; ?>
						
					<?php endif; ?>
							
					<?php foreach ($attributes as $attribute) : 
						
						if ( ! isset( $attribute['is_visible'] ) || ! $attribute['is_visible'] ) continue;
						if ( $attribute['is_taxonomy'] && ! taxonomy_exists( $attribute['name'] ) ) continue;
						
						$alt = $alt * -1; 
						?>
							
						<tr class="<?php if ( $alt == 1 ) echo 'alt'; ?>">
							<th><?php echo $woocommerce->attribute_label( $attribute['name'] ); ?></th>
							<td><?php
								if ( $attribute['is_taxonomy'] ) {
									
									$values = woocommerce_get_product_terms( $product->id, $attribute['name'], 'names' );
									echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );
									
								} else {
								
									// Convert pipes to commas and display values
									$values = explode( '|', $attribute['value'] );
									echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );
									
								}
							?></td>
						</tr>
					<?php endforeach; ?>
				</table>
				<?php
			}
			
			echo "<br />";
			
			// ADD TO CART BUTTON
			
			if (!$product->is_in_stock())
			{
				?>
				<a href="<?php echo apply_filters( 'out_of_stock_add_to_cart_url', get_permalink( $product->id ) ); ?>" class="button"><?php echo apply_filters( 'out_of_stock_add_to_cart_text', __( 'Read More', 'woocommerce' ) ); ?></a>
				<?php 
			} 
			else 
			{
				$link = array(
					'url'   => '',
					'label' => '',
					'class' => ''
				);
		
				$handler = apply_filters( 'woocommerce_add_to_cart_handler', $product->product_type, $product );
		
				switch ( $handler ) {
					case "variable" :
						$link['url'] 	= apply_filters( 'variable_add_to_cart_url', get_permalink( $product->id ) );
						$link['label'] 	= apply_filters( 'variable_add_to_cart_text', __( 'Select options', 'woocommerce' ) );
					break;
					case "grouped" :
						$link['url'] 	= apply_filters( 'grouped_add_to_cart_url', get_permalink( $product->id ) );
						$link['label'] 	= apply_filters( 'grouped_add_to_cart_text', __( 'View options', 'woocommerce' ) );
					break;
					case "external" :
						$link['url'] 	= apply_filters( 'external_add_to_cart_url', get_permalink( $product->id ) );
						$link['label'] 	= apply_filters( 'external_add_to_cart_text', __( 'Read More', 'woocommerce' ) );
					break;
					default :
						if ( $product->is_purchasable() ) {
							$link['url'] 	= apply_filters( 'add_to_cart_url', esc_url( $product->add_to_cart_url() ) );
							$link['label'] 	= apply_filters( 'add_to_cart_text', __( 'Add to cart', 'woocommerce' ) );
							$link['class']  = apply_filters( 'add_to_cart_class', 'add_to_cart_button' );
						} else {
							$link['url'] 	= apply_filters( 'not_purchasable_url', get_permalink( $product->id ) );
							$link['label'] 	= apply_filters( 'not_purchasable_text', __( 'Read More', 'woocommerce' ) );
						}
					break;
				}
			
				echo apply_filters( 'woocommerce_loop_add_to_cart_link', sprintf('<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="%s button product_type_%s">%s</a>', esc_url( $link['url'] ), esc_attr( $product->id ), esc_attr( $product->get_sku() ), esc_attr( $link['class'] ), esc_attr( $product->product_type ), esc_html( $link['label'] ) ), $product, $link );
			}
		}
		else
		{
			// 
			echo "no product found";
		}
		*/
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


function nxs_widgets_wooproductdetail_initplaceholderdata($args)
{
	extract($args);

	$homepageid = nxs_gethomepageid();
	$args['destination_articleid'] = $homepageid;
	$args['destination_articleid_globalid'] = nxs_get_globalid($homepageid, true);	// global referentie
	$args['title'] = nxs_l18n__("title", "nxs_td");
	$args['subtitle'] = nxs_l18n__("subtitle", "nxs_td");
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_wooproductdetail_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>