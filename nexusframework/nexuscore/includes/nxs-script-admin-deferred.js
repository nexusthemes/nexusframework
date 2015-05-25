

function nxs_js_identify_rows_as_recursivelistitems()
{
	// OK
	jQ_nxs(".nxs-layout-editable").each
	(
		function(pcindex, pagecontainer)
		{
			// add dummy rows as dropping points
			var allrows = jQ_nxs(pagecontainer).find(".nxs-row");
			
			// setup initial values
			var rowid = 1;
			allrows.each
			(
				function(index, rowelement)
				{
					jQ_nxs(rowelement).data('rowid', rowid);
					jQ_nxs(rowelement).data('listitemid', '');
					jQ_nxs(rowelement).data('recursionrequired', '');
					rowid++;
				}
			);

			var sofar = "";
			var initialcurrentdepth = 1;
			nxs_js_menuid_siblingcounters = {};
			nxs_js_menuid_preventendlessloop = 1000;			
								
			//
			nxs_js_set_listitemid_recursive(allrows, sofar, initialcurrentdepth);
		}
	);
}

function nxs_js_gui_add_virtual_droppable_pagerows_for_list()
{
	if (jQ_nxs("body").hasClass("nxs-drop-tag"))
	{
		nxs_js_log("dont do it again!");
		return;
	}
	jQ_nxs("body").addClass("nxs-drop-tag");

	nxs_js_identify_rows_as_recursivelistitems();
	
	var element = jQ_nxs('#nxs-drag-container-helper').data('sourcedragelement');
	var sourcerow = jQ_nxs(element).closest('.nxs-row');
	
	var listitemidofsourcerow = jQ_nxs(sourcerow).data('listitemid');
	//nxs_js_log("bronregel:" + listitemidofsourcerow);
	
	// als we hier komen zijn alle menu item ids gezet...
	// OK
	jQ_nxs(".nxs-layout-editable").each
	(
		function(pcindex, pagecontainer)
		{
			var rowcounter = 0;
			var allrows = jQ_nxs(pagecontainer).find(".nxs-row");
			allrows.each
			(
				function(index, rowelement)
				{
					rowcounter++;
					
					var listitemid = jQ_nxs(rowelement).data('listitemid');
					//nxs_js_log('listitemid :' + listitemid);
					
					if (listitemid == listitemidofsourcerow)
					{
						//nxs_js_log('ignoring :' + listitemid + '(dit zijn we zelf)');
					}
					else if (listitemid.startsWith(listitemidofsourcerow))
					{
						// het huidige item is een child van het item dat wordt gedragd,
						// ignoren
						//nxs_js_log('ignoring :' + listitemid + '(dit is een child van het draggable item)');
					}
					else if (false)
					{
						// TODO; er is nog een specifieke uitzonderingssituatie:
						// indien de huidige item de parent is van het item dat wordt gedragged
						// dat kan het item alleen als sibling worden geplaatst,
						// en niet als child (immers: het is dan reeds de child!)
					}
					else
					{
						//nxs_js_log('droppable :' + listitemid + '(dit is geen child van het draggable item)');
						
						var depth = nxs_js_getmenuitemdepth_in_dom(rowelement);
						var parentdepth = depth - 1;
						var superparentdepth = depth - 2;
						var childdepth = depth + 1;
						
						
						var pagerowselement = jQ_nxs(pagecontainer).find(".nxs-postrows")[0];
						
						var line = "<div class='nxs-padding-menu-item'><div class='content2 nxs-border-dash nxs-drop-area nxs-margin-left" + (depth - 1) * 30 + " nxs-show-no-hover-with-drag nxs-show-hover-with-drag'><div class='nxs-clear'>&nbsp;</div></div></div>";
						var current_element_accepting_drop = jQ_nxs(nxs_js_gui_getnewtempdroppablerow2(pagerowselement, line));
						jQ_nxs(current_element_accepting_drop).data('destinationdragtype', 'menuitem');
						jQ_nxs(current_element_accepting_drop).data('destinationdragmeta', index + "_" + depth);
						jQ_nxs(rowelement).append(current_element_accepting_drop);
						
						var line = "<div class='nxs-padding-menu-item'><div class='content2 nxs-border-dash nxs-drop-area nxs-margin-left" + (childdepth - 1) * 30 + " nxs-show-no-hover-with-drag nxs-show-hover-with-drag'><div class='nxs-clear'>&nbsp;</div></div></div>";
						var current_element_accepting_drop = jQ_nxs(nxs_js_gui_getnewtempdroppablerow2(pagerowselement, line));
						jQ_nxs(current_element_accepting_drop).data('destinationdragtype', 'menuitem');
						jQ_nxs(current_element_accepting_drop).data('destinationdragmeta', index + "_" + childdepth);
						jQ_nxs(rowelement).append(current_element_accepting_drop);
						
						// promote to parent
						if (depth > 1)
						{
							var line = "<div class='nxs-padding-menu-item'><div class='content2 nxs-border-dash nxs-drop-area nxs-margin-left" + (parentdepth - 1) * 30 + " nxs-show-no-hover-with-drag nxs-show-hover-with-drag'><div class='nxs-clear'>&nbsp;</div></div></div>";
							var current_element_accepting_drop = jQ_nxs(nxs_js_gui_getnewtempdroppablerow2(pagerowselement, line));
							jQ_nxs(current_element_accepting_drop).data('destinationdragtype', 'menuitem');
							jQ_nxs(current_element_accepting_drop).data('destinationdragmeta', index + "_" + parentdepth);
							jQ_nxs(rowelement).append(current_element_accepting_drop);
						}
						
						// promote to superparent
						if (depth > 2)
						{
							var line = "<div class='nxs-padding-menu-item'><div class='content2 nxs-border-dash nxs-drop-area nxs-margin-left" + (superparentdepth - 1) * 30 + " nxs-show-no-hover-with-drag nxs-show-hover-with-drag'><div class='nxs-clear'>&nbsp;</div></div></div>";
							var current_element_accepting_drop = jQ_nxs(nxs_js_gui_getnewtempdroppablerow2(pagerowselement, line));
							jQ_nxs(current_element_accepting_drop).data('destinationdragtype', 'menuitem');
							jQ_nxs(current_element_accepting_drop).data('destinationdragmeta', index + "_" + superparentdepth);
							jQ_nxs(rowelement).append(current_element_accepting_drop);
						}
					}
				}
			);
		}
	);
}
			
