<?php



/**
 * Adds custom items to a navigation menu
 * Partially based on: 
 * http://teleogistic.net/2013/02/dynamically-add-items-to-a-wp_nav_menu-list/
 * 
 * @param string    $menu_name          The name or slug of the navigation menu
 * @param int       $parent_object_id   The id of the post/page, which must be present 
 *                                      in the menu, and to which we want to add subitems 
 * @param array     $subitems           The sub-items to be added to the menu, as an
 *                                      array( array( 'text' => 'foo', 'url' => '/bar') )
 */
function nxs_widgets_wpmenu_add_subitems_to_menu( $menu_name, $parent_object_id, $subitems ) 
{
  

  // Use wp_get_nav_menu_items filter, is used by Timber to get WP menu items
  add_filter( 'wp_get_nav_menu_items', function( $items, $menu ) 
          use( $menu_name, $parent_object_id, $subitems ) {

      // If no menu found, just return the items without adding anything
      if ( $menu->name != $menu_name && $menu->slug != $menu_name ) {
          return $items;
      }

      // Find the menu item ID corresponding to the given post/page object ID
      // If no post/page found, the subitems won't have any parent (will be on 1st level)
      $parent_menu_item_id = 0;
      foreach ( $items as $item ) {
          if ( $parent_object_id == $item->object_id ) {
              $parent_menu_item_id = $item->ID;
              break;
          }
      }

      $menu_order = count( $items ) + 1;

      foreach ( $subitems as $subitem ) {
          // Create objects containing all (and only) those properties from WP_Post 
          // used by WP to create a menu item
          $items[] = (object) array(
              'ID'                => $menu_order + 1000000000, // ID that WP won't use
              'title'             => $subitem['text'],
              'url'               => $subitem['url'],
              'menu_item_parent'  => $parent_menu_item_id,
              'menu_order'        => $menu_order,
              // These are not necessary, but PHP warning will be thrown if undefined
              'type'              => '',
              'object'            => '',
              'object_id'         => '',
              'db_id'             => '',
              'classes'           => '',
          );
          $menu_order++;
      }
      return $items;
  }, 10, 2);
}

