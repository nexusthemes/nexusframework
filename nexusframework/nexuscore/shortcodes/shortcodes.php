<?php 

function nxs_nxspagerow($rowattributes, $content = null, $name='') 
{
	extract
	(
		shortcode_atts
		(
			array
			(
				"id" => '',
				"class" => ''
			)
			, 
			$rowattributes
		)
	);
		
	global $nxs_global_current_nxsposttype_being_rendered;
	global $nxs_global_current_postid_being_rendered;
	global $nxs_global_current_postmeta_being_rendered;
	global $nxs_global_current_rowindex_being_rendered;	
	global $nxs_global_current_render_mode;
	global $nxs_global_row_render_statebag;

	if ($nxs_global_current_nxsposttype_being_rendered == null)
	{
		echo "nxs_global_current_nxsposttype_being_rendered is NIET gezet";
		die();
	}

	if ($nxs_global_current_postid_being_rendered == null) { nxs_webmethod_return_nack("nxs_global_current_postid_being_rendered not set");}
	if ($nxs_global_current_render_mode == null) { nxs_webmethod_return_nack("nxs_global_current_render_mode not set"); }
	if ($nxs_global_current_postmeta_being_rendered === null) { nxs_webmethod_return_nack("nxs_global_current_postmeta_being_rendered  not set"); }	
	if ($nxs_global_current_rowindex_being_rendered == null) { nxs_webmethod_return_nack("nxs_global_current_rowindex_being_rendered  not set"); }	
	if ($nxs_global_row_render_statebag != null) { nxs_webmethod_return_nack("expected nxs_global_row_render_statebag to be null, but it isn't?"); }	

	$nxs_global_row_render_statebag = array();
	$nxs_global_row_render_statebag["pagerowtemplate"] = $rowattributes["pagerowtemplate"];
	$nxs_global_row_render_statebag["pagerowid"] = $rowattributes["pagerowid"];
	$nxs_global_row_render_statebag["rowindex"] = $nxs_global_current_rowindex_being_rendered;
	
	// render inner html
	$content = nxs_applyshortcodes($content);
	
	// note; the statebag could have been updated / populated by placeholders for outbound data / information
		
	extract($nxs_global_row_render_statebag, EXTR_PREFIX_ALL, "grs_");
	
	$additionalrowclasses = "";
	
	$upgradetofullwidth = $nxs_global_row_render_statebag["upgradetowidescreen"];	
	if ($upgradetofullwidth == "yes")
	{
		//var_dump($nxs_global_row_render_statebag);
		$additionalrowclasses .= " widescreen-row ";
		
	}
		
	/*
	$suppressfilleratrow0 = $nxs_global_row_render_statebag["suppressfilleratrow0"];
	$upgradetoexceptionalresponsiverow = $nxs_global_row_render_statebag["upgradetoexceptionalresponsiverow"];	
	$upgradetoexceptionalresponsiverow2 = $nxs_global_row_render_statebag["upgradetoexceptionalresponsiverow2"];	
	*/

	
	if (isset($nxs_global_row_render_statebag["rrs_cssclass"]))
	{
		$additionalrowclasses .= $nxs_global_row_render_statebag["rrs_cssclass"];
	}
	
	$pagerowtemplate = $rowattributes["pagerowtemplate"];
	
	
	if ($pagerowtemplate == "141214")
	{
		// promote this row to exceptional responsive row
		$grs_upgradetoexceptionalresponsiverow = "true";
	}
	else if (
		$pagerowtemplate == "121414" ||
		$pagerowtemplate == "141412"
	)
	{
		// promote this row to exceptional responsive row
		$grs_upgradetoexceptionalresponsiverow2 = "true";
	}
	else if 
	(
		$pagerowtemplate == "1third2third" || 
		$pagerowtemplate == "1212" || 
		$pagerowtemplate == "131313" || 
		$pagerowtemplate == "14141414" || 
		$pagerowtemplate == "one" || 
		$pagerowtemplate == "twothirdonethird")
	{
		// no upgrade to exceptional responsive row
	}
	else
	{
		echo "Unsupported pagerowtemplate; " . $pagerowtemplate;
		die();
	}
	
	if (isset($grs_upgradetoexceptionalresponsiverow) && $grs_upgradetoexceptionalresponsiverow == "true")
	{
		$additionalrowclasses .= "nxs-exceptional-responsive-row ";
	}
	if (isset($grs_upgradetoexceptionalresponsiverow2) && $grs_upgradetoexceptionalresponsiverow2 == "true")
	{
		$additionalrowclasses .= "nxs-exceptional-responsive-row2 ";
	}
	
	$output = "";
	$cssclass = "";

	if ($rowattributes["pagerowid"] == "")
	{
		// indien de pagerowid niet gezet is...
		$rowidattribute = "";
	}
	else
	{
		$pagerowid = $rowattributes["pagerowid"];
		$rowidattribute = "id='nxs-pagerow-{$pagerowid}' ";
		
		$metadata = nxs_getpagerowmetadata($nxs_global_current_postid_being_rendered, $pagerowid);
		$cssclass = nxs_getcssclassesforrow($metadata);
	}
	
	if (isset($grs_upgradetofullwidth) && $grs_upgradetofullwidth) 
	{
		$output .= "<div class='nxs-row {$cssclass}' {$rowidattribute}>";
		$output .= "<div class='nxs-row-container nxs-row2'>";
		$output .= "<div class='nxs-fullwidth nxs-containsimmediatehovermenu " . $additionalrowclasses . " '>";
	}
	else
	{
		$output .= "<div class='nxs-row {$cssclass} " . $additionalrowclasses . " ' {$rowidattribute}>";
		$output .= "<div class='nxs-row-container nxs-containsimmediatehovermenu nxs-row1'>";
	}
	
	if ($nxs_global_current_render_mode == "default")
	{
		if (nxs_has_adminpermissions()) 
		{
			if ($nxs_global_current_nxsposttype_being_rendered == "menu")
			{
				
			}
			else if ($nxs_global_current_nxsposttype_being_rendered == "slideset")
			{
				
			}
			else if ($nxs_global_current_nxsposttype_being_rendered == "list")
			{
				
			}
			else
			{
				$shouldrenderrowhover = false;
				
				if (nxs_cap_hasdesigncapabilities())
				{
					$shouldrenderrowhover = true;
				}
			
				if ($shouldrenderrowhover)
				{
					// pop up menu
					$output .= "<div class='nxs-hover-menu nxs-row-hover-menu nxs-admin-wrap outside-left-top'>";
					
					$output .= '<ul>';
	      	$output .= '<li>';
	      	
	      	$onclick = 'onclick="nxs_js_edit_row(this); return false;"';
	      	$title = nxs_l18n__("Click to configure this row", "nxs_td");
	      	if (!isset($nxs_global_row_render_statebag["pagerowid"]) || $nxs_global_row_render_statebag["pagerowid"] == "")
					{
						// downwards compatibility, to be removed eventually
						$onclick = "";
						$title = nxs_l18n__("This row is not configurable (#34568793875)", "nxs_td");
					}
					
	      	$output .= '<a href="#" ' . $onclick . ' title="' . $title . '">';
	      	$output .= '<span class="nxs-icon-arrow-right"></span>';
	        $output .= '</a>';
					
					//
					// submenu start
					//
					
					$output .= '<ul>';

					// move row
					$output .= "<li class='nxs-dragrow-handler' style='cursor:move;' title='" . nxs_l18n__("Move row", "nxs_td") ."'><span class='nxs-icon-move'></span></li>";
					
					// delete row					
					$output .= "<a class='nxs-no-event-bubbling nxs-defaultwidgetdeletehandler' href='#' onclick='nxs_js_row_remove(this); return false;'><li title='" . nxs_l18n__("Remove row[nxs:hovermenu,tooltip]", "nxs_td") ."'><span class='nxs-icon-trash'></span></li></a>";


					$output .= "</ul> <!-- nxs-sub-menu -->";
	
					//
					// submenu end
					//
	
	      	$output .= '</li>';      	
					
					$output .= '</ul> <!-- nxs-menu -->';
					
					$output .= "</div>";
				}
			}
		}
	}
	else if ($nxs_global_current_render_mode == "anonymous")
	{
		//
	}
	else
	{
		echo "nxs_global_current_render_mode (nog?) niet ondersteund:" . $nxs_global_current_render_mode;
		die();
	}
	
	$output .= "<ul class='nxs-placeholder-list'>";
	
	$output .= $content;
	$output .= "</ul>";
	$output .= "<div class='nxs-clear'></div>";

	if (isset($grs_upgradetofullwidth) && $grs_upgradetofullwidth) 
	{
		$output .= "</div> <!-- nxs-fullwidth -->";
		$output .= "</div> <!-- nxs-row-container -->";
		$output .= "</div> <!-- nxs-row -->";
	}
	else
	{
		$output .= "</div> <!-- nxs-row-container -->";		
		$output .= "</div> <!-- nxs-row -->";
	}

	// global variable no longer needed
	$nxs_global_row_render_statebag = null;
	
	return $output;
}
add_shortcode('nxspagerow', 'nxs_nxspagerow');