function nxs_js_set_listitemid_recursive(allrows, sofar, currentdepth)
{
	allrows.each
	(
		function(index, rowelement)
		{
			var recursionrequired = jQ_nxs(rowelement).data('recursionrequired');
			var currentlistitemid = jQ_nxs(rowelement).data('listitemid');
			var currentrowid = jQ_nxs(rowelement).data('rowid');
			var currentChildDepthIndex = nxs_js_getmenuitemdepth_in_dom(rowelement);
			
			//nxs_js_log("processing row: " + currentrowid);
			
			if (currentdepth > currentChildDepthIndex)
			{
				// next element please (continue loop)
				// jQ_nxs(rowelement).data('recursionrequired', 'false');
				//nxs_js_log("ignoring row: " + currentrowid);
				return true;
			}
			else
			{
				if (recursionrequired == sofar && sofar == currentlistitemid)
				{						
					//nxs_js_log("processing row : " + currentrowid + " so far; " + sofar);
					
					if (currentChildDepthIndex == currentdepth)
					{
						//nxs_js_log("found leaf for row " + currentrowid);
						
						if (nxs_js_menuid_siblingcounters[currentlistitemid] == null)
						{
							nxs_js_menuid_siblingcounters[currentlistitemid] = 0;
						}
						
						nxs_js_menuid_siblingcounters[currentlistitemid] = nxs_js_menuid_siblingcounters[currentlistitemid] + 1;
						
						//nxs_js_log("new sibling count for [" + currentlistitemid + "] is " + nxs_js_menuid_siblingcounters[currentlistitemid]);
						
						var sofarforthisrow = currentlistitemid + "." + nxs_js_menuid_siblingcounters[currentlistitemid];
						
						jQ_nxs(rowelement).data('listitemid', sofarforthisrow);
						jQ_nxs(rowelement).data('recursionrequired', 'false');
						
						var underscored = sofarforthisrow.split(".").join("-");
						//nxs_js_log("has become;");
						//nxs_js_log(underscored);
						jQ_nxs(rowelement).addClass('nxs-listitemid-x' + underscored);
						
						//nxs_js_log("setting (final) listitemid for row " + currentrowid + " to " + jQ_nxs(rowelement).data('listitemid'));
					}
					else if (currentChildDepthIndex > currentdepth)
					{
						//nxs_js_log("found node for row " + currentrowid);
						
						if (nxs_js_menuid_siblingcounters[currentlistitemid] == null)
						{
							//nxs_js_log("geen nxs_js_menuid_siblingcounters gevonden voor " + currentlistitemid);
						}
						
						var sofarforthisrow = currentlistitemid + "." + nxs_js_menuid_siblingcounters[currentlistitemid];
						jQ_nxs(rowelement).data('listitemid', sofarforthisrow);
						jQ_nxs(rowelement).data('recursionrequired', sofarforthisrow);
						
						//nxs_js_log("setting (non-final) listitemid for row " + currentrowid + " to " + jQ_nxs(rowelement).data('listitemid'));
						
						// recurse!
						nxs_js_menuid_preventendlessloop--;
						if (nxs_js_menuid_preventendlessloop > 0)
						{
							nxs_js_set_listitemid_recursive(allrows, sofarforthisrow, currentdepth + 1);
						}
						else
						{
							//nxs_js_log("preventing endless loop...");
						}
					}
					else
					{
						//nxs_js_log("aha!");
					}
				}
				else
				{
					// ignore; item is finished or will be processed by another invocation
					//nxs_js_log("row: " + currentrowid + " is either already finsihed processing, or will be processed later on");
				}
			}
		}
	);
}

function nxs_js_gui_make_existing_placeholders_droppable_regular()
{
	// restrain the droppable placeholders to the nxs-post-XYZ of the 
	// source, if present 

	var sourcedragelement = jQ_nxs('#nxs-drag-container-helper').data('sourcedragelement');	
	var closestpostid = nxs_js_findclosestpostid_for_dom(sourcedragelement);
	var containers_in_scope;
	if (closestpostid != null)
	{
		// if you only want to allow drag and drop in the 
		// closest container, use the following commented line
		// containers_in_scope = jQ_nxs(".nxs-layout-editable.nxs-post-" + closestpostid);
		// to enable drag and drop between containers, use the following:
		containers_in_scope = jQ_nxs(".nxs-layout-editable");
	}
	else
	{
		// this means we drag a rowtemplate from flyout;
		// allow all editable containers as drop
		containers_in_scope = jQ_nxs(".nxs-layout-editable");
	}

	jQ_nxs(containers_in_scope).each
	(
		function(pcindex, pagecontainer)
		{
			// virtually upgrade placeholder containers as dropping points
			jQ_nxs(pagecontainer).find(".nxs-placeholder").each
			(
				function(index, current_element_accepting_drop)
				{
					try 
					{
						var doelplaceholderid = nxs_js_getfirstplaceholderid_in_dom(current_element_accepting_drop);
						if (doelplaceholderid == null)
						{
							alert("geen placeholderid gevonden in element");
						}
						
						//
						// speciaal uitzonderingsgeval; indien de huidige placeholder zelf nu wordt gedragged
						// dan moeten we deze natuurlijk niet zelf droppable maken
						//
						var dragtype = jQ_nxs('#nxs-drag-container-helper').data('sourcedragtype');
						if (dragtype == 'placeholderswap')
						{
							var sourcedragmeta = jQ_nxs('#nxs-drag-container-helper').data('sourcedragmeta');
							var sourceplaceholderid = sourcedragmeta;
							if (sourceplaceholderid == doelplaceholderid)
							{
								return true;	// this means: continu the loop
							}
						}

						//
						// process normally
						//
						var original = jQ_nxs(current_element_accepting_drop).html();
						jQ_nxs(current_element_accepting_drop).data('nxs-revert-content', original);		// required for reverting back
						
						var width = jQ_nxs(current_element_accepting_drop).width();
						var height = jQ_nxs(current_element_accepting_drop).height();

						jQ_nxs(current_element_accepting_drop).data('destinationdragtype', 'existingplaceholder');
						jQ_nxs(current_element_accepting_drop).data('destinationdragmeta', doelplaceholderid);
						jQ_nxs(current_element_accepting_drop).addClass("nxs-accept-drop");
						
						// nxs-revert-after-dragdrop");
						
						// construct layers for this placeholder
						// layer 1 = wrapped item visualizing the existing content (absolutely positioned; NOT in the document flow)
						// layer 2 = wrapped item representing a semi transparent layer; NOT in the document flow)
						// layer 3 = wrapped item representing a semi transparent layer; NOT in the document flow)
						// inline layer = empty (transparent) layer, required to set the height inline
						var virtuallayer1 = "<div style='width:" + width + "px; height:" + height + "px; position: absolute;'>" + original + "</div>";
						var virtuallayer2 = "<div class='nxs-show-no-hover-with-drag' style='width:" + width + "px; height:" + height + "px; position: absolute;'></div>";
						var virtuallayer3 = "<div class='nxs-show-hover-with-drag' style='width:" + width + "px; height:" + height + "px; position: absolute;'>&nbsp;</div>";

						jQ_nxs(current_element_accepting_drop).find(".nxs-drop-cursor").html(virtuallayer3);

						var inlinelayer = "<div style='width:" + width + "px; height:" + height + "px; display: block; overflow: hidden;'>&nbsp;</div>";
						
						//var combined = virtuallayer1 + virtuallayer2 + virtuallayer3 + inlinelayer;
						// override old html with upgraded variant that allows dropping
						
						//jQ_nxs(current_element_accepting_drop).html(combined);
					} 
					catch(err)
					{
						alert('fee(2);' + err);
					}
				}
			);
		}
	);
}

