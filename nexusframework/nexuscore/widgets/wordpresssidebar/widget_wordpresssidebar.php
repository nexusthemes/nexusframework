<?php

function nxs_widgets_wordpresssidebar_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_wordpresssidebar_gettitle() {
	return nxs_l18n__("Widget Area", "nxs_td");
}

// Unistyle
function nxs_widgets_wordpresssidebar_getunifiedstylinggroup() {
	return "wpsidebarwidget";
}

// Unicontent
function nxs_widgets_wordpresssidebar_getunifiedcontentgroup() {
	return "wpsidebarwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_wordpresssidebar_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_wordpresssidebar_gettitle(),
		"sheeticonid" 		=> nxs_widgets_wordpresssidebar_geticonid(),
		"supporturl" => "https://www.wpsupporthelp.com/wordpress-questions/wordpress-backend-wordpress-questions-15/",
		"unifiedstyling" 	=> array("group" => nxs_widgets_wordpresssidebar_getunifiedstylinggroup(),),
		"unifiedcontent" 	=> array("group" => nxs_widgets_wordpresssidebar_getunifiedcontentgroup(),),
		"fields" => array
		(
			// TITLE
			
			array(
				"id" 				=> "wpsidebarid",
				"type" 				=> "input",
				"visibility" 		=> "hidden",
				"label" 			=> nxs_l18n__("WP sidebar ID", "nxs_td"),
			),
		
			array(
				"id" 				=> "wpsidebarid_visualization",
				"altid" 			=> "wpsidebarid",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_wordpresssidebar_wpsidebarid_popupcontent",
				"label" 			=> nxs_l18n__("WP backend widget area", "nxs_td"),
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

function nxs_widgets_wordpresssidebar_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
			
	$result = array();
	$result["result"] = "OK";

	// sommige eigenschappen zijn vastgelegd op placeholder meta, andere op pagina, en andere daarbuiten
	
	// metadata velden
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	$mixedattributes = array_merge($temp_array, $args);
	$wpsidebarid = $mixedattributes['wpsidebarid'];						// OK

	global $nxs_global_placeholder_render_statebag;
	
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
	
	//
	// render actual control / html
	//
	
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-wordpress-sidebar nxs-applylinkvarcolor";
	
	if ($wpsidebarid == "")
	{
		$wpsidebarid = "1";
	}
	
	?>
	<!-- -->
	
	<div <?php echo $class; ?>>
		<?php 
		nxs_ob_start();
		dynamic_sidebar(intval($wpsidebarid));
		$sidebarcontent = nxs_ob_get_contents();
		nxs_ob_end_clean();
		
		
		
		//
		//$sidebarcontent = do_shortcode($sidebarcontent);

		if ($sidebarcontent == "")
		{
			nxs_renderplaceholderwarning(nxs_l18n__("No widgets found in widget area[nxs:warning]", "nxs_td"));
		}			
		else
		{
			if ( false !== strpos( $sidebarcontent, '[' ) )
			{
				// 
				$sidebarcontent = str_replace("&#039;", "'", $sidebarcontent);
				$sidebarcontent = str_replace("&quot;", '"', $sidebarcontent);
				$sidebarcontent = do_shortcode($sidebarcontent);
			}
			
			?>
			<ul class='nxs-sidebar-widgets'>
				<?php echo $sidebarcontent; ?>
			</ul>
			<?php				
		}
		?>
	</div>
	
	<?php 
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;
	
	return $result;
}

function nxs_wordpresssidebar_wpsidebarid_popupcontent($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);

	$value = $$altid;	// $id is the parametername, $$id is the value of that parameter

	nxs_ob_start();
	?>
	<select onchange="jQuery('#<?php echo $altid; ?>').val(jQuery(this).val()); nxs_js_popup_sessiondata_make_dirty();">
		<?php
		$max_i = nxs_widgets_gettotalwidgetareacount();
		for ($i = 1; $i <= $max_i; $i++)
		{
			?>
			<option <?php if ($value==$i) echo "selected='selected'"; ?> value='<?php echo $i; ?>'>WP Backend Widget area <?php echo $i; ?></option>
			<?php
		}
		?>
	</select>
	<?php
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

function nxs_widgets_wordpresssidebar_initplaceholderdata($args)
{
	extract($args);

	$args['wpsidebarid'] = "1";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_wordpresssidebar_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);

	// current values as defined by unicontent prefail over the above "default" props
	$unicontentgroup = nxs_widgets_wordpresssidebar_getunifiedcontentgroup();
	$args = nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup);
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_dataprotection_nexusframework_widget_wordpresssidebar_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("widget-none");
}

?>