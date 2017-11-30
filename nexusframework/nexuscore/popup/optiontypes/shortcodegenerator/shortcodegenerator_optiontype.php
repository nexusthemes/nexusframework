<?php
function nxs_popup_optiontype_shortcodegenerator_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	
	$contextprocessor = $clientpopupsessioncontext["contextprocessor"];
	$postid = $clientpopupsessioncontext["postid"];
	$placeholderid = $clientpopupsessioncontext["placeholderid"];
	
	$placeholdermetadata = nxs_getwidgetmetadata($postid, $placeholderid);
	$placeholdertype = $placeholdermetadata["type"];
	
	$options = nxs_genericpopup_getoptions($args);
	$style_ids_array = nxs_unistyle_getunistyleablefieldids($options);
	$content_ids_array = nxs_unicontent_getunicontentablefieldids($options);
	
	$generatedshortcode = "";
	$generatedshortcode .= "[nxs_widget type='{$placeholdertype}' ";
	
	// content properties first
	
	$unicontent = $placeholdermeta["unicontent"];
	if ($unicontent == "")
	{
		foreach ($content_ids_array as $field)
		{
			$value = $placeholdermetadata[$field];
			if ($value != "")
			{
				$generatedshortcode .= "{$field}='" . esc_html($value) . "' ";
			}
		}
	}
	else
	{
		$generatedshortcode .= "unicontent='" . esc_html($unicontent) . "' ";
	}
	
	// style properties second
	
	$unistyle = $placeholdermetadata["unistyle"];
	if ($unistyle == "")
	{
		foreach ($content_ids_array as $field)
		{
			$value = $placeholdermetadata[$field];
			if ($value != "")
			{
				$generatedshortcode .= "{$field}='" . esc_html($value) . "' ";
			}
		}
	}
	else
	{
		$generatedshortcode .= "unistyle='" . esc_html($unistyle) . "' ";
	}
	
	$generatedshortcode .= "]";
	
	if (true)
	{	
		?>
	  <div class="content2">
      <div class="box">
    			<?php echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip); ?>
        	<div class="box-content">
        		<?php echo $generatedshortcode; ?>
        		<br /><br />
        	</div>
        </div>
        <div class="nxs-clear"></div>
	    </div> <!-- END content2 -->
		<?php
	}
	//
}

function nxs_popup_optiontype_shortcodegenerator_renderstorestatecontroldata($optionvalues)
{
	// nothing to do here
}

function nxs_popup_optiontype_shortcodegenerator_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_shortcodegenerator_getpersistbehaviour()
{
	return "readonly";
}

?>