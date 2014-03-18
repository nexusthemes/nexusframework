function nxs_js_contactbox_send(postid, placeholderid)
{
	var datatopost = {
		"action": "nxs_ajax_webmethods",
		"webmethod": "contactboxsubmit",
		"containerpostid": nxs_js_getcontainerpostid(),
		"postid": postid,
		"placeholderid": placeholderid,
	}
	
	// find input fields
	jQuery("#nxs-widget-" + placeholderid).find("input").each
	(
		function(i)
		{
			var newid = jQuery(this).attr("id");
			nxs_js_log(newid);
			var newvalue = jQuery(this).val();
			nxs_js_log(newvalue);
			datatopost[newid] = newvalue;
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
			datatopost[newid] = newvalue;
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
			datatopost[newid] = newvalue;
		}
	);
	
	// nxs_js_log(datatopost);
	
	// invoke ajax call
	var ajaxurl = nxs_js_get_adminurladminajax();
	jQuery.ajax
	(
		{
			type: 'POST',
			data: datatopost,
			cache: false,
			dataType: 'JSON',
			url: ajaxurl, 
			success: function(response) 
			{
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
						msg += "<ul>";
						// loop over response.validationerrors
						for (var i = 0; i < response.validationerrors.length; i++) 
						{
						   msg += "<li>" + response.validationerrors[i] + "</li>";
						}
						msg += "</ul>";
						
						msg += "<p>&nbsp;</p>";
						
						msg += "<a href=\"#\" onclick=\"nxs_js_closepopup_unconditionally_if_not_dirty(); return  false;\" class=\"nxsbutton1\">OK</a>";
						
						nxs_js_htmldialogmessageok(response.validationerrorhead, msg);
						
						//nxs_js_alert(msg);
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
				// knop weer tonen zodat gebruiker het nogmaals kan proberen...
				jQuery("#" + placeholderid + "_button").show();
				nxs_js_popup_notifyservererror();
				nxs_js_log(response);
			}										
		}
	);			
}