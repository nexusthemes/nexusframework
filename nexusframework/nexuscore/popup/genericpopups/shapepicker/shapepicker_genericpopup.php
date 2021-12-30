<?php
function nxs_popup_genericpopup_shapepicker_getactiveclass($a, $b)
{
	if ($a == $b)
	{
		$result = "active";
	}
	else
	{
		$result = "none";
	}
	
	return $result;
}

function nxs_popup_genericpopup_shapepicker_getpopup($args)
{
	// initial values, can/will be overridden by the extracts below
	extract($args);
	
	// clientpopupsessiondata bevat key values van de client side
	// deze overschrijft met opzet (tijdelijk) mogelijk waarden die via $args
	// zijn meegegeven; hierdoor kan namelijk een 'gevoel' worden gecreeerd
	// van een 'state' die client side leeft, die helpt om meerdere (popup) 
	// pagina's state te laten delen. De inhoud van clientpopupsessiondata is een
	// array die wordt gevoed door de clientside variabele "popupsessiondata",
	// die gedefinieerd is in de file 'frontendediting.php'
	if ($clientpopupsessiondata != null) { extract($clientpopupsessiondata); }	
	if ($clientshortscopedata != null) { extract($clientshortscopedata); }

	
	$result = array();
	$result["result"] = "OK";
	
	nxs_ob_start();
	
	?>
	
	<style>
		.nxs-admin-wrap .shape-sample-head p 
		{
			font-size: 15px;
			text-align: center;
			line-height: 1.2em;
		}
		
		.nxs-admin-wrap .shape-sample-head 
		{
			margin-right: 10px;
			padding: 10px;
			min-width: 50px;
		}						
		.nxs-admin-wrap .box-transparent-layer
		{
			background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAYAAABytg0kAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAAWdEVYdFNvZnR3YXJlAHBhaW50Lm5ldCA0LjA76PVpAAAAGElEQVQYV2P4//9/S0NDQwsDiPj//38LAGTRCwvADjD8AAAAAElFTkSuQmCC');
			padding: 5px;	
		}

		.active {
			box-shadow: none !important;
		}

		.active path,
		.active polygon {
			fill: red !important;
		}

		.shape {
			cursor: pointer;
		}

	</style>	
	<div class="nxs-admin-wrap">
		<div class="block">
			<?php nxs_render_popup_header(nxs_l18n__("Shape picker", "nxs_td")); ?>
			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
					
					<!-- flat background colors medium -->
					<div class="content2">
						<div class="box">
							<div class="box-title">
								<h4><?php nxs_l18n_e("Shapes", "nxs_td"); ?></h4>
							</div>
							<div class="box-content">
								<div class="box-transparent-layer">							
									<?php
									$shapepaths = nxs_getshapepaths();
									foreach($shapepaths as $pathkey => $path)
									{
										$identification = $pathkey;
										$activeclass = nxs_popup_genericpopup_shapepicker_getactiveclass($nxs_shapepicker_currentvalue, $identification);
										?>
										<div class="shape" onclick='nxs_js_selectshapeitem("<?php echo $identification;?>"); return false;'>
											<div class="<?php echo $activeclass; ?>" style="padding: 15px 10px;">
												<svg class="nxs-width100" x="0px" y="0px" viewBox="0 0 100 5.194" preserveAspectRatio="none">
													<?php echo $path;?>
												</svg>
											</div>
										</div>
										<?php
									}
									?>
									<div class="nxs-clear" />
								</div>
							</div>
						</div>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
					
					
				</div> <!-- END nxs-popup-content-canvas -->
			</div> <!-- END nxs-popup-content-canvas-cropper -->
	
			<div class="content2">
				<div class="box">
					<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_popup_navigateto("<?php echo $nxs_shapepicker_invoker; ?>"); return false;'><?php nxs_l18n_e("Back", "nxs_td"); ?></a>
					<?php 
					if ($nxs_shapepicker_currentvalue != "") 
					{
						?>
						<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton1 nxs-float-right" onclick='nxs_js_selectshapeitem(""); return false;'><?php nxs_l18n_e("No shape", "nxs_td"); ?></a>
						<?php
					}
					?>
				</div>
				<div class="nxs-clear"></div>
			</div> <!-- END content -->
		</div> <!-- END block -->
	</div> <!-- END nxs-admin-wrap -->
	
	<script>
	
		function nxs_js_selectshapeitem(item) 
		{
			nxs_js_popup_setsessiondata("<?php echo $nxs_shapepicker_targetvariable; ?>", item);
			nxs_js_popup_sessiondata_make_dirty();
			// toon eerste scherm in de popup
			nxs_js_popup_navigateto("<?php echo $nxs_shapepicker_invoker; ?>");
		}
	
	</script>
	<?php

	// Setting the contents of the output buffer into a variable and cleaning up te buffer
  $html = nxs_ob_get_contents();
  nxs_ob_end_clean();
    
  // Setting the contents of the variable to the appropriate array position
  // The framework uses this array with its accompanying values to render the page
  $result["html"] = $html;
  return $result;
}
?>