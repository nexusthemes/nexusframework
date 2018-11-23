<?php

function nxs_frontendframework_sitemap_renderplaceholderwarning($message)
{
	if (nxs_has_adminpermissions())
	{
		?>
		<div class="empty nxs-border-dash nxs-admin-wrap nxs-hidewheneditorinactive autosize-smaller">
			<div class='placeholder-warning'>
				<p><?php echo $message; ?></p>
			</div>
		</div>
		<?php
	}
	else
	{
		?>
		<!-- warning detected; please sign in to see the warning -->
		<?php
	}
}

function nxs_frontendframework_sitemap_gethtmlforbutton($args)
{
	extract($args);
	
	$render_errors = array();
	
	if ($_REQUEST["check"] == "atts")
	{
		foreach ($args as $k=>$v)
		{
			if ($k == "color")
			{
				echo "o no (aa); deprecated key found in shortcode?!:";
				echo $k . " / " . $v;
				
				echo "<br />";
				
			}
			else if (nxs_stringstartswith($k, "button_"))
			{
				echo "o no deprecated key found in shortcode?!: "; 
				echo $k . " / " . $v;
				echo "<br />";
				//die();
			}
			else
			{
				echo "OK:" . $k . " / " . $v . "<br />";;
			}
		}
	}
	
	if ($visible == "false")
	{
		return "";
	}
	
	$text = do_shortcode($text);
	
	if ($text == "")
	{
		return "";
	}
	if ($destination_articleid == "" && $destination_popuparticleid == "" && $destination_url == "" && $destination_js == "")
	{
		return "";
	}		

	$align = nxs_getcssclassesforlookup("nxs-align-", $align);
	$colorzen = nxs_getcssclassesforlookup("nxs-colorzen-", $colorzen);
	$scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $scale);
	$margin_cssclass = nxs_getcssclassesforlookup("nxs-margin", $margin);
	$border_radius_cssclass = nxs_getcssclassesforlookup("nxs-border-radius-", $border_radius);
	$fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen-", $fontzen);
	
	if ($destination_target == "_nxspopup")
 	{
 		if ($destination_popuparticleid == "")
 		{
 			$destination_popuparticleid = $destination_articleid;
 			$destination_articleid = "";
 		}
 		else
 		{
 			// add error to the list render_errors
 		}
 	}
	
	if ($destination_articleid != "")
	{
		$posttype = get_post_type($destination_articleid);
		if ($posttype == "attachment")
		{
			$url = wp_get_attachment_url($destination_articleid);
		}
		else
		{
			$url = nxs_geturl_for_postid($destination_articleid);
		}
		$onclick = "";
	}
	else if ($destination_popuparticleid != "")
	{
		$domid = "nxs_popups_template_{$destination_popuparticleid}";
		nxs_popup_renderpopuptemplate($destination_popuparticleid, $domid);
		$url = "#";
		$destination_js = "nxs_js_popup_setsessioncontext('currentpopuptemplateid', '{$domid}'); nxs_js_popup_render_inner(-1, {'result':'OK','rendertemplateid':'{$domid}'});return false;";
		$onclick = "onclick='" . nxs_render_html_escape_singlequote($destination_js) . "' ";
	} 
	else if ($destination_url != "")
	{
		if (nxs_stringstartswith($destination_url, "tel:"))
		{
			// a phone link; if parenthesis or spaces are used; absorb them
			$url = $destination_url;
			$url = str_replace(" ", "", $url);
			$url = str_replace("(", "", $url);
			$url = str_replace(")", "", $url);
		}
		else
		{
			// regular link
			$url = $destination_url;
		}
		$onclick = "";
	}
	else if ($destination_js != "")
	{
		$url = "#";
		$onclick = "onclick='" . nxs_render_html_escape_singlequote($destination_js) . "' ";
	}
	else
	{
		// unsupported
		$url = "nxsunsupporteddestination";
		$onclick = "";
	}
	
	if ($onclick != "")
	{
		$onclick = " " . $onclick . " ";
 	}
 
 	if ($destination_target == "@@@empty@@@" || $destination_target == "")
 	{
 		// auto
 		if ($destination_articleid != "")
 		{
 			// local link = self
 			$destination_target = "_self";
 		}
 		else
 		{
 			$homeurl = nxs_geturl_home();
 			if (nxs_stringstartswith($url, $homeurl))
 			{
 				$destination_target = "_self";
 			}
 			else
 			{
 				$destination_target = "_blank";
 			}
 		}
 	}
 	if ($destination_target == "_self")
 	{
 		$destination_target = "_self";
 	}
 	else if ($destination_target == "_blank")
 	{
 		$destination_target = "_blank";
 	}
 	else if ($destination_target == "_nxspopup")
 	{
 		if ($destination_popuparticleid == "")
 		{
 			$destination_popuparticleid = $destination_articleid;
 			$destination_articleid = "";
 		}
 		else
 		{
 			// add error to the list render_errors
 		}
 	}
 	else
 	{
 		$destination_target = "_self";
	}

	$destination_relation_html = '';
	if ($destination_relation == "nofollow") {
		$destination_relation_html = 'rel="nofollow"';
	}
	
	$title_att = "";
	if ($title != "")
	{
		$title_att = "title='" . esc_html($title) . "'";
	}
	
	$result = '';
	$result .= '<p class="' . $align . ' nxs-padding-bottom0">';
	$result .= '<a '.$title_att.' target="' . $destination_target . '" ' . $destination_relation_html . ' ' . $onclick . ' class="nxs-button ' . $scale_cssclass . ' ' . $border_radius_cssclass . ' ' . $margin_cssclass . ' ' . $colorzen . ' ' . $fontzen_cssclass . '" href="' . $url . '">' . $text . '</a>';
	$result .= '</p>';
	
	return $result;
}

