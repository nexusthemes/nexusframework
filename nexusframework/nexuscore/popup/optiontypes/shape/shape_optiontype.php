<?php
function nxs_popup_optiontype_shape_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
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
	
	?>
	<div class="content2">
	    <div class="box">
	    	<?php echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip); ?>
        	<div class="box-content">
          	<ul>
	          	<li onclick='nxs_js_startcolorzenpicker_<?php echo $id;?>(); return false;' style='cursor: pointer;' class='nxs-float-left'>
	          		<?php
	          		if (isset($value) && $value != "")
	          		{
          				$shapepaths = nxs_getshapepaths();
          				$path = $shapepaths[$value];
          				?>
						<svg class="nxs-width100" x="0px" y="0px" viewBox="0 0 100 5.194" preserveAspectRatio="none">
							<?php echo $path;?>
						</svg>
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
          	<input type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $value; ?>"></input>
          	<a href="#" class="nxsbutton1 nxs-float-right" onclick="nxs_js_startshapepicker_<?php echo $id;?>(); return false;"><?php echo nxs_l18n__("Change", "nxs_td"); ?></a>
          </div>
        </div>
        <div class="nxs-clear"></div>
    </div>
    
	<script type="text/javascript">
		function nxs_js_startshapepicker_<?php echo $id;?>()
		{
			nxs_js_setpopupdatefromcontrols();

			nxs_js_popup_setsessiondata("nxs_shapepicker_invoker", nxs_js_popup_getcurrentsheet()); 
			nxs_js_popup_setsessiondata("nxs_shapepicker_targetvariable", "<?php echo $id;?>"); 
			nxs_js_popup_setsessiondata("nxs_shapepicker_currentvalue", "<?php echo $value;?>");
			
			nxs_js_popup_navigateto("shapepicker");
		}
	</script>
  <?php
}

function nxs_popup_optiontype_shape_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_hiddenfield("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_shape_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_shape_getpersistbehaviour()
{
	return "writeid";
}

?>