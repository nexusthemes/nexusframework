<?php
function nxs_popup_optiontype_icon_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
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
				?>
		<div class="box-content">
      <ul>
				<li onclick='nxs_js_starticonpicker_<?php echo $id;?>(); return false;' style='cursor: pointer' class='nxs-float-left'>
	        <?php 
	        if (isset($value) && $value != "") 
	        { 
	        	?>
	      		<span class="<?php echo $value; ?> nxs-icon">
						<?php 
					} 
					else 
					{ 
						// nothing (yet)
						/*
						?>
						<a href="#" onclick="nxs_js_starticonpicker_<?php echo $id; ?>(); return false;"><?php echo nxs_l18n__("None", "nxs_td"); ?></a>
						<?php
						*/
					} 
					?>
				</li>
      </ul>
          	<?php
          	echo '
          	<input type="hidden" name="' . $id . '" id="' . $id . '" value="' . $value . '"></input>
          	<a href="#" class="nxsbutton1 nxs-float-right" onclick="nxs_js_starticonpicker_' . $id . '(); return false;">' . nxs_l18n__("Change", "nxs_td") .'</a>
          </div>
        </div>
        <div class="nxs-clear"></div>
      </div>
  ';
  ?>
  <script type="text/javascript">
		function nxs_js_starticonpicker_<?php echo $id;?>()
		{
			nxs_js_setpopupdatefromcontrols(); 
			nxs_js_popup_setsessiondata("nxs_iconpicker_invoker", nxs_js_popup_getcurrentsheet()); 
			nxs_js_popup_setsessiondata("nxs_iconpicker_sampletext", "<?php echo $sampletext;?>"); 
			nxs_js_popup_setsessiondata("nxs_iconpicker_targetvariable", "<?php echo $id;?>"); 
			nxs_js_popup_setsessiondata("nxs_iconpicker_currentvalue", "<?php echo $value;?>");
			
			nxs_js_popup_setsessiondata("nxs_iconpicker_colorset_flat_enabled", "<?php echo $colorset_flat_enabled;?>");
			nxs_js_popup_setsessiondata("nxs_iconpicker_colorset_lightgradient_enabled", "<?php echo $colorset_lightgradient_enabled;?>");
			nxs_js_popup_setsessiondata("nxs_iconpicker_colorset_mediumgradient_enabled", "<?php echo $colorset_mediumgradient_enabled;?>");
			
			nxs_js_popup_navigateto("iconpicker");
		}
	</script>
  <?php
	//
}

function nxs_popup_optiontype_icon_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_hiddenfield("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_icon_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_icon_getpersistbehaviour()
{
	return "writeid";
}

?>
