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
	
	$address = $mixedattributes['address'];
	$maptypeid = $mixedattributes['maptypeid'];
	$zoom = $mixedattributes['zoom'];
	$minheight = $mixedattributes['minheight'];
	$lat = $mixedattributes['lat'];
	$lng = $mixedattributes['lng'];

	if ($lat == "")
	{
		$lat = "52.0";
	}
	if ($lng == "")
	{
		$lng = "5.12";
	}
	if ($zoom == "")
	{
		$zoom = "9";
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
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs); 
	
	//
	// render actual control / html
	//
	ob_start();
	?>
		<div id="map_canvas_<?php echo $placeholderid;?>" class="nxs-runtime-autocellsize nxs-minheight <?php echo $minheight_cssclass; ?>"></div>

		<script type='text/javascript'>
			
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
        var marker = new google.maps.Marker(
        {
      		position: new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>),
      		map: nxs_js_maps["map_<?php echo $placeholderid; ?>"],
    		});
			}
			
			function nxs_ext_widget_googlemap_init_<?php echo $placeholderid; ?>_initmapscript()
			{
				if (!nxs_js_mapslazyloaded && !nxs_js_mapslazyloading)
				{
					//nxs_js_log('loading...');
					
					nxs_js_mapslazyloading = true;
					
					var w = window;
					var d = w.document;
					var script = d.createElement('script');
					script.setAttribute('src', 'https://maps.google.com/maps/api/js?v=3&sensor=true&callback=mapOnLoad');
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
	
	$html = ob_get_contents();
	ob_end_clean();
	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

//
// het eerste /hoofd/ scherm dat wordt getoond in de popup als de gebruiker
// het editen van een placeholder initieert
//
function nxs_widgets_googlemap_home_rendersheet($args)
{
	//
	extract($args);
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);

	$address = $temp_array['address'];
	$maptypeid = $temp_array['maptypeid'];
	$zoom = $temp_array['zoom'];
	$minheight = $temp_array['minheight'];
	$lat = $temp_array['lat'];
	$lng = $temp_array['lng'];

	if ($lat == "")
	{
		$lat = "52.0";
	}
	if ($lng == "")
	{
		$lng = "5.12";
	}
	if ($zoom == "")
	{
		$zoom = "9";
	}
	if ($maptypeid == "")
	{
		$maptypeid = "ROADMAP";
	}

	// clientpopupsessiondata bevat key values van de client side, deze overschrijven reeds bestaande variabelen
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);

	$result = array();
	$result["result"] = "OK";
	
	ob_start();

	?>
    <div class="nxs-admin-wrap">
      <div class="block">
      
       	<?php nxs_render_popup_header(nxs_l18n__('Google Map','nxs_td')); ?>

				<div class="nxs-popup-content-canvas-cropper">
					<div class="nxs-popup-content-canvas">
		
						<div class="content2">
		            <div class="box">
		                <div class="box-title">
		                    <h4><?php nxs_l18n_e('Address','nxs_td'); ?></h4>
		                 </div>
		                <div class="box-content">
		                	<a href='#' onclick='nxs_js_ext_widget_googlemap_search_map(); return false;' class='nxsbutton1 nxs-float-right'><?php nxs_l18n_e('Update map','nxs_td'); ?></a>
		                	<input id="address" class='nxs-float-left nxs-width70' placeholder='<?php nxs_l18n_e('Address sample placeholder','nxs_td'); ?>' name="address" type='text' value='<?php echo $address; ?>' />
		                </div>
		            </div>
		            <div class="nxs-clear"></div>
		        </div> <!--END content-->
		        
		        <div id="map_canvas_popup_<?php echo $placeholderid;?>" style="width:100%; height: 300px; minheight: 300px;">
						</div>
						
						<?php
							nxs_requirepopup_optiontype("select");
							$sub_optionvalues = array
							(
								"id" 				=> "minheight",
								"type" 				=> "select",
								"label" 			=> nxs_l18n__("Minimum height", "nxs_td"),
								"dropdown" 			=> nxs_style_getdropdownitems("minheight")
							);
							$sub_args = array();
							$sub_runtimeblendeddata = array
							(
								"minheight" => $minheight,
							);
							nxs_popup_optiontype_select_renderhtmlinpopup($sub_optionvalues, $sub_args, $sub_runtimeblendeddata);
						?>
					</div>
				</div>

        <div class="content2">
          <div class="box">
            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e('Save','nxs_td'); ?></a>
            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e('OK','nxs_td'); ?></a>
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e('Cancel','nxs_td'); ?></a>
           </div>
          <div class="nxs-clear margin"></div>
        </div> <!--END content-->
        
      </div> <!--END block-->
    </div>

    <script type='text/javascript'>
		
		function nxs_js_setpopupdatefromcontrols()
		{
			nxs_js_popup_setsessiondata('address', jQuery('#address').val());
			nxs_js_popup_setsessiondata('maptypeid', map_popup_<?php echo $placeholderid; ?>.getMapTypeId());			
			nxs_js_popup_setsessiondata('zoom', map_popup_<?php echo $placeholderid; ?>.getZoom());
			nxs_js_popup_setsessiondata('minheight', jQuery('#minheight').val());
			nxs_js_popup_setsessiondata('lat', map_popup_<?php echo $placeholderid; ?>.getCenter().lat());
			nxs_js_popup_setsessiondata('lng', map_popup_<?php echo $placeholderid; ?>.getCenter().lng());
		}
		
		function nxs_js_ext_widget_googlemap_search_map()
		{
			var adres;
			adres = jQuery('#address').val();
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
		      else 
		      {
		        nxs_js_alert("<?php nxs_l18n_e('Location was not found','nxs_td'); ?> " + status);
		      }
		    }
			);
		}
		
		function nxs_js_savegenericpopup()
		{
			nxs_js_setpopupdatefromcontrols();
			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQuery.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updateplaceholderdata",
						"placeholderid": "<?php echo $placeholderid;?>",
						"postid": "<?php echo $postid;?>",
						"placeholdertemplate": "googlemap",
						"address": nxs_js_popup_getsessiondata('address'),
						"maptypeid": nxs_js_popup_getsessiondata('maptypeid'),
						"zoom": nxs_js_popup_getsessiondata('zoom'),
						"lat": nxs_js_popup_getsessiondata('lat'),
						"lng": nxs_js_popup_getsessiondata('lng'),
						"minheight": nxs_js_popup_getsessiondata('minheight')
					},
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						//nxs_js_log(response);
						if (response.result == "OK")
						{
							// update UI, the 'current' id will be overriden because null is specified as third parameter
							nxs_js_rerender_row_for_placeholder("<?php echo $postid;?>", "<?php echo $placeholderid;?>");
														
							// close the pop up
							nxs_js_closepopup_unconditionally();
						}
						else
						{
							nxs_js_popup_notifyservererror();
							nxs_js_log(response);
						}
					},
					error: function(response)
					{
						nxs_js_popup_notifyservererror();
						nxs_js_log(response);
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
				
			  var myOptions = 
			  {
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
					}
				);
	      
	      google.maps.event.addListener
	      (
	     		map_popup_<?php echo $placeholderid; ?>, 'maptypeid_changed', function() 
		      {
		      	//nxs_js_log('map type changed');
		   		 	nxs_js_popup_sessiondata_make_dirty();
					}
	      )
	      
	  		jQuery("#address").bind("keyup.defaultenter", function(e)
				{
					if (e.keyCode == 13)
					{
						nxs_js_ext_widget_googlemap_search_map();
					}
				});
				
				jQuery("#address").focus();
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
	$html = ob_get_contents();
	ob_end_clean();
	$result["html"] = $html;
	return $result;
}

function nxs_widgets_googlemap_initplaceholderdata($args)
{
	extract($args);

	$args['address'] = nxs_l18n__('Initial location','nxs_td');
	$args['lat'] = nxs_l18n__('Initial latitude','nxs_td');
	$args['lng'] = nxs_l18n__('Initial longitude','nxs_td');
	$args['maptypeid'] = nxs_l18n__('Initial maptypeid','nxs_td');
	$args['zoom'] = nxs_l18n__('Initial zoom','nxs_td');
	$args['minheight'] = "200";
	
	nxs_widgets_googlemap_updateplaceholderdata($args);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

//
// wordt aangeroepen bij het opslaan van data van deze placeholder
//
function nxs_widgets_googlemap_updateplaceholderdata($args)
{
	extract($args);
	
	$temp_array = array();
	
	// its required to also set the 'type' (used when dragging an item from the toolbox to existing placeholder)
	$temp_array['type'] = 'googlemap';
	
	// placeholder specifieke data
	$temp_array['address'] = $address;
	$temp_array['maptypeid'] = $maptypeid;
	$temp_array['minheight'] = $minheight;
	$temp_array['zoom'] = $zoom;
	$temp_array['lat'] = $lat;
	$temp_array['lng'] = $lng;
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $temp_array);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
