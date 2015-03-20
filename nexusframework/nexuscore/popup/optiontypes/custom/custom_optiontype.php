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
	        ?>
          	</div>
        </div>
        <div class="nxs-clear"></div>
    </div>
<?php
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