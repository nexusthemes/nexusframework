<?php
function nxs_popup_optiontype_checkbox_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	$inverse_mode = "false";
	
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter
	
	?>
	
	<div class="content2">
	  <div class="box">
				<?php
				echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip);
				?>
	      	<div class="box-content">
	          <?php 
	          if ($inverse_mode == "false")
	          {
	          	// normal
		          if ($value == "") 
		          {
		              $option_is_checked = "";
		          } 
		          else 
		          {
		              $option_is_checked = "checked='checked'";
		          }
		        } 
		        else
		        {
		        	// other way around
		          if ($value == "") 
		          {
		            $option_is_checked = "checked='checked'";
		          } 
		          else 
		          {
		          	$option_is_checked = "";  
		          }
		      	}	
		        ?>
	          <div class='nxs-float-left nxs-clear'>
	              <input type='checkbox' id='<?php echo $id; ?>' name='<?php echo $id; ?>' <?php echo $option_is_checked; ?> />
	          </div>
	      </div>
	  	</div>
	  <div class="nxs-clear"></div>
	</div>
	
	<?php
	//
}

function nxs_popup_optiontype_checkbox_renderstorestatecontroldata($optionvalues)
{
	$inverse_mode = "false";
	
	extract($optionvalues);
	
	$id = $optionvalues["id"];
	
	if ($inverse_mode == "false")
	{
		// default
		echo 'nxs_js_popup_storestatecontroldata_checkbox("' . $id . '", "' . $id . '");';	
	}
	else
	{
		// inverse mode
		echo 'nxs_js_popup_storestatecontroldata_checkbox_inverse("' . $id . '", "' . $id . '");';	
	}
}

function nxs_popup_optiontype_checkbox_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_checkbox_getpersistbehaviour()
{
	return "writeid";
}

?>