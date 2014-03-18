/**
 * editor_plugin.js
 *
 * Copyright 2012, Nexus Studios, Nexus Themes
 * Released under GPL License v2.
 */

(
	function() 
	{
		function nxs_js_tiny_plugin_link_getoptionidforeditor(ed)
		{
			var textareaelement = jQuery(ed.getElement());
			var optioniddomelement = jQuery(textareaelement).closest(".nxs-optionid");
			var optionid = nxs_js_findclassidentificationwithprefix(optioniddomelement, "nxs-optionid-");
			return optionid;
		}
		
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
		
		function nxs_js_tiny_plugin_link_shouldeditorinstanceprocessactivity(ed)
		{
			// determine who "we" are
			var optionid = nxs_js_tiny_plugin_link_getoptionidforeditor(ed);
			
			// retrieve the invoker of this activity
			var triggerbyoptionid = nxs_js_popup_getsessiondata("nxs_tinymce_invoker_optionid");

			var result = (optionid == triggerbyoptionid);
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
				
				ed.onInit.add
				(
					function(editor) 
					{
						nxs_js_log('blijkbaar wordt de editor geinitializeerd...');
						// content available?
						nxs_js_log("Content bij init plugin:" + editor.getContent());
						
						var contenttogetanchored = nxs_js_popup_getsessiondata("tinymcecontenttogetanchored");
						var uniqueid = "nxs-magic" + nxs_js_getrandom(999999);
						var scaffoldedcontent = nxs_js_popup_getsessiondata("tinymcescaffoldedcontent");
						scaffoldedcontent = scaffoldedcontent.replace("<span>nxs-domselection</span>", "<a id='" + uniqueid + "'>" + contenttogetanchored + "</a><span id='caret_pos_holder'></span>");
						nxs_js_log('scaffolded content:');
						nxs_js_log(scaffoldedcontent);
						
						editor.setContent(scaffoldedcontent, {format : 'raw'});
						nxs_js_log('content updated');
						
						// focus the editor
						editor.focus();						
						nxs_js_log('focus set');
						
						nxs_js_log('uniqueid:' + uniqueid);
						
						// select anchor
						editor.selection.select(tinyMCE.activeEditor.dom.select('a#' + uniqueid)[0]);
						
						nxs_js_log('decorating...');
						
						// decorating anchor
						tinyMCE.activeEditor.dom.setAttrib(tinyMCE.activeEditor.dom.select('a#' + uniqueid)[0], 'href', tinymcelink);
						tinyMCE.activeEditor.dom.setAttrib(tinyMCE.activeEditor.dom.select('a#' + uniqueid)[0], 'target', tinymcetarget);
						tinyMCE.activeEditor.dom.setAttrib(tinyMCE.activeEditor.dom.select('a#' + uniqueid)[0], 'title', tinymcetitle);
						
						// remove the id
						tinyMCE.activeEditor.dom.setAttrib(tinyMCE.activeEditor.dom.select('a#' + uniqueid)[0], 'id', '');
						
						nxs_js_log('scrolling to position...');
						
						// scroll to position, see partly http://stackoverflow.com/questions/1253303/whats-the-best-way-to-set-cursor-caret-position
						// (actually the scroll happens when the 'mceInsertContent' is invoked; we insert content and remove the content afterwards)
						// the caret_pos_holder was added when the anchor was placed
						tinyMCE.activeEditor.selection.select(ed.dom.select('span#caret_pos_holder')[0]); //select the span
						// we remove the caret_pos_holder
						tinyMCE.activeEditor.dom.remove(ed.dom.select('span#caret_pos_holder')[0]); //remove the span
						// we re-add the caret (_THIS_ is what scrolls the window to the right position)
						tinyMCE.activeEditor.execCommand('mceInsertContent', false, "<span id='caret_pos_holder'></span>");
						// we remove the caret_pos_holder for the 2nd time
						tinyMCE.activeEditor.selection.select(ed.dom.select('span#caret_pos_holder')[0]); //select the span
						tinyMCE.activeEditor.dom.remove(ed.dom.select('span#caret_pos_holder')[0]); //remove the span

						// select anchor
						// editor.selection.select(tinyMCE.activeEditor.dom.select('a#' + uniqueid)[0]);

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
		
		tinymce.create
		(
			'tinymce.plugins.NexusLinkPlugin', 
			{
				init : function(ed, url) 
				{
					// handle activities, if applicable
					nxs_js_tiny_plugin_link_handleactivities(ed, url);
					
					this.editor = ed;
					// Register commands
					ed.addCommand
					(
						'mceNexusLink', 
						function() 
						{
							nxs_js_tiny_plugin_link_handlebuttonpressed(ed)
						}
					);
		
					// Register buttons
					ed.addButton('link', {
						title : 'Nexus Link',
						cmd : 'mceNexusLink'
					});
		
					ed.addShortcut('ctrl+k', 'Nexus Link', 'mceNexusLink');
					
					ed.onNodeChange.add(function(ed, cm, n, co) {
						cm.setDisabled('link', co && n.nodeName != 'A');
						cm.setActive('link', n.nodeName == 'A' && !n.name);
					});
				},
		
				getInfo : function() {
					return {
						longname : 'Nexus Link',
						author : 'Nexus Themes',
						authorurl : 'http://www.nexusthemes.com',
						infourl : 'http://www.nexusthemes.com',
						version : tinymce.majorVersion + "." + tinymce.minorVersion
					};
				}
			}
		);

		// Register plugin
		tinymce.PluginManager.add('nexuslink', tinymce.plugins.NexusLinkPlugin);
	}
)
();