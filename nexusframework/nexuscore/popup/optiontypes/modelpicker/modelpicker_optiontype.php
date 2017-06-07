<?php
function nxs_popup_optiontype_modelpicker_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	// default values
	$emptyitem_enable = "true";
	$emptyitem_text = "";
	$emptyitem_value = "";
	
	$previewlink_enable = "true";
	$previewlink_text = "preview";
	$previewlink_target = "_blank";
	
	$beforeitems = array();
	
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	
	$value = $runtimeblendeddata[$id];	// $id is the parametername, $$id is the value of that parameter
	
	//
	//$iterator_datasource = "embeddable";
	$iteratormodeluri = "singleton@listof{$iterator_datasource}";
	
	global $nxs_g_modelmanager;
	
	$contentmodel = $nxs_g_modelmanager->getcontentmodel($iteratormodeluri);
	$instances = $contentmodel[$iterator_datasource]["instances"];
	
	$items = array();
	foreach ($instances as $instance)
	{
		$itemhumanmodelid = $instance["content"]["humanmodelid"];
		$itemuri = "{$itemhumanmodelid}@${iterator_datasource}";
		$itemtitle = $nxs_g_modelmanager->getcontentmodelproperty($itemuri, $textproperty);
		$itemvalue = $nxs_g_modelmanager->getcontentmodelproperty($itemuri, $valueproperty);
		
		$items[$itemvalue] = $itemtitle;
	}
	
	asort($items);
	
	// add a variable option so people can use a lookup too (perhaps make this optional?)
	$items["{{" . $id . "}}"] = "{{" . $id . "}}";
	
	?>
	<div class="content2">
		<div class="box">
	    <?php echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip); ?>
			<div class="box-content">
				<select id="<?php echo $id; ?>" class="chosen-select" name="<?php echo $id; ?>" onchange="nxs_js_popup_sessiondata_make_dirty();">
				<?php				 
					if ($id == "") 
					{
						$selected = "selected='selected'";
					} 
					else 
					{
						$selected = "";
					}
				
				$isfound = false;
				foreach ($items as $itemvalue => $itemtext) 
				{
					$itemtext = nxs_cutstring($itemtext, 50);
					if (trim($itemtext) == "")
					{
						$itemtext = "(empty)";
					}
					
					$selected = "";
					if ($itemvalue == $value) 
					{
						$isfound = true;
						$selected = "selected='selected'";
					} 
					else 
					{
						$selected = "";
					}
					echo "<option value='$itemvalue' $selected	>$itemtext</option>\r\n";
				}
				?>
				</select>
			</div>
		</div>
		<div class="nxs-clear"></div>
	</div> <!--END content-->
	<?php
}

function nxs_popup_optiontype_modelpicker_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_dropdown("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_modelpicker_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	
	$id = $optionvalues["id"];
	$value = $metadata[$id];
	
	$globalid = nxs_get_globalid($value, true);
	$result[$id . "_globalid"] = $globalid;
	
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
