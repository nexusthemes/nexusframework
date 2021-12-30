<?php

function nxs_postwizard_pdt2_gettitle($args)
{
	return nxs_l18n__("Blogpost title[nxs:popup]", "nxs_td");
}

function nxs_postwizard_pdt2_renderpreview($args)
{
	?>
	<div class="content2">
    <div class="box">
        <div class="box-title">
            <h4>&nbsp;</h4>
         </div>
        <div class="box-content">
        	<span class='title'>
        		<?php nxs_l18n_e("Blogpost preview[nxs:popup,ddl]", "nxs_td"); ?>
        	</span>
        </div>
    </div>
    <div class="nxs-clear"></div>
  </div> <!--END content-->
	<?php
}

function nxs_postwizard_pdt2_home_getsheethtml($args)
{
	//
	extract($args);

	$meta = nxs_getsitemeta();
	
	$catargs = array();
	$catargs['hide_empty'] = 0;
	$categories = get_categories($catargs);
	
	$categoriesfilters = array();
  $categoriesfilters["uncategorized"] = "skip";
  nxs_getfilteredcategories($categories, $categoriesfilters);	

	$selectedcategoryids = "";
	
	if ($clientpopupsessiondata != null) { extract($clientpopupsessiondata); }
	if ($clientshortscopedata != null) { extract($clientshortscopedata); }
	
	$result = array();
	
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	

     	<?php nxs_render_popup_header(nxs_l18n__("Blogpost", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		            
		      <!--  -->
		
		      <div class="content2">
		        <div class="box">
		            <div class="box-title">
		                <h4><?php nxs_l18n_e("Wizard2[nxs:popup,label]", "nxs_td"); ?></h4>
		             </div>
		            <div class="box-content">
		            	<a href="#" onclick="nxs_js_popup_sessiondata_clear_dirty(); nxs_js_popup_site_neweditsession('newposthome'); return false;" class="nxsbutton1 nxs-float-right"><?php nxs_l18n_e("Change wizard[nxs:popup,button]", "nxs_td"); ?></a>
		            	<span class='title'><?php nxs_l18n_e("Blogpost", "nxs_td"); ?></span>
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
		            	<input type='text' id='pagetitle' type='text' placeholder='<?php nxs_l18n_e("Title of the new post[nxs:popup]", "nxs_td"); ?>' value='<?php echo $pagetitle; ?>' class="nxs_defaultenter" />
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		
					<!-- -->

      <!-- cats -->
      
      		      
					<!-- categories -->
		      
		 			<div class="content2">
		        <div class="box">
		          <div class="box-title">
		            <h4><?php nxs_l18n_e("Categories[nxs:popup,label]", "nxs_td"); ?></h4>
		          </div>
		          <div class="box-content">
								<ul class="cat-checklist" id='selectedcategoryids'>
						      <?php 
						    	foreach ($categories as $category)
						    	{
						    		$termid = $category->term_id;
						    		$name = $category->name;
		
						    		$key = "[" . $termid . "]";
						    		if (nxs_stringcontains($selectedcategoryids, $key))
						    		{
						    			$possiblyselected = "checked='checked'";
						    		}
						    		else
						    		{
						    			$possiblyselected = "";
						    		}
						    		
						    		?>
										<li>
											<label>
			            			<input class='selectable_category' id="catid_<?php echo $termid; ?>" type="checkbox" <?php echo $possiblyselected; ?> onchange="nxs_js_popup_sessiondata_make_dirty();" />
			            			<?php echo $name; ?>
			            		</label>
			            	</li>
							    	<?php
							    }
							    ?>	   
							  </ul>
							  <div class='nxs-clear nxs-margin-top5'></div>
							  <a class="nxsbutton1 nxs-float-left" href="<?php echo admin_url('edit-tags.php?taxonomy=category'); ?>"><?php nxs_l18n_e("Edit categories[nxs:popup,button]", "nxs_td"); ?></a>
		          </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->    		
		      
				<!-- -->
	      
	      <div class="content2">
	        <div class="box">
	          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='create(); return false;'><?php nxs_l18n_e("Create", "nxs_td"); ?></a>
	          <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup]", "nxs_td"); ?></a>            
	          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick="nxs_js_popup_sessiondata_clear_dirty(); nxs_js_popup_site_neweditsession('newposthome'); return false;"><?php nxs_l18n_e("Back[nxs:popup,button]", "nxs_td"); ?></a>
	       	</div>
	        <div class="nxs-clear">
	        </div>
	      </div> <!--END content-->
 		  </div>

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
			nxs_js_popup_storestatecontroldata_textbox("pagetitle", "slug");	// slug is filled with pagetitle too
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
				nxs_js_popup_negativebounce('<?php nxs_l18n_e("The title is a required field[nxs:popup,ddl]", "nxs_td"); ?>');
		  	jQuery('#pagetitle').focus();
				return;
			}
			
			var waitgrowltoken = nxs_js_alert_wait_start("<?php nxs_l18n_e("Creating page[nxs:popup,ddl]", "nxs_td"); ?>");
			
			var args = nxs_js_getescaped_popupsession_data();
			nxs_js_addnewarticlewithpostwizardwithargs(titel, slug, 'post', 'publish', postwizard, args, 
			function(response)
			{
				nxs_js_alert_wait_finish(waitgrowltoken);
				nxs_js_alert('<?php nxs_l18n_e("Redirecting to page[nxs:popup,ddl]", "nxs_td"); ?>');
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

		function nxs_js_startcategorieseditor()
		{
			nxs_js_savepopupdata(); 
			nxs_js_popup_setsessiondata("nxs_categorieseditor_invoker", nxs_js_popup_getcurrentsheet()); 
			nxs_js_popup_setsessiondata("nxs_categorieseditor_appendnewitemsto", "<?php echo $id;?>"); 
			
			nxs_js_popup_navigateto("categorieseditor");
		}
		
	</script>
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_postwizard_pdt2_setuppost($args)
{
	if ($postid == "")
	{
		echo "postid niet meegegeven";
		return;
	}
	
	// categories
	if ($selectedcategoryids != "")
	{
		// voeg categorien toe aan deze postid
		// [5][2][1]
		$newcats = array();
		$splitted = explode("[", $selectedcategoryidss);
		foreach($splitted as $splittedpiece)
		{
			// bijv. "1]"
			if ($splittedpiece == "")
			{
				// ignore
			}
			else
			{
				// bijv. "1]"
				$newcats[] = substr($splittedpiece, 0, -1);
			}			
		}
		
		// Update categories
		wp_set_post_categories($postid, $newcats);
	}
	
	$args["pagetemplate"] = "blogentry";
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
						"placeholdertemplate" => "undefined",
						"args" => array
						(
							//"foo" => "bar",
						),
					),
				)
			),
		)
	);
}