<?php

function nxs_widgets_serviceset_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-publicrelations";
}

// Setting the widget title
function nxs_widgets_serviceset_gettitle() {
	return nxs_l18n__("Set of Services", "nxs_td");
}

// Unistyle
function nxs_widgets_serviceset_getunifiedstylinggroup() {
	return "servicesetwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_serviceset_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_serviceset_gettitle(),
		"sheeticonid" => nxs_widgets_serviceset_geticonid(),
		"sheethelp" => nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1764396204"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_serviceset_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			array(
				"id" 				=> "items_genericlistid",
				"type" 				=> "staticgenericlist_link",
				"label" 			=> nxs_l18n__("Edit services", "nxs_td"),
			),
		)
	);
	
	// nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}


/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_serviceset_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_serviceset_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);	
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	global $nxs_global_row_render_statebag;
	
	$items_genericlistid = $mixedattributes['items_genericlistid'];

	/* HOVER MENU HTML
	---------------------------------------------------------------------------------------------------- */
	
	nxs_ob_start();

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
			$nxsrefurlspecial = urlencode(base64_encode($currentcontainerposturl));
			
			//$nxsrefurlspecial = urlencode(base64_encode(nxs_geturl_for_postid($postid)));
			$refurl = nxs_geturl_for_postid($items_genericlistid);
			$refurl = nxs_addqueryparametertourl_v2($refurl, "nxsrefurlspecial", $nxsrefurlspecial, false);
			?>
			<a href='#' class='nxs-defaultwidgetclickhandler' title='<?php nxs_l18n_e("Edit[nxs:tooltip]", "nxs_td"); ?>' onclick="var url='<?php echo $refurl; ?>'; nxs_js_redirect(url); return false;">
				<li title='<?php nxs_l18n_e("Edit[nxs:tooltip]", "nxs_td"); ?>'>
					<span class='nxs-icon-serviceset'></span>
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
					<span id='placeholdertemplate_<?php echo $placeholdertemplate; ?>' class='<?php echo nxs_widgets_serviceset_geticonid();?>'></span>
				</div>
			</div>					
		</li>
		<a class='nxs-no-event-bubbling' href='#' onclick='nxs_js_popup_placeholder_wipe("<?php echo $postid; ?>", "<?php echo $placeholderid; ?>"); return false;'>
			<li title='<?php nxs_l18n_e("Delete[nxs:tooltip]", "nxs_td"); ?>'><span class='nxs-icon-trash'></span></li>
		</a>
		<?php 
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
		echo $debughtml;
		?>
	</ul>
	<?php 
	
	$menu = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	global $nxs_global_placeholder_render_statebag;
	$nxs_global_placeholder_render_statebag["menutopright"] = $menu;
	
	
	// Default name class
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-serviceset ";

	// Render html
	if (true)
	{
		nxs_ob_start();
		$structure = nxs_parsepoststructure($items_genericlistid);
		foreach ($structure as $pagerow) 
		{
			$content = $pagerow["content"];
			$placeholderid = nxs_parsepagerow($content);
			$placeholdermetadata = nxs_getwidgetmetadata($items_genericlistid, $placeholderid);
			
			// Lookup atts
			$placeholdermetadata = nxs_filter_translatelookup($placeholdermetadata, array("title", "text"));	
			$placeholdertype = $placeholdermetadata["type"];					
			
			if ($placeholdertype == "") 
			{
				// ignore
			} else if ($placeholdertype == "undefined") 
			{
				// ignore
			} 
			else 
			{
				// 
				$type = $placeholdermetadata["type"];
				if ($type == "service")
				{
					$semantic = $placeholdermetadata["semantic"];
					echo "<div>service: $semantic</div>";
				}
				else if ($type == "customservice")
				{
					$title = $placeholdermetadata["title"];
					echo "<div>service: $title</div>";
				}
				else
				{
					echo "<div>service: other</div>";
				}
				
				
			} 
		}

		$html = nxs_ob_get_contents();
		nxs_ob_end_clean();
	}
	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-'.$placeholderid;

	return $result;
}

/* INITIATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_serviceset_initplaceholderdata($args)
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	
	// create a new generic list with subtype
	// assign the newly create list to the list property
	
	$subargs = array();
	$subargs["nxsposttype"] = "genericlist";
	$subargs["nxssubposttype"] = "serviceset";	// NOTE!
	$subargs["poststatus"] = "publish";
	$subargs["titel"] = nxs_l18n__("Service Set", "nxs_td") . " " . nxs_generaterandomstring(6);
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
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_serviceset_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	$result = nxs_widgets_initplaceholderdatageneric($args, $widgetname);
	return $result;
}

?>