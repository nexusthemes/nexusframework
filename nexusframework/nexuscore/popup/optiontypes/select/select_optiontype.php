<?php
function nxs_popup_optiontype_select_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter
	
	if (isset($popuprefreshonchange) && $popuprefreshonchange == "true")
	{
		$popuprefreshonchangeaction = "nxs_js_setpopupdatefromcontrols();nxs_js_popup_refresh_v2(true);";
	}
	else
	{
		$popuprefreshonchangeaction = "";
	}
	
	?>
  <div class="content2">
    <?php echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip); ?>
  	<div class="box-content">
      <select id='<?php echo $id; ?>' class="chosen-select" onchange="nxs_js_popup_setsessiondata('<?php echo $id; ?>', jQuery(this).val()); <?php echo $popuprefreshonchangeaction; ?> ">
      	<?php 
    		// dropdown is specified as keys and values
    		$isfound = false;
      	foreach ($dropdown as $currentkey => $currentvalue) 
      	{
      		if ($currentkey == "@@@nxsempty@@@")
      		{
      			$currentkey = "";
      		}
      		
      		$selected = "";
      		if ($currentkey == $value) 
      		{
      			$isfound = true;
      			$selected ="selected='selected'";
      		}
      		?>
          <option <?php echo $selected; ?> value='<?php echo $currentkey; ?>'><?php echo $currentvalue; ?></option>
          <?php 
        } 
        
        if (!$isfound)
        {
        	$selected ="selected='selected'";
        	$presentation = "";
        	if ($value != "")
        	{
        		$presentation = "Value not (or no longer) supported: '{$value}'";
        	}
        	?>
        	<option <?php echo $selected; ?> value='<?php echo $value; ?>'><?php echo $presentation; ?></option>
        	<?php
        }
        ?>
      </select>
    </div>
    <div class="nxs-clear"></div>
  </div> <!-- END content2 -->
	<?php
	//
}

function nxs_popup_optiontype_select_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_dropdown("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_select_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_select_getpersistbehaviour()
{
	return "writeid";
}

?>