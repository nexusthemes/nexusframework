<?php
function nxs_popup_genericpopup_tinymcepicklink_getpopup($args)
{
	//
	extract($args);
	
	if ($_REQUEST["clientpopupsessioncontext"]["contextprocessor"] == "postcontent")
	{
		if ($postid == "")
		{
			nxs_webmethod_return_nack("postid not set in context (nxs_ptrtph)");
		}
		$temp_array = array();
	}
	else
	{
		if ($postid == "")
		{
			nxs_webmethod_return_nack("postid not set in context (nxs_ptrtph)");
		}
		if ($placeholderid == "")
		{
			nxs_webmethod_return_nack("placeholderid not set in context (nxs_ptrtph)");
		}
		$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	}
	
	$linktype = $temp_array['linktype'];
	$linktarget = $temp_array['linktarget'];
	$linktitle = $temp_array['linktitle'];
	$linkpostid = $temp_array['linkpostid'];
	$linkexternurl = $temp_array['linkexternurl'];
	
	if ($linkexternurl == "")
	{
		$linkexternurl = "http://";
	}

	// clientpopupsessiondata bevat key values van de client side, deze overschrijven reeds bestaande variabelen
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);

	$result = array();
	$result["result"] = "OK";
	
	// published posts and pages
	$publishedargs = array();
	$publishedargs["post_status"] = "publish";
	$publishedargs["post_type"] = array("post", "page");
	$publishedargs["orderby"] = "post_date";//$order_by;
	$publishedargs["order"] = "DESC"; //$order;
	$publishedargs["numberposts"] = -1;	// allemaal!
  $publishedpages = get_posts($publishedargs);
	
	ob_start();

	?>
  <div class="nxs-admin-wrap">
    <div class="block">
     
    	<?php nxs_render_popup_header(nxs_l18n__("Link[nxs:popup,title]", "nxs_td")); ?>
    	
			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		    	
		      <div class="content2">
		        <div class="box">
		            <div class="box-title">
		              <h4><?php nxs_l18n_e("Link type[nxs:popup,heading]", "nxs_td"); ?></h4>
		             </div>
		            <div class="box-content">
		            	<select id='linktype' onchange="nxs_js_setpopupdatefromcontrols(); nxs_js_popup_refresh_keep_focus(this);">
		            		<option <?php if ($linktype=='internal' || $linktype=='') echo "selected='selected'"; ?> value='internal'><?php nxs_l18n_e("Internal link[nxs:popup,heading]", "nxs_td"); ?></option>
		            		<option <?php if ($linktype=='external') echo "selected='selected'"; ?> value='external'><?php nxs_l18n_e("External link[nxs:popup,heading]", "nxs_td"); ?></option>
		            		<option <?php if ($linktype=='mail') echo "selected='selected'"; ?> value='mail'><?php nxs_l18n_e("Mail link[nxs:popup,heading]", "nxs_td"); ?></option>
		            	</select>
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
		      <?php if ($linktype == "" || $linktype == 'internal') { ?>
		      
		      <div class="content2">
		        <div class="box">
		          <div class="box-title">
		          	<h4><?php nxs_l18n_e("Article[nxs:popup,heading]", "nxs_td"); ?></h4>
		        	</div>
		          <div class="box-content">
		            <select class="chosen-select" id='linkpostid' name='linkpostid' onchange='nxs_js_popup_sessiondata_make_dirty();'>
		        		<?php 
		        			if ($linkpostid == "")
		        			{
		        				$selected = "selected='selected'";
		        			}
		        			else
									{
										$selected = "";
									}
		        			echo "<option value='' $selected>" . nxs_l18n__("Not yet selected[nxs:ddl]", "nxs_td") . "</option>";
		        			
									foreach ($publishedpages as $currentpost)
									{
										$currentpostid = $currentpost->ID;
										$posttitle = nxs_cutstring($currentpost->post_title, 50);
										$selected = "";
										if ($currentpostid == $linkpostid)
										{
											$selected = "selected='selected'";
										}
										else
										{
											$selected = "";
										}
		            		echo "<option value='$currentpostid' $selected	>$posttitle</option>";
									}
		        		?>
		        		</select>
		          </div>
		        </div>
		        <div class="nxs-clear margin"></div>
		      </div> <!--END content-->
		      
		      <?php } else if ($linktype == 'external') { ?>
		      
		      <div class="content2">
		      	<div class="box">
		      		<div class="box-title">
		      			<h4><?php nxs_l18n_e("Url[nxs:popup,heading]", "nxs_td"); ?></h4>
		      		</div>
		      		<div class="box-content">
		      		<input id='linkexternurl' type='text' value='<?php echo $linkexternurl; ?>' />
		      		</div>
		      	</div>
		      	<div class="nxs-clear margin"></div>
		      </div>
		      
		      <?php } else if ($linktype == 'mail') { ?>
		      
		      <div class="content2">
		      	<div class="box">
		      		<div class="box-title">
		      			<h4><?php nxs_l18n_e("Mail address[nxs:popup,heading]", "nxs_td"); ?></h4>
		      		</div>
		      		<div class="box-content">
		      		<input id='linkmailto' type='text' value='<?php echo $linkmailto; ?>' />
		      		</div>
		      	</div>
		      	<div class="nxs-clear margin"></div>
		      </div>
		      
		      <div class="content2">
		      	<div class="box">
		      		<div class="box-title">
		      			<h4><?php nxs_l18n_e("Mail subject[nxs:popup,heading]", "nxs_td"); ?></h4>
		      		</div>
		      		<div class="box-content">
		      		<input id='linkmailsubject' type='text' value='<?php echo $linkmailsubject; ?>' />
		      		</div>
		      	</div>
		      	<div class="nxs-clear margin"></div>
		      </div>
		      
		      <div class="content2">
		      	<div class="box">
		      		<div class="box-title">
		      			<h4><?php nxs_l18n_e("Mail body[nxs:popup,heading]", "nxs_td"); ?></h4>
		      		</div>
		      		<div class="box-content">
		      		<input id='linkmailbody' type='text' value='<?php echo $linkmailbody; ?>' />
		      		</div>
		      	</div>
		      	<div class="nxs-clear margin"></div>
		      </div>
		      
		      <?php } else { ?>
		      
		      <?php } ?>
		      
		      <div class="content2">
		        <div class="box">
		            <div class="box-title">
		              <h4><?php nxs_l18n_e("Link target[nxs:popup,heading]", "nxs_td"); ?></h4>
		             </div>
		            <div class="box-content">
		            	<select id='linktarget' onchange="nxs_js_setpopupdatefromcontrols(); nxs_js_popup_refresh_keep_focus(this);">
		            		<option <?php if ($linktarget=='_self' || $linktarget=='') echo "selected='selected'"; ?> value='_self'><?php nxs_l18n_e("Current window[nxs:popup,heading]", "nxs_td"); ?></option>
		            		<option <?php if ($linktarget=='_blank') echo "selected='selected'"; ?> value='_blank'><?php nxs_l18n_e("New window[nxs:popup,heading]", "nxs_td"); ?></option>
		            	</select>
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->				      
		      
		      <div class="content2">
		      	<div class="box">
		      		<div class="box-title">
		      			<h4><?php nxs_l18n_e("Title[nxs:heading]", "nxs_td"); ?></h4>
		      		</div>
		      		<div class="box-content">
		      		<input id='linktitle' type='text' value='<?php echo $linktitle; ?>' />
		      		</div>
		      	</div>
		      	<div class="nxs-clear margin"></div>
		      </div>
		      
		    </div>
		  </div>
		      
      <div class="content2">
        <div class="box">
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Save", "nxs_td"); ?></a>
          <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK", "nxs_td"); ?></a>
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel", "nxs_td"); ?></a>                    
        </div>
        <div class="nxs-clear"></div>
    	</div> <!--END content-->
  	
    </div>
  </div>
  
  <script type='text/javascript'>
  	
  	function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showokifnotdirty'; 
		}
		
		function nxs_js_setpopupdatefromcontrols()
		{
			// TODO: betere prefixen van namen om "collisons" te voorkomen (iig prefix)
			nxs_js_popup_storestatecontroldata_dropdown('linktype', 'linktype');
			nxs_js_popup_storestatecontroldata_dropdown('linkpostid', 'linkpostid');
			nxs_js_popup_storestatecontroldata_dropdown('linktarget', 'linktarget');
			nxs_js_popup_storestatecontroldata_textbox('linktitle', 'linktitle');
			nxs_js_popup_storestatecontroldata_textbox('linkexternurl', 'linkexternurl');
			nxs_js_popup_storestatecontroldata_textbox('linkmailto', 'linkmailto');
			nxs_js_popup_storestatecontroldata_textbox('linkmailsubject', 'linkmailsubject');
			nxs_js_popup_storestatecontroldata_textbox('linkmailbody', 'linkmailbody');
		}
		
		function nxs_js_savegenericpopup()
		{
			nxs_js_setpopupdatefromcontrols();
			
			// if we reach this point, the sessionstate contains the updated values that the user entered
			// we now decide what to do next depening on the linktype chosen
			var linktype = nxs_js_popup_getsessiondata('linktype');
			if (linktype == 'external')
			{
				var url = nxs_js_popup_getsessiondata('linkexternurl');
				nxs_js_pickurlandnavigatebacktooriginalpopup(url);
			}
			else if (linktype == 'internal')
			{
				var postid = nxs_js_popup_getsessiondata('linkpostid');
				nxs_js_geturl("postid", postid, "notused", 
				function(response) 
				{
					var url = response.url;
					nxs_js_pickurlandnavigatebacktooriginalpopup(url);
				},
				function(response) 
				{
					nxs_js_alert(nxs_js_gettrans('Unable to retrieve the URL'));
				}
				);
			}
			else if (linktype == 'mail')
			{
				// todo: add email address entered by user
				var address = nxs_js_popup_getsessiondata('linkmailto');
				var subject = nxs_js_popup_getsessiondata('linkmailsubject');
				var escapedsubject = escape(subject);
				var body = nxs_js_popup_getsessiondata('linkmailbody');
				var escapedbody = escape(body);
				
				var url = "mailto:" + address + "?Subject=" + escapedsubject + "&Body=" + escapedbody;
				nxs_js_pickurlandnavigatebacktooriginalpopup(url);
			}
			else
			{
				nxs_js_alert('unsupported linktype;' + linktype);
			}
		}
		
		function nxs_js_pickurlandnavigatebacktooriginalpopup(url)
		{
			// store the title such that our tinymce plugin can continue
			var tinymcetitle = nxs_js_popup_getsessiondata('linktitle');
			nxs_js_popup_setsessiondata('tinymcetitle', tinymcetitle);
			
			// store the target for our tinymce plugin to continue
			var tinymcetarget = nxs_js_popup_getsessiondata('linktarget');
			nxs_js_popup_setsessiondata('tinymcetarget', tinymcetarget);
			
			// store the url for our tinymce plugin to use it
			nxs_js_log(url);					
			nxs_js_popup_setsessiondata('tinymcelink', url);
			
			// store the target for our tinymce plugin to continue
			nxs_js_popup_sessiondata_make_dirty();
			
			// redirect back to the sheet that triggered us
			var nxs_tinymce_invoker_sheet = nxs_js_popup_getsessiondata("nxs_tinymce_invoker_sheet");
			nxs_js_popup_navigateto(nxs_tinymce_invoker_sheet);
		}
		
	</script>

	<?php
	
	$html = ob_get_contents();
	ob_end_clean();

	$result["html"] = $html;
	
	return $result;
}
?>