<?php
function nxs_popup_optiontype_lock_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	
	$id = "lock";
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter
	
	if ($value != "")
	{
		$label = nxs_l18n__("Lock", "nxs_td") . " (active)";
		$tooltip = nxs_l18n__("The lock feature is currently active.", "nxs_td");
	}
	else
	{
		$label = nxs_l18n__("Lock", "nxs_td");
		$tooltip = nxs_l18n__("The lock is currently inactive.", "nxs_td");
	}
	
	$options = nxs_genericpopup_getoptions($args);
	
	$ids_array = nxs_unistyle_getunistyleablefieldids($options);
	$semicolonseperatedids = implode(";", $ids_array);
	
	if (true)
	{	
		?>
	  <div class="content2">
	      <div class="box">
        			<?php echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip); ?>
	          	<div class="box-content">
	            		<?php
	            		if (!isset($value) || $value == "") 
	            		{
	            			// currently not locked
		            		?>
		                <a href="#" class="nxsbutton1 nxs-float-right" onclick="nxs_js_widgetlock(); return false;"><?php nxs_l18n_e("Lock", "nxs_td"); ?></a>
										<script type='text/javascript'>
	            				function nxs_js_widgetlock()
	            				{
	            					// save "other" fields in mem
	            					nxs_js_setpopupdatefromcontrols();
	            					nxs_js_popup_setsessiondata("<?php echo $id; ?>", "locked");
	            					nxs_js_popup_sessiondata_make_dirty();            					
	            					nxs_js_popup_navigateto_v2('unlock', true);
		                	}
		                </script>
		               	<?php
	            		}
	            		else
	            		{
	            			// currently locked, unlock it (in mem)
		            		?>
		                <a href="#" class="nxsbutton1 nxs-float-right" onclick="nxs_js_widgetunlock(); return false;"><?php nxs_l18n_e("Unlock", "nxs_td"); ?></a>
										<script type='text/javascript'>
	            				function nxs_js_widgetunlock()
	            				{
	            					// save "other" fields in mem
	            					nxs_js_setpopupdatefromcontrols();
	            					nxs_js_popup_setsessiondata("<?php echo $id; ?>", "");
	            					nxs_js_popup_sessiondata_make_dirty();            					
	            					nxs_js_popup_navigateto_v2('home', true);
		                	}
		                </script>
		               	<?php
	            		}
	            		?>
	            </div>
	        </div>
	        <div class="nxs-clear"></div>
	    </div> <!-- END content2 -->
		<?php
	}
	//
}

function nxs_popup_optiontype_lock_renderstorestatecontroldata($optionvalues)
{
	$id = "unistyle"; // $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_dropdown("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_lock_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_lock_getpersistbehaviour()
{
	return "writeid";
}

?>