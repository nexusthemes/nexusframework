/**
 * editor_plugin.js
 *
 * Copyright 2012, Nexus Studios
 * Released under GPL License v2.
 */

(function() {
	tinymce.create('tinymce.plugins.NexusEmotionPlugin', 
	{
		init : function(ed, url) 
		{
			var tinymcepopupcontext = nxs_js_popup_getsessiondata('tinymcepopupcontext');
			if (tinymcepopupcontext == 'createemotion')
			{
				nxs_js_log("returning to tinymce...");
				
				// haal de url op van de emotion die we moeten gaan plaatsen,
				// deze is gezet door de popup waar de emotion gekozen is...
				var tinymceemotionurl = nxs_js_popup_getsessiondata('tinymceemotionurl');
				nxs_js_log('we gaan zo verwijzen naar: ' + tinymceemotionurl);

				ed.onInit.add
				(
					function(editor) 
					{
						nxs_js_log('blijkbaar wordt de editor geinitializeerd...');
						
						// gjgj
						var selectedhtml = nxs_js_popup_getsessiondata("tinymceselectedhtml");
						var tinymceemotionurl = nxs_js_popup_getsessiondata('tinymceemotionurl');
						var scaffoldedcontent = nxs_js_popup_getsessiondata("tinymcescaffoldedcontent");
						scaffoldedcontent = scaffoldedcontent.replace("<span>nxs-domselection</span>", "<img class='nxs-emoticon' src='" + tinymceemotionurl + "' />" + selectedhtml + "<span id='caret_pos_holder'></span>");
						nxs_js_log('scaffolded content:');
						nxs_js_log(scaffoldedcontent);

						editor.setContent(scaffoldedcontent, {format : 'raw'});
						nxs_js_log('content updated');

						// focus the editor
						editor.focus();						
						nxs_js_log('focus set');

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

						// resetten van de state
						nxs_js_popup_setsessiondata('tinymcepopupcontext', '');
						
						nxs_js_log("done!...");
					}
	    	);
			}
			else
			{
				//nxs_js_log("initializing nexusemotion v2 in tinymce...");
				//nxs_js_alert('initializing nexusemotion v2 in tinymce...');
			}
			
			this.editor = ed;

			// Register commands
			ed.addCommand
			(
				'mceNexusEmotion', 
				function() 
				{
					nxs_js_log('touched');
					
					var se = ed.selection;
					// save content before modification (undo content)
					var contentbefore = tinyMCE.activeEditor.getContent({format : 'raw'});
					nxs_js_popup_setsessiondata("tinymcecontentbefore", contentbefore);
					
					// save selected content (content to be linked)
					var selectedhtml = se.getContent({format : 'raw'});
					//nxs_js_log("selectedhtml:");
					//nxs_js_log(selectedhtml);
					nxs_js_popup_setsessiondata("tinymceselectedhtml", selectedhtml);
					
					// bij tinymce 3.4br3 oid; probleem dat bij het refreshen van de popup een foutmelding wordt getoond
					// als we de advanced editor gebruiken (simple werkt goed). De foutmelding ontstaat als we
					// de DOM verversen waar de editor in zit; oplossingsrichting; upgrade naar nieuwe versie tiny mce
					
					// bij tinymce 3.5.4.1; het verversen van de DOM werkt goed, echter, de link die we toevoegen
					// wordt direct geclosed, gevolgd door de inhoud van de anchor (<a></a>xyz)
					
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
					nxs_js_popup_setsessiondata('tinymcepopupcontext', 'createemotion');
					nxs_js_popup_navigateto("tinymcepickemotion");
					// the popup will be responsible to redirect back to the 'home' screen,
					// which will eventually re-render this plugin
				}
			);

			//nxs_js_log("de url is als volgt:" + tinymceemotionurl);

			// Register buttons
			ed.addButton('nexusemotion', 
			{
				title : 'Nexus Emotion',
				cmd : 'mceNexusEmotion',
				image : url + '/img/smiley-cool.gif'
			});

			ed.addShortcut('ctrl+k', 'Nexus Emotion', 'mceNexusEmotion');
			
			ed.onNodeChange.add(function(ed, cm, n, co) {
				cm.setDisabled('emotion', co && n.nodeName != 'A');
				cm.setActive('emotion', n.nodeName == 'A' && !n.name);
			});
			
			//nxs_js_log("finished initing tinymce nexusemotion");
		},

		getInfo : function() {
			return {
				longname : 'Nexus Emotion',
				author : 'Nexus Studios',
				authorurl : 'http://www.nexusstudios.nl',
				infourl : 'http://www.nexusstudios.nl',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('nexusemotion', tinymce.plugins.NexusEmotionPlugin);
})();