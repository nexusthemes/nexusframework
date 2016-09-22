<?php

function nxs_widgets_pagevideo_geticonid() {
	return "nxs-icon-youtube";
}

// Setting the widget title
function nxs_widgets_pagevideo_gettitle() {
	return nxs_l18n__("Page video", "nxs_td");
}

// Unistyle
function nxs_widgets_pagevideo_getunifiedstylinggroup() {
	return "pagevideowidget";
}

function nxs_widgets_pagevideo_registerhooksforpagewidget($args)
{
	if ( nxs_ishandheld())
	{
		// ignore; not available on mobiles/tablets
	}
	else
	{
		$pagedecoratorid = $args["pagedecoratorid"]; 
		$pagedecoratorwidgetplaceholderid = $args["pagedecoratorwidgetplaceholderid"];
		
		global $nxs_pagevideo_pagedecoratorid;
		$nxs_pagevideo_pagedecoratorid = $pagedecoratorid;
		global $nxs_pagevideo_pagedecoratorwidgetplaceholderid;
		$nxs_pagevideo_pagedecoratorwidgetplaceholderid = $pagedecoratorwidgetplaceholderid;
		
		add_action('nxs_beforeend_head', 'nxs_widgets_pagevideo_beforeend_head');
		add_action('nxs_ext_betweenheadandcontent', 'nxs_widgets_pagevideo_betweenheadandcontent');
	}
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_pagevideo_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" 		=> nxs_widgets_pagevideo_gettitle(),
		"sheeticonid" 		=> nxs_widgets_pagevideo_geticonid(),
		"sheethelp" => nxs_l18n__("https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1764396204"),
		"unifiedstyling" 	=> array("group" => nxs_widgets_pagevideo_getunifiedstylinggroup(),),
		"fields" => array
		(
			// SLIDES			
			
			array( 
				"id" 				=> "wrapper_pagevideo_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Page video", "nxs_td"),
			),
			
			array
			( 
				"id" 				=> "youtubeid",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Youtube ID", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Enter the ID of the Youtube movie here.", "nxs_td"),
				"unicontentablefield" => true,
			),
			
			array(
				"id" 				=> "screenposition",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Screen position", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("Default (fixed)", "nxs_td"),
					"absolute" => nxs_l18n__("Absolute", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "layoutposition",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Layout position", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("Default", "nxs_td"),
					"betweenheadandcontent" => nxs_l18n__("Between head and content", "nxs_td"),
				),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "translationtop",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Translation top", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("Default", "nxs_td"),
					"top_-1_0" => nxs_l18n__("-1x", "nxs_td"), // 80 pixels
					"top_1_0" => nxs_l18n__("1x", "nxs_td"), // 80 pixels
				),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_pagevideo_end",
				"type" 				=> "wrapperend"
			),
		)
	);

	nxs_extend_widgetoptionfields($options, array("unistyle"));	
	
	return $options;
}


/* ADMIN PAGE HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_pagevideo_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_pagevideo_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	
	global $nxs_global_row_render_statebag;
	
	$items_genericlistid = $mixedattributes['items_genericlistid'];

	/* ADMIN PAGE HOVER MENU HTML
	---------------------------------------------------------------------------------------------------- */
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["enable_decoratewidget"] = false;
	$hovermenuargs["enable_deletewidget"] = false;
	$hovermenuargs["enable_deleterow"] = true;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
	/* ADMIN EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	nxs_ob_start();
	
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-custom-html nxs-applylinkvarcolor";
		
	$shouldrenderalternative = false;
	$trimmedhtmlcustom = $htmlcustom;
	$trimmedhtmlcustom = preg_replace('/<!--(.*)-->/Uis', '', $trimmedhtmlcustom);
	$trimmedhtmlcustom = trim($trimmedhtmlcustom);
	if ($trimmedhtmlcustom == "" && nxs_has_adminpermissions())
	{
		$shouldrenderalternative = true;
	}
	
	/* ADMIN OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	echo '
	<div class="nxs-dragrow-handler nxs-padding-menu-item">
		<div class="content2">
			<div class="box">
	        	<div class="box-title">
					<h4>Page video</h4>
				</div>
				<div class="box-content"></div>
			</div>
			<div class="nxs-clear"></div>
		</div>
	</div>';
	
	/* ------------------------------------------------------------------------------------------------- */
	
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-'.$placeholderid;

	// outbound statebag
	return $result;
}

/* INITIATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_pagevideo_initplaceholderdata($args)
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	
	//$args["item_durationvisibility"] = "5000";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_pagevideo_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	$result = nxs_widgets_initplaceholderdatageneric($args, $widgetname);
	return $result;
}

/* UPDATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_pagevideo_updateplaceholderdata($args) 
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	$result = nxs_widgets_updateplaceholderdatageneric($args, $widgetname);
	return $result;
}

/* PAGE SLIDER HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_pagevideo_beforeend_head()
{
	// do something useful here if thats needed
	
	?>
	
	
	<?php
}

/* OUTPUT
----------------------------------------------------------------------------------------------------*/
	
function nxs_widgets_pagevideo_betweenheadandcontent()
{
	
	// get meta of the slider itself (such as transition time, etc.)
	global $nxs_pagevideo_pagedecoratorid;
	global $nxs_pagevideo_pagedecoratorwidgetplaceholderid;	
	$pagevideo_metadata = nxs_getwidgetmetadata($nxs_pagevideo_pagedecoratorid, $nxs_pagevideo_pagedecoratorwidgetplaceholderid);
	
	// Unistyle
	$unistyle = $pagevideo_metadata["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_pagevideo_getunifiedstylinggroup(), $unistyle);
		$pagevideo_metadata = array_merge($pagevideo_metadata, $unistyleproperties);
	}
	
	extract($pagevideo_metadata);
	
	/* EXPRESSIONS
	----------------------------------------------------------------------------------------------------*/
	
	if ($youtubeid == "")
	{
		$youtubeid = "KlAV4kIQBQ8";
	}
	$script = "";
	$deltatop = "0";
	if (nxs_stringstartswith($translationtop, "top_"))
	{
		$pieces = explode("_", $translationtop);
		$factor = $pieces[1];	// bijv. 3 bij top_3_0
		$deltatop = $factor * 80; // bijv. 240 bij factor 3
	}

	if ($screenposition == "absolute")
	{
		$position = "absolute";
	}
	else
	{
		$position = "fixed";
	}

	$script .= "
		<script type='text/javascript'>
			function nxs_js_pagevideo_updateheight()
			{";

	if ($layoutposition == "betweenheadandcontent")
	{
		$script .= "
				var headerheight = jQuery('#nxs-header').height();
				headerheight += " . $deltatop . ";
				var headerheightstring = headerheight + 'px';
				
				jQuery('#tubular-container').css('top', headerheightstring);
		";
	}
	
	// screen position
	$script .= "jQuery('#tubular-player').css('position', '".$position."');";

	
	$script .= "	
			}

			jQuery(document).bind('nxs_event_resizeend', function() { nxs_js_pagevideo_updateheight(); } );
			// first time
			jQuery(window).load(function(){ nxs_js_pagevideo_updateheight(); });
		</script>
	";
	
	/* OUTPUT
	----------------------------------------------------------------------------------------------------*/

	?>
	<div id="wrapper" class="clearfix"></div>
	<div id="ytcontainer" class="clearfix"></div>
	<script src='<?php echo nxs_getframeworkurl(); ?>/nexuscore/widgets/pagevideo/js/jquery.tubular.1.0.js'></script>
  
	<script type="text/javascript">
  	// '9bZkp7q19f0'
    jQ_nxs('document').ready(function() {
			var options = { videoId: '<?php echo $youtubeid; ?>', start: 3 };
			$('#wrapper').tubular(options);
		});
  </script>
	<?php
	echo $script;
}

?>
