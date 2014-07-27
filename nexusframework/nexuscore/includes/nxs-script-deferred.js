/*
contains functions that are only invoked after the load (this script file is loaded in a deferred way)
*/
function nxs_js_lazyloadmoreblogs(domelement)
{
	// ensure no other lazy load occurs for this widget
	if (jQuery(domelement).closest(".nxs-blogentries").hasClass("nxs-lazyloadinprogress"))
	{
		// ignore
		nxs_js_log("stop poking me!");
		return;
	}
	jQuery(domelement).closest(".nxs-blogentries").addClass("nxs-lazyloadinprogress");

	// find widget in dom
	var placeholderid = nxs_js_findclassidentificationwithprefix_closest(domelement, ".nxs-widget", "nxs-widget-");
	var postcontainerid = nxs_js_getcontainerpostid();
	var postid = nxs_js_findclassidentificationwithprefix_closest(domelement, ".nxs-elements-container", "nxs-post-");	
	var most_recent_page = parseInt(nxs_js_findclassidentificationwithprefix_closest(domelement, ".nxs-blogentries", "nxs-paging-page-"));
	
	//nxs_js_log("placeholderid:" + placeholderid);
	//nxs_js_log("postid:" + postid);
	
			
	var ajaxurl = nxs_js_get_adminurladminajax();
	jQuery.ajax
	(
		{
			type: 'POST',
			data: 
			{
				"action": "nxs_ajax_webmethods",
				"webmethod": "lazyloadblog",
				"postcontainerid": postcontainerid,
				"postid": postid,
				"paging_page": most_recent_page + 1,
				"placeholderid": placeholderid,
				"clientqueryparameters": nxs_js_escaped_getqueryparametervalues()
			},
			cache: false,
			dataType: 'JSON',
			url: ajaxurl, 
			success: function(response) 
			{
				nxs_js_log(response);
				if (response.result == "OK")
				{
					// update page
					jQuery(domelement).closest(".nxs-blogentries").removeClass("nxs-paging-page-" + most_recent_page);
					jQuery(domelement).closest(".nxs-blogentries").addClass("nxs-paging-page-" + (most_recent_page + 1));
					
				 	var html = response.appendhtmlraw.html;
				 	
				 	var widgetid = response.appendhtmlraw.replacedomid;
				 	var widget = jQuery("#" + widgetid);
				 	var lastblogentryinwidget = jQuery(widget).find(".nxs-blogentry").last();
				 	
					jQuery("body").append("<div id='lazyloadtemp' style='display: none;'>" + html + "</div>");

					// replicate the ".blog-entry" from the temp place to the widget
					jQuery("#lazyloadtemp").find(".nxs-blogentry").each
					(
						function(i)
						{
							jQuery(this).insertAfter(lastblogentryinwidget);
						}
					);

					// cleanup the scaffolding
					jQuery("#lazyloadtemp").remove();
					
					// update the heights
					nxs_gui_set_runtime_dimensions_enqueuerequest("lazyloadblog");
					// fast forward; we don't have to wait till the queue is empty, as its not 
					// likely other events occur at the same time
					nxs_gui_set_runtime_dimensions_actualrequest();
					
					// ensure that each img that finishes loading after we set the runtime
					// dimensions will trigger the runtime dimensions to be recalculated...
					jQuery('img').load
					(
						function()
						{
							//nxs_js_log('debug; finished loading img after reenabling all windows events; solved bug');
							nxs_gui_set_runtime_dimensions_enqueuerequest("nxs-framework-imgloaded");
						}
					);					
					
					// update the paging page nr
					nxs_js_log("most recent was:" + most_recent_page);
					// 
					
					// instruct the comments to be reloaded
					if (typeof nxs_js_refreshdisquscount_throttled == 'function')
					{
						nxs_js_refreshdisquscount_throttled();
					}
					else
					{
						//
					}
				}
				else
				{
					nxs_js_popup_notifyservererror_v2(response);
				}
				
				// release the "lock"
				jQuery(domelement).closest(".nxs-blogentries").removeClass("nxs-lazyloadinprogress");
			},
			error: function(xhr, ajaxOptions, thrownError)
			{
				// an error could occur if the user redirects before this operation is completed
				nxs_js_popup_notifyservererror_v2(thrownError);

				// release the "lock"
				jQuery(domelement).closest(".nxs-blogentries").removeClass("nxs-lazyloadinprogress");		
			}
		}
	);
}