function nxs_frontendframework_sitemap_gethtmlfortitle($args)
{
	extract($args);
	
	if ($_REQUEST["check"] == "atts")
	{
		foreach ($args as $k=>$v)
		{
			if ($k == "color")
			{
				echo "o no deprecated key found in shortcode?!"; 
				echo $k . " / " . $v;
			}
			else if (nxs_stringstartswith($k, "text_"))
			{
				echo "o no deprecated key found in shortcode?!"; 
				echo $k . " / " . $v;
				//die();
			}
		}
	}
	
	if ($title == "")
	{
		return "";
	}
	
	if ($destination_target == "_self") {
		$destination_target_html = 'target="_self"';
	} else if ($destination_target == "_blank") {
		$destination_target_html = 'target="_blank"';
	} else {
		if ($destination_articleid != "") {
			$destination_target_html = 'target="_self"';
		} else {
			$homeurl = nxs_geturl_home();
 			if (nxs_stringstartswith($destination_url, $homeurl)) {
 				$destination_target_html = 'target="_self"';
 			} else {
 				$destination_target_html = 'target="_blank"';
 			}
		}
	}

	$destination_relation_html = '';
	if ($destination_relation == "nofollow") {
		$destination_relation_html = 'rel="nofollow"';
	}
	
	// Title alignment
	$alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $align);
	$fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $fontsize);
	
	$margin_cssclass = nxs_getcssclassesforlookup("nxs-margin", $margin);
	$margin_bottom_cssclass = nxs_getcssclassesforlookup("nxs-margin-bottom", $margin_bottom);
	
	$heading = str_replace("h", "", $heading);
	
	// Title importance (H1 - H6)
	if ($heading != "")
	{
		$headingelement = "h" . $heading;

	}
	else
	{
		// TODO: derive the importance based on the fontsize
		//nxs_webmethod_return_nack("to be implemented; derive heading from fontsize");
		$headingelement = "h1";
	}
	
	if ($fontzen != "")
	{
		$fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen nxs-fontzen-", $fontzen);
	}
	

	if ($colorzen != "")
	{
		$colorzen_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $colorzen);
	}

	$cssclasses = nxs_concatenateargswithspaces("nxs-title", $alignment_cssclass, $fontsize_cssclass, $margin_cssclass, $margin_bottom_cssclass, $fontzen_cssclass, $colorzen_cssclass);
	if ($heightiq != "")
	{
		$heightiqprio = "p1";
		$heightiqgroup = "title";
		$cssclasses = nxs_concatenateargswithspaces($cssclasses, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$heightiqgroup}");
	}
	
	if ($microdata != "")
	{
		$itemprop = "itemprop='name'";
	}
	else
	{
		$itemprop = "";
	}
	
	if ($content_justify == "start")
	{
		$styles .= "display: flex; flex-direction: row; justify-content: flex-start;";
	}
	else if ($content_justify == "center")
	{
		$styles .= "display: flex; flex-direction: row; justify-content: center;";
	}
	else if ($content_justify == "end")
	{
		$styles .= "display: flex; flex-direction: row; justify-content: flex-end;";
	}
	
	$result = '<' . $headingelement . ' ' . $itemprop . ' class="' . $cssclasses . '" style="'.$styles.'">' . $title . '</' . $headingelement . '>';
	
	// link
	if ($destination_articleid != "") 
	{
		$destination_url = nxs_geturl_for_postid($destination_articleid);
	}
	
	if (nxs_stringstartswith($destination_url, "tel:"))
	{
		// a phone link; if parenthesis or spaces are used; absorb them
		$destination_url = str_replace(" ", "", $destination_url);
		$destination_url = str_replace("(", "", $destination_url);
		$destination_url = str_replace(")", "", $destination_url);
	}
	
	if ($destination_url != "") 
	{
		$result = '<a href="' . $destination_url .'" '.$destination_target_html.' '.$destination_relation_html.'>' . $result . '</a>';
		
		if ($shouldapplylinkvarcolor == true)
		{
			// this is needed for http://www.sylviedeloge.fr/identite-visuelle/ (link color specified on the widget)
			$result = "<span class='nxs-applylinkvarcolor'>{$result}</span>";
		}
	}
	
	return $result;
}

//
// framework css
//
function nxs_frontendframework_sitemap_theme_styles()
{
	// Register the style like this for a theme:  
  // (First the unique name for the style (custom-style) then the src, 
  // then dependencies and ver no. and media type)
  
  wp_register_style('nxs-framework-style-css-reset', 
    nxs_getframeworkurl() . '/css/css-reset.css', 
    array(), 
    nxs_getthemeversion(),    
    'all' );
  
  wp_register_style('nxs-framework-style', 
    nxs_getframeworkurl() . '/css/framework.css', 
    array(), 
    nxs_getthemeversion(), 
    'all' );


  if (is_child_theme()) 
  {
  	wp_register_style('nxs-framework-style-child', 
    nxs_getframeworkurl() . '/css/style.css', 
    array(), 
    nxs_getthemeversion(), 
    'all' );

  	// enqueing:
    wp_enqueue_style('nxs-framework-style-child');
	}
  
	// enqueing:
	
	// indien we in de WP backend zitten, dan geen css reset!
	$iswordpressbackendshowing = is_admin();
	if (!$iswordpressbackendshowing)
	{
		wp_enqueue_style('nxs-framework-style-css-reset');
	}
	
  wp_enqueue_style('nxs-framework-style');
    
	if (!$iswordpressbackendshowing)
	{
		$sitemeta = nxs_getsitemeta();  

		wp_register_style('nxs-framework-style-responsive', 
	    nxs_getframeworkurl() . '/css/framework-responsive.css', 
	    array(), 
	    nxs_getthemeversion(),
	    'all' );
	    
	    wp_enqueue_style('nxs-framework-style-responsive');
	}
	
	wp_enqueue_script( 'jquery-migrate', nxs_getframeworkurl() . '/js/migrate/jquery-migrate.js', array( 'jquery' ), nxs_getthemeversion(), TRUE );
	
  do_action('nxs_action_after_enqueue_baseframeworkstyles');
}

