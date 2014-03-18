<?php
function nxs_popup_optiontype_colorvariation_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	$sampletext = nxs_l18n__("Sample colorvariation", "nxs_td");
	
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
	          	<li onclick='nxs_js_startcolorvariationpicker_<?php echo $id;?>(); return false;' class='nxs-float-left'>
	          		<?php 
	          		$cssclass = "";
	          		if ($scope == "link")
	          		{
	          			$cssclass = $value;
	          			?>
	          			<?php if (isset($value) && $value != "") { ?>
		          			<div class="nxs-linkcolorvar-<?php echo $value; ?> border-radius-small color-sample">
											<p class="nxs-applylinkvarcolor"><a href='#'><?php echo nxs_l18n__("Sample", "nxs_td"); ?></a></p>
										</div>
									<?php } else { ?>
									
									<?php } ?>
	          			<?php
	          		}
	          		else if ($scope == "background")
	          		{
	          			$cssclass = $value;
	          			?>
	          			<?php if (isset($value) && $value != "") { ?>
		          			<div class="nxs-linkcolorvar-<?php echo $value; ?> border-radius-small color-sample">
											<p class="nxs-applylinkvarcolor">
												<a href='#'>
													<span style='font-size: 50px;'>&#9608;</span> 
												</a>
											</p>
										</div>
									<?php } else { ?>
									
									<?php } ?>
	          			<?php
	          		}
	          		else
	          		{
	          			?>
	          			<div class="border-radius-small color-sample">
										<p>unsupported scope (<?php echo $scope;?>)</p>
									</div>
	          			<?php
	          		}
	          		?>
							</li>
          	</ul>
          	<?php
          	echo '
          	<input type="hidden" name="' . $id . '" id="' . $id . '" value="' . $value . '"></input>
          	<a href="#" class="nxsbutton1 nxs-float-right" onclick="nxs_js_startcolorvariationpicker_' . $id . '(); return false;">' . nxs_l18n__("Change", "nxs_td") . '</a>
          </div>
        </div>
        <div class="nxs-clear"></div>
      </div>
  ';
  ?>
  <script type="text/javascript">
		function nxs_js_startcolorvariationpicker_<?php echo $id;?>()
		{
			nxs_js_setpopupdatefromcontrols(); 
			nxs_js_popup_setsessiondata("nxs_colorvariationpicker_invoker", nxs_js_popup_getcurrentsheet()); 
			nxs_js_popup_setsessiondata("nxs_colorvariationpicker_sampletext", "<?php echo $sampletext;?>"); 
			nxs_js_popup_setsessiondata("nxs_colorvariationpicker_targetvariable", "<?php echo $id;?>"); 
			nxs_js_popup_setsessiondata("nxs_colorvariationpicker_scope", "<?php echo $scope;?>"); 
			nxs_js_popup_setsessiondata("nxs_colorvariationpicker_currentvalue", "<?php echo $value;?>");
			
			nxs_js_popup_navigateto("colorvariationpicker");
		}
	</script>
  <?php
	//
}

function nxs_popup_optiontype_colorvariation_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_hiddenfield("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_colorvariation_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_colorvariation_getpersistbehaviour()
{
	return "writeid";
}

?>