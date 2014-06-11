/**
 * plugin.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
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
	
		function showDialog(linkList) 
		{
			function linkListChangeHandler(e) 
			{
				var textCtrl = win.find('#text');
	
				if (!textCtrl.value() || (e.lastControl && textCtrl.value() == e.lastControl.text())) {
					textCtrl.value(e.control.text());
				}
	
				win.find('#href').value(e.control.value());
			}
	
			function buildLinkList() 
			{
				
				function appendItems(values, output) {
					output = output || [];
	
					tinymce.each(values, function(value) {
						var item = {text: value.text || value.title};
	
						if (value.menu) {
							item.menu = appendItems(value.menu);
						} else {
							item.value = editor.convertURL(value.value || value.url, 'href');
						}
	
						output.push(item);
					});
	
					return output;
				}
	
				return appendItems(linkList, [{text: 'None', value: ''}]);
			}
	
			function applyPreview(items) 
			{
				tinymce.each(items, function(item) {
					item.textStyle = function() {
						return editor.formatter.getCssText({inline: 'a', classes: [item.value]});
					};
				});
	
				return items;
			}
	
			function buildValues(listSettingName, dataItemName, defaultItems) 
			{
				var selectedItem, items = [];
	
				tinymce.each(editor.settings[listSettingName] || defaultItems, function(target) {
					var item = {
						text: target.text || target.title,
						value: target.value
					};
	
					items.push(item);
	
					if (data[dataItemName] === target.value || (!selectedItem && target.selected)) {
						selectedItem = item;
					}
				});
	
				if (selectedItem && !data[dataItemName]) {
					data[dataItemName] = selectedItem.value;
					selectedItem.selected = true;
				}
	
				return items;
			}
	
			function buildAnchorListControl(url) 
			{
				var anchorList = [];
	
				tinymce.each(editor.dom.select('a:not([href])'), function(anchor) {
					var id = anchor.name || anchor.id;
	
					if (id) {
						anchorList.push({
							text: id,
							value: '#' + id,
							selected: url.indexOf('#' + id) != -1
						});
					}
				});
	
				if (anchorList.length) {
					anchorList.unshift({text: 'None', value: ''});
	
					return {
						name: 'anchor',
						type: 'listbox',
						label: 'Anchors',
						values: anchorList,
						onselect: linkListChangeHandler
					};
				}
			}
	
			function urlChange() 
			{
				if (linkListCtrl) {
					linkListCtrl.value(editor.convertURL(this.value(), 'href'));
				}
	
				if (!initialText && data.text.length === 0 && onlyText) {
					this.parent().parent().find('#text')[0].value(this.value());
				}
			}
	
			function isOnlyTextSelected(anchorElm) 
			{
				nxs_js_log("isOnlyTextSelected");
	
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
			
			function nxs_handlesubmitlinkform(e)
			{
				// handles submit of popup form
				
				function insertLink() 
				{
					nxs_js_log("insertLink");
					
					var linkAttrs = 
					{
						href: href,
						target: data.target ? data.target : null,
						rel: data.rel ? data.rel : null,
						"class": data["class"] ? data["class"] : null,
						title: data.title ? data.title : null
					};

					if (anchorElm) 
					{
						editor.focus();

						if (onlyText && data.text != initialText) 
						{
							if ("innerText" in anchorElm) 
							{
								anchorElm.innerText = data.text;
							} 
							else 
							{
								anchorElm.textContent = data.text;
							}
						}

						dom.setAttribs(anchorElm, linkAttrs);

						selection.select(anchorElm);
						editor.undoManager.add();
					} 
					else 
					{
						if (onlyText) 
						{
							editor.insertContent(dom.createHTML('a', linkAttrs, dom.encode(data.text)));
						} 
						else 
						{
							editor.execCommand('mceInsertLink', false, linkAttrs);
						}
					}
				}

				// Delay confirm since onSubmit will move focus
				function delayedConfirm(message, callback) 
				{
					var rng = editor.selection.getRng();

					window.setTimeout
					(
						function() 
						{
							editor.windowManager.confirm
							(
								message, 
								function(state) 
								{
									editor.selection.setRng(rng);
									callback(state);
								}
							);
						}, 
						0
					);
				}

				var href;

				data = tinymce.extend(data, e.data);
				href = data.href;

				if (!href) 
				{
					editor.execCommand('unlink');
					return;
				}

				// Is email and not //user@domain.com
				if (href.indexOf('@') > 0 && href.indexOf('//') == -1 && href.indexOf('mailto:') == -1) 
				{
					delayedConfirm
					(
						'The URL you entered seems to be an email address. Do you want to add the required mailto: prefix?',
						function(state) 
						{
							if (state) 
							{
								href = 'mailto:' + href;
							}

							insertLink();
						}
					);

					return;
				}
				
				// Is www. prefixed
				if (/^\s*www\./i.test(href)) 
				{
					delayedConfirm
					(
						'The URL you entered seems to be an external link. Do you want to add the required http:// prefix?',
						function(state) 
						{
							if (state) 
							{
								href = 'http://' + href;
							}

							insertLink();
						}
					);

					return;
				}

				insertLink();
			}
			

			
			// HIERONDER BEGINT DE CODE DIE DIRECT WORDT UITGEVOERD,
			// ALS DE GEBRUIKER DE KNOP INDRUKT;

			nxs_js_log("showDialog");
			
			// save content before modification (undo content)
			var contentbefore = tinyMCE.activeEditor.getContent({format : 'raw'});
			nxs_js_popup_setsessioncontext("tinymcecontentbefore", contentbefore);

			var data = {}, selection = editor.selection, dom = editor.dom, selectedElm, anchorElm, initialText;
			
			nxs_js_log("editor.selection:");
			nxs_js_log(editor.selection);
			
			var win;
			var onlyText;
			
			// the variables that are used to render the properties in the popup window
			var textListCtrl;
			var linkListCtrl;
			var relListCtrl;
			var targetListCtrl;
			var classListCtrl;
			var linkTitleCtrl;
	
			selectedElm = selection.getNode();
			anchorElm = dom.getParent(selectedElm, 'a[href]');
			onlyText = isOnlyTextSelected();
	
			data.text = initialText = anchorElm ? (anchorElm.innerText || anchorElm.textContent) : selection.getContent({format: 'text'});
			data.href = anchorElm ? dom.getAttrib(anchorElm, 'href') : '';
			data.target = anchorElm ? dom.getAttrib(anchorElm, 'target') : (editor.settings.default_link_target || null);
			data.rel = anchorElm ? dom.getAttrib(anchorElm, 'rel') : null;
			data.class = anchorElm ? dom.getAttrib(anchorElm, 'class') : null;
			data.title = anchorElm ? dom.getAttrib(anchorElm, 'title') : '';

			nxs_js_log("current selection:");
			nxs_js_log(data);

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
				nxs_js_log(domitem);
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
		
				var linktext = nxs_js_popup_getsessiondata('linktext');
				nxs_js_log(linktext);
				
				var linkhref = nxs_js_popup_getsessiondata('linkhref');
				nxs_js_log(linkhref);
				
				var linktarget = nxs_js_popup_getsessiondata('linktarget');
				nxs_js_log(linktarget);
				
				var linktitle = nxs_js_popup_getsessiondata('linktitle');
				nxs_js_log(linktitle);
				
				anchorElm.textContent = linktext;
				
				var atts = 
				{
					'href' : linkhref,
					'target' : linktarget,
					'title' : linktitle,
					'id' : '',	// wipe the ID
				}
				
				editor.dom.setAttribs(anchorElm, atts);
				editor.selection.select(anchorElm);
				
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
				tooltip: 'Insert/edit link NXS 3',
				shortcut: 'Ctrl+K',
				onclick: createLinkList(showDialog),	// showDialog is de callback
				stateSelector: 'a[href]'
			}
		);
	
		this.showDialog = showDialog;
	}
);