function nxs_nxsphcontainer($atts, $content = null, $name='') 
{
	extract(shortcode_atts(array(
		"id" => '',
		"class" => ''
	), $atts));
	
	global $nxs_global_row_render_statebag;
	if ($nxs_global_row_render_statebag == null)
	{
		echo "expected nxs_global_row_render_statebag to be set, but it isn't?";
		die();
	}
	$nxs_global_row_render_statebag["width"] = $atts["width"];
	
	// statebag for rendering this placeholder
	global $nxs_global_current_postid_being_rendered;
	global $nxs_global_current_postmeta_being_rendered;
	global $nxs_global_placeholder_render_statebag;
	global $nxs_global_current_render_mode;
		
	$nxs_global_placeholder_render_statebag = array();
	
	// perform actual render of the placeholder (delegates to widget)
	$content = nxs_applyshortcodes($content);

	extract($nxs_global_placeholder_render_statebag, EXTR_PREFIX_ALL, "gphs");	// underscore is added automatically
	
	$widgetmetadata = $nxs_global_placeholder_render_statebag["widgetmetadata"];
	
	// hover menu's
	$menutopleft = "";
	$menutopright = "";
	$menutypecontainer = "";
	
	$cropwidgetclass = "nxs-crop ";
	if (isset($gphs_widgetcropping) && $gphs_widgetcropping == "no")
	{
		// no cropping, this is needed, for example, in the slider, which exceeds the regular boundaries of the widget
		$cropwidgetclass = "";
	}
	
	$bottommarginclass = nxs_getcssclassesforlookup("nxs-margin-bottom-", $widgetmetadata["ph_margin_bottom"]);
	
	// ----------------------
	
	if ($nxs_global_current_render_mode == "default")
	{
		if (nxs_has_adminpermissions()) 
		{
			if (isset($gphs_placeholderrenderresult) && $gphs_placeholderrenderresult == "OK")
			{
				// er zijn geen fouten opgetreden bij het renderen van de widget
				
				$placeholdertemplate = $gphs_placeholdertemplate;
				$placeholdertitle = nxs_getplaceholdertitle($placeholdertemplate);
				
				if (isset($gphs_menutopleft) && $gphs_menutopleft != "")
				{
					$menutopleft .= "<div class='nxs-hover-menu-positioner'>";
					$menutopleft .= "<div class='nxs-hover-menu nxs-widget-hover-menu nxs-admin-wrap inside-left-top'>";
					$menutopleft .= $gphs_menutopleft;
					$menutopleft .= "</div>";
					$menutopleft .= "</div>";
				}
				else
				{
					// no top left menu is needed
					
				}
				
				if ($gphs_menutopright != "")
				{
					$menutopright .= "
					<div class='nxs-hover-menu-positioner'>
					<div class='nxs-hover-menu nxs-widget-hover-menu nxs-admin-wrap inside-right-top'>
					" . $gphs_menutopright . "
					</div>
					</div>
					";
				}
				else
				{
					// no top right menu is needed
				}
			}
			else
			{
				// an errror occured when rendering the widget,
				// if this is the case we allow the user to move the widget (as no specific logic is required)
				// and to delete the item

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
				
				$menutopright .= "
				<div class='nxs-hover-menu-positioner'>
				<div class='nxs-hover-menu nxs-widget-hover-menu nxs-admin-wrap inside-right-top'>
				<ul>

				<a href='http://nexusthemes.com/features-widgets/' target='_blank' title='" . nxs_l18n__("Documentation[nxs:hovermenu,tooltip]", "nxs_td") ."'>
				<li title='" . nxs_l18n__("Documentation[nxs:hovermenu,tooltip]", "nxs_td") ."' class='nxs-hovermenu-button'>
				<img src='" . get_bloginfo('template_url') . "/images/icon-brokenwidget.png' />
				</li>
				</a>
								
				<li title='" . nxs_l18n__("Move[nxs:hovermenu,tooltip]", "nxs_td") ."' class='cursor nxs-draggable nxs-existing-pageitem nxs-dragtype-placeholder' id='draggableplaceholderid_" . $gphs_placeholderid . "'>
				<div class='nxs-drag-helper' style='display: none;'>
				<div class='placeholder'>
				<img src='" . get_bloginfo('template_url') . "/images/icon-brokenwidget.png' />
				</div>
				</div>
				</li>

				<a class='nxs-no-event-bubbling' href='#' onclick='nxs_js_popup_placeholder_wipe(\"" . $nxs_global_current_postid_being_rendered . "\", \"" . $gphs_placeholderid . "\"); return false;'>
				<li title='" . nxs_l18n__("Remove widget[nxs:hovermenu,tooltip]", "nxs_td") ."'><span class='nxs-icon-trash'></span></li>
				</a>
				
				" . $debughtml . "
				
				</ul>
				</div>
				</div>";
			}
		}
		else
		{
			// no access
		}
	}
	else
	{
		// not needed
	}
	
	// ------------------------------------------ cursors
	
	if (nxs_has_adminpermissions())
	{
		// het 'hover' element; als de muis boven de placeholder hangt, zien we dit element
		$droplayerhtml = "<div class='nxs-runtime-autocellsize nxs-cursor nxs-drop-cursor'><span class='nxs-runtime-autocellsize'></span></div>";
		$cursorlayerhtml = "<div title='" . nxs_l18n__("Edit[nxs:hovermenu,tooltip]", "nxs_td") ."' class='nxs-runtime-autocellsize nxs-cursor nxs-cell-cursor'><span class='nxs-runtime-autocellsize'></span></div>";
	}
	else
	{
		$droplayerhtml = "";
		$cursorlayerhtml = "";
	}

	if ($nxs_global_current_render_mode == "default")
	{
		$placeholdercursors = $droplayerhtml . $cursorlayerhtml;
	}
	else if ($nxs_global_current_render_mode == "anonymous")
	{
		$placeholdercursors = "";
	}
	
	// ------------------------------------------
	
	$ph_colorzen = nxs_getcssclassesforlookup("nxs-colorzen-", $widgetmetadata["ph_colorzen"]);
	$ph_linkcolorvar = nxs_getcssclassesforlookup("nxs-linkcolorvar-", $widgetmetadata["ph_linkcolorvar"]);
	
	$ph_padding = nxs_getcssclassesforlookup("nxs-padding-", $widgetmetadata["ph_padding"]);
	$ph_valign = $widgetmetadata["ph_valign"];
	
	$ph_text_fontsize = nxs_getcssclassesforlookup("nxs-text-fontsize-", $widgetmetadata["ph_text_fontsize"]);
		
	$ph_border_radius = nxs_getcssclassesforlookup("nxs-border-radius-", $widgetmetadata["ph_border_radius"]);
	$ph_borderwidth = nxs_getcssclassesforlookup("nxs-border-width-", $widgetmetadata["ph_border_width"]);
	$ph_cssclass = $widgetmetadata["ph_cssclass"];
	
	// css classes that were added while rendering the widget at runtime
	$ph_runtimecssclass = $nxs_global_placeholder_render_statebag["ph_runtimecssclass"];

	// unistyle css classes	
	if (isset($widgetmetadata["unistyle"]) && $widgetmetadata["unistyle"] != "")
	{
		$ph_unistyleindicator_cssclass = "nxs-unistyled";
		$ph_unistyle_cssclass = "nxs-unistyle-" . nxs_stripspecialchars($widgetmetadata["unistyle"]);
	}
	else
	{
		$ph_unistyle_cssclass = "";
		$ph_unistyleindicator_cssclass = "nxs-not-unistyled";
	}
	
	// unicontent css classes	
	if (isset($widgetmetadata["unicontent"]) && $widgetmetadata["unicontent"] != "")
	{
		$ph_unicontentindicator_cssclass = "nxs-unicontented";
		$ph_unicontent_cssclass = "nxs-unicontent-" . nxs_stripspecialchars($widgetmetadata["unicontent"]);
	}
	else
	{
		$ph_unicontentindicator_cssclass = "nxs-not-unicontented";
		$ph_unicontent_cssclass = "";
	}

	// widgettype css classes	
	if (isset($widgetmetadata["type"]) && $widgetmetadata["type"] != "")
	{
		$ph_widgettype_cssclass = "nxs-widgettype-" . nxs_stripspecialchars($widgetmetadata["type"]);
	}
	else
	{
		$ph_widgettype_cssclass = "";
	}

	// clear the statebag for rendering this placeholder	
	$nxs_global_placeholder_render_statebag = null;

	$widthsupported = false;
	$widthclass = "";

	if ($atts["width"] == "1")
	{
		$widthsupported = true;
		$widthclass = "nxs-one-whole";
	}
	else if ($atts["width"] == "2/3")
	{
		$widthsupported = true;
		$widthclass = "nxs-two-third";		
	}
	else if ($atts["width"] == "1/2")
	{
		$widthsupported = true;
		$widthclass = "nxs-one-half";
	}
	else if ($atts["width"] == "1/3")
	{
		$widthsupported = true;
		$widthclass = "nxs-one-third";
	}	
	else if ($atts["width"] == "1/4")
	{
		$widthsupported = true;
		$widthclass = "nxs-one-fourth";
	}	
	else
	{
		$output = "<li>{$content} (BREEDTE (NOG?) NIET VOLLEDIG ONDERSTEUND)</li>";
	}
		
	if ($widthsupported)
	{
		$output = "";
		
		$concatenated_css = nxs_concatenateargswithspaces($widthclass, $bottommarginclass, $ph_cssclass, $ph_text_fontsize, $ph_unistyle_cssclass, $ph_unistyleindicator_cssclass, $ph_unicontent_cssclass, $ph_unicontentindicator_cssclass, $ph_widgettype_cssclass, $ph_runtimecssclass);
		
		$output .= "<li class='nxs-placeholder nxs-containshovermenu1 nxs-runtime-autocellsize " . $concatenated_css . "'>";
		$output .= $menutopleft;	// will be empty if not allowed, or not needed
		$output .= $menutopright;	// will be empty if not allowed, or not needed
		$output .= $placeholdercursors;	// will be empty if not allowed, or not needed
		
		$concatenated_css = nxs_concatenateargswithspaces($ph_colorzen, $ph_linkcolorvar, $ph_border_radius, $ph_borderwidth);
		$output .= '<div class="ABC nxs-height100 ' . $concatenated_css . ' ">';

		$concatenated_css = nxs_concatenateargswithspaces($ph_padding, $ph_valign);
		$output .= '<div class="XYZ ' . $concatenated_css . '">';
		
		$output .= "<div class='nxs-placeholder-content-wrap " . $cropwidgetclass . "'>";
		$output .= $content;
		$output .= "</div>";
		
		/*
		if (nxs_has_adminpermissions() && $_REQUEST["debugmeta"] == "true")
		{
			$output .= "<div class='nxs-debug-meta'>";
			$output .= "<ul>";
			foreach ($widgetmetadata as $currentkey => $currentval)
			{
				$output .= "<li>";
				$output .= nxs_render_html_escape_gtlt("$currentkey:$currentval");
				$output .= "</li>";
			}
			$output .= "</ul>";
			//$output .= $widgetmetadata;
			$output .= "</div>";	
		}
		*/

		$output .= "</div>";
		$output .= "</div>";
		
		$output .= "</li>";
	}
	
	return $output;
}
add_shortcode('nxsphcontainer', 'nxs_nxsphcontainer');

