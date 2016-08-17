<?php

function nxs_widgets_googlemap_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

function nxs_widgets_googlemap_gettitle()
{
	return nxs_l18n__("Google Map[nxs:widgettitle]", "nxs_td");
}

// Define the properties of this widget
function nxs_widgets_googlemap_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$options = array
	(
		"sheettitle" => nxs_widgets_googlemap_gettitle(),
		"sheeticonid" => nxs_widgets_googlemap_geticonid(),
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/googlemap-widget/"),
		"fields" => array
		(
			// TITLE
			
			// array( 
			// 	"id" 				=> "wrapper_title_begin",
			// 	"type" 				=> "wrapperbegin",
			// 	"label" 			=> nxs_l18n__("Title", "nxs_td"),
			// 	"initial_toggle_state"	=> "closed",
			// ),
			
			// array( 
			// 	"id" 				=> "title",
			// 	"type" 				=> "input",
			// 	"label" 			=> nxs_l18n__("Title", "nxs_td"),
			// 	"placeholder" 		=> nxs_l18n__("Title goes here", "nxs_td"),
			// 	"localizablefield"	=> true
			// ),
			// array(
			// 	"id" 				=> "title_heading",
			// 	"type" 				=> "select",
			// 	"label" 			=> nxs_l18n__("Title importance", "nxs_td"),
			// 	"dropdown" 			=> nxs_style_getdropdownitems("title_heading")
			// ),
			
			// array(
			// 	"id" 				=> "title_alignment",
			// 	"type" 				=> "radiobuttons",
			// 	"subtype" 			=> "halign",
			// 	"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
			// ),
						
			// array(
			// 	"id" 				=> "title_fontsize",
			// 	"type" 				=> "select",
			// 	"label" 			=> nxs_l18n__("Override title fontsize", "nxs_td"),
			// 	"dropdown" 			=> nxs_style_getdropdownitems("fontsize")
			// ),
			// array(
			// 	"id" 				=> "title_heightiq",
			// 	"type" 				=> "checkbox",
			// 	"label" 			=> nxs_l18n__("Row align titles", "nxs_td"),
			// 	"tooltip" 			=> nxs_l18n__("When checked, the widget's title will participate in the title alignment of other partipating widgets in this row", "nxs_td")
			// ),
		
			// array( 
			// 	"id" 				=> "wrapper_title_end",
			// 	"type" 				=> "wrapperend"
			// ),
			
			
			
			// CONFIGURATION
			
			array( 
				"id" 				=> "wrapper_googlemap_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("googlemap settings", "nxs_td"),
			),

			array(
				"id" 				=> "address",
				"type" 				=> "input",
				"visibility" 		=> "hidden",
				"localizablefield"	=> true
			),

			array(
				"id" 				=> "maptypeid",
				"type" 				=> "input",
				"visibility" 		=> "hidden",
				"localizablefield"	=> true
			),

			array(
				"id" 				=> "zoom",
				"type" 				=> "input",
				"visibility" 		=> "hidden",
				"localizablefield"	=> true
			),

			array(
				"id" 				=> "lat",
				"type" 				=> "input",
				"visibility" 		=> "hidden",
				"localizablefield"	=> true
			),

			array(
				"id" 				=> "lng",
				"type" 				=> "input",
				"visibility" 		=> "hidden",
				"localizablefield"	=> true
			),
		
			array(
				"id" 				=> "googlemap_visualization",
				"altid" 			=> array(
					"address" => "address",
					"maptypeid" => "maptypeid",
					"zoom" => "zoom",
					"lat" => "lat",
					"lng" => "lng",
				),
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_googlemap_map_popupcontent",
				"label" 			=> nxs_l18n__("Locate Address", "nxs_td"),
				"layouttype"		=> "custom",
				"localizablefield"	=> "true"
			),

			array(
				"id" 				=> "minheight",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Minimum height", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("minheight")
			),
				
			array( 
				"id" 				=> "wrapper_googlemap_end",
				"type" 				=> "wrapperend"
			),		
		),
	);
	
	// nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_googlemap_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";

	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);

	$mixedattributes = array_merge($temp_array, $args);

	$mixedattributes = nxs_localization_localize($mixedattributes);

	if ($lat == "")
	{
		$lat = "40.8";
	}
	if ($lng == "")
	{
		$lng = "-74";
	}
	if ($zoom == "")
	{
		$zoom = "14";
	}
	if ($maptypeid == "")
	{
		$maptypeid = "ROADMAP";
	}
	
	if ($minheight == "")
	{
		$minheight = "200";
	}

	$minheight_cssclass = nxs_getcssclassesforlookup("nxs-minheight-", $minheight);
	
	global $nxs_global_placeholder_render_statebag;
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-google-map";

	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["enable_decoratewidget"] = false;
	$hovermenuargs["enable_deletewidget"] = true;
	$hovermenuargs["enable_deleterow"] = false;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs); 
	
	$htmltitle = nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, "", "");

	$sitemeta = nxs_getsitemeta_internal(false);
	$apikey = trim($sitemeta["googlemapsapikey"]);

	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */

	nxs_ob_start();
	?>
		<div id="map_canvas_<?php echo $placeholderid;?>" class="nxs-runtime-autocellsize nxs-minheight <?php echo $minheight_cssclass; ?>"></div>
		<div id="alt_map_canvas_<?php echo $placeholderid;?>" style="display:none; padding: 5px; margin: 5px;" class="nxs-default-p nxs-applylinkvarcolor nxs-padding-bottom0 nxs-align-left">
			<?php
			if (is_user_logged_in())
			{
				if ($apikey == "")
				{
					// its not set; explain its mandatory 
					?>
					Google Maps temporary placeholder<br />
					<br />
					Problem; Google Maps API key not yet configured<br />
					<br />
					<a target='_blank' style='backgroundcolor: white; color: blue; text-decoration: underline;' href='https://nexusthemes.com/support/nexus-themes-widgets/google-map-widget/?reason=noapikeyset'>Click here to learn how to configure the Google Maps API key</a><br />
					<a href='#' style='color: blue; text-decoration: underline;' onclick='nxs_js_popup_site_neweditsession("integrationshome"); return false;'>Click here to configure your Google Maps API Key</a>
					<?php
				}
				else
				{
					// its set; explain its not (yet) valid
					?>
					Google Maps temporary placeholder<br />
					<br />
					Problem; Google Maps API key not (yet?) valid<br />
					<br />
					Possible reasons;<br />
					1) It can take up to 5 mins before the API key starts to work. If you 
					updated the existing or created a new key, please be patient and wait 5 mins
					and retry.<br />
					2) Perhaps you entered an incorrect API key (p.e. one that cannot be used
					for this domain, or perhaps you made a typo when copy pasting it)<br />
					3) Perhaps you exceeded the free quota that Google provides. In that case you
					could consider upgrading to the paid version.<br />
					<br />
					<a href='#' style='color: blue; text-decoration: underline;' onclick='nxs_js_popup_site_neweditsession("integrationshome"); return false;'>Click here to re-configure your Google Maps API Key</a><br />
					<a target='_blank' style='backgroundcolor: white; color: blue; text-decoration: underline;' href='https://nexusthemes.com/support/nexus-themes-widgets/google-map-widget/?reason=invalidapikeyset'>Click here to learn how to configure the Google Maps API key</a>
					
					<?php
				}
			}
			else
			{
				?>
				Google Maps temporary placeholder<br />
				(login to see how to fix this)
				<?php
			}
			?>
		</div>
		<script type='text/javascript'>
			
			jQuery(window).bind 
			(
				"nxs_js_trigger_googlemapsapikeyinvalid", 
				function(e) 
				{
					// if the google maps api key is invalid, show a tip on how to fix it,
					// instead of the default "oops" from Google
					//var hinthtml = "<div>Google Maps API not yet configured</div>";
					jQuery("#map_canvas_<?php echo $placeholderid;?>").hide();
					jQuery("#alt_map_canvas_<?php echo $placeholderid;?>").show();
					// remove the front end options to configure this widget
					jQuery("#alt_map_canvas_<?php echo $placeholderid;?>").closest(".nxs-placeholder").find(".nxs-hover-menu").remove();
				}
			);
			
			function nxs_ext_widget_googlemap_init_<?php echo $placeholderid; ?>()
			{
				var myOptions = 
				{
				  	scrollwheel: false,
	          		center: new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>),
	          		zoom: <?php echo $zoom; ?>,
	          		mapTypeId: google.maps.MapTypeId.<?php echo strtoupper($maptypeid); ?>
        		};
        
        		nxs_js_maps["map_<?php echo $placeholderid; ?>"] = new google.maps.Map(document.getElementById("map_canvas_<?php echo $placeholderid; ?>"), myOptions);
        
		        // add marker
		        var marker = new google.maps.Marker
		        ({
	      			position: new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>),
	      			map: nxs_js_maps["map_<?php echo $placeholderid; ?>"],
	    		});
			}
			
			function nxs_ext_widget_googlemap_init_<?php echo $placeholderid; ?>_initmapscript()
			{
				if (!nxs_js_mapslazyloaded && !nxs_js_mapslazyloading)
				{
					// intercept error messages being logged to the console,
					// interpret the fact if the google maps key is not valid,
					// and if so, broadcast an event
					(
						function () 
						{
						  var err = console.error;
						  console.error = function () 
						  {
						  	if (arguments[0] != null)
						  	{
						  		msg = arguments[0];
						  		if (msg.indexOf("Google Maps API error: MissingKeyMapError") > -1)
						  		{
						  			nxs_js_log("broadcasting nxs_js_trigger_googlemapsapikeyinvalid");
						  			jQuery(window).trigger('nxs_js_trigger_googlemapsapikeyinvalid');
						  			//return;
						  		}
						  		else if (msg.indexOf("Google Maps API error: InvalidKeyMapError") > -1)
						  		{
						  			nxs_js_log("broadcasting nxs_js_trigger_googlemapsapikeyinvalid");
						  			jQuery(window).trigger('nxs_js_trigger_googlemapsapikeyinvalid');
						  			//return;
						  		}
						  		
						  	}
						  	
						    err.apply(this, Array.prototype.slice.call(arguments));
						  };
						}()
					);
					
					//nxs_js_log('loading...');
					
					nxs_js_mapslazyloading = true;
					
					var w = window;
					var d = w.document;
					var script = d.createElement('script');
					script.setAttribute('src', 'https://maps.googleapis.com/maps/api/js?v=3&key=<?php echo $apikey; ?>&sensor=true&callback=mapOnLoad');
					d.documentElement.firstChild.appendChild(script);
					w.mapOnLoad = function () 
					{
						// redraw this specific widget
						nxs_ext_widget_googlemap_init_<?php echo $placeholderid; ?>();

						//nxs_js_log('maps script is now loaded!');

						// prevent other maps from listening to the event
						nxs_js_mapslazyloaded = true;
						nxs_js_mapslazyloading = false;

						// trigger event (redraw possible other widgets), see http://weblog.bocoup.com/publishsubscribe-with-jquery-custom-events/
						jQuery(document).trigger("nxs_ext_googlemap_scriptloaded");
					};
				}
				else if (nxs_js_mapslazyloading)
				{
					jQuery(document).bind
					(
						"nxs_ext_googlemap_scriptloaded", 
						function() 
						{ 
							nxs_ext_widget_googlemap_init_<?php echo $placeholderid; ?>();
						}
					);
				}
				else
				{
					nxs_ext_widget_googlemap_init_<?php echo $placeholderid; ?>();
				}
			}
			
			jQuery(document).ready
			(
				function() 
				{
					nxs_ext_widget_googlemap_init_<?php echo $placeholderid; ?>_initmapscript();
				}
			);
		</script>
	
	<?php 
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

