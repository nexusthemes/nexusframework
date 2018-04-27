<?php

function nxs_widgets_pagepopup_geticonid() {
	return "nxs-icon-pagepopup";
}

function nxs_widgets_pagepopup_gettitle() {
	return nxs_l18n__("Page popup", "nxs_td");
}

function nxs_widgets_pagepopup_registerhooksforpagewidget($args)
{	
	$pagedecoratorid = $args["pagedecoratorid"];
	$pagedecoratorwidgetplaceholderid = $args["pagedecoratorwidgetplaceholderid"];
		
	global $nxs_pagepopup_pagedecoratorid;
	$nxs_pagepopup_pagedecoratorid = $pagedecoratorid;
	global $nxs_pagepopup_pagedecoratorwidgetplaceholderid;
	$nxs_pagepopup_pagedecoratorwidgetplaceholderid = $pagedecoratorwidgetplaceholderid;
	
	add_action('nxs_beforeend_head', 'nxs_widgets_pagepopup_beforeend_head');
}

// kudos to http://css-tricks.com/perfect-full-page-background-image/
function nxs_widgets_pagepopup_beforeend_head()
{
	global $nxs_pagepopup_pagedecoratorid;
	global $nxs_pagepopup_pagedecoratorwidgetplaceholderid;

	$placeholderid = $nxs_pagepopup_pagedecoratorwidgetplaceholderid;
	
	$metadata = nxs_getwidgetmetadata($nxs_pagepopup_pagedecoratorid, $nxs_pagepopup_pagedecoratorwidgetplaceholderid);
	extract($metadata);
	
	// Linked title
	if ($destination_articleid != "") 
	{
		$destination_url = nxs_geturl_for_postid($destination_articleid);
	}
	else
	{
		$destination_url = "http://www.example.org";
	}
	
	// prevent administrators from seeing annoying edit features
	$destination_url = nxs_addqueryparametertourl_v2($destination_url, "nxs_impersonate", "anonymous", false, true);
	
	$trigger_on_exit = false;
	$trigger_on_js = false;

	if ($popup_trigger != "")
	{
		if ($popup_trigger == "exit")
		{
			$trigger_on_exit = true;
			$delaypopup_milliseconds = 0;
		}
		else if ($popup_trigger == "js")
		{
			$trigger_on_js = true;
			$delaypopup_milliseconds = 0;
		}
		else
		{
			$delaypopup_milliseconds = $popup_trigger * 1000;
		}
	}
	else
	{
		$delaypopup_milliseconds = 1;
	}
	
	?>
	<style>
		#pagepopupiframe .nxs-one-whole  
		{
			width: 300px;
		}
		.nxs-viewport-gt-319	#pagepopupiframe .nxs-one-whole  
		{
			width: 300px;
		}
		.nxs-viewport-gt-479 	#pagepopupiframe .nxs-one-whole 
		{
			width: 470px;
		}
		.nxs-viewport-gt-719 	#pagepopupiframe .nxs-one-whole 
		{
			width: 673px;
		}
		.nxs-viewport-gt-959 	#pagepopupiframe .nxs-one-whole 
		{
			width: 673px;
		}
		.nxs-viewport-gt-1199 	#pagepopupiframe .nxs-one-whole 
		{
			width: 673px;
		}
		.nxs-viewport-gt-1439	#pagepopupiframe .nxs-one-whole 
		{
			width: 673px;
		}
		#pagepopupiframe img
		{
			box-shadow: none !important;
			-webkit-box-shadow: none !important;
		}
	</style>
	<script>

		var prefetchedresult = { available:false };

		function nxs_js_pagepopup_prefetch()
		{
			// first check (server side) whether the popup should show
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					async: true,
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "pagepopup",
						"subaction": "prefetch",
						"clientpopupsessioncontext": nxs_js_getescaped_popupsession_context(),
						"clientpopupsessiondata": nxs_js_getescaped_popupsession_data(),
						"clientshortscopedata": nxs_js_popup_getescapedshortscopedata(),
						"clientqueryparameters": nxs_js_escaped_getqueryparametervalues(),
						"pagedecoratorid": "<?php echo $nxs_pagepopup_pagedecoratorid; ?>",
						"placeholderid": "<?php echo $nxs_pagepopup_pagedecoratorwidgetplaceholderid; ?>"
					},
					cache: false,
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						//nxs_js_log(response);
						if (response.result == "OK")
						{
							prefetchedresult.response = response;
							prefetchedresult.available = true;
							//nxs_js_log("prefetched data is now available");
						}
					}
				}
			);
		}
		
		function nxs_js_pagepopup_tagshowing()
		{
			// first check (server side) whether the popup should show
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					async: true,
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "pagepopup",
						"subaction": "tag",
						"clientpopupsessioncontext": nxs_js_getescaped_popupsession_context(),
						"clientpopupsessiondata": nxs_js_getescaped_popupsession_data(),
						"clientshortscopedata": nxs_js_popup_getescapedshortscopedata(),
						"clientqueryparameters": nxs_js_escaped_getqueryparametervalues(),
						"pagedecoratorid": "<?php echo $nxs_pagepopup_pagedecoratorid; ?>",
						"placeholderid": "<?php echo $nxs_pagepopup_pagedecoratorwidgetplaceholderid; ?>"
					},
					cache: false,
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
					}
				}
			);
		}

		<?php
		$url = $destination_url;
		$maxheight = 750;
		// $width = 340;		// 1 column
		$width = 738;		// 2 columns
		?>

		jQuery(window).load
		(
			function()
			{
				var shouldshow = true;	// todo: filter based on device?
				var triggerOnExit = <?php echo ($trigger_on_exit) ? 'true' : 'false'; ?>;
				var triggerOnJS  = <?php echo ($trigger_on_js) ? 'true' : 'false'; ?>;
				
				if ((jQuery(window).width() * 0.9) < <?php echo $width; ?>)
				{
					// if the width of the popup would fill up more than 90% of the available space,
					// ignore the popup
					//nxs_js_log("popup would be too wide; ignoring popup;");
					//nxs_js_alert("window:" + jQuery(window).width() * 0.9);
					//nxs_js_alert("popup width: <?php echo $width; ?>");
					shouldshow = false;
				}
				
				if (shouldshow)
				{
					nxs_js_log("prefetching data");
					nxs_js_pagepopup_prefetch();
					
					if (triggerOnExit) 
					{
						var triggered = false;
						var nxs_html = document.documentElement;
						jQ_nxs(nxs_html).on('mouseleave', function(event){
							if (triggered === false) {
								if (event.clientY > 20) { return; }

								triggered = true;
								nxs_js_pagepopup_activate();
							}
						});
					}
					else if (triggerOnJS) 
					{
						//
						var triggered = false;
					}
					else
					{
						setTimeout(function() { nxs_js_pagepopup_activate() }, <?php echo $delaypopup_milliseconds; ?>);
					}
				}
				else
				{
					nxs_js_log('insufficient space to render the popup');
				}
			}
		);
		
		function nxs_js_pagepopup_activate()
		{
			nxs_js_log('activating popup');
			
			if (prefetchedresult.available==false)
			{
				// todo: invoke this method using timed recursive invocation)
				nxs_js_log('sorry, prefetched data not (yet?) available, retrying...');
				setTimeout(function() { nxs_js_pagepopup_activate() }, 100);
				return;
			}
			
			//nxs_js_log('prefetched data is available');
			
			var response = prefetchedresult.response;
			// nxs_js_log(response);
			if (response.result == "OK")
			{
				// step 1; if specified, set a cookie to indicate the popup was shown before
				if (response.setcookie != null && !nxs_js_stringisblank(response.setcookie))
				{
					// expire cookie in a week
					expiretime = 7 * 24 * 60 * 60 * 1000;

					// set cookie
					nxs_js_setcookie(response.setcookie, 'set', expiretime);
				}
				
				// step 2; show the popup, if it should
				if ("yes" == response.shouldshow)
				{
					var width = <?php echo $width; ?>;
										
					// 
					nxs_js_popupsession_startnewcontext();
					
					nxs_js_popup_setsessioncontext("popup_current_dimensions", "gallerybox");
					
					nxs_js_popup_setsessioncontext("contextprocessor", "site");
					nxs_js_popup_setsessiondata("nxs_customhtml_popupheadertitle", "<?php echo $popuptitle; ?>");
					// nxs_js_popup_setsessiondata("minwidth", minwidth);

					var fillbackgroundcolor = 'white';
					
					if (response.tuned == null)
					{
						response.tuned = true;
						var html = "";
						html += "<div id='pagepopup_<?php echo $placeholderid; ?>' style=\"margin: 0 auto; display: table;\">";
	
						var semiborder = "";
	
						html += "<div style=\"padding-top: 10px;\">";	// padding 
						
						
						html += "<div style=\"position:relative;\"><a href=\"#\" onclick=\"nxs_js_closepopup_unconditionally(); return false;\"><span style=\"color: white; position: absolute; right: 0px; top: -10px;\" class=\"sign123 nxs-icon-remove-sign\"></span></a></div>";
						html += "<div style=\"" + semiborder + ";xbackground-color: " + fillbackgroundcolor + "\">";	// surrounding shade
							
						html += "<div id=\"pagepopupiframe\" style=\"width:" + width + "px\">";
						html += prefetchedresult.response.html;
						html += "</div>";
							
						html += "</div>";	// end surrounding shade
						html += "</div>";	// end horizontal alignment
	
						html += "</div>";	// end padding
	
						// the script below is cut off in a stupid way
						// since otherwise the browsers are not able to 
						// interpret it correctly ... (script in script)					
						html += "<" + "script>";
						html += "f" + "unction nxs_js_execute_after_popup_shows() { ";
						html += "j" + "Query('#nxsbox_window').addClass('nxs-gallerypopup'); }";
						html += "</" + "script>";
						
						//var l = html.length;
						//console.log("popup html length; " + l);
						
						// update the html
						response.html = html;
					}
					
					nxs_js_popup_setsessiondata("nxs_customhtml_scaffoldingtype", "nothing");
					nxs_js_popup_setsessiondata("nxs_customhtml_customhtmlcanvascontent", response.html);
					nxs_js_popup_navigateto("customhtml");
					
					//
					nxs_js_pagepopup_tagshowing();
					// 
				}
				else
				{
					//
					//nxs_js_log("server told us not to show the popup");
				}
			}
		}
		
	</script>
	<?php
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_pagepopup_home_getoptions($args) 
{
	$field_selectedtrigger_help = null;
	
	if (nxs_iswebmethodinvocation())
	{
		$clientpopupsessioncontext = $_REQUEST["clientpopupsessioncontext"];
		$clientpopupsessiondata = $_REQUEST["clientpopupsessiondata"];
		//
		$postid = $clientpopupsessioncontext["postid"];
		$placeholderid = $clientpopupsessioncontext["placeholderid"];
		
		// error_log("pagepopup; $postid $placeholderid");
		
		// load the widget's data from the persisted db
		$placeholdermetadata = nxs_getwidgetmetadata($postid, $placeholderid);
		$popup_trigger = $placeholdermetadata["popup_trigger"];
		
		//error_log("pagepopup; popup_trigger; $popup_trigger");
		
		// but allow it to be overriden in the session
		if ($clientpopupsessiondata["popup_trigger"] != "")
		{
			$popup_trigger = $clientpopupsessiondata["popup_trigger"];
		}
		
		// error_log("pagepopup; popup_trigger; $popup_trigger");
	
		if ($popup_trigger == "js")
		{	
			$field_selectedtrigger_help = array
			(
				"id" 				=> "popup_trigger_tip",
				"type" 				=> "custom",
				"custom"	=> "<div>Click <a target='_blank' href='https://www.wpsupporthelp.com/answer/how-to-trigger-page-popups-in-wordpress-using-javascript-1526/'>here</a> to learn how to use this trigger</div>",
			);
		}
		
	}
	
	$options = array
	(
		"sheettitle" => nxs_widgets_pagepopup_gettitle(),
		"sheeticonid" => nxs_widgets_pagepopup_geticonid(),
		"footerfiller" => true,	// add some space at the bottom
		
	
		"fields" => array
		(
			// SLIDES			
			
			array( 
				"id" 				=> "wrapper_input_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Display", "nxs_td"),
			),
			array(
				"id" 				=> "destination_articleid",
				"type" 				=> "article_link",
				"posttypes" => array("nxs_templatepart","page", "post"),
				"label" 			=> nxs_l18n__("Content", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("The content template, post or page to be rendered in the popup", "nxs_td"),
				"enable_mediaselect" => false,
			),
			array(
				"id"     			=> "popup_trigger",
				"type"     			=> "select",
				"popuprefreshonchange" => "true",
				"label"    			=> nxs_l18n__("Popup trigger", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("popup_trigger"),
				"unistylablefield"	=> true
			),
			
			$field_selectedtrigger_help,
			
			array(
				"id"     			=> "repeatpopup_scope",
				"type"     			=> "select",
				"label"    			=> nxs_l18n__("Repeat scope", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("repeatpopup_scope"),
				"unistylablefield"	=> true
			),
			
			array( 
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

function nxs_widgets_pagepopup_render_webpart_render_htmlvisualization($args) 
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
		$hovermenuargs["enable_decoratewidget"] = false;
		$hovermenuargs["enable_deletewidget"] = false;
		$hovermenuargs["enable_deleterow"] = true;
		$hovermenuargs["metadata"] = $mixedattributes;	
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	}
	
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
	// Check if specific variables are empty
	// If so > $shouldrenderalternative = true, which triggers the error message
	$shouldrenderalternative = false;
	/*
	if (
		somealternativeflow
	) {
		$shouldrenderalternative = true;
		$alternativehint = nxs_l18n__("Minimal: image, title, text or button", "nxs_td");
	}
	*/
	
		
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
		   <h4>Page popup</h4>
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

function nxs_widgets_pagepopup_initplaceholderdata($args)
{
	extract($args);

	/*
	$args['property'] = "value";
	*/
		
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
