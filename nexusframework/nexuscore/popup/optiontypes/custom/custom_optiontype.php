<?php
function nxs_popup_optiontype_custom_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	
	if (isset($runtimeblendeddata[$id]))
	{
		$value = $runtimeblendeddata[$id];
	}
	else
	{
		$value = "";
	}
	
	if (!$layouttype )
	{
	?>
		<div class="content2" style="">
		    <div class="box">
		        <div class="box-title">
					<h4><?php echo $label; ?></h4>
					<?php if ($tooltip != ""){ ?>
						<span class="info">?
							<div class="info-description"><?php echo $tooltip; ?></div>
						</span>;
					<?php } ?>
				</div>
	          	<div class="box-content">
	          	<?php
		          	nxs_popup_optiontype_custom_runfunctionifitexists($optionvalues, $args, $runtimeblendeddata);
		        ?>
	          	</div>
	        </div>
	        <div class="nxs-clear"></div>
	    </div>
	<?php
	}

	else if ($layouttype == "custom") {
		nxs_popup_optiontype_custom_runfunctionifitexists($optionvalues, $args, $runtimeblendeddata);
	}

	else {
		echo "Unkown layout type";
	}
}

function nxs_popup_optiontype_custom_runfunctionifitexists($optionvalues, $args, $runtimeblendeddata)
{
	extract($optionvalues);

	echo $custom;
  	// if handler is set, delegate rendering to handler
  	if (isset($customcontenthandler))
  	{
  		$functionnametoinvoke = $customcontenthandler;
		if (function_exists($functionnametoinvoke))
		{
			$p = array();
			$p["optionvalues"] = $optionvalues;
			$p["args"] = $args;
			$p["runtimeblendeddata"] = $runtimeblendeddata;
			$result = call_user_func_array($functionnametoinvoke, $p);
			echo $result;
		}
		else
		{
			echo "function not found; " . $customcontenthandler;
		}
  	}
}

function nxs_popup_optiontype_custom_renderstorestatecontroldata($optionvalues)
{
	// nothing to do here
}

function nxs_popup_optiontype_custom_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	// nothing to do here
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_custom_getpersistbehaviour()
{
	return "readonly";
}

?>