function nxs_js_gui_add_drop_scaffolding()
{
	//nxs_js_log("nxs_js_gui_add_drop_scaffolding()");
	//
	// add and upgrade gui elements
	//
	var sourcedragtype = jQ_nxs('#nxs-drag-container-helper').data('sourcedragtype');
	//nxs_js_log("sourcedragtype:" + sourcedragtype);
	
	if (sourcedragtype == 'placeholderswap')
	{
		//
		// indien de drag source container een nxs-menu-container is, dan 
		// moeten er geen nieuwe regels worden toegevoegd
		//
		
		var pagecontainer = jQ_nxs(".nxs-layout-editable");		
		
		if (pagecontainer.length == 1)
		{
			if (jQ_nxs(pagecontainer).hasClass("nxs-menu-container"))
			{
				nxs_js_gui_add_virtual_droppable_pagerows_for_list();
			}
			else if (jQ_nxs(pagecontainer).hasClass("nxs-list-container"))
			{
				nxs_js_gui_add_virtual_droppable_pagerows_for_list();
			}
			else if (jQ_nxs(pagecontainer).hasClass("nxs-slideset-container"))
			{
				// regular!
				nxs_js_gui_make_existing_placeholders_droppable_regular();
			}
			else if (jQ_nxs(pagecontainer).hasClass("nxs-genericlist-container"))
			{
				// regular!
				nxs_js_gui_make_existing_placeholders_droppable_regular();
			}
			else if (jQ_nxs(pagecontainer).hasClass("nxs-sidebar-container"))
			{
				nxs_js_gui_make_existing_placeholders_droppable_regular();
			}
			else if (jQ_nxs(pagecontainer).hasClass("nxs-pagelet-container"))
			{
				nxs_js_gui_make_existing_placeholders_droppable_regular();
			}
			else if (jQ_nxs(pagecontainer).hasClass("nxs-subheader-container"))
			{
				nxs_js_gui_make_existing_placeholders_droppable_regular();
			}
			else if (jQ_nxs(pagecontainer).hasClass("nxs-subfooter-container"))
			{
				nxs_js_gui_make_existing_placeholders_droppable_regular();
			}
			else if (jQ_nxs(pagecontainer).hasClass("nxs-header-container"))
			{
				nxs_js_gui_make_existing_placeholders_droppable_regular();
			}
			else if (jQ_nxs(pagecontainer).hasClass("nxs-footer-container"))
			{
				nxs_js_gui_make_existing_placeholders_droppable_regular();
			}
			else if (jQ_nxs(pagecontainer).hasClass("nxs-article-container"))
			{
				nxs_js_gui_make_existing_placeholders_droppable_regular();
			}
			else if (jQ_nxs(pagecontainer).hasClass("nxs-busrulesset-container"))
			{
				nxs_js_gui_make_existing_placeholders_droppable_regular();
			}
			else
			{
			  var otherclass = jQ_nxs(pagecontainer).attr("class");
				nxs_js_log('someothercondition?;' + otherclass);
			}
		}
		else
		{
			// reguliere pagina
			nxs_js_gui_make_existing_placeholders_droppable_regular();
		}
	}
	else if (sourcedragtype == 'toolboxpagerowtemplate')
	{
		// note! its not allowed to drop a pagerowtemplate on an existing placeholder ...
		// nxs_js_log("nxs_js_gui_add_virtual_droppable_pagerows()");	
		nxs_js_gui_add_virtual_droppable_pagerows();
	}							
	else
	{
		nxs_js_log("sourcedragtype not (yet?) supported;" + sourcedragtype);
	}
}

function nxs_js_gui_setup_drop_listeners()
{
	//nxs_js_log("nxs_js_gui_setup_drop_listeners()");
	// 
	// allow dropping on scaffolds (draggable objects)
	//

	// first unregister any scripts
	jQ_nxs(".nxs-accept-drop .ui-droppable").droppable("destroy");
	
	// register new drops
	jQ_nxs(".nxs-accept-drop").droppable
	(
		{
			activeClass: "ui-state-active",
			hoverClass: "nxs-ui-state-hover",
			accept: function(d) 
			{
				// accept drops from any type
				return true;
    },
    over: function(event, ui)
    {
    	// ...
    },
    out: function(event, ui)
    {
    	// ...
    },
			drop: function( event, ui ) 
			{
				var sourcedragelement = jQ_nxs('#nxs-drag-container-helper').data('sourcedragelement');
				var sourcepostid = nxs_js_findclosestpostid_for_dom(sourcedragelement);
				var destinationpostid = nxs_js_findclosestpostid_for_dom(this);
				var containerpostid = nxs_js_getcontainerpostid();

				/*
				nxs_js_log("sourcedragelement:" + sourcedragelement);
				nxs_js_log("sourcepostid:" + sourcepostid);
				
				nxs_js_log("destinationpostid:" + destinationpostid);
				nxs_js_log("containerpostid:" + containerpostid);
				*/
				
				var sourcedragtype = jQ_nxs('#nxs-drag-container-helper').data('sourcedragtype');
				var sourcedragmeta = jQ_nxs('#nxs-drag-container-helper').data('sourcedragmeta');
				var destinationdragtype = jQ_nxs(this).data("destinationdragtype");
				var destinationdragmeta = jQ_nxs(this).data('destinationdragmeta');
				
				if (sourcedragtype == 'placeholderswap')
				{
					if (destinationdragtype == 'existingplaceholder')
					{
						// er vindt een 'swap' plaats; een placeholder wordt van de ene naar
						// een andere plek verplaatst, we doen twee dingen:
						// 1. persisteer de wijzigingen op de server
						// 2. swap de DOM van de containers
						var sourceplaceholderid = sourcedragmeta;
						var destinationplaceholderid = destinationdragmeta;
						nxs_js_swapplaceholders(containerpostid, sourcepostid, sourceplaceholderid, containerpostid, destinationpostid, destinationplaceholderid);
						nxs_js_alert(nxs_js_gettrans('Widgets swapped'));
					} 
					else if (destinationdragtype == 'nieuweregel')
					{
						nxs_js_alert('dragging an existing placeholder to a new row is no longer supported...');
					}
					else if (destinationdragtype == 'menuitem')
					{
						// we verplaatsen het menu item naar de nieuwe rij en we updaten de menu diepte
						var insertafterrowindex = destinationdragmeta.split('_')[0];
						var newdepth = destinationdragmeta.split('_')[1];
						var sourceplaceholderid = sourcedragmeta;
						// sourcepostid == destinationpostid
						nxs_js_updatemenuitemlocation(sourcepostid, sourceplaceholderid, insertafterrowindex, newdepth);
					}
					else
					{
						nxs_js_log("destinationdragtype not (yet?) supported (1);" + destinationdragtype);
					}
				} 
				else if (sourcedragtype == 'toolboxpagerowtemplate')
				{
					if (destinationdragtype == 'nieuweregel')
					{
						var waitgrowltoken = nxs_js_alert_wait_start(nxs_js_gettrans('Adding row'));
						
						var insertafterrowindex = destinationdragmeta;
						var pagerowtemplate = sourcedragmeta;
						var destinationpagerows = jQ_nxs(this).closest(".nxs-postrows")[0];
						var placeholdertemplate = ""; // not applicable
						nxs_js_addnewrowwithtemplate(destinationpostid, insertafterrowindex, pagerowtemplate, placeholdertemplate, destinationpagerows, 
						function()
						{
							nxs_js_alert_wait_finish(waitgrowltoken);
						},
						function()
						{
							nxs_js_alert_wait_finish(waitgrowltoken);
						}
						);
					}
					else
					{
						nxs_js_log("destinationdragtype not (yet?) supported (3);" + destinationdragtype);
					}								
				}							
				else
				{
					nxs_js_log("sourcedragtype not (yet?) supported;" + sourcedragtype);
				}
			},
			tolerance: "pointer"
		}
	);			
}