function nxs_js_mailchimpsubmit(domelement)
{
	var placeholder = jQuery(domelement).closest(".nxs-placeholder");
	var submitbutton = jQuery(placeholder).find("input[type=submit]");
	jQuery(submitbutton).click();
}

function nxs_js_validateemail(elementValue)
{
	var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;  
	return emailPattern.test(elementValue);  
}

/**/

function nxs_js_popup_refresh_keep_focus(element)
{
	if (element != undefined)
	{
		nxs_js_popup_setshortscopedata('elementidlastfocussed', jQuery(element).attr('id')); 
		//nxs_js_log('focus was set on ' + nxs_js_popup_getshortscopedata('elementidlastfocussed'));
	}
	else
	{
		nxs_js_log('warning; no element specified to reset focus to');
	}
	nxs_js_popup_refresh();
}

//
// practical function to reload current pop up page
// refresh is based upon the variables that are set
// when popup is navigate to another page
// use this function after setting a variable like a pagenumber in
// paging, requiring the current popup page to refresh
//
function nxs_js_popup_refresh()
{
	nxs_js_popup_refresh_v2(true);
}

function nxs_js_popup_refresh_v2(shouldgrowl)
{
	// get rid of existing popup
	nxs_js_teardownpopupdom();

	var sheet = nxs_js_popup_getcurrentsheet();
	nxs_js_popup_navigateto_v2(sheet, shouldgrowl);
}

//
// returns the name of the current popup box that is showing
//
function nxs_js_popup_getcurrentsheet()
{
	var result = nxs_js_popup_getsessioncontext("sheet");
	return result;
}

function nxs_js_popup_setcurrentsheet(sheet)
{
	nxs_js_popup_setsessioncontext("sheet", sheet);
}

function nxs_js_pop_resetdynamiccontentcontainer()
{
	var initialvalue = "";
	// inject javascript / reset functies
	// dit zorgt ervoor dat popups standaard op basis van een 'auto' 
	// gedrag worden gepositioneerd (vertikaal gecentreerd). 
	// Indien gewenst kan een placeholder deze functie later opnieuw
	// overschrijven (override / overriden / overridden) waardoor het gedrag kan afwijken.
	initialvalue += "<" + "script type='text/javascript'>";
	initialvalue += "function nxs_js_overrule_topmargin() { return 'auto'; }";
	initialvalue += "function nxs_js_execute_after_popup_shows() { }";
	initialvalue += "function nxs_js_execute_before_popup_closes() { }";
	initialvalue += "function nxs_js_popup_get_initialbuttonstate() { return 'showokifnotdirty'; }";
	initialvalue += "function nxs_js_popup_get_minwidth() { return 750; }";
	initialvalue += "function nxs_js_popup_get_maxwidth() { return 1000; }";
	initialvalue += "function nxs_js_popup_get_maxheight() { var contentheight = jQuery('.nxs-popup-content-canvas').height(); var maxheight = Math.round(jQuery(window).height() * 0.8); if (maxheight > contentheight) { maxheight = contentheight; } if (maxheight < 400 && jQuery('.nxs-canvas-footerfiller').length > 0) { maxheight = 400; }  ; return maxheight; }";
	initialvalue += "function nxs_js_showwarning_when_trying_to_close_dirty_popup() { return true; }";
	initialvalue += "<" + "/script>";
	
	// inject initial value to the container
	jQuery('.nxs-popup-dyncontentcontainer').html(initialvalue);
}

//
// opent de meegegeven sheet voor de huidige context (bijv. placeholder_{postid}_{placeholderid})
// binnen de huidige pagina
//
function nxs_js_popup_navigateto(sheet)
{
	nxs_js_popup_navigateto_v2(sheet, true);
}

