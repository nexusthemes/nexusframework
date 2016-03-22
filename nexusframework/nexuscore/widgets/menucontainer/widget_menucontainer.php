<?php

/**
 * Widget icon in menu selection
 * @return string
 */
function nxs_widgets_menucontainer_geticonid() {
    $widget_name = basename(dirname(__FILE__));
    return "nxs-icon-" . $widget_name;
}

/**
 * Widget title in widget setup screen
 * @return string|void
 */
function nxs_widgets_menucontainer_gettitle() {
    return nxs_l18n__("Menu", "nxs_td");
}

/**
 * Unistyle parameter
 * @return string
 */
function nxs_widgets_menucontainer_getunifiedstylinggroup() {
    return "menucontainerwidget";
}

add_action("nxs_getwidgets", "nxs_widgets_menucontainer_inject", 10, 2);	// default prio 10, 2 parameters (result, args)

/**
 * Inject menu container
 * @param $result
 * @param $args
 * @return array
 */
function nxs_widgets_menucontainer_inject($result, $args) {
    $nxsposttype = $args["nxsposttype"];

    if ( $nxsposttype == "header" || $nxsposttype == "footer") {
        $result[] = array (
            "widgetid" => "menucontainer",
        );
    }

    return $result;
}

/*** WIDGET STRUCTURE ***/

/**
 * Define the properties of this widget
 * @param $args
 * @return array
 */
