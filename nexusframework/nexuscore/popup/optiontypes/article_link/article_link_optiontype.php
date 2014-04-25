<?php
function nxs_popup_optiontype_article_link_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter
	
	$publishedargs = array();
	$publishedargs["post_status"] 	= array("publish", "private");
	$publishedargs["post_type"] 	= array("post", "page", "product");
	$publishedargs["orderby"] 		= "post_date";//$order_by;
	$publishedargs["order"] 		= "DESC"; //$order;
	$publishedargs["numberposts"] 	= -1;	// allemaal!
	$items = get_posts($publishedargs);
							              
							echo '
							<div class="content2">
								<div class="box">
									' . nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip) . '
									<div class="box-content">
										<select id="'. $id .'" class="chosen-select" name="'. $id .'" onchange="nxs_js_popup_sessiondata_make_dirty();">
										';
										 
										 if ($id == "") {
											$selected = "selected='selected'";
										} else {
											$selected = "";
										}
										echo "<option value='' $selected >" . nxs_l18n__("No article selected[nxs:heading]", "nxs_td") . "</option>";
										
										foreach ($items as $currentpost) {
											$currentpostid = $currentpost->ID;
											$posttitle = nxs_cutstring($currentpost->post_title, 50);
										
											if ($posttitle == "") {
												$posttitle = "(leeg, ID:" . $currentpostid . ")";
											}                    
										
											$selected = "";
											
											if ($currentpostid == $value) {
												$selected = "selected='selected'";
											} else {
												$selected = "";
											}
											echo "<option value='$currentpostid' $selected	>$posttitle</option>";
										}
											
										 echo '
										</select>
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