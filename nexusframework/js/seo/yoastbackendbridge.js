jQuery(window).load
(
	function()
	{
		if (typeof YoastSEO !== 'undefined') 
		{
			console.log('ignoring yoast bridge since the v3 YoastSEO plugin was found, proceeding...');	
		}
		else
		{
			console.log('ignoring yoast bridge since the v3 YoastSEO plugin was not found');
			return;
		}
		
		console.log('loading yoast bridge ...');
		
		ExamplePlugin = function() 
		{
		  YoastSEO.app.registerPlugin( 'examplePlugin', {status: 'ready'} );
		
		  /**
		   * @param modification    {string}    The name of the filter
		   * @param callable        {function}  The callable
		   * @param pluginName      {string}    The plugin that is registering the modification.
		   * @param priority        {number}    (optional) Used to specify the order in which the callables
		   *                                    associated with a particular filter are called. Lower numbers
		   *                                    correspond with earlier execution.
		   */
		  YoastSEO.app.registerModification( 'content', this.myContentModification, 'examplePlugin', 5 );
		}
		
		/**
		 * Adds some text to the data...
		 *
		 * @param data The data to modify
		 */
		ExamplePlugin.prototype.myContentModification = function(data) 
		{
			// get content of the page
			if (nxs_seo_backend_content == '')
			{
				console.log('retrieving content...');
				nxs_seo_backend_content = 'queued';

				$("#wpseo_meta").hide();
				
				var ajaxurl = nxs_js_get_adminurladminajax();
				jQ_nxs.ajax
				(
					{
						type: 'POST',
						data: 
						{
							"action": "nxs_ajax_webmethods",
							"webmethod": "getcontent",
							"contentcontext": "anonymouspost_" + nxs_seo_backend_id + "_" + nxs_seo_backend_id,
							//"clientpopupsessioncontext": nxs_js_getescaped_popupsession_context(),
							//"contenttype": "webpart",
							//"webparttemplate": "render_htmlvisualization",
							//"clientqueryparameters": nxs_js_escaped_getqueryparametervalues()
						},
						cache: false,
						dataType: 'JSON',
						url: ajaxurl, 
						success: function(response) 
						{
							nxs_js_log(response);
							if (response.result == "OK")
							{
								console.log('finished querying ...');
								nxs_seo_backend_content = response.html;
								
								$("#wpseo_meta").show();
								// trigger a refresh of the analysis
								YoastSEO.app.refresh();
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
			else if (nxs_seo_backend_content == 'queued')
			{
				console.log('stop poking me ...');
			}
			else
			{
				console.log('using cached content ...');
			}
			
			
			// in the address bar of the page the post id could be set
			// also in the page, the form's "post_ID" field could be set
			
			console.log('intercepting yoastbridge data;');
			
			if (nxs_seo_backend_content == '' || nxs_seo_backend_content == 'queued')
			{
				// perhaps inject some html in the DOM to indicate its loading ?
				return data;
			}
			else
			{
				return data + nxs_seo_backend_content;
			}
		};
		
		new ExamplePlugin();
	}
);