function nxs_js_popup_navigateto_v2(sheet, shouldgrowl)
{
	var waitgrowltoken = -1;
	
	if (shouldgrowl)
	{
		waitgrowltoken = nxs_js_alert_wait_start(nxs_js_gettrans("Loading information"));
	}
	nxs_js_popup_setcurrentsheet(sheet);
	
	var ajaxurl = nxs_js_get_adminurladminajax();
	jQuery.ajax
	(
		{
			type: 'POST',
			data: 
			{
				"action": "nxs_ajax_webmethods",
				"webmethod": "getsheet",
				"clientpopupsessioncontext": nxs_js_getescaped_popupsession_context(),
				"clientpopupsessiondata": nxs_js_getescaped_popupsession_data(),
				"clientshortscopedata": nxs_js_popup_getescapedshortscopedata(),
				"clientqueryparameters": nxs_js_escaped_getqueryparametervalues()
			},
			cache: false,
			dataType: 'JSON',
			url: ajaxurl, 
			success: function(response) 
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				
				nxs_js_log(response);
				if (response.result == "OK")
				{
					if (response.html != null && !nxs_js_stringisblank(response.html))
					{
						// OK
						var elementidlastfocussed = nxs_js_popup_getshortscopedata('elementidlastfocussed');
						
						// wipe de shortscope data
						nxs_js_popup_clearshortscopedata();
						
						// dim hover menu's die op dit moment zichtbaar zijn
						jQuery(".nxs-hover-menu").addClass('nxs-suppress');
						
						//
						nxs_js_pop_resetdynamiccontentcontainer();
						
						// remove any previously defined nxs-active indicators (for text-widgets; if these
						// are removed, the nxs-active indicator somehow remains (so this basically is
						// a workaround).
						jQuery("#TB_window.nxs-active").removeClass("nxs-active");
												
						jQuery('.nxs-popup-dyncontentcontainer').html(response.html);
													
						// width=1, see #1283672893762
						tb_show(response.title, "#TB_inline?height=1&width=1&inlineId=nxs_ajax_thickbox&modal=true", "");
						jQuery("#TB_overlay").show();
						jQuery("#TB_window").show();
						
						//
						// enable dragging of the window
						//
						jQuery("#TB_window").draggable
						(
							{
								handle: ".nxs-admin-header",
								start: function() 
								{
									if (!jQuery(".nxs-popup-dyncontentcontainer").hasClass('nxs-wasdragged'))
									{
										jQuery(".nxs-popup-dyncontentcontainer").addClass('nxs-wasdragged');
									}
								}
							}
						)
						
						// ensure the "back" document is not scrollable when the popup shows
						nxs_js_disabledocumentscrollwhenoveringoverelement('.nxs-popup-content-canvas-cropper');
						
						// enable "chosen" script to enhance dropdownlists
						//nxs_js_log("chosen select done");
						jQuery(".chosen-select").chosen({allow_single_deselect: true});

						//nxs_js_log("chosen select done");
						
						nxs_js_popupshows = true;
						
						// show/hide buttons as indicated by the popup for initialization
						// note, this will be overriden if the popup appears to be dirty (handled below)
						var initialbuttonstate = nxs_js_popup_get_initialbuttonstate();
						if (initialbuttonstate == 'showokifnotdirty')
						{
							// show ok, hide cancel, hide save
							if (jQuery('#nxs_popup_genericokbutton').length > 0)
							{
								jQuery('#nxs_popup_genericokbutton').show();										
								if (jQuery('#nxs_popup_genericcancelbutton').length > 0)
								{
									jQuery('#nxs_popup_genericcancelbutton').hide();
								}
								if (jQuery('#nxs_popup_genericsavebutton').length > 0)
								{
									jQuery('#nxs_popup_genericsavebutton').hide();
								}
							}
							else
							{
								//nxs_js_log('cannot apply initialbuttonstate; ok button not found');
							}
						}
						else if (initialbuttonstate == 'showcancel')
						{
							// show cancel, hide ok, hide save
							if (jQuery('#nxs_popup_genericcancelbutton').length > 0)
							{
								jQuery('#nxs_popup_genericcancelbutton').show();										
								if (jQuery('#nxs_popup_genericokbutton').length > 0)
								{
									jQuery('#nxs_popup_genericokbutton').hide();
								}
								if (jQuery('#nxs_popup_genericsavebutton').length > 0)
								{
									jQuery('#nxs_popup_genericsavebutton').hide();
								}
							}
							else
							{
								nxs_js_log('cannot apply initialbuttonstate; cancel button not found');
							}
						}
						else if (initialbuttonstate == 'none')
						{
							// ok :)
						}
						else
						{
							nxs_js_log('unsupported initialbuttonstate;' + initialbuttonstate);
						}
						
						// enable 'save' buttons if session indicates 'dirty' fields
						if (nxs_js_popup_sessiondata_is_dirty())
						{
							nxs_js_popup_handle_becomes_dirty_first_time();
						}
						
						// enables textboxes to automatically set the dirty flag when text is entered
						nxs_js_popup_processautodirtyhandling();
													
						// close de popup als er 'naast' de pop up wordt geklikt (we undo-en de modal eigenschap)
						// dit is een workaround/fix; de modal van de thickbox zorgt er voor dat de MCE editor
						// niet goed her-initiatiseert
						jQuery("#TB_overlay").unbind("click.popupzekerweten");
						jQuery("#TB_overlay").bind("click.popupzekerweten", function(e) 
						{
							nxs_js_log('345897');
							// stop het progageren van het event (bind("click") om te voorkomen dat onderliggende
							// elementen het click event gaan afhandelen (zoals het event dat de body click altijd opvangt...)
							e.stopPropagation();
            	nxs_js_closepopup_unconditionally_if_not_dirty();
						});

						jQuery(document).unbind("keyup.popupcloser");
						jQuery(document).bind("keyup.popupcloser", 
							function(e)
							{ 
								if (e.keyCode == 27 && nxs_js_popupshows == true)	// handled
								{
									//nxs_js_log("closing popup (if not dirty)");
								
									// 27 == escape
									nxs_js_closepopup_unconditionally_if_not_dirty();
									
									// set focus to the body
									jQuery("body").focus();
																		
									// absorb the event
									return false;
								}
							}
						);
						
						jQuery("#TB_window").unbind("click.stoppropagation");
						jQuery("#TB_window").bind("click.stoppropagation", function(e) 
						{
							// stop het progageren van het event (bind("click") om te voorkomen dat onderliggende
							// elementen het click event gaan afhandelen (zoals het event dat de body click altijd opvangt...)
							e.stopPropagation();
						})
						
						// if a nxs_js_execute_after_popup_shows function is present in the dom (optionally), execute it!
						if(typeof nxs_js_execute_after_popup_shows == 'function') 
						{
							nxs_js_execute_after_popup_shows();
						}
						
						
						nxs_js_log("broadcasting afterpopupshows");
						
						// broadcast clientside trigger for dom elements to be notified when the popup shows
						jQuery(window).trigger('nxs_jstrigger_afterpopupshows');

						nxs_js_log("unbinding broadcast receivers");
						
						// remove all listeners
						jQuery(window).unbind("nxs_jstrigger_afterpopupshows");
						
						// reset last focus to specified element (if available)
						if (elementidlastfocussed != null)
						{
							//nxs_js_log('resetting focus ... ');
							if (jQuery('#' + elementidlastfocussed).length > 0)
							{
								jQuery('#' + elementidlastfocussed).focus();
							}
						}
						
						// reset de hoogte op het moment dat er plaatjes in de popup zitten die 
						// nog niet beschikbaar/ingeladen zijn
						jQuery('.nxs-popup-dyncontentcontainer img').load
						(
							function()
							{
								//nxs_js_log('loading of image finished');
								nxs_js_reset_popup_dimensions();
							}
						);
														
						// reset height of popup
						nxs_js_reset_popup_dimensions();
						
						// handle enter auto submit popup								
						nxs_js_popup_registerautosubmitwhenuserpressesenter();
					}
					else
					{
						nxs_js_popup_notifyservererror();
						nxs_js_log('html is null or empty?');
						nxs_js_log(html);
					}
				}
				else
				{
					nxs_js_popup_notifyservererror();
					nxs_js_log(response);
				}
			},
			error: function(response)
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				
				nxs_js_popup_notifyservererror();
				nxs_js_log(response);
			}
		}
	);
}


