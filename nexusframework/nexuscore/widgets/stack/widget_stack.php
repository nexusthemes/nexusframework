<?php
/* 
	NOTE: This file is lazy loaded; its only loaded when this widget is actually used

	TABLE OF CONTENTS
	----------------------------------------------------------------------------------------------------
	- WIDGET HTML
	- WIDGET POPUP
	- MEDIA MANAGER
	- UPDATING WIDGET DATA
*/

function nxs_widgets_stack_geticonid()
{
	return "nxs-icon-menucontainer";
}

// Setting the widget title
function nxs_widgets_stack_gettitle() {
	return nxs_l18n__("Stack[nxs:widgettitle]", "nxs_td");
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_stack_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	// The following array is used in the "popup.php" file: the main php file that renders the widget popup
	// and returns the user's chosen values and options.
	// You can choose the from the following popup UI options: input, textarea, image, article_link, select
	// its also used in the updateplaceholder function.
	
	// Each UI option has the following required and optional values:
	
	// ID: 		The internal identification used to store the sessiondata with javascript, this ID corresponds to the PHP variable name used in the htmlvisualization function below
	// TYPE:		Denotes the type of UI option 
	// LABEL:		The label used in the popup to explain what the UI does (e.g. "Button text" or "Choose image")
	// PLACEHOLDER: Value containing optional textarea and input placeholder text
	// INITIALVALUE: Defines the value that is used when the widget is constructed (dragged on the screen)
	// DROPDOWN: 	Array containing the values shown when using the "select" type
	
	// It's a best practice to prefix the used variables with the name of the widget folder and an underscore ("_") to prevent PHP naming conflicts

	$options = array
	(
		"sheettitle" => nxs_widgets_stack_gettitle(),
		"sheeticonid" => nxs_widgets_stack_geticonid(),

		"fields" => array
		(
			array(
				"id" 				=> "items_genericlistid",
				"type" 				=> "staticgenericlist_link",
				"label" 			=> nxs_l18n__("Items", "nxs_td")
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

function nxs_widgets_stack_render_webpart_render_htmlvisualization($args) 
{	
	//
	extract($args);
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	$meta = nxs_get_corepostmeta($postid);
	if (isset($meta["sidebar_postid"]))
	{
		$existingsidebarid = $meta["sidebar_postid"];
	}
	else
	{
		$existingsidebarid = "";
	}
	
	$mixedattributes = array_merge($temp_array, $args);
	
	$items_genericlistid = $mixedattributes['items_genericlistid'];
	$structure = nxs_parsepoststructure($items_genericlistid);
	
	//
	// render hover menu
	//
	
	nxs_ob_start();

	?>
	<ul class='stack-items'>
		<?php if (count($structure) == 0) { ?>
		<li class='stack-item'>
			<p>
				Stack is empty
			</p>
		</li>
		<?php	} ?>
	<?php
	
	foreach ($structure as $pagerow)
	{
		$content = $pagerow["content"];
		
		$innerplaceholderid = nxs_parsepagerow($content);
		if ($innerplaceholderid == null)
		{
			//
		}
		else
		{
			$innerplaceholdermetadata = nxs_getwidgetmetadata($items_genericlistid, $innerplaceholderid);
			$innerplaceholdertype = $innerplaceholdermetadata["type"];
			
			if ($innerplaceholdertype != "" && $innerplaceholdertype != "undefined")
			{
				
				// render the placeholder here
				
				$temp_array = nxs_getwidgetmetadata($items_genericlistid, $innerplaceholderid);
				$innermixedattributes = $temp_array;
				$innermixedattributes["postid"] = $postid;
				$innermixedattributes["rendermode"] = "anonymous";
				$innermixedattributes["contenttype"] = "webpart";
				$innermixedattributes["webparttemplate"] = "render_htmlvisualization";
				$innermixedattributes["placeholderid"] = $innerplaceholderid;
				$innermixedattributes["placeholdertemplate"] = $innerplaceholdertype;
				
				$innerwidgetrenderresult = nxs_getrenderedwidget($innermixedattributes);		
				
				if ($innerwidgetrenderresult["result"] == "OK")
				{
					global $nxs_global_placeholder_render_statebag;
					$innerwidgetclass = $nxs_global_placeholder_render_statebag["widgetclass"];
					?>
					<li class='stack-item <?php echo $innerwidgetclass; ?>'>
					<?php echo $innerwidgetrenderresult["html"]; ?>
					</li>
					<?php
				}
				else
				{
					// failed, if user is logged in, output error
				}
			}
			else
			{
				// undefined element, ignored!
			}
		}
		
		?>
		<?php
	}
	
	?>
	</ul>
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		// overrule generic widget hover menu, as set by inner-widgets :)
		$hovermenuargs = array();
		$hovermenuargs["postid"] = $postid;
		$hovermenuargs["placeholderid"] = $placeholderid;
		$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
		$hovermenuargs["metadata"] = $mixedattributes;
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	}

	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	global $nxs_global_placeholder_render_statebag;
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-stack ";

	return $result;
}

/* INITIATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_stack_initplaceholderdata($args)
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	
	// create a new generic list with subtype gallery
	// assign the newly create list to the list property
	
	$subargs = array();
	$subargs["nxsposttype"] = "genericlist";
	$subargs["nxssubposttype"] = "stack";	// NOTE!
	$subargs["poststatus"] = "publish";
	$subargs["titel"] = "stack items";
	$subargs["slug"] = $subargs["titel"] . " " . nxs_generaterandomstring(6);
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
	
	$result = nxs_widgets_initplaceholderdatageneric($args, $widgetname);
	return $result;
}


/* UPDATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_stack_updateplaceholderdata($args) 
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	$result = nxs_widgets_updateplaceholderdatageneric($args, $widgetname);
	return $result;
}

?>