<?php

function nxs_popup_genericpopup_iconpicker_getpopup($args)
{
	extract($args);
	
	if ($clientpopupsessiondata != null) { extract($clientpopupsessiondata); }	
	if ($clientshortscopedata != null) { extract($clientshortscopedata); }

	$result = array();
	$result["result"] = "OK";

	global $nxs_g_modelmanager;
	$icontags = $nxs_g_modelmanager->getserializedmodel("nxs.framework.icontag");
	$icons = $nxs_g_modelmanager->getserializedmodel("nxs.framework.icon");
	
	$iconset = "nxs-icon";
	
	nxs_ob_start();
	
	$padding = "";
	
	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">
			<?php nxs_render_popup_header(nxs_l18n__("Icon picker", "nxs_td")); ?>
			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
					
					<?php
					foreach ($icontags as $icontag)
					{
						//echo json_encode($icontag);
						$icontypes = array();
						$icontag_id = $icontag["nxs.framework.icontag_id"];
						$icontag_title = $icontag["icontag"];	// todo; make a dedicated title column
						
						foreach ($icons as $icon)
						{
							$icon_icon = $icon["icon"];
							$icontag_ids_string = $icon["nxs.framework.icontag_ids"];
							$icontag_ids = explode(";", $icontag_ids_string);
							if (in_array($icontag_id, $icontag_ids))
							{
								$icontypes[] = $icon_icon;
							}
						}
						
						//echo $icontag_title . " " . count($icontypes);
						
						if (count($icontypes) > 0)
						{
							?>
							<!-- INTERFACE -->					
							<div class="content2">
								<div class="box">
									<div class="box-title"><h4><?php echo $icontag_title; ?></h4></div>
									<div class="box-content">
										<ul>
											<?php
											foreach($icontypes as $currenticontype)
											{
												$identification = $iconset . "-" . $currenticontype;
												?>
												<li class='nxs-float-left nxs-icon'>
													<a href='#' onclick='nxs_js_selecticonitem("<?php echo $identification;?>"); return false;'>
														<span title="<?php echo $currenticontype; ?>" class="<?php echo $identification; ?>"></span>
													</a>
												</li>
												<?php
											}
											?>
										</ul>
									</div>
								</div>
								<div class="nxs-clear"></div>
							</div> <!-- END content -->
							<?php
						}
					}
					?>
						
				</div> <!-- END nxs-popup-content-canvas -->
			</div> <!-- END nxs-popup-content-canvas-cropper -->
	
			<div class="content2">
				<div class="box">
					<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_popup_navigateto("<?php echo $nxs_iconpicker_invoker; ?>"); return false;'><?php nxs_l18n_e("Back", "nxs_td"); ?></a>
					<?php 
					if ($nxs_iconpicker_currentvalue != "") 
					{
						?>
						<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton1 nxs-float-right" onclick='nxs_js_selecticonitem(""); return false;'><?php nxs_l18n_e("No icon", "nxs_td"); ?></a>
						<?php
					}
					?>
				</div>
				<div class="nxs-clear"></div>
			</div> <!-- END content -->
		</div> <!-- END block -->
	</div> <!-- END nxs-admin-wrap -->
	
	<script>
	
		function nxs_js_selecticonitem(item) 
		{
			nxs_js_popup_setsessiondata("<?php echo $nxs_iconpicker_targetvariable; ?>", item);
			nxs_js_popup_sessiondata_make_dirty();
			// toon eerste scherm in de popup
			nxs_js_popup_navigateto("<?php echo $nxs_iconpicker_invoker; ?>");
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