function nxs_js_popupsession_startnewcontext()
{
	// note! shortscope data remains!
	nxs_js_popupsession_data_clear();
	nxs_js_popupsession_context_clear();
	
	//nxs_js_log('context cleared!');
	//nxs_js_log(nxs_js_getstacktrace());
	
	// each request always passes the #2389724
	nxs_js_popup_setsessioncontext("urlencodedjsonencodedquery_vars", nxs_js_geturlencodedjsonencodedquery_vars());
}

// voids all variables existing in the existing popup session
function nxs_js_popupsession_data_clear()
{
	nxs_js_popupsessiondata = {};	// key values like dictionary
}

function nxs_js_popupsession_data_remove_key(key)
{
	delete nxs_js_popupsessiondata[key];
}

function nxs_js_popupsession_context_clear()
{
	nxs_js_popupsessioncontext = {};	// key values like dictionary
}

// see nxs_urldecodearrayvalues($array) for the php invert function :)
// note its a BEST practise to UNescape properties in the webservices.php
// file (here: *426759487653456), to prevent replications or mistakes in detailed code
// 20131112; note we can not use the escape() function here, as escape() doesn't replace + signs
// (it works 99%, not 100%)
function nxs_js_getescapeddictionary(input)
{
	var escaped = {};
	for (var x in input)
  {
  	if (!input.hasOwnProperty(x)) 
		{
			continue;
		}
		
  	var waarde = input[x];
  	escaped[x] = encodeURIComponent(waarde);
  	/*
  	if (escaped[x] != waarde)
  	{
  		nxs_js_log("delta found");
  		nxs_js_log(escaped[x]);
  		nxs_js_log(waarde);
  	}
  	*/
  }
	return escaped;
}

