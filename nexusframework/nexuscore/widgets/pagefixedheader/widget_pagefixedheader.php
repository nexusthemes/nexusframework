<?php

function nxs_widgets_pagefixedheader_geticonid() {
	return "nxs-icon-pushpin";
}

function nxs_widgets_pagefixedheader_gettitle() {
	return nxs_l18n__("Fixed header", "nxs_td");
}

function nxs_widgets_pagefixedheader_registerhooksforpagewidget($args)
{	
	$pagedecoratorid = $args["pagedecoratorid"]; 
	$pagedecoratorwidgetplaceholderid = $args["pagedecoratorwidgetplaceholderid"];
	
	global $nxs_pagefixedheader_pagedecoratorid;
	$nxs_pagefixedheader_pagedecoratorid = $pagedecoratorid;
	global $nxs_pagefixedheader_pagedecoratorwidgetplaceholderid;
	$nxs_pagefixedheader_pagedecoratorwidgetplaceholderid = $pagedecoratorwidgetplaceholderid;
	
	// $pagevideo_metadata = nxs_getwidgetmetadata($nxs_pagefixedheader_pagedecoratorid, $nxs_pagefixedheader_pagedecoratorwidgetplaceholderid);
	// $condition_enable = $pagevideo_metadata["condition_enable"];
	
	$enabled = true;
	if (nxs_ishandheld())
	{
		$enabled = false;
	}
	
	if ($enabled)
	{
		add_action('nxs_beforeend_head', 'nxs_widgets_pagefixedheader_beforeend_head');
		add_action('nxs_ext_betweenheadandcontent', 'nxs_widgets_pagefixedheader_betweenheadandcontent');
	}
}

function nxs_widgets_pagefixedheader_beforeend_head()
{
	// do something useful here if thats needed
	
	?>
	<?php
}

