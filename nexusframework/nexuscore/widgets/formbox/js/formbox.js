function nxs_js_formbox_handlestartprocessing(postid, placeholderid)
{
	// growl
	//nxs_js_alert_veryshort("...");
	
	// show waiting animation
	jQuery("#nxs-widget-" + placeholderid).find(".nxs-form-wwl").css('display', 'inline');
	jQuery("#nxs-widget-" + placeholderid).find(".nxs-form-submit").css('opacity', 0.5);
	
	//
	jQuery("#nxs-widget-" + placeholderid).find(".nxs-form-submit").addClass('nxs-being-processed');
}

function nxs_js_formbox_handlefinishedprocessing(postid, placeholderid)
{
	// show waiting animation
	jQuery("#nxs-widget-" + placeholderid).find(".nxs-form-wwl").css('display', 'none');
	jQuery("#nxs-widget-" + placeholderid).find(".nxs-form-submit").css('opacity', 1.0);
	
	// re-enable 
	jQuery("#nxs-widget-" + placeholderid).find(".nxs-form-submit").removeClass('nxs-being-processed');
}


function nxs_js_formbox_send(postid, placeholderid)
{
	if (jQuery("#nxs-widget-" + placeholderid).find(".nxs-form-submit").hasClass('nxs-being-processed'))
	{
		console.log("stop poking me!");
		return;
	}
	
	// first we check if all form items that require explicit user consent prior to sending are checked
	// find input fields
	var numberofmissedexplicitconsents = 0;
	jQuery("#nxs-widget-" + placeholderid).find(".nxs-requires-explicitconsent-before-sending").each
	(
		function(i)
		{
			if (!jQuery(this).is(':checked'))
			{
				if (numberofmissedexplicitconsents == 0)
				{
					//nxs_js_log("no checked for;");
					//nxs_js_log(this);
					numberofmissedexplicitconsents++;

					var text = jQuery(this).attr("data-textnoconsent");
					alert(text);
					
					return;
				}
				else 
				{
					// one message is enough; ignore the next ones (would mean multiple consents?)
				}
			}
		}
	);
	
	// don't send if one (or more) consents are missing
	if (numberofmissedexplicitconsents > 0)
	{
		return;
	}
	
	
	nxs_js_formbox_handlestartprocessing(postid, placeholderid);
	
	var datatopost = new FormData();

	datatopost.append("action", "nxs_ajax_webmethods");
	datatopost.append("webmethod", "formboxsubmit");
	datatopost.append("containerpostid", nxs_js_getcontainerpostid());
	datatopost.append("postid", postid);
	datatopost.append("placeholderid", placeholderid);
	
	// find input fields
	jQuery("#nxs-widget-" + placeholderid).find("input").each
	(
		function(i)
		{
			var newid = jQuery(this).attr("id");
			//nxs_js_log(newid);

			var newvalue;
			if (jQuery('#' + newid).is(':checkbox'))
			{
				nxs_js_log('checkbox found');
				
				if (jQuery('#' + newid).is(':checked'))
				{
					newvalue = 'checked';
				}
				else
				{
					newvalue = '';
				}
			}
			else if (jQuery('#' + newid).is(':file'))
			{
				nxs_js_log('file inputbox found');

				var files = jQuery('#' + newid)[0].files;
				newvalue = files[0];
			}
			else
			{
				nxs_js_log('not a checkbox found');
				newvalue = jQuery(this).val();
			}
			
			nxs_js_log(newvalue);
			datatopost.append(newid, newvalue);
		}
	);
		
	// find textareas
	jQuery("#nxs-widget-" + placeholderid).find("textarea").each
	(
		function(i)
		{
			var newid = jQuery(this).attr("id");
			nxs_js_log(newid);
			var newvalue = jQuery(this).val();
			nxs_js_log(newvalue);
			datatopost.append(newid, newvalue);
		}
	);
	
	// find dropdownlists
	jQuery("#nxs-widget-" + placeholderid).find("select").each
	(
		function(i)
		{
			var newid = jQuery(this).attr("id");
			nxs_js_log(newid);
			var newvalue = jQuery(this).val();
			nxs_js_log(newvalue);
			datatopost.append(newid, newvalue);
		}
	);

	// invoke ajax call
	var ajaxurl = nxs_js_get_adminurladminajax();
	jQ_nxs.ajax
	(
		{
			type: 'POST',
			data: datatopost,
			cache: false,
			dataType: 'JSON',
			processData: false, // Don't process the files
      contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			url: ajaxurl, 
			beforeSend: function(XHR)
			{
				// FormBox data is shown in the network tab
				// var xhr = new XMLHttpRequest;
				// xhr.open('POST', '/', true);
				// xhr.send(datatopost);
			},
			success: function(response) 
			{
				nxs_js_formbox_handlefinishedprocessing(postid, placeholderid);
				
				nxs_js_log(response);
				if (response.result == "OK")
				{
					if (response.markclientsideelements != null)
					{
						// clear previously marked errors
						jQuery(".invalidcontent").removeClass("invalidcontent");
						// add marked errors
						for (var i = 0; i < response.markclientsideelements.length; i++) 
						{
							jQuery("#" + response.markclientsideelements[i]).addClass("invalidcontent");
						}
					}
					
					if (response.validationerrors != null)
					{
						var msg = "";
						msg += "<ul class='nxs-formerrors'>";
						// loop over response.validationerrors
						for (var i = 0; i < response.validationerrors.length; i++) 
						{
						   msg += "<li><div>" + response.validationerrors[i] + "</div></li>";
						}
						msg += "</ul>";
						
						msg += "<p>&nbsp;</p>";
						
						msg += "<a href=\"#\" onclick=\"nxs_js_popup_pop(); return  false;\" class=\"nxsbutton1\">OK</a>";
						
						// push the existing popupcontext
						nxs_js_popup_push();
						
						nxs_js_htmldialogmessageok(response.validationerrorhead, msg);
						
						// broadcast event that the form was not handled correctly,
						// allowing clientside handlers to act
						nxs_js_log("broadcasting nxs_js_trigger_formvalidationfailed");
						jQuery(window).trigger('nxs_js_trigger_formvalidationfailed');
					}
					else
					{
						// no validation errrors
						var url = response.url;
						nxs_js_redirect(url);
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
				nxs_js_formbox_handlefinishedprocessing(postid, placeholderid);
				
				// knop weer tonen zodat gebruiker het nogmaals kan proberen...
				jQuery("#" + placeholderid + "_button").show();
				nxs_js_popup_notifyservererror();
				nxs_js_log('An error occured: ' + response);
				console.log(response);
			}										
		}
	);			
}