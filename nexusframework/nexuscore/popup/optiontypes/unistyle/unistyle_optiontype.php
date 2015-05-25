<?php
function nxs_popup_optiontype_unistyle_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	
	$id = "unistyle";
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter
	
	if ($value != "")
	{
		$label = nxs_l18n__("Unistyle", "nxs_td") . " (active)";
		$tooltip = nxs_l18n__("The unistyle feature is currently active. This means the styling properties can be re-used on other items on your site, saving you time to keep the style in-sync. The set of properties (identified by a unistyle name) are stored in a centralized place. If you want to customize this style, press the de-activate button on the right. This will de-activate the unistyle feature. After de-activation you will be able to override an existing, or create a new unistyle.", "nxs_td");
	}
	else
	{
		$label = nxs_l18n__("Unistyle", "nxs_td");
		$tooltip = nxs_l18n__("The unistyle is currently inactive. This means you can tweak and tune individual style properties of this component without having to worry that other parts of your site are impacted. If you are finished configuring the style properties, you could decide to store the style properties in a an existing or new centralized location, called a unistyle. This would allow you to re-use the same style, helping you to speed up creating a beautiful designzen for your site.", "nxs_td");
	}
	
	$options = nxs_genericpopup_getoptions($args);
	
	$ids_array = nxs_unistyle_getunistyleablefieldids($options);
	$semicolonseperatedids = implode(";", $ids_array);
	
	$group = $options["unifiedstyling"]["group"];
	if (!isset($group) || $group == "")
	{
		?>
		<div class="content2">
	    <div class="box">
	      <div class="box-title">Unistyle</div>
	      <div class="box-content">
	      	Not (yet) available (add group in options)
	    	</div>
	    	<div class="nxs-clear"></div>
	    </div>
	  </div>
	  <?php
	}				
	else
	{	
		$dropdown = nxs_unistyle_getunistylenames($group);	
		?>
	  <div class="content2">
	      <div class="box">
					<?php echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip); ?>

	          	<div class="box-content">
	            		<?php
	            		if (!isset($value) || $value == "") 
	            		{
		            		?>
		                <select id='<?php echo $id; ?>_sel' onchange="nxs_js_setunistyle_<?php echo $id; ?>(); return false;">
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
				            	function nxs_js_setunistyle_<?php echo $id; ?>()
				            	{
				            		nxs_js_setpopupdatefromcontrols(); 

				            		var sel = '#<?php echo $id; ?>_sel';
				            		//nxs_js_alert(sel);
				            		var text = jQ_nxs(sel).val();
				            		// store selected unistyle in mem
				            		nxs_js_popup_setsessiondata("<?php echo $id; ?>", text);
				            		
				            		// wipe the in mem keys that are used by the unicontent,
				            		// such that the upcoming popup refresh will reload
				            		// the unicontent as stored in the DB
				            		<?php
				            		$ids_array = nxs_unistyle_getunistyleablefieldids($options);
												foreach($ids_array as $currentid)
												{
													?>
					            		nxs_js_popupsession_data_remove_key("<?php echo $currentid; ?>");
					            		<?php
												}
				            		?>
				            		
				            		// 
				            		nxs_js_popup_refresh_v2(true);
				            	}
				            </script>		                
		                <a href="#" class="nxsbutton1 nxs-float-right" onclick="nxs_js_opensavesheet_unistyle(); return false;"><?php nxs_l18n_e("Persist", "nxs_td"); ?></a>
										<script type='text/javascript'>
	            				function nxs_js_opensavesheet_unistyle()
	            				{
	            					nxs_js_setpopupdatefromcontrols(); 
	            					nxs_js_popup_setsessioncontext("nxs_unistylepersister_group", "<?php echo $group; ?>"); 
	            					nxs_js_popup_setsessioncontext("nxs_unistylepersister_semicolonseperatedids", "<?php echo $semicolonseperatedids; ?>"); 
												nxs_js_popup_setsessioncontext("nxs_unistylepersister_invoker", nxs_js_popup_getcurrentsheet()); 
		                		nxs_js_popup_navigateto_v2('unistylepersister', true);
		                	}
		                </script>
		               	<?php
	            		}
	            		else
	            		{
	            			?>
	            			<span><?php echo $value; ?></span>
	            			<a href="#" class="nxsbutton1 nxs-float-right" onclick="nxs_js_disable_unistyle(); return false;"><?php nxs_l18n_e("De-activate", "nxs_td"); ?></a>
	            			<script type='text/javascript'>
	            				function nxs_js_disable_unistyle()
	            				{
	            					var r = confirm("<?php nxs_l18n_e("This will enable you to customize the style for this widget. Is this what you want?", "nxs_td"); ?>");
												if (r == false)
											  {
												  return;
											  }
												
												// continu
	            					nxs_js_setpopupdatefromcontrols(); 
	            					<?php
	            					$options = nxs_genericpopup_getoptions($args);
	            					
	            					$unistylablefieldsandvalues = nxs_unistyle_getpersistedunistylablefieldsandvalues($options, $value);
	            					$unistyleablefieldids = nxs_unistyle_getunistyleablefieldids($options);
	            					foreach ($unistyleablefieldids as $currentunistyleablefieldid)
	            					{
	            						$currentunistyleablefieldvalue = $unistylablefieldsandvalues[$currentunistyleablefieldid];
	            						if (!isset($currentunistyleablefieldvalue))
	            						{
	            							?>
	            							// value not set in unistyle, resetting...
	            							nxs_js_popup_setsessiondata('<?php echo $currentunistyleablefieldid; ?>', '');
	            							<?php
	            						}
	            						else
	            						{
		            						?>
		            						nxs_js_popup_setsessiondata('<?php echo $currentunistyleablefieldid; ?>', '<?php echo  nxs_render_html_escape_singlequote($currentunistyleablefieldvalue); ?>');
		            						<?php
		            					}
	            					}
	            					?>
	            					// keep the previous one, which will make it easier to store updated values later on
	            					nxs_js_popup_setsessiondata('unistyleprevious', '<?php echo nxs_render_html_escape_singlequote($unistyle); ?>');
	            					nxs_js_popup_setsessiondata('unistyle', '');
	            					nxs_js_popup_sessiondata_make_dirty();
	            					nxs_js_popup_refresh_v2(true);
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

function nxs_popup_optiontype_unistyle_renderstorestatecontroldata($optionvalues)
{
	$id = "unistyle"; // $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_dropdown("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_unistyle_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_unistyle_getpersistbehaviour()
{
	return "writeid";
}

?>