// ------------------------------------------------------------------------------------
// popup session functions
// note that the session of a popup page 'lives' while the user interacts
// with one or subsequent popup pages. Its a clientside state of the properties
// used between popup (ajax) refreshes.

// returns all (escaped) variables of the session of the popup page
function nxs_js_getescaped_popupsession_data()
{
	return nxs_js_getescapeddictionary(nxs_js_popupsessiondata);
}

function nxs_js_getescaped_popupsession_context()
{
	return nxs_js_getescapeddictionary(nxs_js_popupsessioncontext);
}

// gets the value corresponding the specified key from the session of the popup page
function nxs_js_popup_getsessiondata(key)
{
	return nxs_js_popupsessiondata[key];
}

// stores a value for the specified key in the session of the popup page
function nxs_js_popup_setsessiondata(key, val)
{
	nxs_js_popupsessiondata[key] = val;
}

function nxs_js_popup_getsessioncontext(key)
{
	return nxs_js_popupsessioncontext[key];
}

function nxs_js_popup_setsessioncontext(key, val)
{
	nxs_js_popupsessioncontext[key] = val;
}

// a generic implementation that handles when the popup becomes 'dirty' for the first time
function nxs_js_popup_handle_becomes_dirty_first_time()
{
	if (jQuery('#nxs_popup_genericsavebutton').length > 0)
	{
		jQuery('#nxs_popup_genericsavebutton').show();
	}
	else
	{
		nxs_js_log("info: no generic popup save button found");
	}
	
	if (jQuery('#nxs_popup_genericcancelbutton').length > 0)
	{
		jQuery('#nxs_popup_genericcancelbutton').show();
	}
	else
	{
		// nxs_js_log("no generic popup cancel button found?");
	}
	
	if (jQuery('#nxs_popup_genericokbutton').length > 0)
	{
		jQuery('#nxs_popup_genericokbutton').hide();
	}
	else
	{
		// nxs_js_log("no generic popup ok button found?");
	}
}

