<?php
function nxs_popup_genericpopup_colorzenpicker_getactiveclass($a, $b)
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

function nxs_popup_genericpopup_colorzenpicker_getpopup($args)
{
	// initial values, can/will be overridden by the extracts below
	$nxs_colorzenpicker_colorset_flat_enabled = "true";
	$nxs_colorzenpicker_colorset_lightgradient_enabled = "true";
	$nxs_colorzenpicker_colorset_mediumgradient_enabled = "true";
	$nxs_colorzenpicker_sampletext = nxs_l18n__("Sample", "nxs_td")	;

	extract($args);
	
	// clientpopupsessiondata bevat key values van de client side
	// deze overschrijft met opzet (tijdelijk) mogelijk waarden die via $args
	// zijn meegegeven; hierdoor kan namelijk een 'gevoel' worden gecreeerd
	// van een 'state' die client side leeft, die helpt om meerdere (popup) 
	// pagina's state te laten delen. De inhoud van clientpopupsessiondata is een
	// array die wordt gevoed door de clientside variabele "popupsessiondata",
	// die gedefinieerd is in de file 'frontendediting.php'
	extract($clientpopupsessiondata);	
	extract($clientshortscopedata);

	
	$result = array();
	$result["result"] = "OK";
	
	ob_start();
	
	$padding = "";
	
	?>
	
	<style>
		.nxs-admin-wrap .color-sample-head p 
		{
			font-size: 15px;
			text-align: center;
			line-height: 1.2em;
		}
		
		.nxs-admin-wrap .color-sample-head 
		{
			margin-right: 10px;
			padding: 10px;
			min-width: 50px;
		}						
		.nxs-admin-wrap .box-transparent-layer
		{
			background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAYAAABytg0kAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAAWdEVYdFNvZnR3YXJlAHBhaW50Lm5ldCA0LjA76PVpAAAAGElEQVQYV2P4//9/S0NDQwsDiPj//38LAGTRCwvADjD8AAAAAElFTkSuQmCC');
			padding-top:5px;
			padding-bottom:5px;			
		}
	</style>	
	<div class="nxs-admin-wrap">
		<div class="block">
			<?php nxs_render_popup_header(nxs_l18n__("Color zen picker", "nxs_td")); ?>
			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
					
					<?php if ($nxs_colorzenpicker_colorset_flat_enabled == "true") { ?>
					<!-- flat background colors medium -->
					<div class="content2">
						<div class="box">
							<div class="box-title">
								<h4><?php nxs_l18n_e("Flat background colors", "nxs_td"); ?></h4>
							</div>
							<div class="box-content">
								<div class="box-transparent-layer">
									<?php
									$alphas = nxs_getcoloralphas();
									foreach($alphas as $currentalpha)
									{
										if ($currentalpha != 1)
										{
											// skip!
											continue;
											
										}
										else
										{
											$alphasuffix = "";
											// for example -a1-0 for 100%, or -a0-8 for 80% alpha
											// $alphasuffix = "-a" . nxs_getdashedtextrepresentation_for_numericvalue($currentalpha);
										}
										?>
										
										<div style='float:left;'>
											<div class="<?php echo $padding;?> color-sample-head border-radius-small">
												<p>0%</p>
											</div>										
										</div>
																			
										<div>
											<?php
											$subtypes = array("1", "2");
											foreach($subtypes as $currentsubtype)
											{
												if ($currentsubtype == "1")
												{
													$colortypes = array("base");
												}
												else
												{
													$colortypes = nxs_getcolorsinpalette();
												}
												foreach($colortypes as $currentcolortype)
												{
													$identification = $currentcolortype . $currentsubtype . $alphasuffix;
													$activeclass = nxs_popup_genericpopup_colorzenpicker_getactiveclass($nxs_colorzenpicker_currentvalue, $identification);
													
													?>
													<div onclick='nxs_js_selectcolorzenitem("<?php echo $identification;?>"); return false;' class='nxs-float-left'>
														<div class="nxs-colorzen-<?php echo $identification; ?> <?php echo $padding;?> border-radius-small color-sample <?php echo $activeclass; ?>">
															<p><?php echo $nxs_colorzenpicker_sampletext; ?></p>
														</div>
													</div>
													<?php
												}
											}
											?>
										</div>
										<div class="nxs-clear" />
										<?php
									}
									?>
								</div>
							</div>
						</div>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
					<?php } ?>
					
					<?php if ($nxs_colorzenpicker_colorset_flat_enabled == "true") { ?>
					<!-- transparant colors -->
					<div class="content2">
						<div class="box">
							<div class="box-title">
								<h4><?php nxs_l18n_e("Transparent background colors", "nxs_td"); ?></h4>
							</div>
							<div class="box-content">
								<div class="box-transparent-layer">
									<?php
									$alphas = nxs_getcoloralphas();
									foreach($alphas as $currentalpha)
									{
										if ($currentalpha == 1)
										{
											// skip!
											continue;
										}
										else
										{
											// for example -a1-0 for 100%, or -a0-8 for 80% alpha
											$alphasuffix = "-a" . nxs_getdashedtextrepresentation_for_numericvalue($currentalpha);
										}
										$transparencypercentage = 100 - ($currentalpha * 100);
										?>
										<div style='float:left;'>
											<div class="<?php echo $padding;?> color-sample-head border-radius-small">
												<p><?php echo $transparencypercentage; ?>%</p>
											</div>										
										</div>
										<div>
											<?php
											$subtypes = array("1", "2");
											foreach($subtypes as $currentsubtype)
											{
												if ($currentsubtype == "1")
												{
													$colortypes = array("base");
												}
												else
												{
													$colortypes = nxs_getcolorsinpalette();
												}
												
												
												foreach($colortypes as $currentcolortype)
												{
													$identification = $currentcolortype . $currentsubtype . $alphasuffix;
													$activeclass = nxs_popup_genericpopup_colorzenpicker_getactiveclass($nxs_colorzenpicker_currentvalue, $identification);
													
													?>
													<div onclick='nxs_js_selectcolorzenitem("<?php echo $identification;?>"); return false;' class='nxs-float-left'>
														<div class="nxs-colorzen-<?php echo $identification; ?> <?php echo $padding;?> border-radius-small color-sample <?php echo $activeclass; ?>">
															<p><?php echo $nxs_colorzenpicker_sampletext; ?></p>
														</div>
													</div>
													<?php
												}
											}
											?>
										</div>
										<div class="nxs-clear padding" />
										<?php
									}
									?>
								</div>
							</div>
						</div>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
					<?php } ?>
					
					<?php if ($nxs_colorzenpicker_colorset_lightgradient_enabled == "true") { ?>
					<!-- light gradient background colors -->
					<div class="content2">
						<div class="box">
							<div class="box-title">
								<h4><?php nxs_l18n_e("Light gradient background colors", "nxs_td"); ?></h4>
							</div>
							<div class="box-content">
								<div class="box-transparent-layer">
									<div style='float:left;'>
										<div class="<?php echo $padding;?> color-sample-head border-radius-small">
											<p>0%</p>
										</div>										
									</div>
										
									<div>
										<?php
										$subtypes = array("1", "2");
										foreach($subtypes as $currentsubtype)
										{
											if ($currentsubtype == "1")
											{
												$colortypes = array("base");
											}
											else
											{
												$colortypes = nxs_getcolorsinpalette();
											}
											foreach($colortypes as $currentcolortype)
											{
												$variations = array("ml");
												foreach($variations as $currentvariation)
												{
													$identification = $currentcolortype . $currentsubtype . "-" . $currentvariation;
													$activeclass = nxs_popup_genericpopup_colorzenpicker_getactiveclass($nxs_colorzenpicker_currentvalue, $identification);
	
													?>
													<div onclick='nxs_js_selectcolorzenitem("<?php echo $identification;?>"); return false;' class='nxs-float-left'>
														<div class="nxs-colorzen-<?php echo $identification; ?> <?php echo $padding;?> border-radius-small color-sample <?php echo $activeclass; ?>">
															<p><?php echo $nxs_colorzenpicker_sampletext; ?></p>
														</div>
													</div>
													<?php
												}
											}
										}
										?>
										<div class="nxs-clear" />
									</div>
								</div>
							</div>
						</div>
						<div class="nxs-clear"></div>				
					</div> <!-- END content -->
					<?php } ?>
					
					<?php if ($nxs_colorzenpicker_colorset_mediumgradient_enabled == "true") { ?>
					<!-- dark gradient background colors -->
					<div class="content2">
						<div class="box">
							<div class="box-title">
								<h4><?php nxs_l18n_e("Dark gradient background colors", "nxs_td"); ?></h4>
							</div>
							<div class="box-content">
								<div class="box-transparent-layer">
									<div>
										<div style='float:left;'>
											<div class="<?php echo $padding;?> color-sample-head border-radius-small">
												<p>0%</p>
											</div>										
										</div>
										<?php
										$subtypes = array("1", "2");
										foreach($subtypes as $currentsubtype)
										{
											if ($currentsubtype == "1")
											{
												$colortypes = array("base");
											}
											else
											{
												$colortypes = nxs_getcolorsinpalette();
											}
											foreach($colortypes as $currentcolortype)
											{
												$variations = array("dm");
												foreach($variations as $currentvariation)
												{
													$identification = $currentcolortype . $currentsubtype . "-" . $currentvariation;
													$activeclass = nxs_popup_genericpopup_colorzenpicker_getactiveclass($nxs_colorzenpicker_currentvalue, $identification);
													
													?>
													<div onclick='nxs_js_selectcolorzenitem("<?php echo $identification;?>"); return false;' class='nxs-float-left'>
														<div class="nxs-colorzen-<?php echo $identification; ?> <?php echo $padding;?> border-radius-small color-sample <?php echo $activeclass; ?>">
															<p><?php echo $nxs_colorzenpicker_sampletext; ?></p>
														</div>
													</div>
													<?php
												}
											}
										}
										?>
										<div class="nxs-clear" />								
									</div>
								</div>
							</div>
						</div>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
					<?php } ?>
					
				</div> <!-- END nxs-popup-content-canvas -->
			</div> <!-- END nxs-popup-content-canvas-cropper -->
	
			<div class="content2">
				<div class="box">
					<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_popup_navigateto("<?php echo $nxs_colorzenpicker_invoker; ?>"); return false;'><?php nxs_l18n_e("Back", "nxs_td"); ?></a>
					<?php 
					if ($nxs_colorzenpicker_currentvalue != "") 
					{
						?>
						<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton1 nxs-float-right" onclick='nxs_js_selectcolorzenitem(""); return false;'><?php nxs_l18n_e("No color", "nxs_td"); ?></a>
						<?php
					}
					?>
				</div>
				<div class="nxs-clear"></div>
			</div> <!-- END content -->
		</div> <!-- END block -->
	</div> <!-- END nxs-admin-wrap -->
	
	<script type='text/javascript'>
	
		function nxs_js_selectcolorzenitem(item) 
		{
			nxs_js_popup_setsessiondata("<?php echo $nxs_colorzenpicker_targetvariable; ?>", item);
			nxs_js_popup_sessiondata_make_dirty();
			// toon eerste scherm in de popup
			nxs_js_popup_navigateto("<?php echo $nxs_colorzenpicker_invoker; ?>");
		}
	
	</script>
	<?php

	// Setting the contents of the output buffer into a variable and cleaning up te buffer
  $html = ob_get_contents();
  ob_end_clean();
    
  // Setting the contents of the variable to the appropriate array position
  // The framework uses this array with its accompanying values to render the page
  $result["html"] = $html;
  return $result;
}
?>