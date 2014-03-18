function nxs_js_to_verstuurcontactformulier(postid, placeholderid)
{
	var naam = jQuery("#" + placeholderid + "_naam").val();
	if (naam == '')
	{
		nxs_js_alert(nxs_js_gettrans("Please enter your name"));
		jQuery("#" + placeholderid + "_naam").focus();
		return;
	}
	var email = jQuery("#" + placeholderid + "_email").val();
	if (!nxs_js_validateemail(email))
	{
		nxs_js_alert(nxs_js_gettrans("Please enter your email address"));
		jQuery("#" + placeholderid + "_email").focus();
		return;
	}
	
	var tel = jQuery("#" + placeholderid + "_tel").val();
	var company = jQuery("#" + placeholderid + "_company").val();
	
	var msg = jQuery("#" + placeholderid + "_msg").val();
	if (msg == '')
	{
		nxs_js_alert(nxs_js_gettrans("Please enter your message"));
		jQuery("#" + placeholderid + "_email").focus();
		return;
	}
	
	var isakkoord;
	var voorwaardenbox = jQuery("#" + placeholderid + "_voorwaarden");
	if (jQuery(voorwaardenbox).length != 0)
	{
		//nxs_js_log(jQuery(voorwaardenbox).attr('checked'));
		// box is aanwezig; check of de gebruiker akkoord is
		if (!jQuery(voorwaardenbox).attr('checked'))
		{
			nxs_js_alert(nxs_js_gettrans('First accept the conditions'));
			jQuery("#" + placeholderid + "_voorwaarden").focus();
			return;
		}
		isakkoord = jQuery("#" + placeholderid + "_voorwaarden").attr('checked');
	}
	else
	{
		isakkoord = "nvt";
	}
	
	nxs_js_alert(nxs_js_gettrans('One moment'));
	jQuery("#" + placeholderid + "_button").hide();
	
	// invoke ajax call
	var ajaxurl = nxs_js_get_adminurladminajax();
	jQuery.ajax
	(
		{
			type: 'POST',
			data: 
			{
				"action": "nxs_ajax_webmethods",
				"webmethod": "contactform",
				"containerpostid": nxs_js_getcontainerpostid(),
				"postid": postid,
				"placeholderid": placeholderid,
				"naam": naam,
				"email": email,
				"tel": tel,
				"company": company,
				"msg": msg,
				"isakkoord": isakkoord
			},
			cache: false,
			dataType: 'JSON',
			url: ajaxurl, 
			success: function(response) 
			{
				nxs_js_log(response);
				if (response.result == "OK")
				{
					// haal de additionele response velden op, 
					// navigeer naar de pagina die is doorgegeven van de server
					var url = response.url;
					nxs_js_redirect(url);
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