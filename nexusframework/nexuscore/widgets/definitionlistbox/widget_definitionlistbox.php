<?php

function nxs_widgets_definitionlistbox_geticonid() {
	return "nxs-icon-drawer";
}

// Setting the widget title
function nxs_widgets_definitionlistbox_gettitle() {
	return nxs_l18n__("Definition list box", "nxs_td");
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_definitionlistbox_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_definitionlistbox_gettitle(),
		"sheeticonid" => nxs_widgets_definitionlistbox_geticonid(),
		"fields" => array
		(
			// TITLE	
			
			array( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> "Title",
				"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> "Title",
				"placeholder" 		=> "Title goes here",
				"localizablefield"	=> true
			),
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),			
			
			// Form data
			
			array( 
				"id" 				=> "wrapper_selection_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Items", "nxs_td"),
			),
			array(
				"id" 				=> "items_genericlistid",
				"type" 				=> "staticgenericlist_link",
				"label" 			=> nxs_l18n__("Definition elements", "nxs_td")
			),
			
			array( 
				"id" 				=> "wrapper_selection_end",
				"type" 				=> "wrapperend"
			),
			
			// 
			
			array( 
				"id" 				=> "wrapper_selection_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Visualization", "nxs_td"),
			),
			
			array(
				"id" 				=> "listtype",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("List type", "nxs_td"),
				"dropdown" 			=> array("ul" => nxs_l18n__("Unordered list"), "ol" => nxs_l18n__("Ordered list")),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_selection_end",
				"type" 				=> "wrapperend"
			),
		)
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

function nxs_widgets_renderindefinitionlistbox($widget, $args)
{
	$functionnametoinvoke = 'nxs_widgets_' . $widget . '_renderindefinitionlistbox';
	//
	// invokefunction
	//
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		nxs_webmethod_return_nack("function not found; " . $functionnametoinvoke);	
	}
	
	return $result;
}


/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_definitionlistbox_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Localize atts
	$mixedattributes = nxs_localization_localize($mixedattributes);
	
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
	ob_start();
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));

	global $nxs_global_row_render_statebag;
	global $nxs_global_placeholder_render_statebag;
		
	// Appending custom widget class
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-definitionlist ";
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */

	$structure = nxs_parsepoststructure($items_genericlistid);
	
	if (count($structure) == 0) {
		$alternativemessage = nxs_l18n__("Warning:no items found", "nxs_td");
	}
	
	// Filler
	$htmlfiller = nxs_gethtmlforfiller();

	$listtypehtml = "ul";
	if ($listtype	== "" || $listtype	== "ul")
	{
		$listtypehtml = "ul";
	}
	else
	{
		$listtypehtml = "ol";
	}
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	if ($alternativemessage != "" && $alternativemessage != null)
	{
		nxs_renderplaceholderwarning($alternativemessage);
	} 
	else 
	{
		?>
		<h2 style='margin-bottom: 10px; '><?php echo $title; ?></h2>
		<div class="nxs-list nxs-applylinkvarcolor">
	    <<?php echo $listtypehtml;?>>
	    	<?php
	    	$index = -1;
				foreach ($structure as $pagerow)
				{
					$index = $index + 1;
					$rowcontent = $pagerow["content"];
					$currentplaceholderid = nxs_parsepagerow($rowcontent);
					$currentplaceholdermetadata = nxs_getwidgetmetadata($items_genericlistid, $currentplaceholderid);
					$widget = $currentplaceholdermetadata["type"];
					
					if ($widget != "" && $widget != "undefined")
					{
						$requirewidgetresult = nxs_requirewidget($widget);
					 	if ($requirewidgetresult["result"] == "OK")
					 	{
					 		$listboxitemargs = array();
			 				$listboxitemargs["postid"] = $postid;
			 				$listboxitemargs["placeholderid"] = $placeholderid;
			 				$listboxitemargs["metadata"] = $currentplaceholdermetadata;
					 		
					 		// now that the widget is loaded, instruct the widget (listboxitem) to render its output
					 		$subresult = nxs_widgets_renderindefinitionlistbox($widget, $listboxitemargs);
					 		if ($subresult["result"] == "OK")
					 		{
					 			// append subresult to the overall result
					 			echo "<li>";
					 			echo $subresult["html"];
					 			echo "</li>";
					 		}
					 		else
					 		{
					 			echo "[warning, widget found, but returned an error?]";
					 			var_dump($subresult);
					 		}
					 	}
					}
				}
				?>
	    </<?php echo $listtypehtml;?>>
		</div>
		<?php
		
		echo $htmlfiller;
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

/* INITIATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_definitionlistbox_initplaceholderdata($args)
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	
	// create a new generic list with subtype contact
	// assign the newly create list to the list property
	
	$subargs = array();
	$subargs["nxsposttype"] = "genericlist";
	$subargs["nxssubposttype"] = "definitionlist";	// NOTE!
	$subargs["poststatus"] = "publish";
	$subargs["titel"] = nxs_l18n__("definitionlist items", "nxs_td");
	$subargs["slug"] = $subargs["titel"];
	$subargs["postwizard"] = "defaultgenericlist";
	
	$response = nxs_addnewarticle($subargs);
	if ($response["result"] == "OK")
	{
		$args["items_genericlistid"] = $response["postid"];
		$args["items_genericlistid_globalid"] = nxs_get_globalid($response["postid"], true);
	}
	else
	{
		var_dump($response);
		die();
	}
	
	global $current_user;
	get_currentuserinfo();
	
	$result = nxs_widgets_initplaceholderdatageneric($args, $widgetname);
	return $result;
}

?>
