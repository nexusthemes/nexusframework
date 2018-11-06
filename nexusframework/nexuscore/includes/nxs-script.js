/*************************************************************************/
/*************************************************************************/
/*
    Copyright 2012-2016 Nexus Themes
*/
/*************************************************************************/
/*************************************************************************/

var nxs_js_windowhasfocus = true;
var nxs_js_mapslazyloaded = false;
var nxs_js_mapslazyloading = false;
var nxs_js_maps = { };

var nxs_js_panolazyloaded = false;
var nxs_js_panolazyloading = false;
var nxs_js_panos = { };

var nxs_js_requirespagerefresh = false;
var nxs_js_activeviewport = -1;

var nxs_js_nxseditoractive = false;
var nxs_js_editorstategrowltoken;

var nxs_js_nxsmenuactive = true;
var nxs_js_menustategrowltoken;

var nxs_js_nxsisdragging = false;

var nxs_js_runtimedimensionsrefreshtriggers = new Array();
var nxs_js_queuedruntimedimensionsrefreshtriggers = new Array();
var nxs_js_isruntimecssrefreshqueued = false;
var nxs_js_isctrlkeydown = false;
var nxs_js_seorefreshtriggers = { };
var nxs_js_isseorefreshqueued = false;
var nxs_js_vendorprefix_internal = null;

var nxs_js_lazyloadedscripts = { };
var nxs_js_lazyloadingscripts = { };
var nxsboxL10n = {  };

// scroll revert state (used to track pushing and popping of scroll positions)
var nxs_js_scrollstatestack = { };
var nxs_js_scrollstateidentifier = 0;

var nxs_js_lazyloadinggrowl = false;
var nxs_js_queuestatelookup = {};

function nxs_js_popup_showhtml(html)
{
	nxs_js_popupsession_startnewcontext();
	
	
	nxs_js_popup_setsessioncontext("popup_current_dimensions", "gallerybox");
	nxs_js_popup_setsessioncontext("contextprocessor", "site");
	nxs_js_popup_setsessiondata("nxs_customhtml_popupheadertitle", "popup title");
	nxs_js_popup_setsessiondata("nxs_customhtml_scaffoldingtype", "nothing");
	nxs_js_popup_setsessiondata("nxs_customhtml_customhtmlcanvascontent", html);
	nxs_js_popup_navigateto("customhtml");
}

function nxs_js_replaceall(find, replace, str) 
{
  return str.replace(new RegExp(find, 'g'), replace);
}

(
	function($) 
	{
		$html = $('html');
	
		function nxs_js_nxsgrid_resize_actual() 
		{
			if ($(".nxsgrid-container").width() < 481) 
			{
			  return $html.addClass('nxsgrid-mobile-480');
			}
			else	        	
			{
				$html.removeClass('nxsgrid-mobile-480');
			}
			
			if ($(".nxsgrid-container").width() < 721) 
			{
			  return $html.addClass('nxsgrid-mobile-720');
			}
			else	        	
			{
				$html.removeClass('nxsgrid-mobile-720');
			}
		}
	
		// invoke (throttled) if user resizes	      
		var nxs_js_nxsgrid_resize_doit;
		window.onresize = function()
		{
			// do a "throttled" invocation, 
			// to prevent stressing the CPU while resizing the screen
		  clearTimeout(nxs_js_nxsgrid_resize_doit);
		  nxs_js_nxsgrid_resize_doit = setTimeout(nxs_js_nxsgrid_resize_actual, 100);
		};	   
		
		// invoke once at the moment the page is loaded for the first time
		// (proper initialization of the responsive aspects)
		$(window).load
	  (
			function()
			{
				$(window).trigger('resize');
			}
	  );
	}
)
(jQuery);

// fix for jQuery 1.9
jQuery.browser = {};
jQuery.browser.mozilla = /mozilla/.test(navigator.userAgent.toLowerCase()) && !/webkit/.test(navigator.userAgent.toLowerCase());
jQuery.browser.webkit = /webkit/.test(navigator.userAgent.toLowerCase());
jQuery.browser.opera = /opera/.test(navigator.userAgent.toLowerCase());
jQuery.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());

// see http://api.jquery.com/jQuery.browser/
function nxs_js_getvendorprefix()
{
	if (nxs_js_vendorprefix_internal == null)
	{
		// derive it 1x
		var browser = jQuery.browser;
		
		//nxs_js_log(navigator.userAgent);
		
		if (navigator.userAgent.indexOf("Opera") >= 0 || navigator.userAgent.indexOf("OPR") >= 0)
		{
			nxs_js_vendorprefix_internal = "o";
		}
		else if (browser.webkit == true)
		{
			nxs_js_vendorprefix_internal = "webkit";
		}
		else if (browser.msie)
		{
			nxs_js_vendorprefix_internal = "msie";
		}
		else if (navigator.userAgent.indexOf("Trident") > -1)
    {
    	// http://stackoverflow.com/questions/20911175/ie-11-browser-recognizes-itself-as-mozilla
      nxs_js_vendorprefix_internal = "msie";
    }
		else if (browser.mozilla)
		{
			nxs_js_vendorprefix_internal = "mozilla";
		}
		else
		{
			nxs_js_vendorprefix_internal = "unknown";
		}
	}
	return nxs_js_vendorprefix_internal;
}

//
var nxs_allloaded;
nxs_allloaded = false;
jQ_nxs(window).on("load", function() 
{
	nxs_allloaded = true;
}
);

function nxs_js_okforscreenshot()
{
	// disable the scrollbar
	var css = '::-webkit-scrollbar { display: none; }',
	head = document.head || document.getElementsByTagName('head')[0],
	style = document.createElement('style');
	style.type = 'text/css';
	if (style.styleSheet){
	  style.styleSheet.cssText = css;
	} else {
	  style.appendChild(document.createTextNode(css));
	}
	head.appendChild(style);
	
	return nxs_allloaded;
}

var nxs_js_popup_stack = [];


// the popupsessiondata is empty initially.
// by filling this variable from the client (js),
// a state is constructed that is accessible by both the client (js) as well as 
// serverside (php). Values stored by the client are accessible in the upcoming
// server post. By using the same keys as the variable names on the serverside,
// its possible to create a temporary client side state, temporarily overriding
// values persisted on the server side.
// This state can be shared between multiple popup pages ("sheets").
// This enables clients to even have a "cancel" button, wiping just only this
// temporarily state, and not overriding persisted values if that was unwanted.
var nxs_js_popupsessiondata = {};	// key values like dictionary

// the shortscopedata is similar to popupsessiondata, difference is that the shortscopedata is wiped
// so data in here is only available in the next server invocation and lost aftwards. This 
// can be used to store triggers/events such a specific event, that should not be persisted,
// but only result in a certain server side invocation
var nxs_js_shortscopedata = {};	// key values like dictionary

// similar to popupsessiondata, difference is that this container contains information about
// the meta data, like what sheet is being rendered and other contextual data.
var nxs_js_popupsessioncontext = {};	// key values like dictionary
var nxs_js_popupshows = false;

var nxs_js_menuid_preventendlessloop;
var nxs_js_menuid_siblingcounters;

var nxs_js_alert_identifier = 0;

jQ_nxs(window).ready
(
	function()
	{
		nxsboxL10n = { loadingAnimation: nxs_js_getframeworkurl() + "/images/loadingnxsbox.png" };
		
		nxs_js_tagviewports();
		nxs_js_tagdevices();
		nxs_js_tagbrowsers();
		nxs_js_tagfrontendbackend();
		nxs_js_tagresponsiveness();
		// some images load slower than the jquery code is executed.
		// in that case the screen needs to be realigned
		nxs_js_reenable_all_window_events();
		//
		nxs_js_processquerystring();
		
		// reload editor state, based on cookie
		nxs_js_initiateeditorstate();
		
		// reload menu state, based on cookie
		nxs_js_initiatemenustate();
		
		// no background scrolling when hovering over admin top menu
		//nxs_js_disabledocumentscrollwhenhoveringoverelement("#vg_manualcss");
		
		nxs_js_reenable_all_window_events();
		nxs_js_register_windowresizedend_event();
		nxs_js_hook_windowsresizeend_event();
		
		nxs_js_setupwindowscrolllistener();
		nxs_js_setupviewportlistener();

		// after fonts are loaded, re-enable window events/ height-iq
		jQ_nxs(window).bind("load", function() 
		{
			nxs_js_reenable_all_window_events();
			
			jQ_nxs(window).blur(function(){
			  nxs_js_windowhasfocus = false;
			  //nxs_js_log("no focus");

	  		jQ_nxs("html").removeClass("nxs-window-focus");			  
 			  jQ_nxs("html").addClass("nxs-window-blur");
			});
			jQ_nxs(window).focus(function()
			{			
			  nxs_js_windowhasfocus = true;
			  //nxs_js_log("has focus");
			  
 			  jQ_nxs("html").removeClass("nxs-window-blur");
	  		jQ_nxs("html").addClass("nxs-window-focus");			  

			});
		});

		// see http://stackoverflow.com/questions/6677181/how-to-know-when-font-face-has-been-applied
		document.onreadystatechange = function() {
    if (document.readyState === 'complete') 
    {
    	nxs_js_reenable_all_window_events();
    }
};

	}
);

function nxs_js_getdocheight() 
{
  var D = document;
  return Math.max(
      D.body.scrollHeight, D.documentElement.scrollHeight,
      D.body.offsetHeight, D.documentElement.offsetHeight,
      D.body.clientHeight, D.documentElement.clientHeight
  );
}

function nxs_js_setupwindowscrolllistener()
{
	jQ_nxs(window).scroll
	(
		function() 		
		{
			var scrolltop = window.pageYOffset || document.documentElement.scrollTop;
			
			if (scrolltop == 0)
			{
				// "edge" case; if the scroll position is 0,
				// it means we ended up at the top; this is -always- the end of the scrolling
				//nxs_js_log("scroll;edge; TOP");
				jQ_nxs(document).trigger('nxs_event_windowscrolled');
			}
			else if (jQ_nxs(window).scrollTop() + jQ_nxs(window).height() == nxs_js_getdocheight()) 
			{
				// "edge" case; if the scroll position is 100% at the bottom,
				// it means we ended up at the top; this is -always- the end of the scrolling
				//nxs_js_log("scroll;edge; BOTTOM");
				jQ_nxs(document).trigger('nxs_event_windowscrolled');
			}
			else
			{			
				// fire that we're scrolling right NOW
		  	nxs_js_invokethrottled("scrolling", 10, function(){
		  		jQ_nxs(document).trigger('nxs_event_windowscrolling');
		  	});
		  			
		  	// check if the scrolling stopped	
				if (this.scrollTO) clearTimeout(this.scrollTO);
		  	this.scrollTO = setTimeout
		  	(
		  		function() 
		  		{
		  			jQ_nxs(document).trigger('nxs_event_windowscrolled');
		  		}
		  		, 
		  		50
		  	);
		  }
		}
	);
}

function nxs_js_getscrollspeed(location) {
	var scrollTop = jQ_nxs(window).scrollTop();
	var dif = Math.abs(scrollTop - location);
	var speed = 300 + Math.round(dif / 5);
	return speed;
}

// FX --- BEGIN

function nxs_js_getwindowheight() 
{
  var myWidth = 0, myHeight = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
      //Non-IE
      myHeight = window.innerHeight;
  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
      //IE 6+ in 'standards compliant mode'
      myHeight = document.documentElement.clientHeight;
  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
      //IE 4 compatible
      myHeight = document.body.clientHeight;
	}

	return myHeight
}

function nxs_js_appearbox(element, element_top, bottom_of_window) 
{
  /* If the object is completely visible in the window, fade it it */
  var requirespercentage = 50;
  var buffer = element.outerHeight() * requirespercentage / 100;
  if( bottom_of_window > element_top + buffer) 
  {
  	var delay = 0;	// 0 is good for multitasking, alternative; element.data('delay')
    setTimeout
    (
      function()
      {
        element.removeClass('nxs-fx-untriggered'); // .animate({'opacity':'1'}, element.data('speed'))
      }, 
      delay
    );            
  }
}

function nxs_js_appearboxwhenvisible(dom)
{
	var element_offset = dom.offset();
  var element_top = element_offset.top;
  
  bottom_of_window = $(window).scrollTop() + nxs_js_getwindowheight();
  
  nxs_js_appearbox(dom, element_top, bottom_of_window);
}

// FX END

// kudos to http://stackoverflow.com/questions/16323770/stop-page-from-scrolling-if-hovering-div
function nxs_js_disabledocumentscrollwhenhoveringoverelement(e)
{
	jQ_nxs(e).on('DOMMouseScroll mousewheel', function(ev) {
    var $this = $(this),
        scrollTop = this.scrollTop,
        scrollHeight = this.scrollHeight,
        height = $this.height(),
        delta = (ev.type == 'DOMMouseScroll' ?
            ev.originalEvent.detail * -40 :
            ev.originalEvent.wheelDelta),
        up = delta > 0;

    var prevent = function() {
    	nxs_js_log("no propagation (7b)");
        ev.stopPropagation();
        ev.preventDefault();
        ev.returnValue = false;
        return false;
    }

    if (!up && -delta > scrollHeight - height - scrollTop) {
        // Scrolling down, but this will take us past the bottom.
        $this.scrollTop(scrollHeight);
        return prevent();
    } else if (up && delta > scrollTop) {
        // Scrolling up, but this will take us past the top.
        $this.scrollTop(0);
        return prevent();
    }
});
}

function nxs_js_menu_mini_expand(obj, placeholderid) 
{
	nxs_js_log("mini expand triggered");
	nxs_js_log(obj);
	nxs_js_log(placeholderid);
	
	//var expander = jQ_nxs(obj).siblings(".nxs-menu-mini-nav-expander-" + placeholderid);
	var expander = jQ_nxs(".nxs-menu-mini-nav-expander-" + placeholderid);
	nxs_js_log("expander:");
	nxs_js_log(expander);
	
	var extendMenuHeight = nxs_js_calc_menu_mini_height(expander, obj);

	jQ_nxs(expander).toggleClass("nxs-expand");

	if (extendMenuHeight) {
		jQ_nxs(expander).height(extendMenuHeight);
	} else {
		jQ_nxs(expander).height('');
	}
}

function nxs_js_change_menu_mini_expand_height(obj, placeholderid) 
{
	var expander = jQ_nxs(obj).siblings(".nxs-menu-mini-nav-expander-" + placeholderid);
	var extendMenuHeight = nxs_js_calc_menu_mini_height(expander, obj);

	console.log(expander);

	if (extendMenuHeight) {
		jQ_nxs(expander).height(extendMenuHeight);
	} else {
		jQ_nxs(expander).height('');
	}
}
 
function nxs_js_calc_menu_mini_height(expander, obj) {
	var extendMenuHeight = 0;

	// set height on expander if the container is fixed and the menu does not fit in the screen
	if (jQ_nxs('#nxs-fixed-header').length > 0) {
		if (jQ_nxs(expander).closest('#nxs-fixed-header').length > 0) {
			var top = jQ_nxs(obj).offset().top;
			var bottom = top + jQ_nxs(obj).outerHeight();
			var scrollTop = jQ_nxs(window).scrollTop();
			var extendMenuHeight = jQ_nxs(window).height() - bottom + scrollTop;
		}
	}

	return extendMenuHeight;
}

// kudos to http://stackoverflow.com/questions/3885817/how-to-check-if-a-number-is-float-or-integer
function nxs_js_isint(n)
{
   return typeof n === 'number' && parseFloat(n) == parseInt(n, 10) && !isNaN(n);
}

// kudos to http://stackoverflow.com/questions/123999/how-to-tell-if-a-dom-element-is-visible-in-the-current-viewport
function nxs_js_iselementinviewport(el) 
{
  var rect = el.getBoundingClientRect();
  
  nxs_js_log(rect);

  return 
  (
      rect.top >= 0 &&
      rect.left >= 0 &&
      rect.bottom <= (window.innerHeight || document. documentElement.clientHeight) && /*or jQ_nxs(window).height() */
      rect.right <= (window.innerWidth || document. documentElement.clientWidth) /*or jQ_nxs(window).width() */
  );
}

// kudos to http://stackoverflow.com/questions/123999/how-to-tell-if-a-dom-element-is-visible-in-the-current-viewport
function nxs_js_iselementaboveviewport(el) 
{
  var rect = el.getBoundingClientRect();  
	var result = rect.top <= 0;
  return result;
}

// kudos to http://stackoverflow.com/questions/123999/how-to-tell-if-a-dom-element-is-visible-in-the-current-viewport
function nxs_js_iselementabovepieceofviewport(el, piece) 
{
	var viewportheight = jQ_nxs(window).height();
	var halfviewportheight = viewportheight / piece;
  var rect = el.getBoundingClientRect();  
	var result = rect.top <= halfviewportheight;
  return result;
}

function nxs_js_ui_pushscrollrevert()
{
	nxs_js_scrollstateidentifier++;
	var value = { 'scrolltop' : jQ_nxs(window).scrollTop(), 'height' : jQ_nxs(window).height() };
	nxs_js_scrollstatestack[nxs_js_scrollstateidentifier] = value;
	return nxs_js_scrollstateidentifier;
}

function nxs_js_ui_popscrollrevert(scrollstateidentifier)
{
	var orig = nxs_js_scrollstatestack[nxs_js_scrollstateidentifier];
	jQ_nxs(window).scrollTop(orig.scrolltop);	
}

function nxs_js_stringisblank(str) 
{
	// kudos to http://stackoverflow.com/questions/154059/how-do-you-check-for-an-empty-string-in-javascript
  return (!str || /^\s*$/.test(str));
}

function nxs_js_stringcontains(haystack, needle)
{
	return haystack.indexOf(needle) > -1;
}

function nxs_js_processquerystring()
{
	var nxs_onpageload = nxs_js_getqueryparametervalue("nxs_onpageload")
	if (nxs_onpageload != '')
	{
		var nxs_onpageload_splitted = nxs_onpageload.split("_");
		var action = nxs_onpageload_splitted[0];
		if (action == 'editwidget')
		{
			if (nxs_js_nxseditoractive)
			{
				var postid = nxs_onpageload_splitted[1];
				var placeholderid = nxs_onpageload_splitted[2];
				var domelementinwidget = jQ_nxs('.nxs-post-' + postid + ' #nxs-widget-' + placeholderid);
				nxs_js_edit_widget(domelementinwidget);
			}
			else
			{
				// skip
			}
		}
		else
		{
			nxs_js_log('unsupported action;' + action);
		}
	}
	else
	{
		// no onpageload
		//nxs_js_log('no onpageload')
	}
}

function nxs_js_escaped_getqueryparametervalues()
{
	return nxs_js_getescapeddictionary(nxs_js_getqueryparametervalues());
}

// kudos to http://stackoverflow.com/questions/6539761/window-location-search-query-as-json
function nxs_js_getqueryparametervalues()
{
  var pairs = window.location.search.substring(1).split("&"),
    obj = {},
    pair,
    i;

  for ( i in pairs ) 
  {
  	if (!pairs.hasOwnProperty(i)) 
		{
			continue;
		}
  	
    if ( pairs[i] === "" ) continue;

    pair = pairs[i].split("=");
    obj[ decodeURIComponent( pair[0] ) ] = decodeURIComponent( pair[1] );
  }

	// nxs_js_log(obj);

  return obj;
}

// kudos to http://stackoverflow.com/questions/901115/how-can-i-get-query-string-values
function nxs_js_getqueryparametervalue(name)
{
  name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
  var regexS = "[\\?&]" + name + "=([^&#]*)";
  var regex = new RegExp(regexS);
  var results = regex.exec(window.location.search);
  if(results == null)
    return "";
  else
    return decodeURIComponent(results[1].replace(/\+/g, " "));
}

function nxs_js_hook_windowsresizeend_event()
{
	// nxs_js_log('hooked to nxs_event_resizeend');
	
	jQ_nxs(document).bind
	(
		'nxs_event_resizeend', 
		function() 
		{
			// nxs_js_log('receiving nxs_event_resizeend event');
			
			nxs_js_tagviewports();
	    nxs_gui_set_runtime_dimensions_enqueuerequest('nxs-framework-windowresized');
			nxs_js_reset_popup_dimensions();
		}
	);
}

// kudos to http://stackoverflow.com/questions/2996431/detect-when-a-window-is-resized-using-javascript and
// plus IE8 fix; IE8 fires windowsresized if any element is resized
// http://stackoverflow.com/questions/1852751/window-resize-event-firing-in-internet-explorer
function nxs_js_register_windowresizedend_event()
{
	//variables to confirm window height and width
  var lastWindowHeight = jQ_nxs(window).height();
  var lastWindowWidth = jQ_nxs(window).width();

  jQ_nxs(window).resize(function() 
  {
    //confirm window was actually resized
    if(jQ_nxs(window).height()!=lastWindowHeight || jQ_nxs(window).width()!=lastWindowWidth)
    {
      //set this windows size
      lastWindowHeight = jQ_nxs(window).height();
      lastWindowWidth = jQ_nxs(window).width();

      // nxs_js_log('jquery resize event detected');
		
		  if(this.resizeTO) clearTimeout(this.resizeTO);
		  this.resizeTO = setTimeout
		  (
		  	function() 
		  	{
		  		// nxs_js_log('broadcasting nxs_event_resizeend');
		  		jQ_nxs(document).trigger('nxs_event_resizeend');
		  	}
		  	, 
		  	250
		  );
    }
  });
}

// function that exposes outerhtml property
jQuery.fn.outerHTML = function(s) 
{
	return s
    ? this.before(s).remove()
    : jQ_nxs("<p>").append(this.eq(0).clone()).html();
};

// the 'startsWith' function is added to string objects
if (typeof String.prototype.startsWith != 'function') 
{
	String.prototype.startsWith = function (str)
	{
  	return this.indexOf(str) == 0;
	};
}

// kudos to http://stackoverflow.com/questions/5999998/how-can-i-check-if-a-javascript-variable-is-function-type
function nxs_js_isfunction(functionToCheck) {
 var getType = {};
 return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
}

function nxs_js_lazyexecute(scripturl, prependframeworkurl, functiontoinvoke)
{
	// ensure functiontoinvoke contains a "(" (parenthesis)
	// otherwise the invocation will fail without the developer
	// having a clue about what's wrong
	if (nxs_js_isfunction(functiontoinvoke))
	{
	}
	else if (!nxs_js_stringcontains(functiontoinvoke, "(") || !nxs_js_stringcontains(functiontoinvoke, ")"))
	{
		nxs_js_alert("parenthesis not found, use foo(), not foo");
	}
	
	//nxs_js_log("lazy executing:");
	
	if (prependframeworkurl)
	{
		scripturl = nxs_js_getframeworkurl() + scripturl;
	}
	
	//nxs_js_log(scripturl);
	//nxs_js_log(functiontoinvoke);
	
	nxs_js_requirescript
	(
		scripturl,	// id == scripturl 
		"js", 	// must be js
		scripturl, // url
		function() 
		{
			if (nxs_js_isfunction(functiontoinvoke))
			{
				functiontoinvoke();
			}
			else
			{
				eval(functiontoinvoke); 
			}
		}
	);
}

// ensure the specified css or js script is injected
function nxs_js_requirescript(scriptid, scripttype, scripturl, scriptcallbackafterload)
{
	if (scriptcallbackafterload == false)
	{
		// do nothing
	}

	if (scriptcallbackafterload == null)
	{
		nxs_js_alert("warning; loading scriptid; " + scriptid + "; no scriptcallbackafterload set?");
	}

	if (!nxs_js_isscriptlazyloaded(scriptid) && !nxs_js_isscriptlazyloading(scriptid))
	{
		var waitgrowltoken = -1; // nxs_js_alert_wait_start(nxs_js_gettrans("Loading script"));
	
		// nxs_js_log('processing script:' + scriptid);
	
		// markeer dat we deze nu aan het inladen zijn...
		nxs_js_lazyloadingscripts[scriptid] = true;
		var w = window;
		
		var d = w.document;
		var script;
		
		if (scripttype == "js")
		{
			script = d.createElement('script');
			script.setAttribute("type","text/javascript")
  		script.setAttribute('src', scripturl);
		}
		else if (scripttype == "css")
		{
			script = d.createElement('link');
		 	script.setAttribute("rel", "stylesheet")
  		script.setAttribute("type", "text/css")
  		script.setAttribute("href", scripturl);
		}
		
		// set callback for errors, note this is not compatible with
		// all browsers; see http://www.quirksmode.org/dom/events/error.html
		script.onerror = function() 
		{
			nxs_js_alert_wait_finish(waitgrowltoken);
			nxs_js_alert('Error loading resource (script/css); '+ scripturl); 
		}
		
		// set callback function to execute when script is succesfully loaded
		// regular browsers
		script.onload = function()
		{
			nxs_js_alert_wait_finish(waitgrowltoken);
			nxs_js_lazyloadedscripts[scriptid] = true;
			nxs_js_lazyloadingscripts[scriptid] = false;
			if (scriptcallbackafterload != null)
			{
				if (scriptcallbackafterload != false)
				{
					// nxs_js_log('executing... non-ie (fresh)');
					scriptcallbackafterload();
				}
			}
			else
			{
				// nxs_js_log('executing... nothing to execute');
			}
		}
		// for ie browsers
		script.onreadystatechange = function() 
		{
			// nxs_js_log('finished loading (IE)');
		
			if (this.readyState == 'complete' || this.readyState == 'loaded') 
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				nxs_js_lazyloadedscripts[scriptid] = true;
				nxs_js_lazyloadingscripts[scriptid] = false;
				if (scriptcallbackafterload != null)
				{
					if (scriptcallbackafterload != false)
					{
						scriptcallbackafterload();
					}
				}
			}	
		}
		
		//document.head.appendChild(script);
		document.getElementsByTagName("head")[0].appendChild(script);
		//d.documentElement.firstChild.appendChild(script);
		//nxs_js_log('script done');
	}
	else
	{
		// nxs_js_log('skipping (either already queued or already loaded)');
		if (scriptcallbackafterload != null)
		{
			if (scriptcallbackafterload != false)
			{
				scriptcallbackafterload();
			}
		}
	}
}

// checks if the specified script has finished loading
function nxs_js_isscriptlazyloaded(scriptid)
{
	for (var currentscriptid in nxs_js_lazyloadedscripts)
	{
		if (currentscriptid == scriptid)
		{
			return true;
		}
	}	
	return false;
}

// check if the specifiekd script is currently loading
function nxs_js_isscriptlazyloading(scriptid)
{
	for (var currentscriptid in nxs_js_lazyloadingscripts)
	{
		if (currentscriptid == scriptid)
		{
			return true;
		}
	}	
	return false;
}

function nxs_js_log(s)
{
	if ('console' in self && 'log' in console) 
	{
		console.log(s);
		var stacktrace = nxs_js_getqueryparametervalue("stacktrace");
		if (nxs_js_isctrlkeydown || stacktrace != "")
		{
			// practical debug tool; if ctrl is pressed, output the stacktrac
			try { throw new Error("Stracktrace"); } catch (e) { console.log(e.stack); }
		}
	}
}

function nxs_js_getstacktrace()
{
	var result;
	try { throw new Error("Stracktrace"); } catch (e) { result = e.stack; }
	return result;
}

function nxs_js_isemptyorwhitespace(value)
{
	return /^\s*$/.test(value);
}

function nxs_js_doeskeycodemakedirty(e)
{
	var result = true;
	
	if (e.keyCode == 9)
	{
		// tab will not make dirty
		result = false;
	}
	else if (e.keyCode >= 16 && e.keyCode <= 18)
	{
		// shift will not make dirty
		// control will not make dirty
		// alt will not make dirty
		result = false;
	}
	else if (e.keyCode == 20)
	{
		// capslock
		result = false;
	}
	else if (e.keyCode == 27)
	{
		//nxs_js_log('detected esc');
	
		// this closes the popup; do not mark it as dirty in this case
		result = false;
	}
	else if (e.keyCode >= 33 && e.keyCode <= 45)
	{
		// page up will not make dirty
		// page down, end, home, arrow left, up, right, down, printscreen, insert will not make dirty
		result = false;
	}
	else if (e.keyCode >= 112 && e.keyCode <= 123)
	{
		// f1 t/m f12 will not make dirty
		result = false;
	}
	
	return result;
}


// -------------------------------------------------------------------------------------------
	
function nxs_js_getcurrentbrowserurl()
{
	return window.location.href;
}
	
// return the url after setting/updating the parameter (other occurences of the same parameter are removed)
function nxs_js_addqueryparametertourl(url, parameter, value)
{
	var result = url;
	if (nxs_js_stringcontains(url, "?"))
	{
		result = result + "&";
	}
	else
	{
		result = result + "?";
	}
	result = result + parameter + "=" + value;
	return result;
}

// refreshes the current page
function nxs_js_refreshcurrentpage()
{
	location.reload(true);
}

function nxs_js_redirecttopostid(postid)
{
	nxs_js_geturl("postid", postid, "notused", 
	function(response) 
	{
		var url = response.url;
		nxs_js_log(url);
		nxs_js_redirect(url);
	},
	function(response) 
	{
		nxs_js_alert(nxs_js_gettrans('Unable to retrieve the URL'));
	}
	);
}

function nxs_js_redirect(url)
{
	window.location = url;
}

function nxs_js_redirect_top(url)
{
	top.location = url;
}

		// execute the function, in such a way that only one invocation will occur during the period specified for the specified groupname
		function nxs_js_invokethrottled(throttlegroupname, nxs_max_frequency_in_msecs, functiontoinvokethrottled)
		{
			if (!(throttlegroupname in nxs_js_queuestatelookup))
			{
				// nxs_js_log("first time throttle for group " + throttlegroupname);
				// doesn't yet exist
				nxs_js_queuestatelookup[throttlegroupname] = false;
			}
			var isqueued = nxs_js_queuestatelookup[throttlegroupname];
			if (isqueued)
			{
				// nxs_js_log("speeding up performance for group " + throttlegroupname);
				// its queued so will be processed soon...
				return;
			}
			
			// nxs_js_log("enqueued function to invoke for group " + throttlegroupname);
						
			// enqueue it!
			nxs_js_queuestatelookup[throttlegroupname] = true;
			
			// optionally perform an immediate redraw to get a snappier user experience
			setTimeout
			(
				function() 
				{
					// nxs_js_log("actual invoke request for group " + throttlegroupname);
				
					// first we dequeue! 
					nxs_js_queuestatelookup[throttlegroupname] = false;
					// invoke function!
					functiontoinvokethrottled();
				},
				nxs_max_frequency_in_msecs
			);
		}		
	
		function nxs_js_reset_popup_dimensions()
		{
			//jQ_nxs('#nxsbox_ajaxContent').css('opacity', '0');

			nxs_js_invokethrottled("reset_popup_dimensions_actualrequest", 50, nxs_js_reset_popup_dimensions_actualrequest);
			nxs_js_invokethrottled("showpopup", 195, nxs_js_showpopup);
		}
	
		function nxs_js_showpopup()
		{
			// display!
			jQ_nxs('#nxsbox_window .nxs-table').css("opacity", 1);
		}
	
		//
		// reset de dimensies van de nxsbox op basis van de hoogte van het scherm
		//
		function nxs_js_reset_popup_dimensions_actualrequest()
		{
			// turn off footer fillter initially before making any calculations
			jQ_nxs(".nxs-canvas-footerfiller").css("height", 0);
			
			if (nxs_js_popup_getsessiondata("popup_current_dimensions") == "gallerybox")
			{
			}
			else
			{
				if(typeof nxs_js_popup_get_minwidth == 'function')
				{
					//
					var minwidth = nxs_js_popup_get_minwidth();	// can be overriden
					jQ_nxs(".nxs-popup-content-canvas-cropper").css('min-width', '' + minwidth + 'px');
				}
				else
				{
					// default
				}
				
				
				if(typeof nxs_js_popup_get_maxwidth == 'function')
				{
					//
					var maxwidth = nxs_js_popup_get_maxwidth();	// can be overriden
					jQ_nxs(".nxs-popup-content-canvas-cropper").css('max-width', '' + maxwidth + 'px');
				}
				else
				{
					// default
				}
				
				
				if(typeof nxs_js_popup_get_maxheight == 'function')
				{
					var maxheight = nxs_js_popup_get_maxheight();	// can be overriden
					//nxs_js_log("max height is " + maxheight);
					jQ_nxs(".nxs-popup-content-canvas-cropper").css('height', '' + maxheight + 'px');
				}
				else
				{
					// default
				}
			}
			
			var applynewpopuplayoutposition = true;
		
			// maximize the nxsbox_window to consume 100% width and 100% height
			jQ_nxs('#nxsbox_window').css('width', '100%');
			jQ_nxs('#nxsbox_window').css('height', jQ_nxs(window).height() + "px");	// height of the window
			jQ_nxs('#nxsbox_window').css('margin-top', '0px');
			jQ_nxs('#nxsbox_window').css('margin-left', '0px');
			jQ_nxs('#nxsbox_window').css('top', '0px');
			jQ_nxs('#nxsbox_window').css('left', '0px');
			
			// 
			//jQ_nxs('#nxsbox_ajaxContent').css('width', 'auto');

			jQ_nxs("#nxsbox_ajaxContent").width(jQ_nxs(window).width());
			var h = jQ_nxs(window).height();
			var hcontent = jQ_nxs(".nxs-popup-dyncontentcontainer").height();
			var htop = (h - hcontent) / 2;
			if (htop < 0) { htop = 0; }
			
			var w = jQ_nxs(window).width();
			var wcontent = jQ_nxs(".nxs-popup-content-canvas-cropper").width();
			var wleft = (w - wcontent) / 2;
			if (wleft < 0) { wleft = 0; }
			
			jQ_nxs('#nxsbox_ajaxContent').css('position', 'absolute');
			jQ_nxs('#nxsbox_ajaxContent').css('margin-top', htop + 'px');
			jQ_nxs('#nxsbox_ajaxContent').css('overflow', 'hidden');
			
			//
			jQ_nxs('#nxsbox_ajaxContent').each
			(
				function(index, element)
				{
					// horizontal positioning
					var parentWidth = jQ_nxs(element).parent().outerWidth();	// width of browser screen
					//nxs_js_log('parentwidth:' + parentWidth);
					
					var contentWidth = jQ_nxs(element).children().outerWidth();
					//nxs_js_log('contentWidth:' + contentWidth);
					
					if (contentWidth == 1)
					{
						// the popup opens initially with a size of 1 pixel wide (see #1283672893762),
						// we will enqueue the reset of the popup to ensure the position will be ok,
						// after the content is loaded (the content isn't positioned ok when we reach this
						// point)
						nxs_js_reset_popup_dimensions();
					}
					
					//nxs_js_log("parentWidth:");
					//nxs_js_log(parentWidth);
					//nxs_js_log("contentWidth:");
					//nxs_js_log(contentWidth);
					
					var minwidth = 2048;
					
					if(typeof nxs_js_popup_get_minwidth == 'function')
					{
						minwidth = nxs_js_popup_get_minwidth();	// can be overriden
					}
					
					if (contentWidth < minwidth)
					{
						contentWidth = minwidth;
					}

					if (parentWidth >= contentWidth)
					{
						var leftvalue = Math.round((parentWidth - contentWidth)/2);
						//nxs_js_log('setting:' + leftvalue);
						jQ_nxs(element).css
						(
							{
								width: 'auto',
								left: leftvalue
						  }
						);
					}
					else
					{
						jQ_nxs(element).css
						(
							{
								width: parentWidth,
								left: '0px'
						  }
						);
					}

					// ---------------
					
					// vertical positioning
					var parentHeight = jQ_nxs(element).parent().outerHeight();										
					var contentHeight = jQ_nxs(element).children().outerHeight();
				
					//nxs_js_log("parentHeight:");
					//nxs_js_log(parentHeight);
					//nxs_js_log("contentHeight:");
					//nxs_js_log(contentHeight);
					
					if (parentHeight >= contentHeight)
					{
						jQ_nxs(element).css
						(
							{
								height: 'auto',
								top: (parentHeight - contentHeight)/2
						  }
						);
					}
					else
					{
						nxs_js_log("setting to:" + parentHeight);
						
						jQ_nxs(element).css
						(
							{
								height: parentHeight,
								top: '0px'
						  }
						);
					}
				}
			);				

			if (!jQ_nxs("#nxsbox_window").hasClass("nxs-active"))
			{
				jQ_nxs("#nxsbox_window").addClass("nxs-active");
			}
			else
			{
				// was already set, ignored
			}
			
			//jQ_nxs('#nxsbox_ajaxContent').css('opacity', '1');
			
			// add additional spacing at the bottom of the popup
			var footerfillerheight = 0;
			if(typeof nxs_js_popup_get_maxheight == 'function')
			{
				var footerfillerheight = nxs_js_popup_get_maxheight();
				footerfillerheight = footerfillerheight - 60;
				if (footerfillerheight < 0)
				{
					footerfillerheight = 0;
				}
			}
			jQ_nxs(".nxs-canvas-footerfiller").css("height", footerfillerheight);
			
			//
			jQ_nxs('#nxsbox_ajaxContent').css('left', '0px');
			jQ_nxs('#nxsbox_ajaxContent').css('top', '0px');
			
			var w = jQ_nxs(window).width();
			var wcontent = jQ_nxs(".nxs-popup-content-canvas-cropper").width();
			var wleft = (w - wcontent) / 2;
			if (wleft < 0) { wleft = 0; }
			
			jQ_nxs('#nxsbox_ajaxContent').css('position', 'absolute');
			
			if (jQ_nxs('#nxsbox_window').hasClass("nxs-gallerypopup"))
			{
				nxs_js_log("window has class");
				jQ_nxs('#nxsbox_ajaxContent').css('margin-left', '0px');
			}
			else
			{
				//nxs_js_log("window does not have class");
				jQ_nxs('#nxsbox_ajaxContent').css('margin-left', wleft + 'px');
			}
		}
		
		function nxs_js_toggle_editor_state()
		{
			// toggle!
	  	nxs_js_nxseditoractive = !nxs_js_nxseditoractive;
	  	
	  	var cookieval = "";
	  	if (nxs_js_nxseditoractive)
	  	{
	  		cookieval = "active";
	  	}
	  	else
	  	{
	  		cookieval = "inactive";
	  	}
	  	nxs_js_setcookie('nxs_cookie_editoractive', cookieval);
		  nxs_js_process_updated_editor_state();
		}
		
		function nxs_js_initiateeditorstate()
		{
			if (!nxs_js_userhasadminpermissions())
			{
				nxs_js_nxseditoractive = false;
				return;
			}
		
			var cookieval = nxs_js_getcookie('nxs_cookie_editoractive');
			if (cookieval == "active")
			{
				nxs_js_nxseditoractive = true;
			}
			else if (cookieval == "inactive")
			{
				nxs_js_nxseditoractive = false;
			}
			else
			{
				// first time, or else
				nxs_js_nxseditoractive = true;
			}
			
			nxs_js_process_updated_editor_state_silent();
		}
		
		function nxs_js_process_updated_editor_state()
		{
			var growl = true;
			if (jQ_nxs("body").hasClass("nxs-plugin-catalogitem"))
			{
				growl = false;
			}
			nxs_js_process_updated_editor_state_internal(growl);
		}
		
		function nxs_js_process_updated_editor_state_silent()
		{
			var growl = false;
			nxs_js_process_updated_editor_state_internal(growl);
		}
		
		// parameter growl is used to indicate whether
		function nxs_js_process_updated_editor_state_internal(growl)
		{
	    if (!nxs_js_userhasadminpermissions())
	    {
	    	// the popup mechanism is used for galleries too, and other parts,
	    	// if these popups are closed, the method is invoked too,
	    	// in that case we ignore the requests
	    	return;
	    }

			if (!nxs_js_nxseditoractive) 
	  	{	  		
		    jQ_nxs(".nxs-cursor").addClass("nxs-suppress");
		    jQ_nxs(".nxs-hover-menu").addClass("nxs-suppress");
		    
		    if (growl)
		    {
		    	nxs_js_editorstategrowltoken = nxs_js_alert_wait_start(nxs_js_gettrans('Editor is now disabled'));
		    }

		    jQ_nxs(".nxs-editor-toggler").find(".nxs-icon-pause").hide();
		    jQ_nxs(".nxs-editor-toggler").find(".nxs-icon-play").show();

		    jQ_nxs(".nxs-editor-toggler").attr('title', nxs_js_gettrans('Click to reactivate editor '));
		    
		    // als de editor uit staat, moet de gebruiker weer "normaal" tekst kunnen selecteren
				jQ_nxs('body').enableSelection();
				
				// hide particular elements
				jQ_nxs(".nxs-hidewheneditorinactive").hide();
				jQ_nxs(".nxs-border-dash").hide();
			}
	  	else 
		  { 		  
		    jQ_nxs(".nxs-suppress").removeClass("nxs-suppress");
		    
		    if (growl)
		    {
	    		nxs_js_alert_wait_finish(nxs_js_editorstategrowltoken);
	    		nxs_js_alert_veryshort(nxs_js_gettrans('Editor is now active again'));
	    	}
		    
		    jQ_nxs(".nxs-editor-toggler").find(".nxs-icon-pause").show();
		    jQ_nxs(".nxs-editor-toggler").find(".nxs-icon-play").hide();
		    
		    jQ_nxs(".nxs-editor-toggler").attr('title', nxs_js_gettrans('Click to deactivate editor'));
		    		    
		    // als de editor aan staat, kan de gebruiker geen tekst kunnen selecteren (dit voorkomt irritante tekst selecties bij drag/droppen van widgets)
				jQ_nxs('body').disableSelection();		    
				
				// show particular elements
				jQ_nxs(".nxs-hidewheneditorinactive").show();
				jQ_nxs(".nxs-border-dash").show();
	  	}
	  	
	  	// add a class to the html element
	  	if (nxs_js_nxseditoractive)
	  	{
	  		jQ_nxs("html").removeClass("nxs-editor-inactive");
	  		jQ_nxs("html").addClass("nxs-editor-active");
	  	}
	  	else
	  	{
	  		jQ_nxs("html").removeClass("nxs-editor-active");
	  		jQ_nxs("html").addClass("nxs-editor-inactive");
	  	}
	  	
	  	nxs_js_refreshtopmenufillerheight();
	  	
	  	nxs_gui_set_runtime_dimensions_enqueuerequest("nxs-framework-editorstatechanged");	// update gui (for example, heights of "empty" rows)
		}
		
		function nxs_js_toggle_menu_state()
		{
			// toggle!
	  	nxs_js_nxsmenuactive = !nxs_js_nxsmenuactive;
	  	
	  	var cookieval = "";
	  	if (nxs_js_nxsmenuactive)
	  	{
	  		cookieval = "active";
	  	}
	  	else
	  	{
	  		cookieval = "inactive";
	  	}
	  	nxs_js_setcookie('nxs_cookie_menuactive', cookieval);
		  nxs_js_process_updated_menu_state();
		  
		  nxs_js_refreshtopmenufillerheight();
		}
		
		function nxs_js_initiatemenustate()
		{
			var cookieval = nxs_js_getcookie('nxs_cookie_menuactive');
			if (cookieval == "active")
			{
				nxs_js_nxsmenuactive = true;
			}
			else if (cookieval == "inactive")
			{
				nxs_js_nxsmenuactive = false;
			}
			else
			{
				// first time, or else
				nxs_js_nxsmenuactive = true;
			}
			nxs_js_process_updated_menu_state_silent();
		}
		
		function nxs_js_process_updated_menu_state()
		{
			var growl = true;
			nxs_js_process_updated_menu_state_internal(growl);
		}
		
		function nxs_js_process_updated_menu_state_silent()
		{
			var growl = false;
			nxs_js_process_updated_menu_state_internal(growl);
		}
		
		// parameter growl is used to indicate whether
		function nxs_js_process_updated_menu_state_internal(growl)
		{
	    if (!nxs_js_userhasadminpermissions())
	    {
	    	// the popup mechanism is used for galleries too, and other parts,
	    	// if these popups are closed, the method is invoked too,
	    	// in that case we ignore the requests
	    	return;
	    }

			if (!nxs_js_nxsmenuactive) 
	  	{			  	
		    if (growl)
		    {
		    	nxs_js_menustategrowltoken = nxs_js_alert_wait_start(nxs_js_gettrans('menu is now disabled'));
		    }

		    jQ_nxs(".nxs-menu-toggler").find(".nxs-icon-arrow-up").hide();
		    jQ_nxs(".nxs-menu-toggler").find(".nxs-icon-arrow-down").show();

		    jQ_nxs(".nxs-menu-toggler").attr('title', nxs_js_gettrans('Click to reactivate menu'));
		    
				// hide particular elements
				jQ_nxs(".nxs-hidewhenmenuinactive").hide();
			}
	  	else 
		  { 		  
		    if (growl)
		    {
	    		nxs_js_alert_wait_finish(nxs_js_menustategrowltoken);
	    		nxs_js_alert_veryshort(nxs_js_gettrans('menu is now active again'));
	    	}
		    
		    jQ_nxs(".nxs-menu-toggler").find(".nxs-icon-arrow-up").show();
		    jQ_nxs(".nxs-menu-toggler").find(".nxs-icon-arrow-down").hide();
		    
		    jQ_nxs(".nxs-menu-toggler").attr('title', nxs_js_gettrans('Click to deactivate menu'));
				
				// show particular elements
				jQ_nxs(".nxs-hidewhenmenuinactive").show();
	  	}
	  	
	  	// add a class to the html element
	  	if (nxs_js_nxsmenuactive)
	  	{
	  		jQ_nxs("html").removeClass("nxs-menu-inactive");
	  		jQ_nxs("html").addClass("nxs-menu-active");
	  	}
	  	else
	  	{
	  		jQ_nxs("html").removeClass("nxs-menu-active");
	  		jQ_nxs("html").addClass("nxs-menu-inactive");
	  	}
	  	
	  	nxs_js_refreshtopmenufillerheight();
	  	
	  	// show the menu (its hidden when the page is loaded using inline style for
	  	// optimal UX)
	  	jQ_nxs("#nxs-menu-outerwrap").show();
		}
		
		function nxs_js_togglesidebar()
		{
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "togglesidebar",
						"postid": nxs_js_getcontainerpostid()
					},
					cache: false,
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// refresh screen to see the end result :)
							nxs_js_refreshcurrentpage();
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
		
		function nxs_js_handleplaceholderevent(eventname, e, placeholderdom)
		{
			if (!nxs_js_nxseditoractive)
			{
				nxs_js_log("ignoring event; editor is OFF");
				return false;
			}
			
			var isButton = e.target.nodeName == "BUTTON";
			var isSelect = e.target.nodeName == "SELECT";
			var isInput = e.target.nodeName == "INPUT";
			var isTextArea = e.target.nodeName == "TEXTAREA";
			var isInAnchor = e.target.nodeName == "A" || (jQuery(e.target).closest('a').length > 0);
			var isHandledOutsideFramework = isButton || isSelect || isInput || isTextArea || isInAnchor;
			
			//nxs_js_log("isHandledOutsideFramework; " + isHandledOutsideFramework);
			//nxs_js_log("isSelect? " + isSelect);
			//nxs_js_log("isButton? " + isButton);
			//nxs_js_log("isInput? " + isInput);
			//nxs_js_log("isTextArea? " + isTextArea);
			//nxs_js_log("isInAnchor? " + isInAnchor);
			
			if (isHandledOutsideFramework)
			{
				// if the event is a click, and the event is caused by an anchor that
				// has a "regular" href (no onclick), then follow that href, instead
				return false;
			}
			else
			{
				nxs_js_log("Event possibly handled by framework");
				// proceed
			}
			
			nxs_js_log("placeholderdom:");
			nxs_js_log(placeholderdom);
			
			// handle incoming event for the placeholder,
			// note; its possible (as it appears) the event is triggered by objects 
			// outside the framework (such as WooCommerce). We therefore first ensure 
			// we need to act upon this event (and absorb the event), or allow the 
			// event to propagate further
			
			var istriggeredbywidget = jQ_nxs(e.target).closest(".nxs-widget").length > 0;
			var istriggeredbycursor = jQ_nxs(e.target).hasClass("nxs-runtime-autocellsize");
			var istriggeredbynexusframework = (istriggeredbywidget || istriggeredbycursor);
			
			
			if (istriggeredbynexusframework)
			{
				nxs_js_log("no propagation (1)");
				// no further propagation of the click event...
				e.stopPropagation();
				nxs_js_popup_placeholder_handleclick(placeholderdom); 
			}
			else
			{
				// we won't act upon this event as we don't know who are what
				// triggered it. The event wil be logged to help debug problems,
				// and it will propagate further to be handled by someone else's code
				nxs_js_log("Widget detect an event that appears to be triggered outside the nxs framework. It propagates further:");
				nxs_js_log(e);
			}
		}
		
		function nxs_js_reregister_click_and_hover_events()
		{
			if (nxs_js_userhasadminpermissions())
			{
				jQ_nxs("input").unbind("click.reregister");
				jQ_nxs("input").bind("click.reregister", function(e) 
				{
					if (nxs_js_nxseditoractive)
					{
						// if the editor is on, we don't want events in input elements
						// to traverse
						nxs_js_log("no propagation (4)");
				  	e.stopPropagation();
				  }
				  else
			  	{
			  		// if the editor is off we allow these events to be handled
			  	}
				})
				
				// allow user to click on any widget within an editable section
				jQ_nxs(".nxs-widgets-editable .nxs-placeholder-list > .nxs-placeholder").each
				(
					function(index, placeholderelement)
					{
						// remove any previously defined click events
						jQ_nxs(placeholderelement).unbind("click.reregister");
						jQ_nxs(placeholderelement).bind
						(
							"click.reregister", 
							function(e)
							{
								var result = nxs_js_handleplaceholderevent('click', e, this);
								return true;
							}
						);
						
						// remove any previously defined dbl click events
						jQ_nxs(placeholderelement).unbind("dblclick.reregister");
						jQ_nxs(placeholderelement).bind
						(
							"dblclick.reregister", 
							function(e)
							{
								nxs_js_handleplaceholderevent('dblclick', e, this);
							}
						);
					}
				);
				
				//
				// als een gebruiker clickt op het 'verplaats' icoon
				// bij een placeholder moet het click event worden geabsorbeerd
				//
				jQ_nxs(".cursor").unbind("click.startdrag");
				jQ_nxs(".cursor").bind("click.startdrag", function(e) 
				{
					// stop het progageren van het event (bind("click") om te voorkomen dat onderliggende
					// elementen het click event gaan afhandelen (zoals het event dat de body click altijd opvangt...)
					nxs_js_log("no propagation (6)");
					e.stopPropagation();
					
					nxs_js_alert(nxs_js_gettrans('Tip to move widget'));
				});
				
				//
				Mousetrap.bind('alt+c', function(e, combo) 
	  		{
				  console.log(combo);
				  nxs_copytoserverclipboard("designproperties");
				});
				Mousetrap.bind('alt+v', function(e, combo) 
	  		{
				  console.log(combo);
				  nxs_pastefromserverclipboard("designproperties");
				});
				
				jQ_nxs(document).unbind("keydown.ctrlprocessing");
				jQ_nxs(document).bind("keydown.ctrlprocessing", function(evt)
				{
	    		if (!nxs_js_isctrlkeydown)
	    		{
		        if (evt.keyCode == 17)	// 17 = ctrl
		        { 
		        	nxs_js_isctrlkeydown = true;
		        	//nxs_js_log('ctrl pressed');
		        }
		        /*
		        else 
		        { 
		        	nxs_js_log('key pressed:' + evt.keyCode);
		        }
		        */
		      }
		    });
		     
				// control (ctrl) toets zet tijdelijk de cursors uit
				jQ_nxs(document).unbind("keyup.compound");
				jQ_nxs(document).bind("keyup.compound", function(evt)
				{
					if (evt.which == 27)	// handled
				  {				  
				  	if (nxs_js_popupshows)
				  	{
				  		// ignore the request when popup shows (popup itself is responsible for handling escape)
				  	}
				  	else if (nxs_js_nxsisdragging)
				  	{
				  		// ignore the request when user is drag'n'dropping
				  	}
				  	else
				  	{
					  	//nxs_js_log('debug; escape pressed (GJGJ), popup does not show');
				  	
				  		nxs_js_toggle_editor_state();
				  		return false;
				  	}
					}
				
					if (nxs_js_nxseditoractive)
		    	{
		        if (nxs_js_isctrlkeydown)
		        {
		        	// if keyboard focus is set to an element 
		        	// in the top menu, ignore the event to allow "normal"
		        	// copy and pasting of the clipboard
		        	var focusElement = jQ_nxs(document.activeElement);
		        	/*
		        	var isFocusSetOnTopMenu = jQ_nxs(focusElement).closest("#nxs-menu-outerwrap");
		        	if (isFocusSetOnTopMenu)
		        	{
		        		nxs_js_log("ignoring ctrl-keyboard; focus in top menu");
		        		return;
		        	}
		        	*/
		        	if (jQ_nxs(focusElement).is('input:text'))
		        	{
		        		nxs_js_log("ignoring ctrl-keyboard; focus in input textbox");
		        		return;
		        	}
		        	if (jQ_nxs(focusElement).is('textarea'))
		        	{
		        		nxs_js_log("ignoring ctrl-keyboard; focus in textarea");
		        		return;
		        	}
		        	
		        	// detect CTRL-related keyboard combinations like copy-paste
		        	
		        	//nxs_js_log("keycode:" + evt.keyCode);
		        
		        	// ctrl is pressed
		        	
		        	if (evt.keyCode == 67)
		        	{
		        		// CTRL-C / CMD-C
		        		nxs_js_isctrlkeydown = false;
		        		nxs_copytoserverclipboard("all");
		        		return false;
		        	}
		        	else if (evt.keyCode == 86)
		        	{
		        		// CTRL-V / CMD-V
		        		nxs_js_isctrlkeydown = false;
		        		nxs_pastefromserverclipboard("all");
		        		return false;
		        	}
		        	else if (evt.keyCode == 66)	// debug key: ctrl-b = re-enable js events (rebuild UI editor)
		        	{
		        		nxs_js_isctrlkeydown = false;
		        		nxs_js_alert('debug - invoking nxs_js_reenable_all_window_events() BEGIN');
		        		nxs_js_reenable_all_window_events();
		        		nxs_js_alert('debug - invoking nxs_js_reenable_all_window_events() DONE');
		        		return false;
		        	}
			        else if (evt.keyCode == 17) // 17 = ctrl
			        {
			        	nxs_js_isctrlkeydown = false;	// ctrl key released
			        	//nxs_js_log('ctrl released');
			        }
		        	else 
		        	{
		        		//nxs_js_log(evt.keyCode);
		        		//return false;
		        	}
						}
						else
						{
						  if (evt.keyCode == 46)	// delete key
							{
								if (!nxs_js_popupshows)
								{
									var widget = nxs_js_getwidgetdom_overwhichwehover();
									if (widget != null)
									{
										nxs_js_frontendediting_widgethandledelete(widget);
									}
									else
									{
										// not hovering over widget
									}
								}
							}
						}
					}
				});
				
				// if a user hovers over a placeholder that is editable,
				// we add a nxs-hovering class to its cell-cursor and hover-menu,
				// we remove the classes when the user moves out
				// OK
				jQ_nxs(".nxs-widgets-editable .nxs-placeholder").unbind("mouseover.glowwidget");
				jQ_nxs(".nxs-widgets-editable .nxs-placeholder").bind("mouseover.glowwidget", function(e)
				{
					if (!nxs_js_nxsisdragging)
					{
						// add nxs-hovering
						jQ_nxs(this).find(".nxs-cell-cursor").addClass("nxs-hovering");
						jQ_nxs(this).find(".nxs-hover-menu").addClass("nxs-hovering");
						jQ_nxs(this).addClass("nxs-hovering");
						
						// update height of child items (cursors) that should have the same size
						var height = jQ_nxs(this).height();
						jQ_nxs(this).find(".nxs-runtime-autocellsize").height(height);
					}
				});
				// OK
				jQ_nxs(".nxs-widgets-editable .nxs-placeholder").unbind("mouseleave.glowwidget");
				jQ_nxs(".nxs-widgets-editable .nxs-placeholder").bind("mouseleave.glowwidget", function(e)
				{
					//
					jQ_nxs(this).find(".nxs-cell-cursor").removeClass("nxs-hovering");
					jQ_nxs(this).find(".nxs-hover-menu").removeClass("nxs-hovering");
					jQ_nxs(this).removeClass("nxs-hovering");
				}
				);
				
				// if the user hovers over a hovermenu of a pagerow,
				// highlight all widgets within that row, and dim them when the mouse leaves the menu
				// OK
				jQ_nxs(".nxs-layout-editable .nxs-row .nxs-row-container > .nxs-hover-menu").unbind("mouseover.glowrow");
				jQ_nxs(".nxs-layout-editable .nxs-row .nxs-row-container > .nxs-hover-menu").bind("mouseover.glowrow", function(e)
				{
					if (!nxs_js_nxsisdragging)
					{
						jQ_nxs(this).closest(".nxs-row").find(".nxs-cell-cursor").addClass("nxs-hovering");
						jQ_nxs(this).closest(".nxs-row").find(".nxs-placeholder").addClass("nxs-hovering");
					}
				});
				// OK
				jQ_nxs(".nxs-layout-editable .nxs-row .nxs-row-container > .nxs-hover-menu").unbind("mouseleave.glowrow");
				jQ_nxs(".nxs-layout-editable .nxs-row .nxs-row-container > .nxs-hover-menu").bind("mouseleave.glowrow", function(e)
				{
					if (!nxs_js_nxsisdragging)
					{
						jQ_nxs(this).closest(".nxs-row").find(".nxs-cell-cursor").removeClass("nxs-hovering");
						jQ_nxs(this).closest(".nxs-row").find(".nxs-placeholder").removeClass("nxs-hovering");
					}
				}
				);
				
				// als de gebruiker boven een cursor hangt (bijv. sidebar)
				// moet de widget oplichten
				jQ_nxs(".nxs-cursor").unbind("mouseover.glowwidget");
				jQ_nxs(".nxs-cursor").bind("mouseover.glowwidget", function(e)
				{
					if (!nxs_js_nxsisdragging)
					{
						jQ_nxs(this).addClass("nxs-hovering");
						// geef ook nxs-hovering aan mogelijke hovermenu's die hier in zitten
						jQ_nxs(this).find(".nxs-hover-menu").addClass("nxs-hovering");
					}
				});
				jQ_nxs(".nxs-cursor").unbind("mouseleave.glowwidget");
				jQ_nxs(".nxs-cursor").bind("mouseleave.glowwidget", function(e)
				{
					//nxs_js_log('mouse leave detected nxs-cursor');
					jQ_nxs(this).removeClass("nxs-hovering");
					// verwijder ook nxs-hovering aan mogelijke hovermenu's die hier in zitten
					jQ_nxs(this).find(".nxs-hover-menu").removeClass("nxs-hovering");
				}
				);
				
				// als de gebruiker boven een container hangt met een immediate hover menu...
				
				jQ_nxs(".nxs-containsimmediatehovermenu").unbind("mouseover.glowwidget");
				jQ_nxs(".nxs-containsimmediatehovermenu").bind("mouseover.glowwidget", function(e)
				{
					if (!nxs_js_nxsisdragging)
					{
						jQ_nxs(this).children(".nxs-hover-menu").addClass("nxs-hovering");
					}
				});
				jQ_nxs(".nxs-containsimmediatehovermenu").unbind("mouseleave.glowwidget");
				jQ_nxs(".nxs-containsimmediatehovermenu").bind("mouseleave.glowwidget", function(e)
				{
					//nxs_js_log('mouse leave detected');
					jQ_nxs(this).children(".nxs-hover-menu").removeClass("nxs-hovering");
				}
				);
			}
			
			
			// allow extensions to extend this
			jQ_nxs(window).trigger('nxs_js_reregister_click_and_hover_events');
		}
		
		// returns the index of the current row element within the container of rows (nearest to 'element')
		function nxs_js_getrowindex(element)
		{
			var row = jQ_nxs(element).closest('.nxs-row');
			var container = jQ_nxs(element).closest('.nxs-postrows')[0];
			var rows = jQ_nxs(container).find('.nxs-row'); // alle nxs-rows binnen nxs-postrows
			var result = jQ_nxs(rows).index(row);	//  de index van de dichtsbijzijnde nxs-row binnen de lijst
			return result;
		}
		
		// returns the identifier of the row
		function nxs_js_getrowid(element)
		{
			var row = jQ_nxs(element).closest('.nxs-row');	// bijv. nxs-pagerow-prid1182682337
			var result = jQ_nxs(row).attr("id").split("-")[2];
			return result;
		}
		
		// returns the element of the dom element within the container for the specified postid and rowindex
		function nxs_js_getrowelement(postid, rowindex)
		{
			var element = nxs_js_getelementscontainer(postid);
			var result = jQ_nxs(element).children().children()[rowindex];
			return result;
		}
		
		function nxs_js_getplaceholderidsinrow(rowelement)
		{
			var result = [];
			jQ_nxs(rowelement).find(".nxs-widget").each
  		(
  			function(index, widgetelement) 
  			{
  				var widgetelementid = widgetelement.id;	// bijv. nxs-widget-a1362357679
  				var placeholderid = widgetelementid.split("-")[2];
  				result.push(placeholderid);
  			}
  		);
  		return result;
		}
		
		function nxs_js_row_render(postid, rowindex, invokewhenavailable)
		{
			if (rowindex == -1)
			{
				nxs_js_log("postid: " + postid);
				nxs_js_log("rowindex: " + rowindex);
				nxs_js_log("rowindex niet gevonden");
				alert("rowindex niet gevonden");
				return;
			}
			
			var postcontainerid = nxs_js_getcontainerpostid();
			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "getcontent",
						"clientpopupsessioncontext": nxs_js_getescaped_popupsession_context(),
						"contenttype": "webpart",
						"contentcontext": "row_" + postcontainerid + "_" + postid + "_" + rowindex,
						"webparttemplate": "render_htmlvisualization",
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
							invokewhenavailable(postid, rowindex, response);
						}
						else
						{
							nxs_js_log("server response is valid, but not 'ok'");
							nxs_js_log(response);
							nxs_js_popup_notifyservererror_v2(response);
						}
					},
					error: function(xhr, ajaxOptions, thrownError)
					{
						// an error could occur if the user redirects before this operation is completed
						nxs_js_popup_notifyservererror_v2(thrownError);
					}
				}
			);
		}
		
		// helper function that gets the dom element of the elementscontainer for the specified postid,
		// if a page contains a header, sidebar, maincontent and footer, 
		// you can easily get the footer dom, knowing its postid
		function nxs_js_getelementscontainer(postid)
		{
			var result = jQ_nxs(".nxs-elements-container.nxs-post-" + postid).first();
			return result;
		}
		
		// helper function that returns the dom of the widget in the elementscontainer
		function nxs_js_getwidget_withinelementscontainer(elementscontainer, placeholderid)
		{
			var result = jQ_nxs(elementscontainer).find("#nxs-widget-" + placeholderid)[0];
			return result;
		}
		
		// helper function that returns the dom of the widget (placeholderid) in the elementscontainer (postid)
		function nxs_js_getwidgetinpostid(postid, placeholderid)
		{
			var elementscontainer = nxs_js_getelementscontainer(postid);
			var result = nxs_js_getwidget_withinelementscontainer(elementscontainer, placeholderid);
			return result;
		}
		
		// convenience function, rerenders the GUI row for the specified postid and placeholderid,
		// by using this function its possible to re-render a row in the header
		// on the screen.
		function nxs_js_rerender_row_for_placeholder(postid, placeholderid)
		{
			var widget = nxs_js_getwidgetinpostid(postid, placeholderid);
			nxs_js_rerender_row_for_element(widget);
		}

		// convenience function
		function nxs_js_rerender_row_for_pagerow(postid, pagerowid)
		{
			var pagecontainer = jQ_nxs(".nxs-post-" + postid)[0];
			var element = jQ_nxs(pagecontainer).find("#nxs-pagerow-" + pagerowid)[0];
			nxs_js_rerender_row_for_element(element);
		}
		
		function nxs_js_getrowindex_forplaceholder(postid, placeholderid)
		{
			var element = nxs_js_getwidgetelement_forplaceholder(postid, placeholderid);
			var rowindex = nxs_js_getrowindex(element);
			return rowindex;
		}
		
		function nxs_js_getwidgetelement_forplaceholder(postid, placeholderid)
		{
			var pagecontainer = jQ_nxs(".nxs-post-" + postid)[0];
			var element = jQ_nxs(pagecontainer).find("#nxs-widget-" + placeholderid)[0];
			return element;
		}
		
		function nxs_js_rerender_row_for_element(element)
		{
			var postid = nxs_js_findclosestpostid_for_dom(element);
		
			var rowindex = nxs_js_getrowindex(element);
			//nxs_js_log("found:" + rowindex);
			nxs_js_row_render(postid, rowindex, function(postid, rowindex, response)
			{	
				var html = response.html;
				// we gaan er vanuit dat het opnieuw tekenen plaatsvindt op de article container en niet in de sidebar of footer ...							
				var pagecontainer = jQ_nxs(".nxs-post-" + postid)[0];
				var pagerowscontainer = jQ_nxs(pagecontainer).find(".nxs-postrows")[0];
				// afterwards update existing row
				var updateElement = jQ_nxs(pagerowscontainer).children()[rowindex];

				// before updating the dom, we first wipe any functions for notification ajax used by the "old" widgets in this row
				nxs_js_clear_ajaxrefresh_notifications(updateElement);
				
				// 
				//nxs_js_log("replacing index " + rowindex);
				jQ_nxs(updateElement).replaceWith(html);
				
				// after updating the dom, invoke execute_after_clientrefresh_XYZ for each widget in the affected first row, if present
				var updateElement = jQ_nxs(pagerowscontainer).children()[rowindex];
				nxs_js_notify_widgets_after_ajaxrefresh(updateElement);
				
				jQuery(document).trigger('nxs_dom_changed');
				
				nxs_js_reenable_all_window_events();
			});
			//nxs_js_log("done:" + rowindex);
		}

		function nxs_js_getfirstplaceholderid_in_dom(s)
		{
			var result = null;
			
  		jQ_nxs(s).find('.draggable_placeholder').each
  		(
  			function(idx2, elm2) 
  			{
  				var id = elm2.id;
  				result = id.split("-")[3];
				}
			);
			
			if (result == null)
			{
				jQ_nxs(s).find('.nxs-widget').each
	  		(
	  			function(idx2, elm2) 
	  			{
	  				var id = elm2.id;
	  				result = id.split("-")[2];
					}
				);
			}
			
			if (result == null)
			{
				nxs_js_log("placeholderid niet gevonden voor");
				nxs_js_log(s);
				nxs_js_log(jQ_nxs(s).html());
				nxs_js_alert('placeholderid niet gevonden');
				alert('placeholderid not found!');
			}
			
			return result;
		}
		
		function nxs_js_getmenuitemdepth_in_dom(s)
		{
			var result = -1;
			
			jQ_nxs(s).find('.nxs-widget').each
  		(
  			function(idx2, elm2) 
  			{
  				// de nxs-widget heeft een class 'nxs-listitem-depth-X'
  				var classes = this.className.split(" ");
				  for (var i = 0, len = classes.length; i < len; i++) 
				  {
				    if (classes[i].startsWith('nxs-listitem-depth-'))
				    {
				    	result = parseInt(classes[i].split("-", 4)[3]);
				    	break;
				    }
				  }
				}
			);
			
			//alert(result);
			
			return result;
		}
		
		function nxs_js_popup_placeholder_handleclick(placeholderdom)
		{
			var defaultelement = jQ_nxs(placeholderdom).find(".nxs-defaultwidgetclickhandler");
			if (defaultelement.length >= 1)
			{
				var de = defaultelement[0];
				var href = jQ_nxs(de).attr("href");
				if (href != "" && href != "#")
				{
					nxs_js_log("navigation to:" + href);
					window.location = href;
				}
				else
				{
					// ja, er is een default element gedefinieerd
					jQ_nxs(defaultelement[0]).click();
				}
			}
			else
			{
				var postid = nxs_js_findclosestpostid_for_dom(placeholderdom);
				var widget = jQ_nxs(placeholderdom).find(".nxs-widget").first();
				
				var placeholderid = jQ_nxs(widget).attr("id").split("-")[2];
				var rowindex = nxs_js_getrowindex(widget);
			
				// indien (optioneel) een "nxs-clickdefault" attribuut aanwezig
				// is binnen de dom van de widget, wordt het event doorgelust naar
				// dat element
				var widgetelement = nxs_js_getwidgetelement_forplaceholder(postid, placeholderid);				
				var defaultelement = jQ_nxs(placeholderdom).find(".nxs-defaultwidgetclickhandler");
				if (defaultelement.length >= 1)
				{
					// ja, er is een default element gedefinieerd
					jQ_nxs(defaultelement).click();
				}
				else
				{
					// nee, er is geen default gedrag gedefinieerd
					
					nxs_js_log('Warning; please upgrade deprecated widget; nxs-defaultwidgetclickhandler class attribute not set, defaulting to edit widget');
					// default implementation
					
					nxs_js_popup_placeholder_neweditsession(postid, placeholderid, rowindex, 'home');
				}
			}
		}
		
		// converts dictionary to properties of the specified scope object
		function nxs_js_extract(data, scope)
		{
	    for (var key in data) 
	    {
	    	if (!data.hasOwnProperty(key)) 
				{
					continue;
				}
        scope[key] = data[key];
    	}
		}
		
		function nxs_js_popup_placeholder_neweditsession(postid, placeholderid, rowindex, sheet)
		{
			var containerpostid = nxs_js_getcontainerpostid();
			var args = 
			{
				containerpostid:containerpostid,
				postid:postid,
				placeholderid:placeholderid,
				rowindex:rowindex,
				sheet:sheet
			};
			return nxs_js_popup_placeholder_neweditsession_v2(args);
		}
		
		// opent een pop up voor het bewerken van de meegegeven placeholder
		function nxs_js_popup_placeholder_neweditsession_v2(args)
		{
			nxs_js_extract(args, this);
			
			if (sheet == null)
			{
				sheet = 'home'; // default, if not set
			}
		
			if (nxs_js_popup_anyobjectionsforopeningnewpopup())
			{
				return;
			}
			
			// wipe any existing popupsession data
			nxs_js_popupsession_startnewcontext();

			if (typeof onsaverefreshpage === "undefined")
			{
				//
			}
			else
			{
				nxs_js_popup_setsessioncontext("onsaverefreshpage", true);
			}

			//
			nxs_js_popup_setsessioncontext("contextprocessor", "widgets");
			nxs_js_popup_setsessioncontext("containerpostid", containerpostid);
			nxs_js_popup_setsessioncontext("postid", postid);
			nxs_js_popup_setsessioncontext("placeholderid", placeholderid);
			nxs_js_popup_setsessioncontext("rowindex", rowindex);

			// show the popup			
			nxs_js_popup_navigateto(sheet);
		}
		
		// opent een pop up voor het bewerken van de meegegeven pagerowid
		function nxs_js_popup_row_neweditsession(postid, pagerowid, sheet)
		{
			if (nxs_js_popup_anyobjectionsforopeningnewpopup())
			{
				return;
			}
			
			// wipe any existing popupsession data
			nxs_js_popupsession_startnewcontext();
			
			//
			nxs_js_popup_setsessioncontext("contextprocessor", "pagerow");
			nxs_js_popup_setsessioncontext("postid", postid);
			nxs_js_popup_setsessioncontext("pagerowid", pagerowid);

			// show the popup			
			nxs_js_popup_navigateto(sheet);
		}
		
		// opent een pop up voor het bewerken van de meegegeven pagina
		function nxs_js_popup_postcontent_neweditsession(sheet)
		{
			var postid = nxs_js_getcontainerpostid();
		
			if (nxs_js_popup_anyobjectionsforopeningnewpopup())
			{
				return;
			}
			
			// wipe any existing popupsession data
			nxs_js_popupsession_startnewcontext();
			
			nxs_js_popup_setsessioncontext("contextprocessor", "postcontent");
			nxs_js_popup_setsessioncontext("postid", postid);

			// show the popup			
			nxs_js_popup_navigateto(sheet);
		}
		
		// opent een pop up voor het bewerken van de meegegeven pagina
		function nxs_js_popup_page_neweditsession(postid, sheet)
		{
			if (nxs_js_popup_anyobjectionsforopeningnewpopup())
			{
				return;
			}
			
			// wipe any existing popupsession data
			nxs_js_popupsession_startnewcontext();
			
			nxs_js_popup_setsessioncontext("contextprocessor", "post");
			nxs_js_popup_setsessioncontext("postid", postid);

			// show the popup			
			nxs_js_popup_navigateto(sheet);
		}
		
		// opens a popup page to edit for example the footer, header or sidebar of the page
		function nxs_js_popup_pagetemplate_neweditsession(sheet)
		{	
			var postid = nxs_js_getcontainerpostid();
			var pagetemplate = nxs_js_getcontainerpagetemplate();
		
			if (nxs_js_popup_anyobjectionsforopeningnewpopup())
			{
				return;
			}
			
			// wipe any existing popupsession data
			nxs_js_popupsession_startnewcontext();
			
			//
			nxs_js_popup_setsessioncontext("contextprocessor", "pagetemplate");
			nxs_js_popup_setsessioncontext("postid", postid);
			nxs_js_popup_setsessioncontext("pagetemplate", pagetemplate);

			// show the popup			
			nxs_js_popup_navigateto(sheet);
		}
		
		// opent een pop up voor het instellen van de meegegeven pagelet voor de meegegeven pagetemplate in de meegegeven post
		function nxs_js_popup_pageletinpagetemplate_neweditsession(postid, pagetemplate, pageletname, sheet)
		{
			if (nxs_js_popup_anyobjectionsforopeningnewpopup())
			{
				return;
			}
			
			// wipe any existing popupsession data
			nxs_js_popupsession_startnewcontext();
			
			nxs_js_popup_setsessioncontext("contextprocessor", "pageletinpagetemplate");
			nxs_js_popup_setsessioncontext("postid", postid);
			nxs_js_popup_setsessioncontext("pagetemplate", pagetemplate);
			nxs_js_popup_setsessioncontext("pageletname", pageletname);

			// show the popup			
			nxs_js_popup_navigateto(sheet);
		}
		
		// opent een pop up voor het instellen van de meegegeven pagelet voor de meegegeven pagetemplate in de meegegeven post
		function nxs_js_popup_subheaderinpagetemplate_neweditsession(postid, pagetemplate, subheadername, sheet)
		{
			if (nxs_js_popup_anyobjectionsforopeningnewpopup())
			{
				return;
			}
			
			// wipe any existing popupsession data
			nxs_js_popupsession_startnewcontext();
			
			nxs_js_popup_setsessioncontext("contextprocessor", "pageletinpagetemplate");
			nxs_js_popup_setsessioncontext("postid", postid);
			nxs_js_popup_setsessioncontext("pagetemplate", pagetemplate);
			nxs_js_popup_setsessioncontext("subheadername", subheadername);

			// show the popup			
			nxs_js_popup_navigateto(sheet);
		}
		
		// opent een pop up voor het instellen van de meegegeven pagelet voor de meegegeven pagetemplate in de meegegeven post
		function nxs_js_popup_subfooterinpagetemplate_neweditsession(postid, pagetemplate, subfootername, sheet)
		{
			if (nxs_js_popup_anyobjectionsforopeningnewpopup())
			{
				return;
			}
			
			// wipe any existing popupsession data
			nxs_js_popupsession_startnewcontext();
			
			nxs_js_popup_setsessioncontext("contextprocessor", "pageletinpagetemplate");
			nxs_js_popup_setsessioncontext("postid", postid);
			nxs_js_popup_setsessioncontext("pagetemplate", pagetemplate);
			nxs_js_popup_setsessioncontext("subfootername", subfootername);

			// show the popup			
			nxs_js_popup_navigateto(sheet);
		}
		
		function nxs_js_trash_article(postid)
		{
			var answer = confirm(nxs_js_gettrans('Are you sure you want to delete this page?'));
			if (!answer)
			{
				return;
			}
			nxs_js_trash_article_no_question(postid);
		}
		
		function nxs_js_invokewebmethod(options, handlehappyflow, handlealternativeflow, handlefailureflow)
		{
			if ('confirmtext' in options)
			{
				var text = options.confirmtext;
				var answer = confirm(text);
				if (!answer)
				{
					// toch niet
					return true;
				}
			}
			var waitgrowltoken = -1;
			if ('waitgrowltext' in options)
			{
				var text = options.waitgrowltext;
				waitgrowltoken = nxs_js_alert_wait_start(text);
			}
			
			var data = options.webmethoddata;
			var async = true;	// default = async
			if ('async' in options)
			{
				async = options.async;
			}
			var cache = false;	// default = no caching
			if ('cache' in options)
			{
				cache = options.cache;
			}
			data.action = "nxs_ajax_webmethods";
		
			// invoke ajax call
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					url: ajaxurl, 
					data: data,
					async: async,
					cache: cache,
					dataType: 'JSON',
					success: function(response) 
					{
						// dismiss loading growl if exists						
						nxs_js_alert_wait_finish(waitgrowltoken);
						nxs_js_log(response);
						if (response.result == "OK")
						{
							if ('happyflowgrowltext' in options)
							{
								var text = options.happyflowgrowltext;
								nxs_js_alert(text);
							}
							
							if (handlehappyflow)
							{
								handlehappyflow(response);
							}
							else
							{
								nxs_js_log("if you want to invoke a function, pass a function as handlehappyflow parameter");
							}
						}
						else
						{
							
							
							handlealternativeflow(response);
						}
					},
					error: function(response)
					{
						// dismiss loading growl if exists
						nxs_js_alert_wait_finish(waitgrowltoken);
						nxs_js_popup_notifyservererror();
						nxs_js_log(response);
						
						if ('errorflowgrowltext' in options)
						{
							var text = options.errorflowgrowltext;
							nxs_js_alert(text);
						}

						if (handlefailureflow && getClass.call(handlefailureflow) == '[object Function]')
						{
							handlefailureflow(response);
						}
						else
						{
							nxs_js_log("if you want to invoke a function, pass a function as handlefailureflow parameter");
						}
					}										
				}
			);
		}
		
		function nxs_js_trash_article_no_question(postid)
		{		
			// invoke ajax call
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "removepage",
						"postid": postid,
						"howto": "trash"
					},
					async: false,
					cache: false,
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// ok
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

		function nxs_js_delete_article_no_question(postid)
		{
			// invoke ajax call
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "removepage",
						"postid": postid,
						"howto": "permanent"
					},
					async: false,
					cache: false,
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// ok
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
		
		function nxs_js_delete_article(postid)
		{
			var answer = confirm(nxs_js_gettrans('Are you sure you want to delete this page?'));
			if (!answer)
			{
				return;
			}
			
			nxs_js_delete_article_no_question(postid);
		}

		function nxs_js_restore_article(postid)
		{
			// invoke ajax call
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "restorepage",
						"postid": postid
					},
					async: false,
					cache: false,
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// ok
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
		
		function nxs_js_popup_placeholder_wipe(postid, placeholderid)
		{
			nxs_js_log('Warning; obsolete function used; nxs_js_popup_placeholder_wipe(postid, placeholderid)');
			var widget = nxs_js_getwidgetinpostid(postid, placeholderid);
			nxs_js_popup_wipe_widget(widget);
		}
		
		function nxs_js_popup_wipe_closestwidgetindom(dom)
		{
			// first find the placeholder, then within the placeholder find the nxs-widget
			var placeholder = jQ_nxs(dom).closest(".nxs-placeholder");
			var widget = jQ_nxs(placeholder).find(".nxs-widget")[0];
			nxs_js_popup_wipe_widget(widget);
		}
		
		function nxs_js_frontendediting_widgethandledelete(placeholderdom)
		{
			var postid = nxs_js_findclosestpostid_for_dom(placeholderdom);
			var widget = placeholderdom;
			var placeholderid = jQ_nxs(widget).attr("id").split("-")[2];
			var rowindex = nxs_js_getrowindex(widget);
			var widgetelement = nxs_js_getwidgetelement_forplaceholder(postid, placeholderid);				
			var placeholderelement = jQ_nxs(placeholderdom).closest(".nxs-placeholder")[0];
			var defaultelement = jQ_nxs(placeholderelement).find(".nxs-defaultwidgetdeletehandler");
			if (defaultelement.length >= 1)
			{
				// ja, er is een default element gedefinieerd
				jQ_nxs(defaultelement).click();
			}
			else
			{
				nxs_js_log("Fallback scenario; no default delete handler found for this widget, explicity deleting this widget instead.");
				// nee, er is geen default gedrag gedefinieerd; wipe widget
				nxs_js_popup_wipe_widget(widget);
			}
		}
		
		function nxs_js_popup_wipe_widget(widget)
		{
			var postid = nxs_js_findclosestpostid_for_dom(widget);
			var placeholderid = nxs_js_getplaceholderidofwidgetdom(widget);

			// delete the placeholder
			var answer = confirm(nxs_js_gettrans('Are you sure you want to remove this widget?'));
			if (answer)
			{
				var waitgrowltoken = nxs_js_alert_wait_start(nxs_js_gettrans('Wiping element'));
				
				// invoke ajax call
				var ajaxurl = nxs_js_get_adminurladminajax();
				jQ_nxs.ajax
				(
					{
						type: 'POST',
						data: 
						{
							"action": "nxs_ajax_webmethods",
							"webmethod": "wipe",
							"context": "placeholder",
							"postid": postid,
							"placeholderid": placeholderid
						},
						cache: false,
						dataType: 'JSON',
						url: ajaxurl, 
						success: function(response) 
						{
							nxs_js_alert_wait_finish(waitgrowltoken);
							
							//nxs_js_log(response);
							if (response.result == "OK")
							{
								nxs_js_alert(nxs_js_gettrans('Widget is now empty'));
								
								// update screen
								nxs_js_rerender_row_for_placeholder(postid, placeholderid);
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
			else
			{
				// nop
			}
		}
		
		function nxs_js_refreshallpagerows(postid, containerElement, invokewhenavailable)
		{
			var rendermode = "default";
			var containerpostid = nxs_js_getcontainerpostid();
			nxs_js_rows_render(containerpostid, postid, rendermode, function(html)
			{
				jQ_nxs(containerElement).html(html);
				// reenable all events
				nxs_js_reenable_all_window_events();
				
				invokewhenavailable();
				jQuery(document).trigger('nxs_dom_changed');
			});
		}
		
		function nxs_js_refreshelementscontainerforelement(element, rendermode, invokewhenavailable)
		{
			// derive postid to be refreshed
			var containerElement = jQ_nxs(element).closest(".nxs-elements-container");
			var cursorofelementscontainer = jQ_nxs(containerElement).find(".nxs-elements-cursor");
			var elementtoreplace = jQ_nxs(containerElement).find(".nxs-postrows");
			var elementspostid = jQ_nxs(cursorofelementscontainer).attr("id").split("-")[2];
			var containerpostid = nxs_js_getcontainerpostid();
			
			//nxs_js_log("containerpostid;" + containerpostid);
			//nxs_js_log("elementspostid;" + elementspostid);
			// gjgj
			nxs_js_rows_render(containerpostid, elementspostid, rendermode, function(html)
			{
				jQ_nxs(elementtoreplace).replaceWith(html);
				// reenable all events
				nxs_js_reenable_all_window_events();
				
				invokewhenavailable();
			});
		}
		
		function nxs_js_rows_render(containerpostid, postid, rendermode, invokewhenavailable)
		{
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "getcontent",
						"clientpopupsessioncontext": nxs_js_getescaped_popupsession_context(),
						"contenttype": "rows",
						"contentcontext": "rows_" + containerpostid + "_" + postid + "_" + rendermode,
						"clientqueryparameters": nxs_js_escaped_getqueryparametervalues()
					},
					async: false,
					cache: false,
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							invokewhenavailable(response.html);
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

		// removes the row to which this domelement belongs
		// after asking for confirmation. This also updates the dom
		function nxs_js_row_remove(domelement)
		{
      //
			var postid = nxs_js_findclosestpostid_for_dom(domelement);
			var rowindex = nxs_js_getrowindex(domelement);
			var rowid = nxs_js_getrowid(domelement);
			var closestrow = jQ_nxs(domelement).closest('.nxs-row'); // find the row that is going to be deleted
      var widgetid = jQ_nxs(closestrow).find('.nxs-widget').attr('id');
			var num_of_menus_in_row = jQ_nxs(closestrow).find('.nxs-menu').length; // find if there is a menu in the specific row that is going to be deleted
			var firstconfirm = confirm(nxs_js_gettrans('Are you sure you want to delete this row?'));
				
			if(num_of_menus_in_row > 0){ // if there are menus in the row, give a confirm-dialog
				var secondconfirm = confirm(nxs_js_gettrans('There is a menu in this row, are you really sure you want to delete this row?'));
			}
			
			var shoulddotheactualdelete = false; // no deleting yet
			
			if(firstconfirm && (num_of_menus_in_row == 0)){ // first confirm is given and there are no menus in row
					shoulddotheactualdelete = true; // row can be deleted
			} else if((firstconfirm && (num_of_menus_in_row > 0)) && secondconfirm){ // first confirm is given but there are menus in the row, so the second confirm is given
					shoulddotheactualdelete = true; // row can be deleted
			}
	
			if (shoulddotheactualdelete)
			{
				var waitgrowltokenx = nxs_js_alert_wait_start(nxs_js_gettrans('Removing row'));
				
				// invoke ajax call
				var ajaxurl = nxs_js_get_adminurladminajax();
				jQ_nxs.ajax
				(
					{
						type: 'POST',
						data: 
						{
							"action": "nxs_ajax_webmethods",
							"webmethod": "removerow",
							"postid": postid,
							"rowindex": rowindex,
							"rowid": rowid,
              "widgetid": widgetid
						},
						cache: false,
						dataType: 'JSON',
						url: ajaxurl, 
						success: function(response) 
						{
							nxs_js_alert_wait_finish(waitgrowltokenx);
							nxs_js_log(response);
							
							if (response.result == "OK")
							{
								if (response.pagedirty == "true")
								{
									var waitgrowltokenb = nxs_js_alert_wait_start(nxs_js_gettrans('Refreshing page'));
									
									// het hele scherm moet worden bijgewerkt (dit is het geval
									// wanneer bijvoorbeeld de laatste row is verwijderd,
									// en het systeem automatisch een nieuwe row heeft toegevoegd (rowsetup) 
									var containerElement = jQ_nxs(".nxs-post-" + postid)[0];		
									nxs_js_refreshallpagerows(postid, containerElement, function()
									{
										nxs_js_alert_wait_finish(waitgrowltokenb);
									});
								}
								else
								{
									nxs_js_log("row deleted succesfully, index: rowindex:" + rowindex);
									
									// update GUI
									if (false) // rowindex == 0)
									{
										var waitgrowltokenc = nxs_js_alert_wait_start(nxs_js_gettrans('Refreshing page'));
										
										// indien de eerste regel was gedelete moeten we de
										// regel daaronder (die nu dus de eerste regel is
										// geworden) refreshen, voor nu even eenvoudig opgelost
										// door het hele editable deel te verversen
										var containerElement = jQ_nxs(".nxs-post-" + postid)[0];
										
										nxs_js_refreshallpagerows(postid, containerElement, function()
										{
											nxs_js_alert_wait_finish(waitgrowltokenc);
										});
									}
									else
									{
										var waitgrowltokend = nxs_js_alert_wait_start(nxs_js_gettrans('Refreshing page'));
										var row = jQ_nxs(domelement).closest('.nxs-row');
										jQ_nxs(document).trigger('nxs_dom_changed');
										jQ_nxs(row).slideUp('slow', 

										function()
										{
											jQ_nxs(row).remove();
											nxs_js_alert_wait_finish(waitgrowltokend);
											// de hoogte is aangepast, pas ook de sidebar hoogte aan (indien nodig)
											nxs_js_reenable_all_window_events();
										}
									);
									}
									//
								}
							}
							else
							{
								nxs_js_alert_wait_finish(waitgrowltoken);
								
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
			else
			{
				// nop
			}
		}
		
		function nxs_js_reenable_all_window_events()
		{
			if (!nxs_js_enableguieffects())
			{
				// no further processing
				return;
			}
		
			nxs_js_reregister_click_and_hover_events();
			nxs_js_gui_setup_drag_listeners();
			
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
			
			nxs_gui_set_runtime_dimensions_enqueuerequest("nxs-framework-windoweventsreenabled");

			if (nxs_js_userhasadminpermissions())
			{
				// als de gebruiker admin is, start het scherm altijd in edit modus,
				// hierbij staan we niet toe dat er tekst wordt geselecteerd
				if (nxs_js_nxseditoractive)
				{
					jQ_nxs('body').disableSelection();
				}
				else
				{
					jQ_nxs('body').enableSelection();
				}
			}
			//nxs_js_log('runtime dimensions set');	
		}

		function nxs_gui_set_runtime_dimensions_enqueuerequest(trigger)
		{		
			//nxs_js_log('enqueued for:' + trigger);
		
			var skip = false;
			if (nxs_js_isruntimedimensionsrefreshqueued())
			{
				// already enqueued, mark to skip this request,
				// note we wont return yet, as we first need to mark that
				// the enqueue also involves the specified trigger
				skip = true;
			}
			
			var triggerarray;
			// convert trigger to array
			if (typeof trigger == 'string' || trigger instanceof String)
			{
				triggerarray = [trigger];
			}
			else if ($.isArray(trigger))
			{
				triggerarray = trigger;
				if (triggerarray.length == 0)
				{
					nxs_js_alert('Warning, trigger array is empty');
					nxs_js_log(trigger);
					return;	
				}
			}
			else
			{
				nxs_js_alert('Warning, unsupported trigger type');
				nxs_js_log(trigger);
				return;
			}
			
			for (var i = 0; i < triggerarray.length; i++) 
			{
			  var currenttrigger = triggerarray[i];
			  if (!nxs_js_isrefreshqueuetriggeredby(currenttrigger))
				{
					// mark as triggered for this specific trigger
					nxs_js_queuedruntimedimensionsrefreshtriggers.push(currenttrigger);
				}
				else
				{
					skip = true;
				}
			}
			
			if (skip)
			{
				return;
			}
			
			// if we reach this point, it means this is the first enqueued request
			var nxs_max_refresh_frequency_in_msecs = 100;	// lower this amount to get a snappier speed, but poorer performance, 100 = default
			setTimeout
			(
				function() 
				{
					//nxs_js_log('executing actual refresh work');
					// first we dequeue! 
					
					nxs_gui_set_runtime_dimensions_actualrequest();
					
					
				},nxs_max_refresh_frequency_in_msecs
			);
		}
		
		function nxs_js_getheightoftallestwidgetwithindomelement(domelement)
		{
			return nxs_js_getheightoftallestwidgetwithindomelement_v2(domelement, ".XYZ");
		}
		
		function nxs_js_getheightoftallestwidgetwithindomelement_v2(domelement, innerselector)
		{
			var domelements = jQ_nxs(domelement).find(innerselector);
			var result = 0;
			jQuery.each(domelements, function(index, currentdomelement)
			{
				var height = jQ_nxs(currentdomelement).outerHeight(true);
				if (height > result)
				{
					result = height;
				}
			}
			);
			return result;
		}
		
		// kudos to http://stackoverflow.com/questions/5503900/how-to-sort-an-array-of-objects-with-jquery-or-javascript
		function nxs_js_sortbyname(a, b){
		  var aName = a.toLowerCase();
		  var bName = b.toLowerCase(); 
		  return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
		}
		
		function nxs_js_shouldsetelementheight(element)
		{
			var result = true;
		
			if (jQ_nxs(element).hasClass("nxs-column-1-1"))
			{
				result = false;
			}
			else if (jQ_nxs(element).hasClass("nxs-widgettype-twittertweets"))
			{
				result = false;
			}
			else if (jQ_nxs(element).hasClass("nxs-widgettype-fblikebox"))
			{
				result = false;
			}
		
			return result;	
		}
		
		function nxs_js_alignheightofelementswithindomelement(domelement, innerselector)
		{
			//nxs_js_log('aligning height for row');
		
			var domelements = jQ_nxs(domelement).find(innerselector);
			
			var heighest = 0;
			jQuery.each(domelements, function(index, currentdomelement)
			{
				var currentheight = jQ_nxs(currentdomelement).outerHeight(true);
				if (currentheight > heighest)
				{
					heighest = currentheight;
					//nxs_js_log('new heighest:' + heighest);
				}
			}
			);
			
			jQuery.each(domelements, function(index, currentdomelement)
			{
				//nxs_js_log("align heights of elements to: " + heighest);
				jQ_nxs(currentdomelement).height(heighest);
			}
			);
		}
		
		// kudos to https://stackoverflow.com/questions/3514784/what-is-the-best-way-to-detect-a-mobile-device-in-jquery
		// is_mobile is_handheld ishandheld
		function nxs_js_ismobile()
		{
			if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) 
			{
 				return true;
			}
			else
			{
				return false;
			}
		}
		
		function nxs_gui_set_runtime_dimensions_actualrequest()
		{
			var shouldrestorescrollposition = false;
			
			// 20170707; fixes annoying scrolling stall/hickup when scrolling up on mobiles
			// only do this when logged in
			if (nxs_js_isuserloggedin())
			{
				if (nxs_js_ismobile())
				{
					// dont
				}
				else
				{
					shouldrestorescrollposition = true;
				}
			}
			
			if (shouldrestorescrollposition)
			{
				var scrollrevertid = nxs_js_ui_pushscrollrevert();
			}
		
			// todo: this can be optimized: the tagging of columns only has to occur when
			// the page is loaded, and after the core structure of the page is modified
			// basically when widgets/rows/containers are refreshed after a modification (delete, edit, drag... )
			// for now its most save to always invoke the method
			nxs_js_tagcolumns();
		
			// make clone of enqueued triggers
			nxs_js_runtimedimensionsrefreshtriggers = nxs_js_queuedruntimedimensionsrefreshtriggers.slice(0);
			
			// clear the enqueued triggers
			nxs_js_queuedruntimedimensionsrefreshtriggers = new Array();
			
			// verify that at least one trigger is set
			if (nxs_js_runtimedimensionsrefreshtriggers.length == 0)
			{
				// no trigger set?
				nxs_js_log('please set a trigger, absorbing this request');
				return;
			}
			else
			{
				//nxs_js_log('performing actual request, made by trigger(s):');
				//nxs_js_log(nxs_js_runtimedimensionsrefreshtriggers);
			}
		
			// stap 1; we resetten de hoogten, breedten en margins
			// deze undo-stap is belangrijk; als bijv. een lege placeholder naast een gevulde
			// placeholder staat, en de plapceholder er naast wordt korter door een modificatie,
			// dan zou de undefined placeholder de hoogte de hoogte row beinvloeden doordat
			// de hoogte reeds gezet is; dit is ongewenst. We verwijderen dus
			// allereerst de breedte en hoogtes van cellen die 'autocellsize' zijn.
			
			jQ_nxs(".nxs-valign-middle").css('margin-top', 0);
			jQ_nxs(".nxs-valign-bottom").css('margin-top', 0);
			
			jQuery.each
			(
				jQ_nxs(".nxs-runtime-autocellsize"), function(index, element)
				{
					// heights					
					if (jQ_nxs(element).hasClass("nxs-minheight"))
					{
						if (jQ_nxs(element).hasClass("nxs-minheight-40-0"))
						{
							jQ_nxs(element).css('height', '40');	// defaults to height when no height is set
						}
						else if (jQ_nxs(element).hasClass("nxs-minheight-80-0"))
						{
							jQ_nxs(element).css('height', '80');	// defaults to height when no height is set
						}
						else if (jQ_nxs(element).hasClass("nxs-minheight-120-0"))
						{
							jQ_nxs(element).css('height', '120');	// defaults to height when no height is set
						}
						else if (jQ_nxs(element).hasClass("nxs-minheight-160-0"))
						{
							jQ_nxs(element).css('height', '160');	// defaults to height when no height is set
						}
						else if (jQ_nxs(element).hasClass("nxs-minheight-200-0"))
						{
							jQ_nxs(element).css('height', '200');	// defaults to height when no height is set
						}
						else if (jQ_nxs(element).hasClass("nxs-minheight-240-0"))
						{
							jQ_nxs(element).css('height', '240');	// defaults to height when no height is set
						}
						else if (jQ_nxs(element).hasClass("nxs-minheight-280-0"))
						{
							jQ_nxs(element).css('height', '280');	// defaults to height when no height is set
						}
						else if (jQ_nxs(element).hasClass("nxs-minheight-320-0"))
						{
							jQ_nxs(element).css('height', '320');	// defaults to height when no height is set
						}
						else if (jQ_nxs(element).hasClass("nxs-minheight-360-0"))
						{
							jQ_nxs(element).css('height', '360');	// defaults to height when no height is set
						}
						else if (jQ_nxs(element).hasClass("nxs-minheight-400-0"))
						{
							jQ_nxs(element).css('height', '400');	// defaults to height when no height is set
						}
						else if (jQ_nxs(element).hasClass("nxs-minheight-440-0"))
						{
							jQ_nxs(element).css('height', '440');	// defaults to height when no height is set
						}
						else if (jQ_nxs(element).hasClass("nxs-minheight-480-0"))
						{
							jQ_nxs(element).css('height', '480');	// defaults to height when no height is set
						}
						else
						{
							nxs_js_log('warning; min height not (yet?) supported');
							jQ_nxs(element).height(88);	// defaults to height when no height is set
						}
					}
					else
					{
						// afhankelijk van of de editor aan- of uitstaat...
						if (nxs_js_nxseditoractive)
						{
							if (!nxs_js_shouldsetelementheight(element))
							{
								// if a row has just one column, we wont set the height of the row
								// to allow DOM updates to not mess up the layout
							}
							else
							{
								jQ_nxs(element).height(87);	// defaults to height when no height is set
							}
						}
						else
						{
							if (!nxs_js_shouldsetelementheight(element))
							{
								// if a row has just one column, we wont set the height of the row
								// to allow DOM updates to not mess up the layout
							}
							else
							{
								//nxs_js_log("c) setting height to 0");
								jQ_nxs(element).height(0);	// defaults to height when no height is set
							}
						}
					}
					
					// any future resetting ...
				}
			);
			
			//nxs_js_log('broadcasting event nxs_recalculateruntimedimensions_afterclear');
						
			var widthofbrowserwindow = jQ_nxs(window).width();
			// nxs_js_log("width of browser:" + widthofbrowserwindow);
			
			// revert heights set previous time
			// reset heigthiq heights
			// als we deze regel hieronder uitcommentarieren ontstaat het probleem
			// dat de browser omhoog scrollt indien slider2 wordt gebruikt
			// jQ_nxs(".nxs-heightiq").css({height:""});
			
			// allow widgets and/or plugins to extend the functionality of this function (act / bind to the hook / event)
  			jQ_nxs(window).trigger('nxs_recalculateruntimedimensions_afterclear');

			// reset heights of placeholder elements
			jQ_nxs(".nxs-dyn-height").css('height', 'auto').removeClass("nxs-dyn-height");

			var arerowsspreadalongmultirows = (widthofbrowserwindow <= 959);
			
			if (!arerowsspreadalongmultirows)
			{
				jQ_nxs("html").addClass("nxs-rows-nonresponsive");
				jQ_nxs("html").removeClass("nxs-rows-responsive");
			
				//nxs_js_log('regular - valign is active');
				
				// step 2; derive heights on a per-row basis, and set the heights of the nxs-runtime-autocellsizes
				jQuery.each
				(
					jQ_nxs(".nxs-row"), 
					function(index, rowelement)
					{						
						jQ_nxs(rowelement).find(".nxs-heightiq").css({height:""});
					
						// get list of "things" to align
						var distinctheightiqtypes = nxs_js_finddistinctclassidentifications(rowelement, "nxs-heightiq");
						// order items
						distinctheightiqtypes.sort(nxs_js_sortbyname);
						for (var i = 0; i < distinctheightiqtypes.length; i++)
						{
							var distinctheightiqtype = distinctheightiqtypes[i];	// bijv. "title"
							// nxs_js_log("distinctheightiqtype:" + distinctheightiqtype);
							nxs_js_alignheightofelementswithindomelement(rowelement, ".nxs-heightiq-" + distinctheightiqtype);
						}
						
						// determine height for each individual row; 
						// the height will be set to the height of the tallest widget 
						// within that specific row
						var heighestHeightInRow = nxs_js_getheightoftallestwidgetwithindomelement(rowelement);
						if (heighestHeightInRow == 0)
						{
							if (nxs_js_nxseditoractive)
							{
								// if all widgets are "empty" and the editor is active we will default to 81 pixels
								heighestHeightInRow = 81;
							}
							else
							{
								// suppress the row!
								// jQ_nxs(rowelement).addClass('nxs-suppress');
							}
						}
						
						// set height for all elements within this row
						var elementsToBeResized = jQ_nxs(rowelement).find(".nxs-runtime-autocellsize");
						jQuery.each(elementsToBeResized, function(index, element)
						{
							// houdt er rekening mee of de parent class een nxs-margin-bottom20 class heeft,
							// indien zo, dan moet de height 20 korter worden!
	
							var shouldshrinkforborder = false;
							if (jQ_nxs(element).hasClass("autosize-smaller"))
							{
								shouldshrinkforborder = true;
							}
							
							var numofmarginbottoms = jQ_nxs(element).closest(".nxs-placeholder").find(".nxs-margin-bottom20").length;
							var hasextendedmarginbottom = (numofmarginbottoms > 0);
							var shouldshorten = (hasextendedmarginbottom == true);
	
							var heightToSet = heighestHeightInRow;
							if (shouldshorten)
							{
								heightToSet = heightToSet - 20;
							}
							if (shouldshrinkforborder)
							{
								heightToSet = heightToSet - 2;
							}
							
							if (!nxs_js_shouldsetelementheight(element))
							{
								// absorb; if a row has just one column, there's no advantage
								// of setting the height. To allow DOM updates in the widget
								// to be reflected we dont set the height
							}
							else
							{
								//nxs_js_log("b) setting height to :" + heightToSet);
								jQ_nxs(element).height(heightToSet);
							}
						}
						);
						
						//nxs_js_log("heighest row:");
						//nxs_js_log(heighestHeightInRow);
						
						
						// set valign
						var innerplaceholders = jQ_nxs(rowelement).find(".XYZ");
						jQuery.each(innerplaceholders, function(index, currentinnerplaceholder)
						{
							var currentHeight = jQ_nxs(currentinnerplaceholder).outerHeight(true);
							
							// bottom
							var toBeValigned = jQ_nxs(currentinnerplaceholder).parent().find(".nxs-valign-bottom");
							jQuery.each(toBeValigned, function(index, currentToBeValigned)
							{
								//nxs_js_log("found");
															
								var deltaheight = heighestHeightInRow - currentHeight;
								//nxs_js_log("delta:" + deltaheight);
								jQ_nxs(currentToBeValigned).css('margin-top', deltaheight);
							});
							
							// middle
							var toBeValigned = jQ_nxs(currentinnerplaceholder).parent().find(".nxs-valign-middle");
							jQuery.each(toBeValigned, function(index, currentToBeValigned)
							{
								//nxs_js_log("found");
								var deltaheight = Math.floor((heighestHeightInRow - currentHeight) / 2);
								//nxs_js_log("delta:" + deltaheight);
								jQ_nxs(currentToBeValigned).css('margin-top', deltaheight);
							});
						}
						);
					}
				);
			}
			else
			{
				//nxs_js_log("scenario applies!!");
				
				jQ_nxs("html").addClass("nxs-rows-responsive");
				jQ_nxs("html").removeClass("nxs-rows-nonresponsive");
			
				// nxs_js_log('spread across multiple rows');	
				// rows are potentially spread across multiple rows; we should not set heights in that case
				
				// reset heights of heightiq elements
				jQ_nxs(".nxs-heightiq").css('height', 'auto');
				
				jQuery.each
				(
					jQ_nxs(".nxs-row"), 
					function(index, rowelement)
					{
						var columnindex = 1;
					
						// set height
						var innerplaceholders = jQ_nxs(rowelement).find(".XYZ");
						jQuery.each
						(
							innerplaceholders, function(index, currentinnerplaceholder)
							{
								jQ_nxs(currentinnerplaceholder).css('height', 'auto');
								jQ_nxs(currentinnerplaceholder).css('margin-top', '0');
								var height = jQ_nxs(currentinnerplaceholder).outerHeight(true);
								
								var element = jQ_nxs(currentinnerplaceholder).closest('.nxs-placeholder');
								jQ_nxs(element).each
								(
									function(index)
									{
										var element = this;
										if (nxs_js_shouldsetelementheight(element))
										{
											jQ_nxs(element).css('height', height).addClass("nxs-dyn-height");
											//nxs_js_log("f) setting height to: " + height);
										}
										else
										{
											// skip the element
										}
									}
								);
							}
						);
					}
				);
			}
			
			// step 3; (re) initialize google maps (if present)
			// todo: move this implementation to google maps widget...
			for (mapkey in nxs_js_maps)
			{
				if (!nxs_js_maps.hasOwnProperty(mapkey)) 
				{
					continue;
				}
				
				//nxs_js_log('resizing mapkey:' + mapkey);
				var center = nxs_js_maps[mapkey].getCenter();
				google.maps.event.trigger(nxs_js_maps[mapkey], 'resize'); 
				nxs_js_maps[mapkey].setCenter(center); 
			}
			
			//
			// automatically fit cursors that have a "nxs-autofit-to-parent" class
			// to the size of their parents 
			// 
			//nxs_js_log('auto fitting...');
			jQ_nxs(".nxs-autofit-to-parent").each
			(
				function(index)
				{
					var parent = jQ_nxs(this).parent();
					var width = jQ_nxs(parent).outerWidth(true);
					jQ_nxs(this).width(width);
					var height = jQ_nxs(parent).outerHeight(true);
					//nxs_js_log('d) height will be set to: ' + height);
					jQ_nxs(this).height(height);
				}
			);

			//nxs_js_log('broadcasting event nxs_recalculateruntimedimensions');
			
			// allow widgets and/or plugins to extend the functionality of this function (act / bind to the hook / event)
  		jQ_nxs(window).trigger('nxs_recalculateruntimedimensions');
  		jQ_nxs(document).trigger('nxs_dom_changed');
  		
  		if (shouldrestorescrollposition)
  		{
				nxs_js_ui_popscrollrevert(scrollrevertid);
			}
		}
		
		function nxs_js_isrefreshtriggeredbyatleastoneof(trigger)
		{
			var triggerarray;
			// convert trigger to array
			if (typeof trigger == 'string' || trigger instanceof String)
			{
				triggerarray = [trigger];
			}
			else if (jQuery.isArray(trigger))
			{
				triggerarray = trigger;
				if (triggerarray.length == 0)
				{
					nxs_js_alert('Warning, trigger array is empty');
					nxs_js_log(trigger);
					return;	
				}
			}
			else
			{
				nxs_js_alert('Warning, unsupported trigger type');
				nxs_js_log(trigger);
				return;
			}
		
			var result = false;
			
			for (var i = 0; i < triggerarray.length; i++) 
			{
			  var currenttrigger = triggerarray[i];
				if (jQuery.inArray(currenttrigger, nxs_js_runtimedimensionsrefreshtriggers) > -1)
				{
					result = true;
				}
			}
			
			//nxs_js_log('question for ' + trigger + ' is ' + result);
			return result;
		}
		
		function nxs_js_isrefreshqueuetriggeredby(trigger)
		{
			var result = jQuery.inArray(trigger, nxs_js_queuedruntimedimensionsrefreshtriggers) > -1
			//nxs_js_log('question for queued ' + trigger + ' is ' + result);
			return result;
		}
		
		function nxs_js_isruntimedimensionsrefreshqueued()
		{
			return nxs_js_queuedruntimedimensionsrefreshtriggers.length > 0;
		}
		
		function nxs_js_getgrowlscripturl()
		{
			return nxs_js_getframeworkurl() + '/js/growl/jquery.jgrowl.x.js';
		}
		
		function nxs_js_htmldialogmessageok(title, html)
		{
			nxs_js_htmldialogmessageok_v2(title, html, "basic");
		}
		
		// custompopup custom popup htmlpopup html popup popup custom
		function nxs_js_htmldialogmessageok_v2(title, html, scaffoldingtype)
		{
			// show a popup
			nxs_js_popupsession_startnewcontext();
			nxs_js_popup_setsessioncontext("contextprocessor", "site");
			nxs_js_popup_setsessiondata("nxs_customhtml_scaffoldingtype", scaffoldingtype);
			nxs_js_popup_setsessiondata("nxs_customhtml_popupheadertitle", title);
			nxs_js_popup_setsessiondata("minwidth", "368");
			nxs_js_popup_setsessiondata("nxs_customhtml_customhtmlcanvascontent", html);
			nxs_js_popup_navigateto("customhtml");
		}
		
		function nxs_js_alert_sticky(text)
		{
			jQ_nxs.jGrowl(text, { sticky: true });
		}
		
		//
		//
		//
		
		function nxs_js_alert(text)
		{
			nxs_js_log("alerting:" + text);
			jQ_nxs.jGrowl(text, { sticky: false, life: 3000 });
		}
		
		//
		//
		//
		
		function nxs_js_alert_veryshort(text)
		{
			nxs_js_log("alerting very short:" + text);
			jQ_nxs.jGrowl(text, { sticky: false, life: 1000 });
		}
		
		//
		//
		//
		
				
		function nxs_js_alert_wait_start(message)
		{
			//nxs_js_log("nxs_js_getframeworkurl():" + nxs_js_get_frameworkurl());
			//nxs_js_log("alerting wait start:" + message);
			var stacktrace = nxs_js_getqueryparametervalue("stacktrace");
			if (nxs_js_isctrlkeydown || stacktrace != "")
			{
				// practical debug tool; if ctrl is pressed, output the stacktrac
				try { throw new Error("Stracktrace"); } catch (e) { console.log(e.stack); }
			}
			
			
			nxs_js_alert_identifier++;
			var token = "group-wait-" + nxs_js_alert_identifier;
			var text = "";
			text += "<div id='nxs-growl-" + token + "'>";
			text += "<span class='nxs-icon-clock' style='padding-right: 10px;'></span>";
			//text += "<img src='" + nxs_js_getframeworkurl() + "/images/icon-wait.png' style='padding-right: 10px;' />";
			if (message != null)
			{
				text = "<span style='padding-left: 10px;'>" + text + message + "</span>";
			}
			text += "</div>";
			jQ_nxs.jGrowl(text, { sticky: false, life: 3000, group: token });
			
			//nxs_js_log("created:" + token);
			
			return token;
		}
		
		//
		//
		//
		
		function nxs_js_alert_wait_finish(token)
		{
			if (token == -1)
			{
				return;
			}
		
			// if the finish is invoked soon after it was
			// started, the growl hasn't yet initialized
			// the growl popup. In that case the closing of the
			// growl does nothing, and the popup stays on the screen
			// too long. This is fixed by invoking the following function
			var retries = 10;
			nxs_js_alert_wait_finish_internal(token, retries);
		}
		
		function nxs_js_alert_wait_finish_internal(token, retriesleft)
		{
			if (jQ_nxs("#nxs-growl-" + token).length > 0)
			{
				// if dom element exists, invoke immediately
				nxs_js_alert_wait_finish_internal_actual(token);
			}
			else
			{
				//nxs_js_log('detected DOM doesnt (yet) have growl to close');
				if (retriesleft > 0)
				{
					// delayed invoked, but preventing endless loops...
					
					setTimeout
					(
						function()
						{
							//nxs_js_log("postponing close growl...");
							//nxs_js_log(token);
							retriesleft = retriesleft - 1;	
							//nxs_js_log(retriesleft);
							nxs_js_alert_wait_finish_internal(token, retriesleft);
						}
						,
						200);
				}
				else
				{
					//nxs_js_log('no more retries left...');
				}
			}
		}
		
		function nxs_js_alert_wait_finish_internal_actual(token)
		{
			//nxs_js_log("alerting wait finished:" + token);
			//nxs_js_log("closing token:" + token);
			
			jQ_nxs("div.jGrowl-notification." + token).trigger("jGrowl.close");
		}
		
		function nxs_js_logout()
		{
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "logout"
					},
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							if (response.previousstate == "notauthenticated")
							{
								// either the user's session was timed out
								// or the webserver was reset if we reach this point
								nxs_js_log('note; user was not logged on');
							}
							
							// close the pop up
							nxs_js_closepopup_unconditionally();					
							nxs_js_refreshcurrentpage();
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
		
		function nxs_js_help(helpid)
		{
			nxs_js_opensupportoptions();
		}

		function nxs_js_copytoclipboard(text) 
		{
		  window.prompt("Copy to clipboard: Ctrl+C, Enter", text);
		}
		
		// Copies a string to the clipboard. Must be called from within an 
		// event handler such as click. May return false if it failed, but
		// this is not always possible. Browser support for Chrome 43+, 
		// Firefox 42+, Safari 10+, Edge and IE 10+.
		// IE: The clipboard feature may be disabled by an administrator. By
		// default a prompt is shown the first time the clipboard is 
		// used (per session).
		function nxs_js_copytoclipboard_v2(text) 
		{
			nxs_js_alert_veryshort('Updating clipboard (' + text + ')');

    	if (window.clipboardData && window.clipboardData.setData) 
    	{
        // IE specific code path to prevent textarea being shown while dialog is visible.
        return clipboardData.setData("Text", text); 
	    }
	    else if (document.queryCommandSupported && document.queryCommandSupported("copy")) 
	    {
        var textarea = document.createElement("textarea");
        textarea.textContent = text;
        textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in MS Edge.
        document.body.appendChild(textarea);
        textarea.select();
        try {
            return document.execCommand("copy");  // Security exception may be thrown by some browsers.
        } catch (ex) {
            console.warn("Copy to clipboard failed.", ex);
            return false;
        } finally {
            document.body.removeChild(textarea);
        }
    	}
		}
		
		function nxs_js_popup_storestatecontroldata_checkbox(elementid, sessiondatakey)
		{
			if (jQ_nxs('#' + elementid).length > 0)
			{
				if (jQ_nxs('#' + elementid).is(':checked'))
				{
					nxs_js_popup_setsessiondata(sessiondatakey, 'checked=true');
				}
				else
				{
					nxs_js_popup_setsessiondata(sessiondatakey, '');
				}
			}
		}
		
		function nxs_js_popup_storestatecontroldata_checkbox_inverse(elementid, sessiondatakey)
		{
			if (jQ_nxs('#' + elementid).length > 0)
			{
				if (jQ_nxs('#' + elementid).is(':checked'))
				{
					nxs_js_popup_setsessiondata(sessiondatakey, '');
				}
				else
				{
					nxs_js_popup_setsessiondata(sessiondatakey, 'checked=true');
				}
			}
		}

		function nxs_js_popup_storestatecontroldata_listofcheckbox(ulcontainerelementid, checkboxitemsclassname, sessiondatakey)
		{
			if (jQ_nxs('#' + ulcontainerelementid).length > 0)
			{
				var derivedselectedids = '';
				jQ_nxs('#' + ulcontainerelementid + " ." + checkboxitemsclassname).each
				(
					function(index)
					{
						if (jQ_nxs(this).prop('checked'))
						{
							var currentcheckboxelementid = jQ_nxs(this).attr('id');
							derivedselectedids = derivedselectedids + "[" + currentcheckboxelementid.split("_")[1] + "]";
						}
					}
				);
				nxs_js_popup_setsessiondata(sessiondatakey, derivedselectedids);
			}
			else
			{
				nxs_js_log('element not found');
			}
		}
		
		function nxs_js_popup_storestatecontroldata_textbox(elementid, sessiondatakey)
		{
			if (jQ_nxs('#' + elementid).length > 0)
			{
				nxs_js_popup_setsessiondata(sessiondatakey, jQ_nxs('#' + elementid).val());
			}
		}
		
		// kudos to http://stackoverflow.com/questions/18082/validate-numbers-in-javascript-isnumeric
		function isNumber(n) 
		{
  		return !isNaN(parseFloat(n)) && isFinite(n);
		}
		
		function nxs_js_popup_storestatecontroldata_hiddenfield(elementid, sessiondatakey)
		{
			if (jQ_nxs('#' + elementid).length > 0)
			{
				nxs_js_popup_setsessiondata(sessiondatakey, jQ_nxs('#' + elementid).val());
			}
		}
		
		function nxs_js_popup_storestatecontroldata_dropdown(elementid, sessiondatakey)
		{
			if (jQ_nxs('#' + elementid).length > 0)
			{
				nxs_js_popup_setsessiondata(sessiondatakey, jQ_nxs('#' + elementid).val());
			}
		}
		
		// kudos to https://stackoverflow.com/questions/6234773/can-i-escape-html-special-chars-in-javascript
		function nxs_js_escapehtml(unsafe) 
		{
			if (unsafe == null)
			{
				return unsafe;
			}
    	return unsafe
			         .replace(/&/g, "&amp;")
			         .replace(/</g, "&lt;")
			         .replace(/>/g, "&gt;")
			         .replace(/"/g, "&quot;")
			         .replace(/'/g, "&#039;");
		}
		
		function nxs_js_postcomment(postid, containerpostid, placeholderid, parentcommentid, name, email, website, comment, invokewhenready, invokewhenfailed)
		{
			name = nxs_js_escapehtml(name);
			email = nxs_js_escapehtml(email);
			website = nxs_js_escapehtml(website);
			comment = nxs_js_escapehtml(comment);
			
			// invoke ajax call
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "addcomment",
						"postid": postid,
						"name": name,
						"email": email,
						"website": website,
						"comment": comment,
						"parentcommentid": parentcommentid,
						"containerpostid": containerpostid,
						"placeholderid": placeholderid
					},
					async: false,
					cache: false,
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// ok
							invokewhenready(response);
						}
						else
						{
							invokewhenfailed();
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
		
		function nxs_js_removecomment(postid, commentid, invokewhenready, invokewhenfailed)
		{
			// invoke ajax call
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "removecomment",
						"postid": postid,
						"commentid": commentid
					},
					async: false,
					cache: false,
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// ok
							invokewhenready();
						}
						else
						{
							invokewhenfailed();
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
		
		function nxs_js_getrandom(max)
		{
			return Math.floor(Math.random()*max);
		}
		
		function nxs_js_approvecomment(postid, commentid, invokewhenready, invokewhenfailed)
		{
			// invoke ajax call
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "approvecomment",
						"postid": postid,
						"commentid": commentid
					},
					async: false,
					cache: false,
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// ok
							invokewhenready();
						}
						else
						{
							invokewhenfailed();
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
		
		function nxs_js_geturl(destination, postid, nxsrefurlspecial, invokewhenready, invokewhenfailed)
		{
			// invoke ajax call
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "geturl",
						"destination": destination,
						"containerpostid": nxs_js_getcontainerpostid(),
						"postid": postid,
						"nxsrefurlspecial": nxsrefurlspecial
					},
					async: false,
					cache: false,
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// ok
							invokewhenready(response);
						}
						else
						{
							invokewhenfailed();
							nxs_js_popup_notifyservererror();
							nxs_js_log(response);
						}
					},
					error: function(response)
					{
						invokewhenfailed();
						nxs_js_popup_notifyservererror();
						nxs_js_log(response);
					}										
				}
			);
		}
		
		function nxs_js_clear_ajaxrefresh_notifications(container)
		{
			//nxs_js_log('invoking nxs_js_clear_ajaxrefresh_notifications');
		
			// container verwijst naar de regel waarvoor we de client refresh moeten
			// aanroepen (indien noodzakelijk / beschikbaar)
			jQ_nxs(container).find(".nxs-widget").each
  		(
  			function(idx2, elm2) 
  			{
  				var idwithfluff = elm2.id;
  				var id = idwithfluff.split("-")[2];
  				var functionname = "nxs_js_execute_after_ajaxrefresh_widget_" + id;
					var script = "<script>function " + functionname + "() { }</script>";
					// inject script in dom
					jQ_nxs(container).append(script);
					//nxs_js_log('injected:' + script);
				}
			);
		}
		
		// convenience function to get 
		// <div class="nxs-tag nxs-tag-foobar123"><domelement> will return "foobar123"
		// if invoked like nxs_js_findclassidentificationwithprefix_closest(domelement, ".nxs-tag", "nxs-tag-")
		function nxs_js_findclassidentificationwithprefix_closest(domelement, closestselector, prefix)
		{
			var closestElement = jQ_nxs(domelement).closest(closestselector);
			return nxs_js_findclassidentificationwithprefix(closestElement, prefix);
		}
		
		function nxs_js_finddistinctclassidentifications(domelement, prefixtype)
		{
			var result = [];

			var prefixselector = "." + prefixtype;	// for example ".nxs-heightiq"
			var prefix = prefixtype + "-";	// for example "nxs-heightiq-"
			
			var elements = jQ_nxs(domelement).find(prefixselector).each
  		(
  			function(i,currentelement) 
  			{
  				var identification = nxs_js_findclassidentificationwithprefix(currentelement, prefix);
					if (jQuery.inArray(identification, result) == -1)
					{
						result.push(identification);
					}
				}
			);
			
			return result;
		}
		
		// function to return a specific id after a certain prefix in the class attribute,
		// specified on the domelement. for example if the dom element looks like <div class="nxs-post-123">,
		// the function will return 123 if the prefix "nxs-post-" is specified. The function can also
		// be used to return alfa characters, or a combination. <div class="nxs-object-foobar123"> will
		// return "foobar123"
		// tags: identifier, id, identification, find, get, retrieve, subset, prefix, classname, getclass, derive
		function nxs_js_findclassidentificationwithprefix(domelement, prefix, returnifnotfound)
		{
		 	var classname = jQ_nxs(domelement).attr("class");
			var pattern = prefix + "([^\\s]*)";	// any set of chars till the moment a whitespace is found
			var modifiers = "";
		 	var regularexpression=new RegExp(pattern,modifiers);
		 	
		 	var result = null;
			var regexpresult = regularexpression.exec(classname);
			if (regexpresult != null)
			{
				result = regexpresult[1];
			}
			else
			{
				if (typeof returnifnotfound !== 'undefined')
				{
					result = returnifnotfound;
				}
				else
				{
					// not found
					nxs_js_log('warning; identification not found;');
					nxs_js_log('domelement:');
					nxs_js_log(domelement);
					nxs_js_log('prefix:');
					nxs_js_log(prefix);
				}
			}
			
		 	return result;
		}
		
		// gets the postid that this domelement belongs to
		// by traversing up in the dom, and finding the closest "nxs-elements-container" element
		// and returning the XYZ value of its "nxs-post-XYZ" class attribute.
		function nxs_js_findclosestpostid_for_dom(domelement)
		{
			var result;
			var elementscontainer = jQ_nxs(domelement).closest(".nxs-elements-container");
			if (elementscontainer.length == 0)
			{
				//nxs_js_log('not found?!');
				result = null;
			}
			else
			{
				result = nxs_js_findclassidentificationwithprefix(elementscontainer, "nxs-post-");
			}
			return result;
		}

		function nxs_js_edit_entity(domelementinentity)
		{
			nxs_js_alert('one moment...');
			var entitydom = jQ_nxs(domelementinentity).closest(".nxs-entity");
			var postid = jQ_nxs(entitydom).data("id");
			
			// invoke ajax call
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "getpostmetadatabyid",
						"postid": postid,
					},
					async: false,
					cache: false,
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// ok
							var url =  response.editurl;
							url = url.replace(/&amp;/g, '&');
							
							nxs_js_alert(url);
							//nxs_js_log(url);
							nxs_js_redirect(url);
						}
						else
						{
							invokewhenfailed();
							nxs_js_popup_notifyservererror();
							nxs_js_log(response);
						}
					},
					error: function(response)
					{
						invokewhenfailed();
						nxs_js_popup_notifyservererror();
						nxs_js_log(response);
					}										
				}
			);
		}
		
		function nxs_js_wipe_entity(domelementinentity)
		{
			nxs_js_alert('one moment...');
			var entitydom = jQ_nxs(domelementinentity).closest(".nxs-entity");
			var widgetdom = jQ_nxs(domelementinentity).closest(".nxs-widget");
			var postid = jQ_nxs(entitydom).data("id");
			
			// delete the entity
			var answer = confirm(nxs_js_gettrans('Are you sure you want to remove this entity?'));
			if (answer)
			{
				var waitgrowltoken = nxs_js_alert_wait_start(nxs_js_gettrans('Trashing entity'));
				
				// invoke ajax call
				var ajaxurl = nxs_js_get_adminurladminajax();
				jQ_nxs.ajax
				(
					{
						type: 'POST',
						data: 
						{
							"action": "nxs_ajax_webmethods",
							"webmethod": "wipe",
							"context": "entity",
							"postid": postid,
						},
						cache: false,
						dataType: 'JSON',
						url: ajaxurl, 
						success: function(response) 
						{
							nxs_js_alert_wait_finish(waitgrowltoken);
							
							//nxs_js_log(response);
							if (response.result == "OK")
							{
								nxs_js_alert(nxs_js_gettrans('Entity is trashed'));
								
								// update screen
								var postid = nxs_js_findclosestpostid_for_dom(widgetdom);
								var placeholderid = nxs_js_getplaceholderidofwidgetdom(widgetdom);
								nxs_js_rerender_row_for_placeholder(postid, placeholderid);
								
								// note; 
								// in the current implementation we only update the specific
								// row in which the entity exists. This is not optimal,
								// as in theory we also need to update other parts of the screen
								// that could also be using this entity (for example the entity
								// could be shows in both a menu, sidebar, content, etc. at the same
								// page). for now we accept this less optimal solution
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
			else
			{
				// nop
			}
		}
		
		function nxs_js_edit_widget(domelementinwidget)
		{
			nxs_js_edit_widget_v2(domelementinwidget, "home");
		}
		
		function nxs_js_edit_widget_v2(domelementinwidget, sheet)
		{
			// opens the popup window for this widget, defaults to the "home" sheet if not specified
			var postid = nxs_js_findclosestpostid_for_dom(domelementinwidget);
			var widget = jQ_nxs(domelementinwidget).closest(".nxs-placeholder").first();		
			var placeholderid = nxs_js_getfirstplaceholderid_in_dom(widget);
			var rowindex = nxs_js_getrowindex(widget);	
			//
			nxs_js_popup_placeholder_neweditsession(postid, placeholderid, rowindex, sheet);
		}
		
		function nxs_js_edit_row(domelementinwidget)
		{
			nxs_js_edit_row_v2(domelementinwidget, "home");
		}
		
		function nxs_js_edit_row_v2(domelementinwidget, sheet)
		{
			// opens the default "home" popup window for this row
			var postid = nxs_js_findclosestpostid_for_dom(domelementinwidget);
			//nxs_js_log(domelementinwidget);
			var row = jQ_nxs(domelementinwidget).closest(".nxs-row").first();
			var pagerowid = jQ_nxs(row).attr('id').split("-")[2];

			nxs_js_popup_row_neweditsession(postid, pagerowid, sheet);
		}
		
		function nxs_js_notify_widgets_after_ajaxrefresh(container)
		{
			// stage 1
		
			// container verwijst naar de regel waarvoor we de client refresh moeten
			// aanroepen (indien noodzakelijk / beschikbaar)
			jQ_nxs(container).find(".nxs-widget").each
  		(
  			function(idx2, elm2) 
  			{
  				var idwithfluff = elm2.id;
  				var id = idwithfluff.split("-")[2];
  				var functionname = "nxs_js_execute_after_ajaxrefresh_widget_" + id;
  				
  				var fn = window[functionname];
  				if (typeof(fn) == 'function')
  				{
  					//nxs_js_log("invoking function " + functionname);
  					// invoke!
  					fn();
  				}
  				else
  				{
  					//nxs_js_log("ignoring (not found) function " + functionname);
  				}
				}
			);
			
			// stage 2; if SEO tab is active, (enqueue) the SEO refresh
			nxs_js_refresh_seoanalysis();
		}
		
		function nxs_js_saveplaceholdertoclipboard(postid, placeholderid)
		{
			nxs_js_log("about to copy widget data to memory :)");
			nxs_js_log("placeholderid: " + placeholderid);
			
			// invoke ajax call
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": nxs_js_getclipboardhandler() + "copy",
						"clipboardcontext" : "widget",
						"postid": postid,
						"placeholderid": placeholderid
					},
					cache: false,
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							nxs_js_alert(response.growl);
						}
						else
						{
							nxs_js_alert(nxs_js_gettrans('Clipboard failed'));
						}
					},
					error: function(response)
					{
						nxs_js_popup_notifyservererror();
						nxs_js_log(response);
						nxs_js_alert(nxs_js_gettrans('Clipboard failed'));
					}										
				}
			);
		}
		
		function nxs_js_saverowtoclipboard(postid, rowindex)
		{
			nxs_js_log("about to copy row data to memory :)");
			nxs_js_log("postid: " + postid);
			nxs_js_log("rowindex: " + rowindex);
			
			var options = 
			{
				"waitgrowltext": "One moment ...",
				"happyflowgrowltext": "Copied row to clipboard",
				"webmethoddata": 
				{
					"webmethod": nxs_js_getclipboardhandler() + "copy",
					"clipboardcontext" : "row",
					"postid": postid,
					"rowindex": rowindex,
				}
			};
			nxs_js_invokewebmethod(options, null, null, null);
		}
		
		function nxs_copytoserverclipboard(clipboardscope)
		{
			if (nxs_js_popupshows)
			{
				return;
			}
		
			// currently we only support copy pasting of individual widgets (no rows yet...)
			var selectedhovermenus = jQ_nxs(".nxs-widget-hover-menu.nxs-hovering.inside-right-top");
			var hoverrows = jQ_nxs(".nxs-row-container > .nxs-hover-menu.nxs-hovering");		
			if (jQ_nxs(selectedhovermenus).length == 1)
			{
				var postid = nxs_js_findclosestpostid_for_dom(selectedhovermenus[0]);
				var selectedwidget = jQ_nxs(selectedhovermenus).closest(".nxs-placeholder").find(".nxs-widget");
				var placeholderid = jQ_nxs(selectedwidget).attr("id").split("-")[2];

				if (clipboardscope == "all")
				{
					nxs_js_saveplaceholdertoclipboard(postid, placeholderid);
				}
				else
				{
					nxs_js_log('unsupported clipboardscope; ' + clipboardscope);
				}
			}
			else if (jQ_nxs(hoverrows).length == 1)
			{
				nxs_js_log("copying while hovering over a row (not over a widget)");
				
				var postid = nxs_js_findclosestpostid_for_dom(hoverrows[0]);
				var rowindex = nxs_js_getrowindex(hoverrows[0]);

				if (clipboardscope == "all")
				{
					nxs_js_saverowtoclipboard(postid, rowindex);
				}
				else
				{
					nxs_js_log('unsupported clipboardscope; ' + clipboardscope);
				}
			}
			else 
			{
				// show popup for copying info
				if (jQ_nxs("body").hasClass("single") || jQ_nxs("body").hasClass("post") || jQ_nxs("body").hasClass("page"))
				{
					nxs_js_popup_site_neweditsession('clipboardcopyselector');
				}
				else
				{
					nxs_js_alert("Ctrl-c is not supported for this context");
				}
			}
		}
		
		function nxs_pastefromserverclipboard(scope)
		{
			if (nxs_js_popupshows)
			{
				return;
			}
		
			var selectedhovermenus = jQ_nxs(".nxs-widget-hover-menu.nxs-hovering.inside-right-top");
			var hoverrows = jQ_nxs(".nxs-row-container > .nxs-hover-menu.nxs-hovering");
			if (jQ_nxs(selectedhovermenus).length == 1)
			{
				// hovering over a widget
				var postid = nxs_js_findclosestpostid_for_dom(selectedhovermenus[0]);
				var selectedwidget = jQ_nxs(selectedhovermenus).closest(".nxs-placeholder").find(".nxs-widget");
				var placeholderid = jQ_nxs(selectedwidget).attr("id").split("-")[2];
				
				// invoke ajax call
				var ajaxurl = nxs_js_get_adminurladminajax();
				jQ_nxs.ajax
				(
					{
						type: 'POST',
						data: 
						{
							"action": "nxs_ajax_webmethods",
							"webmethod": nxs_js_getclipboardhandler() + "paste",
							"clipboardcontext" : "widget",
							"containerpostid": nxs_js_getcontainerpostid(),
							"postid": postid,
							"placeholderid": placeholderid,
							"clientpopupsessioncontext": nxs_js_getescaped_popupsession_context(),
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
								if (response.refresh == "row")
								{
									// update the DOM
									var rowindex = response.rowindex;
									var rowhtml = response.rowhtml;
									var pagecontainer = jQ_nxs(".nxs-layout-editable.nxs-post-" + postid)[0];
									var pagerowscontainer = jQ_nxs(pagecontainer).find(".nxs-postrows")[0];
									var element = jQ_nxs(pagerowscontainer).children()[rowindex];
									jQ_nxs(element).replaceWith(rowhtml);
									
									// update the GUI step 1
									// invoke execute_after_clientrefresh_XYZ for each widget in the affected first row, if present
									var container = jQ_nxs(pagerowscontainer).children()[rowindex];
									nxs_js_notify_widgets_after_ajaxrefresh(container);
									// update the GUI step 2
									nxs_js_reenable_all_window_events();
									
									// growl!
									nxs_js_alert(response.growl);
								}
								else
								{
									// assumed to have to refresh entire page
									
									nxs_js_refreshcurrentpage();
								}
							}
							else if (response.result == "ALTFLOW")
							{
								if (response.altflowid == "widgetnotallowed")
								{
									// inform used
									nxs_js_alert(response.msg);
								}
								else
								{
									nxs_js_popup_notifyservererror();
									nxs_js_log(response);
								}
							}
							else
							{
								nxs_js_alert(nxs_js_gettrans('Clipboard failed'));
							}
						},
						error: function(response)
						{
							nxs_js_popup_notifyservererror();
							nxs_js_log(response);
							nxs_js_alert(nxs_js_gettrans('Clipboard failed'));
						}										
					}
				);
			}
			else if (jQ_nxs(hoverrows).length == 1)
			{
				nxs_js_log("pasting while hovering over a row (not over a widget)");
				
				var postid = nxs_js_findclosestpostid_for_dom(hoverrows[0]);
				var rowindex = nxs_js_getrowindex(hoverrows[0]);
				var containerpostid = nxs_js_getcontainerpostid();
				
				function nxs_js_updaterow(response)
				{
					nxs_js_log("update row function invoked");
					
					var postid = response.postid;
					var rowindex = response.rowindex;
					var html = response.html;
					
					// update the row; the html is already provided in the response
					var pagecontainer = jQ_nxs(".nxs-post-" + postid)[0];
					var pagerowscontainer = jQ_nxs(pagecontainer).find(".nxs-postrows")[0];
					// afterwards update existing row
					var updateElement = jQ_nxs(pagerowscontainer).children()[rowindex];
	
					// before updating the dom, we first wipe any functions for notification ajax used by the "old" widgets in this row
					nxs_js_clear_ajaxrefresh_notifications(updateElement);
					
					//nxs_js_log("replacing index " + rowindex);
					jQ_nxs(updateElement).replaceWith(html);
					
					// after updating the dom, invoke execute_after_clientrefresh_XYZ for each widget in the affected first row, if present
					var updateElement = jQ_nxs(pagerowscontainer).children()[rowindex];
					nxs_js_notify_widgets_after_ajaxrefresh(updateElement);
					
					nxs_js_reenable_all_window_events();
				}
				
				var options = 
				{
					"waitgrowltext": "One moment ...",
					"happyflowgrowltext": "Pasted row from clipboard",
					"webmethoddata": 
					{
						"webmethod": nxs_js_getclipboardhandler() + "paste",
						"clipboardcontext" : "row",
						"containerpostid": containerpostid,
						"postid": postid,
						"rowindex": rowindex,
					}
				};
				nxs_js_invokewebmethod(options, nxs_js_updaterow, null, null);
				
			}
			else
			{
				// Pasten of elements other than a widget will be handled using a popup
				if (jQ_nxs("body").hasClass("single") || jQ_nxs("body").hasClass("post") || jQ_nxs("body").hasClass("page"))
				{
					nxs_js_popup_site_neweditsession('clipboardpasteselector');
				}
				else
				{
					nxs_js_alert("Ctrl-C is not supported for this context");
				}	
							
			}
		}
		
		function nxs_js_getwidgetdom_overwhichwehover()
		{
			var result = null;
		
			var selectedhovermenus = jQ_nxs(".nxs-widget-hover-menu.nxs-hovering.inside-right-top");
			if (jQ_nxs(selectedhovermenus).length == 1)
			{
				result = jQ_nxs(selectedhovermenus).closest(".nxs-placeholder").find(".nxs-widget")[0];			
			}
			else
			{
				// not found
			}
			
			return result;
		}
		
		function nxs_js_getplaceholderidofwidgetdom(widget)
		{
			var result = jQ_nxs(widget).attr("id").split("-")[2];
			return result;
		}
		
		// 
		// this isn't yet correctly implement for IE, see http://stackoverflow.com/questions/69430/is-there-a-way-to-make-text-unselectable-on-an-html-page
		// disable selecting with mouse
		(
			function($)
			{		
				jQuery.fn.disableSelection = function() 
				{
				  return this.each
				  (
					  function() 
					  {
			        jQ_nxs(this).addClass('nxs-unselectable');
					  }
				  );
				};
			}
		)
		(
			jQuery
		);
		
		//
		// this isn't yet correctly implement for IE, see http://stackoverflow.com/questions/69430/is-there-a-way-to-make-text-unselectable-on-an-html-page		
		// enable selecting with mouse
		(
			function($)
			{		
				jQuery.fn.enableSelection = function() 
				{
				  return this.each
				  (
					  function() 
					  {
			        jQ_nxs(this).removeClass('nxs-unselectable');
					  }
				  );
				};
			}
		)
		(
			jQuery
		);

		var nxs_js_facebookinjected = false;
		
		function nxs_js_inject_facebook()
		{
			if (nxs_js_facebookinjected)
			{
				// 
				nxs_js_fb_postprocessor();
				return;
			}
			nxs_js_facebookinjected = true;
			
			// kudos to http://cyrilmazur.com/2011/08/deferred-lazy-loading-facebook-widgets.html
			
			jQ_nxs('body').append('<div id="fb-root" style="display: none;"></div>');
			
			var locale = nxs_js_getlocale();
			if (locale == 'nl_NL')
			{
				// ok
			}
			else
			{
				locale = 'en_US';
			}
			var scripturl = '//connect.facebook.net/' + locale + '/all.js#xfbml=1';
			
			jQuery.getScript
			(
				scripturl, 
				function() 
				{
			    FB.init({status: true, cookie: true, xfbml: true}); 
			    // activate DOM
			    nxs_js_fb_postprocessor();
				}
			);
		}
		
		function nxs_js_fb_postprocessor()
		{
			nxs_js_log("fb post processor");

			if (typeof(FB) != 'undefined' && FB != null ) 
			{
				FB.XFBML.parse();
			}
			
			// tell the layout engine to post process the layout
			// after the DOM is updated
			nxs_gui_set_runtime_dimensions_enqueuerequest('nxs-framework-fbpostprocessor');
		}
		
		var nxs_js_googleplusinjected = false;
		
		function nxs_js_inject_googleplus()
		{
			if (nxs_js_googleplusinjected)
			{
				return;
			}
			nxs_js_googleplusinjected = true;
			script = document.createElement('script');
	    script.async = true;
	    script.src = '//apis.google.com/js/plusone.js';
	    document.body.appendChild(script);
		}
		
		//
		// generic page redirector for editing header, sidebar, footer, subheader, subfooter or pagelets
		//
		// for example : 
		// layouttype = "header"
		// pagesheetwhennotavailable = "headerhome"
		// 
		function nxs_js_editpagesection(postid, layouttype, pagesheetwhennotavailable)
		{
			var nxsrefurlspecial = nxs_js_get_nxsrefurlspecial();
			nxs_js_geturl(
				layouttype, 
				postid, 
				nxsrefurlspecial, 
				function(response) 
				{
					var url = response.url;
					nxs_js_log(url);
					nxs_js_redirect(url);
				},
				function()
				{
					// fout, kan komen als de layout niet gekoppeld is
					nxs_js_popup_pagetemplate_neweditsession(pagesheetwhennotavailable);
				}
			);
		}
		
		function nxs_js_nop()
		{
			// no operation
		}	
		
		function nxs_js_opensupportoptions()
		{
			nxs_js_popup_site_neweditsession('supportoptions');
		}
		
		function nxs_js_generic_ws(webmethod, inputparameters, invokewhenavailable)
		{			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": webmethod,
						"clientpopupsessioncontext": nxs_js_getescaped_popupsession_context(),
						"inputparameters": inputparameters
					},
					cache: false,
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							invokewhenavailable(response);
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
		
		// kudos to http://stackoverflow.com/questions/3431512/javascript-equivalent-to-phps-urldecode
		function nxs_js_urldecode(url)
		{
		  return decodeURIComponent(url.replace(/\+/g, ' '));
		}
		
		// encoding / base64
		
		function nxs_js_utf8_encode (argString) 
		{
			// http://kevin.vanzonneveld.net
			// +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
			// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			// +   improved by: sowberry
			// +    tweaked by: Jack
			// +   bugfixed by: Onno Marsman
			// +   improved by: Yves Sucaet
			// +   bugfixed by: Onno Marsman
			// +   bugfixed by: Ulrich
			// +   bugfixed by: Rafal Kukawski
			// *     example 1: nxs_js_utf8_encode('Kevin van Zonneveld');
			// *     returns 1: 'Kevin van Zonneveld'
			
			if (argString === null || typeof argString === "undefined") {
			return "";
			}
			
			var string = (argString + ''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
			var utftext = "",
			start, end, stringl = 0;
			
			start = end = 0;
			stringl = string.length;
			for (var n = 0; n < stringl; n++) {
			var c1 = string.charCodeAt(n);
			var enc = null;
			
			if (c1 < 128) {
			end++;
			} else if (c1 > 127 && c1 < 2048) {
			enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
			} else {
			enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
			}
			if (enc !== null) {
			if (end > start) {
			utftext += string.slice(start, end);
			}
			utftext += enc;
			start = end = n + 1;
			}
			}
			
			if (end > start) {
			utftext += string.slice(start, stringl);
			}
			
			return utftext;
		}
			
		function nxs_js_base64_encode(data) 
		{
			// http://kevin.vanzonneveld.net
			// +   original by: Tyler Akins (http://rumkin.com)
			// +   improved by: Bayron Guevara
			// +   improved by: Thunder.m
			// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			// +   bugfixed by: Pellentesque Malesuada
			// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			// -    depends on: nxs_js_utf8_encode
			// *     example 1: base64_encode('Kevin van Zonneveld');
			// *     returns 1: 'S2V2aW4gdmFuIFpvbm5ldmVsZA=='
			// mozilla has this native
			// - but breaks in 2.0.0.12!
			//if (typeof this.window['atob'] == 'function') {
			//    return atob(data);
			//}
			var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
			var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
			ac = 0,
			enc = "",
			tmp_arr = [];
			
			if (!data) {
			return data;
			}
			
			data = this.nxs_js_utf8_encode(data + '');
			
			do { // pack three octets into four hexets
			o1 = data.charCodeAt(i++);
			o2 = data.charCodeAt(i++);
			o3 = data.charCodeAt(i++);
			
			bits = o1 << 16 | o2 << 8 | o3;
			
			h1 = bits >> 18 & 0x3f;
			h2 = bits >> 12 & 0x3f;
			h3 = bits >> 6 & 0x3f;
			h4 = bits & 0x3f;
			
			// use hexets to index into b64, and append result to encoded string
			tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
			} while (i < data.length);
			
			enc = tmp_arr.join('');
			
			switch (data.length % 3) {
			case 1:
			enc = enc.slice(0, -2) + '==';
			break;
			case 2:
			enc = enc.slice(0, -1) + '=';
			break;
			}
			
			return enc;
		}
		
		//
		
		// ------------------------------------------------------------------------------------
		// seo functions
		
		function nxs_js_refresh_seoanalysis()
		{
			//nxs_js_log("seo:");
			//nxs_js_log(jQ_nxs('#tabs-seo'));
			if (jQ_nxs('#tabs-seo #nxsseoanalysisoutput').length > 0)
			{
				nxs_js_refresh_seoanalysis_enqueuerequest();
			}
			else
			{
				// SEO is disabled
			}
		}
		
		function nxs_js_refresh_seoanalysis_enqueuerequest(trigger)
		{
			// mark as triggered
			if (nxs_js_isseorefreshqueued == true)
			{
				// performance boost; we gaan hier niet nogmaals
				// alle berekeningen doorvoeren; er is reeds een ververs
				// request ingepland
				// nxs_js_log('skipping refresh, already queued');
				return;
			}
			else
			{
				// nxs_js_log('nothing in queue yet, enqueueing refresh request');
				// enqueue!
				nxs_js_isseorefreshqueued = true;

				var nxs_max_refresh_frequency_in_msecs = 100;	// lager betekent meer overhead, maar "snappier" gedrag
				setTimeout
				(
					function() 
					{
						//nxs_js_log('executing actual refresh work');
						
						// first we dequeue! 
						nxs_js_isseorefreshqueued = false; 
						nxs_js_refresh_seoanalysis_actualrequest(); 
					},nxs_max_refresh_frequency_in_msecs
				);
			}
		}
		
		function nxs_js_refresh_seoanalysis_actualrequest()
		{
			jQ_nxs('#nxs-seofields').show();

			// refresh yoast v3
			if ("undefined" !== typeof YoastSEO )
			{
				//console.log("refreshing Yoast SEO v3!");
				YoastSEO.app.refresh();
			}

			return;
		
			var postid = nxs_js_getcontainerpostid();
		
			var inputparameters = { "postid" : postid };
		
			nxs_js_generic_ws
			(
				"getseoanalysis",
				inputparameters,
				function(responseparameters)
				{
					// process

					var outputhtml = '';
					
					//
					jQ_nxs('#nxs-seofocuskeyword').val(responseparameters.focuskw);
					jQ_nxs('#nxs-seotitle').val(responseparameters.title);
					jQ_nxs('#nxs-seometadescription').val(responseparameters.metadesc);
					
					// update title of current browser window too
					document.title = responseparameters.title;

					var wperrors = responseparameters.wperrors;
					
					if (wperrors != null)
					{
						var numerrors = wperrors.length;
						for (var i = 0; i < numerrors; i++)
						{
						 	var currenterror = wperrors[i];
						
							nxs_js_log("current error:");
							nxs_js_log(currenterror);
							
							outputhtml += currenterror;
							//nxs_js_alert(currenterror);
						}
						// display output
						jQ_nxs('#nxs-seo-output').html(outputhtml);	
						
						// update height
						nxs_js_refreshtopmenufillerheight();
						
						// update counters
						nxs_js_shownumofchars('#nxs-seotitle', '#seotitlecharsused'); 
						nxs_js_shownumofchars('#nxs-seometadescription', '#seodescriptioncharsused'); 
						
						// cancel further processing
						return;
					}
					
					var calculatedresults = responseparameters.calculatedresults;
					nxs_js_log(calculatedresults);
					if (calculatedresults == null)
					{
						nxs_js_alert('No results found?!');
					}
					
					//
					//
					
					outputhtml += "<ul class='nxs-seoreport'>";
					
					for(var currentcalculatedresult in calculatedresults)
					{
						if (!calculatedresults.hasOwnProperty(currentcalculatedresult)) 
						{
							continue;
						}
						
					  var message = "";
					  message += "<li class='nxs-seoindicator-" + calculatedresults[currentcalculatedresult].indicator + "'>";
					  message += calculatedresults[currentcalculatedresult].msg;
						message += "</li>";
						  
						if (calculatedresults[currentcalculatedresult].msg != null)
						{
						  outputhtml += message;
						}
					}
					
					outputhtml += "</ul>";
					
					// display output
					jQ_nxs('#nxs-seo-output').html(outputhtml);	

					// display snippet
					//nxs_js_log("snippet:");
					var snippet = responseparameters.snippet;
					//nxs_js_log(snippet);
					jQ_nxs('#nxs-seo-snippetwrapper').html(snippet);
					//nxs_js_log('snippet wrapped updated');

					// update counters
					nxs_js_shownumofchars('#nxs-seotitle', '#seotitlecharsused'); 
					nxs_js_log('numchars');
					nxs_js_shownumofchars('#nxs-seometadescription', '#seodescriptioncharsused'); 
					nxs_js_log('numchars');

					try 
					{
						// highlight focus keywordwords
						var words = responseparameters.focuskw;
						// highlighting is performed on a per-word basis
						var wordpieces = words.split(' ');
						for (var i = 0; i < wordpieces.length; i++) 
						{
							var word = wordpieces[i];
							if (word != '')
							{
								nxs_js_log('highlighting..[' + word + ']');
					    	nxs_js_highlightword(document.getElementById('nxs-seo-snippetwrapper'), word);
					    }
					    else
					    {
					    	// ignore (otherwise a browser lockup occurs...)
					    }
						}
					} 
					catch (err)
					{
						// absorb (for example, failed in < ie9
						nxs_js_log(err);
					}
					
					nxs_js_log('updating height');
					
					// update height top menu
					nxs_js_refreshtopmenufillerheight();					
				}
			);
		}
		
		function nxs_js_update_seooption(postid, key, val, success)
		{
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updateseooption",
						"postid": postid,
						"key": key,
						"val": val
					},
					async: false,
					cache: false,
					dataType: 'JSON',
					url: ajaxurl,
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							success(response);
							// ok
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
		
		function nxs_js_update_seoall()
		{
			nxs_js_update_seofocuskeyword();
			nxs_js_update_seotitle();
			nxs_js_update_seometadescription();
						
			nxs_js_refresh_seoanalysis();
			
			// turn off update button
			jQ_nxs('#nxs-seofield-updatebutton').hide();
		}
		
		function nxs_js_update_seofocuskeyword()
		{
			var postid = nxs_js_getcontainerpostid();
			var val = jQ_nxs('#nxs-seofocuskeyword').val();
			if (val != null && val.length > 0)
			{
				var key = 'focuskw';
				nxs_js_update_seooption
				(
					postid, 
					key, 
					val, 
					function()
					{
						//nxs_js_alert('update successful');
					}
				);
			}
			else
			{
				nxs_js_alert('focuskw not updated; empty');
			}
		}
		
		function nxs_js_update_seotitle()
		{
			var postid = nxs_js_getcontainerpostid();
			var val = jQ_nxs('#nxs-seotitle').val();
			if (val != null && val.length > 0)
			{
				var key = 'title';
				nxs_js_update_seooption
				(
					postid, 
					key, 
					val, 
					function()
					{
						//nxs_js_alert('update successful');
					}
				);
			}
			else
			{
				nxs_js_alert('title not updated; empty');
			}

		}

		function nxs_js_update_seometadescription()
		{
			var postid = nxs_js_getcontainerpostid();
			var val = jQ_nxs('#nxs-seometadescription').val();
			if (val != null && val.length > 0)
			{
				var key = 'metadesc';
				nxs_js_update_seooption
				(
					postid, 
					key, 
					val, 
					function()
					{
						//nxs_js_alert('update successful');
					}
				);
			}
			else
			{
				nxs_js_alert('meta desc not updated; empty');
			}
		}
		
		function nxs_js_shownumofchars(inputfield, outputfield)
		{
			if (jQ_nxs(inputfield) == null)
			{
				nxs_js_log("inputfield not found:" + inputfield);
				return;
			}
			if (jQ_nxs(outputfield) == null)
			{
				nxs_js_log("outputfield not found:" + outputfield);
				return;
			}
			var aantal = jQ_nxs(inputfield).val().length;
			jQ_nxs(outputfield).html(aantal);
		}
		
		// kudos to http://stackoverflow.com/questions/10729983/highlight-word-in-html-text-but-not-markup
		function nxs_js_highlightword(root,word){
		  nxs_js_textnodesunder(root).forEach(nxs_js_highlightwords);
		
		  function nxs_js_textnodesunder(root){
		    var n,a=[],w=document.createTreeWalker(root,NodeFilter.SHOW_TEXT,null,false);
		    while(n=w.nextNode()) a.push(n);
		    return a;
		  }
		
		  function nxs_js_highlightwords(n){
		  	// ignores casing
		    for (var i; (i=getnormalizedcomparable(n.nodeValue).indexOf(getnormalizedcomparable(word),i)) > -1; n=after){
		      var after = n.splitText(i+word.length);
		      var highlighted = n.splitText(i);
		      var span = document.createElement('span');
		      span.className = 'nxs-highlighted';
		      span.appendChild(highlighted);
		      after.parentNode.insertBefore(span,after);
		    }
		  }
		}
		
		// kudos to http://lehelk.com/2011/05/06/script-to-remove-diacritics/		
		var defaultDiacriticsRemovalMap = [
    {'base':'A', 'letters':/[\u0041\u24B6\uFF21\u00C0\u00C1\u00C2\u1EA6\u1EA4\u1EAA\u1EA8\u00C3\u0100\u0102\u1EB0\u1EAE\u1EB4\u1EB2\u0226\u01E0\u00C4\u01DE\u1EA2\u00C5\u01FA\u01CD\u0200\u0202\u1EA0\u1EAC\u1EB6\u1E00\u0104\u023A\u2C6F]/g},
    {'base':'AA','letters':/[\uA732]/g},
    {'base':'AE','letters':/[\u00C6\u01FC\u01E2]/g},
    {'base':'AO','letters':/[\uA734]/g},
    {'base':'AU','letters':/[\uA736]/g},
    {'base':'AV','letters':/[\uA738\uA73A]/g},
    {'base':'AY','letters':/[\uA73C]/g},
    {'base':'B', 'letters':/[\u0042\u24B7\uFF22\u1E02\u1E04\u1E06\u0243\u0182\u0181]/g},
    {'base':'C', 'letters':/[\u0043\u24B8\uFF23\u0106\u0108\u010A\u010C\u00C7\u1E08\u0187\u023B\uA73E]/g},
    {'base':'D', 'letters':/[\u0044\u24B9\uFF24\u1E0A\u010E\u1E0C\u1E10\u1E12\u1E0E\u0110\u018B\u018A\u0189\uA779]/g},
    {'base':'DZ','letters':/[\u01F1\u01C4]/g},
    {'base':'Dz','letters':/[\u01F2\u01C5]/g},
    {'base':'E', 'letters':/[\u0045\u24BA\uFF25\u00C8\u00C9\u00CA\u1EC0\u1EBE\u1EC4\u1EC2\u1EBC\u0112\u1E14\u1E16\u0114\u0116\u00CB\u1EBA\u011A\u0204\u0206\u1EB8\u1EC6\u0228\u1E1C\u0118\u1E18\u1E1A\u0190\u018E]/g},
    {'base':'F', 'letters':/[\u0046\u24BB\uFF26\u1E1E\u0191\uA77B]/g},
    {'base':'G', 'letters':/[\u0047\u24BC\uFF27\u01F4\u011C\u1E20\u011E\u0120\u01E6\u0122\u01E4\u0193\uA7A0\uA77D\uA77E]/g},
    {'base':'H', 'letters':/[\u0048\u24BD\uFF28\u0124\u1E22\u1E26\u021E\u1E24\u1E28\u1E2A\u0126\u2C67\u2C75\uA78D]/g},
    {'base':'I', 'letters':/[\u0049\u24BE\uFF29\u00CC\u00CD\u00CE\u0128\u012A\u012C\u0130\u00CF\u1E2E\u1EC8\u01CF\u0208\u020A\u1ECA\u012E\u1E2C\u0197]/g},
    {'base':'J', 'letters':/[\u004A\u24BF\uFF2A\u0134\u0248]/g},
    {'base':'K', 'letters':/[\u004B\u24C0\uFF2B\u1E30\u01E8\u1E32\u0136\u1E34\u0198\u2C69\uA740\uA742\uA744\uA7A2]/g},
    {'base':'L', 'letters':/[\u004C\u24C1\uFF2C\u013F\u0139\u013D\u1E36\u1E38\u013B\u1E3C\u1E3A\u0141\u023D\u2C62\u2C60\uA748\uA746\uA780]/g},
    {'base':'LJ','letters':/[\u01C7]/g},
    {'base':'Lj','letters':/[\u01C8]/g},
    {'base':'M', 'letters':/[\u004D\u24C2\uFF2D\u1E3E\u1E40\u1E42\u2C6E\u019C]/g},
    {'base':'N', 'letters':/[\u004E\u24C3\uFF2E\u01F8\u0143\u00D1\u1E44\u0147\u1E46\u0145\u1E4A\u1E48\u0220\u019D\uA790\uA7A4]/g},
    {'base':'NJ','letters':/[\u01CA]/g},
    {'base':'Nj','letters':/[\u01CB]/g},
    {'base':'O', 'letters':/[\u004F\u24C4\uFF2F\u00D2\u00D3\u00D4\u1ED2\u1ED0\u1ED6\u1ED4\u00D5\u1E4C\u022C\u1E4E\u014C\u1E50\u1E52\u014E\u022E\u0230\u00D6\u022A\u1ECE\u0150\u01D1\u020C\u020E\u01A0\u1EDC\u1EDA\u1EE0\u1EDE\u1EE2\u1ECC\u1ED8\u01EA\u01EC\u00D8\u01FE\u0186\u019F\uA74A\uA74C]/g},
    {'base':'OI','letters':/[\u01A2]/g},
    {'base':'OO','letters':/[\uA74E]/g},
    {'base':'OU','letters':/[\u0222]/g},
    {'base':'P', 'letters':/[\u0050\u24C5\uFF30\u1E54\u1E56\u01A4\u2C63\uA750\uA752\uA754]/g},
    {'base':'Q', 'letters':/[\u0051\u24C6\uFF31\uA756\uA758\u024A]/g},
    {'base':'R', 'letters':/[\u0052\u24C7\uFF32\u0154\u1E58\u0158\u0210\u0212\u1E5A\u1E5C\u0156\u1E5E\u024C\u2C64\uA75A\uA7A6\uA782]/g},
    {'base':'S', 'letters':/[\u0053\u24C8\uFF33\u1E9E\u015A\u1E64\u015C\u1E60\u0160\u1E66\u1E62\u1E68\u0218\u015E\u2C7E\uA7A8\uA784]/g},
    {'base':'T', 'letters':/[\u0054\u24C9\uFF34\u1E6A\u0164\u1E6C\u021A\u0162\u1E70\u1E6E\u0166\u01AC\u01AE\u023E\uA786]/g},
    {'base':'TZ','letters':/[\uA728]/g},
    {'base':'U', 'letters':/[\u0055\u24CA\uFF35\u00D9\u00DA\u00DB\u0168\u1E78\u016A\u1E7A\u016C\u00DC\u01DB\u01D7\u01D5\u01D9\u1EE6\u016E\u0170\u01D3\u0214\u0216\u01AF\u1EEA\u1EE8\u1EEE\u1EEC\u1EF0\u1EE4\u1E72\u0172\u1E76\u1E74\u0244]/g},
    {'base':'V', 'letters':/[\u0056\u24CB\uFF36\u1E7C\u1E7E\u01B2\uA75E\u0245]/g},
    {'base':'VY','letters':/[\uA760]/g},
    {'base':'W', 'letters':/[\u0057\u24CC\uFF37\u1E80\u1E82\u0174\u1E86\u1E84\u1E88\u2C72]/g},
    {'base':'X', 'letters':/[\u0058\u24CD\uFF38\u1E8A\u1E8C]/g},
    {'base':'Y', 'letters':/[\u0059\u24CE\uFF39\u1EF2\u00DD\u0176\u1EF8\u0232\u1E8E\u0178\u1EF6\u1EF4\u01B3\u024E\u1EFE]/g},
    {'base':'Z', 'letters':/[\u005A\u24CF\uFF3A\u0179\u1E90\u017B\u017D\u1E92\u1E94\u01B5\u0224\u2C7F\u2C6B\uA762]/g},
    {'base':'a', 'letters':/[\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250]/g},
    {'base':'aa','letters':/[\uA733]/g},
    {'base':'ae','letters':/[\u00E6\u01FD\u01E3]/g},
    {'base':'ao','letters':/[\uA735]/g},
    {'base':'au','letters':/[\uA737]/g},
    {'base':'av','letters':/[\uA739\uA73B]/g},
    {'base':'ay','letters':/[\uA73D]/g},
    {'base':'b', 'letters':/[\u0062\u24D1\uFF42\u1E03\u1E05\u1E07\u0180\u0183\u0253]/g},
    {'base':'c', 'letters':/[\u0063\u24D2\uFF43\u0107\u0109\u010B\u010D\u00E7\u1E09\u0188\u023C\uA73F\u2184]/g},
    {'base':'d', 'letters':/[\u0064\u24D3\uFF44\u1E0B\u010F\u1E0D\u1E11\u1E13\u1E0F\u0111\u018C\u0256\u0257\uA77A]/g},
    {'base':'dz','letters':/[\u01F3\u01C6]/g},
    {'base':'e', 'letters':/[\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD]/g},
    {'base':'f', 'letters':/[\u0066\u24D5\uFF46\u1E1F\u0192\uA77C]/g},
    {'base':'g', 'letters':/[\u0067\u24D6\uFF47\u01F5\u011D\u1E21\u011F\u0121\u01E7\u0123\u01E5\u0260\uA7A1\u1D79\uA77F]/g},
    {'base':'h', 'letters':/[\u0068\u24D7\uFF48\u0125\u1E23\u1E27\u021F\u1E25\u1E29\u1E2B\u1E96\u0127\u2C68\u2C76\u0265]/g},
    {'base':'hv','letters':/[\u0195]/g},
    {'base':'i', 'letters':/[\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131]/g},
    {'base':'j', 'letters':/[\u006A\u24D9\uFF4A\u0135\u01F0\u0249]/g},
    {'base':'k', 'letters':/[\u006B\u24DA\uFF4B\u1E31\u01E9\u1E33\u0137\u1E35\u0199\u2C6A\uA741\uA743\uA745\uA7A3]/g},
    {'base':'l', 'letters':/[\u006C\u24DB\uFF4C\u0140\u013A\u013E\u1E37\u1E39\u013C\u1E3D\u1E3B\u017F\u0142\u019A\u026B\u2C61\uA749\uA781\uA747]/g},
    {'base':'lj','letters':/[\u01C9]/g},
    {'base':'m', 'letters':/[\u006D\u24DC\uFF4D\u1E3F\u1E41\u1E43\u0271\u026F]/g},
    {'base':'n', 'letters':/[\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5]/g},
    {'base':'nj','letters':/[\u01CC]/g},
    {'base':'o', 'letters':/[\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275]/g},
    {'base':'oi','letters':/[\u01A3]/g},
    {'base':'ou','letters':/[\u0223]/g},
    {'base':'oo','letters':/[\uA74F]/g},
    {'base':'p','letters':/[\u0070\u24DF\uFF50\u1E55\u1E57\u01A5\u1D7D\uA751\uA753\uA755]/g},
    {'base':'q','letters':/[\u0071\u24E0\uFF51\u024B\uA757\uA759]/g},
    {'base':'r','letters':/[\u0072\u24E1\uFF52\u0155\u1E59\u0159\u0211\u0213\u1E5B\u1E5D\u0157\u1E5F\u024D\u027D\uA75B\uA7A7\uA783]/g},
    {'base':'s','letters':/[\u0073\u24E2\uFF53\u00DF\u015B\u1E65\u015D\u1E61\u0161\u1E67\u1E63\u1E69\u0219\u015F\u023F\uA7A9\uA785\u1E9B]/g},
    {'base':'t','letters':/[\u0074\u24E3\uFF54\u1E6B\u1E97\u0165\u1E6D\u021B\u0163\u1E71\u1E6F\u0167\u01AD\u0288\u2C66\uA787]/g},
    {'base':'tz','letters':/[\uA729]/g},
    {'base':'u','letters':/[\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289]/g},
    {'base':'v','letters':/[\u0076\u24E5\uFF56\u1E7D\u1E7F\u028B\uA75F\u028C]/g},
    {'base':'vy','letters':/[\uA761]/g},
    {'base':'w','letters':/[\u0077\u24E6\uFF57\u1E81\u1E83\u0175\u1E87\u1E85\u1E98\u1E89\u2C73]/g},
    {'base':'x','letters':/[\u0078\u24E7\uFF58\u1E8B\u1E8D]/g},
    {'base':'y','letters':/[\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF]/g},
    {'base':'z','letters':/[\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763]/g}
		];
		function nxs_js_removediacritics(str) {
		    var changes = defaultDiacriticsRemovalMap;
		    for(var i=0; i<changes.length; i++) 
		    {
		      str = str.replace(changes[i].letters, changes[i].base);
		    }
		    return str;
		}
		
		function getnormalizedcomparable(str)
		{
			return nxs_js_removediacritics(str).toLowerCase();
		}
		
		/*
		-------------------------------------------------------
		------------------------------------------------------- 
		js color handling
		kudos to http://mjijackson.com/2008/02/rgb-to-hsl-and-rgb-to-hsv-color-model-conversion-algorithms-in-javascript 
		-------------------------------------------------------
		-------------------------------------------------------
		*/
		
		function nxs_js_rgbtohsl(rgb)
		{
			if (rgb == null)
			{
				return null;
			}
		var r = rgb.r;
		var g = rgb.g;
		var b = rgb.b;
		
    r /= 255, g /= 255, b /= 255;
    var max = Math.max(r, g, b), min = Math.min(r, g, b);
    var h, s, l = (max + min) / 2;

    if(max == min){
        h = s = 0; // achromatic
    }else{
        var d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch(max){
            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
            case g: h = (b - r) / d + 2; break;
            case b: h = (r - g) / d + 4; break;
        }
        h /= 6;
    }
    
    var result = {
    h: h,
    s: s,
    l: l
    }

    return result;
}

function nxs_js_adjustlightnessforhsl(hsl, lightnessdelta)
{
	if (hsl == null)
	{
	return null;
	}
	//nxs_js_log(hsl);

	var result = 
	{
		h : hsl.h,
		s : hsl.s,
		l : hsl.l + lightnessdelta
	}
	
	if (result.l < 0)
	{
		result.l = 0;
	}
	else if (result.l > 1)
	{
		result.l = 1;
	}
	
	return result; 
}

function nxs_js_gethextransformedrgblight(rgb, delta)
{
	var hsl = nxs_js_rgbtohsl(rgb);
	var adjustedhsl = nxs_js_adjustlightnessforhsl(hsl, delta);
	var adjustedrgb = nxs_js_hsltorgb(adjustedhsl);
	var adjustedhex = nxs_js_rgbtohex(adjustedrgb);
	return adjustedhex;
}

// within the given hue, select the reverse s and l
function nxs_js_getoppositesaturationandlightforhsl(hsl)
{
	nxs_js_log("before hsl:");
	nxs_js_log(hsl);
	
	var result = 
	{
		h : hsl.h,
		s : 1 - hsl.s,
		l : 1 - hsl.l
	}
	
	nxs_js_log("after hsl:");
	nxs_js_log(result);
	
	return result;
}

function nxs_js_getcomplementaryhsl(hsl)
{
	var result = 
	{
		h : (0.5 + hsl.h) % 1,
		s : hsl.s,
		l : hsl.l
	}
	return result;
}

function nxs_js_hslinbounds(hsl)
{
	// h
	if (hsl.h < 0)
	{
		hsl.h += 1;
	}
	else if (hsl.h > 1)
	{
		hsl.h -= 1;
	}
	// s
	if (hsl.s < 0)
	{
		hsl.s += 1;
	}
	else if (hsl.s > 1)
	{
		hsl.s -= 1;
	}
	// l
	if (hsl.l < 0)
	{
		hsl.l += 1;
	}
	else if (hsl.l > 1)
	{
		hsl.l -= 1;
	}
	return hsl;
}

function nxs_js_getcomplementaryhsl(hsl)
{
	var result = [];
	var item;
	
	item =  
	{
		h : hsl.h,
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);
	
	item =  
	{
		h : hsl.h + (1/2),
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);
		
	return result;
}

function nxs_js_getsplitcomplementaryhsl(hsl)
{
	nxs_js_log("before:");
	nxs_js_log(hsl);

	var result = [];
	var item;
	
	item =  
	{
		h : hsl.h - (1/3),
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);
	
	//nxs_js_log("first:");
	//nxs_js_log(item);
	
	item =  
	{
		h : hsl.h,
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);
	
	//nxs_js_log("second:");
	//nxs_js_log(item);
	
	item =  
	{
		h : hsl.h + (1/3),
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);

	//nxs_js_log("third1:");
	//nxs_js_log(item);
		
	return result;
}

function nxs_js_getmonohsl(hsl)
{
	var result = [];
	var item;
	
	item =  
	{
		h : hsl.h,
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);
	
	return result;
}

function nxs_js_gettriadbyanglehsl(hsl, angle)
{
	nxs_js_log("before:");
	nxs_js_log(hsl);

	var result = [];
	var item;
	
	item =  
	{
		h : hsl.h + 0.5 - (angle/360),
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);
	
	//nxs_js_log("first:");
	//nxs_js_log(item);
	
	item =  
	{
		h : hsl.h,
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);
	
	//nxs_js_log("second:");
	//nxs_js_log(item);
	
	item =  
	{
		h : hsl.h + 0.5 + (angle/360),
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);

	//nxs_js_log("third1:");
	//nxs_js_log(item);
		
	return result;
}

function nxs_js_getanalogicbyanglehsl(hsl, angle)
{
	nxs_js_log("before:");
	nxs_js_log(hsl);

	var result = [];
	var item;
	
	item =  
	{
		h : hsl.h - (angle/360),
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);
	
	//nxs_js_log("first:");
	//nxs_js_log(item);
	
	item =  
	{
		h : hsl.h,
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);
	
	//nxs_js_log("second:");
	//nxs_js_log(item);
	
	item =  
	{
		h : hsl.h + (angle/360),
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);

	//nxs_js_log("third1:");
	//nxs_js_log(item);
		
	return result;
}


function nxs_js_getaccentedanalogicbyanglehsl(hsl, angle)
{
	var result = [];
	var item;
	
	item =  
	{
		h : hsl.h - (angle/360),
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);
	
	item =  
	{
		h : hsl.h,
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);
	
	item =  
	{
		h : hsl.h + (angle/360),
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);

	item =  
	{
		h : hsl.h + 0.5,
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);
		
	return result;
}

function nxs_js_gettetradbyanglehsl(hsl, angle)
{
	var result = [];
	var item;
	
	item =  
	{
		h : hsl.h,
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);

	item =  
	{
		h : hsl.h + (angle/360),
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);
	
	item =  
	{
		h : hsl.h + 0.5,
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);

	item =  
	{
		h : hsl.h + 0.5 + (angle/360),
		s : hsl.s,
		l : hsl.l
	}
	item = nxs_js_hslinbounds(item);
	result.push(item);

		
	return result;
}


/**
 * Converts an HSL color value to RGB. Conversion formula
 * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
 * Assumes h, s, and l are contained in the set [0, 1] and
 * returns r, g, and b in the set [0, 255].
 *
 * @param   Number  h       The hue
 * @param   Number  s       The saturation
 * @param   Number  l       The lightness
 * @return  Array           The RGB representation
 */
function nxs_js_hsltorgb(hsl)
{
	if (hsl == null)	
	{
		return null;
	}

	var h = hsl.h;
	var s = hsl.s;
	var l = hsl.l;
	
    var r, g, b;

    if(s == 0){
        r = g = b = l; // achromatic
    }else{
        function hue2rgb(p, q, t){
            if(t < 0) t += 1;
            if(t > 1) t -= 1;
            if(t < 1/6) return p + (q - p) * 6 * t;
            if(t < 1/2) return q;
            if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
            return p;
        }

        var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        var p = 2 * l - q;
        r = hue2rgb(p, q, h + 1/3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1/3);
    }

    var result = {
    r : Math.round(r * 255), 
    g: Math.round(g * 255), 
    b: Math.round(b * 255)
    };
    
    return result;
}

/*
kudos to http://stackoverflow.com/questions/5623838/rgb-to-hex-and-hex-to-rgb
*/
function nxs_js_hextorgb(hex) {
	if (hex == null || hex == '')
	{
		nxs_js_log("warning; hex;" + hex);
		return { r: 0, g: 0, b: 0 }
	}
    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
    hex = hex.replace(shorthandRegex, function(m, r, g, b) {
        return r + r + g + g + b + b;
    });

    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

/* kudos to http://stackoverflow.com/questions/5623838/rgb-to-hex-and-hex-to-rgb */

function nxs_js_componenttohex(c) {
    var hex = c.toString(16);
    return hex.length == 1 ? "0" + hex : hex;
}

function nxs_js_rgbtohex(rgb) 
{
	if (rgb == null)
	{
		return "#XXX";
	}

	var r = rgb.r;
	var g = rgb.g;
	var b = rgb.b;
  return "#" + nxs_js_componenttohex(r) + nxs_js_componenttohex(g) + nxs_js_componenttohex(b);
}

function nxs_js_hextohsl(hex)
{
	var rgb = nxs_js_hextorgb(hex);
	var hsl = nxs_js_rgbtohsl(rgb);
	return hsl;
}

function nxs_js_displayStyleSheetProperties()
{
    var str = "";
    if( !document.styleSheets )
    {
        str = "Your browser does not support the stylesheets object.";
    }
    else if ( !document.styleSheets.length )
    {
        str = "There were no stylesheets found in the document.styleSheets collection.";
    }
    else
    {
    	nxs_js_log("outputting sheets:");
    	var sheetindex;
    	for (sheetindex in document.styleSheets)
    	{
    		if (!document.styleSheets.hasOwnProperty(sheetindex)) 
				{
					continue;
				}

    		var sheet = document.styleSheets[sheetindex];
    		nxs_js_log(sheet);
    		 
    		nxs_js_log("HREF:" + sheet.href);
    		nxs_js_log("MEDIA:" + sheet.media);
    		nxs_js_log("OWNERNODE:");
    		nxs_js_log(sheet.ownerNode);
    	}
    }
    nxs_js_log(str);
}

// css is the stylesheet contents to add / inject
// the stylesheetcontainer is an object that references the object of for example <style id="myObject"></style>
function nxs_js_setcss(css, containerbaseid)
{
	//nxs_js_log("* setting css for " + containerbaseid);
	
	if(jQuery.browser.msie)
  {
		// great, thanks to MS we need multiple lame IE patches
		// first of all the injection itself is different...
		// see http://stackoverflow.com/questions/9050441/how-do-i-inject-styles-into-ie8
		// and second (even worse), IE 8 + 9 are only capable of handling 4095 selectors for
		// each css script. Thus, if the amount is too big, we need to split the css into
		// multiple parts and inject each

		// var countmsg = nxs_js_getcssselectorcount(css);
		
		var chunksresult = nxs_js_getchunksofcss(css);
		for (var i = 0; i < nxs_js_getmaxservercsschunks(); i++)
		{
			// get script for this chunk
			var stylesheetcontainer = jQ_nxs(containerbaseid + i);
			var stylesheet = stylesheetcontainer.prop('styleSheet');
	  	if (stylesheet != null)
	  	{
	    	stylesheet.cssText=chunksresult.chunks[i];
	    }
	    else
	    {
	    	nxs_js_alert('failed to apply css!');
	    	nxs_js_log(stylesheet);
	    	nxs_js_log('likely the problem is that the number of stylesheets that can be applied in IE is limited to 32; did you exceed the number of allowed stylesheets?');
	    	nxs_js_displayStyleSheetProperties();
	    }
		}
  }
	else
	{
		// always store in containerbaseid 0 for non-ie browsers
		var i = 0;
		var stylesheetcontainer = jQ_nxs(containerbaseid + i);
		jQ_nxs(stylesheetcontainer).html(css);
	}
}

function nxs_js_getkeys(lookup)
{
	var result = new Array();
	for(var key in lookup)
	{
		if (!lookup.hasOwnProperty(key)) 
		{
			continue;
		}
		
	  result[result.length] = key;
	}	
	return result;
}

function nxs_js_sortbylength(a,b)
{
	var al = a.length;
	var bl = b.length;
	if (al == bl)
	{
		return 0;
	}
	else if (al < bl)
	{
		return 1;
	}
	else
	{
		return -1;
	}
}

function nxs_js_removelinebreaks(content)
{
	return content.replace(/(\r\n|\n|\r)/gm,"");
}

function nxs_js_removetabs(content)
{
	return content.replace(/\t/g, '');
}

// kudos to http://stackoverflow.com/questions/3286874/remove-all-multiple-spaces-in-javascript-and-replace-with-single-space
function nxs_js_replacemultiplespaceswithsinglespace(content)
{
	return content.replace(/ +(?= )/g,'');
}

function nxs_js_blendtemplatewithvariables(template, lookup)
{
	return nxs_js_blendtemplatewithvariables_v2(template, lookup, '$nxs_', '');
}

// mixed the colorscheme template with the dictionary
function nxs_js_blendtemplatewithvariables_v2(template, lookup, keyprefix, keysuffix)
{
	if (template == null) { nxs_js_log('template not set?'); }
	if (lookup == null) { nxs_js_log('lookup not set?'); }

	// "lineair" blending (note; this is not as sophisticated as SASS or LESS, but sufficient enough for now,
	// could be further enhanced for more complex replacements, like producing gradients

	var keys;
	keys = nxs_js_getkeys(lookup);
	keys.sort(nxs_js_sortbylength);
	
	// replace placeholders 	
	for (index = 0; index < keys.length; ++index)
	{
		var key = keys[index];
		var key2 = keyprefix + key + keysuffix;
		// note: the regexp "g" part is required to replace all instances instead of just the first
		var valuetouse = lookup[key];
		//nxs_js_log('replacing key:' + key2 + ' with value:' + valuetouse);
		template=template.replace(new RegExp('\\' + key2, "g"), valuetouse);
	}
	
	if (template.indexOf("$") >= 0)
	{
		// it looks like we missed something...
		nxs_js_log('Warning, blender detected $ placeholder not being replaced; is the syntax of the template correct? did you miss a lookup placeholder? (for example use $nxs_color_base2_m)');
		nxs_js_log('template:');
		nxs_js_log(template);
		nxs_js_log('lookup:');
		nxs_js_log(lookup);
	}
	else
	{
		/*
		nxs_js_log('Blender ok');
		nxs_js_log('template:');
		nxs_js_log(template);
		nxs_js_log('lookup:');
		nxs_js_log(lookup);
		*/
	}
	
	return template;
}

//
function nxs_js_updatecss_themecss_actualrequest(shouldusecacheifavailable, shouldupdatedom)
{
	
	var actualcss = null;
	var isdirty = false;
	var storagekey = nxs_js_get_adminurladminajax() + "nxs-dynamiccss-server-chunk";
	var localstorageavailable = Modernizr.localstorage;
	
	localstorageavailable = false; // overruled for now
	
	// store css
	if (shouldusecacheifavailable && localstorageavailable)
	{
		nxs_js_log("* Retrieving CSS from local storage, hopefull its there");
		actualcss = localStorage.getItem(storagekey);
	}
	
	if (actualcss == null)
	{
		// nxs_js_log("* Cached data is not available, (re)producing CSS the hard way");
		
		// get active colorscheme 
		var colorschemelookup = nxs_js_getruntimecolorschemelookup();
		var runtimeurllookup = nxs_js_getruntimecsslookup();
		
		// blend all lookups
		var csslookup = jQuery.extend({}, colorschemelookup, runtimeurllookup);
	
		// the framework css will not be blended with lookups;
		// its required to contain end-css (if lookups
		// need to be translated, use the specifiekd csslookup parameter)
		var frameworkcss = nxs_js_get_frameworkcsstemplate(csslookup);
		var customcss = nxs_js_get_customcsstemplate(csslookup);
	
		// blend the customcss (note! we intentionally only blend the 
		// customcss, not the framework's css, this is done to keep performance high)
		var blendedcustomcss = nxs_js_blendtemplatewithvariables(customcss, csslookup);
	
		// we concatenate the end result; framework first, customcss is able 
		// to override styling as needed
		var actualcss = frameworkcss + blendedcustomcss;
		
		isdirty = true;
	}
	
	// store css
	if (isdirty && localstorageavailable)
	{
		nxs_js_log("* Updating CSS cache");
		try
		{
			localStorage.setItem(storagekey, actualcss);
		}
		catch(err)
		{
			// most likely reason; if a multisite
			// hosts multiple sites using sub folders,
			// the localstorage is shared with all sites,
			// if users accesses many sites, eventually will run
			// out of local storage, in that case we clear the 
			// localstorage to wipe cache of "other sites" too,
			// next time hopefully there will be enough storage left
			nxs_js_log("* Error storing cached CSS");
			localStorage.clear();
		}
	}
	else
	{
		// nxs_js_log("* Leaving CSS cache as is (no update) because localstorage is n/a or because CSS is not dirty");
	}
	
	if (shouldupdatedom)
	{
		// update the css in the DOM
		nxs_js_setcss(actualcss, '#nxs-dynamiccss-server-chunk-');
		
		// if the dom was updated, and the cache was available and used, we will trigger another call
		// to update the cache in certain amount of msecs from now, based on the actual data (non-cached)
		// this way we have best of both worlds; an up to date cache, and a fast performing site :)
		if (localstorageavailable && shouldusecacheifavailable)
		{
			nxs_js_log("* CSS cache will be updated in 5000 msecs from now on");
			setTimeout(function() { nxs_js_log("* Waking up to update css cache"); nxs_js_updatecss_themecss_actualrequest(false, false); nxs_js_log("* Finished waking up (cache should now be up to date)"); }, 5000);
		}
	}
}

// helper function to get the css output for a lineair gradient between the 2 specified colors
function nxs_js_getlineairgradientcss(colora, colorb)
{
	var result = "";
	
	var vendor = nxs_js_getvendorprefix();
	
	result += "background-color: " + colora + ";";
	result += "fill: " + colora + ";";
	
	if (vendor == "o" || vendor == "?")
	{
		result += "background: -o-linear-gradient(" + colorb + ", " + colora + ");";
	}
	else if (vendor == "mozilla" || vendor == "?")
	{
		result += "background: -moz-linear-gradient(" + colorb + ", " + colora + ");";
	}
	else if (vendor == "webkit" || vendor == "?")
	{
		result += "background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(" + colorb + "), to(" + colora + "));";
	}
	else if (vendor == "msie" || vendor == "?")
	{

		result += "background: -ms-linear-gradient(" + colorb + ", " + colora + ");" + "filter: progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=" + colorb + ",EndColorStr=" + colora + ");";
	}
	return result;
}

// kudos to http://stackoverflow.com/questions/11333150/twitter-bootstrap-how-to-remove-gradient-mixin-in-subclass
function nxs_js_getflatbackgroundnogradientcss(hexcolor, alphafactor)
{
	var result = "";
	
	// NOTE the background-image: none is repeated intentionally, see link above function declaration
	
	var vendor = nxs_js_getvendorprefix();
		
	if (alphafactor == 1)
	{
		result += "background-color: " + hexcolor + ";";
		result += "fill: " + hexcolor + ";";
	}
	else if (alphafactor < 1)
	{
		// convert hex value to rgb
		var hexcolor = nxs_js_hextorgb(hexcolor);
		
		result += "background: rgb(" + hexcolor.r + ", " + hexcolor.g + ", " + hexcolor.b + "); /* The Fallback */ ";
		result += "background: rgba(" + hexcolor.r + ", " + hexcolor.g + ", " + hexcolor.b + ", " + alphafactor + ");";

		result += "fill: rgb(" + hexcolor.r + ", " + hexcolor.g + ", " + hexcolor.b + "); /* The Fallback */ ";
		result += "fill: rgba(" + hexcolor.r + ", " + hexcolor.g + ", " + hexcolor.b + ", " + alphafactor + ");";

		//nxs_js_log("hexcolor:" + hexcolor + "; alpha:" + alphafactor);   	
		//nxs_js_log("output:" + result);   	
	}
		
	result += "background-repeat: no-repeat;";
	
	if (vendor == "o" || vendor == "?")
	{
		result += "background-image: none;";
	}
	else if (vendor == "mozilla" || vendor == "?")
	{
		result += "background-image: none;";
	}
	else if (vendor == "webkit" || vendor == "?")
	{
		result += "background-image: none;";
		result += "background-image: none;";
	}
	else if (vendor == "msie" || vendor == "?")
	{
		result += "filter: none;";
	}
	
	// debug
	// result = "background-color: red !important;";
	
	return result;
}

// helper function to get the css output for a lineair gradient between the 2 specified colors
function nxs_js_getflatcss(colora, alphafactor)
{
	return nxs_js_getflatbackgroundnogradientcss(colora, alphafactor);
}

function nxs_js_updatecss_manualcss_actualrequest()
{
	// ------------------- UPDATE CSS
	
	// get active colorscheme 
	var colorschemelookup = nxs_js_getruntimecolorschemelookup();
	var runtimeurllookup = nxs_js_getruntimecsslookup();

	// blend all lookups
	var csslookup = jQuery.extend({}, colorschemelookup, runtimeurllookup);
	
	var csstemplate = nxs_js_get_manualcsstemplate();
	var actualcss = nxs_js_blendtemplatewithvariables(csstemplate, csslookup);
	
	// update the css
	nxs_js_setcss(actualcss, '#nxs-dynamiccss-manual-chunk-');
}

function nxs_js_rerender_facebookbom()
{
	if (typeof(FB) != "undefined" && FB != null ) 
	{
		FB.XFBML.parse();
	}
	else
	{
		// first time / not yet loaded
	}
}

// kudos to http://stackoverflow.com/questions/4825683/how-do-i-create-and-read-a-value-from-cookie
// todo: the path of the cookie should be set to the root folder of the site, this is required for multisites that use folder names
function nxs_js_setcookie(name, value, expiretime) 
{
	var expires = "";

	if (expiretime) 
	{
		var d = new Date();
    d.setTime(d.getTime() + expiretime);
    var expires = " expires=" + d.toUTCString() + " ";
	}

  document.cookie = name + "=" + value + "; " + expires + "; path=/";
}

function nxs_js_getcookie(c_name) 
{
  if (document.cookie.length > 0) 
  {
    c_start = document.cookie.indexOf(c_name + "=");
    if (c_start != -1) 
    {
      c_start = c_start + c_name.length + 1;
      c_end = document.cookie.indexOf(";", c_start);
      if (c_end == -1) {
          c_end = document.cookie.length;
      }
      return unescape(document.cookie.substring(c_start, c_end));
    }
  }
  return "";
}

function nxs_js_tagcolumns()
{
	//nxs_js_log("tagging columns...");
	jQuery.each
	(
		jQ_nxs(".nxs-placeholder-list"), function(index, listelement)
		{
			// nxs_js_log('row found');
			var columnindex = 1;
		
			var placeholders = jQ_nxs(listelement).children(".nxs-placeholder");
			var columnmax = nxs_js_findclassidentificationwithprefix(listelement, "nxs-columnsperrow-", -1);
			if (columnmax == -1)
			{
				// if not found
				// nxs_js_log("fallback :)");
				columnmax = placeholders.length;
			}
			else
			{
				// nxs_js_log("got it :)");
			}
			
			jQuery.each
			(
				placeholders, function(index, currentplaceholder)
				{
					jQ_nxs(currentplaceholder).addClass("nxs-column-" + columnindex + "-" + columnmax);
					if (columnindex == columnmax)
					{
						// last column
						jQ_nxs(currentplaceholder).addClass("nxs-column-lastinrow");
					}
					columnindex++;
					if (columnindex > columnmax)
					{
						columnindex = 1;
					}
				}
			);
		}
	);
}

// kudos to https://gist.github.com/padolsey/527683
var nxs_js_ie=function(){for(var a=3,b=document.createElement("b"),c=b.all||[];b.innerHTML="<!--[if gt IE "+ ++a+"]><i><![endif]-->",c[0];);return 4<a?a:document.documentMode}();
function nxs_js_tagbrowsers()
{
	jQ_nxs("html").addClass("nxs-vendor-" + nxs_js_getvendorprefix());

	if (nxs_js_ie)
	{
		var ie = nxs_js_ie;
		jQ_nxs("html").addClass("nxs-ie");
		var classname = "nxs-ie-" + ie;
		jQ_nxs("html").addClass(classname);

		var compatibleloop = 0;
		while (compatibleloop < 5)
		{
			var classname = "nxs-ie-lte-" + ie;
			jQ_nxs("html").addClass(classname);
			compatibleloop++;
			ie++;
		}
	}
	else
	{
		jQ_nxs("html").addClass("nxs-non-ie");
	}
}

function nxs_js_deviceistouchdevice()
{
	var result = 'ontouchstart' in document.documentElement;
	return result;
}

function nxs_js_tagresponsiveness()
{
	jQ_nxs("html").addClass("nxs-site-responsive");
}

function nxs_js_tagfrontendbackend()
{
	if (nxs_js_isinfrontend())
	{
		jQ_nxs("html").addClass("nxs-frontend");
	}
	else
	{
		jQ_nxs("html").addClass("nxs-backend");
	}
}


function nxs_js_getviewports()
{
	var result = 
	[
	320, 		// iphone portrait
	480, 		// 
	720, 		// 
	960, 		// ipad landscape / desktop
	1200, 	// desktop
	1440		// desktop
	];	
	return result;
}

function nxs_js_setupviewportlistener()
{
	jQ_nxs(document).bind('nxs_event_resizeend', function() { nxs_js_setupviewportlistener_actual(); });
	// initial call is used to set things up
	nxs_js_setupviewportlistener_actual();
}

function nxs_js_setupviewportlistener_actual()
{
	// nxs_js_log("nxs_js_setupviewportlistener_actual");

	var previousactiveviewport = nxs_js_activeviewport;
	//nxs_js_log("previous:" + previousactiveviewport);
	
	var currentactiveviewport = nxs_js_deriveactiveviewport();
	//nxs_js_log("currentactiveviewport:" + currentactiveviewport);
	
	if (previousactiveviewport != currentactiveviewport)
	{
		// store new value
		nxs_js_activeviewport = currentactiveviewport;
		
		if (previousactiveviewport != -1)
		{
			nxs_js_log('broadcasting nxs_event_viewportchanged');
			jQ_nxs(document).trigger('nxs_event_viewportchanged');
		}
		else
		{
			// we don't trigger when the initial viewport is set (we consider that 'not changed')
		}
	}
}

function nxs_js_deriveactiveviewport()
{
	var widthofbrowserwindow = jQ_nxs(window).width();
	var viewports = nxs_js_getviewports();
	var biggest = 0;
	for (var i = 0; i < viewports.length; i++)
	{
		var currentviewport = viewports[i];
		if (currentviewport >= biggest)
		{
			if (widthofbrowserwindow >= currentviewport)
			{
				biggest = currentviewport;
			}
		}
	}
	return biggest;
}

function nxs_js_tagviewports()
{
	var widthofbrowserwindow = jQ_nxs(window).width();
	
	var viewports = nxs_js_getviewports();
	for (var i = 0; i < viewports.length; i++)
	{
		var currentviewport = viewports[i];
		
		// greater than classes
		var compareto = currentviewport - 1;
		if (widthofbrowserwindow >= compareto)
		{
			var classnaam = "nxs-viewport-gt-" + compareto;
			jQ_nxs("html").addClass(classnaam);
		}
		else
		{
			jQ_nxs("html").removeClass("nxs-viewport-gt-" + compareto);
		}
		
		// less than or equal classes
		var compareto = currentviewport - 1;
		if (widthofbrowserwindow <= compareto)
		{
			var classnaam = "nxs-viewport-lte-" + compareto;
			jQ_nxs("html").addClass(classnaam);
		}
		else
		{
			jQ_nxs("html").removeClass("nxs-viewport-lte-" + compareto);
		}
		
		// active viewport
		var compareto = currentviewport;
		jQ_nxs("html").removeClass("nxs-viewport-is-" + compareto);
		// the adding of the active class happens below
	}
	
	// equal classes
	jQ_nxs("html").addClass("nxs-viewport-is-" + nxs_js_deriveactiveviewport());

	
}

function nxs_js_tagdevices()
{
	var deviceistouchdevice = nxs_js_deviceistouchdevice()
	if (deviceistouchdevice)
	{
		jQ_nxs("html").addClass("nxs-touchdevice");
	}
	else
	{
		jQ_nxs("html").addClass("nxs-nontouchdevice");
	}
}

function nxs_js_menuitemclick(domelement, event)
{
	if (event == "mouseleave")
	{
		jQ_nxs(domelement).closest(".nxs-widget").find("li.nxs-touched").removeClass("nxs-touched");
		//nxs_js_log("mouseleave");
		return;
	}
	
	var closestwidget = jQ_nxs(domelement).closest(".nxs-widget");
	
	if (event == "touch")
	{
		if (jQ_nxs(domelement).closest("li").hasClass("nxs-touched"))
		{
			// if item it touched for the 2nd time, hide all touched items
			jQ_nxs(closestwidget).find(".nxs-touched").removeClass("nxs-touched");

			// if item has a link, redirect to link
			var href = jQ_nxs(domelement).attr('href');
			if (href){
				window.location.href = href;
			}
			return;
		}
	}
	
	if (event == "mouseenter" || event == "touch" || event == "click")
	{
		// wipe previous path
		jQ_nxs(closestwidget).find(".nxs-touched").removeClass("nxs-touched");
		// mark new path
		jQ_nxs(domelement).parentsUntil(".nxs-widget").addClass("nxs-touched");
	}
	
	// if user click on a domelement in the menu,
	// we shouldn't redirect immediately,
	// if the item has child items and the device is a touch device, 
	// and the sub menu is not yet showing, absorb the click

	if (event == 'touch' || event == 'click')
	{
		if (nxs_js_deviceistouchdevice()) 
		{
			// the submenu is not an immediate child of the 'this' link,
			// its a child of a sibling element ...
			if (jQ_nxs(domelement).parent().children(".nxs-sub-menu").length > 0)
			{
				// nxs_js_log('NOT about to follow that link!');
			}
			else
			{
				var url = jQ_nxs(domelement).attr("nxsurl");
				if ((url == null || url == ""))
				{
					// empty element; no url, nothing to do
				}
				else
				{
					nxs_js_redirect(url);
				}
			}
		}
		else
		{
			var url = jQ_nxs(domelement).attr("nxsurl");
			if ((url == null || url == ""))
			{
				// nothing to do
			}
			else
			{
				// regular redirect
				nxs_js_redirect(url);
			}
		}
	}
}

function nxs_js_doesuserimpactstyle()
{
	var result = false;
	if (nxs_js_userhasadminpermissions())
	{
		if (!nxs_js_inwpbackend())
		{
			// if menu shows... (color pickers, etc.)
			if (jQ_nxs("#nxs-admin-tabs").length > 0)
			{
				result = true;
			}
		}
	}
	
	return result;
}

// returns the color scheme lookup, according to the runtime environment; if the 
// browser is in readonly mode, this means the lookup is used as defined on the serverside,
// otherwise the values from the flyout menu are used
function nxs_js_getruntimecolorschemelookup()
{
	var corecsslookup;
	if (nxs_js_doesuserimpactstyle())
	{ 
		// dynamic lookup
		corecsslookup = nxs_js_getcsslookupflyoutmenu();
		
		//nxs_js_log('nxs_js_getcsslookupflyoutmenu:');
		//nxs_js_log(corecsslookup);
		//nxs_js_log('-------------');
	}
	else
	{
		// static lookup
		corecsslookup = nxs_js_getcolorschemeaccordingtoserverside();
	}
	
	//
	//
	//
	
	var derivedcsslookup;
	derivedcsslookup = nxs_js_getderivedcsslookup(corecsslookup);
	
	var result = jQuery.extend({}, corecsslookup, derivedcsslookup);

	//nxs_js_log('mixed:');
	//nxs_js_log(result);
	
	return result;
}

// converts 1.0 into 1-0
function nxs_js_getdashedtextrepresentation_for_numericvalue(currentvalue)
{
	var wholepart = Math.floor(currentvalue);
	var fractionpart = nxs_js_getfractionasstring(currentvalue);
	var result = wholepart + '-' + fractionpart;
	return result;
}

// gets derived css lookup variables based on existing lookup
function nxs_js_getderivedcsslookup(corecsslookup)
{
	//nxs_js_log(corecsslookup);
	var result = {}
	var colors = nxs_js_getcolorsinpalette();
	//nxs_js_log('colors:');
	//nxs_js_log(colors);
	
	var coloralphas = nxs_js_getcoloralphas();
	
	for(i=0;i<colors.length;i++)
	{
		var currentcolor = colors[i];
		
		for(subtypei=1;subtypei<=2; subtypei++)
		{
			var identity = currentcolor + subtypei;
			var hexcolor = corecsslookup['color_' + currentcolor + subtypei + '_m'];
			
			if (hexcolor != "" && hexcolor != null)
			{
				//nxs_js_log('hexcolor is set for ' + currentcolor + ':' + hexcolor);
				//nxs_js_log(hexcolor);
			
				var rgb = nxs_js_hextorgb(hexcolor);
				//nxs_js_log(rgb);
				
				var lightDelta = 0.2;
				
				result['color_' + identity + '_dd'] = nxs_js_gethextransformedrgblight(rgb, -lightDelta-lightDelta);	// 2x darker
				result['color_' + identity + '_d'] = nxs_js_gethextransformedrgblight(rgb, -lightDelta);								// 1x darker		
				result['color_' + identity + '_l'] = nxs_js_gethextransformedrgblight(rgb, +lightDelta);
				result['color_' + identity + '_ll'] = nxs_js_gethextransformedrgblight(rgb, +lightDelta+lightDelta);	// 2x lighter
				
				var nxsd = result['color_' + identity + '_d'];
				var nxsm = hexcolor;
				var nxsl = result['color_' + identity + '_l'];
				
				result['gradient_' + identity + '_dm'] = nxs_js_getlineairgradientcss(nxsd, nxsm);
				result['gradient_' + identity + '_ml'] = nxs_js_getlineairgradientcss(nxsm, nxsl);
				
				// flat colors have to take into consideration the alpha's (100%, 80%, 60%, etc.)
				for (var ca_i in coloralphas)
				{
					if (!coloralphas.hasOwnProperty(ca_i)) 
					{
						continue;
					}
					
					var currentcoloralpha = coloralphas[ca_i];
					var alphasuffix;
					
					if (currentcoloralpha < 1)
					{
						alphasuffix = '-a' + nxs_js_getdashedtextrepresentation_for_numericvalue(currentcoloralpha);
					}
					else if (currentcoloralpha == 1)
					{
						// not applicable
						alphasuffix = '';
					}
					else
					{
						nxs_js_log("unsupported currentcoloralpha;" + currentcoloralpha);
					}
				
					result['flat_' + identity + alphasuffix] = nxs_js_getflatcss(nxsm, currentcoloralpha);
				}
			}
			else
			{
				nxs_js_log('hexcolor is not set for ' + currentcolor);
			}
		}
	}
	
	return result;
}

// return the color scheme lookup, according to the color pickers in the flyout menu
function nxs_js_getcsslookupflyoutmenu()
{
	// inject current colorscheme
	var colorschemelookup = {};
	
	var colortypes = nxs_js_getcolorsinpalette();
	var subtypes = ['1','2'];
	
	for (var i in colortypes)
	{
		if (!colortypes.hasOwnProperty(i)) 
		{
			continue;
		}
			
		var currentcolortype = colortypes[i];
		// for example 'primary' color, or 'secundary'
		
		for (var sti in subtypes)
		{
			if (!subtypes.hasOwnProperty(sti)) 
			{
				continue;
			}
			
			var currentsubtype = subtypes[sti];
			var identification = currentcolortype + currentsubtype;
			colorschemelookup['color_' + identification + '_m'] = jQ_nxs('#vg_color_' + identification + '_m').val();
		}
	};
	
	return colorschemelookup;
}

function nxs_js_getfractionasstring(n) 
{
	var result;
  if (n != 0) 
  {
    var fractionpart = n.toString();
    
    fractionpart = fractionpart.split('.');
    if (fractionpart.length == 2)
    {
    	fractionpart = fractionpart[1];
    	result = fractionpart.toString();
    }
    else
    {
    	result = "0";
    }
    //
  }
  else
  {
  	result = "0";
  }
  
  return result;
}

function nxs_js_debug_cssoutput(msg, css)
{
	nxs_js_log("--------------------------");
	nxs_js_log("DEBUG CSS FOR:" + msg);

	css= nxs_js_replacemultiplespaceswithsinglespace(css);
	css = nxs_js_removetabs(css);
	
	var countmsg = nxs_js_getcssselectorcount(css);
	nxs_js_alert("selectors used:" + countmsg);
	
	nxs_js_log("css:");
	nxs_js_log(css);
}

function nxs_js_createcssstyling_fontsizeheading(headingelement, multiplier, fontscale, factor_textfontsize)
{
	// step 1: ensure multiplier exists in available fontsizes
	var availablefontsizes = nxs_js_getstyletypevalues("fontsize");
	if (jQuery.inArray(multiplier, availablefontsizes) < 0)
	{
		nxs_js_alert('warning, unsupported multiplier;' + multiplier);
		nxs_js_log('multiplier:');
		nxs_js_log(multiplier);
		nxs_js_log('availablefontsizes:');
		nxs_js_log(availablefontsizes);
		return;
	}
	
	// step 2: build css

	var u = '';
	u += nxs_js_createcssstyling(
  {
      multipliers: [multiplier], 
      cssparameters: 
      [ 
      	{
        	scale: fontscale,
        	factor: factor_textfontsize,
        	// remove #nxs-container selector from framework.css
          csstemplate: ' \
          	' + headingelement + ', \
          	#nxs-container #nxs-content-container ' + headingelement + ' span \
          	{ \
          		font-size: $nxs_formularesult;px;\
          		line-height: 1.2em;\
          	} \
          	'
        },
      ]
    }
  );
  return u;
}

function nxs_js_createcssstyling(options) 
{    
  var result = '';
  var multipliers = options.multipliers;
  var cssparameters =  options.cssparameters;
  
  for (var i in multipliers) 
  {
		if (!multipliers.hasOwnProperty(i)) 
		{
			continue;
		}

    var currentmultiplier = multipliers[i];
    
    var wholepart = Math.floor(currentmultiplier);
    
    // we need to return integers, not fractions to be able to concatenate with the class name
    var fractionpart = nxs_js_getfractionasstring(currentmultiplier);
    
    for (var j in cssparameters ) 
    {
    	if (!cssparameters.hasOwnProperty(j)) 
			{
				continue;
			}
			
    	var currentcssparameter = cssparameters[j];

    	var identification = wholepart + '-' + fractionpart;
    	var csstemplate = currentcssparameter.csstemplate;
    	var csstemplate = nxs_js_removelinebreaks(csstemplate);
    	var csstemplate = nxs_js_removetabs(csstemplate);
    	var csstemplate = nxs_js_replacemultiplespaceswithsinglespace(csstemplate);
    		
 	    var factor = currentcssparameter.factor;
			if (factor == null)
			{
				// 
				factor = options.factor;
			}
			
			var resultoffset = currentcssparameter.resultoffset;
			if (resultoffset == null)
			{
				resultoffset = options.resultoffset;
				if (resultoffset == null)
				{
					resultoffset = 0;
				}
			}
			
			var scale = currentcssparameter.scale;
			if (scale == null)
			{
				scale = options.scale;
				if (scale == null)
				{
					scale = 1;
				}
			}
    	
      if (csstemplate != null)
      {
      	// apply template
      	var lookup = { 
      		"formularesult;": Math.round((currentmultiplier * factor * scale) + resultoffset), 	// will replace variable $nxs_formularesult;
      		"identification;": identification // will replace variable $nxs_identification;
      	};
      	
      	// apply template
      	result = result + nxs_js_blendtemplatewithvariables(csstemplate, lookup);
      }
			else
			{
				nxs_js_alert("no css template found");
				nxs_js_log(options);
      }
    }
  }
	//nxs_js_log(result);
	return result; 
}

function nxs_js_get_themecsstemplate_part1_colorzen(cssprefix, csspostfix, zenprefix, csslookup, csspostfixanchors)
{
	if (csspostfixanchors == null)
	{
		csspostfixanchors = csspostfix;
	}

	var u = "";
	var coloralphas = nxs_js_getcoloralphas();
	var colortypes = nxs_js_getcolorsinpalette();
	
	for (var i in colortypes)
	{
		if (!colortypes.hasOwnProperty(i)) 
		{
			continue;
		}

		var currentcolortype = colortypes[i];
		
		// currentcolortype is for example 'c1', or 'base1'
		var identification = currentcolortype;
		if (zenprefix == null || zenprefix == "")
		{
			zenprefix = "nxs-colorzen-";
		}
		var cssinnerprefix = zenprefix + currentcolortype;
		
		var comparecolorhex = csslookup["color_" + identification + "2_m"];
		var comparecolorrgb = nxs_js_hextorgb(comparecolorhex);
		var comparecolorhsl = nxs_js_rgbtohsl(comparecolorrgb);			
		var lighttreshhold = 0.7;
		var isbackgroundcolorrelativelydark = comparecolorhsl.l < lighttreshhold;
		
		// COLOR IQ :)
		// GRADIENTS --------------
		
		if (currentcolortype == "base")
		{
			/* BACKGROUND COLOR AND TEXT & TITLES (OPPOSITE OF BACKGROUND) */
			u = u + cssprefix + "." + cssinnerprefix + "1-ml " + csspostfix + " \
			{ \
				border-color: " + csslookup["color_" + identification + "1_d"] + ";\
				color: " + csslookup["color_" + identification + "2_d"] + ";\
				text-shadow: 1px 1px 1px " + csslookup["color_" + identification + "1_ll"] + ";\
				" + csslookup["gradient_" + identification + "1_ml"] + "\
			}";
	
			u = u + cssprefix + "." + cssinnerprefix + "1-dm " + csspostfix + "\
			{\
				border-color: " + csslookup["color_" + identification + "1_dd"] + ";\
				color: " + csslookup["color_" + identification + "2_dd"] + ";\
				text-shadow: 1px 1px 1px " + csslookup["color_" + identification + "1_ll"] + ";\
				" + csslookup["gradient_" + identification + "1_dm"] + "\
			}";
		}
		else
		{
			// skip colorzen for the "1" variations for others than "base"; only base1 is used
		}
		
		if (isbackgroundcolorrelativelydark)
		{
			var textcolor = csslookup["color_" + identification + "1_ll"];
			var textshadowcolor = csslookup["color_" + identification + "2_dd"];
		}
		else
		{
			var textcolor = "black";
			var textshadowcolor = "white";
		}
		
		u = u + cssprefix + "." + cssinnerprefix + "2-ml " + csspostfix + "\
		{\
			border-color: " + csslookup["color_" + identification + "2_d"] + ";\
			" + csslookup["gradient_" + identification + "2_ml"] + "\
			color: " + textcolor + ";\
			text-shadow: 1px 1px 1px " + textshadowcolor + ";\
		}";
		
		if (isbackgroundcolorrelativelydark)
		{
			var textcolor = csslookup["color_" + identification + "1_l"];
			var textshadowcolor = csslookup["color_" + identification + "2_dd"];
		}
		else
		{
			var textcolor = "black";
			var textshadowcolor = "white";
		}

		u = u + cssprefix + "." + cssinnerprefix + "2-dm " + csspostfix + "\
		{\
			border-color: " + csslookup["color_" + identification + "2_dd"] + ";\
			" + csslookup["gradient_" + identification + "2_dm"] + "\
			color: " + textcolor +";\
			text-shadow: 1px 1px 1px " + textshadowcolor + ";\
		}";

		
		
		/* ANCHORS */
		
		if (currentcolortype == "base")
		{
			u = u + cssprefix + "." + cssinnerprefix + "1-ml " + csspostfixanchors + " a { color: " + csslookup["color_" + identification + "2_d"] + "; text-shadow: 1px 1px 1px " + csslookup["color_" + identification + "1_ll"] + "; }";
			u = u + cssprefix + "." + cssinnerprefix + "1-dm " + csspostfixanchors + " a { color: " + csslookup["color_" + identification + "2_dd"] + "; text-shadow: 1px 1px 1px " + csslookup["color_" + identification + "1_ll"] + "; }";
		}
		else
		{
			// skip colorzen for the "1" variations for others than "base"; only base1 is used
		}
		
		if (isbackgroundcolorrelativelydark)
		{
			var textcolor = csslookup["color_" + identification + "1_ll"];
			var textshadowcolor = csslookup["color_" + identification + "2_dd"];
		}
		else
		{
			var textcolor = "black";
			var textshadowcolor = "white";
		}
		
		u = u + cssprefix + "." + cssinnerprefix + "2-ml " + csspostfixanchors + " a\
		{\
			color: " + textcolor + ";\
			text-shadow: 1px 1px 1px " + textshadowcolor + ";\
		}";
		
		if (isbackgroundcolorrelativelydark)
		{
			var textcolor = csslookup["color_" + identification + "1_l"];
			var textshadowcolor = csslookup["color_" + identification + "2_dd"];
		}
		else
		{
			var textcolor = "black";
			var textshadowcolor = "white";
		}
		
		u = u + cssprefix + "." + cssinnerprefix + "2-dm " + csspostfixanchors + " a\
		{\
			color: " + textcolor + ";\
			text-shadow: 1px 1px 1px " + textshadowcolor + ";\
		}";
		
		
		
		// FLAT --------------
		
		/* TEXT & TITLES (OPPOSITE OF BACKGROUND) */
		
		// ------------
		// colorzen for flat colors
		
		var coloralphas = nxs_js_getcoloralphas();
		for (var i in coloralphas)
		{
			if (!coloralphas.hasOwnProperty(i)) 
			{
				continue;
			}
			
			var currentcoloralpha = coloralphas[i];
			var alphasuffix;
			
			if (currentcoloralpha < 1)
			{
				alphasuffix = '-a' + nxs_js_getdashedtextrepresentation_for_numericvalue(currentcoloralpha);
			}
			else if (currentcoloralpha == 1)
			{
				// not applicable
				alphasuffix = '';
			}
			else
			{
				nxs_js_log("unsupported currentcoloralpha;" + currentcoloralpha);
			}
			
			// overruled: background and border are 100% black
			u = u + cssprefix + "." + cssinnerprefix + "1" + alphasuffix + " " + csspostfix + "\
			{\
				" + nxs_js_getflatbackgroundnogradientcss('#FFFFFF', currentcoloralpha) + ";\
				border-color:  #C6C6C6;\
				color: " + csslookup["color_" + identification + "2_d"] + ";\
				text-shadow: 1px 1px 1px " + csslookup["color_" + identification + "1_ll"] + ";\
			}";
			
			// regular color			
			u = u + cssprefix + "." + cssinnerprefix + "2" + alphasuffix + " " + csspostfix + "\
			{";
			
				if (currentcolortype == "base")
				{
					// overruled: background and border are 100% black
					u = u + "\
						border-color: #000000;\
						" + nxs_js_getflatbackgroundnogradientcss('#000000', currentcoloralpha) + ";\
					";
				}
				else
				{
					u = u + "\
						border-color: " + csslookup["color_" + identification + "2_d"] + ";\
						" + csslookup["flat_" + identification + "2" + alphasuffix] + "\
					";
					//nxs_js_log("key:");
					//nxs_js_log("flat_" + identification + "2-a0-8");
				}
	
				if (isbackgroundcolorrelativelydark)
				{
					var textcolor = csslookup["color_" + identification + "1_ll"];
					var textshadowcolor = csslookup["color_" + identification + "2_dd"];
				}
				else
				{
					var textcolor = "black";
					var textshadowcolor = "white";
				}
			
				u = u + "\
					color: " + textcolor + ";\
					text-shadow: 1px 1px 1px " + textshadowcolor + ";\
					";
				
			u = u + "}";
		}
		
		/* ANCHORS FLAT COLOR */		
		
		u = u + cssprefix + "." + cssinnerprefix + "1 " + csspostfixanchors + " a\
		{\
			color: " + csslookup["color_" + identification + "2_dd"] + ";\
			text-shadow: 1px 1px 1px " + csslookup["color_" + identification + "1_ll"] + ";\
		}";
		
		if (isbackgroundcolorrelativelydark)
		{
			var textcolor = csslookup["color_" + identification + "1_l"];
			var textshadowcolor = csslookup["color_" + identification + "2_dd"];
		}
		else
		{
			var textcolor = "black";
			var textshadowcolor = "white";
		}
		
		u = u + cssprefix + "." + cssinnerprefix + "2 " + csspostfixanchors + " a\
		{\
			color: " + textcolor + ";\
			text-shadow: 1px 1px 1px " + textshadowcolor + ";\
		}";
	}
	
	//nxs_js_log("-");
	//nxs_js_log("colorzen output:" + csspostfixanchors);
	//nxs_js_log(u);
	
	return u;
}

function nxs_js_get_themecsstemplate_part1_anchorlinkcolors(cssprefix, pseudo, csslookup)
{
	var u = "";
	
	u += "/* output of nxs_js_get_themecsstemplate_part1_anchorlinkcolors */";
	
	var colortypes = nxs_js_getcolorsinpalette();

	for (var i in colortypes)
	{
		if (!colortypes.hasOwnProperty(i)) 
		{
			continue;
		}		
		
		var currentcolortype = colortypes[i];
		var identification = currentcolortype;
		
		/* EXPLICIT ANCHOR COLORS */
		
		var variations = ["dd", "d", "m", "l", "ll"];
		for (var i in variations) 
	  {
	    var currentvariation = variations[i];
	    
	    var selector = "nxs-linkcolorvar-" + currentcolortype + "2-" + currentvariation;
	    var colorvar = csslookup["color_" + identification + "2_" + currentvariation];
	    
	    // todo: isolate text-shadow: none in seperate css selector?
	     
			u = u + "\
				" + cssprefix + "." + selector + " .nxs-applylinkvarcolor a" + pseudo + "\
				{ \
					color: " + colorvar + ";\
					text-shadow: none;\
				}	\
				";
			
			
			if (currentcolortype == 'base')
			{
				// special case; if the currentcolortype == "base" we should also output the 1- version (for white colors)
				var selector = "nxs-linkcolorvar-" + currentcolortype + "1-" + currentvariation;
	   		var colorvar = csslookup["color_" + identification + "1_" + currentvariation];
	   		
	   		u = u + "\
				" + cssprefix + "." + selector + " .nxs-applylinkvarcolor a" + pseudo + "\
				{ \
					color: " + colorvar + ";\
					text-shadow: none;\
				}	\
				";
				
			}
		}
	}

	return u;
}

function nxs_js_get_nxsrefurlspecial() 
{ 
	var url = nxs_js_geturlcurrentpage();
	var base64encodedurl = nxs_js_base64_encode(url);
	var result = nxs_js_urldecode(base64encodedurl); 
	return result;
}

function nxs_js_cssremovecomments(haystack)
{
	// *? = ungreedy
	// assumes single line css!
	return haystack.replace(/ *\/\*[^)]*?\*\/ */g, "");
}

function nxs_js_getcssselectorcount(haystack)
{
	if (nxs_js_stringcontains(nxs_js_cssremovecomments(haystack), "@media"))
	{
		nxs_js_alert("It looks like media queries are used; these aren't supported in the css splitter (1)");
	}

	var result = 0;

  var splitted = haystack.split("}");
  
  var count = splitted.length;
  for (var i = 0; i < count; i++)
  {
  	var cssdeclaration = splitted[i];
  	  	
  	// splits selectors / assignment 
  	var splitted2 = cssdeclaration.split("{");
  	
  	var selectors = splitted2[0];
  	selectors = nxs_js_cssremovecomments(selectors);
  	
  	var splitted3 = selectors.split(",");
  	result += splitted3.length;
  }
  
  return result;
}

function nxs_js_getchunksofcss(haystack)
{
	// precondition
	if (nxs_js_stringcontains(nxs_js_cssremovecomments(haystack), "@media"))
	{
		nxs_js_alert("It looks like media queries are used; these aren't supported in the css splitter (2)");
		nxs_js_log(haystack);
	}
	
	// initialization
	var maxchunksize = 4095;	// max # of css selectors allowed per chunk
	var result = {};
	var declarationsincurrentchunk = 0;
	result.chunks = {};
	for (var i = 0; i < nxs_js_getmaxservercsschunks(); i++)
	{
		result.chunks[currentchunk] = "";	// initial output
	}
	
	var splitted = haystack.split("}");
  
	var currentchunk = 0;
  var count = splitted.length;
  for (var i = 0; i < count; i++)
  {
  	var cssdeclaration = splitted[i];
  	  	
  	// splits selectors / assignment 
  	var splitted2 = cssdeclaration.split("{");
  	
  	var selectors = splitted2[0];
  	selectors = nxs_js_cssremovecomments(selectors);
  	
  	var splitted3 = selectors.split(","); 	
  	var numofdeclarations = splitted3.length;  	
  	if (declarationsincurrentchunk + numofdeclarations > maxchunksize)
  	{
  		// we need a new chunk
  		currentchunk++;
  		if (currentchunk >= nxs_js_getmaxservercsschunks())
  		{
  			nxs_js_alert('Please add additional css chunks; see nxsstyles.php');
  		}
  		result.chunks[currentchunk] = "";
  		declarationsincurrentchunk = 0;
  	}
  	
	 	// store in current chunk and continue
	 	result.chunks[currentchunk] += (cssdeclaration + "}");
	 	// increase counter of current chunk
	 	declarationsincurrentchunk += numofdeclarations;
  }
  
  return result;
}

// returns the css generated by the framework,
// consists of all scaffolding classes like colors, widths, etc.
// it takes into consideration configured items like number of colors
function nxs_js_get_frameworkcsstemplate(csslookup)
{
	var u = "";
	u += "/* start Nexus framework css template */";
	
	var colortypes = nxs_js_getcolorsinpalette();
	
	u = u + ".nxs-colorzen { border-style: solid; }";
	u = u + "#nxs-menu-wrap { text-shadow: none; }";
	
	//
	// CONTAINER COLORZEN per element, no inheritance
	//		
	// colorzen's are defined on element level. The text and link colors are inherited
	// like body -> page -> container -> row -> placeholder 
	
	u = u + nxs_js_get_themecsstemplate_part1_colorzen("", "", "nxs-colorzen-", csslookup, "");
	u = u + nxs_js_get_themecsstemplate_part1_colorzen(".nxs-colorzen ", "", "nxs-colorzen-", csslookup, "");
	u = u + nxs_js_get_themecsstemplate_part1_colorzen(".nxs-colorzen .nxs-colorzen ", "", "nxs-colorzen-", csslookup, "");
	u = u + nxs_js_get_themecsstemplate_part1_colorzen(".nxs-colorzen .nxs-colorzen .nxs-colorzen ", "", "nxs-colorzen-", csslookup, "");
	u = u + nxs_js_get_themecsstemplate_part1_colorzen(".nxs-colorzen .nxs-colorzen .nxs-colorzen .nxs-colorzen ", "", "nxs-colorzen-", csslookup, "");
	

	// SLIDETOTOP COLORZEN ==============================


	u = u + nxs_js_get_themecsstemplate_part1_colorzen(".nxs-applyhovercolors:hover ", "", "nxs-colorzen-hover-", csslookup, "");
	u = u + nxs_js_get_themecsstemplate_part1_colorzen(".nxs-applyactivecolors ", "", "nxs-colorzen-active-", csslookup, "");

	//
	// 
	//
	
	//
	// MENU COLORZEN ==============================

	// main items -----------

	// colorzen for menu items
	u = u + nxs_js_get_themecsstemplate_part1_colorzen(".nxs-applymenucolors", " > li.nxs-inactive", "nxs-colorzen-menuitem-", csslookup, " > li > ");
		
	// colorzen for (top level) active menu items 
	u = u + nxs_js_get_themecsstemplate_part1_colorzen(".nxs-applymenucolors", " > li.nxs-active", "nxs-colorzen-menuitem-active-", csslookup, " > li.nxs-active > ");
	
	// colorzen for (top level) hover menu items 
	u = u + nxs_js_get_themecsstemplate_part1_colorzen(".nxs-applymenucolors", " > li:hover", "nxs-colorzen-menuitem-hover-", csslookup, " > li:hover > ");
	
	// colorzen for (top level) hover menu items 
	u = u + nxs_js_get_themecsstemplate_part1_colorzen(".nxs-applymenucolors", " > li.nxs-active:hover", "nxs-colorzen-menuitem-active-", csslookup, " > li.nxs-active:hover > ");
	
	// sub items -----------
	
	// colorzen for submenu items
	u = u + nxs_js_get_themecsstemplate_part1_colorzen(".nxs-sub-menu", "", "nxs-colorzen-menuitem-sub-", csslookup, " > li > ");

	// colorzen for (top level) active menu items 
	u = u + nxs_js_get_themecsstemplate_part1_colorzen(".nxs-sub-menu", " > li.nxs-active", "nxs-colorzen-menuitem-sub-active-", csslookup, "> li.nxs-active > ");

	// colorzen for (top level) hover menu items 
	u = u + nxs_js_get_themecsstemplate_part1_colorzen(".nxs-sub-menu", " > li:hover", "nxs-colorzen-menuitem-sub-hover-", csslookup, " > li:hover > ");

	// colorzen for (top level) hover menu items 
	u = u + nxs_js_get_themecsstemplate_part1_colorzen(".nxs-sub-menu", " > li.nxs-active:hover", "nxs-colorzen-menuitem-sub-active-", csslookup, " > li.nxs-active:hover > ");
  
  //
	// ANCHOR LINKS nested inherited (!)
	//		
	// anchor link colors are inherited,
	// like body -> page -> container -> row -> placeholder 
	// anchor links are stronger than (generic) colorzen's

	u = u + nxs_js_get_themecsstemplate_part1_anchorlinkcolors("","" , csslookup);
	u = u + nxs_js_get_themecsstemplate_part1_anchorlinkcolors(".nxs-linkcolorvar ","", csslookup);
	u = u + nxs_js_get_themecsstemplate_part1_anchorlinkcolors(".nxs-linkcolorvar .nxs-linkcolorvar ","", csslookup);
	u = u + nxs_js_get_themecsstemplate_part1_anchorlinkcolors(".nxs-linkcolorvar .nxs-linkcolorvar .nxs-linkcolorvar ","", csslookup);
	u = u + nxs_js_get_themecsstemplate_part1_anchorlinkcolors(".nxs-linkcolorvar .nxs-linkcolorvar .nxs-linkcolorvar .nxs-linkcolorvar ","", csslookup);
	
	//
  
  /* DEFAULT ICON WIDTH CLASSES
  ---------------------------------------------------------------------------------------------------- */
  
  u = u + nxs_js_createcssstyling(
      {
          factor: 80,
          multipliers: nxs_js_getstyletypevalues("image_size"),
          cssparameters: [ 
              {
                  csstemplate: '.nxs-icon-width-$nxs_identification; { width: $nxs_formularesult;px; height: $nxs_formularesult;px; }',
              }
          ]
      }
  );
    
  /* DEFAULT BORDER WIDTH CLASSES
  ---------------------------------------------------------------------------------------------------- */
    
  u = u + nxs_js_createcssstyling(
      {
          factor: 1,        
          multipliers: nxs_js_getstyletypevalues("border_width"),
          cssparameters: [ 
          		{
          			csstemplate: ' \
          				.nxs-border-width-$nxs_identification; { border-width: $nxs_formularesult;px; } 								\
          				.nxs-border-left-width-$nxs_identification; { border-left-width: $nxs_formularesult;px; } 			\
          				.nxs-border-right-width-$nxs_identification; { border-right-width: $nxs_formularesult;px; }		 	\
          				.nxs-border-top-width-$nxs_identification; { border-top-width: $nxs_formularesult;px; }					\
          				.nxs-border-bottom-width-$nxs_identification; { border-bottom-width: $nxs_formularesult;px; }'
          		}
          ]
      }
  );
  
  /* DEFAULT PADDING CLASSES
  ---------------------------------------------------------------------------------------------------- */
	
	u = u + nxs_js_createcssstyling(
  {
      factor: 30,
      multipliers: nxs_js_getstyletypevalues("padding"),
      cssparameters: [ 
					{
      			csstemplate: ' \
      				.nxs-padding-$nxs_identification; { padding: $nxs_formularesult;px; }			\
      				.nxs-padding-top-$nxs_identification; { padding-top: $nxs_formularesult;px; }	\
      				.nxs-padding-bottom-$nxs_identification; { padding-bottom: $nxs_formularesult;px; }'
      		}
     	]
  }
	);
    
  /* DEFAULT MARGIN CLASSES
  ---------------------------------------------------------------------------------------------------- */

	u = u + nxs_js_createcssstyling(
  {
      factor: 30,
      multipliers: nxs_js_getstyletypevalues("margin"),
      cssparameters: [ 
          {
      			csstemplate: '\
      				.nxs-margin-$nxs_identification; { margin: $nxs_formularesult;px; }	\
      				.nxs-margin-top-$nxs_identification; { margin-top: $nxs_formularesult;px; }	\
      				.nxs-margin-bottom-$nxs_identification; { margin-bottom: $nxs_formularesult;px; }'
      		}
       ]
  }
  );
    
  /* DEFAULT BORDER RADIUS CLASSES
  ---------------------------------------------------------------------------------------------------- */
    
  u = u + nxs_js_createcssstyling(
  {
      factor: 3,
      multipliers: nxs_js_getstyletypevalues("border_radius"),
      cssparameters: [ 
					{
      			csstemplate: '.nxs-border-radius-$nxs_identification; { border-radius: $nxs_formularesult;px; }',
      		}
      ]
  }
  );
  
  /* MAX HEIGHT CLASSES
  ---------------------------------------------------------------------------------------------------- */
    
  u = u + nxs_js_createcssstyling(
  {
      factor: 100,
      multipliers: nxs_js_getstyletypevalues("maxheight"),
      cssparameters: [ 
					{
      			csstemplate: '.nxs-maxheight-$nxs_identification; { max-height: $nxs_formularesult;px; }',
      		}
      ]
  }
  );
    
  /* DEFAULT BUTTON SCALE CLASSES
  ---------------------------------------------------------------------------------------------------- */
  
  // padding top and bottom
  
  u = u + nxs_js_createcssstyling(
  {
      multipliers: nxs_js_getstyletypevalues("button_scale"),
      cssparameters: [ 
          {
          	factor: 6,
            csstemplate: '\
            	.nxs-button-scale-$nxs_identification; \
            	{ \
            		padding-top: $nxs_formularesult;px; \
            		padding-bottom: $nxs_formularesult;px; \
            	}',
          },
          {
          	factor: 10,
            csstemplate: '\
            	.nxs-button-scale-$nxs_identification; \
            	{ \
            		padding-left: $nxs_formularesult;px; \
            		padding-right: $nxs_formularesult;px; \
            	}',
          },
          {
          	factor: 12,
            csstemplate: '\
            	.nxs-button.nxs-button-scale-$nxs_identification; \
            	{ \
            		font-size: $nxs_formularesult;px; \
            	}',
          }
      ]
    }
  );
  
  /* DEFAULT BUTTON SCALE CLASSES
  ---------------------------------------------------------------------------------------------------- */
  
  // padding top and bottom
  
  u = u + nxs_js_createcssstyling(
  {
      multipliers: nxs_js_getstyletypevalues("icon_scale"),
      cssparameters: [ 
          {
          	factor: 32,
            csstemplate: '\
            	.nxs-icon-scale-$nxs_identification;, \
            	.nxs-text .top-wrapper span.nxs-icon-scale-$nxs_identification; \
            	{ \
            		font-size: $nxs_formularesult;px; \
            	}',
          }
      ]
    }
  );
  
  /* DEFAULT FONTSIZE SCALE CLASSES
  ---------------------------------------------------------------------------------------------------- */
  
  var fontscale = 10;
  var factor_textfontsize = 1.5;	/* TODO: make this configurable by end user in flyout menu */
    
  // font-sizes (1.0, 1.2, ... 2.0 = 15px ... 30px)
  u = u + nxs_js_createcssstyling(
  {
      multipliers: nxs_js_getstyletypevalues("fontsize"),
      cssparameters: 
      [ 
        {
        	scale: fontscale,
        	factor: factor_textfontsize, 
          csstemplate: '\
        	#nxs-container.nxs-text-fontsize-$nxs_identification; .nxs-default-p p, \
        	#nxs-container.nxs-text-fontsize-$nxs_identification; .nxs-default-p li, \
        	#nxs-container.nxs-text-fontsize-$nxs_identification; .nxs-default-p span, \
	       	#nxs-container .nxs-placeholder.nxs-text-fontsize-$nxs_identification; .nxs-default-p p, \
	       	#nxs-container .nxs-placeholder.nxs-text-fontsize-$nxs_identification; .nxs-default-p li, \
        	#nxs-container .nxs-placeholder.nxs-text-fontsize-$nxs_identification; .nxs-default-p span, \
        	#nxs-container.nxs-head-fontsize-$nxs_identification; .nxs-title, \
        	#nxs-container .nxs-placeholder.nxs-head-fontsize-$nxs_identification; .nxs-title, \
        	#nxs-container .nxs-placeholder .nxs-head-fontsize-$nxs_identification;.nxs-title \
        	{ \
        		 font-size: $nxs_formularesult;px; \
        	}',
        },
      ]
    }
  );
  
  u += nxs_js_createcssstyling_fontsizeheading("H1", 2.0, fontscale, factor_textfontsize);
  u += nxs_js_createcssstyling_fontsizeheading("H2", 1.8, fontscale, factor_textfontsize);
  u += nxs_js_createcssstyling_fontsizeheading("H3", 1.6, fontscale, factor_textfontsize);
  u += nxs_js_createcssstyling_fontsizeheading("H4", 1.4, fontscale, factor_textfontsize);
  u += nxs_js_createcssstyling_fontsizeheading("H5", 1.2, fontscale, factor_textfontsize);
  u += nxs_js_createcssstyling_fontsizeheading("H6", 1.0, fontscale, factor_textfontsize);
  
  u = u + ".nxs-image-wrapper img { width: 100%; display: block; }";
  // scenario 1; logo accountant website template
  u = u + ".nxs-image-wrapper.nxs-ratio-original img { width: inherit; max-width: 100%; display: block; }";
    
	u += "/* end injected by framework */";
	
	//nxs_js_log('part1:');
	//nxs_js_log(u);

	//
	//
	//
	
	return u;
}

function nxs_js_popuptogglewrapper(domelement, id)
{
	var container = jQ_nxs(domelement).closest(".nxs-option-toggler");
	jQ_nxs(container).toggleClass("nxs-toggled-open"); 
	jQ_nxs(container).toggleClass("nxs-toggled-closed");
	
	// after toggle: reposition the popup (immeditately)
	nxs_js_reset_popup_dimensions_actualrequest();	
}

function nxs_js_refreshtopmenufillerheight()
{
	var height = jQ_nxs("#nxs-menu-wrap").height();
	//nxs_js_log('height nxs-menu-wrap is ' + height);
	jQ_nxs('#menufillerinlinecontent').height(height); // + 30);
}

function nxs_js_rerender_dirty_rowcontainers()
{
  // TODO: this can be further optimized; rather than launching one AJAX call
  // for each row container, it would be possible to do one call for all
  jQ_nxs(".nxs-row-container.nxs-dirty").each
	(
		function(index, element) 
		{
			nxs_js_rerender_row_for_element(element);
		}
	);
}

/* nicescroll */

/* jquery.nicescroll 3.5.0 InuYaksa*2013 MIT http://areaaperta.com/nicescroll */(function(e){var z=!1,E=!1,L=5E3,M=2E3,y=0,N=function(){var e=document.getElementsByTagName("script"),e=e[e.length-1].src.split("?")[0];return 0<e.split("/").length?e.split("/").slice(0,-1).join("/")+"/":""}(),H=["ms","moz","webkit","o"],v=window.requestAnimationFrame||!1,w=window.cancelAnimationFrame||!1;if(!v)for(var O in H){var F=H[O];v||(v=window[F+"RequestAnimationFrame"]);w||(w=window[F+"CancelAnimationFrame"]||window[F+"CancelRequestAnimationFrame"])}var A=window.MutationObserver||window.WebKitMutationObserver||
!1,I={zindex:"auto",cursoropacitymin:0,cursoropacitymax:1,cursorcolor:"#424242",cursorwidth:"5px",cursorborder:"1px solid #fff",cursorborderradius:"5px",scrollspeed:60,mousescrollstep:24,touchbehavior:!1,hwacceleration:!0,usetransition:!0,boxzoom:!1,dblclickzoom:!0,gesturezoom:!0,grabcursorenabled:!0,autohidemode:!0,background:"",iframeautoresize:!0,cursorminheight:32,preservenativescrolling:!0,railoffset:!1,bouncescroll:!0,spacebarenabled:!0,railpadding:{top:0,right:0,left:0,bottom:0},disableoutline:!0,
horizrailenabled:!0,railalign:"right",railvalign:"bottom",enabletranslate3d:!0,enablemousewheel:!0,enablekeyboard:!0,smoothscroll:!0,sensitiverail:!0,enablemouselockapi:!0,cursorfixedheight:!1,directionlockdeadzone:6,hidecursordelay:400,nativeparentscrolling:!0,enablescrollonselection:!0,overflowx:!0,overflowy:!0,cursordragspeed:0.3,rtlmode:!1,cursordragontouch:!1,oneaxismousemode:"auto"},G=!1,P=function(){if(G)return G;var e=document.createElement("DIV"),c={haspointerlock:"pointerLockElement"in document||
"mozPointerLockElement"in document||"webkitPointerLockElement"in document};c.isopera="opera"in window;c.isopera12=c.isopera&&"getUserMedia"in navigator;c.isoperamini="[object OperaMini]"===Object.prototype.toString.call(window.operamini);c.isie="all"in document&&"attachEvent"in e&&!c.isopera;c.isieold=c.isie&&!("msInterpolationMode"in e.style);c.isie7=c.isie&&!c.isieold&&(!("documentMode"in document)||7==document.documentMode);c.isie8=c.isie&&"documentMode"in document&&8==document.documentMode;c.isie9=
c.isie&&"performance"in window&&9<=document.documentMode;c.isie10=c.isie&&"performance"in window&&10<=document.documentMode;c.isie9mobile=/iemobile.9/i.test(navigator.userAgent);c.isie9mobile&&(c.isie9=!1);c.isie7mobile=!c.isie9mobile&&c.isie7&&/iemobile/i.test(navigator.userAgent);c.ismozilla="MozAppearance"in e.style;c.iswebkit="WebkitAppearance"in e.style;c.ischrome="chrome"in window;c.ischrome22=c.ischrome&&c.haspointerlock;c.ischrome26=c.ischrome&&"transition"in e.style;c.cantouch="ontouchstart"in
document.documentElement||"ontouchstart"in window;c.hasmstouch=window.navigator.msPointerEnabled||!1;c.ismac=/^mac$/i.test(navigator.platform);c.isios=c.cantouch&&/iphone|ipad|ipod/i.test(navigator.platform);c.isios4=c.isios&&!("seal"in Object);c.isandroid=/android/i.test(navigator.userAgent);c.trstyle=!1;c.hastransform=!1;c.hastranslate3d=!1;c.transitionstyle=!1;c.hastransition=!1;c.transitionend=!1;for(var k=["transform","msTransform","webkitTransform","MozTransform","OTransform"],l=0;l<k.length;l++)if("undefined"!=
typeof e.style[k[l]]){c.trstyle=k[l];break}c.hastransform=!1!=c.trstyle;c.hastransform&&(e.style[c.trstyle]="translate3d(1px,2px,3px)",c.hastranslate3d=/translate3d/.test(e.style[c.trstyle]));c.transitionstyle=!1;c.prefixstyle="";c.transitionend=!1;for(var k="transition webkitTransition MozTransition OTransition OTransition msTransition KhtmlTransition".split(" "),q=" -webkit- -moz- -o- -o -ms- -khtml-".split(" "),t="transitionend webkitTransitionEnd transitionend otransitionend oTransitionEnd msTransitionEnd KhtmlTransitionEnd".split(" "),
l=0;l<k.length;l++)if(k[l]in e.style){c.transitionstyle=k[l];c.prefixstyle=q[l];c.transitionend=t[l];break}c.ischrome26&&(c.prefixstyle=q[1]);c.hastransition=c.transitionstyle;a:{k=["-moz-grab","-webkit-grab","grab"];if(c.ischrome&&!c.ischrome22||c.isie)k=[];for(l=0;l<k.length;l++)if(q=k[l],e.style.cursor=q,e.style.cursor==q){k=q;break a}k="url(https://www.google.com/intl/en_ALL/mapfiles/openhand.cur),n-resize"}c.cursorgrabvalue=k;c.hasmousecapture="setCapture"in e;c.hasMutationObserver=!1!==A;return G=
c},Q=function(h,c){function k(){var d=b.win;if("zIndex"in d)return d.zIndex();for(;0<d.length&&9!=d[0].nodeType;){var c=d.css("zIndex");if(!isNaN(c)&&0!=c)return parseInt(c);d=d.parent()}return!1}function l(d,c,f){c=d.css(c);d=parseFloat(c);return isNaN(d)?(d=u[c]||0,f=3==d?f?b.win.outerHeight()-b.win.innerHeight():b.win.outerWidth()-b.win.innerWidth():1,b.isie8&&d&&(d+=1),f?d:0):d}function q(d,c,f,g){b._bind(d,c,function(b){b=b?b:window.event;var g={original:b,target:b.target||b.srcElement,type:"wheel",
deltaMode:"MozMousePixelScroll"==b.type?0:1,deltaX:0,deltaZ:0,preventDefault:function(){b.preventDefault?b.preventDefault():b.returnValue=!1;return!1},stopImmediatePropagation:function(){b.stopImmediatePropagation?b.stopImmediatePropagation():b.cancelBubble=!0}};"mousewheel"==c?(g.deltaY=-0.025*b.wheelDelta,b.wheelDeltaX&&(g.deltaX=-0.025*b.wheelDeltaX)):g.deltaY=b.detail;return f.call(d,g)},g)}function t(d,c,f){var g,e;0==d.deltaMode?(g=-Math.floor(d.deltaX*(b.opt.mousescrollstep/54)),e=-Math.floor(d.deltaY*
(b.opt.mousescrollstep/54))):1==d.deltaMode&&(g=-Math.floor(d.deltaX*b.opt.mousescrollstep),e=-Math.floor(d.deltaY*b.opt.mousescrollstep));c&&(b.opt.oneaxismousemode&&0==g&&e)&&(g=e,e=0);g&&(b.scrollmom&&b.scrollmom.stop(),b.lastdeltax+=g,b.debounced("mousewheelx",function(){var d=b.lastdeltax;b.lastdeltax=0;b.rail.drag||b.doScrollLeftBy(d)},120));if(e){if(b.opt.nativeparentscrolling&&f&&!b.ispage&&!b.zoomactive)if(0>e){if(b.getScrollTop()>=b.page.maxh)return!0}else if(0>=b.getScrollTop())return!0;
b.scrollmom&&b.scrollmom.stop();b.lastdeltay+=e;b.debounced("mousewheely",function(){var d=b.lastdeltay;b.lastdeltay=0;b.rail.drag||b.doScrollBy(d)},120)}d.stopImmediatePropagation();return d.preventDefault()}var b=this;this.version="3.5.0";this.name="nicescroll";this.me=c;this.opt={doc:e("body"),win:!1};e.extend(this.opt,I);this.opt.snapbackspeed=80;if(h)for(var p in b.opt)"undefined"!=typeof h[p]&&(b.opt[p]=h[p]);this.iddoc=(this.doc=b.opt.doc)&&this.doc[0]?this.doc[0].id||"":"";this.ispage=/BODY|HTML/.test(b.opt.win?
b.opt.win[0].nodeName:this.doc[0].nodeName);this.haswrapper=!1!==b.opt.win;this.win=b.opt.win||(this.ispage?e(window):this.doc);this.docscroll=this.ispage&&!this.haswrapper?e(window):this.win;this.body=e("body");this.iframe=this.isfixed=this.viewport=!1;this.isiframe="IFRAME"==this.doc[0].nodeName&&"IFRAME"==this.win[0].nodeName;this.istextarea="TEXTAREA"==this.win[0].nodeName;this.forcescreen=!1;this.canshowonmouseevent="scroll"!=b.opt.autohidemode;this.page=this.view=this.onzoomout=this.onzoomin=
this.onscrollcancel=this.onscrollend=this.onscrollstart=this.onclick=this.ongesturezoom=this.onkeypress=this.onmousewheel=this.onmousemove=this.onmouseup=this.onmousedown=!1;this.scroll={x:0,y:0};this.scrollratio={x:0,y:0};this.cursorheight=20;this.scrollvaluemax=0;this.observerremover=this.observer=this.scrollmom=this.scrollrunning=this.checkrtlmode=!1;do this.id="ascrail"+M++;while(document.getElementById(this.id));this.hasmousefocus=this.hasfocus=this.zoomactive=this.zoom=this.selectiondrag=this.cursorfreezed=
this.cursor=this.rail=!1;this.visibility=!0;this.hidden=this.locked=!1;this.cursoractive=!0;this.overflowx=b.opt.overflowx;this.overflowy=b.opt.overflowy;this.nativescrollingarea=!1;this.checkarea=0;this.events=[];this.saved={};this.delaylist={};this.synclist={};this.lastdeltay=this.lastdeltax=0;this.detected=P();var g=e.extend({},this.detected);this.ishwscroll=(this.canhwscroll=g.hastransform&&b.opt.hwacceleration)&&b.haswrapper;this.istouchcapable=!1;g.cantouch&&(g.ischrome&&!g.isios&&!g.isandroid)&&
(this.istouchcapable=!0,g.cantouch=!1);g.cantouch&&(g.ismozilla&&!g.isios&&!g.isandroid)&&(this.istouchcapable=!0,g.cantouch=!1);b.opt.enablemouselockapi||(g.hasmousecapture=!1,g.haspointerlock=!1);this.delayed=function(d,c,f,g){var e=b.delaylist[d],k=(new Date).getTime();if(!g&&e&&e.tt)return!1;e&&e.tt&&clearTimeout(e.tt);if(e&&e.last+f>k&&!e.tt)b.delaylist[d]={last:k+f,tt:setTimeout(function(){b.delaylist[d].tt=0;c.call()},f)};else if(!e||!e.tt)b.delaylist[d]={last:k,tt:0},setTimeout(function(){c.call()},
0)};this.debounced=function(d,c,f){var g=b.delaylist[d];(new Date).getTime();b.delaylist[d]=c;g||setTimeout(function(){var c=b.delaylist[d];b.delaylist[d]=!1;c.call()},f)};this.synched=function(d,c){b.synclist[d]=c;(function(){b.onsync||(v(function(){b.onsync=!1;for(d in b.synclist){var c=b.synclist[d];c&&c.call(b);b.synclist[d]=!1}}),b.onsync=!0)})();return d};this.unsynched=function(d){b.synclist[d]&&(b.synclist[d]=!1)};this.css=function(d,c){for(var f in c)b.saved.css.push([d,f,d.css(f)]),d.css(f,
c[f])};this.scrollTop=function(d){return"undefined"==typeof d?b.getScrollTop():b.setScrollTop(d)};this.scrollLeft=function(d){return"undefined"==typeof d?b.getScrollLeft():b.setScrollLeft(d)};BezierClass=function(b,c,f,g,e,k,l){this.st=b;this.ed=c;this.spd=f;this.p1=g||0;this.p2=e||1;this.p3=k||0;this.p4=l||1;this.ts=(new Date).getTime();this.df=this.ed-this.st};BezierClass.prototype={B2:function(b){return 3*b*b*(1-b)},B3:function(b){return 3*b*(1-b)*(1-b)},B4:function(b){return(1-b)*(1-b)*(1-b)},
getNow:function(){var b=1-((new Date).getTime()-this.ts)/this.spd,c=this.B2(b)+this.B3(b)+this.B4(b);return 0>b?this.ed:this.st+Math.round(this.df*c)},update:function(b,c){this.st=this.getNow();this.ed=b;this.spd=c;this.ts=(new Date).getTime();this.df=this.ed-this.st;return this}};if(this.ishwscroll){this.doc.translate={x:0,y:0,tx:"0px",ty:"0px"};g.hastranslate3d&&g.isios&&this.doc.css("-webkit-backface-visibility","hidden");var s=function(){var d=b.doc.css(g.trstyle);return d&&"matrix"==d.substr(0,
6)?d.replace(/^.*\((.*)\)$/g,"$1").replace(/px/g,"").split(/, +/):!1};this.getScrollTop=function(d){if(!d){if(d=s())return 16==d.length?-d[13]:-d[5];if(b.timerscroll&&b.timerscroll.bz)return b.timerscroll.bz.getNow()}return b.doc.translate.y};this.getScrollLeft=function(d){if(!d){if(d=s())return 16==d.length?-d[12]:-d[4];if(b.timerscroll&&b.timerscroll.bh)return b.timerscroll.bh.getNow()}return b.doc.translate.x};this.notifyScrollEvent=document.createEvent?function(b){var c=document.createEvent("UIEvents");
c.initUIEvent("scroll",!1,!0,window,1);b.dispatchEvent(c)}:document.fireEvent?function(b){var c=document.createEventObject();b.fireEvent("onscroll");c.cancelBubble=!0}:function(b,c){};g.hastranslate3d&&b.opt.enabletranslate3d?(this.setScrollTop=function(d,c){b.doc.translate.y=d;b.doc.translate.ty=-1*d+"px";b.doc.css(g.trstyle,"translate3d("+b.doc.translate.tx+","+b.doc.translate.ty+",0px)");c||b.notifyScrollEvent(b.win[0])},this.setScrollLeft=function(d,c){b.doc.translate.x=d;b.doc.translate.tx=-1*
d+"px";b.doc.css(g.trstyle,"translate3d("+b.doc.translate.tx+","+b.doc.translate.ty+",0px)");c||b.notifyScrollEvent(b.win[0])}):(this.setScrollTop=function(d,c){b.doc.translate.y=d;b.doc.translate.ty=-1*d+"px";b.doc.css(g.trstyle,"translate("+b.doc.translate.tx+","+b.doc.translate.ty+")");c||b.notifyScrollEvent(b.win[0])},this.setScrollLeft=function(d,c){b.doc.translate.x=d;b.doc.translate.tx=-1*d+"px";b.doc.css(g.trstyle,"translate("+b.doc.translate.tx+","+b.doc.translate.ty+")");c||b.notifyScrollEvent(b.win[0])})}else this.getScrollTop=
function(){return b.docscroll.scrollTop()},this.setScrollTop=function(d){return b.docscroll.scrollTop(d)},this.getScrollLeft=function(){return b.docscroll.scrollLeft()},this.setScrollLeft=function(d){return b.docscroll.scrollLeft(d)};this.getTarget=function(b){return!b?!1:b.target?b.target:b.srcElement?b.srcElement:!1};this.hasParent=function(b,c){if(!b)return!1;for(var f=b.target||b.srcElement||b||!1;f&&f.id!=c;)f=f.parentNode||!1;return!1!==f};var u={thin:1,medium:3,thick:5};this.getOffset=function(){if(b.isfixed)return{top:parseFloat(b.win.css("top")),
left:parseFloat(b.win.css("left"))};if(!b.viewport)return b.win.offset();var d=b.win.offset(),c=b.viewport.offset();return{top:d.top-c.top+b.viewport.scrollTop(),left:d.left-c.left+b.viewport.scrollLeft()}};this.updateScrollBar=function(d){if(b.ishwscroll)b.rail.css({height:b.win.innerHeight()}),b.railh&&b.railh.css({width:b.win.innerWidth()});else{var c=b.getOffset(),f=c.top,g=c.left,f=f+l(b.win,"border-top-width",!0);b.win.outerWidth();b.win.innerWidth();var g=g+(b.rail.align?b.win.outerWidth()-
l(b.win,"border-right-width")-b.rail.width:l(b.win,"border-left-width")),e=b.opt.railoffset;e&&(e.top&&(f+=e.top),b.rail.align&&e.left&&(g+=e.left));b.locked||b.rail.css({top:f,left:g,height:d?d.h:b.win.innerHeight()});b.zoom&&b.zoom.css({top:f+1,left:1==b.rail.align?g-20:g+b.rail.width+4});b.railh&&!b.locked&&(f=c.top,g=c.left,d=b.railh.align?f+l(b.win,"border-top-width",!0)+b.win.innerHeight()-b.railh.height:f+l(b.win,"border-top-width",!0),g+=l(b.win,"border-left-width"),b.railh.css({top:d,left:g,
width:b.railh.width}))}};this.doRailClick=function(d,c,f){var g;b.locked||(b.cancelEvent(d),c?(c=f?b.doScrollLeft:b.doScrollTop,g=f?(d.pageX-b.railh.offset().left-b.cursorwidth/2)*b.scrollratio.x:(d.pageY-b.rail.offset().top-b.cursorheight/2)*b.scrollratio.y,c(g)):(c=f?b.doScrollLeftBy:b.doScrollBy,g=f?b.scroll.x:b.scroll.y,d=f?d.pageX-b.railh.offset().left:d.pageY-b.rail.offset().top,f=f?b.view.w:b.view.h,g>=d?c(f):c(-f)))};b.hasanimationframe=v;b.hascancelanimationframe=w;b.hasanimationframe?b.hascancelanimationframe||
(w=function(){b.cancelAnimationFrame=!0}):(v=function(b){return setTimeout(b,15-Math.floor(+new Date/1E3)%16)},w=clearInterval);this.init=function(){b.saved.css=[];if(g.isie7mobile||g.isoperamini)return!0;g.hasmstouch&&b.css(b.ispage?e("html"):b.win,{"-ms-touch-action":"none"});b.zindex="auto";b.zindex=!b.ispage&&"auto"==b.opt.zindex?k()||"auto":b.opt.zindex;!b.ispage&&"auto"!=b.zindex&&b.zindex>y&&(y=b.zindex);b.isie&&(0==b.zindex&&"auto"==b.opt.zindex)&&(b.zindex="auto");if(!b.ispage||!g.cantouch&&
!g.isieold&&!g.isie9mobile){var d=b.docscroll;b.ispage&&(d=b.haswrapper?b.win:b.doc);g.isie9mobile||b.css(d,{"overflow-y":"hidden"});b.ispage&&g.isie7&&("BODY"==b.doc[0].nodeName?b.css(e("html"),{"overflow-y":"hidden"}):"HTML"==b.doc[0].nodeName&&b.css(e("body"),{"overflow-y":"hidden"}));g.isios&&(!b.ispage&&!b.haswrapper)&&b.css(e("body"),{"-webkit-overflow-scrolling":"touch"});var c=e(document.createElement("div"));c.css({position:"relative",top:0,"float":"right",width:b.opt.cursorwidth,height:"0px",
"background-color":b.opt.cursorcolor,border:b.opt.cursorborder,"background-clip":"padding-box","-webkit-border-radius":b.opt.cursorborderradius,"-moz-border-radius":b.opt.cursorborderradius,"border-radius":b.opt.cursorborderradius});c.hborder=parseFloat(c.outerHeight()-c.innerHeight());b.cursor=c;var f=e(document.createElement("div"));f.attr("id",b.id);f.addClass("nicescroll-rails");var l,h,x=["left","right"],q;for(q in x)h=x[q],(l=b.opt.railpadding[h])?f.css("padding-"+h,l+"px"):b.opt.railpadding[h]=
0;f.append(c);f.width=Math.max(parseFloat(b.opt.cursorwidth),c.outerWidth())+b.opt.railpadding.left+b.opt.railpadding.right;f.css({width:f.width+"px",zIndex:b.zindex,background:b.opt.background,cursor:"default"});f.visibility=!0;f.scrollable=!0;f.align="left"==b.opt.railalign?0:1;b.rail=f;c=b.rail.drag=!1;b.opt.boxzoom&&(!b.ispage&&!g.isieold)&&(c=document.createElement("div"),b.bind(c,"click",b.doZoom),b.zoom=e(c),b.zoom.css({cursor:"pointer","z-index":b.zindex,backgroundImage:"url("+N+"zoomico.png)",
height:18,width:18,backgroundPosition:"0px 0px"}),b.opt.dblclickzoom&&b.bind(b.win,"dblclick",b.doZoom),g.cantouch&&b.opt.gesturezoom&&(b.ongesturezoom=function(d){1.5<d.scale&&b.doZoomIn(d);0.8>d.scale&&b.doZoomOut(d);return b.cancelEvent(d)},b.bind(b.win,"gestureend",b.ongesturezoom)));b.railh=!1;if(b.opt.horizrailenabled){b.css(d,{"overflow-x":"hidden"});c=e(document.createElement("div"));c.css({position:"relative",top:0,height:b.opt.cursorwidth,width:"0px","background-color":b.opt.cursorcolor,
border:b.opt.cursorborder,"background-clip":"padding-box","-webkit-border-radius":b.opt.cursorborderradius,"-moz-border-radius":b.opt.cursorborderradius,"border-radius":b.opt.cursorborderradius});c.wborder=parseFloat(c.outerWidth()-c.innerWidth());b.cursorh=c;var m=e(document.createElement("div"));m.attr("id",b.id+"-hr");m.addClass("nicescroll-rails");m.height=Math.max(parseFloat(b.opt.cursorwidth),c.outerHeight());m.css({height:m.height+"px",zIndex:b.zindex,background:b.opt.background});m.append(c);
m.visibility=!0;m.scrollable=!0;m.align="top"==b.opt.railvalign?0:1;b.railh=m;b.railh.drag=!1}b.ispage?(f.css({position:"fixed",top:"0px",height:"100%"}),f.align?f.css({right:"0px"}):f.css({left:"0px"}),b.body.append(f),b.railh&&(m.css({position:"fixed",left:"0px",width:"100%"}),m.align?m.css({bottom:"0px"}):m.css({top:"0px"}),b.body.append(m))):(b.ishwscroll?("static"==b.win.css("position")&&b.css(b.win,{position:"relative"}),d="HTML"==b.win[0].nodeName?b.body:b.win,b.zoom&&(b.zoom.css({position:"absolute",
top:1,right:0,"margin-right":f.width+4}),d.append(b.zoom)),f.css({position:"absolute",top:0}),f.align?f.css({right:0}):f.css({left:0}),d.append(f),m&&(m.css({position:"absolute",left:0,bottom:0}),m.align?m.css({bottom:0}):m.css({top:0}),d.append(m))):(b.isfixed="fixed"==b.win.css("position"),d=b.isfixed?"fixed":"absolute",b.isfixed||(b.viewport=b.getViewport(b.win[0])),b.viewport&&(b.body=b.viewport,!1==/fixed|relative|absolute/.test(b.viewport.css("position"))&&b.css(b.viewport,{position:"relative"})),
f.css({position:d}),b.zoom&&b.zoom.css({position:d}),b.updateScrollBar(),b.body.append(f),b.zoom&&b.body.append(b.zoom),b.railh&&(m.css({position:d}),b.body.append(m))),g.isios&&b.css(b.win,{"-webkit-tap-highlight-color":"rgba(0,0,0,0)","-webkit-touch-callout":"none"}),g.isie&&b.opt.disableoutline&&b.win.attr("hideFocus","true"),g.iswebkit&&b.opt.disableoutline&&b.win.css({outline:"none"}));!1===b.opt.autohidemode?(b.autohidedom=!1,b.rail.css({opacity:b.opt.cursoropacitymax}),b.railh&&b.railh.css({opacity:b.opt.cursoropacitymax})):
!0===b.opt.autohidemode||"leave"===b.opt.autohidemode?(b.autohidedom=e().add(b.rail),g.isie8&&(b.autohidedom=b.autohidedom.add(b.cursor)),b.railh&&(b.autohidedom=b.autohidedom.add(b.railh)),b.railh&&g.isie8&&(b.autohidedom=b.autohidedom.add(b.cursorh))):"scroll"==b.opt.autohidemode?(b.autohidedom=e().add(b.rail),b.railh&&(b.autohidedom=b.autohidedom.add(b.railh))):"cursor"==b.opt.autohidemode?(b.autohidedom=e().add(b.cursor),b.railh&&(b.autohidedom=b.autohidedom.add(b.cursorh))):"hidden"==b.opt.autohidemode&&
(b.autohidedom=!1,b.hide(),b.locked=!1);if(g.isie9mobile)b.scrollmom=new J(b),b.onmangotouch=function(d){d=b.getScrollTop();var c=b.getScrollLeft();if(d==b.scrollmom.lastscrolly&&c==b.scrollmom.lastscrollx)return!0;var f=d-b.mangotouch.sy,g=c-b.mangotouch.sx;if(0!=Math.round(Math.sqrt(Math.pow(g,2)+Math.pow(f,2)))){var n=0>f?-1:1,e=0>g?-1:1,k=+new Date;b.mangotouch.lazy&&clearTimeout(b.mangotouch.lazy);80<k-b.mangotouch.tm||b.mangotouch.dry!=n||b.mangotouch.drx!=e?(b.scrollmom.stop(),b.scrollmom.reset(c,
d),b.mangotouch.sy=d,b.mangotouch.ly=d,b.mangotouch.sx=c,b.mangotouch.lx=c,b.mangotouch.dry=n,b.mangotouch.drx=e,b.mangotouch.tm=k):(b.scrollmom.stop(),b.scrollmom.update(b.mangotouch.sx-g,b.mangotouch.sy-f),b.mangotouch.tm=k,f=Math.max(Math.abs(b.mangotouch.ly-d),Math.abs(b.mangotouch.lx-c)),b.mangotouch.ly=d,b.mangotouch.lx=c,2<f&&(b.mangotouch.lazy=setTimeout(function(){b.mangotouch.lazy=!1;b.mangotouch.dry=0;b.mangotouch.drx=0;b.mangotouch.tm=0;b.scrollmom.doMomentum(30)},100)))}},f=b.getScrollTop(),
m=b.getScrollLeft(),b.mangotouch={sy:f,ly:f,dry:0,sx:m,lx:m,drx:0,lazy:!1,tm:0},b.bind(b.docscroll,"scroll",b.onmangotouch);else{if(g.cantouch||b.istouchcapable||b.opt.touchbehavior||g.hasmstouch){b.scrollmom=new J(b);b.ontouchstart=function(d){if(d.pointerType&&2!=d.pointerType)return!1;if(!b.locked){if(g.hasmstouch)for(var c=d.target?d.target:!1;c;){var f=e(c).getNiceScroll();if(0<f.length&&f[0].me==b.me)break;if(0<f.length)return!1;if("DIV"==c.nodeName&&c.id==b.id)break;c=c.parentNode?c.parentNode:
!1}b.cancelScroll();if((c=b.getTarget(d))&&/INPUT/i.test(c.nodeName)&&/range/i.test(c.type))return b.stopPropagation(d);!("clientX"in d)&&"changedTouches"in d&&(d.clientX=d.changedTouches[0].clientX,d.clientY=d.changedTouches[0].clientY);b.forcescreen&&(f=d,d={original:d.original?d.original:d},d.clientX=f.screenX,d.clientY=f.screenY);b.rail.drag={x:d.clientX,y:d.clientY,sx:b.scroll.x,sy:b.scroll.y,st:b.getScrollTop(),sl:b.getScrollLeft(),pt:2,dl:!1};if(b.ispage||!b.opt.directionlockdeadzone)b.rail.drag.dl=
"f";else{var f=e(window).width(),n=e(window).height(),k=Math.max(document.body.scrollWidth,document.documentElement.scrollWidth),l=Math.max(document.body.scrollHeight,document.documentElement.scrollHeight),n=Math.max(0,l-n),f=Math.max(0,k-f);b.rail.drag.ck=!b.rail.scrollable&&b.railh.scrollable?0<n?"v":!1:b.rail.scrollable&&!b.railh.scrollable?0<f?"h":!1:!1;b.rail.drag.ck||(b.rail.drag.dl="f")}b.opt.touchbehavior&&(b.isiframe&&g.isie)&&(f=b.win.position(),b.rail.drag.x+=f.left,b.rail.drag.y+=f.top);
b.hasmoving=!1;b.lastmouseup=!1;b.scrollmom.reset(d.clientX,d.clientY);if(!g.cantouch&&!this.istouchcapable&&!g.hasmstouch){if(!c||!/INPUT|SELECT|TEXTAREA/i.test(c.nodeName))return!b.ispage&&g.hasmousecapture&&c.setCapture(),b.opt.touchbehavior?b.cancelEvent(d):b.stopPropagation(d);/SUBMIT|CANCEL|BUTTON/i.test(e(c).attr("type"))&&(pc={tg:c,click:!1},b.preventclick=pc)}}};b.ontouchend=function(d){if(d.pointerType&&2!=d.pointerType)return!1;if(b.rail.drag&&2==b.rail.drag.pt&&(b.scrollmom.doMomentum(),
b.rail.drag=!1,b.hasmoving&&(b.hasmoving=!1,b.lastmouseup=!0,b.hideCursor(),g.hasmousecapture&&document.releaseCapture(),!g.cantouch)))return b.cancelEvent(d)};var t=b.opt.touchbehavior&&b.isiframe&&!g.hasmousecapture;b.ontouchmove=function(d,c){if(d.pointerType&&2!=d.pointerType)return!1;if(b.rail.drag&&2==b.rail.drag.pt){if(g.cantouch&&"undefined"==typeof d.original)return!0;b.hasmoving=!0;b.preventclick&&!b.preventclick.click&&(b.preventclick.click=b.preventclick.tg.onclick||!1,b.preventclick.tg.onclick=
b.onpreventclick);d=e.extend({original:d},d);"changedTouches"in d&&(d.clientX=d.changedTouches[0].clientX,d.clientY=d.changedTouches[0].clientY);if(b.forcescreen){var f=d;d={original:d.original?d.original:d};d.clientX=f.screenX;d.clientY=f.screenY}f=ofy=0;if(t&&!c){var n=b.win.position(),f=-n.left;ofy=-n.top}var k=d.clientY+ofy,n=k-b.rail.drag.y,l=d.clientX+f,h=l-b.rail.drag.x,r=b.rail.drag.st-n;b.ishwscroll&&b.opt.bouncescroll?0>r?r=Math.round(r/2):r>b.page.maxh&&(r=b.page.maxh+Math.round((r-b.page.maxh)/
2)):(0>r&&(k=r=0),r>b.page.maxh&&(r=b.page.maxh,k=0));if(b.railh&&b.railh.scrollable){var m=b.rail.drag.sl-h;b.ishwscroll&&b.opt.bouncescroll?0>m?m=Math.round(m/2):m>b.page.maxw&&(m=b.page.maxw+Math.round((m-b.page.maxw)/2)):(0>m&&(l=m=0),m>b.page.maxw&&(m=b.page.maxw,l=0))}f=!1;if(b.rail.drag.dl)f=!0,"v"==b.rail.drag.dl?m=b.rail.drag.sl:"h"==b.rail.drag.dl&&(r=b.rail.drag.st);else{var n=Math.abs(n),h=Math.abs(h),x=b.opt.directionlockdeadzone;if("v"==b.rail.drag.ck){if(n>x&&h<=0.3*n)return b.rail.drag=
!1,!0;h>x&&(b.rail.drag.dl="f",e("body").scrollTop(e("body").scrollTop()))}else if("h"==b.rail.drag.ck){if(h>x&&n<=0.3*h)return b.rail.drag=!1,!0;n>x&&(b.rail.drag.dl="f",e("body").scrollLeft(e("body").scrollLeft()))}}b.synched("touchmove",function(){b.rail.drag&&2==b.rail.drag.pt&&(b.prepareTransition&&b.prepareTransition(0),b.rail.scrollable&&b.setScrollTop(r),b.scrollmom.update(l,k),b.railh&&b.railh.scrollable?(b.setScrollLeft(m),b.showCursor(r,m)):b.showCursor(r),g.isie10&&document.selection.clear())});
g.ischrome&&b.istouchcapable&&(f=!1);if(f)return b.cancelEvent(d)}}}b.onmousedown=function(d,c){if(!(b.rail.drag&&1!=b.rail.drag.pt)){if(b.locked)return b.cancelEvent(d);b.cancelScroll();b.rail.drag={x:d.clientX,y:d.clientY,sx:b.scroll.x,sy:b.scroll.y,pt:1,hr:!!c};var f=b.getTarget(d);!b.ispage&&g.hasmousecapture&&f.setCapture();b.isiframe&&!g.hasmousecapture&&(b.saved.csspointerevents=b.doc.css("pointer-events"),b.css(b.doc,{"pointer-events":"none"}));return b.cancelEvent(d)}};b.onmouseup=function(d){if(b.rail.drag&&
(g.hasmousecapture&&document.releaseCapture(),b.isiframe&&!g.hasmousecapture&&b.doc.css("pointer-events",b.saved.csspointerevents),1==b.rail.drag.pt))return b.rail.drag=!1,b.cancelEvent(d)};b.onmousemove=function(d){if(b.rail.drag&&1==b.rail.drag.pt){if(g.ischrome&&0==d.which)return b.onmouseup(d);b.cursorfreezed=!0;if(b.rail.drag.hr){b.scroll.x=b.rail.drag.sx+(d.clientX-b.rail.drag.x);0>b.scroll.x&&(b.scroll.x=0);var c=b.scrollvaluemaxw;b.scroll.x>c&&(b.scroll.x=c)}else b.scroll.y=b.rail.drag.sy+
(d.clientY-b.rail.drag.y),0>b.scroll.y&&(b.scroll.y=0),c=b.scrollvaluemax,b.scroll.y>c&&(b.scroll.y=c);b.synched("mousemove",function(){b.rail.drag&&1==b.rail.drag.pt&&(b.showCursor(),b.rail.drag.hr?b.doScrollLeft(Math.round(b.scroll.x*b.scrollratio.x),b.opt.cursordragspeed):b.doScrollTop(Math.round(b.scroll.y*b.scrollratio.y),b.opt.cursordragspeed))});return b.cancelEvent(d)}};if(g.cantouch||b.opt.touchbehavior)b.onpreventclick=function(d){if(b.preventclick)return b.preventclick.tg.onclick=b.preventclick.click,
b.preventclick=!1,b.cancelEvent(d)},b.bind(b.win,"mousedown",b.ontouchstart),b.onclick=g.isios?!1:function(d){return b.lastmouseup?(b.lastmouseup=!1,b.cancelEvent(d)):!0},b.opt.grabcursorenabled&&g.cursorgrabvalue&&(b.css(b.ispage?b.doc:b.win,{cursor:g.cursorgrabvalue}),b.css(b.rail,{cursor:g.cursorgrabvalue}));else{var p=function(d){if(b.selectiondrag){if(d){var c=b.win.outerHeight();d=d.pageY-b.selectiondrag.top;0<d&&d<c&&(d=0);d>=c&&(d-=c);b.selectiondrag.df=d}0!=b.selectiondrag.df&&(b.doScrollBy(2*
-Math.floor(b.selectiondrag.df/6)),b.debounced("doselectionscroll",function(){p()},50))}};b.hasTextSelected="getSelection"in document?function(){return 0<document.getSelection().rangeCount}:"selection"in document?function(){return"None"!=document.selection.type}:function(){return!1};b.onselectionstart=function(d){b.ispage||(b.selectiondrag=b.win.offset())};b.onselectionend=function(d){b.selectiondrag=!1};b.onselectiondrag=function(d){b.selectiondrag&&b.hasTextSelected()&&b.debounced("selectionscroll",
function(){p(d)},250)}}g.hasmstouch&&(b.css(b.rail,{"-ms-touch-action":"none"}),b.css(b.cursor,{"-ms-touch-action":"none"}),b.bind(b.win,"MSPointerDown",b.ontouchstart),b.bind(document,"MSPointerUp",b.ontouchend),b.bind(document,"MSPointerMove",b.ontouchmove),b.bind(b.cursor,"MSGestureHold",function(b){b.preventDefault()}),b.bind(b.cursor,"contextmenu",function(b){b.preventDefault()}));this.istouchcapable&&(b.bind(b.win,"touchstart",b.ontouchstart),b.bind(document,"touchend",b.ontouchend),b.bind(document,
"touchcancel",b.ontouchend),b.bind(document,"touchmove",b.ontouchmove));b.bind(b.cursor,"mousedown",b.onmousedown);b.bind(b.cursor,"mouseup",b.onmouseup);b.railh&&(b.bind(b.cursorh,"mousedown",function(d){b.onmousedown(d,!0)}),b.bind(b.cursorh,"mouseup",function(d){if(!(b.rail.drag&&2==b.rail.drag.pt))return b.rail.drag=!1,b.hasmoving=!1,b.hideCursor(),g.hasmousecapture&&document.releaseCapture(),b.cancelEvent(d)}));if(b.opt.cursordragontouch||!g.cantouch&&!b.opt.touchbehavior)b.rail.css({cursor:"default"}),
b.railh&&b.railh.css({cursor:"default"}),b.jqbind(b.rail,"mouseenter",function(){b.canshowonmouseevent&&b.showCursor();b.rail.active=!0}),b.jqbind(b.rail,"mouseleave",function(){b.rail.active=!1;b.rail.drag||b.hideCursor()}),b.opt.sensitiverail&&(b.bind(b.rail,"click",function(d){b.doRailClick(d,!1,!1)}),b.bind(b.rail,"dblclick",function(d){b.doRailClick(d,!0,!1)}),b.bind(b.cursor,"click",function(d){b.cancelEvent(d)}),b.bind(b.cursor,"dblclick",function(d){b.cancelEvent(d)})),b.railh&&(b.jqbind(b.railh,
"mouseenter",function(){b.canshowonmouseevent&&b.showCursor();b.rail.active=!0}),b.jqbind(b.railh,"mouseleave",function(){b.rail.active=!1;b.rail.drag||b.hideCursor()}),b.opt.sensitiverail&&(b.bind(b.railh,"click",function(d){b.doRailClick(d,!1,!0)}),b.bind(b.railh,"dblclick",function(d){b.doRailClick(d,!0,!0)}),b.bind(b.cursorh,"click",function(d){b.cancelEvent(d)}),b.bind(b.cursorh,"dblclick",function(d){b.cancelEvent(d)})));!g.cantouch&&!b.opt.touchbehavior?(b.bind(g.hasmousecapture?b.win:document,
"mouseup",b.onmouseup),b.bind(document,"mousemove",b.onmousemove),b.onclick&&b.bind(document,"click",b.onclick),!b.ispage&&b.opt.enablescrollonselection&&(b.bind(b.win[0],"mousedown",b.onselectionstart),b.bind(document,"mouseup",b.onselectionend),b.bind(b.cursor,"mouseup",b.onselectionend),b.cursorh&&b.bind(b.cursorh,"mouseup",b.onselectionend),b.bind(document,"mousemove",b.onselectiondrag)),b.zoom&&(b.jqbind(b.zoom,"mouseenter",function(){b.canshowonmouseevent&&b.showCursor();b.rail.active=!0}),
b.jqbind(b.zoom,"mouseleave",function(){b.rail.active=!1;b.rail.drag||b.hideCursor()}))):(b.bind(g.hasmousecapture?b.win:document,"mouseup",b.ontouchend),b.bind(document,"mousemove",b.ontouchmove),b.onclick&&b.bind(document,"click",b.onclick),b.opt.cursordragontouch&&(b.bind(b.cursor,"mousedown",b.onmousedown),b.bind(b.cursor,"mousemove",b.onmousemove),b.cursorh&&b.bind(b.cursorh,"mousedown",function(d){b.onmousedown(d,!0)}),b.cursorh&&b.bind(b.cursorh,"mousemove",b.onmousemove)));b.opt.enablemousewheel&&
(b.isiframe||b.bind(g.isie&&b.ispage?document:b.win,"mousewheel",b.onmousewheel),b.bind(b.rail,"mousewheel",b.onmousewheel),b.railh&&b.bind(b.railh,"mousewheel",b.onmousewheelhr));!b.ispage&&(!g.cantouch&&!/HTML|BODY/.test(b.win[0].nodeName))&&(b.win.attr("tabindex")||b.win.attr({tabindex:L++}),b.jqbind(b.win,"focus",function(d){z=b.getTarget(d).id||!0;b.hasfocus=!0;b.canshowonmouseevent&&b.noticeCursor()}),b.jqbind(b.win,"blur",function(d){z=!1;b.hasfocus=!1}),b.jqbind(b.win,"mouseenter",function(d){E=
b.getTarget(d).id||!0;b.hasmousefocus=!0;b.canshowonmouseevent&&b.noticeCursor()}),b.jqbind(b.win,"mouseleave",function(){E=!1;b.hasmousefocus=!1;b.rail.drag||b.hideCursor()}))}b.onkeypress=function(d){if(b.locked&&0==b.page.maxh)return!0;d=d?d:window.e;var c=b.getTarget(d);if(c&&/INPUT|TEXTAREA|SELECT|OPTION/.test(c.nodeName)&&(!c.getAttribute("type")&&!c.type||!/submit|button|cancel/i.tp))return!0;if(b.hasfocus||b.hasmousefocus&&!z||b.ispage&&!z&&!E){c=d.keyCode;if(b.locked&&27!=c)return b.cancelEvent(d);
var f=d.ctrlKey||!1,n=d.shiftKey||!1,g=!1;switch(c){case 38:case 63233:b.doScrollBy(72);g=!0;break;case 40:case 63235:b.doScrollBy(-72);g=!0;break;case 37:case 63232:b.railh&&(f?b.doScrollLeft(0):b.doScrollLeftBy(72),g=!0);break;case 39:case 63234:b.railh&&(f?b.doScrollLeft(b.page.maxw):b.doScrollLeftBy(-72),g=!0);break;case 33:case 63276:b.doScrollBy(b.view.h);g=!0;break;case 34:case 63277:b.doScrollBy(-b.view.h);g=!0;break;case 36:case 63273:b.railh&&f?b.doScrollPos(0,0):b.doScrollTo(0);g=!0;break;
case 35:case 63275:b.railh&&f?b.doScrollPos(b.page.maxw,b.page.maxh):b.doScrollTo(b.page.maxh);g=!0;break;case 32:b.opt.spacebarenabled&&(n?b.doScrollBy(b.view.h):b.doScrollBy(-b.view.h),g=!0);break;case 27:b.zoomactive&&(b.doZoom(),g=!0)}if(g)return b.cancelEvent(d)}};b.opt.enablekeyboard&&b.bind(document,g.isopera&&!g.isopera12?"keypress":"keydown",b.onkeypress);b.bind(window,"resize",b.lazyResize);b.bind(window,"orientationchange",b.lazyResize);b.bind(window,"load",b.lazyResize);if(g.ischrome&&
!b.ispage&&!b.haswrapper){var s=b.win.attr("style"),f=parseFloat(b.win.css("width"))+1;b.win.css("width",f);b.synched("chromefix",function(){b.win.attr("style",s)})}b.onAttributeChange=function(d){b.lazyResize(250)};!b.ispage&&!b.haswrapper&&(!1!==A?(b.observer=new A(function(d){d.forEach(b.onAttributeChange)}),b.observer.observe(b.win[0],{childList:!0,characterData:!1,attributes:!0,subtree:!1}),b.observerremover=new A(function(d){d.forEach(function(d){if(0<d.removedNodes.length)for(var c in d.removedNodes)if(d.removedNodes[c]==
b.win[0])return b.remove()})}),b.observerremover.observe(b.win[0].parentNode,{childList:!0,characterData:!1,attributes:!1,subtree:!1})):(b.bind(b.win,g.isie&&!g.isie9?"propertychange":"DOMAttrModified",b.onAttributeChange),g.isie9&&b.win[0].attachEvent("onpropertychange",b.onAttributeChange),b.bind(b.win,"DOMNodeRemoved",function(d){d.target==b.win[0]&&b.remove()})));!b.ispage&&b.opt.boxzoom&&b.bind(window,"resize",b.resizeZoom);b.istextarea&&b.bind(b.win,"mouseup",b.lazyResize);b.checkrtlmode=!0;
b.lazyResize(30)}if("IFRAME"==this.doc[0].nodeName){var K=function(d){b.iframexd=!1;try{var c="contentDocument"in this?this.contentDocument:this.contentWindow.document}catch(f){b.iframexd=!0,c=!1}if(b.iframexd)return"console"in window&&console.log("NiceScroll error: policy restriced iframe"),!0;b.forcescreen=!0;b.isiframe&&(b.iframe={doc:e(c),html:b.doc.contents().find("html")[0],body:b.doc.contents().find("body")[0]},b.getContentSize=function(){return{w:Math.max(b.iframe.html.scrollWidth,b.iframe.body.scrollWidth),
h:Math.max(b.iframe.html.scrollHeight,b.iframe.body.scrollHeight)}},b.docscroll=e(b.iframe.body));!g.isios&&(b.opt.iframeautoresize&&!b.isiframe)&&(b.win.scrollTop(0),b.doc.height(""),d=Math.max(c.getElementsByTagName("html")[0].scrollHeight,c.body.scrollHeight),b.doc.height(d));b.lazyResize(30);g.isie7&&b.css(e(b.iframe.html),{"overflow-y":"hidden"});b.css(e(b.iframe.body),{"overflow-y":"hidden"});g.isios&&b.haswrapper&&b.css(e(c.body),{"-webkit-transform":"translate3d(0,0,0)"});"contentWindow"in
this?b.bind(this.contentWindow,"scroll",b.onscroll):b.bind(c,"scroll",b.onscroll);b.opt.enablemousewheel&&b.bind(c,"mousewheel",b.onmousewheel);b.opt.enablekeyboard&&b.bind(c,g.isopera?"keypress":"keydown",b.onkeypress);if(g.cantouch||b.opt.touchbehavior)b.bind(c,"mousedown",b.ontouchstart),b.bind(c,"mousemove",function(d){b.ontouchmove(d,!0)}),b.opt.grabcursorenabled&&g.cursorgrabvalue&&b.css(e(c.body),{cursor:g.cursorgrabvalue});b.bind(c,"mouseup",b.ontouchend);b.zoom&&(b.opt.dblclickzoom&&b.bind(c,
"dblclick",b.doZoom),b.ongesturezoom&&b.bind(c,"gestureend",b.ongesturezoom))};this.doc[0].readyState&&"complete"==this.doc[0].readyState&&setTimeout(function(){K.call(b.doc[0],!1)},500);b.bind(this.doc,"load",K)}};this.showCursor=function(d,c){b.cursortimeout&&(clearTimeout(b.cursortimeout),b.cursortimeout=0);if(b.rail){b.autohidedom&&(b.autohidedom.stop().css({opacity:b.opt.cursoropacitymax}),b.cursoractive=!0);if(!b.rail.drag||1!=b.rail.drag.pt)"undefined"!=typeof d&&!1!==d&&(b.scroll.y=Math.round(1*
d/b.scrollratio.y)),"undefined"!=typeof c&&(b.scroll.x=Math.round(1*c/b.scrollratio.x));b.cursor.css({height:b.cursorheight,top:b.scroll.y});b.cursorh&&(!b.rail.align&&b.rail.visibility?b.cursorh.css({width:b.cursorwidth,left:b.scroll.x+b.rail.width}):b.cursorh.css({width:b.cursorwidth,left:b.scroll.x}),b.cursoractive=!0);b.zoom&&b.zoom.stop().css({opacity:b.opt.cursoropacitymax})}};this.hideCursor=function(d){!b.cursortimeout&&(b.rail&&b.autohidedom&&!(b.hasmousefocus&&"leave"==b.opt.autohidemode))&&
(b.cursortimeout=setTimeout(function(){if(!b.rail.active||!b.showonmouseevent)b.autohidedom.stop().animate({opacity:b.opt.cursoropacitymin}),b.zoom&&b.zoom.stop().animate({opacity:b.opt.cursoropacitymin}),b.cursoractive=!1;b.cursortimeout=0},d||b.opt.hidecursordelay))};this.noticeCursor=function(d,c,f){b.showCursor(c,f);b.rail.active||b.hideCursor(d)};this.getContentSize=b.ispage?function(){return{w:Math.max(document.body.scrollWidth,document.documentElement.scrollWidth),h:Math.max(document.body.scrollHeight,
document.documentElement.scrollHeight)}}:b.haswrapper?function(){return{w:b.doc.outerWidth()+parseInt(b.win.css("paddingLeft"))+parseInt(b.win.css("paddingRight")),h:b.doc.outerHeight()+parseInt(b.win.css("paddingTop"))+parseInt(b.win.css("paddingBottom"))}}:function(){return{w:b.docscroll[0].scrollWidth,h:b.docscroll[0].scrollHeight}};this.onResize=function(d,c){if(!b.win)return!1;if(!b.haswrapper&&!b.ispage){if("none"==b.win.css("display"))return b.visibility&&b.hideRail().hideRailHr(),!1;!b.hidden&&
!b.visibility&&b.showRail().showRailHr()}var f=b.page.maxh,g=b.page.maxw,e=b.view.w;b.view={w:b.ispage?b.win.width():parseInt(b.win[0].clientWidth),h:b.ispage?b.win.height():parseInt(b.win[0].clientHeight)};b.page=c?c:b.getContentSize();b.page.maxh=Math.max(0,b.page.h-b.view.h);b.page.maxw=Math.max(0,b.page.w-b.view.w);if(b.page.maxh==f&&b.page.maxw==g&&b.view.w==e){if(b.ispage)return b;f=b.win.offset();if(b.lastposition&&(g=b.lastposition,g.top==f.top&&g.left==f.left))return b;b.lastposition=f}0==
b.page.maxh?(b.hideRail(),b.scrollvaluemax=0,b.scroll.y=0,b.scrollratio.y=0,b.cursorheight=0,b.setScrollTop(0),b.rail.scrollable=!1):b.rail.scrollable=!0;0==b.page.maxw?(b.hideRailHr(),b.scrollvaluemaxw=0,b.scroll.x=0,b.scrollratio.x=0,b.cursorwidth=0,b.setScrollLeft(0),b.railh.scrollable=!1):b.railh.scrollable=!0;b.locked=0==b.page.maxh&&0==b.page.maxw;if(b.locked)return b.ispage||b.updateScrollBar(b.view),!1;!b.hidden&&!b.visibility?b.showRail().showRailHr():!b.hidden&&!b.railh.visibility&&b.showRailHr();
b.istextarea&&(b.win.css("resize")&&"none"!=b.win.css("resize"))&&(b.view.h-=20);b.cursorheight=Math.min(b.view.h,Math.round(b.view.h*(b.view.h/b.page.h)));b.cursorheight=b.opt.cursorfixedheight?b.opt.cursorfixedheight:Math.max(b.opt.cursorminheight,b.cursorheight);b.cursorwidth=Math.min(b.view.w,Math.round(b.view.w*(b.view.w/b.page.w)));b.cursorwidth=b.opt.cursorfixedheight?b.opt.cursorfixedheight:Math.max(b.opt.cursorminheight,b.cursorwidth);b.scrollvaluemax=b.view.h-b.cursorheight-b.cursor.hborder;
b.railh&&(b.railh.width=0<b.page.maxh?b.view.w-b.rail.width:b.view.w,b.scrollvaluemaxw=b.railh.width-b.cursorwidth-b.cursorh.wborder);b.checkrtlmode&&b.railh&&(b.checkrtlmode=!1,b.opt.rtlmode&&0==b.scroll.x&&b.setScrollLeft(b.page.maxw));b.ispage||b.updateScrollBar(b.view);b.scrollratio={x:b.page.maxw/b.scrollvaluemaxw,y:b.page.maxh/b.scrollvaluemax};b.getScrollTop()>b.page.maxh?b.doScrollTop(b.page.maxh):(b.scroll.y=Math.round(b.getScrollTop()*(1/b.scrollratio.y)),b.scroll.x=Math.round(b.getScrollLeft()*
(1/b.scrollratio.x)),b.cursoractive&&b.noticeCursor());b.scroll.y&&0==b.getScrollTop()&&b.doScrollTo(Math.floor(b.scroll.y*b.scrollratio.y));return b};this.resize=b.onResize;this.lazyResize=function(d){d=isNaN(d)?30:d;b.delayed("resize",b.resize,d);return b};this._bind=function(d,c,f,g){b.events.push({e:d,n:c,f:f,b:g,q:!1});d.addEventListener?d.addEventListener(c,f,g||!1):d.attachEvent?d.attachEvent("on"+c,f):d["on"+c]=f};this.jqbind=function(d,c,f){b.events.push({e:d,n:c,f:f,q:!0});e(d).bind(c,f)};
this.bind=function(d,c,f,e){var k="jquery"in d?d[0]:d;"mousewheel"==c?"onwheel"in b.win?b._bind(k,"wheel",f,e||!1):(d="undefined"!=typeof document.onmousewheel?"mousewheel":"DOMMouseScroll",q(k,d,f,e||!1),"DOMMouseScroll"==d&&q(k,"MozMousePixelScroll",f,e||!1)):k.addEventListener?(g.cantouch&&/mouseup|mousedown|mousemove/.test(c)&&b._bind(k,"mousedown"==c?"touchstart":"mouseup"==c?"touchend":"touchmove",function(b){if(b.touches){if(2>b.touches.length){var d=b.touches.length?b.touches[0]:b;d.original=
b;f.call(this,d)}}else b.changedTouches&&(d=b.changedTouches[0],d.original=b,f.call(this,d))},e||!1),b._bind(k,c,f,e||!1),g.cantouch&&"mouseup"==c&&b._bind(k,"touchcancel",f,e||!1)):b._bind(k,c,function(d){if((d=d||window.event||!1)&&d.srcElement)d.target=d.srcElement;"pageY"in d||(d.pageX=d.clientX+document.documentElement.scrollLeft,d.pageY=d.clientY+document.documentElement.scrollTop);return!1===f.call(k,d)||!1===e?b.cancelEvent(d):!0})};this._unbind=function(b,c,f,g){b.removeEventListener?b.removeEventListener(c,
f,g):b.detachEvent?b.detachEvent("on"+c,f):b["on"+c]=!1};this.unbindAll=function(){for(var d=0;d<b.events.length;d++){var c=b.events[d];c.q?c.e.unbind(c.n,c.f):b._unbind(c.e,c.n,c.f,c.b)}};this.cancelEvent=function(b){b=b.original?b.original:b?b:window.event||!1;if(!b)return!1;b.preventDefault&&b.preventDefault();b.stopPropagation&&b.stopPropagation();b.preventManipulation&&b.preventManipulation();b.cancelBubble=!0;b.cancel=!0;return b.returnValue=!1};this.stopPropagation=function(b){b=b.original?
b.original:b?b:window.event||!1;if(!b)return!1;if(b.stopPropagation)return b.stopPropagation();b.cancelBubble&&(b.cancelBubble=!0);return!1};this.showRail=function(){if(0!=b.page.maxh&&(b.ispage||"none"!=b.win.css("display")))b.visibility=!0,b.rail.visibility=!0,b.rail.css("display","block");return b};this.showRailHr=function(){if(!b.railh)return b;if(0!=b.page.maxw&&(b.ispage||"none"!=b.win.css("display")))b.railh.visibility=!0,b.railh.css("display","block");return b};this.hideRail=function(){b.visibility=
!1;b.rail.visibility=!1;b.rail.css("display","none");return b};this.hideRailHr=function(){if(!b.railh)return b;b.railh.visibility=!1;b.railh.css("display","none");return b};this.show=function(){b.hidden=!1;b.locked=!1;return b.showRail().showRailHr()};this.hide=function(){b.hidden=!0;b.locked=!0;return b.hideRail().hideRailHr()};this.toggle=function(){return b.hidden?b.show():b.hide()};this.remove=function(){b.stop();b.cursortimeout&&clearTimeout(b.cursortimeout);b.doZoomOut();b.unbindAll();g.isie9&&
b.win[0].detachEvent("onpropertychange",b.onAttributeChange);!1!==b.observer&&b.observer.disconnect();!1!==b.observerremover&&b.observerremover.disconnect();b.events=null;b.cursor&&b.cursor.remove();b.cursorh&&b.cursorh.remove();b.rail&&b.rail.remove();b.railh&&b.railh.remove();b.zoom&&b.zoom.remove();for(var d=0;d<b.saved.css.length;d++){var c=b.saved.css[d];c[0].css(c[1],"undefined"==typeof c[2]?"":c[2])}b.saved=!1;b.me.data("__nicescroll","");var f=e.nicescroll;f.each(function(d){if(this&&this.id===
b.id){delete f[d];for(var c=++d;c<f.length;c++,d++)f[d]=f[c];f.length--;f.length&&delete f[f.length]}});for(var k in b)b[k]=null,delete b[k];b=null};this.scrollstart=function(d){this.onscrollstart=d;return b};this.scrollend=function(d){this.onscrollend=d;return b};this.scrollcancel=function(d){this.onscrollcancel=d;return b};this.zoomin=function(d){this.onzoomin=d;return b};this.zoomout=function(d){this.onzoomout=d;return b};this.isScrollable=function(b){b=b.target?b.target:b;if("OPTION"==b.nodeName)return!0;
for(;b&&1==b.nodeType&&!/BODY|HTML/.test(b.nodeName);){var c=e(b),c=c.css("overflowY")||c.css("overflowX")||c.css("overflow")||"";if(/scroll|auto/.test(c))return b.clientHeight!=b.scrollHeight;b=b.parentNode?b.parentNode:!1}return!1};this.getViewport=function(b){for(b=b&&b.parentNode?b.parentNode:!1;b&&1==b.nodeType&&!/BODY|HTML/.test(b.nodeName);){var c=e(b);if(/fixed|absolute/.test(c.css("position")))return c;var f=c.css("overflowY")||c.css("overflowX")||c.css("overflow")||"";if(/scroll|auto/.test(f)&&
b.clientHeight!=b.scrollHeight||0<c.getNiceScroll().length)return c;b=b.parentNode?b.parentNode:!1}return!1};this.onmousewheel=function(d){if(b.locked)return b.debounced("checkunlock",b.resize,250),!0;if(b.rail.drag)return b.cancelEvent(d);"auto"==b.opt.oneaxismousemode&&0!=d.deltaX&&(b.opt.oneaxismousemode=!1);if(b.opt.oneaxismousemode&&0==d.deltaX&&!b.rail.scrollable)return b.railh&&b.railh.scrollable?b.onmousewheelhr(d):!0;var c=+new Date,f=!1;b.opt.preservenativescrolling&&b.checkarea+600<c&&
(b.nativescrollingarea=b.isScrollable(d),f=!0);b.checkarea=c;if(b.nativescrollingarea)return!0;if(d=t(d,!1,f))b.checkarea=0;return d};this.onmousewheelhr=function(d){if(b.locked||!b.railh.scrollable)return!0;if(b.rail.drag)return b.cancelEvent(d);var c=+new Date,f=!1;b.opt.preservenativescrolling&&b.checkarea+600<c&&(b.nativescrollingarea=b.isScrollable(d),f=!0);b.checkarea=c;return b.nativescrollingarea?!0:b.locked?b.cancelEvent(d):t(d,!0,f)};this.stop=function(){b.cancelScroll();b.scrollmon&&b.scrollmon.stop();
b.cursorfreezed=!1;b.scroll.y=Math.round(b.getScrollTop()*(1/b.scrollratio.y));b.noticeCursor();return b};this.getTransitionSpeed=function(d){var c=Math.round(10*b.opt.scrollspeed);d=Math.min(c,Math.round(d/20*b.opt.scrollspeed));return 20<d?d:0};b.opt.smoothscroll?b.ishwscroll&&g.hastransition&&b.opt.usetransition?(this.prepareTransition=function(d,c){var f=c?20<d?d:0:b.getTransitionSpeed(d),e=f?g.prefixstyle+"transform "+f+"ms ease-out":"";if(!b.lasttransitionstyle||b.lasttransitionstyle!=e)b.lasttransitionstyle=
e,b.doc.css(g.transitionstyle,e);return f},this.doScrollLeft=function(c,g){var f=b.scrollrunning?b.newscrolly:b.getScrollTop();b.doScrollPos(c,f,g)},this.doScrollTop=function(c,g){var f=b.scrollrunning?b.newscrollx:b.getScrollLeft();b.doScrollPos(f,c,g)},this.doScrollPos=function(c,e,f){var k=b.getScrollTop(),l=b.getScrollLeft();(0>(b.newscrolly-k)*(e-k)||0>(b.newscrollx-l)*(c-l))&&b.cancelScroll();!1==b.opt.bouncescroll&&(0>e?e=0:e>b.page.maxh&&(e=b.page.maxh),0>c?c=0:c>b.page.maxw&&(c=b.page.maxw));
if(b.scrollrunning&&c==b.newscrollx&&e==b.newscrolly)return!1;b.newscrolly=e;b.newscrollx=c;b.newscrollspeed=f||!1;if(b.timer)return!1;b.timer=setTimeout(function(){var f=b.getScrollTop(),k=b.getScrollLeft(),l,h;l=c-k;h=e-f;l=Math.round(Math.sqrt(Math.pow(l,2)+Math.pow(h,2)));l=b.newscrollspeed&&1<b.newscrollspeed?b.newscrollspeed:b.getTransitionSpeed(l);b.newscrollspeed&&1>=b.newscrollspeed&&(l*=b.newscrollspeed);b.prepareTransition(l,!0);b.timerscroll&&b.timerscroll.tm&&clearInterval(b.timerscroll.tm);
0<l&&(!b.scrollrunning&&b.onscrollstart&&b.onscrollstart.call(b,{type:"scrollstart",current:{x:k,y:f},request:{x:c,y:e},end:{x:b.newscrollx,y:b.newscrolly},speed:l}),g.transitionend?b.scrollendtrapped||(b.scrollendtrapped=!0,b.bind(b.doc,g.transitionend,b.onScrollEnd,!1)):(b.scrollendtrapped&&clearTimeout(b.scrollendtrapped),b.scrollendtrapped=setTimeout(b.onScrollEnd,l)),b.timerscroll={bz:new BezierClass(f,b.newscrolly,l,0,0,0.58,1),bh:new BezierClass(k,b.newscrollx,l,0,0,0.58,1)},b.cursorfreezed||
(b.timerscroll.tm=setInterval(function(){b.showCursor(b.getScrollTop(),b.getScrollLeft())},60)));b.synched("doScroll-set",function(){b.timer=0;b.scrollendtrapped&&(b.scrollrunning=!0);b.setScrollTop(b.newscrolly);b.setScrollLeft(b.newscrollx);if(!b.scrollendtrapped)b.onScrollEnd()})},50)},this.cancelScroll=function(){if(!b.scrollendtrapped)return!0;var c=b.getScrollTop(),e=b.getScrollLeft();b.scrollrunning=!1;g.transitionend||clearTimeout(g.transitionend);b.scrollendtrapped=!1;b._unbind(b.doc,g.transitionend,
b.onScrollEnd);b.prepareTransition(0);b.setScrollTop(c);b.railh&&b.setScrollLeft(e);b.timerscroll&&b.timerscroll.tm&&clearInterval(b.timerscroll.tm);b.timerscroll=!1;b.cursorfreezed=!1;b.showCursor(c,e);return b},this.onScrollEnd=function(){b.scrollendtrapped&&b._unbind(b.doc,g.transitionend,b.onScrollEnd);b.scrollendtrapped=!1;b.prepareTransition(0);b.timerscroll&&b.timerscroll.tm&&clearInterval(b.timerscroll.tm);b.timerscroll=!1;var c=b.getScrollTop(),e=b.getScrollLeft();b.setScrollTop(c);b.railh&&
b.setScrollLeft(e);b.noticeCursor(!1,c,e);b.cursorfreezed=!1;0>c?c=0:c>b.page.maxh&&(c=b.page.maxh);0>e?e=0:e>b.page.maxw&&(e=b.page.maxw);if(c!=b.newscrolly||e!=b.newscrollx)return b.doScrollPos(e,c,b.opt.snapbackspeed);b.onscrollend&&b.scrollrunning&&b.onscrollend.call(b,{type:"scrollend",current:{x:e,y:c},end:{x:b.newscrollx,y:b.newscrolly}});b.scrollrunning=!1}):(this.doScrollLeft=function(c,g){var f=b.scrollrunning?b.newscrolly:b.getScrollTop();b.doScrollPos(c,f,g)},this.doScrollTop=function(c,
g){var f=b.scrollrunning?b.newscrollx:b.getScrollLeft();b.doScrollPos(f,c,g)},this.doScrollPos=function(c,g,f){function e(){if(b.cancelAnimationFrame)return!0;b.scrollrunning=!0;if(p=1-p)return b.timer=v(e)||1;var c=0,d=sy=b.getScrollTop();if(b.dst.ay){var d=b.bzscroll?b.dst.py+b.bzscroll.getNow()*b.dst.ay:b.newscrolly,f=d-sy;if(0>f&&d<b.newscrolly||0<f&&d>b.newscrolly)d=b.newscrolly;b.setScrollTop(d);d==b.newscrolly&&(c=1)}else c=1;var g=sx=b.getScrollLeft();if(b.dst.ax){g=b.bzscroll?b.dst.px+b.bzscroll.getNow()*
b.dst.ax:b.newscrollx;f=g-sx;if(0>f&&g<b.newscrollx||0<f&&g>b.newscrollx)g=b.newscrollx;b.setScrollLeft(g);g==b.newscrollx&&(c+=1)}else c+=1;2==c?(b.timer=0,b.cursorfreezed=!1,b.bzscroll=!1,b.scrollrunning=!1,0>d?d=0:d>b.page.maxh&&(d=b.page.maxh),0>g?g=0:g>b.page.maxw&&(g=b.page.maxw),g!=b.newscrollx||d!=b.newscrolly?b.doScrollPos(g,d):b.onscrollend&&b.onscrollend.call(b,{type:"scrollend",current:{x:sx,y:sy},end:{x:b.newscrollx,y:b.newscrolly}})):b.timer=v(e)||1}g="undefined"==typeof g||!1===g?b.getScrollTop(!0):
g;if(b.timer&&b.newscrolly==g&&b.newscrollx==c)return!0;b.timer&&w(b.timer);b.timer=0;var k=b.getScrollTop(),l=b.getScrollLeft();(0>(b.newscrolly-k)*(g-k)||0>(b.newscrollx-l)*(c-l))&&b.cancelScroll();b.newscrolly=g;b.newscrollx=c;if(!b.bouncescroll||!b.rail.visibility)0>b.newscrolly?b.newscrolly=0:b.newscrolly>b.page.maxh&&(b.newscrolly=b.page.maxh);if(!b.bouncescroll||!b.railh.visibility)0>b.newscrollx?b.newscrollx=0:b.newscrollx>b.page.maxw&&(b.newscrollx=b.page.maxw);b.dst={};b.dst.x=c-l;b.dst.y=
g-k;b.dst.px=l;b.dst.py=k;var h=Math.round(Math.sqrt(Math.pow(b.dst.x,2)+Math.pow(b.dst.y,2)));b.dst.ax=b.dst.x/h;b.dst.ay=b.dst.y/h;var m=0,q=h;0==b.dst.x?(m=k,q=g,b.dst.ay=1,b.dst.py=0):0==b.dst.y&&(m=l,q=c,b.dst.ax=1,b.dst.px=0);h=b.getTransitionSpeed(h);f&&1>=f&&(h*=f);b.bzscroll=0<h?b.bzscroll?b.bzscroll.update(q,h):new BezierClass(m,q,h,0,1,0,1):!1;if(!b.timer){(k==b.page.maxh&&g>=b.page.maxh||l==b.page.maxw&&c>=b.page.maxw)&&b.checkContentSize();var p=1;b.cancelAnimationFrame=!1;b.timer=1;
b.onscrollstart&&!b.scrollrunning&&b.onscrollstart.call(b,{type:"scrollstart",current:{x:l,y:k},request:{x:c,y:g},end:{x:b.newscrollx,y:b.newscrolly},speed:h});e();(k==b.page.maxh&&g>=k||l==b.page.maxw&&c>=l)&&b.checkContentSize();b.noticeCursor()}},this.cancelScroll=function(){b.timer&&w(b.timer);b.timer=0;b.bzscroll=!1;b.scrollrunning=!1;return b}):(this.doScrollLeft=function(c,g){var f=b.getScrollTop();b.doScrollPos(c,f,g)},this.doScrollTop=function(c,g){var f=b.getScrollLeft();b.doScrollPos(f,
c,g)},this.doScrollPos=function(c,g,f){var e=c>b.page.maxw?b.page.maxw:c;0>e&&(e=0);var k=g>b.page.maxh?b.page.maxh:g;0>k&&(k=0);b.synched("scroll",function(){b.setScrollTop(k);b.setScrollLeft(e)})},this.cancelScroll=function(){});this.doScrollBy=function(c,g){var f=0,f=g?Math.floor((b.scroll.y-c)*b.scrollratio.y):(b.timer?b.newscrolly:b.getScrollTop(!0))-c;if(b.bouncescroll){var e=Math.round(b.view.h/2);f<-e?f=-e:f>b.page.maxh+e&&(f=b.page.maxh+e)}b.cursorfreezed=!1;py=b.getScrollTop(!0);if(0>f&&
0>=py)return b.noticeCursor();if(f>b.page.maxh&&py>=b.page.maxh)return b.checkContentSize(),b.noticeCursor();b.doScrollTop(f)};this.doScrollLeftBy=function(c,g){var f=0,f=g?Math.floor((b.scroll.x-c)*b.scrollratio.x):(b.timer?b.newscrollx:b.getScrollLeft(!0))-c;if(b.bouncescroll){var e=Math.round(b.view.w/2);f<-e?f=-e:f>b.page.maxw+e&&(f=b.page.maxw+e)}b.cursorfreezed=!1;px=b.getScrollLeft(!0);if(0>f&&0>=px||f>b.page.maxw&&px>=b.page.maxw)return b.noticeCursor();b.doScrollLeft(f)};this.doScrollTo=
function(c,g){g&&Math.round(c*b.scrollratio.y);b.cursorfreezed=!1;b.doScrollTop(c)};this.checkContentSize=function(){var c=b.getContentSize();(c.h!=b.page.h||c.w!=b.page.w)&&b.resize(!1,c)};b.onscroll=function(c){b.rail.drag||b.cursorfreezed||b.synched("scroll",function(){b.scroll.y=Math.round(b.getScrollTop()*(1/b.scrollratio.y));b.railh&&(b.scroll.x=Math.round(b.getScrollLeft()*(1/b.scrollratio.x)));b.noticeCursor()})};b.bind(b.docscroll,"scroll",b.onscroll);this.doZoomIn=function(c){if(!b.zoomactive){b.zoomactive=
!0;b.zoomrestore={style:{}};var k="position top left zIndex backgroundColor marginTop marginBottom marginLeft marginRight".split(" "),f=b.win[0].style,l;for(l in k){var h=k[l];b.zoomrestore.style[h]="undefined"!=typeof f[h]?f[h]:""}b.zoomrestore.style.width=b.win.css("width");b.zoomrestore.style.height=b.win.css("height");b.zoomrestore.padding={w:b.win.outerWidth()-b.win.width(),h:b.win.outerHeight()-b.win.height()};g.isios4&&(b.zoomrestore.scrollTop=e(window).scrollTop(),e(window).scrollTop(0));
b.win.css({position:g.isios4?"absolute":"fixed",top:0,left:0,"z-index":y+100,margin:"0px"});k=b.win.css("backgroundColor");(""==k||/transparent|rgba\(0, 0, 0, 0\)|rgba\(0,0,0,0\)/.test(k))&&b.win.css("backgroundColor","#fff");b.rail.css({"z-index":y+101});b.zoom.css({"z-index":y+102});b.zoom.css("backgroundPosition","0px -18px");b.resizeZoom();b.onzoomin&&b.onzoomin.call(b);return b.cancelEvent(c)}};this.doZoomOut=function(c){if(b.zoomactive)return b.zoomactive=!1,b.win.css("margin",""),b.win.css(b.zoomrestore.style),
g.isios4&&e(window).scrollTop(b.zoomrestore.scrollTop),b.rail.css({"z-index":b.zindex}),b.zoom.css({"z-index":b.zindex}),b.zoomrestore=!1,b.zoom.css("backgroundPosition","0px 0px"),b.onResize(),b.onzoomout&&b.onzoomout.call(b),b.cancelEvent(c)};this.doZoom=function(c){return b.zoomactive?b.doZoomOut(c):b.doZoomIn(c)};this.resizeZoom=function(){if(b.zoomactive){var c=b.getScrollTop();b.win.css({width:e(window).width()-b.zoomrestore.padding.w+"px",height:e(window).height()-b.zoomrestore.padding.h+"px"});
b.onResize();b.setScrollTop(Math.min(b.page.maxh,c))}};this.init();e.nicescroll.push(this)},J=function(e){var c=this;this.nc=e;this.steptime=this.lasttime=this.speedy=this.speedx=this.lasty=this.lastx=0;this.snapy=this.snapx=!1;this.demuly=this.demulx=0;this.lastscrolly=this.lastscrollx=-1;this.timer=this.chky=this.chkx=0;this.time=function(){return+new Date};this.reset=function(e,l){c.stop();var h=c.time();c.steptime=0;c.lasttime=h;c.speedx=0;c.speedy=0;c.lastx=e;c.lasty=l;c.lastscrollx=-1;c.lastscrolly=
-1};this.update=function(e,l){var h=c.time();c.steptime=h-c.lasttime;c.lasttime=h;var h=l-c.lasty,t=e-c.lastx,b=c.nc.getScrollTop(),p=c.nc.getScrollLeft(),b=b+h,p=p+t;c.snapx=0>p||p>c.nc.page.maxw;c.snapy=0>b||b>c.nc.page.maxh;c.speedx=t;c.speedy=h;c.lastx=e;c.lasty=l};this.stop=function(){c.nc.unsynched("domomentum2d");c.timer&&clearTimeout(c.timer);c.timer=0;c.lastscrollx=-1;c.lastscrolly=-1};this.doSnapy=function(e,l){var h=!1;0>l?(l=0,h=!0):l>c.nc.page.maxh&&(l=c.nc.page.maxh,h=!0);0>e?(e=0,h=
!0):e>c.nc.page.maxw&&(e=c.nc.page.maxw,h=!0);h&&c.nc.doScrollPos(e,l,c.nc.opt.snapbackspeed)};this.doMomentum=function(e){var l=c.time(),h=e?l+e:c.lasttime;e=c.nc.getScrollLeft();var t=c.nc.getScrollTop(),b=c.nc.page.maxh,p=c.nc.page.maxw;c.speedx=0<p?Math.min(60,c.speedx):0;c.speedy=0<b?Math.min(60,c.speedy):0;h=h&&60>=l-h;if(0>t||t>b||0>e||e>p)h=!1;e=c.speedx&&h?c.speedx:!1;if(c.speedy&&h&&c.speedy||e){var g=Math.max(16,c.steptime);50<g&&(e=g/50,c.speedx*=e,c.speedy*=e,g=50);c.demulxy=0;c.lastscrollx=
c.nc.getScrollLeft();c.chkx=c.lastscrollx;c.lastscrolly=c.nc.getScrollTop();c.chky=c.lastscrolly;var s=c.lastscrollx,u=c.lastscrolly,d=function(){var e=600<c.time()-l?0.04:0.02;if(c.speedx&&(s=Math.floor(c.lastscrollx-c.speedx*(1-c.demulxy)),c.lastscrollx=s,0>s||s>p))e=0.1;if(c.speedy&&(u=Math.floor(c.lastscrolly-c.speedy*(1-c.demulxy)),c.lastscrolly=u,0>u||u>b))e=0.1;c.demulxy=Math.min(1,c.demulxy+e);c.nc.synched("domomentum2d",function(){c.speedx&&(c.nc.getScrollLeft()!=c.chkx&&c.stop(),c.chkx=
s,c.nc.setScrollLeft(s));c.speedy&&(c.nc.getScrollTop()!=c.chky&&c.stop(),c.chky=u,c.nc.setScrollTop(u));c.timer||(c.nc.hideCursor(),c.doSnapy(s,u))});1>c.demulxy?c.timer=setTimeout(d,g):(c.stop(),c.nc.hideCursor(),c.doSnapy(s,u))};d()}else c.doSnapy(c.nc.getScrollLeft(),c.nc.getScrollTop())}},B=e.fn.scrollTop;e.cssHooks.pageYOffset={get:function(h,c,k){return(c=e.data(h,"__nicescroll")||!1)&&c.ishwscroll?c.getScrollTop():B.call(h)},set:function(h,c){var k=e.data(h,"__nicescroll")||!1;k&&k.ishwscroll?
k.setScrollTop(parseInt(c)):B.call(h,c);return this}};e.fn.scrollTop=function(h){if("undefined"==typeof h){var c=this[0]?e.data(this[0],"__nicescroll")||!1:!1;return c&&c.ishwscroll?c.getScrollTop():B.call(this)}return this.each(function(){var c=e.data(this,"__nicescroll")||!1;c&&c.ishwscroll?c.setScrollTop(parseInt(h)):B.call(e(this),h)})};var C=e.fn.scrollLeft;e.cssHooks.pageXOffset={get:function(h,c,k){return(c=e.data(h,"__nicescroll")||!1)&&c.ishwscroll?c.getScrollLeft():C.call(h)},set:function(h,
c){var k=e.data(h,"__nicescroll")||!1;k&&k.ishwscroll?k.setScrollLeft(parseInt(c)):C.call(h,c);return this}};e.fn.scrollLeft=function(h){if("undefined"==typeof h){var c=this[0]?e.data(this[0],"__nicescroll")||!1:!1;return c&&c.ishwscroll?c.getScrollLeft():C.call(this)}return this.each(function(){var c=e.data(this,"__nicescroll")||!1;c&&c.ishwscroll?c.setScrollLeft(parseInt(h)):C.call(e(this),h)})};var D=function(h){var c=this;this.length=0;this.name="nicescrollarray";this.each=function(e){for(var h=
0,k=0;h<c.length;h++)e.call(c[h],k++);return c};this.push=function(e){c[c.length]=e;c.length++};this.eq=function(e){return c[e]};if(h)for(a=0;a<h.length;a++){var k=e.data(h[a],"__nicescroll")||!1;k&&(this[this.length]=k,this.length++)}return this};(function(e,c,k){for(var l=0;l<c.length;l++)k(e,c[l])})(D.prototype,"show hide toggle onResize resize remove stop doScrollPos".split(" "),function(e,c){e[c]=function(){var e=arguments;return this.each(function(){this[c].apply(this,e)})}});e.fn.getNiceScroll=
function(h){return"undefined"==typeof h?new D(this):this[h]&&e.data(this[h],"__nicescroll")||!1};e.extend(e.expr[":"],{nicescroll:function(h){return e.data(h,"__nicescroll")?!0:!1}});e.fn.niceScroll=function(h,c){"undefined"==typeof c&&("object"==typeof h&&!("jquery"in h))&&(c=h,h=!1);var k=new D;"undefined"==typeof c&&(c={});h&&(c.doc=e(h),c.win=e(this));var l=!("doc"in c);!l&&!("win"in c)&&(c.win=e(this));this.each(function(){var h=e(this).data("__nicescroll")||!1;h||(c.doc=l?e(this):c.doc,h=new Q(c,
e(this)),e(this).data("__nicescroll",h));k.push(h)});return 1==k.length?k[0]:k};window.NiceScroll={getjQuery:function(){return e}};e.nicescroll||(e.nicescroll=new D,e.nicescroll.options=I)})(jQuery);

/* mousetrap v1.4.4 craig.is/killing/mice */
(function(){function s(a,b,c){a.addEventListener?a.addEventListener(b,c,!1):a.attachEvent("on"+b,c)}function z(a){if("keypress"==a.type){var b=String.fromCharCode(a.which);a.shiftKey||(b=b.toLowerCase());return" "==b?"space":b}return k[a.which]?k[a.which]:A[a.which]?A[a.which]:String.fromCharCode(a.which).toLowerCase()}function t(a){a=a||{};var b=!1,c;for(c in p)a[c]?b=!0:p[c]=0;b||(u=!1)}function B(a,b,c,d,e,g){var f,l,h=[],k=c.type;if(!m[a])return[];"keyup"==k&&v(a)&&(b=[a]);for(f=0;f<m[a].length;++f)if(l=
m[a][f],!(!d&&l.seq&&p[l.seq]!=l.level||k!=l.action||("keypress"!=k||c.metaKey||c.ctrlKey)&&b.sort().join(",")!==l.modifiers.sort().join(","))){var n=d&&l.seq==d&&l.level==g;(!d&&l.combo==e||n)&&m[a].splice(f,1);h.push(l)}return h}function I(a){var b=[];a.shiftKey&&b.push("shift");a.altKey&&b.push("alt");a.ctrlKey&&b.push("ctrl");a.metaKey&&b.push("meta");return b}function w(a,b,c){n.stopCallback(b,b.target||b.srcElement,c)||!1!==a(b,c)||(b.preventDefault&&b.preventDefault(),b.stopPropagation&&b.stopPropagation(),
b.returnValue=!1,b.cancelBubble=!0)}function x(a){"number"!==typeof a.which&&(a.which=a.keyCode);var b=z(a);b&&("keyup"==a.type&&y===b?y=!1:n.handleKey(b,I(a),a))}function v(a){return"shift"==a||"ctrl"==a||"alt"==a||"meta"==a}function J(a,b,c,d){function e(b){return function(){u=b;++p[a];clearTimeout(C);C=setTimeout(t,1E3)}}function g(b){w(c,b,a);"keyup"!==d&&(y=z(b));setTimeout(t,10)}for(var f=p[a]=0;f<b.length;++f){var h=f+1===b.length?g:e(d||D(b[f+1]).action);E(b[f],h,d,a,f)}}function D(a,b){var c,
d,e,g=[];c="+"===a?["+"]:a.split("+");for(e=0;e<c.length;++e)d=c[e],F[d]&&(d=F[d]),b&&("keypress"!=b&&G[d])&&(d=G[d],g.push("shift")),v(d)&&g.push(d);c=d;e=b;if(!e){if(!q){q={};for(var f in k)95<f&&112>f||k.hasOwnProperty(f)&&(q[k[f]]=f)}e=q[c]?"keydown":"keypress"}"keypress"==e&&g.length&&(e="keydown");return{key:d,modifiers:g,action:e}}function E(a,b,c,d,e){r[a+":"+c]=b;a=a.replace(/\s+/g," ");var g=a.split(" ");1<g.length?J(a,g,b,c):(c=D(a,c),m[c.key]=m[c.key]||[],B(c.key,c.modifiers,{type:c.action},
d,a,e),m[c.key][d?"unshift":"push"]({callback:b,modifiers:c.modifiers,action:c.action,seq:d,level:e,combo:a}))}for(var k={8:"backspace",9:"tab",13:"enter",16:"shift",17:"ctrl",18:"alt",20:"capslock",27:"esc",32:"space",33:"pageup",34:"pagedown",35:"end",36:"home",37:"left",38:"up",39:"right",40:"down",45:"ins",46:"del",91:"meta",93:"meta",224:"meta"},A={106:"*",107:"+",109:"-",110:".",111:"/",186:";",187:"=",188:",",189:"-",190:".",191:"/",192:"`",219:"[",220:"\\",221:"]",222:"'"},G={"~":"`","!":"1",
"@":"2","#":"3",$:"4","%":"5","^":"6","&":"7","*":"8","(":"9",")":"0",_:"-","+":"=",":":";",'"':"'","<":",",">":".","?":"/","|":"\\"},F={option:"alt",command:"meta","return":"enter",escape:"esc",mod:/Mac|iPod|iPhone|iPad/.test(navigator.platform)?"meta":"ctrl"},q,m={},r={},p={},C,y=!1,H=!1,u=!1,h=1;20>h;++h)k[111+h]="f"+h;for(h=0;9>=h;++h)k[h+96]=h;s(document,"keypress",x);s(document,"keydown",x);s(document,"keyup",x);var n={bind:function(a,b,c){a=a instanceof Array?a:[a];for(var d=0;d<a.length;++d)E(a[d],
b,c);return this},unbind:function(a,b){return n.bind(a,function(){},b)},trigger:function(a,b){if(r[a+":"+b])r[a+":"+b]({},a);return this},reset:function(){m={};r={};return this},stopCallback:function(a,b){return-1<(" "+b.className+" ").indexOf(" mousetrap ")?!1:"INPUT"==b.tagName||"SELECT"==b.tagName||"TEXTAREA"==b.tagName||b.contentEditable&&"true"==b.contentEditable},handleKey:function(a,b,c){var d=B(a,b,c),e;b={};var g=0,f=!1;for(e=0;e<d.length;++e)d[e].seq&&(g=Math.max(g,d[e].level));for(e=0;e<
d.length;++e)d[e].seq?d[e].level==g&&(f=!0,b[d[e].seq]=1,w(d[e].callback,c,d[e].combo)):f||w(d[e].callback,c,d[e].combo);d="keypress"==c.type&&H;c.type!=u||(v(a)||d)||t(b);H=f&&"keydown"==c.type}};window.Mousetrap=n;"function"===typeof define&&define.amd&&define(n)})();
/**
 * jGrowl 1.2.6
 *
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * Written by Stan Lemon <stosh1985@gmail.com>
 * Last updated: 2011.03.27
 *
 * jGrowl is a jQuery plugin implementing unobtrusive userland notifications.  These 
 * notifications function similarly to the Growl Framework available for
 * Mac OS X (http://growl.info).
 *
 * To Do:
 * - Move library settings to containers and allow them to be changed per container
 *
 * Changes in 1.2.6
 * - Fixed js error when a notification is opening and closing at the same time
 * 
 * Changes in 1.2.5
 * - Changed wrapper jGrowl's options usage to "o" instead of $.jGrowl.defaults
 * - Added themeState option to control 'highlight' or 'error' for jQuery UI
 * - Ammended some CSS to provide default positioning for nested usage.
 * - Changed some CSS to be prefixed with jGrowl- to prevent namespacing issues
 * - Added two new options - openDuration and closeDuration to allow 
 *   better control of notification open and close speeds, respectively 
 *   Patch contributed by Jesse Vincet.
 * - Added afterOpen callback.  Patch contributed by Russel Branca.
 *
 * Changes in 1.2.4
 * - Fixed IE bug with the close-all button
 * - Fixed IE bug with the filter CSS attribute (special thanks to gotwic)
 * - Update IE opacity CSS
 * - Changed font sizes to use "em", and only set the base style
 *
 * Changes in 1.2.3
 * - The callbacks no longer use the container as context, instead they use the actual notification
 * - The callbacks now receive the container as a parameter after the options parameter
 * - beforeOpen and beforeClose now check the return value, if it's false - the notification does
 *   not continue.  The open callback will also halt execution if it returns false.
 * - Fixed bug where containers would get confused
 * - Expanded the pause functionality to pause an entire container.
 *
 * Changes in 1.2.2
 * - Notification can now be theme rolled for jQuery UI, special thanks to Jeff Chan!
 *
 * Changes in 1.2.1
 * - Fixed instance where the interval would fire the close method multiple times.
 * - Added CSS to hide from print media
 * - Fixed issue with closer button when div { position: relative } is set
 * - Fixed leaking issue with multiple containers.  Special thanks to Matthew Hanlon!
 *
 * Changes in 1.2.0
 * - Added message pooling to limit the number of messages appearing at a given time.
 * - Closing a notification is now bound to the notification object and triggered by the close button.
 *
 * Changes in 1.1.2
 * - Added iPhone styled example
 * - Fixed possible IE7 bug when determining if the ie6 class shoudl be applied.
 * - Added template for the close button, so that it's content could be customized.
 *
 * Changes in 1.1.1
 * - Fixed CSS styling bug for ie6 caused by a mispelling
 * - Changes height restriction on default notifications to min-height
 * - Added skinned examples using a variety of images
 * - Added the ability to customize the content of the [close all] box
 * - Added jTweet, an example of using jGrowl + Twitter
 *
 * Changes in 1.1.0
 * - Multiple container and instances.
 * - Standard $.jGrowl() now wraps $.fn.jGrowl() by first establishing a generic jGrowl container.
 * - Instance methods of a jGrowl container can be called by $.fn.jGrowl(methodName)
 * - Added glue preferenced, which allows notifications to be inserted before or after nodes in the container
 * - Added new log callback which is called before anything is done for the notification
 * - Corner's attribute are now applied on an individual notification basis.
 *
 * Changes in 1.0.4
 * - Various CSS fixes so that jGrowl renders correctly in IE6.
 *
 * Changes in 1.0.3
 * - Fixed bug with options persisting across notifications
 * - Fixed theme application bug
 * - Simplified some selectors and manipulations.
 * - Added beforeOpen and beforeClose callbacks
 * - Reorganized some lines of code to be more readable
 * - Removed unnecessary this.defaults context
 * - If corners plugin is present, it's now customizable.
 * - Customizable open animation.
 * - Customizable close animation.
 * - Customizable animation easing.
 * - Added customizable positioning (top-left, top-right, bottom-left, bottom-right, center)
 *
 * Changes in 1.0.2
 * - All CSS styling is now external.
 * - Added a theme parameter which specifies a secondary class for styling, such
 *   that notifications can be customized in appearance on a per message basis.
 * - Notification life span is now customizable on a per message basis.
 * - Added the ability to disable the global closer, enabled by default.
 * - Added callbacks for when a notification is opened or closed.
 * - Added callback for the global closer.
 * - Customizable animation speed.
 * - jGrowl now set itself up and tears itself down.
 *
 * Changes in 1.0.1:
 * - Removed dependency on metadata plugin in favor of .data()
 * - Namespaced all events
 */
(function($) {

	/** jGrowl Wrapper - Establish a base jGrowl Container for compatibility with older releases. **/
	$.jGrowl = function( m , o ) {
		// To maintain compatibility with older version that only supported one instance we'll create the base container.
		if ( jQ_nxs('#jGrowl').size() == 0 ) 
			$('<div id="jGrowl"></div>').addClass( (o && o.position) ? o.position : $.jGrowl.defaults.position ).appendTo('body');

		// Create a notification on the container.
		$('#jGrowl').jGrowl(m,o);
	};


	/** Raise jGrowl Notification on a jGrowl Container **/
	$.fn.jGrowl = function( m , o ) {
		if ( $.isFunction(this.each) ) {
			var args = arguments;

			return this.each(function() {
				var self = this;

				/** Create a jGrowl Instance on the Container if it does not exist **/
				if ( $(this).data('jGrowl.instance') == undefined ) {
					$(this).data('jGrowl.instance', $.extend( new $.fn.jGrowl(), { notifications: [], element: null, interval: null } ));
					$(this).data('jGrowl.instance').startup( this );
				}

				/** Optionally call jGrowl instance methods, or just raise a normal notification **/
				if ( $.isFunction($(this).data('jGrowl.instance')[m]) ) {
					$(this).data('jGrowl.instance')[m].apply( $(this).data('jGrowl.instance') , $.makeArray(args).slice(1) );
				} else {
					$(this).data('jGrowl.instance').create( m , o );
				}
			});
		};
	};

	$.extend( $.fn.jGrowl.prototype , {

		/** Default JGrowl Settings **/
		defaults: {
			pool: 			0,
			header: 		'',
			group: 			'',
			sticky: 		false,
			position: 		'top-right',
			glue: 			'after',
			theme: 			'default',
			themeState: 	'highlight',
			corners: 		'10px',
			check: 			250,
			life: 			3000,
			closeDuration:  'normal',
			openDuration:   'normal',
			easing: 		'swing',
			closer: 		true,
			closeTemplate: '&times;',
			closerTemplate: '<div style=\'pointer-events: none;\'><!-- [ close all] --></div>',
			log: 			function(e,m,o) {},
			beforeOpen: 	function(e,m,o) {},
			afterOpen: 		function(e,m,o) {},
			open: 			function(e,m,o) {},
			beforeClose: 	function(e,m,o) {},
			close: 			function(e,m,o) {},
			animateOpen: 	{
				opacity: 	'show'
			},
			animateClose: 	{
				opacity: 	'hide'
			}
		},
		
		notifications: [],
		
		/** jGrowl Container Node **/
		element: 	null,
	
		/** Interval Function **/
		interval:   null,
		
		/** Create a Notification **/
		create: 	function( message , o ) {
			var o = $.extend({}, this.defaults, o);

			/* To keep backward compatibility with 1.24 and earlier, honor 'speed' if the user has set it */
			if (typeof o.speed !== 'undefined') {
				o.openDuration = o.speed;
				o.closeDuration = o.speed;
			}

			this.notifications.push({ message: message , options: o });
			
			o.log.apply( this.element , [this.element,message,o] );
		},
		
		render: 		function( notification ) {
			var self = this;
			var message = notification.message;
			var o = notification.options;

			// Support for jQuery theme-states, if this is not used it displays a widget header
			o.themeState = (o.themeState == '') ? '' : 'ui-state-' + o.themeState;

			var notification = $(
				'<div class="jGrowl-notification ' + o.themeState + ' ui-corner-all' + 
				((o.group != undefined && o.group != '') ? ' ' + o.group : '') + '">' +
				'<div class="jGrowl-close">' + o.closeTemplate + '</div>' +
				'<div class="jGrowl-header">' + o.header + '</div>' +
				'<div class="jGrowl-message">' + message + '</div></div>'
			).data("jGrowl", o).addClass(o.theme).children('div.jGrowl-close').bind("click.jGrowl", function() {
				$(this).parent().trigger('jGrowl.close');
			}).parent();


			/** Notification Actions **/
			$(notification).bind("mouseover.jGrowl", function() {
				$('div.jGrowl-notification', self.element).data("jGrowl.pause", true);
			}).bind("mouseout.jGrowl", function() {
				$('div.jGrowl-notification', self.element).data("jGrowl.pause", false);
			}).bind('jGrowl.beforeOpen', function() {
				if ( o.beforeOpen.apply( notification , [notification,message,o,self.element] ) != false ) {
					$(this).trigger('jGrowl.open');
				}
			}).bind('jGrowl.open', function() {
				if ( o.open.apply( notification , [notification,message,o,self.element] ) != false ) {
					if ( o.glue == 'after' ) {
						$('div.jGrowl-notification:last', self.element).after(notification);
					} else {
						$('div.jGrowl-notification:first', self.element).before(notification);
					}
					
					$(this).animate(o.animateOpen, o.openDuration, o.easing, function() {
						// Fixes some anti-aliasing issues with IE filters.
						if ($.browser.msie && (parseInt($(this).css('opacity'), 10) === 1 || parseInt($(this).css('opacity'), 10) === 0))
							this.style.removeAttribute('filter');

						if ( $(this).data("jGrowl") != null ) // Happens when a notification is closing before it's open.
							$(this).data("jGrowl").created = new Date();
						
						$(this).trigger('jGrowl.afterOpen');
					});
				}
			}).bind('jGrowl.afterOpen', function() {
				o.afterOpen.apply( notification , [notification,message,o,self.element] );
			}).bind('jGrowl.beforeClose', function() {
				if ( o.beforeClose.apply( notification , [notification,message,o,self.element] ) != false )
					$(this).trigger('jGrowl.close');
			}).bind('jGrowl.close', function() {
				// Pause the notification, lest during the course of animation another close event gets called.
				$(this).data('jGrowl.pause', true);
				$(this).animate(o.animateClose, o.closeDuration, o.easing, function() {
					if ( $.isFunction(o.close) ) {
						if ( o.close.apply( notification , [notification,message,o,self.element] ) !== false )
							$(this).remove();
					} else {
						$(this).remove();
					}
				});
			}).trigger('jGrowl.beforeOpen');
		
			/** Optional Corners Plugin **/
			if ( o.corners != '' && $.fn.corner != undefined ) $(notification).corner( o.corners );

			/** Add a Global Closer if more than one notification exists **/
			if ( $('div.jGrowl-notification:parent', self.element).size() > 1 && 
				 $('div.jGrowl-closer', self.element).size() == 0 && this.defaults.closer != false ) {
				$(this.defaults.closerTemplate).addClass('jGrowl-closer ' + this.defaults.themeState + ' ui-corner-all').addClass(this.defaults.theme)
					.appendTo(self.element).animate(this.defaults.animateOpen, this.defaults.speed, this.defaults.easing)
					.bind("click.jGrowl", function() {
						$(this).siblings().trigger("jGrowl.beforeClose");

						if ( $.isFunction( self.defaults.closer ) ) {
							self.defaults.closer.apply( $(this).parent()[0] , [$(this).parent()[0]] );
						}
					});
			};
		},

		/** Update the jGrowl Container, removing old jGrowl notifications **/
		update:	 function() {
			try 
			{
				$(this.element).find('div.jGrowl-notification:parent').each( function() {
					if ( $(this).data("jGrowl") != undefined && $(this).data("jGrowl").created != undefined && 
						 ($(this).data("jGrowl").created.getTime() + parseInt($(this).data("jGrowl").life))  < (new Date()).getTime() && 
						 $(this).data("jGrowl").sticky != true && 
						 ($(this).data("jGrowl.pause") == undefined || $(this).data("jGrowl.pause") != true) ) {
	
						// Pause the notification, lest during the course of animation another close event gets called.
						$(this).trigger('jGrowl.beforeClose');
					}
				});
	
				if ( this.notifications.length > 0 && 
					 (this.defaults.pool == 0 || $(this.element).find('div.jGrowl-notification:parent').size() < this.defaults.pool) )
					this.render( this.notifications.shift() );
	
				if ( $(this.element).find('div.jGrowl-notification:parent').size() < 2 ) {
					$(this.element).find('div.jGrowl-closer').animate(this.defaults.animateClose, this.defaults.speed, this.defaults.easing, function() {
						$(this).remove();
					});
				}
			} catch (err)
			{
				nxs_js_log('jgrowl err;' + err);
			}
		},

		/** Setup the jGrowl Notification Container **/
		startup:	function(e) {
			this.element = $(e).addClass('jGrowl').append('<div class="jGrowl-notification"></div>');
			this.interval = setInterval( function() { 
				$(e).data('jGrowl.instance').update(); 
			}, parseInt(this.defaults.check));
			
			if ($.browser.msie && parseInt($.browser.version) < 7 && !window["XMLHttpRequest"]) {
				$(this.element).addClass('ie6');
			}
		},

		/** Shutdown jGrowl, removing it and clearing the interval **/
		shutdown:   function() {
			$(this.element).removeClass('jGrowl').find('div.jGrowl-notification').remove();
			clearInterval( this.interval );
		},
		
		close: 	function() {
			$(this.element).find('div.jGrowl-notification').each(function(){
				$(this).trigger('jGrowl.beforeClose');
			});
		}
	});
	
	/** Reference the Defaults Object for compatibility with older versions of jGrowl **/
	$.jGrowl.defaults = $.fn.jGrowl.prototype.defaults;

})(jQuery);

/* Modernizr 2.6.2 (Custom Build) | MIT & BSD
 * Build: http://modernizr.com/download/#-fontface-backgroundsize-borderimage-borderradius-boxshadow-flexbox-flexboxlegacy-hsla-multiplebgs-opacity-rgba-textshadow-cssanimations-csscolumns-generatedcontent-cssgradients-cssreflections-csstransforms-csstransforms3d-csstransitions-applicationcache-canvas-canvastext-draganddrop-hashchange-history-audio-video-indexeddb-input-inputtypes-localstorage-postmessage-sessionstorage-websockets-websqldatabase-webworkers-geolocation-inlinesvg-smil-svg-svgclippaths-touch-webgl-shiv-mq-cssclasses-teststyles-testprop-testallprops-hasevent-prefixes-domprefixes-load-cssclassprefix:nxs!m!
 */
;window.Modernizr=function(a,b,c){function D(a){j.cssText=a}function E(a,b){return D(n.join(a+";")+(b||""))}function F(a,b){return typeof a===b}function G(a,b){return!!~(""+a).indexOf(b)}function H(a,b){for(var d in a){var e=a[d];if(!G(e,"-")&&j[e]!==c)return b=="pfx"?e:!0}return!1}function I(a,b,d){for(var e in a){var f=b[a[e]];if(f!==c)return d===!1?a[e]:F(f,"function")?f.bind(d||b):f}return!1}function J(a,b,c){var d=a.charAt(0).toUpperCase()+a.slice(1),e=(a+" "+p.join(d+" ")+d).split(" ");return F(b,"string")||F(b,"undefined")?H(e,b):(e=(a+" "+q.join(d+" ")+d).split(" "),I(e,b,c))}function K(){e.input=function(c){for(var d=0,e=c.length;d<e;d++)u[c[d]]=c[d]in k;return u.list&&(u.list=!!b.createElement("datalist")&&!!a.HTMLDataListElement),u}("autocomplete autofocus list placeholder max min multiple pattern required step".split(" ")),e.inputtypes=function(a){for(var d=0,e,f,h,i=a.length;d<i;d++)k.setAttribute("type",f=a[d]),e=k.type!=="text",e&&(k.value=l,k.style.cssText="position:absolute;visibility:hidden;",/^range$/.test(f)&&k.style.WebkitAppearance!==c?(g.appendChild(k),h=b.defaultView,e=h.getComputedStyle&&h.getComputedStyle(k,null).WebkitAppearance!=="textfield"&&k.offsetHeight!==0,g.removeChild(k)):/^(search|tel)$/.test(f)||(/^(url|email)$/.test(f)?e=k.checkValidity&&k.checkValidity()===!1:e=k.value!=l)),t[a[d]]=!!e;return t}("search tel url email datetime date month week time datetime-local number range color".split(" "))}var d="2.6.2",e={},f=!0,g=b.documentElement,h="modernizr",i=b.createElement(h),j=i.style,k=b.createElement("input"),l=":)",m={}.toString,n=" -webkit- -moz- -o- -ms- ".split(" "),o="Webkit Moz O ms",p=o.split(" "),q=o.toLowerCase().split(" "),r={svg:"http://www.w3.org/2000/svg"},s={},t={},u={},v=[],w=v.slice,x,y=function(a,c,d,e){var f,i,j,k,l=b.createElement("div"),m=b.body,n=m||b.createElement("body");if(parseInt(d,10))while(d--)j=b.createElement("div"),j.id=e?e[d]:h+(d+1),l.appendChild(j);return f=["&#173;",'<style id="s',h,'">',a,"</style>"].join(""),l.id=h,(m?l:n).innerHTML+=f,n.appendChild(l),m||(n.style.background="",n.style.overflow="hidden",k=g.style.overflow,g.style.overflow="hidden",g.appendChild(n)),i=c(l,a),m?l.parentNode.removeChild(l):(n.parentNode.removeChild(n),g.style.overflow=k),!!i},z=function(b){var c=a.matchMedia||a.msMatchMedia;if(c)return c(b).matches;var d;return y("@media "+b+" { #"+h+" { position: absolute; } }",function(b){d=(a.getComputedStyle?getComputedStyle(b,null):b.currentStyle)["position"]=="absolute"}),d},A=function(){function d(d,e){e=e||b.createElement(a[d]||"div"),d="on"+d;var f=d in e;return f||(e.setAttribute||(e=b.createElement("div")),e.setAttribute&&e.removeAttribute&&(e.setAttribute(d,""),f=F(e[d],"function"),F(e[d],"undefined")||(e[d]=c),e.removeAttribute(d))),e=null,f}var a={select:"input",change:"input",submit:"form",reset:"form",error:"img",load:"img",abort:"img"};return d}(),B={}.hasOwnProperty,C;!F(B,"undefined")&&!F(B.call,"undefined")?C=function(a,b){return B.call(a,b)}:C=function(a,b){return b in a&&F(a.constructor.prototype[b],"undefined")},Function.prototype.bind||(Function.prototype.bind=function(b){var c=this;if(typeof c!="function")throw new TypeError;var d=w.call(arguments,1),e=function(){if(this instanceof e){var a=function(){};a.prototype=c.prototype;var f=new a,g=c.apply(f,d.concat(w.call(arguments)));return Object(g)===g?g:f}return c.apply(b,d.concat(w.call(arguments)))};return e}),s.flexbox=function(){return J("flexWrap")},s.flexboxlegacy=function(){return J("boxDirection")},s.canvas=function(){var a=b.createElement("canvas");return!!a.getContext&&!!a.getContext("2d")},s.canvastext=function(){return!!e.canvas&&!!F(b.createElement("canvas").getContext("2d").fillText,"function")},s.webgl=function(){return!!a.WebGLRenderingContext},s.touch=function(){var c;return"ontouchstart"in a||a.DocumentTouch&&b instanceof DocumentTouch?c=!0:y(["@media (",n.join("touch-enabled),("),h,")","{#modernizr{top:9px;position:absolute}}"].join(""),function(a){c=a.offsetTop===9}),c},s.geolocation=function(){return"geolocation"in navigator},s.postmessage=function(){return!!a.postMessage},s.websqldatabase=function(){return!!a.openDatabase},s.indexedDB=function(){return!!J("indexedDB",a)},s.hashchange=function(){return A("hashchange",a)&&(b.documentMode===c||b.documentMode>7)},s.history=function(){return!!a.history&&!!history.pushState},s.draganddrop=function(){var a=b.createElement("div");return"draggable"in a||"ondragstart"in a&&"ondrop"in a},s.websockets=function(){return"WebSocket"in a||"MozWebSocket"in a},s.rgba=function(){return D("background-color:rgba(150,255,150,.5)"),G(j.backgroundColor,"rgba")},s.hsla=function(){return D("background-color:hsla(120,40%,100%,.5)"),G(j.backgroundColor,"rgba")||G(j.backgroundColor,"hsla")},s.multiplebgs=function(){return D("background:url(https://),url(https://),red url(https://)"),/(url\s*\(.*?){3}/.test(j.background)},s.backgroundsize=function(){return J("backgroundSize")},s.borderimage=function(){return J("borderImage")},s.borderradius=function(){return J("borderRadius")},s.boxshadow=function(){return J("boxShadow")},s.textshadow=function(){return b.createElement("div").style.textShadow===""},s.opacity=function(){return E("opacity:.55"),/^0.55$/.test(j.opacity)},s.cssanimations=function(){return J("animationName")},s.csscolumns=function(){return J("columnCount")},s.cssgradients=function(){var a="background-image:",b="gradient(linear,left top,right bottom,from(#9f9),to(white));",c="linear-gradient(left top,#9f9, white);";return D((a+"-webkit- ".split(" ").join(b+a)+n.join(c+a)).slice(0,-a.length)),G(j.backgroundImage,"gradient")},s.cssreflections=function(){return J("boxReflect")},s.csstransforms=function(){return!!J("transform")},s.csstransforms3d=function(){var a=!!J("perspective");return a&&"webkitPerspective"in g.style&&y("@media (transform-3d),(-webkit-transform-3d){#modernizr{left:9px;position:absolute;height:3px;}}",function(b,c){a=b.offsetLeft===9&&b.offsetHeight===3}),a},s.csstransitions=function(){return J("transition")},s.fontface=function(){var a;return y('@font-face {font-family:"font";src:url("https://")}',function(c,d){var e=b.getElementById("smodernizr"),f=e.sheet||e.styleSheet,g=f?f.cssRules&&f.cssRules[0]?f.cssRules[0].cssText:f.cssText||"":"";a=/src/i.test(g)&&g.indexOf(d.split(" ")[0])===0}),a},s.generatedcontent=function(){var a;return y(["#",h,"{font:0/0 a}#",h,':after{content:"',l,'";visibility:hidden;font:3px/1 a}'].join(""),function(b){a=b.offsetHeight>=3}),a},s.video=function(){var a=b.createElement("video"),c=!1;try{if(c=!!a.canPlayType)c=new Boolean(c),c.ogg=a.canPlayType('video/ogg; codecs="theora"').replace(/^no$/,""),c.h264=a.canPlayType('video/mp4; codecs="avc1.42E01E"').replace(/^no$/,""),c.webm=a.canPlayType('video/webm; codecs="vp8, vorbis"').replace(/^no$/,"")}catch(d){}return c},s.audio=function(){var a=b.createElement("audio"),c=!1;try{if(c=!!a.canPlayType)c=new Boolean(c),c.ogg=a.canPlayType('audio/ogg; codecs="vorbis"').replace(/^no$/,""),c.mp3=a.canPlayType("audio/mpeg;").replace(/^no$/,""),c.wav=a.canPlayType('audio/wav; codecs="1"').replace(/^no$/,""),c.m4a=(a.canPlayType("audio/x-m4a;")||a.canPlayType("audio/aac;")).replace(/^no$/,"")}catch(d){}return c},s.localstorage=function(){try{return localStorage.setItem(h,h),localStorage.removeItem(h),!0}catch(a){return!1}},s.sessionstorage=function(){try{return sessionStorage.setItem(h,h),sessionStorage.removeItem(h),!0}catch(a){return!1}},s.webworkers=function(){return!!a.Worker},s.applicationcache=function(){return!!a.applicationCache},s.svg=function(){return!!b.createElementNS&&!!b.createElementNS(r.svg,"svg").createSVGRect},s.inlinesvg=function(){var a=b.createElement("div");return a.innerHTML="<svg/>",(a.firstChild&&a.firstChild.namespaceURI)==r.svg},s.smil=function(){return!!b.createElementNS&&/SVGAnimate/.test(m.call(b.createElementNS(r.svg,"animate")))},s.svgclippaths=function(){return!!b.createElementNS&&/SVGClipPath/.test(m.call(b.createElementNS(r.svg,"clipPath")))};for(var L in s)C(s,L)&&(x=L.toLowerCase(),e[x]=s[L](),v.push((e[x]?"":"no-")+x));return e.input||K(),e.addTest=function(a,b){if(typeof a=="object")for(var d in a)C(a,d)&&e.addTest(d,a[d]);else{a=a.toLowerCase();if(e[a]!==c)return e;b=typeof b=="function"?b():b,typeof f!="undefined"&&f&&(g.className+=" nxs-m-"+(b?"":"no-")+a),e[a]=b}return e},D(""),i=k=null,function(a,b){function k(a,b){var c=a.createElement("p"),d=a.getElementsByTagName("head")[0]||a.documentElement;return c.innerHTML="x<style>"+b+"</style>",d.insertBefore(c.lastChild,d.firstChild)}function l(){var a=r.elements;return typeof a=="string"?a.split(" "):a}function m(a){var b=i[a[g]];return b||(b={},h++,a[g]=h,i[h]=b),b}function n(a,c,f){c||(c=b);if(j)return c.createElement(a);f||(f=m(c));var g;return f.cache[a]?g=f.cache[a].cloneNode():e.test(a)?g=(f.cache[a]=f.createElem(a)).cloneNode():g=f.createElem(a),g.canHaveChildren&&!d.test(a)?f.frag.appendChild(g):g}function o(a,c){a||(a=b);if(j)return a.createDocumentFragment();c=c||m(a);var d=c.frag.cloneNode(),e=0,f=l(),g=f.length;for(;e<g;e++)d.createElement(f[e]);return d}function p(a,b){b.cache||(b.cache={},b.createElem=a.createElement,b.createFrag=a.createDocumentFragment,b.frag=b.createFrag()),a.createElement=function(c){return r.shivMethods?n(c,a,b):b.createElem(c)},a.createDocumentFragment=Function("h,f","return function(){var n=f.cloneNode(),c=n.createElement;h.shivMethods&&("+l().join().replace(/\w+/g,function(a){return b.createElem(a),b.frag.createElement(a),'c("'+a+'")'})+");return n}")(r,b.frag)}function q(a){a||(a=b);var c=m(a);return r.shivCSS&&!f&&!c.hasCSS&&(c.hasCSS=!!k(a,"article,aside,figcaption,figure,footer,header,hgroup,nav,section{display:block}mark{background:#FF0;color:#000}")),j||p(a,c),a}var c=a.html5||{},d=/^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i,e=/^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i,f,g="_html5shiv",h=0,i={},j;(function(){try{var a=b.createElement("a");a.innerHTML="<xyz></xyz>",f="hidden"in a,j=a.childNodes.length==1||function(){b.createElement("a");var a=b.createDocumentFragment();return typeof a.cloneNode=="undefined"||typeof a.createDocumentFragment=="undefined"||typeof a.createElement=="undefined"}()}catch(c){f=!0,j=!0}})();var r={elements:c.elements||"abbr article aside audio bdi canvas data datalist details figcaption figure footer header hgroup mark meter nav output progress section summary time video",shivCSS:c.shivCSS!==!1,supportsUnknownElements:j,shivMethods:c.shivMethods!==!1,type:"default",shivDocument:q,createElement:n,createDocumentFragment:o};a.html5=r,q(b)}(this,b),e._version=d,e._prefixes=n,e._domPrefixes=q,e._cssomPrefixes=p,e.mq=z,e.hasEvent=A,e.testProp=function(a){return H([a])},e.testAllProps=J,e.testStyles=y,g.className=g.className.replace(/(^|\s)no-js(\s|$)/,"$1$2")+(f?" nxs-m-js nxs-m-"+v.join(" nxs-m-"):""),e}(this,this.document),function(a,b,c){function d(a){return"[object Function]"==o.call(a)}function e(a){return"string"==typeof a}function f(){}function g(a){return!a||"loaded"==a||"complete"==a||"uninitialized"==a}function h(){var a=p.shift();q=1,a?a.t?m(function(){("c"==a.t?B.injectCss:B.injectJs)(a.s,0,a.a,a.x,a.e,1)},0):(a(),h()):q=0}function i(a,c,d,e,f,i,j){function k(b){if(!o&&g(l.readyState)&&(u.r=o=1,!q&&h(),l.onload=l.onreadystatechange=null,b)){"img"!=a&&m(function(){t.removeChild(l)},50);for(var d in y[c])y[c].hasOwnProperty(d)&&y[c][d].onload()}}var j=j||B.errorTimeout,l=b.createElement(a),o=0,r=0,u={t:d,s:c,e:f,a:i,x:j};1===y[c]&&(r=1,y[c]=[]),"object"==a?l.data=c:(l.src=c,l.type=a),l.width=l.height="0",l.onerror=l.onload=l.onreadystatechange=function(){k.call(this,r)},p.splice(e,0,u),"img"!=a&&(r||2===y[c]?(t.insertBefore(l,s?null:n),m(k,j)):y[c].push(l))}function j(a,b,c,d,f){return q=0,b=b||"j",e(a)?i("c"==b?v:u,a,b,this.i++,c,d,f):(p.splice(this.i++,0,a),1==p.length&&h()),this}function k(){var a=B;return a.loader={load:j,i:0},a}var l=b.documentElement,m=a.setTimeout,n=b.getElementsByTagName("script")[0],o={}.toString,p=[],q=0,r="MozAppearance"in l.style,s=r&&!!b.createRange().compareNode,t=s?l:n.parentNode,l=a.opera&&"[object Opera]"==o.call(a.opera),l=!!b.attachEvent&&!l,u=r?"object":l?"script":"img",v=l?"script":u,w=Array.isArray||function(a){return"[object Array]"==o.call(a)},x=[],y={},z={timeout:function(a,b){return b.length&&(a.timeout=b[0]),a}},A,B;B=function(a){function b(a){var a=a.split("!"),b=x.length,c=a.pop(),d=a.length,c={url:c,origUrl:c,prefixes:a},e,f,g;for(f=0;f<d;f++)g=a[f].split("="),(e=z[g.shift()])&&(c=e(c,g));for(f=0;f<b;f++)c=x[f](c);return c}function g(a,e,f,g,h){var i=b(a),j=i.autoCallback;i.url.split(".").pop().split("?").shift(),i.bypass||(e&&(e=d(e)?e:e[a]||e[g]||e[a.split("/").pop().split("?")[0]]),i.instead?i.instead(a,e,f,g,h):(y[i.url]?i.noexec=!0:y[i.url]=1,f.load(i.url,i.forceCSS||!i.forceJS&&"css"==i.url.split(".").pop().split("?").shift()?"c":c,i.noexec,i.attrs,i.timeout),(d(e)||d(j))&&f.load(function(){k(),e&&e(i.origUrl,h,g),j&&j(i.origUrl,h,g),y[i.url]=2})))}function h(a,b){function c(a,c){if(a){if(e(a))c||(j=function(){var a=[].slice.call(arguments);k.apply(this,a),l()}),g(a,j,b,0,h);else if(Object(a)===a)for(n in m=function(){var b=0,c;for(c in a)a.hasOwnProperty(c)&&b++;return b}(),a)a.hasOwnProperty(n)&&(!c&&!--m&&(d(j)?j=function(){var a=[].slice.call(arguments);k.apply(this,a),l()}:j[n]=function(a){return function(){var b=[].slice.call(arguments);a&&a.apply(this,b),l()}}(k[n])),g(a[n],j,b,n,h))}else!c&&l()}var h=!!a.test,i=a.load||a.both,j=a.callback||f,k=j,l=a.complete||f,m,n;c(h?a.yep:a.nope,!!i),i&&c(i)}var i,j,l=this.yepnope.loader;if(e(a))g(a,0,l,0);else if(w(a))for(i=0;i<a.length;i++)j=a[i],e(j)?g(j,0,l,0):w(j)?B(j):Object(j)===j&&h(j,l);else Object(a)===a&&h(a,l)},B.addPrefix=function(a,b){z[a]=b},B.addFilter=function(a){x.push(a)},B.errorTimeout=1e4,null==b.readyState&&b.addEventListener&&(b.readyState="loading",b.addEventListener("DOMContentLoaded",A=function(){b.removeEventListener("DOMContentLoaded",A,0),b.readyState="complete"},0)),a.yepnope=k(),a.yepnope.executeStack=h,a.yepnope.injectJs=function(a,c,d,e,i,j){var k=b.createElement("script"),l,o,e=e||B.errorTimeout;k.src=a;for(o in d)k.setAttribute(o,d[o]);c=j?h:c||f,k.onreadystatechange=k.onload=function(){!l&&g(k.readyState)&&(l=1,c(),k.onload=k.onreadystatechange=null)},m(function(){l||(l=1,c(1))},e),i?k.onload():n.parentNode.insertBefore(k,n)},a.yepnope.injectCss=function(a,c,d,e,g,i){var e=b.createElement("link"),j,c=i?h:c||f;e.href=a,e.rel="stylesheet",e.type="text/css";for(j in d)e.setAttribute(j,d[j]);g||(n.parentNode.insertBefore(e,n),m(c,0))}}(this,document),Modernizr.load=function(){yepnope.apply(window,[].slice.call(arguments,0))};

/*
 * Thickbox 3.1 - One Box To Rule Them All.
 * By Cody Lindley (http://www.codylindley.com)
 * Copyright (c) 2007 cody lindley
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 * 25 Aug 2014; Tuned to "nxsbox" by Nexus Themes to prevent plugins from messing up our CSS
*/

function nxsbox_init(e){jQ_nxs(e).on("click",nxsbox_click)}function nxsbox_click(){var e=this.title||this.name||null;var t=this.href||this.alt;var n=this.rel||false;nxsbox_show(e,t,n);this.blur();return false}function nxsbox_show(e,t,n){try{if(typeof document.body.style.maxHeight==="undefined"){jQ_nxs("body","html").css({height:"100%",width:"100%"});jQ_nxs("html").css("overflow","hidden");if(document.getElementById("nxsbox_HideSelect")===null){jQ_nxs("body").append("<iframe id='nxsbox_HideSelect'>"+nxsboxL10n.noiframes+"</iframe><div id='nxsbox_overlay'></div><div id='nxsbox_window'></div>");jQ_nxs("#nxsbox_overlay").click(nxsbox_remove)}}else{if(document.getElementById("nxsbox_overlay")===null){jQ_nxs("body").append("<div id='nxsbox_overlay'></div><div id='nxsbox_window'></div>");jQ_nxs("#nxsbox_overlay").click(nxsbox_remove)}}if(nxsbox_detectMacXFF()){jQ_nxs("#nxsbox_overlay").addClass("nxsbox_overlayMacFFBGHack")}else{jQ_nxs("#nxsbox_overlay").addClass("nxsbox_overlayBG")}if(e===null){e=""}jQ_nxs("body").append("<div id='nxsbox_load'><img src='"+imgLoader.src+"' /></div>");jQ_nxs("#nxsbox_load").show();var r;if(t.indexOf("?")!==-1){r=t.substr(0,t.indexOf("?"))}else{r=t}var i=/\.jpg$|\.jpeg$|\.png$|\.gif$|\.bmp$/;var s=r.toLowerCase().match(i);if(s==".jpg"||s==".jpeg"||s==".png"||s==".gif"||s==".bmp"){nxsbox_PrevCaption="";nxsbox_PrevURL="";nxsbox_PrevHTML="";nxsbox_NextCaption="";nxsbox_NextURL="";nxsbox_NextHTML="";nxsbox_imageCount="";nxsbox_FoundURL=false;if(n){nxsbox_TempArray=jQ_nxs("a[rel="+n+"]").get();for(nxsbox_Counter=0;nxsbox_Counter<nxsbox_TempArray.length&&nxsbox_NextHTML==="";nxsbox_Counter++){var o=nxsbox_TempArray[nxsbox_Counter].href.toLowerCase().match(i);if(!(nxsbox_TempArray[nxsbox_Counter].href==t)){if(nxsbox_FoundURL){nxsbox_NextCaption=nxsbox_TempArray[nxsbox_Counter].title;nxsbox_NextURL=nxsbox_TempArray[nxsbox_Counter].href;nxsbox_NextHTML="<span id='nxsbox_next'>��<a href='#'>"+nxsboxL10n.next+"</a></span>"}else{nxsbox_PrevCaption=nxsbox_TempArray[nxsbox_Counter].title;nxsbox_PrevURL=nxsbox_TempArray[nxsbox_Counter].href;nxsbox_PrevHTML="<span id='nxsbox_prev'>��<a href='#'>"+nxsboxL10n.prev+"</a></span>"}}else{nxsbox_FoundURL=true;nxsbox_imageCount=nxsboxL10n.image+" "+(nxsbox_Counter+1)+" "+nxsboxL10n.of+" "+nxsbox_TempArray.length}}}imgPreloader=new Image;imgPreloader.onload=function(){imgPreloader.onload=null;var r=nxsbox_getPageSize();var i=r[0]-150;var s=r[1]-150;var o=imgPreloader.width;var u=imgPreloader.height;if(o>i){u=u*(i/o);o=i;if(u>s){o=o*(s/u);u=s}}else if(u>s){o=o*(s/u);u=s;if(o>i){u=u*(i/o);o=i}}nxsbox_WIDTH=o+30;nxsbox_HEIGHT=u+60;jQ_nxs("#nxsbox_window").append("<a href='' id='nxsbox_ImageOff' title='"+nxsboxL10n.close+"'><img id='nxsbox_Image' src='"+t+"' width='"+o+"' height='"+u+"' alt='"+e+"'/></a>"+"<div id='nxsbox_caption'>"+e+"<div id='nxsbox_secondLine'>"+nxsbox_imageCount+nxsbox_PrevHTML+nxsbox_NextHTML+"</div></div><div id='nxsbox_closeWindow'><a href='#' id='nxsbox_closeWindowButton' title='"+nxsboxL10n.close+"'><img src='"+nxsbox_closeImage+"' /></a></div>");jQ_nxs("#nxsbox_closeWindowButton").click(nxsbox_remove);if(!(nxsbox_PrevHTML==="")){function a(){if(jQ_nxs(document).unbind("click",a)){jQ_nxs(document).unbind("click",a)}jQ_nxs("#nxsbox_window").remove();jQ_nxs("body").append("<div id='nxsbox_window'></div>");nxsbox_show(nxsbox_PrevCaption,nxsbox_PrevURL,n);return false}jQ_nxs("#nxsbox_prev").click(a)}if(!(nxsbox_NextHTML==="")){function f(){jQ_nxs("#nxsbox_window").remove();jQ_nxs("body").append("<div id='nxsbox_window'></div>");nxsbox_show(nxsbox_NextCaption,nxsbox_NextURL,n);return false}jQ_nxs("#nxsbox_next").click(f)}jQ_nxs(document).bind("keydown.nxsbox",function(e){e.stopImmediatePropagation();if(e.which==27){if(!jQ_nxs(document).triggerHandler("wp_CloseOnEscape",[{event:e,what:"nxsbox",cb:nxsbox_remove}]))nxsbox_remove()}else if(e.which==190){if(!(nxsbox_NextHTML=="")){jQ_nxs(document).unbind("nxsbox");f()}}else if(e.which==188){if(!(nxsbox_PrevHTML=="")){jQ_nxs(document).unbind("nxsbox");a()}}return false});nxsbox_position();jQ_nxs("#nxsbox_load").remove();jQ_nxs("#nxsbox_ImageOff").click(nxsbox_remove);jQ_nxs("#nxsbox_window").css({visibility:"visible"})};imgPreloader.src=t}else{var u=t.replace(/^[^\?]+\??/,"");var a=nxsbox_parseQuery(u);nxsbox_WIDTH=a["width"]*1+30||630;nxsbox_HEIGHT=a["height"]*1+40||440;ajaxContentW=nxsbox_WIDTH-30;ajaxContentH=nxsbox_HEIGHT-45;if(t.indexOf("nxsbox_iframe")!=-1){urlNoQuery=t.split("nxsbox_");jQ_nxs("#nxsbox_iframeContent").remove();if(a["modal"]!="true"){jQ_nxs("#nxsbox_window").append("<div id='nxsbox_title'><div id='nxsbox_ajaxWindowTitle'>"+e+"</div><div id='nxsbox_closeAjaxWindow'><a href='#' id='nxsbox_closeWindowButton' title='"+nxsboxL10n.close+"'><img src='"+nxsbox_closeImage+"' /></a></div></div><iframe frameborder='0' hspace='0' src='"+urlNoQuery[0]+"' id='nxsbox_iframeContent' name='nxsbox_iframeContent"+Math.round(Math.random()*1e3)+"' onload='nxsbox_showIframe()' style='width:"+(ajaxContentW+29)+"px;height:"+(ajaxContentH+17)+"px;' >"+nxsboxL10n.noiframes+"</iframe>")}else{jQ_nxs("#nxsbox_overlay").unbind();jQ_nxs("#nxsbox_window").append("<iframe frameborder='0' hspace='0' src='"+urlNoQuery[0]+"' id='nxsbox_iframeContent' name='nxsbox_iframeContent"+Math.round(Math.random()*1e3)+"' onload='nxsbox_showIframe()' style='width:"+(ajaxContentW+29)+"px;height:"+(ajaxContentH+17)+"px;'>"+nxsboxL10n.noiframes+"</iframe>")}}else{if(jQ_nxs("#nxsbox_window").css("visibility")!="visible"){if(a["modal"]!="true"){jQ_nxs("#nxsbox_window").append("<div id='nxsbox_title'><div id='nxsbox_ajaxWindowTitle'>"+e+"</div><div id='nxsbox_closeAjaxWindow'><a href='#' id='nxsbox_closeWindowButton'><img src='"+nxsbox_closeImage+"' /></a></div></div><div id='nxsbox_ajaxContent' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px'></div>")}else{jQ_nxs("#nxsbox_overlay").unbind();jQ_nxs("#nxsbox_window").append("<div id='nxsbox_ajaxContent' class='nxsbox_modal' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px;'></div>")}}else{jQ_nxs("#nxsbox_ajaxContent")[0].style.width=ajaxContentW+"px";jQ_nxs("#nxsbox_ajaxContent")[0].style.height=ajaxContentH+"px";jQ_nxs("#nxsbox_ajaxContent")[0].scrollTop=0;jQ_nxs("#nxsbox_ajaxWindowTitle").html(e)}}jQ_nxs("#nxsbox_closeWindowButton").click(nxsbox_remove);if(t.indexOf("nxsbox_inline")!=-1){jQ_nxs("#nxsbox_ajaxContent").append(jQ_nxs("#"+a["inlineId"]).children());jQ_nxs("#nxsbox_window").bind("nxsbox_unload",function(){jQ_nxs("#"+a["inlineId"]).append(jQ_nxs("#nxsbox_ajaxContent").children())});nxsbox_position();jQ_nxs("#nxsbox_load").remove();jQ_nxs("#nxsbox_window").css({visibility:"visible"})}else if(t.indexOf("nxsbox_iframe")!=-1){nxsbox_position();if(jQuery.browser.safari){jQ_nxs("#nxsbox_load").remove();jQ_nxs("#nxsbox_window").css({visibility:"visible"})}}else{jQ_nxs("#nxsbox_ajaxContent").load(t+="&random="+(new Date).getTime(),function(){nxsbox_position();jQ_nxs("#nxsbox_load").remove();nxsbox_init("#nxsbox_ajaxContent a.nxsbox");jQ_nxs("#nxsbox_window").css({visibility:"visible"})})}}if(!a["modal"]){jQ_nxs(document).bind("keyup.nxsbox",function(e){if(e.which==27){e.stopImmediatePropagation();if(!jQ_nxs(document).triggerHandler("wp_CloseOnEscape",[{event:e,what:"nxsbox",cb:nxsbox_remove}]))nxsbox_remove();return false}})}}catch(f){}}function nxsbox_showIframe(){jQ_nxs("#nxsbox_load").remove();jQ_nxs("#nxsbox_window").css({visibility:"visible"})}function nxsbox_remove(){jQ_nxs("#nxsbox_imageOff").unbind("click");jQ_nxs("#nxsbox_closeWindowButton").unbind("click");jQ_nxs("#nxsbox_window").fadeOut("fast",function(){jQ_nxs("#nxsbox_window,#nxsbox_overlay,#nxsbox_HideSelect").trigger("nxsbox_unload").unbind().remove()});jQ_nxs("#nxsbox_load").remove();
		if(typeof document.body.style.maxHeight=="undefined")
		{
			jQ_nxs("body","html").css({height:"auto",width:"auto"});
			jQ_nxs("html").css("overflow","")
		}
		jQ_nxs(document).unbind(".nxsbox");
		return false
	}
	function nxsbox_position()
	{
		var e=typeof document.body.style.maxHeight==="undefined";
		jQ_nxs("#nxsbox_window").css({marginLeft:"-"+parseInt(nxsbox_WIDTH/2,10)+"px",width:nxsbox_WIDTH+"px"});if(!e){jQ_nxs("#nxsbox_window").css({marginTop:"-"+parseInt(nxsbox_HEIGHT/2,10)+"px"})}}function nxsbox_parseQuery(e){var t={};if(!e){return t}var n=e.split(/[;&]/);for(var r=0;r<n.length;r++){var i=n[r].split("=");if(!i||i.length!=2){continue}var s=unescape(i[0]);var o=unescape(i[1]);o=o.replace(/\+/g," ");t[s]=o}return t}function nxsbox_getPageSize(){var e=document.documentElement;var t=window.innerWidth||self.innerWidth||e&&e.clientWidth||document.body.clientWidth;var n=window.innerHeight||self.innerHeight||e&&e.clientHeight||document.body.clientHeight;arrayPageSize=[t,n];return arrayPageSize}function nxsbox_detectMacXFF(){var e=navigator.userAgent.toLowerCase();if(e.indexOf("mac")!=-1&&e.indexOf("firefox")!=-1){return true}}if(typeof nxsbox_pathToImage!="string"){var nxsbox_pathToImage=nxsboxL10n.loadingAnimation}if(typeof nxsbox_closeImage!="string"){var nxsbox_closeImage=nxsboxL10n.closeImage}jQ_nxs(document).ready(function(){nxsbox_init("a.nxsbox, area.nxsbox, input.nxsbox");imgLoader=new Image;/*imgLoader.src=nxsbox_pathToImage*/})

/* jQuery forms */
/*!
 * jQuery Form Plugin
 * version: 3.32.0-2013.04.09
 * @requires jQuery v1.5 or later
 * Copyright (c) 2013 M. Alsup
 * Examples and documentation at: http://malsup.com/jquery/form/
 * Project repository: https://github.com/malsup/form
 * Dual licensed under the MIT and GPL licenses.
 * https://github.com/malsup/form#copyright-and-license
 */
/*global ActiveXObject */
;(function($) {
"use strict";

/*
    Usage Note:
    -----------
    Do not use both ajaxSubmit and ajaxForm on the same form.  These
    functions are mutually exclusive.  Use ajaxSubmit if you want
    to bind your own submit handler to the form.  For example,

    $(document).ready(function() {
        $('#myForm').on('submit', function(e) {
            e.preventDefault(); // <-- important
            $(this).ajaxSubmit({
                target: '#output'
            });
        });
    });

    Use ajaxForm when you want the plugin to manage all the event binding
    for you.  For example,

    $(document).ready(function() {
        $('#myForm').ajaxForm({
            target: '#output'
        });
    });

    You can also use ajaxForm with delegation (requires jQuery v1.7+), so the
    form does not have to exist when you invoke ajaxForm:

    $('#myForm').ajaxForm({
        delegation: true,
        target: '#output'
    });

    When using ajaxForm, the ajaxSubmit function will be invoked for you
    at the appropriate time.
*/

/**
 * Feature detection
 */
var feature = {};
feature.fileapi = $("<input type='file'/>").get(0).files !== undefined;
feature.formdata = window.FormData !== undefined;

var hasProp = !!$.fn.prop;

// attr2 uses prop when it can but checks the return type for
// an expected string.  this accounts for the case where a form 
// contains inputs with names like "action" or "method"; in those
// cases "prop" returns the element
$.fn.attr2 = function() {
    if ( ! hasProp )
        return this.attr.apply(this, arguments);
    var val = this.prop.apply(this, arguments);
    if ( ( val && val.jquery ) || typeof val === 'string' )
        return val;
    return this.attr.apply(this, arguments);
};

/**
 * ajaxSubmit() provides a mechanism for immediately submitting
 * an HTML form using AJAX.
 */
$.fn.ajaxSubmit = function(options) {
    /*jshint scripturl:true */

    // fast fail if nothing selected (http://dev.jquery.com/ticket/2752)
    if (!this.length) {
        log('ajaxSubmit: skipping submit process - no element selected');
        return this;
    }

    var method, action, url, $form = this;

    if (typeof options == 'function') {
        options = { success: options };
    }

    method = this.attr2('method');
    action = this.attr2('action');

    url = (typeof action === 'string') ? $.trim(action) : '';
    url = url || window.location.href || '';
    if (url) {
        // clean url (don't include hash vaue)
        url = (url.match(/^([^#]+)/)||[])[1];
    }

    options = $.extend(true, {
        url:  url,
        success: $.ajaxSettings.success,
        type: method || 'GET',
        iframeSrc: /^https/i.test(window.location.href || '') ? 'javascript:false' : 'about:blank'
    }, options);

    // hook for manipulating the form data before it is extracted;
    // convenient for use with rich editors like tinyMCE or FCKEditor
    var veto = {};
    this.trigger('form-pre-serialize', [this, options, veto]);
    if (veto.veto) {
        log('ajaxSubmit: submit vetoed via form-pre-serialize trigger');
        return this;
    }

    // provide opportunity to alter form data before it is serialized
    if (options.beforeSerialize && options.beforeSerialize(this, options) === false) {
        log('ajaxSubmit: submit aborted via beforeSerialize callback');
        return this;
    }

    var traditional = options.traditional;
    if ( traditional === undefined ) {
        traditional = $.ajaxSettings.traditional;
    }

    var elements = [];
    var qx, a = this.formToArray(options.semantic, elements);
    if (options.data) {
        options.extraData = options.data;
        qx = $.param(options.data, traditional);
    }

    // give pre-submit callback an opportunity to abort the submit
    if (options.beforeSubmit && options.beforeSubmit(a, this, options) === false) {
        log('ajaxSubmit: submit aborted via beforeSubmit callback');
        return this;
    }

    // fire vetoable 'validate' event
    this.trigger('form-submit-validate', [a, this, options, veto]);
    if (veto.veto) {
        log('ajaxSubmit: submit vetoed via form-submit-validate trigger');
        return this;
    }

    var q = $.param(a, traditional);
    if (qx) {
        q = ( q ? (q + '&' + qx) : qx );
    }
    if (options.type.toUpperCase() == 'GET') {
        options.url += (options.url.indexOf('?') >= 0 ? '&' : '?') + q;
        options.data = null;  // data is null for 'get'
    }
    else {
        options.data = q; // data is the query string for 'post'
    }

    var callbacks = [];
    if (options.resetForm) {
        callbacks.push(function() { $form.resetForm(); });
    }
    if (options.clearForm) {
        callbacks.push(function() { $form.clearForm(options.includeHidden); });
    }

    // perform a load on the target only if dataType is not provided
    if (!options.dataType && options.target) {
        var oldSuccess = options.success || function(){};
        callbacks.push(function(data) {
            var fn = options.replaceTarget ? 'replaceWith' : 'html';
            $(options.target)[fn](data).each(oldSuccess, arguments);
        });
    }
    else if (options.success) {
        callbacks.push(options.success);
    }

    options.success = function(data, status, xhr) { // jQuery 1.4+ passes xhr as 3rd arg
        var context = options.context || this ;    // jQuery 1.4+ supports scope context
        for (var i=0, max=callbacks.length; i < max; i++) {
            callbacks[i].apply(context, [data, status, xhr || $form, $form]);
        }
    };

    // are there files to upload?

    // [value] (issue #113), also see comment:
    // https://github.com/malsup/form/commit/588306aedba1de01388032d5f42a60159eea9228#commitcomment-2180219
    var fileInputs = $('input[type=file]:enabled[value!=""]', this);

    var hasFileInputs = fileInputs.length > 0;
    var mp = 'multipart/form-data';
    var multipart = ($form.attr('enctype') == mp || $form.attr('encoding') == mp);

    var fileAPI = feature.fileapi && feature.formdata;
    log("fileAPI :" + fileAPI);
    var shouldUseFrame = (hasFileInputs || multipart) && !fileAPI;

    var jqxhr;

    // options.iframe allows user to force iframe mode
    // 06-NOV-09: now defaulting to iframe mode if file input is detected
    if (options.iframe !== false && (options.iframe || shouldUseFrame)) {
        // hack to fix Safari hang (thanks to Tim Molendijk for this)
        // see:  http://groups.google.com/group/jquery-dev/browse_thread/thread/36395b7ab510dd5d
        if (options.closeKeepAlive) {
            $.get(options.closeKeepAlive, function() {
                jqxhr = fileUploadIframe(a);
            });
        }
        else {
            jqxhr = fileUploadIframe(a);
        }
    }
    else if ((hasFileInputs || multipart) && fileAPI) {
        jqxhr = fileUploadXhr(a);
    }
    else {
        jqxhr = $.ajax(options);
    }

    $form.removeData('jqxhr').data('jqxhr', jqxhr);

    // clear element array
    for (var k=0; k < elements.length; k++)
        elements[k] = null;

    // fire 'notify' event
    this.trigger('form-submit-notify', [this, options]);
    return this;

    // utility fn for deep serialization
    function deepSerialize(extraData){
        var serialized = $.param(extraData).split('&');
        var len = serialized.length;
        var result = [];
        var i, part;
        for (i=0; i < len; i++) {
            // #252; undo param space replacement
            serialized[i] = serialized[i].replace(/\+/g,' ');
            part = serialized[i].split('=');
            // #278; use array instead of object storage, favoring array serializations
            result.push([decodeURIComponent(part[0]), decodeURIComponent(part[1])]);
        }
        return result;
    }

     // XMLHttpRequest Level 2 file uploads (big hat tip to francois2metz)
    function fileUploadXhr(a) {
        var formdata = new FormData();

        for (var i=0; i < a.length; i++) {
            formdata.append(a[i].name, a[i].value);
        }

        if (options.extraData) {
            var serializedData = deepSerialize(options.extraData);
            for (i=0; i < serializedData.length; i++)
                if (serializedData[i])
                    formdata.append(serializedData[i][0], serializedData[i][1]);
        }

        options.data = null;

        var s = $.extend(true, {}, $.ajaxSettings, options, {
            contentType: false,
            processData: false,
            cache: false,
            type: method || 'POST'
        });

        if (options.uploadProgress) {
            // workaround because jqXHR does not expose upload property
            s.xhr = function() {
                var xhr = jQ_nxs.ajaxSettings.xhr();
                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position; /*event.position is deprecated*/
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                        }
                        options.uploadProgress(event, position, total, percent);
                    }, false);
                }
                return xhr;
            };
        }

        s.data = null;
            var beforeSend = s.beforeSend;
            s.beforeSend = function(xhr, o) {
                o.data = formdata;
                if(beforeSend)
                    beforeSend.call(this, xhr, o);
        };
        return $.ajax(s);
    }

    // private function for handling file uploads (hat tip to YAHOO!)
    function fileUploadIframe(a) {
        var form = $form[0], el, i, s, g, id, $io, io, xhr, sub, n, timedOut, timeoutHandle;
        var deferred = $.Deferred();

        if (a) {
            // ensure that every serialized input is still enabled
            for (i=0; i < elements.length; i++) {
                el = $(elements[i]);
                if ( hasProp )
                    el.prop('disabled', false);
                else
                    el.removeAttr('disabled');
            }
        }

        s = $.extend(true, {}, $.ajaxSettings, options);
        s.context = s.context || s;
        id = 'jqFormIO' + (new Date().getTime());
        if (s.iframeTarget) {
            $io = $(s.iframeTarget);
            n = $io.attr2('name');
            if (!n)
                 $io.attr2('name', id);
            else
                id = n;
        }
        else {
            $io = $('<iframe name="' + id + '" src="'+ s.iframeSrc +'" />');
            $io.css({ position: 'absolute', top: '-1000px', left: '-1000px' });
        }
        io = $io[0];


        xhr = { // mock object
            aborted: 0,
            responseText: null,
            responseXML: null,
            status: 0,
            statusText: 'n/a',
            getAllResponseHeaders: function() {},
            getResponseHeader: function() {},
            setRequestHeader: function() {},
            abort: function(status) {
                var e = (status === 'timeout' ? 'timeout' : 'aborted');
                log('aborting upload... ' + e);
                this.aborted = 1;

                try { // #214, #257
                    if (io.contentWindow.document.execCommand) {
                        io.contentWindow.document.execCommand('Stop');
                    }
                }
                catch(ignore) {}

                $io.attr('src', s.iframeSrc); // abort op in progress
                xhr.error = e;
                if (s.error)
                    s.error.call(s.context, xhr, e, status);
                if (g)
                    $.event.trigger("ajaxError", [xhr, s, e]);
                if (s.complete)
                    s.complete.call(s.context, xhr, e);
            }
        };

        g = s.global;
        // trigger ajax global events so that activity/block indicators work like normal
        if (g && 0 === $.active++) {
            $.event.trigger("ajaxStart");
        }
        if (g) {
            $.event.trigger("ajaxSend", [xhr, s]);
        }

        if (s.beforeSend && s.beforeSend.call(s.context, xhr, s) === false) {
            if (s.global) {
                $.active--;
            }
            deferred.reject();
            return deferred;
        }
        if (xhr.aborted) {
            deferred.reject();
            return deferred;
        }

        // add submitting element to data if we know it
        sub = form.clk;
        if (sub) {
            n = sub.name;
            if (n && !sub.disabled) {
                s.extraData = s.extraData || {};
                s.extraData[n] = sub.value;
                if (sub.type == "image") {
                    s.extraData[n+'.x'] = form.clk_x;
                    s.extraData[n+'.y'] = form.clk_y;
                }
            }
        }

        var CLIENT_TIMEOUT_ABORT = 1;
        var SERVER_ABORT = 2;
                
        function getDoc(frame) {
            /* it looks like contentWindow or contentDocument do not
             * carry the protocol property in ie8, when running under ssl
             * frame.document is the only valid response document, since
             * the protocol is know but not on the other two objects. strange?
             * "Same origin policy" http://en.wikipedia.org/wiki/Same_origin_policy
             */
            
            var doc = null;
            
            // IE8 cascading access check
            try {
                if (frame.contentWindow) {
                    doc = frame.contentWindow.document;
                }
            } catch(err) {
                // IE8 access denied under ssl & missing protocol
                log('cannot get iframe.contentWindow document: ' + err);
            }

            if (doc) { // successful getting content
                return doc;
            }

            try { // simply checking may throw in ie8 under ssl or mismatched protocol
                doc = frame.contentDocument ? frame.contentDocument : frame.document;
            } catch(err) {
                // last attempt
                log('cannot get iframe.contentDocument: ' + err);
                doc = frame.document;
            }
            return doc;
        }

        // Rails CSRF hack (thanks to Yvan Barthelemy)
        var csrf_token = $('meta[name=csrf-token]').attr('content');
        var csrf_param = $('meta[name=csrf-param]').attr('content');
        if (csrf_param && csrf_token) {
            s.extraData = s.extraData || {};
            s.extraData[csrf_param] = csrf_token;
        }

        // take a breath so that pending repaints get some cpu time before the upload starts
        function doSubmit() {
            // make sure form attrs are set
            var t = $form.attr2('target'), a = $form.attr2('action');

            // update form attrs in IE friendly way
            form.setAttribute('target',id);
            if (!method) {
                form.setAttribute('method', 'POST');
            }
            if (a != s.url) {
                form.setAttribute('action', s.url);
            }

            // ie borks in some cases when setting encoding
            if (! s.skipEncodingOverride && (!method || /post/i.test(method))) {
                $form.attr({
                    encoding: 'multipart/form-data',
                    enctype:  'multipart/form-data'
                });
            }

            // support timout
            if (s.timeout) {
                timeoutHandle = setTimeout(function() { timedOut = true; cb(CLIENT_TIMEOUT_ABORT); }, s.timeout);
            }

            // look for server aborts
            function checkState() {
                try {
                    var state = getDoc(io).readyState;
                    log('state = ' + state);
                    if (state && state.toLowerCase() == 'uninitialized')
                        setTimeout(checkState,50);
                }
                catch(e) {
                    log('Server abort: ' , e, ' (', e.name, ')');
                    cb(SERVER_ABORT);
                    if (timeoutHandle)
                        clearTimeout(timeoutHandle);
                    timeoutHandle = undefined;
                }
            }

            // add "extra" data to form if provided in options
            var extraInputs = [];
            try {
                if (s.extraData) {
                    for (var n in s.extraData) {
                        if (s.extraData.hasOwnProperty(n)) {
                           // if using the $.param format that allows for multiple values with the same name
                           if($.isPlainObject(s.extraData[n]) && s.extraData[n].hasOwnProperty('name') && s.extraData[n].hasOwnProperty('value')) {
                               extraInputs.push(
                               $('<input type="hidden" name="'+s.extraData[n].name+'">').val(s.extraData[n].value)
                                   .appendTo(form)[0]);
                           } else {
                               extraInputs.push(
                               $('<input type="hidden" name="'+n+'">').val(s.extraData[n])
                                   .appendTo(form)[0]);
                           }
                        }
                    }
                }

                if (!s.iframeTarget) {
                    // add iframe to doc and submit the form
                    $io.appendTo('body');
                    if (io.attachEvent)
                        io.attachEvent('onload', cb);
                    else
                        io.addEventListener('load', cb, false);
                }
                setTimeout(checkState,15);

                try {
                    form.submit();
                } catch(err) {
                    // just in case form has element with name/id of 'submit'
                    var submitFn = document.createElement('form').submit;
                    submitFn.apply(form);
                }
            }
            finally {
                // reset attrs and remove "extra" input elements
                form.setAttribute('action',a);
                if(t) {
                    form.setAttribute('target', t);
                } else {
                    $form.removeAttr('target');
                }
                $(extraInputs).remove();
            }
        }

        if (s.forceSync) {
            doSubmit();
        }
        else {
            setTimeout(doSubmit, 10); // this lets dom updates render
        }

        var data, doc, domCheckCount = 50, callbackProcessed;

        function cb(e) {
            if (xhr.aborted || callbackProcessed) {
                return;
            }
            
            doc = getDoc(io);
            if(!doc) {
                log('cannot access response document');
                e = SERVER_ABORT;
            }
            if (e === CLIENT_TIMEOUT_ABORT && xhr) {
                xhr.abort('timeout');
                deferred.reject(xhr, 'timeout');
                return;
            }
            else if (e == SERVER_ABORT && xhr) {
                xhr.abort('server abort');
                deferred.reject(xhr, 'error', 'server abort');
                return;
            }

            if (!doc || doc.location.href == s.iframeSrc) {
                // response not received yet
                if (!timedOut)
                    return;
            }
            if (io.detachEvent)
                io.detachEvent('onload', cb);
            else
                io.removeEventListener('load', cb, false);

            var status = 'success', errMsg;
            try {
                if (timedOut) {
                    throw 'timeout';
                }

                var isXml = s.dataType == 'xml' || doc.XMLDocument || $.isXMLDoc(doc);
                log('isXml='+isXml);
                if (!isXml && window.opera && (doc.body === null || !doc.body.innerHTML)) {
                    if (--domCheckCount) {
                        // in some browsers (Opera) the iframe DOM is not always traversable when
                        // the onload callback fires, so we loop a bit to accommodate
                        log('requeing onLoad callback, DOM not available');
                        setTimeout(cb, 250);
                        return;
                    }
                    // let this fall through because server response could be an empty document
                    //log('Could not access iframe DOM after mutiple tries.');
                    //throw 'DOMException: not available';
                }

                //log('response detected');
                var docRoot = doc.body ? doc.body : doc.documentElement;
                xhr.responseText = docRoot ? docRoot.innerHTML : null;
                xhr.responseXML = doc.XMLDocument ? doc.XMLDocument : doc;
                if (isXml)
                    s.dataType = 'xml';
                xhr.getResponseHeader = function(header){
                    var headers = {'content-type': s.dataType};
                    return headers[header];
                };
                // support for XHR 'status' & 'statusText' emulation :
                if (docRoot) {
                    xhr.status = Number( docRoot.getAttribute('status') ) || xhr.status;
                    xhr.statusText = docRoot.getAttribute('statusText') || xhr.statusText;
                }

                var dt = (s.dataType || '').toLowerCase();
                var scr = /(json|script|text)/.test(dt);
                if (scr || s.textarea) {
                    // see if user embedded response in textarea
                    var ta = doc.getElementsByTagName('textarea')[0];
                    if (ta) {
                        xhr.responseText = ta.value;
                        // support for XHR 'status' & 'statusText' emulation :
                        xhr.status = Number( ta.getAttribute('status') ) || xhr.status;
                        xhr.statusText = ta.getAttribute('statusText') || xhr.statusText;
                    }
                    else if (scr) {
                        // account for browsers injecting pre around json response
                        var pre = doc.getElementsByTagName('pre')[0];
                        var b = doc.getElementsByTagName('body')[0];
                        if (pre) {
                            xhr.responseText = pre.textContent ? pre.textContent : pre.innerText;
                        }
                        else if (b) {
                            xhr.responseText = b.textContent ? b.textContent : b.innerText;
                        }
                    }
                }
                else if (dt == 'xml' && !xhr.responseXML && xhr.responseText) {
                    xhr.responseXML = toXml(xhr.responseText);
                }

                try {
                    data = httpData(xhr, dt, s);
                }
                catch (err) {
                    status = 'parsererror';
                    xhr.error = errMsg = (err || status);
                }
            }
            catch (err) {
                log('error caught: ',err);
                status = 'error';
                xhr.error = errMsg = (err || status);
            }

            if (xhr.aborted) {
                log('upload aborted');
                status = null;
            }

            if (xhr.status) { // we've set xhr.status
                status = (xhr.status >= 200 && xhr.status < 300 || xhr.status === 304) ? 'success' : 'error';
            }

            // ordering of these callbacks/triggers is odd, but that's how $.ajax does it
            if (status === 'success') {
                if (s.success)
                    s.success.call(s.context, data, 'success', xhr);
                deferred.resolve(xhr.responseText, 'success', xhr);
                if (g)
                    $.event.trigger("ajaxSuccess", [xhr, s]);
            }
            else if (status) {
                if (errMsg === undefined)
                    errMsg = xhr.statusText;
                if (s.error)
                    s.error.call(s.context, xhr, status, errMsg);
                deferred.reject(xhr, 'error', errMsg);
                if (g)
                    $.event.trigger("ajaxError", [xhr, s, errMsg]);
            }

            if (g)
                $.event.trigger("ajaxComplete", [xhr, s]);

            if (g && ! --$.active) {
                $.event.trigger("ajaxStop");
            }

            if (s.complete)
                s.complete.call(s.context, xhr, status);

            callbackProcessed = true;
            if (s.timeout)
                clearTimeout(timeoutHandle);

            // clean up
            setTimeout(function() {
                if (!s.iframeTarget)
                    $io.remove();
                xhr.responseXML = null;
            }, 100);
        }

        var toXml = $.parseXML || function(s, doc) { // use parseXML if available (jQuery 1.5+)
            if (window.ActiveXObject) {
                doc = new ActiveXObject('Microsoft.XMLDOM');
                doc.async = 'false';
                doc.loadXML(s);
            }
            else {
                doc = (new DOMParser()).parseFromString(s, 'text/xml');
            }
            return (doc && doc.documentElement && doc.documentElement.nodeName != 'parsererror') ? doc : null;
        };
        var parseJSON = $.parseJSON || function(s) {
            /*jslint evil:true */
            return window['eval']('(' + s + ')');
        };

        var httpData = function( xhr, type, s ) { // mostly lifted from jq1.4.4

            var ct = xhr.getResponseHeader('content-type') || '',
                xml = type === 'xml' || !type && ct.indexOf('xml') >= 0,
                data = xml ? xhr.responseXML : xhr.responseText;

            if (xml && data.documentElement.nodeName === 'parsererror') {
                if ($.error)
                    $.error('parsererror');
            }
            if (s && s.dataFilter) {
                data = s.dataFilter(data, type);
            }
            if (typeof data === 'string') {
                if (type === 'json' || !type && ct.indexOf('json') >= 0) {
                    data = parseJSON(data);
                } else if (type === "script" || !type && ct.indexOf("javascript") >= 0) {
                    $.globalEval(data);
                }
            }
            return data;
        };

        return deferred;
    }
};

/**
 * ajaxForm() provides a mechanism for fully automating form submission.
 *
 * The advantages of using this method instead of ajaxSubmit() are:
 *
 * 1: This method will include coordinates for <input type="image" /> elements (if the element
 *    is used to submit the form).
 * 2. This method will include the submit element's name/value data (for the element that was
 *    used to submit the form).
 * 3. This method binds the submit() method to the form for you.
 *
 * The options argument for ajaxForm works exactly as it does for ajaxSubmit.  ajaxForm merely
 * passes the options argument along after properly binding events for submit elements and
 * the form itself.
 */
$.fn.ajaxForm = function(options) {
    options = options || {};
    options.delegation = options.delegation && $.isFunction($.fn.on);

    // in jQuery 1.3+ we can fix mistakes with the ready state
    if (!options.delegation && this.length === 0) {
        var o = { s: this.selector, c: this.context };
        if (!$.isReady && o.s) {
            log('DOM not ready, queuing ajaxForm');
            $(function() {
                $(o.s,o.c).ajaxForm(options);
            });
            return this;
        }
        // is your DOM ready?  http://docs.jquery.com/Tutorials:Introducing_$(document).ready()
        log('terminating; zero elements found by selector' + ($.isReady ? '' : ' (DOM not ready)'));
        return this;
    }

    if ( options.delegation ) {
        $(document)
            .off('submit.form-plugin', this.selector, doAjaxSubmit)
            .off('click.form-plugin', this.selector, captureSubmittingElement)
            .on('submit.form-plugin', this.selector, options, doAjaxSubmit)
            .on('click.form-plugin', this.selector, options, captureSubmittingElement);
        return this;
    }

    return this.ajaxFormUnbind()
        .bind('submit.form-plugin', options, doAjaxSubmit)
        .bind('click.form-plugin', options, captureSubmittingElement);
};

// private event handlers
function doAjaxSubmit(e) {
    /*jshint validthis:true */
    var options = e.data;
    if (!e.isDefaultPrevented()) { // if event has been canceled, don't proceed
        e.preventDefault();
        $(this).ajaxSubmit(options);
    }
}

function captureSubmittingElement(e) {
    /*jshint validthis:true */
    var target = e.target;
    var $el = $(target);
    if (!($el.is("[type=submit],[type=image]"))) {
        // is this a child element of the submit el?  (ex: a span within a button)
        var t = $el.closest('[type=submit]');
        if (t.length === 0) {
            return;
        }
        target = t[0];
    }
    var form = this;
    form.clk = target;
    if (target.type == 'image') {
        if (e.offsetX !== undefined) {
            form.clk_x = e.offsetX;
            form.clk_y = e.offsetY;
        } else if (typeof $.fn.offset == 'function') {
            var offset = $el.offset();
            form.clk_x = e.pageX - offset.left;
            form.clk_y = e.pageY - offset.top;
        } else {
            form.clk_x = e.pageX - target.offsetLeft;
            form.clk_y = e.pageY - target.offsetTop;
        }
    }
    // clear form vars
    setTimeout(function() { form.clk = form.clk_x = form.clk_y = null; }, 100);
}


// ajaxFormUnbind unbinds the event handlers that were bound by ajaxForm
$.fn.ajaxFormUnbind = function() {
    return this.unbind('submit.form-plugin click.form-plugin');
};

/**
 * formToArray() gathers form element data into an array of objects that can
 * be passed to any of the following ajax functions: $.get, $.post, or load.
 * Each object in the array has both a 'name' and 'value' property.  An example of
 * an array for a simple login form might be:
 *
 * [ { name: 'username', value: 'jresig' }, { name: 'password', value: 'secret' } ]
 *
 * It is this array that is passed to pre-submit callback functions provided to the
 * ajaxSubmit() and ajaxForm() methods.
 */
$.fn.formToArray = function(semantic, elements) {
    var a = [];
    if (this.length === 0) {
        return a;
    }

    var form = this[0];
    var els = semantic ? form.getElementsByTagName('*') : form.elements;
    if (!els) {
        return a;
    }

    var i,j,n,v,el,max,jmax;
    for(i=0, max=els.length; i < max; i++) {
        el = els[i];
        n = el.name;
        if (!n || el.disabled) {
            continue;
        }

        if (semantic && form.clk && el.type == "image") {
            // handle image inputs on the fly when semantic == true
            if(form.clk == el) {
                a.push({name: n, value: $(el).val(), type: el.type });
                a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
            }
            continue;
        }

        v = $.fieldValue(el, true);
        if (v && v.constructor == Array) {
            if (elements)
                elements.push(el);
            for(j=0, jmax=v.length; j < jmax; j++) {
                a.push({name: n, value: v[j]});
            }
        }
        else if (feature.fileapi && el.type == 'file') {
            if (elements)
                elements.push(el);
            var files = el.files;
            if (files.length) {
                for (j=0; j < files.length; j++) {
                    a.push({name: n, value: files[j], type: el.type});
                }
            }
            else {
                // #180
                a.push({ name: n, value: '', type: el.type });
            }
        }
        else if (v !== null && typeof v != 'undefined') {
            if (elements)
                elements.push(el);
            a.push({name: n, value: v, type: el.type, required: el.required});
        }
    }

    if (!semantic && form.clk) {
        // input type=='image' are not found in elements array! handle it here
        var $input = $(form.clk), input = $input[0];
        n = input.name;
        if (n && !input.disabled && input.type == 'image') {
            a.push({name: n, value: $input.val()});
            a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
        }
    }
    return a;
};

/**
 * Serializes form data into a 'submittable' string. This method will return a string
 * in the format: name1=value1&amp;name2=value2
 */
$.fn.formSerialize = function(semantic) {
    //hand off to jQuery.param for proper encoding
    return $.param(this.formToArray(semantic));
};

/**
 * Serializes all field elements in the jQuery object into a query string.
 * This method will return a string in the format: name1=value1&amp;name2=value2
 */
$.fn.fieldSerialize = function(successful) {
    var a = [];
    this.each(function() {
        var n = this.name;
        if (!n) {
            return;
        }
        var v = $.fieldValue(this, successful);
        if (v && v.constructor == Array) {
            for (var i=0,max=v.length; i < max; i++) {
                a.push({name: n, value: v[i]});
            }
        }
        else if (v !== null && typeof v != 'undefined') {
            a.push({name: this.name, value: v});
        }
    });
    //hand off to jQuery.param for proper encoding
    return $.param(a);
};

/**
 * Returns the value(s) of the element in the matched set.  For example, consider the following form:
 *
 *  <form><fieldset>
 *      <input name="A" type="text" />
 *      <input name="A" type="text" />
 *      <input name="B" type="checkbox" value="B1" />
 *      <input name="B" type="checkbox" value="B2"/>
 *      <input name="C" type="radio" value="C1" />
 *      <input name="C" type="radio" value="C2" />
 *  </fieldset></form>
 *
 *  var v = $('input[type=text]').fieldValue();
 *  // if no values are entered into the text inputs
 *  v == ['','']
 *  // if values entered into the text inputs are 'foo' and 'bar'
 *  v == ['foo','bar']
 *
 *  var v = $('input[type=checkbox]').fieldValue();
 *  // if neither checkbox is checked
 *  v === undefined
 *  // if both checkboxes are checked
 *  v == ['B1', 'B2']
 *
 *  var v = $('input[type=radio]').fieldValue();
 *  // if neither radio is checked
 *  v === undefined
 *  // if first radio is checked
 *  v == ['C1']
 *
 * The successful argument controls whether or not the field element must be 'successful'
 * (per http://www.w3.org/TR/html4/interact/forms.html#successful-controls).
 * The default value of the successful argument is true.  If this value is false the value(s)
 * for each element is returned.
 *
 * Note: This method *always* returns an array.  If no valid value can be determined the
 *    array will be empty, otherwise it will contain one or more values.
 */
$.fn.fieldValue = function(successful) {
    for (var val=[], i=0, max=this.length; i < max; i++) {
        var el = this[i];
        var v = $.fieldValue(el, successful);
        if (v === null || typeof v == 'undefined' || (v.constructor == Array && !v.length)) {
            continue;
        }
        if (v.constructor == Array)
            $.merge(val, v);
        else
            val.push(v);
    }
    return val;
};

/**
 * Returns the value of the field element.
 */
$.fieldValue = function(el, successful) {
    var n = el.name, t = el.type, tag = el.tagName.toLowerCase();
    if (successful === undefined) {
        successful = true;
    }

    if (successful && (!n || el.disabled || t == 'reset' || t == 'button' ||
        (t == 'checkbox' || t == 'radio') && !el.checked ||
        (t == 'submit' || t == 'image') && el.form && el.form.clk != el ||
        tag == 'select' && el.selectedIndex == -1)) {
            return null;
    }

    if (tag == 'select') {
        var index = el.selectedIndex;
        if (index < 0) {
            return null;
        }
        var a = [], ops = el.options;
        var one = (t == 'select-one');
        var max = (one ? index+1 : ops.length);
        for(var i=(one ? index : 0); i < max; i++) {
            var op = ops[i];
            if (op.selected) {
                var v = op.value;
                if (!v) { // extra pain for IE...
                    v = (op.attributes && op.attributes['value'] && !(op.attributes['value'].specified)) ? op.text : op.value;
                }
                if (one) {
                    return v;
                }
                a.push(v);
            }
        }
        return a;
    }
    return $(el).val();
};

/**
 * Clears the form data.  Takes the following actions on the form's input fields:
 *  - input text fields will have their 'value' property set to the empty string
 *  - select elements will have their 'selectedIndex' property set to -1
 *  - checkbox and radio inputs will have their 'checked' property set to false
 *  - inputs of type submit, button, reset, and hidden will *not* be effected
 *  - button elements will *not* be effected
 */
$.fn.clearForm = function(includeHidden) {
    return this.each(function() {
        $('input,select,textarea', this).clearFields(includeHidden);
    });
};

/**
 * Clears the selected form elements.
 */
$.fn.clearFields = $.fn.clearInputs = function(includeHidden) {
    var re = /^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i; // 'hidden' is not in this list
    return this.each(function() {
        var t = this.type, tag = this.tagName.toLowerCase();
        if (re.test(t) || tag == 'textarea') {
            this.value = '';
        }
        else if (t == 'checkbox' || t == 'radio') {
            this.checked = false;
        }
        else if (tag == 'select') {
            this.selectedIndex = -1;
        }
		else if (t == "file") {
			if (/MSIE/.test(navigator.userAgent)) {
				$(this).replaceWith($(this).clone(true));
			} else {
				$(this).val('');
			}
		}
        else if (includeHidden) {
            // includeHidden can be the value true, or it can be a selector string
            // indicating a special test; for example:
            //  $('#myForm').clearForm('.special:hidden')
            // the above would clean hidden inputs that have the class of 'special'
            if ( (includeHidden === true && /hidden/.test(t)) ||
                 (typeof includeHidden == 'string' && $(this).is(includeHidden)) )
                this.value = '';
        }
    });
};

/**
 * Resets the form data.  Causes all form elements to be reset to their original value.
 */
$.fn.resetForm = function() {
    return this.each(function() {
        // guard against an input with the name of 'reset'
        // note that IE reports the reset function as an 'object'
        if (typeof this.reset == 'function' || (typeof this.reset == 'object' && !this.reset.nodeType)) {
            this.reset();
        }
    });
};

/**
 * Enables or disables any matching elements.
 */
$.fn.enable = function(b) {
    if (b === undefined) {
        b = true;
    }
    return this.each(function() {
        this.disabled = !b;
    });
};

/**
 * Checks/unchecks any matching checkboxes or radio buttons and
 * selects/deselects and matching option elements.
 */
$.fn.selected = function(select) {
    if (select === undefined) {
        select = true;
    }
    return this.each(function() {
        var t = this.type;
        if (t == 'checkbox' || t == 'radio') {
            this.checked = select;
        }
        else if (this.tagName.toLowerCase() == 'option') {
            var $sel = $(this).parent('select');
            if (select && $sel[0] && $sel[0].type == 'select-one') {
                // deselect all other options
                $sel.find('option').selected(false);
            }
            this.selected = select;
        }
    });
};

// expose debug var
$.fn.ajaxSubmit.debug = false;

// helper fn for console logging
function log() {
    if (!$.fn.ajaxSubmit.debug)
        return;
    var msg = '[jquery.form] ' + Array.prototype.join.call(arguments,'');
    if (window.console && window.console.log) {
        window.console.log(msg);
    }
    else if (window.opera && window.opera.postError) {
        window.opera.postError(msg);
    }
}

})(jQuery);

var colorshaken = false;

function nxs_js_colorshake()
{
	if (colorshaken)
	{
		return;
	}
	if (!nxs_js_userhasadminpermissions()) 
	{
		// only one color shake
		colorshaken = true;
	}
	
	if (!nxs_js_inwpbackend())
	{
		nxs_js_updatecss_themecss_actualrequest(true, true);
		nxs_js_updatecss_manualcss_actualrequest();
	}
}

// kudos to https://github.com/mikesherov/jquery-idletimer/

/*
 * Copyright (c) 2009 Nicholas C. Zakas
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

( function( $ ) {

$.idleTimer = function( firstParam, elem, opts ) {

	// defaults that are to be stored as instance props on the elem
	opts = $.extend( {
		startImmediately: true,   //starts a timeout as soon as the timer is set up
		idle: false,              //indicates if the user is idle
		enabled: true,            //indicates if the idle timer is enabled
		timeout: 30000,           //the amount of time (ms) before the user is considered idle
		events: "mousemove keydown DOMMouseScroll mousewheel mousedown touchstart touchmove" // activity is one of these events
	}, opts );


	elem = elem || document;

	var jqElem = $( elem ),
		obj = jqElem.data("idleTimerObj") || {},

		/* (intentionally not documented)
		 * Toggles the idle state and fires an appropriate event.
		 * @return {void}
		 */
		toggleIdleState = function( myelem ) {

			// curse you, mozilla setTimeout lateness bug!
			if ( typeof myelem === "number" ) {
				myelem = undefined;
			}

			var obj = $.data( myelem || elem, "idleTimerObj" );

			//toggle the state
			obj.idle = !obj.idle;

			// reset timeout
			var elapsed = ( +new Date() ) - obj.olddate;
			obj.olddate = +new Date();

			// handle Chrome always triggering idle after js alert or comfirm popup
			if ( obj.idle && ( elapsed < opts.timeout ) ) {
				obj.idle = false;
				clearTimeout( $.idleTimer.tId );
				if ( opts.enabled ) {
					$.idleTimer.tId = setTimeout( toggleIdleState, opts.timeout );
				}
				return;
			}

			// create a custom event, but first, store the new state on the element
			// and then append that string to a namespace
			var event = $.Event( $.data( elem, "idleTimer", obj.idle ? "idle" : "active" ) + ".idleTimer" );
			$( elem ).trigger( event );
		},

		/**
		 * Stops the idle timer. This removes appropriate event handlers
		 * and cancels any pending timeouts.
		 * @return {void}
		 * @method stop
		 * @static
		 */
		stop = function( jqElem ) {

			var obj = jqElem.data("idleTimerObj") || {};

			//set to disabled
			obj.enabled = false;

			//clear any pending timeouts
			clearTimeout( obj.tId );

			//detach the event handlers
			jqElem.off(".idleTimer");
		};

	obj.olddate = obj.olddate || +new Date();

	if ( typeof firstParam === "number" ) {
		opts.timeout = firstParam;
	} else if ( firstParam === "destroy" ) {
		stop( jqElem );
		return this;
	} else if ( firstParam === "getElapsedTime" ) {
		return ( +new Date() ) - obj.olddate;
	}


	/* (intentionally not documented)
	 * Handles a user event indicating that the user isn't idle.
	 * @param {Event} event A DOM2-normalized event object.
	 * @return {void}
	 */
	jqElem.on( $.trim( ( opts.events + " " ).split(" ").join(".idleTimer ") ), function() {
		var obj = $.data( this, "idleTimerObj" );

		//clear any existing timeout
		clearTimeout( obj.tId );

		//if the idle timer is enabled
		if ( obj.enabled ){
			//if it's idle, that means the user is no longer idle
			if ( obj.idle ){
				toggleIdleState( this );
			}

			//set a new timeout
			obj.tId = setTimeout( toggleIdleState, obj.timeout );
		}
	});

	obj.idle = opts.idle;
	obj.enabled = opts.enabled;
	obj.timeout = opts.timeout;

	//set a timeout to toggle state. May wish to omit this in some situations
	if ( opts.startImmediately ) {
		obj.tId = setTimeout( toggleIdleState, obj.timeout );
	}

	// assume the user is active for the first x seconds.
	jqElem.data( "idleTimer", "active" );

	// store our instance on the object
	jqElem.data( "idleTimerObj", obj );
};

$.fn.idleTimer = function( firstParam, opts ) {
	// Allow omission of opts for backward compatibility
	if ( !opts ) {
		opts = {};
	}

	if ( this[0] ){
		$.idleTimer( firstParam, this[0], opts );
	}

	return this;
};

})( jQuery );

//nxs_js_popupsession_startnewcontext();
var nxsfrontendscriptloaded = true;