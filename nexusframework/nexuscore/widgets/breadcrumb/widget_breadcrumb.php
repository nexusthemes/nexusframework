<?php

function nxs_widgets_breadcrumb_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-arrow-double-right-light";
}

// Setting the widget title
function nxs_widgets_breadcrumb_gettitle() {
	return nxs_l18n__("Breadcrumb", "nxs_td");
}

// Unistyle
function nxs_widgets_breadcrumb_getunifiedstylinggroup() {
	return "breadcrumbwidget";
}

// Unicontent
function nxs_widgets_breadcrumb_getunifiedcontentgroup() {
	return "breadcrumbwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_breadcrumb_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_breadcrumb_gettitle(),
		"sheeticonid" => nxs_widgets_breadcrumb_geticonid(),
		"sheethelp" => nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1764396204"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_breadcrumb_getunifiedstylinggroup(),
		),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_breadcrumb_getunifiedcontentgroup(),),
		"fields" => array
		(
            
			// 
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("General", "nxs_td"),
				//"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "prefix",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Prefix", "nxs_td"),
				"placeholder" => nxs_l18n__("You are here", "nxs_td"),
				"unicontentablefield" => true
			),
            array(
				"id" 				=> "icon",
				"type" 				=> "icon",
				"label" 			=> nxs_l18n__("Icon between breadcrumbs", "nxs_td"),
				"unicontentablefield" => true
			),
            array(
				"id" 				=> "homepage_breadcrumb",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Homepage as", "nxs_td"),
				"dropdown" 			=> array
				(
					"" => nxs_l18n__("Default", "nxs_td"),
					"asIcon" => nxs_l18n__("Home-icon", "nxs_td"),
					"asText" => nxs_l18n__("Text", "nxs_td")
				),
				"tooltip" 			=> nxs_l18n__("This option let's you set whether the homepage is showed as an home-icon or text", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
            
            array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Miscellaneous", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
            
            array(
				"id" 				=> "alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
            
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			)
            
		)
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}


