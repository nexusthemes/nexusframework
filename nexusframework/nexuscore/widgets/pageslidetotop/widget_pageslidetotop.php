<?php

function nxs_widgets_pageslidetotop_geticonid() {
	return "nxs-icon-arrow-up-light";
}

function nxs_widgets_pageslidetotop_gettitle() {
	return nxs_l18n__("Slide to top", "nxs_td");
}

function nxs_widgets_pageslidetotop_registerhooksforpagewidget($args)
{	
	$pagedecoratorid = $args["pagedecoratorid"]; 
	$pagedecoratorwidgetplaceholderid = $args["pagedecoratorwidgetplaceholderid"];
	
	global $nxs_pageslidetotop_pagedecoratorid;
	$nxs_pageslidetotop_pagedecoratorid = $pagedecoratorid;
	global $nxs_pageslidetotop_pagedecoratorwidgetplaceholderid;
	$nxs_pageslidetotop_pagedecoratorwidgetplaceholderid = $pagedecoratorwidgetplaceholderid;
	
	$enabled = true;
	// $pagevideo_metadata = nxs_getwidgetmetadata($nxs_pageslidetotop_pagedecoratorid, $nxs_pageslidetotop_pagedecoratorwidgetplaceholderid);
	// $condition_enable = $pagevideo_metadata["condition_enable"];
	// if ($condition_enable == "desktoponly")
	// {
	// 	if (!nxs_isdesktop())
	// 	{
	// 		$enabled = false;
	// 	}
	// }
	
	if ($enabled)
	{
		add_action('nxs_beforeend_head', 'nxs_widgets_pageslidetotop_beforeend_head');
		add_action('nxs_ext_betweenheadandcontent', 'nxs_widgets_pageslidetotop_betweenheadandcontent');
	}
}

function nxs_widgets_pageslidetotop_beforeend_head()
{
	// do something useful here if thats needed
	
	?>
	<?php
}

