<?php

function nxs_widgets_wpmenu_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-menucontainer";
}

// Setting the widget title
function nxs_widgets_wpmenu_gettitle() {
	return nxs_l18n__("Menu", "nxs_td");
}

// Unistyle
function nxs_widgets_wpmenu_getunifiedstylinggroup() {
	return "textwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_wpmenu_home_getoptions($args) 
{
	$menu_id = nxs_widgets_wpmenu_getmenuid();
	$editurl = get_admin_url(get_current_blog_id(), 'nav-menus.php') . "?action=edit&menu={$menu_id}";
	
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_wpmenu_gettitle(),
		"sheeticonid" => nxs_widgets_wpmenu_geticonid(),
		"sheethelp" => nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1764396204"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_wpmenu_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			
			/* CONFIGURATION
			---------------------------------------------------------------------------------------------------- */
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Configuration", "nxs_td"),
			),
			
			/*
			array(
				"id" 				=> "wpsidebarid",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Widget area number", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("None", "nxs_td"),
					"1" 	 => nxs_l18n__("Area 1", "nxs_td"),
					"2" 	 => nxs_l18n__("Area 2", "nxs_td"),
					"3" 	 => nxs_l18n__("Area 3", "nxs_td"),
					"4" 	 => nxs_l18n__("Area 4", "nxs_td"),
					"5" 	 => nxs_l18n__("Area 5", "nxs_td"),
					"6" 	 => nxs_l18n__("Area 6", "nxs_td"),
					"7" 	 => nxs_l18n__("Area 7", "nxs_td"),
					"8" 	 => nxs_l18n__("Area 8", "nxs_td"),
				),
				"tooltip" 			=> nxs_l18n__("
					To effectively use a native WP menu, you need to finish a number of steps the most obvious one is having a WP menu in the first place. 
					Second you will place this WP menu in the appropriate WP widget area. We provide a total of 8 of these areas. 
					The last step is choosing the specific area in the front-end and configuring the overall design.", "nxs_td"),
				"unistylablefield"	=> true
			),
			*/
			
			array(
				"id" 				=> "halign",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Horizontal alignment", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Align the menu to the left, center or right from the placeholder.", "nxs_td"),
				"unistylablefield"	=> true
			), 
			array( 
				"id" 				=> "minified_label",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Label responsive menu", "nxs_td"),
			),
			array(
				"id" 				=> "responsive_display",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Responsive display", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("Never", "nxs_td"),
					"display480" => nxs_l18n__("480", "nxs_td"),
					"display720" => nxs_l18n__("720", "nxs_td"),
					"display960" => nxs_l18n__("960", "nxs_td"),
					"display1200" => nxs_l18n__("1200", "nxs_td"),
					"display1440" => nxs_l18n__("1440", "nxs_td"),
				),
				"tooltip" 			=> nxs_l18n__("This option let's you set the sliders display at a certain viewport and up", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "font_variant",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Font variant", "nxs_td"),
				"dropdown" 			=> array(
					""=>"Default", 
					"small-caps"=>"Small-caps",
				),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "parent_height",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Menu item height", "nxs_td"),
				"dropdown" 			=> array(
					"1.3x"	=>"1.3x",
					"1.2x"	=>"1.2x",
					"1.1x"	=>"1.1x",
					"1x"	=>"1x", 
					"0.9x"	=>"0.9x",
					"0.8x"	=>"0.8x",
				),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "menu_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Menu fontsize", "nxs_td"),
				"dropdown" 			=> array(
					"1.2x"	=>"1.2x",
					"1.1x"	=>"1.1x",
					"1x"	=>"1x", 
					"0.9x"	=>"0.9x",
					"0.8x"	=>"0.8x",
				),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "submenu_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Submenu fontsize", "nxs_td"),
				"dropdown" 			=> array(
					"1x"	=>"1x", 
					"0.9x"	=>"0.9x",
					"0.8x"	=>"0.8x",
				),
				"unistylablefield"	=> true
			),
			
			array
			(
				"id" 				=> "editsection",
				"type" 				=> "custom",
				"custom"	=> "<div><a class='nxsbutton' href='{$editurl}'>Edit Items</a></div>",
				"label" 			=> nxs_l18n__("Items", "nxs_td"),
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
			),
			
			// COLOR STYLING
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Color styling", "nxs_td"),	
				"unistylablefield"	=> true		
			),
						
			array( 
				"id" 				=> "menuitem_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Color menu items", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "menuitem_active_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Color active menu items", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "menuitem_hover_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Color hover menu items", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "menuitem_sub_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Color sub menu items", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "menuitem_sub_active_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Color active sub menu items", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "menuitem_sub_hover_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Color hover sub menu items", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
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

function nxs_widgets_wpmenu_getmenuid()
{
	$locations = get_nav_menu_locations();
	$menu_name = 'nxs-menu-generic';
	$menu_id = $locations[$menu_name];
	return $menu_id;
}

function nxs_widgets_wpmenu_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_wpmenu_getunifiedstylinggroup(), $unistyle);
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
	
	// Parent
	$menuitem_color_cssclass 			= nxs_getcssclassesforlookup("nxs-colorzen-menuitem-", $menuitem_color);
	$menuitem_active_color_cssclass 	= nxs_getcssclassesforlookup("nxs-colorzen-menuitem-active-", $menuitem_active_color);
	$menuitem_hover_color_cssclass 		= nxs_getcssclassesforlookup("nxs-colorzen-menuitem-hover-", $menuitem_hover_color);
	
	// Child
	$menuitem_sub_color_cssclass 		= nxs_getcssclassesforlookup("nxs-colorzen-menuitem-sub-", $menuitem_sub_color);
	$menuitem_sub_active_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-menuitem-sub-active-", $menuitem_sub_active_color);
	$menuitem_sub_hover_color_cssclass 	= nxs_getcssclassesforlookup("nxs-colorzen-menuitem-sub-hover-", $menuitem_sub_hover_color);
	
	// Menu item font variant
	if 		($font_variant == '')								{ $font_variant = ""; }  
	else if ($font_variant == 'small-caps')						{ $font_variant = "nxs-small-caps"; }
	
	// Menu item height
	if 		($parent_height == '1x')		{ $parent_height = "10"; } 
	else if ($parent_height == '1.3x') 		{ $parent_height = "13"; }
	else if ($parent_height == '1.2x') 		{ $parent_height = "12"; }
	else if ($parent_height == '1.1x') 		{ $parent_height = "11"; }
	else if ($parent_height == '0.9x') 		{ $parent_height = "09"; } 
	else if ($parent_height == '0.8x') 		{ $parent_height = "08"; }  
	
	// Menu fontsize
	if 		($menu_fontsize == '1x')		{ $menu_fontsize = "10"; }
	else if ($menu_fontsize == '1.2x') 		{ $menu_fontsize = "12"; } 
	else if ($menu_fontsize == '1.1x') 		{ $menu_fontsize = "11"; } 
	else if ($menu_fontsize == '0.9x') 		{ $menu_fontsize = "09"; } 
	else if ($menu_fontsize == '0.8x') 		{ $menu_fontsize = "08"; }
	
	// Submenu fontsize
	if 		($submenu_fontsize == '1x')		{ $submenu_fontsize = "10"; }
	else if ($submenu_fontsize == '0.9x') 	{ $submenu_fontsize = "09"; } 
	else if ($submenu_fontsize == '0.8x') 	{ $submenu_fontsize = "08"; }
	
	nxs_ob_start();

	$menu_id = nxs_widgets_wpmenu_getmenuid();
	$nav_menu = wp_get_nav_menu_object($menu_id);
	
	if ($nav_menu == "")
	{
		// fallback; use the menu called "main menu"
		$nav_menu = wp_get_nav_menu_object("main menu");
	}
	
	$nav_menu_args = array
	(
		'fallback_cb' => '',
		'menu'        => $nav_menu
	);
	wp_nav_menu( apply_filters( 'widget_nav_menu_args', $nav_menu_args, $nav_menu, $args, $instance ) );

	$menuhtml = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	// Colorization script
	$script = '
	<script>
		// Enabling default menu styling
		jQ_nxs( ".nxs-menu ul.menu" ).addClass( "nxs-menu" );
		
		// Enabling menu item height and font variant
		jQ_nxs( "ul.menu li" ).addClass( "height' . $parent_height . ' ' . $font_variant . '" );
		
		// Enabling menu item colorization
		jQ_nxs( ".nxs-native-menu ul.menu" ).addClass( "nxs-applymenucolors item-fontsize' . $menu_fontsize . '" );
		
		// Enabling default menu item colorization
		jQ_nxs( ".nxs-native-menu ul.menu li" ).addClass( "nxs-inactive" );

		// Enabling active menu item colorization	
		jQ_nxs( ".nxs-native-menu ul.menu li.current-menu-item" ).addClass( "nxs-active" );
		
		// Enabling sub menu item colorization
		jQ_nxs( ".nxs-native-menu ul.sub-menu" ).addClass( "nxs-sub-menu" );
		
		// Injecting classes
		jQ_nxs( ".nxs-native-menu ul.menu" ).addClass( "' . 
			$menuitem_color_cssclass . 
			$menuitem_hover_color_cssclass . 
			$menuitem_active_color_cssclass . 
		'" );
		
		jQ_nxs( ".nxs-native-menu ul.nxs-sub-menu" ).addClass( "' . 
			$menuitem_sub_color_cssclass . 
			$menuitem_sub_active_color_cssclass . 
			$menuitem_sub_hover_color_cssclass . 
			$submenu_fontsize_cssclass . 
			'item-fontsize' . $submenu_fontsize . 
		'" );
	</script>';
		
	$outer_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $menuitem_color);
	
	if ($responsive_display != ""){ $responsive = 'responsive'; }
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($menuhtml == "") {
			nxs_renderplaceholderwarning(nxs_l18n__("No widgets found in widget area[nxs:warning]", "nxs_td"));
		} else {
			
			// Default menu
			echo '
			<div class="' . $halign . '">		
				<div class="nxs-menu nxs-native-menu ' . $responsive_display . '" >
					<ul>' . $menuhtml . '</ul>
				</div>
			</div>';
			
			echo '
			<div style="display:none" class="nxs-menu-minified nxs-applylinkvarcolor responsive-' . $responsive_display . '">';
			
				// Minified anchor
				echo '			
				<a href="#" class="nxs_js_menu_mini_expand-' . $placeholderid . '">
					<div style="text-align: center">
						<span class="nxs-icon-menucontainer"></span>
						<span>' . $minified_label . '</span>
					</div>
				</a>';
				
				// Minified expander
				echo '
                <div class="nxs-menu-mini-nav-expander-' . $placeholderid . '" style="display: none;">

					<div class="nxs-native-menu ' . $responsive . '" >
						<ul>' . $menuhtml . '</ul>
					</div>';
					
				echo '
				</div> <!-- END nxs-menu-mini-nav-expander -->';
				?>
				<script>
            jQ_nxs('a.nxs_js_menu_mini_expand-<?php echo $placeholderid; ?>').off('click.menu_mini_expand');
            jQ_nxs('a.nxs_js_menu_mini_expand-<?php echo $placeholderid; ?>').on('click.menu_mini_expand', function()
            {
            	nxs_js_log('wpmenu mini expand click');
              nxs_js_menu_mini_expand(this, '<?php echo $placeholderid; ?>');
              nxs_gui_set_runtime_dimensions_enqueuerequest('nxs-menu-toggled');

              var self = this;

              jQ_nxs(document).off('nxs_event_resizeend.menu_mini_expand');
              jQ_nxs(document).on('nxs_event_resizeend.menu_mini_expand', function(){
                  nxs_js_change_menu_mini_expand_height(self, '<?php echo $placeholderid; ?>');
                  nxs_gui_set_runtime_dimensions_enqueuerequest('nxs-menu-toggled');
                  return false;
              });
              return false;
            });
        </script>
				
			</div> <!-- END nxs-menu-minified -->
			
			<div class="nxs-clear"></div>
			<?php
			// Script
			echo $script;
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

function nxs_widgets_wpmenu_initplaceholderdata($args)
{
	extract($args);

	$args['wpsidebarid'] = "1";
	$args['minified_label'] = "Menu";
	$args['parent_height'] = "1x";
	$args['menu_fontsize'] = "1x";
	$args['submenu_fontsize'] = "1x";
	$args['ph_margin_bottom'] = "0-0";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_wpmenu_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}
