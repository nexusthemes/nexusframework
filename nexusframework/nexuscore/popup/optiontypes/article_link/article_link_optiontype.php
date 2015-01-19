<?php
function nxs_popup_optiontype_article_link_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter
	
	$publishedargs = array();
	$publishedargs["post_status"] 	= array("publish", "private");
	
	$posttypes = array("post", "page");
	$posttypes = apply_filters("nxs_links_getposttypes", $posttypes);
	$publishedargs["post_type"] = $posttypes;
	
	$publishedargs["orderby"] 		= "post_date";//$order_by;
	$publishedargs["order"] 		= "DESC"; //$order;
	$publishedargs["numberposts"] 	= -1;	// allemaal!
	$items = get_posts($publishedargs);
	$post = get_post($value);
							 
							$isfound = false;
							              
							echo '
							<div class="content2">
								<div class="box">
									' . nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip) . '
									<div class="box-content">
										<!-- ' . $id . ' -->
										<select id="'. $id .'" class="chosen-select" name="'. $id .'" onchange="nxs_js_popup_sessiondata_make_dirty();">
										';
										 
										if ($value == "" || $value == "0" || $post == null) 
										{
											$selected = "selected='selected'";
											$isfound = true;
										} 
										else 
										{
											$selected = "";
										}
										echo "<option value='' $selected >" . nxs_l18n__("No article selected[nxs:heading]", "nxs_td") . "</option>";
										
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
										
										//
										
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
											
										 echo '
										</select>
										
										<!-- allow user to pick a media item -->';
										
										echo '
										
										<div>';
										?>
											<a href="#" onclick='nxs_js_setpopupdatefromcontrols(); nxs_js_popup_setsessiondata("nxs_mediapicker_invoker", nxs_js_popup_getcurrentsheet()); nxs_js_popup_setsessiondata("nxs_mediapicker_targetvariable", "<?php echo $id;?>"); nxs_js_popup_navigateto("mediapicker"); return false;' class="nxsbutton1 nxs-float-right">Select media item</a>
										<?php
										echo '
										</div>
										
									</div>
								</div>
								<div class="nxs-clear"></div>
							</div> <!--END content-->
               ';
	//
}

function nxs_popup_optiontype_article_link_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_dropdown("' . $id . '", "' . $id . '");';
}

function nxs_popup_optiontype_article_link_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	
	$id = $optionvalues["id"];
	$value = $metadata[$id];
	
	$globalid = nxs_get_globalid($value, true);
	$result[$id . "_globalid"] = $globalid;
	
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store a single "id" field
function nxs_popup_optiontype_article_link_getpersistbehaviour()
{
	return "writeid";
}

?>