function nxs_widgets_menucontainer_home_getoptions($args) {

    // CORE WIDGET OPTIONS
    $options = array(
        "sheettitle" => nxs_widgets_menucontainer_gettitle(),
        "sheeticonid" => nxs_widgets_menucontainer_geticonid(),
        "unifiedstyling" => array(
            "group" => nxs_widgets_menucontainer_getunifiedstylinggroup(),
        ),
        "sheethelp" => nxs_l18n__("http://nexusthemes.com/menu-widget/"),
        "fields" => array(

            // INPUT
            array(
                "id" 				=> "wrapper_items_begin",
                "type" 				=> "wrapperbegin",
                "label" 			=> nxs_l18n__("Configuration", "nxs_td"),
                "initial_toggle_state"	=> "closed",
            ),
            array(
                "id" 				=> "menu_menuid",
                "type" 				=> "selectpost",
                "post_type"			=> "nxs_menu",
                "label" 			=> nxs_l18n__("Items", "nxs_td"),
                "tooltip" 			=> nxs_l18n__("The set of menu items to show.", "nxs_td"),
            ),
            array(
                "id" 				=> "minified_label",
                "type" 				=> "input",
                "label" 			=> nxs_l18n__("Label responsive menu", "nxs_td"),
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
                "id" 				=> "responsive_display",
                "type" 				=> "select",
                "label" 			=> nxs_l18n__("Responsive display", "nxs_td"),
                "dropdown" 			=> nxs_style_getdropdownitems("responsive_display"),
                "tooltip" 			=> nxs_l18n__("This option let's you set the sliders display at a certain viewport and up", "nxs_td"),
                "unistylablefield"	=> true
            ),
            array(
                "id" 				=> "wrapper_items_end",
                "type" 				=> "wrapperend"
            ),

            // COLOR STYLING
            array(
                "id" 				=> "wrapper_styling_begin",
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
                "id" 				=> "wrapper_styling_end",
                "type" 				=> "wrapperend",
                "unistylablefield"	=> true
            ),

            // MENU ITEM STYLING
            array(
                "id" 				=> "wrapper_styling_begin",
                "type" 				=> "wrapperbegin",
                "label" 			=> nxs_l18n__("Menu item styling", "nxs_td"),
                "unistylablefield"	=> true
            ),
            array(
                "id" 				=> "font_variant",
                "type" 				=> "select",
                "label" 			=> nxs_l18n__("Font variant", "nxs_td"),
                "dropdown" 			=> array(
                    ""=>"Default",
                    "capitals"=>"Normal-caps",
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
                "id" 				=> "wrapper_styling_end",
                "type" 				=> "wrapperend",
                "unistylablefield"	=> true
            ),
        )
    );

    nxs_extend_widgetoptionfields($options, array("backgroundstyle"));

    return $options;
}

/*** WIDGET HTML ***/

/**
 * Rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
 * hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
 * het bewerken van de placeholder kan opstarten
 * @param $args
 * @return array
 */
function nxs_widgets_menucontainer_render_webpart_render_htmlvisualization($args) {

    extract($args);

    $menuplaceholderid = $placeholderid;

    global $nxs_global_row_render_statebag;
    global $nxs_global_placeholder_render_statebag;

    $temp_array = nxs_getwidgetmetadata($postid, $placeholderid);

    $unistyle = $temp_array["unistyle"];

    if (isset($unistyle) && $unistyle != "") {
        // blend unistyle properties
        $unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_menucontainer_getunifiedstylinggroup(), $unistyle);
        $temp_array = array_merge($temp_array, $unistyleproperties);
    }

    $mixedattributes = array_merge($temp_array, $args);
    extract($mixedattributes);

    // determine default behaviour
    if (!isset($responsive_display) || $responsive_display == "") {
        // backwords compatibility; if the responsive_display is not set,
        // this should default to display960
        $responsive_display = "display960";
    }

    $result = array();
    $result["result"] = "OK";

    nxs_ob_start();

    /*** WIDGET HOVER MENU ***/ ?>

    <ul class="">
        <li title='Edit' class='nxs-hovermenu-button'>
            <a href='#' title='Edit' onclick="nxs_js_popup_placeholder_handleclick(jQuery(this).closest('.nxs-placeholder')); return false;">
                <span class='nxs-icon-menucontainer'></span>
            </a>
            <ul>
            <?php if ($menu_menuid != "") { ?>
                <?php $url = get_home_url() . "/?nxs_menu=" . urlencode(nxs_getslug_for_postid($menu_menuid));?>
                <!-- default = edit -->
                <li title='<?php nxs_l18n_e("Configure menu items", "nxs_td"); ?>' class='tool' style='display: none;'>
                    <a href='#' class='nxs-defaultwidgetclickhandler' title='<?php nxs_l18n_e("Edit[nxs:tooltip]", "nxs_td"); ?>' onclick='var url = "<?php echo $url; ?>&nxsrefurlspecial=" + nxs_js_get_nxsrefurlspecial(); nxs_js_redirect(url);return false;'>
                        <span class='nxs-icon-menucontainer'></span>
                    </a>
                </li>
                <?php if (nxs_cap_hasdesigncapabilities()) { ?>
                    <li title='<?php nxs_l18n_e("Menu properties", "nxs_td"); ?>'>
                        <a href='#' onclick="nxs_js_edit_widget(this); return false;">
                            <span class='nxs-icon-plug'></span>
                        </a>
                    </li>
                <?php } ?>
            <?php }
            else { ?>
                <!-- default = plug -->
                <li title='<?php nxs_l18n_e("Edit[nxs:tooltip]", "nxs_td"); ?>'>
                    <a href='#' class='nxs-defaultwidgetclickhandler' title='<?php nxs_l18n_e("Plug another menu[nxs:tooltip]", "nxs_td"); ?>' onclick="nxs_js_edit_widget(this); return false;">
                        <span class='nxs-icon-plug'></span>
                    </a>
                </li>
            <?php } ?>
                <?php if (nxs_cap_hasdesigncapabilities()) { ?>
                    <li title='<?php nxs_l18n_e("Move[nxs:tooltip]", "nxs_td"); ?>' class='nxs-draggable nxs-existing-pageitem nxs-dragtype-placeholder' id='draggableplaceholderid_<?php echo $placeholderid; ?>'>
                        <span class='nxs-icon-move'></span>
                        <div class="nxs-drag-helper" style='display: none;'>
                            <div class='placeholder'>
                                <span id='placeholdertemplate_<?php echo $placeholdertemplate; ?>' class='<?php echo nxs_getplaceholdericonid($placeholdertemplate); ?>'></span>
                            </div>
                        </div>
                    </li>
                    <a class='nxs-no-event-bubbling' href='#' onclick='nxs_js_popup_placeholder_wipe("<?php echo $postid; ?>", "<?php echo $placeholderid; ?>"); return false;'>
                        <li title='<?php nxs_l18n_e("Delete widget[nxs:tooltip]", "nxs_td"); ?>'><span class='nxs-icon-trash'></span></li>
                    </a>
                <?php } ?>

                <?php if (nxs_shoulddebugmeta()) {
                    nxs_ob_start(); ?>
                    <a class='nxs-no-event-bubbling' href='#' onclick="nxs_js_edit_widget_v2(this, 'debug'); return false; return false;">
                        <li title='<?php nxs_l18n_e("Debug[tooltip]", "nxs_td"); ?>'>
                            <span class='nxs-icon-search'></span>
                        </li>
                    </a>
                    <?php $debughtml = nxs_ob_get_contents();
                    nxs_ob_end_clean();
                } else {
                    $debughtml = "";
                }
                echo $debughtml; ?>
            </ul>
        </li>
    </ul>

    <?php $menu = nxs_ob_get_contents();
    nxs_ob_end_clean();

    $nxs_global_placeholder_render_statebag["menutopright"] = $menu;
    $nxs_global_placeholder_render_statebag["widgetcropping"] = "no";	// menu container will exist beyond regular widget container

    nxs_ob_start();

    /*** EXPRESSIONS ***/
    $menu_menuid = $temp_array['menu_menuid'];
    $poststructure = nxs_parsepoststructure($menu_menuid);

    $orientation = "";
    if ($orientation == "" || $orientation == "horizontal") {
        $corecssclass = "nxs-menu";
    }
    else if ($orientation == "vertical") {
    $corecssclass = "nxs-menu-vertical";
    }
    else {
    $corecssclass = "nxs-menu";
    }

    $nxs_global_placeholder_render_statebag["widgetclass"] = $corecssclass . " ";

    // Font variant
    if (empty($font_variant)) {
        $font_variant = "";
    }
    else if ($font_variant == 'small-caps')	{
        $font_variant = "nxs-small-caps";
    }
    else if ($font_variant == 'capitals') {
        $font_variant = "nxs-capitalize";
    }

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
    if (empty($menu_fontsize) || $menu_fontsize == '1x')    { $menu_fontsize = "10"; }
    else if ($menu_fontsize == '1.4x') 		                { $menu_fontsize = "14"; }
    else if ($menu_fontsize == '1.3x') 		                { $menu_fontsize = "13"; }
    else if ($menu_fontsize == '1.2x') 		                { $menu_fontsize = "12"; }
    else if ($menu_fontsize == '1.1x') 		                { $menu_fontsize = "11"; }
    else if ($menu_fontsize == '0.9x') 		                { $menu_fontsize = "09"; }
    else if ($menu_fontsize == '0.8x') 		                { $menu_fontsize = "08"; }

    // Submenu fontsize
    if 		(empty($submenu_fontsize) || $submenu_fontsize == '1x')     { $submenu_fontsize = "10"; }
    else if ($submenu_fontsize == '0.9x') 	                            { $submenu_fontsize = "09"; }
    else if ($submenu_fontsize == '0.8x') 	                            { $submenu_fontsize = "08"; }

    $menuitem_border_width_cssclass 	= nxs_getcssclassesforlookup("nxs-border-width-", $menuitem_border_width);
    $menuitem_color_cssclass 			= nxs_getcssclassesforlookup("nxs-colorzen-menuitem-", $menuitem_color);
    $menuitem_active_color_cssclass 	= nxs_getcssclassesforlookup("nxs-colorzen-menuitem-active-", $menuitem_active_color);
    $menuitem_hover_color_cssclass 		= nxs_getcssclassesforlookup("nxs-colorzen-menuitem-hover-", $menuitem_hover_color);

    $concatenated = nxs_concatenateargswithspaces("nxs-applymenucolors", $corecssclass, $menuitem_border_width_cssclass, $menuitem_color_cssclass, $menuitem_active_color_cssclass, $menuitem_hover_color_cssclass);

    $menuitem_sub_border_width_cssclass = nxs_getcssclassesforlookup("nxs-border-width-", $menuitem_sub_border_width);
    $menuitem_sub_color_cssclass 		= nxs_getcssclassesforlookup("nxs-colorzen-menuitem-sub-", $menuitem_sub_color);
    $menuitem_sub_active_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-menuitem-sub-active-", $menuitem_sub_active_color);
    $menuitem_sub_hover_color_cssclass 	= nxs_getcssclassesforlookup("nxs-colorzen-menuitem-sub-hover-", $menuitem_sub_hover_color);

    /*** OUTPUT ***/

    if (count($poststructure) == 0) {
        nxs_renderplaceholderwarning(nxs_l18n__("No menu items[nxs:warning]", "nxs_td"));
    }
    else {
        $cache = "";

        $previousdepth = 1;
        $currentdepth = 1;

        $elementcountfordepth = array();
        $elementcountfordepth[$currentdepth] = 0;

        /*** OUTPUT DEFAULT MENU ***/

        echo "<div class='nxs-menu-aligner nxs-applylinkvarcolor " . $horclass . " " . $halign . "'>";
        echo "<ul id='nxs-menu-id-{$placeholderid}' class='{$concatenated} item-fontsize{$menu_fontsize} {$responsive_display}' itemscope='itemscope' itemtype='http://schema.org/SiteNavigationElement'>";

        foreach ($poststructure as $pagerow) {
            $content = $pagerow["content"];
            $placeholderid = nxs_parsepagerow($content);
            $placeholdermetadata = nxs_getwidgetmetadata($menu_menuid, $placeholderid);
            // localize fields
            $placeholdermetadata = nxs_localization_localize($placeholdermetadata);

            $placeholdertype = $placeholdermetadata["type"];
            if (!isset($placeholdertype) || $placeholdertype == "" || $placeholdertype == "undefined") {
            // continu the foreach
                continue;
            }

            $currentdepth = $placeholdermetadata["depthindex"];

            if ($currentdepth == 0 || $currentdepth == "") {
                $currentdepth = 1;
            }

            $issubitem = false;
            if ($currentdepth > 1) {
                $issubitem = true;
            }

            // Setting depths of menu items
            if ($currentdepth == $previousdepth + 1) {

            $concatenated = nxs_concatenateargswithspaces($menuitem_sub_border_width_cssclass, $menuitem_sub_color_cssclass, $menuitem_sub_active_color_cssclass, $menuitem_sub_hover_color_cssclass);

            // 1 dieper dan de vorige betekent een nieuwe ul tag-openen
            $cache = $cache . "<ul class='nxs-sub-menu {$concatenated} item-fontsize{$submenu_fontsize}'>";
            }
            else if ($currentdepth == $previousdepth) {
                // gelijke diepte
                if ($elementcountfordepth[$currentdepth] > 0) {
                    // let op, geloof het of niet, maar het commentaar regeltje is
                    // van belang, anders spuugt het systeem de </ niet uit ?!
                    $cache = $cache . "</li><!--GJ2-->";
                }
            }
            else if ($currentdepth <= $previousdepth - 1) {
                // close the last LI of the previous level, this should only happen 1x !!
                if ($elementcountfordepth[$currentdepth] > 0) {
                    // als we hier komen,
                    $cache = $cache . "<!-- HERE {$currentdepth} : {$previousdepth} -->";
                    $cache = $cache . "</li>";
                }

                $numofmissinguls = ($previousdepth - $currentdepth) + 1;

                for ($currentmissingul = 1; $currentmissingul < $numofmissinguls; $currentmissingul++) {
                    $cache = $cache . "<!-- currentdepth:" . $currentdepth . "/" . $previousdepth . " -->";
                    $cache = $cache . "</ul>";
                    $cache = $cache . "</li>";
                }
            }
            else {
                $cache = $cache . "<!-- warning, incorrect depth delta ?! -->";
            }

            nxs_requirewidget($placeholdertype);
            $functionnametoinvoke = "nxs_widgets_" . $placeholdertype . "_desktop_render";

            if (!function_exists($functionnametoinvoke)) {
                nxs_webmethod_return_nack("functionnametoinvoke not found; " . $functionnametoinvoke);
            }

            $placeholdermetadata["menuitem_color"] = $menuitem_color_cssclass;
            $placeholdermetadata["menuitem_active_color"] = $menuitem_active_color_cssclass;
            $placeholdermetadata["font_variant"] =  $font_variant;
            $placeholdermetadata["parent_height"] = $parent_height;
            $placeholdermetadata["submenu_height"] = $submenu_height;

            $subargs = array("placeholdermetadata" => $placeholdermetadata);

            $subresult = call_user_func($functionnametoinvoke, $subargs);

            $cache .= $subresult;

            $elementcountforcurrentdepth = 0;
            if (isset($elementcountfordepth[$currentdepth])) {
                $elementcountforcurrentdepth = $elementcountfordepth[$currentdepth];
            }
            $elementcountfordepth[$currentdepth] = $elementcountforcurrentdepth + 1;

            // update previous depth to current depth
            $previousdepth = $currentdepth;
        }
        // als we hier komen, kan het zijn dat de currentdepth > 1 is
        // in dat geval moeten we ul tags sluiten
        while ($currentdepth > 0) {
            if ($elementcountfordepth[$currentdepth] > 0) {
                $cache = $cache . "</li>";	// deze is het niet
            }
            $cache = $cache . "</ul><!--tail-->";
            $currentdepth = $currentdepth - 1;
        }

        echo $cache;

        /*** OUTPUT MINIFIED MENU ***/

        echo '<div style="display: none" class="nxs-menu-minified nxs-applylinkvarcolor responsive-' . $responsive_display . '">';

        $outer_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $menuitem_color); ?>
                <a href='#' class="nxs_js_menu_mini_expand-<?php echo $placeholderid; ?> <?php echo $outer_color_cssclass; ?>">
                    <div style="text-align: center">
                        <span class="nxs-icon-menucontainer"></span>
                        <span>&nbsp;<?php echo $minified_label; ?></span>
                    </div>
                </a>

                <div class='nxs-menu-mini-nav-expander-<?php echo $placeholderid; ?>' style="display: none;">

        <?php
        $concatenated = nxs_concatenateargswithspaces("nxs-applymenucolors", $corecssclass, $menuitem_border_width_cssclass, $menuitem_color_cssclass, $menuitem_active_color_cssclass, $menuitem_hover_color_cssclass);

        echo "<ul id='nxs-menu-minified-id-" . $placeholderid . "' class='" . $concatenated . " nxs-menu-minified nav' itemscope='itemscope' itemtype='http://schema.org/SiteNavigationElement'>";

        $cache = "";

        $previousdepth = 1;
        $currentdepth = 1;

        $elementcountfordepth = array();

        foreach ($poststructure as $pagerow) {
            $content = $pagerow["content"];
            $placeholderid = nxs_parsepagerow($content);
            $placeholdermetadata = nxs_getwidgetmetadata($menu_menuid, $placeholderid);

            $placeholdertype = $placeholdermetadata["type"];

            if (!isset($placeholdertype) || $placeholdertype == "" || $placeholdertype == "undefined") {
                // continu the foreach
                continue;
            }

            $currentdepth = $placeholdermetadata["depthindex"];

            if ($currentdepth == 0 || $currentdepth == "") {
                $currentdepth = 1;
            }

            if ($currentdepth == $previousdepth + 1) {
                // eentje dieper
            }
            else if ($currentdepth == $previousdepth) {
                // gelijke diepte
            }
            else if ($currentdepth <= $previousdepth - 1) {
                // eentje minder diep
            }
            else {
                $cache = $cache . "<!-- warning, incorrect depth delta ?! -->";
            }

            $issubitem = false;

            if ($currentdepth > 1) {
                $issubitem = true;
            }

            $functionnametoinvoke = "nxs_widgets_" . $placeholdertype . "_mobile_render";

            if (!function_exists($functionnametoinvoke)) {
                nxs_webmethod_return_nack("functionnametoinvoke not found; " . $functionnametoinvoke);
            }

            $placeholdermetadata["font_variant"] =  $font_variant;
            $placeholdermetadata["parent_height"] = $parent_height;
            $placeholdermetadata["submenu_height"] = $submenu_height;
            $placeholdermetadata["menuitem_color"] = $outer_color_cssclass;
            $placeholdermetadata["menuitem_active_color"] = $menuitem_active_color_cssclass;

            $mobsubargs = array("placeholdermetadata" => $placeholdermetadata);

            $mobsubresult = call_user_func($functionnametoinvoke, $mobsubargs);

            $cache .= $mobsubresult;

            // update previous depth to current depth
            $previousdepth = $currentdepth;
        }

        // als we hier komen, kan het zijn dat de currentdepth > 1 is
        // in dat geval moeten we ul tags sluiten
        while ($currentdepth > 0) {
            $currentdepth = $currentdepth - 1;
        }

        $cache = $cache . "</ul>";

        echo $cache;

        ?>

            </div> <!-- END nxs-menu-mini-nav-expander -->
            <script>
                jQ_nxs('a.nxs_js_menu_mini_expand-<?php echo $placeholderid; ?>').off('click.menu_mini_expand');
                jQ_nxs('a.nxs_js_menu_mini_expand-<?php echo $placeholderid; ?>').on('click.menu_mini_expand', function(){
                    nxs_js_menu_mini_expand(this, '<?php echo $placeholderid; ?>');
                    nxs_js_change_menu_mini_expand_height(this, '<?php echo $placeholderid; ?>');
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

        <?php

    } // if (count == 0)

    echo "</div> <!-- menu aligner -->";
    echo "<div class='nxs-clear'></div>";

    $html = nxs_ob_get_contents();

    nxs_ob_end_clean();

    $result["html"] = $html;
    $result["replacedomid"] = 'nxs-widget-' . $placeholderid;

    $nxs_global_row_render_statebag["upgradetoexceptionalresponsiverow"] = "true";

    return $result;
}

/**
 * Enrich the title with an additional prefix ">>"
 * @param $title
 * @param $currentdepth
 * @return string
 */
function nxs_menu_enrichtitle($title, $currentdepth) {

    $result = $title;

    for ($depthcounter = 1; $depthcounter < $currentdepth; $depthcounter++) {
        $result = "&raquo;&nbsp;" . $result;
    }

    return $result;
}

/**
 * Rendered menu items popup
 * @param $postid
 * @return string
 */
function nxs_page_render_popup_getrenderedmenuitems($postid) {

    $poststructure = nxs_parsepoststructure($postid);
    $cache = "";

    foreach ($poststructure as $pagerow) {
        $content = $pagerow["content"];
        $placeholderid = nxs_parsepagerow($content);
        $placeholdermetadata = nxs_getwidgetmetadata($postid, $placeholderid);

        $placeholdertype = $placeholdermetadata["type"];
        $currentdepth = $placeholdermetadata["depthindex"];

        if ($placeholdertype == "menuitemarticle") {
            //
            $title = $placeholdermetadata["title"];
            $depthindex = $placeholdermetadata["depthindex"];

            $cache = $cache . "<li>" . str_repeat('&gt;', $depthindex) . $title . "</li>";
        }
        else if ($placeholdertype == "menuitemcategory") {
            //
            $title = $placeholdermetadata["title"];
            $depthindex = $placeholdermetadata["depthindex"];

            $cache = $cache . "<li>" . str_repeat('&gt;', $depthindex) . $title . "</li>";
        }
        else if ($placeholdertype == "menuitemcustom") {
            //
            $title = $placeholdermetadata["title"];
            $depthindex = $placeholdermetadata["depthindex"];

            $cache = $cache . "<li>" . str_repeat('&gt;', $depthindex) . $title . "</li>";
        }
        else if ($placeholdertype == "woomenuitem") {
            //
            $title = $placeholdermetadata["title"];
            $depthindex = $placeholdermetadata["depthindex"];

            $cache = $cache . "<li>" . str_repeat('&gt;', $depthindex) . $title . "</li>";
        }
        else if ($placeholdertype == "undefined") {
            // undefined items are ignored
        }
        else if ($placeholdertype == "") {
            // undefined items are ignored
        }
        else {
            nxs_webmethod_return_nack("unexpected placeholdertype;" . $placeholdertype);
        }
    }

    if ($cache == "") {
        $cache = "Leeg";
    }

    return $cache;
}

/**
 * Default data - wordt aangeroepen bij het opslaan van data van deze placeholder
 * @param $args
 * @return array
 */
function nxs_widgets_menucontainer_initplaceholderdata($args) {
    extract($args);

    // create a new menu set custom post type

    $subargs = array();
    $subargs["nxsposttype"] = "menu";
    $subargs["poststatus"] = "publish";
    $subargs["titel"] = nxs_l18n__("Menu items", "nxs_td");
    $subargs["slug"] = $subargs["titel"] . " " . nxs_generaterandomstring(6);
    $subargs["postwizard"] = "defaultmenu";

    $response = nxs_addnewarticle($subargs);
    if ($response["result"] == "OK") {
        $args["menu_menuid"] = $response["postid"];
        $args["menu_menuid_globalid"] = nxs_get_globalid($response["postid"], true);
    }
    else {
        var_dump($response);
        nxs_webmethod_return_nack("unexpected response");
    }

    $args['orientation'] = "horizontal";
    $args['menuitem_color'] = "base2";
    $args['menuitem_active_color'] = "base1";
    $args['menuitem_hover_color'] = "base1";
    $args['menuitem_sub_color'] = "base2";
    $args['menuitem_sub_active_color'] = "base1";
    $args['menuitem_sub_hover_color'] = "base1";
    $args['minified_label'] = "Menu";
    $args['ph_margin_bottom'] = "0-0";
    $args['responsive_display'] = "display960";

    nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);

    $result = array();
    $result["result"] = "OK";

    return $result;
}
