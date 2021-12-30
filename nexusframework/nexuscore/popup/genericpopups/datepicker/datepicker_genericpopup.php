<?php

function nxs_popup_genericpopup_datepicker_getpopup($args)
{
	extract($args);
	
	//
	$nxs_datepicker_currentvalue = $clientpopupsessioncontext["nxs_datepicker_currentvalue"];
	
	if ($clientpopupsessiondata != null) { extract($clientpopupsessiondata); }	
	if ($clientshortscopedata != null) { extract($clientshortscopedata); }

	$result = array();
	$result["result"] = "OK";
	
	nxs_ob_start();
	
	$padding = "";
	
	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">
			<?php nxs_render_popup_header(nxs_l18n__("Date picker", "nxs_td")); ?>
			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
					
					<div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("General", "nxs_td"); ?></h4></div>
							<div class="box-content">
								<?php echo $nxs_datepicker_currentvalue; ?>
							</div>
						</div>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
				</div> <!-- END nxs-popup-content-canvas -->
			</div> <!-- END nxs-popup-content-canvas-cropper -->
	
			<div class="content2">
				<div class="box">
					<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closedatepicker(); return false;'><?php nxs_l18n_e("Back", "nxs_td"); ?></a>
				</div>
				<div class="nxs-clear"></div>
			</div> <!-- END content -->
		</div> <!-- END block -->
	</div> <!-- END nxs-admin-wrap -->
	
	<script>
		
		function nxs_js_pickdate(val) 
		{
			nxs_js_popup_setsessiondata("<?php echo $nxs_datepicker_targetvariable; ?>", val);
			nxs_js_popup_sessiondata_make_dirty();
			nxs_js_closedatepicker();
		}
		
		function nxs_js_closedatepicker() 
		{
			// toon scherm in de popup dat ons aanriep
			var invoker = nxs_js_popup_getsessioncontext("nxs_datepicker_invoker");
			nxs_js_popup_navigateto(invoker);
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
