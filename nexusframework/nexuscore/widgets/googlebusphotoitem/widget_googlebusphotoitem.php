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

function nxs_widgets_googlebusphotoitem_geticonid()
{
	return "nxs-icon-image";
}

// Setting the widget title
function nxs_widgets_googlebusphotoitem_gettitle() 
{
	return nxs_l18n__("googlebusphotoitem[nxs:widgettitle]", "nxs_td");
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_googlebusphotoitem_panoviewer($optionvalues, $args, $runtimeblendeddata) 
{
	nxs_ob_start();
	
	$headingid = "heading";
	$pitchid = "pitch";
	$zoomid = "zoom";
	$panoramid = "panorama_id";
	
	extract($optionvalues);
	
	
	$containerpostid = $args["clientpopupsessioncontext"]["containerpostid"];
	$title = nxs_gettitle_for_postid($containerpostid);
	// todo: rename the $placeholderid in the code below to something like $panoid
	$placeholderid = "popuppanoviewer";	// not really a placeholderid, but will do nevertheless :)
	
	$heading = $runtimeblendeddata[$headingid];
	if ($heading == "") { $heading = 0; };

	$pitch = $runtimeblendeddata[$pitchid];
	if ($pitch == "") { $pitch = 0; };

	$zoom = $runtimeblendeddata[$zoomid];
	if ($zoom == "") { $zoom = 0; };
	
	?>
  <div id="pano_canvas_<?php echo $placeholderid;?>" class="nxs-float-left" style="width: 100%; height: 400px;"></div>
  <div class="nxs-float-left">
  	<input id='helpernewpanoidfield' class='float-left' style='width: 300px;' type='text' value='<?php echo $runtimeblendeddata[$panoramid]; ?>'></input>
  	<a href='#' onclick="nxs_js_processnewpano(); return false;" class='nxsbutton1 class='float-left''>Update</a></div>
  	<div class="nxs-clear"></div>
  </div>
  <div class="nxs-clear"></div>
  
  <script type='text/javascript'>
		
		function nxs_js_processnewpano()
		{
			var panoid = jQuery('#helpernewpanoidfield').val();
			// update view
			nxs_js_panos["pano_<?php echo $placeholderid; ?>"].setPano(panoid);
			// update targetfield
			jQuery("#<?php echo $panoid; ?>").val(panoid);
		}
				
		function nxs_js_widget_googlepano_init_<?php echo $placeholderid; ?>()
		{
			var options = 
			{
				scrollwheel: false,
				linksControl: true,
				zoomControl: true,
	  		panControl: true,
	  		addressControl: true,
			  pov:
			  {
			  	heading: <?php echo $heading; ?>,
			  	pitch: <?php echo $pitch; ?>,
			  	zoom: <?php echo $zoom; ?>
				}
			};
      
      //nxs_js_log("constructing object...");
      nxs_js_panos["pano_<?php echo $placeholderid; ?>"] = new google.maps.StreetViewPanorama(document.getElementById("pano_canvas_<?php echo $placeholderid; ?>"), options);
			//nxs_js_log("constructed object");
			
			// todo: remove existing listeners?
			
			// pano changed
			google.maps.event.addListener(nxs_js_panos["pano_<?php echo $placeholderid; ?>"], 'pano_changed', function() 
			{
	      var panoid = nxs_js_panos["pano_<?php echo $placeholderid; ?>"].getPano();
	      // make dirty
	      nxs_js_popup_sessiondata_make_dirty();
	      jQuery("#<?php echo $panoramid; ?>").val(panoid);
	      jQuery("#helpernewpanoidfield").val(panoid);
	      
		  });
		 	
		 	// pov changed (pitch, heading, zoom) 
		  google.maps.event.addListener(nxs_js_panos["pano_<?php echo $placeholderid; ?>"], 'pov_changed', function() 
		  {
	      var heading = nxs_js_panos["pano_<?php echo $placeholderid; ?>"].getPov().heading;
	      var pitch = nxs_js_panos["pano_<?php echo $placeholderid; ?>"].getPov().pitch;
	      var zoom = nxs_js_panos["pano_<?php echo $placeholderid; ?>"].getPov().zoom;
	      
	      // make dirty
	      nxs_js_popup_sessiondata_make_dirty();
	      jQuery("#<?php echo $headingid; ?>").val(heading);
	      jQuery("#<?php echo $pitchid; ?>").val(pitch);
	      jQuery("#<?php echo $zoomid; ?>").val(zoom);
  		});

			// als we de aanroep niet doen, blijft het scherm grijs...      
      setTimeout
      (
	      function() 
	      { 
	      	// nxs_js_log("setting pano ...");
	      	nxs_js_panos["pano_<?php echo $placeholderid; ?>"].setPano('<?php echo $runtimeblendeddata[$panoramid]; ?>');
	      	// nxs_js_log("pano is now set");
	      	
	      	// nxs_js_log("resizing pano");
	      	google.maps.event.trigger(nxs_js_panos["pano_<?php echo $placeholderid; ?>"], "resize"); 
		      // nxs_js_log("pano is resized");
	      }, 
	      500
	    );
      
      // nxs_js_log("done that");
		}
		
		function nxs_js_widget_googlepano_init_<?php echo $placeholderid; ?>_initmapscript()
		{
			if (!nxs_js_panolazyloaded && !nxs_js_panolazyloading)
			{
				//nxs_js_log('loading...');
				
				nxs_js_panolazyloading = true;
				
				var w = window;
				var d = w.document;
				var script = d.createElement('script');
				script.setAttribute('src', 'http://maps.google.com/maps/api/js?v=3&sensor=true&callback=mapOnLoad');
				d.documentElement.firstChild.appendChild(script);
				w.mapOnLoad = function () 
				{
					// redraw this specific widget
					nxs_js_widget_googlepano_init_<?php echo $placeholderid; ?>();

					//nxs_js_log('maps script is now loaded!');

					// prevent other maps from listening to the event
					nxs_js_panolazyloaded = true;
					nxs_js_panolazyloading = false;

					// trigger event (redraw possible other widgets), see http://weblog.bocoup.com/publishsubscribe-with-jquery-custom-events/
					jQuery(document).trigger("nxs_ext_googlepano_scriptloaded");
				};
			}
			else if (nxs_js_panolazyloading)
			{
				jQuery(document).bind
				(
					"nxs_ext_googlepano_scriptloaded", 
					function() 
					{ 
						nxs_js_widget_googlepano_init_<?php echo $placeholderid; ?>();
					}
				);
			}
			else
			{
				nxs_js_widget_googlepano_init_<?php echo $placeholderid; ?>();
			}
		}
		
		jQuery(document).ready
		(
			function() 
			{
				nxs_js_widget_googlepano_init_<?php echo $placeholderid; ?>_initmapscript();
			}
		);			
	</script>
  <?php
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

// Define the properties of this widget
function nxs_widgets_googlebusphotoitem_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$options = array
	(
		"sheettitle" => nxs_widgets_googlebusphotoitem_gettitle(),
		"sheeticonid" => nxs_widgets_googlebusphotoitem_geticonid(),
		"fields" => array
		(
			/*
			array( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title properties", "nxs_td"),
			),
			array
			( 
				"id" 					=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" => nxs_l18n__("Title goes here", "nxs_td"),
			),
			
			array( 
				"id" 					=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),
			*/
		
			// -------------------------------------------------------			
			array( 
				"id" 					=> "wrapper_input_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("googlebusphotoitem properties", "nxs_td"),
			),
			array(
				"id" 					=> "panorama_id",
				"type" 				=> "input",
				"visibility"	=> "hide",
				"label" 			=> nxs_l18n__("Google panorama ID (pYGutNr-2zUaido2eaJX8g)", "nxs_td"),
				"placeholder" => nxs_l18n__("Enter the Google panorama identifier", "nxs_td"),
			),
			array(
				"id" 					=> "durationinmsecs",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Duration (msecs)", "nxs_td"),
			),
			array(
			
				"id" 					=> "heading",
				"type" 				=> "input",
				"visibility"	=> "hide",
				"label" 			=> nxs_l18n__("Heading (angle/degrees)", "nxs_td"),
			),
			array(
			
				"id" 					=> "pitch",
				"type" 				=> "input",
				"visibility"	=> "hide",
				"label" 			=> nxs_l18n__("Pitch", "nxs_td"),
			),
			array(
			
				"id" 					=> "zoom",
				"type" 				=> "input",
				"visibility"	=> "hide",
				"label" 			=> nxs_l18n__("Zoom", "nxs_td"),
			),
			array(
			
				"id" 					=> "direction",
				"type" 				=> "input",
				"visibility"	=> "hide",
				"label" 			=> nxs_l18n__("Direction (clockwise)", "nxs_td"),
			),
			array(
			
				"id" 					=> "animation",
				"type" 				=> "input",
				"visibility"	=> "hide",
				"label" 			=> nxs_l18n__("Animation (circular180)", "nxs_td"),
			),
			array(
				"id" 					=> "panoviewer",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_widgets_googlebusphotoitem_panoviewer",
				"label" 			=> nxs_l18n__("Pano viewer", "nxs_td"),
			),
			array( 
				"id" 					=> "wrapper_input_end",
				"type" 				=> "wrapperend"
			),
			
			// -------------------------------------------------------
			
			/*
			array( 
				"id" 				=> "wrapper_advancedtitle_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Advanced properties: title", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),

			array(
				"id" 				=> "title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title importance", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading")
			),
			
			array(
				"id" 					=> "title_alignment",
				"type" 				=> "radiobuttons",
				"subtype"  			=> "halign",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
			),
						
			array(
				"id" 					=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Override title fontsize", "nxs_td"),
				"dropdown" 		=> nxs_style_getdropdownitems("fontsize")
			),
			array(
				"id" 					=> "title_heightiq",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align titles", "nxs_td"),
				"tooltip" 		=> nxs_l18n__("When checked, the widget's title will participate in the title alignment of other partipating widgets in this row", "nxs_td")
			),
			array( 
				"id" 					=> "wrapper_advancedtitle_end",
				"type" 				=> "wrapperend"
			),
			*/
			
			// -------------
		)
	);
	
	return $options;
}

/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_googlebusphotoitem_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);

	// Setting the widget name variable to the folder name
	$widget_name = basename(dirname(__FILE__));

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
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs); 
		
	// Turn on output buffering
	nxs_ob_start();
	
	global $nxs_global_placeholder_render_statebag;
	if ($shouldrenderalternative == true) {
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . "-warning ";
	} else {
		// Appending custom widget class
		$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-" . $widget_name . " ";
	}
	
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;
		
	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	if ($shouldrenderalternative) {
		
		nxs_renderplaceholderwarning(nxs_l18n__("Missing input", "nxs_td")); 
	} 
	else 
	{		
		echo "<div>";
		echo "<p>panorama_id:" . $panorama_id . "</p>";
		echo "<p><img src='http://cbk0.google.com/cbk?output=thumbnail&w=500&h=500&panoid=" . $panorama_id . "' /></p>";
		echo "<p>durationinmsecs:" . $durationinmsecs . "</p>";
		echo "<p>heading:" . $heading. "</p>";
		echo "<p>pitch:" . $pitch. "</p>";
		echo "<p>zoom:" . $zoom. "</p>";
		echo "<p>direction:" . $direction. "</p>";
		echo "<p>animation:" . $animation. "</p>";
		echo "</div>";
		echo "<div class='nxs-clear'></div>";
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

function nxs_widgets_googlebusphotoitem_initplaceholderdata($args)
{
	extract($args);

	$args["panorama_id"] = "pYGutNr-2zUaido2eaJX8g";
	$args["durationinmsecs"] = "5000";
	$args["animation"] = "circular180";
	
	$args["heading"] = "0";
	$args["pitch"] = "0";
	$args["zoom"] = "2";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}