// 
function nxs_js_popup_clearshortscopedata()
{
	nxs_js_shortscopedata = {};	// key values like dictionary
}

function nxs_js_popup_getescapedshortscopedata()
{
	return nxs_js_getescapeddictionary(nxs_js_shortscopedata);
}		

function nxs_js_popup_setshortscopedata(key, val)
{
	nxs_js_shortscopedata[key] = val;
}

function nxs_js_popup_getshortscopedata(key)
{
	return nxs_js_shortscopedata[key];
}


function nxs_js_popup_processautodirtyhandling()
{
	// also handle pasting values
	jQuery(".nxs-popup-dyncontentcontainer input").unbind("input propertychange.makedirty");
	jQuery(".nxs-popup-dyncontentcontainer input").bind("input propertychange.makedirty", function(e) 
	{
		nxs_js_popup_sessiondata_make_dirty();
	});

	//nxs_js_log('nxs_js_popup_processautodirtyhandling');
	
	jQuery(".nxs-popup-dyncontentcontainer input").unbind("keyup.makedirty");
	jQuery(".nxs-popup-dyncontentcontainer input").bind("keyup.makedirty", function(e) 
	{
		if (nxs_js_doeskeycodemakedirty(e))
		{
  		nxs_js_popup_sessiondata_make_dirty();
  	}
	});
	
	jQuery(".nxs-popup-dyncontentcontainer input").unbind("change.makedirty");
	jQuery(".nxs-popup-dyncontentcontainer input").bind("change.makedirty", function(e) 
	{
		nxs_js_popup_sessiondata_make_dirty();
	});
	
	jQuery(".nxs-popup-dyncontentcontainer select").unbind("change.makedirty");
	jQuery(".nxs-popup-dyncontentcontainer select").bind("change.makedirty", function(e) 
	{
		nxs_js_popup_sessiondata_make_dirty();
	});
	
	jQuery(".nxs-popup-dyncontentcontainer textarea").unbind("keyup.makedirty");
	jQuery(".nxs-popup-dyncontentcontainer textarea").bind("keyup.makedirty", function(e) 
	{
		if (nxs_js_doeskeycodemakedirty(e))
		{
  		nxs_js_popup_sessiondata_make_dirty();
  	}
	});
}

// makedirty
function nxs_js_popup_sessiondata_make_dirty()
{
	if (nxs_js_popup_getsessiondata('nxs_genericdirty') == 'true')
	{
		// was already dirty
	}
	else
	{
		nxs_js_popup_setsessiondata('nxs_genericdirty', 'true');
		nxs_js_popup_handle_becomes_dirty_first_time();
	}
}

function nxs_js_popup_sessiondata_clear_dirty()
{
	nxs_js_popup_setsessiondata('nxs_genericdirty', '');
}
		
function nxs_js_popup_sessiondata_is_dirty()
{
	if (nxs_js_popup_getsessiondata('nxs_genericdirty') == 'true')
	{
		return true;
	}
	else
	{
		return false;
	}
}

function nxs_js_popup_registerautosubmitwhenuserpressesenter()
{
	//nxs_js_log('executing nxs_js_popup_registerautosubmitwhenuserpressesenter');
	
	jQuery("#TB_window .nxs-admin-wrap .nxs_defaultenter").unbind("keyup.defaultenter");
	jQuery("#TB_window .nxs-admin-wrap .nxs_defaultenter").bind("keyup.defaultenter", function(e)
	{
		if (e.keyCode == 13)
		{
			if (jQuery("#nxs_popup_genericsavebutton").length > 0)
			{
				if (jQuery("#nxs_popup_genericsavebutton").is(':visible'))
				{
					//nxs_js_log('default enter clicked');
					e.stopPropagation();
					jQuery("#nxs_popup_genericsavebutton").click();
				}
				else
				{
					nxs_js_log('default enter clicked, save not visible');
				}
			}
			else
			{
				nxs_js_log('default enter clicked, save not found');
			}
		}
	});
}

