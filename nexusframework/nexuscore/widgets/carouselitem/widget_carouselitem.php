<?php

function nxs_widgets_carouselitem_geticonid()
{
	// there's no carouselitem image yet; we re-use the image icon
	// $widget_name = basename(dirname(__FILE__));
	return "nxs-icon-image";	// . $widget_name;
}

function nxs_widgets_carouselitem_gettitle()
{
	return nxs_l18n__("Carousel item", "nxs_td");
}

// Unistyle
function nxs_widgets_carouselitem_getunifiedstylinggroup() {
	return "carouselitemwidget";
}

// Unicontent
function nxs_widgets_carouselitem_getunifiedcontentgroup() {
	return "carouselitemwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_carouselitem_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_carouselitem_gettitle(),
		"sheeticonid" => nxs_widgets_carouselitem_geticonid(),
		"unifiedstyling" 	=> array("group" => nxs_widgets_carouselitem_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_carouselitem_getunifiedcontentgroup(),),
		"fields" => array
		(
			// TITLE
			
			array
			( 
				"id" 				=> "image_imageid",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Image", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If you want to upload an image for your bio profile use this option.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
			array
			(	
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Article link", "nxs_td"),
				"unicontentablefield" => true,
				"tooltip" 			=> nxs_l18n__("Link the menu item to an article within your site.", "nxs_td"),
			),
			array
			(
				"id" 				=> "destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("External link", "nxs_td"),
				"placeholder"		=> nxs_l18n__("http://www.example.org", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an external source using the full url.", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true
			),
		
		)
	);
	
	return $options;
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_carouselitem_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// blend unistyle properties
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_carouselitem_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_carouselitem_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
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

	if (nxs_has_adminpermissions())
	{
		$renderBeheer = true;
	}
	else
	{
		$renderBeheer = false;
	}
	
	if ($rendermode == "default")
	{
		if ($renderBeheer)
		{
			$shouldrenderhover = true;
		} 
		else
		{
			$shouldrenderhover = false;
		}
	}
	else if ($rendermode == "anonymous")
	{
		$shouldrenderhover = false;
	}
	else
	{
		nxs_webmethod_return_nack("unsupported rendermode;" . $rendermode);
	}

	global $nxs_global_placeholder_render_statebag;

	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
	//
	// render actual control / html
	//
	
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-carouselitemr-item";
	
	?>
	<div class="nxs-dragrow-handler nxs-padding-menu-item">
	  	<div class="content2">
	      	<div class="box-content nxs-width20 nxs-float-left">
	      	<div class='fixed-image-container'>
	      		<img src="<?php echo $url; ?>" />
	      	</div>
	      	<p>
	      		<?php echo sprintf(nxs_l18n__("Dimensions %s px h:%s px[nxs:span]", "nxs_td"), $width, $height); ?>
	      	</p>
	      	</div>
	      	<div class="box-content nxs-width30 nxs-float-left"><?php echo nxs_render_html_escape_gtlt($title); ?></div>
	      	<div class="box-content nxs-width50 nxs-float-left"><?php echo nxs_render_html_escape_gtlt($text); ?></div>
	  	<div class="nxs-clear"></div>
		</div> <!--END content-->
	</div>
	
	<!-- -->
	
	<?php 
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

function nxs_widgets_carouselitem_initplaceholderdata($args)
{
	extract($args);
	
	$args['ph_margin_bottom'] = "0-0";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_carouselitem_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_carouselitem_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);	
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