function nxs_js_addnewrowwithtemplate(postid, insertafterrowindex, pagerowtemplate, placeholdertemplate, element, invokewhenavailable, invokewhenfailed)
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
				"webmethod": "addnewrowwithtemplate",
				
				"postid": postid,
				"insertafterrowindex": insertafterrowindex,
				"pagerowtemplate": pagerowtemplate,
				"placeholdertemplate": placeholdertemplate
			},
			cache: false,
			dataType: 'JSON',
			url: ajaxurl, 
			success: function(insertresponse) 
			{
				nxs_js_log(insertresponse);
				if (insertresponse.result == "OK")
				{
					// insert render result for pagerow ("row insertafterrowindex + 1")
					//nxs_js_log("deze?(2)");
					nxs_js_row_render(postid, insertafterrowindex + 1, 
						function(postid, rowindex, renderresponse)
						{
							var html = renderresponse.html;
							
							// insert the dom element
							if (insertafterrowindex == -1)
							{
								// html toevoegen
								var newhtmlelement = jQ_nxs(html);
								jQ_nxs(newhtmlelement).hide();
								jQ_nxs(element).prepend(newhtmlelement);
								
								var therow = jQ_nxs(element).find(".nxs-row")[0];
								jQ_nxs(therow).slideDown(300, function()
								{
									// de hoogte is aangepast, pas ook de sidebar hoogte aan (indien nodig)
									nxs_js_reenable_all_window_events();
								});
							}
							else
							{
								// insert at certain index
								var pagecontainer = jQ_nxs(element);
								var siblingElement = pagecontainer.children()[insertafterrowindex];
								
								// html toevoegen
								var newhtmlelement = jQ_nxs(html);
								jQ_nxs(newhtmlelement).hide();
								jQ_nxs(siblingElement).after(newhtmlelement);
								
								var therow = jQ_nxs(element).find(".nxs-row")[insertafterrowindex+1];
								jQ_nxs(therow).slideDown(300, function()
								{
									// de hoogte is aangepast, pas ook de sidebar hoogte aan (indien nodig)
									nxs_js_reenable_all_window_events();
								});
							}

							nxs_js_reenable_all_window_events();
							
							invokewhenavailable(insertresponse, renderresponse);
						}
					);
				}
				else if (response.result == "ALTFLOW")
				{
					if (response.altflowid == "rowtemplatenotallowed")
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

function nxs_js_addnewrow(postid, insertafterrowindex, element, invokewhenavailable, invokewhenfailed)
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
				"webmethod": "addnewrow",
				"postid": postid,
				"insertafterrowindex": insertafterrowindex
			},
			cache: false,
			dataType: 'JSON',
			url: ajaxurl, 
			success: function(response) 
			{
				nxs_js_log(response);
				if (response.result == "OK")
				{
					var containerElement = jQ_nxs(".nxs-layout-editable.nxs-post-" + postid)[0];
					nxs_js_refreshallpagerows(postid, containerElement, invokewhenavailable);
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

function nxs_js_initplaceholderwithplaceholdertemplate(postid, placeholderid, placeholdertemplate)
{
	var ajaxurl = nxs_js_get_adminurladminajax();
	jQ_nxs.ajax
	(
		{
			type: 'POST',
			data: 
			{
				"action": "nxs_ajax_webmethods",
				"webmethod": "initplaceholderdata",
				"clientpopupsessioncontext": nxs_js_getescaped_popupsession_context(),
				"placeholderid": placeholderid,
				"postid": postid,
				"containerpostid": nxs_js_getcontainerpostid(),
				"placeholdertemplate": placeholdertemplate,
				"clientqueryparameters": nxs_js_escaped_getqueryparametervalues()
			},
			dataType: 'JSON',
			url: ajaxurl, 
			success: function(response) 
			{
				nxs_js_log(response);
				if (response.result == "OK")
				{
					// TODO: make function for the following logic... its used multiple times...
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

function nxs_js_updatemenuitemlocation(postid, placeholderid, insertafterrowindex, depth)
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
				"webmethod": "updatemenuitemlocation",
				"postid": postid,
				"placeholderid": placeholderid,
				"insertafterrowindex": insertafterrowindex,
				"depth": depth
			},
			cache: false,
			dataType: 'JSON',
			url: ajaxurl, 
			success: function(response) 
			{
				nxs_js_log(response);
				if (response.result == "OK")
				{
					var containerElement = jQ_nxs(".nxs-layout-editable.nxs-post-" + postid)[0];
					jQ_nxs(containerElement).html(response.html);
					// reenable all events
					nxs_js_reenable_all_window_events();
					nxs_js_alert(nxs_js_gettrans('Refreshed'));
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

function nxs_js_swapplaceholders(sourcecontainerpostid, sourcepostid, sourceplaceholderid, destinationcontainerpostid, destinationpostid, destinationplaceholderid)
{
	// GJ: 2012 mei 15; workaround / probleem drag drop dragdrop ui / crash / het zou het beste zijn voor het swappen als deze NIET async
	// zijn. Als we deze functie synced maken ontstaat er een exception bij het verwerken van het draggable object.
	// oorzaak: bij async=true, geldt: event drop -> event revert -> event stop -> verwerking van het resultaat van deze webservice
	// oorzaak: bij async=false, geldt: event drop -> verwerking van het resultaat van deze webservice -> event revert -> event stop -> crash
	// als blijkt dat we in de toekomst toch een async=false nodig hebben, zullen we de webservice aanroep (of in ieder geval
	// de verwerking van het resultaat hiervan) moeten verplaatsen _na_ het stop event...
				
	// invoke ajax call
	var ajaxurl = nxs_js_get_adminurladminajax();
	jQ_nxs.ajax
	(
		{
			async: true,
			type: 'POST',
			data: 
			{
				"action": "nxs_ajax_webmethods",
				"webmethod": "swapplaceholders",
				"sourcecontainerpostid": sourcecontainerpostid,
				"sourcepostid": sourcepostid,
				"sourceplaceholderid": sourceplaceholderid,
				"destinationcontainerpostid": destinationcontainerpostid,
				"destinationpostid": destinationpostid,
				"destinationplaceholderid": destinationplaceholderid,
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
					// haal de additionele response velden op
					var sourcerowindex = response.sourcerowindex;
					var sourcerowhtml = response.sourcerowhtml;
					var destinationrowindex = response.destinationrowindex;
					var destinationrowhtml = response.destinationrowhtml;
					
					var sourcepagecontainer = jQ_nxs(".nxs-layout-editable.nxs-post-" + sourcepostid)[0];
					var sourcepagerowscontainer = jQ_nxs(sourcepagecontainer).find(".nxs-postrows")[0];
					var sourcecontainer = jQ_nxs(sourcepagerowscontainer).children()[sourcerowindex];

					// rerender first row
					var sourceElement = jQ_nxs(sourcepagerowscontainer).children()[sourcerowindex];
					// before updating the dom, we first wipe any functions for notification ajax used by the "old" widgets
					nxs_js_clear_ajaxrefresh_notifications(sourceElement);
					jQ_nxs(sourceElement).replaceWith(sourcerowhtml);
					
					// invoke execute_after_clientrefresh_XYZ for each widget in the affected first row, if present
					nxs_js_notify_widgets_after_ajaxrefresh(sourcecontainer);

					// if the swap is within the same row, updating the source row means we are finished,
					// else we also need to update the destination row
					var isswapwithinthesamerow = (sourcepostid == destinationpostid && sourcerowindex == destinationrowindex);
					if (!isswapwithinthesamerow)
					{
						var destinationpagecontainer = jQ_nxs(".nxs-layout-editable.nxs-post-" + destinationpostid)[0];
						var destinationpagerowscontainer = jQ_nxs(destinationpagecontainer).find(".nxs-postrows")[0];
						var destinationcontainer = jQ_nxs(destinationpagerowscontainer).children()[destinationrowindex];

						// rerender destination row
						var destinationElement = jQ_nxs(destinationpagerowscontainer).children()[destinationrowindex];
						// before updating the dom, we first wipe any functions for notification ajax used by the "old" widgets
						nxs_js_clear_ajaxrefresh_notifications(destinationElement);
						
						jQ_nxs(destinationElement).replaceWith(destinationrowhtml);
						
						// invoke execute_after_clientrefresh_XYZ for each widget in the affected first row, if present
						nxs_js_notify_widgets_after_ajaxrefresh(destinationcontainer);
					}
					else
					{
						// nxs_js_log('only redrawing one row');
					 	// if item was drag and dropped within the same row, re-rendering it won't make sense
					}
					
					// re-enable window events and sizes
					nxs_js_reenable_all_window_events();
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

function nxs_js_addnewarticle(titel, slug, pagetype)
{
	nxs_js_addnewarticle_v2(titel, slug, pagetype, "");
}

function nxs_js_addnewarticle_v2(titel, slug, pagetype, pagesubtype)
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
				"webmethod": "addnewarticle",
				"titel": titel,
				"slug": slug,
				"nxsposttype": pagetype,
				"nxssubposttype": pagesubtype
			},
			cache: false,
			dataType: 'JSON',
			url: ajaxurl, 
			success: function(response) 
			{
				nxs_js_log(response);
				if (response.result == "OK")
				{
					// haal de additionele response velden op
					var postid = response.postid;
					var url = response.url;

					window.location = url;
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

function nxs_js_addnewarticlewithpostwizard(titel, slug, pagetype, postwizard, invokewhenavailable)
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
				"webmethod": "addnewarticle",
				"titel": titel,
				"slug": slug,
				"nxsposttype": pagetype,
				"postwizard": postwizard
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

function nxs_js_addnewarticlewithpostwizardwithargs(titel, slug, pagetype, poststatus, postwizard, args, invokewhenavailable, invokewhenfailed)
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
				"webmethod": "addnewarticle",
				"titel": titel,
				"slug": slug,
				"poststatus": poststatus,
				"nxsposttype": pagetype,
				"postwizard": postwizard,
				"args": args
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
					invokewhenfailed();
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

function nxs_js_addcategory(name, invokewhenavailable)
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
				"webmethod": "addcategory",
				"name": name
			},
			cache: false,
			dataType: 'JSON',
			url: ajaxurl, 
			success: function(response) 
			{
				nxs_js_log(response);
				if (response.result == "OK")
				{
					var catid = response.catid;
					invokewhenavailable(name, catid);
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

function nxs_js_removecategory(catid, invokewhenavailable)
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
				"webmethod": "removecategory",
				"catid": catid
			},
			cache: false,
			dataType: 'JSON',
			url: ajaxurl, 
			success: function(response) 
			{
				nxs_js_log(response);
				if (response.result == "OK")
				{
					invokewhenavailable();
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









function nxs_js_gui_getnewtempdroppablerow(element, message)
{
	var width = "100%;";
	
	var original = "<div class='nxs-scaffoldingrow'>" + message + "</div>";	// representeert een nieuwe regel
		
	var height = 40;	// afmeting van een lege row
		
	var virtuallayer1 = "<div class='nxs-one-whole' style='height:" + height + "px; position: absolute;'><div style='width: 100%; height:" + height + "px; position: inline; text-align: center;'>" + original + "</div></div>";
	var virtuallayer2 = "<div class='nxs-show-no-hover-with-drag nxs-one-whole' style='height:" + height + "px; position: absolute;'></div>";
	var virtuallayer3 = "<div class='nxs-show-hover-with-drag nxs-one-whole' style='height:" + height + "px; position: absolute;'>&nbsp;</div>";
	var inlinelayer = "<div class='nxs-one-whole' style='height:" + height + "px; display: block; margin-top: 2px; margin-bottom: 2px;'>&nbsp;</div>";
		
		var content = virtuallayer1 + virtuallayer2 + virtuallayer3 + inlinelayer;
		
	var line = "<div class='nxs-row1 nxs-remove-after-dragdrop nxs-accept-drop nxs-layout-drop'><div class='nxs-row-container'><ul class='nxs-placeholder-list'><li class='nxs-one-whole' style='list-style: none;'>" + content + "</li></ul><div class='nxs-clear'></div></div></div>";
		return line;
}

function nxs_js_gui_getnewtempdroppablerow2(element, message)
{
	var line = "<div class='nxs-row1 nxs-remove-after-dragdrop nxs-accept-drop'><ul class='nxs-placeholder-list'><li class='nxs-one-whole' style='list-style: none;'>" + message + "</li></ul><div class='nxs-clear'></div></div>";
	return line;
}

function nxs_js_gui_getnewtempdroppablerow3(element, message)
{
	var line = "";
	line += "<div class='nxs-row nxs-not-unistyled  nxs-rowtemplate-one   nxs-listitemid-x-3'>";
	line += "<div class='nxs-row1 nxs-remove-after-dragdrop nxs-accept-drop'><ul class='nxs-placeholder-list'><li class='nxs-one-whole' style='list-style: none;'>" + message + "</li></ul><div class='nxs-clear'></div></div>";
	line += "</div>";
	
	return line;
}

// registers draggable dom elements
function nxs_js_gui_setup_drag_listeners()
{
	//nxs_js_log("nxs_js_gui_setup_drag_listeners()");
	
	// remove draggable features registered before
	jQ_nxs(".nxs-draggable.ui-draggable").draggable("destroy");
	
	// enable dragging of draggable items (placeholders, placeholderrows)
	jQ_nxs(".nxs-draggable").draggable
	(
		{ 
			helper: function(event) 
			{
				return "<div id='nxs-drag-container-helper' class='nxs-admin-wrap'>INIT</div>"; 
			},
			cursor: 'move',
			cursorAt: { top: 0, left: 0 },
			appendTo: 'body',
			start: function(event, ui) 
			{						
				nxs_js_log("drag start!");
				// 
				var scrollrevertid = nxs_js_ui_pushscrollrevert();
				jQ_nxs("html").addClass("nxs-dragging");
				nxs_js_nxsisdragging = true;
			
				// ui: The jQuery object representing the helper that's being dragged.
				
				// nxs_js_log("drag starts!");
				// find the drag container (if any) below the placeholder
				
				// this represens the DOM element that triggers the drag
				var closestpostid = nxs_js_findclosestpostid_for_dom(this);	// could be null if dragging flyout menu ...
				
				var helper = jQ_nxs(this).find(".nxs-drag-helper")[0];
				if (helper != null)
				{
					// set content of the helper
					var helperHtml = jQ_nxs(helper).html();
					jQ_nxs('#nxs-drag-container-helper').html(helperHtml);
					//nxs_js_log("helper:" + helperHtml);
					//
					// reposition the helper such that the location of the mouse cursor is in its center
					//
					
					var helperWidth = jQ_nxs('#nxs-drag-container-helper').width();
					var deltaWidth = (helperWidth / 2);
					//jQ_nxs(this).data('draggable').offset.click.left += deltaWidth;

					var helperHeight = jQ_nxs('#nxs-drag-container-helper').height();
					var deltaHeight = (helperHeight / 2);
					//jQ_nxs(this).data('draggable').offset.click.top += deltaHeight;
				}
				else
				{
					jQ_nxs('#nxs-drag-container-helper').html("helper not found");
				}
				
				// find placeholder element up the DOM
				// het 'this' object is het object waarop de drag start
				// in ons geval is dit de <LI> (cursor)
				
				var sourcedragtype = "";
				var sourcedragmeta = "";
				
				if (jQ_nxs(this).hasClass("nxs-toolbox-item") && jQ_nxs(this).hasClass("nxs-dragtype-pagerowtemplate"))
				{
					// het is een toolbox item en representeert een pagerow
					// sourcedragmeta => pagerowtemplate
					sourcedragtype = "toolboxpagerowtemplate";
					sourcedragmeta = this.id.split("_")[2];
				}
				else if (jQ_nxs(this).hasClass("nxs-existing-pageitem") && jQ_nxs(this).hasClass("nxs-dragtype-placeholder"))
				{
					sourcedragtype = "placeholderswap";
					sourcedragmeta = this.id.split("_")[1];
				}
				else
				{
					alert("unknown source for drag");
				}
				
				jQ_nxs('#nxs-drag-container-helper').data('sourcedragtype', sourcedragtype);
				jQ_nxs('#nxs-drag-container-helper').data('sourcedragmeta', sourcedragmeta);
				jQ_nxs('#nxs-drag-container-helper').data('sourcedragelement', this);
				

				// hier
				nxs_js_gui_add_drop_scaffolding();
				nxs_js_gui_setup_drop_listeners();
				
				nxs_js_ui_popscrollrevert(scrollrevertid);
				//nxs_js_log("drag finishes	!");
				
				if (jQ_nxs("body").hasClass("single-nxs_menu"))
				{
					// het is een menu; in dat geval, zetten we de betrokken menu items
					// even op 50% zodat duidelijk is welke items betrokken zijn
					var therow = jQ_nxs(".nxs-widget-" + sourcedragmeta).closest(".nxs-row");
					var classidentifier = nxs_js_findclassidentificationwithprefix(therow, 'nxs-listitemid-x');
					
					var selector = "div[class*='nxs-listitemid-x" + classidentifier + "']";
					jQ_nxs(selector).addClass("nxs-item-being-dragged");
				}
  },
			revert: function(socketObj) 
			{
				nxs_js_nxsisdragging = false;
				jQ_nxs("html").removeClass("nxs-dragging");
				jQ_nxs("body").removeClass("nxs-drop-tag");
				jQ_nxs(".nxs-item-being-dragged").removeClass("nxs-item-being-dragged");
				
				nxs_js_reenable_all_window_events();
				
				//nxs_js_log("removed nxs-dragging from html");
			
				//nxs_js_log("revert starts!");
				//return false;
				
			    if (socketObj === false) 
			    {
			      // Drop was rejected; don't revert with an animation
			    	//nxs_js_log('drop was rejected');
			        	
								var sourcedragtype = jQ_nxs('#nxs-drag-container-helper').data('sourcedragtype');
								var sourcedragmeta = jQ_nxs('#nxs-drag-container-helper').data('sourcedragmeta');

								if (sourcedragtype == 'placeholderswap')
								{
									nxs_js_alert(nxs_js_gettrans('Widget was not moved. To move the widget release it in a dashed area'));
								}
								else if (sourcedragtype == 'toolboxpagerowtemplate')
								{
									nxs_js_alert(nxs_js_gettrans('Drag the column layout on one of the highlighted sections'));
								}							
								else
								{
									nxs_js_log("sourcedragtype not (yet?) supported;" + sourcedragtype);
								}				
			    	
									//nxs_js_log("revert finishes!");
			    	
			    	//nxs_js_log(this);
			    	nxs_js_reenable_all_window_events();
			      return false;
			    }
			    else 
			    {
									//nxs_js_log("revert finishes (2)!");

			    	//nxs_js_log('drop was accepted');
			      // Drop was accepted, don't revert with an animation
			      nxs_js_reenable_all_window_events();
			      return false;
			    }
			  },
			  stop: function(event, ui) 
			{
				nxs_js_log("drag stop!");
				nxs_js_nxsisdragging = false;
				jQ_nxs("html").removeClass("nxs-dragging");
				//nxs_js_log("removed nxs-dragging from html");
			
				//nxs_js_log("stop starts!");
				nxs_js_gui_cleanup_drag_scaffolding();
				//nxs_js_log("stop finishes!");
				nxs_js_reenable_all_window_events();
				
				// re-enable the drag handlers after 100 msecs,
				// timer is required, otherwise the existing element
				// cannot be re-dragged for some reason...
				setTimeout(function() { nxs_js_reenable_all_window_events(); }, 100);
			},
			drag: function(event, ui)
			{
				nxs_js_log("event drag");
				// Triggered while the mouse is moved during the dragging.
			
				jQ_nxs(".nxs-layout-editable").each
				(
					function(pcindex, pagecontainer)
					{
						if (jQ_nxs(pagecontainer).hasClass("nxs-menu-container") || jQ_nxs(pagecontainer).hasClass("nxs-list-container"))
						{
							// dit moet alleen als er sprake is van een menu / lijst
							
							//nxs_js_log('dragging like crazy...');
							
							//
							// step 1; hide all dropping zones, but show the ones currently showing
							//
							jQ_nxs(pagecontainer).find(".nxs-remove-after-dragdrop").each
							(							
								function(index, element)
								{
									if (!jQ_nxs(element).hasClass("nxs-showinitially"))
									{
										// clean up
										jQ_nxs(element).hide();
									}
								}
							);
							//jQ_nxs(".showing").show();

							//
							// step 2; calculate nearest nxs-row that has scaffolding row
							//
							var nearest = 99999;
							var nearestrow = null;
							jQ_nxs(pagecontainer).find(".nxs-row").each
							(
								function(index, element)
								{
									if (jQ_nxs(element).find(".nxs-remove-after-dragdrop").length > 0)
									{
										// clean up
										var topposition = jQ_nxs(element).offset().top + jQ_nxs(element).height();
										var helperposition = Math.floor(jQ_nxs('#nxs-drag-container-helper').offset().top - 25);
										
										var deltaposition = topposition - helperposition;
										if (deltaposition > 0 && deltaposition < nearest)
										{
											nearest = deltaposition;
											nearestrow = element;
											//nxs_js_log("delta:" + deltaposition + "/nearest:" + nearest);
										}
									}
								}
							);
							
							// step 3; remove any previously showing classes
							jQ_nxs(pagecontainer).find(".showing").removeClass("showing");
							
							//
							// step 4; show each scaffolding item within the closest row
							//
							var scaffolds = jQ_nxs(nearestrow).find(".nxs-remove-after-dragdrop");
							var aantal = jQ_nxs(scaffolds).length;
							if (aantal == 0)
							{
								// nxs_js_log('Expected to show at least one scaffolding row...');
							}
							else if (aantal == 1)
							{
								// if there's one scaffold, always show that one!
								jQ_nxs(scaffolds).show();
							 	jQ_nxs(scaffolds).addClass("showing");
							}
							else if (aantal >= 2)
							{
								//jQ_nxs(scaffolds).show();
								
								var helperpositionleft = jQ_nxs('#nxs-drag-container-helper').offset().left;
								var rowpositionleft = jQ_nxs(nearestrow).offset().left;
								var showwhich = 0;
								var deltaleft = helperpositionleft - rowpositionleft;
								//nxs_js_log("deltaleft:" + deltaleft + ";aantal:" + aantal);
								
								if (aantal == 4)
								{
									if (deltaleft >= 0 && deltaleft <= 30)
									{
										showwhich = 3;
									}
									else if (deltaleft >= 30 && deltaleft <= 60)
									{
										showwhich = 2;
									}
									else if (deltaleft >= 60 && deltaleft <= 90)
									{
										showwhich = 0;
									}
									else if (deltaleft >= 90)
									{
										showwhich = 1;
									}
								}
								else if (aantal == 2)
								{
									if (deltaleft >= 30)
									{
										showwhich = 1;
									}
								}
								else
								{
									if (deltaleft >= 100)
									{
										showwhich = 1;
									}
									else if (deltaleft <= 30)
									{
										showwhich = 2;
									}
								}							
								
								//nxs_js_log('rowpositionleft:' + rowpositionleft);							
								//nxs_js_log('deltaleft:' + deltaleft);							
								// based upon the mouse x we decide whether the show the left, or the right one
								
								jQ_nxs(jQ_nxs(scaffolds)[showwhich]).show();
								jQ_nxs(jQ_nxs(scaffolds)[showwhich]).addClass("showing");
								
								var itembeingdragged = jQ_nxs(".nxs-item-being-dragged");
								var text = jQ_nxs(itembeingdragged).text();
								
								var marker = jQ_nxs(jQ_nxs(scaffolds)[showwhich]).find(".nxs-drop-area");
								
								jQ_nxs(marker).each
								(
									function() 
									{
										if (!jQ_nxs(this).hasClass("nxs-done"))
										{
											jQ_nxs(this).addClass("nxs-done")

											nxs_js_log("adding triggers");
											
											jQ_nxs(marker).css("background-color", "white");
											jQ_nxs(marker).css("outline-width", "thick");
											jQ_nxs(marker).css("outline-style", "dashed");
											jQ_nxs(marker).css("outline-color", "black");
											var h = jQ_nxs(marker).html("<p style='align: center;'>" + text + "(Drop here)</p>");
										}
									}
								);
							}
							else
							{
								nxs_js_log('Expected max 2 scaffolding rows ...');
							}
						}
					}
				);
			}
		}
	);
	
	// by default, the row drag will not be able to scroll,
	// since the nxs-container has overflow: auto
	// jQ_nxs(".nxs-dragrow-handler").each
	// (
	// 	function() 
	// 	{
	// 		// remove any previous mousedown handlers for rowdragfix
	// 		jQ_nxs(this).unbind("mousedown.rowdragfix");
	// 	    jQ_nxs(this).bind
	// 	    (
	// 	    	"mousedown.rowdragfix", 
	// 	    	function() 
	// 	    	{
	// 	    		jQ_nxs("html").addClass("nxs-dragging");
	// 	    	}
	// 	    );
	// 	}
	// );
	
	// enable dragging of rows
	jQ_nxs(".nxs-postrows").sortable
	(
		{
			over: function( event, ui ) 
			{ 
				//nxs_js_log("over!"); 
				var x = jQ_nxs(".nxs-row.ui-sortable-placeholder");
				if (jQ_nxs(x).data("enriched") != "yes")
				{
					// TODO: next lines should be moved to CSS file
					jQ_nxs(".nxs-row").css("opacity", 0.5);
					jQ_nxs(".nxs-row.ui-sortable-helper").css("opacity", 1.0);
					jQ_nxs(".nxs-row.ui-sortable-placeholder").css("opacity", 1.0);

					var h = jQ_nxs(x).height();
					var dashthick = 4;
					if (h - dashthick - dashthick < 0)
					{
						h = 40;
					}
					
					var heightwithoutborders = h - dashthick - dashthick;
					
					var html = '';
					
					//html = 'AAAAA';
					//html = '<li class="nxs-placeholder nxs-one-whole nxs-column-1-1" style="background-color: green; height: 100%;">' + html + '</li>';
					html = '<ul class="nxs-placeholder-list">' + html + '</ul>';
					html = '<div class="nxs-row-container nxs-containsimmediatehovermenu nxs-row1" style="border-color: black; border-width: ' + dashthick + 'px; border-style: dashed; background-color: white;height:' + heightwithoutborders + 'px">' + html + '</div>';
					
					jQ_nxs(x).html(html);
					jQ_nxs(x).css("visibility", "visible"); // .css("background-color", "red");
					//nxs_js_log(x);
					jQ_nxs(x).data("enriched", "yes");
				}
			},
			handle: ".nxs-dragrow-handler",
			scroll: true, 
			//scrollSensitivity: 100,
			//scrollSpeed: 100,
	   		start: function(event, ui) 
			{
				nxs_js_nxsisdragging = true;
				jQ_nxs(ui.item).data("sourcepostid", nxs_js_findclosestpostid_for_dom(ui.item));
				jQ_nxs(ui.item).data("sourcerowindex", ui.item.index());
				
			},
			stop: function(event, ui) 
			{
				nxs_js_nxsisdragging = false;
				jQ_nxs("html").removeClass("nxs-dragging");
				// TODO: next line should be moved to CSS file
				jQ_nxs(".nxs-row").css("opacity", 1.0);
			},
	    	update: function(event, ui) 
			{						
				var sourcerow = ui.item;
				var sourcepostid = jQ_nxs(sourcerow).data("sourcepostid");
				var sourcerowindex = jQ_nxs(sourcerow).data("sourcerowindex");
				var destinationpostid = nxs_js_findclosestpostid_for_dom(ui.item);
				var destinationrowindex = ui.item.index();
				var containerpostid = nxs_js_getcontainerpostid();						
				
				nxs_js_log("source postid:" + sourcepostid);
				nxs_js_log("source row index:" + sourcerowindex);

				nxs_js_log("destination postid:" + destinationpostid);
				nxs_js_log("destination row index:" + destinationrowindex);
				
				
				var options = 
				{
					"waitgrowltext": "One moment ...",
					"happyflowgrowltext": "Moved row",
					"webmethoddata": 
					{
						"webmethod": "moverow",
						"containerpostid": containerpostid,
						"sourcepostid": sourcepostid,
						"sourcerowindex": sourcerowindex,
						"destinationpostid": destinationpostid,
						"destinationrowindex": destinationrowindex,
					}
				};
				nxs_js_invokewebmethod(options, null, null, null);
			},
		}
	);
}

function nxs_js_gui_cleanup_drag_scaffolding()
{
	// revert things requiring reverting...
	jQ_nxs(".nxs-revert-after-dragdrop").each
	(
		function(index, element)
		{
			// clean up
			jQ_nxs(element).removeClass("nxs-revert-after-dragdrop");
			jQ_nxs(element).data('nxs-revert-content', '');
		}
	);
	
	// destroy droppable bits
	jQ_nxs(".nxs-accept-drop .ui-droppable").droppable("destroy");
	
	// remove temporary things (used for rows)
	jQ_nxs(".nxs-remove-after-dragdrop").remove();
	
	// remove rows accepting drops (used for rows)
	jQ_nxs(".nxs-accept-drop").removeClass("nxs-accept-drop");
	
	nxs_js_reenable_all_window_events();
}

function nxs_js_gui_add_virtual_droppable_pagerows()
{
	var scrollstatebefore = { 'scrolltop' : jQ_nxs(window).scrollTop(), 'docheight' : jQ_nxs('html').height() };

	// OK
	jQ_nxs(".nxs-layout-editable").each
	(
		function(pcindex, pagecontainer)
		{
			// filter out 
			if (jQ_nxs(pagecontainer).hasClass("nxs-wpcontent-container")) 
			{
				// continu the foreach jquery loop (not break it; continu!)
				return true;
			}
		
			var text;
			if (jQ_nxs(pagecontainer).hasClass("nxs-subheader-container")) 
			{ 
				text = 'Drop here (subheader)'; 
			}
			else if (jQ_nxs(pagecontainer).hasClass("nxs-subfooter-container")) 
			{ 
				text = 'Drop here (subfooter)'; 
			}
			else if (jQ_nxs(pagecontainer).hasClass("nxs-sidebar-container")) 
			{ 
				text = 'Drop here (sidebar)'; 
			}
			else if (jQ_nxs(pagecontainer).hasClass("nxs-header-container")) 
			{ 
				text = 'Drop here (header)'; 
			}
			else if (jQ_nxs(pagecontainer).hasClass("nxs-footer-container")) 
			{ 
				text = 'Drop here (footer)'; 
			}
			else
			{
			 	text = 'Drop here';
			}
			
			var localizedtext = nxs_js_gettrans(text);
		
			// add dummy rows as dropping points
			var allrows = jQ_nxs(pagecontainer).find(".nxs-row");
			if (allrows.length == 0)
			{			
				var pagerowselement = jQ_nxs(pagecontainer).find(".nxs-postrows")[0];
				
				var newDomElement = jQ_nxs(nxs_js_gui_getnewtempdroppablerow(pagerowselement, localizedtext));
				newDomElement.data('destinationdragtype', 'nieuweregel');
				newDomElement.data('destinationdragmeta', -1);
				jQ_nxs(pagerowselement).append(newDomElement);
			}
			else
			{
				allrows.each
				(
					function(index, rowelement)
					{
						if (index == 0)
						{
							// bij de eerste regel is er iets speciaals aan de hand: 
							// ipv dat we telkens ACHTER de regel een row toevoegen,
							// voegen we hierbij tevens een row toe aan de VOORKANT.
							var newDomElement = jQ_nxs(nxs_js_gui_getnewtempdroppablerow(rowelement, localizedtext));
							newDomElement.data('destinationdragtype', 'nieuweregel');
							newDomElement.data('destinationdragmeta', -1);
							jQ_nxs(rowelement).before(newDomElement);
						}
						
						if (true)
						{
							// we voegen achter iedere rij een tijdelijke placeholder 
							// row toe
							var newDomElement = jQ_nxs(nxs_js_gui_getnewtempdroppablerow(rowelement, localizedtext));
							newDomElement.data('destinationdragtype', 'nieuweregel');
							newDomElement.data('destinationdragmeta', index);
							jQ_nxs(rowelement).after(newDomElement);
						}
					}
				);
			}
		}
	);
	
	// step 2; make rows 0 height
	jQ_nxs(".nxs-remove-after-dragdrop").css('height', '0px');
	jQ_nxs(".nxs-remove-after-dragdrop").css('overflow', 'hidden');
	
	// step 3; determine which elements are visible, and which ones are not
	jQ_nxs(".nxs-remove-after-dragdrop").each
	(
		function(indexer, e)
		{
			if (nxs_js_iselementabovepieceofviewport(e,4))	// 1/4
			{
				jQ_nxs(e).addClass("nxs-aboveviewport");
			}
			else
			{
				jQ_nxs(e).addClass("nxs-inorbelowviewport");
			}
		}
	);
	
	// step 4; make rows visible that were visible above viewport
	jQ_nxs(".nxs-aboveviewport").css('height', 'inherit');
	jQ_nxs(".nxs-aboveviewport").css('overflow', 'inherit');
	
	setTimeout
	(
		function(scrollstatebefore)
		{
			// step 5; if we reach this point, the height will have increased
			var scrollstateafter = { 'scrolltop' : jQ_nxs(window).scrollTop(), 'docheight' : jQ_nxs('html').height() };
			//nxs_js_log("scrollstateafter.docheight:");
			//nxs_js_log(scrollstateafter.docheight);

			// step 6; derive the height difference before and after
			var scrollposdelta = scrollstateafter.docheight - scrollstatebefore.docheight;
			//nxs_js_log("scrollposdelta:");
			//nxs_js_log(scrollposdelta);
			
			// step 7; scroll!!!
			var scrollposto = scrollstateafter.scrolltop + scrollposdelta;
			//nxs_js_log("scrollposto:");
			//nxs_js_log(scrollposto);
			jQ_nxs(window).scrollTop(scrollposto);

			// step 8; make rows visible that were visible in the viewport or below the viewport
			jQ_nxs(".nxs-inorbelowviewport").css('height', 'inherit');
			jQ_nxs(".nxs-inorbelowviewport").css('overflow', 'inherit');
		}, 
		1,	// 1 msec delay to update the gui thread... :/
		scrollstatebefore
	);
}

//
// statemachine processor
//

var nxs_sm_statemachineid;
var nxs_sm_timerid;	// timerid of the timer that invokes the statemachine
var nxs_sm_currentstate = 0;
var nxs_sm_isfinished = false;
var nxs_sm_isprocessingstateonserverside = false;

function nxs_js_sm_handleunexpectederrorwhileactivating(response)
{
	nxs_js_alert_sticky("Bad news; an error occured while activating the theme. Please first check our <a target='_blank' href='http://nexusthemes.com/support/how-to-install-a-wordpress-theme/'>installation guide</a>. The good news is that we can try to help you out, if you <a target='_blank' href='http://www.nexusthemes.com'>contact us</a>.");

	jQ_nxs("#waitwrap").hide();
	jQ_nxs("#errorwrap").show();
	
	if (response != null)
	{
		if (response.responseText != null)
		{
			var lowercase = response.responseText.toLowerCase();
			if (lowercase.indexOf("under development") > -1)
			{
				nxs_js_alert_sticky("Hint: site is under development.");
			}
			else if (lowercase.indexOf("bytes exhausted (tried to allocate") > -1)
			{
				// solutions; http://wordpress.org/support/topic/memory-exhausted-error-in-admin-panel-after-upgrade-to-28
				nxs_js_alert_sticky("Hint: not enough memory. See http://wordpress.org/support/topic/memory-exhausted-error-in-admin-panel-after-upgrade-to-28");
			}
			else if (lowercase.indexOf("maximum execution time") > -1 && lowercase.indexOf("exceeded") > -1)
			{
				nxs_js_alert_sticky("Problem: max time-out exceeded. Solution; Import the initial content manually.");
			}
			else
			{
				nxs_js_alert_sticky("Sorry, no hint available");
			}
		}
	}
}

function nxs_js_extendlog(log, shouldscroll)
{
	// empty
	jQ_nxs("#nxsprocessingspacer").html("");
	
	jQ_nxs('#nxsprocessingindicator').append(log);
	if (shouldscroll)
	{
		nxs_js_logscrolldown();
	}
	
	jQ_nxs('img').load
	(
		function()
		{
			if (shouldscroll)
			{
				nxs_js_logscrolldown();
			}
		}
	);
}

function nxs_js_logscrolldown()
{
	//nxs_js_log('scrolling down');
	var height = jQ_nxs('#nxsprocessingwrapper')[0].scrollHeight;
	jQ_nxs('#nxsprocessingwrapper').stop();
  jQ_nxs('#nxsprocessingwrapper').animate({scrollTop: height}, 1000);
}
		
function nxs_js_sm_processsmstate()
{
	if (nxs_sm_statemachineid == null)
	{
		nxs_js_alert("error; nxs_sm_statemachineid not set");
	}
	
	if (!nxs_sm_isprocessingstateonserverside)
	{
		nxs_sm_isprocessingstateonserverside = true;

		if (!nxs_sm_isfinished)
		{
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "processsmstate",
						"statemachineid": nxs_sm_statemachineid,
						"currentstate": nxs_sm_currentstate,
					},
					cache: false,
					dataType: 'JSON',
					url: ajaxurl,
					async: true,
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							nxs_js_extendlog("<p>" + response.log + "</p>", true);
							if (response.nextstate == "finished")
							{
								nxs_sm_isfinished = true;
							}
							else
							{
								// proceed to next step
								nxs_sm_currentstate = response.nextstate;
								nxs_sm_isfinished = false;
							}

							// allow next async thread to execute next request
							nxs_sm_isprocessingstateonserverside = false;
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
						// stop spinning! (!)
						nxs_js_sm_handleunexpectederrorwhileactivating(response);
					}
				}
			);
		}
		
		if (nxs_sm_isfinished == true)
		{
			// no more!
			clearInterval(nxs_sm_timerid);
			jQ_nxs('#nxsprocessingspacer').hide();
			jQ_nxs('#nxsprocessingspacer2').hide();
			jQ_nxs('#nxsspinner').hide();			
			jQ_nxs("#waitwrap").hide();
			jQ_nxs("#finishedwrap").show();
		}
	}
	else
	{
		// busy!
	}
}