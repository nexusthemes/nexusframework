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
			data['class'] = anchorElm ? dom.getAttrib(anchorElm, 'class') : null;
			data.title = anchorElm ? dom.getAttrib(anchorElm, 'title') : '';
			
			if (onlyText) 
			{
				textListCtrl = {
					name: 'text',
					type: 'textbox',
					size: 40,
					label: 'Text to display',
					onchange: function() {
						data.text = this.value();
					}
				};
			}
	
			if (linkList) 
			{
				linkListCtrl = {
					type: 'listbox',
					label: 'Link list',
					values: buildLinkList(),
					onselect: linkListChangeHandler,
					value: editor.convertURL(data.href, 'href'),
					onPostRender: function() {
						linkListCtrl = this;
					}
				};
			}
	
			// editor.settings zijn alle initialisatie properties van de tinymce editor instance
			
			// target_list => This option lets you specify a predefined list of targets for the link dialog.
	
			// de mogelijk TARGET="" waarden die gekozen kunnen worden
			if (editor.settings.target_list !== false) 
			{
				targetListCtrl = {
					name: 'target',
					type: 'listbox',
					label: 'Target',
					values: buildValues('target_list', 'target', [{text: 'None', value: ''}, {text: 'New window', value: '_blank'}])
				};
			}
	
			// de REL="" property die gekozen kan worden bij een link
			if (editor.settings.rel_list) {
				relListCtrl = {
					name: 'rel',
					type: 'listbox',
					label: 'Rel',
					values: buildValues('rel_list', 'rel', [{text: 'None', value: ''}])
				};
			}
	
			// de CLASS property die gekozen kan worden bij een link
			if (editor.settings.link_class_list) {
				classListCtrl = 
				{
					name: 'class',
					type: 'listbox',
					label: 'Class',
					values: applyPreview(buildValues('link_class_list', 'class'))
				};
			}
	
			// bepaalt of de mogelijkheid aan of uit staat om een TITLE="" te kunnen zetten bij het aanmaken van een link
			if (editor.settings.link_title !== false) 
			{
				linkTitleCtrl = 
				{
					name: 'title',
					type: 'textbox',
					label: 'Title',
					value: data.title
				};
			}
			
			// hier wordt het EDIT scherm opgestart
	
			win = editor.windowManager.open
			(
				{
					title: 'Insert link NXS',
					data: data,
					body: 
					[
						{
							name: 'href',
							type: 'filepicker',
							filetype: 'file',
							size: 40,
							autofocus: true,
							label: 'Url',
							onchange: urlChange,
							onkeyup: urlChange
						},
						textListCtrl,
						linkTitleCtrl,
						buildAnchorListControl(data.href),
						linkListCtrl,
						relListCtrl,
						targetListCtrl,
						classListCtrl
					],
					onSubmit: nxs_handlesubmitlinkform
				}
			);
		}
		
		// hierboven staan functies
		// ------------------
		// hieronder wordt de knop toegevoegd bij initialisatie van de link plugin
	
		editor.addButton
		(
			'link', 
			{
				icon: 'link',
				tooltip: 'Insert/edit link NXS 2',
				shortcut: 'Ctrl+K',
				onclick: createLinkList(showDialog),	// showDialog is de callback
				stateSelector: 'a[href]'
			}
		);
	
		this.showDialog = showDialog;
	}
);