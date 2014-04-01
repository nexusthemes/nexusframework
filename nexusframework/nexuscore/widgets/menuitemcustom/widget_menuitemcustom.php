<?php

nxs_requirewidget("menuitemgeneric");

function nxs_widgets_menuitemcustom_geticonid()
{
	return "nxs-icon-earth";
}

function nxs_widgets_menuitemcustom_gettitle()
{
	return nxs_l18n__("Custom menu item[nxs:widgettitle]", "nxs_td");
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_menuitemcustom_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	$mixedattributes = array_merge($temp_array, $args);
	
	$title = $mixedattributes['title'];
	$destination_url = $mixedattributes['destination_url'];
	$depthindex = $mixedattributes['depthindex'];	// sibling or child

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
		echo "unsupported rendermode;" . $rendermode;
		die();
	}

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
		
	
	ob_start();

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
  
  ?>
	<div class="nxs-padding-menu-item">
		<div class="content2 border <?php echo $positionerclass;?>">
	    <div class="box-content nxs-float-left"><p><?php echo $title; ?></p></div>
	    <div class="nxs-clear"></div>
	  </div> <!--END content-->
	</div>
	
	<?php 
	
	$html = ob_get_contents();
	ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

// Define the properties of this widget
function nxs_widgets_menuitemcustom_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_menuitemcustom_gettitle(),
		"sheeticonid" => nxs_widgets_menuitemcustom_geticonid(),
		"fields" => array
		(
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
			
			array
			(
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

//
// wordt aangeroepen bij het opslaan van data van deze placeholder
//
function nxs_widgets_menuitemcustom_initplaceholderdata($args)
{
	extract($args);

	$args["title"] = "Item";
	$args["depthindex"] = 1;
	$args["destination_target"] = "";
	$args["destination_relation"] = "nofollow";	
	$args['destination_url'] = nxs_geturl_home();
	$args['ph_margin_bottom'] = "0-0";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>