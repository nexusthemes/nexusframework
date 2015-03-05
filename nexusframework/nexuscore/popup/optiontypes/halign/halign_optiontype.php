<?php
function nxs_popup_optiontype_halign_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{	
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter

	$items = array (
		"left"			=> nxs_l18n__("left", "nxs_td"),
		"center"		=> nxs_l18n__("center", "nxs_td"), 
		"right"			=> nxs_l18n__("right", "nxs_td")
	);
	
	?>

      <div class="content2">
		<div class="box">
			<?php echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip); ?>
			<div class="box-content">
				<div id='nxs-halign-<?php echo $id; ?>' class="nxs-halign-container">
					<ul class="nxs-halign-list nxs-float-left">
						<?php
							foreach ($items as $currentkey => $currentvalue) 
      						{
      							$selected = "";
					      		if ($currentkey == $value) 
					      		{
					      			$selected = 'halign-item-active';
					      			$halign_text = $currentvalue;
					      		}
					      		?>
					          	<li class="<?php echo $selected; ?> halign-item">
					          		<span class="nxs-halign-icon"></span>
					          		<span class="nxs-halign-key"><?php echo $currentkey; ?></span>
					          		<span class="nxs-halign-value"><?php echo $currentvalue; ?></span>
					          	</li>
					        	<?php 
        					} 
						?>
					</ul>
					<p class="nxs-halign-text"><?php echo $halign_text; ?></p>
					<input type="hidden" id="<?php echo $id; ?>" name="<?php echo $id; ?>" value="<?php echo $value; ?>" />
				</div>
			</div>
		</div>
		<div class="nxs-clear"></div>
	</div>

  	<script type="text/javascript">
  		$(document).ready(function(){
			$("#nxs-halign-<?php echo $id; ?> li.halign-item").hover(
			  function() {
			    $( this ).addClass("halign-item-hover");
			  }, function() {
			    $( this ).removeClass("halign-item-hover");
			  }
			);

			$("#nxs-halign-<?php echo $id; ?> li.halign-item").click(function(){
				// select the index of the clicked item
				var selectedPosition = $(this).index();

				// set clicked item on active
				$("#nxs-halign-<?php echo $id; ?> li.halign-item").removeClass('halign-item-active');
				$(this).addClass('halign-item-active');

				// get the key and value of the item
				var key = $(this).find('.nxs-halign-key').text();
				var value = $(this).find('.nxs-halign-value').text();

				// change the input and paragraph
				$('#<?php echo $id; ?>').val(key);
				$('#nxs-halign-<?php echo $id; ?> .nxs-halign-text').html(value);

				nxs_js_popup_sessiondata_make_dirty();
				
			});
		});
	</script>

	<?php
}

function nxs_popup_optiontype_halign_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_textbox("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_halign_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_halign_getpersistbehaviour()
{
	return "writeid";
}

?>