<?php

function nxs_widgets_eventsbox_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-calendar";
}

// Setting the widget title
function nxs_widgets_eventsbox_gettitle() {
	return nxs_l18n__("eventsbox[nxs:widgettitle]", "nxs_td");
}

// Unistyle
function nxs_widgets_eventsbox_getunifiedstylinggroup() {
	return "eventsboxwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_eventsbox_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_eventsbox_gettitle(),
		"sheeticonid" => nxs_widgets_eventsbox_geticonid(),
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/events-widget/"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_eventsbox_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			// TITLE
	   
			array( 
				"id"     			=> "wrapper_begin",
				"type"    			=> "wrapperbegin",
				"label"    			=> nxs_l18n__("Title", "nxs_td"),
				"initial_toggle_state" => "closed",
			),
			
			array
			( 
				"id"     			=> "title",
				"type"     			=> "input",
				"label"    			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder"   	=> nxs_l18n__("Title goes here", "nxs_td"),
			),
			array(
				"id"     			=> "title_heading",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Title importance", "nxs_td"),
				"dropdown"    		=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			array(
				"id"     			=> "title_alignment",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Title alignment", "nxs_td"),
				"dropdown"    		=> nxs_style_getdropdownitems("title_halignment"),
				"unistylablefield"	=> true
			),
			  
			array(
				"id"     			=> "title_fontsize",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Override title fontsize", "nxs_td"),
				"dropdown"    		=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			array(
				"id"     			=> "title_heightiq",
				"type"     			=> "checkbox",
				"label"    			=> nxs_l18n__("Row align titles", "nxs_td"),
				"tooltip"    		=> nxs_l18n__("When checked, the widget's title will participate in the title alignment of other partipating widgets in this row", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id"     			=> "wrapper_end",
				"type"     			=> "wrapperend"
			),
			
			// GENERAL
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("General", "nxs_td"),
			),
			
			array(
				"id" 				=> "items_genericlistid",
				"type" 				=> "staticgenericlist_link",
				"label" 			=> nxs_l18n__("Edit events", "nxs_td"),
			),
			array(
				"id"     			=> "eventitem_heading",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Event title importance", "nxs_td"),
				"dropdown"    		=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "month_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Month color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "day_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Day color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "items_filter_hideeventsinpast",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Hide events in past", "nxs_td"),
			),
			
			array(
				"id" 				=> "items_filter_maxcount",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Number of events", "nxs_td"),
				"dropdown" 			=> array("@@@empty@@@"=>"default (no max)","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9","10"=>"10","20"=>"20","30"=>"30","40"=>"40","50"=>"50","100"=>"100")
			),		
			array(
				"id" 				=> "date_size",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Date size", "nxs_td"),
				"dropdown" 			=> array
				(
					"1-0" => nxs_l18n__("1x", "nxs_td"),
					"0-75" => nxs_l18n__("0.75x", "nxs_td"),
				),
				"unistylablefield"	=> true
			),	
			
			array( 
				"id" 				=> "wrapper_input_end",
				"type" 				=> "wrapperend"
			),
			
			/* BUTTON
			---------------------------------------------------------------------------------------------------- */
			
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Button", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "button_text",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Button text", "nxs_td"),
				"placeholder"		=> "Read more",
				"localizablefield"	=> true
			),	
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Article link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an article within your site.", "nxs_td"),
			),
			array(
				"id" 				=> "destination_url",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("External link", "nxs_td"),
				"placeholder"		=> nxs_l18n__("http://www.nexusthemes.com", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an external source using the full url.", "nxs_td"),
				"localizablefield"	=> true
			),
			array(
				"id" 				=> "button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button scale", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true,
			),
			array( 
				"id" 				=> "button_color",
				"type" 				=> "colorzen", 
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "button_alignment",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button alignment", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_halignment"),
				"unistylablefield"	=> true,
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			

		)
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

