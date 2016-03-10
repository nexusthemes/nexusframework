<?php
function nxs_widgets_googlebusinessphoto_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-gallerybox";
}

// Setting the widget title
function nxs_widgets_googlebusinessphoto_gettitle() {
	return nxs_l18n__("Google Business Photo[nxs:widgettitle]", "nxs_td");
}

// Unistyle
function nxs_widgets_googlebusinessphoto_getunifiedstylinggroup() {
	return "googlebusinessphotowidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_googlebusinessphoto_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS
	
	$options = array
	(
		"sheettitle" => nxs_widgets_googlebusinessphoto_gettitle(),
		"sheeticonid" => nxs_widgets_googlebusinessphoto_geticonid(),
		//"sheethelp" => nxs_l18n__("http://nexusthemes.com/archive-widget/"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_googlebusinessphoto_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			// TITLE
			
			array( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title properties", "nxs_td"),
			),
			array
			( 
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Title goes here", "nxs_td"),
			),
			array(
				"id" 				=> "title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title importance", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
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
				"id" 				=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Override title fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "title_minheightiq",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Row align titles", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("When checked, the widget's title will participate in the title alignment of other partipating widgets in this row", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),
			
			// SLIDES

			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Slides", "nxs_td"),
			),

			array(
				"id" 				=> "items_genericlistid",
				"type" 				=> "staticgenericlist_link",
				"label" 			=> nxs_l18n__("Items", "nxs_td")
			),
			array(
				"id" 				=> "panorama_id",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Google panorama ID", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Enter the Google panorama identifier", "nxs_td"),
			),
						
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend"
			),
			
			
			// DISPLAY PROPERTIES
					
			array( 
				"id" 				=> "wrapper_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Display properties", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "disable_whenloggedon",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Disable tour when loggedon", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "show_pancontrol",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show pancontrol", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Provides a way to rotate the panorama", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "show_zoomcontrol",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show zoomcontrol", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Provides a way to zoom within the image", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "show_linkscontrol",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show linkscontrol", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Provides guide arrows on the image for traveling to adjacent panorama images.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "show_addresscontrol",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show addresscontrol", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Provides a visual overlay indicating the address of the associated location.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "enable_scrollwheelzoom",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Zoom mouse scrollwheel", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Allow user to zoom in and out by using the scrollwheel of the mouse.", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "framespersecond",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Frames per second (30)", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Set the number of frames per second", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "minheight",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("minheight", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("minheight"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
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

function nxs_widgets_googlebusinessphoto_render_webpart_render_htmlvisualization($args) 
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
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_googlebusinessphoto_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);
	}
	
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

	$htmltitle = nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, $title_minheightiq, "", "");
	
	if ($minheight == "")
	{
		$minheight = "200";
	}
	$minheight_cssclass = nxs_getcssclassesforlookup("nxs-minheight-", $minheight);
	
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
		echo $htmltitle;
		?>
		
		<div id="pano_canvas_<?php echo $placeholderid;?>" class="nxs-runtime-autocellsize nxs-minheight <?php echo $minheight_cssclass; ?>"></div>

		<script type='text/javascript'>
			var panorama_is_dirty_<?php echo $placeholderid;?> = false;
  		var panorama_currenttick_in_slide_<?php echo $placeholderid;?>;
  		
  		// todo: add placeholderid suffix
			var panoslidenr = 0;
			var panoslides = [];
			
			<?php
			
			if ($framespersecond == "")
			{
				// default
				$framespersecond = "30";
			}
			
			$structure = nxs_parsepoststructure($items_genericlistid);
			$numofslides = count($structure);
			$isfirst = true;
			$firstmeta = array();
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
					if ($isfirst == true)
					{
						$isfirst = false;
						$firstmeta = $innerplaceholdermetadata;
					}
					
					$innerplaceholdertype = $innerplaceholdermetadata["type"];
					if ($innerplaceholdertype == "googlebusphotoitem")
					{
						?>
						panoslides.push(
						{
							durationinmsecs: <?php echo $innerplaceholdermetadata["durationinmsecs"]; ?>,
							to_heading: <?php echo $innerplaceholdermetadata["heading"]; ?>,
							to_pitch: <?php echo $innerplaceholdermetadata["pitch"]; ?>,
							to_zoom: <?php echo $innerplaceholdermetadata["zoom"]; ?>,
							direction: "<?php echo $innerplaceholdermetadata["direction"]; ?>",
							animation: "<?php echo $innerplaceholdermetadata["animation"]; ?>",
						});
						<?php
					}
					else
					{
						// not supported; skip!
					}
				}
				
				?>
				<?php
			}
			?>
			var numofpanoslides = panoslides.length;
			
			<?php
			if ($numofslides > 0)
			{
				?>
				var panorama_lastheadingpreviousanimation_<?php echo $placeholderid;?> = <?php echo $firstmeta["heading"]; ?>;
  			var panorama_lastpitchpreviousanimation_<?php echo $placeholderid;?> = <?php echo $firstmeta["pitch"]; ?>;
  			var panorama_lastzoompreviousanimation_<?php echo $placeholderid;?> = <?php echo $firstmeta["zoom"]; ?>;
				<?php
			}
			else
			{
				?>
  			var panorama_lastheadingpreviousanimation_<?php echo $placeholderid;?> = 0;
  			var panorama_lastpitchpreviousanimation_<?php echo $placeholderid;?> = 0;
  			var panorama_lastzoompreviousanimation_<?php echo $placeholderid;?> = 3;
  			<?php
  		}
  		?>

			function nxs_js_widget_googlepano_init_<?php echo $placeholderid; ?>()
			{
				//nxs_js_log('initing');
				
				var options = {
					<?php 
		  		if ($show_linkscontrol != "") 
		  		{
		  			echo "linksControl: true,";
		  		} 
		  		else
		  		{
		  			echo "linksControl: false,";
		  		}
		  		?>
		  		<?php 
		  		if ($show_zoomcontrol != "") 
		  		{
		  			echo "zoomControl: true,";
		  		} 
		  		else
		  		{
		  			echo "zoomControl: false,";
		  		}
		  		?>
		  		<?php 
		  		if ($show_pancontrol != "") 
		  		{
		  			echo "panControl: true,";
		  		} 
		  		else
		  		{
		  			echo "panControl: false,";
		  		}
		  		?>
		  		<?php 
		  		if ($show_addresscontrol != "") 
		  		{
		  			echo "addressControl: true,";
		  		} 
		  		else
		  		{
		  			echo "addressControl: false,";
		  		}
		  		?>
		  		<?php 
		  		if ($enable_scrollwheelzoom != "") 
		  		{
		  			echo "scrollwheel: true,";
		  		} 
		  		else
		  		{
		  			echo "scrollwheel: false,";
		  		}
		  		?>
					<?php if ($numofslides > 0) { ?>
					pov: 
					{ 
						heading: <?php echo $firstmeta["heading"]; ?>,
						pitch: <?php echo $firstmeta["pitch"]; ?>,
					},
					<?php } ?>
				  zoom: 14,
				  
				  mapTypeId: google.maps.MapTypeId.ROADMAP
				};
        
        nxs_js_panos["pano_<?php echo $placeholderid; ?>"]  = new google.maps.StreetViewPanorama(document.getElementById("pano_canvas_<?php echo $placeholderid; ?>"), options);
        
        // als we de aanroep niet doen, blijft het scherm grijs...      
	      setTimeout
	      (
		      function() 
		      { 
		      	// nxs_js_log("setting pano ...");
		      	nxs_js_panos["pano_<?php echo $placeholderid; ?>"].setPano('<?php echo $firstmeta["panorama_id"]; ?>');
		      	nxs_js_panos["pano_<?php echo $placeholderid; ?>"].setVisible(true);
		      	// nxs_js_log("pano is now set");
		      	
		      	// nxs_js_log("resizing pano");
		      	google.maps.event.trigger(nxs_js_panos["pano_<?php echo $placeholderid; ?>"], "resize"); 
			      // nxs_js_log("pano is resized");
		      }, 
		      500
		    );
        
        panorama_currenttick_in_slide_<?php echo $placeholderid;?> = -1;
        
        
        <?php 
				if ($disable_whenloggedon != "" && is_user_logged_in())
				{
					?>
					nxs_js_alert("Tour is disabled because of configuration for logged on users");
					return;
					<?php
				}
				?>
        
       	if (numofpanoslides > 0) 
       	{	
       		nxs_js_animatepano_<?php echo $placeholderid;?>();
       	}
       	else
     		{
     			nxs_js_log("no slides / animations found");
     		}
			}
			
			function nxs_js_widget_googlepano_init_<?php echo $placeholderid; ?>_initmapscript()
			{
				nxs_js_log('initing');
				
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
						jQuery(document).trigger("nxs_js_googlepano_scriptloaded");
					};
				}
				else if (nxs_js_panolazyloading)
				{
					jQuery(document).bind
					(
						"nxs_js_googlepano_scriptloaded", 
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
					nxs_js_log('ready!');
					nxs_js_widget_googlepano_init_<?php echo $placeholderid; ?>_initmapscript();
				}
			);
			
			//
			//
			//
			
			function nxs_js_animatepano_<?php echo $placeholderid;?>()
			{
				//nxs_js_log('pano animate');
				// stop listening to pov modifications as we will make a pov modification ourselves....
				google.maps.event.clearListeners(nxs_js_panos["pano_<?php echo $placeholderid; ?>"], 'pov_changed');

				var framespersecond = <?php echo $framespersecond; ?>;	// fps, increase for smoother experience
				var msecspertick = Math.round(1000 / framespersecond);
				
				// initialize variables if this is the first tick of the animation
				if (panorama_currenttick_in_slide_<?php echo $placeholderid;?> == 0)
				{
					var currentheading = nxs_js_panos["pano_<?php echo $placeholderid; ?>"].getPov().heading;
					panorama_lastheadingpreviousanimation_<?php echo $placeholderid;?> = currentheading;
					//nxs_js_log("current heading:" + currentheading);
					
					var currentpitch = nxs_js_panos["pano_<?php echo $placeholderid; ?>"].getPov().pitch;
					panorama_lastpitchpreviousanimation_<?php echo $placeholderid;?> = currentpitch;
					
					var currentzoom = nxs_js_panos["pano_<?php echo $placeholderid; ?>"].getPov().zoom;
					panorama_lastzoompreviousanimation_<?php echo $placeholderid;?> = currentzoom;
				}

				// slide / animation				
				var currentpanoslide = panoslides[panoslidenr];
				var animation = currentpanoslide.animation;
				
				var from_heading = panorama_lastheadingpreviousanimation_<?php echo $placeholderid;?>;	// the last heading of the previous animation
				var to_heading = currentpanoslide.to_heading;
				
				var from_pitch = panorama_lastpitchpreviousanimation_<?php echo $placeholderid;?>;	// the last pitch of the previous animation
				var to_pitch = currentpanoslide.to_pitch;
				
				var from_zoom = panorama_lastzoompreviousanimation_<?php echo $placeholderid;?>;	// the last zoom of the previous animation
				var to_zoom = currentpanoslide.to_zoom;
				
				var direction = currentpanoslide.direction;
				var slideanimationdurationinmsecs = currentpanoslide.durationinmsecs;			
				var totalnumofticks_in_slide = Math.round(slideanimationdurationinmsecs / msecspertick);

				if (panorama_currenttick_in_slide_<?php echo $placeholderid;?> == -1)
				{
					// first time!
					panorama_currenttick_in_slide_<?php echo $placeholderid;?> = totalnumofticks_in_slide;
				}

				if (animation == "circular180")	// // meaning: in beginning accelerate faster and faster till we are half way, then decelerate as we reach the destination
				{
					var degrees = 180 + ((panorama_currenttick_in_slide_<?php echo $placeholderid;?> / totalnumofticks_in_slide) * 179);	// from 180..359 degrees
					if (direction == "anticlockwise")
					{
						degrees = degrees + 180;
					}
					
					var radians = degrees * (Math.PI/180);
					// cos goes from -1 to 1, we want a scope of 0..1, therefore divide by 2 and increment by 1
					var factor = ((Math.cos(radians) + 1) / 2);
					
					var delta_heading = to_heading - from_heading;
					var progress = delta_heading * factor;
					var heading = from_heading + progress;
					
					var delta_pitch = to_pitch - from_pitch;
					var progress = delta_pitch * factor;
					var pitch = from_pitch + progress;
					
					var delta_zoom = to_zoom - from_zoom;
					var progress = delta_zoom * factor;
					var zoom = from_zoom + progress;
					
					//nxs_js_log("fromzoom:" + from_zoom + "/ tozoom:" + to_zoom + "/ currentzoom:" + zoom);
					
					nxs_js_panos["pano_<?php echo $placeholderid; ?>"].setPov
					(
						{
							heading:heading,
							pitch:pitch,
							zoom:zoom
						}
					);
				}

				// njs tick				
				panorama_currenttick_in_slide_<?php echo $placeholderid;?>++;
				
				if (panorama_currenttick_in_slide_<?php echo $placeholderid;?> >= totalnumofticks_in_slide)
				{
					// keep static (trigger to wait and to show the njs pano soon)
					// repeat loop
					panorama_currenttick_in_slide_<?php echo $placeholderid;?> = 0;
					// increase the slidenr
					panoslidenr++;
					if (panoslidenr >= numofpanoslides)
					{
						// loop slides
						panoslidenr = 0;
					}
				}
				
				if (panorama_is_dirty_<?php echo $placeholderid;?>)
				{
					// end (event listener was already de-registered)
				}
				else
				{
					// loop!
					setTimeout
				  (
				  	function()
				  	{
				  		// repeat endlessly
				  		nxs_js_animatepano_<?php echo $placeholderid;?>();
				  		
				  		// wellicht is dit zeer cpu intensieve bewerking?
				  		
				  		// detect if user modifies the pov
				  		google.maps.event.addListener
						  (
						  	nxs_js_panos["pano_<?php echo $placeholderid; ?>"], 
						  	'pov_changed', 
						  	function() 
						  	{
						  		//nxs_js_log("pov changed");
						  		panorama_is_dirty_<?php echo $placeholderid;?> = true;
						      // stop listening to pov modifications as we will make a pov modification ourselves....
									google.maps.event.clearListeners(nxs_js_panos["pano_<?php echo $placeholderid; ?>"], 'pov_changed');
						  	}
						  );
						},
						msecspertick	// todo: make configurable
					);
				}
			}
			
			
		</script>
		<!-- -->
		<div class="nxs-clear"></div>
		<?php
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

function nxs_widgets_googlebusinessphoto_initplaceholderdata($args)
{
	extract($args);

	$args['unistyle'] = nxs_unistyle_getdefaultname(nxs_widgets_googlebusinessphoto_getunifiedstylinggroup());
	
	$subargs = array();
	$subargs["nxsposttype"] = "genericlist";
	$subargs["nxssubposttype"] = "googlebusphotoslides";	// NOTE!
	$subargs["poststatus"] = "publish";
	$subargs["titel"] = "google business photo items";
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
		nxs_webmethod_return_nack("unsupported result");
	}
			
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