function nxs_js_popup_notifynotok(message, idofelementofocus)
{
	if (nxs_js_popupshows)
	{
		nxs_js_popup_negativebounce(message);
	}
	else
	{
		nxs_js_alert(message);
	}
	
	if (idofelementofocus != null)
	{
		jQuery('#' + idofelementofocus).focus();
	}
}

function nxs_js_popup_requirepagerefreshwhenpopupcloses()
{
	nxs_js_requirespagerefresh = true;
}

function nxs_js_popup_notifyservererror()
{
	// note; a "error" output could be returned when a ajax call is invoked,
	// if the browser is redirecting; in that case any pending requests
	// get "error", see http://stackoverflow.com/questions/9131614/redirect-after-jquery-ajax-call-giving-error.
	nxs_js_popup_notifyservererror_v2("no debuginfo specified");
}

function nxs_js_popup_notifyservererror_v2(debuginfo)
{
	nxs_js_log('notifyservererror');
	nxs_js_log(debuginfo);

	var stacktrace = nxs_js_getstacktrace();
	nxs_js_log(stacktrace);

	// output stacktrace

	var msg = nxs_js_gettrans('Error transferring data. Please try again later');
	nxs_js_popup_notifynotok(msg, null);
}

// laat het popup scherm schudden (te gebruiken om aan te duiden dat
// de gebruiker een ongeldige aktie heeft uitgevoerd)
function nxs_js_popup_negativebounce(message)
{
	var stacktrace = nxs_js_getstacktrace();
	nxs_js_log(stacktrace);

	if (message != "")
	{
		//alert(message); // optionally use this line if you want a 'true' popup that stalls all threads
		nxs_js_alert(message);
	}
	jQuery('#TB_window').effect("shake", { times:3, distance: 10 }, 50);
}


// indicates whether there are any objections for opening a new popup page,
// this function returns 'true' if for example the existing popup page has
// unsaved items that haven't yet been persisted
function nxs_js_popup_anyobjectionsforopeningnewpopup()
{
	if (nxs_js_popupshows)
	{
		// indien een 'dirty' popup sessie actief is,
		// vragen we of het OK is om de vorige sessie te closen
		if (nxs_js_popup_sessiondata_is_dirty())
		{
			var answer = confirm(nxs_js_gettrans('Ignore unsaved changes?'));
			if (!answer)
			{
				// toch niet
				return true;
			}
			// ok, we gaan door
		}
		else
		{
			// er was geen openstaande sessie informatie
		}
		
		// no objections
		return false;
	}
	else
	{
		return false;
	}
}

// opens a postwizard popup as specified by the sheet name
function nxs_js_popup_postwizard_neweditsession(postwizard, sheet)
{
	if (nxs_js_popup_anyobjectionsforopeningnewpopup())
	{
		// opening a new popup is not allowed; likely some other popup is already opened
		return;
	}
	
	// initiate a new popupsession data as this is a new session
	nxs_js_popupsession_startnewcontext();
	nxs_js_popup_setsessioncontext("contextprocessor", "postwizard");
	nxs_js_popup_setsessioncontext("postwizard", postwizard);

	// show the popup
	nxs_js_popup_navigateto(sheet);
}

// opens a rowscontainer popup as specified by the sheet name
function nxs_js_popup_rowscontainer_neweditsession(postid, sheet)
{
	if (nxs_js_popup_anyobjectionsforopeningnewpopup())
	{
		// opening a new popup is not allowed; likely some other popup is already opened
		return;
	}
	
	// initiate a new popupsession data as this is a new session
	nxs_js_popupsession_startnewcontext();
	nxs_js_popup_setsessioncontext("contextprocessor", "rowscontainer");
	nxs_js_popup_setsessioncontext("postid", postid);
	
	// show the popup
	nxs_js_popup_navigateto(sheet);
}

// opens a site-wide popup as specified by the sheet name,
// using an empty context
function nxs_js_popup_site_neweditsession(sheet)
{
	var initialcontext = {};
	return nxs_js_popup_site_neweditsession_v2(sheet, initialcontext);
}

