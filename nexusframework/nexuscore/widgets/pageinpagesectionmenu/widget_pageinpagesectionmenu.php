<?php

function nxs_widgets_pageinpagesectionmenu_geticonid() {
	return "nxs-icon-inpage";
}

function nxs_widgets_pageinpagesectionmenu_gettitle() {
	return nxs_l18n__("Inpage section menu", "nxs_td");
}

function nxs_widgets_pageinpagesectionmenu_registerhooksforpagewidget($args)
{	
	$pagedecoratorid = $args["pagedecoratorid"]; 
	$pagedecoratorwidgetplaceholderid = $args["pagedecoratorwidgetplaceholderid"];
	
	global $nxs_pageinpagesectionmenu_pagedecoratorid;
	$nxs_pageinpagesectionmenu_pagedecoratorid = $pagedecoratorid;
	global $nxs_pageinpagesectionmenu_pagedecoratorwidgetplaceholderid;
	$nxs_pageinpagesectionmenu_pagedecoratorwidgetplaceholderid = $pagedecoratorwidgetplaceholderid;
	
	// $pagevideo_metadata = nxs_getwidgetmetadata($nxs_pageinpagesectionmenu_pagedecoratorid, $nxs_pageinpagesectionmenu_pagedecoratorwidgetplaceholderid);
	// $condition_enable = $pagevideo_metadata["condition_enable"];
	
	$enabled = true;
	if (nxs_ishandheld())
	{
		$enabled = false;
	}
	
	if ($enabled)
	{
		add_action('nxs_beforeend_head', 'nxs_widgets_pageinpagesectionmenu_beforeend_head');
		add_action('nxs_ext_betweenheadandcontent', 'nxs_widgets_pageinpagesectionmenu_betweenheadandcontent');
	}
}

function nxs_widgets_pageinpagesectionmenu_beforeend_head()
{
	// do something useful here if thats needed
	
	?>
	<?php
}