function nxs_apply_menu()
{
	nxs_requirewidget("menucontainer");

	echo "menu patch :)<br />";
	
	
	// grab menu from first header in the side
	$publishedargs = array();
	$publishedargs["post_status"] 	= array("publish", "private");
	$publishedargs["post_type"] = array("post", "page", "nxs_header", "nxs_footer", "nxs_sidebar", "nxs_subheader", "nxs_subfooter");
	$publishedargs["orderby"] 		= "post_date";//$order_by;
	$publishedargs["order"] 		= "DESC"; //$order;
	$publishedargs["numberposts"] 	= -1;	// allemaal!
	$items = get_posts($publishedargs);
	
	$menu_ids = array();
	
	foreach ($items as $item)
	{
		$container_postid = $item->ID;
		$filter = array("postid" => $container_postid, "widgettype" => "menucontainer");
		$frontendmenuwidgetsmetadataperpost = nxs_getwidgetsmetadatainpost_v2($filter);
		
		foreach ($frontendmenuwidgetsmetadataperpost as $placeholderid => $widgetsmetadata)
		{
			echo "<br />found menu widget; postid: $container_postid placeholderid: $placeholderid<br /><br />";
			//var_dump($widgetsmetadata);
			//continue;	// todo: undo this
			
			if ($widgetsmetadata["menu_menuid"] == "") { continue; }
			
			$menu_id = $widgetsmetadata["menu_menuid"];
			
			if (!isset($menu_ids["distinct"][$menu_id]))
			{
				// first time we encounter this menu
				$menu_ids["count"]++;
				$menu_ids["uniquemapping"][$menu_id] = $menu_ids["count"] - 1;
			}
			$menu_ids["distinct"][$menu_id] = true;
			
			$menu_ids["frontendmenus"][$container_postid][$placeholderid] = "A" . $menu_ids["uniquemapping"][$menu_id];
		}
	}
	
	if (count($menu_ids) == 0)
	{
		echo "no patching required; no menucontainer widgets found<br />";
		die();
	}
	
	foreach ($menu_ids["uniquemapping"] as $menu_id => $new_menu_index)
	{
		echo "processing frontend menuid:";
		var_dump($menu_id);

		echo "which has new index;";
		var_dump($new_menu_index);
		
		echo "concluded we need to patch menu with postid $menu_id<br />";
		
		// parse the menu structure of the front end menu
		$poststructure = nxs_parsepoststructure($menu_id);
		$mem = nxs_menu_getmemstructure($menu_id, $poststructure);
		
		$menu_menuid = $temp_array['menu_menuid'];
	  $poststructure = nxs_parsepoststructure($menu_menuid);
	  
		$memstructure = nxs_menu_getmemstructure($menu_menuid, $poststructure);
		
		// check if backend menu exists
		
		$menus = get_registered_nav_menus();
		$menukeys = array_keys($menus);
		$menu_name = $menukeys[$new_menu_index];
		
		//
		$menu_ids["usagetoname"]["A" . $new_menu_index] = $menu_name;
		
		$menu = wp_get_nav_menu_object($menu_name);
		if ($menu === false)
		{
			echo "menu doesn't yet exist, creating it ...<br />";
			
			$menu_id = wp_create_nav_menu($menu_name);
			echo "finished creating menu ($menu_id)<br />";
			
			$most_recent_menu_item_id_per_depth = array();
			
			foreach ($mem as $item)
			{
				echo "processing menu item... <br />";
				
				$type = $item["type"];
				$title = $item["title"];
				$destination_articleid = $item["destination_articleid"];
				$destination_url = $item["destination_url"];
				$depthindex = $item["depthindex"];
				
				if ($type == "menuitemarticle" && $destination_articleid != "")
				{
					$link_to_postid = $destination_articleid;
					
					// $title = $title;nxs_gettitle_for_postid($link_to_postid);
					if ($title == "") { $title = "X"; }
					$link_to_posttype = get_post_type($link_to_postid);
					
					$menuitem_args = array
					(
			      'menu-item-title' => $title,
			      'menu-item-classes' => '',
			      'menu-item-object-id' => $link_to_postid,
			      'menu-item-object' => $link_to_posttype,
			      'menu-item-status' => 'publish',
			      'menu-item-type' => 'post_type',
			      'menu-item-parent-id' => $most_recent_menu_item_id_per_depth[$depthindex - 1],
			    );
			    
			   
					
					// Set up default menu items
			  	$menu_item_id = wp_update_nav_menu_item
			  	(
			  		$menu_id, 
			  		0, // 0 creates a new item
			  		$menuitem_args
			   	);
			   	
			   	$most_recent_menu_item_id_per_depth[$depthindex] = $menu_item_id;
			   	
			   	echo "menu item id:<br />";
			   	var_dump($menu_item_id);
			   	echo "<br />";
			   	
			   	nxs_wp_update_nav_menu_item($menu_id, $menu_item_id, $menuitem_args);
			  }
			  else if ($type == "menuitemarticle" && $destination_articleid == "")
			  {
			  	// if the link is not set, use a custom url
			  	
			  	$menuitem_args = array
					(
			      'menu-item-title' => $title,
			      'menu-item-classes' => '',
			      'menu-item-object-id' => '',
			      'menu-item-object' => '',
			      'menu-item-status' => 'publish',
			      'menu-item-type' => 'custom',
			      
			      'menu-item-parent-id' => $most_recent_menu_item_id_per_depth[$depthindex - 1],
			      'menu-item-url' => '#', 	// custom url
			    );
					
					// Set up default menu items
			  	$menu_item_id = wp_update_nav_menu_item
			  	(
			  		$menu_id, 
			  		0, // 0 creates a new item
			  		$menuitem_args
			   	);
			   	
			   	$most_recent_menu_item_id_per_depth[$depthindex] = $menu_item_id;
			  }
			  else if ($type == "menuitemcustom")
			  {
			  	// if the link is not set, use a custom url
			  	
			  	$menuitem_args = array
					(
			      'menu-item-title' => $title,
			      'menu-item-classes' => '',
			      'menu-item-object-id' => '',
			      'menu-item-object' => '',
			      'menu-item-status' => 'publish',
			      'menu-item-type' => 'custom',
			      
			      'menu-item-parent-id' => $most_recent_menu_item_id_per_depth[$depthindex - 1],
			      'menu-item-url' => $destination_url, 	// custom url
			    );
					
					// Set up default menu items
			  	$menu_item_id = wp_update_nav_menu_item
			  	(
			  		$menu_id, 
			  		0, // 0 creates a new item
			  		$menuitem_args
			   	);
			   	
			   	$most_recent_menu_item_id_per_depth[$depthindex] = $menu_item_id;
			  }
			  else
			  {
			  	echo "";
			  	var_dump($item);
			  	die();
			  }
			}	
			
			$menu = wp_get_nav_menu_object($menu_name);
		}
		else
		{
			echo "menu already exists<br />";
			var_dump($menu);
			// die();
		}
		
	  // assign this menu to the proper location
	  $locations = get_theme_mod( 'nav_menu_locations' );
	  if ($locations[$menu_name] == 0)
	  {
	  	$locations[$menu_name] = $menu->term_id;
			//var_dump($menu->term_id);
			//die();
			set_theme_mod('nav_menu_locations', $locations);
		}
	}
	
	echo json_encode($menu_ids);
	
	// update the widget(s) in the header(s), and change the menucontainer widget
	// to wpmenu one, also update the widgetmeta such that it points to the right menu?
	foreach ($items as $item)
	{
		$container_postid = $item->ID;
		$filter = array("postid" => $container_postid, "widgettype" => "menucontainer");
		$metabyplaceholderids = nxs_getwidgetsmetadatainpost_v2($filter);
		foreach ($metabyplaceholderids as $placeholderid => $meta)
		{
			$new_menu_index = $menu_ids["frontendmenus"][$container_postid][$placeholderid];
			$menu_name = $menu_ids["usagetoname"][$new_menu_index];
			
			echo "<br />fixing widget meta; postid:$container_postid placeholderid:$placeholderid new_menu_index:$new_menu_index menu_name:$menu_name<br />";
			
			$updatedvalues = array
			(
				"type" => "wpmenu",
				"menu_name" => $menu_name,
			);
			nxs_mergewidgetmetadata_internal($container_postid, $placeholderid, $updatedvalues);
		}
	}
	
  
	die();
		
	// for testing if the data consistency check works ok
	if (false)
	{
		//update_post_meta(943, "menu-item-object-id", "999");
	}
	
	if (false)
	{
		global $wpdb;
		$r = $wpdb->get_results("SELECT max(ID) FROM $wpdb->posts", ARRAY_A );
		var_dump($r);
	}
	
	
	die();
}