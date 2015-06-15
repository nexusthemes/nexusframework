<?php

nxs_requirewidget("menuitemgeneric");

/**
 * Widget icon in menu selection
 * @return string
 */
function nxs_widgets_menuitemcategory_geticonid()
{
	return "nxs-icon-categories";
}

/**
 * Widget title in widget setup screen
 * @return string|void
 */
function nxs_widgets_menuitemcategory_gettitle()
{
	return nxs_l18n__("Category reference (menu item)[nxs:widgettitle]", "nxs_td");
}


/*** WIDGET STRUCTURE ***/

/**
 * Define the properties of this widget
 * @param $args
 * @return array
 */
function nxs_widgets_menuitemcategory_home_getoptions($args)
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_menuitemcategory_gettitle(),
		"sheeticonid" => nxs_widgets_menuitemcategory_geticonid(),
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
			
			array( 
				"id" 				=> "wrapper_link_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Link", "nxs_td"),
			),
			
			array(
				"id" 				=> "destination_category",
				"type" 				=> "categories",
				"maxselectable" => "1",
				"label" 			=> nxs_l18n__("Category", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the menu item to an archive of a specific category.", "nxs_td"),
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
				"excludeitem" => "menuitemcategory",
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
function nxs_widgets_menuitemcategory_render_webpart_render_htmlvisualization($args)
{
	
	//
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
    
	$destination_category = $mixedattributes['destination_category'];
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
	    <div class="box-content nxs-float-left"><p><?php echo "{$icon}{$title}"; ?></p></div>
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
function nxs_widgets_menuitemcategory_render_in_container($args){

    $placeholdermetadata = $args["placeholdermetadata"];

    $title = $placeholdermetadata["title"]; //  . "(" . $currentdepth . ")";

    $icon = $placeholdermetadata["icon"];
    $icon_scale = "0-5";
    $icon_scale_cssclass = nxs_getcssclassesforlookup("nxs-icon-scale-", $icon_scale);

    $font_variant = $placeholdermetadata["font_variant"];
    $parent_height = $placeholdermetadata["parent_height"];

    $destination_category = $placeholdermetadata["destination_category"];
    // for example [92]
    // remove brackets
    $destination_category = str_replace("[", "", $destination_category);
    $destination_category = str_replace("]", "", $destination_category);

    // derive 'current' classes
    global $nxs_global_current_containerpostid_being_rendered;
    global $nxs_global_current_postid_being_rendered;

    $anchorclass = "";
    $class = "";

    if (is_category($destination_category)) {
        $isactiveitem = true;
    } else {
        $isactiveitem = false;
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

    // Get the URL of this category
    $url = get_category_link($destination_category);

    if ($url == "") {
        $anchorclass .= " nxs-menuitemnolink";
    }

    if ($icon != "") {$icon = '<span class="'.$icon.' '.$icon_scale_cssclass.'"></span> ';}

    $anchorclass = "class='{$anchorclass}'";

    $cache = "";
    $cache = $cache . "<li class='menu-item menu-item-post " . $class . " " . $font_variant . " height".$parent_height."' >";
    $cache = $cache . "<a itemprop='url' href='" . $url . "' nxsurl='" . $url . "' ontouchstart='nxs_js_menuitemclick(this, \"touch\"); return false;' onmouseenter='nxs_js_menuitemclick(this, \"mouseenter\"); return false;' onmouseleave='nxs_js_menuitemclick(this, \"mouseleave\"); return false;' onclick='nxs_js_menuitemclick(this, \"click\"); return false;' " . $anchorclass . ">";
    $cache = $cache . "<div itemprop='name'>{$icon}{$title}</div>";
    $cache = $cache . "</a>";

    return $cache;
}

/**
 * Default data - wordt aangeroepen bij het opslaan van data van deze placeholder
 * @param $args
 * @return array
 */
function nxs_widgets_menuitemcategory_initplaceholderdata($args)
{
	extract($args);

	$args["title"] = "Item";
	$args["depthindex"] = 1;
	$args['ph_margin_bottom'] = "0-0";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}
