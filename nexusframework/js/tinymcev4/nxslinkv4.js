/**
 * plugin.js
 *
 * Original file:
 * 
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 * 
 * Modifications: NexusThemes.com
 * 
 */

/*global tinymce:true */

tinymce.PluginManager.add
(
	'link', 
	function(editor) 
	{
		// initialisatie zodra knop op de editor word
		
		function createLinkList(callback) {
			return function() {
				var linkList = editor.settings.link_list;
	
				if (typeof(linkList) == "string") {
					tinymce.util.XHR.send({
						url: linkList,
						success: function(text) {
							callback(tinymce.util.JSON.parse(text));
						}
					});
				} else if (typeof(linkList) == "function") {
					linkList(callback);
				} else {
					callback(linkList);
				}
			};
		}
	
		function removeLink()
		{
			
		}
	
		function showDialog(linkList) 
		{
			function isOnlyTextSelected(anchorElm) 
			{
				//nxs_js_log("isOnlyTextSelected");
	
				var html = selection.getContent();
	
				// Partial html and not a fully selected anchor element
				if (/</.test(html) && (!/^<a [^>]+>[^<]+<\/a>$/.test(html) || html.indexOf('href=') == -1)) {
					return false;
				}
	
				if (anchorElm) {
					var nodes = anchorElm.childNodes, i;
	
					if (nodes.length === 0) {
						return false;
					}
	
					for (i = nodes.length - 1; i >= 0; i--) {
						if (nodes[i].nodeType != 3) {
							return false;
						}
					}
				}
	
				return true;
			}
			
			// HIERONDER BEGINT DE CODE DIE DIRECT WORDT UITGEVOERD,
			// ALS DE GEBRUIKER DE KNOP INDRUKT;

			//nxs_js_log("showDialog");
			
			// save content before modification (undo content)
			var contentbefore = tinyMCE.activeEditor.getContent({format : 'raw'});
			nxs_js_popup_setsessioncontext("tinymcecontentbefore", contentbefore);

			var data = {}, selection = editor.selection, dom = editor.dom, selectedElm, anchorElm, initialText;
			
			var win;
			var onlyText;
			
			selectedElm = selection.getNode();
			anchorElm = dom.getParent(selectedElm, 'a[href]');
			onlyText = isOnlyTextSelected();
	
			data.text = initialText = anchorElm ? (anchorElm.innerText || anchorElm.textContent) : selection.getContent({format: 'text'});
			data.href = anchorElm ? dom.getAttrib(anchorElm, 'href') : '';
			data.target = anchorElm ? dom.getAttrib(anchorElm, 'target') : (editor.settings.default_link_target || null);
			data.rel = anchorElm ? dom.getAttrib(anchorElm, 'rel') : null;
			data.class = anchorElm ? dom.getAttrib(anchorElm, 'class') : null;
			data.title = anchorElm ? dom.getAttrib(anchorElm, 'title') : '';

			// redirect to popup allowing user to select destination
			nxs_js_popup_setshortscopedata('linktarget', data.target);
			nxs_js_popup_setshortscopedata('linktext', data.text);
			nxs_js_popup_setshortscopedata('linkhref', data.href);
			nxs_js_popup_setshortscopedata('linktype', "autoderive");
			nxs_js_popup_setshortscopedata('linkrel', data.rel);	// not (yet) used
			nxs_js_popup_setshortscopedata('linkclass', data.class);	// not (yet) used
			nxs_js_popup_setshortscopedata('linktitle', data.title);	// not (yet) used
			
			var currentatts = new Object();
			currentatts.id = "NXS-SELECTED-ID";
			currentatts.href = "{{NXS-LINK-HREF}}";
			currentatts.target = "{{NXS-LINK-TARGET}}";
			currentatts.rel = "{{NXS-LINK-REL}}";
			currentatts.title = "{{NXS-LINK-TITLE}}";
						
			dom.setAttribs(anchorElm, currentatts);
						
			var text = "{{NXS-LINK-TEXT}}";
						
			if (anchorElm) 
			{
				nxs_js_log("anchorElm is 'set'");
				//anchorElm.innerText = "INNERTEXT:AAP";
				anchorElm.textContent = text;
				selection.select(anchorElm);
				//editor.undoManager.add();
			} 
			else 
			{
				//nxs_js_log("anchorElm is not 'set'");
				var x = dom.createHTML('a', currentatts, text);
				editor.insertContent(x);				
				var domitem = tinymce.activeEditor.dom.select('#' + currentatts.id)[0];
				//nxs_js_log(domitem);
				selection.select(domitem);
			}
			
			// save content after modification (temp content)
			var scaffoldedcontent = tinyMCE.activeEditor.getContent({format : 'raw'});
			nxs_js_popup_setsessioncontext("tinymcescaffoldedcontent", scaffoldedcontent);

			// store information (not just the tiny mce data, but also other fields on the popup) 
			nxs_js_setpopupdatefromcontrols();
			// note that the content contains the (temporary) link to be pimped with the selected DOM
			// in case the user pressed UNDO, we can easily revert the contents based on the 
			// data in "tinymcecontentbefore".

			// redirect to popup allowing user to select destination
			nxs_js_popup_setsessioncontext('tinymcepopupcontext', 'createlink');
			// we mark the current popup as the invoker, such that we will be returned to this 
			// popup when the linkpicker is done
			nxs_js_popup_setsessioncontext("nxs_tinymce_invoker_sheet", nxs_js_popup_getcurrentsheet());
			
			nxs_js_popup_navigateto("tinymcepicklink");
			// the popup will be responsible to redirect back to the 'home' screen,
			// which will eventually re-render this plugin	
		}
		
		function nxs_js_handleinittriggers(event, editor)
		{
			var trigger = nxs_js_popup_getsessioncontext('tinymceinittrigger');
			if (trigger == "setanchor")
			{
				var selection = editor.selection;
				var anchorElm = editor.dom.select('a#NXS-SELECTED-ID')[0];
				//var selection = editor.selection
				//selection.select(anchorElm);
		
				var linktext = nxs_js_popup_getsessioncontext('linktext');
				nxs_js_log(linktext);
				
				var linkhref = nxs_js_popup_getsessioncontext('linkhref');
				nxs_js_log(linkhref);
				
				var linktarget = nxs_js_popup_getsessioncontext('linktarget');
				nxs_js_log(linktarget);
				
				var linktitle = nxs_js_popup_getsessioncontext('linktitle');
				nxs_js_log(linktitle);

				var linkrel = nxs_js_popup_getsessioncontext('linkrel');
				nxs_js_log(linkrel);
				
				anchorElm.textContent = linktext;
				
				var atts = 
				{
					'href' : linkhref,
					'target' : linktarget,
					'title' : linktitle,
					'rel' : linkrel,
					'id' : '',	// wipe the ID
				}
				
				editor.dom.setAttribs(anchorElm, atts);
				editor.selection.select(anchorElm);
				
				// collapse the title section in the popup, used in the text widget
				nxs_js_popuptogglewrapper(this, 'nxs-wrapperbegin-wrapper_title_begin');
				
				// prevent trigger from re-triggering :)
				nxs_js_popup_setsessioncontext('tinymceinittrigger', '');
			}
		}
		
		
		
		// hierboven staan functies
		// ------------------
		// hieronder wordt de knop toegevoegd bij initialisatie van de link plugin
	
		
		
		//trigger
		jQuery(window).bind('nxs_tinymce_setup', nxs_js_handleinittriggers);
		
		editor.addButton
		(
			'link', 
			{
				icon: 'link',
				tooltip: 'Insert/edit link',
				shortcut: 'Ctrl+K',
				onclick: showDialog,
				stateSelector: 'a[href]'
			}
		);
		
		editor.addButton
		(
			'unlink', 
			{
			icon: 'unlink',
			tooltip: 'Remove link',
			cmd: 'unlink',
			stateSelector: 'a[href]'
			}
		);
	
		this.showDialog = showDialog;
	}
);