function nxs_frontendframework_sitemap_clearunwantedscripts()
{
	// if we are in the frontend ...
	if (!is_admin())
	{
		// the theme could break if pointing to an incompatible version
		// therefore we remove jquery scripts added by third party plugins, such as NGG
  	//wp_deregister_script('jquery');
  	
  	
  	// 25 aug 2014; removed; woocommerce adds various scripts that are dependent upon
  	// jquery, and we ignore those too when using the approach below...
  	function nxs_modify_scripts() 
  	{
  		wp_deregister_script('jquery');
			wp_deregister_script('jquery-ui');
			$dependencies = false;
      wp_register_script('jquery', "//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js", $dependencies);
      wp_enqueue_script('jquery');
      
      wp_enqueue_script('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js', array('jquery'), '1.11.1');
		}
		add_action('wp_print_scripts', 'nxs_modify_scripts', 100);
		add_action('wp_head','nxs_setjQ_nxs');
  }
  else
  {
  	add_action('admin_head','nxs_setjQ_nxs');
  }
}

function nxs_frontendframework_sitemap_init()
{
	add_action('wp_enqueue_scripts', 'nxs_frontendframework_sitemap_theme_styles');
	add_action('admin_enqueue_scripts', 'nxs_frontendframework_sitemap_theme_styles');
	add_action('nxs_render_frontendeditor', 'nxs_frontendframework_sitemap_render_frontendeditor');
	add_shortcode("nxspagerow", 'nxs_frontendframework_sitemap_sc_nxspagerow');
	add_shortcode('nxsphcontainer', 'nxs_frontendframework_sitemap_sc_nxsphcontainer');
	add_shortcode('nxsplaceholder', 'nxs_frontendframework_sitemap_sc_nxsplaceholder');
	add_shortcode('nxs_wrap', 'nxs_frontendframework_sitemap_sc_wrap');
	add_shortcode('nxs_image', 'nxs_frontendframework_sitemap_sc_image');

	nxs_frontendframework_sitemap_clearunwantedscripts();
}

function nxs_frontendframework_sitemap_render_frontendeditor()
{
	if (!is_admin())
	{
		require_once("nxsmenu.php");
	}
	
	require_once("frontendediting.php");
	
	/*
	if ($_REQUEST["huh"] == "007")
	{
		echo "JA!";
		die();
	}
	*/
}

//
//
//

// layout specific shortcodes