function nxs_widgets_pageslidetotop_betweenheadandcontent()
{
	// get meta of the slider itself (such as transition time, etc.)
	global $nxs_pageslidetotop_pagedecoratorid;
	global $nxs_pageslidetotop_pagedecoratorwidgetplaceholderid;	
	$pageslidetotop_metadata = nxs_getwidgetmetadata($nxs_pageslidetotop_pagedecoratorid, $nxs_pageslidetotop_pagedecoratorwidgetplaceholderid);
	
	// Unistyle
	$unistyle = $pageslidetotop_metadata["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_pageslidetotop_getunifiedstylinggroup(), $unistyle);
		$pageslidetotop_metadata = array_merge($pageslidetotop_metadata, $unistyleproperties);
	}
	
	extract($pageslidetotop_metadata);
	
	/* EXPRESSIONS
	----------------------------------------------------------------------------------------------------*/
	
	
	/* OUTPUT
	----------------------------------------------------------------------------------------------------*/

	// Icon scale
	$icon_scale_cssclass = nxs_getcssclassesforlookup("nxs-icon-scale-", $icon_scale);
		
	// Icon
	if ($icon != "") {
		$icon = '<span class="'.$icon.' '.$icon_scale_cssclass.'"></span>';
	}

	// Background color
	if ($background_color != "")
	{
		$background_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen nxs-colorzen-", $background_color);
	}

	// Background hover color
	if ($background_hover_color != "")
	{
		$background_color_hover_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-hover-", $background_hover_color);
	}

	// Link color
	if ($linkcolorvar)
	{
		$linkcolorvar_cssclass = nxs_getcssclassesforlookup("nxs-linkcolorvar-", $linkcolorvar);
	}

	// Distance from window
	$distance = nxs_getslidetotopdistance($distance);

	// Docking position
	$docking_position = str_replace(" ","-", $docking_position);
	$docking_position_explode = explode("-", $docking_position);
	$docking_position_cssclass = nxs_getcssclassesforlookup("nxs-slidetotop-position-", $docking_position);

	$distance_style = '';
	if ($docking_position_explode[0] != "center") {
		$distance_style.= $docking_position_explode[0] . ':' . $distance . 'px;';
	}
	$distance_style.= $docking_position_explode[1] . ':' . $distance . 'px;';

	// Offset
	$offsetpixels = 0;
	if ($offset)
	{
		$offsetpixels = substr($offset, 0, -2);
	}

	$concatenatedcssclasses_slidetotop 	= nxs_concatenateargswithspaces('nxs-slidetotop', 'nxs-applyhovercolors', $linkcolorvar_cssclass, $docking_position_cssclass);
	$concatenatedcssclasses_anchor_con 	= nxs_concatenateargswithspaces('anchor_container', 'nxs-applylinkvarcolor', $background_color_cssclass);
	$concatenatedcssclasses_anchor 		= nxs_concatenateargswithspaces($background_color_hover_cssclass);
	
	?>

	<div id='nxs-slidetotop' class="<?php echo $concatenatedcssclasses_slidetotop; ?>" style="<?php echo $distance_style; ?>">
		<div class="<?php echo $concatenatedcssclasses_anchor_con; ?>">
			<a href="#" class='<?php echo $concatenatedcssclasses_anchor; ?>'>
				<?php echo $icon; ?>
			</a>
		</div>
	</div>

	<script type="text/javascript">

		function nxs_js_show_slidetotop() {
			if (jQ_nxs(window).scrollTop() < <?php echo $offsetpixels; ?>) {
				jQ_nxs("#nxs-slidetotop:visible").fadeOut(200);
			}
			else {
				jQ_nxs("#nxs-slidetotop:hidden").fadeIn(200);
			}
		}

		function nxs_js_center_slidetotop() {
			var slidetotopWidth = Math.round(jQ_nxs('#nxs-slidetotop').width());
			var slidetotopMargin = slidetotopWidth / 2;
				slidetotopMargin = Math.round(slidetotopMargin - (slidetotopMargin * 2));

			jQ_nxs('#nxs-slidetotop').css('marginLeft', slidetotopMargin);
		}
	
		jQ_nxs(window).scroll(function () { 
			nxs_js_show_slidetotop();
		});

		setTimeout(function() {
			nxs_js_show_slidetotop();
			<?php
				if ($docking_position_explode[0] == "center") {
			?>
			
			nxs_js_center_slidetotop();

			<?php
				}
			?>
		}, 200);
		
		jQ_nxs('#nxs-slidetotop a').click(function(){
			jQ_nxs('html, body').stop().animate({
				scrollTop: 0
			}, nxs_js_getscrollspeed(0));
			return false;
		});

	</script>
	
	<?php
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_pageslidetotop_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_pageslidetotop_gettitle(),
		"sheeticonid" => nxs_widgets_pageslidetotop_geticonid(),
		"footerfiller" => true,
		"fields" => array
		(
			// SLIDES			
			
			array
			( 
				"id" 				=> "wrapper_input_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Configuration", "nxs_td"),
			),
			
			array
			(
				"id" 				=> "icon",
				"type" 				=> "icon",
				"label" 			=> nxs_l18n__("Icon", "nxs_td"),
			),

			array
			(
				"id"     			=> "icon_scale",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Icon scale", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("icon_scale"),
			),

			array
			( 
				"id" 				=> "background_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Background color", "nxs_td"),
			),

			array
			( 
				"id" 				=> "background_hover_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Background hover color", "nxs_td"),
			),

			array
			( 
				"id" 				=> "linkcolorvar",
				"type" 				=> "colorvariation",
				"scope" 			=> "link",
				"label" 			=> "Link color",
			),

			array
			(
				"id" 				=> "docking_position",
				"type" 				=> "radiobuttons",
				"subtype"			=> "docking_position",
				"disable"			=> array(4, 5, 6),
				"layout" 			=> "3x3",
				"default" 			=> "right bottom",
				"label" 			=> nxs_l18n__("Docking position", "nxs_td"),
			),

			array
			(
				"id" 				=> "distance",
				"type" 				=> "select",
				"label" 			=> "Distance from window",
				"dropdown" 			=> nxs_style_getdropdownitems("distance"),
			),

			array
			(
				"id" 				=> "offset",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Top scroll till visible", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("offset")
			),

			array
			( 
				"id" 				=> "wrapper_input_end",
				"type" 				=> "wrapperend"
			),
		)
	);
	
	return $options;
}


/* ADMIN PAGE HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_pageslidetotop_render_webpart_render_htmlvisualization($args) 
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
	
	// popup menu
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["enable_decoratewidget"] = false;
	$hovermenuargs["enable_deletewidget"] = false;
	$hovermenuargs["enable_deleterow"] = true;
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

		
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) 
	{
		if ($alternativehint == "")
		{
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} 
	else 
	{
		/* ADMIN OUTPUT
		---------------------------------------------------------------------------------------------------- */
		
		echo '
		<div class="nxs-dragrow-handler nxs-padding-menu-item">
		<div class="content2">
		 <div class="box">
		        <div class="box-title">
		   <h4>Slide to top</h4>
		  </div>
		  <div class="box-content"></div>
		 </div>
		 <div class="nxs-clear"></div>
		</div>
		</div>';
		
		/* ------------------------------------------------------------------------------------------------- */
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


function nxs_widgets_pageslidetotop_initplaceholderdata($args)
{
	extract($args);

	$args['icon'] = "nxs-icon-arrow-up-light";
	$args['icon_scale'] = "1-0";
	$args['docking_position'] = "right bottom";
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
