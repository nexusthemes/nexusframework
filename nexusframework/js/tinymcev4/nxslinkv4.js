/**
 * editor_plugin.js
 *
 * Copyright 2012, Nexus Studios, Nexus Themes
 * Released under GPL License v2.
 */

function nxs_js_tiny_plugin_link_shouldeditorinstanceprocessactivity(ed)
{
	// determine who "we" are
	var optionid = nxs_js_tiny_plugin_link_getoptionidforeditor(ed);
	
	// retrieve the invoker of this activity
	var triggerbyoptionid = nxs_js_popup_getsessiondata("nxs_tinymce_invoker_optionid");

	var result = (optionid == triggerbyoptionid);
	
	nxs_js_log("nxs_js_tiny_plugin_link_shouldeditorinstanceprocessactivity " + result);
	
	return result;
}

function nxs_js_tiny_plugin_link_handleactivities(ed, url)
{
	if (!nxs_js_tiny_plugin_link_shouldeditorinstanceprocessactivity(ed))
	{
		// if the activity isn't caused by us, skip it!
		return;
	}
	
	var tinymcepopupcontext = nxs_js_popup_getsessiondata('tinymcepopupcontext');
	if (tinymcepopupcontext == '')
	{
	}
	else if (tinymcepopupcontext == 'createlink')
	{
		// store the target for our tinymce plugin to continue
		nxs_js_popup_sessiondata_make_dirty();

		// als we hier komen, heeft de gebruiker op "save" gedrukt in het "create link" popup scherm,
		// deze 'context' was reeds gezet toen we op de "make link" knop drukten...
		nxs_js_log("returning to tinymce...");

		// haal de url op van de link die we moeten gaan plaatsen,
		// deze is gezet door de popup waar de link gekozen is...
		var tinymcelink = nxs_js_popup_getsessiondata('tinymcelink');
		nxs_js_log('we gaan zo linken naar: ' + tinymcelink);
		//				
		var tinymcetarget = nxs_js_popup_getsessiondata('tinymcetarget');
		nxs_js_log('de target is : ' + tinymcetarget);

		var tinymcetitle = nxs_js_popup_getsessiondata('tinymcetitle');
		nxs_js_log('de title is : ' + tinymcetitle);
		
		ed.on
		(
			'init',
			function(editor) 
			{
				editor = ed;
				
				var contenttogetanchored = nxs_js_popup_getsessiondata("tinymcecontenttogetanchored");
				var uniqueid = "nxs-magic" + nxs_js_getrandom(999999);
				var scaffoldedcontent = nxs_js_popup_getsessiondata("tinymcescaffoldedcontent");
				scaffoldedcontent = scaffoldedcontent.replace("nxs-domselection", "<a id='" + uniqueid + "'>" + contenttogetanchored + "</a><span id='caret_pos_holder'></span>");

				editor.setContent(scaffoldedcontent, {format : 'raw'});
				
				nxs_js_log('content updated');

				// resetten van de state
				nxs_js_popup_setsessiondata('tinymcepopupcontext', '');
				
				nxs_js_log("done!...");
  		}
  	);
	}
	else
	{
		// add handling for other activities here, when needed
		// one plugin could for example have a "process" and "cancel" activity

		//nxs_js_log("initializing nexuslink v2 in tinymce...");
		//nxs_js_alert('initializing nexuslink v2 in tinymce...');
	}
}

tinymce.PluginManager.add
(
	'example', 
	function(editor, url) 
	{
		nxs_js_tiny_plugin_link_handleactivities(editor, url);
		
		// Add a button that opens a window
    editor.addButton('example', {
        text: 'My button GJ',
        icon: false,
        onclick: function() 
        {
        	nxs_js_log("button clicked!");    
        	nxs_js_tiny_plugin_link_handlebuttonpressed(editor);
        }
    });

    // Adds a menu item to the tools menu
    editor.addMenuItem('example', {
        text: 'Example plugin',
        context: 'tools',
        onclick: function() {
            // Open window with a specific url
            editor.windowManager.open({
                title: 'TinyMCE site',
                url: 'http://www.tinymce.com',
                width: 800,
                height: 600,
                buttons: [{
                    text: 'Close',
                    onclick: 'close'
                }]
            });
        }
    });
	}
);

function nxs_js_tiny_plugin_link_handlebuttonpressed(ed)
{
	nxs_js_log('touched');
	
	var se = ed.selection;

	// save content before modification (undo content)
	var contentbefore = tinyMCE.activeEditor.getContent({format : 'raw'});
	nxs_js_popup_setsessiondata("tinymcecontentbefore", contentbefore);
	
	//
	var optionid = nxs_js_tiny_plugin_link_getoptionidforeditor(ed);
	
	// store the 'current' optionid in the session
	nxs_js_popup_setsessiondata("nxs_tinymce_invoker_optionid", optionid);
	
	// save selected content (content to be linked)
	var contenttogetanchored = se.getContent({format : 'raw'});
	nxs_js_log("contenttogetanchored:");
	nxs_js_log(contenttogetanchored);
	nxs_js_popup_setsessiondata("tinymcecontenttogetanchored", contenttogetanchored);
	
	var anchoredcontent = "<span>nxs-domselection</span>";
	
	// adjust the DOM such that we know what piece was selected (no other way to persist the selected DOM unfortunately...)
	tinyMCE.execCommand('mceInsertContent',false,anchoredcontent);
	
	// save content after modification (temp content)
	var scaffoldedcontent = tinyMCE.activeEditor.getContent({format : 'raw'});
	nxs_js_popup_setsessiondata("tinymcescaffoldedcontent", scaffoldedcontent);
	
	// store information (not just the tiny mce data, but also other fields on the popup) 
	nxs_js_setpopupdatefromcontrols();
	// note that the content contains the (temporary) link to be pimped with the selected DOM
	// in case the user pressed UNDO, we can easily revert the contents based on the 
	// data in "tinymcecontentbefore".

	// redirect to popup allowing user to select destination
	nxs_js_popup_setsessiondata('tinymcepopupcontext', 'createlink');
	// we mark the current popup as the invoker, such that we will be returned to this 
	// popup when the linkpicker is done
	nxs_js_popup_setsessiondata("nxs_tinymce_invoker_sheet", nxs_js_popup_getcurrentsheet());
	
	nxs_js_popup_navigateto("tinymcepicklink");
	// the popup will be responsible to redirect back to the 'home' screen,
	// which will eventually re-render this plugin			
}

function nxs_js_tiny_plugin_link_getoptionidforeditor(ed)
{
	var textareaelement = jQuery(ed.getElement());
	var optioniddomelement = jQuery(textareaelement).closest(".nxs-optionid");
	var optionid = nxs_js_findclassidentificationwithprefix(optioniddomelement, "nxs-optionid-");
	return optionid;
}