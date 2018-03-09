<?php
function nxs_popup_optiontype_fontzen_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	$sampletext = nxs_l18n__("Sample fontzen", "nxs_td");
	
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
	          	<li onclick='nxs_js_startfontzenpicker_<?php echo $id;?>(); return false;' style='cursor: pointer;' class='nxs-float-left'>
	          		<?php 
	          		if (isset($value) && $value != "") 
	          		{ 
	          			$fontfamilies = nxs_font_getfontfamiliesforfontidentifier($value);
	          			$first = $fontfamilies[0];
	          			?>
									<div class="nxs-fontzen-<?php echo $value; ?> border-radius-small font-sample">
										<p><?php echo $sampletext; echo " (" . $first . ")"; ?></p>
									</div>
								<?php 
								} 
								else 
								{
									// when no font is selected
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
          	<a href="#" class="nxsbutton1 nxs-float-right" onclick="nxs_js_startfontzenpicker_' . $id . '(); return false;">' . nxs_l18n__("Change", "nxs_td") .'</a>
          </div>
        </div>
        <div class="nxs-clear"></div>
      </div>
  ';
  ?>
  <script>
		function nxs_js_startfontzenpicker_<?php echo $id;?>()
		{
			nxs_js_setpopupdatefromcontrols(); 
			nxs_js_popup_setsessiondata("nxs_fontzenpicker_invoker", nxs_js_popup_getcurrentsheet()); 
			nxs_js_popup_setsessiondata("nxs_fontzenpicker_sampletext", "<?php echo $sampletext;?>"); 
			nxs_js_popup_setsessiondata("nxs_fontzenpicker_targetvariable", "<?php echo $id;?>"); 
			nxs_js_popup_setsessiondata("nxs_fontzenpicker_currentvalue", "<?php echo $value;?>");
			
			nxs_js_popup_navigateto("fontzenpicker");
		}
	</script>
  <?php
	//
}

function nxs_popup_optiontype_fontzen_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_hiddenfield("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_fontzen_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_fontzen_getpersistbehaviour()
{
	return "writeid";
}

?>