function nxs_frontendframework_sitemap_sc_nxspagerow($rowattributes, $content = null, $name='') 
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
		nxs_webmethod_return_nack("nxs_global_current_nxsposttype_being_rendered is NIET gezet");
	}

	if ($nxs_global_current_postid_being_rendered == null) { nxs_webmethod_return_nack("nxs_global_current_postid_being_rendered not set");}
	if ($nxs_global_current_render_mode == null) { nxs_webmethod_return_nack("nxs_global_current_render_mode not set"); }
	if ($nxs_global_current_postmeta_being_rendered === null) { nxs_webmethod_return_nack("nxs_global_current_postmeta_being_rendered  not set"); }	
	if ($nxs_global_current_rowindex_being_rendered == null) { nxs_webmethod_return_nack("nxs_global_current_rowindex_being_rendered  not set"); }	
	if ($nxs_global_row_render_statebag != null) { nxs_webmethod_return_nack("expected nxs_global_row_render_statebag to be null, but it isn't?;" . json_encode($nxs_global_row_render_statebag)); }	

	$nxs_global_row_render_statebag = array();
	$nxs_global_row_render_statebag["pagerowtemplate"] = $rowattributes["pagerowtemplate"];
	$nxs_global_row_render_statebag["pagerowid"] = $rowattributes["pagerowid"];
	$nxs_global_row_render_statebag["rowindex"] = $nxs_global_current_rowindex_being_rendered;
	
	// render inner html
	$content = nxs_applyshortcodes($content);
	
	// note; the statebag could have been updated / populated by placeholders for outbound data / information
		
	extract($nxs_global_row_render_statebag, EXTR_PREFIX_ALL, "grs_");
	
	$pagerowtemplate = $rowattributes["pagerowtemplate"];
	$hidewheneditorinactive = $nxs_global_row_render_statebag["hidewheneditorinactive"];

	$additionalrowclasses = "";
	
	$upgradetofullwidth = $nxs_global_row_render_statebag["upgradetowidescreen"];	
	
	if (isset($nxs_global_row_render_statebag["rrs_cssclass"]))
	{
		$additionalrowclasses .= $nxs_global_row_render_statebag["rrs_cssclass"];
	}
	
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
		// echo "Unsupported pagerowtemplate; [$pagerowtemplate]";
		$pagerowtemplate = "one";
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
		
		$mixedattributes = array();
		$mixedattributes = array_merge($mixedattributes, nxs_getpagerowmetadata($nxs_global_current_postid_being_rendered, $pagerowid));
		
		//
		$combined_lookups = nxs_lookups_getcombinedlookups_for_currenturl();
		$combined_lookups = array_merge($combined_lookups, nxs_parse_keyvalues($mixedattributes["r_lookups"]));

		$combined_lookups = nxs_lookups_evaluate_linebyline($combined_lookups);
		
		// replace values in mixedattributes with the lookup dictionary
		$magicfields = array("r_enabled");
		$translateargs = array
		(
			"lookup" => $combined_lookups,
			"items" => $mixedattributes,
			"fields" => $magicfields,
		);
		$mixedattributes = nxs_filter_translate_v2($translateargs);		
		
		$cssclass = nxs_getcssclassesforrow($mixedattributes);
		
		$should_render_row = true;
		$r_enabled = strtolower(trim($mixedattributes["r_enabled"]));
		if ($r_enabled == "")
		{
			// its enabled in all its glory :) (default)
			$should_render_row = true;
		}
		else if ($r_enabled == "true")
		{
			// its enabled after evaluation
			$should_render_row = true;
			$cssclass .= " nxs-row-enabled-true"; 
		}
		else
		{
			$cssclass .= " nxs-row-enabled-false"; 
			
			if (is_user_logged_in())
			{
				if (nxs_cap_hasdesigncapabilities())
				{
					// do show it (otherwise we wont be able to edit it)
					// but tag it so we can visualize it in a spacial way
					$should_render_row = true;
					$cssclass .= " nxs-hidewheneditorinactive ";
				}
				else
				{
					$should_render_row = false;
				}
			}
			else
			{
				$should_render_row = false;
			}
		}
		
		if ($mixedattributes["r_widescreen"] != "")
		{
			$upgradetofullwidth = "yes";
		}
	}
	
	if ($upgradetofullwidth == "yes")
	{
		if ($pagerowtemplate == "one")
		{
			$additionalrowclasses .= " widescreen-row ";
		}
		else
		{
			// not allowed
		}
	}
	
	$cssclassrowtemplate = "nxs-rowtemplate-" . $nxs_global_row_render_statebag["pagerowtemplate"];
	
	if ($hidewheneditorinactive === true)
	{
		$cssclass .= " nxs-hidewheneditorinactive ";
	}
	
	if (isset($grs_upgradetofullwidth) && $grs_upgradetofullwidth) 
	{
		
		$output .= "<div class='nxs-row {$cssclass} {$cssclassrowtemplate}' {$rowidattribute}>";
		$output .= "<div class='nxs-row-container nxs-row2'>";
		$output .= "<div class='nxs-fullwidth nxs-containsimmediatehovermenu " . $additionalrowclasses . " '>";
	}
	else
	{
		$output .= "<div class='nxs-row {$cssclass} {$cssclassrowtemplate} " . $additionalrowclasses . " ' {$rowidattribute}>";
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
			else if ($nxs_global_current_nxsposttype_being_rendered == "genericlist")
			{
				
			}
			else if ($nxs_global_current_nxsposttype_being_rendered == "busrulesset")
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

	      	if ($r_enabled != "")
	      	{
	      		
	      		
	      		
	      		$circle_color = "#DFDFDF";
	      		$text_color = "#000000";
	      		if ($r_enabled == "true")
	      		{
	      			$circle_color = "#00EE00";
	      			$text_color = "#FFFFFF";
	      		}
	      		else
	      		{
	      			$circle_color = "#FF0000";
	      			$text_color = "#FFFFFF";
	      		}
	      		
	      		$notificationargs = array
	      		(
	      			"link_growl" => "This indicates the row is enabled or disabled based upon a condition",
	      			"circle_color" => $circle_color,
	      			"text_color" => $text_color,
	      			"text" => "C",
	      		);
	      		$notificationhtml = nxs_gethtmlfornotification($notificationargs);
	      		
	      		$output .= $notificationhtml;
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
		nxs_webmethod_return_nack("nxs_global_current_render_mode (nog?) niet ondersteund: {$nxs_global_current_render_mode}");
	}
	
	$output .= "<ul class='nxs-placeholder-list'>";
	$output .= $content;
	$output .= "</ul>";
	$output .= "<div class='nxs-clear'></div>";

	if (isset($grs_upgradetofullwidth) && $grs_upgradetofullwidth) 
	{
		$output .= "</div> <!-- nxs-fullwidth -->";
		$output .= "</div> <!-- nxs-row-container -->";
		$output .= "</div>";
	}
	else
	{
		$output .= "</div> <!-- nxs-row-container -->";		
		$output .= "</div>";
	}

	// widgets have the capability to tell the row to etch itself
	// (for example entities widgets)
	if ($nxs_global_row_render_statebag["etchrow"] === true)
	{
		if (!is_user_logged_in())
		{
			$output = ""; // "<!-- and its gone -->";
		}
	}

	// global variable no longer needed
	$nxs_global_row_render_statebag = null;
	
	if ($pagerowtemplate == "")
	{
		//
		$output = "";
	}
	if ($should_render_row === false)
	{
		$output = "";
	}
	
	return $output;
}

