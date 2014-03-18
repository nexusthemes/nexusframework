<?php
function nxs_popup_optiontype_staticgenericlist_link_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter
	
	$containerpostid = $args["clientpopupsessioncontext"]["containerpostid"];
	$currentcontainerposturl = nxs_geturl_for_postid($containerpostid);
	$urlencbase64referringurl = urlencode(base64_encode($currentcontainerposturl));
	
	$refurl = get_home_url() . "/?nxs_genericlist=" . urlencode(nxs_getslug_for_postid($value)) . "&urlencbase64referringurl=" . $urlencbase64referringurl;
	?>
	<div class="content2">
    <div class="box">
	    <?php echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip); ?>
      <div class="box-content">
        <input type="hidden" id="<?php echo $id; ?>" name="<?php echo $id; ?>" value="<?php echo $value; ?>" />
        <a href='#' onclick="nxs_js_savegenericpopup_internal(function(response){nxs_js_redirect('<?php echo $refurl;?>');}); return false;" class="nxsbutton1 nxs-float-right"><?php nxs_l18n_e("Edit", "nxs_td"); ?></a>
      </div>
    </div>
    <div class="nxs-clear"></div>
	</div>
	<?php
	//
}

function nxs_popup_optiontype_staticgenericlist_link_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_hiddenfield("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_staticgenericlist_link_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
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
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_staticgenericlist_link_getpersistbehaviour()
{
	return "writeid";
}

?>