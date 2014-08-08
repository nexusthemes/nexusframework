<?php

function nxs_popup_genericpopup_effectspicker_getpopup($args)
{
	extract($args);
	
	extract($clientpopupsessiondata);	
	extract($clientshortscopedata);

	$result = array();
	$result["result"] = "OK";
	
	ob_start();
	
	$padding = "";
	
	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">
			<?php nxs_render_popup_header(nxs_l18n__("Effects picker", "nxs_td")); ?>
			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
					
					<div class="content2">
						<div class="box">
							<div class="box-title"><h4><?php nxs_l18n_e("Fonts", "nxs_td"); ?></h4></div>
							<div class="box-content">
								<?php
									echo $nxs_effectspicker_currentvalue;
									if ($nxs_effectspicker_currentvalue != "")
									{
										echo "AAP:<br />";
										$jsonobject = json_decode($nxs_effectspicker_currentvalue);
										var_dump($jsonobject);
										echo "NOOT:<br />";
										$jsonobject = json_decode($nxs_effectspicker_currentvalue, true);
										var_dump($jsonobject);
										echo "MIES:<br />";
										$jsonobject = json_decode('{ "name": "Johnson" }');
										var_dump($jsonobject);
										echo "<br />";
									}
								?>
							</div>
						</div>
						<div class="nxs-clear"></div>
					</div> <!-- END content -->
                  
				</div> <!-- END nxs-popup-content-canvas -->
			</div> <!-- END nxs-popup-content-canvas-cropper -->
	
			<div class="content2">
				<div class="box">
					<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_popup_navigateto("<?php echo $nxs_effectspicker_invoker; ?>"); return false;'><?php nxs_l18n_e("Back", "nxs_td"); ?></a>
					<a href='#' onclick='nxs_js_selectitem_v2(); return false;'>ZET JSON</a>
				</div>
				<div class="nxs-clear"></div>
			</div> <!-- END content -->
		</div> <!-- END block -->
	</div> <!-- END nxs-admin-wrap -->
	
	<script type='text/javascript'>
	
		function nxs_js_selectitem_v2() 
		{
			var str = '{ "name": "PIET" }';
			nxs_js_log(str);
			nxs_js_selectitem(str);
		}
	
		function nxs_js_selectitem(item) 
		{
			nxs_js_popup_setsessiondata("<?php echo $nxs_effectspicker_targetvariable; ?>", item);
			nxs_js_popup_sessiondata_make_dirty();
			// toon eerste scherm in de popup
			nxs_js_popup_navigateto("<?php echo $nxs_effectspicker_invoker; ?>");
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