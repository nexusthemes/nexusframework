/*
contains functions that are only invoked after the load (this script file is loaded in a deferred way)
*/
function nxs_js_lazyloadmoreblogs(domelement)
{
	// ensure no other lazy load occurs for this widget
	if (jQ_nxs(domelement).closest(".nxs-blogentries").hasClass("nxs-lazyloadinprogress"))
	{
		// ignore
		nxs_js_log("stop poking me!");
		return;
	}
	jQ_nxs(domelement).closest(".nxs-blogentries").addClass("nxs-lazyloadinprogress");

	// find widget in dom
	var placeholderid = nxs_js_findclassidentificationwithprefix_closest(domelement, ".nxs-widget", "nxs-widget-");
	var postcontainerid = nxs_js_getcontainerpostid();
	var postid = nxs_js_findclassidentificationwithprefix_closest(domelement, ".nxs-elements-container", "nxs-post-");	
	var most_recent_page = parseInt(nxs_js_findclassidentificationwithprefix_closest(domelement, ".nxs-blogentries", "nxs-paging-page-"));
	
	//nxs_js_log("placeholderid:" + placeholderid);
	//nxs_js_log("postid:" + postid);
	
			
	var ajaxurl = nxs_js_get_adminurladminajax();
	jQ_nxs.ajax
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
					jQ_nxs(domelement).closest(".nxs-blogentries").removeClass("nxs-paging-page-" + most_recent_page);
					jQ_nxs(domelement).closest(".nxs-blogentries").addClass("nxs-paging-page-" + (most_recent_page + 1));
					
				 	var html = response.appendhtmlraw.html;
				 	
				 	var widgetid = response.appendhtmlraw.replacedomid;
				 	var widget = jQ_nxs("#" + widgetid);
				 	var lastblogentryinwidget = jQ_nxs(widget).find(".nxs-blogentry").last();
				 	
					jQ_nxs("body").append("<div id='lazyloadtemp' style='display: none;'>" + html + "</div>");

					// replicate the ".blog-entry" from the temp place to the widget
					jQ_nxs("#lazyloadtemp").find(".nxs-blogentry").each
					(
						function(i)
						{
							jQ_nxs(this).insertAfter(lastblogentryinwidget);
						}
					);

					// cleanup the scaffolding
					jQ_nxs("#lazyloadtemp").remove();
					
					// update the heights
					nxs_gui_set_runtime_dimensions_enqueuerequest("lazyloadblog");
					// fast forward; we don't have to wait till the queue is empty, as its not 
					// likely other events occur at the same time
					nxs_gui_set_runtime_dimensions_actualrequest();
					
					// ensure that each img that finishes loading after we set the runtime
					// dimensions will trigger the runtime dimensions to be recalculated...
					jQ_nxs('img').load
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
				jQ_nxs(domelement).closest(".nxs-blogentries").removeClass("nxs-lazyloadinprogress");
			},
			error: function(xhr, ajaxOptions, thrownError)
			{
				// an error could occur if the user redirects before this operation is completed
				nxs_js_popup_notifyservererror_v2(thrownError);

				// release the "lock"
				jQ_nxs(domelement).closest(".nxs-blogentries").removeClass("nxs-lazyloadinprogress");		
			}
		}
	);
}

