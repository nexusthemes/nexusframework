(function($) {
 
    var MyYoastPlugin = function()
    {
        YoastSEO.app.registerPlugin('myYoastPlugin', {status: 'loading'});
 
        this.getData();
    };
 
    MyYoastPlugin.prototype.getData = function()
    {
        var _self = this;
        
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
								
								_self.custom_content = response.html;
 
				        YoastSEO.app.pluginReady('myYoastPlugin');
				 
				        YoastSEO.app.registerModification('content', $.proxy(_self.getCustomContent, _self), 'myYoastPlugin', 5);
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
        
       
    };
 
    MyYoastPlugin.prototype.getCustomContent = function (content)
    {
    	// var mceid = $('#acf-yoast_fancyeditor textarea').prop('id');
      //return this.custom_content + tinymce.editors[mceid].getContent() + content;
      return this.custom_content + content;
    };
 
    $(window).on('YoastSEO:ready', function ()
    {
      new MyYoastPlugin();
    });
})(jQuery);