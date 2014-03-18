<?php
function nxs_popup_optiontype_image_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter	
		
	if ($value != "") {
		?>
      <div class="content2">
				<div class="box">
					<?php echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip); ?>
          <div class="box-content">
						<script type='text/javascript'>
                                      nxs_js_popup_setsessiondata('<?php echo $id; ?>', '<?php echo $$id; ?>');
                                  </script>
                              
                                  <?php
							$imagemetadata= wp_get_attachment_image_src($value, 'thumbnail', true);
							$imageurl = $imagemetadata[0];	// index 0 = url		
                                  ?>

                                  <a href="#" onclick='nxs_js_setpopupdatefromcontrols(); nxs_js_popup_setsessiondata("nxs_mediapicker_invoker", nxs_js_popup_getcurrentsheet()); nxs_js_popup_setsessiondata("nxs_mediapicker_targetvariable", "<?php echo $id;?>"); nxs_js_popup_navigateto("mediapicker"); return false;' class="nxsbutton1 nxs-float-right">Select</a>
                                  <?php
                                  if (isset($value) && $value != 0)
                                  {
                                  	?>
                                  	<a href="#" onclick='nxs_js_setpopupdatefromcontrols(); nxs_js_popup_setsessiondata("<?php echo $id;?>", ""); nxs_js_popup_sessiondata_make_dirty(); nxs_js_popup_refresh(); return false;' class="nxsbutton1 nxs-float-right">None</a>
                                  	<?php
                                  }
                                  ?>
                                  <a href="#" onclick='nxs_js_setpopupdatefromcontrols(); nxs_js_popup_setsessiondata("nxs_mediapicker_invoker", nxs_js_popup_getcurrentsheet()); nxs_js_popup_setsessiondata("nxs_mediapicker_targetvariable", "<?php echo $id;?>"); nxs_js_popup_navigateto("mediapicker"); return false;' class="nxs-float-left">
                                      <img src='<?php echo $imageurl; ?>' class="nxs-icon-left" />
                                  </a>
                                  
                                  <?php
							if ($imagemetadata != "") {
								// resetting the image to "full" for correct metadata "width" and "height" inclusion 
								$imagemetadata= wp_get_attachment_image_src($value, 'full', true);
								echo '<p>width : ' . $imagemetadata[1] . '</p>';
								echo '<p>height : ' . $imagemetadata[2] . '</p>'; 
							} 
							
							$mimetype = get_post_mime_type($value);
							if ($mimetype == "") {
								$mimetype = __("Unknown mimetype");
                                  	} else {
							echo '<p>type : ' . $mimetype . '</p>';
							}
						?>
                                  
                                  <div class="nxs-clear"></div>
                              </div>
				</div>
				<div class="nxs-clear"></div>
			</div>
		
			<?php } else { ?>
		
      <div class="content2">
				<div class="box">
					<?php echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip); ?>
          <div class="box-content">
						<script type='text/javascript'>
							nxs_js_popup_setsessiondata('thumbid', '<?php echo $thumbid; ?>');
						</script>
						<a href="#" class="nxsbutton1 nxs-float-right" onclick='nxs_js_setpopupdatefromcontrols(); nxs_js_popup_setsessiondata("nxs_mediapicker_invoker", nxs_js_popup_getcurrentsheet()); nxs_js_popup_setsessiondata("nxs_mediapicker_targetvariable", "<?php echo $id;?>"); nxs_js_popup_navigateto("mediapicker"); return false;'><?php echo nxs_l18n__("Change", "nxs_td"); ?></a>
						<a href="#" onclick='nxs_js_setpopupdatefromcontrols(); nxs_js_popup_setsessiondata("nxs_mediapicker_invoker", nxs_js_popup_getcurrentsheet()); nxs_js_popup_setsessiondata("nxs_mediapicker_targetvariable", "<?php echo $id;?>"); nxs_js_popup_navigateto("mediapicker"); return false;'>
							<span class='title'><?php echo nxs_l18n__("Suppressed", "nxs_td"); ?></span>
						</a>
					</div>
				</div>
				<div class="nxs-clear"></div>
			</div>
			
			<?php }
	//
}

function nxs_popup_optiontype_image_renderstorestatecontroldata($optionvalues)
{
	// not used for image
	//$id = $optionvalues["id"];
	//echo 'nxs_js_popup_storestatecontroldata_textbox("' . $id . '", "' . $id . '");';
}

function nxs_popup_optiontype_image_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	
	$id = $optionvalues["id"];
	$value = $metadata[$id];
	
	$globalid = nxs_get_globalid($value, true);
	$result[$id . "_globalid"] = $globalid;
	
	// nothing to do here
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_image_getpersistbehaviour()
{
	return "writeid";
}

?>		