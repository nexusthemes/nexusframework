<?php

nxs_requirewidget("menuitemgeneric");

/**
 * Widget icon in menu selection
 * @return string
 */
function nxs_widgets_menuitemcustom_geticonid() {
	return "nxs-icon-earth";
}

/**
 * Widget title in widget setup screen
 * @return string|void
 */
function nxs_widgets_menuitemcustom_gettitle() {
	return nxs_l18n__("Custom menu item[nxs:widgettitle]", "nxs_td");
}

/*** WIDGET STRUCTURE ***/

/**
 * Define the properties of this widget
 * @param $args
 * @return array
 */
function nxs_widgets_menuitemcustom_home_getoptions($args) {
    // CORE WIDGET OPTIONS

    $options = array(
        "sheettitle" => nxs_widgets_menuitemcustom_gettitle(),
        "sheeticonid" => nxs_widgets_menuitemcustom_geticonid(),
        "fields" => array(

            // ICON
            array(
                "id" 				=> "wrapper_title_begin",
                "type" 				=> "wrapperbegin",
                "initial_toggle_state" => "closed",
                "label" 			=> nxs_l18n__("Icon", "nxs_td"),
            ),
            array(
                "id" 				=> "icon",
                "type" 				=> "icon",
                "label" 			=> nxs_l18n__("Icon", "nxs_td"),
                "unicontentablefield" => false,
            ),
            array(
                "id" 				=> "wrapper_title_end",
                "type" 				=> "wrapperend",
            ),

            // TITLE
            array(
                "id" 				=> "wrapper_title_begin",
                "type" 				=> "wrapperbegin",
                "label" 			=> nxs_l18n__("Title", "nxs_td"),
            ),
            array(
                "id" 				=> "title",
                "type" 				=> "input",
                "label" 			=> nxs_l18n__("Title", "nxs_td"),
                "placeholder" => nxs_l18n__("Title goes here", "nxs_td"),
                "localizablefield"	=> true
            ),
            array(
                "id" 				=> "wrapper_title_end",
                "type" 				=> "wrapperend",
            ),

            array(
                "id" 				=> "wrapper_link_begin",
                "type" 				=> "wrapperbegin",
                "label" 			=> nxs_l18n__("Link", "nxs_td"),
            ),
            array(
                "id" 				=> "destination_url",
                "type" 				=> "input",
                "label" 			=> nxs_l18n__("URL", "nxs_td"),
                "placeholder" => nxs_l18n__("http://www.example.org", "nxs_td"),
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
                "dropdown" 			=> array
                (
                    "nofollow"=>nxs_l18n__("No follow", "nxs_td"),
                    "follow"=>nxs_l18n__("Do follow", "nxs_td"),
                ),
                "unistylablefield"	=> true
            ),

            //destination_target ()
            //destination_relation (nofollow, follow)

            array(
                "id" 				=> "wrapper_link_end",
                "type" 				=> "wrapperend"
            ),

            // SWITCH TYPE
            array(
                "id" 				=> "wrapper_switch_begin",
                "type" 				=> "wrapperbegin",
                "initial_toggle_state" => "closed",
                "label" 			=> nxs_l18n__("Switch type", "nxs_td"),
            ),
            array(
                "id" 					=> "menuitemcustomtoggle",
                "type" 				=> "custom",
                "customcontenthandler"	=> "nxs_menuitemgeneric_switchtype",
                "label" 			=> nxs_l18n__("Switch type", "nxs_td"),
                "excludeitem" => "menuitemcustom",
            ),
            array(
                "id" 				=> "wrapper_switch_end",
                "type" 				=> "wrapperend",
            ),
        )
    );

    return $options;
}

/**
 * Rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
 * hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
 * het bewerken van de placeholder kan opstarten
 * @param $args
 * @return array
 */
function nxs_widgets_menuitemcustom_render_webpart_render_htmlvisualization($args) {

	extract($args);
	
	global $nxs_global_row_render_statebag;
    global $nxs_global_placeholder_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	$mixedattributes = array_merge($temp_array, $args);
	
	$title = $mixedattributes['title'];
    
    $icon = $mixedattributes['icon'];
	$icon_scale = "0-5";
    $icon_scale_cssclass = nxs_getcssclassesforlookup("nxs-icon-scale-", $icon_scale);
    
	$destination_url = $mixedattributes['destination_url'];
	$depthindex = $mixedattributes['depthindex'];	// sibling or child

	if (nxs_has_adminpermissions()) {
		$renderBeheer = true;
	}
	else {
		$renderBeheer = false;
	}
	
	if ($rendermode == "default") {
		if ($renderBeheer) {
			$shouldrenderhover = true;
		} 
		else {
			$shouldrenderhover = false;
		}
	}
	else if ($rendermode == "anonymous") {
		$shouldrenderhover = false;
	}
	else {
		nxs_webmethod_return_nack("unsupported rendermode;" . $rendermode);
	}

    $paddingleft = 30 * ($depthindex - 1);
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["enable_decoratewidget"] = false;
	$hovermenuargs["enable_deletewidget"] = false;
	$hovermenuargs["enable_deleterow"] = true;
	$hovermenuargs["metadata"] = $mixedattributes;	
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);

	// render actual control / html
	
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-menu-item " . "nxs-listitem-depth-" . $depthindex;

    if ($depthindex == 1) {
  	    $positionerclass = "";
    }
    else if ($depthindex == 2) {
        $positionerclass = "nxs-margin-left30";
    }
    else if ($depthindex == 3) {
        $positionerclass = "nxs-margin-left60";
    }
    else if ($depthindex == 4) {
        $positionerclass = "nxs-margin-left90";
    }
    else if ($depthindex == 5)  {
        $positionerclass = "nxs-margin-left120";
    }
    else {
        echo "max depth = 4";
        $positionerclass = "nxs-margin-left120";
    }

    if ($icon != "") {$icon = '<span class="'.$icon.' '.$icon_scale_cssclass.'"></span> '; } ?>
	<div class="nxs-padding-menu-item">
		<div class="content2 border <?php echo $positionerclass;?>">
	    <div class="box-content nxs-float-left"><p><?php echo $icon; ?><?php echo $title; ?></p></div>
	    <div class="nxs-clear"></div>
	  </div> <!--END content-->
	</div>
	
	<?php
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

