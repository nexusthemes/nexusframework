<?php
function nxs_popup_optiontype_backgroundpattern_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
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
	    <div class="box">
	        <div class="box-title">
						<h4>'. $label .'</h4>
	';
							if ($tooltip != "") 
							{
								echo '<span class="info">?
									<div class="info-description">' . $tooltip .'</div>
								</span>';
							}
						?>
					</div>
          <div class="box-content">
          	<ul class="textures">
	          	<li onclick='nxs_js_startbackgroundpatternpicker_<?php echo $id;?>(); return false;' class='nxs-float-left <?php echo $value; ?>'>
	          		&nbsp;	
							</li>
          	</ul>
          	<?php
          	echo '
          	<input type="hidden" name="' . $id . '" id="' . $id . '" value="' . $value . '"></input>
          	<a href="#" class="nxsbutton1 nxs-float-right" onclick="nxs_js_startbackgroundpatternpicker_' . $id . '(); return false;">Change pattern</a>
          </div>
        </div>
        <div class="nxs-clear"></div>
      </div>
  ';
  ?>
  <script>
		function nxs_js_startbackgroundpatternpicker_<?php echo $id;?>()
		{
			nxs_js_setpopupdatefromcontrols(); 
			nxs_js_popup_setsessiondata("nxs_backgroundpatternpicker_invoker", nxs_js_popup_getcurrentsheet()); 
			nxs_js_popup_setsessiondata("nxs_backgroundpatternpicker_sampletext", "<?php echo $sampletext;?>"); 
			nxs_js_popup_setsessiondata("nxs_backgroundpatternpicker_targetvariable", "<?php echo $id;?>"); 
			nxs_js_popup_setsessiondata("nxs_backgroundpatternpicker_currentvalue", "<?php echo $value;?>");
			
			nxs_js_popup_navigateto("backgroundpatternpicker");
		}
	</script>
  <?php
	//
}

function nxs_popup_optiontype_backgroundpattern_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_hiddenfield("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_backgroundpattern_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_backgroundpattern_getpersistbehaviour()
{
	return "writeid";
}

?>