function nxs_nxsplaceholder($inlinepageattributes, $content = null, $name='') 
{
	extract(shortcode_atts(array(
		"id" => '',
		"class" => ''
	), $inlinepageattributes));
	
	//
	global $nxs_global_current_nxsposttype_being_rendered;
	global $nxs_global_current_postid_being_rendered;
	global $nxs_global_current_postmeta_being_rendered;
	global $nxs_global_current_rowindex_being_rendered;
	global $nxs_global_current_render_mode;	
	global $nxs_global_row_render_statebag;	
	global $nxs_global_placeholder_render_statebag;
	
	if ($nxs_global_current_nxsposttype_being_rendered == null)
	{
		echo "nxs_global_current_nxsposttype_being_rendered == null (2)";
	}
	
	if ($nxs_global_current_rowindex_being_rendered == null)
	{
		echo "nxs_global_current_rowindex_being_rendered == null";
	}
	
	if ($nxs_global_current_postid_being_rendered == null || $nxs_global_current_render_mode == null)
	{
		echo "nxs_global_current_postid_being_rendered ($nxs_global_current_postid_being_rendered) en/of nxs_global_current_render_mode ($nxs_global_current_render_mode) is NIET gezet (B)";
		die();
	}
	
	if ($nxs_global_current_postmeta_being_rendered === null)
	{
		echo "nxs_global_current_postmeta_being_rendered is NIET gezet b";
		//die();
	}
	
	if ($nxs_global_current_rowindex_being_rendered == null)
	{
		echo "nxs_global_current_rowindex_being_rendered is niet gezet (2)";
		die();
	}
	if ($nxs_global_row_render_statebag == null)
	{
		echo "expected nxs_global_row_render_statebag to be set, but it isn't?";
		die();
	}
	
	//
	$postid = $nxs_global_current_postid_being_rendered;	
	$placeholderid = $inlinepageattributes["placeholderid"];	
	if ($placeholderid == null || $placeholderid == '')
	{
		// incorrectly configured
		return "<div>incorrectly configured; placeholderid attribute not found on page $postid</div>";
	}
	$placeholdertemplate = nxs_getplaceholdertemplate($postid, $placeholderid);
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// blend unistyle properties
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "")
	{
		// blend unistyle properties with the metadata
		$unistyleprefix = nxs_getunifiedstylinggroup($placeholdertemplate);
		if (isset($unistyleprefix) && $unistyleprefix != "")
		{
			$unistyleproperties = nxs_unistyle_getunistyleproperties($unistyleprefix, $unistyle);
			$temp_array = array_merge($temp_array, $unistyleproperties);	
		}
		else
		{
			// strange; unistyle is set, but widget doesn't support unistyling?
		}
	}
	
	// store the widgetmetadata; its used in the phcontainer "later on"
	$nxs_global_placeholder_render_statebag["widgetmetadata"] = $temp_array;
	
	$mixedattributes = array_merge($inlinepageattributes, $temp_array);
	$mixedattributes["postid"] = $postid;
	$mixedattributes["rendermode"] = $nxs_global_current_render_mode;
	$mixedattributes["contenttype"] = "webpart";
	$mixedattributes["webparttemplate"] = "render_htmlvisualization";
	$mixedattributes["placeholderid"] = $placeholderid;
	$mixedattributes["placeholdertemplate"] = $placeholdertemplate;
	
	// prefetch metadata 
	$widgetmetadata = nxs_getwidgetmetadata($postid, $placeholderid);
	$mixedattributes["widgetmetadata"] = $widgetmetadata;
	
	//
	$placeholderrenderresult = nxs_getrenderedwidget($mixedattributes);
	
	$nxs_global_placeholder_render_statebag["placeholderrenderresult"] = $placeholderrenderresult["result"];	// bijv. "OK"
	$nxs_global_placeholder_render_statebag["placeholdertemplate"] = $placeholdertemplate;
	$nxs_global_placeholder_render_statebag["placeholderid"] = $placeholderid;
	
	if (nxs_has_adminpermissions())
	{
		// het 'hover' element; als de muis boven de placeholder hangt, zien we dit element
		$droplayerhtml = "<div class='nxs-runtime-autocellsize nxs-cursor nxs-drop-cursor'><span class='nxs-runtime-autocellsize'></span></div>";
		$cursorlayerhtml = "<div title='" . nxs_l18n__("Edit[nxs:hovermenu,tooltip]", "nxs_td") ."' class='nxs-runtime-autocellsize nxs-cursor nxs-cell-cursor'><span class='nxs-runtime-autocellsize'></span></div>";
	}
	else
	{
		$droplayerhtml = "";
		$cursorlayerhtml = "";
	}
	
	$widgetclass = "";
	if (isset($nxs_global_placeholder_render_statebag["widgetclass"]) && $nxs_global_placeholder_render_statebag["widgetclass"] != null)
	{
		$widgetclass = $nxs_global_placeholder_render_statebag["widgetclass"];
	}
	
	$healthclass = "";
	if ($nxs_global_placeholder_render_statebag["placeholderrenderresult"] != "OK")
	{
		// a problem occured (for example; widget not found)
		$healthclass = "nxs-render-error";
	}
	
	$inlinehtml = "";		
	$inlinehtml .= "<div id='nxs-widget-" . $placeholderid . "' class='nxs-widget nxs-widget-" . $placeholderid . " " . $healthclass . " " . $widgetclass . "'>";
	
	if ($placeholderrenderresult["result"] == "OK")
	{
		$inlinehtml .= $placeholderrenderresult["html"];
	}
	else
	{
		// output error message
		$inlinehtml .= nxs_getplaceholderwarning($placeholderrenderresult["message"] . " [" . $placeholdertemplate . "]");
	}
	
	$inlinehtml .= "</div>";
	
	if ($nxs_global_current_render_mode == "default")
	{
		$result = $inlinehtml;
	}
	else if ($nxs_global_current_render_mode == "anonymous")
	{
		$result = $inlinehtml;
	}
	else
	{
		echo "nxs_global_current_render_mode (nog?) niet ondersteund:" . $nxs_global_current_render_mode;
		die();
	}
	
	return $result;	
}
add_shortcode('nxsplaceholder', 'nxs_nxsplaceholder');