function nxs_googlemap_map_popupcontent($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);

	$address = $$altid["address"];
	$maptypeid = $$altid["maptypeid"];
	$zoom = $$altid["zoom"];
	$lat = $$altid["lat"];
	$lng = $$altid["lng"];

	if ($maptypeid == "")
	{
		$maptypeid = "ROADMAP";
	}
	if ($zoom == "")
	{
		$zoom = "14";
	}
	if ($lat == "")
	{
		$lat = "40.8";
	}
	if ($lng == "")
	{
		$lng = "-74";
	}
	
	nxs_ob_start();
	?>

	<div class="content2">
        <div class="box">
            <div class="box-title">
                <h4><?php echo $label; ?></h4>
				<?php if ($tooltip != ""){ ?>
					<span class="info">?
						<div class="info-description"><?php echo $tooltip; ?></div>
					</span>;
				<?php } ?>
             </div>
            <div class="box-content">
            	<a href='#' onclick='nxs_js_ext_widget_googlemap_search_map(); return false;' class='nxsbutton1 nxs-float-right'><?php nxs_l18n_e('Update map','nxs_td'); ?></a>
            	<input id="<?php echo $id; ?>" class='nxs-float-left nxs-width70' placeholder='<?php nxs_l18n_e('Address sample placeholder','nxs_td'); ?>' name="address" type='text' value='<?php echo $address; ?>' />
            </div>
        </div>
        <?php
				$sitemeta = nxs_getsitemeta_internal(false);
				$apikey = trim($sitemeta["googlemapsapikey"]);
        if ($apikey == "")
        {
        	?>
	        <div class="nxs-clear"></div>
	       	<div style='margin-top: 10px;'>
	       		Note; to use the search function a (free) <a target='_blank' style='backgroundcolor: white; color: blue; text-decoration: underline;' href='https://nexusthemes.com/support/nexus-themes-widgets/google-map-widget/?reason=noapikeyset'>Google Maps API key is required (learn more)</a>
	       	</div>
	      	<?php
	    	}
	      ?>  
        <div class="nxs-clear"></div>
	</div> <!--END content-->

    <div id="map_canvas_popup_<?php echo $placeholderid;?>" style="width:100%; height: 300px; minheight: 300px;"></div>

    <script type="text/javascript">
    	var address = jQuery('#<?php echo $altid["address"]; ?>').val();
    	var maptypeid = jQuery('#<?php echo $altid["maptypeid"]; ?>').val();
    	var zoom = jQuery('#<?php echo $altid["zoom"]; ?>').val();
    	var lat = jQuery('#<?php echo $altid["lat"]; ?>').val();
    	var lng = jQuery('#<?php echo $altid["lng"]; ?>').val();

    	function nxs_js_ext_widget_googlemap_update_hidden_fields()
		{
			address = jQuery('#<?php echo $id; ?>').val();
			maptypeid = map_popup_<?php echo $placeholderid; ?>.getMapTypeId();			
			zoom = map_popup_<?php echo $placeholderid; ?>.getZoom();
			lat = map_popup_<?php echo $placeholderid; ?>.getCenter().lat();
			lng = map_popup_<?php echo $placeholderid; ?>.getCenter().lng();

			jQuery('#<?php echo $altid["address"]; ?>').val(address);
	    	jQuery('#<?php echo $altid["maptypeid"]; ?>').val(maptypeid);
	    	jQuery('#<?php echo $altid["zoom"]; ?>').val(zoom);
	    	jQuery('#<?php echo $altid["lat"]; ?>').val(lat);
	    	jQuery('#<?php echo $altid["lng"]; ?>').val(lng);
		}

    function nxs_js_ext_widget_googlemap_search_map()
		{
			var adres;
			adres = jQuery('#<?php echo $id; ?>').val();
			
			if (adres == '')
			{			
     		nxs_js_alert("<?php nxs_l18n_e('Please enter an address first','nxs_td'); ?>");
     		jQ_nxs("#googlemap_visualization").focus();
     		return;
     	}
			
			
			var geocoder = new google.maps.Geocoder();
			geocoder.geocode
			(
				{
					'address': adres
				}, 
				function(results, status) 
				{
		      if (status == google.maps.GeocoderStatus.OK) 
		      {
		      	//nxs_js_log(results);

		        map_popup_<?php echo $placeholderid; ?>.setCenter(results[0].geometry.location);
		        map_popup_<?php echo $placeholderid; ?>.setZoom(17);
		        
		        nxs_js_alert("<?php nxs_l18n_e('Address was found; map is updated','nxs_td'); ?>");
		      } 
		      else if (status == google.maps.GeocoderStatus.ZERO_RESULTS)
	      	{
	      		var searchedfor = jQuery('#<?php echo $id; ?>').val();
	      		nxs_js_alert("<?php nxs_l18n_e('No results found for','nxs_td'); ?>" + " '" + searchedfor + "'");
	      	}
		      else if (status == google.maps.GeocoderStatus.REQUEST_DENIED)
	      	{
	      		var fix = "<a href='#' style='color: blue; text-decoration: underline;' onclick='nxs_js_popup_site_neweditsession(\"integrationshome\"); return false;'>Click here to configure your Google Maps API Key</a>";
	      		nxs_js_alert_sticky("Request denied; configure the Google Maps API key first.<br /><br />" + fix);
	      	}
		      else 
		      {
		        nxs_js_alert("<?php nxs_l18n_e('Location was not found','nxs_td'); ?> " + status);
		      }
		    }
			);
		}

		var map_popup_<?php echo $placeholderid; ?>;
		
		function nxs_js_initializetwitterwidget_trigger_<?php echo $placeholderid; ?>()
		{
			google.maps.event.trigger(map_popup_<?php echo $placeholderid; ?>, "resize");
			
			var location = new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>);
			map_popup_<?php echo $placeholderid; ?>.setCenter(location);
		}
		
		//var initialmarker = null;
		var latestmarker = null;
		
		function nxs_js_execute_after_popup_shows()
		{
			// sometimes the map initializes, but fails to resize properly, in that case we need to 
			// repeat after 1 secs
		  	setTimeout(nxs_js_initializetwitterwidget_trigger_<?php echo $placeholderid; ?>, 500);
		  	// repeat after 5 sec
		  	setTimeout(nxs_js_initializetwitterwidget_trigger_<?php echo $placeholderid; ?>, 1000);
			
			if (nxs_js_mapslazyloaded)
			{
				//nxs_js_log('Reeds ingeladen');
				
				var myOptions =  {
			  		streetViewControl: false,
	        		center: new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>),
	        		zoom: <?php echo $zoom; ?>,
	        		mapTypeId: google.maps.MapTypeId.<?php echo strtoupper($maptypeid); ?>
	      		};
	      		map_popup_<?php echo $placeholderid; ?> = new google.maps.Map(document.getElementById("map_canvas_popup_<?php echo $placeholderid; ?>"), myOptions);
	      
	      		google.maps.event.addListener
	      		(
		      		map_popup_<?php echo $placeholderid; ?>, 'zoom_changed', function() 
		      		{
		      			//nxs_js_log('zoom changed');
		   		 		nxs_js_popup_sessiondata_make_dirty();
		   		 		nxs_js_ext_widget_googlemap_update_hidden_fields();
					}
				);
				
				google.maps.event.addListener
	      		(
		      		map_popup_<?php echo $placeholderid; ?>, 'center_changed', function() 
		      		{
 						// update the marker
			    		// add marker
			      		var position = map_popup_<?php echo $placeholderid; ?>.getCenter();
			      		if (position.lat() != null)
			      		{
			      			if (latestmarker == null)
			      			{
				      			latestmarker = new google.maps.Marker
								(
						      		{
							    		position: position,
						    			map: map_popup_<?php echo $placeholderid; ?>,
						  			}
					  			);
						  	}
						  	else
					  		{
					  			latestmarker.setPosition(position);
					  		}
			  			}
			      		//nxs_js_log('map location changed');
			   		 	nxs_js_popup_sessiondata_make_dirty();
			   		 	nxs_js_ext_widget_googlemap_update_hidden_fields();
					}
				);
	      
	      		google.maps.event.addListener
	      		(
	     			map_popup_<?php echo $placeholderid; ?>, 'maptypeid_changed', function() 
		      		{
		      			//nxs_js_log('map type changed');
		   		 		nxs_js_popup_sessiondata_make_dirty();
		   		 		nxs_js_ext_widget_googlemap_update_hidden_fields();
					}
	      		)
	      
	  			jQuery("#<?php echo $id; ?>").bind("keyup.defaultenter", function(e)
				{
					if (e.keyCode == 13)
					{
						nxs_js_ext_widget_googlemap_search_map();
					}
				});
				
				jQuery("#<?php echo $id; ?>").focus();
			}
			else
			{
				//nxs_js_log('Nog niet ingeladen');
				// wait and do recursive call ...
				setTimeout(nxs_js_execute_after_popup_shows,500);
			}
		}

    </script>

	<?php

	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

function nxs_widgets_googlemap_initplaceholderdata($args)
{
	extract($args);

	$args['minheight'] = "200-0";
	$args['maptypeid'] = "ROADMAP";
	$args['zoom'] = "14";
	$args['lat'] = "40.8";
	$args['lng'] = "-74";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}
?>
