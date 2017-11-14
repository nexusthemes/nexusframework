<?php

function nxs_widgets_banneritem_geticonid()
{
	// there's no banneritem image yet; we re-use the image icon
	// $widget_name = basename(dirname(__FILE__));
	return "nxs-icon-image";	// . $widget_name;
}

function nxs_widgets_banneritem_gettitle()
{
	return nxs_l18n__("Banner item", "nxs_td");
}


/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_banneritem_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array(
		"sheettitle" => nxs_widgets_banneritem_gettitle(),
		"sheeticonid" => nxs_widgets_banneritem_geticonid(),
		"fields" => array(
			// TITLE
			
			array( 
				"id" 				=> "image_imageid",
				"type" 				=> "image",
				"label" 			=> nxs_l18n__("Image", "nxs_td"),
				"localizablefield"	=> true
			),

			array(
				"id" 				=> "image_alt",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Image alt text", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("imagealtplaceholder", "nxs_td"),
				"unicontentablefield" => true,
				"localizablefield"	=> true,
				"requirecapability" => nxs_cap_getdesigncapability(),
			),
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Article link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the menu item to an article within your site.", "nxs_td"),
			),
			array(
				"id" 				=> "destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("External link", "nxs_td"),
				"placeholder"		=> nxs_l18n__("http://www.example.org", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an external source using the full url.", "nxs_td"),
				"localizablefield"	=> true
			),

			array(
				"id" 				=> "destination_target",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Target", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@empty@@@"=>nxs_l18n__("Auto", "nxs_td"),
					"_blank"=>nxs_l18n__("New window", "nxs_td"),
					"_self"=>nxs_l18n__("Current window", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "destination_relation", 
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Link relation", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("link_relation"),
			),	
		)
	);
	
	return $options;
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_banneritem_render_webpart_render_htmlvisualization($args)
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
	$hovermenuargs["enable_deleterow"] = true;
	$hovermenuargs["enable_deletewidget"] = false;	
	
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
	//
	// render actual control / html
	//
	
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-banneritemr-item";
	
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
		    <div class="box-content nxs-width50 nxs-float-left"><?php echo $destination_articleid; ?></div>
		    <div class="box-content nxs-width50 nxs-float-left"><?php echo $destination_url; ?></div>

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

function nxs_widgets_banneritem_initplaceholderdata($args)
{
	extract($args);
	
	$args['ph_margin_bottom'] = "0-0";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
