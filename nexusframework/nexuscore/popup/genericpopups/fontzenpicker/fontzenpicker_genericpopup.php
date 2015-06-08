<?php

function nxs_popup_genericpopup_fontzenpicker_getpopup($args)
{
	extract($args);
	
	extract($clientpopupsessiondata);	
	extract($clientshortscopedata);

	$result = array();
	$result["result"] = "OK";
	
	nxs_ob_start();
	
	$padding = "";
	
	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">
			<?php nxs_render_popup_header(nxs_l18n__("Fontzen picker", "nxs_td")); ?>
			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
					
					<div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("Fonts", "nxs_td"); ?></h4></div>
							<div class="box-content">
								<table>
									<?php
									$fontidentifiers = nxs_font_getfontidentifiers();
										
									foreach($fontidentifiers as $currentfontidentifier)
									{
										?>
										<tr>
											<td>
												Font <?php echo $currentfontidentifier; ?>
											</td>
											<td class="nxs-fontzen-<?php echo $currentfontidentifier; ?>">
												<a href='#' onclick='nxs_js_selectitem("<?php echo $currentfontidentifier;?>"); return false;'>
													<span class="<?php echo $currentfontidentifier; ?>">Sample fontfamily text</span>
												</a>
											</td>
										</tr>
										<?php
									}
									?>
								</table>
							</div>
						</div>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
                  
				</div> <!-- END nxs-popup-content-canvas -->
			</div> <!-- END nxs-popup-content-canvas-cropper -->
	
			<div class="content2">
				<div class="box">
					<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_popup_navigateto("<?php echo $nxs_fontzenpicker_invoker; ?>"); return false;'><?php nxs_l18n_e("Back", "nxs_td"); ?></a>
					<?php 
					if ($nxs_fontzenpicker_currentvalue != "") 
					{
						?>
						<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton1 nxs-float-right" onclick='nxs_js_selectitem(""); return false;'><?php nxs_l18n_e("No fontzen", "nxs_td"); ?></a>
						<?php
					}
					?>
				</div>
				<div class="nxs-clear"></div>
			</div> <!-- END content -->
		</div> <!-- END block -->
	</div> <!-- END nxs-admin-wrap -->
	
	<script type='text/javascript'>
	
		function nxs_js_selectitem(item) 
		{
			nxs_js_popup_setsessiondata("<?php echo $nxs_fontzenpicker_targetvariable; ?>", item);
			nxs_js_popup_sessiondata_make_dirty();
			// toon eerste scherm in de popup
			nxs_js_popup_navigateto("<?php echo $nxs_fontzenpicker_invoker; ?>");
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