function nxs_parse_ddmmyyyy($value)
{
	$result = NULL;
  if ($value != "")
  {
		$splitted = explode("-", $value);
		$day = $splitted[0];
		$month = $splitted[1];
		$year = $splitted[2];

		$result = new DateTime();
		$result->setDate($year, $month, $day);
  }
  else
  {
  	echo "value not set";
  }
  return $result;
}

/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_eventsbox_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_eventsbox_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	
	global $nxs_global_row_render_statebag;
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-events" . " " . $cssclass;

	/* HOVER MENU
	---------------------------------------------------------------------------------------------------- */
	
	$items_genericlistid = $mixedattributes['items_genericlistid'];

	// HOVER MENU HTML

	ob_start();

	?>
	<ul>
		<?php 
		if ($items_genericlistid == "") 
		{ 
			?>
			<li title='<?php nxs_l18n_e("Edit[nxs:tooltip]", "nxs_td"); ?>'>
				<a href='#' class='nxs-defaultwidgetclickhandler' title='<?php nxs_l18n_e("Edit[nxs:tooltip]", "nxs_td"); ?>' onclick="nxs_js_edit_widget(this); return false;">
					<span class='nxs-icon-plug'></span>
				</a>
			</li>
			<?php 
		} 
		else 
		{ 
			global $nxs_global_current_containerpostid_being_rendered;
			$currentcontainerposturl = nxs_geturl_for_postid($nxs_global_current_containerpostid_being_rendered);
			$destinationurl = nxs_geturl_for_postid($items_genericlistid);
			
			$nxsrefurlspecial = urlencode(base64_encode($currentcontainerposturl));
			$destinationurl = nxs_addqueryparametertourl_v2($destinationurl, "nxsrefurlspecial", $nxsrefurlspecial, false);
			?>
			<a href='#' class='nxs-defaultwidgetclickhandler' title='<?php nxs_l18n_e("Edit[nxs:tooltip]", "nxs_td"); ?>' onclick="var url='<?php echo $destinationurl; ?>'; nxs_js_redirect(url); return false;">
				<li title='<?php nxs_l18n_e("Edit[nxs:tooltip]", "nxs_td"); ?>'>
					<span class='nxs-icon-calendar'></span>
				</li>
			</a>
			<a href='#' title='<?php nxs_l18n_e("Edit[nxs:tooltip]", "nxs_td"); ?>' onclick="nxs_js_edit_widget(this); return false;">
				<li title='<?php nxs_l18n_e("Edit[nxs:tooltip]", "nxs_td"); ?>'>
					<span class='nxs-icon-plug'></span>
				</li>
			</a>
			<?php 
		} 
		?>
		<li title='<?php nxs_l18n_e("Move[nxs:tooltip]", "nxs_td"); ?>' class='nxs-draggable nxs-existing-pageitem nxs-dragtype-placeholder' id='draggableplaceholderid_<?php echo $placeholderid; ?>'>
			<span class='nxs-icon-move'></span>
			<div class="nxs-drag-helper" style='display: none;'>
				<div class='placeholder'>
					<span id='placeholdertemplate_<?php echo $placeholdertemplate; ?>' class='<?php echo nxs_widgets_eventsbox_geticonid();?>'></span>
				</div>
			</div>					
		</li>
		<a class='nxs-no-event-bubbling' href='#' onclick='nxs_js_popup_placeholder_wipe("<?php echo $postid; ?>", "<?php echo $placeholderid; ?>"); return false;'>
			<li title='<?php nxs_l18n_e("Delete[nxs:tooltip]", "nxs_td"); ?>'><span class='nxs-icon-trash'></span></li>
		</a>
		<?php 
		if (nxs_shoulddebugmeta())
		{
			ob_start();
			?>
			<a class='nxs-no-event-bubbling' href='#' onclick="nxs_js_edit_widget_v2(this, 'debug'); return false; return false;">
       	<li title='<?php nxs_l18n_e("Debug[tooltip]", "nxs_td"); ?>'>
       		<span class='nxs-icon-search'></span>
       	</li>
    	</a>
    	<?php
    	$debughtml = ob_get_contents();
			ob_end_clean();
		}
		else
		{
			$debughtml = "";
		}
		echo $debughtml;
		?>
	</ul>
	<?php 
	
	$menu = ob_get_contents();
	ob_end_clean();
	
	global $nxs_global_placeholder_render_statebag;
	$nxs_global_placeholder_render_statebag["menutopright"] = $menu;
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	$meta = nxs_get_postmeta($postid);
	if (isset($meta["sidebar_postid"])) {
		$existingsidebarid = $meta["sidebar_postid"];
	} else {
		$existingsidebarid = "";
	}
	
	$htmltitle = nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, $destination_articleid, $destination_url);
	
	// Event item heading
	if ($eventitem_heading != "") {
		$eventitem_heading = "h" . $eventitem_heading;	
	} else {
		$eventitem_heading = "h1";
	}

	// Colors
	$month_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $month_color);
	$day_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $day_color);
	
	
	/* LINK
	---------------------------------------------------------------------------------------------------- */
	
	// Article link
	if ($destination_articleid != "") {
		$destination_url = nxs_geturl_for_postid($destination_articleid);
	}
	
	/* BUTTON
	---------------------------------------------------------------------------------------------------- */
	
	// Button aligment
	$button_alignment = nxs_getcssclassesforlookup("nxs-align-", $button_alignment);
	
	// Button color
	$button_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $button_color);
	
	// Button scale
	$button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $button_scale);
	
	// Button	
	if ($destination_articleid != "") {
		$button = '<a href="' . $destination_url .'" class="nxs-button ' . $button_color_cssclass .' ' . $button_scale_cssclass .'">' . $button_text . '</a>';
	} else if ($destination_url != "") {
		$button = '<a href="' . $destination_url .'" class="nx-button ' . $button_color_cssclass .' ' . $button_scale_cssclass .'" target="_blank">' . $button_text . '</a>';
	}
	
	// Applying alignment to button
	$button = '<p class="' . $button_alignment . ' nxs-padding-bottom0">' . $button . '</p>';
	
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	ob_start();
	
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-events ";
	
	$structure = nxs_parsepoststructure($items_genericlistid);
	$numofslides = count($structure);
	
	echo $htmltitle;
	
	$nritemsshowing = 0;
	
	// Rendering of individual events
	foreach ($structure as $pagerow)
	{
		$content = $pagerow["content"];
		$slideplaceholderid = nxs_parsepagerow($content);
		$placeholdermetadata = nxs_getwidgetmetadata($items_genericlistid, $slideplaceholderid);
		$placeholdertype = $placeholdermetadata["type"];					
			
		if ($placeholdertype == "eventsboxitem") 
		{	

			$title = $placeholdermetadata['title'];
			$subtitle = $placeholdermetadata['subtitle'];
			$button_heightiq = "";
			$item_button = nxs_gethtmlforbutton($placeholdermetadata['button_text'], $placeholdermetadata['button_scale'], $placeholdermetadata['button_color'], $placeholdermetadata['destination_articleid'], $placeholdermetadata['destination_url'], $placeholdermetadata['destination_target'], $placeholdermetadata['button_alignment'], $placeholdermetadata['destination_js'], $placeholdermetadata['button_heightiq'], $placeholdermetadata['button_fontzen']);
			$date_dd_mm_yy = $placeholdermetadata['date_dd_mm_yy'];
			// split date 
			$splitted = explode("-", $date_dd_mm_yy);
			$day = $splitted[0];
			$monthwithleadingzero = $splitted[1];
			$month = nxs_getlocalizedmonth($monthwithleadingzero);
			$destination_articleid = $placeholdermetadata['destination_articleid'];
			$destination_url = $placeholdermetadata['destination_url'];
			
			$skipitem = false;
			if ($items_filter_hideeventsinpast != "")
			{
				$objDateTime = new DateTime('NOW');
				$d1 = nxs_parse_ddmmyyyy($date_dd_mm_yy);
				$now = new DateTime();
				$today_dd_mm_yyyy = $now->format('d-m-Y');
				$d2 = nxs_parse_ddmmyyyy($today_dd_mm_yyyy);
				if ($d1 < $d2) {
					// before today
					$skipitem = true;
				}
			}
			
			// ensure we don't show too many items
			if ($skipitem == false)
			{
				if ($items_filter_maxcount == "" || $items_filter_maxcount == 0 || $items_filter_maxcount == "@@@empty@@@")
				{
					// good; show it!
					$nritemsshowing++;
				}
				else if ($nritemsshowing < $items_filter_maxcount)
				{
					// good; show it!
					$nritemsshowing++;
				}
				else
				{
					// too many; skip them!
					$skipitem = true;
				}
			}

			// Article link
			if ($destination_articleid != "") {
				$destination_url = nxs_geturl_for_postid($destination_articleid);
			}

			if ($skipitem === false) {
				if ($shouldrenderalternative) {
					if ($alternativehint == "") {
						$alternativehint = nxs_l18n__("Missing input", "nxs_td");
					}
					nxs_renderplaceholderwarning($alternativehint); 
				} else {
					echo '<div class="nxs-evententry nxs-applylinkvarcolor">';
						
						// Date
						if ($destination_url != "") 
						{ 
							if ($destination_articleid != "")
							{
								// internal link
								echo '<a href="' . $destination_url . '">'; 
							}
							else
							{
								// external link
								echo '<a href="' . $destination_url . '" target="_blank">'; 
							}
						}
						echo '
						<div class="nxs-date nxs-date-size-'.$date_size.'">
							<h4 class="icon nxs-border-width-1-0 month ' . $month_color_cssclass . '">' . $month . '</h4>
							<h4 class="icon nxs-border-width-1-0 day ' . $day_color_cssclass . '">' . $day . '</h4>	
						</div>';
						if ($destination_url != "") { echo '</a>'; }
					
						// Title
						if ($destination_articleid != "")
						{
							// internal link
							echo '<a href="' . $destination_url . '">'; 
						}
						else
						{
							// external link
							echo '<a href="' . $destination_url . '" target="_blank">'; 
						}
						echo '<' . $eventitem_heading . ' class="nxs-title title">' . $title . '</a></' . $eventitem_heading . '>';
						if ($destination_url != "") { echo '</a>'; }
						
						// Subtitle
						echo'<div class="subtitle nxs-default-p nxs-padding-bottom0">' . $subtitle . '</div>';
						
						if ($item_button != "") 
						{ 
							echo '<div class="nxs-clear padding"></div>';	
							echo $item_button; 
						}
						echo '<div class="nxs-clear"></div>
							</div>';
			  }
			} else {
				// item is skipped; ignore
			}
		}
	}
	
	if ($button_text != "") {echo '<div class="nxs-clear padding"></div>';}
	echo $button;
	
	$html = ob_get_contents();
	ob_end_clean();

	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

/* INITIATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_eventsbox_initplaceholderdata($args)
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	
	// create a new generic list with subtype gallery
	// assign the newly create list to the list property
	
	$subargs = array();
	$subargs["nxsposttype"] = "genericlist";
	$subargs["nxssubposttype"] = "eventsbox";	// NOTE!
	$subargs["poststatus"] = "publish";
	$subargs["titel"] = nxs_l18n__("Events items[title]", "nxs_td");
	$subargs["slug"] = nxs_l18n__("Events[slug]", "nxs_td");
	$subargs["postwizard"] = "defaultgenericlist";

	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_eventsbox_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
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
function nxs_widgets_eventsbox_updateplaceholderdata($args) 
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	$result = nxs_widgets_updateplaceholderdatageneric($args, $widgetname);
	return $result;
}

?>