/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_breadcrumb_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_breadcrumb_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
    
    // Blend unicontent properties
	$unicontent = $temp_array["unicontent"];
	if (isset($unicontent) && $unicontent != "") {
		// blend unistyle properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_breadcrumb_getunifiedcontentgroup(), $unicontent);
		$temp_array = array_merge($temp_array, $unicontentproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
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
			
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
    
    
	$shouldrenderalternative = false;
    
    // image is required
	if (!isset($icon)) {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Please set an icon that will show up between the individual breadcrumb items.", "nxs_td");
	}
    
    /* OUTPUT
    ---------------------------------------------------------------------------------------------------- */


	global $nxs_global_placeholder_render_statebag;
	if ($shouldrenderalternative == true) {
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . "-warning ";
	} else {
		// Appending custom widget class
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " ";
	}
	

	if ($shouldrenderalternative) {
        
		if ($alternativehint == "") {
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
        
		nxs_renderplaceholderwarning($alternativehint); 
        
	} else {
        
		global $nxs_global_current_containerpostid_being_rendered;
		$postid = $nxs_global_current_containerpostid_being_rendered;
        
        // check if page has parent page
        $post_parent_id = wp_get_post_parent_id($postid);
        
        // check if page has categories
        $categoriesfilters = array();
		$categoriesfilters["uncategorized"] = "skip"; // Skip uncategorized breadcrumbs;
		$categories = get_the_category($postid);
		nxs_getfilteredcategories($categories, $categoriesfilters); // all categories of current page without the uncategorized ones.
        $countCategoriesOfCurrentPage = count($categories);
        
        // set variable for id of parent of current page (true if exists, false if doesn't exists)
        $currentPageHasParent = ($post_parent_id != false);
        
        // set variable for categorie of current page(true if exists, false if doesn't exists)
        $currentPageHasCategories = ($countCategoriesOfCurrentPage > 0);
        
        // start making an array list for breadcrumb items
		$list = array();
        
        // get homepage ID, name and link
        $homepageID = nxs_gethomepageid(); 
        $homepageName = get_the_title($homepageID);
        $homepageLink = get_permalink($homepageID);
        
        // check if we are on the homepage (true or false)
		$areWeOnTheHomepage = ($homepageID == $nxs_global_current_containerpostid_being_rendered);
        
        if($areWeOnTheHomepage){
            // nothing to show
        } else if($currentPageHasParent || !$currentPageHasCategories){ 
            // parentpages are stronger than categories of current page
            
		    $triesleft = 100;
            while($triesleft > 0){
                $name = get_the_title($postid);
                $link = get_permalink($postid);

                if($postid == $nxs_global_current_containerpostid_being_rendered){
                    // if this IS on the current page
                    $list[] = array("type" => "text", "naam" => $name); //insert current page name,type and link OR pagename type and link of parent, into array
                } else {
                    // if it's NOT the current page
                    $list[] = array("type" => "separator");
                    $list[] = array("type" => "link", "naam" => $name, "link" => $link); //insert current page name,type and link OR pagename type and link of parent, into array
                }
                
                $post_parent_id = wp_get_post_parent_id($postid);

                if($post_parent_id == false){
                    break; // no parent category, so let's jump out of the while loop
                } else {
                    $postid = $post_parent_id; // sets new id of parentpage of current page
                }

                $triesleft--;
            }
            
        } else if($currentPageHasCategories) {
            
            $category_of_current_page = $categories[0]; // get array keys and values for first degree category
            
            $category_id = $category_of_current_page->term_id; // get id of first degree category
            
            $currentPage_id = $nxs_global_current_containerpostid_being_rendered; // get id of current page viewing
            $name = get_the_title($currentPage_id); // get name of current page viewing
            $link = get_permalink($currentPage_id); // get link of current page viewing
            
            $list[] = array("type" => "text", "naam" => $name); //insert current page name, type and link into array

  		    $triesleft = 100; 
            while($triesleft > 0) {
                $current_term = get_term($category_id, 'category'); // returns array with keys and values of category, based on the category_id
                
                $category_name = $current_term->name;
                $category_link = get_category_link($category_id);

                $list[] = array("type" => "separator");
                $list[] = array("naam" => $category_name, "link" => $category_link, "type" => "link");
                
                if($current_term->parent && ( $current_term->parent != $current_term->term_id )){ // if current category has a parent categorie
                    $category_id = $current_term->parent; // sets new category_id, for your information: the one of the parent
                } else {
                    break; // no parent category, so let's jump out of the while loop
                }

                $triesleft--;
            }
        }
        
        // Add homepage to array 

        $homepage_show_as_icon_or_text = $homepage_breadcrumb; // check whether homepage is set as icon or text
        
        if ($homepage_show_as_icon_or_text == "asIcon") { 
            $homepageIcon = "nxs-icon-home";
        }
        
        if($areWeOnTheHomepage){ // are we currently on the homepage?
            $typeofitem = "text"; // current page is homepage, so NO link and separator for this item
            $list[] = array("type" => "text", "naam" => $homepageName, "icon" => $homepageIcon); // add homepage to lijst array
        } else {
            $list[] = array("type" => "separator");
            $list[] = array("type" => "link", "naam" => $homepageName, "link" => $homepageLink, "icon" => $homepageIcon); // add homepage to lijst array
        }
        
		$list = array_reverse($list); // reverse array, because the items were added back to forward.
        
        
        // OUTPUT
        
        // if icon between individual breadcrumbs is set (if not, warning of line 181 will show up)
        if($icon != "") {
            $icon_scale = "0-5"; 
            $icon_scale_cssclass = nxs_getcssclassesforlookup("nxs-icon-scale-", $icon_scale);
            $icon_html = '<span class="'.$icon.' '.$icon_scale_cssclass.'"></span> ';
        }
        
        echo "
        <div class='nxs-breadcrumb nxs-default-p nxs-align-".$alignment." nxs-applylinkvarcolor'>
            <div>
                <b>".$prefix."</b> 
            </div>
            <div>";
        
                foreach($list as $item) // get all arrays out of lijst
                {
       	            $item_type = $item['type']; // type is text or link
                    $item_icon = $item['icon']; // icon of homepage (returns nxs-icon-[icon])
                    
                    if($item_type == "link"){ 
                        if($item_icon){
                            echo "<a href='".$item['link']."'><span class='".$item_icon." nxs-icon-scale-0-5'></span></a> ";
                        } else {
                            echo"<a href='".$item['link']."'>".$item['naam']."</a> ";
                        }
                        
                    } else if($item_type == "text") {
                        if($item_icon){
                            echo "<span class='".$item_icon." nxs-icon-scale-0-5'></span>";
                        } else {
                            echo $item['naam'];
                        }
                    } else if($item_type == "separator"){
                        echo $icon_html;
                    }
                }
        
            echo"
            </div>
        </div>";	        
	} 

			
	/* ------------------------------------------------------------------------------------------------- */
	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-'.$placeholderid;
	return $result;
}

function nxs_widgets_breadcrumb_initplaceholderdata($args)
{
	extract($args);

    $args['alignment'] = "left";
    $args['icon'] = "nxs-icon-arrow-right-light";
    $args['homepage_breadcrumb'] = "";

	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_breadcrumb_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_breadcrumb_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}


?>
