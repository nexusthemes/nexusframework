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
	$linkrel = $temp_array['linkrel'];
	$linktext = $temp_array['linktext'];
	$linkpostid = $temp_array['linkpostid'];
	$linkhref = $temp_array['linkhref'];

	if ($linkhref == "")
	{
		$linkhref = "http://";
	}

	// clientpopupsessiondata bevat key values van de client side, deze overschrijven reeds bestaande variabelen
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	if ($linktype == "autoderive")
	{
		if ($linkhref == "")
		{
			// most likely is to want to make a link internally
			$linktype = "internal";
		}
		else if (nxs_stringcontains($linkhref, "@"))
		{
			$linktype = 'mail';
		}
		else
		{
			// it could be a link within this site
			$postid = url_to_postid($linkhref);
			if ($postid > 0)
			{
				$linkpostid	= $postid;
				$linktype = 'internal';
			}
			else
			{
				$linktype = 'external';
			}
		}
	}
	
	if ($linktype == 'external')
	{
		// ensure link starts with https
		$lowercase = strtolower($linkhref);
		if (nxs_stringstartswith($lowercase, "http://"))
		{
			// ignore
		}
		else if (nxs_stringstartswith($lowercase, "https://"))
		{
			// ignore
		}
		else if (nxs_stringstartswith($lowercase, "//"))
		{
			// ignore
		}
		else
		{
			// prefix the link with http, otherwise it will become a relative url
			// which results in support q's
			$linkhref = "http://" . $linkhref;
		}
	}
	
	$result = array();
	$result["result"] = "OK";
	
	// published posts and pages
	$publishedargs = array();
	$publishedargs["post_status"] 	= array("publish", "private");

	$posttypes = array("post", "page");
	$posttypes = apply_filters("nxs_links_getposttypes", $posttypes);
	$publishedargs["post_type"] = $posttypes;

	$publishedargs["orderby"] = "post_date";//$order_by;
	$publishedargs["order"] = "DESC"; //$order;
	$publishedargs["numberposts"] = -1;	// allemaal!
  	$publishedpages = get_posts($publishedargs);

  	$id = 'linkpostid';
  	$value = $linkpostid;

  	$items = get_posts($publishedargs);
	$post = get_post($value);

  	$isfound = false;
	
	nxs_ob_start();

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
		          	<!-- <?php echo $id ?> -->
		            <select id="<?php echo $id ?>" class="chosen-select" name="<?php echo $id ?>" onchange="nxs_js_popup_sessiondata_make_dirty();">
						<?php
							if ($value == "" || $value == "0" || $post == null) 
							{
								$selected = "selected='selected'";
								$isfound = true;
							} 
							
							else 
							{
								$selected = "";
							}
						?>
						<option value='<?php echo $selected ?>'><?php echo nxs_l18n__("No article selected[nxs:heading]", "nxs_td"); ?></option>
						<?php
							foreach ($items as $currentpost) 
							{
								$currentpostid = $currentpost->ID;
								$posttitle = nxs_cutstring($currentpost->post_title, 50);
								$posttitle = htmlspecialchars($posttitle);
							
								if ($posttitle == "") 
								{
									$posttitle = "(leeg, ID:" . $currentpostid . ")";
								}                    
							
								$selected = "";
								
								if ($currentpostid == $value) 
								{
									$selected = "selected='selected'";
									$isfound = true;
								} 
								else 
								{
									$selected = "";
								}
								echo "<option value='$currentpostid' $selected	>$posttitle</option>";
							}
						
							if ($isfound == false)
							{
								if ($post == null)
								{
									// nothing
								}
								else
								{
									$post_mime_type = $post->post_mime_type;
									$title = $post->post_title;
									
									// if its still not found if we reach this far, 
									// it could be that the selected postid points to somewhere else
									// (for example a PDF attachment)
									$selected = "selected='selected'";
									if ("application/pdf" == $post_mime_type)
									{
										echo "<option value='$value' $selected	>PDF: $title (ID: {$value})</option>";
									}
									else
									{
										echo "<option value='$value' $selected	>Attachment (ID: {$value}, Mime: {$post_mime_type}, Title: {$title})</option>";
									}
								}
							}
						?>
					</select>
					<div>
						<a href="#" onclick='nxs_js_setpopupdatefromcontrols(); nxs_js_popup_setsessiondata("nxs_mediapicker_invoker", nxs_js_popup_getcurrentsheet()); nxs_js_popup_setsessiondata("nxs_mediapicker_targetvariable", "<?php echo $id ?>"); nxs_js_popup_navigateto("mediapicker"); return false;' class="nxsbutton1 nxs-float-right">Select media item</a>
					</div>
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
		      		<input id='linkhref' type='text' value='<?php echo $linkhref; ?>' />
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
		            	<select id='linktarget'>
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
		              <h4><?php nxs_l18n_e("Link relation", "nxs_td"); ?></h4>
		             </div>
		            <div class="box-content">
		            	<select id='linkrel'>
		            		<option <?php if ($linkrel=='') echo "selected='selected'"; ?> value=''><?php nxs_l18n_e("Follow", "nxs_td"); ?></option>
		            		<option <?php if ($linkrel=='nofollow') echo "selected='selected'"; ?> value='nofollow'><?php nxs_l18n_e("No follow", "nxs_td"); ?></option>
		            	</select>
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->				      
		      
		      <div class="content2">
		      	<div class="box">
		      		<div class="box-title">
		      			<h4><?php nxs_l18n_e("Text", "nxs_td"); ?></h4>
		      		</div>
		      		<div class="box-content">
		      		<input id='linktext' type='text' value="<?php echo htmlentities($linktext);?>" />
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
			nxs_js_popup_storestatecontroldata_dropdown('linkrel', 'linkrel');
			nxs_js_popup_storestatecontroldata_textbox('linktext', 'linktext');
			nxs_js_popup_storestatecontroldata_textbox('linkhref', 'linkhref');
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
				var url = nxs_js_popup_getsessiondata('linkhref');
				nxs_js_pickurlandnavigatebacktooriginalpopup(url);
			}
			else if (linktype == 'internal')
			{
				var postid = nxs_js_popup_getsessiondata('linkpostid');
				nxs_js_log('processing internal linktype postid ' + postid);
				nxs_js_geturl("postid", postid, "notused", 
				function(response) 
				{
					nxs_js_log("magnificent result is:");
					nxs_js_log(response);
					
					var url = response.url;
					nxs_js_log("in other words, the URL is:" + url);
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
			nxs_js_log("nxs_js_pickurlandnavigatebacktooriginalpopup(url):" + url);
			
			nxs_js_popup_setsessioncontext('tinymceinittrigger', "setanchor");
			nxs_js_popup_setsessioncontext('linktext', nxs_js_popup_getsessiondata('linktext'));
			nxs_js_popup_setsessioncontext('linktarget', nxs_js_popup_getsessiondata('linktarget'));
			nxs_js_popup_setsessioncontext('linkrel', nxs_js_popup_getsessiondata('linkrel'));
			nxs_js_popup_setsessioncontext('linktitle', nxs_js_popup_getsessiondata('linktitle'));
			nxs_js_popup_setsessioncontext('linkhref', url);
			
			
			// store the target for our tinymce plugin to continue
			nxs_js_popup_sessiondata_make_dirty();
			
			// redirect back to the sheet that triggered us
			var nxs_tinymce_invoker_sheet = nxs_js_popup_getsessioncontext("nxs_tinymce_invoker_sheet");
			nxs_js_popup_navigateto(nxs_tinymce_invoker_sheet);
		}
		
	</script>

	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	$result["html"] = $html;
	
	return $result;
}
?>