function nxs_frontendframework_sitemap_sc_nxsphcontainer($atts, $content = null, $name='') 
{
	extract(shortcode_atts(array(
		"id" => '',
		"class" => ''
	), $atts));
	
	global $nxs_global_row_render_statebag;
	if ($nxs_global_row_render_statebag == null)
	{
		nxs_webmethod_return_nack("expected nxs_global_row_render_statebag to be set, but it isn't?");
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
	
	$phdataattributeshtml = "";
	$data_atts = $nxs_global_placeholder_render_statebag["data_atts"];
	if (isset($data_atts))
	{
		foreach ($data_atts as $key => $val)
		{
			$phdataattributeshtml .= "data-{$key}='{$val}' ";
		}
	}
	
	
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
					nxs_ob_start();
					?>
					<a class='nxs-no-event-bubbling' href='#' onclick="nxs_js_edit_widget_v2(this, 'debug'); return false; return false;">
	         	<li title='<?php nxs_l18n_e("Debug[tooltip]", "nxs_td"); ?>'>
	         		<span class='nxs-icon-search'></span>
	         	</li>
	      	</a>
	      	<?php
	      	$debughtml = nxs_ob_get_contents();
					nxs_ob_end_clean();
				}
				else
				{
					$debughtml = "";
				}
				
				$menutopright .= "
				<div class='nxs-hover-menu-positioner'>
				<div class='nxs-hover-menu nxs-widget-hover-menu nxs-admin-wrap inside-right-top'>
				<ul>

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
		
		$output .= "<li class='nxs-placeholder nxs-containshovermenu1 nxs-runtime-autocellsize " . $concatenated_css . "' {$phdataattributeshtml}>";
		$output .= $menutopleft;	// will be empty if not allowed, or not needed
		$output .= $menutopright;	// will be empty if not allowed, or not needed
		$output .= $placeholdercursors;	// will be empty if not allowed, or not needed
		
		$concatenated_css = nxs_concatenateargswithspaces($ph_colorzen, $ph_linkcolorvar, $ph_border_radius, $ph_borderwidth);
		
		$heightclass = "";
		if ($widgetmetadata["ph_valign"] == "nxs-valign-top" || $widgetmetadata["ph_valign"] == "")
		{
			$heightclass = "nxs-height100";
		}
		
		$output .= "<div class='ABC $heightclass $concatenated_css'>";

		$concatenated_css = nxs_concatenateargswithspaces($ph_padding, $ph_valign);
		$output .= '<div class="XYZ ' . $concatenated_css . '">';
		
		$output .= "<div class='nxs-placeholder-content-wrap " . $cropwidgetclass . "'>";
		$output .= $content;
		$output .= "</div>";
		
		$output .= "</div>";
		$output .= "</div>";
		
		$output .= "</li>";
	}
	
	return $output;
}

function nxs_frontendframework_sitemap_sc_nxsplaceholder($inlinepageattributes, $content = null, $name='') 
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
		nxs_webmethod_return_nack("nxs_global_current_postid_being_rendered ($nxs_global_current_postid_being_rendered) en/of nxs_global_current_render_mode ($nxs_global_current_render_mode) is NIET gezet (B)");
	}
	
	if ($nxs_global_current_postmeta_being_rendered === null)
	{
		echo "nxs_global_current_postmeta_being_rendered is NIET gezet b";
	}
	
	if ($nxs_global_current_rowindex_being_rendered == null)
	{
		nxs_webmethod_return_nack("nxs_global_current_rowindex_being_rendered is niet gezet (2)");
	}
	if ($nxs_global_row_render_statebag == null)
	{
		nxs_webmethod_return_nack("expected nxs_global_row_render_statebag to be set, but it isn't?");
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
		nxs_webmethod_return_nack("nxs_global_current_render_mode (nog?) niet ondersteund:" . $nxs_global_current_render_mode);
	}
	
	return $result;	
}

function nxs_frontendframework_sitemap_sc_wrap($atts, $content = null, $name='') 
{
	$unwrapped_content = do_shortcode($content);
	
	$padding_cssclass = nxs_getcssclassesforlookup("nxs-padding-", $atts["padding"]);
	$margin_cssclass = nxs_getcssclassesforlookup("nxs-margin-", $atts["margin"]);
	$colorzen_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $atts["colorzen"]);
	$border_radius_cssclass = nxs_getcssclassesforlookup("nxs-border-radius-", $atts["border_radius"]);
	$class = $atts["class"];
	
	$cssclasses = nxs_concatenateargswithspaces($class, $border_radius_cssclass, $margin_cssclass, $padding_cssclass, $colorzen_cssclass);
	
	$result = "<div class='{$cssclasses}'>{$unwrapped_content}</div>";
	return $result;
}

