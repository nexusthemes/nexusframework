<?php

function nxs_mediapicker_title_like_posts_where($where, $wp_query) 
{
	global $nxs_glb_mediapicker_filter_title;
  global $wpdb;
  $result = $where . ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape($nxs_glb_mediapicker_filter_title)) . '%\'';
  return $result;
}

function nxs_mediapicker_date_like_posts_where($where, $wp_query) 
{
	global $nxs_glb_mediapicker_filter_date;
  global $wpdb;
  
  if ($nxs_glb_mediapicker_filter_date == "24h")
  {
 		$result = $where . " AND DATEDIFF(NOW(), post_date) <= 1";
  }
  /*
  if ($nxs_glb_mediapicker_filter_date == "yesterday")
  {
  	$result = $where . " AND DATEDIFF(NOW(), post_date) = 1";
  }
  */
  else if ($nxs_glb_mediapicker_filter_date == "7days")
  {
  	$result = $where . " AND DATEDIFF(NOW(), post_date) <= 7";
  }
  else
  {
  	nxs_webmethod_return_nack("unsupported filter (date);" . $nxs_glb_mediapicker_filter_date);
  }
  
  return $result;
}

function nxs_popup_genericpopup_mediapicker_getpopup($args)
{
	extract($args);
	
	// clientpopupsessiondata bevat key values van de client side
	// deze overschrijft met opzet (tijdelijk) mogelijk waarden die via $args
	// zijn meegegeven; hierdoor kan namelijk een 'gevoel' worden gecreeerd
	// van een 'state' die client side leeft, die helpt om meerdere (popup) 
	// pagina's state te laten delen. De inhoud van clientpopupsessiondata is een
	// array die wordt gevoed door de clientside variabele "popupsessiondata",
	// die gedefinieerd is in de file 'frontendediting.php'
	extract($clientpopupsessioncontext);
	extract($clientpopupsessiondata);	
	extract($clientshortscopedata);
	
	$fileuploadurl = admin_url('admin-ajax.php');
		
	$result = array();
	$result["result"] = "OK";
	
	if ($medialist_pagenr == "") {
		$medialist_pagenr = 1;
	}
	
	if (!isset($mediapicker_paging_itemsperpage) || $mediapicker_paging_itemsperpage == "" || $mediapicker_paging_itemsperpage == 0)
	{
		// default
		$mediapicker_paging_itemsperpage = 5;
		// allow overriden defaults
		$mediapicker_paging_itemsperpage = apply_filters("nxs_filter_default_mediapicker_paging_itemsperpage", $mediapicker_paging_itemsperpage);
	}
	
	$firstrownrtoshow = $mediapicker_paging_itemsperpage*($medialist_pagenr - 1);
	
	$postargs = array 
	(
		'numberposts' => -1,	//$mediapicker_paging_itemsperpage,
		'offset' => 0,
		'post_type' => 'attachment',
		// 'post_mime_type' => 'image',	// only fetch images
		'post_parent' => null, // no parent
		'suppress_filters' => false,
	);

	// apply title filter
	if (isset($mediapicker_filter_title) && $mediapicker_filter_title != "")
	{
		global $nxs_glb_mediapicker_filter_title;
		$nxs_glb_mediapicker_filter_title = $mediapicker_filter_title;
		add_filter( 'posts_where', 'nxs_mediapicker_title_like_posts_where', 10, 2 );
	}
	
	// apply date filter
	if (isset($mediapicker_filter_date) && $mediapicker_filter_date != "")
	{
		global $nxs_glb_mediapicker_filter_date;
		$nxs_glb_mediapicker_filter_date = $mediapicker_filter_date;
		add_filter( 'posts_where', 'nxs_mediapicker_date_like_posts_where', 10, 2 );
	}	
	$images = get_posts($postargs);
	
	//
	
	$totalrows = count($images);
	$totalpages = (int) ceil($totalrows / $mediapicker_paging_itemsperpage);
	
	if ($medialist_pagenr < 1 || $medialist_pagenr > $totalpages) {
		// out of bounds
		$medialist_pagenr = 1;
	}
	
	nxs_ob_start();
	
	?>
	
	<div class="nxs-admin-wrap">
		
		<div class="block">
	
			<?php nxs_render_popup_header(nxs_l18n__("Change media", "nxs_td")); ?>
	
			<div class="nxs-popup-content-canvas-cropper">
				
				<div class="nxs-popup-content-canvas">
	
					<div class="content2">
					
						<div class="nxs-mediapicker-filters">

							<form id='nxsuploadform' action="<?php echo $fileuploadurl;?>" method="post" enctype="multipart/form-data">
								<input type="file" name="file" id="file" class="nxs-float-left" onchange="storefile();" />
							</form>										
							
							<!-- filter -->
							<div class="nxs-filterconfig nxs-float-right">
								<span><?php echo nxs_l18n__("Title contains", "nxs_td"); ?></span>
								<input id="mediapicker_filter_title" value="<?php echo nxs_render_html_escape_doublequote($mediapicker_filter_title); ?>">
								<a class='nxsbutton1' href='#' onclick='nxs_js_popup_mediapicker_updatefilter(); return false;'>Filter</a>
							</div>	
							<script type='text/javascript'>
								jQ_nxs("#mediapicker_filter_title").keyup
								(
									function (e) 
									{
    								if (e.keyCode == 13) 
    								{
        							// Do something;
        							nxs_js_popup_mediapicker_updatefilter();
        							return false;
        						}
        					}
        				);
							</script>
							
							<div class="nxs-clear padding"></div>
						</div>
					
						
						
						<script type="text/javascript">
							function setupfiletransfer() 
							{
								//alert("setting up...");
								var filename = jQ_nxs('#file').val().split(/\\|\//).pop();
								var options = {
									data: { action: "nxs_ajax_webmethods", webmethod: "savefileupload", uploadtitel: filename },
									dataType: 'json',
									iframe: true,
									success: processResponse,
									error: function(response)
									{
										nxs_js_popup_notifyservererror_v2(response);
									}
								};
								jQ_nxs('#nxsuploadform').ajaxForm(options);
							}
							
							function storefile() {
								// setup form to support ajax submission (file transfer using html5 features)
								setupfiletransfer();
								if (!verifyFileSelected()) {
									return;
								}
								// submit form
								jQ_nxs("#nxsuploadform").submit(); 
							}
							
							function verifyFileSelected() {
								var f = document.getElementById("file");
								if (f.value == "") {
									nxs_js_alert("<?php nxs_l18n_e("Please select a file first", "nxs_td"); ?>");
									return false;
								} else {
									return true;
								}
							}
							
							function processResponse(data, statusText, xhr, $form) {
								if (data.result == "OK") {
									// file upload was succesful
									nxs_js_popup_setsessiondata("<?php echo $nxs_mediapicker_targetvariable; ?>", data.imageid);
									nxs_js_popup_sessiondata_make_dirty();                
									// refresh to main screen
									nxs_js_popup_navigateto("<?php echo $nxs_mediapicker_invoker; ?>");
								} else if (data.result == "ALTFLOW") {
									// upload failed
									nxs_js_log(data);
									nxs_js_alert("<?php nxs_l18n_e("File upload failed", "nxs_td");?>");
									if (data.altflowid == "UPLOADERROR1")
									{
										nxs_js_alert("<?php nxs_l18n_e("The uploaded file exceeds the upload_max_filesize directive in php.ini", "nxs_td");?>");
									}
									else if (data.altflowid == "SIZEERR")
									{
										nxs_js_alert(data.message);
									}
								} else {
									nxs_js_log(data);
									nxs_js_log(statusText);
									nxs_js_log(xhr);
									nxs_js_alert("<?php nxs_l18n_e("An error occured. The file was not uploaded", "nxs_td"); ?>"); 	
								}
							}
							
							function nxs_js_setpagenr(pagenr) {
								nxs_js_popup_setsessiondata("medialist_pagenr", pagenr);
								nxs_js_popup_refresh();
							}
							
							function nxs_js_overrule_topmargin() {
								return 40;
							}
							
							jQ_nxs("#pagechanger").unbind("keyup.defaultenter");
							jQ_nxs("#pagechanger").bind("keyup.defaultenter", function(e) {
								if (e.keyCode == 13) {
									var nieuwepagenr = parseInt(jQ_nxs("#pagechanger").val());
									if (isNaN(nieuwepagenr)) {
										//ignore
									} else {
										nxs_js_setpagenr(nieuwepagenr);
									}
								}
							});
							
							function nxs_js_execute_after_popup_shows()
							{
								<?php
								if (!isset($nxs_mediapicker_targetvariable))
								{
								?>
									nxs_js_alert_sticky('Warning, looks like the widget developer forgot to set the nxs_mediapicker_targetvariable!');	
									nxs_js_alert_sticky('Found:' + nxs_js_popup_getsessiondata("nxs_mediapicker_invoker") + ' ....');
								<?php
								}
								?>
								
								<?php
								if (!isset($nxs_mediapicker_invoker))
								{
								?>
									nxs_js_alert_sticky('Warning, looks like the widget developer forgot to set the nxs_mediapicker_invoker!');	
									nxs_js_alert_sticky('Found:' + nxs_js_popup_getsessiondata("nxs_mediapicker_invoker") + ' ....');
								<?php
								}
								?>
							}
						</script>
						<?php if ($totalpages > 1) { ?>
							<div class="nxs-pagination nxs-float-right">
								<span><?php echo nxs_l18n__("Page", "nxs_td"); ?></span>
								<span class="">
									<?php if ($medialist_pagenr > 1) { ?>
										<a class="current" href="#" onclick="nxs_js_setpagenr('1'); return false;">&lt;&lt;</a>
										<a class="current" href="#" onclick="nxs_js_setpagenr('<?php echo $medialist_pagenr - 1; ?>'); return false;">&lt;</a>
									<?php } ?>
									<span class="">
										<input id="pagechanger" type="text" value="<?php echo $medialist_pagenr;?>" size="3" class="small2"> <?php echo nxs_l18n__("of", "nxs_td"); ?> <?php echo $totalpages; ?>
									</span>
									<?php if ($medialist_pagenr < $totalpages) { ?>
										<a class="current" href="#" onclick="nxs_js_setpagenr('<?php echo $medialist_pagenr + 1; ?>'); return false;">&gt;</a>
										<a class="current" href="#" onclick="nxs_js_setpagenr('<?php echo $totalpages; ?>'); return false;">&gt;&gt;</a>
									<?php } ?>
								</span>
							</div>
						<?php } ?>
						
						<!-- pagesize -->
						<div class="nxs-paginationconfig nxs-float-right">
							<span><?php echo nxs_l18n__("Pagesize", "nxs_td"); ?></span>
							<select id="mediapicker_paging_itemsperpage" onchange="nxs_js_popup_mediapicker_changepagesize(this);">
								<?php
								$pagingspossible = array(5,10,25,50,100,250,500,1000);
								foreach ($pagingspossible as $currentitemsperpage)
								{
									$currentitemsperpageisselected = "";
									if ($currentitemsperpage == $mediapicker_paging_itemsperpage)
									{
										$currentitemsperpageisselected = "selected=selected";
									}
									?>
									<option value='<?php echo $currentitemsperpage; ?>' <?php echo $currentitemsperpageisselected; ?>><?php echo $currentitemsperpage; ?></option>
									<?php
								}
								?>
							</select>
						</div>
						
						<!-- date created -->
						<div class="nxs-paginationconfig nxs-float-right">
							<span><?php echo nxs_l18n__("Created", "nxs_td"); ?></span>
							<select id="mediapicker_filter_date" onchange="nxs_js_popup_mediapicker_changedatefilter(this);">
								<?php
								$datespossible = array(''=>nxs_l18n__("Any", "nxs_td"),'24h'=>nxs_l18n__("Last 24h", "nxs_td"),'7days'=>nxs_l18n__("Last 7 days", "nxs_td"));
								foreach ($datespossible as $currentdatepossiblekey => $currentdatepossibletext)
								{
									$isselected = "";
									if ($currentdatepossiblekey == $mediapicker_filter_date)
									{
										$isselected = "selected=selected";
									}
									?>
									<option value='<?php echo $currentdatepossiblekey; ?>' <?php echo $isselected; ?>><?php echo $currentdatepossibletext; ?></option>
									<?php
								}
								?>
							</select>
						</div>						
						
						<div class="nxs-clear padding"></div>
						
						<?php
						nxs_ext_inject_popup_optiontype("image");
						if (nxs_popup_optiontype_image_shouldrenderphotopackpromo())
						{
					  	?>
							<div class="content2">
								<div class="box">
									<div class="xbox-content">
										Tip: To avoid the hassle with finding nice photos,
										cutting them in the right proportions/aspect ratio, optimal filesize and 
										arranging a valid license to avoid copyright 
										infringements, consider purchasing the photopack.
										<br />
										<a class="nxsbutton1" href='http://nexusthemes.com/cart/?add-to-cart=6399&trigger=iopphotopack2&themeid=<?php echo nxs_getthemeid(); ?>' target='_blank'>Purchase photopack</a>
									</div>
									<div class="nxs-clear"></div>				
								</div>
							</div>
					  	<?php
					  }						
						?>
						
						<table>
							
							<thead>
								<tr>
									<th class="file">
										<span><?php echo nxs_l18n__("Image", "nxs_td"); ?></span>
										<span class="sorting-indicator"></span>
									</th>
									<th></th>
								</tr>
							</thead>
							
							<tfoot>
								<tr>
									<th class="file">
										<span><?php echo nxs_l18n__("Image", "nxs_td"); ?></span>
										<span class="sorting-indicator"></span>
									</th>
									<th></th>
								</tr>
							</tfoot>
						
							<tbody>
							
							<?php 
							foreach($images as $currentimage) {
								
								$rownr = $rownr + 1;
								
								if ($rownr < $firstrownrtoshow) {
									// continue looping
									continue;
								}
								
								$visiblenr = $visiblenr + 1;
								
								if ($visiblenr > $mediapicker_paging_itemsperpage) {
									// break the loop
									break;
								}		
					
								// wp_get_attachment_url($attachmentID);
								$lookup = wp_get_attachment_image_src($currentimage->ID, 'thumbnail', true);
								$url = $lookup[0];
								$url = nxs_img_getimageurlthemeversion($url);
								
								$fullimagemetadata = wp_get_attachment_image_src($currentimage->ID, 'full', true);
								$fullimagewidth 	= $fullimagemetadata[1] . "px";
								$fullimageheight 	= $fullimagemetadata[2] . "px";	
								
								$rowclass = "";
								
								if ($rownr % 2 == 0) {
									$rowclass = "class='alt'";
								}
							?>
					
								<tr <?php echo $rowclass; ?>>
									
									<td class="file">
										<a href='#' onclick='nxs_js_selectattachment("<?php echo $currentimage->ID; ?>"); return false;'>
											<img src='<?php echo $url;?>' />
										</a>
									</td>
									
									<td>
										<p>Titel: <?php echo $currentimage->post_title; ?></p>
										<p>Type: <?php echo $currentimage->post_mime_type; ?></p>
										<p>Dimensions (width x height): <?php echo "{$fullimagewidth} x {$fullimageheight}"; ?></p>
									</td>
								
								</tr>
						
							<?php } ?>
						
							</tbody>
	
						</table>
					
					</div> <!-- END content -->
	
				</div> <!-- END nxs-popup-content-canvas -->
			
			</div> <!-- END nxs-popup-content-canvas-cropper -->
	
			<div class="content2">
	
				<div class="box">
					<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel", "nxs_td"); ?></a>
					<?php
					if ($allow_featuredimage == true)
					{
						?>
						<a href='#' class="nxsbutton1 nxs-float-right" onclick='nxs_js_usefeaturedimg(); return false;'><?php nxs_l18n_e("Use 'featured image'", "nxs_td"); ?></a>
						<?php
					}
					?>
				</div>
				<div class="nxs-clear margin"></div>
	
			</div> <!-- END content -->
	
		</div> <!-- END block -->
	
	</div> <!-- END nxs-admin-wrap -->
	
	<script type='text/javascript'>
	
		function nxs_js_popup_mediapicker_updatefilter()
		{
			var value = jQ_nxs('#mediapicker_filter_title').val();
			nxs_js_popup_setsessioncontext('mediapicker_filter_title', value);
			nxs_js_popup_refresh_v2(true);
		}
		
		function nxs_js_usefeaturedimg()
		{
			nxs_js_selectattachment("featuredimg");
		}
		
		function nxs_js_popup_mediapicker_changepagesize(element)
		{
			nxs_js_popup_setsessioncontext('mediapicker_paging_itemsperpage', element.value); 
			nxs_js_popup_refresh_v2(true);
		}
		
		function nxs_js_popup_mediapicker_changedatefilter(element)
		{
			nxs_js_popup_setsessioncontext('mediapicker_filter_date', element.value); 
			nxs_js_popup_refresh_v2(true);
		}
	
		function nxs_js_popup_get_initialbuttonstate() { 
			return 'none'; 
		}
	
		function nxs_js_selectattachment(attachmentid) {
			nxs_js_popup_setsessiondata("<?php echo $nxs_mediapicker_targetvariable; ?>", attachmentid);
			nxs_js_popup_sessiondata_make_dirty();
			// toon eerste scherm in de popup
			nxs_js_popup_navigateto("<?php echo $nxs_mediapicker_invoker; ?>");
		}
	
	</script>
	<?php
		
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
  $html = nxs_ob_get_contents();
  nxs_ob_end_clean();
    
  // Setting the contents of the variable to the appropriate array position
  // The framework uses this array with its accompanying values to render the page
  $result["html"] = $html;
  return $result;
}
?>