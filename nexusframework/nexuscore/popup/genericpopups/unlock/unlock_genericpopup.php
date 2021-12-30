<?php

function nxs_popup_genericpopup_unistylepersister_getpopup($args)
{
	extract($args);
	
	// clientpopupsessiondata bevat key values van de client side
	// deze overschrijft met opzet (tijdelijk) mogelijk waarden die via $args
	// zijn meegegeven; hierdoor kan namelijk een 'gevoel' worden gecreeerd
	// van een 'state' die client side leeft, die helpt om meerdere (popup) 
	// pagina's state te laten delen. De inhoud van clientpopupsessiondata is een
	// array die wordt gevoed door de clientside variabele "popupsessiondata",
	// die gedefinieerd is in de file 'frontendediting.php'
	
	if ($clientpopupsessioncontext != null) { if ($clientpopupsessioncontext != null) { extract($clientpopupsessioncontext); } }
	if ($clientpopupsessiondata != null) { extract($clientpopupsessiondata); }
	if ($clientshortscopedata != null) { extract($clientshortscopedata); }
	
	if ($nxs_unistylepersister_group == "")
	{
		nxs_webmethod_return_nack("error; nxs_unistylepersister_group not set");
	}
	
	$result = array();
	$result["result"] = "OK";

	nxs_ob_start();

	//
	// event handling
	//

	if ($popupaction != "")
	{
		if ($popupaction == "unistylesavenew" || $popupaction == "unistyleoverride")
		{
			if ($nxs_unistylepersister_semicolonseperatedids == "") { nxs_webmethod_return_nack("error; nxs_unistylepersister_semicolonseperatedids not set"); }
			if ($nxs_unistylepersister_group == "") { nxs_webmethod_return_nack("error; nxs_unistylepersister_group not set"); }
			
			$unistyleproperties = array();
			$ids_array = explode(";", $nxs_unistylepersister_semicolonseperatedids);
			foreach($ids_array as $currentid)
			{
				$currentvalue = $clientpopupsessiondata[$currentid];
				if (!isset($currentvalue))
				{
					$currentvalue = "";
				}
				$unistyleproperties[$currentid] = $currentvalue;
			}
			
			if ($popupaction == "unistylesavenew")
			{
				// ensure its not existing yet
				//$existingproperties = nxs_unistyle_getunistyleproperties($nxs_unistylepersister_group, $unistyletostore);
				//if (isset($existingproperties))
				//{
				//	nxs_webmethod_return_nack("unistyle already exists [$unistyletostore] [$nxs_unistylepersister_group]");
				//}
			}

			// step 1 - store the properties as a unistyle
			nxs_unistyle_persistunistyle($nxs_unistylepersister_group, $unistyletostore, $unistyleproperties);

			// step 2 - persist the unistyle for the contextprocessor,
			// if invoked by a widget, this means in the widget we will persist/
			// configure the selected unistyle
			$functionnametoinvoke = 'nxs_popup_contextprocessor_' . $contextprocessor . '_mergedata_internal';
						
			if (function_exists($functionnametoinvoke))
			{
				$blendedmetadata = array();
				$blendedmetadata["unistyle"] = $unistyletostore;
				$result = call_user_func($functionnametoinvoke, $args, $blendedmetadata);
			}
			else
			{
				nxs_webmethod_return_nack("missing function name for option type; $functionnametoinvoke");
			}
			
			?>		
			<script>
				// reload the entire page; this is required, because its possible that other widgets
				// use the same style as the one we just persisted, in that case its impossible to
				// update all of them, unless we refresh the entire page
				nxs_js_redirecttopostid(<?php echo $containerpostid; ?>);
			</script>
			<?php
			// Setting the contents of the output buffer into a variable and cleaning up te buffer
		}
		else
		{
			nxs_webmethod_return_nack("action is not yet supported; " . $popupaction);
		}
	}
	else
	{
		?>
		<div class="nxs-admin-wrap">
			<div class="block">
				<?php nxs_render_popup_header(nxs_l18n__("Unistyle persister")); ?>
				<div class="nxs-popup-content-canvas-cropper">
					<div class="nxs-popup-content-canvas">
						<?php
						if ($unistyleprevious != "")
						{
							?>
							<div class="content2">
								<div class="box-title">
									<h4><?php nxs_l18n_e("Override previous used unistyle", "nxs_td"); ?></h4>		                
			          </div>
			          <div class="box-content">			
									<a href="#" class="nxsbutton1 nxs-float-right" onclick="nxs_js_overridepreviousunistyle(); return false;"><?php nxs_l18n_e("Persist", "nxs_td"); ?> <?php echo $unistyleprevious; ?></a>
									<script>
		        				function nxs_js_overridepreviousunistyle()
		        				{
		        					var r = confirm("<?php nxs_l18n_e("All components that are connected to this unistyle will be updated if you proceed. Is this what you want?", "nxs_td"); ?>");
											if (r == false)
										  {
											  return;
										  }
												  
											nxs_js_popup_setshortscopedata("unistyletostore", "<?php echo $unistyleprevious; ?>");
		        					nxs_js_popup_setshortscopedata("popupaction", "unistyleoverride");
		        					nxs_js_popup_refresh_v2(true);
		              	}
		              </script>
		            </div>
	 							<div class="nxs-clear"></div>
							</div> <!-- END content -->
							<?php
						}
						?>
						
						<div class="content2">
							<div class="box-title">
								<h4><?php nxs_l18n_e("Override existing unistyle", "nxs_td"); ?></h4>		                
		          </div>
		          <div class="box-content">							
								<select id='overrideexistingunistyle'>
									<?php
									$unistyles = nxs_unistyle_getunistylenames($nxs_unistylepersister_group);
									foreach($unistyles as $currentunistylekey => $currentunistyleval)
									{
										?>
										<option value='<?php echo $currentunistylekey; ?>'><?php echo $currentunistyleval; ?></option>
										<?php
									}								
									?>
								</select>
								<a href="#" class="nxsbutton1 nxs-float-right" onclick="nxs_js_overrideexistingunistyle(); return false;"><?php nxs_l18n_e("Persist", "nxs_td"); ?></a>
								<script>
	        				function nxs_js_overrideexistingunistyle()
	        				{
	        					var stylename = jQ_nxs('#overrideexistingunistyle').val();
										if (stylename == '')
										{
											nxs_js_popup_notifynotok('<?php nxs_l18n_e("Please select a name"); ?>', 'newunistylename');
											return;
										}
	        					
	        					var r = confirm("<?php nxs_l18n_e("All components that are connected to this unistyle will be updated if you proceed. Is this what you want?", "nxs_td"); ?>");
										if (r == false)
									  {
										  return;
									  }
										
										nxs_js_popup_setshortscopedata("unistyletostore", stylename);
	        					nxs_js_popup_setshortscopedata("popupaction", "unistyleoverride");
	        					nxs_js_popup_refresh_v2(true);
	              	}
	              </script>
	            </div>
							<div class="nxs-clear"></div>
						</div> <!-- END content -->
						
						<div class="content2">
							<div class="box-title">
								<h4><?php nxs_l18n_e("Create a new unistyle", "nxs_td"); ?></h4>		                
		          </div>
		          <div class="box-content">
								<input type='text' id='newunistylename' style='width: 80%' value='' placeholder='enter your name here' />
								<a href="#" class="nxsbutton1 nxs-float-right" onclick="nxs_js_storenewunistyle(); return false;"><?php nxs_l18n_e("Persist", "nxs_td"); ?></a>
								<script>
	        				function nxs_js_storenewunistyle()
	        				{
	        					var stylename = jQ_nxs('#newunistylename').val();
										if (stylename == '')
										{
											nxs_js_popup_notifynotok('<?php nxs_l18n_e("Please enter a name"); ?>', 'newunistylename');
											return;
										}
	        					
	        					var r = confirm("<?php nxs_l18n_e("All components that are connected to this unistyle will be updated if you proceed. Is this what you want?", "nxs_td"); ?>");
										if (r == false)
									  {
										  return;
									  }
										
										nxs_js_popup_setshortscopedata("unistyletostore", stylename);
	        					nxs_js_popup_setshortscopedata("popupaction", "unistylesavenew");
	        					nxs_js_popup_refresh_v2(true);
	              	}
	              </script>
	            </div>
							<div class="nxs-clear"></div>
						</div> <!-- END content -->
						
					</div> <!-- END nxs-popup-content-canvas -->
				</div> <!-- END nxs-popup-content-canvas-cropper -->
		
				<div class="content2">
					<div class="box">
						<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_popup_navigateto("<?php echo $nxs_unistylepersister_invoker; ?>"); return false;'><?php nxs_l18n_e("Back"); ?></a>
					</div>
					<div class="nxs-clear margin"></div>
				</div> <!-- END content -->
				
			</div> <!-- END block -->
		</div> <!-- END nxs-admin-wrap -->
		<?php
	}

	// Setting the contents of the output buffer into a variable and cleaning up te buffer
  $html = nxs_ob_get_contents();
  nxs_ob_end_clean();
    
  // Setting the contents of the variable to the appropriate array position
  // The framework uses this array with its accompanying values to render the page
  $result["html"] = $html;
  return $result;
}
?>