function nxs_frontendframework_sitemap_setgenericwidgethovermenu($args)
{
	// defaults
	$enable_decoratewidget = false;
	$defaultwidgetclickhandler = "edit";
	
	// if (support
	
	$enable_editwidget = true;
	$enable_movewidget = true;
	$enable_deletewidget = true;
	$enable_deleterow = false;
	$enable_debugmeta = nxs_shoulddebugmeta();
	$enable_addentity = false;
	
	extract($args);
	
	if (!isset($postid)) { nxs_webmethod_return_nack("postid not set (nxs_widgets_setgenericwidgethovermenu_v2);" . nxs_geturlcurrentpage()); }
	if (!isset($placeholderid)) { nxs_webmethod_return_nack("placeholderid not set"); }
	if (!isset($placeholdertemplate)) { nxs_webmethod_return_nack("placeholdertemplate not set"); }
	
	//
	// 
	//

	// check permission
	if (nxs_cap_hasdesigncapabilities())
	{
		// ok
	}
	else
	{
		$enable_movewidget = false;
		$enable_deletewidget = false;
		$enable_deleterow = false;
		$lockedwidget = true;
	}
	
 	$widgeticonid = nxs_getwidgeticonid($placeholdertemplate);
 	
	// Turn on output buffering
	nxs_ob_start();
	// --------------------------------------------------------------------------------------------------
	$islocked = false;
	
	$metadata = $args["metadata"];
	if (isset($metadata))
	{
		if ($metadata["lock"] == "locked")
		{
			$islocked = true;
		}
	}
	
 	if (!$islocked)
 	{
		?>
	  <ul class="">
	  	<?php
	  	if ($enable_movewidget === "first")
	  	{
	  		?>
		    <li title='<?php nxs_l18n_e("Move[tooltip]", "nxs_td"); ?>' class='nxs-draggable nxs-existing-pageitem nxs-dragtype-placeholder' id='draggableplaceholderid_<?php echo $placeholderid; ?>'>
	      	<span class='nxs-icon-move'></span>
	        <div class="nxs-drag-helper" style='display: none;'>
	          <div class='placeholder'>
	          	<span class='<?php echo $widgeticonid; ?>'></span>
	          </div>
	        </div>
	        <!-- li is closed further on -->
		    <?php
	  	}
	  	else if ($enable_editwidget === true)
	  	{
		  	?>
		    <li title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>' class='nxs-hovermenu-button'>
		  		<a href='#' title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>' <?php if ($defaultwidgetclickhandler=='edit') { echo 'class="nxs-defaultwidgetclickhandler"'; } ?> onclick="nxs_js_edit_widget(this); return false;">
		      	<span class='<?php echo $widgeticonid; ?>'></span>
		      </a>
		      <!-- li is closed further on -->
		    <?php
	  	}
	  	else
	  	{
	  		nxs_webmethod_return_nack("unsupported first widget menu item");
	  	}
	    ?>
	      <ul class="">
	      	<?php
	      	if ($enable_addentity === true)
	      	{
	      		// adding entities should be done in the modeleditor,
	      		// not in wp
	      	}
	      	?>
	      	<?php
	      	if ($enable_editwidget === "second")
	      	{
	      		?>
				    <li title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>' class='nxs-hovermenu-button'>
				  		<a href='#' title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>' <?php if ($defaultwidgetclickhandler=='edit') { echo 'class="nxs-defaultwidgetclickhandler"'; } ?> onclick="nxs_js_edit_widget(this); return false;">
				      	<span class='<?php echo $widgeticonid; ?>'></span>
				      </a>
						</li>	      		
	      		<?php
	      	}
	      	?>
      		<?php 
      		if ($enable_decoratewidget === true)
      		{
      			echo nxs_render_widgetbackgroundstyler($placeholdertemplate); 
      		}
      		?>
      		<?php
      		if ($enable_movewidget === true)
      		{
      			$widgeticonid = nxs_getwidgeticonid($placeholdertemplate);
      			?>
		        <li title='<?php nxs_l18n_e("Move[tooltip]", "nxs_td"); ?>' class='nxs-draggable nxs-existing-pageitem nxs-dragtype-placeholder' id='draggableplaceholderid_<?php echo $placeholderid; ?>'>
		        	<span class='nxs-icon-move'></span>
	            <div class="nxs-drag-helper" style='display: none;'>
                <div class='placeholder'>
                	<span class='<?php echo $widgeticonid; ?>'></span>
                </div>
	            </div>					
		        </li>
		       	<?php
		      }
		      ?>
		      <?php
      		if ($enable_deletewidget === true)
      		{
      			?>
	        	<a class='nxs-no-event-bubbling' href='#' onclick='nxs_js_popup_placeholder_wipe("<?php echo $postid; ?>", "<?php echo $placeholderid; ?>"); return false;'>
	           	<li title='<?php nxs_l18n_e("Delete[tooltip]", "nxs_td"); ?>'>
	           		<span class='nxs-icon-trash'></span>
	           	</li>
	        	</a>		
	        	<?php
	        }
	        ?>
	        <?php
      		if ($enable_deleterow === true)
      		{
      			?>
	        	<a class='nxs-no-event-bubbling nxs-defaultwidgetdeletehandler' href='#' onclick='nxs_js_row_remove(this); return false;'>
	           	<li title='<?php nxs_l18n_e("Delete[tooltip]", "nxs_td"); ?>'><span class='nxs-icon-trash'></span></li>
	        	</a>		
	        	<?php
	        }
	        ?>
	        <?php
	        if ($enable_debugmeta === true)
	        {
	        	?>
	         	<li title='<?php nxs_l18n_e("Debug[tooltip]", "nxs_td"); ?>'>
	  	      	<a class='nxs-no-event-bubbling' href='#' onclick="nxs_js_edit_widget_v2(this, 'debug'); return false; return false;">
		          		<span class='nxs-icon-search'></span>
		        	</a>	
	         	</li>    		
	        	<?php
	        }
	        ?>
		    </ul>	
	  	</li>
		</ul>
		<?php
	}
	else
	{
		if (nxs_cap_hasdesigncapabilities())
		{
			?>
		  <ul class="">
			 	<li title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>' class='nxs-hovermenu-button'>
		  		<a href='#' title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>'  class="nxs-defaultwidgetclickhandler" onclick="nxs_js_edit_widget_v2(this, 'unlock'); return false;">
		      	<span class='<?php echo $widgeticonid; ?>'></span>
		      </a>
	    	</li>
	  		<li>
		  		<a href='#' title='<?php nxs_l18n_e("Unlock", "nxs_td"); ?>' onclick="nxs_js_edit_widget_v2(this, 'unlock'); return false;">
		      	<span class='nxs-icon-unlocked'></span>
		      </a>
		  	</li>
			</ul>
			<?php
		}
		else
		{
			// hide all icons
			?>
			<ul class="">
			 	<li title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>' class='nxs-hovermenu-button'>
		  		<a href='#' title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>'  class="nxs-defaultwidgetclickhandler" onclick="nxs_js_alert('<?php nxs_l18n_e("This item is locked, only a webdesigner can modify it", "nxs_td"); ?>');">
		      	<span class='<?php echo $widgeticonid; ?>'></span>
		      </a>
	    	</li>
		  	<li>
		  		<a href='#' title='<?php nxs_l18n_e("Locked", "nxs_td"); ?>' onclick="nxs_js_alert('<?php nxs_l18n_e("This item is locked, only a webdesigner can modify it", "nxs_td"); ?>');">
		      	<span class='nxs-icon-lock'></span>
		      </a>
		  	</li>
			</ul>
			<?php
		}
	}
	
  // --------------------------------------------------------------------------------------------------
    
  // Setting the contents of the output buffer into a variable and cleaning up te buffer
  $menu = nxs_ob_get_contents();
  nxs_ob_end_clean();
  
  // Setting the contents of the variable to the appropriate array position
  // The framework uses this array with its accompanying values to render the page
  global $nxs_global_placeholder_render_statebag;
	$nxs_global_placeholder_render_statebag["menutopright"] = $menu;
}