function nxs_widgets_pagefixedheader_betweenheadandcontent()
{
	// get meta of the slider itself (such as transition time, etc.)
	global $nxs_pagefixedheader_pagedecoratorid;
	global $nxs_pagefixedheader_pagedecoratorwidgetplaceholderid;	
	$pagefixedheader_metadata = nxs_getwidgetmetadata($nxs_pagefixedheader_pagedecoratorid, $nxs_pagefixedheader_pagedecoratorwidgetplaceholderid);
	
	// Unistyle
	$unistyle = $pagefixedheader_metadata["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_pagefixedheader_getunifiedstylinggroup(), $unistyle);
		$pagefixedheader_metadata = array_merge($pagefixedheader_metadata, $unistyleproperties);
	}
	
	extract($pagefixedheader_metadata);
	
	/* EXPRESSIONS
	----------------------------------------------------------------------------------------------------*/
	
	
	/* OUTPUT
	----------------------------------------------------------------------------------------------------*/

	// derive 'current' classes
	global $nxs_global_current_containerpostid_being_rendered;
	global $nxs_global_current_postid_being_rendered;

	$postid = $nxs_global_current_containerpostid_being_rendered;

	if ( $display == "")
	{
		$display = 'float';
	}

	$widescreenclass = "";
	if ($widescreen)
	{
		$widescreenclass = "nxs-widescreen";
	}

	$shadowclass = "";
	if ($shadow != "none")
	{
		$shadowclass = "nxs-shadow";
	}

	// Offset
	$offsetpixels = 0;
	$visible_class = '';
	if ( $offset != '' )
	{
		$offsetpixels = substr($offset, 0, -2);
	} else {
		$visible_class = 'show';
	}

	$concatenatedcssclasses_container 	= nxs_concatenateargswithspaces($widescreenclass, $shadowclass, $visible_class);

	
	if (isset($header_postid) && $header_postid != 0)
	{
		$cssclass = "nxs-elements-container nxs-post-" . $header_postid;

		//var_dump($cssclass);
		?>

		<div id="nxs-fixed-header" class="nxs-fixed-header nxs-sitewide-element <?php echo $concatenatedcssclasses_container; ?>">
			<div id="nxs-fixed-header-container" class="nxs-sitewide-container nxs-fixed-header-container nxs-elements-container nxs-post-<?php echo $existingheaderid . " " . $cssclass; ?>">
				<?php 
					if ($header_postid != "")
					{	
						echo nxs_getrenderedhtmlincontainer($postid, $header_postid, "anonymous");
					}
					else
					{
						// don't render anything if its not there
					}
				?>
			</div>
		</div>
	  	<?php 
	}

	?>

	<script type="text/javascript">

		<?php
			if ( $offset == '')
			{
				if ( $display == 'inline' )
				{
				?>

					function nxs_js_set_fixedheader_padding() {
						var fixedheaderheight = $('.nxs-fixed-header').height();
						$('body').css('paddingTop', fixedheaderheight);
					}

					jQuery(document).bind('nxs_event_resizeend', function() {
						nxs_js_set_fixedheader_padding();
					});

					$(document).ready(function(){
						nxs_js_set_fixedheader_padding();
					});

				<?php
				}
			}

			// if the offset is not set then their is no need for the nxs_js_show_fixedheader function
			else {
			?>
				function nxs_js_show_fixedheader() {
					if ($(window).scrollTop() < <?php echo $offsetpixels; ?>) {
						$("#nxs-fixed-header:visible").fadeOut(200);
					}
					else {
						$("#nxs-fixed-header:hidden").fadeIn(200);
					}
				}

				setTimeout(function() {
					nxs_js_show_fixedheader();
				}, 1000);
			
				$(window).scroll(function () { 
					nxs_js_show_fixedheader();
				});
			<?php
			}
		?>
	</script>
	
	<?php
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_pagefixedheader_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_pagefixedheader_gettitle(),
		"sheeticonid" => nxs_widgets_pagefixedheader_geticonid(),
		"footerfiller" => true,
		"fields" => array
		(
			// SLIDES			
			
			array
			( 
				"id" 				=> "wrapper_input_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Configuration", "nxs_td"),
			),

			array
			( 
				"id"					=> "header_postid",
				"type" 					=> "selectpost",
				"post_status"			=> array("publish", "future"),
				"previewlink_enable"	=> "false",
				"label" 				=> nxs_l18n__("Fixed header", "nxs_td"),
				"tooltip" 				=> nxs_l18n__("Select a header to show on the top of your page. The header will stay on top, even when scrolling down.", "nxs_td"),
				"post_type" 			=> "nxs_header",
				"buttontext" 			=> nxs_l18n__("Style header", "nxs_td"),
				"emptyitem_enable"		=> false,
			),

			array
			(
				"id" 				=> "widescreen",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Widescreen", "nxs_td"),
			),

			array
			(
				"id" 				=> "shadow",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Shadow", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Adds a shadow at the bottom of the fixed header", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("shadow")
			),

			array
			(
				"id" 				=> "display",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Display", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Display inline will be ignored if a top scroll till visible is given.", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fixedheader_display")
			),

			array
			(
				"id" 				=> "offset",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Top scroll till visible", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Display inline will be ignored if a top scroll till visible is given.", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("offset")
			),

			array
			( 
				"id" 				=> "wrapper_input_end",
				"type" 				=> "wrapperend"
			),
		)
	);
	
	return $options;
}


/* ADMIN PAGE HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_pagefixedheader_render_webpart_render_htmlvisualization($args) 
{
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	// Widget specific variables
	extract($mixedattributes);
	
	// popup menu
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["enable_decoratewidget"] = false;
	$hovermenuargs["enable_deletewidget"] = false;
	$hovermenuargs["enable_deleterow"] = true;
	$hovermenuargs["metadata"] = $mixedattributes;	
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
	// Turn on output buffering
	nxs_ob_start();
	
	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));
		
	global $nxs_global_placeholder_render_statebag;
	if ($shouldrenderalternative == true) {
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . "-warning ";
	} else {
		// Appending custom widget class
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " ";
	}
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */

		
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) 
	{
		if ($alternativehint == "")
		{
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		}
		nxs_renderplaceholderwarning($alternativehint); 
	} 
	else 
	{
		/* ADMIN OUTPUT
		---------------------------------------------------------------------------------------------------- */
		
		echo '
		<div class="nxs-dragrow-handler nxs-padding-menu-item">
		<div class="content2">
		 <div class="box">
		        <div class="box-title">
		   <h4>Fixed header</h4>
		  </div>
		  <div class="box-content"></div>
		 </div>
		 <div class="nxs-clear"></div>
		</div>
		</div>';
		
		/* ------------------------------------------------------------------------------------------------- */
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


function nxs_widgets_pagefixedheader_initplaceholderdata($args)
{
	extract($args);

	$args['display'] = "";
	$args['shadow'] = "";
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