function nxs_js_mailchimpsubmit(domelement)
{
	var placeholder = jQ_nxs(domelement).closest(".nxs-placeholder");
	var submitbutton = jQ_nxs(placeholder).find("input[type=submit]");
	jQ_nxs(submitbutton).click();
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
		nxs_js_popup_setshortscopedata('elementidlastfocussed', jQ_nxs(element).attr('id')); 
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
	initialvalue += "function nxs_js_popup_get_maxheight() { var contentheight = jQ_nxs('.nxs-popup-content-canvas').height(); var maxheight = Math.round(jQ_nxs(window).height() * 0.8); if (maxheight > contentheight) { maxheight = contentheight; } if (maxheight < 400 && jQ_nxs('.nxs-canvas-footerfiller').length > 0) { maxheight = 400; }  ; return maxheight; }";
	initialvalue += "function nxs_js_showwarning_when_trying_to_close_dirty_popup() { return true; }";
	initialvalue += "<" + "/script>";
	
	// inject initial value to the container
	jQ_nxs('.nxs-popup-dyncontentcontainer').html(initialvalue);
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
	jQ_nxs.ajax
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
				nxs_js_popup_render_inner(waitgrowltoken, response);
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

function nxs_js_popup_render_inner(waitgrowltoken, response)
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
			jQ_nxs(".nxs-hover-menu").addClass('nxs-suppress');
			
			//
			nxs_js_pop_resetdynamiccontentcontainer();
			
			// remove any previously defined nxs-active indicators (for text-widgets; if these
			// are removed, the nxs-active indicator somehow remains (so this basically is
			// a workaround).
			jQ_nxs("#nxsbox_window.nxs-active").removeClass("nxs-active");
			
			jQ_nxs('.nxs-popup-dyncontentcontainer').html(response.html);
										
			// width=1, see #1283672893762
			nxsbox_show(response.title, "#nxsbox_inline?height=1&width=1&inlineId=nxs_ajax_nxsbox&modal=true", "");
			jQ_nxs("#nxsbox_overlay").show();
			jQ_nxs("#nxsbox_window").show();
			
			//
			// enable dragging of the window
			//
			jQ_nxs("#nxsbox_window").draggable
			(
				{
					handle: ".nxs-admin-header",
					start: function() 
					{
						if (!jQ_nxs(".nxs-popup-dyncontentcontainer").hasClass('nxs-wasdragged'))
						{
							jQ_nxs(".nxs-popup-dyncontentcontainer").addClass('nxs-wasdragged');
						}
					}
				}
			)
			
			// ensure the "back" document is not scrollable when the popup shows
			nxs_js_disabledocumentscrollwhenhoveringoverelement('.nxs-popup-content-canvas-cropper');
			
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
				if (jQ_nxs('#nxs_popup_genericokbutton').length > 0)
				{
					jQ_nxs('#nxs_popup_genericokbutton').show();										
					if (jQ_nxs('#nxs_popup_genericcancelbutton').length > 0)
					{
						jQ_nxs('#nxs_popup_genericcancelbutton').hide();
					}
					if (jQ_nxs('#nxs_popup_genericsavebutton').length > 0)
					{
						jQ_nxs('#nxs_popup_genericsavebutton').hide();
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
				if (jQ_nxs('#nxs_popup_genericcancelbutton').length > 0)
				{
					jQ_nxs('#nxs_popup_genericcancelbutton').show();										
					if (jQ_nxs('#nxs_popup_genericokbutton').length > 0)
					{
						jQ_nxs('#nxs_popup_genericokbutton').hide();
					}
					if (jQ_nxs('#nxs_popup_genericsavebutton').length > 0)
					{
						jQ_nxs('#nxs_popup_genericsavebutton').hide();
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
			// dit is een workaround/fix; de modal van de nxsbox zorgt er voor dat de MCE editor
			// niet goed her-initiatiseert
			jQ_nxs("#nxsbox_overlay").unbind("click.popupzekerweten");
			jQ_nxs("#nxsbox_overlay").bind("click.popupzekerweten", function(e) 
			{
				nxs_js_log('345897');
				// stop het progageren van het event (bind("click") om te voorkomen dat onderliggende
				// elementen het click event gaan afhandelen (zoals het event dat de body click altijd opvangt...)
				e.stopPropagation();
      	nxs_js_closepopup_unconditionally_if_not_dirty();
			});

			jQ_nxs(document).unbind("keyup.popupcloser");
			jQ_nxs(document).bind("keyup.popupcloser", 
				function(e)
				{ 
					if (e.keyCode == 27 && nxs_js_popupshows == true)	// handled
					{
						//nxs_js_log("closing popup (if not dirty)");
					
						// 27 == escape
						nxs_js_closepopup_unconditionally_if_not_dirty();
						
						// set focus to the body
						jQ_nxs("body").focus();
															
						// absorb the event
						return false;
					}
				}
			);
			
			jQ_nxs("#nxsbox_window").unbind("click.stoppropagation");
			jQ_nxs("#nxsbox_window").bind("click.stoppropagation", function(e) 
			{
				// stop het progageren van het event (bind("click") om te voorkomen dat onderliggende
				// elementen het click event gaan afhandelen (zoals het event dat de body click altijd opvangt...)
				e.stopPropagation();
			});
			
			jQuery('#nxsbox_window').removeClass('nxs-gallerypopup');
			
			// if a nxs_js_execute_after_popup_shows function is present in the dom (optionally), execute it!
			if(typeof nxs_js_execute_after_popup_shows == 'function') 
			{
				nxs_js_execute_after_popup_shows();
			}
			
			
			nxs_js_log("broadcasting afterpopupshows");
			
			// broadcast clientside trigger for dom elements to be notified when the popup shows
			jQ_nxs(window).trigger('nxs_jstrigger_afterpopupshows');

			nxs_js_log("unbinding broadcast receivers");
			
			// remove all listeners
			jQ_nxs(window).unbind("nxs_jstrigger_afterpopupshows");
			
			// reset last focus to specified element (if available)
			if (elementidlastfocussed != null)
			{
				//nxs_js_log('resetting focus ... ');
				if (jQ_nxs('#' + elementidlastfocussed).length > 0)
				{
					jQ_nxs('#' + elementidlastfocussed).focus();
				}
			}
			
			// reset de hoogte op het moment dat er plaatjes in de popup zitten die 
			// nog niet beschikbaar/ingeladen zijn
			jQ_nxs('.nxs-popup-dyncontentcontainer img').load
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

function nxs_js_popup_push()
{
	console.log("popup; push");
	if (nxs_js_popupshows)
	{
		// push a new level on the stack of popups
		var o = 
		{
			'nxs_js_popupsessiondata' : nxs_js_popupsessiondata,
			'nxs_js_shortscopedata' : nxs_js_shortscopedata,
			'nxs_js_popupsessioncontext' : nxs_js_popupsessioncontext,
		}
		nxs_js_popup_stack.push(o);
	}
	else
	{
		// nothing to push
		console.log("popup; push; nothing to push (no popup shows)");
	}
}

function nxs_js_popup_pop()
{
	console.log("popup; pop");

	// pops a level on the stack of popups
	var o = nxs_js_popup_stack.pop();
	
	console.log(o);
	
	if (o != null)
	{
		nxs_js_popupsessiondata = o.nxs_js_popupsessiondata;
		nxs_js_shortscopedata = o.nxs_js_shortscopedata;
		nxs_js_popupsessioncontext = o.nxs_js_popupsessioncontext;
		
		// rerender the new context
		nxs_js_popup_navigateto_v2(nxs_js_popup_getcurrentsheet(), true); 
	}
	else
	{
		// tear down anything that is left
		nxs_js_closepopup_unconditionally();
	}
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
	if (jQ_nxs('#nxs_popup_genericsavebutton').length > 0)
	{
		jQ_nxs('#nxs_popup_genericsavebutton').show();
	}
	else
	{
		nxs_js_log("info: no generic popup save button found");
	}
	
	if (jQ_nxs('#nxs_popup_genericcancelbutton').length > 0)
	{
		jQ_nxs('#nxs_popup_genericcancelbutton').show();
	}
	else
	{
		// nxs_js_log("no generic popup cancel button found?");
	}
	
	if (jQ_nxs('#nxs_popup_genericokbutton').length > 0)
	{
		jQ_nxs('#nxs_popup_genericokbutton').hide();
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
	jQ_nxs(".nxs-popup-dyncontentcontainer input").unbind("input propertychange.makedirty");
	jQ_nxs(".nxs-popup-dyncontentcontainer input").bind("input propertychange.makedirty", function(e) 
	{
		nxs_js_popup_sessiondata_make_dirty();
	});

	//nxs_js_log('nxs_js_popup_processautodirtyhandling');
	
	jQ_nxs(".nxs-popup-dyncontentcontainer input").unbind("keyup.makedirty");
	jQ_nxs(".nxs-popup-dyncontentcontainer input").bind("keyup.makedirty", function(e) 
	{
		if (nxs_js_doeskeycodemakedirty(e))
		{
  		nxs_js_popup_sessiondata_make_dirty();
  	}
	});
	
	jQ_nxs(".nxs-popup-dyncontentcontainer input").unbind("change.makedirty");
	jQ_nxs(".nxs-popup-dyncontentcontainer input").bind("change.makedirty", function(e) 
	{
		nxs_js_popup_sessiondata_make_dirty();
	});
	
	jQ_nxs(".nxs-popup-dyncontentcontainer select").unbind("change.makedirty");
	jQ_nxs(".nxs-popup-dyncontentcontainer select").bind("change.makedirty", function(e) 
	{
		nxs_js_popup_sessiondata_make_dirty();
	});
	
	jQ_nxs(".nxs-popup-dyncontentcontainer textarea").unbind("keyup.makedirty");
	jQ_nxs(".nxs-popup-dyncontentcontainer textarea").bind("keyup.makedirty", function(e) 
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
	
	jQ_nxs("#nxsbox_window .nxs-admin-wrap .nxs_defaultenter").unbind("keyup.defaultenter");
	jQ_nxs("#nxsbox_window .nxs-admin-wrap .nxs_defaultenter").bind("keyup.defaultenter", function(e)
	{
		if (e.keyCode == 13)
		{
			if (jQ_nxs("#nxs_popup_genericsavebutton").length > 0)
			{
				if (jQ_nxs("#nxs_popup_genericsavebutton").is(':visible'))
				{
					//nxs_js_log('default enter clicked');
					e.stopPropagation();
					jQ_nxs("#nxs_popup_genericsavebutton").click();
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
		jQ_nxs('#' + idofelementofocus).focus();
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
	jQ_nxs('#nxsbox_window').effect("shake", { times:3, distance: 10 }, 50);
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
  	nxs_js_popup_setshortscopedata(initialcontextkey, initialcontextvalue);
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
	// allow plugin triggered implementations
	jQ_nxs(document).trigger('nxs_event_popup_closeunconditionally');
	
	if (nxs_js_requirespagerefresh)
	{
		nxs_js_broadcastpopupcloses();
		nxs_js_refreshcurrentpage();
	}
	else
	{
		nxs_js_popupsession_startnewcontext();

		nxs_js_teardownpopupdom();	// removes dom elements, and prepares for the next nxsbox
		nxs_js_hidepopupoverlay();	// removed the popup overlay "sheet"
		
		// re-enable hover menu's
		nxs_js_process_updated_editor_state_silent();
	}
}

function nxs_js_hidepopupoverlay()
{
	jQ_nxs("#nxsbox_overlay").hide();
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
	jQ_nxs(window).trigger('nxs_jstrigger_beforepopupcloses');
}

function nxs_js_teardownpopupdom()
{
	nxs_js_log("broadcast b");
	nxs_js_broadcastpopupcloses();

	jQ_nxs("#nxsbox_window").removeClass("nxs-active");
	jQ_nxs("#nxsbox_window").removeClass("nxs-wasdragged");
	
	// its important to actually hide the nxsboxes,
	// otherwise the GUI event like hover will be intercepted
	jQ_nxs("#nxsbox_window").hide();
	
	//nxs_js_log('found:' + jQ_nxs("#nxsbox_window.nxsboxwindow").length);
	jQ_nxs("#nxsbox_window.nxsboxwindow").each(function(i)
	{
		//nxs_js_log('removing useless nxsbox');
		jQ_nxs(this).remove();
	});
	
	nxs_js_popupshows = false;

	jQ_nxs("body").append("<div id='nxsbox_window' class='nxsboxwindow' style='display:none;'></div>");
	
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

/* Chosen v1.0.0 | (c) 2011-2013 by Harvest | MIT License, https://github.com/harvesthq/chosen/blob/master/LICENSE.md */
!function(){var a,AbstractChosen,Chosen,SelectParser,b,c={}.hasOwnProperty,d=function(a,b){function d(){this.constructor=a}for(var e in b)c.call(b,e)&&(a[e]=b[e]);return d.prototype=b.prototype,a.prototype=new d,a.__super__=b.prototype,a};SelectParser=function(){function SelectParser(){this.options_index=0,this.parsed=[]}return SelectParser.prototype.add_node=function(a){return"OPTGROUP"===a.nodeName.toUpperCase()?this.add_group(a):this.add_option(a)},SelectParser.prototype.add_group=function(a){var b,c,d,e,f,g;for(b=this.parsed.length,this.parsed.push({array_index:b,group:!0,label:this.escapeExpression(a.label),children:0,disabled:a.disabled}),f=a.childNodes,g=[],d=0,e=f.length;e>d;d++)c=f[d],g.push(this.add_option(c,b,a.disabled));return g},SelectParser.prototype.add_option=function(a,b,c){return"OPTION"===a.nodeName.toUpperCase()?(""!==a.text?(null!=b&&(this.parsed[b].children+=1),this.parsed.push({array_index:this.parsed.length,options_index:this.options_index,value:a.value,text:a.text,html:a.innerHTML,selected:a.selected,disabled:c===!0?c:a.disabled,group_array_index:b,classes:a.className,style:a.style.cssText})):this.parsed.push({array_index:this.parsed.length,options_index:this.options_index,empty:!0}),this.options_index+=1):void 0},SelectParser.prototype.escapeExpression=function(a){var b,c;return null==a||a===!1?"":/[\&\<\>\"\'\`]/.test(a)?(b={"<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#x27;","`":"&#x60;"},c=/&(?!\w+;)|[\<\>\"\'\`]/g,a.replace(c,function(a){return b[a]||"&amp;"})):a},SelectParser}(),SelectParser.select_to_array=function(a){var b,c,d,e,f;for(c=new SelectParser,f=a.childNodes,d=0,e=f.length;e>d;d++)b=f[d],c.add_node(b);return c.parsed},AbstractChosen=function(){function AbstractChosen(a,b){this.form_field=a,this.options=null!=b?b:{},AbstractChosen.browser_is_supported()&&(this.is_multiple=this.form_field.multiple,this.set_default_text(),this.set_default_values(),this.setup(),this.set_up_html(),this.register_observers())}return AbstractChosen.prototype.set_default_values=function(){var a=this;return this.click_test_action=function(b){return a.test_active_click(b)},this.activate_action=function(b){return a.activate_field(b)},this.active_field=!1,this.mouse_on_container=!1,this.results_showing=!1,this.result_highlighted=null,this.result_single_selected=null,this.allow_single_deselect=null!=this.options.allow_single_deselect&&null!=this.form_field.options[0]&&""===this.form_field.options[0].text?this.options.allow_single_deselect:!1,this.disable_search_threshold=this.options.disable_search_threshold||0,this.disable_search=this.options.disable_search||!1,this.enable_split_word_search=null!=this.options.enable_split_word_search?this.options.enable_split_word_search:!0,this.group_search=null!=this.options.group_search?this.options.group_search:!0,this.search_contains=this.options.search_contains||!1,this.single_backstroke_delete=null!=this.options.single_backstroke_delete?this.options.single_backstroke_delete:!0,this.max_selected_options=this.options.max_selected_options||1/0,this.inherit_select_classes=this.options.inherit_select_classes||!1,this.display_selected_options=null!=this.options.display_selected_options?this.options.display_selected_options:!0,this.display_disabled_options=null!=this.options.display_disabled_options?this.options.display_disabled_options:!0},AbstractChosen.prototype.set_default_text=function(){return this.default_text=this.form_field.getAttribute("data-placeholder")?this.form_field.getAttribute("data-placeholder"):this.is_multiple?this.options.placeholder_text_multiple||this.options.placeholder_text||AbstractChosen.default_multiple_text:this.options.placeholder_text_single||this.options.placeholder_text||AbstractChosen.default_single_text,this.results_none_found=this.form_field.getAttribute("data-no_results_text")||this.options.no_results_text||AbstractChosen.default_no_result_text},AbstractChosen.prototype.mouse_enter=function(){return this.mouse_on_container=!0},AbstractChosen.prototype.mouse_leave=function(){return this.mouse_on_container=!1},AbstractChosen.prototype.input_focus=function(){var a=this;if(this.is_multiple){if(!this.active_field)return setTimeout(function(){return a.container_mousedown()},50)}else if(!this.active_field)return this.activate_field()},AbstractChosen.prototype.input_blur=function(){var a=this;return this.mouse_on_container?void 0:(this.active_field=!1,setTimeout(function(){return a.blur_test()},100))},AbstractChosen.prototype.results_option_build=function(a){var b,c,d,e,f;for(b="",f=this.results_data,d=0,e=f.length;e>d;d++)c=f[d],b+=c.group?this.result_add_group(c):this.result_add_option(c),(null!=a?a.first:void 0)&&(c.selected&&this.is_multiple?this.choice_build(c):c.selected&&!this.is_multiple&&this.single_set_selected_text(c.text));return b},AbstractChosen.prototype.result_add_option=function(a){var b,c;return a.search_match?this.include_option_in_results(a)?(b=[],a.disabled||a.selected&&this.is_multiple||b.push("active-result"),!a.disabled||a.selected&&this.is_multiple||b.push("disabled-result"),a.selected&&b.push("result-selected"),null!=a.group_array_index&&b.push("group-option"),""!==a.classes&&b.push(a.classes),c=""!==a.style.cssText?' style="'+a.style+'"':"",'<li class="'+b.join(" ")+'"'+c+' data-option-array-index="'+a.array_index+'">'+a.search_text+"</li>"):"":""},AbstractChosen.prototype.result_add_group=function(a){return a.search_match||a.group_match?a.active_options>0?'<li class="group-result">'+a.search_text+"</li>":"":""},AbstractChosen.prototype.results_update_field=function(){return this.set_default_text(),this.is_multiple||this.results_reset_cleanup(),this.result_clear_highlight(),this.result_single_selected=null,this.results_build(),this.results_showing?this.winnow_results():void 0},AbstractChosen.prototype.results_toggle=function(){return this.results_showing?this.results_hide():this.results_show()},AbstractChosen.prototype.results_search=function(){return this.results_showing?this.winnow_results():this.results_show()},AbstractChosen.prototype.winnow_results=function(){var a,b,c,d,e,f,g,h,i,j,k,l,m;for(this.no_results_clear(),e=0,g=this.get_search_text(),a=g.replace(/[-[\]{}()*+?.,\\^$|#\s]/g,"\\$&"),d=this.search_contains?"":"^",c=new RegExp(d+a,"i"),j=new RegExp(a,"i"),m=this.results_data,k=0,l=m.length;l>k;k++)b=m[k],b.search_match=!1,f=null,this.include_option_in_results(b)&&(b.group&&(b.group_match=!1,b.active_options=0),null!=b.group_array_index&&this.results_data[b.group_array_index]&&(f=this.results_data[b.group_array_index],0===f.active_options&&f.search_match&&(e+=1),f.active_options+=1),(!b.group||this.group_search)&&(b.search_text=b.group?b.label:b.html,b.search_match=this.search_string_match(b.search_text,c),b.search_match&&!b.group&&(e+=1),b.search_match?(g.length&&(h=b.search_text.search(j),i=b.search_text.substr(0,h+g.length)+"</em>"+b.search_text.substr(h+g.length),b.search_text=i.substr(0,h)+"<em>"+i.substr(h)),null!=f&&(f.group_match=!0)):null!=b.group_array_index&&this.results_data[b.group_array_index].search_match&&(b.search_match=!0)));return this.result_clear_highlight(),1>e&&g.length?(this.update_results_content(""),this.no_results(g)):(this.update_results_content(this.results_option_build()),this.winnow_results_set_highlight())},AbstractChosen.prototype.search_string_match=function(a,b){var c,d,e,f;if(b.test(a))return!0;if(this.enable_split_word_search&&(a.indexOf(" ")>=0||0===a.indexOf("["))&&(d=a.replace(/\[|\]/g,"").split(" "),d.length))for(e=0,f=d.length;f>e;e++)if(c=d[e],b.test(c))return!0},AbstractChosen.prototype.choices_count=function(){var a,b,c,d;if(null!=this.selected_option_count)return this.selected_option_count;for(this.selected_option_count=0,d=this.form_field.options,b=0,c=d.length;c>b;b++)a=d[b],a.selected&&(this.selected_option_count+=1);return this.selected_option_count},AbstractChosen.prototype.choices_click=function(a){return a.preventDefault(),this.results_showing||this.is_disabled?void 0:this.results_show()},AbstractChosen.prototype.keyup_checker=function(a){var b,c;switch(b=null!=(c=a.which)?c:a.keyCode,this.search_field_scale(),b){case 8:if(this.is_multiple&&this.backstroke_length<1&&this.choices_count()>0)return this.keydown_backstroke();if(!this.pending_backstroke)return this.result_clear_highlight(),this.results_search();break;case 13:if(a.preventDefault(),this.results_showing)return this.result_select(a);break;case 27:return this.results_showing&&this.results_hide(),!0;case 9:case 38:case 40:case 16:case 91:case 17:break;default:return this.results_search()}},AbstractChosen.prototype.container_width=function(){return null!=this.options.width?this.options.width:""+this.form_field.offsetWidth+"px"},AbstractChosen.prototype.include_option_in_results=function(a){return this.is_multiple&&!this.display_selected_options&&a.selected?!1:!this.display_disabled_options&&a.disabled?!1:a.empty?!1:!0},AbstractChosen.browser_is_supported=function(){return"Microsoft Internet Explorer"===window.navigator.appName?document.documentMode>=8:/iP(od|hone)/i.test(window.navigator.userAgent)?!1:/Android/i.test(window.navigator.userAgent)&&/Mobile/i.test(window.navigator.userAgent)?!1:!0},AbstractChosen.default_multiple_text="Select Some Options",AbstractChosen.default_single_text="Select an Option",AbstractChosen.default_no_result_text="No results match",AbstractChosen}(),a=jQuery,a.fn.extend({chosen:function(b){return AbstractChosen.browser_is_supported()?this.each(function(){var c,d;c=a(this),d=c.data("chosen"),"destroy"===b&&d?d.destroy():d||c.data("chosen",new Chosen(this,b))}):this}}),Chosen=function(c){function Chosen(){return b=Chosen.__super__.constructor.apply(this,arguments)}return d(Chosen,c),Chosen.prototype.setup=function(){return this.form_field_jq=a(this.form_field),this.current_selectedIndex=this.form_field.selectedIndex,this.is_rtl=this.form_field_jq.hasClass("chosen-rtl")},Chosen.prototype.set_up_html=function(){var b,c;return b=["chosen-container"],b.push("chosen-container-"+(this.is_multiple?"multi":"single")),this.inherit_select_classes&&this.form_field.className&&b.push(this.form_field.className),this.is_rtl&&b.push("chosen-rtl"),c={"class":b.join(" "),style:"width: "+this.container_width()+";",title:this.form_field.title},this.form_field.id.length&&(c.id=this.form_field.id.replace(/[^\w]/g,"_")+"_chosen"),this.container=a("<div />",c),this.is_multiple?this.container.html('<ul class="chosen-choices"><li class="search-field"><input type="text" value="'+this.default_text+'" class="default" autocomplete="off" style="width:25px;" /></li></ul><div class="chosen-drop"><ul class="chosen-results"></ul></div>'):this.container.html('<a class="chosen-single chosen-default" tabindex="-1"><span>'+this.default_text+'</span><div><b></b></div></a><div class="chosen-drop"><div class="chosen-search"><input type="text" autocomplete="off" /></div><ul class="chosen-results"></ul></div>'),this.form_field_jq.hide().after(this.container),this.dropdown=this.container.find("div.chosen-drop").first(),this.search_field=this.container.find("input").first(),this.search_results=this.container.find("ul.chosen-results").first(),this.search_field_scale(),this.search_no_results=this.container.find("li.no-results").first(),this.is_multiple?(this.search_choices=this.container.find("ul.chosen-choices").first(),this.search_container=this.container.find("li.search-field").first()):(this.search_container=this.container.find("div.chosen-search").first(),this.selected_item=this.container.find(".chosen-single").first()),this.results_build(),this.set_tab_index(),this.set_label_behavior(),this.form_field_jq.trigger("chosen:ready",{chosen:this})},Chosen.prototype.register_observers=function(){var a=this;return this.container.bind("mousedown.chosen",function(b){a.container_mousedown(b)}),this.container.bind("mouseup.chosen",function(b){a.container_mouseup(b)}),this.container.bind("mouseenter.chosen",function(b){a.mouse_enter(b)}),this.container.bind("mouseleave.chosen",function(b){a.mouse_leave(b)}),this.search_results.bind("mouseup.chosen",function(b){a.search_results_mouseup(b)}),this.search_results.bind("mouseover.chosen",function(b){a.search_results_mouseover(b)}),this.search_results.bind("mouseout.chosen",function(b){a.search_results_mouseout(b)}),this.search_results.bind("mousewheel.chosen DOMMouseScroll.chosen",function(b){a.search_results_mousewheel(b)}),this.form_field_jq.bind("chosen:updated.chosen",function(b){a.results_update_field(b)}),this.form_field_jq.bind("chosen:activate.chosen",function(b){a.activate_field(b)}),this.form_field_jq.bind("chosen:open.chosen",function(b){a.container_mousedown(b)}),this.search_field.bind("blur.chosen",function(b){a.input_blur(b)}),this.search_field.bind("keyup.chosen",function(b){a.keyup_checker(b)}),this.search_field.bind("keydown.chosen",function(b){a.keydown_checker(b)}),this.search_field.bind("focus.chosen",function(b){a.input_focus(b)}),this.is_multiple?this.search_choices.bind("click.chosen",function(b){a.choices_click(b)}):this.container.bind("click.chosen",function(a){a.preventDefault()})},Chosen.prototype.destroy=function(){return a(document).unbind("click.chosen",this.click_test_action),this.search_field[0].tabIndex&&(this.form_field_jq[0].tabIndex=this.search_field[0].tabIndex),this.container.remove(),this.form_field_jq.removeData("chosen"),this.form_field_jq.show()},Chosen.prototype.search_field_disabled=function(){return this.is_disabled=this.form_field_jq[0].disabled,this.is_disabled?(this.container.addClass("chosen-disabled"),this.search_field[0].disabled=!0,this.is_multiple||this.selected_item.unbind("focus.chosen",this.activate_action),this.close_field()):(this.container.removeClass("chosen-disabled"),this.search_field[0].disabled=!1,this.is_multiple?void 0:this.selected_item.bind("focus.chosen",this.activate_action))},Chosen.prototype.container_mousedown=function(b){return this.is_disabled||(b&&"mousedown"===b.type&&!this.results_showing&&b.preventDefault(),null!=b&&a(b.target).hasClass("search-choice-close"))?void 0:(this.active_field?this.is_multiple||!b||a(b.target)[0]!==this.selected_item[0]&&!a(b.target).parents("a.chosen-single").length||(b.preventDefault(),this.results_toggle()):(this.is_multiple&&this.search_field.val(""),a(document).bind("click.chosen",this.click_test_action),this.results_show()),this.activate_field())},Chosen.prototype.container_mouseup=function(a){return"ABBR"!==a.target.nodeName||this.is_disabled?void 0:this.results_reset(a)},Chosen.prototype.search_results_mousewheel=function(a){var b,c,d;return b=-(null!=(c=a.originalEvent)?c.wheelDelta:void 0)||(null!=(d=a.originialEvent)?d.detail:void 0),null!=b?(a.preventDefault(),"DOMMouseScroll"===a.type&&(b=40*b),this.search_results.scrollTop(b+this.search_results.scrollTop())):void 0},Chosen.prototype.blur_test=function(){return!this.active_field&&this.container.hasClass("chosen-container-active")?this.close_field():void 0},Chosen.prototype.close_field=function(){return a(document).unbind("click.chosen",this.click_test_action),this.active_field=!1,this.results_hide(),this.container.removeClass("chosen-container-active"),this.clear_backstroke(),this.show_search_field_default(),this.search_field_scale()},Chosen.prototype.activate_field=function(){return this.container.addClass("chosen-container-active"),this.active_field=!0,this.search_field.val(this.search_field.val()),this.search_field.focus()},Chosen.prototype.test_active_click=function(b){return this.container.is(a(b.target).closest(".chosen-container"))?this.active_field=!0:this.close_field()},Chosen.prototype.results_build=function(){return this.parsing=!0,this.selected_option_count=null,this.results_data=SelectParser.select_to_array(this.form_field),this.is_multiple?this.search_choices.find("li.search-choice").remove():this.is_multiple||(this.single_set_selected_text(),this.disable_search||this.form_field.options.length<=this.disable_search_threshold?(this.search_field[0].readOnly=!0,this.container.addClass("chosen-container-single-nosearch")):(this.search_field[0].readOnly=!1,this.container.removeClass("chosen-container-single-nosearch"))),this.update_results_content(this.results_option_build({first:!0})),this.search_field_disabled(),this.show_search_field_default(),this.search_field_scale(),this.parsing=!1},Chosen.prototype.result_do_highlight=function(a){var b,c,d,e,f;if(a.length){if(this.result_clear_highlight(),this.result_highlight=a,this.result_highlight.addClass("highlighted"),d=parseInt(this.search_results.css("maxHeight"),10),f=this.search_results.scrollTop(),e=d+f,c=this.result_highlight.position().top+this.search_results.scrollTop(),b=c+this.result_highlight.outerHeight(),b>=e)return this.search_results.scrollTop(b-d>0?b-d:0);if(f>c)return this.search_results.scrollTop(c)}},Chosen.prototype.result_clear_highlight=function(){return this.result_highlight&&this.result_highlight.removeClass("highlighted"),this.result_highlight=null},Chosen.prototype.results_show=function(){return this.is_multiple&&this.max_selected_options<=this.choices_count()?(this.form_field_jq.trigger("chosen:maxselected",{chosen:this}),!1):(this.container.addClass("chosen-with-drop"),this.form_field_jq.trigger("chosen:showing_dropdown",{chosen:this}),this.results_showing=!0,this.search_field.focus(),this.search_field.val(this.search_field.val()),this.winnow_results())},Chosen.prototype.update_results_content=function(a){return this.search_results.html(a)},Chosen.prototype.results_hide=function(){return this.results_showing&&(this.result_clear_highlight(),this.container.removeClass("chosen-with-drop"),this.form_field_jq.trigger("chosen:hiding_dropdown",{chosen:this})),this.results_showing=!1},Chosen.prototype.set_tab_index=function(){var a;return this.form_field.tabIndex?(a=this.form_field.tabIndex,this.form_field.tabIndex=-1,this.search_field[0].tabIndex=a):void 0},Chosen.prototype.set_label_behavior=function(){var b=this;return this.form_field_label=this.form_field_jq.parents("label"),!this.form_field_label.length&&this.form_field.id.length&&(this.form_field_label=a("label[for='"+this.form_field.id+"']")),this.form_field_label.length>0?this.form_field_label.bind("click.chosen",function(a){return b.is_multiple?b.container_mousedown(a):b.activate_field()}):void 0},Chosen.prototype.show_search_field_default=function(){return this.is_multiple&&this.choices_count()<1&&!this.active_field?(this.search_field.val(this.default_text),this.search_field.addClass("default")):(this.search_field.val(""),this.search_field.removeClass("default"))},Chosen.prototype.search_results_mouseup=function(b){var c;return c=a(b.target).hasClass("active-result")?a(b.target):a(b.target).parents(".active-result").first(),c.length?(this.result_highlight=c,this.result_select(b),this.search_field.focus()):void 0},Chosen.prototype.search_results_mouseover=function(b){var c;return c=a(b.target).hasClass("active-result")?a(b.target):a(b.target).parents(".active-result").first(),c?this.result_do_highlight(c):void 0},Chosen.prototype.search_results_mouseout=function(b){return a(b.target).hasClass("active-result")?this.result_clear_highlight():void 0},Chosen.prototype.choice_build=function(b){var c,d,e=this;return c=a("<li />",{"class":"search-choice"}).html("<span>"+b.html+"</span>"),b.disabled?c.addClass("search-choice-disabled"):(d=a("<a />",{"class":"search-choice-close","data-option-array-index":b.array_index}),d.bind("click.chosen",function(a){return e.choice_destroy_link_click(a)}),c.append(d)),this.search_container.before(c)},Chosen.prototype.choice_destroy_link_click=function(b){return b.preventDefault(),b.stopPropagation(),this.is_disabled?void 0:this.choice_destroy(a(b.target))},Chosen.prototype.choice_destroy=function(a){return this.result_deselect(a[0].getAttribute("data-option-array-index"))?(this.show_search_field_default(),this.is_multiple&&this.choices_count()>0&&this.search_field.val().length<1&&this.results_hide(),a.parents("li").first().remove(),this.search_field_scale()):void 0},Chosen.prototype.results_reset=function(){return this.form_field.options[0].selected=!0,this.selected_option_count=null,this.single_set_selected_text(),this.show_search_field_default(),this.results_reset_cleanup(),this.form_field_jq.trigger("change"),this.active_field?this.results_hide():void 0},Chosen.prototype.results_reset_cleanup=function(){return this.current_selectedIndex=this.form_field.selectedIndex,this.selected_item.find("abbr").remove()},Chosen.prototype.result_select=function(a){var b,c,d;return this.result_highlight?(b=this.result_highlight,this.result_clear_highlight(),this.is_multiple&&this.max_selected_options<=this.choices_count()?(this.form_field_jq.trigger("chosen:maxselected",{chosen:this}),!1):(this.is_multiple?b.removeClass("active-result"):(this.result_single_selected&&(this.result_single_selected.removeClass("result-selected"),d=this.result_single_selected[0].getAttribute("data-option-array-index"),this.results_data[d].selected=!1),this.result_single_selected=b),b.addClass("result-selected"),c=this.results_data[b[0].getAttribute("data-option-array-index")],c.selected=!0,this.form_field.options[c.options_index].selected=!0,this.selected_option_count=null,this.is_multiple?this.choice_build(c):this.single_set_selected_text(c.text),(a.metaKey||a.ctrlKey)&&this.is_multiple||this.results_hide(),this.search_field.val(""),(this.is_multiple||this.form_field.selectedIndex!==this.current_selectedIndex)&&this.form_field_jq.trigger("change",{selected:this.form_field.options[c.options_index].value}),this.current_selectedIndex=this.form_field.selectedIndex,this.search_field_scale())):void 0},Chosen.prototype.single_set_selected_text=function(a){return null==a&&(a=this.default_text),a===this.default_text?this.selected_item.addClass("chosen-default"):(this.single_deselect_control_build(),this.selected_item.removeClass("chosen-default")),this.selected_item.find("span").text(a)},Chosen.prototype.result_deselect=function(a){var b;return b=this.results_data[a],this.form_field.options[b.options_index].disabled?!1:(b.selected=!1,this.form_field.options[b.options_index].selected=!1,this.selected_option_count=null,this.result_clear_highlight(),this.results_showing&&this.winnow_results(),this.form_field_jq.trigger("change",{deselected:this.form_field.options[b.options_index].value}),this.search_field_scale(),!0)},Chosen.prototype.single_deselect_control_build=function(){return this.allow_single_deselect?(this.selected_item.find("abbr").length||this.selected_item.find("span").first().after('<abbr class="search-choice-close"></abbr>'),this.selected_item.addClass("chosen-single-with-deselect")):void 0},Chosen.prototype.get_search_text=function(){return this.search_field.val()===this.default_text?"":a("<div/>").text(a.trim(this.search_field.val())).html()},Chosen.prototype.winnow_results_set_highlight=function(){var a,b;return b=this.is_multiple?[]:this.search_results.find(".result-selected.active-result"),a=b.length?b.first():this.search_results.find(".active-result").first(),null!=a?this.result_do_highlight(a):void 0},Chosen.prototype.no_results=function(b){var c;return c=a('<li class="no-results">'+this.results_none_found+' "<span></span>"</li>'),c.find("span").first().html(b),this.search_results.append(c)},Chosen.prototype.no_results_clear=function(){return this.search_results.find(".no-results").remove()},Chosen.prototype.keydown_arrow=function(){var a;return this.results_showing&&this.result_highlight?(a=this.result_highlight.nextAll("li.active-result").first())?this.result_do_highlight(a):void 0:this.results_show()},Chosen.prototype.keyup_arrow=function(){var a;return this.results_showing||this.is_multiple?this.result_highlight?(a=this.result_highlight.prevAll("li.active-result"),a.length?this.result_do_highlight(a.first()):(this.choices_count()>0&&this.results_hide(),this.result_clear_highlight())):void 0:this.results_show()},Chosen.prototype.keydown_backstroke=function(){var a;return this.pending_backstroke?(this.choice_destroy(this.pending_backstroke.find("a").first()),this.clear_backstroke()):(a=this.search_container.siblings("li.search-choice").last(),a.length&&!a.hasClass("search-choice-disabled")?(this.pending_backstroke=a,this.single_backstroke_delete?this.keydown_backstroke():this.pending_backstroke.addClass("search-choice-focus")):void 0)},Chosen.prototype.clear_backstroke=function(){return this.pending_backstroke&&this.pending_backstroke.removeClass("search-choice-focus"),this.pending_backstroke=null},Chosen.prototype.keydown_checker=function(a){var b,c;switch(b=null!=(c=a.which)?c:a.keyCode,this.search_field_scale(),8!==b&&this.pending_backstroke&&this.clear_backstroke(),b){case 8:this.backstroke_length=this.search_field.val().length;break;case 9:this.results_showing&&!this.is_multiple&&this.result_select(a),this.mouse_on_container=!1;break;case 13:a.preventDefault();break;case 38:a.preventDefault(),this.keyup_arrow();break;case 40:a.preventDefault(),this.keydown_arrow()}},Chosen.prototype.search_field_scale=function(){var b,c,d,e,f,g,h,i,j;if(this.is_multiple){for(d=0,h=0,f="position:absolute; left: -1000px; top: -1000px; display:none;",g=["font-size","font-style","font-weight","font-family","line-height","text-transform","letter-spacing"],i=0,j=g.length;j>i;i++)e=g[i],f+=e+":"+this.search_field.css(e)+";";return b=a("<div />",{style:f}),b.text(this.search_field.val()),a("body").append(b),h=b.width()+25,b.remove(),c=this.container.outerWidth(),h>c-10&&(h=c-10),this.search_field.css({width:h+"px"})}},Chosen}(AbstractChosen)}.call(this);

		
(
	function() 
	{
		nxs_js_log("nxs-script-deferred.js ******");
		
		//
		nxs_js_log("loading css [start]");
		
		nxs_js_requirescript('nxs_admin_css', 'css', nxs_js_getframeworkurl() + '/css/admin.css', function(){nxs_js_log("loaded admin.css");});
		
		// optimization: the chosen script is only loaded if the user is logged on
		if (nxs_js_isuserloggedin())
		{
			nxs_js_requirescript('nxs_chosen_css', 'css', nxs_js_getframeworkurl() + '/css/chosen.min.css', function(){nxs_js_log("loaded chosen.min.css");});
		}
		
		nxs_js_log("loading css [done]");
	}
)
();	