function nxs_frontendframework_sitemap_gethtmlforimage($args)
{
	extract($args);
	
	$image_alt = trim($image_alt);
	$image_title = trim($image_title);
	$image_maxheight_cssclass = nxs_getcssclassesforlookup("nxs-maxheight-", $image_maxheight);

	if ($destination_target == "_nxspopup")
 	{
		if ($destination_popuparticleid == "")
 		{
 			$destination_popuparticleid = $destination_articleid;
 			$domid = "nxs_popups_template_{$destination_popuparticleid}";
			nxs_popup_renderpopuptemplate($destination_popuparticleid, $domid);
			$url = "#";
			$destination_js = "nxs_js_popup_setsessioncontext('currentpopuptemplateid', '{$domid}'); ";
 			$destination_articleid = "";
 		}
 		else
 		{
 			// add error to the list render_errors
 		}
 	}

	if ($image_size == "")
	{
		$image_size = "auto-fit";
	}
	
	// Image metadata
	if ($image_imageid == "" && $image_src == "") 
	{
		return "";
	}
	if (!nxs_isimagesizevisible($image_size))
	{
		return "";
	}
	
	// Image shadow
	if ($image_shadow != "") {
		$image_shadow = 'nxs-shadow';
	}
	
	// Hover effects
	if ($enlarge != "") { $enlarge = 'nxs-enlarge'; }
	if ($grayscale != "") {	$grayscale = 'nxs-grayscale'; }
	
	// escape quotes used in title and alt, preventing malformed html
	$image_title = str_replace("\"", "&quote;", $image_title);
	$image_alt = str_replace("\"", "&quote;", $image_alt);
	
	$wpsize = nxs_getwpimagesize($image_size);
	
	if ($image_imageid != "")
	{
		$imagemetadata= nxs_wp_get_attachment_image_src($image_imageid, $wpsize, true);
	
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$imageurl 		= $imagemetadata[0];
		$imageurl = nxs_img_getimageurlthemeversion($imageurl);
		$imagewidth 	= $imagemetadata[1] . "px";
		$imageheight 	= $imagemetadata[2] . "px";	
	}
	else if ($image_src != "")
	{
		$imageurl = $image_src;
	}
	
	$image_size_cssclass = nxs_getimagecsssizeclass($image_size);
	$image_alignment_cssclass = nxs_getimagecssalignmentclass($image_alignment); // "nxs-icon-left";
	
	// Border size
	$image_border_width = nxs_getcssclassesforlookup("nxs-border-width-", $image_border_width);
	
	$image_margin_cssclass = nxs_getcssclassesforlookup("nxs-margin-", $image_margin);
	$margin_bottom_cssclass = nxs_getcssclassesforlookup("nxs-margin-bottom-", $margin_bottom);
	$border_radius_cssclass = nxs_getcssclassesforlookup("nxs-border-radius-", $border_radius);
	
	$img_style = "";
	
	// Image border
	$image_border = '';
	$image_border .= '<div class="nxs-image-wrapper ' . $image_shadow . ' ' . $image_size_cssclass . ' ' . $image_alignment_cssclass . ' ' . '">';
	$image_border .= '<div style="right: 0; left: 0; top: 0; bottom: 0; border-style: solid;" class="nxs-overflow ' . $image_border_width . '">';
	
	$id_att = "";
	if ($id != "")
	{
		$id_att = "id=\"{$id_att}\"";
	}
	
	// note the display: block is essential/required! else the containing div
	// will have two additional pixels; kudos to http://stackoverflow.com/questions/8828215/css-a-2-pixel-line-appears-below-image-img-element
	
	$classes = nxs_concatenateargswithspaces($grayscale, $enlarge, $image_maxheight_cssclass, $border_radius_cssclass, $margin_bottom_cssclass, $image_margin_cssclass, $class); 
	 
	$class_att = implode(" ", $classes);
	
	$image_border .= '<img ' . $id_att . ' style="' . $img_style . '" class="'.$classes.'" ';
	$image_border .= 'src="' . $imageurl . '" ';
	if ($image_alt != "")
	{
		$image_border .= 'alt="' . $image_alt . '" ';
	}
	if ($image_title != "")
	{
		$image_border .= 'title="' . $image_title . '" ';
	}
	$image_border .= '/>';
	$image_border .= '</div>';
	$image_border .= '</div>';
	
	// Image shadow
	// TODO: make ddl too
	if ($image_shadow != "") 				{ $image_shadow = 'nxs-shadow'; }
	
	if ($destination_target == "@@@empty@@@" || $destination_target == "")
 	{
 		// auto
 		if ($destination_articleid != "")
 		{
 			// local link = self
 			$destination_target = "_self";
 		}
 		else
 		{
 			$homeurl = nxs_geturl_home();
 			if (nxs_stringstartswith($url, $homeurl))
 			{
 				$destination_target = "_self";
 			}
 			else
 			{
 				$destination_target = "_blank";
 			}
 		}
 	}
 	if ($destination_target == "_self")
 	{
 		$destination_target = "_self";
 	}
 	else if ($destination_target == "_blank")
 	{
 		$destination_target = "_blank";
 	}
 	else if ($destination_target == "_nxspopup")
 	{
 		$destination_target = "_nxspopup";
 	}
 	else
 	{
 		$destination_target = "_self";
	}

	$destination_relation_html = '';
	if ($destination_relation == "nofollow") {
		$destination_relation_html = 'rel="nofollow"';
	}
	
	// Image link
	if ($destination_articleid != "") 
	{
		$destination_articleid = nxs_geturl_for_postid($destination_articleid);
		$image_border = '<a target="' . $destination_target . '" ' . $destination_relation_html . ' href="' . $destination_articleid .'">' . $image_border . '</a>';
	} 
	else if ($destination_url != "") 
	{
		$image_border = '<a target="' . $destination_target . '" ' . $destination_relation_html . ' href="' . $destination_url .'" target="_blank">' . $image_border . '</a>';
	}
	else if ($destination_js != "")
	{
		$onclick = "onclick='" . nxs_render_html_escape_singlequote($destination_js) . "' ";
		$image_border = "<a href='#' target='_blank' $onclick>{$image_border}</a>";
	}
	
	// Image
	$result = '';
	if ($image_imageid != "" || $image_src != "")
	{
		$result .= '<div class="nxs-relative">';
		$result .= $image_border;
		$result .= '</div>';
	}
	
	return $result;	
}

