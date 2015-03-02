<?php
function nxs_popup_optiontype_backgroundposition_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{	
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter

	$items = array (
		"left top"			=> nxs_l18n__("left top", "nxs_td"),
		"center top"		=> nxs_l18n__("center top", "nxs_td"), 
		"right top"			=> nxs_l18n__("right top", "nxs_td"), 
		"left center"		=> nxs_l18n__("left center", "nxs_td"),
		"center center"		=> nxs_l18n__("center center", "nxs_td"), 
		"right center"		=> nxs_l18n__("right center", "nxs_td"), 
		"left bottom"		=> nxs_l18n__("left bottom", "nxs_td"),
		"center bottom"		=> nxs_l18n__("center bottom", "nxs_td"), 
		"right bottom"		=> nxs_l18n__("right bottom", "nxs_td")
	);
	
	?>

      <div class="content2">
		<div class="box">
			<?php echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip); ?>
			<div class="box-content">
				<div id='nxs-bgpos-<?php echo $id; ?>' class="nxs-bgpos-container">
					<ul class="nxs-bgpos-list nxs-float-left">
						<?php
							$isfound = false;
							foreach ($items as $currentkey => $currentvalue) 
      						{
      							$selected = "";
					      		if ($currentkey == $value) 
					      		{
					      			$isfound = true;
					      			$selected = 'bgpos-item-active';
					      			$bgpos_text = $currentvalue;
					      		}
					      		?>
					          	<li class="<?php echo $selected; ?> bgpos-item">
					          		<span class="nxs-bgpos-arrow"></span>
					          		<span class="nxs-bgpos-key"><?php echo $currentkey; ?></span>
					          		<span class="nxs-bgpos-value"><?php echo $currentvalue; ?></span>
					          	</li>
					        	<?php 
        					} 
						?>
					</ul>
					<p class="nxs-bgpos-text"><?php echo $bgpos_text; ?></p>
					<input type="hidden" id="<?php echo $id; ?>" name="<?php echo $id; ?>" value="<?php echo $value; ?>" />
				</div>
			</div>
		</div>
		<div class="nxs-clear"></div>
	</div>

  	<script type="text/javascript">
  		$(document).ready(function(){
			$("#nxs-bgpos-<?php echo $id; ?> li.bgpos-item").hover(
			  function() {
			    $( this ).addClass("bgpos-item-hover");
			  }, function() {
			    $( this ).removeClass("bgpos-item-hover");
			  }
			);

			$("#nxs-bgpos-<?php echo $id; ?> li.bgpos-item").click(function(){
				// select the index of the clicked item
				var selectedPosition = $(this).index();

				// set clicked item on active
				$("#nxs-bgpos-<?php echo $id; ?> li.bgpos-item").removeClass('bgpos-item-active');
				$(this).addClass('bgpos-item-active');

				// get the key and value of the item
				var key = $(this).find('.nxs-bgpos-key').text();
				var value = $(this).find('.nxs-bgpos-value').text();

				// change the input and paragraph
				$('#<?php echo $id; ?>').val(key);
				$('#nxs-bgpos-<?php echo $id; ?> .nxs-bgpos-text').html(value);

				nxs_js_popup_sessiondata_make_dirty();
				
			});
		});
	</script>

	<?php
}

function nxs_popup_optiontype_backgroundposition_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_textbox("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_backgroundposition_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_backgroundposition_getpersistbehaviour()
{
	return "writeid";
}

?>