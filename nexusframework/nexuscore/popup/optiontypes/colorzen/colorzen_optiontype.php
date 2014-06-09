<?php
function nxs_popup_optiontype_colorzen_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	$sampletext = nxs_l18n__("Sample colorzen", "nxs_td");
	$colorset_flat_enabled = "true";
	$colorset_lightgradient_enabled = "true";
	$colorset_mediumgradient_enabled = "true";
	
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
	    	?>
          <div class="box-content">
          	<ul>
	          	<li onclick='nxs_js_startcolorzenpicker_<?php echo $id;?>(); return false;' style='cursor: pointer;' class='nxs-float-left'>
	          		<?php if (isset($value) && $value != "") { ?>
									<div class="nxs-colorzen-<?php echo $value; ?> border-radius-small color-sample">
										<p><?php echo $sampletext; ?></p>
									</div>
								<?php 
								} 
								else 
								{
									// when no color is selected
									?>
									<p><?php echo "Not selected"; ?></p>
									<?php 
								} 
								?>
							</li>
          	</ul>
          	<?php
          	echo '
          	<input type="hidden" name="' . $id . '" id="' . $id . '" value="' . $value . '"></input>
          	<a href="#" class="nxsbutton1 nxs-float-right" onclick="nxs_js_startcolorzenpicker_' . $id . '(); return false;">' . nxs_l18n__("Change", "nxs_td") .'</a>
          </div>
        </div>
        <div class="nxs-clear"></div>
      </div>
  ';
  ?>
  <script type="text/javascript">
		function nxs_js_startcolorzenpicker_<?php echo $id;?>()
		{
			nxs_js_setpopupdatefromcontrols(); 
			nxs_js_popup_setsessiondata("nxs_colorzenpicker_invoker", nxs_js_popup_getcurrentsheet()); 
			nxs_js_popup_setsessiondata("nxs_colorzenpicker_sampletext", "<?php echo $sampletext;?>"); 
			nxs_js_popup_setsessiondata("nxs_colorzenpicker_targetvariable", "<?php echo $id;?>"); 
			nxs_js_popup_setsessiondata("nxs_colorzenpicker_currentvalue", "<?php echo $value;?>");
			
			nxs_js_popup_setsessiondata("nxs_colorzenpicker_colorset_flat_enabled", "<?php echo $colorset_flat_enabled;?>");
			nxs_js_popup_setsessiondata("nxs_colorzenpicker_colorset_lightgradient_enabled", "<?php echo $colorset_lightgradient_enabled;?>");
			nxs_js_popup_setsessiondata("nxs_colorzenpicker_colorset_mediumgradient_enabled", "<?php echo $colorset_mediumgradient_enabled;?>");
			
			nxs_js_popup_navigateto("colorzenpicker");
		}
	</script>
  <?php
	//
}

function nxs_popup_optiontype_colorzen_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_hiddenfield("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_colorzen_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_colorzen_getpersistbehaviour()
{
	return "writeid";
}

?>