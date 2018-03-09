<?php
function nxs_popup_genericpopup_backgroundpatternpicker_getactiveclass($a, $b)
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

function nxs_popup_genericpopup_backgroundpatternpicker_getpopup($args)
{
	// initial values, can/will be overridden by the extracts below
	$nxs_backgroundpatternpicker_sampletext = nxs_l18n__("Sample pattern", "nxs_td");

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
	
	nxs_ob_start();
	
	$padding = "";
	
	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">
			<?php nxs_render_popup_header(nxs_l18n__("Color zen picker", "nxs_td")); ?>
			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
					<div class="content2">
						<div class="box">
							<div class="box-title">
								<h4>Patterns</h4>
							</div>
							<div class="box-content">
								<ul class="textures">
									<?php
									$textures = array("1", "2", "3", "4", "5", "6", "7", "8");
									foreach($textures as $currenttexture)
									{
										$identification = "nxs-bgtexture texture" . $currenttexture;
										?>
										<li class="<?php echo $identification; ?>" onclick='nxs_js_selectbackgroundpatternitem("<?php echo $identification;?>"); return false;' class='nxs-float-left'>
										</li>
										<?php
									}
									?>
								</ul>
							</div>
						</div>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
				</div> <!-- END nxs-popup-content-canvas -->
			</div> <!-- END nxs-popup-content-canvas-cropper -->
	
			<div class="content2">
				<div class="box">
					<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_popup_navigateto("<?php echo $nxs_backgroundpatternpicker_invoker; ?>"); return false;'><?php nxs_l18n_e("Back", "nxs_td"); ?></a>
					<?php 
					if ($nxs_backgroundpatternpicker_currentvalue != "") 
					{
						?>
						<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton1 nxs-float-right" onclick='nxs_js_selectbackgroundpatternitem(""); return false;'><?php nxs_l18n_e("No pattern", "nxs_td"); ?></a>
						<?php
					}
					?>
				</div>
				<div class="nxs-clear"></div>
			</div> <!-- END content -->
		</div> <!-- END block -->
	</div> <!-- END nxs-admin-wrap -->
	
	<script>
	
		function nxs_js_selectbackgroundpatternitem(item) 
		{
			nxs_js_popup_setsessiondata("<?php echo $nxs_backgroundpatternpicker_targetvariable; ?>", item);
			nxs_js_popup_sessiondata_make_dirty();
			// toon eerste scherm in de popup
			nxs_js_popup_navigateto("<?php echo $nxs_backgroundpatternpicker_invoker; ?>");
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