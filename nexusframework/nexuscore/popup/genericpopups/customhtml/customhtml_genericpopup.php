<?php

function nxs_popup_genericpopup_customhtml_getpopup_basic($args)
{
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
			<?php nxs_render_popup_header($nxs_customhtml_popupheadertitle); ?>
			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
					<div class="content2">
						
						<?php echo $nxs_customhtml_customhtmlcanvascontent; ?>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
				</div> <!-- END nxs-popup-content-canvas -->
			</div> <!-- END nxs-popup-content-canvas-cropper -->
	
			<div class="content2">
				<?php echo $nxs_customhtml_customhtmlfootercontent; ?>
				<div class="nxs-clear"></div>
			</div> <!-- END content -->
		</div> <!-- END block -->
	</div> <!-- END nxs-admin-wrap -->
	
	<script type='text/javascript'>
		<?php 
			echo $nxs_customhtml_customhtmlscriptfunctions; 
			if ($minwidth != "")
			{
				?>
				function nxs_js_popup_get_minwidth()
				{
					return <?php echo $minwidth; ?>;
				}
				<?php
			}
		?>
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

function nxs_popup_genericpopup_customhtml_getpopup_completecustom($args)
{
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
	
	<?php echo $nxs_customhtml_customhtmlcanvascontent; ?>
	
	<script type='text/javascript'>
		<?php 
			echo $nxs_customhtml_customhtmlscriptfunctions; 
			if ($minwidth != "")
			{
				?>
				function nxs_js_popup_get_minwidth()
				{
					return <?php echo $minwidth; ?>;
				}
				<?php
			}
		?>
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

function nxs_popup_genericpopup_customhtml_getpopup($args)
{
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

	if ($nxs_customhtml_scaffoldingtype == "basic")
	{
		$result = nxs_popup_genericpopup_customhtml_getpopup_basic($args);
	}
	// else if () // add additional supported scaffolds here
	else
	{
		// a "naked" popup => the custom html will handle all html
		$result = nxs_popup_genericpopup_customhtml_getpopup_completecustom($args);
	}
	
	return $result;
}
?>