function nxs_widgets_pageinpagesectionmenu_betweenheadandcontent()
{
	// get meta of the slider itself (such as transition time, etc.)
	global $nxs_pageinpagesectionmenu_pagedecoratorid;
	global $nxs_pageinpagesectionmenu_pagedecoratorwidgetplaceholderid;	
	$pageinpagesectionmenu_metadata = nxs_getwidgetmetadata($nxs_pageinpagesectionmenu_pagedecoratorid, $nxs_pageinpagesectionmenu_pagedecoratorwidgetplaceholderid);
	
	// Unistyle
	$unistyle = $pageinpagesectionmenu_metadata["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_pageinpagesectionmenu_getunifieditemstylinggroup(), $unistyle);
		$pageinpagesectionmenu_metadata = array_merge($pageinpagesectionmenu_metadata, $unistyleproperties);
	}
	
	extract($pageinpagesectionmenu_metadata);
	
	/* EXPRESSIONS
	----------------------------------------------------------------------------------------------------*/
	
	
	/* OUTPUT
	----------------------------------------------------------------------------------------------------*/

	// Items
	$showprevnext = true;
	if ($items == "sections")
	{
		$showprevnext = false;
	}

	// Item syle
	$itemstyling_cssclass = 'nxs-inpagesectionmenu-' . $itemstyling;

	// Item scale
	$items_scale = nxs_getpixelsfrommultiplier($items_scale, 20);
	$icon_scale = $items_scale / 1.5;
	$span_scale = 10 + $icon_scale / 4;
	$items_scale_style = 'width: ' . $items_scale . 'px; height: ' . $items_scale . 'px;';
	$icon_scale_style = 'font-size: ' . $icon_scale . 'px;';
	$span_scale_style = 'font-size: ' . $span_scale . 'px;';

	// Item color
	if ($items_color != "")
	{
		$items_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen nxs-colorzen-", $items_color);
	}

	// Item hover color
	if ($items_hover_color != "")
	{
		$items_color_hover_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-hover-", $items_hover_color);
	}

	// Item hover color
	if ($items_active_color != "")
	{
		$items_color_active_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-active-", $items_active_color);
	}

	// Title show
	if ($title_visualisation != "")
	{
		$title_visualisation = 'showtitle-' . $title_visualisation;
	}

	// Title color
	if ($title_visualisation_background_color != "")
	{
		$title_visualisation_background_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen nxs-colorzen-", $title_visualisation_background_color);
	}

	// Docking position
	$docking_position = str_replace(" ","-", $docking_position);
	$docking_position_explode = explode("-", $docking_position);
	$docking_position_cssclass = nxs_getcssclassesforlookup("nxs-inpagesectionmenu-position-", $docking_position);

	// Distance
	$distance = nxs_getpixelsfrommultiplier($distance);
	$distance_style = '';
	$distance_style.= $docking_position_explode[0] . ':' . $distance . 'px;';
	if ($docking_position_explode[1] != "center") {
		$distance_style.= $docking_position_explode[1] . ':' . $distance . 'px;';
	} else {
		$distance_style.= 'top: 50%;';
	}

	$span_cssclass = '';
	if ( $docking_position_explode[0] === "left" ) {
		$span_cssclass = 'left';
	}

	$concatenatedcssclasses_inpagesectionmenu 	= nxs_concatenateargswithspaces('nxs-inpagesectionmenu', $docking_position_cssclass, $items, $itemstyling_cssclass, $title_visualisation);
	$concatenatedcssclasses_span 		= nxs_concatenateargswithspaces($title_visualisation_background_color_cssclass, $span_cssclass);
	$concatenatedcssclasses_a 			= nxs_concatenateargswithspaces($items_color_hover_cssclass, $items_color_active_cssclass);

	$concatenatedstyles_container		= $icon_scale_style . $distance_style;

	?>
	
	<div id="nxs-inpagesectionmenu" class="<?php echo $concatenatedcssclasses_inpagesectionmenu; ?>" style="<?php echo $concatenatedstyles_container; ?>">
		<ul class="nxs-inpagesectionmenu-itemcontainer">
		</ul>
	</div>

	<script>
		var inpagesectionmenu = jQ_nxs('#nxs-inpagesectionmenu'),
			inpagesectionmenuContainer = jQ_nxs('#nxs-inpagesectionmenu .nxs-inpagesectionmenu-itemcontainer'),
			screenHeight = jQ_nxs(window).height(),
			scrollTop = jQ_nxs(window).scrollTop(),
			sectionStart = new Array(),
			sectionsLength,
			sections,
			index,
			url,
			scrollTop,
			currentActive = 0;

		jQ_nxs(document).ready(function(){
			// remove and create inpagesectionmenu when sections are changed/removed/added
			jQ_nxs(document).bind('nxs_dom_changed', function() {
				nxs_js_init_inpagesectionmenu();
			});

			// create inpagesectionmenu after a delay
			setTimeout(function() {
				nxs_js_init_inpagesectionmenu();
			}, 1000);
		});

		function nxs_js_init_inpagesectionmenu() {
			sections = jQ_nxs('.nxs-section').get();
			sectionsLength = sections.length;

			// order sections on section position
			var tempSections = [], tempTop;
			for (var i = 0; i < sectionsLength; i++) {
				tempTop = jQ_nxs(sections[i]).offset();
				tempTop = Math.round(tempTop.top);
				tempSections[tempTop] = jQ_nxs(sections[i]);
			}

			var tempSections = tempSections.filter(function(){return true;});
			for (var i = 0; i < sectionsLength; i++) {
				sections[i] = jQ_nxs(tempSections[i]).get(0);
			}

			// remove all events if they were set earlier and empty the inpagesectionmenu
			jQ_nxs(document).off("nxs_event_windowscrolling.inpagesectionmenu");
			jQ_nxs(inpagesectionmenuContainer).find('.link').off("click.inpagesectionmenu");
			jQ_nxs(inpagesectionmenuContainer).html("");

			var elem = '';
			var prevnext = false;

			// create the previous item
			<?php if ($showprevnext) { ?>
				prevnext = true;
				elem+= '<li class="prevnext prev nxs-applyhovercolors <?php echo $items_color_cssclass; ?>">';
				elem+= '<a href="#" class="<?php echo $concatenatedcssclasses_a; ?>" style="<?php echo $items_scale_style; ?>">';
				elem+= '<span class="icon nxs-icon-arrow-up-light"></span>';
				elem+= '</a>';
				elem+= '</li>';

				<?php if ( $itemstyling == 'circlesline') { ?>
					elem+= '<li class="line <?php echo $items_color_cssclass; ?>"></li>';
				<?php } ?>
			<?php } ?>
			
			// create all the list items for each section point
			var title, displayTitle, hash;
			for (var i = 0; i < sectionsLength; i++) {
				title = jQ_nxs(sections[i]).find('.nxs-section-title').html();
				hash = sections[i].id;			
				elem+= '<li class="item nxs-applyhovercolors <?php echo $items_color_cssclass; ?>">';
				elem+= '<a href="#' + hash + '" class="link <?php echo $concatenatedcssclasses_a; ?>" style="<?php echo $items_scale_style; ?>">'
				<?php if ( $itemstyling == 'blocksicons') { ?>
					var icon = jQ_nxs(sections[i]).find('.nxs-section-icon span').attr('class');
					elem+= '<span class="' + icon + '"></span>';
				<?php } ?>
				elem+= '</a>';
				elem+= '<span class="link <?php echo $concatenatedcssclasses_span; ?>" style="<?php echo $span_scale_style; ?>">' + title + '</span>';
				elem+= '</li>';

				<?php if ( $itemstyling == 'circlesline') { ?>
					if ( i < sectionsLength - 1 ) {
						elem+= '<li class="line <?php echo $items_color_cssclass; ?>"></li>';
					}
				<?php } ?>
			}

			// create the next item
			<?php if ($showprevnext) { ?>
				<?php if ( $itemstyling == 'circlesline') { ?>
					elem+= '<li class="line <?php echo $items_color_cssclass; ?>"></li>';
				<?php } ?>
				elem+= '<li class="prevnext next nxs-applyhovercolors <?php echo $items_color_cssclass; ?>">';
				elem+= '<a href="#" class="<?php echo $concatenatedcssclasses_a; ?>" style="<?php echo $items_scale_style; ?>">';
				elem+= '<span class="icon nxs-icon-arrow-down-light"></span>';
				elem+= '</a>';
				elem+= '</li>';
			<?php } ?>

			nxs_js_inpagesectionmenu_setsectionstartpoint();

			jQ_nxs(inpagesectionmenuContainer).append(elem);

			// verticle align center the inpagesectionmenu if doking position verticle align is set centered
			<?php if ($docking_position_explode[1] == "center") { ?>
				var inpagesectionmenuHeight = jQ_nxs(inpagesectionmenu).height();
				var inpagesectionmenuMargin = 0 - ( inpagesectionmenuHeight / 2 );
				jQ_nxs(inpagesectionmenu).css({
					'marginTop': inpagesectionmenuMargin + 'px'
				});
			<?php } ?>

			// make the .link class (inpagesectionmenu items and tooltip) clickable
			var li, sectionOffset, sectionTop, scrollSpeed, tagName;
			jQ_nxs(inpagesectionmenuContainer).find('.link').on("click.inpagesectionmenu", function() {
				li = jQ_nxs(this).closest('li.item');
				tagName = jQ_nxs(this).prop("tagName");

				// get the url hash of the inpagesectionmenu item
				if (tagName == 'SPAN') {
					url = jQ_nxs(li).find('a').attr('href');
				} else {
					url = jQ_nxs(this).attr('href');
				}

				index = jQ_nxs(li).index();
				index = (prevnext) ? index - 1 : index;
				<?php if ( $itemstyling == 'circlesline') { ?>
					index = (prevnext) ? index - 1 : index;
					index = index / 2;
				<?php } ?>

				// animate the page to the right section
				nxs_js_inpagesectionmenu_animatetosection();
				return false;
			});

			<?php if ($showprevnext) { ?>
				jQ_nxs(inpagesectionmenuContainer).find('.next, .prev').on("click.inpagesectionmenu", function() {
					var action = (jQ_nxs(this).hasClass('next'))? 'next': 'prev';
					var animate = true;
					if (currentActive == 0) {
						if (action == 'prev') {
							animate = false;
						}
					} else if (currentActive == sectionsLength - 1) {
						if (action == 'next') {
							animate = false;
						}
					}
					
					if (animate) {
						var activeItem = jQ_nxs('#nxs-inpagesectionmenu li.item').get(currentActive);
						if (jQ_nxs(this).hasClass('next')) {
							var newItem = jQ_nxs('#nxs-inpagesectionmenu li.item').get(currentActive + 1);
						} else {
							var newItem = jQ_nxs('#nxs-inpagesectionmenu li.item').get(currentActive - 1);
						}

						index = jQ_nxs(newItem).index();
						index-= 1;
						url = jQ_nxs(newItem).find('a').attr('href');

						<?php if ( $itemstyling == 'circlesline') { ?>
							index+= 1;
							index = index / 2;
							index-= 1;
						<?php } ?>

						// animate the page to the right section
						nxs_js_inpagesectionmenu_animatetosection();
					} else {
						// do nothing
					}
					return false;
				});
			<?php } ?>

			jQ_nxs(document).bind('nxs_event_resizeend', function() {
				nxs_js_inpagesectionmenu_setsectionstartpoint();
			});

			jQ_nxs(document).bind('nxs_event_windowscrolling.inpagesectionmenu', function() {
				scrollTop = jQ_nxs(window).scrollTop();
				nxs_js_inpagesectionmenu_setactiveitem();
			});

			nxs_js_inpagesectionmenu_setactiveitem();
		}

		function nxs_js_inpagesectionmenu_animatetosection() {
			sectionOffset = jQ_nxs(sections[index]).offset();
			sectionTop = sectionOffset.top - 40;
			scrollSpeed = nxs_js_getscrollspeed(sectionTop);
			jQ_nxs('html, body').stop().animate({
				scrollTop: sectionTop
			}, scrollSpeed, function(){
				// set the url hash after the animation
				window.location.hash = url;
			});
		}

		function nxs_js_inpagesectionmenu_setsectionstartpoint() {
			screenHeight = jQ_nxs(window).height();
			sectionStart = [];
			s = 0;
			for (var i = 0; i < sectionsLength; i++) {
				sectionStart[i] = Math.round(jQ_nxs(sections[i]).offset().top - screenHeight / 2 );
				if (sectionStart[i] < 0) {
					sectionStart[i] = s;
					s++;
				}
			}
		}

		function nxs_js_inpagesectionmenu_setactiveitem() {
			var activeItem;
			for (var i = 0; i < sectionStart.length; i++) {
				if (scrollTop >= sectionStart[i]) {
					if ( i + 1 != sectionStart.length ) {
						if ( scrollTop < sectionStart[i + 1] ) {
							currentActive = i;
						}
					} else {
						currentActive = i;
					}
				}
			}

			if (currentActive >= 0) {
				activeItem = jQ_nxs('#nxs-inpagesectionmenu li.item').get(currentActive);
				if ( !jQ_nxs(activeItem).hasClass('active-item')) {
					jQ_nxs('#nxs-inpagesectionmenu li.active-item').removeClass('active-item nxs-applyactivecolors');
					jQ_nxs(activeItem).addClass('active-item nxs-applyactivecolors');
				}
			}

			if (currentActive == 0) {
				jQ_nxs('.prev').addClass('disabled');
			} else if (currentActive == sectionsLength - 1) {
				jQ_nxs('.next').addClass('disabled');
			} else {
				if ( jQ_nxs('.prev, .next').hasClass('disabled')) {
					jQ_nxs('.prev, .next').removeClass('disabled');
				}
			}
		}

	</script>

	<?php
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_pageinpagesectionmenu_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_pageinpagesectionmenu_gettitle(),
		"sheeticonid" => nxs_widgets_pageinpagesectionmenu_geticonid(),
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
				"id" 				=> "docking_position",
				"type" 				=> "radiobuttons",
				"subtype"			=> "docking_position",
				"disable"			=> array(2, 5, 8),
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
				"id" 				=> "wrapper_input_end",
				"type" 				=> "wrapperend"
			),

			array
			( 
				"id" 				=> "wrapper_input_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Items visualisation", "nxs_td"),
			),

			array
			(
				"id" 				=> "items",
				"type" 				=> "select",
				"label" 			=> "Items",
				"dropdown" 			=> nxs_style_getdropdownitems("inpagesectionmenu_items"),
			),

			array
			(
				"id" 				=> "itemstyling",
				"type" 				=> "select",
				"label" 			=> "Items styling",
				"dropdown" 			=> nxs_style_getdropdownitems("inpagesectionmenu_style"),
			),

			array
			(
				"id" 				=> "items_scale",
				"type" 				=> "select",
				"label" 			=> "Items scale",
				"dropdown" 			=> nxs_style_getdropdownitems("items_scale"),
			),

			array
			( 
				"id" 				=> "items_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Items color", "nxs_td"),
			),

			array
			( 
				"id" 				=> "items_hover_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Items hover color", "nxs_td"),
			),

			array
			( 
				"id" 				=> "items_active_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Items active color", "nxs_td"),
			),

			array
			( 
				"id" 				=> "wrapper_input_end",
				"type" 				=> "wrapperend"
			),

			array
			( 
				"id" 				=> "wrapper_input_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title visualisation", "nxs_td"),
			),

			array
			(
				"id" 				=> "title_visualisation",
				"type" 				=> "select",
				"label" 			=> "Title visualisation",
				"dropdown" 			=> nxs_style_getdropdownitems("inpagesectionmenu_showtitle"),
			),

			array
			( 
				"id" 				=> "title_visualisation_background_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Title background", "nxs_td"),
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

function nxs_widgets_pageinpagesectionmenu_render_webpart_render_htmlvisualization($args) 
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
	
	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		//	
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
	}
	
	// Turn on output buffering
	ob_start();
	
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
		   <h4>In-page menu</h4>
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
	$html = ob_get_contents();
	ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	return $result;
}


function nxs_widgets_pageinpagesectionmenu_initplaceholderdata($args)
{
	extract($args);

	$args['itemstyling'] = "circles";
	$args['items'] = "sections";
	$args['items_scale'] = "1-0";
	$args['distance'] = "1-0";
	$args['docking_position'] = "left center";
	$args['title_visualisation'] = "onchange";

	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