/**
 * Desktop rendering function front-end
 * @param $args
 * @return string
 */
function nxs_widgets_menuitemcustom_desktop_render($args) {

    $placeholdermetadata = $args["placeholdermetadata"];
    $currentdepth = $placeholdermetadata["depthindex"];

    if ($currentdepth == 0 || $currentdepth == "") {
        $currentdepth = 1;
    }

    $issubitem = false;
    if ($currentdepth > 1) {
        $issubitem = true;
    }
    
    $title = $placeholdermetadata["title"]; //  . "(" . $currentdepth . ")";

    $icon = $placeholdermetadata["icon"];
    $icon_scale = "0-5";
    $icon_scale_cssclass = nxs_getcssclassesforlookup("nxs-icon-scale-", $icon_scale);

    $font_variant = $placeholdermetadata["font_variant"];
    $parent_height = $placeholdermetadata["parent_height"];
    $submenu_height = $placeholdermetadata["submenu_height"];

    if($issubitem == true){
        $menuitem_height = $submenu_height;
    } else {
        $menuitem_height = $parent_height;
    }

    $url = $placeholdermetadata["destination_url"];
    $destination_target = $placeholdermetadata["destination_target"];
    $destination_relation = $placeholdermetadata["destination_relation"];
    $destination_relationatt = $destination_relation;

    if (empty($url)) 
    {
    	$anchorclass = "class='nxs-menuitemnolink'";
    }
    else 
    {
      // echo $anchorclass = "class='{$cssclasssubitem}'";
    }

		if (empty($destination_target)) 
		{
			$targetatt = "target='_self'";
		} 
		else 
		{
			$targetatt = "target='" . $destination_target . "'";
		}

    if ($icon != "") {
        $icon = '<span class="' . $icon . ' ' . $icon_scale_cssclass . '"></span> ';
    }

    $cache = "";
    $cache = $cache . "<li class='menu-item menu-item-custom nxs-inactive height{$menuitem_height} " . $font_variant . "' style='" . $font_variant . "' >";
    $cache = $cache . "<a itemprop='url' href='" . $url . "' " . $targetatt . " attr='" . $destination_relationatt . "' " . $anchorclass . ">";
    $cache = $cache . "<div itemprop='name'>{$icon}{$title}</div>";
    $cache = $cache . "</a>";

    return $cache;
}

/**
 * Mobile rendering function front-end
 * @param $args
 * @return string $cache
 */
function nxs_widgets_menuitemcustom_mobile_render($args) {

    global $nxs_global_current_containerpostid_being_rendered;
    global $nxs_global_current_postid_being_rendered;

    $placeholdermetadata = $args["placeholdermetadata"];

    $class = "";

    $currentdepth = $placeholdermetadata["depthindex"];

    $title = $placeholdermetadata["title"]; //  . "(" . $currentdepth . ")";
    $title = nxs_menu_enrichtitle($title, $currentdepth);

    $icon = $placeholdermetadata["icon"];
    $icon_scale = "0-5";
    $icon_scale_cssclass = nxs_getcssclassesforlookup("nxs-icon-scale-", $icon_scale);

    $url = $placeholdermetadata["destination_url"];

    $mob_cssclass = $placeholdermetadata["menuitem_color"];
    $mob_cssclass_inactiveitemlink = $mob_cssclass;

    $mob_cssclass_active = $placeholdermetadata["menuitem_active_color"];
    $mob_cssclass_activeitemlink = $mob_cssclass_active;

    $destination_articleid = $placeholdermetadata["destination_articleid"];

    $isactiveitem = ($destination_articleid == $nxs_global_current_containerpostid_being_rendered || $destination_articleid == $nxs_global_current_postid_being_rendered);

    if ($isactiveitem) {
        $class = "{$mob_cssclass_activeitemlink} nxs-active ";
        $itemcolor = " {$mob_cssclass_activeitemlink}";
    }
    else    {
        // inactief subitem
        $itemcolor = " {$mob_cssclass_inactiveitemlink}";
    }

    $anchorclass = "class='{$itemcolor}'";

    if ($icon != "") {$icon = '<span class="'.$icon.' '.$icon_scale_cssclass.'"></span> ';}

    $cache = "";
    $cache = $cache . "<li class='menu-item menu-item-custom menu-depth-" . $currentdepth . " {$class}'>";
    $cache = $cache . "<a itemprop='url' href='{$url}' {$anchorclass}'>";
    $cache = $cache . "<div itemprop='name'>{$icon}{$title}</div>";
    $cache = $cache . "</a>";
    $cache = $cache . "</li>";

    return $cache;
}

/**
 * Default data - wordt aangeroepen bij het opslaan van data van deze placeholder
 * @param $args
 * @return array
 */
function nxs_widgets_menuitemcustom_initplaceholderdata($args) {

    extract($args);

	$args["title"] = "Item";
	$args["depthindex"] = 1;
	$args["destination_target"] = "_self";
	$args["destination_relation"] = "nofollow";	
	$args['destination_url'] = nxs_geturl_home();
	$args['ph_margin_bottom'] = "0-0";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}
