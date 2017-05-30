<?php
require_once("/srv/generic/plugins-available/nxs-contentprovider/businessmodellogic.php");

function nxs_popup_optiontype_modelpicker_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
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
	
	$singularschema = "websitebouwerusp";
	// load all data
	
	$listschema = "listof{$singularschema}";
	$listhumanmodelidentification = "singleton";
	
	$listresult = nxs_businessmodel_getmodelbyhumanid($listschema, $listhumanmodelidentification);
	
	nxs_ob_start();
	
	?>
	<div class="content2">
    <div class="box">
    	<?php echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip); ?>
			<div class="box-content">
				<script src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js" />
				<script>
					var entiretexttoid = [
						<?php
						$instances = $listresult["contentmodel"][$singularschema]["instances"];
						foreach ($instances as $index => $instancemeta)
						{
							$currentcontent = $instancemeta["content"];
							$currenthumanmodelid = $currentcontent["humanmodelid"];
							$instance = nxs_businessmodel_getmodelbyhumanid($singularschema, $currenthumanmodelid);
							$contentmodel = $instance["contentmodel"];
							$taxonomyproperties = $contentmodel["properties"]["taxonomy"];
							var_dump($taxonomyproperties);
							?>
							{
								<?php
								$isfirstprop = true;
								foreach ($taxonomyproperties as $key => $val)
								{
									if ($isfirstprop)
									{
										$isfirstprop = false;
									}
									else
									{
										echo ",\r\n";
									}
									echo "{$key}: '{$val}'";
								}
								?>
							}
							<?php
						}
						?>
					];
					
				</script>
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
      	<input type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $value; ?>" />
      </div>
		</div>
    <div class="nxs-clear"></div>
  </div>
  
  <?php
  
  $html = nxs_ob_get_contents();
  
  //error_log("list picker;");
  //error_log($html);
  
	nxs_ob_end_clean();
	
	echo $html;
	//
}

function nxs_popup_optiontype_modelpicker_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_hiddenfield("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_modelpicker_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_modelpicker_getpersistbehaviour()
{
	return "writeid";
}