// Whitelist the TEDTalks oEmbed URL
wp_oembed_add_provider( 'http://www.ted.com/talks/*', 'http://www.ted.com/talks/oembed.json' );

// kudos to http://wordpress.stackexchange.com/questions/67740/ted-talks-shortcode-not-working
function nxs_ted_shortcode( $atts ) {
    // We need to use the WP_Embed class instance
    global $wp_embed;

    // The "id" parameter is required
    if ( empty($atts['id']) )
        return '';

    // Construct the TEDTalk URL
    $url = 'http://www.ted.com/talks/view/lang/eng/id/' . $atts['id'];

    // Run the URL through the  handler.
    // This handler handles calling the oEmbed class
    // and more importantly will also do the caching!
    return $wp_embed->shortcode( $atts, $url );
}
add_shortcode('ted', 'nxs_ted_shortcode');

function nxs_vimeo_shortcode( $atts ) 
{
	if (count($atts) == 1)
	{
		$videoid = $atts[0];
		if (nxs_stringstartswith($videoid, "http://vimeo.com/"))
		{
			$videoid = str_replace("http://vimeo.com/", "", $videoid);
		}
		$result = '<iframe class="nxs-inline-vimeo" src="http://player.vimeo.com/video/'.$videoid.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
	}
	else
	{
		$result = "(Unsupported vimeo)";
	}
  return $result;
}
add_shortcode('vimeo', 'nxs_vimeo_shortcode');

?>