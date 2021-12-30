<?php

function nxs_postwizard_pdt3_gettitle($args)
{
	return __("Searchresults title[nxs:popup]", "nxs_td");
}

function nxs_postwizard_pdt3_renderpreview($args)
{
	?>
	<div class="content2">
    <div class="box">
        <div class="box-title">
            <h4>&nbsp;</h4>
         </div>
        <div class="box-content">
        	<span class='title'>
        		<?php nxs_l18n_e("Preview searchresults[nxs:popup,ddl]", "nxs_td"); ?>
        	</span>
        </div>
    </div>
    <div class="nxs-clear"></div>
  </div> <!--END content-->
	<?php
}

function nxs_postwizard_pdt3_home_getsheethtml($args)
{
	//
	extract($args);

	$catargs = array();
	$catargs['hide_empty'] = 0;
	$categories = get_categories($catargs);
	
	$categoriesfilters = array();
  $categoriesfilters["uncategorized"] = "skip";
  nxs_getfilteredcategories($categories, $categoriesfilters);	

	$selectedcategoryids = "";

	$meta = nxs_getsitemeta();
	
	if ($clientpopupsessiondata != null) { extract($clientpopupsessiondata); }
	if ($clientshortscopedata != null) { extract($clientshortscopedata); }
	
	$result = array();
	
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	

     	<?php nxs_render_popup_header(nxs_l18n__("Searchresults title[nxs:popup]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		            
		      <!--  -->
		
		      <div class="content2">
		        <div class="box">
		            <div class="box-title">
		                <h4><?php nxs_l18n_e("Wizard[nxs:popup,label]", "nxs_td"); ?></h4>
		             </div>
		            <div class="box-content">
		            	<a href="#" onclick="nxs_js_savepopupdata(); nxs_js_popup_navigateto('newposthome'); return false;" class="nxsbutton1 nxs-float-right"><?php nxs_l18n_e("Other wizard[nxs:popup,button]", "nxs_td"); ?></a>
		            	<span class='title'><?php nxs_l18n_e("Searchresults title[nxs:popup]", "nxs_td"); ?></span>
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
		      <div class="content2">
		        <div class="box">
		            <div class="box-title">
		                <h4><?php nxs_l18n_e("Title[nxs:popup,label]", "nxs_td"); ?></h4>
		             </div>
		            <div class="box-content">
		            	<input type='text' id='pagetitle' type='text' placeholder='<?php nxs_l18n_e("Title of the new searchresults page[nxs:popup,placeholder]", "nxs_td"); ?>' value='<?php echo $pagetitle; ?>' class="nxs_defaultenter" />
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		
					<!-- -->
		     
		    </div>
		  </div>
      
			<!-- -->
      
      <div class="content2">
        <div class="box">
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='create(); return false;'><?php nxs_l18n_e("Create", "nxs_td"); ?></a>
          <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick="nxs_js_savepopupdata(); nxs_js_popup_navigateto('newposthome'); return false;"><?php nxs_l18n_e("Back[nxs:popup,button]", "nxs_td"); ?></a>
       	</div>
        <div class="nxs-clear">
        </div>
      </div> <!--END content-->
		</div>
	</div>
	
	<script>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}
		
		function nxs_js_savepopupdata()
		{
			nxs_js_popup_storestatecontroldata_textbox("pagetitle", "pagetitle");
			nxs_js_popup_storestatecontroldata_textbox("pagetitle", "slug");
			nxs_js_popup_storestatecontroldata_listofcheckbox('selectedcategoryids', 'selectable_category', 'selectedcategoryids');
		}
		
		function create()
		{
			nxs_js_savepopupdata();
			
			var postwizard = nxs_js_popup_getsessiondata('postwizard');
			var titel = nxs_js_popup_getsessiondata('pagetitle');
			var slug = nxs_js_popup_getsessiondata('slug');
		
			if (titel == '')
			{
				nxs_js_popup_negativebounce('<?php nxs_l18n_e("The title is a required field[nxs:negativebounce]", "nxs_td"); ?>');
		  	jQuery('#pagetitle').focus();
				return;
			}
		
			var waitgrowltoken = nxs_js_alert_wait_start("<?php nxs_l18n_e("Creating page[nxs:popup,growl]", "nxs_td"); ?>");
			
			var args = nxs_js_getescaped_popupsession_data();
			
			nxs_js_addnewarticlewithpostwizardwithargs(titel, slug, 'post', 'publish', postwizard, args, 
			function(response)
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				nxs_js_alert('<?php nxs_l18n_e("Redirecting to page[nxs:popup,growl]", "nxs_td"); ?>');
				// redirect to the url as specified in the response
				
				nxs_js_redirect(response.url);
			},
			function(response)
			{
				nxs_js_alert_wait_finish(waitgrowltoken);				
			});
		}
		
		function nxs_js_execute_after_popup_shows()
		{
			jQuery('#pagetitle').focus();
		}
		
	</script>
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_postwizard_pdt3_setuppost($args)
{
	if ($postid == "")
	{
		echo "postid niet meegegeven";
		return;
	}
	
	$args["pagetemplate"] = "searchresults";
	nxs_updatepagetemplate($args);

	//
	//
	//	
	
	nxs_append_posttemplate
	(
		$postid, 
		array
		(
			array
			(
				"pagerowtemplate" => "one", 
				"pagerowid" => nxs_getrandompagerowid(), 
				"pagerowtemplateinitializationargs" => array
				(
					array
					(
						"placeholdertemplate" => "searchresults",
						"args" => array
						(
							"foo" => "bar",							
						),
					),
				)
			),
		)
	);
}

