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
	
	?>

      <div class="content2">
		<div class="box">
			<?php echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip); ?>
			<div class="box-content">
				<div id='nxs-<?php echo $subtype; ?>-<?php echo $id; ?>' class="nxs-radiobuttons-container <?php echo $classes; ?>">
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
					          		<?php if (!$no_icon) { ?>
					          			<span class="nxs-radiobuttons-icon nxs-icon-<?php echo $currenticon; ?> nxs-icon"></span>
					          		<?php } else { ?>
					          			<span class="nxs-radiobuttons-multiplier"><?php echo $currentvalue; ?></span>
				          			<?php } ?>
				          			<span class="nxs-radiobuttons-value"><?php echo $currentvalue; ?></span>
					          		<span class="nxs-radiobuttons-key"><?php echo $currentkey; ?></span>
					          	</li>
					        	<?php 
        					} 
						?>
					</ul>
					<p class="nxs-radiobuttons-text"><?php echo $radiobuttons_text; ?></p>
					<input type="hidden" id="<?php echo $id; ?>" name="<?php echo $id; ?>" value="<?php echo $value; ?>" />
				</div>
			</div>
		</div>
		<div class="nxs-clear"></div>
	</div>

  	<script type="text/javascript">
  		jQ_nxs(document).ready(function(){
			jQ_nxs("#nxs-<?php echo $subtype; ?>-<?php echo $id; ?> li.radiobuttons-item").hover(
			  function() {
			    jQ_nxs( this ).addClass("radiobuttons-item-hover");
			  }, function() {
			    jQ_nxs( this ).removeClass("radiobuttons-item-hover");
			  }
			);

			jQ_nxs("#nxs-<?php echo $subtype; ?>-<?php echo $id; ?> li.radiobuttons-item").click(function(){
				// ignore click if the item is disabled
				if (!jQ_nxs(this).hasClass('radiobuttons-item-disabled')){
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
			});
		});
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