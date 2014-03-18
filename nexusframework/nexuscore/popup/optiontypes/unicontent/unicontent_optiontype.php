<?php
function nxs_popup_optiontype_unicontent_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	$readonly = "false";
	$visibility = "show";
	
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);

	$id = "unicontent";
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter
	
	if (isset($required) && $required == "true")
	{
		$isrequiredhtml = "<span class='required'>*</span>";	
	}
	else
	{
		$isrequiredhtml = "";
	}
	
	if (isset($readonly) && $readonly == "true")
	{
		$readonly = "readonly='readonly'";
	}
	else
	{
		$readonly = "";
	}
	
	if ($visibility == "hide" || $visibility == "hidden")
	{
		$content2style = "style='display: none;'";
	}
	
	$options = nxs_genericpopup_getoptions($args);
	
	$group = $options["unifiedcontent"]["group"];
	
	if (!isset($group) || $group == "")
	{
		//
		echo '
		<div class="content2" ' . $content2style . '>
	    <div class="box">
        <div class="box-title">
					<h4>'. $label . $isrequiredhtml . '</h4>
				</div>
				<div class="box-content">
					' . nxs_l18n__("N/A", "nxs_td") . '
        </div>
			</div>
			<div class="nxs-clear"></div>
		</div>
		';
	}
	else
	{
		echo '
		<div class="content2" ' . $content2style . '>
		    <div class="box">';
     			echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip);
					echo '
	          <div class="box-content">';
	          if ($value == "")
	          {
	          	
	          	$dropdown = nxs_unicontent_getunicontentnames($group);
	          	
	          	echo '<input type="text" id="'. $id . '" name="'. $id . '" value="' . nxs_render_html_escape_doublequote($value) . '" placeholder="' . nxs_render_html_escape_doublequote($placeholder) . '" ' . $readonly . ' />';
	          	echo '<br />';
	          	echo 'or load an existing one: ';
	          	?>
	            <select id='<?php echo $id; ?>_sel' onchange="nxs_js_setunicontent_<?php echo $id; ?>(); return false;">
	            	<?php 
	          		// dropdown is specified as keys and values
	            	foreach ($dropdown as $currentkey => $currentvalue) 
	            	{
	            		if ($currentkey == "@@@nxsempty@@@")
	            		{
	            			$currentkey = "";
	            		}
	            		
	            		$selected = "";
	            		if ($currentkey == $value) 
	            		{
	            			$selected ="selected='selected'";
	            		}
	            		?>
	                <option <?php echo $selected; ?> value='<?php echo $currentkey; ?>'><?php echo $currentvalue; ?></option>
	                <?php 
	              } 
	              ?>
	            </select>
	            <script type='text/javascript'>
	            	function nxs_js_setunicontent_<?php echo $id; ?>()
	            	{
	            		
	            		nxs_js_setpopupdatefromcontrols(); 

	            		var sel = '#<?php echo $id; ?>_sel';
	            		//nxs_js_alert(sel);
	            		var text = jQuery(sel).val();
	            		// store selected unicontent in mem
	            		nxs_js_popup_setsessiondata("<?php echo $id; ?>", text);
	            		
	            		// wipe the in mem keys that are used by the unicontent,
	            		// such that the upcoming popup refresh will reload
	            		// the unicontent as stored in the DB
	            		<?php
	            		$ids_array = nxs_unicontent_getunicontentablefieldids($options);
									foreach($ids_array as $currentid)
									{
										?>
		            		nxs_js_popupsession_data_remove_key("<?php echo $currentid; ?>");
		            		nxs_js_log("<?php echo $currentid; ?>");
		            		<?php
									}
	            		?>
	            		
	            		// 
	            		nxs_js_popup_refresh_v2(true);
	            	}
	            </script>
	          	<?php
	          }
	          else 
	          {
	          	$name = $value;
	          	if (nxs_unicontent_exists($group, $name))
	          	{
		          	echo '<input type="hidden" id="'. $id . '" name="'. $id . '" value="' . nxs_render_html_escape_doublequote($value) . '" placeholder="' . nxs_render_html_escape_doublequote($placeholder) . '" ' . $readonly . ' />';
		          	echo $value;
		          	echo ' <a class="nxsbutton1 nxs-float-right" href="#" onclick="nxs_js_clearunicontent(); return false;">Change</a>';
	          	}
	          	else
	          	{
		          	echo '<input type="text" id="'. $id . '" name="'. $id . '" value="' . nxs_render_html_escape_doublequote($value) . '" placeholder="' . nxs_render_html_escape_doublequote($placeholder) . '" ' . $readonly . ' />';
		          	echo '(new)';
	          	}
	          }
						echo '
	          </div>
	        </div>
	        <div class="nxs-clear"></div>
	      </div>
	      <script type="text/javascript">
	      	function nxs_js_clearunicontent()
	      	{
	      		jQuery("#' . $id . '").val("");
	      		nxs_js_popup_sessiondata_make_dirty();
	      		nxs_js_setpopupdatefromcontrols(); 
	      		nxs_js_popup_refresh_v2(true);
	      	}
	      </script>
	      ';
		//
	}
}

function nxs_popup_optiontype_unicontent_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_textbox("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_unicontent_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_unicontent_getpersistbehaviour()
{
	return "writeid";
}

?>