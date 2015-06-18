<?php

nxs_requirewidget("menuitemgeneric");

/**
 * Widget icon in menu selection
 * @return string
 */
function nxs_widgets_menuitemarticle_geticonid()
{
	return "nxs-icon-text";
}

/**
 * Widget title in widget setup screen
 * @return string|void
 */
function nxs_widgets_menuitemarticle_gettitle()
{
	return nxs_l18n__("Article reference (menu item)[nxs:widgettitle]", "nxs_td");
}

/*** WIDGET STRUCTURE ***/

/**
 * Define the properties of this widget
 * @param $args
 * @return array
 */
function nxs_widgets_menuitemarticle_home_getoptions($args)
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_menuitemarticle_gettitle(),
		"sheeticonid" => nxs_widgets_menuitemarticle_geticonid(),
		"footerfiller" => true,
		"fields" => array
		(
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
			array
			(
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
            
            //
			
			array( 
				"id" 				=> "wrapper_link_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Link", "nxs_td"),
			),
			
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Article link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the menu item to an article within your site.", "nxs_td"),
			),	
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
				"excludeitem" => "menuitemarticle",
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
 * rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
 * hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
 * het bewerken van de placeholder kan opstarten
 * @param $args
 * @return array
 */
function nxs_widgets_menuitemarticle_render_webpart_render_htmlvisualization($args)
{
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	$mixedattributes = nxs_getwidgetmetadata($postid, $placeholderid);
	
	$mixedattributes = array_merge($mixedattributes, $args);

	// Localize atts
	$mixedattributes = nxs_localization_localize($mixedattributes);
	
	$title = $mixedattributes['title'];
	
    $icon = $mixedattributes['icon'];
	$icon_scale = "0-5";
    $icon_scale_cssclass = nxs_getcssclassesforlookup("nxs-icon-scale-", $icon_scale);
    
	$destination_articleid = $mixedattributes['destination_articleid'];
	$depthindex = $mixedattributes['depthindex'];	// sibling or child

	//
	//
	//
	
	$paddingleft = 30 * ($depthindex - 1);
	
	global $nxs_global_placeholder_render_statebag;
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["enable_decoratewidget"] = false;
	$hovermenuargs["enable_deletewidget"] = false;
	$hovermenuargs["enable_deleterow"] = true;
	
	//$hovermenuargs["enable_movewidget"] = "first";
	//$hovermenuargs["enable_editwidget"] = "second";
	
	$hovermenuargs["metadata"] = $mixedattributes;	
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
	//
	// render actual control / html
	//
	
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-menu-item " . "nxs-listitem-depth-" . $depthindex;
	
  if ($depthindex == 1)
  {
  	$positionerclass = "";
  } 
  else if ($depthindex == 2)
  {
  	$positionerclass = "nxs-margin-left30";
  }
  else if ($depthindex == 3)
  {
  	$positionerclass = "nxs-margin-left60";
  }
  else if ($depthindex == 4)
  {
  	$positionerclass = "nxs-margin-left90";
  }
  else if ($depthindex == 5)
  {
  	$positionerclass = "nxs-margin-left120";
  }
  else
  {
  	echo "max depth = 4";
  	$positionerclass = "nxs-margin-left120";
  }
  
  if ($icon != "") {$icon = '<span class="'.$icon.' '.$icon_scale_cssclass.'"></span> ';}
    
  ?>
	<div class="nxs-padding-menu-item nxs-draggable nxs-existing-pageitem nxs-dragtype-placeholder" id='draggableplaceholderid_<?php echo $placeholderid; ?>'>
		<div class="nxs-drag-helper" style='display: none;'>
			<div class='placeholder'>
			</div>
		</div>
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
 * Rendering function front-end
 * @param $args
 * @return string
 */
function nxs_widgets_menuitemarticle_render_in_container($args){

    $placeholdermetadata = $args["placeholdermetadata"];

    $title = $placeholdermetadata["title"]; //  . "(" . $currentdepth . ")";

    $icon = $placeholdermetadata["icon"];
    $icon_scale = "0-5";
    $icon_scale_cssclass = nxs_getcssclassesforlookup("nxs-icon-scale-", $icon_scale);

    $font_variant = $placeholdermetadata["font_variant"];
    $parent_height = $placeholdermetadata["parent_height"];

    $destination_articleid = $placeholdermetadata["destination_articleid"];

    // derive 'current' classes
    global $nxs_global_current_containerpostid_being_rendered;
    global $nxs_global_current_postid_being_rendered;

    $anchorclass = "";
    $class = "";

    if (is_archive()) {
        // the archive pages (for example list of category posts) we 'mimic' the
        // system, there the postid is set to the postid of the homepage. In that
        // case we don't want to mark the menu item of the home to be active
        $isactiveitem = false;
    } else {
        $isactiveitem = ($destination_articleid == $nxs_global_current_containerpostid_being_rendered || $destination_articleid == $nxs_global_current_postid_being_rendered);
    }

    if ($isactiveitem)
    {
        $class .= "{$cssclassactiveitem} nxs-active";
        $anchorclass .= " {$cssclassactiveitemlink}";
    } else {
        $class .= "{$cssclassactiveitem} nxs-inactive";
        if ($issubitem == true) {
            // inactive subitem
            $anchorclass .= " {$cssclasssubitemlink}";
        } else {
            // inactief hoofditem
            $anchorclass .= " {$cssclassitemlink}";
        }
    }

    $url = nxs_geturl_for_postid($destination_articleid);
    if ($url == "") {
        $anchorclass .= " nxs-menuitemnolink";
    }


    if ($icon != "") {$icon = '<span class="'.$icon.' '.$icon_scale_cssclass.'"></span> ';}

    $anchorclass = "class='{$anchorclass}'";

    //
    // http://stackoverflow.com/questions/2851663/how-do-i-simulate-a-hover-with-a-touch-in-touch-enabled-browsers
    // http://stackoverflow.com/questions/7018919/how-to-bind-touchstart-and-click-events-but-not-respond-to-both

    $cache = "";
    $cache = $cache . "<li class='menu-item menu-item-post " . $class . " " . $font_variant . " height".$parent_height."' >";
    $cache = $cache . "<a itemprop='url' href='" . $url . "' nxsurl='" . $url . "' ontouchstart='nxs_js_menuitemclick(this, \"touch\"); return false;' onmouseenter='nxs_js_menuitemclick(this, \"mouseenter\"); return false;' onmouseleave='nxs_js_menuitemclick(this, \"mouseleave\"); return false;' onclick='nxs_js_menuitemclick(this, \"click\"); return false;' " . $anchorclass . ">";
    $cache = $cache . "<div itemprop='name'>{$icon}{$title}</div>";
    $cache = $cache . "</a>";

    return $cache;
}

/**
 * @param $args
 * @return string
 */
function nxs_widgets_menuitemarticle_mobile_render($args){

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

    $mobcssclass = $placeholdermetadata["menuitem_color"];
    $outer_color_cssclass = $mobcssclass;

    $mobcssclass_active = $placeholdermetadata["menuitem_active_color"];
    $cssclassactiveitemlink = $mobcssclass_active;

    $destination_articleid = $placeholdermetadata["destination_articleid"];


    $isactiveitem = ($destination_articleid == $nxs_global_current_containerpostid_being_rendered || $destination_articleid == $nxs_global_current_postid_being_rendered);

    if ($isactiveitem)
    {
        $class .= "nxs-active ";
        $itemcolor = " {$cssclassactiveitemlink}";
    }
    else
    {
        // inactief subitem
        $itemcolor = " {$outer_color_cssclass}";
    }

    if ($icon != "") {$icon = '<span class="'.$icon.' '.$icon_scale_cssclass.'"></span> ';}

    $anchorclass = "class='{$itemcolor}'";

    $url = nxs_geturl_for_postid($destination_articleid);

    $cache = "";
    $cache = $cache . "<li class='menu-item menu-item-post menu-depth-" . $currentdepth . " {$class}'>";
    $cache = $cache . "<a itemprop='url'  href='" . $url . "' {$anchorclass}>";
    $cache = $cache . "<div itemprop='name'>{$icon}{$title}</div>";
    $cache = $cache . "</a>";
    $cache = $cache . "</li>";	// deze is het niet

    return $cache;
}

/**
 * Default data - wordt aangeroepen bij het opslaan van data van deze placeholder
 * @param $args
 * @return array
 */
function nxs_widgets_menuitemarticle_initplaceholderdata($args)
{
	extract($args);

	$args["title"] = "Item";
	$args["depthindex"] = 1;
	$args["destination_target"] = "";
	$args['ph_margin_bottom'] = "0-0";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>