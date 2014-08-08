<?php
function nxs_popup_optiontype_effects_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	
	if (isset($$id))
	{
		$value = $$id;	// $id is the parametername, $$id is the value of that parameter
	}
	else
	{
		$value = "";
	}
	
	echo '
	<div class="content2">
	    <div class="box">';
	    	echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip);
	    	//$value = '{ \"name\": \"Johnson\" }';
	    	?>
          <div class="box-content">
          	<ul>
	          	<li onclick='nxs_js_starteffectspicker_<?php echo $id;?>(); return false;' style='cursor: pointer;' class='nxs-float-left'>
	          		<?php echo $value; ?>
							</li>
          	</ul>
          	<?php
          	echo '
          	<input type="hidden" name="' . $id . '" id="' . $id . '" value=\''.$value.'\'></input>
          	<a href="#" class="nxsbutton1 nxs-float-right" onclick="nxs_js_starteffectspicker_' . $id . '(); return false;">' . nxs_l18n__("Change", "nxs_td") .'</a>
          </div>
        </div>
        <div class="nxs-clear"></div>
      </div>
  ';
  ?>
  <script type="text/javascript">
		function nxs_js_starteffectspicker_<?php echo $id;?>()
		{
			nxs_js_setpopupdatefromcontrols(); 
			nxs_js_popup_setsessiondata("nxs_effectspicker_invoker", nxs_js_popup_getcurrentsheet()); 
			nxs_js_popup_setsessiondata("nxs_effectspicker_sampletext", "<?php echo $sampletext;?>"); 
			nxs_js_popup_setsessiondata("nxs_effectspicker_targetvariable", "<?php echo $id;?>"); 
			nxs_js_popup_setsessiondata("nxs_effectspicker_currentvalue", "<?php echo $value;?>");
			
			nxs_js_popup_navigateto("effectspicker");
		}
	</script>
  <?php
	//
}

function nxs_popup_optiontype_effects_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo "nxs_js_popup_storestatecontroldata_hiddenfield('{$id}', '{$id}');";	
}

function nxs_popup_optiontype_effects_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_effects_getpersistbehaviour()
{
	return "writeid";
}

?>