<?php

// Setting the widget image
function nxs_widgets_csv_geticonid() {
	return "nxs-icon-csv";
}

// Setting the widget title
function nxs_widgets_csv_gettitle() {
	$widget_name = basename(dirname(__FILE__));
	return __($widget_name);
}

// Unistyling
function nxs_widgets_csv_getunifiedstylinggroup() {
	return "csvwidget";
}

// Unicontent
function nxs_widgets_csv_getunifiedcontentgroup() {
	return "csvwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_csv_home_getoptions() 
{
	$options = array
	(
		"sheettitle" 		=> "CSV table",
		"sheeticonid" 		=> nxs_widgets_csv_geticonid(),
		"supporturl" => "https://www.wpsupporthelp.com/wordpress-questions/widgets-csv-wordpress-questions-21/",
		"unifiedstyling" 	=> array ("group" => nxs_widgets_csv_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array ("group" => nxs_widgets_csv_getunifiedcontentgroup(),),
		"fields" => array
		(
			// LOOKUPS
			
			array
			( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Lookups", "nxs_td"),
      	"initial_toggle_state" => "closed-if-empty",
      	"initial_toggle_state_id" => "lookups",
			),
			array
      (
				"id" 					=> "lookups",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("Lookup table (evaluated one time when the widget renders)", "nxs_td"),
			),
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),
					
		
			// SEPARATOR
			
			array( 
				"id" 				=> "wrapper_sep_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Separators", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "col_seperator",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Column seperator", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@empty@@@"=>nxs_l18n__("Comma", "nxs_td"),
					"semicolon"=>nxs_l18n__(";", "nxs_td"),
				),
			),
			
			/*
			array(
				"id" 				=> "line_seperator",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Line separator", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@empty@@@"=>nxs_l18n__("Slash n", "nxs_td"),
				),
			),
			*/			
			
			array( 
				"id" 				=> "wrapper_sep_end",
				"type" 				=> "wrapperend",
			),
			
		
			// TITLE
			
			array( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"initial_toggle_state"	=> "closed-if-empty",
				"initial_toggle_state_id" => "title",

			),
			
			array( 			
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"tooltip"			=> nxs_l18n__("If you want to give the entire widget a title, you can use this option.", "nxs_td"),
				"unicontentablefield" => true,
			),	
			array(
				"id" 				=> "title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title importance", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Override title fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),	
			array(
				"id" 				=> "title_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "top_info_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Top info color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id"     			=> "top_info_padding",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Top info padding", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("padding"),
				"unistylablefield"	=> true
			),
					
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),
			
			// ICON
			
			array( 
				"id" 				=> "wrapper_icon_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Icon", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "icon",
				"type" 				=> "icon",
				"label" 			=> nxs_l18n__("Icon", "nxs_td"),
				"unicontentablefield" => true,
			),
			array(
				"id"     			=> "icon_scale",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Icon scale", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("icon_scale"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_icon_end",
				"type" 				=> "wrapperend"
			),
			
			// DATA
			
			array( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Data", "nxs_td"),
			),
			array(
				"id" 				=> "csv_data",
				"type" 				=> "textarea",
				"label" 			=> nxs_l18n__("CSV data", "nxs_td"),
				"unicontentablefield" => true,
			),
			array( 
				"id" 				=> "responsive",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Disable responsiveness", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),
			
			// BUTTON
			
			array( 
				"id" 				=> "wrapper_button_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Button", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array(
				"id" 				=> "button_text",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Button text", "nxs_td"),
				"placeholder"		=> "Read more",
				"unicontentablefield" => true,
			),	
			
			array(
				"id" 				=> "button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "button_color",
				"type" 				=> "colorzen", // "select",
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "button_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Button alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"label" 			=> nxs_l18n__("Article link", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Link the button to an article within your site.", "nxs_td"),
				"unicontentablefield" => true,
			),
			
			array( 
				"id" 				=> "wrapper_button_end",
				"type" 				=> "wrapperend"
			),
		),
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_csv_parsedata($mixedattributes)
{
	extract($mixedattributes);
	
	if (!isset($col_seperator))
	{
		// default: ,
		$col_seperator = ",";
	}
	else 
	{
		if ($col_seperator == "@@@empty@@@")
		{
			$col_seperator = ",";
		}
		else if ($col_seperator == "semicolon")
		{
			$col_seperator = ";";
		}
	}
	
	$csv_data = str_replace("@@NXSNEWLINE@@", "\n", $csv_data);
	
	if (!isset($line_seperator))
	{
		if (nxs_stringcontains($csv_data, "\n"))
		{
			$line_seperator = "\n";
		}
		else
		{
			// important step; the exporter replaces \n's to \r's
			$line_seperator = "\r";
		}
	}
	if (!isset($skip_empty_rows))
	{
		// default: ,
		$skip_empty_rows = "true";
	}
	if (!isset($is_first_row_header))
	{
		// default: ,
		$is_first_row_header = "true";
	}
	
	$result = array();
	$result["columns"] = array();
	$result["rows"] = array();
	
	// parse csv_data based on other values
	$rows = explode($line_seperator, $csv_data);
	
	$rowindex = 0;
	$foundheader = false;
	foreach ($rows as $currentrow)
	{
		$shouldprocessrow = true;
		$cells = explode($col_seperator, $currentrow);	
		if ($currentrow == "" || count($cells) == 0)
		{
			// empty line
			if ($skip_empty_rows == "true")
			{
				$shouldprocessrow = false;
			}
		}
		
		//
		if ($shouldprocessrow)
		{
			if ($foundheader === false)
			{
				// this is the header :)
				$result["columns"] = $cells;
				$foundheader = true;
			}
			else
			{
				// this is a data row :)
				$result["rows"][] = $cells;
			}
		}
	}
	
	return $result;
}

function nxs_widgets_csv_render_webpart_render_htmlvisualization($args) 
{
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	if ($render_behaviour == "code")
	{
		//
		$mixedattributes = $args;
	}
	else
	{
		// Every widget needs it's own unique id for all sorts of purposes
		// The $postid and $placeholderid are used when building the HTML later on
		$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
		// Blend unistyling properties
		$unistyle = $temp_array["unistyle"];
		if (isset($unistyle) && $unistyle != "") {
			// blend unistyle properties
			$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_csv_getunifiedstylinggroup(), $unistyle);
			$temp_array = array_merge($temp_array, $unistyleproperties);	
		}
		
		// Blend unicontent properties
		$unicontent = $temp_array["unicontent"];
		if (isset($unicontent) && $unicontent != "") {
			// blend unistyle properties
			$unicontentproperties = nxs_unicontent_getunicontentproperties(nxs_widgets_csv_getunifiedcontentgroup(), $unicontent);
			$temp_array = array_merge($temp_array, $unicontentproperties);
		}
		
		// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
		$mixedattributes = array_merge($temp_array, $args);
		
		// Lookup atts
		$mixedattributes = nxs_filter_translatelookup($mixedattributes, array("title"));
		
		if ($render_behaviour == "code")
		{
			//
		}
		else
		{
			//	
			$hovermenuargs = array();
			$hovermenuargs["postid"] = $postid;
			$hovermenuargs["placeholderid"] = $placeholderid;
			$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
			$hovermenuargs["metadata"] = $mixedattributes;
			nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs); 
		}
	}
	
	// Translate model magical fields
	if (true)
	{
		global $nxs_g_modelmanager;
		
		$combined_lookups = nxs_lookups_getcombinedlookups_for_currenturl();
		$combined_lookups = array_merge($combined_lookups, nxs_parse_keyvalues($mixedattributes["lookups"]));

		// evaluate the lookups widget values line by line
		$sofar = array();
		foreach ($combined_lookups as $key => $val)
		{
			$sofar[$key] = $val;
			//echo "step 1; processing $key=$val sofar=".json_encode($sofar)."<br />";

			//echo "step 2; about to evaluate lookup tables on; $val<br />";
			// apply the lookup values
			$sofar = nxs_lookups_blendlookupstoitselfrecursively($sofar);

			// apply shortcodes
			$val = $sofar[$key];
			//echo "step 3; result is $val<br />";

			//echo "step 4; about to evaluate shortcode on; $val<br />";

			$val = do_shortcode($val);
			$sofar[$key] = $val;

			//echo "step 5; $key evaluates to $val (after applying shortcodes)<br /><br />";

			$combined_lookups[$key] = $val;
		}
		
		// apply the lookups and shortcodes to properties
		$magicfields = array("csv_data");
		$translateargs = array
		(
			"lookup" => $combined_lookups,
			"items" => $mixedattributes,
			"fields" => $magicfields,
		);
		$mixedattributes = nxs_filter_translate_v2($translateargs);
	}
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
	
	// Turn on output buffering
	nxs_ob_start();
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));	
	
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;
	
	//$sitemeta = nxs_getsitemeta();
	//$enablelazyloadwheneditoron = $sitemeta["enablelazyloadwheneditoron"];
	
	// Turn on output buffering
	nxs_ob_start();
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));
		
	// Appending custom widget class
	global $nxs_global_placeholder_render_statebag;
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " " . $cssclass;
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */	
	
	// Title fontsize
	$title_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $title_fontsize);
	
	// Title alignment
	$title_alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $title_alignment);

	// Top info padding and color
	$top_info_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $top_info_color);
	$top_info_padding_cssclass = nxs_getcssclassesforlookup("nxs-padding-", $top_info_padding);
	
	// Responsive
	if ($responsive != "") 	{ $responsive = 'not-responsive'; }
	else					{ $responsive = 'responsive'; }
	
	/* ICON
	---------------------------------------------------------------------------------------------------- */
	
	// Icon scale
	$icon_scale_cssclass = nxs_getcssclassesforlookup("nxs-icon-scale-", $icon_scale);
		
	// Icon
	if ($icon != "") {$icon = '<span class="' . $icon . ' ' . $icon_scale_cssclass . '"></span>';}
	
	// Title heading
	if ($title_heading != "") {
		$headingelement = "h" . $title_heading;	
	} else {
		$headingelement = "h1";
	}
	
	if ($title != "") { $title = '<'.$headingelement.' class="nxs-title '.$title_fontsize_cssclass.' '.$title_alignment_cssclass.'">' . $title . '</' . $headingelement . '>'; }
	
    if ($title_alignment == "center") { $top_info_title_alignment = "margin: 0 auto;"; } else
	if ($title_alignment == "right")  { $top_info_title_alignment = "margin-left: auto;"; } 
    
	// Default HMTL rendering
	$htmlforbutton = nxs_gethtmlforbutton($button_text, $button_scale, $button_color, $destination_articleid, $destination_url, $destination_target, $button_alignment, $destination_js);
	
	if (nxs_has_adminpermissions())
	{
		if ($_REQUEST["nxs"] == "nocsv")
		{
			$shouldrenderalternative = true;
			$alternativehint = "temporarily disabled";
		}
	}
	
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) 
	{
		if ($alternativehint == "") {
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} 
	else 
	{
		$html = "";
		$parsed = nxs_csv_parsedata($mixedattributes);
		
		$columns = $parsed["columns"];
		$rows = $parsed["rows"];
		
		/* Top info
		---------------------------------------------------------------------------------------------------- */
		
		if ($title != "") { 
            
			echo '
			<div class="top-wrapper nxs-border-width-1-0 ' . $top_info_color_cssclass . ' ' . $top_info_padding_cssclass . '">
				<div class="nxs-table" style="'.$top_info_title_alignment.'">';
				
					// Icon
					echo $icon;
					
					// Title
					echo $title;
					
	
			echo '</div>
			</div>
			
			<div class="nxs-clear nxs-padding-top10"></div>';
		}
			
		/* Rendering of individual table
		---------------------------------------------------------------------------------------------------- */
	
		$html .= '<table id="table-' . $placeholderid . '" class="' . $responsive . ' nxs-applylinkvarcolor">';
	  $html .= '<thead class="' . $headcssclass . '">';
	    
	    //
	    foreach ($columns as $currentcolumn) {
				$skip = false;
				
				if (isset($mixedattributes["output_exclude_" . $currentcolumn])) {
					if ($mixedattributes["output_exclude_" . $currentcolumn] != "") {
						$skip = true;
					}
				}
				
				if (!$skip) {
					// $columnname = nxs_getcolumntext($articlevariation, $columnid);
					$html .= "<th>" . nxs_render_html_escape_singlequote($currentcolumn) . "</th>";
				}
			}
			$html .= "</tr>";
	    
	    //
	    
	    $html .= "</thead>";
	    $html .= "<tbody>";
	    
	    $rowcounter = 0;
	    
	    foreach ($rows as $currentrowdataarray)
			{
				$rowcounter += 1;
				
				if ($rowcounter % 2 == 0)
				{
					$rowcss = $evenrowcssclass;
				}
				else
				{
					$rowcss = $oddrowcssclass;
				}
				
				$html .= "<tr class='" . $rowcss . "'>";
									
				foreach ($currentrowdataarray as $keyindex=>$value)
				{
					$key = $columns[$keyindex];
					
					$skip = false;
			
					if (isset($mixedattributes["output_exclude_" . $key]))
					{
						if ($mixedattributes["output_exclude_" . $key] != "")
						{
							$skip = true;
						}
					}
					
					if ($skip === false)
					{
						$columnmeta = array();
						$type = $columnmeta["type"];
						if (!isset($type))
						{
							$type = "string";
						}
						
						if ($type == "string" || $type == "clocktime" || $type == "integer")
						{
							$html .= "<td>" . $value . "</td>";
						}
						else
						{
							$html .= "<td>" . "[" . $type . "]" . $value . "</td>";
						}
					}
					else
					{
					 	// skip
					}
				}
				$html .= "</tr>";
			}
			
			$html .= "</tbody>";
	    $html .= "</table>";
	    
	    echo $html;
		
		
		/* ------------------------------------------------------------------------------------------------- */
		
		// Button
		if ($htmlforbutton != "") { echo '<div class="nxs-clear nxs-margin"></div>'; }
		echo $htmlforbutton;	
		
	}
	
	/* ------------------------------------------------------------------------------------------------- */
	 
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	return $result;
}

function nxs_widgets_csv_initplaceholderdata($args)
{
	extract($args);

	//$args['key'] = "value";
	$args['csv_data'] = "head1,head2,head3\r\ncol1row1,col2row1,col3row1\r\ncol1row2,col2row2,col3row2\r\n";
	// add more initialization here if needed ...
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_csv_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_csv_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}