<?php
function nxs_popup_optiontype_textarea_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	$cols = "50";	// default
	$rows = "15";	// default
	
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter
	
	echo '
  <div class="content2">
    <div class="box">';
			echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip);
			echo '
      <div class="box-content">
          <textarea style="display: block; height: inherit;" id="'. $id . '" name="content" cols="' . $cols . '" rows="' . $rows . '" placeholder="' . nxs_render_html_escape_doublequote($placeholder) . '" >' . nxs_render_html_escape_gtlt($value) . '</textarea>
      </div>
  </div>
  <div class="nxs-clear"></div>
</div>
';
//
}

function nxs_popup_optiontype_textarea_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_textbox("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_textarea_getitemstoextendbeforepersistoccurs($optionvalues, $args)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_textarea_getpersistbehaviour()
{
	return "writeid";
}

?>