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
	return "menucontainerwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_wpmenu_home_getoptions($args) 
{
	if (nxs_iswebmethodinvocation())
	{
		$clientpopupsessioncontext = $_REQUEST["clientpopupsessioncontext"];
		$clientpopupsessiondata = $_REQUEST["clientpopupsessiondata"];
		//
		$postid = $clientpopupsessioncontext["postid"];
		$placeholderid = $clientpopupsessioncontext["placeholderid"];
		
		// load the widget's data from the persisted db
		$placeholdermetadata = nxs_getwidgetmetadata($postid, $placeholderid);
		$menu_name = $placeholdermetadata["menu_name"];
		
		// but allow it to be overriden in the session
		if (isset($clientpopupsessiondata["menu_name"]))
		{
			$menu_name = $clientpopupsessiondata["menu_name"];
		}
		
		if ($menu_name == "")
		{
			// fallback
			$menu_name = "nxs-menu-generic";
		}
		
		$locations = get_nav_menu_locations();
		$menu_id = $locations[$menu_name];
		
		$editurl = get_admin_url(get_current_blog_id(), 'nav-menus.php') . "?action=edit&menu={$menu_id}";
	}
	else
	{
		// 
	}
	
	$menu_dropdown = get_registered_nav_menus();
	
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_wpmenu_gettitle(),
		"sheeticonid" => nxs_widgets_wpmenu_geticonid(),
		"supporturl" => "https://www.wpsupporthelp.com/wordpress-questions/menu-wordpress-questions-78/",
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

			array
			(
				"id" => "menu_name",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Menu", "nxs_td"),
				"dropdown" 			=> $menu_dropdown,
			),			
			
			array
			(
				"id" 				=> "editsection",
				"type" 				=> "custom",
				"custom"	=> "<div><a class='nxsbutton' href='{$editurl}'>Edit Items</a></div>",
				"label" 			=> nxs_l18n__("Items", "nxs_td"),
			),
			
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
          "1x"	=>"1x",
          "2x"	=>"2x",
          "1.5x"	=>"1.5x",
          "1.4x"	=>"1.4x",
          "1.3x"	=>"1.3x",
          "1.2x"	=>"1.2x",
          "1.1x"	=>"1.1x",
          "0.9x"	=>"0.9x",
          "0.8x"	=>"0.8x",
				),
				"unistylablefield"	=> true
			),
			/*
			array(
                "id"                => "submenu_height",
                "type"              => "select",
                "label"             => nxs_l18n__("Submenu item height", "nxs_td"),
                "dropdown"          => array(
                    "1x"    =>"1x",
                    "2x"    =>"2x",
                    "1.5x"  =>"1.5x",
                    "1.4x"  =>"1.4x",
                    "1.3x"  =>"1.3x",
                    "1.2x"  =>"1.2x",
                    "1.1x"  =>"1.1x",
                    "0.9x"  =>"0.9x",
                    "0.8x"  =>"0.8x",
                ),
                "unistylablefield"  => true
            ),
			*/
			array(
                "id" 				=> "menu_fontsize",
                "type" 				=> "select",
                "label" 			=> nxs_l18n__("Menu fontsize", "nxs_td"),
                "dropdown" 			=> array(
                    "1.4x"	=>"1.4x",
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

function nxs_widgets_wpmenu_applylookups($result)
{
	/* process lookup entries/shortcodes in the response */
	
	// for the urls we cannot use placeholders, here we use name convention: http(s)://__x__ means {{x}}
	if (nxs_stringcontains_v2($result, "://__", false))
	{
		$result = str_replace("https://__", "{{", $result);
		$result = str_replace("http://__", "{{", $result);
		$result = str_replace("__", "}}", $result);
	}
	
	// for the titles
	if (nxs_stringcontains_v2($result, "{", false))
	{
		$mixedattributes = array("item" => $result);
		
		$combined_lookups = nxs_lookups_getcombinedlookups_for_currenturl();
		// replace values in mixedattributes with the lookup dictionary
		$magicfields = array("item");
		$translateargs = array
		(
			"lookup" => $combined_lookups,
			"items" => $mixedattributes,
			"fields" => $magicfields,
		);
		$mixedattributes = nxs_filter_translate_v2($translateargs);
		$result = $mixedattributes["item"];
	}
	
	return $result;
}


/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

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
		$hovermenuargs = array();
		$hovermenuargs["postid"] = $postid;
		$hovermenuargs["placeholderid"] = $placeholderid;
		$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
		$hovermenuargs["metadata"] = $mixedattributes;
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	}
	
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
  if (empty($parent_height) || $parent_height == '1x')    { $parent_height = "10"; }
  else if ($parent_height == '2x') 						{ $parent_height = "20"; }
  else if ($parent_height == '1.5x') 						{ $parent_height = "15"; }
  else if ($parent_height == '1.4x') 						{ $parent_height = "14"; }
  else if ($parent_height == '1.3x') 						{ $parent_height = "13"; }
  else if ($parent_height == '1.2x') 						{ $parent_height = "12"; }
  else if ($parent_height == '1.1x') 						{ $parent_height = "11"; }
  else if ($parent_height == '0.9x') 						{ $parent_height = "09"; }
  else if ($parent_height == '0.8x') 						{ $parent_height = "08"; }
	
	// Submenu item height
  if (empty($submenu_height) || $submenu_height == '1x')    { $submenu_height = "10"; }
  else if ($submenu_height == '2x')                        { $submenu_height = "20"; }
  else if ($submenu_height == '1.5x')                      { $submenu_height = "15"; }
  else if ($submenu_height == '1.4x')                      { $submenu_height = "14"; }
  else if ($submenu_height == '1.3x')                      { $submenu_height = "13"; }
  else if ($submenu_height == '1.2x')                      { $submenu_height = "12"; }
  else if ($submenu_height == '1.1x')                      { $submenu_height = "11"; }
  else if ($submenu_height == '0.9x')                      { $submenu_height = "09"; }
  else if ($submenu_height == '0.8x')                      { $submenu_height = "08"; }
	
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

	// echo "menu is set to; $menu_name <br /><br />";

	if ($menu_name == "")
	{
		$menu_name = "nxs-menu-generic";
	}
	
	$locations = nxs_get_nav_menu_locations();
	$menu_id = $locations[$menu_name];
	
	global $nxs_gl_currentmenuwidget_mixedattributes;
	$nxs_gl_currentmenuwidget_mixedattributes = $mixedattributes;
	
	$nav_menu_args = array
	(
		'fallback_cb' => '',
		'menu'        => $menu_id,
		'menu_class'	=> 'menu nxs-menu',
		"echo" => false,
	);
	$menuhtml = wp_nav_menu( $nav_menu_args );
	
	// replace id="" to avoid w3c errs
	$menuhtml = str_replace("id=", "data-id=", $menuhtml);
	
	//
	$menuhtml = nxs_widgets_wpmenu_applylookups($menuhtml);
	
	$tuned_menu_fontsize = $menu_fontsize; // item-fontsize1.4x
  $tuned_menu_fontsize = str_replace(".", "", $tuned_menu_fontsize);
  $tuned_menu_fontsize = str_replace("x", "", $tuned_menu_fontsize);
  
  $tuned_submenu_fontsize = $submenu_fontsize; // item-fontsize1.4x
  $tuned_submenu_fontsize = str_replace(".", "", $tuned_submenu_fontsize);
  $tuned_submenu_fontsize = str_replace("x", "", $tuned_submenu_fontsize);
	
	// Colorization script
	$script = '
	<script>
		// Enabling default menu styling
		jQ_nxs( ".nxs-menu ul.menu" ).addClass( "nxs-menu" );
		
		// Enabling menu item height and font variant
		jQ_nxs( "ul.menu li" ).addClass( "height' . $parent_height . ' ' . $font_variant . '" );
		
		// Enabling menu item colorization
		jQ_nxs( ".nxs-native-menu ul.menu" ).addClass( "nxs-applymenucolors item-fontsize' . $tuned_menu_fontsize . '" );
		
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
			'item-fontsize' . $tuned_submenu_fontsize . 
		'" );
	</script>';
		
	$outer_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $menuitem_color);
	
	if ($responsive_display != ""){ $responsive = 'responsive'; }
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($menuhtml == "") 
	{
		nxs_renderplaceholderwarning(nxs_l18n__("No menu items found in the menu", "nxs_td"));
	} 
	else 
	{
			// Default menu
			echo '
			<div class="' . $halign . '">		
				<div class="nxs-menu nxs-native-menu ' . $responsive_display . '" >
					' . $menuhtml . '
				</div>
			</div>';
			
			if (nxs_frontendframework_getfrontendframework() == "nxs2")
			{
				$nxs2anchorcode = "onclick='nxs_js_menu_mini_expand_v2(this, \"$placeholderid\", \"toggle\"); return false;'";
			}
			
			echo '
			<div style="display:none" class="nxs-menu-minified nxs-applylinkvarcolor responsive-' . $responsive_display . '">';
			
				// Minified anchor
				echo '			
				<a href="#" id="a_nav_expander_' . $placeholderid . '" class="nxs_js_menu_mini_expand-' . $placeholderid . '" ' . $nxs2anchorcode . '>
					<div style="text-align: center">
						<span id="icon_nav_' . $placeholderid . '" class="nxs-icon-menucontainer"></span>
						<span>' . $minified_label . '</span>
					</div>
				</a>';
				
				// Minified expander
				echo '
          <div id="a_nav_collapser_' . $placeholderid . '" class="nxs-menu-mini-nav-expander-' . $placeholderid . '" style="display: none;">

					<div class="nxs-native-menu ' . $responsive . '" >
						' . $menuhtml . '
					</div>';
					
				echo '
				</div> <!-- END nxs-menu-mini-nav-expander -->';
				
				nxs_ob_start();
				?>
				<script>
						// wpmenu
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
				<?php
				$script2 = nxs_ob_get_contents();
				nxs_ob_end_clean();
				
				$framework = nxs_frontendframework_getfrontendframework();
				if ($framework == "nxs")
				{
					echo $script2;
				}
				?>	
			</div> <!-- END nxs-menu-minified -->
			
			<div class="nxs-clear"></div>
			<?php
			// Script
			
			$framework = nxs_frontendframework_getfrontendframework();
			if ($framework == "nxs")
			{
				echo $script;
			}
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

function nxs_dataprotection_nexusframework_widget_wpmenu_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("widget-none");
}

?>