// opens a site-wide popup as specified by the sheet name
function nxs_js_popup_site_neweditsession_v2(sheet, initialcontext)
{
	if (nxs_js_popup_anyobjectionsforopeningnewpopup())
	{
		// opening a new popup is not allowed; likely some other popup is already opened
		return;
	}
	
	// initiate a new popupsession data as this is a new session
	nxs_js_popupsession_startnewcontext();
	nxs_js_popup_setsessioncontext("contextprocessor", "site");
	
	// set initial context
	for (var initialcontextkey in initialcontext) 
	{
  	if (!initialcontext.hasOwnProperty(initialcontextkey)) 
		{
			continue;
		}
		
  	var initialcontextvalue = initialcontext[initialcontextkey];
  	nxs_js_popup_setsessioncontext(initialcontextkey, initialcontextvalue);
  }

	// show the popup
	nxs_js_popup_navigateto(sheet);
}
								
		//
		// sluit het pop up scherm en verwijderd alle tijdelijke variabelen
		//
		function nxs_js_closepopup()
		{
			// prevent click events from propagating...
			try { event.stopPropagation(); } catch (err) { }
			
			var answer = confirm(nxs_js_gettrans('Are you sure you want to close this window?'));
			if (answer)
			{
				nxs_js_closepopup_unconditionally();
			}
		}
		
		// closes the active popup that was opened using the function nxs_js_popup_navigateto
		function nxs_js_closepopup_unconditionally()
		{
			if (nxs_js_requirespagerefresh)
			{
				nxs_js_broadcastpopupcloses();
				nxs_js_refreshcurrentpage();
			}
			else
			{
				nxs_js_popupsession_startnewcontext();
	
				nxs_js_teardownpopupdom();	// removes dom elements, and prepares for the next thickbox
				nxs_js_hidepopupoverlay();	// removed the popup overlay "sheet"
				
				// re-enable hover menu's
				nxs_js_process_updated_editor_state_silent();
			}
		}
		
		function nxs_js_hidepopupoverlay()
		{
			jQuery("#TB_overlay").hide();
		}
		
		function nxs_js_broadcastpopupcloses()
		{
			// if a nxs_js_execute_before_popup_closes function is present in the dom (optionally), execute it!
			if(typeof nxs_js_execute_before_popup_closes == 'function') 
			{
				nxs_js_execute_before_popup_closes();
			}
			
			// broadcast clientside trigger for dom elements to be notified when the popup shows
			nxs_js_log("broadcasting trigger: nxs_jstrigger_beforepopupcloses");
			jQuery(window).trigger('nxs_jstrigger_beforepopupcloses');
		}
		
		function nxs_js_teardownpopupdom()
		{
			nxs_js_log("broadcast b");
			nxs_js_broadcastpopupcloses();
		
			jQuery("#TB_window").removeClass("nxs-active");
			jQuery("#TB_window").removeClass("nxs-wasdragged");
			
			// its important to actually hide the thickboxes,
			// otherwise the GUI event like hover will be intercepted
			jQuery("#TB_window").hide();
			
			//nxs_js_log('found:' + jQuery("#TB_window.thickboxwindow").length);
			jQuery("#TB_window.thickboxwindow").each(function(i)
			{
				//nxs_js_log('removing useless thickbox');
   			jQuery(this).remove();
			});
			
			nxs_js_popupshows = false;

			jQuery("body").append("<div id='TB_window' class='thickboxwindow' style='display:none;'></div>");
			
			//
		}
		
		function nxs_js_closepopup_unconditionally_if_not_dirty()
		{
			//nxs_js_log('detected...');
		
			if (nxs_js_popup_sessiondata_is_dirty())
			{
				try { event.stopPropagation(); } catch (err) { }
				
				if (nxs_js_showwarning_when_trying_to_close_dirty_popup())
				{
					var answer = confirm(nxs_js_gettrans("Ignore unsaved popup data?"));
					if (answer)
					{
						nxs_js_closepopup_unconditionally();
					}
				}
				else
				{
					// popup explicitly overruled the warning question (praktisch bij de login popup)
					nxs_js_closepopup_unconditionally();					
				}
			}
			else
			{
				nxs_js_closepopup_unconditionally();
			}
		}
		