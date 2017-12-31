<?php
function nxs_popup_optiontype_radiobuttons_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{	
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter

	if (!$value){
		$value = $default;
	}

	if ($layout) {
		$layout = " nxs-radiobuttons-layout-" . $layout . " ";
	}

	$items = nxs_style_getradiobuttonsitems($subtype);

	$no_icon = "";
	$is_array = array_shift(array_values($items));

	if ( !is_array($is_array))
	{
		$no_icon = " nxs-radiobutton-noicon ";
	}

	$classes = "nxs-radiobutton-" . $subtype . $layout . $no_icon;
	$display_attribute = "";
	if ($display == "none")
	{
		$display_attribute .= "display:none;";
	}
	else if ($display == "noneifempty")
	{
		if ($value == "")
		{
			$display_attribute .= "display:none;";
		}
	}
	
	$wrapper_classes = array("content2");
	if ($optionvalues["formobiles"] == true)
	{
		$wrapper_classes[] = "nxs-for-mobiles";
		$wrapper_classes[] = "nxs-viewport-dependent";
	}
	if ($optionvalues["fortablets"] == true)
	{
		$wrapper_classes[] = "nxs-for-tablets";
		$wrapper_classes[] = "nxs-viewport-dependent";
	}
	
	$wrapper_classes_value = implode(" ", $wrapper_classes);
	
	$actions_html = "";
	
	// allow toggling of fields
	if ($optionvalues["mobile_action_toggles"] != "")
	{
		nxs_ob_start();
		?>
		<div class='nxs-action-wrap'>
			<a href='#' onclick="jQuery('<?php echo $optionvalues["mobile_action_toggles"]; ?>').show();return false;">
				<span class="nxs-icon-mobile"></span>
			</a>
		</div>
		<?php
		$actions_html .= nxs_ob_get_contents();
		nxs_ob_end_clean();
	}
		
	//
	
	?>
  <div class="<?php echo $wrapper_classes_value; ?>" style="<?php echo $display_attribute;?>">
		<div class="box">
			<?php echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip); ?>
			<div class="box-content">
				<div id='nxs-<?php echo $subtype; ?>-<?php echo $id; ?>' class="nxs-radiobuttons-container <?php echo $classes; ?>" style='display: flex;'>
					<div style='flex-grow: 1;'>
						<ul class="nxs-radiobuttons nxs-radiobuttons-list nxs-float-left">
							<?php
								$i = 0;
								foreach ($items as $currentkey => $currentvalue) 
    						{
    							$i++;
    							$selected = "";

    							if ($no_icon == "") {
    								$currenticon = $currentvalue[1];
    								$currentvalue = $currentvalue[0];
    							}

				      		if ($currentkey == $value) 
				      		{
				      			$selected = 'radiobuttons-item-active ';
				      			$radiobuttons_text = $currentvalue;
				      		}

				      		if ($currentvalue == "")
				      		{
				      			$currentvalue = "default";
				      		}

				      		$disable_class = '';
				      		if ($disable)
				      		{
				      			if (in_array ($i, $disable))
				      			{
				      				$disable_class = 'radiobuttons-item-disabled ';
				      			}
				      		}
				      		?>
				          	<li class="<?php echo $disable_class . $selected; ?>radiobuttons-item">
				          		<?php 
				          		if (!$no_icon) 
				          		{ 
				          			?>
				          			<span class="nxs-radiobuttons-icon nxs-icon-<?php echo $currenticon; ?> nxs-icon"></span>
				          			<?php 
				          		} 
				          		else 
				          		{ 
				          			?>
				          			<span class="nxs-radiobuttons-multiplier"><?php echo $currentvalue; ?></span>
			          				<?php 
			          			} 
			          			?>
			          			<span class="nxs-radiobuttons-value"><?php echo $currentvalue; ?></span>
				          		<span class="nxs-radiobuttons-key"><?php echo $currentkey; ?></span>
				          	</li>
				        	<?php 
      					}
      					if ($optionvalues["enable_deselect"] == true)
      					{
      						?>
      						<li class="radiobuttons-item-deselect">
      							<span class="nxs-radiobuttons-icon nxs-icon-remove-sign nxs-icon"></span>
      						</li>
      						<?php
      					}
							?>
						</ul>
						<p class="nxs-radiobuttons-text" style="display: none"><?php echo $radiobuttons_text; ?></p>
						<input type="hidden" id="<?php echo $id; ?>" name="<?php echo $id; ?>" value="<?php echo $value; ?>" />
					</div>
					<?php echo $actions_html; ?>
				</div>
			</div>
		</div>
		<div class="nxs-clear"></div>
	</div>

  	<script type="text/javascript">
  		jQ_nxs(document).ready
  		(
	  		function()
	  		{
					jQ_nxs("#nxs-<?php echo $subtype; ?>-<?php echo $id; ?> li.radiobuttons-item").hover(
					  function() {
					    jQ_nxs( this ).addClass("radiobuttons-item-hover");
					  }, function() {
					    jQ_nxs( this ).removeClass("radiobuttons-item-hover");
					  }
					);
		
					jQ_nxs("#nxs-<?php echo $subtype; ?>-<?php echo $id; ?> li.radiobuttons-item").click
					(
						function()
						{
							if (jQ_nxs(this).hasClass('radiobuttons-item-disabled'))
							{
								// ignore click if the item is disabled
							}
							else
							{
								// select the index of the clicked item
								var selectedPosition = jQ_nxs(this).index();
			
								// set clicked item on active
								jQ_nxs("#nxs-<?php echo $subtype; ?>-<?php echo $id; ?> li.radiobuttons-item").removeClass('radiobuttons-item-active');
								jQ_nxs(this).addClass('radiobuttons-item-active');
			
								// get the key and value of the item
								var key = jQ_nxs(this).find('.nxs-radiobuttons-key').text();
								var value = jQ_nxs(this).find('.nxs-radiobuttons-value').text();
			
								// change the input and paragraph
								jQ_nxs('#<?php echo $id; ?>').val(key);
								jQ_nxs('#nxs-<?php echo $subtype; ?>-<?php echo $id; ?> .nxs-radiobuttons-text').html(value);
			
								nxs_js_popup_sessiondata_make_dirty();
							}
						}
					);
					
					jQ_nxs("#nxs-<?php echo $subtype; ?>-<?php echo $id; ?> li.radiobuttons-item-deselect").hover(
					  function() {
					    jQ_nxs( this ).addClass("radiobuttons-item-hover");
					  }, function() {
					    jQ_nxs( this ).removeClass("radiobuttons-item-hover");
					  }
					);
					
					jQ_nxs("#nxs-<?php echo $subtype; ?>-<?php echo $id; ?> li.radiobuttons-item-deselect").click
					(
						function()
						{
							// set clicked item on active
							jQ_nxs("#nxs-<?php echo $subtype; ?>-<?php echo $id; ?> li.radiobuttons-item").removeClass('radiobuttons-item-active');
							
							// change the input and paragraph
							jQ_nxs('#<?php echo $id; ?>').val("");
							jQ_nxs('#nxs-<?php echo $subtype; ?>-<?php echo $id; ?> .nxs-radiobuttons-text').html("");
		
							nxs_js_popup_sessiondata_make_dirty();
						}
					);
				}
			);
		</script>

	<?php
}

function nxs_popup_optiontype_radiobuttons_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_textbox("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_radiobuttons_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_radiobuttons_getpersistbehaviour()
{
	return "writeid";
}

?>