function nxs_frontendframework_sitemap_sc_image($attributes, $content = null, $name='') 
{
	extract($attributes);
	
	$result = nxs_frontendframework_sitemap_gethtmlforimage($attributes);
	return $result;
}

function nxs_frontendframework_sitemap_gethtmlfortext($args)
{
	extract($args);
	
	if ($_REQUEST["check"] == "atts")
	{
		foreach ($args as $k=>$v)
		{
			if ($k == "color")
			{
				echo "o no deprecated key found in shortcode?!"; 
				echo $k . " / " . $v;
			}
			else if (nxs_stringstartswith($k, "text_"))
			{
				echo "o no deprecated key found in shortcode?!"; 
				echo $k . " / " . $v;
				//die();
			}
		}
	}
	
	if ($text == "")
	{
		return "";
	}
	
	if ($wrappingelement == "") 
	{
		$wrappingelement = 'p';
	}
	
	// Text styling
	if ($showliftnote != "") { $showliftnote_cssclass = 'nxs-liftnote'; }
	if ($showdropcap != "") { $showdropcap_cssclass = 'nxs-dropcap'; }
	
	$alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $align);
	
	$fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen-", $fontzen);
	
	$cssclasses = nxs_concatenateargswithspaces("nxs-default-p", "nxs-applylinkvarcolor", "nxs-padding-bottom0", $alignment_cssclass, $showliftnote_cssclass, $showdropcap_cssclass, $fontzen_cssclass);
	
	if ($heightiq != "") 
	{
		$heightiqprio = "p1";
		$heightiqgroup = "text";
		$cssclasses = nxs_concatenateargswithspaces($cssclasses, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$heightiqgroup}");
	}
	
	if ($texttype == "quote")
	{
		$styles .= "font-style: italic;";
	}
	
	if ($content_justify == "start")
	{
		$styles .= "display: flex; flex-direction: row; justify-content: flex-start;";
	}
	else if ($content_justify == "center")
	{
		$styles .= "display: flex; flex-direction: row; justify-content: center;";
	}
	else if ($content_justify == "end")
	{
		$styles .= "display: flex; flex-direction: row; justify-content: flex-end;";
	}
	
	if (nxs_stringcontains($fontsize, "-"))
	{
		// format; nxs-fontsize-1-2
		$val = $fontsize;
		$val = str_replace("nxs-fontsize-", "", $val);
		
		$pieces = explode("-", $val);
		$whole = $pieces[0];
		$fraction = $pieces[1];
		$value = $whole + ($fraction / 10);
		$factor = 15;
		$value = $value * $factor;
		
		$styles .= "font-size: {$value}px !important;";
	}
	
	if ($line_height == "")
	{
		// default
	}
	else if (nxs_stringcontains($line_height, "-"))
	{
		// format; nxs-line-height-1-0
		$val = $line_height;
		$val = str_replace("nxs-line-height-", "", $val);
		
		$pieces = explode("-", $val);
		$whole = $pieces[0];
		$fraction = $pieces[1];
		$value = $whole + ($fraction / 10);
		$factor = 1.625;
		$value = $value * $factor;
		
		$styles .= "line-height: {$value}em !important;";
	}
	else
	{
		$styles .= "line-height: unsupported_$val;";
	}
	
	// apply shortcode on text widget
	$text = do_shortcode($text);
		
	$result .= '<'. $wrappingelement . ' class="' . $cssclasses . '" style="'.$styles.'">' . $text . '</'. $wrappingelement . '>';
	
	return $result;
}
